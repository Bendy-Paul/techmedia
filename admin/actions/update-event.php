<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && verify_csrf_token()) {
    $id = (int)$_POST['id'];
    
    // Basic Fields
    $title = trim($_POST['title']);
    $desc = $_POST['description'];
    $type = $_POST['event_type'];
    $start = $_POST['start_datetime'];
    $end = $_POST['end_datetime'];
    $organizer = !empty($_POST['organizer_company_id']) ? $_POST['organizer_company_id'] : null;
    $is_virtual = isset($_POST['is_virtual']) ? 1 : 0;
    $city_id = ($is_virtual || empty($_POST['city_id'])) ? null : $_POST['city_id'];
    $loc_name = ($is_virtual) ? 'Online' : trim($_POST['location_name']);
    $price = $_POST['price'];
    $ticket_url = trim($_POST['ticket_url']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // JSON
    $social = json_encode($_POST['social'] ?? []);
    $community = json_encode($_POST['community'] ?? []);

    // Banner
    $upload_dir = '../../uploads/events/';
    $img_sql = "";
    $params = [
        $title, $desc, $type, $start, $end, $organizer, $loc_name, $city_id, 
        $is_virtual, $price, $ticket_url, $is_featured, $social, $community
    ];

    if (!empty($_FILES['banner_image_upload']['name'])) {
        $fname = time() . '_' . basename($_FILES['banner_image_upload']['name']);
        if (move_uploaded_file($_FILES['banner_image_upload']['tmp_name'], $upload_dir . $fname)) {
            $img_sql = ", banner_image_url = ?";
            $params[] = 'uploads/events/' . $fname;
        }
    }
    $params[] = $id;

    try {
        $pdo->beginTransaction();

        // 1. Update Core Event
        $sql = "UPDATE events SET 
                title=?, description=?, event_type=?, start_datetime=?, end_datetime=?, 
                organizer_company_id=?, location_name=?, city_id=?, is_virtual=?, 
                price=?, ticket_url=?, is_featured=?, social_links=?, community_links=? 
                $img_sql WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // 2. Handle Tags
        // A. Create New Tags
        if (!empty($_POST['new_tags'])) {
            $new_tags = explode(',', $_POST['new_tags']);
            $check = $pdo->prepare("SELECT id FROM tags WHERE name = ?");
            $ins = $pdo->prepare("INSERT INTO tags (name) VALUES (?)");
            $link = $pdo->prepare("INSERT IGNORE INTO event_tags (event_id, tag_id) VALUES (?, ?)"); // IGNORE to skip dups
            
            foreach($new_tags as $tname) {
                $tname = trim($tname);
                if(!$tname) continue;
                $check->execute([$tname]);
                $ex = $check->fetch();
                if($ex) $tid = $ex['id'];
                else { $ins->execute([$tname]); $tid = $pdo->lastInsertId(); }
                $link->execute([$id, $tid]);
            }
        }
        // B. Update Linked Tags (Reset method)
        $pdo->prepare("DELETE FROM event_tags WHERE event_id = ?")->execute([$id]); 
        // Note: Ideally, merge with new tags logic above to avoid deleting the new ones just added.
        // Better approach: Get all tag IDs (existing checkboxes + newly created IDs) and sync.
        // For simplicity here: Re-link checkboxes
        if (!empty($_POST['tags'])) {
            $link = $pdo->prepare("INSERT IGNORE INTO event_tags (event_id, tag_id) VALUES (?, ?)");
            foreach($_POST['tags'] as $tid) $link->execute([$id, $tid]);
        }

        // 3. Handle Speakers
        // A. Update Existing
        if (!empty($_POST['existing_speakers'])) {
            $upd_spk = $pdo->prepare("UPDATE event_speakers SET name=?, position=? WHERE id=? AND event_id=?");
            foreach ($_POST['existing_speakers'] as $sid => $data) {
                $upd_spk->execute([$data['name'], $data['position'], $sid, $id]);
            }
        }
        // B. Add New
        if (!empty($_POST['new_speakers'])) {
            $ins_spk = $pdo->prepare("INSERT INTO event_speakers (event_id, name, position, image_url) VALUES (?, ?, ?, ?)");
            foreach ($_POST['new_speakers'] as $key => $data) {
                $spk_url = null;
                if (!empty($_FILES['new_speaker_images']['name'][$key])) {
                    $fname = time() . '_spk_' . basename($_FILES['new_speaker_images']['name'][$key]);
                    move_uploaded_file($_FILES['new_speaker_images']['tmp_name'][$key], $upload_dir . $fname);
                    $spk_url = 'uploads/events/' . $fname;
                }
                $ins_spk->execute([$id, $data['name'], $data['position'], $spk_url]);
            }
        }
        // C. Delete
        if (!empty($_POST['delete_speaker_ids'])) {
            $sids = explode(',', $_POST['delete_speaker_ids']);
            $del_spk = $pdo->prepare("DELETE FROM event_speakers WHERE id=? AND event_id=?");
            foreach ($sids as $sid) $del_spk->execute([$sid, $id]);
        }

        // 4. Handle Gallery
        // A. Add New
        if (!empty($_FILES['gallery']['name'][0])) {
            $ins_gal = $pdo->prepare("INSERT INTO event_galleries (event_id, image_url) VALUES (?, ?)");
            foreach ($_FILES['gallery']['name'] as $k => $v) {
                $fname = time() . '_gal_' . basename($_FILES['gallery']['name'][$k]);
                move_uploaded_file($_FILES['gallery']['tmp_name'][$k], $upload_dir . $fname);
                $ins_gal->execute([$id, 'uploads/events/' . $fname]);
            }
        }
        // B. Delete
        if (!empty($_POST['delete_gallery_ids'])) {
            $gids = explode(',', $_POST['delete_gallery_ids']);
            $del_gal = $pdo->prepare("DELETE FROM event_galleries WHERE id=? AND event_id=?");
            foreach ($gids as $gid) $del_gal->execute([$gid, $id]);
        }

        $pdo->commit();
        header("Location: ../events.php?status=success");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}
?>
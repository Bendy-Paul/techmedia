<?php
session_start();
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && verify_csrf_token()) {
    
    // Basic Data
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
    
    // JSON Data
    $social_links = json_encode($_POST['social'] ?? []);
    $community_links = json_encode($_POST['community'] ?? []);

    // Slug
    function create_slug($string) {
        $string = strtolower(trim($string));
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);
        return rtrim($string, '-');
    }
    $slug = create_slug($title) . '-' . rand(100, 999);

    // Banner Upload
    $event_upload_dir = '../../uploads/events/';
    if (!is_dir($event_upload_dir)) mkdir($event_upload_dir, 0777, true);
    
    $banner_url = null;
    if (!empty($_FILES['banner_image_upload']['name'])) {
        $file_name = time() . '_' . basename($_FILES['banner_image_upload']['name']);
        if (move_uploaded_file($_FILES['banner_image_upload']['tmp_name'], $event_upload_dir . $file_name)) {
            $banner_url = 'uploads/events/' . $file_name;
        }
    }

    try {
        $pdo->beginTransaction();

        // 1. Insert Event
        $sql = "INSERT INTO events (
            title, slug, description, event_type, start_datetime, end_datetime, 
            organizer_company_id, location_name, city_id, is_virtual, price, ticket_url, 
            is_featured, banner_image_url, social_links, community_links
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $title, $slug, $desc, $type, $start, $end, 
            $organizer, $loc_name, $city_id, $is_virtual, $price, $ticket_url, 
            $is_featured, $banner_url, $social_links, $community_links
        ]);
        $event_id = $pdo->lastInsertId();

        // 2. Handle Tags (New & Existing)
        // Similar logic to articles - create if not exists
        if (!empty($_POST['new_tags'])) {
            $new_tags = explode(',', $_POST['new_tags']);
            $check_tag = $pdo->prepare("SELECT id FROM tags WHERE name = ?");
            $insert_tag = $pdo->prepare("INSERT INTO tags (name) VALUES (?)");
            $link_tag = $pdo->prepare("INSERT INTO event_tags (event_id, tag_id) VALUES (?, ?)");

            foreach ($new_tags as $tag_name) {
                $tag_name = trim($tag_name);
                if(empty($tag_name)) continue;
                $check_tag->execute([$tag_name]);
                $existing = $check_tag->fetch();
                if ($existing) $tag_id = $existing['id'];
                else { $insert_tag->execute([$tag_name]); $tag_id = $pdo->lastInsertId(); }
                try { $link_tag->execute([$event_id, $tag_id]); } catch(Exception $e){}
            }
        }
        // Link Existing Tags
        if (!empty($_POST['tags'])) {
            $link_tag = $pdo->prepare("INSERT INTO event_tags (event_id, tag_id) VALUES (?, ?)");
            foreach ($_POST['tags'] as $tid) {
                $link_tag->execute([$event_id, $tid]);
            }
        }

        // 3. Handle Speakers
        if (!empty($_POST['speakers'])) {
            $spk_stmt = $pdo->prepare("INSERT INTO event_speakers (event_id, name, position, image_url) VALUES (?, ?, ?, ?)");
            
            foreach ($_POST['speakers'] as $key => $spk_data) {
                if(empty($spk_data['name'])) continue;
                
                $spk_img_url = null;
                if (!empty($_FILES['speaker_images']['name'][$key])) {
                    $fname = time() . '_spk_' . basename($_FILES['speaker_images']['name'][$key]);
                    if (move_uploaded_file($_FILES['speaker_images']['tmp_name'][$key], $event_upload_dir . $fname)) {
                        $spk_img_url = 'uploads/events/' . $fname;
                    }
                }
                
                $spk_stmt->execute([$event_id, $spk_data['name'], $spk_data['position'], $spk_img_url]);
            }
        }

        // 4. Handle Gallery
        if (!empty($_FILES['gallery']['name'][0])) {
            $gal_stmt = $pdo->prepare("INSERT INTO event_galleries (event_id, image_url) VALUES (?, ?)");
            foreach ($_FILES['gallery']['name'] as $key => $val) {
                $fname = time() . '_gal_' . basename($_FILES['gallery']['name'][$key]);
                if (move_uploaded_file($_FILES['gallery']['tmp_name'][$key], $event_upload_dir . $fname)) {
                    $gal_stmt->execute([$event_id, 'uploads/events/' . $fname]);
                }
            }
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
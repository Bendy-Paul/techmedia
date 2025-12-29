<?php
require 'includes/db.php';
require 'includes/header.php';
require 'includes/sidebar.php';

if (!isset($_GET['id'])) die("Invalid ID");
$id = (int)$_GET['id'];

// Fetch Event
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();
if (!$event) die("Event not found");

// Fetch Related Data
$speakers = $pdo->query("SELECT * FROM event_speakers WHERE event_id = $id")->fetchAll();
$gallery = $pdo->query("SELECT * FROM event_galleries WHERE event_id = $id")->fetchAll();
$social = json_decode($event['social_links'] ?? '{}', true);
$community = json_decode($event['community_links'] ?? '{}', true);

// Fetch Tags (Selected & All)
$sel_tags = $pdo->query("SELECT tag_id FROM event_tags WHERE event_id = $id")->fetchAll(PDO::FETCH_COLUMN);
$all_tags = $pdo->query("SELECT * FROM tags ORDER BY name ASC")->fetchAll();

$cities = $pdo->query("SELECT * FROM cities ORDER BY name ASC")->fetchAll();
$companies = $pdo->query("SELECT id, name FROM companies ORDER BY name ASC")->fetchAll();
?>

<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Edit Event</h2>
            <p class="text-muted mb-0">Updating: <?= htmlspecialchars($event['title']) ?></p>
        </div>
        <a href="events.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Back</a>
    </div>

    <form action="actions/update-event.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $event['id'] ?>">
        
        <div class="row">
            <!-- Left Column -->
            <div class="col-md-8">
                <!-- Basic Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Title</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($event['title']) ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Type</label>
                                <select name="event_type" class="form-select">
                                    <?php $types=['conference','meetup','hackathon','workshop','webinar'];
                                    foreach($types as $t): ?>
                                        <option value="<?=$t?>" <?= $event['event_type']==$t?'selected':'' ?>><?=ucfirst($t)?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Organizer</label>
                                <select name="organizer_company_id" class="form-select">
                                    <option value="">-- Third Party --</option>
                                    <?php foreach($companies as $comp): ?>
                                        <option value="<?=$comp['id']?>" <?= $event['organizer_company_id']==$comp['id']?'selected':'' ?>>
                                            <?= htmlspecialchars($comp['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Description</label>
                            <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($event['description']) ?></textarea>
                        </div>
                        
                        <!-- Tags -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Tags</label>
                            <input type="text" name="new_tags" class="form-control mb-2" placeholder="Add NEW tags (comma separated)">
                            <div class="border rounded p-2 d-flex flex-wrap gap-2" style="max-height: 100px; overflow-y: auto;">
                                <?php foreach($all_tags as $tag): ?>
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input" type="checkbox" name="tags[]" value="<?=$tag['id']?>" <?= in_array($tag['id'], $sel_tags)?'checked':'' ?>>
                                        <label class="form-check-label small">#<?= htmlspecialchars($tag['name']) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Speakers Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">Speakers</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSpeaker()">+ Add Speaker</button>
                    </div>
                    <div class="card-body" id="speakers_container">
                        <!-- Existing Speakers -->
                        <?php foreach($speakers as $spk): ?>
                        <div class="border rounded p-3 mb-3 position-relative" id="ex_spk_<?= $spk['id'] ?>">
                            <input type="hidden" name="existing_speakers[<?= $spk['id'] ?>][id]" value="<?= $spk['id'] ?>">
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="deleteSpeaker(<?= $spk['id'] ?>)"></button>
                            <div class="row g-2 align-items-center">
                                <div class="col-md-2">
                                    <?php if($spk['image_url']): ?>
                                        <img src="../<?= htmlspecialchars($spk['image_url']) ?>" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width:50px; height:50px"><i class="fas fa-user"></i></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" name="existing_speakers[<?= $spk['id'] ?>][name]" class="form-control form-control-sm mb-1 fw-bold" value="<?= htmlspecialchars($spk['name']) ?>">
                                    <input type="text" name="existing_speakers[<?= $spk['id'] ?>][position]" class="form-control form-control-sm" value="<?= htmlspecialchars($spk['position']) ?>">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <input type="hidden" name="delete_speaker_ids" id="delete_speaker_ids">
                        <!-- New speakers inserted here via JS -->
                    </div>
                </div>

                <!-- Gallery Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Event Gallery</h6></div>
                    <div class="card-body">
                        <!-- Existing -->
                        <?php if($gallery): ?>
                        <div class="row g-2 mb-3">
                            <?php foreach($gallery as $img): ?>
                            <div class="col-3 position-relative" id="gal_<?= $img['id'] ?>">
                                <img src="../<?= htmlspecialchars($img['image_url']) ?>" class="img-thumbnail" style="height: 80px; width: 100%; object-fit:cover;">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-0 px-1" onclick="deleteGallery(<?= $img['id'] ?>)">&times;</button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="delete_gallery_ids" id="delete_gallery_ids">
                        <?php endif; ?>
                        
                        <label class="small text-muted fw-bold">Upload New</label>
                        <input type="file" name="gallery[]" class="form-control" multiple accept="image/*">
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                <!-- Logistics -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Logistics</h6></div>
                    <div class="card-body">
                        <!-- Dates -->
                        <div class="mb-2">
                            <label class="small text-muted">Start</label>
                            <input type="datetime-local" name="start_datetime" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($event['start_datetime'])) ?>">
                        </div>
                        <div class="mb-2">
                            <label class="small text-muted">End</label>
                            <input type="datetime-local" name="end_datetime" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($event['end_datetime'])) ?>">
                        </div>
                        
                        <!-- Location -->
                        <div class="form-check form-switch my-3">
                            <input class="form-check-input" type="checkbox" name="is_virtual" id="virtualCheck" value="1" <?= $event['is_virtual']?'checked':'' ?>>
                            <label class="form-check-label">Virtual</label>
                        </div>
                        <div class="mb-2">
                            <input type="text" name="location_name" class="form-control" value="<?= htmlspecialchars($event['location_name']) ?>" placeholder="Venue">
                        </div>
                        <div class="mb-2">
                            <select name="city_id" class="form-select">
                                <option value="">City...</option>
                                <?php foreach($cities as $c): ?>
                                    <option value="<?=$c['id']?>" <?= $event['city_id']==$c['id']?'selected':'' ?>><?= $c['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Socials & Community -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Links</h6></div>
                    <div class="card-body">
                        <h6 class="small fw-bold text-muted mt-2">Social Media</h6>
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text"><i class="fab fa-linkedin"></i></span>
                            <input type="text" name="social[linkedin]" class="form-control" value="<?= htmlspecialchars($social['linkedin']??'') ?>" placeholder="LinkedIn URL">
                        </div>
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                            <input type="text" name="social[twitter]" class="form-control" value="<?= htmlspecialchars($social['twitter']??'') ?>" placeholder="X/Twitter URL">
                        </div>
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                            <input type="text" name="social[facebook]" class="form-control" value="<?= htmlspecialchars($social['facebook']??'') ?>" placeholder="Facebook URL">
                        </div>
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                            <input type="text" name="social[instagram]" class="form-control" value="<?= htmlspecialchars($social['instagram']??'') ?>" placeholder="Instagram URL">
                        </div>

                        <h6 class="small fw-bold text-muted mt-3">Community Links</h6>
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                            <input type="text" name="community[whatsapp]" class="form-control" value="<?= htmlspecialchars($community['whatsapp']??'') ?>" placeholder="WhatsApp Group">
                        </div>
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text"><i class="fab fa-telegram"></i></span>
                            <input type="text" name="community[telegram]" class="form-control" value="<?= htmlspecialchars($community['telegram']??'') ?>" placeholder="Telegram Channel">
                        </div>
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="text" name="community[newsletter]" class="form-control" value="<?= htmlspecialchars($community['newsletter']??'') ?>" placeholder="Newsletter URL">
                        </div>
                    </div>
                </div>

                <!-- Banner & Save -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">Tickets & Banner</h6></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small text-muted">Price (NGN)</label>
                            <input type="number" name="price" class="form-control" value="<?= htmlspecialchars($event['price']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted">Ticket Link</label>
                            <input type="url" name="ticket_url" class="form-control" value="<?= htmlspecialchars($event['ticket_url']) ?>" placeholder="https://">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted">Banner Image</label>
                            <?php if($event['banner_image_url']): ?>
                                <img src="../<?= htmlspecialchars($event['banner_image_url']) ?>" class="img-fluid rounded mb-2">
                            <?php endif; ?>
                            <input type="file" name="banner_image_upload" class="form-control form-control-sm">
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" <?= $event['is_featured'] ? 'checked' : '' ?>>
                            <label class="form-check-label">Feature Event</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<script>
    let newSpeakerCount = 0;
    
    function addSpeaker() {
        const container = document.getElementById('speakers_container');
        const html = `
            <div class="border rounded p-3 mb-3 position-relative new-speaker-row" id="new_spk_${newSpeakerCount}">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="removeNewSpeaker(${newSpeakerCount})"></button>
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="file" name="new_speaker_images[${newSpeakerCount}]" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-9">
                        <input type="text" name="new_speakers[${newSpeakerCount}][name]" class="form-control form-control-sm mb-2" placeholder="Name">
                        <input type="text" name="new_speakers[${newSpeakerCount}][position]" class="form-control form-control-sm" placeholder="Role">
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        newSpeakerCount++;
    }

    function removeNewSpeaker(id) { document.getElementById('new_spk_' + id).remove(); }

    function deleteSpeaker(id) {
        if(confirm('Remove speaker?')) {
            document.getElementById('ex_spk_' + id).style.display = 'none';
            let input = document.getElementById('delete_speaker_ids');
            let ids = input.value ? input.value.split(',') : [];
            ids.push(id);
            input.value = ids.join(',');
        }
    }

    function deleteGallery(id) {
        if(confirm('Delete image?')) {
            document.getElementById('gal_' + id).style.display = 'none';
            let input = document.getElementById('delete_gallery_ids');
            let ids = input.value ? input.value.split(',') : [];
            ids.push(id);
            input.value = ids.join(',');
        }
    }
</script>
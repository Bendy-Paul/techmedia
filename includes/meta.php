<?php
// Default meta values
$meta_title = isset($meta_title) ? $meta_title : 'Tech Media Directory | Nigeria\'s Tech Ecosystem';
$meta_description = isset($meta_description) ? $meta_description : 'Discover companies, jobs, news, and events in Nigeria\'s tech space.';
$meta_image = isset($meta_image) ? $meta_image : 'https://techmedia.ng/assets/images/og-image.jpg';
$meta_url = isset($meta_url) ? $meta_url : (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'https') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<!-- SEO Meta Tags -->
<title><?= htmlspecialchars($meta_title) ?></title>
<meta name="description" content="<?= htmlspecialchars($meta_description) ?>">
<meta name="keywords" content="tech, nigeria, companies, jobs, events, news, directory">
<meta name="author" content="Tech Media Directory">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:title" content="<?= htmlspecialchars($meta_title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($meta_description) ?>">
<meta property="og:image" content="<?= htmlspecialchars($meta_image) ?>">
<meta property="og:url" content="<?= htmlspecialchars($meta_url) ?>">
<meta property="og:site_name" content="Tech Media Directory">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars($meta_title) ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($meta_description) ?>">
<meta name="twitter:image" content="<?= htmlspecialchars($meta_image) ?>">
<meta name="twitter:site" content="@techmedia">

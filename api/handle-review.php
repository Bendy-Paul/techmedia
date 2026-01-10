<?php
session_start();
require '../includes/wertyuytre.php';
require '../includes/Review-Manager.php';

// Check Auth
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$manager = new ReviewManager($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $redirect = $_SERVER['HTTP_REFERER'] ?? '../index.php';

    if ($action === 'submit') {
        $item_type = $_POST['item_type'];
        $item_id = (int)$_POST['item_id'];
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);

        $result = $manager->addReview($user_id, $item_type, $item_id, $rating, $comment);
        // Set flash message (you can implement a simple session flash system later)
        // $_SESSION['flash'] = $result['message'];
    } 
    elseif ($action === 'edit') {
        $review_id = (int)$_POST['review_id'];
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);
        
        $result = $manager->updateReview($review_id, $user_id, $rating, $comment);
    }
    elseif ($action === 'delete') {
        $review_id = (int)$_POST['review_id'];
        $result = $manager->deleteReview($review_id, $user_id);
    }

    header("Location: $redirect");
    exit();
}
?>
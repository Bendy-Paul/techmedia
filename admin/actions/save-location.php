<?php
session_start();
require '../includes/db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && verify_csrf_token()) {
    
    $type = $_POST['type'] ?? '';
    
    try {
        if ($type === 'country') {
            // Validate
            $name = trim($_POST['name']);
            $sortname = strtoupper(trim($_POST['sortname']));
            $phonecode = (int)$_POST['phonecode'];

            if (!empty($name) && !empty($sortname)) {
                $stmt = $pdo->prepare("INSERT INTO countries (sortname, name, phonecode) VALUES (?, ?, ?)");
                $stmt->execute([$sortname, $name, $phonecode]);
            }

        } elseif ($type === 'state') {
            // Validate
            $name = trim($_POST['name']);
            $code = trim($_POST['statecode']);
            $country_id = (int)$_POST['country_id'];

            if (!empty($name) && $country_id > 0) {
                $stmt = $pdo->prepare("INSERT INTO states (name, state_code, country_id) VALUES (?, ?, ?)");
                $stmt->execute([$name, $code, $country_id]);
            }

        } elseif ($type === 'city') {
            // Validate
            $name = trim($_POST['name']);
            $code = trim($_POST['citycode']);
            $state_id = (int)$_POST['state_id'];

            if (!empty($name) && $state_id > 0) {
                $stmt = $pdo->prepare("INSERT INTO cities (name, city_code, state_id) VALUES (?, ?, ?)");
                $stmt->execute([$name, $code, $state_id]);
            }
        }

        // Redirect back with success
        header("Location: ../locations.php?status=success");
        exit();

    } catch (PDOException $e) {
        // Redirect back with error (In production, log the error)
        header("Location: ../locations.php?status=error&msg=" . urlencode($e->getMessage()));
        exit();
    }

} else {
    header("Location: ../locations.php");
    exit();
}
?>
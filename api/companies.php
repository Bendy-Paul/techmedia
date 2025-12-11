<?php
// api/companies.php
header('Content-Type: application/json');
require_once '../includes/wertyuytre.php';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = isset($_GET['per_page']) ? max(1, intval($_GET['per_page'])) : 12;
$offset = ($page - 1) * $perPage;

$category = isset($_GET['category']) ? trim($_GET['category']) : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : null;
$state= isset($_GET['location']) ? trim($_GET['location']) : null;
$verified = isset($_GET['verified']) ? trim($_GET['verified']) : null;
$price_range = isset($_GET['price_range']) ? trim($_GET['price_range']) : null;
$team_size = isset($_GET['team_size']) ? trim($_GET['team_size']) : null;

$where = [];
$params = [];

if ($category) {
    $where[] = 'c.id = ?';
    $params[] = $category;
}
if ($state) {
    $where[] = 'c.state_id = ?';
    $params[] = $state;
}
if ($verified) {
    if ($verified === '1') {
        $where[] = 'c.is_verified = 1';
    } elseif ($verified === '0') {
        $where[] = 'c.is_verified = 0';
    }
}
if ($price_range) {
    if ($price_range === '500000') {
        $where[] = 'c.price_range < 500000';
    } elseif ($price_range === '2000000') {
        $where[] = 'c.price_range BETWEEN 500000 AND 2000000';
    } elseif ($price_range === '5000000') {
        $where[] = 'c.price_range BETWEEN 2000000 AND 5000000';
    } elseif ($price_range === '10000000') {
        $where[] = 'c.price_range > 5000000';
    }
}
if ($team_size) {
    if ($team_size === '1-10') {
        $where[] = 'c.team_size BETWEEN 1 AND 10';
    } elseif ($team_size === '11-50') {
        $where[] = 'c.team_size BETWEEN 11 AND 50';
    } elseif ($team_size === '51-200') {
        $where[] = 'c.team_size BETWEEN 51 AND 200';
    } elseif ($team_size === '200+') {
        $where[] = 'c.team_size > 200';
    }
}


if ($search) {
    $where[] = '(c.name LIKE ? OR c.tagline LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// Get total count
$countSql = "SELECT COUNT(*) FROM companies c $whereSql";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = $stmt->fetchColumn();

// Get paginated companies
$sql = "SELECT c.*, cat.name as category_name FROM companies c LEFT JOIN categories cat ON c.id = cat.id $whereSql ORDER BY c.name ASC LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return JSON
$response = [
    'companies' => $companies,
    'total' => (int)$total,
    'page' => $page,
    'per_page' => $perPage,
    'total_pages' => ceil($total / $perPage)
];
echo json_encode($response);
<?php
// api/events.php
header('Content-Type: application/json');
require_once '../includes/wertyuytre.php';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = isset($_GET['per_page']) ? max(1, intval($_GET['per_page'])) : 9;
$offset = ($page - 1) * $perPage;

$location = isset($_GET['location']) ? trim($_GET['location']) : null;
$ticket = isset($_GET['ticket']) ? trim($_GET['ticket']) : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : null;

$where = [];
$params = [];

if ($location) {
    $where[] = 'e.location_name = ?';
    $params[] = $location;
}
if ($ticket) {
    if ($ticket === 'free') {
        $where[] = 'e.price = 0';
    } elseif ($ticket === 'paid') {
        $where[] = 'e.price > 0';
    }
}
if ($search) {
    $where[] = '(e.title LIKE ? OR e.description LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// Get total count
$countSql = "SELECT COUNT(*) FROM events e $whereSql";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = $stmt->fetchColumn();

// Get paginated events
$sql = "SELECT e.*, c.name as city_name FROM events e LEFT JOIN cities c ON e.city_id = c.id $whereSql ORDER BY e.start_datetime DESC LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$response = [
    'events' => $events,
    'total' => (int)$total,
    'page' => $page,
    'per_page' => $perPage,
    'total_pages' => ceil($total / $perPage)
];
echo json_encode($response);
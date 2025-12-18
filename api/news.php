<?php
// api/news.php
header('Content-Type: application/json');
require_once '../includes/wertyuytre.php';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = isset($_GET['per_page']) ? max(1, intval($_GET['per_page'])) : 10;
$offset = ($page - 1) * $perPage;

$category = isset($_GET['category']) ? trim($_GET['category']) : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : null;

$where = [];
$params = [];

if ($category) {
    $where[] = 'c.id = ?';
    $params[] = $category;
}
if ($search) {
    $where[] = '(n.title LIKE ? OR n.content LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// Get total count
$countSql = "SELECT COUNT(*) FROM articles n LEFT JOIN article_categories cat ON n.id = cat.article_id LEFT JOIN categories c ON c.id = cat.category_id $whereSql";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = $stmt->fetchColumn();

// Get paginated news
$sql = "SELECT n.*, c.name as category_name FROM articles n LEFT JOIN article_categories cat ON n.id = cat.article_id LEFT JOIN categories c ON c.id = cat.category_id $whereSql ORDER BY n.published_at DESC LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);

$response = [
    'news' => $news,
    'total' => (int)$total,
    'page' => $page,
    'per_page' => $perPage,
    'total_pages' => ceil($total / $perPage)
];
echo json_encode($response);
<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'auth'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method'], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = file_get_contents('php://input');
$data = is_string($raw) ? json_decode($raw, true) : null;
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'json'], JSON_UNESCAPED_UNICODE);
    exit;
}

$token = $data['csrf_token'] ?? '';
if (!checkCsrf(is_string($token) ? $token : null)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'csrf'], JSON_UNESCAPED_UNICODE);
    exit;
}

$slug = isset($data['section']) && is_string($data['section']) ? trim($data['section']) : '';
$percent = isset($data['percent']) ? (int)$data['percent'] : -1;

if ($slug === '' || $percent < 0 || $percent > 100) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'params'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!getSectionConfig($slug)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'section'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    upsertReadingProgress((int)currentUser()['id'], $slug, $percent);
    echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'db'], JSON_UNESCAPED_UNICODE);
}

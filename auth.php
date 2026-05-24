<?php
declare(strict_types=1);

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function isAdmin(): bool
{
    $user = currentUser();
    return $user !== null && ($user['role'] ?? '') === 'admin';
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function checkCsrf(?string $token): bool
{
    return is_string($token) && hash_equals(csrfToken(), $token);
}

function loginUser(string $username, string $password): bool
{
    $username = trim($username);
    if ($username === '' || $password === '') {
        return false;
    }

    $stmt = getDb()->prepare('SELECT id, username, role, password_hash FROM users WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if (!$user) {
        return false;
    }

    $passwordHash = hashPassword($password);
    if (!hash_equals($user['password_hash'], $passwordHash)) {
        return false;
    }

    $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'username' => $user['username'],
        'role' => $user['role'],
    ];

    return true;
}

function hashPassword(string $password): string
{
    return hash('sha256', $password);
}

function registerUser(string $username, string $password): array
{
    $username = trim($username);
    if ($username === '' || $password === '') {
        return ['ok' => false, 'message' => 'Заполните логин и пароль.'];
    }

    if (!preg_match('/^[a-zA-Z0-9_]{3,32}$/', $username)) {
        return ['ok' => false, 'message' => 'Логин: 3-32 символа, только буквы, цифры и _.'];
    }

    if (strlen($password) < 6) {
        return ['ok' => false, 'message' => 'Пароль должен быть не короче 6 символов.'];
    }

    try {
        $stmt = getDb()->prepare('INSERT INTO users (username, password_hash, role) VALUES (:username, :password_hash, :role)');
        $stmt->execute([
            'username' => $username,
            'password_hash' => hashPassword($password),
            'role' => 'user',
        ]);

        return ['ok' => true, 'message' => 'Регистрация выполнена. Теперь войдите в систему.'];
    } catch (PDOException $e) {
        return ['ok' => false, 'message' => 'Такой логин уже существует.'];
    }
}

function createUserByAdmin(string $username, string $password, string $role): array
{
    $role = $role === 'admin' ? 'admin' : 'user';
    $result = registerUser($username, $password);
    if (!($result['ok'] ?? false)) {
        return $result;
    }

    if ($role === 'admin') {
        $update = getDb()->prepare('UPDATE users SET role = :role WHERE username = :username');
        $update->execute([
            'role' => 'admin',
            'username' => trim($username),
        ]);
    }

    return ['ok' => true, 'message' => 'Пользователь создан.'];
}

function updateUserRole(int $userId, string $role): bool
{
    $role = $role === 'admin' ? 'admin' : 'user';
    $stmt = getDb()->prepare('UPDATE users SET role = :role WHERE id = :id');
    return $stmt->execute(['role' => $role, 'id' => $userId]);
}

function updateUserPassword(int $userId, string $password): array
{
    if (strlen($password) < 6) {
        return ['ok' => false, 'message' => 'Пароль должен быть не короче 6 символов.'];
    }

    $stmt = getDb()->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
    $ok = $stmt->execute([
        'password_hash' => hashPassword($password),
        'id' => $userId,
    ]);

    return $ok
        ? ['ok' => true, 'message' => 'Пароль обновлен.']
        : ['ok' => false, 'message' => 'Не удалось обновить пароль.'];
}

function getAllUsers(): array
{
    $stmt = getDb()->query('SELECT id, username, role, created_at FROM users ORDER BY id ASC');
    return $stmt->fetchAll();
}

function logoutUser(): void
{
    unset($_SESSION['user']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        $script = basename($_SERVER['SCRIPT_NAME'] ?? '');
        if ($script === '' || !preg_match('/^[a-zA-Z0-9._-]+\\.php$/', $script)) {
            header('Location: login.php');
            exit;
        }
        $qs = $_SERVER['QUERY_STRING'] ?? '';
        $suffix = (is_string($qs) && $qs !== '') ? '?' . $qs : '';
        $target = safeRedirectTarget($script . $suffix);
        if ($target === 'index.php') {
            header('Location: login.php');
            exit;
        }
        header('Location: login.php?next=' . rawurlencode($target));
        exit;
    }
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Разрешённый локальный редирект после входа (без внешних URL).
 */
function safeRedirectTarget(string $next): string
{
    $next = trim($next);
    if ($next === '') {
        return 'index.php';
    }
    if (strpos($next, "\0") !== false || strpos($next, '..') !== false) {
        return 'index.php';
    }

    $parts = parse_url($next);
    if (isset($parts['scheme']) || isset($parts['host'])) {
        return 'index.php';
    }

    $path = $parts['path'] ?? $next;
    $file = basename($path);
    $allowed = [
        'osnovy.php',
        'algoritmy.php',
        'programmirovanie.php',
        'seti.php',
        'apparat.php',
        'bezopasnost.php',
        'search.php',
        'cabinet.php',
        'admin.php',
        'index.php',
    ];
    if (!in_array($file, $allowed, true)) {
        return 'index.php';
    }

    $query = isset($parts['query']) && is_string($parts['query']) ? '?' . $parts['query'] : '';

    return $file . $query;
}

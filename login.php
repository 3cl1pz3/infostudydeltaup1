<?php
$current_page = 'login';
$page_title = 'Вход';
require_once __DIR__ . '/includes/bootstrap.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$next = safeRedirectTarget((string)($_GET['next'] ?? ''));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkCsrf($_POST['csrf_token'] ?? null)) {
        $error = 'Неверный CSRF-токен. Обновите страницу.';
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $next = safeRedirectTarget((string)($_POST['next'] ?? $_GET['next'] ?? ''));
        if (loginUser($username, $password)) {
            header('Location: ' . $next);
            exit;
        }
        $error = 'Неверный логин или пароль.';
    }
}

include 'includes/header.php';
?>

<div class="content-block auth-card">
  <h1 class="page-title">Вход в систему</h1>
  <p>После входа вам будут доступны разделы энциклопедии, поиск и личный кабинет с прогрессом чтения. Администратор может редактировать разделы.</p>
  <p>Нет аккаунта? <a href="register.php<?php echo $next !== 'index.php' ? '?next=' . rawurlencode($next) : ''; ?>">Зарегистрируйтесь</a>.</p>
  <?php if ($error !== ''): ?>
    <p class="form-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
  <?php endif; ?>
  <form method="post" class="auth-form">
    <input type="hidden" name="next" value="<?php echo htmlspecialchars($next, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
    <label>Логин</label>
    <input type="text" name="username" required>
    <label>Пароль</label>
    <input type="password" name="password" required>
    <button type="submit" class="nav-btn">Войти</button>
  </form>
</div>

<?php include 'includes/footer.php'; ?>

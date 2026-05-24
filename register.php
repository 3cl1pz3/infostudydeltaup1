<?php
$current_page = 'register';
$page_title = 'Регистрация';
require_once __DIR__ . '/includes/bootstrap.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$next = safeRedirectTarget((string)($_GET['next'] ?? ''));
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkCsrf($_POST['csrf_token'] ?? null)) {
        $error = 'Неверный CSRF-токен. Обновите страницу.';
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordRepeat = $_POST['password_repeat'] ?? '';

        if ($password !== $passwordRepeat) {
            $error = 'Пароли не совпадают.';
        } else {
            $result = registerUser($username, $password);
            if ($result['ok']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="content-block auth-card">
  <h1 class="page-title">Регистрация</h1>
  <p>После регистрации вы получите доступ к разделам, поиску и личному кабинету с прогрессом чтения.</p>
  <?php if ($success === ''): ?>
    <p>Уже есть аккаунт? <a href="login.php<?php echo $next !== 'index.php' ? '?next=' . rawurlencode($next) : ''; ?>">Войти</a>.</p>
  <?php endif; ?>
  <?php if ($success !== ''): ?>
    <p class="form-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
    <p><a class="nav-btn" href="login.php<?php echo $next !== 'index.php' ? '?next=' . rawurlencode($next) : ''; ?>">Перейти ко входу</a></p>
  <?php endif; ?>
  <?php if ($error !== ''): ?>
    <p class="form-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
  <?php endif; ?>
  <form method="post" class="auth-form">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
    <label>Логин</label>
    <input type="text" name="username" required>
    <label>Пароль</label>
    <input type="password" name="password" required>
    <label>Повторите пароль</label>
    <input type="password" name="password_repeat" required>
    <button type="submit" class="nav-btn">Зарегистрироваться</button>
  </form>
</div>

<?php include 'includes/footer.php'; ?>

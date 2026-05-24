<?php
$current_page = 'admin';
$page_title = 'Админ-панель';
require_once __DIR__ . '/includes/bootstrap.php';
requireAdmin();

$sections = sectionConfigs();
$selected = $_GET['section'] ?? 'osnovy';
if (!isset($sections[$selected])) {
    $selected = 'osnovy';
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkCsrf($_POST['csrf_token'] ?? null)) {
        $error = 'Неверный CSRF-токен. Обновите страницу.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'save_section') {
            $selected = $_POST['section'] ?? $selected;
            if (!isset($sections[$selected])) {
                $selected = 'osnovy';
            }
            $content = $_POST['content_html'] ?? '';
            upsertSectionContent($selected, $sections[$selected]['display_title'], $content);
            $message = 'Изменения раздела сохранены.';
        } elseif ($action === 'create_user') {
            $username = $_POST['new_username'] ?? '';
            $password = $_POST['new_password'] ?? '';
            $role = $_POST['new_role'] ?? 'user';
            $result = createUserByAdmin($username, $password, $role);
            if ($result['ok']) {
                $message = $result['message'];
            } else {
                $error = $result['message'];
            }
        } elseif ($action === 'update_role') {
            $userId = (int)($_POST['user_id'] ?? 0);
            $role = $_POST['role'] ?? 'user';
            if ($userId > 0) {
                updateUserRole($userId, $role);
                $message = 'Роль пользователя обновлена.';
            }
        } elseif ($action === 'update_password') {
            $userId = (int)($_POST['user_id'] ?? 0);
            $newPassword = $_POST['set_password'] ?? '';
            if ($userId > 0) {
                $result = updateUserPassword($userId, $newPassword);
                if ($result['ok']) {
                    $message = $result['message'];
                } else {
                    $error = $result['message'];
                }
            }
        }
    }
}

$currentContent = getSectionContent($selected);
$users = getAllUsers();
$currentUserId = (int)(currentUser()['id'] ?? 0);
include 'includes/header.php';
?>

<div class="content-block">
  <h1 class="page-title">Админ-панель</h1>
  <p>Здесь можно редактировать разделы в визуальном редакторе и управлять пользователями.</p>
  <?php if ($message !== ''): ?>
    <p class="form-success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
  <?php endif; ?>
  <?php if ($error !== ''): ?>
    <p class="form-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
  <?php endif; ?>

  <form method="get" class="admin-switch-form">
    <label for="section">Раздел</label>
    <select id="section" name="section" onchange="this.form.submit()">
      <?php foreach ($sections as $slug => $data): ?>
        <option value="<?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selected === $slug ? 'selected' : ''; ?>>
          <?php echo htmlspecialchars($data['display_title'], ENT_QUOTES, 'UTF-8'); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </form>

  <form method="post" class="admin-editor-form">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="action" value="save_section">
    <input type="hidden" name="section" value="<?php echo htmlspecialchars($selected, ENT_QUOTES, 'UTF-8'); ?>">
    <label for="content_html">Контент раздела</label>
    <p class="admin-editor-hint">Редактор визуальный: заголовки, списки, ссылки и таблицы. При необходимости правки кода включите режим «Исходный код» на панели.</p>
    <textarea id="content_html" name="content_html" rows="24"><?php echo htmlspecialchars($currentContent, ENT_QUOTES, 'UTF-8'); ?></textarea>
    <button type="submit" class="nav-btn">Сохранить изменения</button>
  </form>
</div>

<div class="content-block">
  <h2>Управление пользователями</h2>

  <form method="post" class="admin-user-create-form">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="action" value="create_user">
    <label>Логин нового пользователя</label>
    <input type="text" name="new_username" required>
    <label>Пароль</label>
    <input type="password" name="new_password" required>
    <label>Роль</label>
    <select name="new_role">
      <option value="user">user</option>
      <option value="admin">admin</option>
    </select>
    <button type="submit" class="nav-btn">Создать пользователя</button>
  </form>

  <div class="user-list">
    <?php foreach ($users as $user): ?>
      <div class="user-card">
        <div class="user-card-head">
          <strong><?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></strong>
          <span>ID: <?php echo (int)$user['id']; ?></span>
          <span>Создан: <?php echo htmlspecialchars((string)$user['created_at'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>

        <form method="post" class="user-inline-form">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="action" value="update_role">
          <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
          <label>Роль</label>
          <select name="role">
            <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>user</option>
            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>admin</option>
          </select>
          <button type="submit" class="nav-btn">Сменить роль</button>
        </form>

        <form method="post" class="user-inline-form">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="action" value="update_password">
          <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
          <label>Новый пароль</label>
          <input type="password" name="set_password" minlength="6" required>
          <button type="submit" class="nav-btn">Сменить пароль</button>
        </form>

        <?php if ((int)$user['id'] === $currentUserId): ?>
          <small>Вы вошли под этим пользователем.</small>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.4/tinymce.min.js" referrerpolicy="origin"></script>
<script>
(function () {
  tinymce.init({
    selector: '#content_html',
    height: 520,
    menubar: true,
    language: 'ru',
    language_url: 'https://cdn.jsdelivr.net/npm/tinymce@6.8.4/langs/ru.js',
    plugins: 'lists link image table autoresize code fullscreen help wordcount',
    toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist outdent indent | link image table | removeformat | code fullscreen | help',
    branding: false,
    resize: true,
    content_style: 'body { font-family: Segoe UI, system-ui, sans-serif; font-size: 16px; line-height: 1.6; }',
    setup: function (editor) {
      editor.on('change input undo redo', function () {
        editor.save();
      });
    }
  });
  document.querySelector('.admin-editor-form').addEventListener('submit', function () {
    if (window.tinymce && tinymce.get('content_html')) {
      tinymce.triggerSave();
    }
  });
})();
</script>
<?php include 'includes/footer.php'; ?>

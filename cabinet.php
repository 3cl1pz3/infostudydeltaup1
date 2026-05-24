<?php
$current_page = 'cabinet';
$page_title = 'Личный кабинет';
require_once __DIR__ . '/includes/bootstrap.php';

$user = currentUser();
$uid = (int)($user['id'] ?? 0);
$progressMap = getUserReadingProgressMap($uid);
$sections = sectionConfigs();

include 'includes/header.php';
?>

<div class="content-block cabinet-intro">
  <h1 class="page-title">Личный кабинет</h1>
  <p>Здесь отображается прогресс чтения разделов энциклопедии. Прокрутка страницы раздела автоматически сохраняется (не чаще чем раз в несколько секунд).</p>
</div>

<div class="cabinet-grid">
  <?php foreach ($sections as $slug => $cfg): ?>
    <?php
    $p = $progressMap[$slug]['progress_percent'] ?? 0;
    $updated = $progressMap[$slug]['updated_at'] ?? null;
    $href = htmlspecialchars($slug . '.php', ENT_QUOTES, 'UTF-8');
    ?>
    <article class="cabinet-card">
      <h2><a href="<?php echo $href; ?>"><?php echo htmlspecialchars($cfg['display_title'], ENT_QUOTES, 'UTF-8'); ?></a></h2>
      <div class="cabinet-progress-wrap" aria-label="Прогресс <?php echo (int)$p; ?> процентов">
        <div class="cabinet-progress-bar" style="width: <?php echo (int)$p; ?>%;"></div>
      </div>
      <p class="cabinet-progress-label"><?php echo (int)$p; ?>% прочитано</p>
      <?php if ($updated): ?>
        <p class="cabinet-updated">Обновлено: <?php echo htmlspecialchars($updated, ENT_QUOTES, 'UTF-8'); ?></p>
      <?php else: ?>
        <p class="cabinet-updated muted">Раздел ещё не открывали или прогресс не сохранялся.</p>
      <?php endif; ?>
      <a class="nav-btn cabinet-open-btn" href="<?php echo $href; ?>">Открыть раздел</a>
    </article>
  <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>

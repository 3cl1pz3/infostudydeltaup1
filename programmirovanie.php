<?php
$current_page = 'programmirovanie';
require_once __DIR__ . '/includes/bootstrap.php';
$config = getSectionConfig($current_page);
$page_title = $config['page_title'];
include 'includes/header.php';
?>

  <a href="index.php" class="btn-home">&#8592; На главную</a>
  <h1 class="page-title"><?php echo htmlspecialchars($config['display_title'], ENT_QUOTES, 'UTF-8'); ?></h1>

  <div class="section-reading-area" id="section-reading-area" data-section-slug="<?php echo htmlspecialchars($current_page, ENT_QUOTES, 'UTF-8'); ?>">
  <?php echo getSectionContent($current_page); ?>
  </div>

<?php include 'includes/footer.php'; ?>

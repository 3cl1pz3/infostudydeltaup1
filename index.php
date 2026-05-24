<?php
require_once __DIR__ . '/includes/bootstrap.php';
$current_page = 'home';
$page_title = 'Главная';
include 'includes/header.php';
$guest = !isLoggedIn();
?>

  <section class="hero hero-video-block">
    <div class="hero-video-wrap">
      <video class="hero-video-bg" autoplay muted loop playsinline>
        <source src="video/coding/codage2.mp4" type="video/mp4">
      </video>
      <div class="hero-video-overlay"></div>
    </div>
    <div class="hero-content">
      <h1>Энциклопедия по информатике</h1>
      <p>Мультимедийный справочник по основам информатики, алгоритмам, программированию, сетям и безопасности.</p>
    </div>
  </section>

  <div class="media-block" style="max-width: 800px; margin-left: auto; margin-right: auto; margin-bottom: 2rem;">
    <img src="images/image1.jpg" alt="Информатика">
    <span class="caption">Информатика — наука о методах и процессах сбора, хранения и обработки информации</span>
  </div>

  <h2 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--text-primary);">Разделы энциклопедии</h2>
  <?php if ($guest): ?>
    <p class="home-guest-hint">Чтобы читать материалы разделов и видеть прогресс в <a href="login.php?next=cabinet.php">личном кабинете</a>, выполните <a href="login.php">вход</a> или <a href="register.php">регистрацию</a>.</p>
  <?php endif; ?>
  <div class="section-cards">
    <?php
    $section_media = [
      ['href' => 'osnovy.php', 'icon' => '&#128202;', 'title' => 'Основы информатики', 'desc' => 'Информация, данные, системы счисления, кодирование.', 'path' => 'video/' . rawurlencode('fundamentals of computer science') . '/preview.jpg', 'type' => 'image'],
      ['href' => 'algoritmy.php', 'icon' => '&#128200;', 'title' => 'Алгоритмы и структуры данных', 'desc' => 'Понятие алгоритма, блок-схемы, массивы, списки, деревья.', 'path' => 'video/' . rawurlencode('Algorithms and data structures') . '/preview.gif', 'type' => 'image'],
      ['href' => 'programmirovanie.php', 'icon' => '&#128187;', 'title' => 'Языки программирования', 'desc' => 'Обзор языков, парадигмы, синтаксис и практика.', 'path' => 'video/' . rawurlencode('Programming languages') . '/' . rawurlencode('Developer Coding Background.mp4'), 'type' => 'video'],
      ['href' => 'seti.php', 'icon' => '&#128268;', 'title' => 'Компьютерные сети', 'desc' => 'Протоколы, топологии, интернет, веб-технологии.', 'path' => 'video/' . rawurlencode('Computer networks') . '/preview.gif', 'type' => 'image'],
      ['href' => 'apparat.php', 'icon' => '&#128424;', 'title' => 'Аппаратное обеспечение', 'desc' => 'Процессор, память, устройства ввода-вывода.', 'path' => 'video/' . rawurlencode('Hardware support') . '/preview.gif', 'type' => 'image'],
      ['href' => 'bezopasnost.php', 'icon' => '&#128274;', 'title' => 'Информационная безопасность', 'desc' => 'Защита данных, криптография, вирусы и антивирусы.', 'path' => 'video/' . rawurlencode('Information security') . '/preview.gif', 'type' => 'image'],
    ];
    foreach ($section_media as $s):
      $card_href = $guest ? ('login.php?next=' . rawurlencode($s['href'])) : $s['href'];
    ?>
    <a href="<?php echo htmlspecialchars($card_href); ?>" class="section-card<?php echo $guest ? ' section-card-locked' : ''; ?>">
      <div class="section-card-bg">
        <?php if ($s['type'] === 'video'): ?>
        <video class="section-card-media" autoplay muted loop playsinline>
          <source src="<?php echo htmlspecialchars($s['path']); ?>" type="video/mp4">
        </video>
        <?php else: ?>
        <img class="section-card-media" src="<?php echo htmlspecialchars($s['path']); ?>" alt="">
        <?php endif; ?>
        <div class="section-card-overlay"></div>
      </div>
      <div class="section-card-content">
        <div class="card-icon"><?php echo $s['icon']; ?></div>
        <h3><?php echo htmlspecialchars($s['title']); ?></h3>
        <p><?php echo htmlspecialchars($s['desc']); ?></p>
      </div>
    </a>
    <?php endforeach; ?>
  </div>

<?php include 'includes/footer.php'; ?>

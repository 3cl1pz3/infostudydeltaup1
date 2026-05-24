<?php
require_once __DIR__ . '/bootstrap.php';

$public_pages = ['home', 'login', 'register'];
$page_id = $current_page ?? '';
if (!in_array($page_id, $public_pages, true)) {
    requireLogin();
}

$show_section_nav = isLoggedIn();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? $page_title . ' | ' : ''; ?>Энциклопедия по информатике</title>
  <link rel="stylesheet" href="css/style.css">
  <?php if (isset($current_page) && $current_page === 'search'): ?>
    <link rel="stylesheet" href="css/search.css">
  <?php endif; ?>
</head>
<body>
  <aside class="auth-plaque" aria-label="Учётная запись">
    <?php if (isLoggedIn()): ?>
      <span class="auth-plaque-user"><?php echo htmlspecialchars(currentUser()['username'], ENT_QUOTES, 'UTF-8'); ?></span>
      <a href="cabinet.php" class="auth-plaque-link">Кабинет</a>
      <?php if (isAdmin()): ?>
        <a href="admin.php" class="auth-plaque-link">Админка</a>
      <?php endif; ?>
      <a href="logout.php" class="auth-plaque-link auth-plaque-link-primary">Выход</a>
    <?php else: ?>
      <a href="login.php" class="auth-plaque-link auth-plaque-link-primary">Вход</a>
      <a href="register.php" class="auth-plaque-link">Регистрация</a>
    <?php endif; ?>
  </aside>

  <header class="site-header">
    <div class="container">
      <a href="index.php" class="logo">
        <span class="logo-icon">&#9638;</span>
        <span>ИнфоСтуди — Энциклопедия</span>
      </a>
      <nav class="nav-buttons">
        <a href="index.php" class="nav-btn <?php echo (!isset($current_page) || $current_page === 'home') ? 'active' : ''; ?>">Главная</a>
        <?php if ($show_section_nav): ?>
        <div class="nav-group">
          <a href="osnovy.php" class="nav-btn nav-btn-expand <?php echo (isset($current_page) && $current_page === 'osnovy') ? 'active' : ''; ?>" data-nav-expand="true" data-group-id="osnovy" aria-expanded="false">
            <span>Основы</span>
            <span class="nav-caret" aria-hidden="true">&#9662;</span>
          </a>
          <div class="nav-topics" data-nav-topics="osnovy">
            <a class="nav-topic-link" href="osnovy.php#info-data">Информация и данные</a>
            <a class="nav-topic-link" href="osnovy.php#number-systems">Системы счисления</a>
            <a class="nav-topic-link" href="osnovy.php#encoding">Кодирование информации</a>
          </div>
        </div>
        <div class="nav-group">
          <a href="algoritmy.php" class="nav-btn nav-btn-expand <?php echo (isset($current_page) && $current_page === 'algoritmy') ? 'active' : ''; ?>" data-nav-expand="true" data-group-id="algoritmy" aria-expanded="false">
            <span>Алгоритмы</span>
            <span class="nav-caret" aria-hidden="true">&#9662;</span>
          </a>
          <div class="nav-topics" data-nav-topics="algoritmy">
            <a class="nav-topic-link" href="algoritmy.php#algorithm-concept">Понятие алгоритма</a>
            <a class="nav-topic-link" href="algoritmy.php#data-structures">Структуры данных</a>
            <a class="nav-topic-link" href="algoritmy.php#sorting-search">Сортировка и поиск</a>
          </div>
        </div>
        <div class="nav-group">
          <a href="programmirovanie.php" class="nav-btn nav-btn-expand <?php echo (isset($current_page) && $current_page === 'programmirovanie') ? 'active' : ''; ?>" data-nav-expand="true" data-group-id="programmirovanie" aria-expanded="false">
            <span>Программирование</span>
            <span class="nav-caret" aria-hidden="true">&#9662;</span>
          </a>
          <div class="nav-topics" data-nav-topics="programmirovanie">
            <a class="nav-topic-link" href="programmirovanie.php#language-overview">Обзор языков</a>
            <a class="nav-topic-link" href="programmirovanie.php#paradigms">Парадигмы программирования</a>
            <a class="nav-topic-link" href="programmirovanie.php#development-stages">Этапы разработки</a>
          </div>
        </div>
        <div class="nav-group">
          <a href="seti.php" class="nav-btn nav-btn-expand <?php echo (isset($current_page) && $current_page === 'seti') ? 'active' : ''; ?>" data-nav-expand="true" data-group-id="seti" aria-expanded="false">
            <span>Сети</span>
            <span class="nav-caret" aria-hidden="true">&#9662;</span>
          </a>
          <div class="nav-topics" data-nav-topics="seti">
            <a class="nav-topic-link" href="seti.php#network-types">Типы и топологии</a>
            <a class="nav-topic-link" href="seti.php#protocols-osi">Протоколы и OSI</a>
            <a class="nav-topic-link" href="seti.php#internet-web">Интернет и веб</a>
          </div>
        </div>
        <div class="nav-group">
          <a href="apparat.php" class="nav-btn nav-btn-expand <?php echo (isset($current_page) && $current_page === 'apparat') ? 'active' : ''; ?>" data-nav-expand="true" data-group-id="apparat" aria-expanded="false">
            <span>Аппаратура</span>
            <span class="nav-caret" aria-hidden="true">&#9662;</span>
          </a>
          <div class="nav-topics" data-nav-topics="apparat">
            <a class="nav-topic-link" href="apparat.php#cpu">Центральный процессор</a>
            <a class="nav-topic-link" href="apparat.php#memory">Память</a>
            <a class="nav-topic-link" href="apparat.php#io-devices">Устройства ввода-вывода</a>
          </div>
        </div>
        <div class="nav-group">
          <a href="bezopasnost.php" class="nav-btn nav-btn-expand <?php echo (isset($current_page) && $current_page === 'bezopasnost') ? 'active' : ''; ?>" data-nav-expand="true" data-group-id="bezopasnost" aria-expanded="false">
            <span>Безопасность</span>
            <span class="nav-caret" aria-hidden="true">&#9662;</span>
          </a>
          <div class="nav-topics" data-nav-topics="bezopasnost">
            <a class="nav-topic-link" href="bezopasnost.php#security-basics">Основы защиты</a>
            <a class="nav-topic-link" href="bezopasnost.php#cryptography">Криптография</a>
            <a class="nav-topic-link" href="bezopasnost.php#malware-antivirus">Вредоносное ПО</a>
          </div>
        </div>
        <a href="search.php" class="nav-btn <?php echo (isset($current_page) && $current_page === 'search') ? 'active' : ''; ?>">Поиск</a>
        <?php endif; ?>
        <button type="button" class="theme-toggle" id="theme-toggle" aria-label="Переключить тему">
          <span class="theme-toggle-icon" aria-hidden="true">🌙</span>
          <span class="theme-toggle-text">Светлая тема</span>
        </button>
      </nav>
    </div>
  </header>
  <main class="main-content">

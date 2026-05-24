<?php $current_page = 'search'; $page_title = 'Поиск по энциклопедии'; ?>
<?php
if (function_exists('mb_internal_encoding')) {
  mb_internal_encoding('UTF-8');
}

$index = [
  [
    'title' => 'Основы информатики',
    'url' => 'osnovy.php',
    'section' => 'Основы',
    'description' => 'Информация, данные, системы счисления, кодирование информации.',
    'keywords' => 'информация данные сообщение величина измерение количества информации бит байт двоичная система счисления основание системы перевод чисел кодирование ascii unicode код хартли шеннон'
  ],
  [
    'title' => 'Алгоритмы и структуры данных',
    'url' => 'algoritmy.php',
    'section' => 'Алгоритмы',
    'description' => 'Понятие алгоритма, блок-схемы, массивы, списки, деревья, сортировки и поиск.',
    'keywords' => 'алгоритм свойства алгоритма дискретность массовость результативность блок-схема ветвление цикл линейный алгоритм структуры данных массив список стек очередь дерево граф сортировка пузырьковая быстрая сортировка слиянием бинарный поиск хеш-таблица'
  ],
  [
    'title' => 'Языки программирования',
    'url' => 'programmirovanie.php',
    'section' => 'Программирование',
    'description' => 'Языки программирования, парадигмы, этапы разработки программ.',
    'keywords' => 'язык программирования синтаксис семантика компилятор интерпретатор c java python парадигма процедурное программирование объектно-ориентированное ооп инкапсуляция наследование полиморфизм функциональное программирование события ide git'
  ],
  [
    'title' => 'Компьютерные сети',
    'url' => 'seti.php',
    'section' => 'Сети',
    'description' => 'LAN и WAN, топологии, модель OSI, интернет и веб-технологии.',
    'keywords' => 'сеть lan wan wifi топология шина звезда кольцо протокол tcp ip http https модель osi уровни маршрутизация коммутатор маршрутизатор интернет браузер веб-сайт клиент сервер'
  ],
  [
    'title' => 'Аппаратное обеспечение',
    'url' => 'apparat.php',
    'section' => 'Аппаратура',
    'description' => 'Процессор, память, устройства ввода-вывода и шины.',
    'keywords' => 'аппаратное обеспечение процессор cpu alu регистры тактовая частота ядро кэш оперативная память ram постоянная rom ssd hdd устройства ввода клавиатура мышь сканер устройства вывода монитор принтер шины контроллер'
  ],
  [
    'title' => 'Информационная безопасность',
    'url' => 'bezopasnost.php',
    'section' => 'Безопасность',
    'description' => 'Конфиденциальность, криптография, вредоносное ПО и антивирусы.',
    'keywords' => 'информационная безопасность конфиденциальность целостность доступность шифрование криптография симметричное асимметричное шифр открытый ключ закрытый ключ хеш-функция md5 sha вирус троян шпионское по ransomware антивирус фаервол резервное копирование'
  ],
];

$query = '';
$results = [];

if (!empty($_GET['q'])) {
  $query = trim($_GET['q']);
  $q = function_exists('mb_strtolower') ? mb_strtolower($query) : strtolower($query);

  foreach ($index as $item) {
    $haystackRaw = $item['title'].' '.$item['section'].' '.$item['description'].' '.$item['keywords'];
    $haystack = function_exists('mb_strtolower') ? mb_strtolower($haystackRaw) : strtolower($haystackRaw);
    $pos = function_exists('mb_strpos') ? mb_strpos($haystack, $q) : strpos($haystack, $q);
    if ($pos !== false) {
      $results[] = $item;
    }
  }
}
?>
<?php include 'includes/header.php'; ?>

  <section class="search-page">
    <section class="hero hero-video-block search-hero">
      <div class="hero-video-wrap">
        <video class="hero-video-bg" autoplay muted loop playsinline>
          <source src="video/coding/codage2.mp4" type="video/mp4">
        </video>
        <div class="hero-video-overlay"></div>
      </div>
      <div class="hero-content">
        <h1 class="search-hero-title">Поиск по энциклопедии</h1>
        <p class="search-hero-subtitle">Найдите нужный раздел по ключевым словам: «алгоритм», «TCP/IP», «бит и байт», «вирусы» и т.д.</p>

        <div class="search-mascot" aria-hidden="true">
          <div class="eye" data-eye>
            <div class="pupil" data-pupil></div>
          </div>
          <div class="eye" data-eye>
            <div class="pupil" data-pupil></div>
          </div>
        </div>

        <form class="search-page-form search-hero-form" method="get" action="search.php">
          <input
            type="text"
            name="q"
            class="search-page-input"
            placeholder="Введите тему или ключевые слова..."
            value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>"
          >
          <button type="submit" class="search-page-btn">Найти</button>
        </form>
      </div>
    </section>

    <div class="search-page-results">
      <?php if ($query === ''): ?>
        <p class="search-page-hint">Введите запрос, чтобы увидеть результаты. Например: <span>«двоичная система»</span>, <span>«структуры данных»</span>, <span>«криптография»</span>.</p>
      <?php elseif (empty($results)): ?>
        <p class="search-page-hint">По запросу «<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>» ничего не найдено. Попробуйте другие слова или обобщите формулировку.</p>
      <?php else: ?>
        <p class="search-page-count">Найдено совпадений: <?php echo count($results); ?></p>
        <div class="search-result-list">
          <?php foreach ($results as $item): ?>
            <a href="<?php echo htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>" class="search-result-card">
              <div class="search-result-pill"><?php echo htmlspecialchars($item['section'], ENT_QUOTES, 'UTF-8'); ?></div>
              <h2><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
              <p><?php echo htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8'); ?></p>
              <span class="search-result-link">Перейти к разделу →</span>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

<?php include 'includes/footer.php'; ?>


  </main>
  <footer class="site-footer">
    <p>Энциклопедия по информатике — учебный проект. Мультимедийное содержание.</p>
  </footer>
  <script src="js/main.js"></script>
  <?php
  if (function_exists('isLoggedIn') && isLoggedIn() && isset($current_page)) {
      $reading_slugs = array_keys(sectionConfigs());
      if (in_array($current_page, $reading_slugs, true)) :
  ?>
  <script>
    window.INFST_READING = {
      section: <?php echo json_encode($current_page, JSON_UNESCAPED_UNICODE); ?>,
      csrf: <?php echo json_encode(csrfToken(), JSON_UNESCAPED_UNICODE); ?>
    };
  </script>
  <script src="js/reading-progress.js"></script>
  <?php
      endif;
  }
  ?>
</body>
</html>

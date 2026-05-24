-- Выполните один раз, если база уже создана из старого database.sql без таблицы прогресса.
USE infostudy;

CREATE TABLE IF NOT EXISTS reading_progress (
  user_id INT NOT NULL,
  section_slug VARCHAR(64) NOT NULL,
  progress_percent TINYINT UNSIGNED NOT NULL DEFAULT 0,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, section_slug),
  CONSTRAINT fk_reading_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

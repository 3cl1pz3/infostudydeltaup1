CREATE DATABASE IF NOT EXISTS infostudy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE infostudy;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(64) NOT NULL UNIQUE,
  password_hash CHAR(64) NOT NULL,
  role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(64) NOT NULL UNIQUE,
  title VARCHAR(255) NOT NULL,
  content_html LONGTEXT NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO users (username, password_hash, role)
VALUES
  ('admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'admin'),
  ('student', 'e606e38b0d8c19b24cf0ee3808183162ea7cd63ff7912dbb22b5e803286b4446', 'user')
ON DUPLICATE KEY UPDATE username = VALUES(username);

INSERT INTO sections (slug, title, content_html)
VALUES
  ('osnovy', 'Основы информатики', ''),
  ('algoritmy', 'Алгоритмы и структуры данных', ''),
  ('programmirovanie', 'Языки программирования', ''),
  ('seti', 'Компьютерные сети', ''),
  ('apparat', 'Аппаратное обеспечение', ''),
  ('bezopasnost', 'Информационная безопасность', '')
ON DUPLICATE KEY UPDATE title = VALUES(title);

CREATE TABLE IF NOT EXISTS reading_progress (
  user_id INT NOT NULL,
  section_slug VARCHAR(64) NOT NULL,
  progress_percent TINYINT UNSIGNED NOT NULL DEFAULT 0,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, section_slug),
  CONSTRAINT fk_reading_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

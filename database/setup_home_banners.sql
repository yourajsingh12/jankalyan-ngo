-- Creates only the table required by the home page banner manager.
-- Safe to run more than once; existing banners are never deleted or replaced.

CREATE TABLE IF NOT EXISTS banners (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  page_name  VARCHAR(80)  NOT NULL DEFAULT 'home',
  title      VARCHAR(255) NOT NULL,
  subtitle   VARCHAR(255) NULL,
  image_path VARCHAR(255) NOT NULL,
  link_url   VARCHAR(255) NULL,
  link_label VARCHAR(60)  NULL,
  sort_order INT          NOT NULL DEFAULT 0,
  status     VARCHAR(20)  NOT NULL DEFAULT 'Active',
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_banners_page_order (page_name, status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO banners
  (page_name, title, subtitle, image_path, link_url, link_label, sort_order, status)
SELECT 'home', 'Education For All', 'Every child deserves a classroom',
       'assets/img/hero/banner1.png', 'donate.php', 'Donate Now', 1, 'Active'
WHERE NOT EXISTS (SELECT 1 FROM banners WHERE page_name = 'home' AND title = 'Education For All');

INSERT INTO banners
  (page_name, title, subtitle, image_path, link_url, link_label, sort_order, status)
SELECT 'home', 'Health & Nutrition', 'Care that reaches the last mile',
       'assets/img/hero/banner2.png', 'donate.php', 'Donate Now', 2, 'Active'
WHERE NOT EXISTS (SELECT 1 FROM banners WHERE page_name = 'home' AND title = 'Health & Nutrition');

INSERT INTO banners
  (page_name, title, subtitle, image_path, link_url, link_label, sort_order, status)
SELECT 'home', 'Community Development', 'Building dignity and opportunity',
       'assets/img/hero/banner3.png', 'donate.php', 'Donate Now', 3, 'Active'
WHERE NOT EXISTS (SELECT 1 FROM banners WHERE page_name = 'home' AND title = 'Community Development');

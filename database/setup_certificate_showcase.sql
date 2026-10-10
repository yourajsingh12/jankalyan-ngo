-- Run this file in phpMyAdmin after selecting the `ngo` database.
-- It creates the website certificate gallery table and imports the eight
-- certificate images currently used by certificate.php. Re-running it does
-- not duplicate those entries or replace existing certificate records.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS certificate_showcase (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(255) NOT NULL,
  description TEXT         NULL,
  image       VARCHAR(255) NULL,
  issued_by   VARCHAR(160) NULL,
  issued_on   DATE         NULL,
  sort_order  INT          NOT NULL DEFAULT 0,
  status      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_order (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO certificate_showcase (title, image, sort_order, status)
SELECT 'Community Service Recognition', 'assets/img/gallery/01.jpg', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM certificate_showcase WHERE title = 'Community Service Recognition');

INSERT INTO certificate_showcase (title, image, sort_order, status)
SELECT 'Education Support Initiative', 'assets/img/gallery/02.jpg', 2, 1
WHERE NOT EXISTS (SELECT 1 FROM certificate_showcase WHERE title = 'Education Support Initiative');

INSERT INTO certificate_showcase (title, image, sort_order, status)
SELECT 'Health & Nutrition Program', 'assets/img/gallery/03.jpg', 3, 1
WHERE NOT EXISTS (SELECT 1 FROM certificate_showcase WHERE title = 'Health & Nutrition Program');

INSERT INTO certificate_showcase (title, image, sort_order, status)
SELECT 'Community Development Award', 'assets/img/gallery/04.jpg', 4, 1
WHERE NOT EXISTS (SELECT 1 FROM certificate_showcase WHERE title = 'Community Development Award');

INSERT INTO certificate_showcase (title, image, sort_order, status)
SELECT 'Volunteer Appreciation', 'assets/img/gallery/05.jpg', 5, 1
WHERE NOT EXISTS (SELECT 1 FROM certificate_showcase WHERE title = 'Volunteer Appreciation');

INSERT INTO certificate_showcase (title, image, sort_order, status)
SELECT 'Social Impact Recognition', 'assets/img/gallery/06.jpg', 6, 1
WHERE NOT EXISTS (SELECT 1 FROM certificate_showcase WHERE title = 'Social Impact Recognition');

INSERT INTO certificate_showcase (title, image, sort_order, status)
SELECT 'Partner Appreciation', 'assets/img/gallery/07.jpg', 7, 1
WHERE NOT EXISTS (SELECT 1 FROM certificate_showcase WHERE title = 'Partner Appreciation');

INSERT INTO certificate_showcase (title, image, sort_order, status)
SELECT 'Outstanding Contribution', 'assets/img/gallery/08.jpg', 8, 1
WHERE NOT EXISTS (SELECT 1 FROM certificate_showcase WHERE title = 'Outstanding Contribution');

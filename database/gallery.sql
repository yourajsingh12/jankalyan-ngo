-- Gallery table and starter photos for the Jan Kalyan NGO website.
-- Existing gallery records are preserved; starter photos are only inserted
-- when their image path is not already present.

CREATE TABLE IF NOT EXISTS gallery (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  image_path  VARCHAR(255) NOT NULL,
  title       VARCHAR(255) NULL,
  alt_text    VARCHAR(255) NULL,
  category    VARCHAR(80) NULL,
  sort_order  INT NOT NULL DEFAULT 0,
  status      TINYINT(1) NOT NULL DEFAULT 1,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_gallery_order (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE gallery
  ADD COLUMN IF NOT EXISTS title      VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS alt_text   VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS category   VARCHAR(80) NULL,
  ADD COLUMN IF NOT EXISTS sort_order INT NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS status     TINYINT(1) NOT NULL DEFAULT 1,
  ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

INSERT INTO gallery (image_path, sort_order, status)
SELECT 'assets/img/gallery/01.jpg', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM gallery WHERE image_path = 'assets/img/gallery/01.jpg');

INSERT INTO gallery (image_path, sort_order, status)
SELECT 'assets/img/gallery/02.jpg', 2, 1
WHERE NOT EXISTS (SELECT 1 FROM gallery WHERE image_path = 'assets/img/gallery/02.jpg');

INSERT INTO gallery (image_path, sort_order, status)
SELECT 'assets/img/gallery/03.jpg', 3, 1
WHERE NOT EXISTS (SELECT 1 FROM gallery WHERE image_path = 'assets/img/gallery/03.jpg');

INSERT INTO gallery (image_path, sort_order, status)
SELECT 'assets/img/gallery/04.jpg', 4, 1
WHERE NOT EXISTS (SELECT 1 FROM gallery WHERE image_path = 'assets/img/gallery/04.jpg');

INSERT INTO gallery (image_path, sort_order, status)
SELECT 'assets/img/gallery/05.jpg', 5, 1
WHERE NOT EXISTS (SELECT 1 FROM gallery WHERE image_path = 'assets/img/gallery/05.jpg');

INSERT INTO gallery (image_path, sort_order, status)
SELECT 'assets/img/gallery/06.jpg', 6, 1
WHERE NOT EXISTS (SELECT 1 FROM gallery WHERE image_path = 'assets/img/gallery/06.jpg');

INSERT INTO gallery (image_path, sort_order, status)
SELECT 'assets/img/gallery/07.jpg', 7, 1
WHERE NOT EXISTS (SELECT 1 FROM gallery WHERE image_path = 'assets/img/gallery/07.jpg');

INSERT INTO gallery (image_path, sort_order, status)
SELECT 'assets/img/gallery/08.jpg', 8, 1
WHERE NOT EXISTS (SELECT 1 FROM gallery WHERE image_path = 'assets/img/gallery/08.jpg');

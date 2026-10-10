-- Run this file in the NGO database to create the Our Focus Areas table.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS focus_areas (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(160) NOT NULL,
  description TEXT         NULL,
  icon        VARCHAR(255) NULL,
  image       VARCHAR(255) NULL,
  link_url    VARCHAR(255) NOT NULL DEFAULT 'donate.php',
  link_label  VARCHAR(60)  NOT NULL DEFAULT 'Support Now',
  sort_order  INT          NOT NULL DEFAULT 0,
  status      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_order (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

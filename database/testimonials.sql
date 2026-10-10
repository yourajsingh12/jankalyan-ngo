-- Testimonials shown in the About page's Community Voices section.
-- Creates the table when needed and migrates the existing static testimonials
-- only when there are no testimonial records yet.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS testimonials (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(120) NOT NULL,
  designation VARCHAR(160) NULL,
  message     TEXT         NOT NULL,
  image       VARCHAR(255) NULL,
  rating      TINYINT      NOT NULL DEFAULT 5,
  sort_order  INT          NOT NULL DEFAULT 0,
  status      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_order (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO testimonials (name, designation, message, image, rating, sort_order, status)
SELECT sample.name, sample.designation, sample.message, sample.image, 5, sample.sort_order, 1
FROM (
  SELECT 'Meena Devi' AS name, 'Community Beneficiary' AS designation,
         'The support we received helped our children continue their education and our family feel safer, healthier, and more hopeful.' AS message,
         'assets/img/testimonial/01.jpg' AS image, 1 AS sort_order
  UNION ALL
  SELECT 'Ramesh Kumar', 'Volunteer',
         'Working with Jan Kalyan Sangh Foundation has shown me how small acts of service can transform lives and strengthen whole communities.',
         'assets/img/testimonial/02.jpg', 2
  UNION ALL
  SELECT 'Anita Sharma', 'Women Empowerment Program',
         'The awareness and support we received gave us courage, confidence, and the chance to build a better future for our families.',
         'assets/img/testimonial/03.jpg', 3
  UNION ALL
  SELECT 'Sunita Verma', 'Health Program Participant',
         'Their health and nutrition support helped our family become more aware, healthier, and stronger together.',
         'assets/img/testimonial/04.jpg', 4
) AS sample
WHERE NOT EXISTS (SELECT 1 FROM testimonials);

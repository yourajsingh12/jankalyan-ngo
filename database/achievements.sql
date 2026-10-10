-- -------------------------------------------------------------
-- Jan Kalyan Sangh Foundation
-- Achievements data seed
-- -------------------------------------------------------------

DROP TABLE IF EXISTS achievements;

CREATE TABLE achievements (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(255) NOT NULL,
  description TEXT NULL,
  category    VARCHAR(80) NULL,
  year        VARCHAR(10) NULL,
  highlight   VARCHAR(160) NULL,
  image       VARCHAR(255) NULL,
  icon        VARCHAR(255) NULL,
  sort_order  INT NOT NULL DEFAULT 0,
  status      TINYINT(1) NOT NULL DEFAULT 1,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_order (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO achievements (title, description, category, year, highlight, image, icon, sort_order, status) VALUES
('Education Outreach Program', 'Expanded school support and mentorship for children to stay engaged in learning and build brighter futures.', 'Education', '2024', '1200 students supported', 'assets/img/gallery/03.jpg', 'assets/img/icon/student-visa.svg', 1, 1),
('Women Empowerment Campaign', 'Provided skill-building sessions, confidence workshops and community guidance for women and young girls.', 'Women Welfare', '2024', '320 women trained', 'assets/img/gallery/04.jpg', 'assets/img/icon/team.svg', 2, 1),
('Health & Nutrition Initiative', 'Reached families through health camps, nutrition distribution and awareness programs across underserved areas.', 'Health', '2023', '4500 families reached', 'assets/img/gallery/02.jpg', 'assets/img/icon/quality-service.svg', 3, 1),
('Community Relief Support', 'Delivered urgent assistance and welfare support to families facing hardship with care and dignity.', 'Relief', '2022', '2800 beneficiaries served', 'assets/img/gallery/01.jpg', 'assets/img/icon/award.svg', 4, 1),
('Volunteer Network Growth', 'Strengthened a growing volunteer base that supports education, outreach and local community action.', 'Community', '2023', '48 active volunteers', 'assets/img/gallery/05.jpg', 'assets/img/icon/team.svg', 5, 1),
('Clean & Green Community Drive', 'Encouraged local participation in cleanliness and environment-focused action to support healthier living spaces.', 'Environment', '2021', '18 communities mobilized', 'assets/img/gallery/06.jpg', 'assets/img/icon/country.svg', 6, 1);

SELECT * FROM achievements ORDER BY sort_order ASC;

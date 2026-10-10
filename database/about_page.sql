-- About Us page content and defaults for the admin editor.
-- Safe to re-run: existing rows and admin edits are preserved.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS page_sections (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  page        VARCHAR(40)  NOT NULL,
  section_key VARCHAR(60)  NOT NULL,
  tagline     VARCHAR(160) NULL,
  heading     VARCHAR(255) NULL,
  subheading  VARCHAR(255) NULL,
  body        TEXT         NULL,
  image       VARCHAR(255) NULL,
  extra       TEXT         NULL,
  status      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_slot (page, section_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO page_sections (page, section_key, tagline, heading, body, image, extra, status) VALUES
  ('about', 'page', NULL, 'About Us', NULL, NULL, NULL, 1),
  ('about', 'intro', 'About Us', 'Empowering communities through education, health, and dignity',
   'Jan Kalyan Sangh Foundation is a grassroots social impact organisation dedicated to uplifting underserved communities through education support, health awareness, women empowerment, and sustainable local development.',
   'assets/img/about/about1.png', 'assets/img/about/about.png', 1),
  ('about', 'experience', NULL, '10', 'Years of Service', NULL, NULL, 1),
  ('about', 'education', NULL, 'Education Support',
   'We help children continue their learning with school access, learning material, and mentorship.',
   'assets/img/icon/accurate.svg', NULL, 1),
  ('about', 'health', NULL, 'Health & Awareness',
   'We organize health camps, nutrition support, and awareness programs for rural families.',
   'assets/img/icon/support.svg', NULL, 1),
  ('about', 'counter_children', NULL, '5000', '+ Children Supported', NULL, 'assets/img/icon/visa.svg', 1),
  ('about', 'counter_families', NULL, '6000', '+ Families Reached', NULL, 'assets/img/icon/happy.svg', 1),
  ('about', 'counter_villages', NULL, '40', '+ Villages Impacted', NULL, 'assets/img/icon/country.svg', 1),
  ('about', 'counter_programs', NULL, '25', '+ Community Programs', NULL, 'assets/img/icon/award.svg', 1),
  ('about', 'explore', NULL, 'Explore More', NULL, NULL, 'mission.php', 1);

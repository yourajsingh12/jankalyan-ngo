-- Mission page content and editable focus cards.
-- Safe to re-run: existing page copy and admin edits are preserved.

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

CREATE TABLE IF NOT EXISTS mission_focus_areas (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(160) NOT NULL,
  description TEXT         NULL,
  icon        VARCHAR(255) NULL,
  sort_order  INT          NOT NULL DEFAULT 0,
  status      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_mission_focus_title (title),
  KEY idx_mission_focus_order (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO page_sections (page, section_key, tagline, heading, body, extra, image, status) VALUES
  ('mission', 'intro', 'Our Purpose', 'Creating a future built on equality and opportunity',
   'At JAN KALYAN SANGH FOUNDATION, we believe lasting change begins when communities have the resources, knowledge and confidence to shape their own future.',
   NULL, NULL, 1),
  ('mission', 'mission', 'Our Mission', 'Driving social change through sustainable development',
   'We are dedicated to improving education, healthcare, environmental conservation and women''s empowerment by providing communities with the resources they need to build brighter futures.',
   'Our ultimate goal is to create self-reliant, resilient communities that thrive in harmony with their environment.',
   'assets/img/icon/accurate-guidance.svg', 1),
  ('mission', 'vision', 'Our Vision', 'A world where every person can reach their potential',
   'We envision a future where equality, education and environmental sustainability form the bedrock of thriving communities.',
   'We strive for a society where collaboration drives innovation and communities work together to build lasting solutions for generations to come.',
   'assets/img/icon/country.svg', 1),
  ('mission', 'focus', 'Our Focus', 'Turning purpose into action',
   'Our work is focused on practical support that helps people build secure, healthy and independent lives.', NULL, NULL, 1);

INSERT IGNORE INTO mission_focus_areas (title, description, icon, sort_order, status) VALUES
  ('Education',
   'We create access to learning, guidance and opportunities for children and young people.',
   'assets/img/icon/student-visa.svg', 1, 1),
  ('Healthcare',
   'We support healthier families through awareness, checkups and nutrition programs.',
   'assets/img/icon/quality-service.svg', 2, 1),
  ('Environment',
   'We encourage responsible community action that protects our shared environment.',
   'assets/img/icon/country.svg', 3, 1),
  ('Women''s Empowerment',
   'We help women access skills, confidence and opportunities to lead with independence.',
   'assets/img/icon/team.svg', 4, 1);

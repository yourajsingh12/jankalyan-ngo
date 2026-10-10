-- Homepage content used by Add Content in the admin dashboard.
-- Safe to run more than once: existing records, including admin edits, remain unchanged.

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

CREATE TABLE IF NOT EXISTS home_features (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(160) NOT NULL,
  description TEXT         NULL,
  detail      LONGTEXT     NULL,
  image       VARCHAR(255) NULL,
  icon        VARCHAR(255) NULL,
  sort_order  INT          NOT NULL DEFAULT 0,
  status      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_order (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE home_features
  ADD COLUMN IF NOT EXISTS detail LONGTEXT NULL AFTER description,
  ADD COLUMN IF NOT EXISTS image VARCHAR(255) NULL AFTER detail;

CREATE TABLE IF NOT EXISTS process_steps (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(160) NOT NULL,
  description TEXT         NULL,
  icon        VARCHAR(255) NULL,
  link_url    VARCHAR(255) NULL,
  sort_order  INT          NOT NULL DEFAULT 0,
  status      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_order (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO home_features (title, description, icon, sort_order, status)
SELECT 'Education For All',
       'We support children''s education through school access, learning resources, and mentorship.',
       'assets/img/icon/assistance.svg', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM home_features WHERE title = 'Education For All');

INSERT INTO home_features (title, description, icon, sort_order, status)
SELECT 'Health & Nutrition',
       'We organize health checkups, nutrition distribution, and awareness drives for rural families.',
       'assets/img/icon/happy.svg', 2, 1
WHERE NOT EXISTS (SELECT 1 FROM home_features WHERE title = 'Health & Nutrition');

INSERT INTO home_features (title, description, icon, sort_order, status)
SELECT 'Community Development',
       'We work with local communities to build dignity, opportunity, and sustainable growth.',
       'assets/img/icon/team.svg', 3, 1
WHERE NOT EXISTS (SELECT 1 FROM home_features WHERE title = 'Community Development');

UPDATE home_features
   SET image = 'assets/img/visa/seva1.png'
 WHERE title = 'Education For All' AND (image IS NULL OR image = '');

UPDATE home_features
   SET image = 'assets/img/visa/seva2.png'
 WHERE title = 'Health & Nutrition' AND (image IS NULL OR image = '');

UPDATE home_features
   SET image = 'assets/img/visa/seva1.png'
 WHERE title = 'Community Development' AND (image IS NULL OR image = '');

UPDATE home_features
   SET image = 'assets/img/visa/seva1.png'
 WHERE title = 'Education For All' AND (image IS NULL OR image = '');

UPDATE home_features
   SET image = 'assets/img/visa/seva2.png'
 WHERE title = 'Health & Nutrition' AND (image IS NULL OR image = '');

UPDATE home_features
   SET image = 'assets/img/visa/seva1.png'
 WHERE title = 'Community Development' AND (image IS NULL OR image = '');

UPDATE home_features
   SET detail = 'We support children''s education by helping them access school, learning resources, and caring mentorship. Our work encourages regular learning and gives children a safe space to build confidence and curiosity.\n\nBy working with families, schools, and local communities, we help identify barriers to education and connect children with practical support. We believe every child deserves the opportunity to learn, grow, and shape a brighter future.'
 WHERE title = 'Education For All' AND (detail IS NULL OR detail = '');

UPDATE home_features
   SET detail = 'We organize health checkups, nutrition distribution, and awareness activities for rural families. These services help people access basic care, understand everyday health needs, and seek timely support.\n\nOur outreach works alongside local communities to make health information easier to reach and to encourage healthier practices for children and families. We aim to support well-being with care, dignity, and sustained community participation.'
 WHERE title = 'Health & Nutrition' AND (detail IS NULL OR detail = '');

UPDATE home_features
   SET detail = 'We work with local communities to strengthen dignity, opportunity, and sustainable growth. Community members help identify priorities and take part in shaping practical solutions that respond to local needs.\n\nThrough awareness, collaboration, and inclusive support, we encourage families to access opportunities and build resilience. Our goal is lasting progress led with and for the communities we serve.'
 WHERE title = 'Community Development' AND (detail IS NULL OR detail = '');

INSERT INTO process_steps (title, description, icon, link_url, sort_order, status)
SELECT 'Volunteer',
       'Join our outreach programs and contribute your time, skills, and energy.',
       'assets/img/icon/team.svg', 'participate.php', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM process_steps WHERE title = 'Volunteer');

INSERT INTO process_steps (title, description, icon, link_url, sort_order, status)
SELECT 'Support A Program',
       'Your donation helps us provide education, food, care, and livelihood support.',
       'assets/img/icon/loan.svg', 'donate.php', 2, 1
WHERE NOT EXISTS (SELECT 1 FROM process_steps WHERE title = 'Support A Program');

INSERT INTO process_steps (title, description, icon, link_url, sort_order, status)
SELECT 'Partner With Us',
       'Collaborate with us to expand social impact and serve more families.',
       'assets/img/icon/document.svg', 'contact.php', 3, 1
WHERE NOT EXISTS (SELECT 1 FROM process_steps WHERE title = 'Partner With Us');

INSERT INTO process_steps (title, description, icon, link_url, sort_order, status)
SELECT 'Spread The Word',
       'Share our mission and inspire others to build a brighter future together.',
       'assets/img/icon/approval.svg', 'about.php', 4, 1
WHERE NOT EXISTS (SELECT 1 FROM process_steps WHERE title = 'Spread The Word');

INSERT INTO page_sections (page, section_key, tagline, heading, status)
SELECT 'home', 'focus', 'Our Focus Areas', 'Building a stronger and healthier society', 1
WHERE NOT EXISTS (SELECT 1 FROM page_sections WHERE page = 'home' AND section_key = 'focus');

INSERT INTO page_sections (page, section_key, heading, body, image, extra, status)
SELECT 'home', 'cta',
       'We work for a healthier, kinder, and stronger tomorrow',
       'Jan Kalyan Sangh Foundation is committed to empowering underserved communities through education, healthcare, social awareness, and community development.',
       'assets/img/cta/flood.png', 'Donate Now|donate.php', 1
WHERE NOT EXISTS (SELECT 1 FROM page_sections WHERE page = 'home' AND section_key = 'cta');

INSERT INTO page_sections (page, section_key, tagline, heading, status)
SELECT 'home', 'involve', 'Get Involved', 'Small actions create lasting change', 1
WHERE NOT EXISTS (SELECT 1 FROM page_sections WHERE page = 'home' AND section_key = 'involve');

-- =============================================================================
--  Jan Kalyan Sangh Foundation — content schema
-- -----------------------------------------------------------------------------
--  Safe to re-run. Every statement is IF NOT EXISTS, so running this against a
--  database that is already partly built adds only what is missing and leaves
--  existing rows untouched.
--
--  The database already carried a partial schema (team_members, projects,
--  gallery, achievements, missions, banners, certificates, donations,
--  donation_settings, contact_info, participants, enquiries, admin_users).
--  Those are extended in place rather than replaced. `hostels` and `products`
--  are left alone: they are leftovers from a different project, they are
--  empty, and dropping tables is not something a content migration should do
--  on its own.
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;


-- =============================================================================
--  1. SITE-WIDE CONFIGURATION
-- =============================================================================

-- Organisation name, logo, contact details, footer copy, meta defaults.
-- A key/value table rather than a one-row table with forty columns: adding a
-- new setting should not be a schema change.
CREATE TABLE IF NOT EXISTS site_settings (
  setting_key   VARCHAR(80)  NOT NULL,
  setting_value TEXT         NULL,
  setting_group VARCHAR(40)  NOT NULL DEFAULT 'general',
  label         VARCHAR(120) NULL,
  input_type    VARCHAR(20)  NOT NULL DEFAULT 'text',
  sort_order    INT          NOT NULL DEFAULT 0,
  updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (setting_key),
  KEY idx_group (setting_group, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- The main navigation. parent_id supports one level of dropdown.
CREATE TABLE IF NOT EXISTS menu_items (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  label      VARCHAR(80)  NOT NULL,
  url        VARCHAR(255) NOT NULL DEFAULT '#',
  parent_id  INT          NULL,
  target     VARCHAR(12)  NOT NULL DEFAULT '_self',
  sort_order INT          NOT NULL DEFAULT 0,
  status     TINYINT(1)   NOT NULL DEFAULT 1,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_order (status, sort_order),
  KEY idx_parent (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Social profiles, used in the header strip, the slide-out panel and the footer.
CREATE TABLE IF NOT EXISTS social_links (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  platform   VARCHAR(40)  NOT NULL,
  icon_class VARCHAR(60)  NOT NULL DEFAULT 'fab fa-link',
  url        VARCHAR(255) NOT NULL,
  sort_order INT          NOT NULL DEFAULT 0,
  status     TINYINT(1)   NOT NULL DEFAULT 1,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_order (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- The scrolling programme names in the header top bar.
CREATE TABLE IF NOT EXISTS ticker_items (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  text       VARCHAR(160) NOT NULL,
  sort_order INT          NOT NULL DEFAULT 0,
  status     TINYINT(1)   NOT NULL DEFAULT 1,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_order (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
--  2. PAGE COPY
-- -----------------------------------------------------------------------------
--  Headings, taglines and intro paragraphs are copy an admin needs to edit,
--  but they are not entities — there is exactly one "our focus areas" heading.
--  One generic table keyed by page + slot beats fifteen near-identical ones.
--  `extra` is a JSON-ish free field for the odd section that needs one more
--  value (a button label, a background image credit).
-- =============================================================================

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


-- Short bullet lists that hang off a section: the mission page's values, the
-- projects page's impact points, the donation page's "how your money is used".
CREATE TABLE IF NOT EXISTS bullet_points (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  page        VARCHAR(40)  NOT NULL,
  section_key VARCHAR(60)  NOT NULL DEFAULT 'default',
  text        VARCHAR(400) NOT NULL,
  icon        VARCHAR(60)  NULL,
  sort_order  INT          NOT NULL DEFAULT 0,
  status      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_slot (page, section_key, status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
--  3. HOMEPAGE BLOCKS
-- =============================================================================

-- The three cards that overlap the hero.
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


-- "Our Focus Areas" — the carousel of programme areas.
CREATE TABLE IF NOT EXISTS focus_areas (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(160) NOT NULL,
  description TEXT         NULL,
  detail      LONGTEXT     NULL,
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

ALTER TABLE focus_areas
  ADD COLUMN IF NOT EXISTS detail LONGTEXT NULL AFTER description;


-- The mission page's focus cards are separate from the homepage programme list.
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


-- "Get Involved" — volunteer / donate / partner / share.
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


-- =============================================================================
--  4. CONTENT COLLECTIONS
-- =============================================================================

CREATE TABLE IF NOT EXISTS services (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(160) NOT NULL,
  description TEXT         NULL,
  icon        VARCHAR(255) NULL,
  image       VARCHAR(255) NULL,
  link_url    VARCHAR(255) NULL,
  sort_order  INT          NOT NULL DEFAULT 0,
  status      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_order (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


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


-- The animated statistics. `value` is text, not a number: some of these read
-- "10K" or "24/7" and forcing them into an INT would lose that.
CREATE TABLE IF NOT EXISTS counters (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  label      VARCHAR(160) NOT NULL,
  value      VARCHAR(20)  NOT NULL,
  suffix     VARCHAR(10)  NULL,
  icon       VARCHAR(255) NULL,
  sort_order INT          NOT NULL DEFAULT 0,
  status     TINYINT(1)   NOT NULL DEFAULT 1,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_order (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS partners (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(160) NOT NULL,
  logo       VARCHAR(255) NULL,
  url        VARCHAR(255) NULL,
  sort_order INT          NOT NULL DEFAULT 0,
  status     TINYINT(1)   NOT NULL DEFAULT 1,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_order (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Press coverage and news articles.
CREATE TABLE IF NOT EXISTS posts (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  title        VARCHAR(255) NOT NULL,
  slug         VARCHAR(255) NOT NULL,
  excerpt      TEXT         NULL,
  body         LONGTEXT     NULL,
  image        VARCHAR(255) NULL,
  category     VARCHAR(80)  NULL,
  author       VARCHAR(120) NULL,
  source       VARCHAR(160) NULL,
  source_url   VARCHAR(255) NULL,
  published_on DATE         NULL,
  sort_order   INT          NOT NULL DEFAULT 0,
  status       TINYINT(1)   NOT NULL DEFAULT 1,
  created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_slug (slug),
  KEY idx_live (status, published_on)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS events (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(255) NOT NULL,
  description TEXT         NULL,
  image       VARCHAR(255) NULL,
  event_date  DATE         NULL,
  event_time  VARCHAR(60)  NULL,
  location    VARCHAR(255) NULL,
  link_url    VARCHAR(255) NULL,
  sort_order  INT          NOT NULL DEFAULT 0,
  status      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_live (status, event_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS awards (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(255) NOT NULL,
  description TEXT         NULL,
  awarded_by  VARCHAR(160) NULL,
  awarded_on  DATE         NULL,
  icon        VARCHAR(255) NULL,
  sort_order  INT          NOT NULL DEFAULT 0,
  status      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_order (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- The public "Certificates & Achievements" wall. Deliberately separate from
-- the existing `certificates` table, which records certificates ISSUED to
-- individuals (name, course, reference) and is a different thing entirely.
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


-- Messages from the contact form.
CREATE TABLE IF NOT EXISTS contact_messages (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(160) NOT NULL,
  email      VARCHAR(190) NULL,
  phone      VARCHAR(40)  NULL,
  subject    VARCHAR(255) NULL,
  message    TEXT         NULL,
  ip_address VARCHAR(45)  NULL,
  status     VARCHAR(20)  NOT NULL DEFAULT 'new',
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_status (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
--  5. EXTENSIONS TO EXISTING TABLES
-- -----------------------------------------------------------------------------
--  MariaDB supports ADD COLUMN IF NOT EXISTS, so these are safe to re-run.
-- =============================================================================

ALTER TABLE banners
  ADD COLUMN IF NOT EXISTS subtitle   VARCHAR(255) NULL AFTER title,
  ADD COLUMN IF NOT EXISTS link_url   VARCHAR(255) NULL AFTER image_path,
  ADD COLUMN IF NOT EXISTS link_label VARCHAR(60)  NULL AFTER link_url,
  ADD COLUMN IF NOT EXISTS sort_order INT NOT NULL DEFAULT 0 AFTER link_label;

CREATE TABLE IF NOT EXISTS team_members (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(255) NOT NULL,
  position    VARCHAR(160) NOT NULL,
  email       VARCHAR(190) NULL,
  phone       VARCHAR(32) NULL,
  image       VARCHAR(255) NULL,
  bio         TEXT NULL,
  facebook    VARCHAR(255) NULL,
  twitter     VARCHAR(255) NULL,
  linkedin    VARCHAR(255) NULL,
  instagram   VARCHAR(255) NULL,
  sort_order  INT NOT NULL DEFAULT 0,
  status      VARCHAR(20) NOT NULL DEFAULT 'Active',
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_team_order (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS participants (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  full_name       VARCHAR(255) NOT NULL,
  mobile          VARCHAR(10) NOT NULL,
  email           VARCHAR(190) NOT NULL,
  password_hash   VARCHAR(255) NULL,
  dob             DATE NOT NULL,
  gender          VARCHAR(20) NOT NULL,
  marital_status  VARCHAR(20) NOT NULL,
  guardian_name   VARCHAR(255) NOT NULL,
  aadhar          VARCHAR(12) NOT NULL,
  category        VARCHAR(40) NOT NULL,
  blood_group     VARCHAR(3) NULL,
  occupation      VARCHAR(80) NOT NULL,
  address         TEXT NOT NULL,
  pincode         VARCHAR(6) NOT NULL,
  state           VARCHAR(80) NOT NULL,
  district        VARCHAR(80) NOT NULL,
  designation     VARCHAR(80) NOT NULL,
  profile_photo   VARCHAR(255) NULL,
  status          TINYINT(1) NOT NULL DEFAULT 0,
  submitted_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_participants_status (status, submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE team_members
  ADD COLUMN IF NOT EXISTS position   VARCHAR(160) NOT NULL DEFAULT '',
  ADD COLUMN IF NOT EXISTS email      VARCHAR(190) NULL,
  ADD COLUMN IF NOT EXISTS phone      VARCHAR(32) NULL,
  ADD COLUMN IF NOT EXISTS image      VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS bio        TEXT NULL,
  ADD COLUMN IF NOT EXISTS facebook   VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS twitter    VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS linkedin   VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS instagram  VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS sort_order INT NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS status     VARCHAR(20) NOT NULL DEFAULT 'Active',
  ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE team_members
  MODIFY status VARCHAR(20) NOT NULL DEFAULT 'Active';

UPDATE team_members
   SET status = CASE
       WHEN LOWER(CAST(status AS CHAR)) IN ('0', 'inactive', 'hidden') THEN 'Inactive'
       ELSE 'Active'
   END;

ALTER TABLE gallery
  ADD COLUMN IF NOT EXISTS title      VARCHAR(255) NULL AFTER image_path,
  ADD COLUMN IF NOT EXISTS category   VARCHAR(80)  NULL AFTER alt_text,
  ADD COLUMN IF NOT EXISTS sort_order INT        NOT NULL DEFAULT 0 AFTER category,
  ADD COLUMN IF NOT EXISTS status     TINYINT(1) NOT NULL DEFAULT 1 AFTER sort_order,
  ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

CREATE TABLE IF NOT EXISTS achievements (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(255) NOT NULL,
  description TEXT         NULL,
  category    VARCHAR(80)  NULL,
  year        VARCHAR(10)  NULL,
  highlight   VARCHAR(160) NULL,
  image       VARCHAR(255) NULL,
  icon        VARCHAR(255) NULL,
  sort_order  INT          NOT NULL DEFAULT 0,
  status      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_order (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE achievements
  ADD COLUMN IF NOT EXISTS category   VARCHAR(80)  NULL AFTER description,
  ADD COLUMN IF NOT EXISTS year       VARCHAR(10)  NULL AFTER category,
  ADD COLUMN IF NOT EXISTS highlight  VARCHAR(160) NULL AFTER year,
  ADD COLUMN IF NOT EXISTS image      VARCHAR(255) NULL AFTER highlight,
  ADD COLUMN IF NOT EXISTS icon       VARCHAR(255) NULL AFTER image,
  ADD COLUMN IF NOT EXISTS sort_order INT NOT NULL DEFAULT 0 AFTER icon,
  ADD COLUMN IF NOT EXISTS status     TINYINT(1) NOT NULL DEFAULT 1 AFTER sort_order;

CREATE TABLE IF NOT EXISTS projects (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(255) NOT NULL,
  slug          VARCHAR(255) NULL,
  location      VARCHAR(255) NULL,
  category      VARCHAR(80)  NULL,
  description   TEXT         NULL,
  beneficiaries INT          NULL,
  progress      TINYINT      NOT NULL DEFAULT 0,
  goal_amount   DECIMAL(12,2) NULL,
  raised_amount DECIMAL(12,2) NULL,
  image         VARCHAR(255) NULL,
  is_featured   TINYINT(1)   NOT NULL DEFAULT 0,
  sort_order    INT          NOT NULL DEFAULT 0,
  status        VARCHAR(20)  NOT NULL DEFAULT 'Active',
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_projects_order (status, is_featured, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE projects
  ADD COLUMN IF NOT EXISTS slug        VARCHAR(255) NULL AFTER name,
  ADD COLUMN IF NOT EXISTS category    VARCHAR(80)  NULL AFTER location,
  ADD COLUMN IF NOT EXISTS progress    TINYINT      NOT NULL DEFAULT 0 AFTER beneficiaries,
  ADD COLUMN IF NOT EXISTS goal_amount DECIMAL(12,2) NULL AFTER progress,
  ADD COLUMN IF NOT EXISTS raised_amount DECIMAL(12,2) NULL AFTER goal_amount,
  ADD COLUMN IF NOT EXISTS is_featured TINYINT(1)   NOT NULL DEFAULT 0 AFTER image,
  ADD COLUMN IF NOT EXISTS sort_order  INT          NOT NULL DEFAULT 0 AFTER is_featured;

ALTER TABLE missions
  ADD COLUMN IF NOT EXISTS icon       VARCHAR(255) NULL AFTER description,
  ADD COLUMN IF NOT EXISTS image      VARCHAR(255) NULL AFTER icon,
  ADD COLUMN IF NOT EXISTS sort_order INT NOT NULL DEFAULT 0 AFTER image;

ALTER TABLE certificates
  ADD COLUMN IF NOT EXISTS email      VARCHAR(190) NULL AFTER name,
  ADD COLUMN IF NOT EXISTS sort_order INT NOT NULL DEFAULT 0;

ALTER TABLE admin_users
  ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) NULL AFTER email;

SET FOREIGN_KEY_CHECKS = 1;

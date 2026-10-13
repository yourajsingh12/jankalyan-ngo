-- Project table and starter data for the Jan Kalyan NGO projects page.
-- Safe to run more than once: starter rows are inserted only when their name
-- does not already exist.

CREATE TABLE IF NOT EXISTS projects (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(255) NOT NULL,
  slug          VARCHAR(255) NULL,
  location      VARCHAR(255) NULL,
  category      VARCHAR(80)  NULL,
  description   TEXT         NULL,
  full_details  LONGTEXT     NULL,
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

INSERT INTO projects
  (name, slug, location, category, description, full_details, beneficiaries, progress, goal_amount, raised_amount, image, is_featured, sort_order, status)
SELECT
  'Learning For Every Child',
  'learning-for-every-child',
  'Indore',
  'Education',
  'Providing learning materials, mentoring and safe spaces that help children stay connected to education.',
  'Learning For Every Child gives children access to learning materials, mentoring, and a safe study environment so they can stay engaged with education and build a stronger future.',
  850,
  78,
  500000.00,
  390000.00,
  'assets/img/coaching/01.jpg',
  1,
  1,
  'Active'
WHERE NOT EXISTS (
  SELECT 1 FROM projects WHERE name = 'Learning For Every Child'
);

INSERT INTO projects
  (name, slug, location, category, description, full_details, beneficiaries, progress, goal_amount, raised_amount, image, is_featured, sort_order, status)
SELECT
  'Healthy Family Outreach',
  'healthy-family-outreach',
  'Bhopal',
  'Healthcare',
  'Health checkups, nutrition support and awareness sessions for families who need care close to home.',
  'Healthy Family Outreach brings health checkups, nutrition guidance, and awareness sessions directly to families who need support, helping them improve health, prevent disease, and strengthen everyday wellbeing.',
  420,
  64,
  350000.00,
  224000.00,
  'assets/img/coaching/02.jpg',
  0,
  2,
  'Active'
WHERE NOT EXISTS (
  SELECT 1 FROM projects WHERE name = 'Healthy Family Outreach'
);

INSERT INTO projects
  (name, slug, location, category, description, full_details, beneficiaries, progress, goal_amount, raised_amount, image, is_featured, sort_order, status)
SELECT
  'Women And Youth Skills',
  'women-and-youth-skills',
  'Ujjain',
  'Empowerment',
  'Building confidence and earning opportunities through practical skills and community leadership.',
  'Women And Youth Skills creates practical opportunities for women and youth to learn income-generating skills, build leadership confidence, and become active contributors to their communities.',
  380,
  72,
  400000.00,
  288000.00,
  'assets/img/coaching/03.jpg',
  0,
  3,
  'Active'
WHERE NOT EXISTS (
  SELECT 1 FROM projects WHERE name = 'Women And Youth Skills'
);

INSERT INTO projects
  (name, slug, location, category, description, full_details, beneficiaries, progress, goal_amount, raised_amount, image, is_featured, sort_order, status)
SELECT
  'Greener Neighbourhoods',
  'greener-neighbourhoods',
  'Jabalpur',
  'Environment',
  'Supporting local action for cleaner surroundings, environmental awareness and sustainable living.',
  'Greener Neighbourhoods promotes cleaner surroundings, environmental awareness, and sustainable community action so local families can live healthier and more resilient lives.',
  1200,
  55,
  300000.00,
  165000.00,
  'assets/img/coaching/04.jpg',
  0,
  4,
  'Active'
WHERE NOT EXISTS (
  SELECT 1 FROM projects WHERE name = 'Greener Neighbourhoods'
);

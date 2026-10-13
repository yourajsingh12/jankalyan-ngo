-- Campaigns page schema and sample data for Jan Kalyan Sangh Foundation.
-- Safe to run more than once: existing campaign names are not duplicated.

CREATE TABLE IF NOT EXISTS campaigns (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  title         VARCHAR(255) NOT NULL,
  category      VARCHAR(80) NOT NULL,
  description   TEXT NOT NULL,
  full_details  LONGTEXT NOT NULL,
  image         VARCHAR(255) NOT NULL,
  start_date    DATE NOT NULL,
  end_date      DATE NOT NULL,
  goal_amount   DECIMAL(12,2) NOT NULL DEFAULT 0,
  raised_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  supporters    INT UNSIGNED NOT NULL DEFAULT 0,
  status        VARCHAR(20) NOT NULL DEFAULT 'Active',
  is_featured   TINYINT(1) NOT NULL DEFAULT 0,
  sort_order    INT NOT NULL DEFAULT 0,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_campaigns_order (status, is_featured, sort_order),
  KEY idx_campaigns_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO campaigns
  (title, category, description, full_details, image, start_date, end_date, goal_amount, raised_amount, supporters, status, is_featured, sort_order)
SELECT
  'Nutrition for Growing Children',
  'Food & Nutrition',
  'Help provide nutritious meals and nutrition guidance to children and families who need support.',
  'This campaign brings nutritious meals and practical nutrition guidance to children and families facing food insecurity. Contributions help provide balanced food, organize community nutrition sessions, and connect families with local support. Our team works with community volunteers to reach households and monitor the programme so that assistance reaches those who need it most.',
  'assets/img/coaching/01.jpg',
  '2026-10-01',
  '2026-12-31',
  500000.00,
  0.00,
  0,
  'Active',
  1,
  1
WHERE NOT EXISTS (SELECT 1 FROM campaigns WHERE title = 'Nutrition for Growing Children');

INSERT INTO campaigns
  (title, category, description, full_details, image, start_date, end_date, goal_amount, raised_amount, supporters, status, is_featured, sort_order)
SELECT
  'Learning Support for Every Child',
  'Education Support',
  'Support learning materials, mentoring and safe study spaces for children in underserved communities.',
  'Learning Support for Every Child helps children continue their education with school supplies, learning materials, mentoring, and safe spaces to study. The campaign works with families and community educators to identify learning needs and help young people build confidence and skills for the future.',
  'assets/img/coaching/02.jpg',
  '2026-10-03',
  '2027-02-28',
  300000.00,
  12000.00,
  1,
  'Active',
  1,
  2
WHERE NOT EXISTS (SELECT 1 FROM campaigns WHERE title = 'Learning Support for Every Child');

INSERT INTO campaigns
  (title, category, description, full_details, image, start_date, end_date, goal_amount, raised_amount, supporters, status, is_featured, sort_order)
SELECT
  'Healthy Communities Outreach',
  'Healthcare',
  'Bring basic health awareness, checkups and wellbeing support closer to local families.',
  'Healthy Communities Outreach connects families with basic health awareness, community checkups, and practical wellbeing support. Through local outreach and trusted volunteers, the campaign helps people access information and services that can support healthier, more resilient communities.',
  'assets/img/coaching/03.jpg',
  '2026-08-11',
  '2026-09-30',
  100000.00,
  100000.00,
  2,
  'Completed',
  0,
  3
WHERE NOT EXISTS (SELECT 1 FROM campaigns WHERE title = 'Healthy Communities Outreach');

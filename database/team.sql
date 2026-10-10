-- Team member table and fields used by the admin and public team pages.
-- Safe to run repeatedly; existing team records are preserved.

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

-- Example starter records are added only when there are no team members yet.
-- Replace these sample names/details with the organisation's actual team.
INSERT INTO team_members (name, position, bio, sort_order, status)
SELECT sample.name, sample.position, sample.bio, sample.sort_order, 'Active'
FROM (
  SELECT 'Sample Member 1' AS name, 'Executive Director' AS position,
         'Replace with the team member biography.' AS bio, 1 AS sort_order
  UNION ALL
  SELECT 'Sample Member 2', 'Program Manager',
         'Replace with the team member biography.', 2
  UNION ALL
  SELECT 'Sample Member 3', 'Community Coordinator',
         'Replace with the team member biography.', 3
  UNION ALL
  SELECT 'Sample Member 4', 'Finance Officer',
         'Replace with the team member biography.', 4
) AS sample
WHERE NOT EXISTS (SELECT 1 FROM team_members);

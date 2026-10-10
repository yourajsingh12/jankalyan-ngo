-- Dashboard summary cards.
-- Run this file once against the configured NGO database before opening the
-- admin dashboard. Re-running it preserves changes made in the admin panel.

CREATE TABLE IF NOT EXISTS dashboard_stats (
  stat_key   VARCHAR(40)  NOT NULL PRIMARY KEY,
  label      VARCHAR(80)  NOT NULL,
  value      VARCHAR(100) NOT NULL,
  trend      VARCHAR(100) NOT NULL DEFAULT '',
  icon       VARCHAR(20)  NOT NULL,
  color      VARCHAR(7)   NOT NULL,
  sort_order INT          NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO dashboard_stats (stat_key, label, value, trend, icon, color, sort_order) VALUES
  ('donations', 'Total Donations', '₹ 4,82,500', '+18.2%', '💰', '#22c55e', 1),
  ('programs', 'Active Programs', '12', '+3 this month', '📚', '#0f766e', 2),
  ('beneficiaries', 'Beneficiaries', '8,240', '+12.4%', '🤝', '#f59e0b', 3),
  ('volunteers', 'Volunteers', '356', '+9 new', '👥', '#ec4899', 4);

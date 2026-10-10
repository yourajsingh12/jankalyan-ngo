-- Donation ledger used by the admin Donations page and dashboard Top Donors.
-- Import this file before adding or managing donation records.
-- No sample donations are inserted, so dashboard totals only show real entries.

CREATE TABLE IF NOT EXISTS donation_records (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  donor_name     VARCHAR(160) NOT NULL,
  donor_mobile   VARCHAR(20) NULL,
  amount         DECIMAL(12,2) NOT NULL,
  donation_date  DATE NOT NULL,
  payment_method VARCHAR(40) NULL,
  purpose        VARCHAR(255) NULL,
  status         VARCHAR(20) NOT NULL DEFAULT 'Confirmed',
  created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_donation_date (donation_date, id),
  KEY idx_donation_status_donor (status, donor_name),
  CONSTRAINT chk_donation_amount_positive CHECK (amount > 0),
  CONSTRAINT chk_donation_status CHECK (status IN ('Confirmed', 'Pending', 'Cancelled'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Keep existing installs compatible when adding donor contact numbers.
ALTER TABLE donation_records
  ADD COLUMN IF NOT EXISTS donor_mobile VARCHAR(20) NULL AFTER donor_name;

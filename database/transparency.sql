-- -------------------------------------------------------------
-- Jan Kalyan Sangh Foundation
-- Public transparency reports
-- -------------------------------------------------------------

CREATE TABLE IF NOT EXISTS transparency_documents (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(255) NOT NULL,
  category    VARCHAR(80) NOT NULL DEFAULT 'Other',
  description TEXT NULL,
  report_year VARCHAR(20) NULL,
  file_path   VARCHAR(500) NOT NULL,
  sort_order  INT NOT NULL DEFAULT 0,
  status      TINYINT(1) NOT NULL DEFAULT 1,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_transparency_order (status, sort_order, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO transparency_documents
  (title, category, description, report_year, file_path, sort_order, status)
SELECT 'Annual Report', 'Annual Report',
       'Read our annual report and learn more about our work and use of resources.',
       NULL, 'assets/img/pdf/Annual Report.pdf', 1, 1
WHERE NOT EXISTS (
  SELECT 1 FROM transparency_documents
  WHERE file_path = 'assets/img/pdf/Annual Report.pdf'
);

INSERT INTO transparency_documents
  (title, category, description, report_year, file_path, sort_order, status)
SELECT 'Audit Report 2021-22', 'Audit Report',
       'View the audited financial report for the 2021-22 financial year.',
       '2021-22', 'assets/img/pdf/Audit-Report-2021-22.pdf', 2, 1
WHERE NOT EXISTS (
  SELECT 1 FROM transparency_documents
  WHERE file_path = 'assets/img/pdf/Audit-Report-2021-22.pdf'
);

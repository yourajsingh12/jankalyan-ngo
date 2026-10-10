-- Donate page content for the public page and admin editor.
-- Run this once against the NGO database (for example, through phpMyAdmin).
-- INSERT IGNORE keeps existing admin edits intact when this file is run again.

CREATE TABLE IF NOT EXISTS donate_page_content (
  id                   TINYINT UNSIGNED NOT NULL PRIMARY KEY,
  breadcrumb_title     VARCHAR(160) NOT NULL,
  tagline              VARCHAR(160) NOT NULL,
  heading              VARCHAR(180) NOT NULL,
  heading_highlight    VARCHAR(180) NOT NULL,
  intro                TEXT NOT NULL,
  bank_section_title   VARCHAR(160) NOT NULL,
  bank_name_label      VARCHAR(80) NOT NULL,
  bank_name            VARCHAR(120) NOT NULL,
  account_number_label VARCHAR(80) NOT NULL,
  account_number       VARCHAR(80) NOT NULL,
  ifsc_label           VARCHAR(80) NOT NULL,
  ifsc_code            VARCHAR(30) NOT NULL,
  micr_label           VARCHAR(80) NOT NULL,
  micr_code            VARCHAR(30) NOT NULL,
  impact_title         VARCHAR(160) NOT NULL,
  impact_intro         TEXT NOT NULL,
  benefit_1            VARCHAR(255) NOT NULL,
  benefit_2            VARCHAR(255) NOT NULL,
  benefit_3            VARCHAR(255) NOT NULL,
  benefit_4            VARCHAR(255) NOT NULL,
  contact_title        VARCHAR(160) NOT NULL,
  contact_phone        VARCHAR(50) NOT NULL,
  contact_email        VARCHAR(190) NOT NULL,
  qr_code_image        VARCHAR(255) NULL,
  closing_message      TEXT NOT NULL,
  updated_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Keep existing installs compatible when adding QR support.
ALTER TABLE donate_page_content
  ADD COLUMN IF NOT EXISTS qr_code_image VARCHAR(255) NULL AFTER contact_email;

INSERT IGNORE INTO donate_page_content (
  id, breadcrumb_title, tagline, heading, heading_highlight, intro,
  bank_section_title, bank_name_label, bank_name, account_number_label,
  account_number, ifsc_label, ifsc_code, micr_label, micr_code,
  impact_title, impact_intro, benefit_1, benefit_2, benefit_3, benefit_4,
  contact_title, contact_phone, contact_email, closing_message
) VALUES (
  1,
  'Support Our Mission',
  'Make A Difference',
  'Your support creates',
  'lasting change',
  'Your generous contributions help us continue our mission and expand our reach to those in need. Together, we can make a difference.',
  'NGO Bank Account Details',
  'Bank Name',
  'HDFC BANK',
  'Account Number',
  '50200101465765',
  'IFSC Code',
  'HDFC0002645',
  'MICR',
  '1100240248',
  'Why Your Donation Matters',
  'Your support directly impacts our community. With your donations, we can:',
  'Support education and learning opportunities.',
  'Provide health and nutrition assistance.',
  'Expand community welfare programs.',
  'Bring hope and dignity to families in need.',
  'Need More Information?',
  '+91 8929939502',
  'support@jankalyansanghfoundation.com',
  'Join us in our journey to create a better future for everyone. Your contribution, no matter how small, helps us achieve our goals and bring hope to many lives.'
);

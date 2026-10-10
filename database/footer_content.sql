-- Update the footer copy and contact information without replacing other site settings.
-- Safe to run repeatedly; the supplied settings are updated in place.

SET NAMES utf8mb4;

INSERT INTO site_settings
  (setting_key, setting_value, setting_group, label, input_type, sort_order)
VALUES
  ('footer_about', 'We build a stronger tomorrow by supporting education, health, dignity, and community development for underserved families across India.', 'footer', 'Footer about text', 'textarea', 1),
  ('contact_address', '252-A/2, KH No-44/18,<br>Nangli Vihar Extn., Baprola BLK C5&amp;C6,<br>New Delhi, India', 'contact', 'Head office address', 'textarea', 4),
  ('contact_address_plain', '252-A/2, KH No-44/18, Nangli Vihar Extn., Baprola BLK C5 & C6, New Delhi, India', 'contact', 'Address (single line)', 'text', 5),
  ('contact_phone', '+91 8929939502', 'contact', 'Phone number', 'text', 2),
  ('contact_phone_raw', '+918929939502', 'contact', 'Phone (dial format)', 'text', 3),
  ('contact_email', 'support@jankalyansanghfoundation.com', 'contact', 'Email address', 'text', 1)
ON DUPLICATE KEY UPDATE
  setting_value = VALUES(setting_value),
  setting_group = VALUES(setting_group),
  label = VALUES(label),
  input_type = VALUES(input_type),
  sort_order = VALUES(sort_order);

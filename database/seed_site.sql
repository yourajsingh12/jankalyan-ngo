-- =============================================================================
--  Seed: site-wide chrome and the homepage
-- -----------------------------------------------------------------------------
--  Every value here was lifted verbatim from the hardcoded templates, so the
--  site renders identically the moment the pages start reading from the
--  database. Nothing is invented and nothing is dropped.
--
--  Re-runnable: each block clears its own table first, so running this again
--  restores the shipped content rather than duplicating it.
-- =============================================================================

SET NAMES utf8mb4;

-- Footer link columns are menus too, so they need to say which menu they are in.
ALTER TABLE menu_items
  ADD COLUMN IF NOT EXISTS location VARCHAR(30) NOT NULL DEFAULT 'main' AFTER url;


-- =============================================================================
--  SITE SETTINGS
-- =============================================================================
DELETE FROM site_settings;

INSERT INTO site_settings (setting_key, setting_value, setting_group, label, input_type, sort_order) VALUES
('site_name',        'Jan Kalyan Sangh Foundation', 'general', 'Organisation name',   'text',     1),
('site_short_name',  'JKSF',                        'general', 'Short name',          'text',     2),
('site_tagline',     'Serving communities with care, dignity and purpose', 'general', 'Tagline', 'text', 3),
('logo',             'assets/img/logo/study.png',   'general', 'Logo',                'image',    4),
('favicon',          'assets/img/logo/study.png',   'general', 'Favicon',             'image',    5),
('meta_description', 'Jan Kalyan Sangh Foundation empowers underserved communities across India through education, healthcare, social awareness and community development.', 'seo', 'Default meta description', 'textarea', 1),
('meta_keywords',    'NGO Delhi, Jan Kalyan Sangh Foundation, education for children, health camps, women empowerment, community development, donate India', 'seo', 'Default meta keywords', 'textarea', 2),

('contact_email',    'support@jankalyansanghfoundation.com', 'contact', 'Email address', 'text', 1),
('contact_phone',    '+91 8929939502',              'contact', 'Phone number',        'text',     2),
('contact_phone_raw','+918929939502',               'contact', 'Phone (dial format)', 'text',     3),
('contact_address',  '252-A/2, KH No-44/18,<br>Nangli Vihar Extn., Baprola BLK C5&amp;C6,<br>New Delhi, India', 'contact', 'Head office address', 'textarea', 4),
('contact_address_plain', '252-A/2, KH No-44/18, Nangli Vihar Extn., Baprola BLK C5 & C6, New Delhi, India', 'contact', 'Address (single line)', 'text', 5),
('contact_map_embed','',                            'contact', 'Google Maps embed URL','text',    6),
('office_hours',     'Mon – Sat, 10:00 AM – 6:00 PM','contact','Office hours',        'text',     7),

('footer_about',     'We build a stronger tomorrow by supporting education, health, dignity, and community development for underserved families across India.', 'footer', 'Footer about text', 'textarea', 1),
('footer_copyright', 'All Rights Reserved.',        'footer', 'Copyright line',      'text',     2),
('newsletter_heading','Subscribe Our Newsletter',   'footer', 'Newsletter heading',  'text',     3),

('sidebar_about_heading', 'About Us',               'sidebar','Slide-out panel heading','text',   1),
('sidebar_about_text', 'Jan Kalyan Sangh Foundation works alongside underserved communities across India — supporting children''s education, running health and nutrition camps, and creating opportunities for women and families to build a life of dignity.', 'sidebar', 'Slide-out panel text', 'textarea', 2),

('donate_button_label','Donate',                    'general','Header button label',  'text',     6),
('donate_button_url', 'donate.php',                 'general','Header button link',   'text',     7);


-- =============================================================================
--  NAVIGATION
-- =============================================================================
DELETE FROM menu_items;

INSERT INTO menu_items (label, url, location, sort_order, status) VALUES
('Home',         'index.php',        'main', 1,  1),
('About',        'about.php',        'main', 2,  1),
('Certificate',  'certificate.php',  'main', 3,  1),
('Achievements', 'achievements.php', 'main', 4,  1),
('Projects',     'projects.php',     'main', 5,  1),
('Team',         'team.php',         'main', 6,  1),
('Mission',      'mission.php',      'main', 7,  1),
('Participate',  'participate.php',  'main', 8,  1),
('Gallery',      'gallery.php',      'main', 9,  1),
('Contact',      'contact.php',      'main', 10, 1),

('About Us',     'about.php',        'footer_quick', 1, 1),
('Mission',      'mission.php',      'footer_quick', 2, 1),
('Projects',     'projects.php',     'footer_quick', 3, 1),
('Gallery',      'gallery.php',      'footer_quick', 4, 1),
('Contact',      'contact.php',      'footer_quick', 5, 1),

('Education',        'about.php',    'footer_focus', 1, 1),
('Health & Nutrition','mission.php', 'footer_focus', 2, 1),
('Women Empowerment','projects.php', 'footer_focus', 3, 1),
('Community Support','donate.php',   'footer_focus', 4, 1);


-- =============================================================================
--  SOCIAL PROFILES
-- =============================================================================
DELETE FROM social_links;

INSERT INTO social_links (platform, icon_class, url, sort_order, status) VALUES
('Facebook',  'fab fa-facebook',    'https://www.facebook.com/profile.php?id=61560875186705', 1, 1),
('X',         'fab fa-x-twitter',   'https://x.com/official_jksf',                            2, 1),
('YouTube',   'fab fa-youtube',     'https://www.youtube.com/@JanKalyanSanghFoundation',      3, 1),
('LinkedIn',  'fab fa-linkedin-in', 'https://www.linkedin.com/in/jan-kalyan-sangh-foundation-82b770331/', 4, 1),
('Instagram', 'fab fa-instagram',   'https://www.instagram.com/jankalyansanghfoundation/',    5, 1);


-- =============================================================================
--  HEADER TICKER
-- =============================================================================
DELETE FROM ticker_items;

INSERT INTO ticker_items (text, sort_order, status) VALUES
('Health Checkup & Nutrition Distribution',      1, 1),
('Development Program for Weaker Community',     2, 1),
('De-Addiction Awareness Program',               3, 1);


-- =============================================================================
--  HERO SLIDES
-- -----------------------------------------------------------------------------
--  `banners` held four placeholder rows with no images. They are replaced by
--  the three slides the homepage was actually rendering.
-- =============================================================================
DELETE FROM banners;

INSERT INTO banners (page_name, title, subtitle, image_path, link_url, link_label, sort_order, status) VALUES
('home', 'Education For All',      'Every child deserves a classroom', 'assets/img/hero/banner1.png', 'donate.php', 'Donate Now', 1, 'Active'),
('home', 'Health & Nutrition',     'Care that reaches the last mile',  'assets/img/hero/banner2.png', 'donate.php', 'Donate Now', 2, 'Active'),
('home', 'Community Development',  'Building dignity and opportunity', 'assets/img/hero/banner3.png', 'donate.php', 'Donate Now', 3, 'Active');


-- =============================================================================
--  HOMEPAGE — FEATURE CARDS
-- =============================================================================
DELETE FROM home_features;

ALTER TABLE home_features
  ADD COLUMN IF NOT EXISTS detail LONGTEXT NULL AFTER description,
  ADD COLUMN IF NOT EXISTS image VARCHAR(255) NULL AFTER detail;

INSERT INTO home_features (title, description, detail, image, icon, sort_order, status) VALUES
('Education For All',
 'We support children''s education through school access, learning resources, and mentorship.',
 'We support children''s education by helping them access school, learning resources, and caring mentorship. Our work encourages regular learning and gives children a safe space to build confidence and curiosity.\n\nBy working with families, schools, and local communities, we help identify barriers to education and connect children with practical support. We believe every child deserves the opportunity to learn, grow, and shape a brighter future.',
 'assets/img/visa/seva1.png', 'assets/img/icon/assistance.svg', 1, 1),
('Health & Nutrition',
 'We organize health checkups, nutrition distribution, and awareness drives for rural families.',
 'We organize health checkups, nutrition distribution, and awareness activities for rural families. These services help people access basic care, understand everyday health needs, and seek timely support.\n\nOur outreach works alongside local communities to make health information easier to reach and to encourage healthier practices for children and families. We aim to support well-being with care, dignity, and sustained community participation.',
 'assets/img/visa/seva2.png', 'assets/img/icon/happy.svg', 2, 1),
('Community Development',
 'We work with local communities to build dignity, opportunity, and sustainable growth.',
 'We work with local communities to strengthen dignity, opportunity, and sustainable growth. Community members help identify priorities and take part in shaping practical solutions that respond to local needs.\n\nThrough awareness, collaboration, and inclusive support, we encourage families to access opportunities and build resilience. Our goal is lasting progress led with and for the communities we serve.',
 'assets/img/visa/seva1.png', 'assets/img/icon/team.svg', 3, 1);


-- =============================================================================
--  HOMEPAGE — FOCUS AREAS
-- =============================================================================
DELETE FROM focus_areas;

INSERT INTO focus_areas (title, description, detail, icon, image, link_url, link_label, sort_order, status) VALUES
('Education Support',
 'We promote access to school, learning materials, and academic encouragement for children.',
 'We help children continue their education by supporting access to school and learning materials, and by encouraging regular participation in learning. Families and community partners can help identify barriers that make it harder for children to attend and progress.\n\nThrough practical guidance and academic encouragement, children can build confidence, strengthen their skills, and explore future opportunities. Our education work aims to make learning more accessible and supportive for every child.',
 'assets/img/icon/student-visa.svg', 'assets/img/visa/seva1.png', 'donate.php', 'Support Now', 1, 1),
('Health & Nutrition',
 'Regular health camps, nutrition distribution, and awareness programs improve everyday well-being.',
 'We support community well-being through health camps, nutrition assistance, and practical awareness activities. These efforts help families learn about everyday health needs and connect with basic support in a welcoming, community-based setting.\n\nBy sharing clear information and encouraging timely care, we aim to help children and adults make informed choices for their health. Our approach focuses on dignity, prevention, and making essential support easier to reach.',
 'assets/img/icon/tourist-visa.svg', 'assets/img/visa/seva2.png', 'donate.php', 'Donate', 2, 1),
('Women Empowerment',
 'We support women through awareness, confidence-building, skill development, and inclusive participation.',
 'We work to strengthen women''s confidence, skills, and participation in community life. Awareness activities and opportunities to learn practical skills can help women make informed choices and take part in decisions that affect their families and communities.\n\nWe believe lasting progress is built by listening to women, recognizing their abilities, and supporting equal participation. Our work encourages connection, learning, and opportunities for women to shape their own goals.',
 'assets/img/icon/commercial-visa.svg', 'assets/img/visa/seva1.png', 'donate.php', 'Join Us', 3, 1),
('Community Support',
 'From emergency relief to local development, we stand beside communities in need.',
 'We work alongside communities during difficult times and support locally identified development priorities. Emergency assistance can help families address immediate needs, while continued cooperation helps communities plan for longer-term well-being.\n\nBy listening to residents and connecting people with practical support, we encourage shared solutions and stronger local participation. Our aim is to stand with communities with respect, care, and a commitment to sustainable progress.',
 'assets/img/icon/residence-visa.svg', 'assets/img/visa/seva2.png', 'donate.php', 'Help Today', 4, 1);


-- =============================================================================
--  HOMEPAGE — GET INVOLVED
-- =============================================================================
DELETE FROM process_steps;

INSERT INTO process_steps (title, description, icon, link_url, sort_order, status) VALUES
('Volunteer',         'Join our outreach programs and contribute your time, skills, and energy.',      'assets/img/icon/team.svg',     'participate.php', 1, 1),
('Support A Program', 'Your donation helps us provide education, food, care, and livelihood support.',  'assets/img/icon/loan.svg',     'donate.php',      2, 1),
('Partner With Us',   'Collaborate with us to expand social impact and serve more families.',          'assets/img/icon/document.svg', 'contact.php',     3, 1),
('Spread The Word',   'Share our mission and inspire others to build a brighter future together.',     'assets/img/icon/approval.svg', 'about.php',       4, 1);


-- =============================================================================
--  HOMEPAGE — SECTION COPY
-- =============================================================================
DELETE FROM page_sections WHERE page = 'home';

INSERT INTO page_sections (page, section_key, tagline, heading, subheading, body, image, extra, status) VALUES
('home', 'focus',  'Our Focus Areas', 'Building a <span>stronger</span> and healthier society', NULL, NULL, NULL, 'far fa-hands-helping', 1),
('home', 'cta',    NULL, 'We work for a <span>healthier, kinder, and stronger</span> tomorrow', NULL,
   'Jan Kalyan Sangh Foundation is committed to empowering underserved communities through education, healthcare, social awareness, and community development.',
   'assets/img/cta/flood.png', 'Donate Now|donate.php', 1),
('home', 'involve','Get Involved', 'Small actions create lasting change', NULL, NULL, NULL, 'fas fa-hands-helping', 1);

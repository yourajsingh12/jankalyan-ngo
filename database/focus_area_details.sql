-- Add editable long-form descriptions to Focus Area cards.
-- Safe to run more than once; only empty detail fields receive starter copy.

SET NAMES utf8mb4;

ALTER TABLE focus_areas
  ADD COLUMN IF NOT EXISTS detail LONGTEXT NULL AFTER description;

UPDATE focus_areas
SET detail = 'We help children continue their education by supporting access to school and learning materials, and by encouraging regular participation in learning. Families and community partners can help identify barriers that make it harder for children to attend and progress.\n\nThrough practical guidance and academic encouragement, children can build confidence, strengthen their skills, and explore future opportunities. Our education work aims to make learning more accessible and supportive for every child.'
WHERE title = 'Education Support'
  AND (detail IS NULL OR detail = '');

UPDATE focus_areas
SET detail = 'We support community well-being through health camps, nutrition assistance, and practical awareness activities. These efforts help families learn about everyday health needs and connect with basic support in a welcoming, community-based setting.\n\nBy sharing clear information and encouraging timely care, we aim to help children and adults make informed choices for their health. Our approach focuses on dignity, prevention, and making essential support easier to reach.'
WHERE title = 'Health & Nutrition'
  AND (detail IS NULL OR detail = '');

UPDATE focus_areas
SET detail = 'We work to strengthen women''s confidence, skills, and participation in community life. Awareness activities and opportunities to learn practical skills can help women make informed choices and take part in decisions that affect their families and communities.\n\nWe believe lasting progress is built by listening to women, recognizing their abilities, and supporting equal participation. Our work encourages connection, learning, and opportunities for women to shape their own goals.'
WHERE title = 'Women Empowerment'
  AND (detail IS NULL OR detail = '');

UPDATE focus_areas
SET detail = 'We work alongside communities during difficult times and support locally identified development priorities. Emergency assistance can help families address immediate needs, while continued cooperation helps communities plan for longer-term well-being.\n\nBy listening to residents and connecting people with practical support, we encourage shared solutions and stronger local participation. Our aim is to stand with communities with respect, care, and a commitment to sustainable progress.'
WHERE title = 'Community Support'
  AND (detail IS NULL OR detail = '');

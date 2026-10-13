-- Add the full details field for project pages and seed richer content.
-- Run this after the main schema is in place.

ALTER TABLE projects
  ADD COLUMN IF NOT EXISTS full_details LONGTEXT NULL AFTER description;

UPDATE projects
SET full_details = 'Learning For Every Child gives children access to learning materials, mentoring, and a safe study environment so they can stay engaged with education and build a stronger future.'
WHERE name = 'Learning For Every Child' AND (full_details IS NULL OR full_details = '');

UPDATE projects
SET full_details = 'Healthy Family Outreach brings health checkups, nutrition guidance, and awareness sessions directly to families who need support, helping them improve health, prevent disease, and strengthen everyday wellbeing.'
WHERE name = 'Healthy Family Outreach' AND (full_details IS NULL OR full_details = '');

UPDATE projects
SET full_details = 'Women And Youth Skills creates practical opportunities for women and youth to learn income-generating skills, build leadership confidence, and become active contributors to their communities.'
WHERE name = 'Women And Youth Skills' AND (full_details IS NULL OR full_details = '');

UPDATE projects
SET full_details = 'Greener Neighbourhoods promotes cleaner surroundings, environmental awareness, and sustainable community action so local families can live healthier and more resilient lives.'
WHERE name = 'Greener Neighbourhoods' AND (full_details IS NULL OR full_details = '');

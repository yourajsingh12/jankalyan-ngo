<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

function about_content_redirect(): never
{
    redirect('about_content.php');
}

function valid_about_path(string $path): bool
{
    if ($path === '' || preg_match('/[\x00-\x20]/', $path)) {
        return false;
    }

    if (preg_match('#^https?://#i', $path)) {
        return filter_var($path, FILTER_VALIDATE_URL) !== false;
    }

    return !str_starts_with($path, '//')
        && !preg_match('/^[a-z][a-z0-9+.-]*:/i', $path)
        && !str_contains($path, '\\');
}

$fields = [
    'page_title' => ['section' => 'page', 'column' => 'heading', 'label' => 'Breadcrumb title', 'default' => 'About Us', 'type' => 'text', 'max' => 120],
    'intro_tagline' => ['section' => 'intro', 'column' => 'tagline', 'label' => 'Introduction tagline', 'default' => 'About Us', 'type' => 'text', 'max' => 160],
    'intro_heading' => ['section' => 'intro', 'column' => 'heading', 'label' => 'Introduction heading', 'default' => 'Empowering communities through education, health, and dignity', 'type' => 'text', 'max' => 255],
    'intro_body' => ['section' => 'intro', 'column' => 'body', 'label' => 'Introduction description', 'default' => 'Jan Kalyan Sangh Foundation is a grassroots social impact organisation dedicated to uplifting underserved communities through education support, health awareness, women empowerment, and sustainable local development.', 'type' => 'textarea', 'max' => 10000],
    'image_one' => ['section' => 'intro', 'column' => 'image', 'label' => 'First About Us photo', 'default' => 'assets/img/about/about1.png', 'type' => 'image_upload', 'max' => 255],
    'image_two' => ['section' => 'intro', 'column' => 'extra', 'label' => 'Second About Us photo', 'default' => 'assets/img/about/about.png', 'type' => 'image_upload', 'max' => 255],
    'experience_years' => ['section' => 'experience', 'column' => 'heading', 'label' => 'Years of service', 'default' => '10', 'type' => 'number', 'max' => 4],
    'experience_label' => ['section' => 'experience', 'column' => 'body', 'label' => 'Experience label', 'default' => 'Years of Service', 'type' => 'text', 'max' => 120],
    'education_title' => ['section' => 'education', 'column' => 'heading', 'label' => 'Education point title', 'default' => 'Education Support', 'type' => 'text', 'max' => 160],
    'education_description' => ['section' => 'education', 'column' => 'body', 'label' => 'Education point description', 'default' => 'We help children continue their learning with school access, learning material, and mentorship.', 'type' => 'textarea', 'max' => 2000],
    'health_title' => ['section' => 'health', 'column' => 'heading', 'label' => 'Health point title', 'default' => 'Health & Awareness', 'type' => 'text', 'max' => 160],
    'health_description' => ['section' => 'health', 'column' => 'body', 'label' => 'Health point description', 'default' => 'We organize health camps, nutrition support, and awareness programs for rural families.', 'type' => 'textarea', 'max' => 2000],
    'children_value' => ['section' => 'counter_children', 'column' => 'heading', 'label' => 'Children supported count', 'default' => '5000', 'type' => 'number', 'max' => 8],
    'children_label' => ['section' => 'counter_children', 'column' => 'body', 'label' => 'Children counter label', 'default' => '+ Children Supported', 'type' => 'text', 'max' => 120],
    'families_value' => ['section' => 'counter_families', 'column' => 'heading', 'label' => 'Families reached count', 'default' => '6000', 'type' => 'number', 'max' => 8],
    'families_label' => ['section' => 'counter_families', 'column' => 'body', 'label' => 'Families counter label', 'default' => '+ Families Reached', 'type' => 'text', 'max' => 120],
    'villages_value' => ['section' => 'counter_villages', 'column' => 'heading', 'label' => 'Villages impacted count', 'default' => '40', 'type' => 'number', 'max' => 8],
    'villages_label' => ['section' => 'counter_villages', 'column' => 'body', 'label' => 'Villages counter label', 'default' => '+ Villages Impacted', 'type' => 'text', 'max' => 120],
    'programs_value' => ['section' => 'counter_programs', 'column' => 'heading', 'label' => 'Community programs count', 'default' => '25', 'type' => 'number', 'max' => 8],
    'programs_label' => ['section' => 'counter_programs', 'column' => 'body', 'label' => 'Programs counter label', 'default' => '+ Community Programs', 'type' => 'text', 'max' => 120],
    'explore_label' => ['section' => 'explore', 'column' => 'heading', 'label' => 'Explore button label', 'default' => 'Explore More', 'type' => 'text', 'max' => 60],
    'explore_url' => ['section' => 'explore', 'column' => 'extra', 'label' => 'Explore button link', 'default' => 'mission.php', 'type' => 'path', 'max' => 255],
];

$groups = [
    [
        'title' => 'Page heading & introduction',
        'description' => 'The breadcrumb and main About Us story shown at the top of the page.',
        'icon' => 'fa-align-left',
        'fields' => ['page_title', 'intro_tagline', 'intro_heading', 'intro_body', 'image_one', 'image_two'],
    ],
    [
        'title' => 'Years of service',
        'description' => 'The experience badge displayed beside the About Us images.',
        'icon' => 'fa-calendar-check',
        'fields' => ['experience_years', 'experience_label'],
    ],
    [
        'title' => 'About Us highlights',
        'description' => 'Education and health highlight cards, including their icons.',
        'icon' => 'fa-heart',
        'fields' => ['education_title', 'education_description', 'health_title', 'health_description'],
    ],
    [
        'title' => 'Impact counters',
        'description' => 'Update the numbers and labels shown in the impact counters.',
        'icon' => 'fa-chart-simple',
        'fields' => ['children_value', 'children_label', 'families_value', 'families_label', 'villages_value', 'villages_label', 'programs_value', 'programs_label'],
    ],
    [
        'title' => 'Explore button',
        'description' => 'Set the call-to-action text and where it links.',
        'icon' => 'fa-arrow-up-right-from-square',
        'fields' => ['explore_label', 'explore_url'],
    ],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $aboutSections = sections('about');
    $values = [];
    $invalid = false;

    foreach ($fields as $key => $field) {
        if ($field['type'] === 'image_upload') {
            $values[$key] = (string) ($aboutSections[$field['section']][$field['column']] ?? $field['default']);
            continue;
        }

        $value = trim((string) ($_POST[$key] ?? ''));
        if ($value === '' || mb_strlen($value) > $field['max']) {
            $invalid = true;
            break;
        }
        if ($field['type'] === 'number' && !preg_match('/^\d+$/', $value)) {
            $invalid = true;
            break;
        }
        if ($field['type'] === 'path' && !valid_about_path($value)) {
            $invalid = true;
            break;
        }
        $values[$key] = $value;
    }

    if ($invalid) {
        flash('Please check that all About Us fields contain valid values.', 'error');
        about_content_redirect();
    }

    $newImages = [];
    try {
        foreach (['image_one', 'image_two'] as $imageKey) {
            $uploadedImage = upload_image($_FILES[$imageKey] ?? null, 'about');
            if ($uploadedImage !== null) {
                $uploadedPath = BASE_PATH . DIRECTORY_SEPARATOR
                    . str_replace('/', DIRECTORY_SEPARATOR, $uploadedImage);
                $dimensions = @getimagesize($uploadedPath);
                if ($dimensions === false || $dimensions[0] !== 1086 || $dimensions[1] !== 1448) {
                    delete_upload($uploadedImage);
                    throw new RuntimeException('About Us photos must be exactly 1086 × 1448 pixels.');
                }
                $newImages[$imageKey] = $uploadedImage;
                $values[$imageKey] = $uploadedImage;
            }
        }
    } catch (RuntimeException $exception) {
        foreach ($newImages as $uploadedImage) {
            delete_upload($uploadedImage);
        }
        flash($exception->getMessage(), 'error');
        about_content_redirect();
    }

    $sectionsToSave = [];
    foreach ($fields as $key => $field) {
        if (str_ends_with($key, '_icon')) {
            continue;
        }
        $sectionKey = $field['section'];
        $sectionsToSave[$sectionKey][$field['column']] = $values[$key];
    }

    try {
        db()->beginTransaction();
        foreach ($sectionsToSave as $sectionKey => $sectionValues) {
            q(
                'INSERT INTO page_sections (page, section_key, tagline, heading, body, image, extra, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 1)
                 ON DUPLICATE KEY UPDATE tagline = VALUES(tagline), heading = VALUES(heading), body = VALUES(body), image = VALUES(image), extra = VALUES(extra), status = 1',
                [
                    'about',
                    $sectionKey,
                    $sectionValues['tagline'] ?? null,
                    $sectionValues['heading'] ?? null,
                    $sectionValues['body'] ?? null,
                    $sectionValues['image'] ?? null,
                    $sectionValues['extra'] ?? null,
                ]
            );
        }
        db()->commit();
        foreach ($newImages as $imageKey => $uploadedImage) {
            $field = $fields[$imageKey];
            $previousImage = $aboutSections[$field['section']][$field['column']] ?? null;
            if ($previousImage !== null) {
                delete_upload((string) $previousImage);
            }
        }
        flash('About Us content updated successfully.');
    } catch (PDOException $exception) {
        if (db()->inTransaction()) {
            db()->rollBack();
        }
        foreach ($newImages as $uploadedImage) {
            delete_upload($uploadedImage);
        }
        error_log('About Us content save failed: ' . $exception->getMessage());
        flash('About Us content could not be saved. Please try again.', 'error');
    }

    about_content_redirect();
}

$aboutSections = sections('about');
$values = [];
foreach ($fields as $key => $field) {
    $values[$key] = $aboutSections[$field['section']][$field['column']] ?? $field['default'];
}
$messages = take_flash();
$counterCards = [
    ['title' => 'Children supported', 'value' => 'children_value', 'label' => 'children_label'],
    ['title' => 'Families reached', 'value' => 'families_value', 'label' => 'families_label'],
    ['title' => 'Villages impacted', 'value' => 'villages_value', 'label' => 'villages_label'],
    ['title' => 'Community programs', 'value' => 'programs_value', 'label' => 'programs_label'],
];
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/header.php'; ?>
<body class="crm_body_bg">
    <?php include_once __DIR__ . '/sidebar.php'; ?>

    <section class="main_content dashboard_part large_header_bg" style="padding:24px;">
        <div class="container-fluid g-0">
            <div class="page_title_box d-flex flex-wrap justify-content-between align-items-center"
                style="background:linear-gradient(135deg,#0f172a,#0f766e); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(15,118,110,.18);">
                <div>
                    <h3 style="margin:0; font-size:1.8rem; color:#fff;">About Us Content</h3>
                    <p class="mb-0 mt-1" style="color:#d1fae5;">Manage each page section and preview its images.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="page_content.php" class="btn btn-outline-light"><i class="fas fa-arrow-left"></i> Pages</a>
                    <a href="../about.php" target="_blank" rel="noopener" class="btn btn-light"><i class="fas fa-external-link-alt"></i> View About Us</a>
                </div>
            </div>

            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>" role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
            <?php endforeach; ?>

            <style>
                .about-editor-card {
                    background: #fff;
                    border: 1px solid #e5e7eb;
                    border-radius: 18px;
                    box-shadow: 0 10px 25px rgba(15, 23, 42, .045);
                    margin-bottom: 22px;
                }
                .about-editor-heading {
                    border-bottom: 1px solid #eef2f7;
                    padding: 20px 22px 16px;
                }
                .about-editor-body { padding: 20px 22px 22px; }
                .about-field { min-width: 0; }
                .about-image-preview {
                    align-items: center;
                    background: #f8fafc;
                    border: 1px solid #e2e8f0;
                    border-radius: 12px;
                    display: flex;
                    height: 92px;
                    justify-content: center;
                    margin-bottom: 9px;
                    overflow: hidden;
                    padding: 8px;
                    width: 112px;
                }
                .about-image-preview img {
                    height: 100%;
                    max-width: 100%;
                    object-fit: contain;
                    width: 100%;
                }
                .about-image-preview.is-photo { width: 156px; }
                .about-image-preview.is-photo img { object-fit: cover; }
                .about-counter-card {
                    background: #f8fafc;
                    border: 1px solid #e8edf3;
                    border-radius: 14px;
                    padding: 16px;
                }
            </style>
            <form method="post" enctype="multipart/form-data" id="about-content-form">
                <?= csrf_field() ?>
                <?php foreach ($groups as $groupIndex => $group): ?>
                    <section class="about-editor-card">
                        <div class="about-editor-heading d-flex align-items-start gap-3">
                            <span class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                                style="width:42px;height:42px;background:#e6f5f2;color:#0f766e;">
                                <i class="fas <?= e($group['icon']) ?>"></i>
                            </span>
                            <div>
                                <h4 class="mb-1"><?= e($group['title']) ?></h4>
                                <p class="text-muted mb-0"><?= e($group['description']) ?></p>
                            </div>
                        </div>
                        <div class="about-editor-body">
                            <?php if ($groupIndex === 3): ?>
                                <div class="row g-3">
                                    <?php foreach ($counterCards as $counter):
                                    ?>
                                        <div class="col-xl-3 col-md-6">
                                            <div class="about-counter-card h-100">
                                                <div class="d-flex align-items-center gap-2 mb-3">
                                                    <span class="d-flex align-items-center justify-content-center rounded-circle"
                                                        style="width:34px;height:34px;background:#e6f5f2;color:#0f766e;"><i class="fas fa-chart-line"></i></span>
                                                    <strong><?= e($counter['title']) ?></strong>
                                                </div>
                                                <label class="form-label" for="<?= e($counter['value']) ?>">Count</label>
                                                <input class="form-control mb-3" id="<?= e($counter['value']) ?>" name="<?= e($counter['value']) ?>" type="number" min="0" maxlength="8" value="<?= e($values[$counter['value']]) ?>" required>
                                                <label class="form-label" for="<?= e($counter['label']) ?>">Display label</label>
                                                <input class="form-control mb-3" id="<?= e($counter['label']) ?>" name="<?= e($counter['label']) ?>" type="text" maxlength="120" value="<?= e($values[$counter['label']]) ?>" required>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="row g-3">
                                    <?php foreach ($group['fields'] as $key):
                                        $field = $fields[$key];
                                        $isLong = $field['type'] === 'textarea';
                                        $isImage = $field['type'] === 'image_upload';
                                        $imageUrl = $isImage ? img_url((string) $values[$key], $field['default']) : '';
                                        $previewClass = in_array($key, ['image_one', 'image_two'], true) ? 'is-photo' : '';
                                    ?>
                                        <div class="<?= $isLong ? 'col-12' : 'col-md-6' ?> about-field">
                                            <label class="form-label" for="<?= e($key) ?>"><?= e($field['label']) ?></label>
                                            <?php if ($isLong): ?>
                                                <textarea class="form-control" id="<?= e($key) ?>" name="<?= e($key) ?>" rows="4" maxlength="<?= (int) $field['max'] ?>" required><?= e($values[$key]) ?></textarea>
                                            <?php elseif ($isImage): ?>
                                                <div class="about-image-preview <?= e($previewClass) ?>">
                                                    <img src="<?= e($imageUrl) ?>" alt="<?= e($field['label']) ?> preview" data-image-preview="<?= e($key) ?>" data-original-src="<?= e($imageUrl) ?>">
                                                </div>
                                                <input class="form-control about-image-upload" id="<?= e($key) ?>" name="<?= e($key) ?>" type="file" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif" data-preview="<?= e($key) ?>">
                                                <small class="text-muted d-block mt-1" data-image-message="<?= e($key) ?>">Choose a photo sized exactly 1086 × 1448 pixels. Leave empty to keep the current one.</small>
                                            <?php else: ?>
                                                <input class="form-control" id="<?= e($key) ?>" name="<?= e($key) ?>" type="<?= $field['type'] === 'number' ? 'number' : 'text' ?>" maxlength="<?= (int) $field['max'] ?>" <?= $field['type'] === 'number' ? 'min="0"' : '' ?> value="<?= e($values[$key]) ?>" required>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
                <div class="mb-4">
                    <button class="btn btn-success btn-lg" type="submit"><i class="fas fa-save"></i> Save About Us Content</button>
                </div>
            </form>
        </div>
    </section>
    <script>
        (() => {
            const form = document.getElementById('about-content-form');
            if (!form) return;

            form.querySelectorAll('input[type="file"][data-preview]').forEach((input) => {
                input.addEventListener('change', () => {
                    const preview = form.querySelector(`[data-image-preview="${input.dataset.preview}"]`);
                    const imageMessage = form.querySelector(`[data-image-message="${input.dataset.preview}"]`);
                    const file = input.files && input.files[0];
                    input.setCustomValidity('');
                    if (imageMessage) {
                        imageMessage.textContent = 'Required image size: 1086 × 1448 pixels.';
                        imageMessage.classList.remove('text-danger');
                        imageMessage.classList.add('text-muted');
                    }
                    if (!preview) return;

                    if (!file) {
                        preview.src = preview.dataset.originalSrc;
                        return;
                    }

                    const objectUrl = URL.createObjectURL(file);
                    const uploadedPreview = new Image();
                    uploadedPreview.onload = () => {
                        if (uploadedPreview.naturalWidth !== 1086 || uploadedPreview.naturalHeight !== 1448) {
                            URL.revokeObjectURL(objectUrl);
                            input.setCustomValidity('Image must be exactly 1086 × 1448 pixels.');
                            if (imageMessage) {
                                imageMessage.textContent = `Selected image is ${uploadedPreview.naturalWidth} × ${uploadedPreview.naturalHeight}. Required: 1086 × 1448 pixels.`;
                                imageMessage.classList.remove('text-muted');
                                imageMessage.classList.add('text-danger');
                            }
                            preview.src = preview.dataset.originalSrc;
                            return;
                        }

                        preview.onload = () => URL.revokeObjectURL(objectUrl);
                        preview.src = objectUrl;
                    };
                    uploadedPreview.onerror = () => {
                        URL.revokeObjectURL(objectUrl);
                        input.setCustomValidity('Select a valid image file.');
                        if (imageMessage) {
                            imageMessage.textContent = 'This image could not be read. Select a valid image file.';
                            imageMessage.classList.remove('text-muted');
                            imageMessage.classList.add('text-danger');
                        }
                    };
                    uploadedPreview.src = objectUrl;
                });
            });
        })();
    </script>
</body>
</html>

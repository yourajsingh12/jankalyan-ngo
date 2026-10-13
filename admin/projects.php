<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

function project_admin_redirect(): never
{
    header('Location: projects.php');
    exit;
}

function remove_unused_project_asset(string $assetPath): void
{
    if ((int) val('SELECT COUNT(*) FROM projects WHERE image = ?', [$assetPath], 0) === 0) {
        delete_upload($assetPath);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $action = (string) ($_POST['action'] ?? '');
    $idInput = trim((string) ($_POST['id'] ?? ''));
    $id = $idInput === '' ? null : filter_var($idInput, FILTER_VALIDATE_INT);

    if ($action === 'delete') {
        if ($id === false || $id === null || $id < 1) {
            flash('That project could not be found.', 'error');
            project_admin_redirect();
        }

        $project = row('SELECT id, image FROM projects WHERE id = ?', [$id]);
        if ($project === null) {
            flash('That project could not be found.', 'error');
            project_admin_redirect();
        }

        q('DELETE FROM projects WHERE id = ?', [$id]);
        if (!empty($project['image'])) {
            remove_unused_project_asset((string) $project['image']);
        }

        flash('Project deleted successfully.');
        project_admin_redirect();
    }

    if ($action !== 'save' || ($id !== null && ($id === false || $id < 1))) {
        flash('The requested action is not valid.', 'error');
        project_admin_redirect();
    }

    $existing = $id === null ? null : row('SELECT id, image FROM projects WHERE id = ?', [$id]);
    if ($id !== null && $existing === null) {
        flash('That project could not be found.', 'error');
        project_admin_redirect();
    }

    $name = trim((string) ($_POST['name'] ?? ''));
    $location = trim((string) ($_POST['location'] ?? ''));
    $category = trim((string) ($_POST['category'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $fullDetails = trim((string) ($_POST['full_details'] ?? ''));
    $beneficiariesInput = trim((string) ($_POST['beneficiaries'] ?? ''));
    $progress = filter_var($_POST['progress'] ?? '0', FILTER_VALIDATE_INT);
    $sortOrder = filter_var($_POST['sort_order'] ?? '0', FILTER_VALIDATE_INT);
    $goalInput = trim((string) ($_POST['goal_amount'] ?? ''));
    $raisedInput = trim((string) ($_POST['raised_amount'] ?? ''));
    $status = (string) ($_POST['status'] ?? '');
    $isFeatured = (string) ($_POST['is_featured'] ?? '0');

    $beneficiaries = $beneficiariesInput === '' ? null : filter_var($beneficiariesInput, FILTER_VALIDATE_INT);
    $goalAmount = $goalInput === '' ? null : $goalInput;
    $raisedAmount = $raisedInput === '' ? null : $raisedInput;
    $validAmount = static fn (?string $amount): bool => $amount === null
        || (is_numeric($amount) && is_finite((float) $amount) && (float) $amount >= 0 && (float) $amount <= 9999999999.99);

    if (
        $name === ''
        || mb_strlen($name) > 255
        || mb_strlen($location) > 255
        || mb_strlen($category) > 80
        || $progress === false
        || $progress < 0
        || $progress > 100
        || $sortOrder === false
        || $sortOrder < 0
        || $sortOrder > 999999
        || ($beneficiariesInput !== '' && ($beneficiaries === false || $beneficiaries < 0))
        || !$validAmount($goalAmount)
        || !$validAmount($raisedAmount)
        || !in_array($status, ['Active', 'Completed'], true)
        || !in_array($isFeatured, ['0', '1'], true)
    ) {
        flash('Please enter valid project details, progress, amounts, order and status.', 'error');
        project_admin_redirect();
    }

    try {
        $newImage = upload_image($_FILES['image'] ?? null, 'projects');
        if ($newImage !== null) {
            validate_uploaded_image_dimensions($newImage, 600, 450, 'Project images');
        }
    } catch (RuntimeException $exception) {
        flash($exception->getMessage(), 'error');
        project_admin_redirect();
    }

    $image = $newImage ?? (string) ($existing['image'] ?? '');

    try {
        if ($existing === null) {
            q(
                'INSERT INTO projects (name, location, category, description, full_details, beneficiaries, progress, goal_amount, raised_amount, image, is_featured, sort_order, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $name,
                    $location !== '' ? $location : null,
                    $category !== '' ? $category : null,
                    $description !== '' ? $description : null,
                    $fullDetails !== '' ? $fullDetails : null,
                    $beneficiaries,
                    $progress,
                    $goalAmount,
                    $raisedAmount,
                    $image !== '' ? $image : null,
                    (int) $isFeatured,
                    $sortOrder,
                    $status,
                ]
            );
            flash('Project added successfully.');
        } else {
            q(
                'UPDATE projects
                    SET name = ?, location = ?, category = ?, description = ?, full_details = ?, beneficiaries = ?, progress = ?,
                        goal_amount = ?, raised_amount = ?, image = ?, is_featured = ?, sort_order = ?, status = ?
                  WHERE id = ?',
                [
                    $name,
                    $location !== '' ? $location : null,
                    $category !== '' ? $category : null,
                    $description !== '' ? $description : null,
                    $fullDetails !== '' ? $fullDetails : null,
                    $beneficiaries,
                    $progress,
                    $goalAmount,
                    $raisedAmount,
                    $image !== '' ? $image : null,
                    (int) $isFeatured,
                    $sortOrder,
                    $status,
                    $id,
                ]
            );
            if ($newImage !== null && !empty($existing['image'])) {
                remove_unused_project_asset((string) $existing['image']);
            }
            flash('Project updated successfully.');
        }
    } catch (PDOException $exception) {
        if ($newImage !== null) {
            delete_upload($newImage);
        }
        error_log('Project save failed: ' . $exception->getMessage());
        flash('The project could not be saved. Please try again.', 'error');
    }

    project_admin_redirect();
}

$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
$selected = null;
if ($editId !== null && $editId !== false) {
    $selected = row('SELECT * FROM projects WHERE id = ?', [$editId]);
    if ($selected === null) {
        flash('That project could not be found.', 'error');
        project_admin_redirect();
    }
}

$projects = rows(
    'SELECT id, name, location, category, beneficiaries, progress, status, is_featured, sort_order
       FROM projects
      ORDER BY sort_order ASC, id ASC'
);
$messages = take_flash();
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/header.php'; ?>

<body class="crm_body_bg">
    <?php include_once __DIR__ . '/sidebar.php'; ?>

    <section class="main_content dashboard_part large_header_bg" style="padding:24px;">
        <div class="container-fluid g-0">
            <div class="page_title_box d-flex flex-wrap justify-content-between align-items-center"
                style="background:linear-gradient(135deg,#0f172a,#7c3aed); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(124,58,237,.18);">
                <h3 style="margin:0; font-size:1.8rem; color:#fff;">Projects</h3>
                <div class="page_date_button" style="background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#f3e8ff; border-radius:12px; padding:8px 12px;">
                    <i class="far fa-folder-open"></i> Project Panel
                </div>
            </div>

            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>" role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
            <?php endforeach; ?>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:22px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                        <h4 class="mb-0" style="font-size:1.25rem; font-weight:700; color:#0f172a;"><?= $selected === null ? 'Add Project' : 'Edit Project' ?></h4>
                        <?php if ($selected !== null): ?>
                            <a href="projects.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel Edit</a>
                        <?php endif; ?>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" value="<?= e($selected['id'] ?? '') ?>">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label for="project-name" class="form-label">Project name *</label>
                                <input id="project-name" class="form-control" name="name" maxlength="255" value="<?= e($selected['name'] ?? '') ?>" required>
                            </div>
                            <div class="col-lg-3">
                                <label for="project-category" class="form-label">Category</label>
                                <input id="project-category" class="form-control" name="category" maxlength="80" value="<?= e($selected['category'] ?? '') ?>">
                            </div>
                            <div class="col-lg-3">
                                <label for="project-location" class="form-label">Location</label>
                                <input id="project-location" class="form-control" name="location" maxlength="255" value="<?= e($selected['location'] ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <label for="project-description" class="form-label">Short Description</label>
                                <textarea id="project-description" class="form-control" name="description" rows="4"><?= e($selected['description'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                                <label for="project-full-details" class="form-label">Full Details</label>
                                <textarea id="project-full-details" class="form-control" name="full_details" rows="6"><?= e($selected['full_details'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="project-beneficiaries" class="form-label">Beneficiaries</label>
                                <input id="project-beneficiaries" class="form-control" type="number" name="beneficiaries" min="0" value="<?= e($selected['beneficiaries'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="project-progress" class="form-label">Progress (%)</label>
                                <input id="project-progress" class="form-control" type="number" name="progress" min="0" max="100" value="<?= e($selected['progress'] ?? '0') ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="project-goal" class="form-label">Funding goal</label>
                                <input id="project-goal" class="form-control" type="number" name="goal_amount" min="0" step="0.01" value="<?= e($selected['goal_amount'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="project-raised" class="form-label">Amount raised</label>
                                <input id="project-raised" class="form-control" type="number" name="raised_amount" min="0" step="0.01" value="<?= e($selected['raised_amount'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="project-status" class="form-label">Status</label>
                                <select id="project-status" class="form-select" name="status" required>
                                    <option value="Active" <?= ($selected['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Completed" <?= ($selected['status'] ?? '') === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="project-order" class="form-label">Display order</label>
                                <input id="project-order" class="form-control" type="number" name="sort_order" min="0" max="999999" value="<?= e($selected['sort_order'] ?? count($projects) + 1) ?>" required>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="project-featured" <?= (int) ($selected['is_featured'] ?? 0) === 1 ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="project-featured">Feature this project</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="project-image" class="form-label">Project image <?= $selected === null ? '' : '(optional replacement)' ?></label>
                                <input id="project-image" class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif">
                                <small class="text-muted">Required size: 600 × 450 px.</small>
                            </div>
                            <?php if ($selected !== null && !empty($selected['image'])): ?>
                                <div class="col-md-6">
                                    <label class="form-label">Current image</label><br>
                                    <img src="<?= e(img_url($selected['image'])) ?>" alt="<?= e($selected['name']) ?>" style="max-width:180px; max-height:100px; border-radius:10px; object-fit:cover;">
                                </div>
                            <?php endif; ?>
                            <div class="col-12 text-end">
                                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> <?= $selected === null ? 'Add Project' : 'Save Changes' ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:20px;">
                    <h4 class="mb-4" style="font-size:1.25rem; font-weight:700; color:#0f172a;">Project Records</h4>
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:950px;">
                            <thead style="background:#f1f5f9; color:#0f172a;">
                                <tr>
                                    <th>ID</th>
                                    <th>Project</th>
                                    <th>Location</th>
                                    <th>Beneficiaries</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($projects === []): ?>
                                    <tr><td colspan="7" class="text-center text-muted py-4">No projects have been added yet.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($projects as $project): ?>
                                    <tr>
                                        <td><?= e($project['id']) ?></td>
                                        <td>
                                            <strong><?= e($project['name']) ?></strong>
                                            <?php if ((int) $project['is_featured'] === 1): ?>
                                                <span class="badge bg-warning text-dark ms-1">Featured</span>
                                            <?php endif; ?>
                                            <div class="small text-muted"><?= e($project['category'] ?? '') ?></div>
                                        </td>
                                        <td><?= e($project['location'] ?? '') ?></td>
                                        <td><?= e($project['beneficiaries'] ?? '') ?></td>
                                        <td><?= e($project['progress']) ?>%</td>
                                        <td><?= e($project['status']) ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="?edit=<?= e((string) $project['id']) ?>" class="btn btn-sm btn-primary" title="Edit" aria-label="Edit project" style="background:#0f766e; border:none; border-radius:8px; color:#fff;">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="post" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= e((string) $project['id']) ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" aria-label="Delete project" style="background:#dc2626; border:none; border-radius:8px; color:#fff;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>

<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

function team_admin_redirect(): never
{
    header('Location: team.php');
    exit;
}

function remove_unused_team_image(string $image): void
{
    if ((int) val('SELECT COUNT(*) FROM team_members WHERE image = ?', [$image], 0) === 0) {
        delete_upload($image);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $action = (string) ($_POST['action'] ?? '');
    $idInput = trim((string) ($_POST['id'] ?? ''));
    $id = $idInput === '' ? null : filter_var($idInput, FILTER_VALIDATE_INT);

    if ($action === 'delete') {
        if ($id === false || $id === null || $id < 1) {
            flash('That team member could not be found.', 'error');
            team_admin_redirect();
        }

        $member = row('SELECT id, image FROM team_members WHERE id = ?', [$id]);
        if ($member === null) {
            flash('That team member could not be found.', 'error');
            team_admin_redirect();
        }

        q('DELETE FROM team_members WHERE id = ?', [$id]);
        if (!empty($member['image'])) {
            remove_unused_team_image((string) $member['image']);
        }

        flash('Team member deleted successfully.');
        team_admin_redirect();
    }

    if ($action !== 'save' || ($id !== null && ($id === false || $id < 1))) {
        flash('The requested action is not valid.', 'error');
        team_admin_redirect();
    }

    $existing = $id === null ? null : row('SELECT id, image FROM team_members WHERE id = ?', [$id]);
    if ($id !== null && $existing === null) {
        flash('That team member could not be found.', 'error');
        team_admin_redirect();
    }

    $name = trim((string) ($_POST['name'] ?? ''));
    $position = trim((string) ($_POST['position'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $bio = trim((string) ($_POST['bio'] ?? ''));
    $sortOrder = filter_var($_POST['sort_order'] ?? '0', FILTER_VALIDATE_INT);
    $status = (string) ($_POST['status'] ?? '');
    $socials = [];

    foreach (['facebook', 'twitter', 'linkedin', 'instagram'] as $network) {
        $socials[$network] = trim((string) ($_POST[$network] ?? ''));
    }

    $validSocials = true;
    foreach ($socials as $url) {
        if ($url !== '' && (mb_strlen($url) > 255 || filter_var($url, FILTER_VALIDATE_URL) === false || !preg_match('#^https?://#i', $url))) {
            $validSocials = false;
            break;
        }
    }

    if (
        $name === ''
        || mb_strlen($name) > 255
        || $position === ''
        || mb_strlen($position) > 160
        || ($email !== '' && (mb_strlen($email) > 190 || filter_var($email, FILTER_VALIDATE_EMAIL) === false))
        || mb_strlen($phone) > 32
        || $sortOrder === false
        || $sortOrder < 0
        || $sortOrder > 999999
        || !in_array($status, ['Active', 'Inactive'], true)
        || !$validSocials
    ) {
        flash('Please enter a valid name, role, email, phone, social link, display order and status.', 'error');
        team_admin_redirect();
    }

    try {
        $newImage = upload_image($_FILES['image'] ?? null, 'team');
    } catch (RuntimeException $exception) {
        flash($exception->getMessage(), 'error');
        team_admin_redirect();
    }

    $image = $newImage ?? (string) ($existing['image'] ?? '');

    try {
        if ($existing === null) {
            q(
                'INSERT INTO team_members (name, position, email, phone, image, bio, facebook, twitter, linkedin, instagram, sort_order, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $name,
                    $position,
                    $email !== '' ? $email : null,
                    $phone !== '' ? $phone : null,
                    $image !== '' ? $image : null,
                    $bio !== '' ? $bio : null,
                    $socials['facebook'] !== '' ? $socials['facebook'] : null,
                    $socials['twitter'] !== '' ? $socials['twitter'] : null,
                    $socials['linkedin'] !== '' ? $socials['linkedin'] : null,
                    $socials['instagram'] !== '' ? $socials['instagram'] : null,
                    $sortOrder,
                    $status,
                ]
            );
            flash('Team member added successfully.');
        } else {
            q(
                'UPDATE team_members
                    SET name = ?, position = ?, email = ?, phone = ?, image = ?, bio = ?, facebook = ?, twitter = ?,
                        linkedin = ?, instagram = ?, sort_order = ?, status = ?
                  WHERE id = ?',
                [
                    $name,
                    $position,
                    $email !== '' ? $email : null,
                    $phone !== '' ? $phone : null,
                    $image !== '' ? $image : null,
                    $bio !== '' ? $bio : null,
                    $socials['facebook'] !== '' ? $socials['facebook'] : null,
                    $socials['twitter'] !== '' ? $socials['twitter'] : null,
                    $socials['linkedin'] !== '' ? $socials['linkedin'] : null,
                    $socials['instagram'] !== '' ? $socials['instagram'] : null,
                    $sortOrder,
                    $status,
                    $id,
                ]
            );
            if ($newImage !== null && !empty($existing['image'])) {
                remove_unused_team_image((string) $existing['image']);
            }
            flash('Team member updated successfully.');
        }
    } catch (PDOException $exception) {
        if ($newImage !== null) {
            delete_upload($newImage);
        }
        error_log('Team member save failed: ' . $exception->getMessage());
        flash('The team member could not be saved. Please try again.', 'error');
    }

    team_admin_redirect();
}

$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
$selected = null;
if ($editId !== null && $editId !== false) {
    $selected = row('SELECT * FROM team_members WHERE id = ?', [$editId]);
    if ($selected === null) {
        flash('That team member could not be found.', 'error');
        team_admin_redirect();
    }
}

$teamMembers = rows(
    'SELECT id, name, position, email, phone, image, sort_order, status
       FROM team_members
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
                style="background:linear-gradient(135deg,#0f172a,#ec4899); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(236,72,153,.18);">
                <h3 style="margin:0; font-size:1.8rem; color:#fff;">Team Members</h3>
                <div class="page_date_button" style="background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#fce7f3; border-radius:12px; padding:8px 12px;">
                    <i class="far fa-users"></i> Team Panel
                </div>
            </div>

            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>" role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
            <?php endforeach; ?>

            <div class="white_card mb_30" id="team-form" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:22px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                        <h4 class="mb-0" style="font-size:1.25rem; font-weight:700; color:#0f172a;"><?= $selected === null ? 'Add Team Member' : 'Edit Team Member' ?></h4>
                        <?php if ($selected !== null): ?>
                            <a href="team.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel Edit</a>
                        <?php endif; ?>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" value="<?= e($selected['id'] ?? '') ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="team-name" class="form-label">Full name *</label>
                                <input id="team-name" class="form-control" name="name" maxlength="255" value="<?= e($selected['name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="team-position" class="form-label">Role / designation *</label>
                                <input id="team-position" class="form-control" name="position" maxlength="160" value="<?= e($selected['position'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="team-email" class="form-label">Email</label>
                                <input id="team-email" class="form-control" type="email" name="email" maxlength="190" value="<?= e($selected['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="team-phone" class="form-label">Phone</label>
                                <input id="team-phone" class="form-control" type="tel" name="phone" maxlength="32" value="<?= e($selected['phone'] ?? '') ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="team-status" class="form-label">Status</label>
                                <select id="team-status" class="form-select" name="status">
                                    <option value="Active" <?= ($selected['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Inactive" <?= ($selected['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="team-order" class="form-label">Display order</label>
                                <input id="team-order" class="form-control" type="number" name="sort_order" min="0" max="999999" value="<?= e($selected['sort_order'] ?? count($teamMembers) + 1) ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="team-bio" class="form-label">Bio</label>
                                <textarea id="team-bio" class="form-control" name="bio" rows="3"><?= e($selected['bio'] ?? '') ?></textarea>
                            </div>
                            <?php foreach (['facebook' => 'Facebook', 'twitter' => 'X / Twitter', 'linkedin' => 'LinkedIn', 'instagram' => 'Instagram'] as $network => $label): ?>
                                <div class="col-md-6">
                                    <label for="team-<?= e($network) ?>" class="form-label"><?= e($label) ?> URL</label>
                                    <input id="team-<?= e($network) ?>" class="form-control" type="url" name="<?= e($network) ?>" maxlength="255" value="<?= e($selected[$network] ?? '') ?>">
                                </div>
                            <?php endforeach; ?>
                            <div class="col-md-6">
                                <label for="team-image" class="form-label">Profile image <?= $selected === null ? '' : '(optional replacement)' ?></label>
                                <input id="team-image" class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif">
                            </div>
                            <?php if ($selected !== null && !empty($selected['image'])): ?>
                                <div class="col-md-6">
                                    <label class="form-label">Current image</label><br>
                                    <img src="<?= e(img_url($selected['image'])) ?>" alt="<?= e($selected['name']) ?>" style="max-width:180px; max-height:120px; border-radius:10px; object-fit:cover;">
                                </div>
                            <?php endif; ?>
                            <div class="col-12 text-end">
                                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> <?= $selected === null ? 'Add Member' : 'Save Changes' ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:20px;">
                    <h4 class="mb-4" style="font-size:1.25rem; font-weight:700; color:#0f172a;">Team Records</h4>
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:900px;">
                            <thead style="background:#f1f5f9; color:#0f172a;">
                                <tr><th>ID</th><th>Name</th><th>Role</th><th>Email</th><th>Phone</th><th>Status</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php if ($teamMembers === []): ?>
                                    <tr><td colspan="7" class="text-center text-muted py-4">No team members have been added yet.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($teamMembers as $member): ?>
                                    <tr>
                                        <td><?= e($member['id']) ?></td>
                                        <td><strong><?= e($member['name']) ?></strong></td>
                                        <td><?= e($member['position']) ?></td>
                                        <td><?= e($member['email'] ?? '') ?></td>
                                        <td><?= e($member['phone'] ?? '') ?></td>
                                        <td><span class="badge <?= $member['status'] === 'Active' ? 'bg-success' : 'bg-secondary' ?>"><?= e($member['status']) ?></span></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="?edit=<?= e((string) $member['id']) ?>#team-form" class="btn btn-sm btn-primary" title="Edit" aria-label="Edit team member" style="background:#0f766e; border:none; border-radius:8px; color:#fff;">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="post" onsubmit="return confirm('Are you sure you want to delete this team member?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= e((string) $member['id']) ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" aria-label="Delete team member" style="background:#dc2626; border:none; border-radius:8px; color:#fff;">
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

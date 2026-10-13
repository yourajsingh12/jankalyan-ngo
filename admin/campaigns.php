<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

function campaign_admin_redirect(): never
{
    header('Location: campaigns.php');
    exit;
}

function remove_unused_campaign_image(string $imagePath): void
{
    if ((int) val('SELECT COUNT(*) FROM campaigns WHERE image = ?', [$imagePath], 0) === 0) {
        delete_upload($imagePath);
    }
}

function valid_campaign_date(string $date): bool
{
    $parts = explode('-', $date);

    return count($parts) === 3
        && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1
        && checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $action = (string) ($_POST['action'] ?? '');
    $idInput = trim((string) ($_POST['id'] ?? ''));
    $id = $idInput === '' ? null : filter_var($idInput, FILTER_VALIDATE_INT);

    if ($action === 'delete') {
        if ($id === false || $id === null || $id < 1) {
            flash('That campaign could not be found.', 'error');
            campaign_admin_redirect();
        }

        $campaign = row('SELECT id, image FROM campaigns WHERE id = ?', [$id]);
        if ($campaign === null) {
            flash('That campaign could not be found.', 'error');
            campaign_admin_redirect();
        }

        q('DELETE FROM campaigns WHERE id = ?', [$id]);
        if (!empty($campaign['image'])) {
            remove_unused_campaign_image((string) $campaign['image']);
        }
        flash('Campaign deleted successfully.');
        campaign_admin_redirect();
    }

    if ($action !== 'save' || ($id !== null && ($id === false || $id < 1))) {
        flash('The requested action is not valid.', 'error');
        campaign_admin_redirect();
    }

    $existing = $id === null ? null : row('SELECT id, image FROM campaigns WHERE id = ?', [$id]);
    if ($id !== null && $existing === null) {
        flash('That campaign could not be found.', 'error');
        campaign_admin_redirect();
    }

    $title = trim((string) ($_POST['title'] ?? ''));
    $category = trim((string) ($_POST['category'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $fullDetails = trim((string) ($_POST['full_details'] ?? ''));
    $startDate = trim((string) ($_POST['start_date'] ?? ''));
    $endDate = trim((string) ($_POST['end_date'] ?? ''));
    $goalInput = trim((string) ($_POST['goal_amount'] ?? ''));
    $raisedInput = trim((string) ($_POST['raised_amount'] ?? '0'));
    $supporters = filter_var($_POST['supporters'] ?? '0', FILTER_VALIDATE_INT);
    $sortOrder = filter_var($_POST['sort_order'] ?? '0', FILTER_VALIDATE_INT);
    $status = (string) ($_POST['status'] ?? '');
    $isFeatured = (string) ($_POST['is_featured'] ?? '0');
    $validAmount = static fn (string $amount): bool => is_numeric($amount)
        && is_finite((float) $amount)
        && (float) $amount >= 0
        && (float) $amount <= 9999999999.99;

    if (
        $title === ''
        || mb_strlen($title) > 255
        || $category === ''
        || mb_strlen($category) > 80
        || $description === ''
        || mb_strlen($description) > 5000
        || $fullDetails === ''
        || mb_strlen($fullDetails) > 30000
        || !valid_campaign_date($startDate)
        || !valid_campaign_date($endDate)
        || $endDate < $startDate
        || !$validAmount($goalInput)
        || (float) $goalInput <= 0
        || !$validAmount($raisedInput)
        || $supporters === false
        || $supporters < 0
        || $sortOrder === false
        || $sortOrder < 0
        || $sortOrder > 999999
        || !in_array($status, ['Active', 'Completed', 'Upcoming'], true)
        || !in_array($isFeatured, ['0', '1'], true)
    ) {
        flash('Please enter valid campaign details, dates, fundraising amounts, order and status.', 'error');
        campaign_admin_redirect();
    }

    $newImage = null;
    try {
        $newImage = upload_image($_FILES['image'] ?? null, 'campaigns');
        if ($newImage !== null) {
            validate_uploaded_image_dimensions($newImage, 600, 450, 'Campaign images');
        }
        if ($existing === null && $newImage === null) {
            throw new RuntimeException('Please choose an image for the campaign.');
        }
    } catch (RuntimeException $exception) {
        if ($newImage !== null) {
            delete_upload($newImage);
        }
        flash($exception->getMessage(), 'error');
        campaign_admin_redirect();
    }

    $image = $newImage ?? (string) ($existing['image'] ?? '');

    try {
        if ($existing === null) {
            q(
                'INSERT INTO campaigns (title, category, description, full_details, image, start_date, end_date, goal_amount, raised_amount, supporters, status, is_featured, sort_order)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [$title, $category, $description, $fullDetails, $image, $startDate, $endDate, $goalInput, $raisedInput, $supporters, $status, (int) $isFeatured, $sortOrder]
            );
            flash('Campaign added successfully.');
        } else {
            q(
                'UPDATE campaigns
                    SET title = ?, category = ?, description = ?, full_details = ?, image = ?, start_date = ?, end_date = ?,
                        goal_amount = ?, raised_amount = ?, supporters = ?, status = ?, is_featured = ?, sort_order = ?
                  WHERE id = ?',
                [$title, $category, $description, $fullDetails, $image, $startDate, $endDate, $goalInput, $raisedInput, $supporters, $status, (int) $isFeatured, $sortOrder, $id]
            );
            if ($newImage !== null && !empty($existing['image'])) {
                remove_unused_campaign_image((string) $existing['image']);
            }
            flash('Campaign updated successfully.');
        }
    } catch (PDOException $exception) {
        if ($newImage !== null) {
            delete_upload($newImage);
        }
        error_log('Campaign save failed: ' . $exception->getMessage());
        flash('The campaign could not be saved. Please try again.', 'error');
    }

    campaign_admin_redirect();
}

$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
$selected = null;
if ($editId !== null && $editId !== false) {
    $selected = row('SELECT * FROM campaigns WHERE id = ?', [$editId]);
    if ($selected === null) {
        flash('That campaign could not be found.', 'error');
        campaign_admin_redirect();
    }
}

$campaigns = rows('SELECT id, title, category, image, goal_amount, raised_amount, status, is_featured, sort_order FROM campaigns ORDER BY is_featured DESC, sort_order ASC, id DESC');
$messages = take_flash();
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/header.php'; ?>
<body class="crm_body_bg">
    <?php include_once __DIR__ . '/sidebar.php'; ?>
    <section class="main_content dashboard_part large_header_bg" style="padding:24px;">
        <div class="container-fluid g-0">
            <div class="page_title_box d-flex flex-wrap justify-content-between align-items-center" style="background:linear-gradient(135deg,#0f172a,#0f766e); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px;">
                <h3 style="margin:0; font-size:1.8rem; color:#fff;">Campaigns</h3>
                <a href="../campaigns.php" target="_blank" rel="noopener" class="btn btn-light"><i class="fas fa-external-link-alt"></i> View Campaigns</a>
            </div>

            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>" role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
            <?php endforeach; ?>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px;">
                <div class="white_card_body" style="padding:22px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <h4 class="mb-0"><?= $selected === null ? 'Add Campaign' : 'Edit Campaign' ?></h4>
                        <?php if ($selected !== null): ?><a href="campaigns.php" class="btn btn-secondary">Cancel Edit</a><?php endif; ?>
                    </div>
                    <form method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" value="<?= e($selected['id'] ?? '') ?>">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label for="campaign-title" class="form-label">Campaign name *</label>
                                <input id="campaign-title" class="form-control" name="title" maxlength="255" value="<?= e($selected['title'] ?? '') ?>" required>
                            </div>
                            <div class="col-lg-6">
                                <label for="campaign-category" class="form-label">Category *</label>
                                <input id="campaign-category" class="form-control" name="category" maxlength="80" value="<?= e($selected['category'] ?? '') ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="campaign-description" class="form-label">Short description *</label>
                                <textarea id="campaign-description" class="form-control" name="description" rows="3" maxlength="5000" required><?= e($selected['description'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                                <label for="campaign-full-details" class="form-label">Full details *</label>
                                <textarea id="campaign-full-details" class="form-control" name="full_details" rows="6" maxlength="30000" required><?= e($selected['full_details'] ?? '') ?></textarea>
                            </div>
                            <div class="col-lg-6">
                                <label for="campaign-image" class="form-label">Campaign image <?= $selected === null ? '*' : '(optional replacement)' ?></label>
                                <input id="campaign-image" class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif" <?= $selected === null ? 'required' : '' ?>>
                                <small class="text-muted">JPG, PNG, WEBP or GIF; maximum 5 MB; exactly 600 × 450 px.</small>
                                <?php if ($selected !== null && !empty($selected['image'])): ?>
                                    <div class="mt-2"><img src="<?= e(img_url((string) $selected['image'])) ?>" alt="<?= e($selected['title']) ?>" style="max-width:220px; max-height:130px; border-radius:10px; object-fit:cover;"></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <label for="campaign-start-date" class="form-label">Start date *</label>
                                <input id="campaign-start-date" class="form-control" type="date" name="start_date" value="<?= e($selected['start_date'] ?? '') ?>" required>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <label for="campaign-end-date" class="form-label">End date *</label>
                                <input id="campaign-end-date" class="form-control" type="date" name="end_date" value="<?= e($selected['end_date'] ?? '') ?>" required>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <label for="campaign-goal" class="form-label">Fundraising goal (₹) *</label>
                                <input id="campaign-goal" class="form-control" type="number" name="goal_amount" min="1" step="0.01" value="<?= e($selected['goal_amount'] ?? '') ?>" required>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <label for="campaign-raised" class="form-label">Amount raised (₹)</label>
                                <input id="campaign-raised" class="form-control" type="number" name="raised_amount" min="0" step="0.01" value="<?= e($selected['raised_amount'] ?? '0') ?>" required>
                            </div>
                            <div class="col-sm-6 col-lg-2">
                                <label for="campaign-supporters" class="form-label">Supporters</label>
                                <input id="campaign-supporters" class="form-control" type="number" name="supporters" min="0" value="<?= e($selected['supporters'] ?? '0') ?>" required>
                            </div>
                            <div class="col-sm-6 col-lg-2">
                                <label for="campaign-order" class="form-label">Display order</label>
                                <input id="campaign-order" class="form-control" type="number" name="sort_order" min="0" max="999999" value="<?= e($selected['sort_order'] ?? count($campaigns) + 1) ?>" required>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <label for="campaign-status" class="form-label">Status</label>
                                <select id="campaign-status" class="form-select" name="status" required>
                                    <?php foreach (['Active', 'Upcoming', 'Completed'] as $status): ?>
                                        <option value="<?= e($status) ?>" <?= ($selected['status'] ?? 'Active') === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-6 col-lg-2 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="campaign-featured" <?= (int) ($selected['is_featured'] ?? 0) === 1 ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="campaign-featured">Featured</label>
                                </div>
                            </div>
                            <div class="col-12 text-end">
                                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> <?= $selected === null ? 'Add Campaign' : 'Save Changes' ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px;">
                <div class="white_card_body" style="padding:22px;">
                    <h4 class="mb-4">Campaign Records</h4>
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:850px;">
                            <thead><tr><th>Image</th><th>Campaign</th><th>Category</th><th>Fundraising</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php if ($campaigns === []): ?><tr><td colspan="6" class="text-center text-muted">No campaigns have been added yet.</td></tr><?php endif; ?>
                                <?php foreach ($campaigns as $campaign): ?>
                                    <tr>
                                        <td><img src="<?= e(img_url((string) $campaign['image'])) ?>" alt="<?= e($campaign['title']) ?>" style="width:90px; height:60px; object-fit:cover; border-radius:8px;"></td>
                                        <td><strong><?= e($campaign['title']) ?></strong><?= (int) $campaign['is_featured'] === 1 ? ' <span class="badge bg-warning text-dark">Featured</span>' : '' ?></td>
                                        <td><?= e($campaign['category']) ?></td>
                                        <td>₹<?= e(number_format((float) $campaign['raised_amount'], 0)) ?> / ₹<?= e(number_format((float) $campaign['goal_amount'], 0)) ?></td>
                                        <td><?= e($campaign['status']) ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a class="btn btn-sm btn-primary" href="campaigns.php?edit=<?= (int) $campaign['id'] ?>" aria-label="Edit campaign"><i class="fas fa-edit"></i></a>
                                                <form method="post" onsubmit="return confirm('Delete this campaign?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= (int) $campaign['id'] ?>">
                                                    <button class="btn btn-sm btn-danger" type="submit" aria-label="Delete campaign"><i class="fas fa-trash"></i></button>
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

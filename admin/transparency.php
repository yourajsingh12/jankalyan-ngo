<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

function transparency_admin_redirect(): never
{
    header('Location: transparency.php');
    exit;
}

function ensure_transparency_table(): void
{
    db()->exec(
        'CREATE TABLE IF NOT EXISTS transparency_documents (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            title       VARCHAR(255) NOT NULL,
            category    VARCHAR(80) NOT NULL DEFAULT \'Other\',
            description TEXT NULL,
            report_year VARCHAR(20) NULL,
            file_path   VARCHAR(500) NOT NULL,
            sort_order  INT NOT NULL DEFAULT 0,
            status      TINYINT(1) NOT NULL DEFAULT 1,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_transparency_order (status, sort_order, id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

function upload_transparency_pdf(?array $file): ?string
{
    if ($file === null || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException(upload_error_message((int) $file['error']));
    }

    $maxBytes = 30 * 1024 * 1024;
    if (!is_uploaded_file((string) $file['tmp_name'])) {
        throw new RuntimeException('The PDF upload could not be verified. Please try again.');
    }
    if ((int) $file['size'] > $maxBytes) {
        throw new RuntimeException('PDF files must be 30 MB or smaller.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string) $finfo->file((string) $file['tmp_name']);
    $handle = fopen((string) $file['tmp_name'], 'rb');
    $signature = $handle === false ? false : fread($handle, 5);
    if (is_resource($handle)) {
        fclose($handle);
    }

    if ($mime !== 'application/pdf' || $signature !== '%PDF-') {
        throw new RuntimeException('Please upload a valid PDF file.');
    }

    $directory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'pdf';
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException('The PDF upload folder could not be created.');
    }

    $filename = 'transparency-' . date('Ymd-His') . '-' . bin2hex(random_bytes(6)) . '.pdf';
    if (!move_uploaded_file((string) $file['tmp_name'], $directory . DIRECTORY_SEPARATOR . $filename)) {
        throw new RuntimeException('The PDF could not be saved. Please try again.');
    }

    return 'assets/img/pdf/' . $filename;
}

function remove_transparency_upload(string $filePath): void
{
    if (preg_match('~^assets/img/pdf/transparency-[a-zA-Z0-9-]+\.pdf$~D', $filePath) !== 1) {
        return;
    }

    $pdfDirectory = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'pdf');
    $realPath = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $filePath));
    if ($pdfDirectory !== false && $realPath !== false && is_file($realPath) && dirname($realPath) === $pdfDirectory) {
        if (!unlink($realPath)) {
            error_log('Transparency PDF could not be removed: ' . $realPath);
        }
    }
}

ensure_transparency_table();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $action = (string) ($_POST['action'] ?? '');
    $idInput = trim((string) ($_POST['id'] ?? ''));
    $id = $idInput === '' ? null : filter_var($idInput, FILTER_VALIDATE_INT);

    if ($action === 'delete') {
        if ($id === false || $id === null || $id < 1) {
            flash('That transparency report could not be found.', 'error');
            transparency_admin_redirect();
        }

        $document = row('SELECT id, file_path FROM transparency_documents WHERE id = ?', [$id]);
        if ($document === null) {
            flash('That transparency report could not be found.', 'error');
            transparency_admin_redirect();
        }

        q('DELETE FROM transparency_documents WHERE id = ?', [$id]);
        remove_transparency_upload((string) $document['file_path']);
        flash('Transparency report deleted successfully.');
        transparency_admin_redirect();
    }

    if ($action !== 'save' || ($id !== null && ($id === false || $id < 1))) {
        flash('The requested action is not valid.', 'error');
        transparency_admin_redirect();
    }

    $existing = $id === null
        ? null
        : row('SELECT id, file_path FROM transparency_documents WHERE id = ?', [$id]);
    if ($id !== null && $existing === null) {
        flash('That transparency report could not be found.', 'error');
        transparency_admin_redirect();
    }

    $title = trim((string) ($_POST['title'] ?? ''));
    $category = trim((string) ($_POST['category'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $reportYear = trim((string) ($_POST['report_year'] ?? ''));
    $sortOrder = filter_var($_POST['sort_order'] ?? '0', FILTER_VALIDATE_INT);
    $status = (string) ($_POST['status'] ?? '');

    if (
        $title === ''
        || mb_strlen($title) > 255
        || $category === ''
        || mb_strlen($category) > 80
        || mb_strlen($description) > 10000
        || mb_strlen($reportYear) > 20
        || $sortOrder === false
        || $sortOrder < 0
        || $sortOrder > 999999
        || !in_array($status, ['0', '1'], true)
    ) {
        flash('Please check the report title, category, year, display order, and status.', 'error');
        transparency_admin_redirect();
    }

    try {
        $newFile = upload_transparency_pdf($_FILES['pdf'] ?? null);
    } catch (RuntimeException $exception) {
        flash($exception->getMessage(), 'error');
        transparency_admin_redirect();
    }

    if ($existing === null && $newFile === null) {
        flash('Please choose a PDF report to upload.', 'error');
        transparency_admin_redirect();
    }

    $filePath = $newFile ?? (string) $existing['file_path'];
    try {
        if ($existing === null) {
            q(
                'INSERT INTO transparency_documents (title, category, description, report_year, file_path, sort_order, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?)',
                [$title, $category, $description, $reportYear !== '' ? $reportYear : null, $filePath, $sortOrder, (int) $status]
            );
            flash('Transparency report added successfully.');
        } else {
            q(
                'UPDATE transparency_documents
                    SET title = ?, category = ?, description = ?, report_year = ?, file_path = ?, sort_order = ?, status = ?
                  WHERE id = ?',
                [$title, $category, $description, $reportYear !== '' ? $reportYear : null, $filePath, $sortOrder, (int) $status, $id]
            );
            if ($newFile !== null) {
                remove_transparency_upload((string) $existing['file_path']);
            }
            flash('Transparency report updated successfully.');
        }
    } catch (PDOException $exception) {
        if ($newFile !== null) {
            remove_transparency_upload($newFile);
        }
        error_log('Transparency report save failed: ' . $exception->getMessage());
        flash('The transparency report could not be saved. Please try again.', 'error');
    }

    transparency_admin_redirect();
}

$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
$selected = null;
if ($editId !== null && $editId !== false) {
    $selected = row('SELECT * FROM transparency_documents WHERE id = ?', [$editId]);
    if ($selected === null) {
        flash('That transparency report could not be found.', 'error');
        transparency_admin_redirect();
    }
}

$documents = rows(
    'SELECT id, title, category, report_year, file_path, sort_order, status
       FROM transparency_documents
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
                style="background:linear-gradient(135deg,#0f172a,#0f766e); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(15,118,110,.18);">
                <h3 style="margin:0; font-size:1.8rem; color:#fff;">Transparency Reports</h3>
                <div class="page_date_button" style="background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.18); color:#ecfeff; border-radius:12px; padding:8px 12px;">
                    <i class="fas fa-file-pdf"></i> Annual &amp; Financial Reports
                </div>
            </div>

            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : 'success' ?>" role="<?= $message['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($message['message']) ?></div>
            <?php endforeach; ?>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:22px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                        <div>
                            <h4 class="mb-1" style="font-size:1.25rem; font-weight:700; color:#0f172a;"><?= $selected === null ? 'Add Transparency Report' : 'Edit Transparency Report' ?></h4>
                            <p class="text-muted mb-0">Upload a PDF up to 30 MB. Your server must allow uploads of this size.</p>
                        </div>
                        <?php if ($selected !== null): ?>
                            <a href="transparency.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel Edit</a>
                        <?php endif; ?>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" value="<?= e($selected['id'] ?? '') ?>">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label for="transparency-title" class="form-label">Report title *</label>
                                <input id="transparency-title" class="form-control" name="title" maxlength="255" value="<?= e($selected['title'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="transparency-category" class="form-label">Category *</label>
                                <select id="transparency-category" class="form-select" name="category" required>
                                    <?php foreach (['Annual Report', 'Financial Report', 'Audit Report', 'Other'] as $category): ?>
                                        <option value="<?= e($category) ?>" <?= ($selected['category'] ?? 'Annual Report') === $category ? 'selected' : '' ?>><?= e($category) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="transparency-year" class="form-label">Financial/report year</label>
                                <input id="transparency-year" class="form-control" name="report_year" maxlength="20" placeholder="e.g. 2024-25" value="<?= e($selected['report_year'] ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <label for="transparency-description" class="form-label">Description</label>
                                <textarea id="transparency-description" class="form-control" name="description" rows="3" maxlength="10000"><?= e($selected['description'] ?? '') ?></textarea>
                            </div>
                            <div class="col-lg-6">
                                <label for="transparency-pdf" class="form-label">PDF file <?= $selected === null ? '*' : '(choose only to replace the current PDF)' ?></label>
                                <input id="transparency-pdf" class="form-control" type="file" name="pdf" accept=".pdf,application/pdf" <?= $selected === null ? 'required' : '' ?>>
                                <small class="text-muted">PDF only, maximum 30 MB.</small>
                            </div>
                            <?php if ($selected !== null): ?>
                                <div class="col-lg-6">
                                    <label class="form-label">Current PDF</label><br>
                                    <a href="../<?= e($selected['file_path']) ?>" target="_blank" rel="noopener"><?= e(basename($selected['file_path'])) ?> <i class="fas fa-external-link-alt"></i></a>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-6 col-lg-3">
                                <label for="transparency-order" class="form-label">Display order</label>
                                <input id="transparency-order" class="form-control" type="number" name="sort_order" min="0" max="999999" value="<?= e($selected['sort_order'] ?? count($documents) + 1) ?>" required>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="transparency-status" class="form-label">Status</label>
                                <select id="transparency-status" class="form-select" name="status">
                                    <option value="1" <?= (int) ($selected['status'] ?? 1) === 1 ? 'selected' : '' ?>>Visible on website</option>
                                    <option value="0" <?= (int) ($selected['status'] ?? 1) === 0 ? 'selected' : '' ?>>Hidden</option>
                                </select>
                            </div>
                            <div class="col-12 text-end">
                                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> <?= $selected === null ? 'Add Report' : 'Save Changes' ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="white_card mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                <div class="white_card_body" style="padding:20px;">
                    <h4 class="mb-4" style="font-size:1.25rem; font-weight:700; color:#0f172a;">Published Report Entries</h4>
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width:850px;">
                            <thead style="background:#f1f5f9;">
                                <tr><th>Title</th><th>Category</th><th>Year</th><th>PDF</th><th>Order</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php if ($documents === []): ?>
                                    <tr><td colspan="7" class="text-center text-muted py-4">No reports yet. Add one using the form above.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($documents as $document): ?>
                                    <tr>
                                        <td><?= e($document['title']) ?></td>
                                        <td><?= e($document['category']) ?></td>
                                        <td><?= e($document['report_year'] ?? '') ?></td>
                                        <td><a href="../<?= e($document['file_path']) ?>" target="_blank" rel="noopener">View PDF</a></td>
                                        <td><?= e($document['sort_order']) ?></td>
                                        <td><?= (int) $document['status'] === 1 ? 'Visible' : 'Hidden' ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a class="btn btn-sm btn-primary" href="transparency.php?edit=<?= e($document['id']) ?>" aria-label="Edit report"><i class="fas fa-edit"></i></a>
                                                <form method="post" onsubmit="return confirm('Delete this transparency report?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= e($document['id']) ?>">
                                                    <button class="btn btn-sm btn-danger" type="submit" aria-label="Delete report"><i class="fas fa-trash"></i></button>
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

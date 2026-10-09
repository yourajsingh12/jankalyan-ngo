<?php
include('db_conn.php');

// Get page id from URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = mysqli_query($conn, "SELECT * FROM content WHERE id = $id");
    $row = mysqli_fetch_assoc($query);
}

$message = ""; // For showing alert

// Update logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $page_title = mysqli_real_escape_string($conn, $_POST['page_title']);
    $page_content = mysqli_real_escape_string($conn, $_POST['page_content']);

    $update = "UPDATE content SET page_title='$page_title', page_content='$page_content' WHERE id=$id";
    if (mysqli_query($conn, $update)) {
        $message = "<div class='alert alert-success mt-3'>✅ Content updated successfully!</div>";
        
        // Reload latest data after update
        $query = mysqli_query($conn, "SELECT * FROM content WHERE id = $id");
        $row = mysqli_fetch_assoc($query);
    } else {
        $message = "<div class='alert alert-danger mt-3'>❌ Error updating content!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once('header.php'); ?>
<body class="crm_body_bg">
    <?php include_once('sidebar.php'); ?>

    <section class="main_content dashboard_part large_header_bg" style="padding: 24px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page_title_box d-flex flex-wrap justify-content-between align-items-center" style="background: linear-gradient(135deg,#0f172a,#0ea5e9); color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:24px; box-shadow:0 18px 30px rgba(14,165,233,.18);">
                        <div>
                            <h3 style="margin:0; font-size:1.8rem; color:#fff;">Content Management</h3>
                        </div>
                        <div class="page_date_button" style="background: rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.15); color:#e0f2fe; border-radius:12px; padding:8px 12px;">
                            <i class="far fa-table"></i> Content Panel
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($selected): ?>
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="white_card card_height_100 mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                            <div class="white_card_body" style="padding:22px;">
                                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                                    <div>
                                        <h4 class="mb-0" style="font-size:1.3rem; font-weight:700; color:#0f172a;">Edit: <?= htmlspecialchars($selected['name']) ?></h4>
                                    </div>
                                    <a href="edit_content.php" class="btn btn-secondary" style="border-radius:10px; background:#475569; border:none; padding:10px 14px;">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </a>
                                </div>

                                <form>
                                    <div class="mb-3">
                                        <label class="form-label" style="font-weight:600; color:#334155;">Page Name</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($selected['name']) ?>" readonly style="border-radius:10px; border:1px solid #dbeafe; background:#f8fafc;">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" style="font-weight:600; color:#334155;">Page Title</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($selected['title']) ?>" style="border-radius:10px; border:1px solid #dbeafe;">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" style="font-weight:600; color:#334155;">Content</label>
                                        <textarea class="form-control" rows="7" style="border-radius:10px; border:1px solid #dbeafe; resize:vertical;"><?= htmlspecialchars($selected['content']) ?></textarea>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-success" style="background:#16a34a; border:none; border-radius:10px;">
                                            <i class="fas fa-save"></i> Save
                                        </button>
                                        <button type="button" class="btn btn-danger" style="background:#ef4444; border:none; border-radius:10px;">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-12">
                        <div class="white_card card_height_100 mb_30" style="background:#fff; border-radius:20px; box-shadow:0 14px 28px rgba(15,23,42,.06); border:1px solid rgba(148,163,184,.2);">
                            <div class="white_card_body" style="padding:20px;">
                                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                                    <div>
                                        <h4 class="mb-0" style="font-size:1.3rem; font-weight:700; color:#0f172a;">Content Table</h4>
                                    </div>
                                    <a href="editor.php" class="btn btn-primary" style="background:linear-gradient(135deg,#0f766e,#10b981); border:none; border-radius:10px; padding:10px 16px; font-weight:600;">
                                        <i class="fas fa-arrow-left"></i> Back to Pages
                                    </a>
                                </div>

                                <div class="table-responsive">
                                    <table class="table align-middle" style="border-collapse:separate; border-spacing:0; min-width:900px;">
                                        <thead style="background: linear-gradient(135deg,#0f172a,#1e293b); color:#fff;">
                                            <tr>
                                                <th style="padding:14px 12px;">ID</th>
                                                <th style="padding:14px 12px;">Page Name</th>
                                                <th style="padding:14px 12px;">Title</th>
                                                <th style="padding:14px 12px;">Content</th>
                                                <th style="padding:14px 12px;">Updated</th>
                                                <th style="padding:14px 12px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pages as $page): ?>
                                                <tr style="background:#fff; border-bottom:1px solid #e2e8f0;">
                                                    <td style="padding:14px 12px; font-weight:600; color:#334155;"><?= $page['id'] ?></td>
                                                    <td style="padding:14px 12px; font-weight:600; color:#0f172a;">
                                                        <?= htmlspecialchars($page['name']) ?>
                                                    </td>
                                                    <td style="padding:14px 12px; color:#475569;">
                                                        <?= htmlspecialchars($page['title']) ?>
                                                    </td>
                                                    <td style="padding:14px 12px; color:#475569; max-width:320px;">
                                                        <?= htmlspecialchars($page['content']) ?>
                                                    </td>
                                                    <td style="padding:14px 12px; color:#475569;">
                                                        <?= htmlspecialchars($page['updated']) ?>
                                                    </td>
                                                    <td style="padding:14px 12px;">
                                                        <div class="d-flex gap-2 flex-wrap">
                                                            <a href="edit_content.php?id=<?= $page['id'] ?>" class="btn btn-sm btn-primary" title="Edit" style="background:#0f766e; border:none; border-radius:8px;">
                                                                <i class="far fa-edit"></i>
                                                            </a>
                                                            <a href="#" class="btn btn-sm btn-danger" title="Delete" style="background:#ef4444; border:none; border-radius:8px;">
                                                                <i class="far fa-trash-alt"></i>
                                                            </a>
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
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include_once('footer.php'); ?>
</body>
</html>

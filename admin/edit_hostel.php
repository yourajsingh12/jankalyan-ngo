<?php
session_start();
include('db_conn.php');

if (!isset($_GET['id'])) {
  echo "<script>alert('Invalid Request!'); window.location='add_hostels.php';</script>";
  exit;
}

$id = intval($_GET['id']);

// Fetch existing hostel details
$query = mysqli_query($conn, "SELECT * FROM hostels WHERE id='$id'");
$hostel = mysqli_fetch_assoc($query);

if (!$hostel) {
  echo "<script>alert('Hostel not found!'); window.location='add_hostels.php';</script>";
  exit;
}

// 🔹 Handle form submission
if (isset($_POST['update'])) {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $location = mysqli_real_escape_string($conn, $_POST['location']);
  $map_link = mysqli_real_escape_string($conn, $_POST['map_link']);
  $price = mysqli_real_escape_string($conn, $_POST['price']);
  $description = mysqli_real_escape_string($conn, $_POST['description']);

  // ✅ Limit description to 120 words
  if (str_word_count($description) > 120) {
    echo "<script>alert('Description should not exceed 120 words.'); window.history.back();</script>";
    exit;
  }

  // Directory for uploads
  $uploadDir = 'img/hostels/';
  if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

  // 🔹 Handle images
  for ($i = 1; $i <= 4; $i++) {
    if (!empty($_FILES["image$i"]["name"])) {

        // Generate unique filename
        $fileName = time() . "_$i_" . basename($_FILES["image$i"]["name"]);
        $targetFile = $uploadDir . $fileName;

        // Upload new file
        move_uploaded_file($_FILES["image$i"]["tmp_name"], $targetFile);

        // Delete old image if it exists
        if (!empty($hostel["image$i"]) && file_exists($hostel["image$i"])) {
            unlink($hostel["image$i"]);
        }

        // Update database
        mysqli_query($conn, "UPDATE hostels SET image$i='$targetFile' WHERE id='$id'");
    }
}


  // 🔹 Update text fields
      $updateQuery = "UPDATE hostels 
    SET name='$name',
        location='$location',
        price='$price',
        description='$description',
        map_link='$map_link'
    WHERE id='$id'";


  if (mysqli_query($conn, $updateQuery)) {
    $_SESSION['update_success'] = true;
    header("Location: edit_hostel.php?id=$id");
    exit;
  } else {
    echo "<script>alert('Error updating hostel.');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="zxx">
<?php include_once('header.php'); ?>

<body class="crm_body_bg">
<?php include_once('sidebar.php'); ?>

<section class="main_content dashboard_part large_header_bg">
  <div class="container-fluid g-0">
    <div class="row">
      <div class="col-lg-12 p-0">
        <div class="header_iner d-flex justify-content-between align-items-center">
          <h4>Edit Hostel</h4>
          <a href="add_hostels.php" class="btn btn-secondary btn-sm">← Back</a>
        </div>
      </div>
    </div>
  </div>

  <div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="white_card mb_30">
            <div class="white_card_header">
              <h4>Update Hostel Details</h4>
            </div>
            <div class="white_card_body">
              <form method="POST" enctype="multipart/form-data">
                <div class="form-group mb-3">
                  <label>Hostel Name</label>
                  <input type="text" name="name" value="<?= htmlspecialchars($hostel['name']); ?>" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                  <label>Location</label>
                  <input type="text" name="location" value="<?= htmlspecialchars($hostel['location']); ?>" class="form-control" required>
                </div>
                
                <div class="form-group mb-3">
                      <label>Google Map Link</label>
                      <input 
                        type="url" 
                        name="map_link" 
                        class="form-control"
                        placeholder="Paste Google Map link"
                        value="<?= htmlspecialchars($hostel['map_link'] ?? ''); ?>"
                        required
                      >
                      <small class="text-muted">
                        Example: https://maps.google.com/?q=...
                      </small>
                    </div>


                <div class="form-group mb-3">
                  <label>Price</label>
                  <input type="number" name="price" value="<?= htmlspecialchars($hostel['price']); ?>" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                  <label>Description (Max 120 words)</label>
                  <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($hostel['description']); ?></textarea>
                </div>

                <div class="form-group mb-3">
                  <label>Current Images</label>
                  <div class="d-flex flex-wrap gap-2">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                      <?php if (!empty($hostel["image$i"])): ?>
                        <img src="<?= htmlspecialchars($hostel["image$i"]); ?>" width="70" height="70" style="object-fit:cover;border-radius:5px;border:1px solid #ccc;">
                      <?php endif; ?>
                    <?php endfor; ?>
                  </div>
                </div>

                <div class="form-group mb-3">
                  <label>Upload New Images (optional)</label>
                  <div class="row">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                      <div class="col-md-3 mb-2">
                        <input type="file" name="image<?= $i ?>" class="form-control" accept="image/*">
                      </div>
                    <?php endfor; ?>
                  </div>
                </div>

                <div class="text-center mt-4">
                  <button type="submit" name="update" class="btn btn-primary">Update Hostel</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
// ✅ Show SweetAlert after successful update
if (isset($_SESSION['update_success']) && $_SESSION['update_success'] === true) {
  echo "<script>
    Swal.fire({
      icon: 'success',
      title: 'Updated!',
      text: 'Hostel updated successfully!',
      timer: 1500,
      showConfirmButton: false
    }).then(() => {
      window.location = 'add_hostels.php';
    });
  </script>";
  unset($_SESSION['update_success']);
}
?>
</body>
</html>

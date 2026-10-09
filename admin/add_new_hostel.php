<?php
include('db_conn.php');

// Handle form submission
if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $map_link = mysqli_real_escape_string($conn, $_POST['map_link']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $created_at = date('Y-m-d H:i:s');

    // Check description word limit (server-side validation)
    $wordCount = str_word_count($description);
    if ($wordCount > 120) {
        echo "<script>alert('Description should not exceed 120 words.'); window.history.back();</script>";
        exit;
    }

    // Directory to upload images
    $uploadDir = 'img/hostels/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $images = [];

    // Upload up to 4 images with dimension check
    for ($i = 1; $i <= 4; $i++) {
        if (!empty($_FILES["image$i"]["name"])) {

            $fileName = time() . "_$i_" . basename($_FILES["image$i"]["name"]);
            $targetFile = $uploadDir . $fileName;
            move_uploaded_file($_FILES["image$i"]["tmp_name"], $targetFile);

            $images["image$i"] = $targetFile;

        } else {
            $images["image$i"] = '';
        }
    }

    // Insert into database
       $sql = "INSERT INTO hostels 
    (name, location, price, description, image1, image2, image3, image4, map_link, created_at)
    VALUES 
    ('$name', '$location', '$price', '$description',
     '{$images['image1']}', '{$images['image2']}', '{$images['image3']}', '{$images['image4']}',
     '$map_link', '$created_at')";


    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Hostel added successfully!'); window.location='add_hostels.php';</script>";
    } else {
        echo "<script>alert('Error adding hostel. Please try again.');</script>";
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
          <div class="sidebar_icon d-lg-none"><i class="ti-menu"></i></div>
          <h4 class="mb-0">Add New Hostel</h4>
          <a href="add_hostels.php" class="btn btn-secondary btn-sm">← Back to Hostel List</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Form Section -->
  <div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="white_card mb_30">
            <div class="white_card_header">
              <h4>Add Hostel Details</h4>
            </div>
            <div class="white_card_body">
              <form method="POST" enctype="multipart/form-data" id="hostelForm">
                <div class="form-group mb-3">
                  <label>Hostel Name</label>
                  <input type="text" name="name" class="form-control" placeholder="Enter hostel name" required>
                </div>

                <div class="form-group mb-3">
                  <label>Location</label>
                  <input type="text" name="location" class="form-control" placeholder="Enter location" required>
                </div>

                <div class="form-group mb-3">
                  <label>Google Map Link</label>
                  <input 
                    type="url" 
                    name="map_link" 
                    class="form-control" 
                    placeholder="Paste Google Map link here"
                    required
                  >
                  <small class="text-muted">
                    Example: https://maps.google.com/?q=...
                  </small>
                </div>

                <div class="form-group mb-3">
                  <label>Price</label>
                  <input type="number" name="price" class="form-control" placeholder="Enter price" required>
                </div>

                <div class="form-group mb-3">
                  <label>Description <small>(Max 120 words)</small></label>
                  <textarea name="description" id="description" class="form-control" rows="4" placeholder="Enter description" required></textarea>
                  <small id="wordCount" class="text-muted">0 / 120 words</small>
                </div>

                <div class="form-group mb-3">
                  <label>Upload Images (Max 4, Max Size 550x456)</label>
                  <div class="row">
                    <?php for ($i = 1; $i <= 4; $i++) { ?>
                      <div class="col-md-3 mb-2">
                        <input type="file" name="image<?php echo $i; ?>" class="form-control" accept="image/*">
                      </div>
                    <?php } ?>
                  </div>
                </div>

                <div class="text-center mt-4">
                  <button type="submit" name="submit" class="btn btn-primary">Add Hostel</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="footer_part">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-lg-12">
          <div class="footer_text text-center">
            <p>© 2025 Management Admin. All rights reserved.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

</section>

<?php include_once('footer.php'); ?>

<!-- JS Validation for description only -->
<script>
  const description = document.getElementById("description");
  const wordCount = document.getElementById("wordCount");

  description.addEventListener("input", function () {
    const words = this.value.trim().split(/\s+/).filter(Boolean);
    wordCount.textContent = `${words.length} / 120 words`;

    if (words.length > 120) {
      this.value = words.slice(0, 120).join(" ");
      alert("Description cannot exceed 120 words!");
    }
  });
</script>

</body>
</html>

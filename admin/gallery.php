<?php
include('db_conn.php');

// Image Upload Handler
if (isset($_POST['upload'])) {
    $fileName = $_FILES['image']['name'];
    $fileTmp = $_FILES['image']['tmp_name'];
    $fileSize = $_FILES['image']['size'];
    $uploadDir = "img/gallery/";
    $uploadPath = $uploadDir . basename($fileName);
    $fileType = strtolower(pathinfo($uploadPath, PATHINFO_EXTENSION));

    // Ensure folder exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Allowed file formats
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    // Max limits (550.456 KB / 550.456 px)
    $maxSize = 550456; // bytes
    $maxDimension = 550.456; // pixels

    // // Validate file type
    // if (!in_array($fileType, $allowedTypes)) {
    //     echo "<script>alert('Only JPG, JPEG, PNG, and GIF files are allowed.'); window.location.href='gallery.php';</script>";
    //     exit;
    // }

    // // Validate file size (≤ 550.456 KB)
    // if ($fileSize > $maxSize) {
    //     echo "<script>alert('Image size must be less than or equal to 550 KB.'); window.location.href='gallery.php';</script>";
    //     exit;
    // }

    // Validate image dimensions (≤ 550.456 px)
    // $imageInfo = getimagesize($fileTmp);
    // if ($imageInfo !== false) {
    //     $width = $imageInfo[0];
    //     $height = $imageInfo[1];

    //     if ($width > $maxDimension || $height > $maxDimension) {
    //         echo "<script>alert('Image width and height must be less than or equal to 550 px.'); window.location.href='gallery.php';</script>";
    //         exit;
    //     }
    // } else {
    //     echo "<script>alert('Invalid image file.'); window.location.href='gallery.php';</script>";
    //     exit;
    // }

    // Upload and insert into database
    if (move_uploaded_file($fileTmp, $uploadPath)) {
        $insert = mysqli_query($conn, "INSERT INTO gallery (image_path) VALUES ('$uploadPath')");
        if ($insert) {
            echo "<script>alert('Image uploaded successfully.'); window.location.href='gallery.php';</script>";
        } else {
            echo "<script>alert('Database insert error.'); window.location.href='gallery.php';</script>";
        }
    } else {
        echo "<script>alert('Failed to move uploaded file. Check folder permissions.'); window.location.href='gallery.php';</script>";
    }
}

// Delete Image Handler
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $getImg = mysqli_query($conn, "SELECT image_path FROM gallery WHERE id='$id'");
    $imgData = mysqli_fetch_assoc($getImg);
    $imgPath = $imgData['image_path'];

    if (file_exists($imgPath)) {
        unlink($imgPath);
    }

    mysqli_query($conn, "DELETE FROM gallery WHERE id='$id'");
    echo "<script>alert('Image deleted successfully.'); window.location.href='gallery.php';</script>";
}

// Fetch all images
$images = mysqli_query($conn, "SELECT * FROM gallery ORDER BY id DESC");
?>


<!DOCTYPE html>
<html lang="zxx">
<?php include_once('header.php'); ?>

<body class="crm_body_bg">
  <?php include_once('sidebar.php'); ?>

  <section class="main_content dashboard_part large_header_bg">
    <div class="container-fluid g-0">
      <div class="row">
        <div class="col-lg-12 p-4">
          <div class="white_card card_height_100 mb_30">
            <div class="white_card_header d-flex justify-content-between align-items-center">
              <h3>Manage Gallery</h3>
              <form action="" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-3">
                <input type="file" name="image" required class="form-control" accept="image/*" style="width:250px;">
                <button type="submit" name="upload" class="btn btn-primary">Upload</button>
              </form>
            </div>

            <div class="white_card_body mt-4">
              <div class="row">
                <?php while ($row = mysqli_fetch_assoc($images)) { ?>
                  <div class="col-lg-3 col-md-4 col-sm-6 mb-4 position-relative">
                    <div class="single_gallery_part position-relative" style="overflow:hidden; border-radius:8px;">
                      <img src="<?php echo $row['image_path']; ?>" alt="Gallery Image" class="w-100 rounded" style="height:200px; object-fit:cover;">
                      <a href="?delete=<?php echo $row['id']; ?>" 
                         onclick="return confirm('Are you sure you want to delete this image?')" 
                         class="delete-btn">
                        <i class="fa fa-trash"></i>
                      </a>
                    </div>
                  </div>
                <?php } ?>
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

  <style>
    .delete-btn {
      position: absolute;
      top: 8px;
      right: 10px;
      background: rgba(255, 0, 0, 0.8);
      color: #fff;
      padding: 6px 8px;
      border-radius: 50%;
      text-align: center;
      font-size: 14px;
      transition: background 0.3s ease;
    }

    .delete-btn:hover {
      background: #d00000;
      color: #fff;
    }
  </style>

  <?php include_once('footer.php'); ?>
</body>
</html>

<?php
include('db_conn.php');

// Image Upload Handler
if (isset($_POST['upload'])) {
    $fileName = $_FILES['image']['name'];
    $fileTmp = $_FILES['image']['tmp_name'];
    $uploadPath = "upload/" . $fileName;

    if (move_uploaded_file($fileTmp, $uploadPath)) {
        $insert = mysqli_query($conn, "INSERT INTO gallery (image_path) VALUES ('$uploadPath')");
        if ($insert) {
            echo "<script>alert('Image uploaded successfully'); window.location.href='gallery.php';</script>";
        } else {
            echo "<script>alert('Database error');</script>";
        }
    } else {
        echo "<script>alert('File upload failed');</script>";
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
    echo "<script>alert('Image deleted successfully'); window.location.href='gallery.php';</script>";
}

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
        <div class="white_card">
          <div class="white_card_header d-flex justify-content-between align-items-center">
            <h3>Manage Gallery</h3>
            <form action="" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-3">
              <input type="file" name="image" required class="form-control" accept="image/*" style="width:250px;">
              <button type="submit" name="upload" class="btn btn-primary">Upload</button>
            </form>
          </div>

          <div class="white_card_body mt-4">
            <table class="table table-bordered text-center align-middle">
              <thead class="table-dark">
                <tr>
                  <th>#</th>
                  <th>Image</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $i = 1;
                while ($row = mysqli_fetch_assoc($images)) {
                ?>
                  <tr>
                    <td><?php echo $i++; ?></td>
                    <td><img src="<?php echo $row['image_path']; ?>" alt="Gallery Image" style="width:100px;height:70px;border-radius:5px;"></td>
                    <td>
                      <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this image?')">Delete</a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>

<?php include_once('footer.php'); ?>
</body>
</html>

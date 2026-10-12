<?php
include('db_conn.php');

// Fetch all hostels
$hostels = mysqli_query($conn, "SELECT * FROM hostels ORDER BY id DESC");
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
            <div class="sidebar_icon d-lg-none">
              <i class="ti-menu"></i>
            </div>
            <div class="serach_field-area d-flex align-items-center">
              <div class="search_inner">
                <form action="#">
                  <div class="search_field">
                    <input type="text" placeholder="Search hostel..." />
                  </div>
                  <button type="submit"><img src="img/icon/icon_search.svg" alt=""></button>
                </form>
              </div>
            </div>
            <div class="header_right d-flex align-items-center">
              <div class="profile_info">
                <img src="img/client_img.png" alt="#">
                <div class="profile_info_iner">
                  <p>Welcome to Jan Kalyan Sangh Foundation</p>
                  <h5>Admin</h5>
                  <div class="profile_info_details">
                    <a href="#">My Profile</a>
                    <a href="#">Settings</a>
                    <a href="#">Log Out</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main_content_iner overly_inner">
      <div class="container-fluid p-0">
        <div class="row">
          <div class="col-lg-12">
            <div class="white_card card_height_100 mb_30 pt-4">
              <div class="white_card_body">
                <div class="QA_section">
                  <div class="white_box_tittle list_header d-flex justify-content-between align-items-center">
                    <h4>Hostel List</h4>
                    <a href="add_new_hostel.php" class="btn_1">+ Add Hostel</a>
                  </div>

                  <div class="QA_table mt-4">
  <table class="table table-bordered table-striped text-center align-middle">
    <thead class="table-dark">
      <tr>
        <th>Sr No.</th>
        <th>Hostel Name</th>
        <th>Location</th>
        <th>Map</th>
        <th>Price</th>
        <th>Description</th>
        <th>Images</th>
        <th>Created At</th>
        <th>Action</th>
      </tr>
    </thead>
   <tbody>
    <?php
    if (mysqli_num_rows($hostels) > 0) {

        $sn = 1; // Serial number start

        while ($row = mysqli_fetch_assoc($hostels)) {
    ?>
            <tr>
              <td><?php echo $sn++; ?></td> <!-- SERIAL NUMBER ONLY -->

              <td><?php echo $row['name']; ?></td>
              <td><?php echo $row['location']; ?></td>
                <td>
                 <?php if (!empty($row['map_link'])) { ?>
                    <a href="<?php echo htmlspecialchars($row['map_link']); ?>" 
                       target="_blank" 
                       class="btn btn-sm btn-success">
                       <i class="fas fa-map-marker-alt"></i> View Map
                    </a>
                  <?php } else { ?>
                    <span class="text-muted">N/A</span>
                  <?php } ?>
                </td>

              <td><?php echo $row['price']; ?></td>

              <td style="max-width:300px;"><?php echo $row['description']; ?></td>

              <td>
                <div class="d-flex justify-content-center flex-wrap gap-2">
                  <?php for ($i = 1; $i <= 4; $i++) {
                    $img = $row["image$i"];
                    if (!empty($img)) {
                      echo "<img src='$img' style='width:60px;height:60px;object-fit:cover;border-radius:5px;border:1px solid #ddd;'>";
                    }
                  } ?>
                </div>
              </td>

              <td><?php echo date("d M Y", strtotime($row['created_at'])); ?></td>

              <td>
                <div class="d-flex justify-content-center gap-2">
                  <a href="edit_hostel.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">
                    <i class="far fa-edit"></i>
                  </a>
                  
                  <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $row['id']; ?>">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>

            </tr>

    <?php
        }
    } else {
      echo "<tr><td colspan='7'>No hostels found</td></tr>";
    }
    ?>
</tbody>

  </table>
</div>

                </div>
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
              <p>© 2025 Jan Kalyan Sangh Foundation. All rights reserved.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php include_once('footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<script>
document.querySelectorAll('.delete-btn').forEach(button => {
  button.addEventListener('click', function() {
    const hostelId = this.getAttribute('data-id');

    Swal.fire({
      title: 'Are you sure?',
      text: "This action will permanently delete the hostel!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        // Call PHP delete script
        fetch(`delete_hostel.php?id=${hostelId}`)
          .then(res => res.text())
          .then(() => {
            // Show success alert and refresh
            Swal.fire({
              icon: 'success',
              title: 'Deleted!',
              text: 'Hostel deleted successfully!',
              timer: 1500,
              showConfirmButton: false
            }).then(() => {
              // Refresh page automatically
              window.location.reload();
            });
          });
      }
    });
  });
});
</script>



</body>

</html>

<?php
include('admin/db_conn.php');
$result = mysqli_query($conn, "SELECT * FROM contact_info");
?>

<!DOCTYPE html>
<html lang="zxx">
<?php include_once('header.php'); ?>
<body class="crm_body_bg">
<?php include_once('sidebar.php'); ?>

<section class="main_content dashboard_part large_header_bg">
  <div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
      <div class="row">
        <div class="col-lg-12">
          <div class="white_card card_height_100 mb_30 pt-4">
            <div class="white_card_body">
              <div class="QA_section">
                <div class="white_box_tittle list_header d-flex justify-content-between align-items-center">
                  <h4>Manage Contact Info</h4>
                  <a href="add_contact.php" class="btn_1">Add New</a>
                </div>

                <div class="QA_table mb_30 mt-4">
                  <table class="table table-bordered text-center align-middle">
                    <thead class="table-dark">
                      <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Website</th>
                        <th>Created At</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                          <td><?php echo $row['id']; ?></td>
                          <td><?php echo htmlspecialchars($row['contact_name']); ?></td>
                          <td><?php echo htmlspecialchars($row['phone']); ?></td>
                          <td><?php echo htmlspecialchars($row['email']); ?></td>
                          <td><?php echo htmlspecialchars($row['address']); ?></td>
                          <td>
                            <a href="<?php echo htmlspecialchars($row['website']); ?>" target="_blank">
                              <?php echo htmlspecialchars($row['website']); ?>
                            </a>
                          </td>
                          <td><?php echo $row['created_at']; ?></td>
                          <td>
                            <div class="action_btns d-flex justify-content-center">
                              <a href="edit_contact.php?id=<?php echo $row['id']; ?>" class="action_btn mr_10" title="Edit">
                                <i class="far fa-edit"></i>
                              </a>
                              <a href="copy_contact.php?id=<?php echo $row['id']; ?>" class="action_btn mr_10" title="Copy">
                                <i class="far fa-copy"></i>
                              </a>
                              <a href="delete_contact.php?id=<?php echo $row['id']; ?>" class="action_btn text-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this record?')">
                                <i class="fas fa-trash"></i>
                              </a>
                            </div>
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
      </div>
    </div>
  </div>

  <?php include_once('footer.php'); ?>
</section>
</body>
</html>

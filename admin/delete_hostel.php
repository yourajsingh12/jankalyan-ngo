<?php
include('db_conn.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch hostel for deleting images
    $result = mysqli_query($conn, "SELECT * FROM hostels WHERE id='$id'");
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        for ($i = 1; $i <= 4; $i++) {
            if (!empty($row["image$i"]) && file_exists($row["image$i"])) {
                unlink($row["image$i"]); // Delete image from folder
            }
        }

        // Delete from DB
        mysqli_query($conn, "DELETE FROM hostels WHERE id='$id'");

        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        Swal.fire({
          icon: 'success',
          title: 'Deleted!',
          text: 'Hostel deleted successfully!',
          timer: 2000,
          showConfirmButton: false
        }).then(() => window.location='add_hostels.php');
        </script>";
    } else {
        echo "<script>alert('Hostel not found!'); window.location='add_hostels.php';</script>";
    }
} else {
    header("Location: add_hostels.php");
    exit;
}
?>

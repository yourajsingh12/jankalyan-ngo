<?php
include('db_conn.php');

$alert = ''; // To track alert state

// Fetch data by ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM contact_info WHERE id=$id");
    $data = mysqli_fetch_assoc($result);
}

// Update logic
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $contact_name = mysqli_real_escape_string($conn, $_POST['contact_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $website = mysqli_real_escape_string($conn, $_POST['website']);

    $update = mysqli_query($conn, "
        UPDATE contact_info 
        SET contact_name='$contact_name', phone='$phone', email='$email', address='$address', website='$website' 
        WHERE id=$id
    ");

    if ($update) {
        header("Location: edit_contact.php?id=$id&status=success");
        exit();
    } else {
        header("Location: edit_contact.php?id=$id&status=error");
        exit();
    }
}

// Check for alert in URL
if (isset($_GET['status'])) {
    $alert = $_GET['status'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Contact Info</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f7f8fc;
            color: #333;
        }
        .edit-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 600px;
        }
        .edit-card h2 {
            font-weight: 700;
            color: #4e73df;
            margin-bottom: 25px;
            text-align: center;
        }
        label {
            font-weight: 600;
        }
        .btn-success {
            background-color: #1cc88a;
            border: none;
        }
        .btn-success:hover {
            background-color: #17a673;
        }
        .btn-secondary {
            background-color: #858796;
            border: none;
        }
    </style>
</head>
<body>

<div class="edit-card">
    <h2>Edit Contact Information</h2>

    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

        <div class="mb-3">
            <label>Contact Name</label>
            <input type="text" name="contact_name" value="<?php echo $data['contact_name']; ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo $data['phone']; ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo $data['email']; ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control" rows="3" required><?php echo $data['address']; ?></textarea>
        </div>

        <div class="mb-3">
            <label>Website</label>
            <input type="url" name="website" value="<?php echo $data['website']; ?>" class="form-control" required>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <button type="submit" name="update" class="btn btn-success px-4">Update</button>
            <a href="address.php" class="btn btn-secondary px-4">Cancel</a>
        </div>
    </form>
</div>

<!-- SweetAlert for Success/Error -->
<?php if ($alert === 'success'): ?>
<script>
Swal.fire({
    title: "Updated!",
    text: "Contact information updated successfully.",
    icon: "success",
    confirmButtonColor: "#1cc88a"
}).then(() => {
    window.location.href = "address.php";
});
</script>
<?php elseif ($alert === 'error'): ?>
<script>
Swal.fire({
    title: "Error!",
    text: "Something went wrong while updating data.",
    icon: "error",
    confirmButtonColor: "#e74a3b"
});
</script>
<?php endif; ?>

</body>
</html>

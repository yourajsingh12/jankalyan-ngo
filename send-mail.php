<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Getting Form Values
    $name = $_POST["name"];
    $email = $_POST["email"];
    $subject = $_POST["subject"];
    $message = $_POST["message"];

    // WhatsApp Redirect Number
    $whatsappNumber = "918929939502"; // Without +

    // WhatsApp Message
    $waMessage = urlencode("
New Website Inquiry:
Name: $name
Email: $email
Subject: $subject
Message: $message
");

    // Redirect to WhatsApp and then back with SweetAlert
    echo "<script>
            window.location.href='https://wa.me/$whatsappNumber?text=$waMessage';
            setTimeout(function(){
                window.location.href='contact.php?success=1';
            }, 2000);
          </script>";
    exit();
}
?>

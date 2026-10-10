<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
start_session();

$submissionStatus = is_string($_GET['status'] ?? null) ? $_GET['status'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<?php include_once('head.php');?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body>

    


    <?php include_once('header.php');?>


    <main class="main">

        <!-- breadcrumb -->
        <div class="site-breadcrumb" style="background: url(assets/img/breadcrumb/01.jpg)">
            <div class="container">
                <h2 class="breadcrumb-title">Contact Us</h2>
                <ul class="breadcrumb-menu">
                    <li><a href="index.php">Home</a></li>
                    <li class="active">Contact Us</li>
                </ul>
            </div>
        </div>
        <!-- breadcrumb end -->


        <!-- contact area -->
        <div class="contact-area py-100">
            <div class="container">
                <div class="contact-content">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="contact-info">
                                <div class="icon">
                                    <i class="fal fa-map-location-dot"></i>
                                </div>
                                <div class="content">
                                    <h5>Office Address</h5>
                                    <p>252-A/2, KH No-44/18,<br>
                                        Nangli Vihar Extn., Baprola BLK C5&amp;C6,<br>
                                        New Delhi, India</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="contact-info">
                                <div class="icon">
                                    <i class="fal fa-phone-volume"></i>
                                </div>
                                <div class="content">
                                    <h5>Call Us</h5>
                                    <p>+91 8929939502</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="contact-info">
                                <div class="icon">
                                    <i class="fal fa-envelopes"></i>
                                </div>
                                <div class="content">
                                    <h5>Email Us</h5>
                                    <p><a href="mailto:support@jankalyansanghfoundation.com">support@jankalyansanghfoundation.com</a></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="contact-info">
                                <div class="icon">
                                    <i class="fal fa-alarm-clock"></i>
                                </div>
                                <div class="content">
                                    <h5>Open Time</h5>
                                    <p>Mon - Sun (10.00AM - 07.30PM)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="contact-form-wrap">
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="contact-form">
                                <div class="contact-form-header">
                                    <h2>Get In Touch</h2>
                                    <p>Feel free to reach out to us anytime. We are here to assist you with your questions, services, or support needs.</p>
                                </div>

                                <?php if ($submissionStatus === 'success'): ?>
                                    <div class="form-message alert alert-success" role="status">
                                        Thank you for contacting us. Your message has been received.
                                    </div>
                                <?php elseif ($submissionStatus === 'invalid'): ?>
                                    <div class="form-message alert alert-danger" role="alert">
                                        Please check your details and make sure your message is between 10 and 5,000 characters.
                                    </div>
                                <?php elseif ($submissionStatus === 'error'): ?>
                                    <div class="form-message alert alert-danger" role="alert">
                                        We could not send your message right now. Please try again shortly.
                                    </div>
                                <?php endif; ?>
                                <form method="post" action="send-mail.php" id="contact-form">
                                    <?= csrf_field() ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="form-icon">
                                                    <i class="far fa-user-tie"></i>
                                                    <input type="text" class="form-control" name="name" placeholder="Your Name" autocomplete="name" minlength="2" maxlength="160" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="form-icon">
                                                    <i class="far fa-envelope"></i>
                                                    <input type="email" class="form-control" name="email" placeholder="Your Email" autocomplete="email" maxlength="190" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="form-icon">
                                            <i class="far fa-phone"></i>
                                            <input type="tel" class="form-control" name="phone" placeholder="Your Mobile Number" autocomplete="tel" inputmode="numeric" minlength="10" maxlength="10" pattern="[6-9][0-9]{9}" title="Enter a valid 10-digit Indian mobile number" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="form-icon">
                                            <i class="far fa-pen"></i>
                                            <input type="text" class="form-control" name="subject" placeholder="Your Subject" maxlength="255" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="form-icon">
                                            <i class="far fa-comment-lines"></i>
                                            <textarea name="message" cols="30" rows="5" class="form-control"
                                                placeholder="Write Your Message" minlength="10" maxlength="5000" required></textarea>
                                        </div>
                                    </div>

                                    <button type="submit" class="theme-btn">Send
                                        Message <i class="far fa-paper-plane"></i></button>
                                </form>

                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="contact-img">
                                <img src="assets/img/contact/01.php" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end contact area -->


    

    </main>


    <?php include_once('footer.php');?>
</body>



</html>
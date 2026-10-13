<?php
require_once __DIR__ . '/includes/functions.php';
$footerAbout = setting('footer_about');
$contactAddress = setting('contact_address_plain');
$contactPhone = setting('contact_phone');
$contactPhoneHref = preg_replace('/[^0-9+]/', '', $contactPhone);
$contactEmail = setting('contact_email');
$footerSocialLinks = rows(
    'SELECT platform, icon_class, url FROM social_links WHERE status = 1 ORDER BY sort_order ASC, id ASC'
);
?>

<!-- footer area -->
    <footer class="footer-area">
        <div class="footer-shape">
            <img src="assets/img/shape/01.png" alt="">
        </div>
        <div class="footer-widget">
            <div class="container">
                <div class="footer-widget-wrap pt-100 pb-80">
                    <div class="row g-4">
                        <!-- About Us -->
                        <div class="col-lg-5">
                            <div class="footer-widget-box about-us">
                                <a href="index.php" class="footer-logo">
                                    <img src="assets/img/logo/study.png" alt="Jan Kalyan Sangh Foundation logo">
                                </a>
                                <p class="mb-4">
                                    <?= e($footerAbout) ?>
                                </p>
                                <div class="footer-newsletter">
                                    <h6>Subscribe Our Newsletter</h6>
                                    <div class="newsletter-form">
                                        <form action="#">
                                            <div class="form-group">
                                                <div class="form-icon">
                                                    <i class="far fa-envelopes"></i>
                                                    <input type="email" class="form-control" placeholder="Your Email">
                                                    <button class="theme-btn" type="submit">
                                                        Subscribe <span class="far fa-paper-plane"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Company Links -->
                        <div class="col-6 col-lg-2">
                            <div class="footer-widget-box list">
                                <h4 class="footer-widget-title">Quick Links</h4>
                                <ul class="footer-list">
                                    <li><a href="about.php"><i class="far fa-angle-double-right"></i>About Us</a></li>
                                    <li><a href="mission.php"><i class="far fa-angle-double-right"></i>Mission</a></li>
                                    <li><a href="projects.php"><i class="far fa-angle-double-right"></i>Projects</a></li>
                                    <li><a href="campaigns.php"><i class="far fa-angle-double-right"></i>Campaigns</a></li>
                                    <li><a href="gallery.php"><i class="far fa-angle-double-right"></i>Gallery</a></li>
                                    <li><a href="contact.php"><i class="far fa-angle-double-right"></i>Contact</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- NGO Services Links -->
                        <div class="col-6 col-lg-2">
                            <div class="footer-widget-box list">
                                <h4 class="footer-widget-title">Our Focus</h4>
                                <ul class="footer-list">
                                    <li><a href="about.php"><i class="far fa-angle-double-right"></i>Education</a></li>
                                    <li><a href="mission.php"><i class="far fa-angle-double-right"></i>Health & Nutrition</a></li>
                                    <li><a href="projects.php"><i class="far fa-angle-double-right"></i>Women Empowerment</a></li>
                                    <li><a href="donate.php"><i class="far fa-angle-double-right"></i>Community Support</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="col-lg-3">
                            <div class="footer-widget-box">
                                <h4 class="footer-widget-title">Get In Touch</h4>
                                <ul class="footer-contact">
                                    <li>
                                        <div class="icon">
                                            <i class="far fa-location-dot"></i>
                                        </div>
                                        <div class="content">
                                            <h6>Head Office</h6>
                                            <p><?= nl2br(e($contactAddress)) ?></p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="icon">
                                            <i class="far fa-phone"></i>
                                        </div>
                                        <div class="content">
                                            <h6>Call Us</h6>
                                            <a href="tel:<?= e($contactPhoneHref) ?>"><?= e($contactPhone) ?></a>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="icon">
                                            <i class="far fa-envelope"></i>
                                        </div>
                                        <div class="content">
                                            <h6>Mail Us</h6>
                                            <a href="mailto:<?= e($contactEmail) ?>"><?= e($contactEmail) ?></a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 align-self-center">
                        <p class="copyright-text">
                            &copy; <span id="date"></span> <a href="index.php">Jan Kalyan Sangh Foundation</a> All Rights Reserved.
                        </p>
                    </div>
                    <div class="col-md-6 align-self-center">
                        <ul class="footer-social">
                            <?php foreach ($footerSocialLinks as $socialLink): ?>
                                <li><a href="<?= e($socialLink['url']) ?>" target="_blank" rel="noopener" aria-label="<?= e($socialLink['platform']) ?>"><i class="<?= e($socialLink['icon_class']) ?>"></i></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- footer area end -->


    <!-- scroll-top -->
    <a href="#" id="scroll-top"><i class="far fa-arrow-up"></i></a>
    <!-- scroll-top end -->

    <script>
document.getElementById("loanForm").addEventListener("submit", function(e){
    e.preventDefault();

    let P = parseFloat(document.getElementById("loanAmount").value);
    let R = parseFloat(document.getElementById("interestRate").value) / 12 / 100;
    let N = parseFloat(document.getElementById("loanYears").value) * 12;

    let emi = P * R * Math.pow(1+R, N) / (Math.pow(1+R, N) - 1);

    let totalPayment = emi * N;
    let totalInterest = totalPayment - P;

    document.getElementById("emiResult").value = "₹ " + emi.toFixed(2);
    document.getElementById("totalInterest").value = "₹ " + totalInterest.toFixed(2);
});
</script>

<script>
// Show popup after 5 seconds
window.addEventListener('load', function() {
    setTimeout(function() {
        document.getElementById('quotePopup').style.display = 'block';
    }, 5000); // 5000ms = 5 seconds
});

// Close popup on clicking 'x'
document.getElementById('closePopup').addEventListener('click', function() {
    document.getElementById('quotePopup').style.display = 'none';
});

// Close popup when clicking outside the popup content
window.addEventListener('click', function(e) {
    if(e.target == document.getElementById('quotePopup')) {
        document.getElementById('quotePopup').style.display = 'none';
    }
});
</script>

    <!-- js -->
    <script data-cfasync="false" src="../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/modernizr.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/imagesloaded.pkgd.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/isotope.pkgd.min.js"></script>
    <script src="assets/js/jquery.appear.min.js"></script>
    <script src="assets/js/jquery.easing.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/counter-up.js"></script>
    <script src="assets/js/jquery.nice-select.min.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/main.js"></script>

<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"version":"2024.11.0","token":"1190e059c5bc497bafd35e121aae37b1","r":1,"server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}' crossorigin="anonymous"></script>
</body>
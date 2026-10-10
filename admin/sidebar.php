<nav class="sidebar dark_sidebar">
        <div class="logo d-flex justify-editor-between">
            <a class="large_logo" href="index.php"><img src="img/logo_white.png" alt=""></a>
            <a class="small_logo" href="index.php"><img src="img/mini_logo.png" alt=""></a>
            <div class="sidebar_close_icon d-lg-none">
                <i class="ti-close"></i>
            </div>
        </div>
        <ul id="sidebar_menu">
           

            <li class="">
                <a href="index.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div class="nav_title">
                        <span>Dashboard</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="index.php#contact-messages" aria-expanded="false">
                    <div class="nav_icon_small">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="nav_title">
                        <span>Contact Messages</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="page_content.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <i class="fas fa-pen"></i>
                    </div>
                    <div class="nav_title">
                        <span>Add Content</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="header_content.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="nav_title">
                        <span>Header Content</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="footer_content.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="nav_title">
                        <span>Footer Content</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="testimonials.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <div class="nav_title">
                        <span>Testimonials</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="social_links.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <i class="fas fa-share-alt"></i>
                    </div>
                    <div class="nav_title">
                        <span>Social Media Links</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="banner.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <i class="fas fa-images"></i>
                    </div>
                    <div class="nav_title">
                        <span>Add Banner</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="mission.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <div class="nav_title">
                        <span>Mission & Vision</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="participants.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="nav_title">
                        <span>Participants</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="achievements.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="nav_title">
                        <span>Achievements</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="projects.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="nav_title">
                        <span>Projects</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="team.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div class="nav_title">
                        <span>Team</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="certificates.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div class="nav_title">
                        <span>Certificates</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="donate.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <div class="nav_title">
                        <span>Donations</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="update_donate.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="nav_title">
                        <span>Edit Donate Page</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a class="has-arrow" href="#" aria-expanded="false">
                    <div class="nav_icon_small">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="nav_title">
                        <span>Setting</span>
                    </div>
                </a>
                <ul>
                    <li><a href="gallery.php"><i class="fas fa-images me-2"></i>Add Gallery</a></li>
                </ul>
            </li>

           <li class="logout-btn-container text-center" style="margin-top: 20px;">
                <button onclick="confirmLogout(event)" class="logout-btn">
                    <i class="fas fa-sign-out-alt" style="margin-right:8px;"></i>
                    Logout
                </button>
           </li>

           
        </ul>
    </nav>

    <style>
        body {
            overflow-x: hidden;
        }

        .sidebar {
            position: fixed !important;
            left: 0 !important;
            top: 0 !important;
            width: 270px !important;
            z-index: 1040 !important;
        }

        .nav_icon_small {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(59,130,246,0.14), rgba(16,185,129,0.12));
            color: #1d4ed8;
            border: 1px solid rgba(59,130,246,0.12);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.6);
            transition: all 0.3s ease;
        }

        .nav_icon_small i {
            font-size: 15px;
            line-height: 1;
            color: #1d4ed8;
        }

        .sidebar #sidebar_menu > li a:hover .nav_icon_small,
        .sidebar #sidebar_menu > li a.active .nav_icon_small,
        .sidebar #sidebar_menu > li.mm-active > a .nav_icon_small {
            background: rgba(255,255,255,0.18);
            border-color: rgba(255,255,255,0.3);
            box-shadow: 0 8px 18px rgba(37,99,235,0.18);
            transform: translateY(-1px);
        }

        .sidebar #sidebar_menu > li a:hover .nav_icon_small i,
        .sidebar #sidebar_menu > li a.active .nav_icon_small i,
        .sidebar #sidebar_menu > li.mm-active > a .nav_icon_small i {
            color: #ffffff;
        }

        .sidebar #sidebar_menu > li ul li a i {
            font-size: 12px;
            color: inherit;
        }

        .main_content.dashboard_part {
            padding-left: 270px !important;
            margin-left: 0 !important;
            width: 100% !important;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 991px) {
            .sidebar {
                left: -280px !important;
            }

            .sidebar.open {
                left: 0 !important;
            }

            .main_content.dashboard_part {
                padding-left: 0 !important;
            }
        }

        @media (max-width: 575px) {
            .page_title_box {
                padding: 18px 16px !important;
            }

            .white_card_body {
                padding: 16px !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.querySelector('.sidebar');
            const closeIcon = document.querySelector('.sidebar_close_icon');

            if (closeIcon && sidebar) {
                closeIcon.addEventListener('click', function () {
                    sidebar.classList.toggle('open');
                });
            }
        });
    </script>


<script>
function confirmLogout(e) {
    e.preventDefault();
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "logout.php";
    }
}
</script>
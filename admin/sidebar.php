<nav class="sidebar dark_sidebar">
        <div class="logo d-flex justify-editor-between">
            <a class="large_logo" href="index-2.php"><img src="img/logo_white.png" alt=""></a>
            <a class="small_logo" href="index-2.php"><img src="img/mini_logo.png" alt=""></a>
            <div class="sidebar_close_icon d-lg-none">
                <i class="ti-close"></i>
            </div>
        </div>
        <ul id="sidebar_menu">
           

            <li class="">
                <a href="index.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <img src="img/menu-icon/dashboard.svg" alt="">
                    </div>
                    <div class="nav_title">
                        <span>Dashboard</span>
                    </div>
                </a>
            </li>



            <li class="">
                <a href="editor.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <img src="img/menu-icon/2.svg" alt="">
                    </div>
                    <div class="nav_title">
                        <span>Add Content</span>
                    </div>
                </a>
            </li>

            
            <li class="">
                <a href="banner.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <img src="img/menu-icon/3.svg" alt="">
                    </div>
                    <div class="nav_title">
                        <span>Add Banner</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="mission.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <img src="img/menu-icon/2.svg" alt="">
                    </div>
                    <div class="nav_title">
                        <span>Mission & Vision</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="participants.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <img src="img/menu-icon/2.svg" alt="">
                    </div>
                    <div class="nav_title">
                        <span>Participants</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="achievements.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <img src="img/menu-icon/2.svg" alt="">
                    </div>
                    <div class="nav_title">
                        <span>Achievements</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="projects.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <img src="img/menu-icon/2.svg" alt="">
                    </div>
                    <div class="nav_title">
                        <span>Projects</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="team.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <img src="img/menu-icon/2.svg" alt="">
                    </div>
                    <div class="nav_title">
                        <span>Team</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="certificates.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <img src="img/menu-icon/2.svg" alt="">
                    </div>
                    <div class="nav_title">
                        <span>Certificates</span>
                    </div>
                </a>
            </li>

            <li class="">
                <a href="donate.php" aria-expanded="false">
                    <div class="nav_icon_small">
                        <img src="img/menu-icon/2.svg" alt="">
                    </div>
                    <div class="nav_title">
                        <span>Donations</span>
                    </div>
                </a>
            </li>



           
            <li class="">
                <a class="has-arrow" href="#" aria-expanded="false">
                    <div class="nav_icon_small">
                        <img src="img/menu-icon/4.svg" alt="">
                    </div>
                    <div class="nav_title">
                        <span>Setting</span>    
                    </div>
                </a>
                <ul>
                    <li><a href="add_hostels.php">Add Hostels</a></li>   
                    <li><a href="address.php">Update Address</a></li>
                    <li><a href="gallery.php">Add Gallery</a></li>    
                </ul>
            </li>

           <li class="logout-btn-container text-center" style="margin-top: 20px;">
                <button onclick="confirmLogout(event)" class="logout-btn">
                    <img src="img/menu-icon/logout.svg" alt="" style="width:18px; margin-right:8px;">
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
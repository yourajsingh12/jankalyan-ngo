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
                        <div class="line_icon open_miniSide d-none d-lg-block">
                            <img src="img/line_img.png" alt="">
                        </div>
                        <div class="serach_field-area d-flex align-items-center">
                            <div class="search_inner">
                                <form action="#">
                                    <div class="search_field">
                                        <input type="text" placeholder="Search">
                                    </div>
                                    <button type="submit">
                                        <img src="img/icon/icon_search.svg" alt="">
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="header_right d-flex justify-content-between align-items-center">
                            <div class="header_notification_warp d-flex align-items-center">
                                <li>
                                    <a class="bell_notification_clicker" href="#">
                                        <img src="img/icon/bell.svg" alt="">
                                        <span>2</span>
                                    </a>
                                    <div class="Menu_NOtification_Wrap">
                                        <div class="notification_Header">
                                            <h4>Notifications</h4>
                                        </div>
                                        <div class="Notification_body"></div>
                                    </div>
                                </li>
                                <li>
                                    <a class="CHATBOX_open" href="#">
                                        <img src="img/icon/msg.svg" alt="">
                                        <span>2</span>
                                    </a>
                                </li>
                            </div>
                            <div class="profile_info">
                                <img src="img/client_img.png" alt="#">
                                <div class="profile_info_iner">
                                    <div class="profile_author_name">
                                        <p>Admin</p>
                                        <h5>Mr Pradeep</h5>
                                    </div>
                                    <div class="profile_info_details"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================== Main Content ================== -->
        <div class="main_content_iner overly_inner">
            <div class="container-fluid p-0">

                <div class="row">
                    <div class="col-12">
                        <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                            <div class="page_title_left d-flex align-items-center">
                                <h3 class="f_s_25 f_w_700 dark_text mr_30">Dashboard</h3>
                                <ol class="breadcrumb page_bradcam mb-0"></ol>
                            </div>
                            <div class="page_title_right">
                                <div class="page_date_button d-flex align-items-center">
                                    <img src="img/icon/calender_icon.svg" alt="">
                                    November 4, 2025
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== Contact Info Table ===== -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30 pt-4">
                            <div class="white_card_body">
                                <div class="QA_section">
                                    <div class="white_box_tittle list_header">
                                        <h4>Contact Information</h4>
                                        <div class="box_right d-flex lms_block">
                                            <div class="serach_field_2">
                                                <div class="search_inner">
                                                    <form action="#">
                                                        <div class="search_field">
                                                            <input type="text" placeholder="Search contacts...">
                                                        </div>
                                                        <button type="submit">
                                                            <i class="ti-search"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="add_button ms-2">
                                                <a href="#" class="btn_1">Add New</a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="QA_table mb_30">
                                        <table class="table lms_table_active text-center align-middle">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Contact Name</th>
                                                    <th>Phone</th>
                                                    <th>Email</th>
                                                    <th>Address</th>
                                                    <th>Website</th>
                                                    <th>Created At</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                include('db_conn.php');
                                                $query = mysqli_query($conn, "SELECT * FROM contact_info");
                                                $i = 1;
                                                while ($row = mysqli_fetch_assoc($query)) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo $i++; ?></td>
                                                        <td><?php echo $row['contact_name']; ?></td>
                                                        <td><?php echo $row['phone']; ?></td>
                                                        <td><?php echo $row['email']; ?></td>
                                                        <td><?php echo $row['address']; ?></td>
                                                        <td><a href="<?php echo $row['website']; ?>" target="_blank"><?php echo $row['website']; ?></a></td>
                                                        <td><?php echo $row['created_at']; ?></td>
                                                        <td>
                                                            <div class="action_btns d-flex justify-content-center">
                                                                <a href="edit_contact.php?id=<?php echo $row['id']; ?>" class="action_btn mr_10" title="Edit">
                                                                    <i class="far fa-edit"></i>
                                                                </a>
                                                                <a href="#" class="action_btn mr_10" title="Copy">
                                                                    <i class="far fa-copy"></i>
                                                                </a>
                                                                <!-- <a href="delete_contact.php?id=<?php echo $row['id']; ?>" class="action_btn" title="Delete" onclick="return confirm('Are you sure you want to delete this record?');">
                                                                    <i class="fas fa-trash"></i>
                                                                </a> -->
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

        <!-- ===== Footer ===== -->
        <div class="footer_part">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="footer_iner text-center">
                            <p>2025 © Influence - Designed by <a href="#"><i class="ti-heart"></i></a> <a href="#">Dashboard</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <?php include_once('footer.php'); ?>

</body>
</html>

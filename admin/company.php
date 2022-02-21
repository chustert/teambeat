<?php include "includes/admin_header.php"; ?>

<?php 
if (!is_admin($_SESSION['user_email'])) {
    header("Location: index.php");
}
?>

    <div id="wrapper">

    <?php include "includes/admin_navigation.php"; ?>

        <div id="page-wrapper">

            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            Welcome to admin
                            <small><?php echo $_SESSION['firstname'] ?></small>
                        </h1>

                        <?php  

                        if(isset($_GET['source'])) {
                            $source = $_GET['source'];
                        } else {
                            $source = '';
                        }

                        switch($source) {
                            case 'add_company';
                            include "includes/add_company.php";
                            break;

                            case 'edit_company';
                            include "includes/edit_company.php";
                            break;

                            default:
                            include "includes/view_all_companies.php";
                            break;

                        }

                        ?>

                    </div>
                </div>
                <!-- /.row -->

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->

<?php include "includes/admin_footer.php"; ?>

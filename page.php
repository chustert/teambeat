<?php
include "includes/db.php";
include "includes/header.php";
?>

    <!-- Navigation -->
    <?php
    include "includes/navigation.php";
    ?>

    <!-- Page Content -->
    <div class="container">

        <div class="row">

            <!-- Blog Entries Column -->
            <div class="col-md-8">

                <?php

                if(isset($_GET['pg_id'])) {
                    $the_page_id = $_GET['pg_id'];
            
                    $query = "SELECT * FROM pages WHERE page_id = $the_page_id";
                    $select_all_pages_query = mysqli_query($connection, $query);

                     while($row = mysqli_fetch_assoc($select_all_pages_query)) {
                        $page_title = $row['page_title'];
                        $page_subtitle = $row['page_subtitle'];
                        $page_image = $row['page_image'];
                        $page_content = $row['page_content'];
                        $page_user = $row['page_user'];
                        $page_status = $row['page_status'];                       
                        

                        if($page_status == 'published') {
                        ?>
                        <!-- Page Start -->
                        <h1 class="page-header"><?php echo $page_title ?>
                        <br><small><?php echo $page_subtitle ?></small></h1>
                        
                        <hr>
                        <img class="img-responsive" src="images/<?php echo $page_image; ?>" alt="">
                        <hr>
                        <p><?php echo $page_content ?></p>
                        <hr>

                     <?php } else { ?>
                        <h3>This page has not been published.</h3>
                     <?php }

                 } 
             } else {
                header("Location: index.php");
             }
             ?>

            </div>

            <!-- Blog Sidebar Widgets Column -->
            <?php
            include "includes/sidebar.php";
            ?>

        </div>
        <!-- /.row -->

        <hr>

<?php
include "includes/footer.php";
?>

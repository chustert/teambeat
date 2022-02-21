<?php
include "includes/db.php";
include "includes/header.php";
include "admin/functions.php";
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

                if(isset($_SESSION['user_email'])) {
                    
                    $user_email = $_SESSION['user_email'];
                    
                    $query = "SELECT * FROM users WHERE user_email = '{$user_email}' ";
                    $select_user_profile_query = mysqli_query($connection, $query);

                    while ($row = mysqli_fetch_array($select_user_profile_query)) {
                        $user_id = $row['user_id'];
                        $user_password = $row['user_password'];
                        $user_firstname = $row['user_firstname'];
                        $user_lastname = $row['user_lastname'];
                        $user_email = $row['user_email'];
                        $user_phone = $row['user_phone'];
                        $user_role = $row['user_role'];
                        $user_company_id = $row['user_company_id'];
                        $user_company = $row['user_company'];
                        $user_department_id = $row['user_department_id'];
                        $user_department = $row['user_department'];
                    }

                

                    if(isset($_GET['pl_id'])) {
                        $the_poll_id = $_GET['pl_id'];

                        $poll_query = "SELECT * FROM polls WHERE poll_id = $the_poll_id";
                        $select_poll_query = mysqli_query($connection, $poll_query);

                        while($row = mysqli_fetch_assoc($select_poll_query)) {
                            $poll_id = $row['poll_id'];
                            $poll_question = $row['poll_question'];
                            $poll_description = $row['poll_description'];
                            $poll_user_id = $row['poll_user_id'];
                            $poll_company = $row['poll_company'];
                            $poll_status = $row['poll_status'];
                            $poll_expires = $row['poll_expires'];
                            $poll_date = $row['poll_date'];

                            $poll_date = strtotime($poll_date);
                            $poll_date = date( "l, j F 'y", $poll_date );
                        }

                        // If the user clicked the "Vote" button...
                        if (isset($_POST['poll_answered'])) {
                            $the_poll_answer_id = $_POST['poll_answered'];

                            $query = "UPDATE poll_answers SET poll_votes = poll_votes + 1 WHERE poll_answer_id = $the_poll_answer_id";

                            $count_vote_query = mysqli_query($connection, $query);

                            if(!$count_vote_query) {
                                die('QUERY FAILED' . mysqli_error($connection));
                            }

                            // CREATE FUNCTION HERE SO USER CAN ONLY VOTE ONCE

                            echo "<p class='bg-success'>You voted id $the_poll_answer_id </p>";

                        }
                        ?>

                        <h1 class="page-header">
                        <?php echo $poll_question ?>
                        <small><?php echo $poll_description ?></small>
                        </h1>

                        <form action="" method="post">

                            <?php  
                            $poll_answer_query = "SELECT * FROM poll_answers WHERE poll_id = $the_poll_id";
                            $select_poll_answer_query = mysqli_query($connection, $poll_answer_query);

                            while ($row = mysqli_fetch_assoc($select_poll_answer_query)) {
                                $poll_answer_id = $row['poll_answer_id'];
                                $poll_answer = $row['poll_answer'];

                                ?>
                                <label>
                                    <input type="radio" name="poll_answered" value="<?php echo $poll_answer_id; ?>">
                                    <?php echo $poll_answer; ?>
                                </label>
                                <?php 
                            } ?>

                            <div>
                                <input type="submit" value="Vote">
                            </div>
                        </form>
                    
                        <?php 

                    } else {
                        header("Location: index.php");
                    }




                } elseif (isset($_COOKIE['rememberme'])) {
                   // Decrypt cookie variable value
                   $user_id = decryptCookie($_COOKIE['rememberme']);

                    // Fetch records
    
                    $query = "SELECT * FROM users WHERE user_id = {$user_id} ";
                    $select_user_query = mysqli_query($connection, $query);

                    if (!$select_user_query) {
                        die("QUERY FAILED " . mysqli_error($connection));
                    }

                    while ($row = mysqli_fetch_array($select_user_query)) {
                        $db_user_id = $row['user_id'];
                        $db_user_email = $row['user_email'];
                        $db_user_password = $row['user_password'];
                        $db_user_firstname = $row['user_firstname'];
                        $db_user_lastname = $row['user_lastname'];
                        $db_user_role = $row['user_role'];
                        $db_user_company = $row['user_company'];
                    }

                    if (session_status() === PHP_SESSION_NONE) session_start();

                    $_SESSION['user_id'] = $db_user_id;
                    $_SESSION['user_email'] = $db_user_email;
                    $_SESSION['firstname'] = $db_user_firstname;
                    $_SESSION['lastname'] = $db_user_lastname;
                    $_SESSION['user_role'] = $db_user_role;
                    $_SESSION['user_company'] = $db_user_company;

                    header("Location: poll.php?pl_id=$the_poll_id");
                } else {
                    header("Location: login.php");

                } ?>



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

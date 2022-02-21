<?php
include "includes/db.php";
include "includes/header.php";
include "admin/functions.php";
?>

    <!-- Navigation -->
    <?php
    include "includes/navigation.php";
    ?>

    <main role="main" class="flex-shrink-0">
        <div class="container">


            <div class="d-flex align-items-center">

                <div class="mx-auto login-min-width">
                                                                            
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

                        $count_num_polls_query = "SELECT * FROM polls WHERE poll_company = '$user_company' ";
                        $num_polls_query = mysqli_query($connection, $count_num_polls_query);

                        $num_polls = mysqli_num_rows($num_polls_query);

                        if ($num_polls == 0) {
                            echo "<h1 class='page-header'><img src='images/orange-line.png'> No polls entered yet</h1>";


                        // ************************************************************** //
                        // START POLLS FUNCTIONALITY AND PAGINATION

                        } else {

                            if ($num_polls == 20) {
                                $num_polls_amount = 20;
                                $highest_poll_id_correction = 19;
                            } else {
                                $num_polls_amount = 5;
                                $highest_poll_id_correction = 4;
                            }

                            if (isset($_GET['poll_page'])) {
                                $poll_page = $_GET['poll_page'];
                            } else {
                                $poll_page = "";
                            }

                            if($poll_page == "" || $poll_page == 1) {
                                $poll_page_1 = 0;
                            } else {
                                $poll_page_1 = ($poll_page * 1) - 1;
                            }


                            $query = "SELECT * FROM polls WHERE poll_company = '$user_company' ORDER BY poll_id DESC LIMIT $poll_page_1, 1";
                            $select_polls = mysqli_query($connection, $query);

                            while($row = mysqli_fetch_assoc($select_polls)) {
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
                            

                                // COUNTING AMOUNT OF VOTES FOR PARTICULAR POLL
                                $count_user_votes_query = "SELECT user_answers_poll_id FROM user_answers WHERE user_answers_poll_id = $poll_id AND user_answers_user_id = $user_id";
                                $user_vote_count = mysqli_query($connection, $count_user_votes_query);

                                $count_user_votes = mysqli_num_rows($user_vote_count);


                                // CHECK IF USER HAS ANSWERED ALL 5 POLLS
                                // 1) identify highest poll_id with query from above DESC LIMIT 1,1
                                $query_highest_poll_id = "SELECT * FROM polls WHERE poll_company = '$user_company' ORDER BY poll_id DESC LIMIT 0, 1";
                                $select_highest_poll_id = mysqli_query($connection, $query_highest_poll_id);

                                $row = mysqli_fetch_array($select_highest_poll_id);
                                $highest_poll_id = $row['poll_id'];

                                // 2) then check user votes complete between $highest_poll_id and $highest_poll_id - 5 
                                $query_check_user_votes_complete = "SELECT * FROM user_answers WHERE user_answers_user_id = $user_id AND user_answers_poll_id BETWEEN $highest_poll_id - $highest_poll_id_correction AND $highest_poll_id";
                                $select_check_user_votes_complete = mysqli_query($connection, $query_check_user_votes_complete);

                                $count_check_user_votes_complete = mysqli_num_rows($select_check_user_votes_complete);


                                // if $insert_user_answers_query num rows where user_id == $user_id say you voted already
                                if ($count_user_votes > 0 && $poll_page == "") { 
                                    header("Location: index.php?poll_page=1");

                                } elseif ($count_user_votes > 0 && $poll_page >= 1 && $poll_page <= $highest_poll_id_correction) {
                                    $poll_page = $poll_page + 1;
                                    header("Location: index.php?poll_page=" . $poll_page);

                                } elseif ($count_user_votes > 0 && $poll_page = $num_polls_amount && $count_check_user_votes_complete < $num_polls_amount) {
                                    header("Location: index.php?poll_page=1");
                
                                } elseif ($count_user_votes > 0 && $poll_page = $num_polls_amount && $count_check_user_votes_complete = $num_polls_amount) {
                                    header("Location: thank-you.php");
                
                                } else {

                                    // If the user clicked the "Vote" button...
                                    if (isset($_POST['poll_answered'])) {
                                        $the_poll_answer_id = escape($_POST['poll_answered']);

                                        

                                        $query = "UPDATE poll_answers SET poll_votes = poll_votes + 1 WHERE poll_answer_id = $the_poll_answer_id";

                                        $count_vote_query = mysqli_query($connection, $query);

                                        if(!$count_vote_query) {
                                            die('QUERY FAILED' . mysqli_error($connection));
                                        }

                                        $insert_user_answers_query = "INSERT INTO user_answers(user_answers_user_id, user_answers_user_company_id, user_answers_user_department_id, user_answers_poll_id, user_answers_poll_answer_id, user_answers_date) ";
                                        $insert_user_answers_query .= "VALUES($user_id, $user_company_id, $user_department_id, $poll_id, $the_poll_answer_id, NOW()+INTERVAL 18 HOUR) ";

                                        $create_user_answers_query = mysqli_query($connection, $insert_user_answers_query);

                                        confirmQuery($create_user_answers_query);

                                        header("Location: index.php?poll_page=1");

                                    }

                                    if($poll_status == 'published') {
                                    ?>
                                        <h4 class="day-page-header"><small class="text-muted">
                                            <p><?php echo $poll_date ?></p>
                                            <p>Hi <?php echo $user_firstname ?>, you have answered 
                                                <?php echo $count_check_user_votes_complete; 

                                                if ($count_check_user_votes_complete==1) {
                                                    echo " question";
                                                } else {
                                                    echo " questions";
                                                }
                                                ?>
                                                so far this week.
                                            </p>
                                        </small></h4>
                                        
                                        <h1 class="page-header"><?php echo $poll_question ?></h1>

                                        <div class="d-flex justify-content-center">
                                            <form action="" method="post" style="width: 100%">
                                                <div class="cc-selector">
                                                    <div class="row">

                                                    <?php  
                                                    $poll_answer_query = "SELECT * FROM poll_answers WHERE poll_id = $poll_id";
                                                    $select_poll_answer_query = mysqli_query($connection, $poll_answer_query);

                                                    while ($row = mysqli_fetch_assoc($select_poll_answer_query)) {
                                                        $poll_answer_id = $row['poll_answer_id'];
                                                        $poll_answer = $row['poll_answer'];

                                                        ?>
                                                            <div class="col-md-3 py-2">
                                                                <input id="<?php echo $poll_answer_id; ?>" type="radio" name="poll_answered" value="<?php echo $poll_answer_id; ?>">
                                                                <label for="<?php echo $poll_answer_id; ?>" class="drinkcard-cc smiley-bg">
                                                                    <span class="d-flex label-font-positioning"><?php echo $poll_answer; ?></span>
                                                                </label>
                                                            </div>
                                                        <?php 
                                                    } ?>
                                                </div>

                                                </div>
                                                <div class="d-flex justify-content-center">
                                                    <!-- <input type="submit" value="Vote"> -->
                                                    <button class="btn btn-lg btn-primary btn-block" type="submit">Vote</button>
                                                </div>
                                            </form>
                                        </div>

                                    <?php } else {
                                        echo "<h1 class='page-header'><img src='images/orange-line.png'> This poll has not been published yet. Please come back later.</h1>";
                                    }

                                }
                            } // WHile loop ends

                            ?>

                            <div class="d-flex justify-content-center pagination-wrapper">
                                <ul class="pagination <?php if($num_polls_amount==20) echo 'pagination-sm-20 pagination-small'; ?>">
                                    <?php
                                    
                                    // if ($poll_page != "" && $poll_page > 1) {
                                    //     echo "<li class='page-item'><a class='page-link' href='index.php?poll_page=" . $poll_page - 1 . "'>Previous</a></li>";
                                    // }

                                    for ($i = 1; $i <= $num_polls_amount ; $i++) {

                                        $count_user_votes_for_poll_query = "SELECT user_answers_poll_id FROM user_answers WHERE user_answers_poll_id = $highest_poll_id +1 - $i AND user_answers_user_id = $user_id";
                                        $user_votes_for_poll_count = mysqli_query($connection, $count_user_votes_for_poll_query);

                                        $count_for_poll_votes = mysqli_num_rows($user_votes_for_poll_count);

                                        if ($i == $poll_page) {
                                            echo "<li class='page-item'><a class='page-link active_link' href='index.php?poll_page=$i'>$i</a></li>";
                                        } elseif ($count_for_poll_votes > 0) {
                                            echo "<li class='page-item'><a class='page-link btn disabled' href='index.php?poll_page=$i'>$i</a></li>";
                                        } else {
                                            echo "<li class='page-item'><a class='page-link' href='index.php?poll_page=$i'>$i</a></li>";
                                        }
                                        
                                    }

                                    // if ($poll_page == "") {
                                    //     echo "<li class='page-item'><a class='page-link' href='index.php?poll_page=2'>Next</a></li>";
                                    // } else {
                                    //     if ($poll_page + 1 < 6) {
                                    //         echo "<li class='page-item'><a class='page-link' href='index.php?poll_page=" . $poll_page + 1 . "'>Next</a></li>";
                                    //     }
                                    // }
                                    
                                    ?>
                                </ul>
                            </div>
                            <!-- <p> User ID (user_id): <?php echo $user_id ?> </p>
                            <br>
                            <p> Num polls total (num_polls): <?php echo $num_polls ?> </p>
                            <p> Highest Poll ID (highest_poll_id): <?php echo $highest_poll_id ?> </p>
                            <br>
                            <p> Amount of Polls (num_polls_amount): <?php echo $num_polls_amount ?> </p>
                            <p> Highest Poll ID (highest_poll_id): <?php echo $highest_poll_id ?> </p>
                            <p> Highest Poll ID Correction (highest_poll_id_correction): <?php echo $highest_poll_id_correction ?> </p>
                            <p> Highest Poll ID - Highest Poll ID Correction: <?php echo $highest_poll_id - $highest_poll_id_correction ?> </p>
                            <br>
                            <p> Amount of Votes completed in this poll (count_check_user_votes_complete): <?php echo $count_check_user_votes_complete ?> </p>
                            <p> Amount of Votes for this poll (count_user_votes): <?php echo $count_user_votes ?> </p>
                            <br>
                            <p> Poll page (poll_page): <?php echo $poll_page ?> </p>
                            <p> Poll page_1 (poll_page_1): <?php echo $poll_page_1 ?> </p> -->


                        <?php
                        }

                        // END POLLS FUNCTIONALITY AND PAGINATION
                        // ************************************************************** //

                    
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

                        header("Location: index.php");
                    } else {
                        header("Location: login.php");

                    } ?>

                </div>
                                      
            </div>

        </div>
    </main>

<?php
include "includes/footer.php";
?>

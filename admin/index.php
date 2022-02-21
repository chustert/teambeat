 <?php include "includes/admin_header.php"; ?>

<?php 
// if (!is_admin($_SESSION['user_email'])) {
//     header("Location: ../index.php");
// }

if(isset($_SESSION['user_email'])) {
  $session_user_email = $_SESSION['user_email'];
            
  $session_query = "SELECT * FROM users WHERE user_email = '{$session_user_email}' ";
  $select_session_user_profile_query = mysqli_query($connection, $session_query);

    while ($row = mysqli_fetch_array($select_session_user_profile_query)) {
        $session_user_id = $row['user_id'];
        $session_user_password = $row['user_password'];
        $session_user_firstname = $row['user_firstname'];
        $session_user_lastname = $row['user_lastname'];
        $session_user_email = $row['user_email'];
        $session_user_phone = $row['user_phone'];
        $session_user_role = $row['user_role'];
        $session_user_company = $row['user_company'];
    }
}

if(isset($_POST['create_first_poll'])) {

    $select_20_polls_from_poll_pool_query = "SELECT * FROM poll_pool ORDER BY RAND() LIMIT 0,20";
    $select_20_polls_from_poll_pool = mysqli_query($connection, $select_20_polls_from_poll_pool_query);

    while($row = mysqli_fetch_assoc($select_20_polls_from_poll_pool)) {
        $question = $row['question'];
        $answers = escape($row['answers']);
        $answers_rating = escape($row['answers_rating']);
        $type_id = $row['type_id'];
        $subtype_id = $row['subtype_id'];


        $query = "INSERT INTO polls(poll_question, poll_description, poll_type_id, poll_subtype_id, poll_user_id, poll_company, poll_status, poll_expires, poll_date, poll_votes_count) ";
        // CREATES A TIMESTAMP US TIME + 1 DAY TO HAVE RIGHT DATE FOR NZ - IF DIFFERENT COUNTRY, THERE NEEDS TO BE ANOTHER SOLUTION
        $query .= "VALUES('$question', '', $type_id, $subtype_id, $session_user_id, '$session_user_company', 'published', '2025-11-05 14:29:36', NOW()+INTERVAL 18 HOUR, 0)";
        $copy_query = mysqli_query($connection, $query);

        if(!$copy_query) {
            die("Query failed" . mysqli_error($connection));
        }

        // Below will get the last inserted ID, this will be the poll id
        $the_poll_id = mysqli_insert_id($connection);

        // Get the answers and convert the multiline string to an array, so we can add each answer to the "poll_answers" table
        $poll_answers = explode(";", $answers);
        $poll_answers_rating = explode(";", $answers_rating);

        // This foreach takes the $poll_answers array. 
        // Both $poll_answers and $poll_answers_rating are arrays of the same length, and with the same keys. 
        // $k is the key in the $poll_answers array, and $poll_answers_rating[$k] is looking to find a corresponding entry with the same key in the $poll_answers_rating array
        foreach ($poll_answers as $k => $poll_answer) {
            $poll_answer_rating = $poll_answers_rating[$k];


            // Add answer to the "poll_answers" table
            $query = "INSERT INTO poll_answers(poll_id, poll_type_id, poll_subtype_id, poll_company, poll_answer, poll_answer_rating, poll_votes, poll_answer_date) ";
            $query .= "VALUES($the_poll_id, $type_id, $subtype_id, '$session_user_company', '$poll_answer', $poll_answer_rating, 0, NOW()+INTERVAL 18 HOUR) "; 

            $create_poll_answer_query = mysqli_query($connection, $query);

            confirmQuery($create_poll_answer_query);
        }
    }

    header("Location: polls.php");


    // $query = "INSERT INTO polls(poll_question, poll_user_id, poll_company, poll_status, poll_date, poll_votes_count) ";
    // $query .= "VALUES('How was your day?', $session_user_id, '$session_user_company', 'published', now(), 0)";
    // $copy_query = mysqli_query($connection, $query);

    // if(!$copy_query) {
    //     die("Query failed" . mysqli_error($connection));
    // }

    // // Below will get the last insert ID, this will be the poll id
    // $the_poll_id = mysqli_insert_id($connection);

    // // Get the answers and convert the multiline string to an array, so we can add each answer to the "poll_answers" table
    // $poll_answers = array("Very Bad", "Bad", "Neutral", "Good", "Very Good");
    // foreach ($poll_answers as $poll_answer) {
    //     // If the answer is empty there is no need to insert
    //     if (empty($poll_answer)) continue;
    //     // Add answer to the "poll_answers" table
    //     $query = "INSERT INTO poll_answers(poll_id, poll_company, poll_answer, poll_votes) ";
    //     $query .= "VALUES({$the_poll_id}, '{$session_user_company}', '{$poll_answer}', 0) ";

    //     $create_poll_answer_query = mysqli_query($connection, $query);

    //     confirmQuery($create_poll_answer_query);
    // }

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
                            <?php echo $session_user_company ?>'s Dashboard
                        </h1>
                    </div>
                </div>

                <?php 
                $count_num_polls_query = "SELECT * FROM polls WHERE poll_company = '$session_user_company' ";
                $num_polls_query = mysqli_query($connection, $count_num_polls_query);

                $num_polls = mysqli_num_rows($num_polls_query);


                $select_polls = mysqli_query($connection, $count_num_polls_query);  

                while($row = mysqli_fetch_assoc($select_polls)) {
                    $poll_id = $row['poll_id'];

                    // CHECK IF VOTES EXIST FOR ANY POLLS
                    $query = "SELECT * FROM poll_answers WHERE poll_company = '$session_user_company' AND poll_votes > 0 ";
                    $send_votes_query = mysqli_query($connection, $query);

                    $count_votes = mysqli_num_rows($send_votes_query);
                }


                if ($num_polls == 0) { ?>
                    <p> You haven't started any surveys yet. <br>
                    Create your first survey containing 20 questions with the button below. From then on, the system will automatically create a new poll every week.</p>
                    <p>Now, create your first survey and let your team know that they can vote.</p>
                    <form action="" method='post'>
                        <table class="table table-bordered table-hover">
                            <div class="col-xs-4" style="padding-bottom: 10px;">
                                <input type='submit' name='create_first_poll' class='btn btn-success' value='Create First Poll'>
                            </div>
                        </table>
                    </form>
                <?php } elseif ($count_votes == 0) { ?>
                    <p> Your team hasn't entered any votes yet. <br> Let your team know that they can vote!</p>

                <?php } else {
                ?>
                    <!-- /.row -->
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-fw fa-cog fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div><p class="lead">Account</p></div>
                                        </div>
                                    </div>
                                </div>
                                <a href="settings.php">
                                    <div class="panel-footer">
                                        <span class="pull-left">Change Settings</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="panel panel-red">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-list fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div><p class="lead" style="margin-bottom: 0px">Polls</p></div>
                                            <?php 
                                            $count_num_polls_query = "SELECT * FROM polls WHERE poll_company = '$session_user_company' ";
                                            $num_polls_query = mysqli_query($connection, $count_num_polls_query);
                                            $num_polls = mysqli_num_rows($num_polls_query);
                                            echo "<div class='huge'>$num_polls</div>"  
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <a href="polls.php">
                                    <div class="panel-footer">
                                        <span class="pull-left">View Polls</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="panel panel-yellow">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-user fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div><p class="lead" style="margin-bottom: 0px">Users</p></div>
                                            <?php
                                            $query = "SELECT * FROM users WHERE user_company = '$session_user_company' ";
                                            $select_all_users = mysqli_query($connection, $query);
                                            $user_count = mysqli_num_rows($select_all_users);
                                            echo "<div class='huge'>$user_count</div>"  
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <a href="users.php">
                                        <span class="pull-left">View All Users</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                        <div class="clearfix"></div>
                                    </a>
                                    <a href="users.php?source=add_user">
                                        <span class="pull-left">Add User</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                        <div class="clearfix"></div>
                                    </a>     
                                </div>
                                <div class="panel-footer">
                                    <a href="../profile.php">
                                        <span class="pull-left">View Your Profile</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                        <div class="clearfix"></div>
                                    </a>   
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="panel panel-green">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-users fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                          <div><p class="lead" style="margin-bottom: 0px">Departments</p></div>
                                          <?php
                                            $query = "SELECT * FROM departments WHERE dep_company = '$session_user_company' ";
                                            $select_all_users = mysqli_query($connection, $query);
                                            $user_count = mysqli_num_rows($select_all_users);
                                            echo "<div class='huge'>$user_count</div>"  
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <a href="departments.php">
                                    <div class="panel-footer">
                                        <span class="pull-left">Go to Departments</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- /.row -->

<!-- ------------------------------------------------------------------------------------------------------------------- -->

                    <div class="row">
                        <div class="col-lg-12">
                            <h2 class="page-header">
                                Scores
                            </h2>
                        </div>
                    </div>
                    <?php 
                    /// READ OUT DATE OF FIRST POLL OF CERTAIN COMPANY
                    $find_least_date_query = "SELECT MIN(poll_date) AS least_poll_date FROM polls WHERE poll_company = '$session_user_company' "; 
                    $send_least_date_query = mysqli_query($connection, $find_least_date_query);

                    $row = mysqli_fetch_array($send_least_date_query);
                    $least_poll_date = isset($row['least_poll_date']) ? $row['least_poll_date'] : 0;

                    /// Days from first poll date until today
                    $days_least_poll_date_to_today = round((time() - strtotime($least_poll_date)) / (60 * 60 * 24));
                    ?>

                    <?php 
                    $chosen_period = $days_least_poll_date_to_today;
                    ?>

                    <?php  
                    if (!isset($_POST['apply_period']) && empty($_POST['choose_period'])) {
                        $chosen_period = $days_least_poll_date_to_today;
                    } elseif (isset($_POST['apply_period']) && !empty($_POST['choose_period'])) {
                        $chosen_period = escape($_POST['choose_period']);
                    }
                    ?>

                    <p><?php echo $least_poll_date ?></p>

                    <?php
                    /// SELECT MIN( price ) FROM table WHERE price > ( SELECT MIN( price ) FROM table ) 

                    // $find_date_query_2 = "SELECT MIN(poll_date) AS second_least FROM polls WHERE poll_date NOT IN (SELECT DISTINCT TOP 1 poll_date FROM polls ORDER BY poll_date) AND poll_company = '$session_user_company' ";
                    // $send_date_query_2 = mysqli_query($connection, $find_date_query_2);

                    // $row = mysqli_fetch_array($send_date_query_2);
                    // $least_poll_date_2 = isset($row['second_least']) ? $row['second_least'] : 0;
                    ?>

                    <!-- <p><?php echo $least_poll_date_2 ?></p> -->


                    <form action="" method='post'>

                        <div class="row" style="margin-bottom: 1rem;">
                            <div class="col-xs-12">
                                <h4>Select period:</h4> 
                                <div id="choosePeriod" class="col-xs-4">
                                    <select name="choose_period" class="form-control">
                                        <option value='<?php echo $chosen_period ?>'>
                                            <?php 
                                            if ($chosen_period==$days_least_poll_date_to_today) {
                                                echo 'All time';
                                            } elseif ($chosen_period==30) {
                                                echo 'Last 30 days';
                                            } elseif ($chosen_period==7) {
                                                echo 'Current poll';
                                            }
                                            ?>
                                        </option>
                                        <?php if ($chosen_period==$days_least_poll_date_to_today): ?>
                                            <option value='7'>Current poll</option>
                                            <option value='30'>Last 30 days</option>
                                        <?php elseif ($chosen_period==30): ?>
                                            <option value='7'>Current poll</option>
                                            <option value='<?php echo $days_least_poll_date_to_today ?>'>All time</option>
                                        <?php elseif ($chosen_period==7): ?>
                                            <option value='30'>Last 30 days</option>
                                            <option value='<?php echo $days_least_poll_date_to_today ?>'>All time</option>
                                        <?php endif ?>
                                    </select>
                                </div>
                                <div class="col-xs-4">
                                    <input type='submit' name='apply_period' class='btn btn-success' value='Apply'>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 3rem;">
                            
                        </div>
                    </form>

                    <?php   

                    /////////////////////////////////
                    // CALCULATE OVERALL SCORE     //
                    /////////////////////////////////

                    for($i = 1; $i <= 4; $i++) {
                        $score_query_[$i] = "SELECT SUM(poll_votes) AS votes FROM poll_answers WHERE poll_company = '$session_user_company' AND poll_answer_rating = $i AND poll_answer_date >= DATE(NOW()) - INTERVAL $chosen_period DAY ";
                        $send_score_query_[$i] = mysqli_query($connection, $score_query_[$i]);

                        $row = mysqli_fetch_array($send_score_query_[$i]);
                        $poll_vote_[$i] = isset($row['votes']) ? $row['votes'] : 0;

                        if ($i == 1) {
                            $score_verybad = $poll_vote_[$i] *0;
                        } elseif ($i == 2) {
                            $score_bad = $poll_vote_[$i] *33.3333;
                        } elseif ($i == 3) {
                            $score_good = $poll_vote_[$i] *66.6666;
                        } elseif ($i == 4) {
                            $score_verygood = $poll_vote_[$i] *100;
                        }
                    }

                    global $score_verybad;
                    global $score_bad;
                    global $score_good;
                    global $score_verygood;


                    /// COUNT TOTAL VOTES
                    $total_votes_query = "SELECT SUM(poll_votes) AS total_votes_count FROM poll_answers WHERE poll_company = '$session_user_company' AND poll_answer_date >= DATE(NOW()) - INTERVAL $chosen_period DAY ";
                    $send_total_votes_query = mysqli_query($connection, $total_votes_query);

                    $row = mysqli_fetch_assoc($send_total_votes_query);
                    $total_votes_0 = isset($row['total_votes_count']) ? $row['total_votes_count'] : 0;


                    if ($total_votes_0 > 0) {
                        $score_overall = ($score_verybad + $score_bad + $score_good + $score_verygood)/$total_votes_0;
                    }
                    
                    global $score_overall;

                    $score_0 = $score_overall;

                    //////////////////////////

                    /////////////////////////////////
                    //    CALCULATE ALL SCORES     //
                    /////////////////////////////////

                    //////////////////////////
                    // CALCULATE AREA SCORE //
                    //                      //
                    // Health       = 1     //
                    // Social       = 2     //
                    // Work         = 3     //
                    // Finances     = 4     //
                    // Environment  = 5     //
                    //                      //
                    //////////////////////////

                    for ($t = 1; $t <= 5 ; $t++) { 

                        for($i = 1; $i <= 4; $i++) {
                            $score_query_[$i] = "SELECT SUM(poll_votes) AS votes FROM poll_answers WHERE poll_company = '$session_user_company' AND poll_type_id = $t AND poll_answer_rating = $i ";
                            $send_score_query_[$i] = mysqli_query($connection, $score_query_[$i]);

                            $row = mysqli_fetch_array($send_score_query_[$i]);
                            $poll_vote_[$i] = isset($row['votes']) ? $row['votes'] : 0;

                            if ($i == 1) {
                                $score_verybad = $poll_vote_[$i] *0;
                            } elseif ($i == 2) {
                                $score_bad = $poll_vote_[$i] *33.3333;
                            } elseif ($i == 3) {
                                $score_good = $poll_vote_[$i] *66.6666;
                            } elseif ($i == 4) {
                                $score_verygood = $poll_vote_[$i] *100;
                            }
                        }

                        global $score_verybad;
                        global $score_bad;
                        global $score_good;
                        global $score_verygood;

                        /// COUNT TOTAL VOTES
                        $total_votes_query = "SELECT SUM(poll_votes) AS total_votes_count FROM poll_answers WHERE poll_company = '$session_user_company' AND poll_type_id = $t ";
                        $send_total_votes_query = mysqli_query($connection, $total_votes_query);

                        $row = mysqli_fetch_assoc($send_total_votes_query);
                        $total_votes_[$t] = isset($row['total_votes_count']) ? $row['total_votes_count'] : 0;


                        if ($total_votes_[$t] == 0) {
                            $score_overall = 0;
                        } elseif ($total_votes_[$t] > 0) {
                            $score_overall = ($score_verybad + $score_bad + $score_good + $score_verygood)/$total_votes_[$t];
                        }
                        
                        global $score_overall;

                        if ($t == 1) {
                            $score_1 = $score_overall;
                        } elseif ($t == 2) {
                            $score_2 = $score_overall;
                        } elseif ($t == 3) {
                            $score_3 = $score_overall;
                        } elseif ($t == 4) {
                            $score_4 = $score_overall;
                        } elseif ($t == 5) {
                            $score_5 = $score_overall;
                        }

                    }
                    ?>

<!-- ------------------------------------------------------------------------------------------------------------------- -->

                    <div class="row">
                        <div class="col-md-5">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-8">
                                            <div class='huge'>Overall</div>
                                        </div>
                                        <div class="col-xs-4 text-right">
                                            <div class='huge'>
                                                <?php 
                                                if ($total_votes_0 == 0) {
                                                    echo 0;
                                                } else {
                                                    echo round($score_0,2); 
                                                } 
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <a href="posts.php">
                                    <div class="panel-footer">
                                        <span class="pull-left">View Details</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                        <div class="clearfix"></div>
                                    </div>
                                </a> -->
                            </div>
                            <div class="panel panel-red">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-8">
                                            <div class='huge'>Health</div>
                                        </div>
                                        <div class="col-xs-4 text-right">
                                            <div class='huge'>
                                                <?php echo round($score_1, 2); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-yellow">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-8">
                                            <div class='huge'>Social</div>
                                        </div>
                                        <div class="col-xs-4 text-right">
                                            <div class='huge'>
                                                <?php echo round($score_2, 2); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-8">
                                            <div class='huge'>Work</div>
                                        </div>
                                        <div class="col-xs-4 text-right">
                                            <div class='huge'>
                                                <?php echo round($score_3, 2); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-finances">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-8">
                                            <div class='huge'>Finances</div>
                                        </div>
                                        <div class="col-xs-4 text-right">
                                            <div class='huge'>
                                                <?php echo round($score_4, 2); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-green">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-8">
                                            <div class='huge'>Environment</div>
                                        </div>
                                        <div class="col-xs-4 text-right">
                                            <div class='huge'>
                                                <?php echo round($score_5, 2); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">

                            <div class="chart-container">
                                <canvas id="myChart"></canvas>
                            </div>

                            <script>
                                let myChart = document.getElementById('myChart').getContext('2d');

                                // Global Options
                                Chart.defaults.font.family = 'Lato';
                                Chart.defaults.font.size = 16;
                                Chart.defaults.font.color = '#777';


                                let massPopChart = new Chart(myChart, {
                                    type: 'radar', // bar, horizontalBar, pie, line, doughnut, radar, polarArea
                                    data:{
                                        labels:['Health', 'Social', 'Work', 'Finances', 'Environment'],
                                        datasets:[
                                            {
                                                label:'Team Score',
                                                data:[
                                                    <?php echo round($score_1); ?>,
                                                    <?php echo round($score_2); ?>,
                                                    <?php echo round($score_3); ?>,
                                                    <?php echo round($score_4); ?>,
                                                    <?php echo round($score_5); ?>
                                                ],
                                                backgroundColor:'rgba(255, 159, 64, 0.6)',
                                                borderWidth: 1,
                                                borderColor: '#777',
                                                hoverBorderWidth: 3,
                                                hoverBorderColor: '#000'
                                            }
                                            // },
                                            // {
                                            //     label:'Your Score',
                                            //     data:[
                                            //         22,
                                            //         100,
                                            //         33.36,
                                            //         66.67,
                                            //         75
                                            //     ],
                                            //     backgroundColor:'rgba(217, 83, 79, 0.6)',
                                            //     borderWidth: 1,
                                            //     borderColor: '#777',
                                            //     hoverBorderWidth: 3,
                                            //     hoverBorderColor: '#000'
                                            // }
                                        ]
                                    },
                                    options:{
                                        plugins: {
                                            title:{
                                                display: false,
                                                text:'',
                                                fontSize: 25
                                            },
                                            legend:{
                                                display: false,
                                                position:'bottom',
                                                labels:{
                                                    fontColor: '#000'
                                                }
                                            },
                                            tooltip:{
                                                enabled: true
                                            }
                                        },
                                        scales: {
                                            r: {
                                                beginAtZero: true,
                                                max: 100,
                                                maintainAspectRatio: false
                                            }
                                        },

                                    }
                                });
                            </script>
                        </div>
                    </div>
                    <!-- /.row -->
                <?php } ?>
        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->

<?php include "includes/admin_footer.php"; ?>

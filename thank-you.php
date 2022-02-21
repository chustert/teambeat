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
                                                                        
                <?php if(isset($_SESSION['user_email'])): ?>
                    <?php
                    $user_email = $_SESSION['user_email'];
                    
                    $query = "SELECT * FROM users WHERE user_email = '{$user_email}' ";
                    $select_user_profile_query = mysqli_query($connection, $query);

                    while ($row = mysqli_fetch_array($select_user_profile_query)) {
                        $user_id = $row['user_id'];
                        $user_password = $row['user_password'];
                        $user_firstname = $row['user_firstname'];
                        $user_lastname = $row['user_lastname'];
                        $user_email = $row['user_email'];
                        $user_company = $row['user_company'];
                        $user_role = $row['user_role'];
                    }

                    // // COUNTING AMOUNT OF COMMENTS FOR EACH POST
                    $count_user_votes_query = "SELECT user_answers_poll_id FROM user_answers WHERE user_answers_user_id = $user_id";
                    $user_vote_count = mysqli_query($connection, $count_user_votes_query);

                    $count_user_votes = mysqli_num_rows($user_vote_count);

                    // if $insert_user_answers_query num rows where user_id == $user_id say you voted already
                    if ($count_user_votes > 0) { ?>
                        <h1>Thank you for your anonymous feedback, <?php echo $user_firstname ?></h1>


<!-- ------------------------------------------------------------------------------------------------------------------- -->

<?php 
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

/////////////////////////////////
// CALCULATE OVERALL SCORE     //
/////////////////////////////////

for($i = 1; $i <= 4; $i++) {
    $score_query_[$i] = "SELECT SUM(poll_votes) AS votes FROM poll_answers WHERE poll_company = '$user_company' AND poll_answer_rating = $i ";
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
$total_votes_query = "SELECT SUM(poll_votes) AS total_votes_count FROM poll_answers WHERE poll_company = '$user_company' ";
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

for ($t = 1; $t <= 5 ; $t++) { 

    for($i = 1; $i <= 4; $i++) {
        $score_query_[$i] = "SELECT SUM(poll_votes) AS votes FROM poll_answers WHERE poll_company = '$user_company' AND poll_type_id = $t AND poll_answer_rating = $i ";
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
    $total_votes_query = "SELECT SUM(poll_votes) AS total_votes_count FROM poll_answers WHERE poll_company = '$user_company' AND poll_type_id = $t ";
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
//////////////////////////

?>

<!-- ------------------------------------------------------------------------------------------------------------------- -->

<div class="row align-items-md-stretch">
    <div class="col-md-6 py-3">
        <div class="h-100 p-5 bg-light rounded-3">
            <h3>Your wellbeing is your most valuable asset</h3>
            <p>If you think you need immediate support, contact NZ Lifeline or Healthline.</p>
            <h5 style="margin-top: 20px;">NZ Lifeline</h5>
            <p>0800 543 354 (0800 LIFELINE) or free text 4357 (HELP)</p>
            <h5 style="margin-top: 20px;">Healthline</h5>
            <p>0800 611 116</p>
        </div>
    </div>
    <div class="col-md-6 py-3">
        <div class="h-100 p-5 bg-light rounded-3">

            <?php 
            $select_polls_from_open_polls_pool_query = "SELECT * FROM open_polls_pool ORDER BY RAND() LIMIT 0,1";
            $select_polls_from_open_polls_pool = mysqli_query($connection, $select_polls_from_open_polls_pool_query);

            while($row = mysqli_fetch_assoc($select_polls_from_open_polls_pool)) {
                $question = $row['question'];
                $type_id = $row['type_id'];
                $subtype_id = $row['subtype_id'];

                // CONVERT poll_type_id to poll_type --> from ID to NAME
                $select_type_query = "SELECT * FROM poll_types WHERE id = $type_id ";
                $select_type = mysqli_query($connection, $select_type_query);

                $row = mysqli_fetch_assoc($select_type);
                $poll_type = $row['type'];
                //

                // CONVERT poll_subtype_id to poll_subtype --> from ID to NAME
                $select_subtype_query = "SELECT * FROM poll_subtypes WHERE id = $subtype_id ";
                $select_subtype = mysqli_query($connection, $select_subtype_query);

                $row = mysqli_fetch_assoc($select_subtype);
                $poll_subtype = $row['subtype'];
                //
            }
            ?>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response'])) {

                // Build POST request:
                $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
                $recaptcha_secret = '6Lc0vdYaAAAAABwh2xT7MZayh3fdzYlg4Go33TBj';
                $recaptcha_response = $_POST['recaptcha_response'];

                // Make and decode POST request:
                $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
                $recaptcha = json_decode($recaptcha);

                // Take action based on the score returned:
                if ($recaptcha->score >= 0.5) {
                    if ($_POST['body']!="") {
                        $to         = "murray@teambeat.co.nz";
                        $headers    = 'You received an answer from one of your employees:' . "\r\n" .
                                    'Question: ' . $question . "\r\n" .
                                    'Type: ' . $poll_type . "\r\n" .
                                    'Subtype: ' . $poll_subtype;
                        $subject    = "TeamBeat: $question";
                        $body       = 'Answer: ' . trim($_POST['body']);

                        mail($to, $subject, $body, $headers);
                        echo "<p class='alert alert-success'>Thanks for your message.</p>";
                    } else {
                        echo "<p class='alert alert-danger'>Please fill out the message field.</p>";
                    }
                    
                } else {
                    echo "<p class='alert alert-danger'>Something went wrong.</p>";
                }
            }
            ?>

            <h3><?php echo $question ?></h3>
            <p>Your answer is sent anonymously.</p>
            <form role="form" action="" method="post" autocomplete="off">
                <div class="mb-3">
                    <label for="message" class="sr-only">Message</label>
                    <textarea name="body" class="form-control" id="body" placeholder="Message" rows="4"></textarea>
                </div>
                <button type="submit" name="submit" class="btn btn-primary CustomBtn">Submit</button>
                <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
            </form>
        </div>
    </div>
</div>

<div class="row align-items-md-stretch">
    <div class="col-md-6 py-3">
        <div class="h-100 p-5 rounded-3" style="background-color: rgba(255, 159, 64, 0.6)">
            <h4 style="line-height: 1.4; margin-bottom: 20px">Your Team's Overall Beat: <br><?php if ($total_votes_0 == 0) { echo 0; } else { echo round($score_0); } ?> / 100</h4>
            <h5>Health: <?php echo round($score_1); ?></h6>
            <h5>Social: <?php echo round($score_2); ?></h6>
            <h5>Work: <?php echo round($score_3); ?></h6>
            <h5>Finances: <?php echo round($score_4); ?></h6>
            <h5>Environment: <?php echo round($score_5); ?></h6>

        </div>
    </div>
    <div class="col-md-6 py-3">
        <div class="h-100 border rounded-3 vertical-center-chart">
            <div class="container chart-container">
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
</div>
                        
                    <?php } else {

                        header("Location: index.php");

                    } ?>
                    

                <?php else: ?>
                    <?php header("Location: login.php"); ?>

                <?php endif; ?>

            </div>
                                  
        </div>

    </div>
</main>












<?php
include "includes/footer.php";
?>

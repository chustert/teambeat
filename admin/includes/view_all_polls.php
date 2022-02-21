<?php
 
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
        $session_user_company_id = $row['user_company_id'];
        $session_user_company = $row['user_company'];
        $session_user_department_id = $row['user_department_id'];
        $session_user_department = $row['user_department'];
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
}
?>

<?php  
$count_num_polls_query = "SELECT * FROM polls WHERE poll_company = '$session_user_company' ";
$num_polls_query = mysqli_query($connection, $count_num_polls_query);

$num_polls = mysqli_num_rows($num_polls_query);

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
<?php } else { ?>

<!-- ------------------------------------------------------------------------------------------------------------------- -->

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <!-- <th>Description</th> -->
                <!-- <th>Author</th> -->
                <th>Area</th>
                <!-- <th>Status</th> -->
                <th>Date</th>
                <th>Votes</th>
                <!-- <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th> -->
                <th>Score</th>
                <!-- <th>Publish</th>
                <th>Draft</th> -->
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
        <?php

        $query = "SELECT * FROM polls WHERE poll_company = '$session_user_company' ORDER BY poll_id DESC";
        $select_polls = mysqli_query($connection, $query);  

        while($row = mysqli_fetch_assoc($select_polls)) {
            $poll_id = $row['poll_id'];
            $poll_question = $row['poll_question'];
            $poll_description = $row['poll_description'];
            $poll_type_id = $row['poll_type_id'];
            $poll_user_id = $row['poll_user_id'];
            $poll_company = $row['poll_company'];
            $poll_status = $row['poll_status'];
            $poll_expires = $row['poll_expires'];
            $poll_date = $row['poll_date'];
            $poll_votes_count = $row['poll_votes_count'];

            $poll_date = strtotime($poll_date);
            $poll_date = date( 'd/m/y', $poll_date );

            // CONVERT poll_type_id to poll_type --> from ID to NAME
            $select_poll_type_query = "SELECT * FROM poll_types WHERE id = $poll_type_id";
            $select_poll_type = mysqli_query($connection, $select_poll_type_query);

            $row = mysqli_fetch_assoc($select_poll_type);
            $poll_type = $row['type'];
            //

            // COUNTING AMOUNT OF USERS
            $query = "SELECT * FROM users WHERE user_company = '$session_user_company' ";
            $send_users_count_query = mysqli_query($connection, $query);

            $count_users = mysqli_num_rows($send_users_count_query);

            // COUNTING AMOUNT OF VOTES FOR EACH POLL
            $query = "SELECT * FROM user_answers WHERE user_answers_poll_id = $poll_id";
            $send_votes_query = mysqli_query($connection, $query);

            $count_votes = mysqli_num_rows($send_votes_query);

            //////////////////////////
            // CALCULATE POLL SCORE //
            //////////////////////////
            
            /// VERY BAD
            $score_verybad_query = "SELECT poll_votes FROM poll_answers WHERE poll_id = $poll_id AND poll_answer_rating = 1 ";
            $send_score_verybad_query = mysqli_query($connection, $score_verybad_query);

            $row = mysqli_fetch_array($send_score_verybad_query);
            $poll_vote_verybad = isset($row['poll_votes']) ? $row['poll_votes'] : 0;

            /// BAD
            $score_bad_query = "SELECT poll_votes FROM poll_answers WHERE poll_id = $poll_id AND poll_answer_rating = 2 ";
            $send_score_bad_query = mysqli_query($connection, $score_bad_query);

            $row = mysqli_fetch_array($send_score_bad_query);
            $poll_vote_bad = isset($row['poll_votes']) ? $row['poll_votes'] : 0;

            /// GOOD
            $score_good_query = "SELECT poll_votes FROM poll_answers WHERE poll_id = $poll_id AND poll_answer_rating = 3 ";
            $send_score_good_query = mysqli_query($connection, $score_good_query);

            $row = mysqli_fetch_array($send_score_good_query);
            $poll_vote_good = isset($row['poll_votes']) ? $row['poll_votes'] : 0;

            /// VERY GOOD
            $score_verygood_query = "SELECT poll_votes FROM poll_answers WHERE poll_id = $poll_id AND poll_answer_rating = 4 ";
            $send_score_verygood_query = mysqli_query($connection, $score_verygood_query);

            $row = mysqli_fetch_array($send_score_verygood_query);
            $poll_vote_verygood = isset($row['poll_votes']) ? $row['poll_votes'] : 0;


            $score_verybad = $poll_vote_verybad *0;
            $score_bad = $poll_vote_bad *33.3333;
            $score_good = $poll_vote_good *66.6666;
            $score_verygood = $poll_vote_verygood *100;

            if ($count_votes > 0) {
                $score_overall = ($score_verybad + $score_bad + $score_good + $score_verygood)/$count_votes;
            }
            

            global $score_overall;

            $bg_status = "";

            if ($count_votes == 0) {
                $bg_status = "";
            } elseif ($score_overall <= 55) {
                $bg_status = "danger";
            } elseif ($score_overall > 55 && $score_overall < 75) {
                $bg_status = "warning";
            } elseif ($score_overall >= 75) {
                $bg_status = "success";
            }


            echo "<tr class='$bg_status'>";
                
                echo "<td>{$poll_id}</td>";
                echo "<td>{$poll_question}
                        <ul>";
                        $select_poll_answers_query = "SELECT * FROM poll_answers WHERE poll_id = '$poll_id'";
                        $select_poll_answers = mysqli_query($connection, $select_poll_answers_query);  

                        while($row = mysqli_fetch_assoc($select_poll_answers)) {
                            $poll_answer = $row['poll_answer'];
                            $poll_votes = $row['poll_votes'];

                            echo "<li><small>({$poll_votes}) {$poll_answer}</small></li>";
                        }"
                        </ul>
                    </td>";
                // echo "<td>{$poll_description}</td>";

                // if (!empty($poll_user_id)) {
                //     echo "<td>{$poll_user_id}</td>";
                // }

                echo "<td>{$poll_type}</td>";

                // echo "<td>{$poll_status}</td>";
                echo "<td>{$poll_date}</td>";

                echo "<td>{$count_votes} &#47; {$count_users}</td>";

                // echo "<td>$poll_vote_verybad</td>";
                // echo "<td>$poll_vote_bad</td>";
                // echo "<td>$poll_vote_good</td>";
                // echo "<td>$poll_vote_verygood</td>";

                echo "<td>";
                    if ($count_votes == 0) {
                        echo 0;
                    } else {
                        echo round($score_overall,2); 
                    }
                echo "</td>";


                //////////////////////////
                //////////////////////////

                
                // echo "<td><a class='btn btn-primary' href='../poll.php?pl_id={$poll_id}'>View Poll</a></td>";
                // echo "<td><a class='btn btn-info' href='polls.php?source=edit_poll&pl_id={$poll_id}'>Edit</a></td>";
                 
                // echo "<td><a href='polls.php?change_to_publish={$poll_id}'>Publish</a></td>";
                // echo "<td><a href='polls.php?change_to_draft={$poll_id}'>Draft</a></td>";


                ?>
                <!-- DELETE VIA POST BECAUSE MORE SECURE --> 
                <form method="post">
                    <input type="hidden" name="poll_id" value="<?php echo $poll_id ?>">
                    <?php  
                    echo '<td><input class="btn btn-danger btn-sm" type="submit" name="delete" value="Delete"></td>'
                    ?>
                </form>

                <?php

            echo "</tr>";
        }
        ?>

        </tbody>
    </table>

<!-- ------------------------------------------------------------------------------------------------------------------- -->

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">
            Polls by Department
        </h3>
    </div>
</div>
<form action="" method='post'>
<table class="table table-bordered table-hover">

    <div id="chooseDepartmentContainer" class="col-xs-4">
        <select name="choose_department" class="form-control">
            <option value="">Select</option>
            <?php

            $query = "SELECT * FROM departments WHERE dep_company = '$session_user_company'";
            $select_department = mysqli_query($connection, $query); 

            confirmQuery($select_department);

            while($row = mysqli_fetch_assoc($select_department)) {
                $dep_id = $row['dep_id'];
                $dep_title = $row['dep_title'];  

                echo "<option value='{$dep_id}'>{$dep_title}</option>";
            }
            ?>
        </select>
    </div>
    <div class="col-xs-4">
        <input type='submit' name='apply_department' class='btn btn-success' value='Apply'>
    </div>

    <?php  

    if (isset($_POST['apply_department']) && !empty($_POST['choose_department'])) {
        $chosen_department_id = escape($_POST['choose_department']);

        $query = "SELECT dep_title FROM departments WHERE dep_id = $chosen_department_id ";
        $select_department = mysqli_query($connection, $query); 

        confirmQuery($select_department);

        $row = mysqli_fetch_array($select_department);
        $chosen_dep_title = $row['dep_title'];

    ?>


        <div class="row">
            <div class="col-xs-12">
                <h4>Selected department: <?php echo "$chosen_dep_title"; ?></h4>
            </div>
        </div>

        <thead>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Area</th>
                <!-- <th>Author</th> -->
                <!-- <th>Status</th> -->
                <th>Date</th>
                <th>Votes</th>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
        <?php

        $query = "SELECT * FROM polls WHERE poll_company = '$session_user_company' ORDER BY poll_id DESC";
        $select_polls = mysqli_query($connection, $query);  

        while($row = mysqli_fetch_assoc($select_polls)) {
            $poll_id = $row['poll_id'];
            $poll_question = $row['poll_question'];
            $poll_description = $row['poll_description'];
            $poll_type_id = $row['poll_type_id'];
            $poll_user_id = $row['poll_user_id'];
            $poll_company = $row['poll_company'];
            $poll_status = $row['poll_status'];
            $poll_expires = $row['poll_expires'];
            $poll_date = $row['poll_date'];
            $poll_votes_count = $row['poll_votes_count'];

            $poll_date = strtotime($poll_date);
            $poll_date = date( 'd/m/y', $poll_date );

            // CONVERT poll_type_id to poll_type --> from ID to NAME
            $select_poll_type_query = "SELECT * FROM poll_types WHERE id = $poll_type_id";
            $select_poll_type = mysqli_query($connection, $select_poll_type_query);

            $row = mysqli_fetch_assoc($select_poll_type);
            $poll_type = $row['type'];
            //




            echo "<tr>";
                echo "<td>{$poll_id}</td>";
                echo "<td>{$poll_question}</td>";
                // echo "<td>{$poll_description}</td>";

                // if (!empty($poll_user_id)) {
                //     echo "<td>{$poll_user_id}</td>";
                // }

                // echo "<td>{$poll_status}</td>";
                echo "<td>{$poll_type}</td>";
                echo "<td>{$poll_date}</td>";

                // COUNTING AMOUNT OF USERS
                $query = "SELECT * FROM users WHERE user_company = '$session_user_company' AND user_department_id = $chosen_department_id ";
                $send_users_count_query = mysqli_query($connection, $query);

                $count_users = mysqli_num_rows($send_users_count_query);

                // COUNTING AMOUNT OF VOTES FOR EACH POLL
                $query = "SELECT * FROM user_answers WHERE user_answers_poll_id = $poll_id AND user_answers_user_department_id = $chosen_department_id ";
                $send_votes_query = mysqli_query($connection, $query);

                $count_votes = mysqli_num_rows($send_votes_query);

                echo "<td>{$count_votes} &#47; {$count_users}</td>";


                //////////////////////////
                // CALCULATE POLL SCORE //
                //////////////////////////
                
                /// VERY BAD
                $poll_answer_id_verybad_query = "SELECT poll_answer_id FROM poll_answers WHERE poll_id = $poll_id AND poll_answer_rating = 1 ";
                $send_poll_answer_id_verybad_query = mysqli_query($connection, $poll_answer_id_verybad_query);

                $row = mysqli_fetch_array($send_poll_answer_id_verybad_query);
                $answer_id_verybad = isset($row['poll_answer_id']) ? $row['poll_answer_id'] : 0;

                $verybad_votes_query = "SELECT * FROM user_answers WHERE user_answers_poll_answer_id = $answer_id_verybad AND user_answers_user_department_id = $chosen_department_id ";
                $count_verybad_votes_query = mysqli_query($connection, $verybad_votes_query);

                $count_verybad_votes = mysqli_num_rows($count_verybad_votes_query);

                /// BAD
                $poll_answer_id_bad_query = "SELECT poll_answer_id FROM poll_answers WHERE poll_id = $poll_id AND poll_answer_rating = 2 ";
                $send_poll_answer_id_bad_query = mysqli_query($connection, $poll_answer_id_bad_query);

                $row = mysqli_fetch_array($send_poll_answer_id_bad_query);
                $answer_id_bad = isset($row['poll_answer_id']) ? $row['poll_answer_id'] : 0;

                $bad_votes_query = "SELECT * FROM user_answers WHERE user_answers_poll_answer_id = $answer_id_bad AND user_answers_user_department_id = $chosen_department_id ";
                $count_bad_votes_query = mysqli_query($connection, $bad_votes_query);

                $count_bad_votes = mysqli_num_rows($count_bad_votes_query);

                /// GOOD
                $poll_answer_id_good_query = "SELECT poll_answer_id FROM poll_answers WHERE poll_id = $poll_id AND poll_answer_rating = 3 ";
                $send_poll_answer_id_good_query = mysqli_query($connection, $poll_answer_id_good_query);

                $row = mysqli_fetch_array($send_poll_answer_id_good_query);
                $answer_id_good = isset($row['poll_answer_id']) ? $row['poll_answer_id'] : 0;

                $good_votes_query = "SELECT * FROM user_answers WHERE user_answers_poll_answer_id = $answer_id_good AND user_answers_user_department_id = $chosen_department_id ";
                $count_good_votes_query = mysqli_query($connection, $good_votes_query);

                $count_good_votes = mysqli_num_rows($count_good_votes_query);

                /// VERY GOOD
                $poll_answer_id_verygood_query = "SELECT poll_answer_id FROM poll_answers WHERE poll_id = $poll_id AND poll_answer_rating = 4 ";
                $send_poll_answer_id_verygood_query = mysqli_query($connection, $poll_answer_id_verygood_query);

                $row = mysqli_fetch_array($send_poll_answer_id_verygood_query);
                $answer_id_verygood = isset($row['poll_answer_id']) ? $row['poll_answer_id'] : 0;

                $verygood_votes_query = "SELECT * FROM user_answers WHERE user_answers_poll_answer_id = $answer_id_verygood AND user_answers_user_department_id = $chosen_department_id ";
                $count_verygood_votes_query = mysqli_query($connection, $verygood_votes_query);

                $count_verygood_votes = mysqli_num_rows($count_verygood_votes_query);


                $score_verybad = $count_verybad_votes *0;
                $score_bad = $count_bad_votes *33.3333;
                $score_good = $count_good_votes *66.6666;
                $score_verygood = $count_verygood_votes *100;

                if ($count_votes > 0) {
                    $score_overall = ($score_verybad + $score_bad + $score_good + $score_verygood)/$count_votes;
                }
                

                global $score_overall;

                echo "<td>$count_verybad_votes</td>";
                echo "<td>$count_bad_votes</td>";
                echo "<td>$count_good_votes</td>";
                echo "<td>$count_verygood_votes</td>";

                echo "<td>";
                    if ($count_votes == 0) {
                        echo 0;
                    } else {
                        echo round($score_overall,2); 
                    }
                echo "</td>";

            echo "</tr>";
        }
        ?>

        </tbody>
    <?php } ?>
</table>
</form>

<?php } ?>

<!-- ------------------------------------------------------------------------------------------------------------------- -->

<?php
if(isset($_GET['change_to_publish'])) {
    $the_poll_id = $_GET['change_to_publish'];
    $query = "UPDATE polls SET poll_status = 'published' WHERE poll_id = $the_poll_id";
    $change_to_publish_query = mysqli_query($connection, $query);
    header("Location: polls.php");
}

if(isset($_GET['change_to_draft'])) {
    $the_poll_id = $_GET['change_to_draft'];
    $query = "UPDATE polls SET poll_status = 'draft' WHERE poll_id = $the_poll_id";
    $change_to_draft_query = mysqli_query($connection, $query);
    header("Location: polls.php");
}

if(isset($_POST['delete'])) {

    if (isset($_SESSION['user_role'])) {
        if ($_SESSION['user_role'] == 'admin') {
            $the_poll_id = $_POST['poll_id'];
            $query = "DELETE FROM polls WHERE poll_id = {$the_poll_id} ";
            $delete_query = mysqli_query($connection, $query);
            $query = "DELETE FROM poll_answers WHERE poll_id = {$the_poll_id} ";
            $delete_query = mysqli_query($connection, $query);
            $query = "DELETE FROM user_answers WHERE user_answers_poll_id = {$the_poll_id} ";
            $delete_query = mysqli_query($connection, $query);
            header("Location: polls.php");
        }
    }
}
?>
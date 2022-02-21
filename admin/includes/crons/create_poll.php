<?php include "../../../includes/db.php"; ?>
<?php include "../../functions.php"; ?>

<?php 


$query = "SELECT * FROM companies ";
$select_companies = mysqli_query($connection, $query);  

while($row = mysqli_fetch_assoc($select_companies)) {
    $company_id = $row['company_id'];
    $company_name = $row['company_name'];
    $company_user_id = $row['company_user_id'];



	$count_num_polls_query = "SELECT * FROM polls WHERE poll_company = '$company_name' ";
	$num_polls_query = mysqli_query($connection, $count_num_polls_query);

	$num_polls = mysqli_num_rows($num_polls_query);

	// THIS IF FUNCTION CHECKS IF THE COMPANY HAS CREATED ITS FIRST POLL MANUALLY. IF SO, $NUM_POLLS MUST BE HIGHER THAN 0 - AND ONLY THIS WOULD CREATE MORE POLLS BY THE CRONJOB
	if ($num_polls > 0) {

		$select_polls_from_poll_pool_query = "SELECT * FROM poll_pool ORDER BY RAND() LIMIT 0,5";
		$select_polls_from_poll_pool = mysqli_query($connection, $select_polls_from_poll_pool_query);

		while($row = mysqli_fetch_assoc($select_polls_from_poll_pool)) {
			$question = $row['question'];
			$answers = escape($row['answers']);
			$answers_rating = escape($row['answers_rating']);
			$type_id = $row['type_id'];
			$subtype_id = $row['subtype_id'];


			$query = "INSERT INTO polls(poll_question, poll_description, poll_type_id, poll_subtype_id, poll_user_id, poll_company, poll_status, poll_expires, poll_date, poll_votes_count) ";
			// CREATES A TIMESTAMP US TIME + 1 DAY TO HAVE RIGHT DATE FOR NZ - IF DIFFERENT COUNTRY, THERE NEEDS TO BE ANOTHER SOLUTION
		    $query .= "VALUES('$question', '', $type_id, $subtype_id, $company_user_id, '$company_name', 'published', '2025-11-05 14:29:36', NOW()+INTERVAL 18 HOUR, 0)";
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
		        $query .= "VALUES($the_poll_id, $type_id, $subtype_id, '$company_name', '$poll_answer', $poll_answer_rating, 0, NOW()+INTERVAL 18 HOUR) "; 

		        $create_poll_answer_query = mysqli_query($connection, $query);

		        confirmQuery($create_poll_answer_query);
		    }
		}
	}
}




?>
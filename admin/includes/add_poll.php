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
        $session_user_company = $row['user_company'];
    }
}

if(isset($_POST['create_poll'])) {

  $poll_question = escape($_POST['poll_question']); //ESCAPE CLEANS DATA AGAINST MSQL INJECTION
  $poll_description = escape($_POST['poll_description']);
  $poll_answers = escape($_POST['poll_answers']);
  $poll_status = escape($_POST['poll_status']);
  $poll_date = escape(date('d-m-y');)

  $query = "INSERT INTO polls(poll_question, poll_description, poll_user_id, poll_company, poll_status, poll_date) ";
  $query .= "VALUES('{$poll_question}', '{$poll_description}', '{$session_user_firstname}', '{$session_user_company}', '{$poll_status}', now()) ";

  $create_poll_query = mysqli_query($connection, $query);

  confirmQuery($create_poll_query);

  $the_poll_id = mysqli_insert_id($connection);

  // Below will get the last insert ID, this will be the poll id
  // $poll_id = SELECT LAST_INSERT_ID();
  
  // Get the answers and convert the multiline string to an array, so we can add each answer to the "poll_answers" table
  $poll_answers = explode(PHP_EOL, escape($_POST['poll_answers']));
  foreach ($poll_answers as $poll_answer) {
        // If the answer is empty there is no need to insert
        if (empty($poll_answer)) continue;
        // Add answer to the "poll_answers" table
        $query = "INSERT INTO poll_answers(poll_id, poll_company, poll_answer, poll_votes) ";
        $query .= "VALUES({$the_poll_id}, '{$session_user_company}', '{$poll_answer}', 0) ";

        $create_poll_answer_query = mysqli_query($connection, $query);

        confirmQuery($create_poll_answer_query);
    }


  echo "<p class='bg-success'>Poll added: <a href='../poll.php?pl_id={$the_poll_id}'>View Poll</a> | <a href='polls.php?source=add_poll'>Add another poll</a></p>";
}

?>




<form action="" method="post" enctype="multipart/form-data">    
     
     
      <div class="form-group">
        <label for="title">Poll Question</label>
        <input type="text" class="form-control" name="poll_question">
      </div>

      <div class="form-group">
        <label for="title">Poll Description</label>
        <input type="text" class="form-control" name="poll_description">
      </div>

      <div class="form-group">
        <label for="answers">Answers (per line)</label>
        <textarea name="poll_answers" id="answers"></textarea>
      </div>

      <div class="form-group">
         <label for="title">Poll Status</label>
         <select class="form-control" style="width: 25%" name="poll_status">
           <option value="draft">Select Option</option>
           <option value="published">Published</option>
           <option value="draft">Draft</option>
         </select>
      </div>

      
      <div class="form-group">
         <input class="btn btn-primary" type="submit" name="create_poll" value="Create Poll">
      </div>


</form>
    
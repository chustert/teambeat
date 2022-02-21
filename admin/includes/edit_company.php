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

if(isset($_GET['c_id'])) {
  $the_company_id = $_GET['c_id'];

  $query = "SELECT * FROM companies WHERE company_id = $the_company_id";
  $select_companies_by_id = mysqli_query($connection, $query);  

  while($row = mysqli_fetch_assoc($select_companies_by_id)) {
      $company_id = $row['company_id'];
      $company_name = $row['company_name'];
      $company_user_id = $row['company_user_id'];
      $company_type = $row['company_type'];
      $company_subscription = $row['company_subscription'];
  }

  if(isset($_POST['update_company'])) {
    $company_type = $_POST['company_type'];
    $company_subscription = $_POST['company_subscription'];

      $query = "UPDATE companies SET ";
      $query .= "company_type = '{$company_type}', ";
      $query .= "company_subscription = '{$company_subscription}' ";
      $query .= "WHERE company_id = {$the_company_id} ";

      $update_company = mysqli_query($connection, $query);

      confirmQuery($update_company);
      
      echo "Company Updated" . " | <a href='company.php'>View Company</a>";

    }

} else {
  header("Location: index.php");
}

?>

<form action="" method="post" enctype="multipart/form-data">    

     <div class="form-group">
         <label for="title">Company Type</label>
         <input value="<?php echo $company_type; ?>" type="text" class="form-control" name="company_type">
      </div>
      
      <div class="form-group">
         <label for="title">Company Subscription</label>
         <input value="<?php echo $company_subscription; ?>" type="text" class="form-control" name="company_subscription">
      </div>
      
      <div class="form-group">
         <input class="btn btn-primary" type="submit" name="update_company" value="Update Company">
      </div>


</form>

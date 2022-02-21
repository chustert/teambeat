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

if(isset($_GET['u_id'])) {
  $the_user_id = escape($_GET['u_id']);

  $query = "SELECT * FROM users WHERE user_id = $the_user_id";
  $select_users_by_id = mysqli_query($connection, $query);  

  while($row = mysqli_fetch_assoc($select_users_by_id)) {
      $user_id = $row['user_id'];
      $user_firstname = $row['user_firstname'];
      $user_lastname = $row['user_lastname'];
      $user_email = $row['user_email'];
      $user_phone = $row['user_phone'];
      $user_role = $row['user_role'];
      $user_company_id = $row['user_company_id'];
      $user_company = $row['user_company'];
      $user_department_id = $row['user_department_id'];
      $user_department = $row['user_department'];
      $user_password = $row['user_password'];
  }

  if(isset($_POST['update_user'])) {
    $user_firstname = escape($_POST['user_firstname']);
    $user_lastname = escape($_POST['user_lastname']);
    $user_email = escape($_POST['user_email']);
    $user_phone = escape($_POST['user_phone']);
    $user_role = escape($_POST['user_role']);
    $user_company_id = escape($_POST['user_company']);
    $user_department_id = escape($_POST['user_department']);
    $user_password = escape($_POST['user_password']);

    $query = "SELECT company_name FROM companies WHERE company_id = $user_company_id";
    $select_company_name_query = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_array($select_company_name_query)) {
      $user_company = $row['company_name'];
    }

    $query = "SELECT dep_title FROM departments WHERE dep_id = $user_department_id";
    $select_department_title_query = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_array($select_department_title_query)) {
      $user_department = $row['dep_title'];
    }
    
    if(!empty($user_password)) {
      $query_password = "SELECT user_password FROM users WHERE user_id = $the_user_id ";
      $get_user_query = mysqli_query($connection, $query_password);

      confirmQuery($get_user_query);

      $row = mysqli_fetch_array($get_user_query);
      $db_user_password = $row['user_password'];

      if($db_user_password != $user_password) {
        $hashed_password = password_hash($user_password, PASSWORD_BCRYPT, array('cost' => 10));
      }

      $query = "UPDATE users SET ";
      $query .= "user_firstname = '{$user_firstname}', ";
      $query .= "user_lastname = '{$user_lastname}', ";
      $query .= "user_email = '{$user_email}', ";
      $query .= "user_phone = '{$user_phone}', ";
      $query .= "user_role = '{$user_role}', ";
      $query .= "user_company_id = {$user_company_id}, ";
      $query .= "user_company = '{$user_company}', ";
      $query .= "user_department_id = {$user_department_id}, ";
      $query .= "user_department = '{$user_department}', ";
      $query .= "user_password = '{$hashed_password}' ";
      $query .= "WHERE user_id = {$the_user_id} ";

      $update_user = mysqli_query($connection, $query);

      confirmQuery($update_user);
      
      echo "User Updated" . " | <a href='users.php'>View Users</a>";

    }

    if(empty($user_password)) {
      $query = "UPDATE users SET ";
      $query .= "user_firstname = '{$user_firstname}', ";
      $query .= "user_lastname = '{$user_lastname}', ";
      $query .= "user_email = '{$user_email}', ";
      $query .= "user_phone = '{$user_phone}', ";
      $query .= "user_role = '{$user_role}', ";
      $query .= "user_company_id = {$user_company_id}, ";
      $query .= "user_company = '{$user_company}', ";
      $query .= "user_department_id = {$user_department_id}, ";
      $query .= "user_department = '{$user_department}' ";
      $query .= "WHERE user_id = {$the_user_id} ";

      $update_user = mysqli_query($connection, $query);

      confirmQuery($update_user);
      
      echo "User Updated" . " | <a href='users.php'>View Users</a>";
    }
   
  }

} else {
  header("Location: index.php");
}

?>

<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Edit User
        </h1>

<form action="" method="post" enctype="multipart/form-data">    

     <div class="form-group">
         <label for="title">First Name</label>
         <input value="<?php echo $user_firstname; ?>" type="text" class="form-control" name="user_firstname">
      </div>
      
      <div class="form-group">
         <label for="title">Last Name</label>
         <input value="<?php echo $user_lastname; ?>" type="text" class="form-control" name="user_lastname">
      </div>

      <div class="form-group">
         <label for="title">Email</label>
         <input value="<?php echo $user_email; ?>" type="email" class="form-control" name="user_email">
      </div>

      <div class="form-group">
         <label for="title">Mobile Number</label>
         <input value="<?php echo $user_phone; ?>" type="tel" class="form-control" name="user_phone">
      </div>

      <div class="form-group">
        <label for="post_category">Select Role</label>
        <select name="user_role" id="user_role" style="display: block;">
        <option value="<?php echo $user_role; ?>"><?php echo $user_role; ?></option>
        <?php
        if($user_role == 'admin') {
          echo "<option value='subscriber'>subscriber</option>";
        } else {
          echo "<option value='admin'>admin</option>";
        }
        ?>
        </select>
      </div>

      <div class="form-group">
        <label for="title">Company</label>
        <select name="user_company" class="form-control" style="width: 25%">
        <?php

        $query = "SELECT * FROM companies WHERE company_name = '$session_user_company'";
        $select_companies = mysqli_query($connection, $query); 

        confirmQuery($select_companies);

        while($row = mysqli_fetch_assoc($select_companies)) {
            $company_id = $row['company_id'];
            $company_name = $row['company_name'];  

            echo "<option value='{$company_id}'>{$company_name}</option>";
        }
        ?>
        </select>
      </div>

      <div class="form-group">
        <label for="title">Department</label>
        <select name="user_department" class="form-control" style="width: 25%">
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

      <div class="form-group">
         <label for="post_image">Password</label>
         <input autocomplete="off" type="password" class="form-control" name="user_password">
      </div>
      
      <div class="form-group">
         <input class="btn btn-primary" type="submit" name="update_user" value="Update User">
      </div>


</form>

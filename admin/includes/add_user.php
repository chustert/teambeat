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


if(isset($_POST['create_user'])) {

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





  $error = [
        'email' => '',
        'password' => ''
        // 'user_phone' => ''
    ];

    if($user_email == '') {
        $error['email'] = 'E-Mail cannot be empty';
    }

    if (email_exists($user_email)) {
        $error['email'] = 'E-Mail already exists';
    }

    if($user_password == '') {
        $error['password'] = 'Password cannot be empty';
    }

    // if (phone_exists($user_phone)) {
    //     $error['user_phone'] = 'Phone number already exists';
    // }

    foreach ($error as $key => $value) {
        if (empty($value)) {

            unset($error[$key]);
            
        }
    }

    if (empty($error)) {

      $user_password = password_hash($user_password, PASSWORD_BCRYPT, array('cost' => 10));

      $query = "INSERT INTO users(user_firstname, user_lastname, user_email, user_phone, user_role, user_company_id, user_company, user_department_id, user_department, user_password) ";
      $query .= "VALUES('{$user_firstname}', '{$user_lastname}', '{$user_email}', '{$user_phone}', '{$user_role}', {$user_company_id}, '{$user_company}', {$user_department_id}, '{$user_department}', '{$user_password}' ) ";

      $create_user_query = mysqli_query($connection, $query);

      confirmQuery($create_user_query);

      echo "User created: " . " " . "<a href='users.php'>View Users</a>";
    }
}

?>

<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Add User
        </h1>

        <?php if (isset($error['email']) || isset($error['password'])) { ?>
          <div class="alert alert-danger" role="alert">
            <p><?php echo isset($error['email']) ? $error['email'] : ''?></p>
            <p><?php echo isset($error['password']) ? $error['password'] : ''?></p>
          </div>
        <?php } ?>

<form action="" method="post" enctype="multipart/form-data">    
     
     <div class="form-group">
         <label for="title">First Name</label>
         <input type="text" class="form-control" name="user_firstname">
      </div>
      
      <div class="form-group">
         <label for="title">Last Name</label>
         <input type="text" class="form-control" name="user_lastname">
      </div>

      <div class="form-group">
         <label for="title">Email</label>
         <input type="email" class="form-control" name="user_email" readonly onfocus="this.removeAttribute('readonly');" style="background-color: white;">
      </div>

      <div class="form-group">
         <label for="title">Mobile Phone</label>
         <input type="tel" class="form-control" name="user_phone">
      </div>

      <div class="form-group">
        <label for="post_category">Select Role</label>
        <select name="user_role" id="" style="display: block;">
        <option value="subscriber">Select Option</option>
        <option value="admin">Admin</option>
        <option value="subscriber">Subscriber</option>
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
         <label for="password">Password</label>
         <input type="password" class="form-control" name="user_password" readonly onfocus="this.removeAttribute('readonly');" style="background-color: white;">
      </div>
      
      <div class="form-group">
         <input class="btn btn-primary" type="submit" name="create_user" value="Add User">
      </div>


</form>
    
<?php ob_start(); ?>
<?php session_start(); ?>
<?php 
if(!isset($_SESSION['user_role'])) {
    header("Location: index.php");
} 
?>
<?php
include "includes/db.php";
include "includes/header.php";
include "admin/functions.php";
?>

<!-- Navigation -->
<?php  include "includes/navigation.php"; ?>

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
        $user_company = $row['user_company'];
    }

    if(isset($_POST['update_user'])) {
        $user_firstname = escape($_POST['user_firstname']);
        $user_lastname = escape($_POST['user_lastname']);
        $user_email = escape($_POST['user_email']);
        $user_phone = escape($_POST['user_phone']);
        $user_password = escape($_POST['user_password']);

        if(!empty($user_password)) {
            $query_password = "SELECT user_password FROM users WHERE user_id = $user_id ";
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
            $query .= "user_password = '{$hashed_password}' ";
            $query .= "WHERE user_email = '{$user_email}' ";

            $update_user = mysqli_query($connection, $query);

            confirmQuery($update_user);
        }

        if(empty($user_password)) {
            $query = "UPDATE users SET ";
            $query .= "user_firstname = '{$user_firstname}', ";
            $query .= "user_lastname = '{$user_lastname}', ";
            $query .= "user_email = '{$user_email}', ";
            $query .= "user_phone = '{$user_phone}' ";
            $query .= "WHERE user_email = '{$user_email}' ";

            $update_user = mysqli_query($connection, $query);

            confirmQuery($update_user);
        }
    }
}
?>


<main role="main" class="flex-shrink-0">
  <div class="container">

    <div class="d-flex align-items-center">

        <div class="mx-auto login-min-width">
                                                                    
            <h1 class="page-header"><img src="images/orange-line.png" alt=""> Your Profile, <?php echo $user_firstname; ?></h1>

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
                     <label for="title">Mobile Phone</label>
                     <input value="<?php echo $user_phone; ?>" type="tel" class="form-control" name="user_phone">
                  </div>

                  <div class="form-group">
                     <label for="post_image">Password</label>
                     <input autocomplete="off" type="password" class="form-control" name="user_password">
                  </div>
                  
                  <div class="form-group">
                     <input class="btn btn-primary" type="submit" name="update_user" value="Update Profile">
                  </div>


            </form>

        </div>
                              
    </div>

  </div>
</main>

<?php include "includes/footer.php";?>

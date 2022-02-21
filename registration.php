<?php  include "includes/db.php"; ?>
<?php  include "includes/header.php"; ?>
<?php include "admin/functions.php"; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $firstname = escape($_POST['firstname']);
    $company = escape($_POST['company']);
    $email = escape($_POST['email']);
    $password = escape($_POST['password']);

    $error = [
        'company' => '',
        'email' => '',
        'password' => ''
    ];

    if($company == '') {
        $error['company'] = 'Company cannot be empty';
    }

    if (company_exists($company)) {
        $error['company'] = 'Company already exists.';
    }

    if($email == '') {
        $error['email'] = 'E-Mail cannot be empty';
    }

    if (email_exists($email)) {
        $error['email'] = 'E-Mail already exists, <a href="index.php">please log in.</a>';
    }

    if($password == '') {
        $error['password'] = 'Password cannot be empty';
    }

    foreach ($error as $key => $value) {
        if (empty($value)) {

            unset($error[$key]);
            
        }
    }

    if (empty($error)) {
        register_user($firstname, $company, $email, $password);

        login_user($email, $password);
    }


}
?>


<!-- Navigation -->

<?php  include "includes/navigation.php"; ?>
    
<main role="main" class="flex-shrink-0">
    <div class="container">

        <div class="d-flex align-items-center">

            <div class="mx-auto login-min-width">
                                                                        

                    <h1><img src="images/orange-line.png" alt=""> Register</h1>
                    
                    <form role="form" action="registration.php" method="post" autocomplete="off" class="form-register">

                        <label for="firstname" class="sr-only">First Name</label>
                        <input type="text" name="firstname" class="form-control" placeholder="First Name" autocomplete="on" value="<?php echo isset($firstname) ? $firstname : ''?>">

                        <label for="company" class="sr-only">Company</label>
                        <input type="text" name="company" class="form-control register-company" placeholder="Company" autocomplete="on" value="<?php echo isset($company) ? $company : ''?>">

                        <label for="email" class="sr-only">Email address</label>
                        <input type="email" name="email" class="form-control" placeholder="Email address" autocomplete="on" value="<?php echo isset($email) ? $email : ''?>">

                        <label for="password" class="sr-only">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password">

                        <button class="btn btn-lg btn-primary btn-block" id="btn-login" type="submit" name="register">Register</button>
                        <p><?php echo isset($error['company']) ? $error['company'] : ''?></p>
                        <p><?php echo isset($error['email']) ? $error['email'] : ''?></p>
                        <p><?php echo isset($error['password']) ? $error['password'] : ''?></p>
                    </form>


            </div>
                                  
        </div>

    </div>
</main>

<?php include "includes/footer.php";?>

<?php
include "includes/db.php";
include "includes/header.php";
include "admin/functions.php";
?>

<!-- Navigation -->
<?php  include "includes/navigation.php"; ?>
    
<main role="main" class="flex-shrink-0">
    <div class="container">

        <div class="d-flex align-items-center">

            <div class="mx-auto login-min-width">
                                                                        
                <?php if(isset($_SESSION['user_role'])):
                    header("Location: index.php");

                else: ?>
                    <form role="form" action="includes/login.php" method="post" id="login-form" autocomplete="on" class="form-signin">
                        <h1 style="text-align: center;"><img src="images/teambeat-logo-trim.png" alt="Teambeat Logo - Login" style="width: 40%; height: auto; padding-bottom: 30px;"></h1>
                        <label for="email" class="sr-only">Email address</label>
                        <input type="email" name="user_email" class="form-control" placeholder="Email address" autocomplete="on">
                        <label for="password" class="sr-only">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password">
                        <input type="checkbox" name="rememberme" value="1" />&nbsp;Remember Me
                        <button class="btn btn-lg btn-primary btn-block" id="btn-login" type="submit" name="login">Log in</button>
                    </form>
                <?php endif; ?>

            </div>
                                  
        </div>

    </div>
</main>


<?php include "includes/footer.php";?>
<?php ob_start(); ?>
<?php session_start(); ?>

<?php

$_SESSION['user_id'] = null;
$_SESSION['firstname'] = null;
$_SESSION['lastname'] = null;
$_SESSION['user_email'] = null;
$_SESSION['user_phone'] = null;
$_SESSION['user_role'] = null;
$_SESSION['user_company'] = null;

 // Remove cookie variables
 $days = 30;
 setcookie ("rememberme","", time() - ($days * 24 * 60 * 60 * 1000), '/');

header("Location: ../index.php");

?>
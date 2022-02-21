<?php session_start(); ?>
<?php include "db.php"; ?>
<?php include "../admin/functions.php"; ?>

<?php 

if(isset($_POST['login'])) {

	if (isset($_POST['rememberme'])) {
		login_user_and_remember($_POST['user_email'], $_POST['password'], $_POST['rememberme']);
	} else {
		login_user($_POST['user_email'], $_POST['password']);
	}

}














?>
<?php 

function redirect($location) {
    return header("Location:" . $location);
    exit;
}

function ifItIsMethod($method=null){
    if($_SERVER['REQUEST_METHOD'] == strtoupper($method)){
        return true;
    }
    return false;
}


function isLoggedIn() {
    if(isset($_SESSION['user_role'])) {
        return true;
    }
    return false;
}

function checkIfUserIsLoggedInAndRedirect($redirectLocation=null) {
    if(isLoggedIn()) {
        redirect($redirectLocation);
    }
}

function confirmQuery($result) {

    global $connection;

    if(!$result) {
    die("QUERY FAILED" . mysqli_error($connection));
  }
}

function escape($string) {
    global $connection;
    return mysqli_real_escape_string($connection, trim($string));
}

function insertDepartments() {

	global $connection;

	if(isset($_POST['submit'])) {
        $dep_title = escape($_POST['dep_title']);
        if($dep_title == "" || empty($dep_title)) {
            echo "This field should not be empty.";
        } else {
            $query = "INSERT INTO departments(dep_title, dep_company) ";
            $query .= "VALUE('{$dep_title}', '{$session_user_company}') ";

            $create_department_query = mysqli_query($connection, $query);

            if(!$create_department_query) {
                die('QUERY FAILED' . mysqli_error($connection));
            }
        }
    }

}

function findAllDepartments() {

	global $connection;

	$query = "SELECT * FROM departments";
    $select_departments = mysqli_query($connection, $query);  

    while($row = mysqli_fetch_assoc($select_departments)) {
        $dep_id = $row['dep_id'];
        $dep_title = $row['dep_title'];

        echo "<tr>";
        echo "<td>{$dep_id}</td>";
        echo "<td>{$dep_title}</td>";
        echo "<td><a href='departments.php?delete={$dep_id}'>Delete</a></td>";
        echo "<td><a href='departments.php?edit={$dep_id}'>Edit</a></td>";
        echo "</tr>";
    }

}

function updateAndIncludeDepartments() {

	global $connection;

	if(isset($_GET['edit'])) {
        $cat_id = $_GET['edit'];

        include "includes/update_department.php";
    }

}

function deleteDepartments() {

	global $connection;

	if(isset($_GET['delete'])) {
        $the_dep_id = $_GET['delete'];
        $query = "DELETE FROM departments WHERE dep_id = {$the_dep_id} ";
        $delete_query = mysqli_query($connection, $query);
        header("Location: departments.php");
    }
}


// CHECK IF USER IS ADMIN
function is_admin($user_email = '') {
    global $connection;

    $query = "SELECT user_role FROM users WHERE user_email = '$user_email'";
    $result = mysqli_query($connection, $query);
    confirmQuery($result);

    $row = mysqli_fetch_array($result);

    if ($row['user_role'] == 'admin') {
        return true;
    } else {
        return false;
    }
}

function company_exists($user_company) {
    global $connection;

    $query = "SELECT company_name FROM companies WHERE company_name = '$user_company'";
    $result = mysqli_query($connection, $query);
    confirmQuery($result);

    if (mysqli_num_rows($result) > 0) {
        return true;
    } else {
        false;
    }
}

function email_exists($user_email) {
    global $connection;

    $query = "SELECT user_email FROM users WHERE user_email = '$user_email'";
    $result = mysqli_query($connection, $query);
    confirmQuery($result);

    if (mysqli_num_rows($result) > 0) {
        return true;
    } else {
        false;
    }
}

// function phone_exists($user_phone) {
//     global $connection;

//     $query = "SELECT user_phone FROM users WHERE user_phone = '$user_phone'";
//     $result = mysqli_query($connection, $query);
//     confirmQuery($result);

//     if (mysqli_num_rows($result) > 0) {
//         return true;
//     } else {
//         false;
//     }
// }

function register_user($firstname, $company, $email, $password) {
    global $connection;

    $firstname = mysqli_real_escape_string($connection, $firstname);
    $company = mysqli_real_escape_string($connection, $company);
    $email = mysqli_real_escape_string($connection, $email);
    $password = mysqli_real_escape_string($connection, $password);

    $password = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));

    $user_query = "INSERT INTO users (user_firstname, user_lastname, user_email, user_phone, user_password, user_role, user_company_id, user_company, user_department_id, user_department) ";
    $user_query .= "VALUES('{$firstname}', '', '{$email}', '', '{$password}', 'admin', 0, '{$company}', 0, '' )";
    $register_user_query = mysqli_query($connection, $user_query);
    
    confirmQuery($register_user_query);

    $the_last_user_id = mysqli_insert_id($connection);

    $company_query = "INSERT INTO companies (company_name, company_user_id, company_type, company_subscription) ";
    $company_query .= "VALUES('{$company}', {$the_last_user_id}, '', 'basic' )";
    $register_company_query = mysqli_query($connection, $company_query);
    
    confirmQuery($register_company_query);

    $the_last_company_id = mysqli_insert_id($connection);

    //UPDATE COMPANY ID IN USER TABLE

    $update_user_query = "UPDATE users SET user_company_id = $the_last_company_id WHERE user_company = '$company'";

    $update_user = mysqli_query($connection, $update_user_query);
    
    confirmQuery($update_user);

    
}

function login_user($email, $password) {

    global $connection;
    
    $email = trim($email);
    $password = trim($password);

    $email = mysqli_real_escape_string($connection, $email);
    $password = mysqli_real_escape_string($connection, $password);

    $query = "SELECT * FROM users WHERE user_email = '{$email}' ";
    $select_user_query = mysqli_query($connection, $query);

    if (!$select_user_query) {
        die("QUERY FAILED " . mysqli_error($connection));
    }

    while ($row = mysqli_fetch_array($select_user_query)) {
        $db_user_id = $row['user_id'];
        $db_user_email = $row['user_email'];
        $db_user_password = $row['user_password'];
        $db_user_firstname = $row['user_firstname'];
        $db_user_lastname = $row['user_lastname'];
        $db_user_role = $row['user_role'];
        $db_user_company = $row['user_company'];
    }
    
    if (password_verify($password, $db_user_password)) {
        
        if (session_status() === PHP_SESSION_NONE) session_start();

        $_SESSION['user_id'] = $db_user_id;
        $_SESSION['user_email'] = $db_user_email;
        $_SESSION['firstname'] = $db_user_firstname;
        $_SESSION['lastname'] = $db_user_lastname;
        $_SESSION['user_role'] = $db_user_role;
        $_SESSION['user_company'] = $db_user_company;
        
        redirect("/amelio/admin");
    } else {
        redirect("/amelio/login.php");
    }
}

function login_user_and_remember($email, $password, $rememberme) {

    global $connection;
    
    $email = trim($email);
    $password = trim($password);

    $email = mysqli_real_escape_string($connection, $email);
    $password = mysqli_real_escape_string($connection, $password);

    $query = "SELECT * FROM users WHERE user_email = '{$email}' ";
    $select_user_query = mysqli_query($connection, $query);

    if (!$select_user_query) {
        die("QUERY FAILED " . mysqli_error($connection));
    }

    while ($row = mysqli_fetch_array($select_user_query)) {
        $db_user_id = $row['user_id'];
        $db_user_email = $row['user_email'];
        $db_user_password = $row['user_password'];
        $db_user_firstname = $row['user_firstname'];
        $db_user_lastname = $row['user_lastname'];
        $db_user_role = $row['user_role'];
        $db_user_company = $row['user_company'];
    }
    
    if (password_verify($password, $db_user_password)) {
        
        if (session_status() === PHP_SESSION_NONE) session_start();

        $_SESSION['user_id'] = $db_user_id;
        $_SESSION['user_email'] = $db_user_email;
        $_SESSION['firstname'] = $db_user_firstname;
        $_SESSION['lastname'] = $db_user_lastname;
        $_SESSION['user_role'] = $db_user_role;
        $_SESSION['user_company'] = $db_user_company;

        // Set cookie variables
        $days = 30;
        $value = encryptCookie($db_user_id);
        setcookie ("rememberme",$value,time()+ ($days * 24 * 60 * 60 * 1000), '/');
        
        redirect("/amelio/admin");
    } else {
        redirect("/amelio/login.php");
    }
}

// Encrypt cookie
function encryptCookie( $value ) {

   $key = hex2bin(openssl_random_pseudo_bytes(4));

   $cipher = "aes-256-cbc";
   $ivlen = openssl_cipher_iv_length($cipher);
   $iv = openssl_random_pseudo_bytes($ivlen);

   $ciphertext = openssl_encrypt($value, $cipher, $key, 0, $iv);

   return( base64_encode($ciphertext . '::' . $iv. '::' .$key) );
}

// Decrypt cookie
function decryptCookie( $ciphertext ) {

   $cipher = "aes-256-cbc";

   list($encrypted_data, $iv,$key) = explode('::', base64_decode($ciphertext));
   return openssl_decrypt($encrypted_data, $cipher, $key, 0, $iv);

}

?>
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
?>
<h4>Your companies</h4>
<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Company Name</th>
            <!-- <th>Company User</th> -->
            <th>Company Type</th>
        </tr>
    </thead>
    <tbody>

        <?php

        $query = "SELECT * FROM companies WHERE company_user_id = $session_user_id ";
        $select_companies = mysqli_query($connection, $query);  

        while($row = mysqli_fetch_assoc($select_companies)) {
            $company_id = $row['company_id'];
            $company_name = $row['company_name'];
            $company_user_id = $row['company_user_id'];
            $company_type = $row['company_type'];

            echo "<tr>";
            echo "<td>{$company_id}</td>";
            echo "<td>{$company_name}</td>";
            // echo "<td>{$company_user_id}</td>";
            echo "<td>{$company_type}</td>";

            echo "<td><a href='company.php?source=edit_company&c_id={$company_id}'>Edit</a></td>";
            echo "</tr>";
        }

        ?>

    </tbody>
</table>

<?php

if(isset($_GET['delete'])) {

    if (isset($_SESSION['user_role'])) {
        if ($_SESSION['user_role'] == 'admin') {
            $the_user_id = mysqli_real_escape_string($connection, $_GET['delete']);
            $query = "DELETE FROM users WHERE user_id = {$the_user_id} ";
            $delete_query = mysqli_query($connection, $query);
            header("Location: users.php");
        }
    }

}

?>
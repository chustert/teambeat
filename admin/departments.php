<?php include "includes/admin_header.php"; ?>

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

?>

    <div id="wrapper">

    <?php include "includes/admin_navigation.php"; ?>

        <div id="page-wrapper">

            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            Departments of <?php echo $session_user_company ?>
                        </h1>

                        <div class="col-xs-6">

                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="dep_title">Add Department</label>
                                    <input class="form-control" type="text" name="dep_title">
                                </div>
                                <div class="form-group">
                                    <input class="btn btn-primary" type="submit" name="submit" value="Add Department">

                                    <?php 
                                    if(isset($_POST['submit'])) {
                                        $dep_title = escape($_POST['dep_title']);
                                        if($dep_title == "" || empty($dep_title)) {
                                            echo "This field should not be empty.";
                                        } else {
                                            $query = "INSERT INTO departments(dep_title, dep_company_id, dep_company) ";
                                            $query .= "VALUE('{$dep_title}', {$session_user_company_id}, '{$session_user_company}') ";

                                            $create_department_query = mysqli_query($connection, $query);

                                            if(!$create_department_query) {
                                                die('QUERY FAILED' . mysqli_error($connection));
                                            }

                                            redirect('departments.php');
                                        }
                                    } 
                                    ?>

                                </div>
                            </form>

                            <?php updateAndIncludeDepartments(); ?>

                        </div>

                        <div class="col-xs-6">
                            <table class="table table-bordered table-hover">

                                <?php 
                                $query = "SELECT * FROM departments WHERE dep_company = '$session_user_company'";
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
                                ?>
                                
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Department</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php deleteDepartments(); ?>

                                </tbody>
                            </table>
                        </div>



                    </div>
                </div>
                <!-- /.row -->

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->

<?php include "includes/admin_footer.php"; ?>

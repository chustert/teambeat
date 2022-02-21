<form action="" method="post">
    <div class="form-group">
        <label for="cat_title">Edit Department</label>

        <?php 

        if(isset($_GET['edit'])) {
            $dep_id = $_GET['edit'];
            
            $query = "SELECT * FROM departments WHERE dep_id = $dep_id ";
            $select_department_id = mysqli_query($connection, $query);  

            while($row = mysqli_fetch_assoc($select_department_id)) {
                $dep_id = $row['dep_id'];
                $dep_title = $row['dep_title'];
        ?>

        <input value="<?php if(isset($dep_title)){echo $dep_title;} ?>" type="text" class="form-control" name="dep_title">

        <?php }} ?>
        <?php 
        // UPDATE/EDIT DEPARTMENT QUERY
        if(isset($_POST['update_department'])) {
                $the_dep_title = $_POST['dep_title'];
                $query = "UPDATE departments SET dep_title = '{$the_dep_title}' WHERE dep_id = {$dep_id}";
                $update_query = mysqli_query($connection, $query);

                if(!$update_query){
                    die("Query failed" . mysqli_error($connection));
                }
        }
        ?>


    </div>
    <div class="form-group">
        <input class="btn btn-primary" type="submit" name="update_department" value="Edit Department">
    </div>
</form>
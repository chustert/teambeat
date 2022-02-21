<?php  
if(isset($_POST['checkBoxArray'])) {
    foreach ($_POST['checkBoxArray'] as $pageValue_id) {
        $bulk_options = $_POST['bulk_options'];

        switch ($bulk_options) {
            case 'published':
    
                $query = "UPDATE pages SET ";
                $query .= "page_status = '{$bulk_options}' ";
                $query .= "WHERE page_id = '{$pageValue_id}' ";

                $update_to_published_status = mysqli_query($connection, $query);

                confirmQuery($update_to_published_status);

                break;

            case 'draft':
    
                $query = "UPDATE pages SET ";
                $query .= "page_status = '{$bulk_options}' ";
                $query .= "WHERE page_id = '{$pageValue_id}' ";

                $update_to_draft_status = mysqli_query($connection, $query);

                confirmQuery($update_to_draft_status);

                break;

            case 'delete':
    
                $query = "DELETE FROM pages ";
                $query .= "WHERE page_id = '{$pageValue_id}' ";

                $update_to_delete_status = mysqli_query($connection, $query);

                confirmQuery($update_to_delete_status);

                break;

            case 'clone':
    
                $query = "SELECT * FROM pages ";
                $query .= "WHERE page_id = '{$pageValue_id}' ";

                $select_page_query = mysqli_query($connection, $query);

                while ($row = mysqli_fetch_array($select_page_query)) {
                    $page_title = $row['page_title'];
                    $page_subtitle = $row['page_subtitle'];
                    $page_image = $row['page_image'];
                    $page_content = $row['page_content'];
                    $page_user = $row['page_user'];
                    $page_status = $row['page_status'];
                }

                $query = "INSERT INTO pages(page_title, page_subtitle, page_image, page_content, page_user, page_status) ";
                $query .= "VALUES('$page_title', '$page_subtitle', '$page_image', '$page_content', '$page_user', '$page_status')";
                $copy_query = mysqli_query($connection, $query);

                if(!$copy_query) {
                    die("Query failed" . mysqli_error($connection));
                }

                break;
            
            default:
                # code...
                break;
        }
    }
}

?>

<form action="" method='post'>
    <table class="table table-bordered table-hover">
        
        <div id="bulkOptionsContainer" class="col-xs-4">
            <select class="form-control" name="bulk_options" id="">
                <option value="">Select Options</option>
                <option value="published">Publish</option>
                <option value="draft">Draft</option>
                <option value="delete">Delete</option>
                <option value="clone">Clone</option>
            </select>
        </div>

        <div class="col-xs-4">
            <input type="submit" name="submit" class="btn btn-success" value="Apply" style="margin-right: 10px;"><a href="pages.php?source=add_page" class="btn btn-primary">Add New</a>
        </div>

        <thead>
            <tr>
                <th>
                    <input id="selectAllBoxes" type="checkbox" name="">
                </th>
                <th>ID</th>
                <th>Title</th>
                <th>Subtitle</th>
                <th>Image</th>
                <th>Author</th>
                <th>Status</th>
                <th>View Page</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>

            <?php

            $query = "SELECT * FROM pages ORDER BY page_id DESC";
            $select_pages = mysqli_query($connection, $query);  

            while($row = mysqli_fetch_assoc($select_pages)) {
                $page_id = $row['page_id'];
                $page_title = $row['page_title'];
                $page_subtitle = $row['page_subtitle'];
                $page_image = $row['page_image'];
                $page_user = $row['page_user'];
                $page_status = $row['page_status'];

                echo "<tr>";
                ?>
                <td><input class='checkBoxes' type='checkbox' name='checkBoxArray[]' value='<?php echo $page_id; ?>'></td>
                <?php
                echo "<td>{$page_id}</td>";
                
                echo "<td>{$page_title}</td>";
                echo "<td>{$page_subtitle}</td>";
                echo "<td><img src='../images/{$page_image}' width='100' alt=''</td>";
                if (!empty($page_user)) {
                    echo "<td>{$page_user}</td>";
                }
                echo "<td>{$page_status}</td>";

                echo "<td><a class='btn btn-primary' href='../page.php?pg_id={$page_id}'>View Page</a></td>";
                echo "<td><a class='btn btn-info' href='pages.php?source=edit_page&pg_id={$page_id}'>Edit</a></td>";

                ?>
                <!-- DELETE VIA POST BECAUSE MORE SECURE --> 
                <form method="post">
                    <input type="hidden" name="page_id" value="<?php echo $page_id ?>">
                    <?php  
                    echo '<td><input class="btn btn-danger" type="submit" name="delete" value="Delete"></td>'
                    ?>
                    
                </form>

                <?php


                //echo "<td><a onClick=\"javascript: return confirm('Are you sure you want to delete?'); \" href='pages.php?delete={$page_id}'>Delete</a></td>";

                echo "</tr>";
            }

            ?>

        </tbody>
    </table>
</form>

<?php  
if(isset($_POST['delete'])) {

    if (isset($_SESSION['user_role'])) {
        if ($_SESSION['user_role'] == 'admin') {
            $the_page_id = $_POST['page_id'];
            $query = "DELETE FROM pages WHERE page_id = {$the_page_id} ";
            $delete_query = mysqli_query($connection, $query);
            header("Location: pages.php");
        }
    }
}
?>
<?php

if(isset($_GET['pg_id'])) {
  $the_page_id = $_GET['pg_id'];
}

$query = "SELECT * FROM pages WHERE page_id = $the_page_id";
$select_pages_by_id = mysqli_query($connection, $query);  

  while($row = mysqli_fetch_assoc($select_pages_by_id)) {
      $page_id = $row['page_id'];
      $page_title = $row['page_title'];
      $page_subtitle = $row['page_subtitle'];
      $page_image = $row['page_image'];
      $page_content = $row['page_content'];
      $page_user = $row['page_user'];
      $page_status = $row['page_status'];
  }

  if(isset($_POST['update_page'])) {
    $page_title = $_POST['page_title'];
    $page_subtitle = $_POST['page_subtitle'];
    $page_image = $_FILES['image']['name'];
    $page_image_temp = $_FILES['image']['tmp_name'];
    $page_content = $_POST['page_content'];
    $page_user = $_POST['page_user'];
    $page_status = $_POST['page_status'];
    
    move_uploaded_file($page_image_temp, "../images/$page_image");

    if(empty($page_image)) {
      $query = "SELECT * FROM pages WHERE page_id = $the_page_id ";
      $select_image = mysqli_query($connection, $query);

      while ($row = mysqli_fetch_array($select_image)) {
        $page_image = $row['page_image'];
      }
    }

    $query = "UPDATE pages SET ";
    $query .= "page_title = '{$page_title}', ";
    $query .= "page_subtitle = '{$page_subtitle}', ";
    $query .= "page_image = '{$page_image}', ";
    $query .= "page_content = '{$page_content}', ";
    $query .= "page_user = '{$page_user}', ";
    $query .= "page_status = '{$page_status}' ";
    $query .= "WHERE page_id = {$the_page_id} ";

    $update_page = mysqli_query($connection, $query);

    confirmQuery($update_page);

    echo "<p class='bg-success'>Page updated: <a href='../page.php?pg_id={$the_page_id}'>View Page</a> | <a href='pages.php'>Edit more pages</a></p>";
  }
?>

<form action="" method="post" enctype="multipart/form-data">    
     
     
      <div class="form-group">
        <label for="title">Page Title</label>
        <input value="<?php echo $page_title; ?>" type="text" class="form-control" name="page_title">
      </div>

      <div class="form-group">
        <label for="title">Page Subtitle</label>
        <input value="<?php echo $page_subtitle; ?>" type="text" class="form-control" name="page_subtitle">
      </div>

      <div class="form-group">
         <label for="page_image">Page Image</label>
         <img src="../images/<?php echo $page_image; ?>" alt="" width="100" style="display: block;">
         <input type="file" name="image">
      </div>

      <div class="form-group">
        <label for="title">Page Author</label>
        <select name="page_user" class="form-control" style="width: 25%">
          <option value="<?php echo $page_user; ?>"><?php echo $page_user; ?></option>
        <?php

        $query = "SELECT * FROM users ";
        $select_users = mysqli_query($connection, $query); 

        confirmQuery($select_users);

        while($row = mysqli_fetch_assoc($select_users)) {
            $user_id = $row['user_id'];
            $username = $row['username'];  

            echo "<option value='{$username}'>{$username}</option>";
        }
        ?>
        </select>
      </div>

      <div class="form-group">
         <label for="title">Page Status</label>
         <select name="page_status">
            <option value="<?php echo $page_status; ?>"><?php echo $page_status; ?></option>
            <?php

            if($page_status == 'published') {
              echo "<option value='draft'>draft</option>";
            } else {
              echo "<option value='published'>publish</option>";
            }

            ?>
         </select>
      </div>
      
      <div class="form-group">
         <label for="page_content">Page Content</label>
         <textarea class="form-control" name="page_content" id="body" cols="30" rows="10"><?php echo $page_content; ?></textarea>
      </div>
      
      <div class="form-group">
         <input class="btn btn-primary" type="submit" name="update_page" value="Update Page">
      </div>


</form>
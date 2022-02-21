<?php

if(isset($_POST['create_page'])) {

  $page_title = escape($_POST['title']);
  $page_subtitle = escape($_POST['subtitle']);

  $page_image = escape($_FILES['image']['name']);
  $page_image_temp = escape($_FILES['image']['tmp_name']);

  $page_user = escape($_POST['page_user']);
  $page_status = escape($_POST['page_status']);
  $page_content = escape($_POST['page_content']);

  move_uploaded_file($page_image_temp, "../images/$page_image");

  $query = "INSERT INTO pages(page_title, page_subtitle, page_image, page_content, page_user, page_status) ";
  $query .= "VALUES('$page_title', '$page_subtitle', '$page_image', '$page_content', '$page_user', '$page_status')";

  $create_page_query = mysqli_query($connection, $query);

  confirmQuery($create_page_query);

  $the_page_id = mysqli_insert_id($connection);

  echo "<p class='bg-success'>Page added: <a href='../page.php?pg_id={$the_page_id}'>View Page</a> | <a href='pages.php?source=add_page'>Add another page</a></p>";
}

?>




<form action="" method="post" enctype="multipart/form-data">    
     
     
      <div class="form-group">
        <label for="title">Page Title</label>
        <input type="text" class="form-control" name="title">
      </div>

      <div class="form-group">
        <label for="title">Page Subtitle</label>
        <input type="text" class="form-control" name="subtitle">
      </div>

      <div class="form-group">
         <label for="page_image">Page Image</label>
         <input type="file"  name="image">
      </div>

      <div class="form-group">
        <label for="title">Page Author</label>
        <select name="page_user" class="form-control" style="width: 25%">
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
         <select class="form-control" style="width: 25%" name="page_status">
           <option value="draft">Select Option</option>
           <option value="published">Published</option>
           <option value="draft">Draft</option>
         </select>
      </div>
      
      <div class="form-group">
         <label for="page_content">Page Content</label>
         <textarea class="form-control" name="page_content" id="body" cols="30" rows="10"></textarea>
      </div>
      
      <div class="form-group">
         <input class="btn btn-primary" type="submit" name="create_page" value="Publish Page">
      </div>


</form>
    
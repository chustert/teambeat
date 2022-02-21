<?php

if(isset($_GET['pl_id'])) {
  $the_poll_id = $_GET['pl_id'];
}

$query = "SELECT * FROM polls WHERE poll_id = $the_poll_id";
$select_polls_by_id = mysqli_query($connection, $query);  

  while($row = mysqli_fetch_assoc($select_polls_by_id)) {
      $poll_id = $row['poll_id'];
      $post_user = $row['post_user'];
      $post_title = $row['post_title'];
      $post_category_id = $row['post_category_id'];
      $post_status = $row['post_status'];
      $post_image = $row['post_image'];
      $post_content = $row['post_content'];
      $post_tags = $row['post_tags'];
      $post_comment_count = $row['post_comment_count'];
      $post_date = $row['post_date'];
      $post_views_count = $row['post_views_count'];
  }

  if(isset($_POST['update_post'])) {
    $post_user = $_POST['post_user'];
    $post_title = $_POST['post_title'];
    $post_category_id = $_POST['post_category'];
    $post_status = $_POST['post_status'];
    $post_image = $_FILES['image']['name'];
    $post_image_temp = $_FILES['image']['tmp_name'];
    $post_content = $_POST['post_content'];
    $post_tags = $_POST['post_tags'];
    $post_views_count = $_POST['post_views_count'];
    
    move_uploaded_file($post_image_temp, "../images/$post_image");

    if(empty($post_image)) {
      $query = "SELECT * FROM posts WHERE post_id = $the_post_id ";
      $select_image = mysqli_query($connection, $query);

      while ($row = mysqli_fetch_array($select_image)) {
        $post_image = $row['post_image'];
      }
    }

    $query = "UPDATE posts SET ";
    $query .= "post_title = '{$post_title}', ";
    $query .= "post_category_id = '{$post_category_id}', ";
    $query .= "post_date = now(), ";
    $query .= "post_user = '{$post_user}', ";
    $query .= "post_status = '{$post_status}', ";
    $query .= "post_views_count = '{$post_views_count}', ";
    $query .= "post_tags = '{$post_tags}', ";
    $query .= "post_content = '{$post_content}', ";
    $query .= "post_image = '{$post_image}' ";
    $query .= "WHERE post_id = {$the_post_id} ";

    $update_post = mysqli_query($connection, $query);

    confirmQuery($update_post);

    echo "<p class='bg-success'>Post updated: <a href='../post.php?p_id={$the_post_id}'>View Post</a> | <a href='posts.php'>Edit more posts</a></p>";
  }
?>

<form action="" method="post" enctype="multipart/form-data">    
     
     
      <div class="form-group">
        <label for="title">Post Title</label>
        <input value="<?php echo $post_title; ?>" type="text" class="form-control" name="post_title">
      </div>

      <div class="form-group">
        <label for="post_category">Post Category</label>
        <select name="post_category" id="post_category" style="display: block;">
        <?php

        $query = "SELECT * FROM categories ";
        $select_categories = mysqli_query($connection, $query); 

        confirmQuery($select_categories);

        while($row = mysqli_fetch_assoc($select_categories)) {
            $cat_id = $row['cat_id'];
            $cat_title = $row['cat_title'];  

            echo "<option value='{$cat_id}'>{$cat_title}</option>";
        }
        ?>
        </select>
      </div>

      <div class="form-group">
        <label for="title">Post Author</label>
        <select name="post_user" class="form-control" style="width: 25%">
          <option value="<?php echo $post_user; ?>"><?php echo $post_user; ?></option>
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
         <label for="title">Post Status</label>
         <select name="post_status">
            <option value="<?php echo $post_status; ?>"><?php echo $post_status; ?></option>
            <?php

            if($post_status == 'published') {
              echo "<option value='draft'>draft</option>";
            } else {
              echo "<option value='published'>publish</option>";
            }

            ?>
         </select>
      </div>

      <div class="form-group">
         <label for="post_tags">Post Views</label>
         <input value="<?php echo $post_views_count; ?>" type="text" class="form-control" name="post_views_count">
      </div>

      <div class="form-group">
         <label for="post_image">Post Image</label>
         <img src="../images/<?php echo $post_image; ?>" alt="" width="100" style="display: block;">
         <input type="file" name="image">
      </div>

      <div class="form-group">
         <label for="post_tags">Post Tags</label>
         <input value="<?php echo $post_tags; ?>" type="text" class="form-control" name="post_tags">
      </div>
      
      <div class="form-group">
         <label for="post_content">Post Content</label>
         <textarea class="form-control" name="post_content" id="body" cols="30" rows="10"><?php echo $post_content; ?></textarea>
      </div>
      
      <div class="form-group">
         <input class="btn btn-primary" type="submit" name="update_post" value="Update Post">
      </div>


</form>
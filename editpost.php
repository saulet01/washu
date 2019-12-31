<?php
    session_start();
    // -----------------------------------------------------------------
    // Import needed view files and database
    // -----------------------------------------------------------------
    require('views/header.php');
    require('views/navbar.php'); 
    require('config.php');

    if(isset($_POST['editpost'])){

        // Get Values from post form
        $username = $_SESSION['username'];
        $user_id = $_SESSION['id'];
        $title = $_POST['title'];
        $short_desc = $_POST['short_desc'];
        $long_desc = $_POST['long_desc'];
        $category = $_POST['category'];

        // Convert String post id to Integer
        $get_post_id = $_GET['edit'];
        $integer_post_id = (int)$get_post_id;
        
        // Check whether generated token when user logged are actually the same. Otherwise die()
        if(!hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }
    
        // Get filename of image file
        $filename = $_FILES['imageFile']['name'];
    
        // Get date of current zone. For example: 2019-09-23
        date_default_timezone_set('America/Chicago'); 
        $todaysDate = date("Y-m-d");
        
        // If image is uploaded then store in database. Otherwise, image value is NULL
        if($filename){

            // Get content of image file 
            $data = file_get_contents($_FILES['imageFile']['tmp_name']);
            $stmt = $conn->prepare('update posts set title = ?, short_desc = ?, long_desc = ?, date = ?, category = ?, img = ? where id = ?');
            // If query is true then continue. Otherwise, return error
            if(!$stmt){
                echo($conn->error);
            }
            // Bind Parameters
            $stmt->bind_param("ssssssi", $title, $short_desc, $long_desc, $todaysDate, $category, $data, $integer_post_id);
    
            $stmt->execute();
            $stmt->close();
              // If query is successfull redirect to userpost page
            header("Location: userpost.php");
            exit();
        }else{
            $stmt = $conn->prepare('update posts set title = ?, short_desc = ?, long_desc = ?, date = ?, category = ? where id = ?');
            // If query is true then continue. Otherwise, return error
            if(!$stmt){
                echo($conn->error);
            }
            // Bind Parameters
            $stmt->bind_param("sssssi", $title, $short_desc, $long_desc, $todaysDate, $category, $integer_post_id);
    
            $stmt->execute();
            $stmt->close();
            // If query is successfull redirect to userpost page
            header("Location: userpost.php");
            exit();
        }
    } 

    // Get String Post ID and then convert it to Int type
    $editPostId = $_GET['edit'];
    $intEditId = (int)$editPostId;
    $usernameOfPost = $_SESSION['username'];

    $stmt = $conn->prepare('select id, title, short_desc, long_desc, date, category, img from posts where id = ? and username = ?');
    // Bind parameters
    $stmt->bind_param('is', $intEditId, $usernameOfPost);
    $stmt->execute();
?>


<div class="container">
    <h1 class="display-4 mt-5 text-center">Edit Post</h1>
    <hr>
    <?php
    // -----------------------------------------------------------------
    // Bind query results for all posts and iterate over each post
    // -----------------------------------------------------------------
    $stmt->bind_result($id_post, $titlePost, $short_description, $long_description, $datePost, $categoryPost, $imgPost);
    while($stmt->fetch()) :?>

    <!-- Edit Post Form -->
    <div class="row">
        <div class="col-12 col-lg-12">
            <form action="" class="mb-5" method="post" enctype="multipart/form-data">
                <input type="hidden" name="editpost" value="1">
                <!-- CSRF tokens are passed -->
                <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" class="form-control" placeholder="Enter Title" name="title" value="<?php echo htmlentities($titlePost); ?>">
                </div>
                <div class="form-group">
                    <label for="short-desc">Short Description</label>
                    <input type="text" class="form-control" placeholder="Enter Short Description" name="short_desc" value="<?php echo htmlentities($short_description); ?>">
                    <small class="form-text text-muted">No more than 255 characters</small>
                </div>
                <div class="form-group">
                    <label for="long-desc">Text:</label>
                    <textarea name="long_desc" cols="30" rows="5" class="form-control"><?php echo htmlentities($long_description); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select class="form-control" name="category">
                        <?php echo '<option selected>'. htmlentities($categoryPost) .'</option>'?>
                        <option>Other</option>
                        <option>Sport</option>
                        <option>Education</option>
                        <option>Technology</option>
                        <option>Science</option>
                        <option>Politics</option>
                        <option>Travel</option>
                        <option>Culture</option>
                        <option>Business</option>
                        <option>Health</option>
                        <option>Music</option>
                        <option>Entertainment & Arts</option>
                        <option>Food</option>
                    </select>
                </div>
                <!-- If image is not in the database, then put default WashU Times image -->
                <?php 
                if ($imgPost == null){
                    echo '<img src="img/washu.jpg" alt="WashU Image" class="img-thumbnail" width="300"/>';
                }else{
                    echo '<img src="data:image/jpeg;base64,'.base64_encode($imgPost).'" alt="Post Images" class="img-thumbnail" width="300" />';
                }
                ?>
                <!-- Upload Image Form -->
                <div class="form-group">
                    <label for="image" class="mt-3">Image:</label>
                    <input name="imageFile" type="file" />
                    <small class="form-text text-muted">Image file size must be less than 16MB</small>
                </div>
                <button type="submit" class="btn btn-primary">Edit</button>
            </form>
        </div>
    </div>
    <!-- Stop Iterating -->
    <?php endwhile; ?>
</div>
<?php
    // -----------------------------------------------------------------
    // Import footer
    // ----------------------------------------------------------------- 
    require('views/footer.php'); 
?>
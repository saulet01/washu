<?php
    session_start();
    // -----------------------------------------------------------------
    // Import needed view files and database
    // -----------------------------------------------------------------
    require('views/header.php');
    require('views/navbar.php'); 
    require('config.php');

    if(isset($_POST['createpost'])){
        // Get values from create post form
        $username = $_SESSION['username'];
        $user_id = $_SESSION['id'];
        $title = $_POST['title'];
        $short_desc = $_POST['short_desc'];
        $long_desc = $_POST['long_desc'];
        $category = $_POST['category'];

        // Check whether generated token when user logged are actually the same. Otherwise die()
        if(!hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }

        // Filename of image file
        $filename = $_FILES['imageFile']['name'];

        // Get date of current zone. For example: 2019-09-23
        date_default_timezone_set('America/Chicago'); 
        $todaysDate = date("Y-m-d");

         // If image is uploaded then store in database. Otherwise, image value is NULL
        if($filename){
            $data = file_get_contents($_FILES['imageFile']['tmp_name']);
            $stmt = $conn->prepare('insert into posts(user_id, username, title, short_desc, long_desc, date, category, img) values (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param("isssssss", $user_id, $username, $title, $short_desc, $long_desc, $todaysDate, $category, $data);
        }else{
            $stmt = $conn->prepare('insert into posts(user_id, username, title, short_desc, long_desc, date, category) values (?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param("issssss", $user_id, $username, $title, $short_desc, $long_desc, $todaysDate, $category);
        }

        $stmt->execute();
        $stmt->close();
        header("Location: userpost.php");
        exit();
    } 
?>


<div class="container">
    <!-- Create Post form -->
    <h1 class="display-4 mt-5 text-center">Create Post</h1>
    <hr>
    <div class="row">
        <div class="col-12 col-lg-12">
            <form class="mb-5" method="post" enctype="multipart/form-data">
                <input type="hidden" name="createpost" value="1">
                <!-- CSRF tokens are passed -->
                <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                <div class="form-group">
                    <label>Title:</label>
                    <input type="text" class="form-control" placeholder="Enter Title" name="title">
                </div>
                <div class="form-group">
                    <label>Short Description</label>
                    <input type="text" class="form-control" placeholder="Enter Short Description" name="short_desc">
                    <small class="form-text text-muted">No more than 255 characters</small>
                </div>
                <div class="form-group">
                    <label>Text:</label>
                    <textarea name="long_desc" cols="30" rows="5" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label>Category:</label>
                    <select class="form-control" name="category">
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
                <div class="form-group">
                    <!-- Upload Image Form -->
                    <label>Image:</label>
                    <input name="imageFile" type="file" />
                    <small class="form-text text-muted">Image file size must be less than 16MB</small>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>

<?php 
    // -----------------------------------------------------------------
    // Import footer
    // -----------------------------------------------------------------
    require('views/footer.php'); 
?>
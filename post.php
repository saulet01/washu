<?php
session_start();
// -----------------------------------------------------------------
// Import needed view files and database
// -----------------------------------------------------------------
require('views/header.php');
require('views/navbar.php'); 
require('config.php');

// -----------------------------------------------------------------
// Query to write a comment
// -----------------------------------------------------------------
if(isset($_POST['comment'])){
    if(isset($_SESSION['username'])){
        $postID = $_GET['id'];
        $user_id = $_SESSION['id'];
        $username = $_SESSION['username'];

        // Get comment input value
        $commentInfo = $_POST['comment'];

        // Get right date and time for user. For example: 2019-09-23 10:05:10
        date_default_timezone_set('America/Chicago'); 
        $todaysDate = date("Y-m-d H:i:s");
        
        // Check whether generated token when user logged are actually the same. Otherwise die()
        if(!hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }
    
        // Query
        $stmt = $conn->prepare('insert into comments (post_id, username, user_id, comment, date) values (?, ?, ?, ?, ?) ');
        // If query is true then continue. Otherwise, return error
        if(!$stmt){
            echo($conn->error);
        }
        $stmt->bind_param('isiss', $postID, $username, $user_id, $commentInfo, $todaysDate);
        // If query is true then generate simple toast successful notification
        if($stmt->execute()){
            echo '
            <div class="position-absolute w-100 mt-1 mr-4">
                <div class="toast ml-auto" role="alert" data-delay="900" data-autohide="false">
                    <div class="toast-header">
                        <strong class="mr-auto text-success">Successful!</strong>
                        <button type="button" class="close" data-dismiss="toast" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="toast-body">
                        Your comment added!
                    </div>
                </div>
            </div>';
        }
    }
    else{
        // If user tries to write a comment without being authorized. Alert would be triggered!
        echo "<script type='text/javascript'>alert('Sorry, you need to authorize. Please login or register!');</script>";
    }
}

if(isset($_GET['id'])){
    $postID = $_GET['id'];
    
    // -------- Query For Posts
    $stmt = $conn->prepare('select username, title, short_desc, long_desc, date, category, img from posts where id = ?');
    if(!$stmt){
        echo($conn->error);
    }
    $stmt->bind_param('i', $postID);
    $stmt->execute();
}
?>

<div class="container">

    <?php     
    // -----------------------------------------------------------------
    // Bind query results for all posts and iterate over each post
    // -----------------------------------------------------------------
    $stmt->bind_result($author, $title, $short_description, $long_description, $date, $category, $img);
    while($stmt->fetch()):
    ?>

    <h1 class="mt-5 mb-4 text-center"><?php echo htmlentities($title);?></h1>
    <hr>
    <div class="row">
        <div class="col-12 col-lg-8 mx-auto d-block">
            <!-- If image is not in the database, then put default WashU Times image -->
            <?php 
                if ($img == null){
                    echo '<img src="img/washu.jpg" alt="WashU Image" class="img-thumbnail" />';
                }else{
                    echo '<img src="data:image/jpeg;base64,'.base64_encode($img).'" alt="Post Images" class="img-thumbnail"/>';
                }
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-lg-12 mt-4">
            <hr>
            <p class="lead">
            <?php echo htmlentities($short_description); ?>
            </p>
            <p><?php echo htmlentities($long_description); ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-lg-12">
            <hr>
            <ul class="list-inline">
                <li class="list-inline-item text-muted mr-5 "><i class="fas fa-user"></i> <em><?php echo htmlentities($author); ?></em></li>
                <li class="list-inline-item text-muted mr-5"><i class="far fa-clock"></i> <em><?php echo htmlentities($date); ?></em></li>
                <li class="list-inline-item text-muted"><em><?php echo htmlentities($category); ?></em></li>
            </ul>
            <hr>
        </div>
    </div>
    <!-- Stop Iterating -->
    <?php endwhile; ?>
    <div class="input-group mb-4">
        <div class="input-group-prepend">
            <span class="input-group-text" id="addon-wrapping">Share News Link:</span>
        </div>
        <input type="text" class="form-control" value="<?php echo"http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
    </div>
    <div class="card">
        <div class="card-header">
            Write Comment
        </div>
        <div class="card-body">
            <form method="post">
                <!-- CSRF tokens are passed -->
                <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                <input type="text" name="comment" class="form-control"/>
                <button type="submit" class="btn btn-primary mt-3">Submit</button>
            </form>
        </div>
    </div>
    <?php 
    if(isset($_GET['id'])){
        $postID = $_GET['id'];
        
        // -------- Query For Posts
        $stmt = $conn->prepare('select username, comment, date from comments where post_id=? order by date desc');
        // If query is true then continue. Otherwise, return error
        if(!$stmt){
            echo($conn->error);
        }
        $stmt->bind_param('i', $postID);
        $stmt->execute();
    }    
    ?>
    <?php 
        // -----------------------------------------------------------------
        // Bind query results for all posts and iterate over each post
        // -----------------------------------------------------------------
        $stmt->bind_result($userComment, $comment, $commentDate);
        while($stmt->fetch()):
    ?>
    <div class="row mb-2">
        <div class="col">
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlentities($userComment); ?> </h5>
                <p class="card-text"><?php echo htmlentities($comment); ?></p>
            </div>
            <div class="card-footer text-muted">
            <i class="far fa-clock"></i> 
                <?php echo htmlentities($commentDate); ?>
            </div>
        </div>
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
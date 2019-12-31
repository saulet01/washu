<?php
    session_start();
    // -----------------------------------------------------------------
    // Import needed view files and database
    // -----------------------------------------------------------------
    require('views/header.php');
    require('views/navbar.php'); 
    require('config.php');

    if(isset($_POST['editComment'])){
        // Get Edit Comments From values 
        $commentID = $_GET['editcomment'];
        // Convert comment id to int type
        $intCommentID = (int)$commentID;
        $commenData = $_POST['commentData'];
        $username = $_SESSION['username'];
        $user_id = $_SESSION['id'];

        // Get date of current zone. For example: 2019-09-23
        date_default_timezone_set('America/Chicago'); 
        $todaysDate = date("Y-m-d H:i:s");

        // Check whether generated token when user logged are actually the same. Otherwise die()
        if(!hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }

        $stmt = $conn->prepare('update comments set comment = ?, date = ? where id = ?');
        // If query is true then continue. Otherwise, return error
        if(!$stmt){
            echo($conn->error);
        }
        $stmt->bind_param("ssi", $commenData, $todaysDate, $intCommentID);
    
        $stmt->execute();
        $stmt->close();
        header("Location: usercomment.php");
        exit();

    }

    $getCommentId = $_GET['editcomment'];
    // convert comment id to int type
    $intGetCommentID = (int)$getCommentId;
    $commentUser = $_SESSION['username'];
    $stmt = $conn->prepare('select id, comment from comments where id = ? and username = ?');
    $stmt->bind_param('is', $intGetCommentID, $commentUser);
    $stmt->execute();
?>

<div class="container">
    <h1 class="display-4 mt-5 text-center">Edit Comment</h1>
    <hr>
    <?php
     // -----------------------------------------------------------------
    // Bind query results for all user comments and iterate over each post
    // -----------------------------------------------------------------
    $stmt->bind_result($commentID, $commentData);
    while($stmt->fetch()) :?>
    <div class="row">
        <div class="col-12">
            <form method="post">
                <!-- CSRF tokens are passed -->
                <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                <div class="form-group">
                    <input type="hidden" name="editComment" value="1">
                    <label for="title">Comment:</label>
                    <input type="text" class="form-control" name="commentData" value="<?php echo htmlentities($commentData); ?>">
                </div>
                <button class="btn btn-primary">Edit</button>
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
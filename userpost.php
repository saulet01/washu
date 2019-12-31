<?php
// -----------------------------------------------------------------
// Import needed view files and database
// -----------------------------------------------------------------
session_start();
require('views/header.php');
require('views/navbar.php');
require('config.php');

$user = $_SESSION['username'];
$userID = $_SESSION['id'];

if (isset($_GET['delete'])) {
    $deleteID = $_GET['delete'];
    // Convert post id to int type
    $intPostId = (int) $deleteID;
    $stmt = $conn->prepare('delete from posts where id=?');
    $stmt->bind_param('i', $intPostId);
    // If query is true then generate simple toast successful notification
    if ($stmt->execute()) {
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
                    Your post deleted!
                </div>
            </div>
        </div>';
    }
}

// -----------------------------------------------------------------
// Query for user post 
// -----------------------------------------------------------------
$stmt = $conn->prepare('select id, username, title, short_desc, long_desc, date, category, img from posts where user_id = ? order by id desc');
// If query is true then continue. Otherwise, return error
if (!$stmt) {
    echo ($conn->error);
}
$stmt->bind_param('i', $userID);
$stmt->execute();
?>

<!-- My Post Form -->
<div class="container">
    <h1 class="display-4 text-center mt-5">My Posts</h1>
    <ul class="nav justify-content-end">
        <li class="nav-item mr-2">
            <a href="http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/createpost.php" class="btn btn-primary btn-lg"><i class="fas fa-pen fa-sm"></i> Create Post</a>
        </li>
    </ul>
    <hr>
    <?php
    // -----------------------------------------------------------------
    // Bind query results for all user posts and iterate over each post
    // -----------------------------------------------------------------
    $stmt->bind_result($postid, $author, $title, $short_description, $long_description, $date, $category, $img);

    while ($stmt->fetch()) :
    ?>
        <div class="row shadow mb-4 mt-4 posts">
            <div class="col-lg-4 d-flex align-items-center justify-content-center">
                <!-- If image is not in the database, then put default WashU Times image -->
                <?php
                if ($img == null) {
                    echo '<img src="img/washu.jpg" alt="WashU Image" class="img-thumbnail" />';
                } else {
                    echo '<img src="data:image/jpeg;base64,' . base64_encode($img) . '" alt="Post Images" class="img-thumbnail"/>';
                }
                ?>
            </div>
            <div class="col-lg-8">
                <ul class="nav justify-content-end">
                    <li class="nav-item mr-2">
                        <a href="http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/editpost.php?edit=<?php echo htmlentities($postid); ?>" class="btn btn-info btn-sm mb-2">Edit</a>
                    </li>
                    <li class="nav-item">
                        <a href="http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/?delete=<?php echo htmlentities($postid); ?>" class="btn btn-danger btn-sm mb-2">Delete</a>
                    </li>
                </ul>
                <h5><?php echo htmlentities($title); ?></h5>
                <ul class="list-inline">
                    <li class="list-inline-item text-muted mr-5"><i class="fas fa-user"></i> <em><?php echo htmlentities($author); ?></em></li>
                    <li class="list-inline-item text-muted mr-5"><i class="far fa-clock"></i> <em><?php echo htmlentities($date); ?></em></li>
                    <li class="list-inline-item text-muted"><em><?php echo htmlentities($category); ?></em></li>
                </ul>
                <p class="lead"><?php echo htmlentities($short_description); ?></p>
                <p><?php echo htmlentities($long_description); ?></p>
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
<?php

session_start();
// -----------------------------------------------------------------
// Import needed view files and database
// -----------------------------------------------------------------
require('views/header.php');
require('views/navbar.php');
require('config.php');

if (isset($_GET['remove'])) {
    $commID = $_GET['remove'];
    // Convert comment id to int type
    $intCommentID = (int) $commID;
    $stmt = $conn->prepare('delete from comments where id = ?');
    $stmt->bind_param('i', $intCommentID);
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
                    Your comment deleted!
                </div>
            </div>
        </div>';
    }
}

$userID = $_SESSION['id'];
$stmt = $conn->prepare('select id, comment, date from comments where user_id = ? order by date desc');
// If query is true then continue. Otherwise, return error
if (!$stmt) {
    echo ($conn->error);
}
$stmt->bind_param('i', $userID);
$stmt->execute();
?>

<div class="container">
    <h1 class="display-4 text-center mt-5">My Comments</h1>
    <hr>
    <?php
    // -----------------------------------------------------------------
    // Bind query results for all user comments and iterate over each post
    // -----------------------------------------------------------------
    $stmt->bind_result($commentID, $userComment, $dateComment);
    while ($stmt->fetch()) :
    ?>
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card mt-3">
                    <div class="card-header">
                        <ul class="nav nav-pills card-header-pills justify-content-end">
                            <li class="nav-item mr-2">
                                <a class="nav-link btn-sm btn-info" href="http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/editcomment.php?editcomment=<?php echo htmlentities($commentID); ?>">Edit</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-sm btn-danger" href="http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/?remove=<?php echo htmlentities($commentID); ?>">Delete</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlentities($userComment); ?></h5>
                    </div>
                    <div class="card-footer text-muted">
                        <?php echo htmlentities($dateComment); ?>
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
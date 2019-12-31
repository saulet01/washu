<?php
session_start();
// -----------------------------------------------------------------
// Import needed view files and database
// -----------------------------------------------------------------
require('views/header.php');
require('views/navbar.php');
require('config.php');

$category = $_GET['category'];

// -------- Query For Posts
$stmt = $conn->prepare('select id,username, title, short_desc, date, category, img from posts where category = ? order by id desc');
// If query is true then continue. Otherwise, return error
if (!$stmt) {
    echo ($conn->error);
}
$stmt->bind_param("s", $category);
$stmt->execute();
?>
<!-- Jumbtron with chosen catogory topic -->
<div class="jumbotron jumbotron-fluid text-center shadow">
    <div class="container washu">
        <h1 class="display-1 jumb-text"><?php echo htmlentities($category); ?></h1>
    </div>
</div>

<div class="container mt-3">

    <?php
    // -----------------------------------------------------------------
    // Bind query results for all user posts and iterate over each post
    // -----------------------------------------------------------------
    $stmt->bind_result($postid, $author, $title, $short_desc, $datePublished, $category, $img);
    while ($stmt->fetch()) : ?>

        <div class="row posts shadow mb-4">
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
                <h4><?php echo $title ?></h4>
                <ul class="list-inline">
                    <li class="list-inline-item text-muted mr-5"><i class="fas fa-user"></i> <em><?php echo htmlentities($author); ?></em></li>
                    <li class="list-inline-item text-muted mr-5"><i class="far fa-clock"></i> <em><?php echo htmlentities($datePublished); ?></em></li>
                    <li class="list-inline-item text-muted"><em><?php echo htmlentities($category); ?></em></li>
                </ul>
                <p class="lead"><?php echo htmlentities($short_desc); ?></p>
                <!-- Redirect to the post -->
                <a href="http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/post.php?id=<?php echo htmlentities($postid); ?>" class="btn btn-danger">Read More</a>
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
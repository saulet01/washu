<?php
// -----------------------------------------------------------------
// Import needed view files and database
// -----------------------------------------------------------------
session_start();
require('views/header.php');
require('views/navbar.php');
require('views/jumbotron.php');
require('config.php');

// -----------------------------------------------------------------
// Query for all posts
// -----------------------------------------------------------------
$stmt = $conn->prepare('select id,username, title, short_desc, date, category, img from posts order by id desc');
$stmt->execute();
?>

<!-- Category of posts links -->
<form class="list-group list-group-horizontal-xl justify-content-center">
    <a href="http://http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/category.php?category=Sport" class="list-group-item category">Sport</a>
    <a href="http://http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/category.php?category=Education" class="list-group-item category">Education</a>
    <a href="http://http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/category.php?category=Technology" class="list-group-item category">Technology</a>
    <a href="http://http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/category.php?category=Science" class="list-group-item category">Science</a>
    <a href="http://http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/category.php?category=Politics" class="list-group-item category">Politics</a>
    <a href="http://http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/category.php?category=Travel" class="list-group-item category">Travel</a>
    <a href="http://http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/category.php?category=Business" class="list-group-item category">Business</a>
    <a href="http://http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/category.php?category=Health" class="list-group-item category">Health</a>
    <a href="http://http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/category.php?category=Entertainment" class="list-group-item category">Entertainment</a>
    <a href="http://http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/category.php?category=Other" class="list-group-item category">Other</a>
</form>
<div class="container mt-3">

    <?php
    // -----------------------------------------------------------------
    // Bind query results for all posts and iterate over each post
    // -----------------------------------------------------------------
    $stmt->bind_result($postid, $author, $title, $short_desc, $datePublished, $category, $img);

    // Start Iterating
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

                <!-- Output Post Detail Infromation  -->
                <h4><?php echo htmlentities($title); ?></h4>
                <ul class="list-inline">
                    <li class="list-inline-item text-muted mr-5"><i class="fas fa-user"></i> <em><?php echo htmlentities($author); ?></em></li>
                    <li class="list-inline-item text-muted mr-5"><i class="far fa-clock"></i> <em><?php echo htmlentities($datePublished); ?></em></li>
                    <li class="list-inline-item text-muted"><em><?php echo $category ?></em></li>
                </ul>
                <p class="lead"><?php echo htmlentities($short_desc); ?></p>

                <!-- Read More Button that redirects to more detailed posts infomration -->
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
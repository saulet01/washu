    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <a class="navbar-brand" href="/">
            <img src="img/logo.png" width="30" height="30" class="d-inline-block align-top" alt="WashU Logo">
            WashU Times
        </a>
        <?php
        // If user logged in create new admin panel links
        if (isset($_SESSION['username']) && isset($_SESSION['id'])) {
            echo '
                <form class="form-inline">
                    <a class="navbar-brand mr-4" href="http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/">All Posts</a>
                    <a class="navbar-brand mr-4" href="http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/userpost.php">My Posts</a>
                    <a class="navbar-brand" href="http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/usercomment.php">My Comments</a>
                    <a class="navbar-brand ml-2" href="http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/?logout">Logout</a>
                </form>
                ';
        } else {
            echo '<a class="navbar-brand" href="http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/authorization.php">Authorization</a>';
        }
        ?>
    </nav>

    <?php

    // User Logout
    if (isset($_GET['logout'])) {
        unset($_SESSION['username']);
        unset($_SESSION['id']);
        header('Location: http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/');
        exit();
    }

    ?>
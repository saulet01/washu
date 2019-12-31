<?php

session_start();

// -----------------------------------------------------------------
// Import needed view files and database
// -----------------------------------------------------------------
require('views/header.php');
require('views/navbar.php');
require('config.php');

// -----------------------------------------------------------------
// Query to Register
// -----------------------------------------------------------------
if (isset($_POST['register']) && isset($_POST['registerPassword'])) {
    $username = trim($_POST['register']);
    $password = trim($_POST['registerPassword']);

    // Generate salt
    $options = array("cost" => 4);

    //Encrypt password in new PHP7 way
    $hashingPassword = password_hash($password, PASSWORD_BCRYPT, $options);

    $stmt = $conn->prepare('insert into users (username, password) values (?, ?)');

    // Bind Parameters
    $stmt->bind_param("ss", $username, $hashingPassword);

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
                    Now, you are able to log in.
                </div>
            </div>
        </div>';
    }
}

// -----------------------------------------------------------------
// Query to Login 
// -----------------------------------------------------------------
if (isset($_POST['login']) && isset($_POST['loginPassword'])) {
    $username = trim($_POST['login']);
    $password = trim($_POST['loginPassword']);

    $stmt = $conn->prepare('select * from users where username=?');

    // Bind Parameters
    $stmt->bind_param('s', $username);
    $stmt->execute();

    // Bind Results
    $stmt->bind_result($user_id, $user_name, $pwd_hashpassword);
    $stmt->fetch();

    // Check whether entered password is the same as registered. If true then create user session along with token.
    // After that redirect to main index.php page
    if (password_verify($password, $pwd_hashpassword)) {
        $_SESSION['username'] = $user_name;
        $_SESSION['id'] = $user_id;
        $_SESSION['token'] = bin2hex(random_bytes(32));
        header('Location: http://ec2-3-81-107-156.compute-1.amazonaws.com/~saulet/washu/');
        exit();
    } else {
        echo ('Login Fail');
    }
}

?>


<div class="container">
    <div class="row">
        <!--  Login Form -->
        <div class="col-lg-5 authorization shadow">
            <h1 class="text-center">Login</h1>
            <form action="authorization.php" method="post">
                <div class="form-group">
                    <label for="login">Login:</label>
                    <input type="text" name="login" placeholder="Enter login" class="form-control">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="loginPassword" class="form-control" placeholder="Enter Password">
                </div>
                <button type="submit" class="btn btn-danger">Login</button>
            </form>
        </div>
        <div class="col-lg-2"></div>
        <!-- Register Form -->
        <div class="col-lg-5 authorization shadow">
            <h1 class="text-center">Register</h1>
            <form action="authorization.php" method="post">
                <div class="form-group">
                    <label for="registtration">Register:</label>
                    <input type="text" name="register" placeholder="Enter login" class="form-control">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="registerPassword" class="form-control" placeholder="Enter Password">
                </div>
                <button type="submit" class="btn btn-danger">Register</button>
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
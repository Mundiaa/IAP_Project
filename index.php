<?php

require_once "conf.php";


if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notez Wiz</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <a class="navbar-brand" href="#">
        <h3 class="mb-0">Notez Wiz</h3>
    </a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="btn btn-outline-light mr-2" href="#" id="show-register">Sign Up</a>
            </li>
            <li class="nav-item">
                <a class="btn btn-light" href="#" id="show-login">Log In</a>
            </li>
        </ul>
    </div>
</nav>

<div class="jumbotron jumbotron-fluid text-center text-white" style="background: linear-gradient(to right, #6a11cb, #2575fc);">
    <div class="container">
        <h1 class="display-4 font-weight-bold">Welcome to Notez Wiz!</h1>
        <p class="lead">Your simple and secure way to manage notes.</p>
    </div>
</div>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div id="login-form-container" class="card shadow-lg p-4 mb-4">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Log In to Your Account</h3>
                    <form action="login.php" method="post">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Log In</button>
                    </form>
                </div>
            </div>

            <div id="register-form-container" class="card shadow-lg p-4 mb-4" style="display: none;">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Create Your Notez Wiz Account</h3>
                    <form action="register.php" method="post">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Sign Up</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="asset/js/script.js"></script>
</body>
</html>
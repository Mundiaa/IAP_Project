<?php
session_start();

require_once "conf.php";
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    //Check if email exists
    $sql = "SELECT id, fullname, password FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $fullname, $hashed_password);
            $stmt->fetch();

            //Verify password
            if (password_verify($password, $hashed_password)) {
                $_SESSION["loggedin"] = true;
                $_SESSION["user_id"]  = $id;
                $_SESSION["fullname"] = $fullname;

                header("Location: dashboard.php");
                exit;
            } else {
                echo "⚠️ Invalid password.";
            }
        } else {
            echo "⚠️ No account found with that email.";
        }
        $stmt->close();
    } else {
        die("Database error: " . $conn->error);
    }
    $conn->close();
}
?>


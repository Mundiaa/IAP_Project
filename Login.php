<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

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
            if ($hashed_password !== null && password_verify($password, $hashed_password)) {
                $_SESSION["loggedin"] = true;
                $_SESSION["user_id"]  = $id;
                $_SESSION["fullname"] = $fullname;

                // Track login interaction
                if (isset($conn)) {
                    $table_check = $conn->query("SHOW TABLES LIKE 'user_interactions'");
                    if ($table_check->num_rows > 0) {
                        $track_stmt = $conn->prepare("INSERT INTO user_interactions (user_id, interaction_type, interaction_details, page_url) VALUES (?, 'login', 'User logged in', ?)");
                        $url = $_SERVER['HTTP_REFERER'] ?? 'Login.php';
                        $track_stmt->bind_param("is", $id, $url);
                        $track_stmt->execute();
                        $track_stmt->close();
                    }
                }

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


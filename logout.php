<?php
// Start or resume the session
session_start();

// Unset all session variables
session_unset();

// Destroy the session completely
session_destroy();

// Redirect user back to index page
header("Location: index.php");
exit;
?>

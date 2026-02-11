<?php
session_start();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: /myshop/login.php");
exit;
?>

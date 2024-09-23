<?php
include 'connect.php';
// Set the 'admin_id' cookie with an expired time to effectively delete it
setcookie('admin_id', '', time() - 1, '/');
// Redirect the user to the login page after logging out
header('location:../admin/login.php');
?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USJ-R School Management System</title>
    <link rel="stylesheet" href="/phpsite/assets/style.css">
</head>
<body>
<div class="layout-wrapper">
    <header class="top-header">
        <div class="header-left">
            <span class="header-logo">USJ-R</span>
            <span class="header-title">School Management System <span class="header-version">v1.01</span></span>
        </div>
        <div class="header-right">
            <span class="header-user">&#128100; <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
            <a href="/phpsite/auth/logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </header>
    <div class="body-wrapper">

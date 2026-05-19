<?php
session_start();
require 'db.php';
require 'auth.php';
require 'helpers.php';

$auth_error = '';

if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $result = login_user($_POST['username'] ?? '', $_POST['password'] ?? '');
    if (isset($result['error'])) {
        $auth_error = $result['error'];
    } else {
        $_SESSION['logged_in'] = true;
        $_SESSION['username']  = clean_text($_POST['username']);
        $_SESSION['role']      = $result['role'];
        header('Location: home.php');
        exit;
    }
}

if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

if (is_logged_in()) {
    header('Location: home.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USJ-R School Management System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header class="header">
    <div class="header-brand">
        <div class="logo-box">USJ</div>
        <div>
            <div class="sys-title">USJ-R School Management System</div>
            <div class="sys-version">v1.01 &mdash; Academic Administration Portal</div>
        </div>
    </div>
    <div class="header-right">
        <form method="POST" action="index.php" class="header-login">
            <input type="hidden" name="action" value="login">
            <input type="text"     name="username" placeholder="Username" autocomplete="username">
            <input type="password" name="password" placeholder="Password" autocomplete="current-password">
            <button type="submit" class="btn btn-green btn-sm">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        <?php if ($auth_error): ?>
            <span class="header-auth-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo h($auth_error); ?>
            </span>
        <?php endif; ?>
    </div>
</header>

<div class="page-wrap">
    <div class="landing">

        <div class="hero-banner">
            <h1>Welcome to <span>USJ-R</span><br>School Management System</h1>
            <p>Manage your school's operations efficiently — schools, departments, programs, and students all in one place.</p>
        </div>

        <div class="quick-cards">
            <div class="qcard">
                <div class="qcard-icon ic-green"><i class="fas fa-university"></i></div>
                <h3>Schools</h3>
                <p>Manage school information and details.</p>
            </div>
            <div class="qcard">
                <div class="qcard-icon ic-navy"><i class="fas fa-sitemap"></i></div>
                <h3>Departments</h3>
                <p>Organize departments within schools.</p>
            </div>
            <div class="qcard">
                <div class="qcard-icon ic-gold"><i class="fas fa-book-open"></i></div>
                <h3>Programs</h3>
                <p>Manage academic programs and courses.</p>
            </div>
            <div class="qcard">
                <div class="qcard-icon ic-red"><i class="fas fa-user-graduate"></i></div>
                <h3>Students</h3>
                <p>Manage student records and enrollment.</p>
            </div>
        </div>

        <div class="getting-started">
            <h3><i class="fas fa-rocket" style="color:var(--green);"></i> Getting Started</h3>
            <ol>
                <li><strong>Login</strong> using the username and password fields in the header above.</li>
                <li>Use the <strong>sidebar</strong> to navigate between Schools, Departments, Programs, and Students.</li>
                <li><strong>Filter by school or department</strong> before managing departments or programs.</li>
                <li>Use the <strong>Create / Update / Delete</strong> buttons on each table to manage records.</li>
            </ol>
            <p style="margin-top:14px; font-size:.8rem; color:var(--text-muted);">
                <i class="fas fa-info-circle"></i>
                Credentials &mdash; <strong>admin</strong> / admin123 &nbsp;|&nbsp;
                <strong>staff</strong> / staff456 &nbsp;|&nbsp;
                <strong>viewer</strong> / viewer789
            </p>
        </div>

    </div>
</div>

</body>
</html>

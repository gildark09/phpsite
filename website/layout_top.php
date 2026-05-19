<?php
$flash = get_flash();
$tab   = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USJ-R &mdash; <?php echo ucfirst($tab); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header class="header">
    <div class="header-brand">
        <div class="logo-box">USJ-R</div>
        <div>
            <div class="sys-title">USJ-R School Management System</div>
            <div class="sys-version">v1.01 &mdash; Academic Administration Portal</div>
        </div>
    </div>
    <div class="header-right">
        <span class="header-user">
            Logged in as: <strong><?php echo h(current_user()); ?></strong>
            <span style="color:rgba(255,255,255,.4);">(<?php echo h(current_role()); ?>)</span>
        </span>
        <form method="POST" action="index.php">
            <input type="hidden" name="action" value="logout">
            <button type="submit" class="btn btn-red btn-sm">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</header>

<div class="page-wrap">
<div class="app-layout">

    <nav class="sidebar">
        <div class="sidebar-label">Navigation</div>
        <ul class="sidebar-nav">
            <li><a href="home.php"        class="<?php echo $tab === 'home'        ? 'active' : ''; ?>"><i class="fas fa-th-large"></i> Home</a></li>
            <li><a href="schools.php"     class="<?php echo $tab === 'schools'     ? 'active' : ''; ?>"><i class="fas fa-university"></i> Schools</a></li>
            <li><a href="departments.php" class="<?php echo $tab === 'departments' ? 'active' : ''; ?>"><i class="fas fa-sitemap"></i> Departments</a></li>
            <li><a href="programs.php"    class="<?php echo $tab === 'programs'    ? 'active' : ''; ?>"><i class="fas fa-book-open"></i> Programs</a></li>
            <li><a href="students.php"    class="<?php echo $tab === 'students'    ? 'active' : ''; ?>"><i class="fas fa-user-graduate"></i> Students</a></li>
        </ul>
    </nav>

    <main class="main-content">

        <?php if ($flash): ?>
            <div class="flash flash-<?php echo h($flash['type']); ?>">
                <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo h($flash['msg']); ?>
            </div>
        <?php endif; ?>

<?php
require_once "auth/check.php";
require_once "config/db.php";

$schools   = $pdo->query("SELECT COUNT(*) FROM colleges")->fetchColumn();
$depts     = $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();
$programs  = $pdo->query("SELECT COUNT(*) FROM programs")->fetchColumn();
$students  = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
?>
<?php require_once "includes/header.php"; ?>
<?php require_once "includes/sidebar.php"; ?>

<main class="main-content">
    <div class="page-title">Dashboard</div>
    <div class="page-subtitle">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! Here is an overview of the system.</div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo $schools; ?></div>
            <div class="stat-label">Schools</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $depts; ?></div>
            <div class="stat-label">Departments</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $programs; ?></div>
            <div class="stat-label">Programs</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $students; ?></div>
            <div class="stat-label">Students</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Quick Navigation</span>
        </div>
        <p style="color:#4a5568; font-size:14px; margin-bottom:16px;">Use the sidebar to navigate between modules or click a shortcut below.</p>
        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <a href="/phpsite/schools/index.php" class="btn btn-primary">Manage Schools</a>
            <a href="/phpsite/departments/index.php" class="btn btn-primary">Manage Departments</a>
            <a href="/phpsite/programs/index.php" class="btn btn-primary">Manage Programs</a>
            <a href="/phpsite/students/index.php" class="btn btn-primary">Manage Students</a>
        </div>
    </div>
</main>

<?php require_once "includes/footer.php"; ?>

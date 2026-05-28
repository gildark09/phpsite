<?php
require_once "../auth/check.php";
require_once "../config/db.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$back_school = filter_input(INPUT_GET, 'school', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM departments WHERE deptid = ?");
$stmt->execute([$id]);
$dept = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dept) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    $stmt = $pdo->prepare("DELETE FROM departments WHERE deptid = ?");
    $stmt->execute([$id]);
    header("Location: index.php?school=$back_school&msg=deleted");
    exit();
}
?>
<?php require_once "../includes/header.php"; ?>
<?php require_once "../includes/sidebar.php"; ?>

<main class="main-content">
    <div class="breadcrumb">
        <a href="/phpsite/index.php">Home</a> <span>&rsaquo;</span>
        <a href="index.php<?php echo $back_school ? '?school='.$back_school : ''; ?>">Departments</a> <span>&rsaquo;</span> Delete Department
    </div>

    <div class="card confirm-box">
        <div class="confirm-icon">&#9888;</div>
        <div class="confirm-message">Delete this department?</div>
        <div class="confirm-sub">
            You are about to delete: <strong><?php echo htmlspecialchars($dept['deptfullname']); ?></strong><br>
            This action cannot be undone.
        </div>
        <div class="confirm-actions">
            <a href="delete.php?id=<?php echo $id; ?>&school=<?php echo $back_school; ?>&confirm=yes" class="btn btn-danger">Yes, Delete</a>
            <a href="index.php<?php echo $back_school ? '?school='.$back_school : ''; ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>

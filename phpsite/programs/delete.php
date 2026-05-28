<?php
require_once "../auth/check.php";
require_once "../config/db.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$back_dept = filter_input(INPUT_GET, 'dept', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM programs WHERE progid = ?");
$stmt->execute([$id]);
$prog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prog) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    $stmt = $pdo->prepare("DELETE FROM programs WHERE progid = ?");
    $stmt->execute([$id]);
    header("Location: index.php?dept=$back_dept&msg=deleted");
    exit();
}
?>
<?php require_once "../includes/header.php"; ?>
<?php require_once "../includes/sidebar.php"; ?>

<main class="main-content">
    <div class="breadcrumb">
        <a href="/phpsite/index.php">Home</a> <span>&rsaquo;</span>
        <a href="index.php<?php echo $back_dept ? '?dept='.$back_dept : ''; ?>">Programs</a> <span>&rsaquo;</span> Delete Program
    </div>

    <div class="card confirm-box">
        <div class="confirm-icon">&#9888;</div>
        <div class="confirm-message">Delete this program?</div>
        <div class="confirm-sub">
            You are about to delete: <strong><?php echo htmlspecialchars($prog['progfullname']); ?></strong><br>
            This action cannot be undone.
        </div>
        <div class="confirm-actions">
            <a href="delete.php?id=<?php echo $id; ?>&dept=<?php echo $back_dept; ?>&confirm=yes" class="btn btn-danger">Yes, Delete</a>
            <a href="index.php<?php echo $back_dept ? '?dept='.$back_dept : ''; ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>

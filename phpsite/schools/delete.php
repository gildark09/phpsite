<?php
require_once "../auth/check.php";
require_once "../config/db.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM colleges WHERE collid = ?");
$stmt->execute([$id]);
$school = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$school) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    $stmt = $pdo->prepare("DELETE FROM colleges WHERE collid = ?");
    $stmt->execute([$id]);
    header("Location: index.php?msg=deleted");
    exit();
}
?>
<?php require_once "../includes/header.php"; ?>
<?php require_once "../includes/sidebar.php"; ?>

<main class="main-content">
    <div class="breadcrumb">
        <a href="/phpsite/index.php">Home</a> <span>&rsaquo;</span>
        <a href="index.php">Schools</a> <span>&rsaquo;</span> Delete School
    </div>

    <div class="card confirm-box">
        <div class="confirm-icon">&#9888;</div>
        <div class="confirm-message">Delete this school?</div>
        <div class="confirm-sub">
            You are about to delete: <strong><?php echo htmlspecialchars($school['collfullname']); ?></strong><br>
            This action cannot be undone.
        </div>
        <div class="confirm-actions">
            <a href="delete.php?id=<?php echo $id; ?>&confirm=yes" class="btn btn-danger">Yes, Delete</a>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>

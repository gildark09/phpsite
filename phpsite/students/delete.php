<?php
require_once "../auth/check.php";
require_once "../config/db.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM students WHERE studid = ?");
$stmt->execute([$id]);
$stud = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$stud) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    $stmt = $pdo->prepare("DELETE FROM students WHERE studid = ?");
    $stmt->execute([$id]);
    header("Location: index.php?msg=deleted");
    exit();
}

$fullname = htmlspecialchars($stud['studlastname']) . ', ' . htmlspecialchars($stud['studfirstname']);
?>
<?php require_once "../includes/header.php"; ?>
<?php require_once "../includes/sidebar.php"; ?>

<main class="main-content">
    <div class="breadcrumb">
        <a href="/phpsite/index.php">Home</a> <span>&rsaquo;</span>
        <a href="index.php">Students</a> <span>&rsaquo;</span> Delete Student
    </div>

    <div class="card confirm-box">
        <div class="confirm-icon">&#9888;</div>
        <div class="confirm-message">Delete this student?</div>
        <div class="confirm-sub">
            You are about to delete: <strong><?php echo $fullname; ?></strong> (ID: <?php echo htmlspecialchars($stud['studid']); ?>)<br>
            This action cannot be undone.
        </div>
        <div class="confirm-actions">
            <a href="delete.php?id=<?php echo $id; ?>&confirm=yes" class="btn btn-danger">Yes, Delete</a>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>

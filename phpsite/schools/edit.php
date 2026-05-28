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

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collfullname  = filter_input(INPUT_POST, 'collfullname', FILTER_SANITIZE_SPECIAL_CHARS);
    $collshortname = filter_input(INPUT_POST, 'collshortname', FILTER_SANITIZE_SPECIAL_CHARS);

    if ($collfullname && $collshortname) {
        $stmt = $pdo->prepare("UPDATE colleges SET collfullname = ?, collshortname = ? WHERE collid = ?");
        $stmt->execute([$collfullname, $collshortname, $id]);
        header("Location: index.php?msg=updated");
        exit();
    } else {
        $error = "All fields are required.";
    }
}
?>
<?php require_once "../includes/header.php"; ?>
<?php require_once "../includes/sidebar.php"; ?>

<main class="main-content">
    <div class="breadcrumb">
        <a href="/phpsite/index.php">Home</a> <span>&rsaquo;</span>
        <a href="index.php">Schools</a> <span>&rsaquo;</span> Edit School
    </div>

    <div class="card form-wrapper">
        <div class="card-header">
            <span class="card-title">Edit School</span>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>School ID</label>
                <input type="text" value="<?php echo htmlspecialchars($school['collid']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="collfullname">School Full Name</label>
                <input type="text" id="collfullname" name="collfullname" value="<?php echo htmlspecialchars($school['collfullname']); ?>" required>
            </div>
            <div class="form-group">
                <label for="collshortname">School Short Name</label>
                <input type="text" id="collshortname" name="collshortname" value="<?php echo htmlspecialchars($school['collshortname']); ?>" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Update School</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>

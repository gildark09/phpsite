<?php
require_once "../auth/check.php";
require_once "../config/db.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collid        = filter_input(INPUT_POST, 'collid', FILTER_VALIDATE_INT);
    $collfullname  = filter_input(INPUT_POST, 'collfullname', FILTER_SANITIZE_SPECIAL_CHARS);
    $collshortname = filter_input(INPUT_POST, 'collshortname', FILTER_SANITIZE_SPECIAL_CHARS);

    if ($collid && $collfullname && $collshortname) {
        $stmt = $pdo->prepare("INSERT INTO colleges (collid, collfullname, collshortname) VALUES (?, ?, ?)");
        $stmt->execute([$collid, $collfullname, $collshortname]);
        header("Location: index.php?msg=added");
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
        <a href="index.php">Schools</a> <span>&rsaquo;</span> Add School
    </div>

    <div class="card form-wrapper">
        <div class="card-header">
            <span class="card-title">Add School</span>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="collid">School ID</label>
                <input type="number" id="collid" name="collid" placeholder="e.g. 12" required>
            </div>
            <div class="form-group">
                <label for="collfullname">School Full Name</label>
                <input type="text" id="collfullname" name="collfullname" placeholder="e.g. School of Computer Studies" required>
            </div>
            <div class="form-group">
                <label for="collshortname">School Short Name</label>
                <input type="text" id="collshortname" name="collshortname" placeholder="e.g. SCS" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Save School</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>

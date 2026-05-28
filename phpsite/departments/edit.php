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

$schools = $pdo->query("SELECT * FROM colleges ORDER BY collfullname")->fetchAll(PDO::FETCH_ASSOC);
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deptfullname  = filter_input(INPUT_POST, 'deptfullname', FILTER_SANITIZE_SPECIAL_CHARS);
    $deptshortname = filter_input(INPUT_POST, 'deptshortname', FILTER_SANITIZE_SPECIAL_CHARS);
    $deptcollid    = filter_input(INPUT_POST, 'deptcollid', FILTER_VALIDATE_INT);

    if ($deptfullname && $deptcollid) {
        $stmt = $pdo->prepare("UPDATE departments SET deptfullname = ?, deptshortname = ?, deptcollid = ? WHERE deptid = ?");
        $stmt->execute([$deptfullname, $deptshortname ?? '', $deptcollid, $id]);
        header("Location: index.php?school=$deptcollid&msg=updated");
        exit();
    } else {
        $error = "Full Name and School are required.";
    }
}
?>
<?php require_once "../includes/header.php"; ?>
<?php require_once "../includes/sidebar.php"; ?>

<main class="main-content">
    <div class="breadcrumb">
        <a href="/phpsite/index.php">Home</a> <span>&rsaquo;</span>
        <a href="index.php<?php echo $back_school ? '?school='.$back_school : ''; ?>">Departments</a> <span>&rsaquo;</span> Edit Department
    </div>

    <div class="card form-wrapper">
        <div class="card-header">
            <span class="card-title">Edit Department</span>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Department ID</label>
                <input type="text" value="<?php echo htmlspecialchars($dept['deptid']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="deptcollid">School</label>
                <select id="deptcollid" name="deptcollid" required>
                    <option value="">-- Select School --</option>
                    <?php foreach ($schools as $s): ?>
                        <option value="<?php echo $s['collid']; ?>" <?php echo ($dept['deptcollid'] == $s['collid']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s['collfullname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="deptfullname">Department Full Name</label>
                <input type="text" id="deptfullname" name="deptfullname" value="<?php echo htmlspecialchars($dept['deptfullname']); ?>" required>
            </div>
            <div class="form-group">
                <label for="deptshortname">Department Short Name</label>
                <input type="text" id="deptshortname" name="deptshortname" value="<?php echo htmlspecialchars($dept['deptshortname']); ?>">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Update Department</button>
                <a href="index.php<?php echo $back_school ? '?school='.$back_school : ''; ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>

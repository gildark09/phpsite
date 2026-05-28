<?php
require_once "../auth/check.php";
require_once "../config/db.php";

$schools = $pdo->query("SELECT * FROM colleges ORDER BY collfullname")->fetchAll(PDO::FETCH_ASSOC);

$preselected_school = filter_input(INPUT_GET, 'school', FILTER_VALIDATE_INT);
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deptid        = filter_input(INPUT_POST, 'deptid', FILTER_VALIDATE_INT);
    $deptfullname  = filter_input(INPUT_POST, 'deptfullname', FILTER_SANITIZE_SPECIAL_CHARS);
    $deptshortname = filter_input(INPUT_POST, 'deptshortname', FILTER_SANITIZE_SPECIAL_CHARS);
    $deptcollid    = filter_input(INPUT_POST, 'deptcollid', FILTER_VALIDATE_INT);

    if ($deptid && $deptfullname && $deptcollid) {
        $stmt = $pdo->prepare("INSERT INTO departments (deptid, deptfullname, deptshortname, deptcollid) VALUES (?, ?, ?, ?)");
        $stmt->execute([$deptid, $deptfullname, $deptshortname ?? '', $deptcollid]);
        header("Location: index.php?school=$deptcollid&msg=added");
        exit();
    } else {
        $error = "Department ID, Full Name, and School are required.";
    }
}
?>
<?php require_once "../includes/header.php"; ?>
<?php require_once "../includes/sidebar.php"; ?>

<main class="main-content">
    <div class="breadcrumb">
        <a href="/phpsite/index.php">Home</a> <span>&rsaquo;</span>
        <a href="index.php<?php echo $preselected_school ? '?school='.$preselected_school : ''; ?>">Departments</a> <span>&rsaquo;</span> Add Department
    </div>

    <div class="card form-wrapper">
        <div class="card-header">
            <span class="card-title">Add Department</span>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="deptcollid">School</label>
                <select id="deptcollid" name="deptcollid" required>
                    <option value="">-- Select School --</option>
                    <?php foreach ($schools as $s): ?>
                        <option value="<?php echo $s['collid']; ?>" <?php echo ($preselected_school == $s['collid']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s['collfullname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="deptid">Department ID</label>
                <input type="number" id="deptid" name="deptid" placeholder="e.g. 11002" required>
            </div>
            <div class="form-group">
                <label for="deptfullname">Department Full Name</label>
                <input type="text" id="deptfullname" name="deptfullname" placeholder="e.g. Department of Computer Science" required>
            </div>
            <div class="form-group">
                <label for="deptshortname">Department Short Name</label>
                <input type="text" id="deptshortname" name="deptshortname" placeholder="e.g. DCS">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Save Department</button>
                <a href="index.php<?php echo $preselected_school ? '?school='.$preselected_school : ''; ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>

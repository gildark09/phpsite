<?php
require_once "../auth/check.php";
require_once "../config/db.php";

$departments = $pdo->query("SELECT d.*, c.collfullname, c.collid FROM departments d JOIN colleges c ON d.deptcollid = c.collid ORDER BY c.collfullname, d.deptfullname")->fetchAll(PDO::FETCH_ASSOC);

$preselected_dept = filter_input(INPUT_GET, 'dept', FILTER_VALIDATE_INT);
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $progid        = filter_input(INPUT_POST, 'progid', FILTER_VALIDATE_INT);
    $progfullname  = filter_input(INPUT_POST, 'progfullname', FILTER_SANITIZE_SPECIAL_CHARS);
    $progshortname = filter_input(INPUT_POST, 'progshortname', FILTER_SANITIZE_SPECIAL_CHARS);
    $progcolldeptid = filter_input(INPUT_POST, 'progcolldeptid', FILTER_VALIDATE_INT);

    if ($progid && $progfullname && $progcolldeptid) {
        $stmt_dept = $pdo->prepare("SELECT deptcollid FROM departments WHERE deptid = ?");
        $stmt_dept->execute([$progcolldeptid]);
        $dept_row = $stmt_dept->fetch(PDO::FETCH_ASSOC);
        $progcollid = $dept_row['deptcollid'];

        $stmt = $pdo->prepare("INSERT INTO programs (progid, progfullname, progshortname, progcollid, progcolldeptid) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$progid, $progfullname, $progshortname ?? '', $progcollid, $progcolldeptid]);
        header("Location: index.php?dept=$progcolldeptid&msg=added");
        exit();
    } else {
        $error = "Program ID, Full Name, and Department are required.";
    }
}
?>
<?php require_once "../includes/header.php"; ?>
<?php require_once "../includes/sidebar.php"; ?>

<main class="main-content">
    <div class="breadcrumb">
        <a href="/phpsite/index.php">Home</a> <span>&rsaquo;</span>
        <a href="index.php<?php echo $preselected_dept ? '?dept='.$preselected_dept : ''; ?>">Programs</a> <span>&rsaquo;</span> Add Program
    </div>

    <div class="card form-wrapper">
        <div class="card-header">
            <span class="card-title">Add Program</span>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="progcolldeptid">Department</label>
                <select id="progcolldeptid" name="progcolldeptid" required>
                    <option value="">-- Select Department --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?php echo $d['deptid']; ?>" <?php echo ($preselected_dept == $d['deptid']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($d['collfullname'] . ' — ' . $d['deptfullname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="progid">Program ID</label>
                <input type="number" id="progid" name="progid" placeholder="e.g. 1111001005" required>
            </div>
            <div class="form-group">
                <label for="progfullname">Program Full Name</label>
                <input type="text" id="progfullname" name="progfullname" placeholder="e.g. Bachelor of Science in Data Science" required>
            </div>
            <div class="form-group">
                <label for="progshortname">Program Code</label>
                <input type="text" id="progshortname" name="progshortname" placeholder="e.g. BSDS">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Save Program</button>
                <a href="index.php<?php echo $preselected_dept ? '?dept='.$preselected_dept : ''; ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>

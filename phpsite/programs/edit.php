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

$departments = $pdo->query("SELECT d.*, c.collfullname FROM departments d JOIN colleges c ON d.deptcollid = c.collid ORDER BY c.collfullname, d.deptfullname")->fetchAll(PDO::FETCH_ASSOC);
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $progfullname   = filter_input(INPUT_POST, 'progfullname', FILTER_SANITIZE_SPECIAL_CHARS);
    $progshortname  = filter_input(INPUT_POST, 'progshortname', FILTER_SANITIZE_SPECIAL_CHARS);
    $progcolldeptid = filter_input(INPUT_POST, 'progcolldeptid', FILTER_VALIDATE_INT);

    if ($progfullname && $progcolldeptid) {
        $stmt_dept = $pdo->prepare("SELECT deptcollid FROM departments WHERE deptid = ?");
        $stmt_dept->execute([$progcolldeptid]);
        $dept_row = $stmt_dept->fetch(PDO::FETCH_ASSOC);
        $progcollid = $dept_row['deptcollid'];

        $stmt = $pdo->prepare("UPDATE programs SET progfullname = ?, progshortname = ?, progcollid = ?, progcolldeptid = ? WHERE progid = ?");
        $stmt->execute([$progfullname, $progshortname ?? '', $progcollid, $progcolldeptid, $id]);
        header("Location: index.php?dept=$progcolldeptid&msg=updated");
        exit();
    } else {
        $error = "Full Name and Department are required.";
    }
}
?>
<?php require_once "../includes/header.php"; ?>
<?php require_once "../includes/sidebar.php"; ?>

<main class="main-content">
    <div class="breadcrumb">
        <a href="/phpsite/index.php">Home</a> <span>&rsaquo;</span>
        <a href="index.php<?php echo $back_dept ? '?dept='.$back_dept : ''; ?>">Programs</a> <span>&rsaquo;</span> Edit Program
    </div>

    <div class="card form-wrapper">
        <div class="card-header">
            <span class="card-title">Edit Program</span>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Program ID</label>
                <input type="text" value="<?php echo htmlspecialchars($prog['progid']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="progcolldeptid">Department</label>
                <select id="progcolldeptid" name="progcolldeptid" required>
                    <option value="">-- Select Department --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?php echo $d['deptid']; ?>" <?php echo ($prog['progcolldeptid'] == $d['deptid']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($d['collfullname'] . ' — ' . $d['deptfullname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="progfullname">Program Full Name</label>
                <input type="text" id="progfullname" name="progfullname" value="<?php echo htmlspecialchars($prog['progfullname']); ?>" required>
            </div>
            <div class="form-group">
                <label for="progshortname">Program Code</label>
                <input type="text" id="progshortname" name="progshortname" value="<?php echo htmlspecialchars($prog['progshortname']); ?>">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Update Program</button>
                <a href="index.php<?php echo $back_dept ? '?dept='.$back_dept : ''; ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>

<?php
require_once "../auth/check.php";
require_once "../config/db.php";

$colleges = $pdo->query("SELECT * FROM colleges ORDER BY collfullname")->fetchAll(PDO::FETCH_ASSOC);
$departments = $pdo->query("SELECT d.*, c.collfullname FROM departments d JOIN colleges c ON d.deptcollid = c.collid ORDER BY c.collfullname, d.deptfullname")->fetchAll(PDO::FETCH_ASSOC);
$programs = $pdo->query("SELECT p.*, d.deptfullname FROM programs p JOIN departments d ON p.progcolldeptid = d.deptid ORDER BY p.progfullname")->fetchAll(PDO::FETCH_ASSOC);

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studid        = filter_input(INPUT_POST, 'studid', FILTER_VALIDATE_INT);
    $studfirstname = filter_input(INPUT_POST, 'studfirstname', FILTER_SANITIZE_SPECIAL_CHARS);
    $studlastname  = filter_input(INPUT_POST, 'studlastname', FILTER_SANITIZE_SPECIAL_CHARS);
    $studmidname   = filter_input(INPUT_POST, 'studmidname', FILTER_SANITIZE_SPECIAL_CHARS);
    $studcollid    = filter_input(INPUT_POST, 'studcollid', FILTER_VALIDATE_INT);
    $studcolldeptid = filter_input(INPUT_POST, 'studcolldeptid', FILTER_VALIDATE_INT);
    $studprogid    = filter_input(INPUT_POST, 'studprogid', FILTER_VALIDATE_INT);
    $studyear      = filter_input(INPUT_POST, 'studyear', FILTER_VALIDATE_INT);

    if ($studid && $studfirstname && $studlastname && $studcollid && $studcolldeptid && $studprogid && $studyear) {
        $stmt = $pdo->prepare("INSERT INTO students (studid, studfirstname, studlastname, studmidname, studcollid, studcolldeptid, studprogid, studyear) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$studid, $studfirstname, $studlastname, $studmidname ?? '', $studcollid, $studcolldeptid, $studprogid, $studyear]);
        header("Location: index.php?msg=added");
        exit();
    } else {
        $error = "All required fields must be filled.";
    }
}
?>
<?php require_once "../includes/header.php"; ?>
<?php require_once "../includes/sidebar.php"; ?>

<main class="main-content">
    <div class="breadcrumb">
        <a href="/phpsite/index.php">Home</a> <span>&rsaquo;</span>
        <a href="index.php">Students</a> <span>&rsaquo;</span> Add Student
    </div>

    <div class="card" style="max-width:680px;">
        <div class="card-header">
            <span class="card-title">Add Student</span>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="studid">Student ID</label>
                <input type="number" id="studid" name="studid" placeholder="e.g. 20230001" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="studfirstname">First Name <span style="color:#c62828">*</span></label>
                    <input type="text" id="studfirstname" name="studfirstname" placeholder="First name" required>
                </div>
                <div class="form-group">
                    <label for="studlastname">Last Name <span style="color:#c62828">*</span></label>
                    <input type="text" id="studlastname" name="studlastname" placeholder="Last name" required>
                </div>
            </div>

            <div class="form-group">
                <label for="studmidname">Middle Name <span style="color:#8a9ab0">(optional)</span></label>
                <input type="text" id="studmidname" name="studmidname" placeholder="Middle name">
            </div>

            <div class="form-group">
                <label for="studcollid">School <span style="color:#c62828">*</span></label>
                <select id="studcollid" name="studcollid" required>
                    <option value="">-- Select School --</option>
                    <?php foreach ($colleges as $c): ?>
                        <option value="<?php echo $c['collid']; ?>"><?php echo htmlspecialchars($c['collfullname']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="studcolldeptid">Department <span style="color:#c62828">*</span></label>
                <select id="studcolldeptid" name="studcolldeptid" required>
                    <option value="">-- Select Department --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?php echo $d['deptid']; ?>"><?php echo htmlspecialchars($d['collfullname'] . ' — ' . $d['deptfullname']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="studprogid">Program <span style="color:#c62828">*</span></label>
                <select id="studprogid" name="studprogid" required>
                    <option value="">-- Select Program --</option>
                    <?php foreach ($programs as $p): ?>
                        <option value="<?php echo $p['progid']; ?>"><?php echo htmlspecialchars($p['progfullname'] . ' (' . $p['progshortname'] . ')'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="studyear">Year Level <span style="color:#c62828">*</span></label>
                <select id="studyear" name="studyear" required>
                    <option value="">-- Select Year --</option>
                    <option value="1">Year 1</option>
                    <option value="2">Year 2</option>
                    <option value="3">Year 3</option>
                    <option value="4">Year 4</option>
                    <option value="5">Year 5</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">Save Student</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>

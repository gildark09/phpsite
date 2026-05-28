<?php
require_once "../auth/check.php";
require_once "../config/db.php";

$departments = $pdo->query("SELECT d.*, c.collfullname FROM departments d JOIN colleges c ON d.deptcollid = c.collid ORDER BY c.collfullname, d.deptfullname")->fetchAll(PDO::FETCH_ASSOC);

$selected_dept = filter_input(INPUT_GET, 'dept', FILTER_VALIDATE_INT);

$programs = [];
if ($selected_dept) {
    $stmt = $pdo->prepare("SELECT p.*, d.deptshortname FROM programs p JOIN departments d ON p.progcolldeptid = d.deptid WHERE p.progcolldeptid = ? ORDER BY p.progid");
    $stmt->execute([$selected_dept]);
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php require_once "../includes/header.php"; ?>
<?php require_once "../includes/sidebar.php"; ?>

<main class="main-content">
    <div class="breadcrumb">
        <a href="/phpsite/index.php">Home</a> <span>&rsaquo;</span> Programs
    </div>

    <div class="filter-bar">
        <div class="form-group">
            <label for="dept">Filter by Department</label>
            <select id="dept" name="dept" onchange="window.location.href='index.php?dept='+this.value">
                <option value="">-- Select a Department --</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?php echo $d['deptid']; ?>" <?php echo ($selected_dept == $d['deptid']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($d['collfullname'] . ' — ' . $d['deptfullname']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($selected_dept): ?>
            <a href="add.php?dept=<?php echo $selected_dept; ?>" class="btn btn-success">+ Add Program</a>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Programs</span>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
            <div class="alert alert-success">Program added successfully.</div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
            <div class="alert alert-success">Program updated successfully.</div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div class="alert alert-success">Program deleted successfully.</div>
        <?php endif; ?>

        <?php if (!$selected_dept): ?>
            <div class="empty-state">Please select a department above to view its programs.</div>
        <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Program ID</th>
                        <th>Program Full Name</th>
                        <th>Program Code</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($programs) === 0): ?>
                        <tr><td colspan="4" class="empty-state">No programs found for this department.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($programs as $prog): ?>
                    <tr>
                        <td><span class="id-badge"><?php echo htmlspecialchars($prog['progid']); ?></span></td>
                        <td><?php echo htmlspecialchars($prog['progfullname']); ?></td>
                        <td><?php echo htmlspecialchars($prog['progshortname']); ?></td>
                        <td>
                            <div class="td-actions">
                                <a href="edit.php?id=<?php echo $prog['progid']; ?>&dept=<?php echo $selected_dept; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete.php?id=<?php echo $prog['progid']; ?>&dept=<?php echo $selected_dept; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>

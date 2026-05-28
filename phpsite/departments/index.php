<?php
require_once "../auth/check.php";
require_once "../config/db.php";

$schools = $pdo->query("SELECT * FROM colleges ORDER BY collfullname")->fetchAll(PDO::FETCH_ASSOC);

$selected_school = filter_input(INPUT_GET, 'school', FILTER_VALIDATE_INT);

$departments = [];
if ($selected_school) {
    $stmt = $pdo->prepare("SELECT d.*, c.collshortname FROM departments d JOIN colleges c ON d.deptcollid = c.collid WHERE d.deptcollid = ? ORDER BY d.deptid");
    $stmt->execute([$selected_school]);
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php require_once "../includes/header.php"; ?>
<?php require_once "../includes/sidebar.php"; ?>

<main class="main-content">
    <div class="breadcrumb">
        <a href="/phpsite/index.php">Home</a> <span>&rsaquo;</span> Departments
    </div>

    <div class="filter-bar">
        <div class="form-group">
            <label for="school">Filter by School</label>
            <select id="school" name="school" onchange="window.location.href='index.php?school='+this.value">
                <option value="">-- Select a School --</option>
                <?php foreach ($schools as $s): ?>
                    <option value="<?php echo $s['collid']; ?>" <?php echo ($selected_school == $s['collid']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($s['collfullname']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($selected_school): ?>
            <a href="add.php?school=<?php echo $selected_school; ?>" class="btn btn-success">+ Add Department</a>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Departments</span>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
            <div class="alert alert-success">Department added successfully.</div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
            <div class="alert alert-success">Department updated successfully.</div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div class="alert alert-success">Department deleted successfully.</div>
        <?php endif; ?>

        <?php if (!$selected_school): ?>
            <div class="empty-state">Please select a school above to view its departments.</div>
        <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Dept ID</th>
                        <th>Department Full Name</th>
                        <th>Short Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($departments) === 0): ?>
                        <tr><td colspan="4" class="empty-state">No departments found for this school.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($departments as $dept): ?>
                    <tr>
                        <td><span class="id-badge"><?php echo htmlspecialchars($dept['deptid']); ?></span></td>
                        <td><?php echo htmlspecialchars($dept['deptfullname']); ?></td>
                        <td><?php echo htmlspecialchars($dept['deptshortname']); ?></td>
                        <td>
                            <div class="td-actions">
                                <a href="edit.php?id=<?php echo $dept['deptid']; ?>&school=<?php echo $selected_school; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete.php?id=<?php echo $dept['deptid']; ?>&school=<?php echo $selected_school; ?>" class="btn btn-danger btn-sm">Delete</a>
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

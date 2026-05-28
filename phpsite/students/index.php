<?php
require_once "../auth/check.php";
require_once "../config/db.php";

$stmt = $pdo->query("
    SELECT s.*, 
           p.progshortname,
           p.progfullname,
           c.collshortname,
           d.deptshortname
    FROM students s
    JOIN programs p ON s.studprogid = p.progid
    JOIN colleges c ON s.studcollid = c.collid
    JOIN departments d ON s.studcolldeptid = d.deptid
    ORDER BY s.studid
");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php require_once "../includes/header.php"; ?>
<?php require_once "../includes/sidebar.php"; ?>

<main class="main-content">
    <div class="breadcrumb">
        <a href="/phpsite/index.php">Home</a> <span>&rsaquo;</span> Students
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Students</span>
            <a href="add.php" class="btn btn-success btn-sm">+ Add Student</a>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
            <div class="alert alert-success">Student added successfully.</div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
            <div class="alert alert-success">Student updated successfully.</div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div class="alert alert-success">Student deleted successfully.</div>
        <?php endif; ?>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Year Level</th>
                        <th>Program</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($students) === 0): ?>
                        <tr><td colspan="5" class="empty-state">No students found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($students as $stud): ?>
                    <tr>
                        <td><span class="id-badge"><?php echo htmlspecialchars($stud['studid']); ?></span></td>
                        <td>
                            <?php
                            $fullname = htmlspecialchars($stud['studlastname']) . ', ' . htmlspecialchars($stud['studfirstname']);
                            if (!empty($stud['studmidname'])) {
                                $fullname .= ' ' . htmlspecialchars($stud['studmidname']);
                            }
                            echo $fullname;
                            ?>
                        </td>
                        <td>Year <?php echo htmlspecialchars($stud['studyear']); ?></td>
                        <td title="<?php echo htmlspecialchars($stud['progfullname']); ?>">
                            <?php echo htmlspecialchars($stud['progshortname']); ?>
                        </td>
                        <td>
                            <div class="td-actions">
                                <a href="edit.php?id=<?php echo $stud['studid']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete.php?id=<?php echo $stud['studid']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>

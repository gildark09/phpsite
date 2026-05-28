<?php
require_once "../auth/check.php";
require_once "../config/db.php";

$schools = $pdo->query("SELECT * FROM colleges ORDER BY collid")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php require_once "../includes/header.php"; ?>
<?php require_once "../includes/sidebar.php"; ?>

<main class="main-content">
    <div class="breadcrumb">
        <a href="../index.php">Home</a> <span>&rsaquo;</span> Schools
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Schools</span>
            <a href="add.php" class="btn btn-success btn-sm">+ Add School</a>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
            <div class="alert alert-success">School added successfully.</div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
            <div class="alert alert-success">School updated successfully.</div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div class="alert alert-success">School deleted successfully.</div>
        <?php endif; ?>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>School ID</th>
                        <th>School Full Name</th>
                        <th>Short Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($schools) === 0): ?>
                        <tr><td colspan="4" class="empty-state">No schools found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($schools as $school): ?>
                    <tr>
                        <td><span class="id-badge"><?php echo htmlspecialchars($school['collid']); ?></span></td>
                        <td><?php echo htmlspecialchars($school['collfullname']); ?></td>
                        <td><?php echo htmlspecialchars($school['collshortname']); ?></td>
                        <td>
                            <div class="td-actions">
                                <a href="edit.php?id=<?php echo $school['collid']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete.php?id=<?php echo $school['collid']; ?>" class="btn btn-danger btn-sm">Delete</a>
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

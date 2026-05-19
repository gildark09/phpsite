<?php
session_start();
require 'db.php';
require 'auth.php';
require 'helpers.php';
require_login();

$errors    = [];
$form_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && can_edit()) {

    $action = $_POST['action'] ?? '';

    if ($action === 'save_college') {

        $id        = clean_int($_POST['collid'] ?? 0);
        $fullname  = clean_text($_POST['collfullname'] ?? '');
        $shortname = clean_text($_POST['collshortname'] ?? '');

        $err = validate_name($fullname, 'school full name');
        if ($err) $errors['collfullname'] = $err;

        $err = validate_shortname($shortname, 'school short name');
        if ($err) $errors['collshortname'] = $err;

        if (empty($errors)) {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE colleges SET collfullname = ?, collshortname = ? WHERE collid = ?");
                $stmt->execute([$fullname, $shortname, $id]);
                set_flash('success', 'School updated successfully.');
            } else {
                $nid  = next_id($pdo, 'colleges', 'collid');
                $stmt = $pdo->prepare("INSERT INTO colleges (collid, collfullname, collshortname) VALUES (?, ?, ?)");
                $stmt->execute([$nid, $fullname, $shortname]);
                set_flash('success', 'School created successfully.');
            }
            header('Location: schools.php');
            exit;
        }

        $form_data = $_POST;
    }

    if ($action === 'delete_college') {
        $id = clean_int($_POST['collid'] ?? 0);
        if (validate_int($id)) {
            $stmt = $pdo->prepare("DELETE FROM colleges WHERE collid = ?");
            $stmt->execute([$id]);
            set_flash('success', 'School deleted.');
        }
        header('Location: schools.php');
        exit;
    }
}

$colleges    = get_colleges($pdo);
$edit_row    = null;
$show_form   = isset($_GET['new']);

if (isset($_GET['edit'])) {
    $eid      = clean_int($_GET['edit']);
    $edit_row = find_by_id($colleges, 'collid', $eid);
    $show_form = true;
}

require 'layout_top.php';
?>

<div class="page-header">
    <div class="page-header-left">
        <h2><i class="fas fa-university" style="color:var(--green);margin-right:8px;"></i>Schools</h2>
        <p>Manage all schools and colleges in the system</p>
    </div>
    <?php if (can_edit()): ?>
        <a href="schools.php?new=1" class="btn btn-green">
            <i class="fas fa-plus"></i> Create School Entry
        </a>
    <?php endif; ?>
</div>

<?php if ($show_form && can_edit()): ?>
<div class="form-card">
    <h3>
        <i class="fas fa-<?php echo $edit_row ? 'edit' : 'plus-circle'; ?>" style="color:var(--green);"></i>
        <?php echo $edit_row ? 'Update School' : 'New School'; ?>
    </h3>
    <form method="POST" action="schools.php">
        <input type="hidden" name="action" value="save_college">
        <?php if ($edit_row): ?>
            <input type="hidden" name="collid" value="<?php echo h($edit_row['collid']); ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label>Full Name <span class="required-mark">*</span></label>
                <input type="text" name="collfullname"
                       class="<?php echo err_class($errors, 'collfullname'); ?>"
                       value="<?php echo old($form_data, 'collfullname', $edit_row['collfullname'] ?? ''); ?>"
                       placeholder="e.g. School of Computer Studies">
                <?php err_msg($errors, 'collfullname'); ?>
            </div>
            <div class="form-group">
                <label>Short Name <span class="required-mark">*</span></label>
                <input type="text" name="collshortname"
                       class="<?php echo err_class($errors, 'collshortname'); ?>"
                       value="<?php echo old($form_data, 'collshortname', $edit_row['collshortname'] ?? ''); ?>"
                       placeholder="e.g. SCS">
                <?php err_msg($errors, 'collshortname'); ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-green">
                <i class="fas fa-save"></i> <?php echo $edit_row ? 'Update' : 'Save'; ?>
            </button>
            <button type="reset" class="btn btn-outline">
                <i class="fas fa-undo"></i> Reset
            </button>
            <a href="schools.php" class="btn btn-red">
                <i class="fas fa-times"></i> Exit
            </a>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>School ID</th>
                <th>School Full Name</th>
                <th>Short Name</th>
                <?php if (can_edit()): ?><th>Actions</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($colleges)): ?>
            <tr>
                <td colspan="4">
                    <div class="empty-state">
                        <i class="fas fa-university"></i>
                        <p>No schools found.</p>
                    </div>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($colleges as $c): ?>
            <tr>
                <td><span class="id-badge"><?php echo h($c['collid']); ?></span></td>
                <td><?php echo h($c['collfullname']); ?></td>
                <td><span class="short-badge"><?php echo h($c['collshortname']); ?></span></td>
                <?php if (can_edit()): ?>
                <td>
                    <a href="schools.php?edit=<?php echo h($c['collid']); ?>" class="btn btn-green btn-xs">
                        <i class="fas fa-pencil-alt"></i> Update
                    </a>
                    <form method="POST" action="schools.php" style="display:inline;"
                          onsubmit="return confirm('Delete this school? This cannot be undone.');">
                        <input type="hidden" name="action" value="delete_college">
                        <input type="hidden" name="collid" value="<?php echo h($c['collid']); ?>">
                        <button type="submit" class="btn btn-red btn-xs">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <div class="table-footer">
        Total of: <strong><?php echo count($colleges); ?></strong> school(s) in the database.
    </div>
</div>

<?php require 'layout_bottom.php'; ?>

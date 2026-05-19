<?php
session_start();
require 'db.php';
require 'auth.php';
require 'helpers.php';
require_login();

$errors      = [];
$form_data   = [];
$filter_coll = (int) clean_int($_GET['filter_coll'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && can_edit()) {

    $action = $_POST['action'] ?? '';

    if ($action === 'save_dept') {

        $id        = (int) clean_int($_POST['deptid'] ?? 0);
        $collid    = (int) clean_int($_POST['deptcollid'] ?? 0);
        $fullname  = clean_text($_POST['deptfullname'] ?? '');
        $shortname = clean_text($_POST['deptshortname'] ?? '');

        $err = validate_name($fullname, 'department full name');
        if ($err) $errors['deptfullname'] = $err;

        $err = validate_select($collid, 'school');
        if ($err) $errors['deptcollid'] = $err;

        if (empty($errors)) {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE departments SET deptfullname = ?, deptshortname = ?, deptcollid = ? WHERE deptid = ?");
                $stmt->execute([$fullname, $shortname, $collid, $id]);
                set_flash('success', 'Department updated successfully.');
            } else {
                $nid  = next_id($pdo, 'departments', 'deptid');
                $stmt = $pdo->prepare("INSERT INTO departments (deptid, deptfullname, deptshortname, deptcollid) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nid, $fullname, $shortname, $collid]);
                set_flash('success', 'Department created successfully.');
            }
            header('Location: departments.php?filter_coll=' . $collid);
            exit;
        }

        $form_data   = $_POST;
        $filter_coll = $collid;
    }

    if ($action === 'delete_dept') {
        $id     = (int) clean_int($_POST['deptid'] ?? 0);
        $collid = (int) clean_int($_POST['deptcollid_filter'] ?? 0);
        if (validate_int($id)) {
            $stmt = $pdo->prepare("DELETE FROM departments WHERE deptid = ?");
            $stmt->execute([$id]);
            set_flash('success', 'Department deleted.');
        }
        header('Location: departments.php?filter_coll=' . $collid);
        exit;
    }
}

$colleges         = get_colleges($pdo);
$all_departments  = get_departments($pdo);
$filtered_depts   = $filter_coll ? array_filter($all_departments, function($d) use ($filter_coll) {
    return $d['deptcollid'] == $filter_coll;
}) : [];

$edit_row  = null;
$show_form = isset($_GET['new']);

if (isset($_GET['edit'])) {
    $eid      = (int) clean_int($_GET['edit']);
    $edit_row = find_by_id($all_departments, 'deptid', $eid);
    $show_form = true;
}

require 'layout_top.php';
?>

<div class="page-header">
    <div class="page-header-left">
        <h2><i class="fas fa-sitemap" style="color:var(--green);margin-right:8px;"></i>Departments</h2>
        <p>Select a school first, then manage its departments</p>
    </div>
</div>

<div class="filter-bar">
    <div class="form-group">
        <label><i class="fas fa-filter"></i> Select School</label>
        <select onchange="window.location='departments.php?filter_coll='+this.value">
            <option value="0">— Choose a School —</option>
            <?php foreach ($colleges as $c): ?>
                <option value="<?php echo h($c['collid']); ?>"
                    <?php echo $filter_coll == $c['collid'] ? 'selected' : ''; ?>>
                    <?php echo h($c['collfullname']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if ($filter_coll && can_edit()): ?>
        <a href="departments.php?filter_coll=<?php echo $filter_coll; ?>&new=1" class="btn btn-green" style="margin-bottom:0;">
            <i class="fas fa-plus"></i> Create Department Entry
        </a>
    <?php endif; ?>

    <?php if ($filter_coll): ?>
        <a href="departments.php" class="btn btn-red" style="margin-bottom:0;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    <?php endif; ?>
</div>

<?php if ($filter_coll): ?>

    <div class="context-tag">
        <i class="fas fa-university"></i>
        Department List &mdash; <?php echo h(find_by_id($colleges, 'collid', $filter_coll)['collfullname'] ?? ''); ?>
    </div>

    <?php if ($show_form && can_edit()): ?>
    <div class="form-card">
        <h3>
            <i class="fas fa-<?php echo $edit_row ? 'edit' : 'plus-circle'; ?>" style="color:var(--green);"></i>
            <?php echo $edit_row ? 'Update Department' : 'New Department'; ?>
        </h3>
        <form method="POST" action="departments.php">
            <input type="hidden" name="action"      value="save_dept">
            <input type="hidden" name="deptcollid"  value="<?php echo $filter_coll; ?>">
            <?php if ($edit_row): ?>
                <input type="hidden" name="deptid"  value="<?php echo h($edit_row['deptid']); ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Full Name <span class="required-mark">*</span></label>
                <input type="text" name="deptfullname"
                       class="<?php echo err_class($errors, 'deptfullname'); ?>"
                       value="<?php echo old($form_data, 'deptfullname', $edit_row['deptfullname'] ?? ''); ?>"
                       placeholder="e.g. CS/IT Department">
                <?php err_msg($errors, 'deptfullname'); ?>
            </div>

            <div class="form-group">
                <label>Short Name</label>
                <input type="text" name="deptshortname"
                       value="<?php echo old($form_data, 'deptshortname', $edit_row['deptshortname'] ?? ''); ?>"
                       placeholder="e.g. CSIT (optional)">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-green">
                    <i class="fas fa-save"></i> <?php echo $edit_row ? 'Update' : 'Save'; ?>
                </button>
                <button type="reset" class="btn btn-outline">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <a href="departments.php?filter_coll=<?php echo $filter_coll; ?>" class="btn btn-red">
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
                    <th>Dept ID</th>
                    <th>Department Full Name</th>
                    <th>Short Name</th>
                    <?php if (can_edit()): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($filtered_depts)): ?>
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <i class="fas fa-sitemap"></i>
                            <p>No departments found for this school.</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($filtered_depts as $d): ?>
                <tr>
                    <td><span class="id-badge"><?php echo h($d['deptid']); ?></span></td>
                    <td><?php echo h($d['deptfullname']); ?></td>
                    <td><span class="short-badge"><?php echo h($d['deptshortname'] ?: '—'); ?></span></td>
                    <?php if (can_edit()): ?>
                    <td>
                        <a href="departments.php?filter_coll=<?php echo $filter_coll; ?>&edit=<?php echo h($d['deptid']); ?>"
                           class="btn btn-green btn-xs">
                            <i class="fas fa-pencil-alt"></i> Update
                        </a>
                        <form method="POST" action="departments.php" style="display:inline;"
                              onsubmit="return confirm('Delete this department?');">
                            <input type="hidden" name="action"           value="delete_dept">
                            <input type="hidden" name="deptid"           value="<?php echo h($d['deptid']); ?>">
                            <input type="hidden" name="deptcollid_filter" value="<?php echo $filter_coll; ?>">
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
            Total of: <strong><?php echo count($filtered_depts); ?></strong> department(s) for this school.
        </div>
    </div>

<?php else: ?>
    <div class="empty-state" style="margin-top:40px;">
        <i class="fas fa-filter"></i>
        <p>Please select a school above to view its departments.</p>
    </div>
<?php endif; ?>

<?php require 'layout_bottom.php'; ?>

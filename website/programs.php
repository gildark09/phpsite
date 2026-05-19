<?php
session_start();
require 'db.php';
require 'auth.php';
require 'helpers.php';
require_login();

$errors      = [];
$form_data   = [];
$filter_coll = (int) clean_int($_GET['filter_coll'] ?? 0);
$filter_dept = (int) clean_int($_GET['filter_dept'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && can_edit()) {

    $action = $_POST['action'] ?? '';

    if ($action === 'save_prog') {

        $id        = (int) clean_int($_POST['progid'] ?? 0);
        $collid    = (int) clean_int($_POST['progcollid'] ?? 0);
        $deptid    = (int) clean_int($_POST['progcolldeptid'] ?? 0);
        $fullname  = clean_text($_POST['progfullname'] ?? '');
        $shortname = clean_text($_POST['progshortname'] ?? '');

        $err = validate_name($fullname, 'program full name');
        if ($err) $errors['progfullname'] = $err;

        $err = validate_select($collid, 'school');
        if ($err) $errors['progcollid'] = $err;

        $err = validate_select($deptid, 'department');
        if ($err) $errors['progcolldeptid'] = $err;

        if (empty($errors)) {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE programs SET progfullname = ?, progshortname = ?, progcollid = ?, progcolldeptid = ? WHERE progid = ?");
                $stmt->execute([$fullname, $shortname, $collid, $deptid, $id]);
                set_flash('success', 'Program updated successfully.');
            } else {
                $nid  = next_id($pdo, 'programs', 'progid');
                $stmt = $pdo->prepare("INSERT INTO programs (progid, progfullname, progshortname, progcollid, progcolldeptid) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nid, $fullname, $shortname, $collid, $deptid]);
                set_flash('success', 'Program created successfully.');
            }
            header('Location: programs.php?filter_coll=' . $collid . '&filter_dept=' . $deptid);
            exit;
        }

        $form_data   = $_POST;
        $filter_coll = $collid;
        $filter_dept = $deptid;
    }

    if ($action === 'delete_prog') {
        $id     = (int) clean_int($_POST['progid'] ?? 0);
        $collid = (int) clean_int($_POST['progcollid_filter'] ?? 0);
        $deptid = (int) clean_int($_POST['progdeptid_filter'] ?? 0);
        if (validate_int($id)) {
            $stmt = $pdo->prepare("DELETE FROM programs WHERE progid = ?");
            $stmt->execute([$id]);
            set_flash('success', 'Program deleted.');
        }
        header('Location: programs.php?filter_coll=' . $collid . '&filter_dept=' . $deptid);
        exit;
    }
}

$colleges        = get_colleges($pdo);
$all_departments = get_departments($pdo);
$all_programs    = get_programs($pdo);

$depts_for_school = $filter_coll ? array_filter($all_departments, function($d) use ($filter_coll) {
    return $d['deptcollid'] == $filter_coll;
}) : [];

$filtered_progs = ($filter_coll && $filter_dept) ? array_filter($all_programs, function($p) use ($filter_coll, $filter_dept) {
    return $p['progcollid'] == $filter_coll && $p['progcolldeptid'] == $filter_dept;
}) : [];

$edit_row  = null;
$show_form = isset($_GET['new']);

if (isset($_GET['edit'])) {
    $eid      = (int) clean_int($_GET['edit']);
    $edit_row = find_by_id($all_programs, 'progid', $eid);
    $show_form = true;
}

require 'layout_top.php';
?>

<div class="page-header">
    <div class="page-header-left">
        <h2><i class="fas fa-book-open" style="color:var(--green);margin-right:8px;"></i>Programs</h2>
        <p>Select a school and department to view programs</p>
    </div>
</div>

<div class="filter-bar">
    <div class="form-group">
        <label><i class="fas fa-university"></i> Select School</label>
        <select onchange="window.location='programs.php?filter_coll='+this.value">
            <option value="0">— Choose a School —</option>
            <?php foreach ($colleges as $c): ?>
                <option value="<?php echo h($c['collid']); ?>"
                    <?php echo $filter_coll == $c['collid'] ? 'selected' : ''; ?>>
                    <?php echo h($c['collfullname']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if ($filter_coll): ?>
    <div class="form-group">
        <label><i class="fas fa-sitemap"></i> Select Department</label>
        <select onchange="window.location='programs.php?filter_coll=<?php echo $filter_coll; ?>&filter_dept='+this.value">
            <option value="0">— Choose a Department —</option>
            <?php foreach ($depts_for_school as $d): ?>
                <option value="<?php echo h($d['deptid']); ?>"
                    <?php echo $filter_dept == $d['deptid'] ? 'selected' : ''; ?>>
                    <?php echo h($d['deptfullname']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>

    <?php if ($filter_coll && $filter_dept && can_edit()): ?>
        <a href="programs.php?filter_coll=<?php echo $filter_coll; ?>&filter_dept=<?php echo $filter_dept; ?>&new=1"
           class="btn btn-green" style="margin-bottom:0;">
            <i class="fas fa-plus"></i> Create Program Entry
        </a>
    <?php endif; ?>

    <?php if ($filter_coll): ?>
        <a href="programs.php" class="btn btn-red" style="margin-bottom:0;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    <?php endif; ?>
</div>

<?php if ($filter_coll && $filter_dept): ?>

    <div class="context-tag">
        <i class="fas fa-book-open"></i>
        Program List &mdash; <?php echo h(find_by_id($all_departments, 'deptid', $filter_dept)['deptfullname'] ?? ''); ?>
    </div>

    <?php if ($show_form && can_edit()): ?>
    <div class="form-card">
        <h3>
            <i class="fas fa-<?php echo $edit_row ? 'edit' : 'plus-circle'; ?>" style="color:var(--green);"></i>
            <?php echo $edit_row ? 'Update Program' : 'New Program'; ?>
        </h3>
        <form method="POST" action="programs.php">
            <input type="hidden" name="action"        value="save_prog">
            <input type="hidden" name="progcollid"    value="<?php echo $filter_coll; ?>">
            <input type="hidden" name="progcolldeptid" value="<?php echo $filter_dept; ?>">
            <?php if ($edit_row): ?>
                <input type="hidden" name="progid"    value="<?php echo h($edit_row['progid']); ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Program Full Name <span class="required-mark">*</span></label>
                <input type="text" name="progfullname"
                       class="<?php echo err_class($errors, 'progfullname'); ?>"
                       value="<?php echo old($form_data, 'progfullname', $edit_row['progfullname'] ?? ''); ?>"
                       placeholder="e.g. Bachelor of Science in Computer Science">
                <?php err_msg($errors, 'progfullname'); ?>
            </div>

            <div class="form-group">
                <label>Short Name / Code</label>
                <input type="text" name="progshortname"
                       value="<?php echo old($form_data, 'progshortname', $edit_row['progshortname'] ?? ''); ?>"
                       placeholder="e.g. BSCS (optional)">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-green">
                    <i class="fas fa-save"></i> <?php echo $edit_row ? 'Update' : 'Save'; ?>
                </button>
                <button type="reset" class="btn btn-outline">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <a href="programs.php?filter_coll=<?php echo $filter_coll; ?>&filter_dept=<?php echo $filter_dept; ?>"
                   class="btn btn-red">
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
                    <th>Program ID</th>
                    <th>Program Full Name</th>
                    <th>Code</th>
                    <?php if (can_edit()): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($filtered_progs)): ?>
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <i class="fas fa-book-open"></i>
                            <p>No programs found for this department.</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($filtered_progs as $p): ?>
                <tr>
                    <td><span class="id-badge"><?php echo h($p['progid']); ?></span></td>
                    <td><?php echo h($p['progfullname']); ?></td>
                    <td><span class="short-badge"><?php echo h($p['progshortname'] ?: '—'); ?></span></td>
                    <?php if (can_edit()): ?>
                    <td>
                        <a href="programs.php?filter_coll=<?php echo $filter_coll; ?>&filter_dept=<?php echo $filter_dept; ?>&edit=<?php echo h($p['progid']); ?>"
                           class="btn btn-green btn-xs">
                            <i class="fas fa-pencil-alt"></i> Update
                        </a>
                        <form method="POST" action="programs.php" style="display:inline;"
                              onsubmit="return confirm('Delete this program?');">
                            <input type="hidden" name="action"            value="delete_prog">
                            <input type="hidden" name="progid"            value="<?php echo h($p['progid']); ?>">
                            <input type="hidden" name="progcollid_filter"  value="<?php echo $filter_coll; ?>">
                            <input type="hidden" name="progdeptid_filter"  value="<?php echo $filter_dept; ?>">
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
            Total of: <strong><?php echo count($filtered_progs); ?></strong> program(s) for this department.
        </div>
    </div>

<?php elseif ($filter_coll): ?>
    <div class="empty-state" style="margin-top:20px;">
        <i class="fas fa-sitemap"></i>
        <p>Please select a department above to view its programs.</p>
    </div>
<?php else: ?>
    <div class="empty-state" style="margin-top:40px;">
        <i class="fas fa-filter"></i>
        <p>Please select a school above to begin filtering.</p>
    </div>
<?php endif; ?>

<?php require 'layout_bottom.php'; ?>

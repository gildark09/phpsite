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

    if ($action === 'save_student') {

        $id        = (int) clean_int($_POST['studid'] ?? 0);
        $fname     = clean_text($_POST['studfirstname'] ?? '');
        $lname     = clean_text($_POST['studlastname'] ?? '');
        $mname     = clean_text($_POST['studmidname'] ?? '');
        $collid    = (int) clean_int($_POST['studcollid'] ?? 0);
        $deptid    = (int) clean_int($_POST['studcolldeptid'] ?? 0);
        $progid    = (int) clean_int($_POST['studprogid'] ?? 0);
        $year      = clean_int($_POST['studyear'] ?? '');

        $err = validate_name($fname, 'first name');
        if ($err) $errors['studfirstname'] = $err;

        $err = validate_name($lname, 'last name');
        if ($err) $errors['studlastname'] = $err;

        $err = validate_select($collid, 'school');
        if ($err) $errors['studcollid'] = $err;

        $err = validate_select($deptid, 'department');
        if ($err) $errors['studcolldeptid'] = $err;

        $err = validate_select($progid, 'program');
        if ($err) $errors['studprogid'] = $err;

        $err = validate_year($year);
        if ($err) $errors['studyear'] = $err;

        if (empty($errors)) {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE students SET studfirstname=?, studlastname=?, studmidname=?, studcollid=?, studcolldeptid=?, studprogid=?, studyear=? WHERE studid=?");
                $stmt->execute([$fname, $lname, $mname, $collid, $deptid, $progid, (int)$year, $id]);
                set_flash('success', 'Student updated successfully.');
            } else {
                $nid  = next_id($pdo, 'students', 'studid');
                $stmt = $pdo->prepare("INSERT INTO students (studid, studfirstname, studlastname, studmidname, studcollid, studcolldeptid, studprogid, studyear) VALUES (?,?,?,?,?,?,?,?)");
                $stmt->execute([$nid, $fname, $lname, $mname, $collid, $deptid, $progid, (int)$year]);
                set_flash('success', 'Student registered successfully.');
            }
            header('Location: students.php');
            exit;
        }

        $form_data = $_POST;
    }

    if ($action === 'delete_student') {
        $id = (int) clean_int($_POST['studid'] ?? 0);
        if (validate_int($id)) {
            $stmt = $pdo->prepare("DELETE FROM students WHERE studid = ?");
            $stmt->execute([$id]);
            set_flash('success', 'Student deleted.');
        }
        header('Location: students.php');
        exit;
    }
}

$colleges        = get_colleges($pdo);
$all_departments = get_departments($pdo);
$all_programs    = get_programs($pdo);
$students        = get_students($pdo);

$search = clean_text($_GET['search'] ?? '');
$s_coll = (int) clean_int($_GET['s_coll'] ?? 0);
$s_year = (int) clean_int($_GET['s_year'] ?? 0);

$filtered_students = $students;

if ($search) {
    $filtered_students = array_filter($filtered_students, function($s) use ($search) {
        $full = $s['studfirstname'] . ' ' . $s['studlastname'] . ' ' . $s['studmidname'];
        return stripos($full, $search) !== false || stripos((string)$s['studid'], $search) !== false;
    });
}

if ($s_coll) {
    $filtered_students = array_filter($filtered_students, function($s) use ($s_coll) {
        return $s['studcollid'] == $s_coll;
    });
}

if ($s_year) {
    $filtered_students = array_filter($filtered_students, function($s) use ($s_year) {
        return $s['studyear'] == $s_year;
    });
}

$edit_row  = null;
$show_form = isset($_GET['new']);

if (isset($_GET['edit'])) {
    $eid      = (int) clean_int($_GET['edit']);
    $edit_row = find_by_id($students, 'studid', $eid);
    $show_form = true;
}

$form_coll_id = (int) ($edit_row['studcollid'] ?? $form_data['studcollid'] ?? 0);
$form_dept_id = (int) ($edit_row['studcolldeptid'] ?? $form_data['studcolldeptid'] ?? 0);

$form_depts = $form_coll_id ? array_filter($all_departments, function($d) use ($form_coll_id) {
    return $d['deptcollid'] == $form_coll_id;
}) : [];

$form_progs = $form_dept_id ? array_filter($all_programs, function($p) use ($form_dept_id) {
    return $p['progcolldeptid'] == $form_dept_id;
}) : [];

require 'layout_top.php';
?>

<div class="page-header">
    <div class="page-header-left">
        <h2><i class="fas fa-user-graduate" style="color:var(--green);margin-right:8px;"></i>Students</h2>
        <p>Register and manage student records</p>
    </div>
    <?php if (can_edit()): ?>
        <a href="students.php?new=1" class="btn btn-green">
            <i class="fas fa-plus"></i> Register Student
        </a>
    <?php endif; ?>
</div>

<div class="filter-bar">
    <div class="form-group" style="flex:2;">
        <label><i class="fas fa-search"></i> Search Student</label>
        <form method="GET" action="students.php" style="display:flex; gap:8px;">
            <input type="text"   name="search"  value="<?php echo h($search); ?>" placeholder="Name or Student ID...">
            <input type="hidden" name="s_coll"  value="<?php echo $s_coll; ?>">
            <input type="hidden" name="s_year"  value="<?php echo $s_year; ?>">
            <button type="submit" class="btn btn-navy btn-sm">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <div class="form-group">
        <label><i class="fas fa-university"></i> Filter School</label>
        <select onchange="window.location='students.php?s_coll='+this.value+'&s_year=<?php echo $s_year; ?>&search=<?php echo urlencode($search); ?>'">
            <option value="0">All Schools</option>
            <?php foreach ($colleges as $c): ?>
                <option value="<?php echo h($c['collid']); ?>"
                    <?php echo $s_coll == $c['collid'] ? 'selected' : ''; ?>>
                    <?php echo h($c['collshortname']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label><i class="fas fa-layer-group"></i> Filter Year</label>
        <select onchange="window.location='students.php?s_year='+this.value+'&s_coll=<?php echo $s_coll; ?>&search=<?php echo urlencode($search); ?>'">
            <option value="0">All Years</option>
            <?php for ($y = 1; $y <= 5; $y++): ?>
                <option value="<?php echo $y; ?>" <?php echo $s_year == $y ? 'selected' : ''; ?>>
                    Year <?php echo $y; ?>
                </option>
            <?php endfor; ?>
        </select>
    </div>
</div>

<?php if ($show_form && can_edit()): ?>
<div class="form-card form-card-wide">
    <h3>
        <i class="fas fa-<?php echo $edit_row ? 'edit' : 'user-plus'; ?>" style="color:var(--green);"></i>
        <?php echo $edit_row ? 'Update Student' : 'Register New Student'; ?>
    </h3>
    <form method="POST" action="students.php">
        <input type="hidden" name="action" value="save_student">
        <?php if ($edit_row): ?>
            <input type="hidden" name="studid" value="<?php echo h($edit_row['studid']); ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label>First Name <span class="required-mark">*</span></label>
                <input type="text" name="studfirstname"
                       class="<?php echo err_class($errors, 'studfirstname'); ?>"
                       value="<?php echo old($form_data, 'studfirstname', $edit_row['studfirstname'] ?? ''); ?>"
                       placeholder="First Name">
                <?php err_msg($errors, 'studfirstname'); ?>
            </div>

            <div class="form-group">
                <label>Last Name <span class="required-mark">*</span></label>
                <input type="text" name="studlastname"
                       class="<?php echo err_class($errors, 'studlastname'); ?>"
                       value="<?php echo old($form_data, 'studlastname', $edit_row['studlastname'] ?? ''); ?>"
                       placeholder="Last Name">
                <?php err_msg($errors, 'studlastname'); ?>
            </div>

            <div class="form-group">
                <label>Middle Name</label>
                <input type="text" name="studmidname"
                       value="<?php echo old($form_data, 'studmidname', $edit_row['studmidname'] ?? ''); ?>"
                       placeholder="Middle Name (optional)">
            </div>

            <div class="form-group">
                <label>Year Level <span class="required-mark">*</span></label>
                <select name="studyear" class="<?php echo err_class($errors, 'studyear'); ?>">
                    <option value="">— Year Level —</option>
                    <?php for ($y = 1; $y <= 5; $y++): ?>
                        <option value="<?php echo $y; ?>"
                            <?php echo (($edit_row['studyear'] ?? $form_data['studyear'] ?? '') == $y) ? 'selected' : ''; ?>>
                            Year <?php echo $y; ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <?php err_msg($errors, 'studyear'); ?>
            </div>

            <div class="form-group">
                <label>School <span class="required-mark">*</span></label>
                <select name="studcollid"
                        class="<?php echo err_class($errors, 'studcollid'); ?>"
                        onchange="this.form.submit()">
                    <option value="">— Select School —</option>
                    <?php foreach ($colleges as $c): ?>
                        <option value="<?php echo h($c['collid']); ?>"
                            <?php echo $form_coll_id == $c['collid'] ? 'selected' : ''; ?>>
                            <?php echo h($c['collfullname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php err_msg($errors, 'studcollid'); ?>
            </div>

            <div class="form-group">
                <label>Department <span class="required-mark">*</span></label>
                <select name="studcolldeptid"
                        class="<?php echo err_class($errors, 'studcolldeptid'); ?>"
                        <?php echo !$form_coll_id ? 'disabled' : ''; ?>
                        onchange="this.form.submit()">
                    <option value="">— Select Department —</option>
                    <?php foreach ($form_depts as $d): ?>
                        <option value="<?php echo h($d['deptid']); ?>"
                            <?php echo $form_dept_id == $d['deptid'] ? 'selected' : ''; ?>>
                            <?php echo h($d['deptfullname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php err_msg($errors, 'studcolldeptid'); ?>
            </div>

            <div class="form-group" style="grid-column:1/-1;">
                <label>Program <span class="required-mark">*</span></label>
                <select name="studprogid"
                        class="<?php echo err_class($errors, 'studprogid'); ?>"
                        <?php echo !$form_dept_id ? 'disabled' : ''; ?>>
                    <option value="">— Select Program —</option>
                    <?php foreach ($form_progs as $p): ?>
                        <option value="<?php echo h($p['progid']); ?>"
                            <?php echo (($edit_row['studprogid'] ?? $form_data['studprogid'] ?? '') == $p['progid']) ? 'selected' : ''; ?>>
                            <?php echo h($p['progfullname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php err_msg($errors, 'studprogid'); ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-green">
                <i class="fas fa-save"></i> <?php echo $edit_row ? 'Update' : 'Register'; ?>
            </button>
            <button type="reset" class="btn btn-outline">
                <i class="fas fa-undo"></i> Reset
            </button>
            <a href="students.php" class="btn btn-red">
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
                <th>Stud ID</th>
                <th>Full Name</th>
                <th>Year</th>
                <th>School</th>
                <th>Program</th>
                <?php if (can_edit()): ?><th>Actions</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($filtered_students)): ?>
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <i class="fas fa-user-graduate"></i>
                        <p>No students found.</p>
                    </div>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($filtered_students as $s): ?>
            <tr>
                <td><span class="id-badge"><?php echo h($s['studid']); ?></span></td>
                <td>
                    <?php echo h($s['studlastname']); ?>,
                    <?php echo h($s['studfirstname']); ?>
                    <?php echo $s['studmidname'] ? h(substr($s['studmidname'], 0, 1)) . '.' : ''; ?>
                </td>
                <td><span class="year-badge">Year <?php echo h($s['studyear']); ?></span></td>
                <td><span class="short-badge"><?php echo h($s['collshortname']); ?></span></td>
                <td><?php echo h($s['progshortname']); ?></td>
                <?php if (can_edit()): ?>
                <td>
                    <a href="students.php?edit=<?php echo h($s['studid']); ?>" class="btn btn-green btn-xs">
                        <i class="fas fa-pencil-alt"></i> Update
                    </a>
                    <form method="POST" action="students.php" style="display:inline;"
                          onsubmit="return confirm('Delete this student?');">
                        <input type="hidden" name="action"  value="delete_student">
                        <input type="hidden" name="studid"  value="<?php echo h($s['studid']); ?>">
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
        Total of: <strong><?php echo count($filtered_students); ?></strong> student(s)
        <?php echo ($search || $s_coll || $s_year) ? '(filtered)' : 'in the database'; ?>.
    </div>
</div>

<?php require 'layout_bottom.php'; ?>

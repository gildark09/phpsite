<?php
session_start();
require 'db.php';
require 'auth.php';
require 'helpers.php';
require_login();

$colleges    = get_colleges($pdo);
$departments = get_departments($pdo);
$programs    = get_programs($pdo);
$students    = get_students($pdo);

require 'layout_top.php';
?>

<div class="page-header">
    <div class="page-header-left">
        <h2><i class="fas fa-th-large" style="color:var(--green);margin-right:8px;"></i>Dashboard</h2>
        <p>Overview of the USJ-R Management System</p>
    </div>
</div>

<div class="dash-stats">
    <div class="stat-card">
        <div class="stat-icon ic-green"><i class="fas fa-university"></i></div>
        <div class="stat-num"><?php echo count($colleges); ?></div>
        <div class="stat-label">Schools / Colleges</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon ic-navy"><i class="fas fa-sitemap"></i></div>
        <div class="stat-num"><?php echo count($departments); ?></div>
        <div class="stat-label">Departments</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon ic-gold"><i class="fas fa-book-open"></i></div>
        <div class="stat-num"><?php echo count($programs); ?></div>
        <div class="stat-label">Programs</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon ic-red"><i class="fas fa-user-graduate"></i></div>
        <div class="stat-num"><?php echo count($students); ?></div>
        <div class="stat-label">Students</div>
    </div>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>School</th>
                <th>Short Name</th>
                <th>Departments</th>
                <th>Programs</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($colleges as $c):
            $dept_count = count(array_filter($departments, function($d) use ($c) {
                return $d['deptcollid'] == $c['collid'];
            }));
            $prog_count = count(array_filter($programs, function($p) use ($c) {
                return $p['progcollid'] == $c['collid'];
            }));
        ?>
            <tr>
                <td><?php echo h($c['collfullname']); ?></td>
                <td><span class="short-badge"><?php echo h($c['collshortname']); ?></span></td>
                <td><?php echo $dept_count; ?></td>
                <td><?php echo $prog_count; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require 'layout_bottom.php'; ?>

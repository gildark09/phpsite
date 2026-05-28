<?php
$current = $_SERVER['REQUEST_URI'];

function is_active($path) {
    global $current;
    return strpos($current, $path) !== false ? 'active' : '';
}
?>

<nav class="sidebar">
    <ul class="sidebar-nav">

        <li>
            <a href="/phpsite/index.php"
               class="sidebar-link <?php echo ($current === '/phpsite/' || $current === '/phpsite/index.php') ? 'active' : ''; ?>">
                <span class="sidebar-icon">&#9632;</span>
                Home
            </a>
        </li>

        <li>
            <a href="/phpsite/schools/index.php"
               class="sidebar-link <?php echo is_active('/phpsite/schools'); ?>">
                <span class="sidebar-icon">&#9632;</span>
                Schools
            </a>
        </li>

        <li>
            <a href="/phpsite/departments/index.php"
               class="sidebar-link <?php echo is_active('/phpsite/departments'); ?>">
                <span class="sidebar-icon">&#9632;</span>
                Departments
            </a>
        </li>

        <li>
            <a href="/phpsite/programs/index.php"
               class="sidebar-link <?php echo is_active('/phpsite/programs'); ?>">
                <span class="sidebar-icon">&#9632;</span>
                Programs
            </a>
        </li>

        <li>
            <a href="/phpsite/students/index.php"
               class="sidebar-link <?php echo is_active('/phpsite/students'); ?>">
                <span class="sidebar-icon">&#9632;</span>
                Students
            </a>
        </li>

    </ul>
</nav>
<?php

function clean_text($value) {
    $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
    return trim($value);
}

function clean_int($value) {
    return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
}

function validate_int($value) {
    return filter_var($value, FILTER_VALIDATE_INT);
}

function validate_name($value, $label) {
    $value = clean_text($value);
    if (empty($value)) {
        return "Invalid $label: field cannot be empty.";
    }
    if (strlen($value) < 2) {
        return "Invalid $label: must be at least 2 characters.";
    }
    return null;
}

function validate_shortname($value, $label) {
    $value = clean_text($value);
    if (empty($value)) {
        return "Invalid $label: field cannot be empty.";
    }
    return null;
}

function validate_year($value) {
    $value = clean_int($value);
    $int = validate_int($value);
    if ($int === false || $int < 1 || $int > 5) {
        return "Invalid year level: must be a number between 1 and 5.";
    }
    return null;
}

function validate_select($value, $label) {
    $int = validate_int(clean_int($value));
    if ($int === false || $int <= 0) {
        return "Invalid $label: please select a valid option.";
    }
    return null;
}

function set_flash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function get_flash() {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function get_colleges($pdo) {
    $stmt = $pdo->query("SELECT * FROM colleges ORDER BY collid");
    return $stmt->fetchAll();
}

function get_departments($pdo) {
    $stmt = $pdo->query("SELECT d.*, c.collshortname FROM departments d JOIN colleges c ON d.deptcollid = c.collid ORDER BY d.deptcollid, d.deptid");
    return $stmt->fetchAll();
}

function get_programs($pdo) {
    $stmt = $pdo->query("SELECT p.*, c.collshortname, d.deptshortname FROM programs p JOIN colleges c ON p.progcollid = c.collid JOIN departments d ON p.progcolldeptid = d.deptid ORDER BY p.progcollid, p.progcolldeptid, p.progid");
    return $stmt->fetchAll();
}

function get_students($pdo) {
    $stmt = $pdo->query("SELECT s.*, c.collshortname, d.deptshortname, pr.progshortname FROM students s JOIN colleges c ON s.studcollid = c.collid JOIN departments d ON s.studcolldeptid = d.deptid JOIN programs pr ON s.studprogid = pr.progid ORDER BY s.studid");
    return $stmt->fetchAll();
}

function find_by_id($list, $id_key, $id_val) {
    foreach ($list as $item) {
        if ($item[$id_key] == $id_val) return $item;
    }
    return null;
}

function next_id($pdo, $table, $id_col) {
    $stmt = $pdo->query("SELECT MAX($id_col) + 1 AS nid FROM $table");
    $row = $stmt->fetch();
    return $row['nid'] ?? 1;
}

function h($val) {
    return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
}

function err_class($errors, $field) {
    return isset($errors[$field]) ? ' error' : '';
}

function err_msg($errors, $field) {
    if (isset($errors[$field])) {
        echo '<span class="field-error">' . h($errors[$field]) . '</span>';
    }
}

function old($form_data, $field, $fallback = '') {
    return h($form_data[$field] ?? $fallback);
}

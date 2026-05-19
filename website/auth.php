<?php

$USERS = [
    'admin'  => ['password' => 'admin123',  'role' => 'admin'],
    'staff'  => ['password' => 'staff456',  'role' => 'staff'],
    'viewer' => ['password' => 'viewer789', 'role' => 'viewer'],
];

function login_user($username, $password) {
    global $USERS;

    $username = filter_var($username, FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($username)) {
        return ['error' => 'Username is required.'];
    }

    if (empty($password)) {
        return ['error' => 'Password is required.'];
    }

    if (!isset($USERS[$username])) {
        return ['error' => 'Invalid username or password.'];
    }

    if ($password !== $USERS[$username]['password']) {
        return ['error' => 'Invalid username or password.'];
    }

    return ['success' => true, 'role' => $USERS[$username]['role']];
}

function is_logged_in() {
    return !empty($_SESSION['logged_in']);
}

function current_user() {
    return $_SESSION['username'] ?? '';
}

function current_role() {
    return $_SESSION['role'] ?? '';
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: index.php');
        exit;
    }
}

function can_edit() {
    return current_role() !== 'viewer';
}

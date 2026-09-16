<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function isAdminLoggedIn()
{
    return isset($_SESSION['admin_id']);
}


function requireAdmin()
{
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}


function adminUser()
{
    return [
        'id' => $_SESSION['admin_id'] ?? null,
        'name' => $_SESSION['admin_name'] ?? null,
        'email' => $_SESSION['admin_email'] ?? null,
        'role' => $_SESSION['admin_role'] ?? null
    ];
}
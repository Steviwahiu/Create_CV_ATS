<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';

if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

header('Location: login.php');
exit;
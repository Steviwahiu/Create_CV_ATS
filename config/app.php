<?php

define('APP_NAME', 'CV ATS Professional');

$appUrl = getenv('APP_URL');

if (!$appUrl) {
    $appUrl = 'http://localhost/cv-ats';
}

define('APP_URL', rtrim($appUrl, '/'));

define('ADMIN_URL', APP_URL . '/admin');
define('ASSETS_URL', APP_URL . '/assets');

define(
    'UPLOAD_PATH',
    dirname(__DIR__) . '/uploads/'
);

define(
    'PDF_PATH',
    dirname(__DIR__) . '/generated/pdf/'
);

define('APP_DEBUG', false);

date_default_timezone_set('Asia/Makassar');
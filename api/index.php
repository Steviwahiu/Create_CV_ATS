<?php

$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

$requestUri = '/' . ltrim($requestUri, '/');

/*
|--------------------------------------------------------------------------
| Halaman utama
|--------------------------------------------------------------------------
*/

if ($requestUri === '/') {
    require dirname(__DIR__) . '/index.php';
    exit;
}

/*
|--------------------------------------------------------------------------
| Daftar halaman PHP
|--------------------------------------------------------------------------
*/

$allowedPages = [
    '/buat-cv.php',
    '/preview.php',
    '/checkout.php',
    '/pembayaran.php',
    '/proses-pembayaran.php',
    '/status.php',
];

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

if (str_starts_with($requestUri, '/admin/')) {

    $adminPath = substr($requestUri, strlen('/admin/'));

    $allowedAdminPages = [
        'index.php',
        'login.php',
        'logout.php',
        'dashboard.php',
        'pesanan.php',
        'detail-pesanan.php',
        'verifikasi-pembayaran.php',
        'proses-pesanan.php',
        'kirim-email.php',
        'template.php',
        'pelanggan.php',
        'pengaturan.php',
    ];

    if (in_array($adminPath, $allowedAdminPages, true)) {
        require dirname(__DIR__) . '/admin/' . $adminPath;
        exit;
    }

    http_response_code(404);
    echo 'Halaman admin tidak ditemukan.';
    exit;
}

/*
|--------------------------------------------------------------------------
| Halaman utama lainnya
|--------------------------------------------------------------------------
*/

if (in_array($requestUri, $allowedPages, true)) {
    require dirname(__DIR__) . $requestUri;
    exit;
}

/*
|--------------------------------------------------------------------------
| 404
|--------------------------------------------------------------------------
*/

http_response_code(404);

echo '<h1>404</h1>';
echo '<p>Halaman tidak ditemukan.</p>';
<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/mail.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(
        ADMIN_URL . '/pesanan.php'
    );
}

verifyCsrf();

$orderId = (int) (
    $_POST['order_id'] ?? 0
);

if ($orderId <= 0) {
    die('Pesanan tidak valid.');
}

/*
|--------------------------------------------------------------------------
| Ambil data pesanan
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        o.id,
        o.order_code,
        o.email,
        o.payment_status,
        o.order_status,
        o.pdf_file,
        o.email_sent_at,
        p.full_name
    FROM cv_orders o
    LEFT JOIN cv_personal p
        ON p.order_id = o.id
    WHERE o.id = ?
    LIMIT 1
");

$stmt->execute([$orderId]);

$order = $stmt->fetch();

if (!$order) {
    die('Pesanan tidak ditemukan.');
}

/*
|--------------------------------------------------------------------------
| Validasi pembayaran
|--------------------------------------------------------------------------
*/

if ($order['payment_status'] !== 'paid') {
    die('Pesanan belum dibayar.');
}

/*
|--------------------------------------------------------------------------
| Validasi PDF
|--------------------------------------------------------------------------
*/

if (empty($order['pdf_file'])) {
    die('File PDF CV belum tersedia.');
}

$pdfPath =
    dirname(__DIR__) .
    '/' .
    $order['pdf_file'];

if (!file_exists($pdfPath)) {
    die('File PDF tidak ditemukan.');
}

/*
|--------------------------------------------------------------------------
| Validasi email
|--------------------------------------------------------------------------
*/

if (
    empty($order['email']) ||
    !filter_var(
        $order['email'],
        FILTER_VALIDATE_EMAIL
    )
) {
    die('Alamat email pelanggan tidak valid.');
}

/*
|--------------------------------------------------------------------------
| Nama pelanggan
|--------------------------------------------------------------------------
*/

$customerName =
    !empty($order['full_name'])
        ? $order['full_name']
        : 'Pelanggan';

/*
|--------------------------------------------------------------------------
| Kirim email
|--------------------------------------------------------------------------
*/

$result = sendCvEmail(
    $order['email'],
    $customerName,
    $order['order_code'],
    $pdfPath
);

/*
|--------------------------------------------------------------------------
| Jika gagal
|--------------------------------------------------------------------------
*/

if (!$result['success']) {

    die(
        'Email gagal dikirim: ' .
        e($result['message'])
    );
}

/*
|--------------------------------------------------------------------------
| Simpan waktu email dikirim
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    UPDATE cv_orders
    SET
        email_sent_at = NOW()
    WHERE id = ?
");

$stmt->execute([
    $orderId
]);

/*
|--------------------------------------------------------------------------
| Kembali ke detail pesanan
|--------------------------------------------------------------------------
*/

redirect(
    ADMIN_URL .
    '/detail-pesanan.php?id=' .
    $orderId .
    '&success=email'
);
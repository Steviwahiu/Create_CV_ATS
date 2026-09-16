<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

requireAdmin();


/*
|--------------------------------------------------------------------------
| Pastikan hanya POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(ADMIN_URL . '/pesanan.php');
}

verifyCsrf();


/*
|--------------------------------------------------------------------------
| Ambil Order ID
|--------------------------------------------------------------------------
*/

$orderId = (int) ($_POST['order_id'] ?? 0);

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
        id,
        order_code,
        amount,
        payment_status,
        order_status
    FROM cv_orders
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$orderId]);

$order = $stmt->fetch();

if (!$order) {
    die('Pesanan tidak ditemukan.');
}


/*
|--------------------------------------------------------------------------
| Jika sudah dibayar
|--------------------------------------------------------------------------
*/

if ($order['payment_status'] === 'paid') {

    redirect(
        ADMIN_URL .
        '/detail-pesanan.php?id=' .
        $orderId
    );
}


/*
|--------------------------------------------------------------------------
| Ambil pembayaran terakhir
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        order_id,
        transaction_id,
        payment_method,
        amount,
        status,
        paid_at
    FROM payments
    WHERE order_id = ?
    ORDER BY id DESC
    LIMIT 1
");

$stmt->execute([$orderId]);

$payment = $stmt->fetch();


/*
|--------------------------------------------------------------------------
| Mulai transaksi database
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();


    /*
    |--------------------------------------------------------------------------
    | Jika belum ada record payment
    |--------------------------------------------------------------------------
    */

    if (!$payment) {

        $stmt = $pdo->prepare("
            INSERT INTO payments
            (
                order_id,
                transaction_id,
                payment_method,
                amount,
                status,
                paid_at
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                'paid',
                NOW()
            )
        ");

        $stmt->execute([
            $orderId,
            null,
            'Manual Verification',
            $order['amount']
        ]);

    } else {

        /*
        |--------------------------------------------------------------------------
        | Update pembayaran menjadi paid
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            UPDATE payments
            SET
                status = 'paid',
                paid_at = NOW()
            WHERE id = ?
        ");

        $stmt->execute([
            $payment['id']
        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | Update status order
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        UPDATE cv_orders
        SET
            payment_status = 'paid',
            order_status = 'processing'
        WHERE id = ?
    ");

    $stmt->execute([
        $orderId
    ]);


    $pdo->commit();


    /*
    |--------------------------------------------------------------------------
    | Kembali ke detail
    |--------------------------------------------------------------------------
    */

    redirect(
        ADMIN_URL .
        '/detail-pesanan.php?id=' .
        $orderId
    );


} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    die(
        'Gagal memverifikasi pembayaran. ' .
        e($e->getMessage())
    );
}
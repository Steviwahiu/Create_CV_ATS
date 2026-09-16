<?php

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$orderCode = trim($_GET['order'] ?? '');

if ($orderCode === '') {
    redirect(APP_URL . '/buat-cv.php');
}


/*
|--------------------------------------------------------------------------
| Ambil Order
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        o.id,
        o.order_code,
        o.email,
        o.amount,
        o.payment_status,
        o.order_status,
        p.full_name,
        t.name AS template_name
    FROM cv_orders o
    LEFT JOIN cv_personal p
        ON p.order_id = o.id
    LEFT JOIN cv_templates t
        ON t.id = o.template_id
    WHERE o.order_code = ?
    LIMIT 1
");

$stmt->execute([$orderCode]);

$order = $stmt->fetch();

if (!$order) {
    http_response_code(404);
    die('Pesanan tidak ditemukan.');
}

$orderId = (int) $order['id'];
$amount = (float) $order['amount'];


/*
|--------------------------------------------------------------------------
| Validasi Order
|--------------------------------------------------------------------------
*/

if ($amount <= 0) {
    die('Nominal pembayaran tidak valid.');
}

if ($order['payment_status'] === 'paid') {

    redirect(
        APP_URL .
        '/status.php?order=' .
        urlencode($orderCode)
    );
}


/*
|--------------------------------------------------------------------------
| Ambil Payment
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
        paid_at,
        created_at
    FROM payments
    WHERE order_id = ?
    ORDER BY id DESC
    LIMIT 1
");

$stmt->execute([$orderId]);

$payment = $stmt->fetch();


/*
|--------------------------------------------------------------------------
| Jika Belum Ada Payment
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
            status
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            'pending'
        )
    ");

    $stmt->execute([
        $orderId,
        null,
        null,
        $amount
    ]);

    $paymentId = (int) $pdo->lastInsertId();

} else {

    $paymentId = (int) $payment['id'];

}


/*
|--------------------------------------------------------------------------
| Update Status Order
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    UPDATE cv_orders
    SET
        payment_status = 'pending',
        order_status = 'waiting_payment'
    WHERE id = ?
");

$stmt->execute([$orderId]);


/*
|--------------------------------------------------------------------------
| Tampilkan Halaman Simulasi Pembayaran
|--------------------------------------------------------------------------
*/

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Proses Pembayaran - <?= e(APP_NAME) ?>
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="<?= e(ASSETS_URL) ?>/css/style.css"
    >

    <style>

        body {
            background: #f8fafc;
        }

        .payment-process {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        .payment-box {
            width: 100%;
            max-width: 600px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 30px;
        }

        .payment-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: #fef2f2;
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .payment-title {
            text-align: center;
            font-size: 25px;
            font-weight: 700;
            color: #111827;
        }

        .payment-description {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
        }

        .payment-summary {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 18px;
            margin-top: 25px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 9px 0;
        }

        .summary-label {
            color: #6b7280;
            font-size: 13px;
        }

        .summary-value {
            color: #111827;
            font-size: 14px;
            font-weight: 600;
            text-align: right;
        }

        .total {
            color: #dc2626;
            font-size: 22px;
            font-weight: 700;
        }

    </style>

</head>

<body>

<div class="payment-process">

    <div class="payment-box">

        <div class="payment-icon">
            💳
        </div>

        <h1 class="payment-title">
            Proses Pembayaran
        </h1>

        <p class="payment-description">
            Pesanan Anda telah dicatat dan menunggu proses pembayaran.
        </p>


        <div class="payment-summary">

            <div class="summary-row">

                <div class="summary-label">
                    Kode Pesanan
                </div>

                <div class="summary-value">
                    <?= e($order['order_code']) ?>
                </div>

            </div>


            <div class="summary-row">

                <div class="summary-label">
                    Nama
                </div>

                <div class="summary-value">
                    <?= e($order['full_name'] ?? 'Pelanggan') ?>
                </div>

            </div>


            <div class="summary-row">

                <div class="summary-label">
                    Template
                </div>

                <div class="summary-value">
                    <?= e($order['template_name'] ?? 'Template CV') ?>
                </div>

            </div>


            <div class="summary-row">

                <div class="summary-label">
                    Total
                </div>

                <div class="summary-value total">
                    <?= e(formatRupiah($amount)) ?>
                </div>

            </div>

        </div>


        <div class="alert alert-warning mt-4 small">

            <strong>
                Pembayaran gateway belum terhubung.
            </strong>

            <br>

            Saat ini sistem baru membuat data pembayaran
            dengan status <strong>pending</strong>.
            Integrasi payment gateway dapat ditambahkan
            pada tahap berikutnya.

        </div>


        <div class="d-grid gap-2 mt-4">

            <a
                href="<?= e(APP_URL) ?>/status.php?order=<?= urlencode($orderCode) ?>"
                class="btn btn-primary"
            >
                Lihat Status Pesanan
            </a>

            <a
                href="<?= e(APP_URL) ?>"
                class="btn btn-outline-secondary"
            >
                Kembali ke Beranda
            </a>

        </div>

    </div>

</div>

</body>
</html>
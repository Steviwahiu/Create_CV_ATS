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
| DATA PEMBAYARAN DANA
|--------------------------------------------------------------------------
| Ganti nomor dan nama di bawah ini dengan akun DANA Anda.
*/

$danaNumber = '081527762630';
$danaOwner = 'Stevi Wahiu';


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
| Ambil Payment Terakhir
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
        'DANA',
        $amount
    ]);

} else {

    /*
    | Jika payment sebelumnya belum dibayar,
    | pastikan metode pembayaran tercatat sebagai DANA.
    */

    if (
        $payment['status'] !== 'paid' &&
        $payment['payment_method'] !== 'DANA'
    ) {

        $stmt = $pdo->prepare("
            UPDATE payments
            SET payment_method = 'DANA'
            WHERE id = ?
        ");

        $stmt->execute([
            (int) $payment['id']
        ]);
    }
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
| Tampilkan Halaman Pembayaran
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
        Pembayaran - <?= e(APP_NAME) ?>
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
            margin-bottom: 8px;
        }

        .payment-description {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 0;
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


        /*
        |--------------------------------------------------------------------------
        | DANA
        |--------------------------------------------------------------------------
        */

        .dana-box {
            margin-top: 24px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            background: #ffffff;
        }

        .dana-header {
            background: #fef2f2;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dana-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: #dc2626;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
        }

        .dana-title {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .dana-subtitle {
            color: #6b7280;
            font-size: 12px;
            margin: 2px 0 0;
        }

        .dana-content {
            padding: 18px;
        }

        .dana-label {
            display: block;
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .dana-number-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 9px;
            padding: 11px 12px;
        }

        .dana-number {
            flex: 1;
            font-size: 19px;
            font-weight: 700;
            color: #111827;
            letter-spacing: .4px;
        }

        .dana-copy-button {
            border: none;
            background: #ffffff;
            color: #dc2626;
            border: 1px solid #fecaca;
            border-radius: 7px;
            padding: 7px 11px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
        }

        .dana-copy-button:hover {
            background: #fef2f2;
        }

        .dana-owner {
            margin-top: 8px;
            color: #374151;
            font-size: 13px;
        }

        .dana-instruction {
            margin-top: 18px;
            background: #f9fafb;
            border-radius: 9px;
            padding: 15px 17px;
        }

        .dana-instruction-title {
            color: #111827;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 9px;
        }

        .dana-instruction ol {
            margin: 0;
            padding-left: 20px;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.7;
        }

        .payment-note {
            font-size: 12px;
            line-height: 1.6;
        }

        .btn-primary {
            background: #dc2626;
            border-color: #dc2626;
        }

        .btn-primary:hover {
            background: #b91c1c;
            border-color: #b91c1c;
        }

        @media (max-width: 576px) {

            .payment-box {
                padding: 22px;
            }

            .payment-title {
                font-size: 22px;
            }

            .summary-row {
                align-items: flex-start;
            }

            .summary-value {
                max-width: 60%;
            }

            .dana-number {
                font-size: 16px;
            }

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
            Pembayaran Pesanan
        </h1>

        <p class="payment-description">
            Silakan lakukan pembayaran sesuai dengan nominal
            yang tertera di bawah.
        </p>


        <!--
        |--------------------------------------------------------------------------
        | RINGKASAN PESANAN
        |--------------------------------------------------------------------------
        -->

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
                    Total Pembayaran
                </div>

                <div class="summary-value total">
                    <?= e(formatRupiah($amount)) ?>
                </div>

            </div>

        </div>


        <!--
        |--------------------------------------------------------------------------
        | PEMBAYARAN DANA
        |--------------------------------------------------------------------------
        -->

        <div class="dana-box">

            <div class="dana-header">

                <div class="dana-icon">
                    D
                </div>

                <div>

                    <p class="dana-title">
                        Pembayaran melalui DANA
                    </p>

                    <p class="dana-subtitle">
                        Transfer sesuai nominal pembayaran
                    </p>

                </div>

            </div>


            <div class="dana-content">

                <label class="dana-label">
                    Nomor DANA
                </label>


                <div class="dana-number-box">

                    <div
                        class="dana-number"
                        id="danaNumber"
                    >
                        <?= e($danaNumber) ?>
                    </div>

                    <button
                        type="button"
                        class="dana-copy-button"
                        onclick="copyDanaNumber()"
                    >
                        Salin Nomor
                    </button>

                </div>


                <div class="dana-owner">

                    <strong>
                        Atas nama:
                    </strong>

                    <?= e($danaOwner) ?>

                </div>


                <div class="dana-instruction">

                    <div class="dana-instruction-title">
                        Cara melakukan pembayaran
                    </div>

                    <ol>

                        <li>
                            Buka aplikasi DANA.
                        </li>

                        <li>
                            Pilih menu <strong>Kirim</strong>.
                        </li>

                        <li>
                            Masukkan nomor DANA di atas.
                        </li>

                        <li>
                            Masukkan nominal
                            <strong>
                                <?= e(formatRupiah($amount)) ?>
                            </strong>.
                        </li>

                        <li>
                            Pastikan nama penerima sudah sesuai,
                            kemudian selesaikan pembayaran.
                        </li>

                        <li>
                            Setelah pembayaran berhasil,
                            klik tombol
                            <strong>
                                Saya Sudah Melakukan Pembayaran
                            </strong>.
                        </li>

                    </ol>

                </div>

            </div>

        </div>


        <!--
        |--------------------------------------------------------------------------
        | CATATAN
        |--------------------------------------------------------------------------
        -->

        <div class="alert alert-warning mt-4 payment-note">

            <strong>
                Penting:
            </strong>

            <br>

            Setelah melakukan pembayaran, pembayaran Anda akan
            berstatus <strong>menunggu verifikasi</strong>.
            Admin akan memeriksa pembayaran sebelum CV diproses.

        </div>


        <!--
        |--------------------------------------------------------------------------
        | BUTTON
        |--------------------------------------------------------------------------
        -->

        <div class="d-grid gap-2 mt-4">

            <a
                href="<?= e(APP_URL) ?>/status.php?order=<?= urlencode($orderCode) ?>"
                class="btn btn-primary"
            >
                <i class="bi bi-check-circle me-1"></i>
                Saya Sudah Melakukan Pembayaran
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


<script>

function copyDanaNumber()
{
    const numberElement =
        document.getElementById('danaNumber');

    const number =
        numberElement.innerText.trim();

    if (
        navigator.clipboard &&
        window.isSecureContext
    ) {

        navigator.clipboard
            .writeText(number)
            .then(function () {

                showCopySuccess();

            })
            .catch(function () {

                fallbackCopy(number);

            });

    } else {

        fallbackCopy(number);

    }
}


function fallbackCopy(text)
{
    const textarea =
        document.createElement('textarea');

    textarea.value = text;

    textarea.style.position = 'fixed';
    textarea.style.left = '-9999px';

    document.body.appendChild(textarea);

    textarea.focus();
    textarea.select();

    try {

        document.execCommand('copy');

        showCopySuccess();

    } catch (error) {

        alert(
            'Nomor DANA: ' +
            text
        );

    }

    document.body.removeChild(textarea);
}


function showCopySuccess()
{
    const button =
        document.querySelector(
            '.dana-copy-button'
        );

    const originalText =
        button.innerText;

    button.innerText =
        'Tersalin ✓';

    setTimeout(function () {

        button.innerText =
            originalText;

    }, 2000);
}

</script>

</body>
</html>
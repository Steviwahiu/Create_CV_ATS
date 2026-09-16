<?php

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

cleanupExpiredOrders($pdo);
/*
|--------------------------------------------------------------------------
| Fungsi hapus pesanan yang sudah kedaluwarsa
|--------------------------------------------------------------------------
*/

function deleteExpiredOrder(PDO $pdo, int $orderId)
{
    try {

        $pdo->beginTransaction();

        /*
        |--------------------------------------------------------------
        | Hapus data yang berhubungan dengan order
        |--------------------------------------------------------------
        */

        $tables = [
            'cv_education',
            'cv_experience',
            'cv_organization',
            'cv_skills',
            'cv_languages',
            'cv_personal',
            'payments'
        ];

        foreach ($tables as $table) {

            $stmt = $pdo->prepare(
                "DELETE FROM {$table} WHERE order_id = ?"
            );

            $stmt->execute([$orderId]);
        }


        /*
        |--------------------------------------------------------------
        | Hapus pesanan utama
        |--------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            DELETE FROM cv_orders
            WHERE id = ?
        ");

        $stmt->execute([$orderId]);


        $pdo->commit();

        return true;

    } catch (Throwable $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        return false;
    }
}


/*
|--------------------------------------------------------------------------
| Ambil kode pesanan
|--------------------------------------------------------------------------
*/

$orderCode = trim(
    $_GET['order'] ?? ''
);


if ($orderCode === '') {
    die('Kode pesanan tidak ditemukan.');
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
        o.amount,
        o.payment_status,
        o.order_status,
        o.pdf_file,
        o.email_sent_at,
        o.created_at,

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

$stmt->execute([
    $orderCode
]);

$order = $stmt->fetch();




/*
|--------------------------------------------------------------------------
| Pesanan tidak ditemukan
|--------------------------------------------------------------------------
*/

if (!$order) {
    ?>

    <!DOCTYPE html>
    <html lang="id">

    <head>

        <meta charset="UTF-8">

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
        >

        <title>Pesanan Tidak Ditemukan - <?= e(APP_NAME) ?></title>

        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
        >

        <link
            rel="stylesheet"
            href="<?= e(ASSETS_URL) ?>/css/style.css"
        >

    </head>

    <body>

        <div class="container py-5">

            <div
                class="card border-0 shadow-sm mx-auto"
                style="max-width: 600px;"
            >

                <div class="card-body p-5 text-center">

                    <div
                        class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center mx-auto mb-4"
                        style="width:80px;height:80px;font-size:32px;"
                    >
                        !
                    </div>

                    <h3 class="fw-bold mb-3">
                        Pesanan Tidak Ditemukan
                    </h3>

                    <p class="text-muted mb-4">
                        Pesanan mungkin sudah kedaluwarsa dan
                        telah dihapus dari sistem.
                    </p>

                    <a
                        href="<?= e(APP_URL) ?>"
                        class="btn btn-primary"
                    >
                        Kembali ke Beranda
                    </a>

                </div>

            </div>

        </div>

    </body>

    </html>

    <?php
    exit;
}


/*
|--------------------------------------------------------------------------
| Waktu kedaluwarsa
|--------------------------------------------------------------------------
|
| Batas pembayaran = 24 jam sejak created_at
|
*/

$createdAt = strtotime(
    $order['created_at']
);

$expiredAt = $createdAt + (24 * 60 * 60);

$currentTime = time();


/*
|--------------------------------------------------------------------------
| Cek apakah pembayaran sudah lewat 24 jam
|--------------------------------------------------------------------------
|
| Hanya pesanan yang belum dibayar yang akan dihapus.
|
*/

$isUnpaid =
    in_array(
        $order['payment_status'],
        ['unpaid', 'pending'],
        true
    );


$isWaitingPayment =
    $order['order_status'] === 'waiting_payment';


if (
    $isUnpaid &&
    $isWaitingPayment &&
    $currentTime >= $expiredAt
) {

    /*
    |--------------------------------------------------------------
    | Hapus pesanan
    |--------------------------------------------------------------
    */

    $deleted = deleteExpiredOrder(
        $pdo,
        (int) $order['id']
    );


    if ($deleted) {

        ?>

        <!DOCTYPE html>
        <html lang="id">

        <head>

            <meta charset="UTF-8">

            <meta
                name="viewport"
                content="width=device-width, initial-scale=1"
            >

            <title>
                Pesanan Kedaluwarsa - <?= e(APP_NAME) ?>
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

                .expired-page {
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    padding: 40px 0;
                    background: #f8fafc;
                }

                .expired-card {
                    max-width: 620px;
                    margin: auto;
                    background: #ffffff;
                    border: 1px solid #e5e7eb;
                    border-radius: 18px;
                    padding: 40px;
                    box-shadow: 0 10px 30px rgba(0,0,0,.05);
                }

                .expired-icon {
                    width: 80px;
                    height: 80px;
                    border-radius: 50%;
                    background: #fee2e2;
                    color: #dc2626;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 20px;
                    font-size: 34px;
                    font-weight: bold;
                }

            </style>

        </head>

        <body>

        <div class="expired-page">

            <div class="container">

                <div class="expired-card text-center">

                    <div class="expired-icon">
                        !
                    </div>

                    <h2 class="fw-bold mb-3">
                        Pesanan Kedaluwarsa
                    </h2>

                    <p class="text-muted mb-3">

                        Waktu pembayaran untuk pesanan

                        <strong>
                            <?= e($orderCode) ?>
                        </strong>

                        telah melewati batas
                        <strong>24 jam</strong>.

                    </p>

                    <p class="text-muted mb-4">

                        Pesanan tersebut telah dihapus
                        secara otomatis dari sistem karena
                        pembayaran belum dilakukan.

                    </p>

                    <a
                        href="<?= e(APP_URL) ?>"
                        class="btn btn-primary px-4"
                    >
                        Buat Pesanan Baru
                    </a>

                </div>

            </div>

        </div>

        </body>

        </html>

        <?php

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Hitung sisa waktu pembayaran
|--------------------------------------------------------------------------
*/

$remainingSeconds = max(
    0,
    $expiredAt - time()
);

$remainingHours = floor(
    $remainingSeconds / 3600
);

$remainingMinutes = floor(
    ($remainingSeconds % 3600) / 60
);

$remainingSecondsOnly = $remainingSeconds % 60;


/*
|--------------------------------------------------------------------------
| Status pembayaran
|--------------------------------------------------------------------------
*/

$paymentStatus =
    $order['payment_status'];


switch ($paymentStatus) {

    case 'paid':

        $paymentLabel =
            'Pembayaran Berhasil';

        $paymentClass =
            'success';

        break;


    case 'pending':

        $paymentLabel =
            'Menunggu Pembayaran';

        $paymentClass =
            'warning';

        break;


    case 'failed':

        $paymentLabel =
            'Pembayaran Gagal';

        $paymentClass =
            'danger';

        break;


    case 'expired':

        $paymentLabel =
            'Pembayaran Kedaluwarsa';

        $paymentClass =
            'secondary';

        break;


    default:

        $paymentLabel =
            'Belum Dibayar';

        $paymentClass =
            'secondary';

        break;
}


/*
|--------------------------------------------------------------------------
| Status pesanan
|--------------------------------------------------------------------------
*/

$orderStatus =
    $order['order_status'];


switch ($orderStatus) {

    case 'waiting_payment':

        $orderLabel =
            'Menunggu Pembayaran';

        break;


    case 'processing':

        $orderLabel =
            'Sedang Diproses';

        break;


    case 'completed':

        $orderLabel =
            'Selesai';

        break;


    case 'cancelled':

        $orderLabel =
            'Dibatalkan';

        break;


    default:

        $orderLabel =
            'Draft';

        break;
}
/*
|--------------------------------------------------------------------------
| WhatsApp Admin
|--------------------------------------------------------------------------
| Gunakan format internasional tanpa tanda +
| Contoh: 628123456789
*/

$adminWhatsApp = '6281527762630';


/*
|--------------------------------------------------------------------------
| Pesan WhatsApp Otomatis
|--------------------------------------------------------------------------
*/

$whatsappMessage =
    "Halo Admin, saya ingin menanyakan pesanan CV saya.\n\n" .
    "Kode Pesanan: " . ($order['order_code'] ?? '-') . "\n" .
    "Nama: " . ($order['full_name'] ?? '-') . "\n" .
    "Email: " . ($order['email'] ?? '-') . "\n" .
    "Template: " . ($order['template_name'] ?? '-') . "\n" .
    "Total: " . formatRupiah($order['amount'] ?? 0) . "\n" .
    "Status Pembayaran: " . ($paymentStatus ?? '-') . "\n" .
    "Status Pesanan: " . ($order['order_status'] ?? '-') . "\n\n" .
    "Mohon bantuannya terkait pesanan saya. Terima kasih.";


$whatsappUrl =
    'https://wa.me/' .
    $adminWhatsApp .
    '?text=' .
    urlencode($whatsappMessage);

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Status Pesanan - <?= e(APP_NAME) ?>
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

        .status-page {
            min-height: 100vh;
            padding: 60px 0;
            background: #f8fafc;
        }

        .status-card {
            max-width: 700px;
            margin: auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0,0,0,.05);
        }

        .status-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
            font-weight: bold;
        }

        .status-icon.success {
            background: #dcfce7;
            color: #16a34a;
        }

        .status-icon.warning {
            background: #fef3c7;
            color: #d97706;
        }

        .status-icon.danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .status-icon.secondary {
            background: #e5e7eb;
            color: #4b5563;
        }

        .order-info {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin-top: 25px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #6b7280;
        }

        .info-value {
            font-weight: 600;
            text-align: right;
        }

        .countdown-box {
            margin-top: 20px;
            padding: 18px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 12px;
            text-align: center;
        }

        .countdown-label {
            font-size: 13px;
            color: #9a3412;
            margin-bottom: 5px;
        }

        .countdown {
            font-size: 28px;
            font-weight: 700;
            color: #dc2626;
            letter-spacing: 1px;
        }

    </style>

</head>


<body>

<div class="status-page">

    <div class="container">

        <div class="status-card">


            <!-- =================================================
                 STATUS ICON
            ================================================== -->

            <div class="text-center">

                <?php if (
                    $paymentStatus === 'paid'
                ): ?>

                    <div class="status-icon success">
                        ✓
                    </div>

                <?php elseif (
                    $paymentStatus === 'pending'
                ): ?>

                    <div class="status-icon warning">
                        !
                    </div>

                <?php else: ?>

                    <div class="status-icon danger">
                        !
                    </div>

                <?php endif; ?>


                <h2 class="fw-bold mb-2">

                    <?= e(
                        $paymentLabel
                    ) ?>

                </h2>


                <p class="text-muted mb-0">

                    Status pesanan:

                    <strong>
                        <?= e(
                            $orderLabel
                        ) ?>
                    </strong>

                </p>

            </div>


            <!-- =================================================
                 INFORMASI PESANAN
            ================================================== -->

            <div class="order-info">


                <div class="info-row">

                    <span class="info-label">
                        Kode Pesanan
                    </span>

                    <span class="info-value">
                        <?= e(
                            $order['order_code']
                        ) ?>
                    </span>

                </div>


                <div class="info-row">

                    <span class="info-label">
                        Nama
                    </span>

                    <span class="info-value">
                        <?= e(
                            $order['full_name'] ?? '-'
                        ) ?>
                    </span>

                </div>


                <div class="info-row">

                    <span class="info-label">
                        Email
                    </span>

                    <span class="info-value">
                        <?= e(
                            $order['email']
                        ) ?>
                    </span>

                </div>


                <div class="info-row">

                    <span class="info-label">
                        Template
                    </span>

                    <span class="info-value">
                        <?= e(
                            $order['template_name'] ?? '-'
                        ) ?>
                    </span>

                </div>


                <div class="info-row">

                    <span class="info-label">
                        Total
                    </span>

                    <span class="info-value">
                        <?= formatRupiah(
                            $order['amount']
                        ) ?>
                    </span>

                </div>


                <div class="info-row">

                    <span class="info-label">
                        Pembayaran
                    </span>

                    <span class="info-value">

                        <span
                            class="badge text-bg-<?= e(
                                $paymentClass
                            ) ?>"
                        >

                            <?= e(
                                $paymentLabel
                            ) ?>

                        </span>

                    </span>

                </div>


                <div class="info-row">

                    <span class="info-label">
                        Proses CV
                    </span>

                    <span class="info-value">
                        <?= e(
                            $orderLabel
                        ) ?>
                    </span>

                </div>


            </div>


            <!-- =================================================
                 COUNTDOWN 24 JAM
            ================================================== -->

            <?php if (
                $paymentStatus !== 'paid' &&
                $orderStatus === 'waiting_payment'
            ): ?>

                <div
                    class="countdown-box"
                    id="countdownBox"
                >

                    <div class="countdown-label">
                        Batas waktu pembayaran
                    </div>


                    <div
                        class="countdown"
                        id="countdown"
                    >

                        <?= sprintf(
                            '%02d:%02d:%02d',
                            $remainingHours,
                            $remainingMinutes,
                            $remainingSecondsOnly
                        ) ?>

                    </div>


                    <div class="small text-muted mt-2">

                        Pembayaran harus dilakukan
                        dalam waktu 24 jam sejak pesanan dibuat.

                    </div>

                </div>


                <script>

                    let remainingSeconds =
                        <?= (int) $remainingSeconds ?>;


                    const countdown =
                        document.getElementById(
                            'countdown'
                        );


                    const countdownBox =
                        document.getElementById(
                            'countdownBox'
                        );


                    function updateCountdown()
                    {
                        if (
                            remainingSeconds <= 0
                        ) {

                            countdown.innerHTML =
                                '00:00:00';

                            countdownBox.innerHTML = `

                                <div class="text-danger fw-bold">
                                    Waktu pembayaran telah habis.
                                </div>

                                <div class="small text-muted mt-1">
                                    Pesanan akan dihapus dari sistem.
                                </div>

                            `;

                            return;
                        }


                        const hours =
                            Math.floor(
                                remainingSeconds / 3600
                            );


                        const minutes =
                            Math.floor(
                                (
                                    remainingSeconds % 3600
                                ) / 60
                            );


                        const seconds =
                            remainingSeconds % 60;


                        countdown.innerHTML =
                            String(hours).padStart(2, '0') +
                            ':' +
                            String(minutes).padStart(2, '0') +
                            ':' +
                            String(seconds).padStart(2, '0');


                        remainingSeconds--;

                    }


                    updateCountdown();


                    setInterval(
                        updateCountdown,
                        1000
                    );

                </script>

            <?php endif; ?>


            <!-- =================================================
                 PESAN PENDING
            ================================================== -->

            <?php if (
                $paymentStatus === 'pending'
            ): ?>

                <div
                    class="alert alert-warning mt-4"
                >

                    <strong>
                        Pembayaran masih menunggu konfirmasi.
                    </strong>

                    <br>

                    Setelah pembayaran dikonfirmasi
                    oleh admin, pesanan akan diproses.

                    <br><br>

                    Pastikan pembayaran dilakukan sebelum
                    batas waktu 24 jam berakhir.

                </div>

            <?php endif; ?>


            <!-- =================================================
                 PEMBAYARAN BERHASIL
            ================================================== -->

            <?php if (
                $paymentStatus === 'paid'
            ): ?>

                <?php if (
                    $orderStatus === 'processing'
                ): ?>

                    <div
                        class="alert alert-info mt-4"
                    >

                        Pembayaran telah berhasil.

                        CV Anda sedang diproses
                        oleh admin.

                    </div>


                <?php elseif (
                    $orderStatus === 'completed'
                ): ?>

                    <div
                        class="alert alert-success mt-4"
                    >

                        CV Anda telah selesai diproses.

                    </div>


                    <?php if (
                        !empty(
                            $order['pdf_file']
                        )
                    ): ?>

                        <a
                            href="<?= e(
                                APP_URL .
                                '/' .
                                $order['pdf_file']
                            ) ?>"
                            class="btn btn-primary w-100"
                            target="_blank"
                        >

                            Lihat / Download CV

                        </a>

                    <?php endif; ?>

                <?php endif; ?>

            <?php endif; ?>


            <!-- =================================================
                BUTTON
            ================================================== -->

            <div class="d-flex gap-2 mt-4 flex-wrap">

                <a
                    href="<?= e(APP_URL) ?>"
                    class="btn btn-outline-secondary flex-fill"
                >
                    Kembali ke Beranda
                </a>


                <?php if (
                    $paymentStatus !== 'paid'
                ): ?>

                    <a
                        href="<?= e(
                            APP_URL .
                            '/pembayaran.php?order=' .
                            urlencode($orderCode)
                        ) ?>"
                        class="btn btn-primary flex-fill"
                    >
                        Kembali ke Pembayaran
                    </a>

                <?php endif; ?>

            </div>


            <!-- =================================================
                WHATSAPP ADMIN
            ================================================== -->

            <div class="mt-3">

                <a
                    href="<?= e($whatsappUrl) ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn btn-success w-100"
                >

                    <span style="font-size: 18px;">
                        WhatsApp
                    </span>

                    &nbsp;

                    Chat Admin

                </a>

            </div>


        </div>

    </div>

</div>

</body>

</html>
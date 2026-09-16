<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

requireAdmin();

/*
|--------------------------------------------------------------------------
| Ambil ID pesanan
|--------------------------------------------------------------------------
*/

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    die('ID pesanan tidak valid.');
}

/*
|--------------------------------------------------------------------------
| Ambil pesanan
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        o.*,

        p.full_name,
        p.professional_title,
        p.phone,
        p.city,
        p.address,
        p.linkedin,
        p.portfolio,
        p.summary,
        p.photo,

        t.name AS template_name,
        t.description AS template_description

    FROM cv_orders o

    LEFT JOIN cv_personal p
        ON p.order_id = o.id

    LEFT JOIN cv_templates t
        ON t.id = o.template_id

    WHERE o.id = ?

    LIMIT 1
");

$stmt->execute([$id]);

$order = $stmt->fetch();

if (!$order) {
    die('Pesanan tidak ditemukan.');
}

/*
|--------------------------------------------------------------------------
| Pendidikan
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM cv_education
    WHERE order_id = ?
    ORDER BY end_year DESC, start_year DESC, id DESC
");

$stmt->execute([$id]);

$educations = $stmt->fetchAll();

/*
|--------------------------------------------------------------------------
| Pengalaman
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM cv_experience
    WHERE order_id = ?
    ORDER BY end_date DESC, start_date DESC, id DESC
");

$stmt->execute([$id]);

$experiences = $stmt->fetchAll();

/*
|--------------------------------------------------------------------------
| Organisasi
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM cv_organization
    WHERE order_id = ?
    ORDER BY end_date DESC, start_date DESC, id DESC
");

$stmt->execute([$id]);

$organizations = $stmt->fetchAll();

/*
|--------------------------------------------------------------------------
| Skills
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM cv_skills
    WHERE order_id = ?
    ORDER BY id ASC
");

$stmt->execute([$id]);

$skills = $stmt->fetchAll();

/*
|--------------------------------------------------------------------------
| Bahasa
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM cv_languages
    WHERE order_id = ?
    ORDER BY id ASC
");

$stmt->execute([$id]);

$languages = $stmt->fetchAll();

/*
|--------------------------------------------------------------------------
| Pembayaran terakhir
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

$stmt->execute([$id]);

$payment = $stmt->fetch();

/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
*/

$paymentStatus = $order['payment_status'];
$orderStatus = $order['order_status'];

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
        Detail <?= e($order['order_code']) ?> -
        <?= e(APP_NAME) ?>
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

        .admin-page {
            padding: 35px 0 60px;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h1 {
            font-size: 26px;
            font-weight: 700;
        }

        .card-box {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-row:last-child {
            border-bottom: 0;
        }

        .info-label {
            color: #6b7280;
        }

        .info-value {
            font-weight: 500;
            text-align: right;
        }

        .section-item {
            padding: 15px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .section-item:last-child {
            border-bottom: 0;
        }

        .section-item h6 {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .section-item p {
            margin-bottom: 5px;
            color: #6b7280;
            white-space: pre-line;
        }

        .action-card {
            position: sticky;
            top: 20px;
        }

        .btn-danger {
            background: #dc2626;
            border-color: #dc2626;
        }

        .btn-danger:hover {
            background: #b91c1c;
            border-color: #b91c1c;
        }

    </style>

</head>

<body>
<?php if (
    ($_GET['success'] ?? '') === 'email'
): ?>

    <div class="alert alert-success">
        <i class="bi bi-check-circle me-1"></i>
        CV berhasil dikirim ke email pelanggan.
    </div>

<?php endif; ?>
<div class="container admin-page">

    <div class="page-header">

        <a
            href="<?= e('pesanan.php') ?>"
            class="text-danger text-decoration-none"
        >
            ← Kembali ke Pesanan
        </a>

        <h1 class="mt-3">
            <?= e($order['order_code']) ?>
        </h1>

        <p class="text-muted mb-0">
            Detail data CV pelanggan
        </p>

    </div>


    <div class="row g-4">

        <div class="col-lg-8">


            <!-- DATA PESANAN -->

            <div class="card-box">

                <div class="card-title">
                    Informasi Pesanan
                </div>

                <div class="info-row">

                    <span class="info-label">
                        Kode Pesanan
                    </span>

                    <span class="info-value">
                        <?= e($order['order_code']) ?>
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        Email
                    </span>

                    <span class="info-value">
                        <?= e($order['email']) ?>
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        Template
                    </span>

                    <span class="info-value">
                        <?= e($order['template_name'] ?? '-') ?>
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        Total
                    </span>

                    <span class="info-value">
                        <?= formatRupiah($order['amount']) ?>
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        Pembayaran
                    </span>

                    <span class="info-value">

                        <span class="badge
                            <?= $paymentStatus === 'paid'
                                ? 'bg-success'
                                : ($paymentStatus === 'pending'
                                    ? 'bg-warning text-dark'
                                    : 'bg-secondary') ?>"
                        >
                            <?= e($paymentStatus) ?>
                        </span>

                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        Status Pesanan
                    </span>

                    <span class="info-value">
                        <?= e($orderStatus) ?>
                    </span>

                </div>

            </div>


            <!-- DATA PERSONAL -->

            <div class="card-box">

                <div class="card-title">
                    Data Personal
                </div>

                <div class="info-row">

                    <span class="info-label">
                        Nama Lengkap
                    </span>

                    <span class="info-value">
                        <?= e($order['full_name'] ?? '-') ?>
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        Jabatan Profesional
                    </span>

                    <span class="info-value">
                        <?= e($order['professional_title'] ?? '-') ?>
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        No. HP
                    </span>

                    <span class="info-value">
                        <?= e($order['phone'] ?? '-') ?>
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        Kota
                    </span>

                    <span class="info-value">
                        <?= e($order['city'] ?? '-') ?>
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        Alamat
                    </span>

                    <span class="info-value">
                        <?= nl2br(e($order['address'] ?? '-')) ?>
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        LinkedIn
                    </span>

                    <span class="info-value">
                        <?= e($order['linkedin'] ?? '-') ?>
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        Portfolio
                    </span>

                    <span class="info-value">
                        <?= e($order['portfolio'] ?? '-') ?>
                    </span>

                </div>

                <div class="mt-3">

                    <strong>
                        Ringkasan Profil
                    </strong>

                    <p class="text-muted mt-2 mb-0">
                        <?= nl2br(e($order['summary'] ?? '-')) ?>
                    </p>

                </div>

            </div>


            <!-- PENDIDIKAN -->

            <div class="card-box">

                <div class="card-title">
                    Pendidikan
                </div>

                <?php if (empty($educations)): ?>

                    <p class="text-muted mb-0">
                        Tidak ada data pendidikan.
                    </p>

                <?php else: ?>

                    <?php foreach ($educations as $education): ?>

                        <div class="section-item">

                            <h6>
                                <?= e($education['institution']) ?>
                            </h6>

                            <p>
                                <?= e($education['major'] ?? '') ?>
                                <?php if (!empty($education['degree'])): ?>
                                    · <?= e($education['degree']) ?>
                                <?php endif; ?>
                            </p>

                            <small class="text-muted">

                                <?= e($education['start_year']) ?>

                                -

                                <?= e($education['end_year']) ?>

                                <?php if (!empty($education['gpa'])): ?>

                                    · IPK:
                                    <?= e($education['gpa']) ?>

                                <?php endif; ?>

                            </small>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>


            <!-- PENGALAMAN -->

            <div class="card-box">

                <div class="card-title">
                    Pengalaman Kerja
                </div>

                <?php if (empty($experiences)): ?>

                    <p class="text-muted mb-0">
                        Tidak ada data pengalaman kerja.
                    </p>

                <?php else: ?>

                    <?php foreach ($experiences as $experience): ?>

                        <div class="section-item">

                            <h6>
                                <?= e($experience['position']) ?>
                            </h6>

                            <p>
                                <?= e($experience['company']) ?>

                                <?php if (!empty($experience['location'])): ?>

                                    · <?= e($experience['location']) ?>

                                <?php endif; ?>

                            </p>

                            <small class="text-muted">

                                <?= e($experience['start_date']) ?>

                                -

                                <?= $experience['is_current']
                                    ? 'Sekarang'
                                    : e($experience['end_date']) ?>

                            </small>

                            <?php if (!empty($experience['description'])): ?>

                                <p class="mt-2 mb-0">
                                    <?= nl2br(
                                        e($experience['description'])
                                    ) ?>
                                </p>

                            <?php endif; ?>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>


            <!-- ORGANISASI -->

            <div class="card-box">

                <div class="card-title">
                    Organisasi
                </div>

                <?php if (empty($organizations)): ?>

                    <p class="text-muted mb-0">
                        Tidak ada data organisasi.
                    </p>

                <?php else: ?>

                    <?php foreach ($organizations as $organization): ?>

                        <div class="section-item">

                            <h6>
                                <?= e($organization['organization']) ?>
                            </h6>

                            <p>
                                <?= e($organization['position']) ?>
                            </p>

                            <small class="text-muted">

                                <?= e($organization['start_date']) ?>

                                -

                                <?= e($organization['end_date']) ?>

                            </small>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>


            <!-- SKILLS -->

            <div class="card-box">

                <div class="card-title">
                    Keahlian
                </div>

                <?php if (empty($skills)): ?>

                    <p class="text-muted mb-0">
                        Tidak ada data keahlian.
                    </p>

                <?php else: ?>

                    <div class="d-flex flex-wrap gap-2">

                        <?php foreach ($skills as $skill): ?>

                            <span class="badge bg-light text-dark border">
                                <?= e($skill['skill']) ?>
                            </span>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>


            <!-- BAHASA -->

            <div class="card-box">

                <div class="card-title">
                    Bahasa
                </div>

                <?php if (empty($languages)): ?>

                    <p class="text-muted mb-0">
                        Tidak ada data bahasa.
                    </p>

                <?php else: ?>

                    <?php foreach ($languages as $language): ?>

                        <div class="section-item">

                            <strong>
                                <?= e($language['language']) ?>
                            </strong>

                            <div class="text-muted">

                                <?= e(
                                    $language['proficiency']
                                ) ?>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div>


        <!-- SIDEBAR -->

        <div class="col-lg-4">

            <div class="action-card">


                <!-- PEMBAYARAN -->

                <div class="card-box">

                    <div class="card-title">
                        Informasi Pembayaran
                    </div>

                    <?php if ($payment): ?>

                        <div class="info-row">

                            <span class="info-label">
                                Status
                            </span>

                            <span class="info-value">
                                <?= e($payment['status']) ?>
                            </span>

                        </div>

                        <div class="info-row">

                            <span class="info-label">
                                Metode
                            </span>

                            <span class="info-value">
                                <?= e(
                                    $payment['payment_method'] ?? '-'
                                ) ?>
                            </span>

                        </div>

                        <div class="info-row">

                            <span class="info-label">
                                Jumlah
                            </span>

                            <span class="info-value">
                                <?= formatRupiah(
                                    $payment['amount']
                                ) ?>
                            </span>

                        </div>

                        <div class="info-row">

                            <span class="info-label">
                                Transaksi
                            </span>

                            <span class="info-value">
                                <?= e(
                                    $payment['transaction_id'] ?? '-'
                                ) ?>
                            </span>

                        </div>

                        <?php if (!empty($payment['paid_at'])): ?>

                            <div class="info-row">

                                <span class="info-label">
                                    Dibayar
                                </span>

                                <span class="info-value">
                                    <?= e($payment['paid_at']) ?>
                                </span>

                            </div>

                        <?php endif; ?>

                    <?php else: ?>

                        <p class="text-muted mb-0">
                            Belum terdapat data pembayaran.
                        </p>

                    <?php endif; ?>

                </div>


                <!-- AKSI -->

                <div class="card-box">

                    <div class="card-title">
                        Aksi Pesanan
                    </div>

                    <?php if ($paymentStatus !== 'paid'): ?>

                        <form
                            method="POST"
                            action="verifikasi-pembayaran.php"
                        >

                            <?= csrfField() ?>

                            <input
                                type="hidden"
                                name="order_id"
                                value="<?= $order['id'] ?>"
                            >

                            <button
                                type="submit"
                                class="btn btn-success w-100"
                                onclick="
                                    return confirm(
                                        'Konfirmasi bahwa pembayaran pesanan ini telah diterima?'
                                    );
                                "
                            >
                                ✓ Verifikasi Pembayaran
                            </button>

                        </form>

                    <?php else: ?>

                        <div class="alert alert-success">

                            Pembayaran sudah dikonfirmasi.

                        </div>

                    <?php endif; ?>


                    <?php if ($paymentStatus === 'paid'): ?>

                        <form
                            method="POST"
                            action="proses-pesanan.php"
                            class="mt-2"
                        >

                            <?= csrfField() ?>

                            <input
                                type="hidden"
                                name="order_id"
                                value="<?= $order['id'] ?>"
                            >

                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >
                                Proses CV
                            </button>

                        </form>

                    <?php endif; ?>


                    <?php if (!empty($order['pdf_file'])): ?>

                        <a
                            href="<?= e(
                                APP_URL .
                                '/' .
                                $order['pdf_file']
                            ) ?>"
                            target="_blank"
                            class="btn btn-outline-danger w-100 mt-2"
                        >
                            Lihat PDF
                        </a>

                    <?php endif; ?>
                    <?php if (
                        !empty($order['pdf_file']) &&
                        $order['order_status'] === 'completed'
                    ): ?>

                        <form
                            action="<?= ADMIN_URL ?>/kirim-email.php"
                            method="POST"
                            class="d-inline"
                            onsubmit="
                                return confirm(
                                    'Kirim CV PDF ke email pelanggan?'
                                );
                            "
                        >

                            <?= csrfField() ?>

                            <input
                                type="hidden"
                                name="order_id"
                                value="<?= (int) $order['id'] ?>"
                            >

                            <button
                                type="submit"
                                class="btn btn-success"
                            >
                                <i class="bi bi-envelope me-1"></i>
                                Kirim CV ke Email
                            </button>

                        </form>

                    <?php endif; ?>
                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>
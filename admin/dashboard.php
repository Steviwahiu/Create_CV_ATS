<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();
cleanupExpiredOrders($pdo);

/*
|--------------------------------------------------------------------------
| Statistik
|--------------------------------------------------------------------------
*/

$totalOrders = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM cv_orders
    ")
    ->fetchColumn();


$waitingPayment = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM cv_orders
        WHERE payment_status IN ('unpaid', 'pending')
    ")
    ->fetchColumn();


$processing = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM cv_orders
        WHERE order_status = 'processing'
    ")
    ->fetchColumn();


$completed = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM cv_orders
        WHERE order_status = 'completed'
    ")
    ->fetchColumn();


/*
|--------------------------------------------------------------------------
| Pesanan Terbaru
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        o.id,
        o.order_code,
        o.email,
        o.amount,
        o.payment_status,
        o.order_status,
        o.created_at,

        p.full_name,

        t.name AS template_name

    FROM cv_orders o

    LEFT JOIN cv_personal p
        ON p.order_id = o.id

    LEFT JOIN cv_templates t
        ON t.id = o.template_id

    ORDER BY o.created_at DESC

    LIMIT 10
");

$orders = $stmt->fetchAll();

$admin = adminUser();

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
        Dashboard - <?= e(APP_NAME) ?>
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

        .admin-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }

        .admin-page {
            padding: 35px 0 60px;
        }

        .welcome {
            margin-bottom: 30px;
        }

        .welcome h1 {
            font-size: 28px;
            font-weight: 700;
        }

        .welcome p {
            color: #6b7280;
            margin-bottom: 0;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 22px;
            height: 100%;
        }

        .stat-label {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .stat-number {
            font-size: 30px;
            font-weight: 700;
        }

        .content-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            overflow: hidden;
        }

        .content-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-header h5 {
            margin: 0;
            font-weight: 700;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background: #f8fafc;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            white-space: nowrap;
        }

        .table td {
            font-size: 14px;
            color: #4b5563;
            vertical-align: middle;
        }

        .order-code {
            color: #dc2626;
            font-weight: 600;
            text-decoration: none;
        }

        .order-code:hover {
            color: #b91c1c;
        }

    </style>

</head>

<body>


<!-- NAVBAR -->

<nav class="navbar admin-navbar">

    <div class="container">

        <a
            class="navbar-brand"
            href="<?= e(ADMIN_URL . '/dashboard.php') ?>"
        >
            CV ATS Professional
        </a>


        <div class="d-flex align-items-center gap-3">

            <span class="text-muted small">
                <?= e($admin['name']) ?>
            </span>

            <a
                href="<?= e(ADMIN_URL . '/logout.php') ?>"
                class="btn btn-sm btn-outline-danger"
            >
                Logout
            </a>

        </div>

    </div>

</nav>


<div class="container admin-page">


    <!-- WELCOME -->

    <div class="welcome">

        <h1>
            Dashboard
        </h1>

        <p>
            Selamat datang, <?= e($admin['name']) ?>.
            Kelola pesanan CV dari halaman ini.
        </p>

    </div>


    <!-- STATISTIK -->

    <div class="row g-3 mb-4">

        <div class="col-md-3">

            <div class="stat-card">

                <div class="stat-label">
                    Total Pesanan
                </div>

                <div class="stat-number">
                    <?= $totalOrders ?>
                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="stat-card">

                <div class="stat-label">
                    Menunggu Pembayaran
                </div>

                <div class="stat-number">
                    <?= $waitingPayment ?>
                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="stat-card">

                <div class="stat-label">
                    Sedang Diproses
                </div>

                <div class="stat-number">
                    <?= $processing ?>
                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="stat-card">

                <div class="stat-label">
                    Selesai
                </div>

                <div class="stat-number">
                    <?= $completed ?>
                </div>

            </div>

        </div>

    </div>


    <!-- PESANAN TERBARU -->

    <div class="content-card">

        <div class="content-header">

            <h5>
                Pesanan Terbaru
            </h5>

            <a
                href="<?= e(ADMIN_URL . '/pesanan.php') ?>"
                class="btn btn-sm btn-danger"
            >
                Lihat Semua
            </a>

        </div>


        <?php if (empty($orders)): ?>

            <div class="text-center py-5 text-muted">

                Belum ada pesanan.

            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table">

                    <thead>

                        <tr>

                            <th>No</th>
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>Template</th>
                            <th>Total</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($orders as $index => $order): ?>

                        <tr>

                            <td>
                                <?= $index + 1 ?>
                            </td>

                            <td>

                                <a
                                    href="<?= e(
                                        ADMIN_URL .
                                        '/detail-pesanan.php?id=' .
                                        $order['id']
                                    ) ?>"
                                    class="order-code"
                                >
                                    <?= e($order['order_code']) ?>
                                </a>

                            </td>

                            <td>

                                <div class="fw-semibold text-dark">
                                    <?= e(
                                        $order['full_name'] ?? '-'
                                    ) ?>
                                </div>

                                <small class="text-muted">
                                    <?= e($order['email']) ?>
                                </small>

                            </td>

                            <td>
                                <?= e(
                                    $order['template_name'] ?? '-'
                                ) ?>
                            </td>

                            <td>
                                <?= formatRupiah(
                                    $order['amount']
                                ) ?>
                            </td>

                            <td>

                                <?php if (
                                    $order['payment_status'] === 'paid'
                                ): ?>

                                    <span class="badge bg-success">
                                        Paid
                                    </span>

                                <?php elseif (
                                    $order['payment_status'] === 'pending'
                                ): ?>

                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-secondary">
                                        <?= e(
                                            $order['payment_status']
                                        ) ?>
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <?php if (
                                    $order['order_status'] === 'completed'
                                ): ?>

                                    <span class="badge bg-success">
                                        Selesai
                                    </span>

                                <?php elseif (
                                    $order['order_status'] === 'processing'
                                ): ?>

                                    <span class="badge bg-info text-dark">
                                        Diproses
                                    </span>

                                <?php elseif (
                                    $order['order_status'] === 'waiting_payment'
                                ): ?>

                                    <span class="badge bg-warning text-dark">
                                        Menunggu
                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-secondary">
                                        <?= e(
                                            $order['order_status']
                                        ) ?>
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <a
                                    href="<?= e(
                                        ADMIN_URL .
                                        '/detail-pesanan.php?id=' .
                                        $order['id']
                                    ) ?>"
                                    class="btn btn-sm btn-outline-danger"
                                >
                                    Detail
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>

</body>

</html>
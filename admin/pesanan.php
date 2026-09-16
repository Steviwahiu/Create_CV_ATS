<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

cleanupExpiredOrders($pdo);

$keyword = trim($_GET['keyword'] ?? '');
$paymentStatus = trim($_GET['payment_status'] ?? '');
$orderStatus = trim($_GET['order_status'] ?? '');

$where = [];
$params = [];

if ($keyword !== '') {
    $where[] = "(
        o.order_code LIKE ?
        OR o.email LIKE ?
        OR p.full_name LIKE ?
    )";

    $keywordLike = '%' . $keyword . '%';

    $params[] = $keywordLike;
    $params[] = $keywordLike;
    $params[] = $keywordLike;
}

if ($paymentStatus !== '') {
    $where[] = "o.payment_status = ?";
    $params[] = $paymentStatus;
}

if ($orderStatus !== '') {
    $where[] = "o.order_status = ?";
    $params[] = $orderStatus;
}

$whereSql = '';

if (!empty($where)) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

$stmt = $pdo->prepare("
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

    {$whereSql}

    ORDER BY o.created_at DESC
");

$stmt->execute($params);

$orders = $stmt->fetchAll();


function paymentBadge($status)
{
    switch ($status) {

        case 'paid':
            return '<span class="badge bg-success">Paid</span>';

        case 'pending':
            return '<span class="badge bg-warning text-dark">Pending</span>';

        case 'failed':
            return '<span class="badge bg-danger">Failed</span>';

        case 'expired':
            return '<span class="badge bg-secondary">Expired</span>';

        default:
            return '<span class="badge bg-light text-dark">Unpaid</span>';
    }
}


function orderBadge($status)
{
    switch ($status) {

        case 'waiting_payment':
            return '<span class="badge bg-warning text-dark">Menunggu Pembayaran</span>';

        case 'processing':
            return '<span class="badge bg-info text-dark">Diproses</span>';

        case 'completed':
            return '<span class="badge bg-success">Selesai</span>';

        case 'cancelled':
            return '<span class="badge bg-danger">Dibatalkan</span>';

        default:
            return '<span class="badge bg-secondary">Draft</span>';
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Pesanan - <?= e(APP_NAME) ?></title>

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
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .page-header p {
            color: #6b7280;
            margin: 0;
        }

        .filter-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .table-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: #f8fafc;
            color: #374151;
            font-size: 13px;
            font-weight: 600;
            padding: 14px 16px;
            white-space: nowrap;
        }

        .table tbody td {
            color: #4b5563;
            font-size: 14px;
            padding: 15px 16px;
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

        .btn-primary {
            background: #dc2626;
            border-color: #dc2626;
        }

        .btn-primary:hover {
            background: #b91c1c;
            border-color: #b91c1c;
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #6b7280;
        }

    </style>

</head>

<body>

<div class="container admin-page">

    <div class="page-header">

        <h1>Pesanan CV</h1>

        <p>
            Kelola pesanan, pembayaran, dan proses CV pelanggan.
        </p>

    </div>


    <!-- FILTER -->

    <div class="filter-card">

        <form method="GET">

            <div class="row g-3">

                <div class="col-md-5">

                    <label class="form-label">
                        Cari Pesanan
                    </label>

                    <input
                        type="text"
                        name="keyword"
                        class="form-control"
                        placeholder="Kode pesanan, nama, atau email"
                        value="<?= e($keyword) ?>"
                    >

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Pembayaran
                    </label>

                    <select
                        name="payment_status"
                        class="form-select"
                    >

                        <option value="">
                            Semua
                        </option>

                        <option
                            value="unpaid"
                            <?= $paymentStatus === 'unpaid' ? 'selected' : '' ?>
                        >
                            Unpaid
                        </option>

                        <option
                            value="pending"
                            <?= $paymentStatus === 'pending' ? 'selected' : '' ?>
                        >
                            Pending
                        </option>

                        <option
                            value="paid"
                            <?= $paymentStatus === 'paid' ? 'selected' : '' ?>
                        >
                            Paid
                        </option>

                        <option
                            value="failed"
                            <?= $paymentStatus === 'failed' ? 'selected' : '' ?>
                        >
                            Failed
                        </option>

                        <option
                            value="expired"
                            <?= $paymentStatus === 'expired' ? 'selected' : '' ?>
                        >
                            Expired
                        </option>

                    </select>

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Status Pesanan
                    </label>

                    <select
                        name="order_status"
                        class="form-select"
                    >

                        <option value="">
                            Semua
                        </option>

                        <option
                            value="draft"
                            <?= $orderStatus === 'draft' ? 'selected' : '' ?>
                        >
                            Draft
                        </option>

                        <option
                            value="waiting_payment"
                            <?= $orderStatus === 'waiting_payment' ? 'selected' : '' ?>
                        >
                            Menunggu Pembayaran
                        </option>

                        <option
                            value="processing"
                            <?= $orderStatus === 'processing' ? 'selected' : '' ?>
                        >
                            Diproses
                        </option>

                        <option
                            value="completed"
                            <?= $orderStatus === 'completed' ? 'selected' : '' ?>
                        >
                            Selesai
                        </option>

                        <option
                            value="cancelled"
                            <?= $orderStatus === 'cancelled' ? 'selected' : '' ?>
                        >
                            Dibatalkan
                        </option>

                    </select>

                </div>


                <div class="col-md-1 d-flex align-items-end">

                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                    >
                        Cari
                    </button>

                </div>

            </div>

        </form>

    </div>


    <!-- TABLE -->

    <div class="table-card">

        <?php if (empty($orders)): ?>

            <div class="empty-state">

                <h5 class="mb-2">
                    Belum ada pesanan
                </h5>

                <p class="mb-0">
                    Data pesanan akan muncul di halaman ini.
                </p>

            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Kode Pesanan</th>

                            <th>Pelanggan</th>

                            <th>Template</th>

                            <th>Total</th>

                            <th>Pembayaran</th>

                            <th>Status</th>

                            <th>Tanggal</th>

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
                                    <?= e($order['full_name'] ?? '-') ?>
                                </div>

                                <small class="text-muted">
                                    <?= e($order['email']) ?>
                                </small>

                            </td>

                            <td>
                                <?= e($order['template_name'] ?? '-') ?>
                            </td>

                            <td>
                                <?= formatRupiah($order['amount']) ?>
                            </td>

                            <td>
                                <?= paymentBadge($order['payment_status']) ?>
                            </td>

                            <td>
                                <?= orderBadge($order['order_status']) ?>
                            </td>

                            <td>
                                <?= date(
                                    'd/m/Y H:i',
                                    strtotime($order['created_at'])
                                ) ?>
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
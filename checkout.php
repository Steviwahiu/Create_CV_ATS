<?php

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$orderCode = trim($_GET['order'] ?? '');

if ($orderCode === '') {
    redirect(APP_URL . '/buat-cv.php');
}


/*
|--------------------------------------------------------------------------
| Ambil Data Order
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        o.*,
        p.full_name,
        t.name AS selected_template,
        t.template_folder
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


/*
|--------------------------------------------------------------------------
| ID ORDER
|--------------------------------------------------------------------------
*/

$orderId = (int) $order['id'];


/*
|--------------------------------------------------------------------------
| Validasi Status Order
|--------------------------------------------------------------------------
|
| Struktur database menggunakan:
| draft
| waiting_payment
| processing
| completed
|
*/

$allowedStatuses = [
    'draft',
    'waiting_payment'
];

if (!in_array($order['order_status'], $allowedStatuses, true)) {

    if (
        $order['payment_status'] === 'paid'
    ) {
        redirect(
            APP_URL .
            '/pembayaran.php?order=' .
            urlencode($orderCode)
        );
    }

    die('Pesanan ini tidak dapat diubah kembali.');
}


/*
|--------------------------------------------------------------------------
| Ambil Template Aktif
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        id,
        name,
        description,
        preview,
        template_folder,
        price
    FROM cv_templates
    WHERE status = 'active'
    ORDER BY price ASC, id ASC
");

$templates = $stmt->fetchAll();


/*
|--------------------------------------------------------------------------
| Template Terpilih
|--------------------------------------------------------------------------
*/

$selectedTemplateId = (int) (
    $order['template_id'] ?? 0
);


/*
|--------------------------------------------------------------------------
| Proses Pemilihan Template
|--------------------------------------------------------------------------
*/

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verifyCsrf();

    $templateId = (int) (
        $_POST['template_id'] ?? 0
    );

    if ($templateId <= 0) {

        $error =
            'Silakan pilih salah satu template CV.';

    } else {

        /*
        |--------------------------------------------------------------------------
        | Pastikan Template Benar-benar Aktif
        |--------------------------------------------------------------------------
        */

            $stmt = $pdo->prepare("
                SELECT
                    id,
                    name,
                    description,
                    preview,
                    template_folder,
                    price
                FROM cv_templates
                WHERE id = ?
                AND status = 'active'
                LIMIT 1
            ");

            $stmt->execute([$templateId]);

            $selectedTemplate = $stmt->fetch();

        if (!$selectedTemplate) {

            $error =
                'Template yang dipilih tidak tersedia.';

        } else {

            /*
            |--------------------------------------------------------------------------
            | Harga Diambil dari Database
            |--------------------------------------------------------------------------
            */

            $price = (float) $selectedTemplate['price'];

            try {

                $pdo->beginTransaction();

                /*
                |--------------------------------------------------------------------------
                | Update Order
                |--------------------------------------------------------------------------
                */

                $stmt = $pdo->prepare("
                    UPDATE cv_orders
                    SET
                        template_id = ?,
                        amount = ?,
                        order_status = 'waiting_payment'
                    WHERE id = ?
                ");

                $stmt->execute([
                    $selectedTemplate['id'],
                    $price,
                    $orderId
                ]);

                $pdo->commit();

                /*
                |--------------------------------------------------------------------------
                | Lanjut ke Pembayaran
                |--------------------------------------------------------------------------
                */

                redirect(
                    APP_URL .
                    '/pembayaran.php?order=' .
                    urlencode($orderCode)
                );

            } catch (Throwable $e) {

                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $error =
                    'Terjadi kesalahan saat menyimpan template. ' .
                    'Silakan coba kembali.';

                if (
                    defined('APP_DEBUG') &&
                    APP_DEBUG === true
                ) {
                    $error .=
                        ' ' .
                        $e->getMessage();
                }
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| Jika Sebelumnya Sudah Memilih Template
|--------------------------------------------------------------------------
*/

$displayTotal = (float) (
    $order['amount'] ?? 0
);

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
        Pilih Template - <?= e(APP_NAME) ?>
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

        .checkout-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }

        .checkout-wrapper {
            padding: 45px 15px 70px;
        }

        .checkout-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 35px;
        }

        .checkout-header h1 {
            font-size: 30px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
        }

        .checkout-header p {
            color: #6b7280;
            margin-bottom: 0;
        }

        .order-info {
            max-width: 950px;
            margin: 0 auto 25px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px 20px;
        }

        .order-info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .order-label {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 3px;
        }

        .order-value {
            color: #111827;
            font-weight: 600;
            font-size: 14px;
        }

        .template-grid {
            max-width: 950px;
            margin: 0 auto;
        }

        .template-card-wrapper {
            height: 100%;
        }

        .template-card {
            position: relative;
            height: 100%;
            background: #ffffff;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            overflow: hidden;
            cursor: pointer;
            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                transform 0.2s ease;
        }

        .template-card:hover {
            border-color: #fca5a5;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.07);
        }

        .template-card.selected {
            border-color: #dc2626;
            box-shadow:
                0 0 0 3px rgba(220, 38, 38, 0.10);
        }

        .template-radio {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 2;
            width: 20px;
            height: 20px;
            accent-color: #dc2626;
            cursor: pointer;
        }

        .template-preview {
            height: 280px;
            background: #f3f4f6;
            padding: 18px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .template-preview img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #ffffff;
            border: 1px solid #e5e7eb;
        }

        .fake-cv {
            width: 150px;
            height: 215px;
            background: #ffffff;
            border: 1px solid #d1d5db;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.10);
            padding: 14px;
        }

        .fake-cv-header {
            border-bottom: 2px solid #111827;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .fake-line {
            height: 4px;
            background: #d1d5db;
            border-radius: 3px;
            margin-bottom: 5px;
        }

        .fake-line.long {
            width: 90%;
        }

        .fake-line.medium {
            width: 65%;
        }

        .fake-line.short {
            width: 40%;
        }

        .fake-title {
            height: 7px;
            width: 70%;
            background: #111827;
            margin-bottom: 5px;
            border-radius: 2px;
        }

        .fake-section {
            height: 5px;
            width: 45%;
            background: #374151;
            margin-top: 12px;
            margin-bottom: 7px;
        }

        .template-body {
            padding: 20px;
        }

        .template-name {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }

        .template-description {
            min-height: 42px;
            font-size: 13px;
            line-height: 1.5;
            color: #6b7280;
            margin-bottom: 15px;
        }

        .template-price {
            font-size: 20px;
            font-weight: 700;
            color: #dc2626;
        }

        .template-select-text {
            font-size: 12px;
            color: #6b7280;
        }

        .checkout-bottom {
            max-width: 950px;
            margin: 30px auto 0;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 20px;
        }

        .summary-label {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .summary-template {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
        }

        .summary-price {
            font-size: 24px;
            font-weight: 700;
            color: #dc2626;
        }

        .checkout-note {
            font-size: 12px;
            color: #6b7280;
            margin-top: 10px;
        }

        .btn-primary {
            background-color: #dc2626;
            border-color: #dc2626;
        }

        .btn-primary:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
        }

        .btn-primary:disabled {
            background-color: #9ca3af;
            border-color: #9ca3af;
        }

        @media (max-width: 768px) {

            .checkout-wrapper {
                padding: 30px 12px 50px;
            }

            .checkout-header h1 {
                font-size: 25px;
            }

            .order-info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .template-preview {
                height: 250px;
            }

            .checkout-bottom {
                padding: 16px;
            }

        }

    </style>

</head>

<body>


<!-- =========================================================
     NAVBAR
========================================================= -->

<nav class="navbar checkout-navbar">

    <div class="container">

        <a
            class="navbar-brand"
            href="<?= e(APP_URL) ?>"
        >
            <?= e(APP_NAME) ?>
        </a>

        <a
            href="<?= e(APP_URL) ?>/preview.php?order=<?= urlencode($orderCode) ?>"
            class="btn btn-sm btn-outline-secondary"
        >
            ← Kembali ke Preview
        </a>

    </div>

</nav>


<!-- =========================================================
     CONTENT
========================================================= -->

<main class="checkout-wrapper">


    <div class="checkout-header">

        <h1>
            Pilih Template CV
        </h1>

        <p>
            Pilih desain CV yang sesuai dengan kebutuhan Anda.
        </p>

    </div>


    <!-- =====================================================
         ORDER INFO
    ====================================================== -->

    <div class="order-info">

        <div class="order-info-row">

            <div>

                <div class="order-label">
                    Kode Pesanan
                </div>

                <div class="order-value">
                    <?= e($order['order_code']) ?>
                </div>

            </div>


            <div>

                <div class="order-label">
                    Nama
                </div>

                <div class="order-value">
                    <?= e($order['full_name']) ?>
                </div>

            </div>


            <div>

                <div class="order-label">
                    Email
                </div>

                <div class="order-value">
                    <?= e($order['email']) ?>
                </div>

            </div>

        </div>

    </div>


    <?php if ($error !== ''): ?>

        <div class="alert alert-danger mx-auto" style="max-width:950px;">
            <?= e($error) ?>
        </div>

    <?php endif; ?>


    <!-- =====================================================
         TEMPLATE FORM
    ====================================================== -->

    <form
        method="POST"
        action=""
        id="templateForm"
    >

        <?= csrfField() ?>


        <div class="row g-4 template-grid">

            <?php if (empty($templates)): ?>

                <div class="col-12">

                    <div class="alert alert-warning">
                        Belum ada template CV yang tersedia.
                    </div>

                </div>

            <?php else: ?>

                <?php foreach ($templates as $template): ?>

                    <?php

                    /*
                    |--------------------------------------------------------------------------
                    | Data Template
                    |--------------------------------------------------------------------------
                    */

                    $templateId = (int) $template['id'];

                    $templateName = $template['name'] ?? '';

                    $templateDescription =
                        $template['description'] ?? '';

                    $templatePreview =
                        $template['preview'] ?? '';

                    $templatePrice =
                        (float) ($template['price'] ?? 0);

                    $isSelected =
                        $selectedTemplateId === $templateId;

                    ?>

                    <div class="col-md-4">

                        <div class="template-card-wrapper">

                            <label
                                class="template-card <?= $isSelected ? 'selected' : '' ?>"
                                data-template-card="<?= $templateId ?>"
                            >


                                <input
                                    type="radio"
                                    name="template_id"
                                    value="<?= $templateId ?>"
                                    class="template-radio"
                                    data-template-name="<?= e($templateName) ?>"
                                    data-template-price="<?= e($templatePrice) ?>"
                                    <?= $isSelected ? 'checked' : '' ?>
                                >


                                <!-- PREVIEW -->

                                <div class="template-preview">

                                    <?php if (!empty($templatePreview)): ?>

                                        <img
                                            src="<?= e(
                                                APP_URL .
                                                '/' .
                                                ltrim(
                                                    $templatePreview,
                                                    '/'
                                                )
                                            ) ?>"
                                            alt="<?= e($templateName) ?>"
                                        >

                                    <?php else: ?>

                                        <div class="fake-cv">

                                            <div class="fake-cv-header">

                                                <div class="fake-title"></div>

                                                <div class="fake-line medium"></div>

                                                <div class="fake-line short"></div>

                                            </div>


                                            <div class="fake-section"></div>

                                            <div class="fake-line long"></div>
                                            <div class="fake-line medium"></div>
                                            <div class="fake-line long"></div>


                                            <div class="fake-section"></div>

                                            <div class="fake-line long"></div>
                                            <div class="fake-line long"></div>
                                            <div class="fake-line medium"></div>


                                            <div class="fake-section"></div>

                                            <div class="fake-line medium"></div>
                                            <div class="fake-line long"></div>

                                        </div>

                                    <?php endif; ?>

                                </div>


                                <!-- BODY -->

                                <div class="template-body">

                                    <div class="template-name">
                                        <?= e($templateName) ?>
                                    </div>


                                    <div class="template-description">

                                        <?= !empty($templateDescription)
                                            ? e($templateDescription)
                                            : 'Template CV profesional dengan struktur yang rapi dan mudah dibaca.'
                                        ?>

                                    </div>


                                    <div class="d-flex justify-content-between align-items-end">

                                        <div>

                                            <div class="template-select-text">
                                                Harga
                                            </div>

                                            <div class="template-price">
                                                <?= e(
                                                    formatRupiah(
                                                        $templatePrice
                                                    )
                                                ) ?>
                                            </div>

                                        </div>


                                        <span
                                            class="badge text-bg-light"
                                            data-selected-badge="<?= $templateId ?>"
                                            style="<?= $isSelected ? '' : 'display:none;' ?>"
                                        >
                                            Dipilih
                                        </span>

                                    </div>

                                </div>


                            </label>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>


        <!-- =================================================
             SUMMARY
        ================================================== -->

        <div class="checkout-bottom">

            <div class="row align-items-center g-3">

                <div class="col-md-7">

                    <div class="summary-label">
                        Template yang dipilih
                    </div>

                    <div
                        class="summary-template"
                        id="summaryTemplate"
                    >

                        <?php if (!empty($order['selected_template'])): ?>

                            <?= e($order['selected_template']) ?>

                        <?php else: ?>

                            Belum dipilih

                        <?php endif; ?>

                    </div>


                    <div class="checkout-note">
                        Harga template ditentukan oleh sistem
                        berdasarkan data yang tersimpan di database.
                    </div>

                </div>


                <div class="col-md-2">

                    <div class="summary-label">
                        Total
                    </div>

                    <div
                        class="summary-price"
                        id="summaryPrice"
                    >

                        <?php if ($displayTotal > 0): ?>

                            <?= e(
                                formatRupiah($displayTotal)
                            ) ?>

                        <?php else: ?>

                            Rp0

                        <?php endif; ?>

                    </div>

                </div>


                <div class="col-md-3 text-md-end">

                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                        id="continueButton"
                        <?= empty($templates) ? 'disabled' : '' ?>
                    >
                        Lanjut ke Pembayaran →
                    </button>

                </div>

            </div>

        </div>


    </form>

</main>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const cards =
        document.querySelectorAll('[data-template-card]');

    const radios =
        document.querySelectorAll(
            'input[name="template_id"]'
        );

    const summaryTemplate =
        document.getElementById('summaryTemplate');

    const summaryPrice =
        document.getElementById('summaryPrice');

    const continueButton =
        document.getElementById('continueButton');


    function formatRupiah(number) {

        return new Intl.NumberFormat(
            'id-ID',
            {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }
        ).format(number);

    }


    function updateSelection(radio) {

        cards.forEach(function (card) {

            card.classList.remove('selected');

        });


        document
            .querySelectorAll('[data-selected-badge]')
            .forEach(function (badge) {

                badge.style.display = 'none';

            });


        const selectedCard =
            document.querySelector(
                '[data-template-card="' +
                radio.value +
                '"]'
            );


        const selectedBadge =
            document.querySelector(
                '[data-selected-badge="' +
                radio.value +
                '"]'
            );


        if (selectedCard) {
            selectedCard.classList.add('selected');
        }


        if (selectedBadge) {
            selectedBadge.style.display = 'inline-block';
        }


        const name =
            radio.dataset.name || 'Template dipilih';

        const price =
            parseFloat(radio.dataset.price || 0);


        if (summaryTemplate) {
            summaryTemplate.textContent = name;
        }


        if (summaryPrice) {
            summaryPrice.textContent =
                formatRupiah(price);
        }


        if (continueButton) {
            continueButton.disabled = false;
        }

    }


    /*
    |--------------------------------------------------------------------------
    | Tambahkan data template ke radio
    |--------------------------------------------------------------------------
    */

    cards.forEach(function (card) {

        const radio =
            card.querySelector(
                'input[name="template_id"]'
            );

        const nameElement =
            card.querySelector('.template-name');

        const priceElement =
            card.querySelector('.template-price');


        if (radio && nameElement && priceElement) {

            radio.dataset.name =
                nameElement.textContent.trim();

            radio.dataset.price =
                priceElement.textContent
                    .replace(/[^\d]/g, '');

        }

    });


    /*
    |--------------------------------------------------------------------------
    | Klik Radio
    |--------------------------------------------------------------------------
    */

    radios.forEach(function (radio) {

        radio.addEventListener(
            'change',
            function () {

                updateSelection(this);

            }
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Klik Card
    |--------------------------------------------------------------------------
    */

    cards.forEach(function (card) {

        card.addEventListener(
            'click',
            function (event) {

                if (
                    event.target.tagName.toLowerCase() ===
                    'input'
                ) {
                    return;
                }


                const radio =
                    this.querySelector(
                        'input[name="template_id"]'
                    );


                if (radio) {

                    radio.checked = true;

                    updateSelection(radio);

                }

            }
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Inisialisasi Template Terpilih
    |--------------------------------------------------------------------------
    */

    const checked =
        document.querySelector(
            'input[name="template_id"]:checked'
        );


    if (checked) {

        updateSelection(checked);

    } else {

        if (continueButton) {
            continueButton.disabled = true;
        }

    }

});

</script>


</body>
</html>
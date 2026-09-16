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
| Ambil Data Order
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        o.*,
        p.full_name,
        t.name AS template_name,
        t.description AS template_description,
        t.preview AS template_preview,
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
    die('Data CV tidak ditemukan.');
}


/*
|--------------------------------------------------------------------------
| ID ORDER
|--------------------------------------------------------------------------
*/

$orderId = (int) $order['id'];


/*
|--------------------------------------------------------------------------
| Data Personal
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM cv_personal
    WHERE order_id = ?
    LIMIT 1
");

$stmt->execute([$orderId]);

$personal = $stmt->fetch();

if (!$personal) {
    die('Data pribadi CV tidak ditemukan.');
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

$stmt->execute([$orderId]);

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

$stmt->execute([$orderId]);

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

$stmt->execute([$orderId]);

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

$stmt->execute([$orderId]);

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

$stmt->execute([$orderId]);

$languages = $stmt->fetchAll();


/*
|--------------------------------------------------------------------------
| Helper
|--------------------------------------------------------------------------
*/

function formatYearRange($start, $end)
{
    $start = trim((string) $start);
    $end = trim((string) $end);

    if ($start !== '' && $end !== '') {
        return $start . ' - ' . $end;
    }

    if ($start !== '') {
        return $start . ' - Sekarang';
    }

    if ($end !== '') {
        return $end;
    }

    return '';
}


function formatDateIndonesia($date)
{
    if (!$date) {
        return '';
    }

    $timestamp = strtotime($date);

    if (!$timestamp) {
        return $date;
    }

    $months = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    return date('j', $timestamp)
        . ' '
        . $months[(int) date('n', $timestamp)]
        . ' '
        . date('Y', $timestamp);
}


function cleanUrl($url)
{
    $url = trim((string) $url);

    if ($url === '') {
        return '';
    }

    if (
        !str_starts_with($url, 'http://') &&
        !str_starts_with($url, 'https://')
    ) {
        return 'https://' . $url;
    }

    return $url;
}

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
        Preview CV - <?= e($personal['full_name']) ?>
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
            background: #eef1f5;
            color: #1f2937;
        }

        .preview-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .preview-wrapper {
            padding: 35px 15px 60px;
        }

        .preview-toolbar {
            max-width: 900px;
            margin: 0 auto 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
        }

        .preview-toolbar-title h1 {
            font-size: 22px;
            font-weight: 700;
            margin: 0;
        }

        .preview-toolbar-title p {
            margin: 4px 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        .cv-paper {
            width: 210mm;
            min-height: 297mm;
            max-width: 100%;
            margin: 0 auto;
            background: #ffffff;
            padding: 18mm 18mm 16mm;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.10);
            box-sizing: border-box;
        }

        .cv-header {
            border-bottom: 2px solid #111827;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .cv-name {
            font-size: 30px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .cv-contact {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 15px;
            font-size: 11px;
            color: #4b5563;
        }

        .cv-contact a {
            color: #374151;
            text-decoration: none;
        }

        .cv-section {
            margin-bottom: 19px;
        }

        .cv-section-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #111827;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .cv-profile {
            font-size: 11px;
            line-height: 1.65;
            color: #374151;
            white-space: pre-line;
        }

        .cv-item {
            margin-bottom: 13px;
        }

        .cv-item:last-child {
            margin-bottom: 0;
        }

        .cv-item-header {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .cv-item-title {
            font-size: 12px;
            font-weight: 700;
            color: #111827;
        }

        .cv-item-subtitle {
            font-size: 11px;
            color: #374151;
            margin-top: 2px;
        }

        .cv-item-period {
            flex-shrink: 0;
            font-size: 10px;
            color: #6b7280;
            text-align: right;
            white-space: nowrap;
        }

        .cv-item-description {
            margin-top: 5px;
            font-size: 10.5px;
            line-height: 1.55;
            color: #4b5563;
            white-space: pre-line;
        }

        .skill-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .skill-item {
            font-size: 10.5px;
            border: 1px solid #d1d5db;
            padding: 4px 9px;
            border-radius: 3px;
            color: #374151;
        }

        .language-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 7px 20px;
        }

        .language-item {
            font-size: 11px;
            color: #374151;
        }

        .language-item strong {
            color: #111827;
        }

        .cv-empty {
            font-size: 10.5px;
            color: #9ca3af;
            font-style: italic;
        }

        .template-info {
            max-width: 900px;
            margin: 20px auto 0;
        }

        .template-info-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 18px 20px;
        }

        .template-info-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .template-info-text {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
        }

        .btn-primary {
            background-color: #dc2626;
            border-color: #dc2626;
        }

        .btn-primary:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
        }

        .btn-outline-primary {
            color: #dc2626;
            border-color: #dc2626;
        }

        .btn-outline-primary:hover {
            background-color: #dc2626;
            border-color: #dc2626;
        }

        @media (max-width: 768px) {

            .preview-wrapper {
                padding: 20px 10px 40px;
            }

            .cv-paper {
                width: 100%;
                min-height: auto;
                padding: 25px 20px;
            }

            .cv-name {
                font-size: 24px;
            }

            .cv-item-header {
                flex-direction: column;
                gap: 3px;
            }

            .cv-item-period {
                text-align: left;
            }

            .language-list {
                grid-template-columns: 1fr;
            }

        }

        @media print {

            @page {
                size: A4;
                margin: 0;
            }

            body {
                background: #ffffff !important;
            }

            .no-print {
                display: none !important;
            }

            .preview-wrapper {
                padding: 0;
            }

            .cv-paper {
                width: 210mm;
                min-height: 297mm;
                max-width: none;
                margin: 0;
                padding: 18mm 18mm 16mm;
                box-shadow: none;
            }

        }

    </style>

</head>

<body>


<!-- =========================================================
     NAVBAR
========================================================= -->

<nav class="navbar preview-navbar no-print">

    <div class="container">

        <a
            class="navbar-brand"
            href="<?= e(APP_URL) ?>"
        >
            CV ATS Professional
        </a>

        <span class="text-muted small">
            Preview CV
        </span>

    </div>

</nav>


<!-- =========================================================
     PREVIEW
========================================================= -->

<div class="preview-wrapper">


    <div class="preview-toolbar no-print">

        <div class="preview-toolbar-title">

            <h1>
                Preview CV
            </h1>

            <p>
                Periksa data CV Anda sebelum memilih template.
            </p>

        </div>


        <div class="d-flex gap-2">

            <a
                href="<?= e(APP_URL) ?>/buat-cv.php"
                class="btn btn-outline-secondary btn-sm"
            >
                ← Kembali Edit
            </a>

            <a
                href="<?= e(APP_URL) ?>/checkout.php?order=<?= urlencode($orderCode) ?>"
                class="btn btn-primary btn-sm"
            >
                Pilih Template →
            </a>

        </div>

    </div>


    <!-- =====================================================
         CV PAPER
    ====================================================== -->

    <div class="cv-paper">


 <!-- HEADER -->

<div class="cv-header">

    <h1 class="cv-name">
        <?= e($personal['full_name']) ?>
    </h1>

    <?php if (!empty($personal['professional_title'])): ?>
        <div
            style="
                font-size: 14px;
                color: #4b5563;
                margin-bottom: 8px;
            "
        >
            <?= e($personal['professional_title']) ?>
        </div>
    <?php endif; ?>

    <div class="cv-contact">

        <?php if (!empty($order['email'])): ?>
            <span>
                <?= e($order['email']) ?>
            </span>
        <?php endif; ?>

        <?php if (!empty($personal['phone'])): ?>
            <span>
                <?= e($personal['phone']) ?>
            </span>
        <?php endif; ?>

        <?php if (!empty($personal['city'])): ?>
            <span>
                <?= e($personal['city']) ?>
            </span>
        <?php endif; ?>

        <?php if (!empty($personal['address'])): ?>
            <span>
                <?= e($personal['address']) ?>
            </span>
        <?php endif; ?>

        <?php if (!empty($personal['linkedin'])): ?>

            <a
                href="<?= e(cleanUrl($personal['linkedin'])) ?>"
                target="_blank"
                rel="noopener"
            >
                LinkedIn
            </a>

        <?php endif; ?>

        <?php if (!empty($personal['portfolio'])): ?>

            <a
                href="<?= e(cleanUrl($personal['portfolio'])) ?>"
                target="_blank"
                rel="noopener"
            >
                Portfolio
            </a>

        <?php endif; ?>

    </div>

</div>


        <!-- PROFIL -->

<?php if (!empty(trim($personal['summary'] ?? ''))): ?>

    <section class="cv-section">

        <div class="cv-section-title">
            Profil
        </div>

        <div class="cv-profile">
            <?= e($personal['summary']) ?>
        </div>

    </section>

<?php endif; ?>


<!-- PENDIDIKAN -->

<?php if (!empty($educations)): ?>

    <section class="cv-section">

        <div class="cv-section-title">
            Pendidikan
        </div>

        <?php foreach ($educations as $education): ?>

            <div class="cv-item">

                <div class="cv-item-header">

                    <div>

                        <?php if (!empty($education['institution'])): ?>

                            <div class="cv-item-title">
                                <?= e($education['institution']) ?>
                            </div>

                        <?php endif; ?>

                        <?php if (
                            !empty($education['degree']) ||
                            !empty($education['major'])
                        ): ?>

                            <div class="cv-item-subtitle">

                                <?php if (!empty($education['degree'])): ?>
                                    <?= e($education['degree']) ?>
                                <?php endif; ?>

                                <?php if (
                                    !empty($education['degree']) &&
                                    !empty($education['major'])
                                ): ?>
                                    ·
                                <?php endif; ?>

                                <?php if (!empty($education['major'])): ?>
                                    <?= e($education['major']) ?>
                                <?php endif; ?>

                            </div>

                        <?php endif; ?>

                    </div>

                    <?php
                    $period = formatYearRange(
                        $education['start_year'] ?? '',
                        $education['end_year'] ?? ''
                    );
                    ?>

                    <?php if ($period !== ''): ?>

                        <div class="cv-item-period">
                            <?= e($period) ?>
                        </div>

                    <?php endif; ?>

                </div>

                <?php if (!empty($education['description'])): ?>

                    <div class="cv-item-description">
                        <?= e($education['description']) ?>
                    </div>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </section>

<?php endif; ?>


<!-- PENGALAMAN -->

<?php if (!empty($experiences)): ?>

    <section class="cv-section">

        <div class="cv-section-title">
            Pengalaman Kerja
        </div>

        <?php foreach ($experiences as $experience): ?>

            <div class="cv-item">

                <div class="cv-item-header">

                    <div>

                        <?php if (!empty($experience['position'])): ?>

                            <div class="cv-item-title">
                                <?= e($experience['position']) ?>
                            </div>

                        <?php endif; ?>

                        <?php if (!empty($experience['company'])): ?>

                            <div class="cv-item-subtitle">

                                <?= e($experience['company']) ?>

                                <?php if (!empty($experience['location'])): ?>
                                    · <?= e($experience['location']) ?>
                                <?php endif; ?>

                            </div>

                        <?php endif; ?>

                    </div>

                    <?php
                    $period = formatYearRange(
                        $experience['start_date'] ?? '',
                        $experience['end_date'] ?? ''
                    );
                    ?>

                    <?php
                    $startDate = $experience['start_date'] ?? '';
                    $endDate = $experience['end_date'] ?? '';

                    if (!empty($experience['is_current'])) {
                        $period = trim((string) $startDate);

                        if ($period !== '') {
                            $period .= ' - Sekarang';
                        }
                    } else {
                        $period = formatYearRange(
                            $startDate,
                            $endDate
                        );
                    }
                    ?>

                    <?php if ($period !== ''): ?>

                        <div class="cv-item-period">
                            <?= e($period) ?>
                        </div>

                    <?php endif; ?>

                </div>

                <?php if (!empty($experience['description'])): ?>

                    <div class="cv-item-description">
                        <?= e($experience['description']) ?>
                    </div>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </section>

<?php endif; ?>


        <!-- ORGANISASI -->

<!-- ORGANISASI -->

<?php if (!empty($organizations)): ?>

    <section class="cv-section">

        <div class="cv-section-title">
            Organisasi
        </div>

        <?php foreach ($organizations as $organization): ?>

            <div class="cv-item">

                <div class="cv-item-header">

                    <div>

                        <?php if (!empty($organization['organization'])): ?>

                            <div class="cv-item-title">
                                <?= e($organization['organization']) ?>
                            </div>

                        <?php endif; ?>

                        <?php if (!empty($organization['position'])): ?>

                            <div class="cv-item-subtitle">
                                <?= e($organization['position']) ?>
                            </div>

                        <?php endif; ?>

                    </div>

                    <?php
                    $period = formatYearRange(
                        $organization['start_date'] ?? '',
                        $organization['end_date'] ?? ''
                    );
                    ?>

                    <?php if ($period !== ''): ?>

                        <div class="cv-item-period">
                            <?= e($period) ?>
                        </div>

                    <?php endif; ?>

                </div>

                <?php if (!empty($organization['description'])): ?>

                    <div class="cv-item-description">
                        <?= e($organization['description']) ?>
                    </div>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </section>

<?php endif; ?>


        <!-- KEAHLIAN -->

<!-- KEAHLIAN -->

<?php if (!empty($skills)): ?>

    <section class="cv-section">

        <div class="cv-section-title">
            Keahlian
        </div>

        <div class="skill-list">

            <?php foreach ($skills as $skill): ?>

                <?php if (!empty(trim($skill['skill'] ?? ''))): ?>

                    <span class="skill-item">
                        <?= e($skill['skill']) ?>
                    </span>

                <?php endif; ?>

            <?php endforeach; ?>

        </div>

    </section>

<?php endif; ?>


        <!-- BAHASA -->

 <!-- BAHASA -->

<?php if (!empty($languages)): ?>

    <section class="cv-section">

        <div class="cv-section-title">
            Bahasa
        </div>

        <div class="language-list">

            <?php foreach ($languages as $language): ?>

                <?php if (!empty($language['language'])): ?>

                    <div class="language-item">

                        <strong>
                            <?= e($language['language']) ?>
                        </strong>

                        <?php if (!empty($language['proficiency'])): ?>

                            — <?= e($language['proficiency']) ?>

                        <?php endif; ?>

                    </div>

                <?php endif; ?>

            <?php endforeach; ?>

        </div>

    </section>

<?php endif; ?>


    </div>


    <!-- =====================================================
         TEMPLATE INFO
    ====================================================== -->

    <div class="template-info no-print">

        <div class="template-info-card">

            <div class="template-info-title">
                Langkah berikutnya
            </div>

            <p class="template-info-text">
                Data CV Anda sudah tersimpan.
                Lanjutkan untuk memilih desain template
                sebelum melakukan pembayaran.
            </p>

            <div class="mt-3">

                <a
                    href="<?= e(APP_URL) ?>/checkout.php?order=<?= urlencode($orderCode) ?>"
                    class="btn btn-primary"
                >
                    Pilih Template & Lanjutkan
                </a>

            </div>

        </div>

    </div>


</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>
</html>
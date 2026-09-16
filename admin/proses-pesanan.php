<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(ADMIN_URL . '/pesanan.php');
}

verifyCsrf();

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
        t.template_folder
    FROM cv_orders o

    LEFT JOIN cv_personal p
        ON p.order_id = o.id

    LEFT JOIN cv_templates t
        ON t.id = o.template_id

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
| Cek pembayaran
|--------------------------------------------------------------------------
*/

if ($order['payment_status'] !== 'paid') {
    die(
        'CV belum dapat diproses karena pembayaran belum diverifikasi.'
    );
}


/*
|--------------------------------------------------------------------------
| Cek data personal
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
    die('Data personal CV tidak ditemukan.');
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
| Pengalaman kerja
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
| Keahlian
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
| Validasi template
|--------------------------------------------------------------------------
*/

$templateFolder = trim(
    $order['template_folder'] ?? ''
);

$allowedTemplates = [
    'ats01',
    'ats02',
    'ats03'
];

if (
    !in_array(
        $templateFolder,
        $allowedTemplates,
        true
    )
) {
    die('Template CV tidak valid.');
}


/*
|--------------------------------------------------------------------------
| Tentukan file template
|--------------------------------------------------------------------------
*/

$templateFile =
    dirname(__DIR__) .
    '/templates/' .
    $templateFolder .
    '/template.php';

if (!file_exists($templateFile)) {
    die(
        'File template tidak ditemukan.'
    );
}


/*
|--------------------------------------------------------------------------
| Render template
|--------------------------------------------------------------------------
*/

ob_start();

require $templateFile;

$html = ob_get_clean();


if (empty(trim($html))) {
    die('Template CV menghasilkan data kosong.');
}


/*
|--------------------------------------------------------------------------
| Cek Dompdf
|--------------------------------------------------------------------------
*/

$autoload = dirname(__DIR__) . '/vendor/autoload.php';

if (!file_exists($autoload)) {
    die(
        'Dompdf belum terpasang. Jalankan composer require dompdf/dompdf'
    );
}

require_once $autoload;

use Dompdf\Dompdf;
use Dompdf\Options;


/*
|--------------------------------------------------------------------------
| Konfigurasi Dompdf
|--------------------------------------------------------------------------
*/

$options = new Options();

$options->set(
    'isHtml5ParserEnabled',
    true
);

$options->set(
    'isRemoteEnabled',
    true
);

$options->set(
    'defaultFont',
    'DejaVu Sans'
);

$dompdf = new Dompdf($options);


/*
|--------------------------------------------------------------------------
| Load HTML
|--------------------------------------------------------------------------
*/

$dompdf->loadHtml($html);

$dompdf->setPaper(
    'A4',
    'portrait'
);


/*
|--------------------------------------------------------------------------
| Generate PDF
|--------------------------------------------------------------------------
*/

$dompdf->render();


/*
|--------------------------------------------------------------------------
| Folder PDF
|--------------------------------------------------------------------------
*/

if (!is_dir(PDF_PATH)) {

    if (!mkdir(PDF_PATH, 0755, true)) {
        die(
            'Folder PDF gagal dibuat.'
        );
    }
}


/*
|--------------------------------------------------------------------------
| Nama file PDF
|--------------------------------------------------------------------------
*/

$fileName = preg_replace(
    '/[^A-Za-z0-9_-]/',
    '_',
    $order['order_code']
);

$fileName .= '.pdf';


$filePath =
    PDF_PATH .
    $fileName;


/*
|--------------------------------------------------------------------------
| Simpan PDF
|--------------------------------------------------------------------------
*/

$pdfContent = $dompdf->output();

$saved = file_put_contents(
    $filePath,
    $pdfContent
);

if ($saved === false) {

    die(
        'PDF gagal disimpan. Periksa folder generated/pdf.'
    );
}


/*
|--------------------------------------------------------------------------
| Path database
|--------------------------------------------------------------------------
*/

$pdfDatabasePath =
    'generated/pdf/' .
    $fileName;


/*
|--------------------------------------------------------------------------
| Update status pesanan
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    UPDATE cv_orders
    SET
        pdf_file = ?,
        order_status = 'completed'
    WHERE id = ?
");

$stmt->execute([
    $pdfDatabasePath,
    $orderId
]);


/*
|--------------------------------------------------------------------------
| Selesai
|--------------------------------------------------------------------------
*/

redirect(
    ADMIN_URL .
    '/detail-pesanan.php?id=' .
    $orderId .
    '&success=pdf'
);
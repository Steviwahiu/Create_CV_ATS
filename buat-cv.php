<?php

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verifyCsrf();

    /*
    |--------------------------------------------------------------------------
    | DATA PRIBADI
    |--------------------------------------------------------------------------
    */

    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $kota = trim($_POST['kota'] ?? '');
    $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
    $tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
    $jenis_kelamin = trim($_POST['jenis_kelamin'] ?? '');
    $profil = trim($_POST['profil'] ?? '');
    $linkedin = trim($_POST['linkedin'] ?? '');
    $portfolio = trim($_POST['portfolio'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

    if ($nama_lengkap === '') {
        $errors[] = 'Nama lengkap wajib diisi.';
    }

    if ($email === '') {
        $errors[] = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    /*
    |--------------------------------------------------------------------------
    | PENDIDIKAN
    |--------------------------------------------------------------------------
    */

    $educationInstitusi =
        $_POST['education_institusi'] ?? [];

    $educationJurusan =
        $_POST['education_jurusan'] ?? [];

    $educationJenjang =
        $_POST['education_jenjang'] ?? [];

    $educationMulai =
        $_POST['education_mulai'] ?? [];

    $educationSelesai =
        $_POST['education_selesai'] ?? [];

    $educationKeterangan =
        $_POST['education_keterangan'] ?? [];

    /*
    |--------------------------------------------------------------------------
    | PENGALAMAN
    |--------------------------------------------------------------------------
    */

    $experiencePerusahaan =
        $_POST['experience_perusahaan'] ?? [];

    $experiencePosisi =
        $_POST['experience_posisi'] ?? [];

    $experienceLokasi =
        $_POST['experience_lokasi'] ?? [];

    $experienceMulai =
        $_POST['experience_mulai'] ?? [];

    $experienceSelesai =
        $_POST['experience_selesai'] ?? [];

    $experienceDeskripsi =
        $_POST['experience_deskripsi'] ?? [];

    /*
    |--------------------------------------------------------------------------
    | ORGANISASI
    |--------------------------------------------------------------------------
    */

    $organizationNama =
        $_POST['organization_nama'] ?? [];

    $organizationPosisi =
        $_POST['organization_posisi'] ?? [];

    $organizationMulai =
        $_POST['organization_mulai'] ?? [];

    $organizationSelesai =
        $_POST['organization_selesai'] ?? [];

    $organizationDeskripsi =
        $_POST['organization_deskripsi'] ?? [];

    /*
    |--------------------------------------------------------------------------
    | SKILLS
    |--------------------------------------------------------------------------
    */

    $skills =
        $_POST['skills'] ?? [];

    /*
    |--------------------------------------------------------------------------
    | BAHASA
    |--------------------------------------------------------------------------
    */

    $languages =
        $_POST['language_bahasa'] ?? [];

    $languageLevels =
        $_POST['language_tingkat'] ?? [];


    /*
    |--------------------------------------------------------------------------
    | SIMPAN DATABASE
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {

        try {

            $pdo->beginTransaction();

            /*
            |--------------------------------------------------------------------------
            | ORDER
            |--------------------------------------------------------------------------
            */

            $orderCode = generateOrderCode();

            $stmt = $pdo->prepare("
                INSERT INTO cv_orders
                (
                    order_code,
                    email,
                    amount,
                    payment_status,
                    order_status
                )
                VALUES
                (
                    :order_code,
                    :email,
                    0,
                    'unpaid',
                    'draft'
                )
            ");

            $stmt->execute([
                ':order_code' => $orderCode,
                ':email' => $email
            ]);

            /*
            | Ambil ID order yang baru dibuat
            */

            $orderId = $pdo->lastInsertId();


            /*
            |--------------------------------------------------------------------------
            | PERSONAL
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("
                INSERT INTO cv_personal
                (
                    order_id,
                    full_name,
                    phone,
                    city,
                    address,
                    linkedin,
                    portfolio,
                    summary,
                    photo
                )
                VALUES
                (
                    :order_id,
                    :full_name,
                    :phone,
                    :city,
                    :address,
                    :linkedin,
                    :portfolio,
                    :summary,
                    :photo
                )
            ");

            $stmt->execute([
                ':order_id' => $orderId,
                ':full_name' => $nama_lengkap,
                ':phone' => $no_hp ?: null,
                ':city' => $kota ?: null,
                ':address' => $alamat ?: null,
                ':linkedin' => $linkedin ?: null,
                ':portfolio' => $portfolio ?: null,
                ':summary' => $profil ?: null,
                ':photo' => null
            ]);


            /*
            |--------------------------------------------------------------------------
            | PENDIDIKAN
            |--------------------------------------------------------------------------
            */

            $stmtEducation = $pdo->prepare("
                INSERT INTO cv_education
                (
                    order_id,
                    institution,
                    major,
                    degree,
                    start_year,
                    end_year,
                    description
                )
                VALUES
                (
                    :order_id,
                    :institution,
                    :major,
                    :degree,
                    :start_year,
                    :end_year,
                    :description
                )
            ");

            foreach ($educationInstitusi as $i => $institusi) {

                $institusi = trim($institusi);

                if ($institusi === '') {
                    continue;
                }

                $stmtEducation->execute([
                    ':order_id' => $orderId,

                    ':institution' => $institusi,

                    ':major' => trim(
                        $educationJurusan[$i] ?? ''
                    ) ?: null,

                    ':degree' => trim(
                        $educationJenjang[$i] ?? ''
                    ) ?: null,

                    ':start_year' => !empty(
                        $educationMulai[$i] ?? ''
                    )
                        ? $educationMulai[$i]
                        : null,

                    ':end_year' => !empty(
                        $educationSelesai[$i] ?? ''
                    )
                        ? $educationSelesai[$i]
                        : null,

                    ':description' => trim(
                        $educationKeterangan[$i] ?? ''
                    ) ?: null
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | PENGALAMAN
            |--------------------------------------------------------------------------
            */

            $stmtExperience = $pdo->prepare("
                INSERT INTO cv_experience
                (
                    order_id,
                    company,
                    position,
                    location,
                    start_date,
                    end_date,
                    description
                )
                VALUES
                (
                    :order_id,
                    :company,
                    :position,
                    :location,
                    :start_date,
                    :end_date,
                    :description
                )
            ");

            foreach ($experiencePerusahaan as $i => $perusahaan) {

                $perusahaan = trim($perusahaan);

                $posisi = trim(
                    $experiencePosisi[$i] ?? ''
                );

                if ($perusahaan === '' && $posisi === '') {
                    continue;
                }

                $stmtExperience->execute([
                    ':order_id' => $orderId,

                    ':company' => $perusahaan,

                    ':position' => $posisi,

                    ':location' => trim(
                        $experienceLokasi[$i] ?? ''
                    ) ?: null,

                    ':start_date' => !empty(
                        $experienceMulai[$i] ?? ''
                    )
                        ? $experienceMulai[$i]
                        : null,

                    ':end_date' => !empty(
                        $experienceSelesai[$i] ?? ''
                    )
                        ? $experienceSelesai[$i]
                        : null,

                    ':description' => trim(
                        $experienceDeskripsi[$i] ?? ''
                    ) ?: null
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | ORGANISASI
            |--------------------------------------------------------------------------
            */

            $stmtOrganization = $pdo->prepare("
                INSERT INTO cv_organization
                (
                    order_id,
                    organization,
                    position,
                    start_date,
                    end_date,
                    description
                )
                VALUES
                (
                    :order_id,
                    :organization,
                    :position,
                    :start_date,
                    :end_date,
                    :description
                )
            ");

            foreach ($organizationNama as $i => $namaOrganisasi) {

                $namaOrganisasi = trim($namaOrganisasi);

                if ($namaOrganisasi === '') {
                    continue;
                }

                $stmtOrganization->execute([
                    ':order_id' => $orderId,

                    ':organization' => $namaOrganisasi,

                    ':position' => trim(
                        $organizationPosisi[$i] ?? ''
                    ) ?: null,

                    ':start_date' => !empty(
                        $organizationMulai[$i] ?? ''
                    )
                        ? $organizationMulai[$i]
                        : null,

                    ':end_date' => !empty(
                        $organizationSelesai[$i] ?? ''
                    )
                        ? $organizationSelesai[$i]
                        : null,

                    ':description' => trim(
                        $organizationDeskripsi[$i] ?? ''
                    ) ?: null
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | SKILLS
            |--------------------------------------------------------------------------
            */

            $stmtSkill = $pdo->prepare("
                INSERT INTO cv_skills
                (
                    order_id,
                    skill
                )
                VALUES
                (
                    :order_id,
                    :skill
                )
            ");

            foreach ($skills as $skill) {

                $skill = trim($skill);

                if ($skill === '') {
                    continue;
                }

                $stmtSkill->execute([
                    ':order_id' => $orderId,
                    ':skill' => $skill
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | BAHASA
            |--------------------------------------------------------------------------
            */

            $stmtLanguage = $pdo->prepare("
                INSERT INTO cv_languages
                (
                    order_id,
                    language,
                    proficiency
                )
                VALUES
                (
                    :order_id,
                    :language,
                    :proficiency
                )
            ");

            foreach ($languages as $i => $language) {

                $language = trim($language);

                if ($language === '') {
                    continue;
                }

                $stmtLanguage->execute([
                    ':order_id' => $orderId,

                    ':language' => $language,

                    ':proficiency' => trim(
                        $languageLevels[$i] ?? ''
                    ) ?: null
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | COMMIT
            |--------------------------------------------------------------------------
            */

            $pdo->commit();


            /*
            |--------------------------------------------------------------------------
            | REDIRECT
            |--------------------------------------------------------------------------
            */

            redirect(
                APP_URL .
                '/preview.php?order=' .
                urlencode($orderCode)
            );

        } catch (Throwable $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $errors[] =
                'Terjadi kesalahan saat menyimpan data. ' .
                'Silakan coba kembali.';

            /*
            |--------------------------------------------------------------------------
            | DEBUG
            |--------------------------------------------------------------------------
            */

            if (
                defined('APP_DEBUG') &&
                APP_DEBUG === true
            ) {
                $errors[] =
                    'Detail: ' .
                    $e->getMessage();
            }
        }
    }
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
        Buat CV - <?= e(APP_NAME) ?>
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="<?= ASSETS_URL ?>/css/style.css"
        rel="stylesheet"
    >

    <style>

        .cv-form-wrapper {
            max-width: 950px;
            margin: 50px auto;
        }

        .form-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 35px;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 35px;
            position: relative;
        }

        .step-item {
            text-align: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-weight: 700;
        }

        .step-item.active .step-number,
        .step-item.completed .step-number {
            background: #dc2626;
            color: #ffffff;
        }

        .step-title {
            font-size: 13px;
            color: #6b7280;
        }

        .step-item.active .step-title {
            color: #dc2626;
            font-weight: 600;
        }

        .step-line {
            position: absolute;
            top: 20px;
            left: 12%;
            right: 12%;
            height: 2px;
            background: #e5e7eb;
            z-index: 1;
        }

        .dynamic-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 18px;
            background: #fafafa;
        }

        .dynamic-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .dynamic-card-header h6 {
            margin: 0;
            font-weight: 600;
        }

        .btn-remove {
            color: #dc2626;
            border-color: #fecaca;
        }

        .btn-remove:hover {
            background: #dc2626;
            color: #ffffff;
        }

        .form-label {
            font-weight: 600;
            font-size: 14px;
        }

        .required {
            color: #dc2626;
        }

        .form-control,
        .form-select {
            padding: 11px 13px;
            border-radius: 8px;
        }

        textarea.form-control {
            min-height: 100px;
        }

        .form-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e5e7eb;
        }

    </style>

</head>

<body>

<nav class="navbar navbar-expand-lg bg-white border-bottom">

    <div class="container">

        <a
            class="navbar-brand"
            href="<?= APP_URL ?>"
        >
            <?= e(APP_NAME) ?>
        </a>

    </div>

</nav>


<div class="container">

    <div class="cv-form-wrapper">

        <div class="mb-4">

            <h2 class="fw-bold">
                Buat CV Profesional
            </h2>

            <p class="text-muted">
                Lengkapi data Anda untuk membuat CV
                dengan format yang terstruktur dan profesional.
            </p>

        </div>


        <?php if (!empty($errors)): ?>

            <div class="alert alert-danger">

                <strong>
                    Data belum dapat disimpan.
                </strong>

                <ul class="mb-0 mt-2">

                    <?php foreach ($errors as $error): ?>

                        <li>
                            <?= e($error) ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>


        <div class="form-card">

            <!-- STEP INDICATOR -->

            <div class="step-indicator">

                <div class="step-line"></div>

                <div
                    class="step-item active"
                    data-step="1"
                >
                    <div class="step-number">
                        1
                    </div>

                    <div class="step-title">
                        Data Pribadi
                    </div>
                </div>

                <div
                    class="step-item"
                    data-step="2"
                >
                    <div class="step-number">
                        2
                    </div>

                    <div class="step-title">
                        Pendidikan
                    </div>
                </div>

                <div
                    class="step-item"
                    data-step="3"
                >
                    <div class="step-number">
                        3
                    </div>

                    <div class="step-title">
                        Pengalaman
                    </div>
                </div>

                <div
                    class="step-item"
                    data-step="4"
                >
                    <div class="step-number">
                        4
                    </div>

                    <div class="step-title">
                        Keahlian
                    </div>
                </div>

            </div>


            <form
                method="POST"
                action=""
                id="cvForm"
                novalidate
            >

                <?= csrfField() ?>


                <!-- =====================================================
                     STEP 1
                ====================================================== -->

                <div
                    class="step active"
                    data-step="1"
                >

                    <h4 class="fw-bold mb-1">
                        Data Pribadi
                    </h4>

                    <p class="text-muted mb-4">
                        Masukkan informasi dasar yang akan
                        ditampilkan pada CV.
                    </p>


                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">
                                Nama Lengkap
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                name="nama_lengkap"
                                class="form-control"
                                value="<?= old('nama_lengkap') ?>"
                                required
                            >

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Email
                                <span class="required">*</span>
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="<?= old('email') ?>"
                                required
                            >

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Nomor HP
                            </label>

                            <input
                                type="text"
                                name="no_hp"
                                class="form-control"
                                value="<?= old('no_hp') ?>"
                                placeholder="08xxxxxxxxxx"
                            >

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Kota Domisili
                            </label>

                            <input
                                type="text"
                                name="kota"
                                class="form-control"
                                value="<?= old('kota') ?>"
                                placeholder="Contoh: Bitung"
                            >

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Tempat Lahir
                            </label>

                            <input
                                type="text"
                                name="tempat_lahir"
                                class="form-control"
                                value="<?= old('tempat_lahir') ?>"
                            >

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Tanggal Lahir
                            </label>

                            <input
                                type="date"
                                name="tanggal_lahir"
                                class="form-control"
                                value="<?= old('tanggal_lahir') ?>"
                            >

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Jenis Kelamin
                            </label>

                            <select
                                name="jenis_kelamin"
                                class="form-select"
                            >

                                <option value="">
                                    Pilih
                                </option>

                                <option
                                    value="Laki-laki"
                                    <?= ($_POST['jenis_kelamin'] ?? '') === 'Laki-laki' ? 'selected' : '' ?>
                                >
                                    Laki-laki
                                </option>

                                <option
                                    value="Perempuan"
                                    <?= ($_POST['jenis_kelamin'] ?? '') === 'Perempuan' ? 'selected' : '' ?>
                                >
                                    Perempuan
                                </option>

                            </select>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                LinkedIn
                            </label>

                            <input
                                type="url"
                                name="linkedin"
                                class="form-control"
                                value="<?= old('linkedin') ?>"
                                placeholder="linkedin.com/in/nama"
                            >

                        </div>


                        <div class="col-12">

                            <label class="form-label">
                                Alamat
                            </label>

                            <textarea
                                name="alamat"
                                class="form-control"
                                rows="2"
                            ><?= old('alamat') ?></textarea>

                        </div>


                        <div class="col-12">

                            <label class="form-label">
                                Profil Singkat
                            </label>

                            <textarea
                                name="profil"
                                class="form-control"
                                rows="5"
                                placeholder="Tuliskan ringkasan profesional Anda..."
                            ><?= old('profil') ?></textarea>

                            <small class="text-muted">
                                Disarankan 2–4 kalimat.
                            </small>

                        </div>


                        <div class="col-12">

                            <label class="form-label">
                                Portfolio / Website
                            </label>

                            <input
                                type="url"
                                name="portfolio"
                                class="form-control"
                                value="<?= old('portfolio') ?>"
                                placeholder="www.portfolioanda.com"
                            >

                        </div>

                    </div>


                    <div class="form-navigation">

                        <div></div>

                        <button
                            type="button"
                            class="btn btn-primary next-btn"
                        >
                            Berikutnya
                            →
                        </button>

                    </div>

                </div>


                <!-- =====================================================
                     STEP 2
                ====================================================== -->

                <div
                    class="step"
                    data-step="2"
                >

                    <h4 class="fw-bold mb-1">
                        Pendidikan
                    </h4>

                    <p class="text-muted mb-4">
                        Tambahkan riwayat pendidikan Anda.
                    </p>


                    <div id="educationContainer">

                        <div class="dynamic-card">

                            <div class="dynamic-card-header">

                                <h6>
                                    Pendidikan 1
                                </h6>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-remove remove-item"
                                >
                                    Hapus
                                </button>

                            </div>


                            <div class="row g-3">

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Institusi
                                    </label>

                                    <input
                                        type="text"
                                        name="education_institusi[]"
                                        class="form-control"
                                        placeholder="Nama sekolah/universitas"
                                    >

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Jurusan
                                    </label>

                                    <input
                                        type="text"
                                        name="education_jurusan[]"
                                        class="form-control"
                                    >

                                </div>


                                <div class="col-md-4">

                                    <label class="form-label">
                                        Jenjang
                                    </label>

                                    <select
                                        name="education_jenjang[]"
                                        class="form-select"
                                    >

                                        <option value="">
                                            Pilih
                                        </option>

                                        <option>SD</option>
                                        <option>SMP</option>
                                        <option>SMA</option>
                                        <option>SMK</option>
                                        <option>D3</option>
                                        <option>D4</option>
                                        <option>S1</option>
                                        <option>S2</option>
                                        <option>S3</option>

                                    </select>

                                </div>


                                <div class="col-md-4">

                                    <label class="form-label">
                                        Tahun Mulai
                                    </label>

                                    <input
                                        type="number"
                                        name="education_mulai[]"
                                        class="form-control"
                                        min="1950"
                                        max="2100"
                                    >

                                </div>


                                <div class="col-md-4">

                                    <label class="form-label">
                                        Tahun Selesai
                                    </label>

                                    <input
                                        type="number"
                                        name="education_selesai[]"
                                        class="form-control"
                                        min="1950"
                                        max="2100"
                                    >

                                </div>


                                <div class="col-12">

                                    <label class="form-label">
                                        Keterangan
                                    </label>

                                    <textarea
                                        name="education_keterangan[]"
                                        class="form-control"
                                        rows="2"
                                        placeholder="Contoh: IPK 3.70"
                                    ></textarea>

                                </div>

                            </div>

                        </div>

                    </div>


                    <button
                        type="button"
                        class="btn btn-outline-primary"
                        id="addEducation"
                    >
                        + Tambah Pendidikan
                    </button>


                    <div class="form-navigation">

                        <button
                            type="button"
                            class="btn btn-outline-secondary back-btn"
                        >
                            ← Kembali
                        </button>

                        <button
                            type="button"
                            class="btn btn-primary next-btn"
                        >
                            Berikutnya →
                        </button>

                    </div>

                </div>


                <!-- =====================================================
                     STEP 3
                ====================================================== -->

                <div
                    class="step"
                    data-step="3"
                >

                    <h4 class="fw-bold mb-1">
                        Pengalaman & Organisasi
                    </h4>

                    <p class="text-muted mb-4">
                        Tambahkan pengalaman kerja, magang,
                        freelance, atau organisasi.
                    </p>


                    <h5 class="fw-bold mb-3">
                        Pengalaman Kerja
                    </h5>

                    <div id="experienceContainer">

                        <div class="dynamic-card">

                            <div class="dynamic-card-header">

                                <h6>
                                    Pengalaman 1
                                </h6>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-remove remove-item"
                                >
                                    Hapus
                                </button>

                            </div>


                            <div class="row g-3">

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Perusahaan / Instansi
                                    </label>

                                    <input
                                        type="text"
                                        name="experience_perusahaan[]"
                                        class="form-control"
                                    >

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Posisi
                                    </label>

                                    <input
                                        type="text"
                                        name="experience_posisi[]"
                                        class="form-control"
                                    >

                                </div>


                                <div class="col-md-4">

                                    <label class="form-label">
                                        Lokasi
                                    </label>

                                    <input
                                        type="text"
                                        name="experience_lokasi[]"
                                        class="form-control"
                                    >

                                </div>


                                <div class="col-md-4">

                                    <label class="form-label">
                                        Tahun Mulai
                                    </label>

                                    <input
                                        type="number"
                                        name="experience_mulai[]"
                                        class="form-control"
                                        min="1950"
                                        max="2100"
                                    >

                                </div>


                                <div class="col-md-4">

                                    <label class="form-label">
                                        Tahun Selesai
                                    </label>

                                    <input
                                        type="number"
                                        name="experience_selesai[]"
                                        class="form-control"
                                        min="1950"
                                        max="2100"
                                    >

                                </div>


                                <div class="col-12">

                                    <label class="form-label">
                                        Deskripsi
                                    </label>

                                    <textarea
                                        name="experience_deskripsi[]"
                                        class="form-control"
                                        rows="3"
                                        placeholder="Jelaskan tanggung jawab dan pencapaian..."
                                    ></textarea>

                                </div>

                            </div>

                        </div>

                    </div>


                    <button
                        type="button"
                        class="btn btn-outline-primary mb-4"
                        id="addExperience"
                    >
                        + Tambah Pengalaman
                    </button>


                    <h5 class="fw-bold mb-3">
                        Organisasi
                    </h5>


                    <div id="organizationContainer">

                        <div class="dynamic-card">

                            <div class="dynamic-card-header">

                                <h6>
                                    Organisasi 1
                                </h6>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-remove remove-item"
                                >
                                    Hapus
                                </button>

                            </div>


                            <div class="row g-3">

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Nama Organisasi
                                    </label>

                                    <input
                                        type="text"
                                        name="organization_nama[]"
                                        class="form-control"
                                    >

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Posisi
                                    </label>

                                    <input
                                        type="text"
                                        name="organization_posisi[]"
                                        class="form-control"
                                    >

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Tahun Mulai
                                    </label>

                                    <input
                                        type="number"
                                        name="organization_mulai[]"
                                        class="form-control"
                                        min="1950"
                                        max="2100"
                                    >

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Tahun Selesai
                                    </label>

                                    <input
                                        type="number"
                                        name="organization_selesai[]"
                                        class="form-control"
                                        min="1950"
                                        max="2100"
                                    >

                                </div>


                                <div class="col-12">

                                    <label class="form-label">
                                        Deskripsi
                                    </label>

                                    <textarea
                                        name="organization_deskripsi[]"
                                        class="form-control"
                                        rows="3"
                                    ></textarea>

                                </div>

                            </div>

                        </div>

                    </div>


                    <button
                        type="button"
                        class="btn btn-outline-primary"
                        id="addOrganization"
                    >
                        + Tambah Organisasi
                    </button>


                    <div class="form-navigation">

                        <button
                            type="button"
                            class="btn btn-outline-secondary back-btn"
                        >
                            ← Kembali
                        </button>

                        <button
                            type="button"
                            class="btn btn-primary next-btn"
                        >
                            Berikutnya →
                        </button>

                    </div>

                </div>


                <!-- =====================================================
                     STEP 4
                ====================================================== -->

                <div
                    class="step"
                    data-step="4"
                >

                    <h4 class="fw-bold mb-1">
                        Keahlian & Bahasa
                    </h4>

                    <p class="text-muted mb-4">
                        Tambahkan keahlian dan kemampuan bahasa
                        yang Anda miliki.
                    </p>


                    <div class="mb-4">

                        <label class="form-label">
                            Keahlian
                        </label>

                        <div id="skillsContainer">

                            <div class="input-group mb-2">

                                <input
                                    type="text"
                                    name="skills[]"
                                    class="form-control"
                                    placeholder="Contoh: Microsoft Excel"
                                >

                                <button
                                    type="button"
                                    class="btn btn-outline-danger remove-simple"
                                >
                                    Hapus
                                </button>

                            </div>

                        </div>


                        <button
                            type="button"
                            class="btn btn-outline-primary btn-sm"
                            id="addSkill"
                        >
                            + Tambah Keahlian
                        </button>

                    </div>


                    <div class="mb-4">

                        <label class="form-label">
                            Bahasa
                        </label>

                        <div id="languageContainer">

                            <div class="row g-2 mb-2">

                                <div class="col-md-7">

                                    <input
                                        type="text"
                                        name="language_bahasa[]"
                                        class="form-control"
                                        placeholder="Contoh: Bahasa Inggris"
                                    >

                                </div>

                                <div class="col-md-3">

                                    <select
                                        name="language_tingkat[]"
                                        class="form-select"
                                    >

                                        <option value="">
                                            Tingkat
                                        </option>

                                        <option>Dasar</option>
                                        <option>Menengah</option>
                                        <option>Mahir</option>
                                        <option>Fasih</option>

                                    </select>

                                </div>

                                <div class="col-md-2">

                                    <button
                                        type="button"
                                        class="btn btn-outline-danger w-100 remove-language"
                                    >
                                        Hapus
                                    </button>

                                </div>

                            </div>

                        </div>


                        <button
                            type="button"
                            class="btn btn-outline-primary btn-sm"
                            id="addLanguage"
                        >
                            + Tambah Bahasa
                        </button>

                    </div>


                    <div class="alert alert-light border">

                        <strong>
                            Pastikan data Anda sudah benar.
                        </strong>

                        <br>

                        Setelah menekan tombol
                        <strong>
                            Simpan & Lanjutkan
                        </strong>,
                        data akan disimpan dan Anda akan
                        diarahkan ke halaman preview.

                    </div>


                    <div class="form-navigation">

                        <button
                            type="button"
                            class="btn btn-outline-secondary back-btn"
                        >
                            ← Kembali
                        </button>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Simpan & Lanjutkan →
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>


<script>

let currentStep = 1;

const totalSteps = 4;


/*
|--------------------------------------------------------------------------
| STEP NAVIGATION
|--------------------------------------------------------------------------
*/

function showStep(step) {

    document
        .querySelectorAll('.step')
        .forEach(function (element) {

            element.classList.remove('active');

        });


    const activeStep =
        document.querySelector(
            '.step[data-step="' + step + '"]'
        );

    if (activeStep) {
        activeStep.classList.add('active');
    }


    document
        .querySelectorAll('.step-item')
        .forEach(function (element) {

            const number =
                parseInt(
                    element.dataset.step
                );

            element.classList.remove(
                'active',
                'completed'
            );

            if (number === step) {

                element.classList.add(
                    'active'
                );

            } else if (number < step) {

                element.classList.add(
                    'completed'
                );

            }

        });


    currentStep = step;

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}


/*
|--------------------------------------------------------------------------
| NEXT
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll('.next-btn')
    .forEach(function (button) {

        button.addEventListener(
            'click',
            function () {

                const current =
                    document.querySelector(
                        '.step[data-step="' +
                        currentStep +
                        '"]'
                    );

                const inputs =
                    current.querySelectorAll(
                        'input, select, textarea'
                    );

                let valid = true;


                inputs.forEach(function (input) {

                    if (
                        input.hasAttribute('required') &&
                        !input.checkValidity()
                    ) {

                        input.reportValidity();

                        valid = false;

                    }

                });


                if (!valid) {
                    return;
                }


                if (
                    currentStep < totalSteps
                ) {

                    showStep(
                        currentStep + 1
                    );

                }

            }
        );

    });


/*
|--------------------------------------------------------------------------
| BACK
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll('.back-btn')
    .forEach(function (button) {

        button.addEventListener(
            'click',
            function () {

                if (currentStep > 1) {

                    showStep(
                        currentStep - 1
                    );

                }

            }
        );

    });


/*
|--------------------------------------------------------------------------
| CREATE DYNAMIC EDUCATION
|--------------------------------------------------------------------------
*/

document
    .getElementById('addEducation')
    .addEventListener(
        'click',
        function () {

            const container =
                document.getElementById(
                    'educationContainer'
                );

            const count =
                container.children.length + 1;


            const html = `
                <div class="dynamic-card">

                    <div class="dynamic-card-header">

                        <h6>
                            Pendidikan ${count}
                        </h6>

                        <button
                            type="button"
                            class="btn btn-sm btn-remove remove-item"
                        >
                            Hapus
                        </button>

                    </div>

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">
                                Institusi
                            </label>

                            <input
                                type="text"
                                name="education_institusi[]"
                                class="form-control"
                            >

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Jurusan
                            </label>

                            <input
                                type="text"
                                name="education_jurusan[]"
                                class="form-control"
                            >

                        </div>

                        <div class="col-md-4">

                            <label class="form-label">
                                Jenjang
                            </label>

                            <select
                                name="education_jenjang[]"
                                class="form-select"
                            >

                                <option value="">
                                    Pilih
                                </option>

                                <option>SD</option>
                                <option>SMP</option>
                                <option>SMA</option>
                                <option>SMK</option>
                                <option>D3</option>
                                <option>D4</option>
                                <option>S1</option>
                                <option>S2</option>
                                <option>S3</option>

                            </select>

                        </div>

                        <div class="col-md-4">

                            <label class="form-label">
                                Tahun Mulai
                            </label>

                            <input
                                type="number"
                                name="education_mulai[]"
                                class="form-control"
                                min="1950"
                                max="2100"
                            >

                        </div>

                        <div class="col-md-4">

                            <label class="form-label">
                                Tahun Selesai
                            </label>

                            <input
                                type="number"
                                name="education_selesai[]"
                                class="form-control"
                                min="1950"
                                max="2100"
                            >

                        </div>

                        <div class="col-12">

                            <label class="form-label">
                                Keterangan
                            </label>

                            <textarea
                                name="education_keterangan[]"
                                class="form-control"
                                rows="2"
                            ></textarea>

                        </div>

                    </div>

                </div>
            `;

            container.insertAdjacentHTML(
                'beforeend',
                html
            );

        }
    );


/*
|--------------------------------------------------------------------------
| CREATE DYNAMIC EXPERIENCE
|--------------------------------------------------------------------------
*/

document
    .getElementById('addExperience')
    .addEventListener(
        'click',
        function () {

            const container =
                document.getElementById(
                    'experienceContainer'
                );

            const count =
                container.children.length + 1;


            const html = `
                <div class="dynamic-card">

                    <div class="dynamic-card-header">

                        <h6>
                            Pengalaman ${count}
                        </h6>

                        <button
                            type="button"
                            class="btn btn-sm btn-remove remove-item"
                        >
                            Hapus
                        </button>

                    </div>

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">
                                Perusahaan / Instansi
                            </label>

                            <input
                                type="text"
                                name="experience_perusahaan[]"
                                class="form-control"
                            >

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Posisi
                            </label>

                            <input
                                type="text"
                                name="experience_posisi[]"
                                class="form-control"
                            >

                        </div>

                        <div class="col-md-4">

                            <label class="form-label">
                                Lokasi
                            </label>

                            <input
                                type="text"
                                name="experience_lokasi[]"
                                class="form-control"
                            >

                        </div>

                        <div class="col-md-4">

                            <label class="form-label">
                                Tahun Mulai
                            </label>

                            <input
                                type="number"
                                name="experience_mulai[]"
                                class="form-control"
                                min="1950"
                                max="2100"
                            >

                        </div>

                        <div class="col-md-4">

                            <label class="form-label">
                                Tahun Selesai
                            </label>

                            <input
                                type="number"
                                name="experience_selesai[]"
                                class="form-control"
                                min="1950"
                                max="2100"
                            >

                        </div>

                        <div class="col-12">

                            <label class="form-label">
                                Deskripsi
                            </label>

                            <textarea
                                name="experience_deskripsi[]"
                                class="form-control"
                                rows="3"
                            ></textarea>

                        </div>

                    </div>

                </div>
            `;

            container.insertAdjacentHTML(
                'beforeend',
                html
            );

        }
    );


/*
|--------------------------------------------------------------------------
| CREATE DYNAMIC ORGANIZATION
|--------------------------------------------------------------------------
*/

document
    .getElementById('addOrganization')
    .addEventListener(
        'click',
        function () {

            const container =
                document.getElementById(
                    'organizationContainer'
                );

            const count =
                container.children.length + 1;


            const html = `
                <div class="dynamic-card">

                    <div class="dynamic-card-header">

                        <h6>
                            Organisasi ${count}
                        </h6>

                        <button
                            type="button"
                            class="btn btn-sm btn-remove remove-item"
                        >
                            Hapus
                        </button>

                    </div>

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">
                                Nama Organisasi
                            </label>

                            <input
                                type="text"
                                name="organization_nama[]"
                                class="form-control"
                            >

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Posisi
                            </label>

                            <input
                                type="text"
                                name="organization_posisi[]"
                                class="form-control"
                            >

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Tahun Mulai
                            </label>

                            <input
                                type="number"
                                name="organization_mulai[]"
                                class="form-control"
                                min="1950"
                                max="2100"
                            >

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Tahun Selesai
                            </label>

                            <input
                                type="number"
                                name="organization_selesai[]"
                                class="form-control"
                                min="1950"
                                max="2100"
                            >

                        </div>

                        <div class="col-12">

                            <label class="form-label">
                                Deskripsi
                            </label>

                            <textarea
                                name="organization_deskripsi[]"
                                class="form-control"
                                rows="3"
                            ></textarea>

                        </div>

                    </div>

                </div>
            `;

            container.insertAdjacentHTML(
                'beforeend',
                html
            );

        }
    );


/*
|--------------------------------------------------------------------------
| ADD SKILL
|--------------------------------------------------------------------------
*/

document
    .getElementById('addSkill')
    .addEventListener(
        'click',
        function () {

            const container =
                document.getElementById(
                    'skillsContainer'
                );

            const html = `
                <div class="input-group mb-2">

                    <input
                        type="text"
                        name="skills[]"
                        class="form-control"
                        placeholder="Contoh: Microsoft Excel"
                    >

                    <button
                        type="button"
                        class="btn btn-outline-danger remove-simple"
                    >
                        Hapus
                    </button>

                </div>
            `;

            container.insertAdjacentHTML(
                'beforeend',
                html
            );

        }
    );


/*
|--------------------------------------------------------------------------
| ADD LANGUAGE
|--------------------------------------------------------------------------
*/

document
    .getElementById('addLanguage')
    .addEventListener(
        'click',
        function () {

            const container =
                document.getElementById(
                    'languageContainer'
                );

            const html = `
                <div class="row g-2 mb-2">

                    <div class="col-md-7">

                        <input
                            type="text"
                            name="language_bahasa[]"
                            class="form-control"
                            placeholder="Contoh: Bahasa Inggris"
                        >

                    </div>

                    <div class="col-md-3">

                        <select
                            name="language_tingkat[]"
                            class="form-select"
                        >

                            <option value="">
                                Tingkat
                            </option>

                            <option>Dasar</option>
                            <option>Menengah</option>
                            <option>Mahir</option>
                            <option>Fasih</option>

                        </select>

                    </div>

                    <div class="col-md-2">

                        <button
                            type="button"
                            class="btn btn-outline-danger w-100 remove-language"
                        >
                            Hapus
                        </button>

                    </div>

                </div>
            `;

            container.insertAdjacentHTML(
                'beforeend',
                html
            );

        }
    );


/*
|--------------------------------------------------------------------------
| REMOVE DYNAMIC ITEM
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'click',
    function (event) {

        if (
            event.target.classList.contains(
                'remove-item'
            )
        ) {

            const card =
                event.target.closest(
                    '.dynamic-card'
                );

            if (card) {
                card.remove();
            }

        }


        if (
            event.target.classList.contains(
                'remove-simple'
            )
        ) {

            const item =
                event.target.closest(
                    '.input-group'
                );

            if (item) {
                item.remove();
            }

        }


        if (
            event.target.classList.contains(
                'remove-language'
            )
        ) {

            const item =
                event.target.closest(
                    '.row'
                );

            if (item) {
                item.remove();
            }

        }

    }
);

</script>

</body>
</html>
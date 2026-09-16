<?php

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';

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
        <?= e(APP_NAME) ?>
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="<?= ASSETS_URL ?>/css/style.css"
    >

</head>

<body>

<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg bg-white border-bottom">

    <div class="container">

        <a
            class="navbar-brand"
            href="<?= APP_URL ?>"
        >
            CV ATS
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarMenu"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div
            class="collapse navbar-collapse"
            id="navbarMenu"
        >

            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="#cara-kerja"
                    >
                        Cara Kerja
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="#template"
                    >
                        Template
                    </a>
                </li>

                <li class="nav-item ms-lg-3">
                    <a
                        href="<?= APP_URL ?>/buat-cv.php"
                        class="btn btn-primary px-4"
                    >
                        Buat CV
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>


<!-- HERO -->

<section class="hero">

    <div class="container">

        <div class="row align-items-center g-5">

            <div class="col-lg-7">

                <span class="badge text-bg-danger mb-3">
                    CV ATS Professional
                </span>

                <h1>
                    Buat CV Profesional
                    untuk Mendukung
                    Lamaran Kerja Anda
                </h1>

                <p class="mt-4">
                    Isi data diri Anda melalui form sederhana,
                    pilih template CV, lakukan pembayaran,
                    dan dapatkan CV dalam format PDF melalui email.
                </p>

                <div class="d-flex gap-3 mt-4">

                    <a
                        href="<?= APP_URL ?>/buat-cv.php"
                        class="btn btn-primary btn-lg px-4"
                    >
                        Buat CV Sekarang
                    </a>

                    <a
                        href="#cara-kerja"
                        class="btn btn-outline-secondary btn-lg px-4"
                    >
                        Cara Kerja
                    </a>

                </div>

            </div>

            <div class="col-lg-5">

                <div class="feature-card">

                    <h5 class="fw-bold mb-4">
                        CV ATS Professional
                    </h5>

                    <div class="mb-3">
                        ✓ Format terstruktur
                    </div>

                    <div class="mb-3">
                        ✓ Tampilan profesional
                    </div>

                    <div class="mb-3">
                        ✓ Mudah dibaca sistem ATS
                    </div>

                    <div class="mb-3">
                        ✓ Format PDF
                    </div>

                    <div>
                        ✓ Dikirim langsung ke email
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- CARA KERJA -->

<section
    class="section bg-white"
    id="cara-kerja"
>

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="fw-bold">
                Cara Kerja
            </h2>

            <p class="text-secondary">
                Membuat CV menjadi lebih mudah
            </p>

        </div>

        <div class="row g-4">

            <div class="col-md-4">

                <div class="feature-card">

                    <h5 class="fw-bold">
                        01. Isi Data
                    </h5>

                    <p class="text-secondary mb-0">
                        Lengkapi data pribadi, pendidikan,
                        pengalaman, keahlian, dan informasi
                        lainnya melalui form.
                    </p>

                </div>

            </div>

            <div class="col-md-4">

                <div class="feature-card">

                    <h5 class="fw-bold">
                        02. Pilih Template
                    </h5>

                    <p class="text-secondary mb-0">
                        Pilih template CV profesional
                        yang sesuai dengan kebutuhan Anda.
                    </p>

                </div>

            </div>

            <div class="col-md-4">

                <div class="feature-card">

                    <h5 class="fw-bold">
                        03. Bayar & Terima CV
                    </h5>

                    <p class="text-secondary mb-0">
                        Lakukan pembayaran dan CV akan
                        diproses untuk kemudian dikirim
                        melalui email.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- TEMPLATE -->

<section
    class="section"
    id="template"
>

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="fw-bold">
                Template CV
            </h2>

            <p class="text-secondary">
                Pilih template CV yang sesuai dengan kebutuhan Anda
            </p>

        </div>


        <div class="row g-4 justify-content-center">

            <?php

            $templates = [
                [
                    'name' => 'ATS Professional',
                    'description' =>
                        'Template bersih dan profesional untuk berbagai bidang pekerjaan.',
                    'folder' => 'ats01',
                    'image' => 'ats01.png'
                ],
                [
                    'name' => 'ATS Modern',
                    'description' =>
                        'Template modern dengan tampilan dua kolom yang rapi dan profesional.',
                    'folder' => 'ats02',
                    'image' => 'ats02.png'
                ],
                [
                    'name' => 'ATS Executive',
                    'description' =>
                        'Template elegan dan formal untuk menampilkan profil profesional.',
                    'folder' => 'ats03',
                    'image' => 'ats03.png'
                ]
            ];

            foreach (
                $templates as $index => $template
            ):
            ?>

                <div class="col-12 col-sm-6 col-lg-4">

                    <div class="template-card">

                        <!-- PREVIEW KECIL -->

                        <div
                            class="template-preview"
                            data-bs-toggle="modal"
                            data-bs-target="#templateModal<?= $index ?>"
                        >

                            <img
                                src="<?= APP_URL ?>/assets/images/templates/<?= e($template['image']) ?>"
                                alt="<?= e($template['name']) ?>"
                            >

                            <div class="preview-overlay">

                                <span>
                                    Lihat Preview
                                </span>

                            </div>

                        </div>


                        <!-- INFORMASI -->

                        <div class="template-content">

                            <h3>
                                <?= e($template['name']) ?>
                            </h3>

                            <p>
                                <?= e($template['description']) ?>
                            </p>


                            <button
                                type="button"
                                class="btn btn-outline-danger w-100"
                                data-bs-toggle="modal"
                                data-bs-target="#templateModal<?= $index ?>"
                            >
                                Lihat Template
                            </button>

                        </div>

                    </div>

                </div>


                <!-- =====================================================
                     MODAL PREVIEW
                ====================================================== -->

                <div
                    class="modal fade"
                    id="templateModal<?= $index ?>"
                    tabindex="-1"
                    aria-hidden="true"
                >

                    <div class="modal-dialog modal-xl modal-dialog-centered">

                        <div class="modal-content template-modal">

                            <div class="modal-header">

                                <div>

                                    <h5 class="modal-title fw-bold">
                                        <?= e($template['name']) ?>
                                    </h5>

                                    <small class="text-secondary">
                                        Preview Template CV
                                    </small>

                                </div>

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"
                                ></button>

                            </div>


                            <div class="modal-body">

                                <div class="full-preview">

                                    <img
                                        src="<?= APP_URL ?>/assets/images/templates/<?= e($template['image']) ?>"
                                        alt="<?= e($template['name']) ?>"
                                    >

                                </div>

                            </div>


                            <div class="modal-footer">

                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal"
                                >
                                    Tutup
                                </button>

                                <a
                                    href="<?= APP_URL ?>/buat-cv.php"
                                    class="btn btn-primary"
                                >
                                    Gunakan Template
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

<script
    src="<?= ASSETS_URL ?>/js/app.js"
></script>

</body>

</html>
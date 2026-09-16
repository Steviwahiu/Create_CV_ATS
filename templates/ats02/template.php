<?php

/*
|--------------------------------------------------------------------------
| ATS02 - ATS Modern Professional
|--------------------------------------------------------------------------
| Layout A4 - compact, clean, professional
|--------------------------------------------------------------------------
*/

function cvText($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<style>

/* =========================================================
   PAGE
========================================================= */

@page {
    size: A4;
    margin: 24px 30px 22px 30px;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 0;

    font-family: DejaVu Sans, sans-serif;

    color: #222;

    font-size: 8.4px;

    line-height: 1.42;

    background: #fff;
}


/* =========================================================
   HEADER
========================================================= */

.header {
    width: 100%;

    padding-bottom: 11px;

    margin-bottom: 14px;

    border-bottom: 2px solid #222;
}

.header-table {
    width: 100%;

    border-collapse: collapse;
}

.header-left {
    width: 67%;

    vertical-align: top;
}

.header-right {
    width: 33%;

    vertical-align: top;

    text-align: right;
}

.cv-label {
    font-size: 6.5px;

    letter-spacing: 2px;

    text-transform: uppercase;

    color: #777;

    margin-bottom: 4px;
}

.name {
    font-size: 22px;

    line-height: 1.05;

    font-weight: bold;

    color: #111;

    margin-bottom: 4px;
}

.position {
    font-size: 9.2px;

    color: #555;

    margin-bottom: 6px;
}

.header-contact {
    font-size: 7.2px;

    color: #555;

    line-height: 1.55;
}

.header-label {
    font-size: 6.2px;

    text-transform: uppercase;

    letter-spacing: 0.7px;

    color: #999;

    margin-bottom: 1px;
}

.header-link {
    font-size: 7px;

    color: #444;

    word-wrap: break-word;

    line-height: 1.45;
}


/* =========================================================
   MAIN LAYOUT
========================================================= */

.main-table {
    width: 100%;

    border-collapse: collapse;

    table-layout: fixed;
}

.main-left {
    width: 66%;

    vertical-align: top;

    padding-right: 18px;
}

.main-right {
    width: 34%;

    vertical-align: top;

    padding-left: 17px;

    border-left: 1px solid #d5d5d5;
}


/* =========================================================
   SECTION
========================================================= */

.section {
    margin-bottom: 14px;
}

.section:last-child {
    margin-bottom: 0;
}

.section-title {
    font-size: 8.8px;

    font-weight: bold;

    text-transform: uppercase;

    letter-spacing: 0.8px;

    color: #111;

    padding-bottom: 4px;

    margin-bottom: 7px;

    border-bottom: 1px solid #cfcfcf;
}

.section-number {
    color: #999;

    font-size: 6.8px;

    margin-right: 4px;
}


/* =========================================================
   SUMMARY
========================================================= */

.summary {
    color: #333;

    text-align: justify;

    line-height: 1.55;
}


/* =========================================================
   ITEM
========================================================= */

.item {
    margin-bottom: 10px;
}

.item:last-child {
    margin-bottom: 0;
}

.item-title {
    font-size: 8.8px;

    font-weight: bold;

    color: #111;

    line-height: 1.35;

    margin-bottom: 1px;
}

.item-subtitle {
    font-size: 8px;

    color: #444;

    line-height: 1.35;

    margin-bottom: 1px;
}

.item-date {
    font-size: 7px;

    color: #777;

    margin-bottom: 3px;
}

.description {
    font-size: 7.8px;

    color: #333;

    line-height: 1.48;

    text-align: justify;
}


/* =========================================================
   SIDEBAR
========================================================= */

.side-section {
    margin-bottom: 14px;
}

.side-section:last-child {
    margin-bottom: 0;
}

.side-title {
    font-size: 8.4px;

    font-weight: bold;

    text-transform: uppercase;

    letter-spacing: 0.8px;

    color: #111;

    padding-bottom: 4px;

    margin-bottom: 7px;

    border-bottom: 1px solid #cfcfcf;
}

.side-item {
    margin-bottom: 7px;
}

.side-item:last-child {
    margin-bottom: 0;
}

.side-label {
    font-size: 6.3px;

    text-transform: uppercase;

    letter-spacing: 0.7px;

    color: #999;

    margin-bottom: 1px;
}

.side-value {
    font-size: 7.7px;

    color: #333;

    line-height: 1.45;

    word-wrap: break-word;
}


/* =========================================================
   SKILLS
========================================================= */

.skill {
    margin-bottom: 4px;

    padding-left: 9px;

    position: relative;

    font-size: 7.8px;

    color: #333;

    line-height: 1.4;
}

.skill:before {
    content: "•";

    position: absolute;

    left: 0;

    top: 0;

    font-weight: bold;
}


/* =========================================================
   LANGUAGE
========================================================= */

.language {
    margin-bottom: 6px;
}

.language:last-child {
    margin-bottom: 0;
}

.language-name {
    font-size: 7.9px;

    font-weight: bold;

    color: #222;
}

.language-level {
    font-size: 7px;

    color: #777;

    margin-top: 1px;
}


/* =========================================================
   FOOTER
========================================================= */

.footer {
    margin-top: 12px;

    padding-top: 5px;

    border-top: 1px solid #ddd;

    font-size: 6px;

    color: #999;
}

.footer-table {
    width: 100%;

    border-collapse: collapse;
}

.footer-left {
    text-align: left;
}

.footer-right {
    text-align: right;
}


/* =========================================================
   AVOID BAD PAGE BREAK
========================================================= */

.section,
.item,
.side-section,
.side-item {
    page-break-inside: avoid;
}

</style>

</head>

<body>


<!-- =========================================================
     HEADER
========================================================= -->

<div class="header">

    <table class="header-table">

        <tr>

            <td class="header-left">

                <div class="cv-label">
                    Curriculum Vitae
                </div>

                <div class="name">

                    <?= cvText(
                        $personal['full_name'] ?? ''
                    ) ?>

                </div>


                <?php if (
                    !empty(
                        $personal['professional_title']
                    )
                ): ?>

                    <div class="position">

                        <?= cvText(
                            $personal['professional_title']
                        ) ?>

                    </div>

                <?php endif; ?>


                <div class="header-contact">

                    <?php if (
                        !empty(
                            $personal['phone']
                        )
                    ): ?>

                        <?= cvText(
                            $personal['phone']
                        ) ?>

                    <?php endif; ?>


                    <?php if (
                        !empty(
                            $personal['phone']
                        ) &&
                        !empty(
                            $order['email']
                        )
                    ): ?>

                        &nbsp; • &nbsp;

                    <?php endif; ?>


                    <?php if (
                        !empty(
                            $order['email']
                        )
                    ): ?>

                        <?= cvText(
                            $order['email']
                        ) ?>

                    <?php endif; ?>


                    <?php if (
                        (
                            !empty(
                                $personal['phone']
                            ) ||
                            !empty(
                                $order['email']
                            )
                        ) &&
                        !empty(
                            $personal['city']
                        )
                    ): ?>

                        &nbsp; • &nbsp;

                    <?php endif; ?>


                    <?php if (
                        !empty(
                            $personal['city']
                        )
                    ): ?>

                        <?= cvText(
                            $personal['city']
                        ) ?>

                    <?php endif; ?>

                </div>

            </td>


            <td class="header-right">

                <?php if (
                    !empty(
                        $personal['linkedin']
                    )
                ): ?>

                    <div class="header-label">
                        LinkedIn
                    </div>

                    <div class="header-link">

                        <?= cvText(
                            $personal['linkedin']
                        ) ?>

                    </div>

                <?php endif; ?>


                <?php if (
                    !empty(
                        $personal['portfolio']
                    )
                ): ?>

                    <div
                        class="header-label"
                        style="margin-top:5px;"
                    >
                        Portfolio
                    </div>

                    <div class="header-link">

                        <?= cvText(
                            $personal['portfolio']
                        ) ?>

                    </div>

                <?php endif; ?>

            </td>

        </tr>

    </table>

</div>


<!-- =========================================================
     CONTENT
========================================================= -->

<table class="main-table">

<tr>


<!-- =========================================================
     LEFT
========================================================= -->

<td class="main-left">


    <!-- =====================================================
         PROFIL
    ====================================================== -->

    <?php if (
        !empty(
            $personal['summary']
        )
    ): ?>

        <div class="section">

            <div class="section-title">

                <span class="section-number">
                    01
                </span>

                Profil Profesional

            </div>


            <div class="summary">

                <?= nl2br(
                    cvText(
                        $personal['summary']
                    )
                ) ?>

            </div>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         PENGALAMAN
    ====================================================== -->

    <?php if (
        !empty($experiences)
    ): ?>

        <div class="section">

            <div class="section-title">

                <span class="section-number">
                    02
                </span>

                Pengalaman Kerja

            </div>


            <?php foreach (
                $experiences
                as $experience
            ): ?>

                <div class="item">

                    <div class="item-title">

                        <?= cvText(
                            $experience['position'] ?? ''
                        ) ?>

                    </div>


                    <div class="item-subtitle">

                        <?= cvText(
                            $experience['company'] ?? ''
                        ) ?>


                        <?php if (
                            !empty(
                                $experience['location']
                            )
                        ): ?>

                            &nbsp;•&nbsp;

                            <?= cvText(
                                $experience['location']
                            ) ?>

                        <?php endif; ?>

                    </div>


                    <div class="item-date">

                        <?= cvText(
                            $experience['start_date'] ?? ''
                        ) ?>

                        -

                        <?php if (
                            !empty(
                                $experience['is_current']
                            )
                        ): ?>

                            Sekarang

                        <?php else: ?>

                            <?= cvText(
                                $experience['end_date'] ?? ''
                            ) ?>

                        <?php endif; ?>

                    </div>


                    <?php if (
                        !empty(
                            $experience['description']
                        )
                    ): ?>

                        <div class="description">

                            <?= nl2br(
                                cvText(
                                    $experience['description']
                                )
                            ) ?>

                        </div>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         PENDIDIKAN
    ====================================================== -->

    <?php if (
        !empty($educations)
    ): ?>

        <div class="section">

            <div class="section-title">

                <span class="section-number">
                    03
                </span>

                Pendidikan

            </div>


            <?php foreach (
                $educations
                as $education
            ): ?>

                <div class="item">

                    <div class="item-title">

                        <?= cvText(
                            $education['institution'] ?? ''
                        ) ?>

                    </div>


                    <div class="item-subtitle">

                        <?php if (
                            !empty(
                                $education['degree']
                            )
                        ): ?>

                            <?= cvText(
                                $education['degree']
                            ) ?>

                        <?php endif; ?>


                        <?php if (
                            !empty(
                                $education['degree']
                            ) &&
                            !empty(
                                $education['major']
                            )
                        ): ?>

                            &nbsp;•&nbsp;

                        <?php endif; ?>


                        <?php if (
                            !empty(
                                $education['major']
                            )
                        ): ?>

                            <?= cvText(
                                $education['major']
                            ) ?>

                        <?php endif; ?>

                    </div>


                    <div class="item-date">

                        <?= cvText(
                            $education['start_year'] ?? ''
                        ) ?>

                        -

                        <?= cvText(
                            $education['end_year'] ?? ''
                        ) ?>

                    </div>


                    <?php if (
                        !empty(
                            $education['gpa']
                        )
                    ): ?>

                        <div class="description">

                            IPK:
                            <?= cvText(
                                $education['gpa']
                            ) ?>

                        </div>

                    <?php endif; ?>


                    <?php if (
                        !empty(
                            $education['description']
                        )
                    ): ?>

                        <div class="description">

                            <?= nl2br(
                                cvText(
                                    $education['description']
                                )
                            ) ?>

                        </div>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         ORGANISASI
    ====================================================== -->

    <?php if (
        !empty($organizations)
    ): ?>

        <div class="section">

            <div class="section-title">

                <span class="section-number">
                    04
                </span>

                Organisasi

            </div>


            <?php foreach (
                $organizations
                as $organization
            ): ?>

                <div class="item">

                    <div class="item-title">

                        <?= cvText(
                            $organization['position'] ?? ''
                        ) ?>

                    </div>


                    <div class="item-subtitle">

                        <?= cvText(
                            $organization['organization'] ?? ''
                        ) ?>

                    </div>


                    <div class="item-date">

                        <?= cvText(
                            $organization['start_date'] ?? ''
                        ) ?>

                        -

                        <?= cvText(
                            $organization['end_date'] ?? ''
                        ) ?>

                    </div>


                    <?php if (
                        !empty(
                            $organization['description']
                        )
                    ): ?>

                        <div class="description">

                            <?= nl2br(
                                cvText(
                                    $organization['description']
                                )
                            ) ?>

                        </div>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>


</td>


<!-- =========================================================
     RIGHT
========================================================= -->

<td class="main-right">


    <!-- =====================================================
         KONTAK
    ====================================================== -->

    <div class="side-section">

        <div class="side-title">
            Kontak
        </div>


        <?php if (
            !empty(
                $personal['phone']
            )
        ): ?>

            <div class="side-item">

                <div class="side-label">
                    Telepon
                </div>

                <div class="side-value">

                    <?= cvText(
                        $personal['phone']
                    ) ?>

                </div>

            </div>

        <?php endif; ?>


        <?php if (
            !empty(
                $order['email']
            )
        ): ?>

            <div class="side-item">

                <div class="side-label">
                    Email
                </div>

                <div class="side-value">

                    <?= cvText(
                        $order['email']
                    ) ?>

                </div>

            </div>

        <?php endif; ?>


        <?php if (
            !empty(
                $personal['city']
            )
        ): ?>

            <div class="side-item">

                <div class="side-label">
                    Kota
                </div>

                <div class="side-value">

                    <?= cvText(
                        $personal['city']
                    ) ?>

                </div>

            </div>

        <?php endif; ?>


        <?php if (
            !empty(
                $personal['address']
            )
        ): ?>

            <div class="side-item">

                <div class="side-label">
                    Alamat
                </div>

                <div class="side-value">

                    <?= nl2br(
                        cvText(
                            $personal['address']
                        )
                    ) ?>

                </div>

            </div>

        <?php endif; ?>

    </div>


    <!-- =====================================================
         KEAHLIAN
    ====================================================== -->

    <?php if (
        !empty($skills)
    ): ?>

        <div class="side-section">

            <div class="side-title">
                Keahlian
            </div>


            <?php foreach (
                $skills
                as $skill
            ): ?>

                <?php if (
                    !empty(
                        $skill['skill']
                    )
                ): ?>

                    <div class="skill">

                        <?= cvText(
                            $skill['skill']
                        ) ?>

                    </div>

                <?php endif; ?>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         BAHASA
    ====================================================== -->

    <?php if (
        !empty($languages)
    ): ?>

        <div class="side-section">

            <div class="side-title">
                Bahasa
            </div>


            <?php foreach (
                $languages
                as $language
            ): ?>

                <?php if (
                    !empty(
                        $language['language']
                    )
                ): ?>

                    <div class="language">

                        <div class="language-name">

                            <?= cvText(
                                $language['language']
                            ) ?>

                        </div>


                        <?php if (
                            !empty(
                                $language['proficiency']
                            )
                        ): ?>

                            <div class="language-level">

                                <?= cvText(
                                    $language['proficiency']
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>

                <?php endif; ?>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         PROFIL ONLINE
    ====================================================== -->

    <?php if (
        !empty(
            $personal['linkedin']
        ) ||
        !empty(
            $personal['portfolio']
        )
    ): ?>

        <div class="side-section">

            <div class="side-title">
                Profil Online
            </div>


            <?php if (
                !empty(
                    $personal['linkedin']
                )
            ): ?>

                <div class="side-item">

                    <div class="side-label">
                        LinkedIn
                    </div>

                    <div class="side-value">

                        <?= cvText(
                            $personal['linkedin']
                        ) ?>

                    </div>

                </div>

            <?php endif; ?>


            <?php if (
                !empty(
                    $personal['portfolio']
                )
            ): ?>

                <div class="side-item">

                    <div class="side-label">
                        Portfolio
                    </div>

                    <div class="side-value">

                        <?= cvText(
                            $personal['portfolio']
                        ) ?>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    <?php endif; ?>


</td>

</tr>

</table>


<!-- =========================================================
     FOOTER
========================================================= -->

<div class="footer">

    <table class="footer-table">

        <tr>

            <td class="footer-left">
                Curriculum Vitae
            </td>

            <td class="footer-right">
                ATS Modern & Professional
            </td>

        </tr>

    </table>

</div>


</body>

</html>
<?php

/*
|--------------------------------------------------------------------------
| ATS03 - ATS Executive
|--------------------------------------------------------------------------
| Layout A4 - Executive, Elegant, Compact & ATS Friendly
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
    margin: 24px 32px 22px 32px;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 0;

    font-family: DejaVu Sans, sans-serif;

    font-size: 8.3px;

    line-height: 1.42;

    color: #222;

    background: #fff;
}


/* =========================================================
   HEADER
========================================================= */

.header {
    width: 100%;

    margin-bottom: 15px;

    padding-bottom: 12px;

    border-bottom: 1.5px solid #222;
}

.header-table {
    width: 100%;

    border-collapse: collapse;
}

.header-left {
    width: 68%;

    vertical-align: top;
}

.header-right {
    width: 32%;

    vertical-align: bottom;

    text-align: right;
}

.executive-label {
    font-size: 6.2px;

    letter-spacing: 2.2px;

    text-transform: uppercase;

    color: #888;

    margin-bottom: 5px;
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

    line-height: 1.35;

    margin-bottom: 7px;
}

.contact-line {
    font-size: 7.1px;

    color: #555;

    line-height: 1.5;
}

.online-label {
    font-size: 6px;

    text-transform: uppercase;

    letter-spacing: 0.7px;

    color: #999;

    margin-bottom: 2px;
}

.online-value {
    font-size: 6.9px;

    color: #444;

    line-height: 1.4;

    word-wrap: break-word;
}


/* =========================================================
   INTRO / SUMMARY
========================================================= */

.summary-section {
    width: 100%;

    margin-bottom: 14px;
}

.summary-title {
    font-size: 8.6px;

    font-weight: bold;

    text-transform: uppercase;

    letter-spacing: 0.8px;

    color: #111;

    margin-bottom: 5px;
}

.summary {
    font-size: 8px;

    color: #333;

    line-height: 1.55;

    text-align: justify;
}


/* =========================================================
   MAIN GRID
========================================================= */

.main-table {
    width: 100%;

    border-collapse: collapse;

    table-layout: fixed;
}

.main-left {
    width: 68%;

    vertical-align: top;

    padding-right: 18px;
}

.main-right {
    width: 32%;

    vertical-align: top;

    padding-left: 17px;

    border-left: 1px solid #d3d3d3;
}


/* =========================================================
   SECTION
========================================================= */

.section {
    margin-bottom: 13px;
}

.section:last-child {
    margin-bottom: 0;
}

.section-title {
    font-size: 8.7px;

    font-weight: bold;

    text-transform: uppercase;

    letter-spacing: 0.85px;

    color: #111;

    padding-bottom: 4px;

    margin-bottom: 7px;

    border-bottom: 1px solid #cfcfcf;
}

.section-index {
    font-size: 6.5px;

    color: #999;

    margin-right: 5px;
}


/* =========================================================
   EXPERIENCE
========================================================= */

.experience-item {
    margin-bottom: 10px;
}

.experience-item:last-child {
    margin-bottom: 0;
}

.experience-position {
    font-size: 8.7px;

    font-weight: bold;

    color: #111;

    line-height: 1.35;

    margin-bottom: 1px;
}

.experience-company {
    font-size: 7.9px;

    color: #444;

    line-height: 1.35;

    margin-bottom: 1px;
}

.experience-date {
    font-size: 6.9px;

    color: #777;

    margin-bottom: 3px;
}

.description {
    font-size: 7.7px;

    color: #333;

    line-height: 1.48;

    text-align: justify;
}


/* =========================================================
   EDUCATION
========================================================= */

.education-item {
    margin-bottom: 9px;
}

.education-item:last-child {
    margin-bottom: 0;
}

.education-institution {
    font-size: 8.6px;

    font-weight: bold;

    color: #111;

    line-height: 1.35;

    margin-bottom: 1px;
}

.education-study {
    font-size: 7.8px;

    color: #444;

    line-height: 1.35;

    margin-bottom: 1px;
}

.education-date {
    font-size: 6.9px;

    color: #777;

    margin-bottom: 3px;
}


/* =========================================================
   ORGANIZATION
========================================================= */

.organization-item {
    margin-bottom: 9px;
}

.organization-item:last-child {
    margin-bottom: 0;
}

.organization-position {
    font-size: 8.5px;

    font-weight: bold;

    color: #111;

    margin-bottom: 1px;
}

.organization-name {
    font-size: 7.8px;

    color: #444;

    margin-bottom: 1px;
}

.organization-date {
    font-size: 6.9px;

    color: #777;

    margin-bottom: 3px;
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
    font-size: 8.3px;

    font-weight: bold;

    text-transform: uppercase;

    letter-spacing: 0.8px;

    color: #111;

    padding-bottom: 4px;

    margin-bottom: 7px;

    border-bottom: 1px solid #cfcfcf;
}


/* =========================================================
   CONTACT
========================================================= */

.contact-item {
    margin-bottom: 7px;
}

.contact-item:last-child {
    margin-bottom: 0;
}

.contact-label {
    font-size: 6.1px;

    text-transform: uppercase;

    letter-spacing: 0.65px;

    color: #999;

    margin-bottom: 1px;
}

.contact-value {
    font-size: 7.6px;

    color: #333;

    line-height: 1.45;

    word-wrap: break-word;
}


/* =========================================================
   SKILLS
========================================================= */

.skill-item {
    font-size: 7.7px;

    color: #333;

    line-height: 1.4;

    margin-bottom: 4px;

    padding-left: 9px;

    position: relative;
}

.skill-item:before {
    content: "•";

    position: absolute;

    left: 0;

    top: 0;

    font-weight: bold;
}


/* =========================================================
   LANGUAGE
========================================================= */

.language-item {
    margin-bottom: 6px;
}

.language-item:last-child {
    margin-bottom: 0;
}

.language-name {
    font-size: 7.8px;

    font-weight: bold;

    color: #222;
}

.language-level {
    font-size: 6.9px;

    color: #777;

    margin-top: 1px;
}


/* =========================================================
   ONLINE PROFILE
========================================================= */

.profile-item {
    margin-bottom: 7px;
}

.profile-item:last-child {
    margin-bottom: 0;
}

.profile-label {
    font-size: 6.1px;

    text-transform: uppercase;

    letter-spacing: 0.65px;

    color: #999;

    margin-bottom: 1px;
}

.profile-value {
    font-size: 7.3px;

    color: #333;

    line-height: 1.45;

    word-wrap: break-word;
}


/* =========================================================
   FOOTER
========================================================= */

.footer {
    margin-top: 12px;

    padding-top: 5px;

    border-top: 1px solid #ddd;

    font-size: 5.9px;

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
   PAGE BREAK CONTROL
========================================================= */

.summary-section,
.section,
.experience-item,
.education-item,
.organization-item,
.side-section,
.contact-item,
.skill-item,
.language-item,
.profile-item {
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

                <div class="executive-label">
                    Executive Curriculum Vitae
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


                <div class="contact-line">

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

                    <div class="online-label">
                        LinkedIn
                    </div>

                    <div class="online-value">

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
                        class="online-label"
                        style="margin-top:5px;"
                    >
                        Portfolio
                    </div>

                    <div class="online-value">

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
     SUMMARY
========================================================= -->

<?php if (
    !empty(
        $personal['summary']
    )
): ?>

    <div class="summary-section">

        <div class="summary-title">

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


<!-- =========================================================
     MAIN CONTENT
========================================================= -->

<table class="main-table">

<tr>


<!-- =========================================================
     LEFT COLUMN
========================================================= -->

<td class="main-left">


    <!-- =====================================================
         EXPERIENCE
    ====================================================== -->

    <?php if (
        !empty(
            $experiences
        )
    ): ?>

        <div class="section">

            <div class="section-title">

                <span class="section-index">
                    01
                </span>

                Pengalaman Kerja

            </div>


            <?php foreach (
                $experiences
                as $experience
            ): ?>

                <div class="experience-item">


                    <div class="experience-position">

                        <?= cvText(
                            $experience['position'] ?? ''
                        ) ?>

                    </div>


                    <div class="experience-company">

                        <?= cvText(
                            $experience['company'] ?? ''
                        ) ?>


                        <?php if (
                            !empty(
                                $experience['location']
                            )
                        ): ?>

                            &nbsp; • &nbsp;

                            <?= cvText(
                                $experience['location']
                            ) ?>

                        <?php endif; ?>

                    </div>


                    <div class="experience-date">

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
         EDUCATION
    ====================================================== -->

    <?php if (
        !empty(
            $educations
        )
    ): ?>

        <div class="section">

            <div class="section-title">

                <span class="section-index">
                    02
                </span>

                Pendidikan

            </div>


            <?php foreach (
                $educations
                as $education
            ): ?>

                <div class="education-item">


                    <div class="education-institution">

                        <?= cvText(
                            $education['institution'] ?? ''
                        ) ?>

                    </div>


                    <div class="education-study">

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

                            &nbsp; • &nbsp;

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


                    <div class="education-date">

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
         ORGANIZATION
    ====================================================== -->

    <?php if (
        !empty(
            $organizations
        )
    ): ?>

        <div class="section">

            <div class="section-title">

                <span class="section-index">
                    03
                </span>

                Organisasi

            </div>


            <?php foreach (
                $organizations
                as $organization
            ): ?>

                <div class="organization-item">


                    <div class="organization-position">

                        <?= cvText(
                            $organization['position'] ?? ''
                        ) ?>

                    </div>


                    <div class="organization-name">

                        <?= cvText(
                            $organization['organization'] ?? ''
                        ) ?>

                    </div>


                    <div class="organization-date">

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
     RIGHT COLUMN
========================================================= -->

<td class="main-right">


    <!-- =====================================================
         CONTACT
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

            <div class="contact-item">

                <div class="contact-label">
                    Telepon
                </div>

                <div class="contact-value">

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

            <div class="contact-item">

                <div class="contact-label">
                    Email
                </div>

                <div class="contact-value">

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

            <div class="contact-item">

                <div class="contact-label">
                    Kota
                </div>

                <div class="contact-value">

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

            <div class="contact-item">

                <div class="contact-label">
                    Alamat
                </div>

                <div class="contact-value">

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
         SKILLS
    ====================================================== -->

    <?php if (
        !empty(
            $skills
        )
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

                    <div class="skill-item">

                        <?= cvText(
                            $skill['skill']
                        ) ?>

                    </div>

                <?php endif; ?>

            <?php endforeach; ?>


        </div>

    <?php endif; ?>


    <!-- =====================================================
         LANGUAGES
    ====================================================== -->

    <?php if (
        !empty(
            $languages
        )
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

                    <div class="language-item">

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
         ONLINE PROFILE
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

                <div class="profile-item">

                    <div class="profile-label">
                        LinkedIn
                    </div>

                    <div class="profile-value">

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

                <div class="profile-item">

                    <div class="profile-label">
                        Portfolio
                    </div>

                    <div class="profile-value">

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

                ATS Executive

            </td>

        </tr>

    </table>

</div>


</body>

</html>
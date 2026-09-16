<?php

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

@page {
    margin: 35px 42px;
}

body {
    font-family: DejaVu Sans, sans-serif;
    color: #222222;
    font-size: 9.5px;
    line-height: 1.5;
}

.header {
    padding-bottom: 12px;
    border-bottom: 2px solid #222222;
}

.name {
    font-size: 23px;
    font-weight: bold;
}

.position {
    margin-top: 3px;
    font-size: 10.5px;
}

.contact {
    margin-top: 7px;
    color: #555555;
    font-size: 8.5px;
}

.section {
    margin-top: 17px;
}

.section-title {
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
    margin-bottom: 8px;
    padding-bottom: 4px;
    border-bottom: 1px solid #555555;
}

.item {
    margin-bottom: 10px;
}

.item-title {
    font-weight: bold;
    font-size: 10px;
}

.item-subtitle {
    color: #444444;
}

.date {
    color: #666666;
    font-size: 8.5px;
}

.description {
    margin-top: 3px;
    text-align: justify;
}

.skill {
    margin-bottom: 4px;
}

</style>

</head>

<body>


<!-- =====================================================
     HEADER
===================================================== -->

<div class="header">

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


    <div class="contact">

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
            !empty(
                $personal['phone']
            )
        ): ?>

            &nbsp; | &nbsp;

            <?= cvText(
                $personal['phone']
            ) ?>

        <?php endif; ?>


        <?php if (
            !empty(
                $personal['city']
            )
        ): ?>

            &nbsp; | &nbsp;

            <?= cvText(
                $personal['city']
            ) ?>

        <?php endif; ?>

    </div>


    <?php if (
        !empty(
            $personal['linkedin']
        ) ||
        !empty(
            $personal['portfolio']
        )
    ): ?>

        <div class="contact">

            <?php if (
                !empty(
                    $personal['linkedin']
                )
            ): ?>

                LinkedIn:
                <?= cvText(
                    $personal['linkedin']
                ) ?>

            <?php endif; ?>


            <?php if (
                !empty(
                    $personal['portfolio']
                )
            ): ?>

                <?php if (
                    !empty(
                        $personal['linkedin']
                    )
                ): ?>

                    &nbsp; | &nbsp;

                <?php endif; ?>

                Portfolio:
                <?= cvText(
                    $personal['portfolio']
                ) ?>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</div>



<!-- =====================================================
     PROFIL
===================================================== -->

<?php if (
    !empty(
        $personal['summary']
    )
): ?>

<div class="section">

    <div class="section-title">
        Profil Profesional
    </div>

    <div class="description">

        <?= nl2br(
            cvText(
                $personal['summary']
            )
        ) ?>

    </div>

</div>

<?php endif; ?>

<!-- =====================================================
     PENDIDIKAN
===================================================== -->

<?php if (
    !empty($educations)
): ?>

<div class="section">

    <div class="section-title">
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
                        $education['major']
                    )
                ): ?>

                    —
                    <?= cvText(
                        $education['major']
                    ) ?>

                <?php endif; ?>

            </div>


            <div class="date">

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

                <div>

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
     PENGALAMAN KERJA
===================================================== -->

<?php if (
    !empty($experiences)
): ?>

<div class="section">

    <div class="section-title">
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

                    — <?= cvText(
                        $experience['location']
                    ) ?>

                <?php endif; ?>

            </div>


            <div class="date">

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
     ORGANISASI
===================================================== -->

<?php if (
    !empty($organizations)
): ?>

<div class="section">

    <div class="section-title">
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


            <div class="date">

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



<!-- =====================================================
     KEAHLIAN
===================================================== -->

<?php if (
    !empty($skills)
): ?>

<div class="section">

    <div class="section-title">
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

                •
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
===================================================== -->

<?php if (
    !empty($languages)
): ?>

<div class="section">

    <div class="section-title">
        Bahasa
    </div>


    <?php foreach (
        $languages
        as $language
    ): ?>

        <div class="skill">

            <strong>

                <?= cvText(
                    $language['language'] ?? ''
                ) ?>

            </strong>


            <?php if (
                !empty(
                    $language['proficiency']
                )
            ): ?>

                —
                <?= cvText(
                    $language['proficiency']
                ) ?>

            <?php endif; ?>

        </div>

    <?php endforeach; ?>

</div>

<?php endif; ?>


</body>

</html>
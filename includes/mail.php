<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/mail.php';

function sendCvEmail(
    $customerEmail,
    $customerName,
    $orderCode,
    $pdfPath
) {
    $mail = new PHPMailer(true);

    try {

        // SMTP
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;

        $mail->SMTPSecure =
            PHPMailer::ENCRYPTION_STARTTLS;

        $mail->Port = MAIL_PORT;

        // Pengirim
        $mail->setFrom(
            MAIL_FROM_ADDRESS,
            MAIL_FROM_NAME
        );

        // Penerima
        $mail->addAddress(
            $customerEmail,
            $customerName
        );

        // Lampiran PDF
        $mail->addAttachment(
            $pdfPath,
            'CV-' . $orderCode . '.pdf'
        );

        // Isi email
        $mail->isHTML(true);

        $mail->Subject =
            'CV Anda Telah Selesai - ' .
            $orderCode;

        $safeName = htmlspecialchars(
            $customerName,
            ENT_QUOTES,
            'UTF-8'
        );

        $safeOrderCode = htmlspecialchars(
            $orderCode,
            ENT_QUOTES,
            'UTF-8'
        );

        $mail->Body = '
            <div style="
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: auto;
            ">

                <h2 style="color: #dc2626;">
                    CV Anda Telah Selesai
                </h2>

                <p>
                    Halo <strong>' .
                    $safeName .
                    '</strong>,
                </p>

                <p>
                    CV Anda dengan nomor pesanan
                    <strong>' .
                    $safeOrderCode .
                    '</strong>
                    telah selesai diproses.
                </p>

                <p>
                    File CV dalam format PDF
                    telah dilampirkan pada email ini.
                </p>

                <p>
                    Silakan download dan periksa
                    kembali CV Anda.
                </p>

                <hr>

                <p>
                    Terima kasih telah menggunakan
                    <strong>CV ATS Professional</strong>.
                </p>

                <p>
                    Salam,<br>
                    <strong>CV ATS Professional</strong>
                </p>

            </div>
        ';

        // Versi teks jika email client tidak mendukung HTML
        $mail->AltBody =
            'Halo ' .
            $customerName .
            ', CV Anda dengan nomor pesanan ' .
            $orderCode .
            ' telah selesai diproses. ' .
            'File CV PDF terlampir pada email ini.';

        $mail->send();

        return [
            'success' => true,
            'message' => 'Email berhasil dikirim.'
        ];

    } catch (Exception $e) {

        return [
            'success' => false,
            'message' => $mail->ErrorInfo
        ];
    }
}
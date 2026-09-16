<?php


/*
|--------------------------------------------------------------------------
| Escape HTML
|--------------------------------------------------------------------------
*/

function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}


/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

function redirect($url)
{
    header("Location: {$url}");
    exit;
}


/*
|--------------------------------------------------------------------------
| Generate Order Code
|--------------------------------------------------------------------------
*/

function generateOrderCode()
{
    return 'CV-' .
        date('Ymd') .
        '-' .
        strtoupper(
            substr(
                bin2hex(
                    random_bytes(4)
                ),
                0,
                6
            )
        );
}


/*
|--------------------------------------------------------------------------
| Format Rupiah
|--------------------------------------------------------------------------
*/

function formatRupiah($amount)
{
    return 'Rp' .
        number_format(
            $amount,
            0,
            ',',
            '.'
        );
}


/*
|--------------------------------------------------------------------------
| Old Form Value
|--------------------------------------------------------------------------
*/

function old($key, $default = '')
{
    return e(
        $_POST[$key] ?? $default
    );
}


/*
|--------------------------------------------------------------------------
| Hapus Pesanan Kedaluwarsa
|--------------------------------------------------------------------------
|
| Pesanan yang:
|
| - payment_status = unpaid / pending
| - order_status   = waiting_payment
| - created_at     lebih dari 24 jam
|
| akan dihapus bersama seluruh data CV.
|
|--------------------------------------------------------------------------
*/

function cleanupExpiredOrders(PDO $pdo)
{
    try {

        /*
        |--------------------------------------------------------------
        | Cari pesanan yang sudah lebih dari 24 jam
        |--------------------------------------------------------------
        */

        $stmt = $pdo->query("
            SELECT
                id,
                order_code
            FROM cv_orders
            WHERE payment_status IN ('unpaid', 'pending')
            AND order_status = 'waiting_payment'
            AND created_at <= DATE_SUB(
                NOW(),
                INTERVAL 24 HOUR
            )
        ");

        $expiredOrders =
            $stmt->fetchAll();


        if (
            empty($expiredOrders)
        ) {
            return 0;
        }


        /*
        |--------------------------------------------------------------
        | Mulai transaksi
        |--------------------------------------------------------------
        */

        $pdo->beginTransaction();


        $deletedCount = 0;


        /*
        |--------------------------------------------------------------
        | Hapus setiap pesanan
        |--------------------------------------------------------------
        */

        foreach (
            $expiredOrders
            as $order
        ) {

            $orderId =
                (int) $order['id'];


            /*
            |----------------------------------------------------------
            | Hapus data pembayaran
            |----------------------------------------------------------
            */

            $stmtPayment =
                $pdo->prepare("
                    DELETE FROM payments
                    WHERE order_id = ?
                ");

            $stmtPayment->execute([
                $orderId
            ]);


            /*
            |----------------------------------------------------------
            | Hapus pendidikan
            |----------------------------------------------------------
            */

            $stmtEducation =
                $pdo->prepare("
                    DELETE FROM cv_education
                    WHERE order_id = ?
                ");

            $stmtEducation->execute([
                $orderId
            ]);


            /*
            |----------------------------------------------------------
            | Hapus pengalaman
            |----------------------------------------------------------
            */

            $stmtExperience =
                $pdo->prepare("
                    DELETE FROM cv_experience
                    WHERE order_id = ?
                ");

            $stmtExperience->execute([
                $orderId
            ]);


            /*
            |----------------------------------------------------------
            | Hapus organisasi
            |----------------------------------------------------------
            */

            $stmtOrganization =
                $pdo->prepare("
                    DELETE FROM cv_organization
                    WHERE order_id = ?
                ");

            $stmtOrganization->execute([
                $orderId
            ]);


            /*
            |----------------------------------------------------------
            | Hapus skills
            |----------------------------------------------------------
            */

            $stmtSkills =
                $pdo->prepare("
                    DELETE FROM cv_skills
                    WHERE order_id = ?
                ");

            $stmtSkills->execute([
                $orderId
            ]);


            /*
            |----------------------------------------------------------
            | Hapus bahasa
            |----------------------------------------------------------
            */

            $stmtLanguages =
                $pdo->prepare("
                    DELETE FROM cv_languages
                    WHERE order_id = ?
                ");

            $stmtLanguages->execute([
                $orderId
            ]);


            /*
            |----------------------------------------------------------
            | Hapus data personal
            |----------------------------------------------------------
            */

            $stmtPersonal =
                $pdo->prepare("
                    DELETE FROM cv_personal
                    WHERE order_id = ?
                ");

            $stmtPersonal->execute([
                $orderId
            ]);


            /*
            |----------------------------------------------------------
            | Hapus order
            |----------------------------------------------------------
            */

            $stmtOrder =
                $pdo->prepare("
                    DELETE FROM cv_orders
                    WHERE id = ?
                ");

            $stmtOrder->execute([
                $orderId
            ]);


            $deletedCount++;

        }


        /*
        |--------------------------------------------------------------
        | Commit
        |--------------------------------------------------------------
        */

        $pdo->commit();


        return $deletedCount;


    } catch (Throwable $e) {

        /*
        |--------------------------------------------------------------
        | Rollback jika terjadi error
        |--------------------------------------------------------------
        */

        if (
            $pdo->inTransaction()
        ) {

            $pdo->rollBack();

        }


        /*
        |--------------------------------------------------------------
        | Jangan hentikan sistem utama
        |--------------------------------------------------------------
        */

        return 0;
    }
}
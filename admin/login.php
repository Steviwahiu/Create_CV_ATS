<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';


/*
|--------------------------------------------------------------------------
| Jika sudah login
|--------------------------------------------------------------------------
*/

if (isAdminLoggedIn()) {
    redirect(ADMIN_URL . '/dashboard.php');
}


$error = '';


/*
|--------------------------------------------------------------------------
| Proses Login
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verifyCsrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {

        $error = 'Email dan password wajib diisi.';

    } else {

        $stmt = $pdo->prepare("
            SELECT
                id,
                name,
                email,
                password,
                role,
                status
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        $user = $stmt->fetch();


        if (
            $user &&
            $user['status'] === 'active' &&
            password_verify($password, $user['password'])
        ) {

            /*
            |--------------------------------------------------------------------------
            | Regenerate Session
            |--------------------------------------------------------------------------
            */

            session_regenerate_id(true);

            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_role'] = $user['role'];

            redirect(ADMIN_URL . '/dashboard.php');

        } else {

            $error = 'Email atau password tidak valid.';

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
        content="width=device-width, initial-scale=1"
    >

    <title>
        Login Admin - <?= e(APP_NAME) ?>
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
            min-height: 100vh;
            background: #f8fafc;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 35px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, .06);
        }

        .login-logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            border-radius: 14px;
            background: #dc2626;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 700;
        }

        .login-title {
            font-size: 25px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 5px;
        }

        .login-subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
        }

        .form-control {
            min-height: 48px;
            border-radius: 10px;
        }

        .form-control:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 .2rem rgba(220, 38, 38, .10);
        }

        .btn-login {
            min-height: 48px;
            border-radius: 10px;
            background: #dc2626;
            border-color: #dc2626;
            color: #ffffff;
            font-weight: 600;
        }

        .btn-login:hover {
            background: #b91c1c;
            border-color: #b91c1c;
            color: #ffffff;
        }

        .back-home {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #6b7280;
            text-decoration: none;
            font-size: 14px;
        }

        .back-home:hover {
            color: #dc2626;
        }

    </style>

</head>

<body>

<div class="login-wrapper">

    <div class="login-card">

        <div class="login-logo">
            CV
        </div>

        <h1 class="login-title">
            Login Admin
        </h1>

        <p class="login-subtitle">
            Masuk ke panel administrasi
        </p>


        <?php if ($error !== ''): ?>

            <div class="alert alert-danger">

                <?= e($error) ?>

            </div>

        <?php endif; ?>


        <form method="POST">

            <?= csrfField() ?>


            <div class="mb-3">

                <label
                    for="email"
                    class="form-label"
                >
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    placeholder="Masukkan email"
                    value="<?= old('email') ?>"
                    autocomplete="email"
                    required
                >

            </div>


            <div class="mb-4">

                <label
                    for="password"
                    class="form-label"
                >
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn btn-login w-100"
            >
                Masuk
            </button>

        </form>


        <a
            href="<?= e(APP_URL) ?>"
            class="back-home"
        >
            ← Kembali ke halaman utama
        </a>

    </div>

</div>

</body>

</html>
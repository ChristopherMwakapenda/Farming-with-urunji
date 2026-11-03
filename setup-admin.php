<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

start_app_session();

$message = '';
$error = '';

try {
    $pdo = db();

    // Prevent setup from being used after an administrator exists.
    $count = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    if ($count > 0) {
        exit('Administrator setup has already been completed. Delete setup-admin.php.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if ($username === '') {
            throw new RuntimeException('Username is required.');
        }

        if (strlen($username) < 3) {
            throw new RuntimeException('Username must contain at least 3 characters.');
        }

        if ($password === '') {
            throw new RuntimeException('Password is required.');
        }

        if (strlen($password) < 8) {
            throw new RuntimeException('Password must contain at least 8 characters.');
        }

        if ($password !== $confirm) {
            throw new RuntimeException('Passwords do not match.');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users
                (username, password_hash, role, active)
            VALUES
                (:username, :password_hash, 'admin', 1)
        ");

        $stmt->execute([
            ':username' => $username,
            ':password_hash' => $passwordHash
        ]);

        $message = 'Administrator account created successfully. Delete setup-admin.php now, then use the login page.';
    }

} catch (Throwable $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farming with Urunji - Administrator Setup</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f1eb;
            font-family: Arial, sans-serif;
            color: #2d2d2d;
            padding: 20px;
        }

        .card {
            width: 100%;
            max-width: 430px;
            background: #ffffff;
            border-radius: 14px;
            padding: 32px;
            box-shadow: 0 10px 35px rgba(0,0,0,.08);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 25px;
        }

        .subtitle {
            margin: 0 0 25px;
            color: #66705f;
        }

        label {
            display: block;
            margin: 16px 0 7px;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d7d0c7;
            border-radius: 8px;
            font-size: 15px;
        }

        button {
            width: 100%;
            margin-top: 24px;
            padding: 13px;
            border: 0;
            border-radius: 8px;
            background: #667a3e;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        .success {
            padding: 13px;
            border-radius: 8px;
            background: #e9f4e4;
            color: #35602c;
            margin-bottom: 20px;
        }

        .error {
            padding: 13px;
            border-radius: 8px;
            background: #fbeaea;
            color: #8a2f2f;
            margin-bottom: 20px;
        }

        .note {
            margin-top: 20px;
            font-size: 13px;
            color: #777;
            line-height: 1.5;
        }
    </style>
</head>

<body>

<div class="card">

    <h1>Farming with Urunji</h1>
    <p class="subtitle">Farm Management System — Administrator Setup</p>

    <?php if ($message): ?>
        <div class="success">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <?php if (!$message): ?>

        <form method="POST">

            <label for="username">Administrator Username</label>
            <input
                type="text"
                id="username"
                name="username"
                required
                autocomplete="username"
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
                minlength="8"
                autocomplete="new-password"
            >

            <label for="confirm_password">Confirm Password</label>
            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                required
                minlength="8"
                autocomplete="new-password"
            >

            <button type="submit">
                Create Administrator
            </button>

        </form>

        <div class="note">
            Your password will be stored as a secure PHP password hash.
            It will not be stored as plain text.
        </div>

    <?php endif; ?>

</div>

</body>
</html>
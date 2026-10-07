<?php
require 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errors  = [];
$success = '';
$email   = $_COOKIE['remember_email'] ?? '';

if (isset($_GET['registered'])) {
    $success = 'Account created successfully! Please log in.';
}
if (isset($_GET['loggedout'])) {
    $success = 'You have been logged out.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!csrf_valid($_POST['csrf'] ?? '')) {
        $errors['general'] = 'Invalid request. Please refresh and try again.';
    }

    if ($email === '') {
        $errors['email'] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if ($password === '') {
        $errors['password'] = 'Password is required.';
    }

    if (!$errors) {
        $row = find_user($email);

        if ($row && password_verify($password, $row['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['user_name'] = $row['full_name'];

            if (!empty($_POST['remember'])) {
                setcookie('remember_email', $email, time() + 60 * 60 * 24 * 30, '/');
            } else {
                setcookie('remember_email', '', time() - 3600, '/');
            }
            header('Location: dashboard.php');
            exit;
        }
        $errors['general'] = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="page">
    <section class="welcome">
        <h1>Welcome Back</h1>
        <h2>Good to see you <span>again.</span></h2>
        <p>Log in to continue and get back<br>to what matters.</p>
        <div class="line"></div>
    </section>

    <section class="card">
        <h3>Log In</h3>
        <p class="sub">Enter your email and password to continue.</p>

        <?php if ($success): ?><div class="alert success"><?= e($success) ?></div><?php endif; ?>
        <?php if (isset($errors['general'])): ?><div class="alert error"><?= e($errors['general']) ?></div><?php endif; ?>

        <form method="POST" action="index.php" novalidate>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

            <div class="field">
                <div class="input <?= isset($errors['email']) ? 'invalid' : '' ?>">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email address" value="<?= e($email) ?>">
                </div>
                <?php if (isset($errors['email'])): ?><small class="msg"><?= e($errors['email']) ?></small><?php endif; ?>
            </div>

            <div class="field">
                <div class="input <?= isset($errors['password']) ? 'invalid' : '' ?>">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Password">
                    <i class="fa-regular fa-eye toggle"></i>
                </div>
                <?php if (isset($errors['password'])): ?><small class="msg"><?= e($errors['password']) ?></small><?php endif; ?>
            </div>

            <div class="row">
                <label class="remember">
                    <input type="checkbox" name="remember" <?= $email && isset($_COOKIE['remember_email']) ? 'checked' : '' ?>> Remember me
                </label>
                <a href="#" class="link" onclick="alert('Password reset is not part of this exam.'); return false;">Forgot password?</a>
            </div>

            <button type="submit" class="btn">Log In</button>
        </form>

        <div class="or">or</div>
        <p class="switch">Don't have an account? <a href="register.php">Sign Up</a></p>
    </section>
</div>
<script src="assets/js/script.js"></script>
</body>
</html>

<?php
require 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errors   = [];
$fullName = '';
$email    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (!csrf_valid($_POST['csrf'] ?? '')) {
        $errors['general'] = 'Invalid request. Please refresh and try again.';
    }

    // Full name
    if ($fullName === '') {
        $errors['full_name'] = 'Full name is required.';
    } elseif (strlen($fullName) < 2 || strlen($fullName) > 100) {
        $errors['full_name'] = 'Full name must be 2 to 100 characters.';
    } elseif (!preg_match("/^[\p{L}\s.'-]+$/u", $fullName)) {
        $errors['full_name'] = 'Full name contains invalid characters.';
    }

    // Email
    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    } else {
        if (find_user($email)) {
            $errors['email'] = 'This email is already registered.';
        }
    }

    // Password
    if ($password === '') {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors['password'] = 'Use uppercase, lowercase, and a number.';
    }

    // Confirm password
    if ($confirm === '') {
        $errors['confirm_password'] = 'Please confirm your password.';
    } elseif ($password !== $confirm) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if (!$errors) {
        $users = users_load();
        $users[] = [
            'id'         => uniqid(),
            'full_name'  => $fullName,
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
        ];
        users_save($users);
        header('Location: index.php?registered=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
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
        <h3>Create Account</h3>
        <p class="sub">Fill in your details to get started.</p>

        <?php if (isset($errors['general'])): ?><div class="alert error"><?= e($errors['general']) ?></div><?php endif; ?>

        <form method="POST" action="register.php" novalidate>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

            <div class="field">
                <div class="input <?= isset($errors['full_name']) ? 'invalid' : '' ?>">
                    <i class="fa-regular fa-user"></i>
                    <input type="text" name="full_name" placeholder="Full Name" value="<?= e($fullName) ?>">
                </div>
                <?php if (isset($errors['full_name'])): ?><small class="msg"><?= e($errors['full_name']) ?></small><?php endif; ?>
            </div>

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

            <div class="field">
                <div class="input <?= isset($errors['confirm_password']) ? 'invalid' : '' ?>">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="confirm_password" placeholder="Confirm Password">
                    <i class="fa-regular fa-eye toggle"></i>
                </div>
                <?php if (isset($errors['confirm_password'])): ?><small class="msg"><?= e($errors['confirm_password']) ?></small><?php endif; ?>
            </div>

            <button type="submit" class="btn">Register</button>
        </form>

        <div class="or">or</div>
        <p class="switch">Already have an account? <a href="index.php">Log In</a></p>
    </section>
</div>
<script src="assets/js/script.js"></script>
</body>
</html>

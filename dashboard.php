<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="page">
    <section class="card dash">
        <h3>Welcome, <?= e($_SESSION['user_name']) ?>!</h3>
        <div class="alert success">You are successfully logged in.</div>
        <a href="logout.php" class="btn" style="display:block;text-align:center;text-decoration:none;">Log Out</a>
    </section>
</div>
</body>
</html>

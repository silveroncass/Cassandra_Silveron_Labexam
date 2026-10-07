<?php
session_start();
$_SESSION = [];
session_destroy();
header('Location: index.php?loggedout=1');
exit;

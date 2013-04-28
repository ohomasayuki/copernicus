<?php
session_start();
$next_url = urlencode("http://" . $_SERVER['SERVER_NAME'] . '/');
$logout_url = "https://www.facebook.com/logout.php?next=" . $next_url . "&access_token=" . $_SESSION['access_token'];
//echo $logout_url;
//var_dump($_SESSION);
//exit;
header('Location: ' . $logout_url);

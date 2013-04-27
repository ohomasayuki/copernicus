<?php
session_start();
$_SESSION['application_id'] = '473647019370431';
$_SESSION['application_secret'] = '6501064879c32dea845b6e0caaafd099';
$_SESSION['redirect_uri'] = 'http://' . $_SERVER['SERVER_NAME'] . '/facebook/oauth_end.php';
$_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF対策
 
$url = 'https://www.facebook.com/dialog/oauth'
        . '?client_id=' . $_SESSION['application_id']
        . '&redirect_uri=' . urlencode($_SESSION['redirect_uri'])
        . '&state=' . $_SESSION['state']
        . '&scope=publish_stream'; //投稿する場合
header('Location: ' . $url);

<?php
require_once '../common.inc.php';
//session_start();
if (!isset($_SESSION['state'], $_GET['state']) || ($_SESSION['state'] !== $_GET['state'])) {
  var_dump($_SESSION);
  echo "state error: session=" . $_SESSION['state'] . " get=" . $_GET['state'];
  //session_destroy();
  die();
}

//$cxContext = stream_context_create(array(//プロキシサーバを利用する場合
//    'http' => array('proxy' => 'tcp://proxy.example.net',
//        'request_fulluri' => True)));

$url = 'https://graph.facebook.com/oauth/access_token'
        . '?client_id=' . $_SESSION['application_id']
        . '&client_secret=' . $_SESSION['application_secret']
        . '&redirect_uri=' . $_SESSION['redirect_uri']
        . '&code=' . $_GET['code'];
$result = file_get_contents($url);
$output = null;
parse_str($result, $output);
var_dump($output);
if (!isset($output['access_token'])) {
  echo "no access token";
  //session_destroy();
  die();
}
$_SESSION['access_token'] = $output['access_token'];

echo 'token=' . $_SESSION['access_token'];
redirect('../m/start.html');

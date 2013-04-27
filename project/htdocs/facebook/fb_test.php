<?php
require_once '../common.inc.php';
$graph_url = "https://graph.facebook.com/me?fields=picture,username&access_token=" . $_SESSION['access_token'];
$user = json_decode(file_get_contents($graph_url));
var_dump($user);
echo "てすと";
$smarty->assign('user',$user);
finish();


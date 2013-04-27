<?php
require_once 'common.inc.php';
var_dump($_SESSION);
$_SESSION['test'] = date('Y/m/d H:i:s');
finish();

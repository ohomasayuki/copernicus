<?php
mb_internal_encoding('UTF-8');
//require_once('../inc/start.inc.php');
define('DEBUG',1);
$LIB_DIR = dirname(__FILE__);
require_once($LIB_DIR . '/Karamail.class.php');
require_once($LIB_DIR . '/Debug.class.php');
require_once($LIB_DIR . '/MailTag.class.php');
require_once($LIB_DIR . '/MemberCode.class.php');

$TEST = 1;

$mail = new Karamail();
$mail->parse();


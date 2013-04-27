<?php
//サーバ毎のセッティングファイル
//本番環境やファイル出力などデバッグ表示を禁止するときに1にする
define('NO_ECHO', 0);
//デバッグモードフラグ
define('DEBUG', 1);
//デバッグ画面出力フラグ
define('DEBUG_DISPLAY', 0);
//エラー画面表示フラグ
ini_set('display_errors', 1);
//エラーレベル
error_reporting(E_ALL);
mb_internal_encoding('UTF-8');
//assert
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 1);
assert_options(ASSERT_QUIET_EVAL, 0);
//DB
define('DB_ENCODING', 'utf8');
define('DB_USER', 'yoshida');
define('DB_PASS', '');
define('DB_NAME', 'yoshida');
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
//slave
define('DB_USER2', 'blt');
define('DB_PASS2', 'bltpass');
define('DB_NAME2', 'blt');
define('DB_HOST2', '127.0.0.1');
define('DB_PORT2', '3306');
//公式サイトID
define('SID_DOCOMO'  , 'IXXX');
define('SID_AU'      , 'EXXX');
define('SID_SOFTBANK', 'ASF1');
//キャリアID
define('CARRIER_I', 'i'); // i-mode
define('CARRIER_E', 'e'); // EZweb
define('CARRIER_Y', 'y'); // Yahoo! mobile
//PATH
define('ROOT_PATH', dirname(dirname(__FILE__)) . '/' ); // DocumentRootの1つ上(例:/usr/local/apache2/)
define('ROOT_URL', 'http://' . $_SERVER['SERVER_NAME'] . '/');
define('SSL_ROOT_URL', 'https://' . $_SERVER['SERVER_NAME'] . '/'); // 使用しない
define('ROOT_URI', 'asunaro2/test/htdocs/');
define('HTDOCS_PATH', ROOT_PATH . 'htdocs/');
define('UP_BASE_URL', ROOT_URL . 'up/');
define('UP_BASE_PATH', HTDOCS_PATH . 'up/');
// DEBUG: デフォルトのユーザーエージェント
//define('DEBUG_USERAGENT', 'KDDI-HI37 UP.Browser/6.2.0.10.3.3 (GUI) MMP/2.0');
//define('DEBUG_USERAGNET', 'KDDI-HI34 UP.Browser/6.2.0.7.3.129 (GUI) MMP/2.0'); // BENCK
//docomo
//define('DEBUG_USERAGENT', 'DoCoMo/2.0 N2001(c10)');	//回答まち
//define('DEBUG_USERAGENT', 'DoCoMo/1.0/P506iC/c20/TB/W20H10');	//HTML
//define('DEBUG_USERAGENT', 'DoCoMo/1.0/D504i/c10');	//非対応
define('DEBUG_USERAGENT', 'DoCoMo/2.0 D902i(c100;TB;W23H16)');	//XHTML
// ロボットがきたとき、どの機種として遷移させるか設定
define('ROBOT_USERAGENT', 'DoCoMo/2.0 P905i(c100;TB;W23H16)');	//XHTML
//softbank
//define('DEBUG_USERAGENT', 'J-PHONE/4.3/V602SH/0123456789 SH/0006aa Profile/MIDP-1.0 Configuration/CLDC-1.0 Ext-Profile/JSCL-1.2.2');	//HTML
//define('DEBUG_USERAGENT', 'Vodafone/1.0/V903SH/SHJ001/0123456789 Browser/UP.Browser/7.0.2.1 Profile/MIDP-2.0 Configuration/CLDC-1.1 Ext-J-Profile/JSCL-1.2.2 Ext-V-Profile/VSCL-2.1.0');	//XHTML
// HTML|XHTML判別
define('HTML', 'HTML');
define('XHTML', 'XHTML');


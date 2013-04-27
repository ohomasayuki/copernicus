<?php
//サーバ毎のセッティングファイル
//本番環境やファイル出力などデバッグ表示を禁止するときに1にする
define('NO_ECHO', 0);
//デバッグモードフラグ
define('DEBUG', 1);
//デバッグ画面出力フラグ
define('DEBUG_DISPLAY', 1);
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
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'myapps_development');
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
//slave
define('DB_USER2', '');
define('DB_PASS2', '');
define('DB_NAME2', '');
define('DB_HOST2', '127.0.0.1');
define('DB_PORT2', '3306');
define('ROOT_PATH', dirname(dirname(__FILE__)) . '/' ); // DocumentRootの1つ上(例:/usr/local/apache2/)
define('ROOT_URL', 'http://' . $_SERVER['SERVER_NAME'] . '/');
define('SSL_ROOT_URL', 'https://' . $_SERVER['SERVER_NAME'] . '/'); // 使用しない
define('ROOT_URI', '');
define('HTDOCS_PATH', ROOT_PATH . 'htdocs/');
define('UP_BASE_URL', ROOT_URL . 'up/');
define('UP_BASE_PATH', HTDOCS_PATH . 'up/');
// HTML|XHTML判別
define('HTML', 'HTML');
define('XHTML', 'XHTML');


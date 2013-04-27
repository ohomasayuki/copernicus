<?
// バッチなども共通でインクルードするファイル
ob_start('my_mb_output_handler');
session_start();
error_reporting(E_ALL);
if( phpversion() > 5 )  date_default_timezone_set('Asia/Tokyo');
$g = new Common();
$g->PATH['root'] = dirname(dirname(__FILE__));
$g->PATH['libs'] = $g->PATH['root'].'/lib';
$g->PATH['inc'] = $g->PATH['root'].'/inc';
require_once $g->PATH['libs'] . '/settings.inc.php';
require_once $g->PATH['libs'] . '/mobile/emoji_sjis.inc.php';
require_once $g->PATH['libs'] . '/Debug.class.php';
$debugger = getDebugger();
$debugger->is_display_off = 1;
if( strpos($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'], 'admin') !== false ){
  // ADMIN
  $g->g['OUTPUT_ENCODING'] = 'UTF-8';
  $_is_admin = true;
}else{
  $g->g['OUTPUT_ENCODING'] = 'Shift_JIS';
  $_is_admin = false;
  if( $_SERVER['REMOTE_ADDR'] == '125.200.77.99' ){
    // 社内開発時のみデバッグ出力
    $debugger->is_display_off = 0; 
  }
}
assert_options(ASSERT_CALLBACK, 'my_assert_handler');
set_error_handler("my_error_handler");
require_once $g->PATH['libs'] . '/Lib.inc.php';
$g->g['start_time'] = microtime_float(); // 起動時間
require_once $g->PATH['libs'] . '/Db.class.php';
//debug_setComment(1);
require_once $g->PATH['libs'] . '/smarty.inc.php';
require_once $g->PATH['root'] . '/smarty/Smarty.class.php';
require_once $g->PATH['inc']  . '/define.inc.php'; // アプリ定義
require_once $g->PATH['inc']  . '/common.inc.php'; // アプリ共通
require_once $g->PATH['libs'] . '/mobile/Mobile.inc.php';
require_once $g->PATH['libs'] . '/mobile/mobile.conf.php';
require_once $g->PATH['libs'] . '/mobile/mobile_swf.conf.php';
require_once $g->PATH['libs'] . '/mobile/mobile_swf.conf.php';
require_once $g->PATH['libs'] . '/check.inc.php';
require_once $g->PATH['libs'] . '/Pager.class.php';
require_once $g->PATH['libs'] . '/MailTag.class.php';
$req = array_merge($_GET,$_POST);
if(!$_is_admin){$req = requestConvertEmoji($req);}
debug('start '.$_SERVER['REQUEST_URI']);
debug($req,'req');
//mbconvert($req);
$db = new Db();
$db->dbname = DB_NAME;
$db->user = DB_USER;
$db->password = DB_PASS;
$db->host = DB_HOST;
$db->connect();
$db->query('set character set utf8');
$db->query('set names utf8');

//=================  以下、関数,クラス定義のみ ============================

function isRobotAccess(){
	global $g;
	if( strpos( $g->g['useragent'], 'Apache') !== false || strpos($g->g['useragent'], 'ISIM0404') !== false){
		return true;
	}
	return false;
}
/**
 * 共通パラメータを付与したURLを取得
 * 外部サイトなら何も付与しない。
 * "http://yahoo.co.jp" = urlWlap('http://yahoo.co.jp');
 * "http://内部サイト/xxx/index.php?userID=xx" urlWlap('http://内部サイト/xxx/index.php');
 * "index.php?a=1&userID=xx" = urlWlap('index.php?a=1');
 */
function urlWlap($url){
  global $g;
  //debug(__FUNCTION__.$url);
  $add['back'] = urlencode(createBackUrl());
  if( isDocomo() ){
    $add['uid'] = 'NULLGWDOCOMO';
    $add['guid'] = 'on';
  }
  $add['rnd'] = rand(1000,9999); // キャッシュを読まないように乱数をセット
  if( strpos($url, 'http') !== 0 ){
    return addParam( $url, $add ); // 内部サイト(絶対URL)
  }
  if( strpos($url, ROOT_URL) !== false || strpos($url, SSL_ROOT_URL) !== false){
    return addParam( $url, $add ); // 内部サイト(絶対URL)
  }
  return $url; // 外部サイト(共通パラメータを付与しない)
}
/**
 * $back = uelencode('a.php?a=1&back=z.php'); 
 * 'a.php?a=1' = this('b.php?c=2&back='.$back)
 * 注: backパラメータがどんどん長くならないよう、backは外す
 */
function getBackUrl(){	
	global $req;
	if( ! isset($req['back']) ) return ROOT_URL;
	$url = $req['back'];
	return urldecode($url);
}
/**
 * $_SERVER['REQUEST_URI'] = 'a.php?abc=123&back=xxxxx';
 * 'a.php?abc=123' = this();
 */
function createBackUrl(){
	$url = $_SERVER['REQUEST_URI'];
	$pos = strpos($url, '?');
	if( $pos === false ) return $url;
	$param = getUrlParam($url);
	if( isset($param['back']) ) unset($param['back']);
	$url = substr($url, 0, $pos);
	foreach($param as $k => $v){
		$url .= strpos($url, '?') !== false ? '&' : '?';
		$url .= $k . '=' . $v;
	}
	return $url;
}

function getCarrier($useragent){
	if( strpos($useragent, 'DoCoMo') === 0 ){
		return 'i';
	}
	if( strpos($useragent, 'KDDI') !== false || strpos($useragent, 'UP. Browser') !== false){
		return 'e';
	}
	if( strpos($useragent, 'SoftBank') === 0 || strpos($useragent, 'Vodafone') === 0 || strpos($useragent, 'J-PHONE') === 0){
		return 'y';
	}
	// default
	return 'i';
}

function mbconvert(&$s, $to='UTF-8', $from='Shift_JIS'){
	if( is_array($s) ){
		foreach($s as $k => $v){
			mbconvert($v);
			$s[$k] = $v;
		}
	}else if( is_string($s) ){
		$s = mb_convert_encoding($s, $to, $from);
	}
}
function exitError($s=""){
	global $g;
	$g->exitError($s);
}
class Common{
	var $PATH;
	var $THIS_PAGE;
	var $g; // global
	var $member;
	var $error;
	var $report;
	var $is_xhtml = true;
	var $is_pc = false;
	function Common(){
		$this->PATH = array();
		$this->error = array();
		$this->report = array();
	}
	function isPc(){
		return $this->is_pc;
	}
	function isXhtml(){
		return $this->is_xhtml;
	}
	function setLogin($userId){
		$_SESSION['userId'] = $userId;
	}
	function setLogout(){
		$_SESSION['userId'] = null;
	}
	function checkLogin(){
		global $db;
		// 毎回DBと照合する
		//if( $this->isLogin() ){
		//	return true;
		//}
		// 自動ログイン
		$data = $db->select1('select * from member where uid = :uid and valid = true',array('uid'=>getCarrierUid()));
		if( $data ){
			$this->setLogin($data['id']);
			return true;
		}
		// 会員でない or 非ログイン
		//redirect('login.php');
	}
	function getUserData(){
		global $db;
		$this->checkLogin();
		$data = $db->select1('select * from member where id = :id', array('id'=>$this->getUserId()) );
		debug($data,'member');
		return $data;
	}
	function isLogin(){
		return $_SESSION['userId'] ? true : false;
	}
	function getUserId(){
		return $_SESSION['userId'];
	}
	function exitError($message){
		_log($message);
		//ob_clean();
		echo '<html><title></title><body>大変申し訳ありません｡<br />ただいまｻｲﾄが混み合っています｡時間をおいて再度ｱｸｾｽください｡</body></html>';
		exit;
	}
	function paramExitError($message){
		$this->exitError($message);
	}
	function getNow(){
		return time();
	}
	/**
	 * ログイン専用画面で使用。
	 */
	function onlyLogin(){
		if( ! $this->isLogin() ){
			redirect('entry_top.php?from=memberpage');
		}
	}
}
function my_mb_output_handler($buffer){
  global $g, $_is_admin;
  if( $_is_admin ){
    // 管理画面
    return $buffer;
  }
  $buffer = mb_convert_kana($buffer, 'ka');
  if( ! $buffer ){
    //exitError();
  }
  $enc = $g->g['OUTPUT_ENCODING'];
  if( ! $enc ){
    $enc = 'Shift_JIS';
  }
  return mb_convert_encoding($buffer, $enc);
}	
// ハンドラ関数を作成する
function my_assert_handler($file, $line, $code) 
{
  debug('Assertion Failed:File '.$file.':Line '.$line.':Code '.$code);
}
function my_error_handler($errno, $str, $file, $line)
{
  if( $errno == E_USER_ERROR || $errno == E_WARNING){
    //mail('');
  }else if($errno == E_NOTICE){
  }
  debug('['.$file.' line'.$line.':'.$errno.']'.$str);
}


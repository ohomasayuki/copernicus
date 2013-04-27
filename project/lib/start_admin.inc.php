<?
<?
require_once dirname(__FILE__).'/core.inc.php';
$g->g['template_file'] = getStaticTemplateFile();
$g->PATH['templates'] = dirname($g->g['template_file']).'/';
//setDebugMode();
$g->THIS_PAGE = getThisPage();
$smarty->assign('THIS_PAGE', $g->THIS_PAGE);
$smarty->assign('OUTPUT_ENCODING', $g->g['OUTPUT_ENCODING']);
//-----------------------
// 共通処理
//-----------------------
$g->g['useragent'] = $_SERVER['HTTP_USER_AGENT'];
if( isRobotAccess() ){
	debug('++++++++++++ROBOT+['.$g->g['useragent'].']++++++++++++++++');
	$g->g['useragent'] = ROBOT_USERAGENT;
}
$g->g['carrier'] = getCarrier($g->g['useragent']);
$g->is_pc = strpos($g->g['useragent'], 'Mozilla') !== false;
// コンテントヘッダ出力
if( ! $g->isPc() ){
	debug_setComment(1);
}else{
	session_start();
	debug_setComment(0);
	// エミュレート
	if( isset($_REQUEST['useragent']) ){
		$g->g['useragent'] = $_REQUEST['useragent'];
	}
	$g->g['useragent'] = DEBUG_USERAGENT;
	$g->g['carrier'] = getCarrier($g->g['useragent']);
}
$g->g['short_useragent'] = getShortUseragent($g->g['carrier'], $g->g['useragent']);
debug('carrier:' . $g->g['carrier'] . ' useragent:' . $g->g['useragent'] . ' short_ua:' . $g->g['short_useragent']);
if( isSoftbank() ){
	$g->g['sid'] = SID_SOFTBANK;
}else if( isAu() ){
	$g->g['sid'] = SID_AU;
}else if( isDocomo() ){
	$g->g['sid'] = SID_DOCOMO;	
}else{
	exitError('pc');
}
// パケホーダイ設定
if($req['DCMPAKEHO']=='ON' && isDocomo() && !isset($_SERVER['HTTP_X_DCM_PAKEHO']) ){
  debug('debug: set pakeho');
  $_SERVER['HTTP_X_DCM_PAKEHO'] = 1;
}
// XHTML判定
$g->is_xhtml = true;
if(getTypeHTML($g->g['carrier'], $g->g['short_useragent']) == XHTML ){$g->is_xhtml = true;}else
if(getTypeHTML($g->g['carrier'], $g->g['short_useragent']) == HTML ){$g->is_xhtml = false;}else
if(getTypeHTML($g->g['carrier'], $g->g['short_useragent']) == false || isHdml() ){
  /*非対応機種*/;
  debug('HITAIOU: carrier=['.$g->g['carrier'].'] ua=[' . $g->g['useragent'] . '] short['.$g->g['short_useragent'].']');
  redirect(HITAIOU_URL);
  $smarty->display('hitaiou.tpl');
  exit;
}
//if($g->g['short_useragent'] == 'HI34') $g->is_xhtml = false;
if( $g->isPc() ){
	if(  isset($_SESSION['debug']['is_html']) && $_SESSION['debug']['is_html'] ){
		$g->is_xhtml = false;
	}
	if( strpos($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'], 'admin') !== false ){
		$g->is_xhtml = false;
	}
	header('Content-type: text/html; charset='.$g->g['OUTPUT_ENCODING']);
}else{
	if( $g->isXhtml() ){
		if( ! isset($NO_HEADER) ){
			header('Content-type: application/xhtml+xml; charset='.$g->g['OUTPUT_ENCODING']);
		}
	}
}
$g->g['uid'] = getCarrierUid();
$g->member = $g->getUserData();
$smarty->assign('IS_MEMBER', $g->member ? true : false );
$smarty->assign('member', $g->member);
$smarty->assign('CARRIER', $g->g['carrier']);
// テンプレートディレクトリ切替
$smarty->template_dir .= $g->isXhtml() ? 'xhtml/' : 'html/';
debug('templates_dir=['.$smarty->template_dir.'] file=['.$g->g['template_file'].']');
$g->g['FOOTER_FILE'] = './footer.inc.tpl';
$smarty->assign('ERROR_MESSAGE_TPL','./error.inc.tpl');
//getDebugger()->is_display_off = 0;
debug('start_common end');





// 管理画面用共通ファイル
ini_set('session.cache_limiter', 'none');
error_reporting(E_ALL);
ob_start('my_mb_output_handler');
//date_default_timezone_set('Asia/Tokyo');
$g = new Common();
$g->PATH['root'] = dirname(dirname(__FILE__));
$g->PATH['libs'] = $g->PATH['root'].'/lib';
require_once $g->PATH['libs'] . '/settings.inc.php';
require_once $g->PATH['libs'] . '/Debug.class.php';
// ADMIN
$g->g['OUTPUT_ENCODING'] = 'UTF-8';
getDebugger()->is_display_off = 1;
assert_options(ASSERT_CALLBACK, 'my_assert_handler');
set_error_handler("my_error_handler");
require_once $g->PATH['libs'] . '/Lib.inc.php';
require_once $g->PATH['libs'] . '/Db.class.php';
debug_setComment(1);
require_once $g->PATH['libs'] . '/smarty.inc.php';
require_once $g->PATH['root'] . '/smarty/Smarty.class.php';
require_once $g->PATH['libs'] . '/appli/common.inc.php';
require_once $g->PATH['libs'] . '/Mobile.inc.php';
require_once $g->PATH['libs'] . '/mobile.conf.php';
debug('start common');
$req = array_merge($_GET,$_POST);

//mbconvert($req);
$db = new Db();
$db->dbname = DB_NAME;
$db->user = DB_USER;
$db->password = DB_PASS;
$db->host = DB_HOST;
$db->connect();
//$db->query('set character set sjis');
//$db->query('set names sjis');
$g->g['template_file'] = getStaticTemplateFile();
$g->PATH['templates'] = dirname($g->g['template_file']).'/';
//setDebugMode();
$g->THIS_PAGE = getThisPage();
$smarty->assign('THIS_PAGE', $g->THIS_PAGE);
$smarty->assign('OUTPUT_ENCODING', $g->g['OUTPUT_ENCODING']);
//-----------------------
// 共通処理
//-----------------------
session_start();
debug_setComment(0);
header('Content-type: text/html; charset='.$g->g['OUTPUT_ENCODING']);
// テンプレートディレクトリ切替
$smarty->template_dir .= 'admin/';
debug('templates_dir=['.$smarty->template_dir.'] file=['.$g->g['template_file'].']');
$g->g['FOOTER_FILE'] = './footer.inc.tpl';
//getDebugger()->is_display_off = 0;
debug('start_common end');

/**
 * 共通パラメータを付与したURLを取得
 * 外部サイトなら何も付与しない。
 * "http://yahoo.co.jp" = urlWlap('http://yahoo.co.jp');
 * "http://内部サイト/xxx/index.php?userID=xx" urlWlap('http://内部サイト/xxx/index.php');
 * "index.php?a=1&userID=xx" = urlWlap('index.php?a=1');
 */
function urlWlap($url){
  global $g;
  $add['back'] = urlencode(createBackUrl());
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
	if( strpos($useragent, 'KDDI') !== false ){
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
class Common{
	var $PATH;
	var $THIS_PAGE;
	var $g; // global
	var $user;
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
		if( $this->isLogin() ){
			return true;
		}
		redirect('login.php');
	}
	function getUserData(){
		global $db;
		$this->checkLogin();
		$data = $db->select('select * from account where id = :id', array('id'=>$this->getUserId()) );
		return $data[0];
	}
	function isLogin(){
		return $_SESSION['userId'] ? true : false;
	}
	function getSearchUserId(){
		$d = $this->getUserData();
		return $d['search_id'];
	}
	function getUserId(){
		return $_SESSION['userId'];
	}
	function exitError($message){
		echo $message;
		exit;
	}
	function paramExitError($message){
		$this->exitError($message);
	}
	function getNow(){
		return time();
	}
}
function my_mb_output_handler($buffer){
    return $buffer;
}	
// ハンドラ関数を作成する
function my_assert_handler($file, $line, $code) 
{
    debug('Assertion Failed:File '.$file.':Line '.$line.':Code '.$code);
}
function my_error_handler($errno, $str, $file, $line)
{
	if( $errno == E_USER_ERROR){
	}else if($errno == E_WARNING){
	}else if($errno == E_NOTICE){
	}
	debug('['.$file.' line'.$line.':'.$errno.']'.$str);
}

function convertSjisToUtf8($request_arr){
	foreach($request_arr as $k => $v){
		if(is_array($v)){
			convertSjisToUtf8($v);
		}else{
			$request_arr[$k] = mb_convert_encoding($v,"UTF-8","SJIS");
		}
	}
	return $request_arr;
}
<?
//ini_set('session.cache_limiter', 'none');
//error_reporting(E_ALL);
ob_start('my_mb_output_handler');
date_default_timezone_set('Asia/Tokyo');
$g = new Common();
$g->PATH['root'] = dirname(dirname(__FILE__));
$g->PATH['libs'] = $g->PATH['root'].'/lib';
require_once $g->PATH['libs'] . '/settings.inc.php';
require_once $g->PATH['libs'] . '/Debug.class.php';
$g->g['OUTPUT_ENCODING'] = 'UTF-8';
assert_options(ASSERT_CALLBACK, 'my_assert_handler');
set_error_handler("my_error_handler");
require_once $g->PATH['libs'] . '/Lib.inc.php';
require_once $g->PATH['libs'] . '/Db.class.php';
require_once $g->PATH['libs'] . '/appli/common.inc.php';
debug('start common');
//mbconvert($req);
$db = new Db();
$db->dbname = DB_NAME;
$db->user = DB_USER;
$db->password = DB_PASS;
$db->host = DB_HOST;
$db->connect();
//$db->query('set character set sjis');
//$db->query('set names sjis');
//setDebugMode();
//-----------------------
// 共通処理
//-----------------------
debug('start_common end');

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


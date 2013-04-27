<?
class DbMysql {
  var $port = '3306';
  var $connection = null;
  var $db_encoding = 'UTF8';
  function  _query($sql){
     return mysql_query($sql);
     //return mysql_query($sql, $this->connection);
  }
  function _fetch($result){
    return mysql_fetch_assoc($result);
  }
  function num_rows($result){
    return mysql_num_rows($result);
  }
  function _connect($host, $port, $dbname, $user, $password){
    $this->debug("$host, $port, $dbname, $user, $password");
    if( $port ){
      $host .= ':' . $port;
    }
    $con = mysql_connect ($host, $user, $password);
   //or die ("couldn't connetc to MySql".$this->_error());
    $res = mysql_select_db ($dbname,$con);
    //or die ("Couldn't Connect to table'$db_name'".$this->_error());
    if( ! $res ) return false;
    return $con;
  }
  function _close(){
    return @mysql_close();
  }
  function _error(){
    return mysql_error();
  }
  function _fetch_all( $result ){
    $data = array();
    while ($row = mysql_fetch_assoc($result)) {
      $data[] = $row;
    }
    mysql_free_result($result);
    return $data;
  }
  function _escape($s){
    return mysql_escape_string($s);
  }
}
class DbPostgres {
  var $port = '5432';
  var $connection = null;
  function  _query($sql){
     return @pg_query($this->connection, $sql);
  }
  function _fetch($result){
    return pg_fetch_assoc($result);
  }
  function num_rows($result){
    return pg_num_rows($result);
  }
  function _connect($host, $port, $dbname, $user, $password){
    return pg_connect('host='.$host.' port='.$port.' dbname='.$dbname.' user='.$user.' password='.$password);
  }
  function _close(){
    return pg_close($this->connection);
  }
  function _error(){
    return pg_last_error();
  }
  function _fetch_all( $result ){
    return pg_fetch_all( $result );
  }
  function _escape($s){
    return pg_escape_string($s);
  }
}
// アプリケーション固有のDBクラス
class Db extends BaseDb{
function connect(){
  $con = $this->_connect( DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS);
  if (!$con){
    _log('DB CONNECTION ERROR ');
    // select専用のスレーブDBに接続
    $con = $this->_connect( DB_HOST2, DB_PORT2, DB_NAME2, DB_USER2, DB_PASS2);
    if( !$con ){
      _log('DB CONNECTION ERROR(slave)');
      exitError();
	}
    _log('DB CONNECTION SLAVE');
    $this->is_only_select = false;
  }
  $this->connection = $con;
  return $con;
}
/**
 * :NOWをアプリケーション共通の現在時刻で変換
 */
function bind($sql, $h){
  global $g;
  //if( ! $h ) return $sql;
  foreach($h as $k => $v){
    $sql = str_replace(':'.$k, $this->escape($v), $sql);
  }
  $sql = str_replace(':NOW', 'FROM_UNIXTIME('.$g->getNow().')', $sql);
  return $sql;
}
}

/**
 * 汎用DBクラス(アプリケーション固有の関数や変数を使う場合は継承して使用する)
 */
//class Db extends DbPostgres{
class BaseDb extends DbMysql{
var $debug_on = true; // デバッグメッセージ表示用
var $host = "127.0.0.1";
var $dbname = "";
var $user = "";
var $port = "";
var $password = "";
var $debug_func = "debug"; // デバッグ用ファンクション名
//var $db_encoding = 'EUC-JP'; // DBエンコーディング
var $db_encoding = 'UTF-8'; // DBエンコーディング
var $php_encoding = 'UTF-8'; // PHPエンコーディング
var $is_only_select = false; // SELECT以外は発行しない。スレーブ用やテスト用に使用。
function connect(){
  $con = $this->_connect( $this->host, $this->port, $this->dbname, $this->user, $this->password);
  if (!$con){
    //die ("couldn't connet [".$this->error().']');
	return false;
  }
  $this->connection = $con;
  return $con;
}
function isSelect($sql){
  return stripos(trim($sql), 'select') === 0;
}
function query($sql, $h=null) {
  if( $h ) $sql = $this->bind($sql, $h);
  if( $this->is_only_select ){
    // スレーブDBへのクエリーの場合、SELECT以外はスキップ
    if( ! $this->isSelect($sql) ){
	  $this->debug('SKIP: only select mode. ['.$sql.']');
	  return true;
	}
  }
  //$this->connect();
  $result = $this->_query($sql);
  //$this->close();
  $this->debug('sql:'.$sql);
  if( !$result ) {
    $this->debug( get_class($this).'::query error:'.$this->error() );
    trigger_error('DB ERROR['.$sql.']'.$this->error(), E_USER_WARNING);
  }
  return $result;
}
function close(){
  $this->_close();
  $this->connection = null;
}
function debug($m) {
  //debug( $message );
  debug($m);
  if( is_array($m) ){
    $m = print_r($m,1);
  }
  if ( mb_strlen($m) > 255 ) $m = mb_strpos($m, 0, 255);
  $file = dirname(__FILE__).'/../logs/sql.log';
  $m = date('[m/d H:i:s] ') . $m . "\n";
  $fp = fopen($file,'a');
  fputs($fp, $m);
}
function error() {
  return $this->_error();
}
function fetch($result){
  return $this->_fetch($result);
}
function bind($sql, $h=null){
  if( ! $h ) return $sql;
  foreach($h as $k => $v){
    $sql = str_replace(':'.$k, $this->escape($v), $sql);
  }
  return $sql;
}

function count($table, $condition, $condition_h = null) {
  $condition = $this->bind($condition, $condition_h);
  $sql = 'select count(*) as count from ' . $table . ' where ' . $condition;
  $d = $this->select($sql);
  return $d[0][0];
}

function exists($table, $condition, $condition_h = null) {
  $condition = $this->bind($condition, $condition_h);
  $sql = 'select * from ' . $table . ' where ' . $condition . ' limit 1';
  return $this->select($sql) ? true : false;
}
function select1($sql, $h = null) {
	$list = $this->select($sql, $h);
	return $list ? $list[0] : null;
}
function select($sql, $h = null) {
  $sql = $this->bind($sql, $h);
  $result = $this->_fetch_all( $this->query($sql, $h) );
  if( !$result ){
    $this->debug('select:false');
  } else {
    $this->debug('select:hit='.count($result).'');
  }
  return $result;
}
/**
 * SELECT結果の際、キーに指定したカラム値を使用する
 * $list = this();
 * 出現した順に格納し、重複するものはスキップする。
 */
function selectOnUniq($uniq_col, $sql, $h = null, $limit = null){
  $sql = $this->bind($sql, $h);
  $result = $this->query($sql);
  $data = array();
  while ($row = $this->_fetch($result) ){
    $key = $row[$uniq_col];
    if( ! isset($data[$key]) ) $data[$key] = $row;
    if( $limit > 0 && count($data) >= $limit ) break;
  }
  return $data;
}
function insert($table, $h) {
  $names = $values = "";
  foreach($h as $k => $v){
    if($names) $names .= ',';
    if($values) $values .= ',';
    $names .= $k;
    $values .= $this->escape($v);
  }
  $sql = 'INSERT INTO '.$table.' ('.$names.') VALUES ('.$values.')';
  $result = $this->query($sql);
  if($result===FALSE){
    debug($this->error(),'Db::delete error');
  }
  return $result;
}
function update($table, $h, $condition, $condition_h=null) {
  _log($condition);
  _log($condition_h);
  $condition = $this->bind($condition, $condition_h);
  $names = "";
  foreach($h as $k => $v){
    if($names) $names .= ',';
    if( $k == 'key' || $k == 'date' ) $k = '`'.$k.'`';
    $names .= "$k=" . $this->escape($v);
  }
  $sql = 'UPDATE '.$table.' SET '.$names.' WHERE ' . $condition;
  $result = $this->query($sql);
  return $result;
}
function delete($table, $condition, $condition_h=null){
  $condition = $this->bind($condition, $condition_h);
  $sql = 'DELETE FROM ' . $table . ' WHERE ' . $condition;
  $result = $this->query($sql);
  if($result===FALSE){
    debug($this->error(),'Db::delete error');
  }
  return $result;
}
/**
 * SQL用サニタイジングを行う
 * 自動的に'で囲う。'で囲いたくない場合は$auto_quote=falseを指定する。
 *@param string v 対象文字列
 *@param boolean quto_string 自動的にシングルコーテーションで囲む
 *@return string エスケープした文字列
 */
function escape($v, $auto_quote = true){
  if( strtolower($v) == 'now()' || $v === 0 || preg_match('/^[1-9][0-9]*$/',$v) ){
    return $v;
  }else if($v===null || strtolower($v)=='null'){
    return 'null';
  }
  if( $auto_quote ){
    return "'".$this->_escape($v)."'";
  }else{
    return $this->_escape($v);
  }
}
/**
 * SQL用サニタイジングを行う('を囲わない指定をしてescape関数を実行)
 */
function simple_escape($v){
  $auto_quote = false;
  return $this->escape($v,$auto_quote);
}
function convertFromDb($s){
  return $s;//mb_convert_encoding($s, $this->php_encoding, $this->db_encoding);
}
function convertToDb($s){
  return $s;//mb_convert_encoding($s, $this->db_encoding, $this->php_encoding);
}
/**
 * 'YYYY-MM-DD HH:II:SS'の文字列をunixタイム値に変換
 *@param string タイムスタンプ文字列
 *@return int UNIXタイム値
 */
function dateToUnixtime($date){
  if( ! $date || strpos($date,'0000')===0 ) return false;
  $year = substr($date,0,4);
  $month = substr($date,5,2);
  $day = substr($date,8,2);
  if( ! $day ) $day = 1;
  $hour = substr($date,11,2);
  $minute = substr($date,14,2);
  $second = substr($date,17,2);
  return mktime($hour, $minute, $second, $month, $day, $year);
}
}


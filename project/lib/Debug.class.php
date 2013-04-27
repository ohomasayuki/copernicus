<?
/**
(使用例)
define('DEBUG', 1); // デバッグ有効フラグ 1 or 0
require_once this;
debug_setDisplay(true); // 標準出力フラグ 1 or 0
debug_setComment(true); // HTMLコメントフラグ 1 or 0
debug("this is test");
-----------------------------
標準出力に "<!-- this is test -->"
../logs/debug.log に"[月/日 20:13:08] this is test"のように出力される。

*/
//debug_setComment(false);
/**
 * デバッグ関数
 * @param mix デバッグ変数
 * @param string 補足
 * 例)
 * this("test"); // testとデバッグ出力
 * $a = array('id'=>123, 'hoge'=>'fuga');
 * this($v, "abc"); // abc:id=>123,hoge=>fuga
 */
function debug($m, $name = "", $logfile=""){
  $d = getDebugger();
  $d->debug($m, $name, $logfile);
}
function debug_setLog($flag){
  $d = getDebugger();
  $d->is_log = $flag;
}
function debug_setComment($flag){
  $d = getDebugger();
  $d->is_comment = $flag;
}
function debug_setDisplay($flag=true){
  $d = getDebugger();
  $d->is_display = $flag;
  //$GLOBALS['g_debug']->is_display_off = $flag;
}
function getDebugger(){
  global $g_debug;
  if( ! $g_debug ){
    $g_debug = new Debugger();
  }
  return $g_debug;
}
class Debugger {
  var $is_comment = false;
  var $is_display = 0;
  var $is_log = true;
  /**
   * デバッグ関数。指定文字列を設定によりログに記述したり画面に出力したりする。
   *@param m デバッグ出力する文字列
   *@param name デバッグに追加出力する文字列
   *@param logfile ログファイルを指定するときに使用。
   */
  function debug($m, $name="", $logfile=""){
    global $g;
    if( ! DEBUG ) return;
    if( is_array($m) ){
      $m = print_r($m, true);
    }
    if( $this->is_log ){
      $file = dirname(__FILE__).'/../logs/debug'.$logfile.'.log';
      $m = date('[m/d H:i:s] ') . $name . ':' . $m . "\n"; 
      $fp = fopen($file,'a');
      fputs($fp, $m);
    }
    //if( isset($g) && ! $g->isPc() ) return;
    if( ! $this->is_display ){
      if( $_SERVER ){
	    if( $this->is_comment ){
	      echo '<!--'.$name.':'.$m."-->\n";
	    }else{
	      echo '<div style="font-size:small;color:green">[debug:'.$name.']'.$m."</div>\n";
	    }
      }else{
        echo '[debug:'.$name.']'.$m."\n";
      }
    }
  }
}
function _log($m){
  if( is_array($m) ){
    $m = print_r($m,1);
  }
  $file = dirname(__FILE__).'/../logs/log.log';
  $m = date('[m/d H:i:s] ') . $m . "\n";
  $fp = fopen($file,'a');
  fputs($fp, $m);
}

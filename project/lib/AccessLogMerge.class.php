<?php
require_once(dirname(__FILE__).'/AccessLogParser.class.php');
/**
２つのアクセスログをマージする
ex) $ this access_log access_log2 > access_log_merged.txt
1 時刻A,時刻B取得 時刻Aの方が未来とする
2 時刻Aまで、ファイルB出力、超えた時刻を時刻Bとする
3 時刻Bまで、ファイルA出力、超えた時刻を時刻Aとする
4 2に戻る
 */
class AccessLogMerge{
  var $logs;
  var $fps;
  var $parser;
  function setup($accesslogs){
    $this->logs = array();
    $this->fps = array();
    foreach($accesslogs as $v){
      $this->logs[] = $v;
      $this->fps[] = $fp = fopen($v,'r');
      if( ! $fp ){ die('error can not open ['.$v.']');  }
    }
  }
  function parseLine($line){
    if( ! $this->parser ) $parser = new AccessLogParser();
    return $this->parser->parseLine($line);
  }
  function execute(){
    $this->debug("in ".__FUNCTION__);
    $this->parser = new AccessLogParser();
    $lines[0] = fgets($this->fps[0]);
    $lines[1] = fgets($this->fps[1]);

    $d = $this->parseLine($lines[0]);
    $times[0] = $d['date'];
    $d = $this->parseLine($lines[1]);
    $times[1] = $d['date'];

    //大きい方をセット
    if( $times[0] > $times[1] ){
      $fp = $this->fps[0];
      $time = $times[0];
      $line = $lines[0];
      rewind($this->fps[1]);
     
    }else{
      $fp = $this->fps[1];
      $time = $times[1];
      $line = $lines[1];
      rewind($this->fps[0]);
    }
    
    $this->debug("大きい時刻を{$time}にセット。行0:時刻=".date('Y/m/d H:i:s',$times[0]).",行=[{$lines[0]}] 行1:時刻=".date('Y/m/d H:i:s',$times[0]).",行=[{$lines[0]}]");
    while(1) {

      // 時刻が小さい方にスイッチ
      $fp = ($fp == $this->fps[1]) ? $this->fps[0] : $this->fps[1];

      $line = $this->next($line, $fp);
      if( ! $line ) {
        $fp = ($fp == $this->fps[1]) ? $this->fps[0] : $this->fps[1];
        
        $this->debug("最後まで到達。もう１つのファイルを最後まで出力して終了");
        while(!feof($fp)){
          $l = fgets($fp);
          echo $l;//."\n";
        }
        break;
      }
    }
    $this->debug("終了");
  }
  function debug($message){echo "";}//$message."\n";}

  /**
   * 時刻1まで,ファイル2出力,超えた時刻を返却
   */
  function next($line, $fp){
    $d = $this->parseLine($line);
    $time = $d['date'];
    $this->debug("時刻[".date('Y/m/d H:i:s',$time)."]まで出力");
    while(!feof($fp)) {
      $l = fgets($fp);
      $d = $this->parseLine($l);
      if( ! $d ){
        $this->error("failed parse [$l]");
      }
      
      if( $time && $d['date'] > $time){
        $this->debug("超えた行が出現 行:時刻=".date('Y/m/d H:i:s',$time).",行=[{$l}]");
        echo $line;//."\n"; // 超えた行が現れたので出力
        return $l;//超えた行
      }
      echo $l;//."\n";
    }
    
    echo $line;// . "\n";
    return false;// ファイルの最後に到達
  }
  function error($message){
    trigger_error($message, E_USER_ERROR);
  }
} 


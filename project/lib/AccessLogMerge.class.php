<?php
require_once(dirname(__FILE__).'/AccessLogParser.class.php');
/**
���ĤΥ�����������ޡ�������
ex) $ this access_log access_log2 > access_log_merged.txt
1 ����A,����B���� ����A������̤��Ȥ���
2 ����A�ޤǡ��ե�����B���ϡ�Ķ������������B�Ȥ���
3 ����B�ޤǡ��ե�����A���ϡ�Ķ������������A�Ȥ���
4 2�����
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

    //�礭�����򥻥å�
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
    
    $this->debug("�礭�������{$time}�˥��åȡ���0:����=".date('Y/m/d H:i:s',$times[0]).",��=[{$lines[0]}] ��1:����=".date('Y/m/d H:i:s',$times[0]).",��=[{$lines[0]}]");
    while(1) {

      // ���郎���������˥����å�
      $fp = ($fp == $this->fps[1]) ? $this->fps[0] : $this->fps[1];

      $line = $this->next($line, $fp);
      if( ! $line ) {
        $fp = ($fp == $this->fps[1]) ? $this->fps[0] : $this->fps[1];
        
        $this->debug("�Ǹ�ޤ���ã���⤦���ĤΥե������Ǹ�ޤǽ��Ϥ��ƽ�λ");
        while(!feof($fp)){
          $l = fgets($fp);
          echo $l;//."\n";
        }
        break;
      }
    }
    $this->debug("��λ");
  }
  function debug($message){echo "";}//$message."\n";}

  /**
   * ����1�ޤ�,�ե�����2����,Ķ����������ֵ�
   */
  function next($line, $fp){
    $d = $this->parseLine($line);
    $time = $d['date'];
    $this->debug("����[".date('Y/m/d H:i:s',$time)."]�ޤǽ���");
    while(!feof($fp)) {
      $l = fgets($fp);
      $d = $this->parseLine($l);
      if( ! $d ){
        $this->error("failed parse [$l]");
      }
      
      if( $time && $d['date'] > $time){
        $this->debug("Ķ�����Ԥ��и� ��:����=".date('Y/m/d H:i:s',$time).",��=[{$l}]");
        echo $line;//."\n"; // Ķ�����Ԥ����줿�Τǽ���
        return $l;//Ķ������
      }
      echo $l;//."\n";
    }
    
    echo $line;// . "\n";
    return false;// �ե�����κǸ����ã
  }
  function error($message){
    trigger_error($message, E_USER_ERROR);
  }
} 


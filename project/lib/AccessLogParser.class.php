<?php
/**
 * �������������ϥ��饹
 */
class AccessLogParser{
  var $fp;
  //var $mode = "basic"; // basic�ޤ���custom�Τ��б��������������Υե����ޥåȤ����
  var $mode = "custom";
  /** �ɹ���λ�ե饰 */
  var $end_flag = false;
  /** �����оݴ��֤γ�������(unix time)  */
  var $time_from;
  /** �����оݴ��֤ν�λ����(unix time)  */
  var $time_to;
  /** �����������η���б�ɽ  */
  var $month_list = array('Jan'=>1,'Feb'=>2,'Mar'=>3,'Apr'=>4,'May'=>5,'Jun'=>6,'Jul'=>7,'Aug'=>8,'Sep'=>9,'Oct'=>10,'Nov'=>11,'Dec'=>12);
  //var $log; // �����饹
  // ����
  function error($s){
    echo $s;
    trigger_error($s, E_USER_WARNING); // warning��٥��PHP���顼
  }
  function debug($s){
    echo "";//[debug]".$s."\n";
    //$this->log->info($s);
  }
  /**
   * ���פ����оݴ��֤򥻥å�
   *@param int time_from ���׳���������unixtime ���ꤷ�ʤ�����false�ޤ���0
   *@param int time_from ���׽�λ������unixtime ���ꤷ�ʤ�����false�ޤ���0
   */
  function setTime($time_from, $time_to){
    $this->time_from = $time_from;
    $this->time_to   = $time_to;
    $this->debug('����:'. date('Y-m-d H:i:s',$this->time_from) .' - '. date('Y-m-d H:i:s',$this->time_to));
  }
  function open($file){
    $this->fp = fopen($file,'r');
    if( ! $this->fp ){
      $s = "can not open access_log [".$file."]";
      $this->error($s);
      //exit;
    }
    return $this->fp;
  }
  function hasNext(){
    if( $this->end_flag ){
      $this->debug('end_flag�����äƤ��뤿�Ὢλ');
      return false;
    }
    return feof($this->fp) ? false : true;
  }
  function next(){
    $l = fgets($this->fp);
    if( ! $l ) return false;
    $d = $this->parseLine($l);
    return $d;
  }
  function close(){
    return fclose($this->fp);
  }
  function parseLine($line){
    if( $this->mode == 'basic' ){
      $d = $this->parseLine4basic($line);
    }else if( $this->mode == 'custom' ){
      $d = $this->parseLine4custom($line);
    }
    $d['date'] = $this->date2unixtime($d['rawdate']);
    // �оݴ��ֻ�������å�
    if( $this->time_from && $this->time_from > $d['date'] ){
      $this->debug("[".date('Y/m/d',$d['date'])."]�Ͻ����ϰ�[".date('Y/m/d',$this->time_from)."]�������ʤ����᥹���å�");
      return false;
    }
    if( $this->time_to   && $this->time_to   < $d['date'] ){
      $this->end_flag = 1;
      $this->debug("[".date('Y/m/d',$d['date'])."]�Ͻ����ϰ�[".date('Y/m/d',$this->time_to)."]��Ķ���Ƥ��뤿�Ὢλ�ե饰��Ω�Ƥޤ���");
      return false;
    }
    $d['urldata'] = $this->parseUrl($d['url']);
    if( $d['referer'] ){
      $d['refererdata'] = $this->parseUrl($d['referer']);
    }
    return $d;
  }
  function parseLine4basic($line){
    $basic = '^([0-9\.]+) [^\[]+\[([^ ]+) [^"]+"([A-Z]+) ([^ ]+) [^"]+" ([0-9]+)';
    if( ! preg_match('#'.$basic.'#', $line, $m) ){
      $this->error('not match. reg=['.$basic.'] line=['.$line.']');
      return false;
    }
    list($d['line'],$d['ip'],$d['rawdate'],$d['method'],$d['url'],$d['status']) = $m;
    return $d;
  }
  function parseLine4custom($line){
    $basic = '^([0-9\.]+) [^\[]+\[([^ ]+) [^"]+"([A-Z]+) ([^ ]+) [^"]+" ([0-9]+)';
    $custom = $basic . '.* ([^ ]+) "([^"]+)" "([^"]+)"$';
    if( ! preg_match('#'.$custom.'#', $line, $m) ){
      $this->error('not match. reg=['.$custom.'] line=['.$line.']');
      return false;
    }
    list($d['line'],$d['ip'],$d['rawdate'],$d['method'],$d['url'],$d['status'],
         $d['size'],$d['referer'],$d['user_agent']) = $m;
    return $d;
  }
  /**
   * URL��ʬ��
   *@param string url URL
   *@return array url_item array()
   * ex)
   * $url = 'http://username:password@hostname/abc/def.php?arg=value&arg2=value2#anchor';
   * ��url
    [scheme] => http
    [host] => hostname
    [user] => username
    [pass] => password
    [path] => /abc/def.php
    [query] => arg=value&arg2=value2
    [fragment] => anchor
    [param] => Array ( [arg] => value, [arg2] => value2 )
    [dirname] => /abc
    [basename] => def.php
    [extension] => php
   */
  function parseUrl($url){
    $urldata = parse_url($url);
    $pathinfo = pathinfo($urldata['path']);
    $urldata['extension'] = trim(strtolower($pathinfo['extension']));
    $urldata['dirname']   = trim($pathinfo['dirname']);
    $urldata['basename']  = trim($pathinfo['basename']);
    $query = $urldata['query'];
    $params = explode( '&', $query);
    foreach( $params as $keqv ){
      list($key, $val) = explode('=', $keqv);
      $urldata['param'][$key] = $val;
    }
    return $urldata;
  }
  /**
   * ����������������ʸ�����UNIX��������Ѵ�
   *@param string date ��������������ʸ����
   *@return int unixtime
   * ex) unix������ = this("19/Jul/2007:16:51:29");
   */
  function date2unixtime($date){
    //19/Jul/2007:16:51:29
    $text = "#([0-9]+)/([a-zA-Z]+)/([0-9]+):([0-9]+):([0-9]+):([0-9]+)#";
    if( ! preg_match($text, $date, $m) ){
      $this->error('fault '.__FUNCTION__.' date='.$date.' reg='.$text);
      return false;
    }
    $d = array();
    $year = $m[3];
    $mon  = $this->month_list[$m[2]]; // ���3ʸ��������ͤ��Ѵ�
    $day  = $m[1];
    $hour = $m[4];
    $min  = $m[5];
    $sec  = $m[6];
    $time = mktime($hour, $min, $sec, $mon, $day, $year);
    #$this->debug('�����Ѵ����:'.date('Y-m-d H:i:s', $time));
    return $time;
  }
}


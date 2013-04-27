<?
class RunLog {
  var $file = 'log/run_log.csv';
  var $tab  = ",";
  var $end  = "\r\n";
  var $date_format;
  var $application = 'APPLICATION';
  var $id = 0;
  var $program = 'program';
  var $error_num = 0;
  var $insert_num = 0;
  var $update_num = 0;
  function log($s,$status){
    $h=array();
    $h[] = date('Y/m/d');
    $h[] = date('H:i:s');
    $h[] = $this->application;
    $h[] = $status;
    $h[] = $this->program;
    $h[] = $this->id++;
    $h[] = $s;
    $this->_log( join( $this->tab ,$h ) );
  }
  function start(){
    $this->info('����');
  }
  function finish(){
    $this->info('��Ͽ:'.$this->insert_num.'��');
    $this->info('����:'.$this->update_num.'��');
    $err = '���顼:'.$this->error_num.'��';
    if( $this->error_num ){
      $this->error($err);
    }else{
      $this->info($err);
    }
    $this->info('��λ');
  }
  function info($s){
    $this->log($s,'INFOMATION');
  }
  function error($s){
    $this->log($s,'ERROR');
  }
  function warning($s){
    $this->log($s,'WARNING');
  }
  function _log($s){
    debug($s);
    $fp = fopen($this->file, 'a');
    fputs($fp, $s . $this->end);
    fclose($fp);
  }
  /**
   * �۾ｪλ����
   */
  function errorExit(){
    $this->error('�۾ｪλ');
    exit(1);
  }
}

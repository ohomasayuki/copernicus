<?
/**
 Csv.class.php
ex)
Csv::test_Write('test.csv');
Csv::test_Read('test.csv');
*/
class Csv {
  var $file = '../test/customer.csv';
  var $fp;
  var $log;
  var $tab = ",";
  var $line=0;
  var $end = "\r\n";
  var $is_double_quote = true; // 項目を"で囲う形式か 
  var $encode_out = 'UTF-8';
  var $encode_csv  = 'SJIS';
  function fputs($h){
    if( $this->is_double_quote ){
      foreach( $h as $k => $v ){
        $h[$k] = str_replace('"','""',$v);
      }
      $l = '"' . join('"' . $this->tab . '"', $h) . '"' . $this->end;
    }else{
      $l = join($this->tab, $h) . $this->end;
    }
    //debug($l);
    $l = mb_convert_encoding($l, $this->encode_csv, $this->encode_out);
    fputs($this->fp, $l);
  }
  function fgets(){
    $l = fgets($this->fp);
    $this->line++;
    if( ! trim($l) ){
      //$this->log->warning('line'.$this->line.' is null');
      return false;
    }
    $l = mb_convert_encoding($l, $this->encode_out, $this->encode_csv);
    $l = str_replace("\r","",str_replace("\n","",$l));	    
    if( $this->is_double_quote ){ 
      // 項目を"で囲っているとき
      $l = substr( $l, 1, strlen($l)-2 ); // 両端の"を削る
      $ls = explode( '"' . $this->tab . '"', $l );
      foreach( $ls as $k => $v ){
        $ls[$k] = str_replace('""','"', $v);
      }
    }else{
      $ls = explode($this->tab, $l);
    }
    //debug($ls);
    return $ls;
  }
  function fopen($param='r'){
    $this->fp = fopen( $this->file, $param );
    if( ! $this->fp && isset($this->log) ){
      $this->log->error($this->file . ' のオープンに失敗しました');
      $this->log->error_num++;
      $this->log->errorExit();
    }
    return $this->fp;
  }
  function fclose(){
    fclose($this->fp);
  }
  function feof(){
    return feof( $this->fp );
  }
  //---------------------------------
  // TEST CODE
  //---------------------------------
  function test_Read($filename){
    $csv = new Csv();
    $csv->encode = 'SJIS';
    $csv->fp = fopen($filename,'r');
    $csv->tab = ",";
    while(!$csv->feof()){
      $l = $csv->fgets();
      print_r($l);
    }
    $csv->fclose();
  }
  function test_Download(){
    $csv = new Csv();
    $csv->encode_out = 'SJIS';
    $csv->fp = fopen('php://output','w');
    $list = $db->select('select * from menber order by id');
    header('Content-type: application/x-csv');
    header("Content-Disposition: attachment;filename=member.csv");
    ob_clean();
    $csv->fputs(array('id','e-mail','nickname','point'));
    foreach($list as $k => $v){
      $csv->fputs(array($v['id'],$v['mail'],$v['nickname'],$v['point']));
    }
  }
  function test_Write($filename){
    $csv = new Csv();
    $csv->encode_out = 'SJIS';
    $csv->tab = ",";
    $csv->fp = fopen($filename,'a');
    $list = array(
array('id'=>'1','mail'=>'a@aaa.com','nickname'=>'nickname1','point'=>'100'),
array('id'=>'2','mail'=>'b@aaa.com','nickname'=>'nickname2','point'=>'200'),
array('id'=>'3','mail'=>'c@aaa.com','nickname'=>'nickname3','point'=>'300'),
    );
    $csv->fputs(array('id','e-mail','nickname','point'));
    foreach($list as $k => $v){
      $csv->fputs(array($v['id'],$v['mail'],$v['nickname'],$v['point']));
    }
  }
}


<?php
/**
 * 受信メール解析クラス
 * メール受信時にメール内容を解析する
 * メディアファイルを取得
 * 対応：通常メール、画像添付、動画添付、着うた添付、デコメール
 * 添付ファイルは１ファイル目のみ対応
 * 例)
$o = new Karamail();
$d = $o->parse();
$d['to']; // kara@webservice.jp
$d['from']; // user1@docomo.ne.jp
$d['body']; // 本文
$d['multipart']; // 添付データ情報
// 添付ファイルがあるか
if( $o->hasAttach() ){
  // デコメールか？
  if( $o->isDecomail() ){
  }
  // バイナリデータ取得(例：添付画像データ)
  $binarys = $o->getAttachData();
  
  // 画像添付してるか？
  if( $o->hasImage() ){
  }
  // 画像添付してるか？

if( ! file_put_contents($file_path, $image) ){
                batchExitError(__FILE__.' error can not open [' . $file_path.']');
        }
        // ファイルの権限変更
        chmod($file_path, 0777);

  // 画像添付してるか？

}
 * 
 */
class Karamail {

/** 生メールデータ(１行ごとに配列にしたもの)  */
var $row_mail;
var $headers;
var $body;
/** 添付データ */
var $attach_binaries;
/** */
var $from; // from のメールアドレス部
var $to; // toのメールアドレス部

/**
 * 添付データ取得
 */
function getAttachData(){
  return $this->attach_binaries;
}

/**
 *  標準入力取得(パイプなど用)
 *  @return array 受信メールデータ
 */
function readStdin(){
  $fp = fopen('php://stdin', "r");
  $mail = array();
  while (!feof($fp)){
    $line = fgets($fp);
    $mail[] = $line;
  }
  fclose($fp);
  debug("mail=".implode("\n",$mail));
  return $mail;
}

/**
 * 受信メールを解析する
 */
function parse(){
  debug("start parse");
  // 標準入力の内容を取得
  $mail = $this->readStdin();
  $flg=0;
  $now_header='';
  $boundary='';
  $i=0;
  $m=0;
  $m_flg=0;
  $body = "";
  $headers = array();
  $multipart = array(); // 添付データ
  // メール内容を解析
  foreach($mail as $k => $v){
    if($v=="\n") {
      $flg=1;
    }
    if($i==0){
      $data['file_head'] = str_replace("\n", "", $v);
    }elseif($flg==0) {
      if(strpos($v,"\t")===0){
	$headers[$now_header] .= $v;
	// 区切り文字の取得
	if( preg_match("/boundary=\"(.+)\"/", $v, $bound) ){
	  $boundary = '--'.$bound[1];
	}
      }else{
	list($now_header, $value) = explode(':', $v, 2);
	$now_header = strtolower($now_header);
	$headers[$now_header] = str_replace("\n", "", $value);
	$headers[$now_header] = str_replace(" ", "", $value);
	// 区切り文字の取得
	if( preg_match("/boundary=\"(.+)\"/", $v, $bound) ){
	  $boundary = '--'.$bound[1];
	}
      }
    }else{
      $body .= $v;
      // マルチデータの取得
      if( $boundary != '' && strpos($v, $boundary) === 0 ){
	$m++;
	$m_flg = 0;
      }
      // データの取得
      if($m_flg == 1){
	$multipart[($m-1)]['data'] .= $v;
      }elseif(0 < $m){
	// コンテンツタイプ取得
	if( preg_match("/Content-Type:(.+);/", $v, $type) ){
	  $multipart[($m-1)]['type'] = trim($type[1]);
	}
	// 拡張子取得 例) [ name="070708_030444.3gp"]
	if( preg_match('/[^a-zA-Z]name="([^.]+)([.a-z0-9]+)"/', $v, $ext) ){
	    $multipart[($m-1)]['ext'] = trim($ext[2]);
	    $multipart[($m-1)]['name'] = trim($ext[1].$ext[2]);
	}
	// コンテンツID
	if( preg_match( '/Content\-ID: *<([^>]+)>/i', $v, $contentid) ){
	    $multipart[($m-1)]['contentid'] = trim($contentid[1]);
	}
	// コンテンツタイプ取得
	if( preg_match("/Content-Transfer-Encoding:(.+)/", $v, $enco) ){
	  $multipart[($m-1)]['encoding'] = trim($enco[1]);
	}
	if($v=="\n") {
	  $m_flg = 1;
	}
      }
    }
    $i++;
  }
  $this->body = $body; // 本文
  $this->headers = $headers; // ヘッダ情報(キーは小文字)
  $this->subject = mb_decode_mimeheader($headers['subject']); // サブジェクト
  $this->data = $data;
  // 添付ファイル解析
  $this->attach_binaries = array();
  for($i=0; $i < count($multipart); $i++){
    $this->attach_binaries[] = base64_decode( str_replace("\n","",$multipart[$i]['data']) );
  }
  // From欄の xxxxx<xx@xxx.xx> のメールアドレス部のみ抽出
  if( preg_match('/.*<([^>]+)>.*/',$headers['from'],$matches) ){
    $this->from = $matches[1];
  }else{
    $this->from = $headers['from'];
  }
}

}

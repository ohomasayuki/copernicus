<?
require_once dirname(__FILE__).'/MemberCode.class.php';
/**
 * メール本文にパラメータを埋め込む＆パースする
ex)
$o = new MailTag();
$mk = new MemberCode();
$h = array(); // メールに埋め込むパラメータ
$h['code'] = $mk->getOrCreateCode(); // 端末固有IDのキーを生成または取得
$mail_param = $o->createParam($h);
$mail = array();
$mail['subject'] = 'メルマガ登録'; // 題名
$mail['body'] = $mail_param.'このまま送信して下さい。'; // 本文
$mail['to'] = 'mag@'.$_SERVER['SERVER_NAME']; // 宛先(空メール受信アドレスを指定)
$mail['submit'] = 'ﾒｰﾙ送信'; // ボタン表示名
$mail_tag = $o->getMailTag($mail['to'],$mail['subject'],$mail['body'],$mail['submit']);
$smarty->assign('mail_tag',$mail_tag);
--- メール受信プログラム
$mp = new MailParam();
$param = $mp->parseParam($body); // body:本文
$param['code']; // mysessionid1
*param['secret']; // 1234
 */
class MailTag {
var $tag_s = '{';
var $tag_e = '}';
function createParam($hash){
  $p=$this->tag_s;
  foreach($hash as $k => $v){
    if(strlen($p) > strlen($this->tag_s)) $p .= '&';
    $p .= $k.'='.urlencode($v);
  }
  $p .= $this->tag_e;
  return $p;
}
function parseParam($body){
  $s = strpos($body,$this->tag_s);
  $e = strpos($body,$this->tag_e);
  if( $s === false || $e === false ){
    return false;
  }
  $query = substr($body, $s + strlen($this->tag_s), $e - $s - strlen($this->tag_e) );
  $params = explode('&', $query);
  $param = array();
  foreach($params as $v){
    list($key,$value) = explode('=', $v);
    $param[$key] = $value;
  }
  return $param;
}
/**
 * メール送信用タグ
ex)
$o = new This();
$mk = new MemberCode();
$h = array(); // メールに埋め込むパラメータ
$h['code'] = $mk->getOrCreateCode(); // 端末固有IDのキーを生成または取得
$mail_param = $o->createParam($h);
$mail = array();
$mail['subject'] = 'メルマガ登録'; // 題名
$mail['body'] = $param.'このまま送信して下さい。'; // 本文
$mail['to'] = 'mag@exsample.com'; // 宛先(空メール受信アドレスを指定)
$mail['submit'] = 'ﾒｰﾙ送信'; // ボタン表示名
$tag = $o->this($mail['to'],$mail['subject'],$mail['body'],$mail['submit']);
 */
function getMailTag($to,$subject,$body,$submit){
  $tag = '<form method="get" action="mailto:'.$to.'">
<input type="hidden" name="subject" value="'.$subject.'">
<input type="hidden" name="body" value="'.$body.'">
<input type="submit" value="'.$submit.'">
</form>';
  return $tag;
}
}

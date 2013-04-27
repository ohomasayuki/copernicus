<?
/**
 * リダイレクト用ラッパー
 * リダイレクトするときは必ずこの関数を通してください
 * リダイレクトしてexitします。
 *@param string url リンク先URL
 */
function redirect($url){
  $out = false; // 外部リンクならtrue, 内部リンクならfalse
  if( strpos($url,'http') === 0 ){
    if( strpos($_SERVER['SERVER_NAME'],$url) !== false ){
      $out = true;
    }else{
      $out = false;
    }
  }else{
    $out = false;
  }
  if( ! $out ){
    $url = urlWlap($url);
  }
  debug($url,'redirect');
  header('Location: ' . $url);
  exit;
}
/**
 * 現在アクセス中のファイルを取得
 */
function getThisPage(){
	$file = $_SERVER['REQUEST_URI'];
	if( strpos($file, '?') ){
		$file = substr($file, 0, strpos($file, '?'));
	}
	if( strpos($file, '.php') === false ){
	}
	return $file;
}
function setTemplate($name){
	global $g;
	$g->g['template_file'] = $name . '.tpl';
}
function getStaticTemplateFile(){
	$file = $_SERVER['REQUEST_URI'];
	$root_uri = ROOT_URI;
	if( strpos($_SERVER['REQUEST_URI'], '?') ){
		$file = substr($file, 0, strpos($_SERVER['REQUEST_URI'], '?'));
	}
	if( strpos($file, '.php') === false ){
		$file .= '/index.php';
	}
	$file = '.' . str_replace('.php', '.tpl', $file);
	$file = str_replace($root_uri, '',$file);
	return $file;
}
function createHidden($h){
	$tag = "";
	foreach($h as $k => $v){
		$tag .= '<input type="hidden" name="'.$k.'" value="'.htmlspecialchars($v).'" />';
	}
	return $tag;
}
function getMustParam($name){
	global $req;
	if( ! isset($req[$name]) ){
		exitError('パラメータがただしくありません。');
	}
	return $req[$name];
}
function getDefParam($name, $def_value){
	global $req;
	if( ! isset($req[$name]) || $req[$name] == ''){
		return $def_value;
	}
	return $req[$name];
}
/**
 * 最後に呼ぶ関数
 * pc_end.inc.phpをインクルードして処理を終了する
 */
function finish(){
	require_once dirname(__FILE__)."/end.inc.php";
}
/**
 * "a.php?id=3" = this("a.php", array("id"=>3) )
 * "a.php?a=1&b=2&c=3" = this("a.php?a=1&b=4&c=3", array("b"=>2) )
 */
function addParam( $url, $arr ){
	if( ! $arr ){
		 return $url;
	}
	$param = getUrlParam($url);
	$arr = array_merge( $param, $arr);
	$pos = strpos($url, '?');
	if( $pos !== false ){
		$url = substr($url, 0, $pos);
	}
	foreach($arr as $k => $v){
		$url .= strstr( $url, '?' ) !== false ? '&' : '?';
		$url .= $k . '=' . $v;
	}
	//debug($url.','.pr($arr).'=>'.$url);
	return $url;
}
/**
 * URLからリクエストをパース
 *@return array キー=値のハッシュ
 * ex)
 * array('a'=>1,'b'=>'xyz') = this('/test/test.php?a=1&b=xyz')
 * array('a'=>1,'b'=>'xyz') = this('http://exsample.com/test/test.php?a=1&b=xyz')
 * array('a'=>1,'b'=>'xyz') = this('a=1&b=xyz')
 */
function getUrlParam($url){
	$pos = strpos($url, '?');
	if( $pos === false ){
		return array();
	}
	$query = substr($url, $pos + 1);
	$params = explode( '&', $query);
	$ret = array();
	foreach( $params as $keqv ){
		list($key, $val) = explode('=', $keqv);
		$ret[$key] = $val;
	}
	return $ret;
}

/**
 * セッションdebug_modeがセットされていれば、デバッグモードにする
 */
function setDebugMode(){
	if( ! isDebugMode() ){
		return;
	}
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');
}
/**
 * デバッグモード判定
 */
function isDebugMode(){
	return $_SESSION['debug_mode'];
}
function a(){ return array(); }
function pr($s,$flag=true){
	return print_r($s,$flag);
}
function startsWith($s, $start){
	return strpos($s, $start) === 0;
}
function endsWith($s, $end){
	return strrpos($s, $end) === strlen($s) - strlen($end);
}
function isAbsUrl($url){
	return preg_match('#^https?://#', $url);
}
function myassert($url){
	assert($url != false);
}
/**
 * 相対URLを絶対URLに変換
 * 例) 'http://xxx.xx/aaa/bbb.html' = this('http://xxx.xx/aaa/zzz/a.html', '../bbb.html')
 */
function getAbsUrl($base, $href){
	if( isAbsUrl($href) ) return $href;
	$url_info = parse_url($base);
	$bdir = (substr($url_info['path'], -1) == '/') ? $url_info['path'] : dirname($url_info['path']).'/';
	if(ereg('^https?://', $href)){
		$path = $href;
	}elseif(substr($href, 0, 1) == '/'){
		$path = $url_info['scheme'].'://'.$url_info['host'].$href;
	}else{
		$path = $url_info['scheme'].'://'.$url_info['host'].$bdir.$href;
	}
	$path = ereg_replace("/\./", "/", $path);
	while( ereg("\.\./", $path) ){
		$url_info = parse_url($path);
		$paths = preg_split("/\//", $url_info['path']);
		for( $cnt=1 ; $cnt<count($paths) ; $cnt++ ){
			if( $paths[$cnt] == ".." ){
				$paths[$cnt-1] = "";
				$paths[$cnt] = "";
			}
		}
		$str = "";
		foreach( $paths as $value ){
			if( strlen($value) != 0 ){
				$str .= "/".$value;
			}
		}
		$path = $url_info['scheme'].'://'.$url_info['host'].$str;
	}
	$url_info = parse_url($path);
	$path = $url_info['scheme'].'://'.$url_info['host'].str_replace('//','/',$url_info['path']);
	return $path;
}

/**
 * マルチバイトを含む文字列を指定バイト毎に区切り文字を入れる
   ex) 123あ<br>いうえ<br>お567<br>かきく<br> = this('123あいうえお567かきく',array('length'=>5,'break'=>'<br>'));
   ex) あいうえ…1 = this('あいうえお',array('length'=>7,'cut'=>true));
 *@param $string 対象文字列
 *@param $h ハッシュ
   length     区切る桁数(バイト数、デフォルト80桁)
   break      区切り文字(デフォルト："<br />")
   escape  htmlタグの無効化(true:実行、false:非実行、デフォルト：true)
   cut=false  区切らず切り捨てる(true:実行、false:非実行、デフォルト：false)
   cut_string 切り詰め時の文字列(デフォルト:…)
 *@return 結果文字列
 */
function _mb_filter($string, $h){
    //$length, $break, $htmlspecialchars, $cut, $break_flg, $nl2br_flg, $cut_string = '…'
    if( ! isset($h['cut_string']) ) $h['cut_string'] = '…';
    if( ! isset($h['break']) ) $h['break'] = '<br />';
    if( ! isset($h['length']) ) $h['length'] = 80;
    if( ! isset($h['escape']) ) $h['escape'] = true;

    // mb_strwidthで1byteと判断される文字
    $twobyte_string = array(
        '◯' => true,
        '●' => true,
    );
    $ret = '';
    $col = 0;
    $line = array();
    for( $i = 0; $i < mb_strlen($string); $i++ ) {
        $s = mb_substr( $string, $i, 1 );
        if( mb_strwidth($s) == 1 and isset($twobyte_string[$s]) == false) {
            $col++;//1列追加
            if( preg_match('/^(<%EMOJI::[0-9]+%>)/', mb_substr( $string, $i ), $matches )) {
                $emoji_len = mb_strlen( $matches[1] );
                $col++;//1列追加
                $i = $i + $emoji_len-1;
                $s = $matches[1];
            } elseif( mb_strwidth($s) == 1 and ($s == "\r" or $s == "\n")) { // 改行が来たら区切り文字を挟みクリア
                if( $h['cut'] ) {
                    $ret .= $h['cut_string'];
                    $line[] = $ret;
                    $ret = '';
                    break;
                }
                $s2 = mb_substr( $string, $i+1, 1 );
                // Sift_JISの改行は'\r\n'(UTF8は'n')
                if( mb_strwidth($s2) == 1 and $s == "\r" and $s2 == "\n" ) {
                    $i++;
                }
                $col = 0;
                $line[] = $ret;
                $ret = '';
            }
        }else{
            $col+=2; // マルチバイト文字なら2列追加
        }
        $ret .= $s;
        if( $col >= $h['length'] ) { // 指定列数に達したら区切り文字を挟む
            if( $h['cut'] ) {
                $ret .= $h['cut_string'];
                $line[] = $ret;
                $ret = '';
                break;
            }
            $col = 0;
            $line[] = $ret;
            $ret = '';
        }
    }
    if( strlen($ret) > 0 ) {
        $line[] = $ret;
    }
    $ret = '';
    for( $i = 0; $i < count($line); $i++ ) {
        if( $i > 0 ) {
            $ret .= $h['break'];
        }
        if( $h['escape'] ) {
                $ret .= htmlspecialchars($line[$i]);
        } else {
                $ret .= $line[$i];
        }
    }
    return $ret;
}

/**
 * CSVを配列に変換
$data = this('
id	name	detail
no5	名前5  詳細5
no6	名前6  詳細6
',"\t");
);
// array('no5'=>array('id'=>'no5','name'=>'名前5','detail'=>'詳細5'),
//       'no6'=>array('id'=>'no6','name'=>'名前6','detail'=>'詳細6'))
 *@param string csv CSVまたはTSV文字列
 *@param string tab 区切り文字。デフォルト=\t
 *@return array 配列(1行目をキーにしたもの)
 */
function loadFromCsv($csv,$tab="\t"){
  $csv = str_replace("\r","",$csv);
  $lines = explode("\n",$csv);
  $data = array();
  $title = null;
  foreach($lines as $line){
    if( ! trim($line) ) continue;
    $h = explode($tab,$line);
    if( ! $title ){
      $title = $h;
      continue;
    }
    $l = array();
    for($i=0;$i<count($title);$i++){
      $l[$title[$i]] = $h[$i];
    }
    $data[$h[0]] = $l;
  }
  return $data;
}

/**
 * メール送信関数
 *@param string to 送信先メールアドレス
 *@param string subject 件名
 *@param string body 本文
 *@param string from_email 送信元メールアドレス
 *@param string from_name 送信者名
 *@return bool 送信結果
 */
function sendMail($to, $subject, $body, $from_email,$from_name)
{
	$headers  = "MIME-Version: 1.0 \n" ;
	$headers .= "From: " .
	       "".mb_encode_mimeheader (mb_convert_encoding($from_name,"ISO-2022-JP","AUTO")) ."" .
	       "<".$from_email."> \n";
	$headers .= "Reply-To: " .
	       "".mb_encode_mimeheader (mb_convert_encoding($from_name,"ISO-2022-JP","AUTO")) ."" .
	       "<".$from_email."> \n";
	$headers .= "Content-Type: text/plain;charset=ISO-2022-JP \n";

	/* Convert body to same encoding as stated
	in Content-Type header above */
	$body = mb_convert_encoding($body, "ISO-2022-JP","AUTO");
	   
	/* Mail, optional paramiters. */
	$sendmail_params  = "-f$from_email";
	
	mb_language("ja");
	$subject = mb_convert_encoding($subject, "ISO-2022-JP","AUTO");
	$subject = mb_encode_mimeheader($subject);

	$result = mail($to, $subject, $body, $headers, $sendmail_params);
	// 管理用
	@mail('oho@synapsesoft.co.jp', $subject, $body, $headers, $sendmail_params);

	return $result;
}

/**
 * マイクロ秒単位で現在のUNIXタイムを取得
 * "598273.434432" = this()
 *@return float マイクロ秒レベルのunixtime
 */
function microtime_float(){
  list($usec, $sec) = explode(" ", microtime());
  return ((float)$usec + (float)$sec);
}

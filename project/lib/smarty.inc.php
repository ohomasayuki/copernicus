<?
/**
 *      smarty設定ファイル
 **/
// Smartyライブラリを読み込む
require_once $g->PATH['root']."/smarty/Smarty.class.php";
$smarty = new Smarty;
$smarty->force_compile = true;
$smarty->template_dir = $g->PATH['root'] . '/templates/';
$smarty->compile_dir  = $g->PATH['root'] . '/templates_c/';
$smarty->left_delimiter = '[:';
$smarty->right_delimiter = ':]';
require_once dirname(__FILE__).'/smarty_emoji.inc.php';

// カスタム関数のファイルを読み込む
//require_once(dirname(__FILE__)."/smarty_custom.inc.php");

// function
//$smarty->register_function("post_para", "function_post_para");
//$smarty->register_modifier("trim", "trim");
$smarty->register_function("session", "function_session");
$smarty->register_outputfilter("function_outputfilter");
$smarty->register_function("formset", "function_formset");

function function_formset(){
	$str = '<input type="hidden" name="token" value="" />';
	$str .= '<input type="hidden" name="guid" value="on" />';
	return $str;
}

function function_outputfilter($source) {
	global $carrier_id;
	$source = str_replace( array('&lt;%', '%&gt;'), array('<%', '%>'), $source );
	$source = preg_replace_callback ( "/<\%([-#*_|:,=.%a-zA-Z0-9]+)\%>/", "preg_function", $source);
        //$source = preg_replace_callback ( '|\[:([a-zA-Z0-9_]+)():\]/", "preg_function", $source);
        $source = str_replace( array('&lt;_%', '%_&gt;'), array('<%', '%>'), $source );
	if( $carrier_id == 'e') {
		$source = au_emoji_code2bin($source);
	}
	//$source = mb_convert_encoding($source,'SJIS-win','UTF-8');
	return $source;
}

$smarty->register_function("link", "function_link");
$smarty->register_function("img", "function_img");
$smarty->register_function("deco", "function_deco");
$smarty->register_function("form", "function_form");
//$smarty->register_function("emoji", "function_emoji");
function preg_function($para) {
//var_dump($para);
        $function_arr = array(
                'link' => array( 'function' => 'customtag_link' ),
                'EMOJI' => array( 'function' => 'preg_emoji' ),
                'ACCESS_KEY' => array( 'function' => 'preg_access_key' ),
                'INPUT_MODE' => array( 'function' => 'preg_input_mode' ),
  );
        $arr = explode('::', $para[1]);
//var_dump($arr);
        if( isset($function_arr[$arr[0]])) {
                $w_para = explode(',', $arr[1]);
                $return_str = call_user_func ( $function_arr[$arr[0]]['function'], $w_para);
        } else {
                $smarty->trigger_error("[smarty_funtion] Attribute \"$arr[0]\" is incompleted or wrong format.[{$arr[1]}]");
                $return_str = '';
        }
//debug(__FUNCTION__.$return_str);
        return($return_str);
}
function customtag_link($param){
  $url = $param[0];
  return urlWlap($url);
}
/**
 $smarty->assign('url','a.php?b=2&c=3');
 [:form url=$url:]
 <input type="submit" value="購入" />
 </form>
<form action="a.php" method="post">
<input type="hidden" name="b" value="2"/>
<input type="hidden" name="c" value="3"/>
<input type="hidden" name="_otp" value="a832ur8320"/>
<input type="hidden" name="uid" value="NULLGWDOCOMO"/>
<input type="hidden" name="PHPSESSID" value="..."/>
<input type="submit" value="購入" />
</form>
 */
function function_form($param){
	if( ! isset($param['param']) ) $param['param'] = array();
	if( ! isset($param['url']) ) $param['url'] = getThisPage();
	$url = $param['url'];
	$url = urlWlap($url);
	$param = $param['param'];
	$param = array_merge($param,getUrlParam($url));
	if( strpos($url,'?') !== false ) $url = substr($url, 0, strpos($url,'?'));
	$html = '<form action="'.$url.'" method="post">'.createHidden($param);
	return $html;
}
function function_link($param){
	return urlWlap($param['url']);
}
function function_img($param){
	$src = $param['src'];
	if( ! $src ) return "";
	if( strpos($src,'http://') === false ){
		$url = IMG_BASE_URL . $src;
	}else{
		$url = $src;
	}
	if( strpos($src,'.') === false ) $url .= '.jpg';
	$html = '<img src="' . $url . '" />';
	return $html;
}
function function_deco($param){
	global $g;
	$is_xhtml = $g->isXhtml();
	$type = $param['type'];
	if( $type == 'title' ){
		$title = $param['title'];
		if( $is_xhtml ){
			return '<?xml version="1.0" encoding="'. $g->g['OUTPUT_ENCODING'] . '"?>
<html>
<head>
<meta http-equiv="Content-Type" content="application/xhtml+xml; charset='.$g->g['OUTPUT_ENCODING'].'" />
<title>B.L.T. Movie</title>
<style type="text/css">
<![CDATA[
a:link{color: #0000FF}
a:visited{color: #660099}
]]>
</style>
</head>
<body>
<a id="pagetop"></a>
<div style="background-color:#FFF;color:#000;font-size:xx-small;">
<!--====s/MAIN=====-->
<div style="text-align:center;font-size:medium;">
<div style="background-color:#0066CC;color:#FFF;">'.$title.'</div>
			';
		}else{
			return '<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=' . $g->g['OUTPUT_ENCODING'] . '">
			<meta http-equiv="Pragma" content="no-cache" />
			<meta http-equiv="Cache-Control" content="no-cache" />
			<meta http-equiv="Cache-Control" content="max-age=0" />
			<meta http-equiv="Expires" content="0"/>
			<title>' . $title . '</title>
			</head>
			<body link="#FF1493" text="#696969">
			<a name="ptop"></a>
			<font size="-1">
			<center><font color="#FF1493">' . $title . '</font><br /></center>';
		}
	}
	if( $type == 'floatclear' ){
		return '<div style="background-color:#FFFFFF;clear:both"></div>';
	}
	if( $type == 'accesskey' ){
		if( isSoftBank() && ! $g->isXhtml() ){
			// softbank旧機種
			// 3gc機種はaccesskeyにしないとnonumberをつけても番号表示されてしまう
			return 'directkey="'.$param['value'].'" nonumber';
		}
		return 'accesskey="'.$param['value'].'"';
	}
	if( $type == 'line' ){
		return '<div style="text-align:center;"><img src="/up/img/line_dot.gif" width="206" height="3" border="0" style="margin:5px 0;" /></div>';
	}
	if( $type == 'hr' ){
		if( $is_xhtml ){
			return '<div style="text-align:center;"><img src="images/bar_dia.gif" width="240" height="10"></div>';
		}else{
			return '<hr color="#FF1493">';
			return '<center><img src="images/bar_dia.gif" width="240" height="10"></center>';
		}
	}
	if( $type == 'toTop' ){
		return '<a href="/">音女ﾄｯﾌﾟ</a><br />';
	}
	if( $type == 'toMumoTop' ){
		if( $is_xhtml ){
			return '<a href="http://mu-mo.net">うたﾑｭｳﾓﾄｯﾌﾟ</a><br />';
		}else{
			return '';
		}
	}
	if( $type == 'back' ){
		$back = getBackUrl();
		if( $is_xhtml ){
			return '<div style=background-color:#FFFFFF;text-align:left;"><a href="' . urlWlap($back) . '"><span style="font-size:small;color:#FF1493;">もどる</span></a></div>';
		}else{
			return '<a href="' . urlWlap($back) . '">もどる</a><br />';
		}
	}
	if( $type == 'backnumber' ){
		if( ! $g->g['BACKNUMBER_ENABLE'] ) return '';
		if( $is_xhtml ){
			//return '<div style="background-color:#696969;text-align:right"><a href="' . urlWlap('backnumber.php') . '"><span style="font-size:small;color:#FF69B4;">→視聴ﾊﾞｯｸﾅﾝﾊﾞｰ</span></a></div>';
			return '<a href="' . urlWlap('backnumber.php') . '"><span style="font-size:small;color:#FF1493;">視聴ﾊﾞｯｸﾅﾝﾊﾞｰ</span></a><br />';
		}else{
			return '<a href="' . urlWlap('backnumber.php') . '">視聴ﾊﾞｯｸﾅﾝﾊﾞｰ</a><br />';
		}
	}
	if( $type == 'header' ){
		$title = $param['title'];
		return '</head>
		<body link="#FF1493" text="#696969">
		<a name="ptop"></a>
		<font size="-1">
		<center><font color="#FF1493">'.$title.'</font><br>';
	}
}

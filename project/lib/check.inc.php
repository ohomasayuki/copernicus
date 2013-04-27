<?php
/*
*  入力チェック
*/

/*  全角チェック
*  全角ならtrueを返します。
*  @param 対象の文字列
*/
function checkZen($data) {
    //magic_quotes_gpcがONの時は、エスケープを解除する
    if (get_magic_quotes_gpc()) {
        $data = stripslashes($data);
    }

    if (strlen($data) == mb_strlen($data) * 2) {
        return true;
    } else {
        return false;
    }
}

/*  全角ひらがなチェック
*  全角ひらがなならtrueを返します。
*  @param 対象の文字列
*/
function isZenHkana($data) {
    //magic_quotes_gpcがONの時は、エスケープを解除する
    if (get_magic_quotes_gpc()) {
        $data = stripslashes($data);
    }

//    $data= trim($data);
    $pat = "^[ぁあぃいぅうぇえぉおかがきぎくぐけげこごさざしじすずせぜそぞただちぢっつづてでとどなにぬねのはばぱひびぴふぶぷへべぺほぼぽまみむめもゃやゅゆょよらりるれろゎわゐゑをん゛゜　ー]+$";   
    if (mb_ereg_match($pat, $data)) {
        return true;
    } else {
        return false;
    }
}

/*  半角カタカナチェック
*  半角カタカナが文字列に入っていればtureを返します。
*  @param 対象の文字列
*/
function isHnkakukana ($data ) {

	$data_tmp = mb_convert_kana($data, 'K');
	if($data == $data_tmp)
	{
		return false;
	}
	else
	{
		return true;
	}
}

/*  入力チェック
*  空ならtrueを返します。
*  @param 対象データ
*/
function isEmpty($data) {
    //全角スペースを半角スペースに変換する
    $data = mb_convert_kana($data, "s");
    if (!strlen(trim($data))) {
        return true;
    } else {
        return false;
    }
}

/*  半角数字チェック
*  半角数字ならtrueを返します。
*  @param 
*/
function isNum($data) {
    $pat = "^[0-9]+$";
    if (ereg($pat, $data)) {
        return true;
    } else {
        return false;
    }
}

/*  半角英数字チェック
*  半角英数字ならtrueを返します。
*  @param 
*/
function isHalfChar($data) {
    $pat = "^[a-zA-Z0-9_]+$";
    if (ereg($pat, $data)) {
        return true;
    } else {
        return false;
    }
}

/*  半角チェック
*  半角ならtrueを返します。
*  @param 
*/
function checkHan($data) {
    //magic_quotes_gpcがONの時は、エスケープを解除する
    if (get_magic_quotes_gpc()) {
        $data = stripslashes($data);
    }

    if (strlen($data) == mb_strlen($data)) {
        return true;
    } else {
        return false;
    }
}

/*  半角の最小値チェック
*  最小値以上ならtrueを返します。
*  @param 文字列
*/
function checkMin($data, $min) {
    //magic_quotes_gpcがONの時は、エスケープを解除する
    if (get_magic_quotes_gpc()) {
        $data = stripslashes($data);
    }
    if (strlen($data) < $min) {
        return false;
    } else {
        return true;
    }
}

/*  半角の最大値チェック
*  最大値以下ならtrueを返します。
*  @param 文字列
*/

function checkMax($data, $max) {
    //magic_quotes_gpcがONの時は、エスケープを解除する
    if (get_magic_quotes_gpc()) {
        $data = stripslashes($data);
    }
    if (strlen($data) > $max ) {
        return false;
    } else {
        return true;
    }
}

/* 日付存在チェック
*  存在しないならtrueを返します。
*  @param 日付(20070601)
*/

function checkData($date) {
//__echo("checkData in date($date)<br>");
	if (!is_numeric($date) or strlen($date) <> 8 ) {
		return FALSE;
	} elseif( !checkdate(substr($date, 4, 2), substr($date, 6, 2), substr($date, 0, 4)) ) {
		return FALSE;
	}
	return TRUE;
}

/*  メールチェック
*  適正ならtrueを返します。
*  @param 対象の文字列
*/
function checkMail($data)
{
//  if ( ereg("^[^@]+@[^.]+\..+", $data) )
	if ( preg_match('/^[a-z0-9\-\.\/_+]+@([a-z0-9]\.)?([0-9a-z\-]*[a-z0-9]\.)+[a-z]{1,4}$/i', $data) )
	{
		return true;
	}
	else
	{
		return false;
	}
}

/*  ドメインチェック
*  適正ならtrueを返します。
*  @param 対象の文字列
*/
function checkDomain($data)
{
  if ( ereg("^[^.@]+\.+[a-z0-9_]+", $data) )
//  if ( ereg("(^[\w\.-]+)(.+)", $data) )
    {
    return true;
  }
  else
  {
    return false;
  }
}

/*  全角半角文字数取得(UTF-8)
*  utf-8文字列用で全角=2,半角=1で長さを計算します。
*  @param 対象の文字列
*/
function strlenZenHan($string)
{
	$len = 0;
	for( $i = 0; $i < mb_strlen($string); $i++ ) {
		$s = mb_substr( $string, $i, 1 );
		if( mb_strwidth($s) == 1 ) {
			if( preg_match('/^(<%EMOJI::[0-9]+%>)/', mb_substr( $string, $i ), $matches )) {
				// 絵文字タグ
				$emoji_len = mb_strlen( $matches[1] );
				$i = $i + $emoji_len-1;
				$len+=2;	// 2追加
			} else {
				// 半角
				$len++;		//1追加
			}
		} else {
			// 全角
			$len+=2;	// 2追加
		}
	}
	return($len);
}


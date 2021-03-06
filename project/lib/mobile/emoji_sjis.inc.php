<?php
require_once dirname(__FILE__).'/emoji_softbank.inc.php';
// 絵文字を変換するライブラリ

/**
 * ドコモのバイナリ絵文字を絵文字タグに変換
 * 例: 
 * list($emoji_uni, $emoji_tag) = loadEmojiArray();
 * "晴れ=<%EMOJI::1%> 雨<%EMOJI::3%>雨<%EMOJI::3%>" = convertEmoji('晴れ=�� 雨�｡雨�｡', $emoji_uni, $emoji_tag);
 */
function convertEmoji( $text, $emoji_uni, $emoji_tag)
{
	if( isDocomo() ){
		$text = docomoEmojiToTag($text); // 高精度なドコモバイナリ絵文字変換
	}elseif( isAu() ){
		$text = auEmojiToTag($text); //
	}elseif( isSoftbank() ){
		$text = softbankEmojiToTag($text); //
	}else{
		//$text =  str_replace( $emoji_uni, $emoji_tag, $text);
		$text = docomoEmojiToTag($text); // 高精度なドコモバイナリ絵文字変換
	}
//__log("convertEmoji text($text)");
	return $text;
}

/**
 * バイナリ絵文字と変換配列を取得
 * 例: "晴れ=<%EMOJI::1%> 雨<%EMOJI::3%>雨<%EMOJI::3%>" = str_replace($emoji_uni, $emoji_change, '晴れ=�� 雨�｡雨�｡');
 */
function loadEmojiArray(){
  $emoji_tag = array();
  if( isAu() ){
    $emoji_uni_au = loadEmoji_au();
    foreach($emoji_uni_au as $k => $v){
      //if( $v['idx'] == 0 )	$v['idx'] = 1; // no imode emoji
      //$emoji_tag[$k] = '<%EMOJI::' . $v['idx'] . '%>';
      if( $v['idx'] == 0 ) {
      	//$emoji_tag[$k] = '□';	// []
      	$emoji_tag[$k] = mb_convert_encoding(NO_CONVERT_EMOJI_STRING, 'SJIS-win', 'UTF-8');
      	//$emoji_tag[$k] = '？';	// ?
      } else {
      	$emoji_tag[$k] = '<%EMOJI::' . $v['idx'] . '%>';
      }
      //$emoji_uni[$k] = $v['bin'];
      $emoji_uni[$k] = $v['hex'];
//__log("uni({$emoji_uni[$k]}) tag({$emoji_tag[$k]})");
    }
  } elseif( isSoftbank() ){
    $emoji_uni = loadEmoji_softbank();
    foreach($emoji_uni as $k => $v){
      if( $v['idx'] == 0 ) {
      	//$emoji_tag[$k] = '[]';	// []
      	//$emoji_tag[$k] = mb_convert_encoding(NO_CONVERT_EMOJI_STRING, 'SJIS-win', 'UTF-8');
      	$emoji_tag[$k] = NO_CONVERT_EMOJI_STRING;
      } else {
	$emoji_tag[$k] = '<%EMOJI::' . $v['idx'] . '%>';
      }
    }
  } else {
    $emoji_uni = loadEmoji();
    foreach($emoji_uni as $k => $v){
      $emoji_tag[$k] = '<%EMOJI::' . $k . '%>';
    }
  }
  return array($emoji_uni, $emoji_tag);
}

/**
 * 概要　　：ドコモバイナリ絵文字を絵文字タグに変換
 * 引数　　：$s ドコモバイナリ絵文字を含む文字列
 * 戻り値　：絵文字タグに変換した文字列
 * グローバル変数emoji_uni, emoji_tagを参照します。
 */
function docomoEmojiToTag($s){
  global $emoji_uni, $emoji_tag;
  $now = 0; // 現在のカーソル位置
  // バイナリデータを配列に代入
//__log("string($s)");
  $bs = unpack("C*", $s);
  $length = count($bs);
  for($i=1; $i < $length; $i++){
    $s1 = $bs[$i];
    $s2 = $bs[$i+1];
//__log("s1(".sprintf("%x",$s1).")s2(".sprintf("%x",$s2).")");
    /*
    if( $s1 != 0xF9 && $s1 != 0xF8 ){
      // 絵文字1バイト目ではない
      $now++;
      continue;
    }*/
    if(($s1 == 0xF8) && (0x9F <= $s2) && ($s2 <= 0xFC)) {
//__log("1>s1(".sprintf("%x",$s1).")s2(".sprintf("%x",$s2).")");
    } elseif(($s1 == 0xF9) &&
        ((0x40 <= $s2) && ($s2 <= 0x49) ||
         (0x50 <= $s2) && ($s2 <= 0x52) ||
         (0x55 <= $s2) && ($s2 <= 0x57) ||
         (0x5B <= $s2) && ($s2 <= 0x5E) ||
         (0x72 <= $s2) && ($s2 <= 0x7E) ||
         (0x80 <= $s2) && ($s2 <= 0xFC))) {
//__log("2>s1(".sprintf("%x",$s1).")s2(".sprintf("%x",$s2).")");
    } elseif(((0x81 <= $s1) && ($s1 <= 0x9f))
             || ((0xe0 <= $s1) && ($s1 <= 0xfc))) {
      $now+=2;
      $i++;
//__log("3>s1(".sprintf("%x",$s1).")s2(".sprintf("%x",$s2).")");
      continue;
    }else{
      $now++;
//__log("4>s1(".sprintf("%x",$s1).")s2(".sprintf("%x",$s2).")");
      continue;
    }
    // 2つのバイナリを合わせて絵文字作成
    $emoji = pack('C*', $s1 , $s2);
    $tag = str_replace($emoji_uni, $emoji_tag, $emoji);
    // 絵文字の2バイトをタグに置換
    $emoji_byte = 2;
    $s = substr_replace( $s, $tag, $now, $emoji_byte );
    $now += strlen($tag); // タグの終わりまでカーソルを進める
    $i++; // 絵文字2バイト目をスキップ
  }
//echo $s;
//__log("ret string($s)");
  return $s;
}

function auEmojiToTag($s){
  global $emoji_uni, $emoji_tag;
  $now = 0; // 現在のカーソル位置
  // バイナリデータを配列に代入
  $bs = unpack("C*", $s);
  $length = count($bs);
  for($i=1; $i < $length; $i++){
    $s1 = $bs[$i];
    $s2 = $bs[$i+1];
    /*
    if( $s1 != 0xF9 && $s1 != 0xF8 ){
      // 絵文字1バイト目ではない
      $now++;
      continue;
    }*/
    if($s1 == 0xF3 && ($s2 >= 0x40 && $s2 <= 0xFC)) {
    } elseif($s1 == 0xF4 && ($s2 >= 0x40 && $s2 <= 0x8D)) {
    } elseif($s1 == 0xF6 && ($s2 >= 0x40 && $s2 <= 0xFC)) {
    } elseif($s1 == 0xF7 && ($s2 >= 0x40 && $s2 <= 0xFC)) {
    } elseif(((0x81 <= $s1) && ($s1 <= 0x9f))
             || ((0xe0 <= $s1) && ($s1 <= 0xfc))) {
      $now+=2;
      $i++;
      continue;
    }else{
      $now++;
      continue;
    }
    // 2つのバイナリを合わせて絵文字作成
    //$emoji = pack('C*', $s1 , $s2);
    $emoji = sprintf("%2X%2X", $s1 , $s2);
//__log("emoji($emoji)");
    $tag = str_replace($emoji_uni, $emoji_tag, $emoji);
    // 絵文字の2バイトをタグに置換
    $emoji_byte = 2;
    $s = substr_replace( $s, $tag, $now, $emoji_byte );
    $now += strlen($tag); // タグの終わりまでカーソルを進める
    $i++; // 絵文字2バイト目をスキップ
  }
//echo $s;
  return $s;
}

function softbankEmojiToTag($s){
//echo("softbankEmojiToTag in<br>");
//echo("[$s]");
  global $emoji_uni, $emoji_tag;
  $now = 0; // 現在のカーソル位置
  // バイナリデータを配列に代入
  $bs = unpack("C*", $s);
  $length = count($bs);
  for($i=1; $i < $length; $i++){
    $s1 = $bs[$i];
    $s2 = $bs[$i+1];
    $s3 = $bs[$i+2];
    // 1byte:0x00ｿ0x7F
    if($s1 >= 0x00 && $s1 < 0x80) {
//echo("1");
      $now++;
      continue;
    // 2byte:0xC0ｿ0xDF
    } elseif($s1 >= 0xC0 && $s1 < 0xE0) {
//echo("2");
      $now+=2;
      $i++;
      continue;
    // 3byte:0xE0ｿ0xEF
    } elseif($s1 >= 0xE0 && $s1 < 0xF0) {
//echo("3");
	if( $s1 == 0xEE ) {
	} else {
	  $now+=3;
	  $i+=2;
	  continue;
	}
    }else{
//echo("e");
      $now++;
      continue;
    }
    // 2つのバイナリを合わせて絵文字作成
    //$emoji = pack('C*', $s1 , $s2);
    $emoji = sprintf("%2X%2X", $s2 , $s3);
//__log("emoji($emoji)");
    $tag = $emoji_tag[$emoji];
    //$tag = str_replace($emoji_uni, $emoji_tag, $emoji);
//__log("tag($tag)");
    // 絵文字の2バイトをタグに置換
    $emoji_byte = 3;
    $s = substr_replace( $s, $tag, $now, $emoji_byte );
    $now += strlen($tag); // タグの終わりまでカーソルを進める
    //$i++; // 絵文字2バイト目をスキップ
    $i+=2; // 絵文字2バイト目をスキップ
  }
//echo $s;
//var_dump($s);
//__log("[$s]<br>");
  return $s;
}

/**
 * $_REQUESTを一括変換
 * 絵文字を絵文字タグに変換し、SJISからUTF8に変換を行う。
 *　戻り値　　　　　: 変換した配列
 */
function requestConvertEmoji($request){
	global $emoji_uni,$emoji_tag;
	foreach( $request as $k => $v ){
		if( is_array($v) ){
			$request[$k] = requestConvertEmoji($v);
		}else{
/**
			{
				$a = array('��','��','��','��');
				$b = array('<%EMOJI::1072%>','<%EMOJI::90%>','<%EMOJI::91%>','<%EMOJI::1073%>');
				$v = convertEmoji( $v, $a, $b);
			}
**/
			$v = convertEmoji($v, $emoji_uni, $emoji_tag);
			//$v = str_replace(array('＜','＞'),array('<','>'), $v);
//__log("encoding bef($v)");
			//$request[$k] = mb_convert_encoding($v, 'UTF-8', 'SJIS');
			global	$carrier_id;
			if( $carrier_id <> 's' ) {
				$request[$k] = mb_convert_encoding($v, 'UTF-8', 'SJIS-win');
			} else {
				$request[$k] = $v;
			}
//__log("encoding aft({$request[$k]})");
			//__debug("$k convert-> $v");
		}
	}
	return $request;
}

/**
 * ドコモのバイナリ絵文字を取得
 */
function loadEmoji(){
	return array(
1=>'��',
2=>'��',
3=>'�｡',
4=>'�｢',
5=>'�｣',
6=>'�､',
7=>'�･',
8=>'�ｦ',
9=>'�ｧ',
10=>'�ｨ',
11=>'�ｩ',
12=>'�ｪ',
13=>'�ｫ',
14=>'�ｬ',
15=>'�ｭ',
16=>'�ｮ',
17=>'�ｯ',
18=>'�ｰ',
19=>'�ｱ',
20=>'�ｲ',
21=>'�ｳ',
22=>'�ｴ',
23=>'�ｵ',
24=>'�ｶ',
25=>'�ｷ',
26=>'�ｸ',
27=>'�ｹ',
28=>'�ｺ',
29=>'�ｻ',
30=>'�ｼ',
31=>'�ｽ',
32=>'�ｾ',
33=>'�ｿ',
34=>'�ﾀ',
35=>'�ﾁ',
36=>'�ﾂ',
37=>'�ﾃ',
38=>'�ﾄ',
39=>'�ﾅ',
40=>'�ﾆ',
41=>'�ﾇ',
42=>'�ﾈ',
43=>'�ﾉ',
44=>'�ﾊ',
45=>'�ﾋ',
46=>'�ﾌ',
47=>'�ﾍ',
48=>'�ﾎ',
49=>'�ﾏ',
50=>'�ﾐ',
51=>'�ﾑ',
52=>'�ﾒ',
53=>'�ﾓ',
54=>'�ﾔ',
55=>'�ﾕ',
56=>'�ﾖ',
57=>'�ﾗ',
58=>'�ﾘ',
59=>'�ﾙ',
60=>'�ﾚ',
61=>'�ﾛ',
62=>'�ﾜ',
63=>'�ﾝ',
64=>'�ﾞ',
65=>'�ﾟ',
66=>'��',
67=>'��',
68=>'��',
69=>'��',
70=>'��',
71=>'��',
72=>'��',
73=>'��',
74=>'��',
75=>'��',
76=>'��',
77=>'��',
78=>'��',
79=>'��',
80=>'��',
81=>'��',
82=>'��',
83=>'��',
84=>'��',
85=>'��',
86=>'��',
87=>'��',
88=>'��',
89=>'��',
90=>'��',
91=>'��',
92=>'��',
93=>'��',
94=>'��',
95=>'�@',
96=>'�A',
97=>'�B',
98=>'�C',
99=>'�D',
100=>'�E',
101=>'�F',
102=>'�G',
103=>'�H',
104=>'�I',
105=>'�r',
106=>'�s',
107=>'�t',
108=>'�u',
109=>'�v',
110=>'�w',
111=>'�x',
112=>'�y',
113=>'�z',
114=>'�{',
115=>'�|',
116=>'�}',
117=>'�~',
118=>'��',
119=>'��',
120=>'��',
121=>'��',
122=>'��',
123=>'��',
124=>'��',
125=>'��',
126=>'��',
127=>'��',
128=>'��',
129=>'��',
130=>'��',
131=>'��',
132=>'��',
133=>'��',
134=>'��',
135=>'�ｰ',
136=>'��',
137=>'��',
138=>'��',
139=>'��',
140=>'��',
141=>'��',
142=>'��',
143=>'��',
144=>'��',
145=>'��',
146=>'��',
147=>'��',
148=>'��',
149=>'��',
150=>'��',
151=>'��',
152=>'�｡',
153=>'�｢',
154=>'�｣',
155=>'�､',
156=>'�･',
157=>'�ｦ',
158=>'�ｧ',
159=>'�ｨ',
160=>'�ｩ',
161=>'�ｪ',
162=>'�ｫ',
163=>'�ｬ',
164=>'�ｭ',
165=>'�ｮ',
166=>'�ｯ',
167=>'�P',
168=>'�Q',
169=>'�R',
170=>'�U',
171=>'�V',
172=>'�W',
173=>'�[',
//174=>'�\', // これだけ特殊文字が入ってしまっている。
175=>'�]',
176=>'�^',
1001=>'�ｱ',
1002=>'�ｲ',
1003=>'�ｳ',
1004=>'�ｴ',
1005=>'�ｵ',
1006=>'�ｶ',
1007=>'�ｷ',
1008=>'�ｸ',
1009=>'�ｹ',
1010=>'�ｺ',
1011=>'�ｻ',
1012=>'�ｼ',
1013=>'�ｽ',
1014=>'�ｾ',
1015=>'�ｿ',
1016=>'�ﾀ',
1017=>'�ﾁ',
1018=>'�ﾂ',
1019=>'�ﾃ',
1020=>'�ﾄ',
1021=>'�ﾅ',
1022=>'�ﾆ',
1023=>'�ﾇ',
1024=>'�ﾈ',
1025=>'�ﾉ',
1026=>'�ﾊ',
1027=>'�ﾋ',
1028=>'�ﾌ',
1029=>'�ﾍ',
1030=>'�ﾎ',
1031=>'�ﾏ',
1032=>'�ﾐ',
1033=>'�ﾑ',
1034=>'�ﾒ',
1035=>'�ﾓ',
1036=>'�ﾔ',
1037=>'�ﾕ',
1038=>'�ﾖ',
1039=>'�ﾗ',
1040=>'�ﾘ',
1041=>'�ﾙ',
1042=>'�ﾚ',
1043=>'�ﾛ',
1044=>'�ﾜ',
1045=>'�ﾝ',
1046=>'�ﾞ',
1047=>'�ﾟ',
1048=>'��',
1049=>'��',
1050=>'��',
1051=>'��',
1052=>'��',
1053=>'��',
1054=>'��',
1055=>'��',
1056=>'��',
1057=>'��',
1058=>'��',
1059=>'��',
1060=>'��',
1061=>'��',
1062=>'��',
1063=>'��',
1064=>'��',
1065=>'��',
1066=>'��',
1067=>'��',
1068=>'��',
1069=>'��',
1070=>'��',
1071=>'��',
1072=>'��',
1073=>'��',
1074=>'��',
1075=>'��',
1076=>'��',
	);
}

/**
 * ドコモのバイナリ絵文字を取得
 */
function loadEmoji_au(){
	return array(
1=>array('bin'=>'�Y','hex'=>'F659','idx'=>1044),
2=>array('bin'=>'�Z','hex'=>'F65A','idx'=>158),
3=>array('bin'=>'�[','hex'=>'F65B','idx'=>0),
4=>array('bin'=>'�H','hex'=>'F748','idx'=>124),
5=>array('bin'=>'�I','hex'=>'F749','idx'=>0),
6=>array('bin'=>'�J','hex'=>'F74A','idx'=>0),
7=>array('bin'=>'�K','hex'=>'F74B','idx'=>0),
8=>array('bin'=>'�L','hex'=>'F74C','idx'=>0),
9=>array('bin'=>'�M','hex'=>'F74D','idx'=>0),
10=>array('bin'=>'�N','hex'=>'F74E','idx'=>0),
11=>array('bin'=>'�O','hex'=>'F74F','idx'=>0),
12=>array('bin'=>'��','hex'=>'F69A','idx'=>1075),
13=>array('bin'=>'��','hex'=>'F6EA','idx'=>0),
14=>array('bin'=>'��','hex'=>'F796','idx'=>0),
15=>array('bin'=>'�^','hex'=>'F65E','idx'=>98),
16=>array('bin'=>'�_','hex'=>'F65F','idx'=>5),
17=>array('bin'=>'�P','hex'=>'F750','idx'=>0),
18=>array('bin'=>'�Q','hex'=>'F751','idx'=>0),
19=>array('bin'=>'�R','hex'=>'F752','idx'=>0),
20=>array('bin'=>'�S','hex'=>'F753','idx'=>0),
21=>array('bin'=>'�T','hex'=>'F754','idx'=>0),
22=>array('bin'=>'�U','hex'=>'F755','idx'=>0),
23=>array('bin'=>'�V','hex'=>'F756','idx'=>0),
24=>array('bin'=>'�W','hex'=>'F757','idx'=>0),
25=>array('bin'=>'��','hex'=>'F797','idx'=>1020),
26=>array('bin'=>'�X','hex'=>'F758','idx'=>0),
27=>array('bin'=>'�Y','hex'=>'F759','idx'=>0),
28=>array('bin'=>'�Z','hex'=>'F75A','idx'=>0),
29=>array('bin'=>'�[','hex'=>'F75B','idx'=>0),
30=>array('bin'=>'�\\','hex'=>'F75C','idx'=>0),
31=>array('bin'=>'�]','hex'=>'F75D','idx'=>0),
32=>array('bin'=>'�^','hex'=>'F75E','idx'=>0),
33=>array('bin'=>'�_','hex'=>'F75F','idx'=>0),
34=>array('bin'=>'�`','hex'=>'F760','idx'=>0),
35=>array('bin'=>'�a','hex'=>'F761','idx'=>0),
36=>array('bin'=>'�b','hex'=>'F762','idx'=>0),
37=>array('bin'=>'�c','hex'=>'F763','idx'=>0),
38=>array('bin'=>'�d','hex'=>'F764','idx'=>0),
39=>array('bin'=>'�e','hex'=>'F765','idx'=>0),
40=>array('bin'=>'�f','hex'=>'F766','idx'=>0),
41=>array('bin'=>'�g','hex'=>'F767','idx'=>0),
42=>array('bin'=>'�h','hex'=>'F768','idx'=>90),
43=>array('bin'=>'�i','hex'=>'F769','idx'=>89),
44=>array('bin'=>'�`','hex'=>'F660','idx'=>1),
45=>array('bin'=>'��','hex'=>'F693','idx'=>22),
46=>array('bin'=>'�ｱ','hex'=>'F7B1','idx'=>176),
47=>array('bin'=>'�a','hex'=>'F661','idx'=>0),
48=>array('bin'=>'��','hex'=>'F6EB','idx'=>1008),
49=>array('bin'=>'�|','hex'=>'F77C','idx'=>0),
50=>array('bin'=>'�ﾓ','hex'=>'F6D3','idx'=>0),
51=>array('bin'=>'�ｲ','hex'=>'F7B2','idx'=>136),
52=>array('bin'=>'��','hex'=>'F69B','idx'=>52),
53=>array('bin'=>'��','hex'=>'F6EC','idx'=>1054),
54=>array('bin'=>'�j','hex'=>'F76A','idx'=>1039),
55=>array('bin'=>'�k','hex'=>'F76B','idx'=>0),
56=>array('bin'=>'�}','hex'=>'F77D','idx'=>0),
57=>array('bin'=>'��','hex'=>'F798','idx'=>0),
58=>array('bin'=>'�T','hex'=>'F654','idx'=>1017),
59=>array('bin'=>'�~','hex'=>'F77E','idx'=>0),
60=>array('bin'=>'�b','hex'=>'F662','idx'=>0),
61=>array('bin'=>'�l','hex'=>'F76C','idx'=>0),
62=>array('bin'=>'�m','hex'=>'F76D','idx'=>0),
63=>array('bin'=>'�n','hex'=>'F76E','idx'=>0),
64=>array('bin'=>'�o','hex'=>'F76F','idx'=>0),
65=>array('bin'=>'��','hex'=>'F69C','idx'=>53),
66=>array('bin'=>'�p','hex'=>'F770','idx'=>0),
67=>array('bin'=>'��','hex'=>'F780','idx'=>0),
68=>array('bin'=>'�ﾔ','hex'=>'F6D4','idx'=>0),
69=>array('bin'=>'�c','hex'=>'F663','idx'=>0),
70=>array('bin'=>'�q','hex'=>'F771','idx'=>59),
71=>array('bin'=>'�r','hex'=>'F772','idx'=>104),
72=>array('bin'=>'��','hex'=>'F6ED','idx'=>1016),
73=>array('bin'=>'�s','hex'=>'F773','idx'=>0),
74=>array('bin'=>'�ｸ','hex'=>'F6B8','idx'=>0),
75=>array('bin'=>'�@','hex'=>'F640','idx'=>0),
76=>array('bin'=>'�D','hex'=>'F644','idx'=>0),
77=>array('bin'=>'�N','hex'=>'F64E','idx'=>151),
78=>array('bin'=>'�ｹ','hex'=>'F6B9','idx'=>1068),
79=>array('bin'=>'�ｬ','hex'=>'F7AC','idx'=>0),
80=>array('bin'=>'�ﾕ','hex'=>'F6D5','idx'=>0),
81=>array('bin'=>'�t','hex'=>'F774','idx'=>1038),
82=>array('bin'=>'�u','hex'=>'F775','idx'=>1043),
83=>array('bin'=>'�t','hex'=>'F674','idx'=>69),
84=>array('bin'=>'�ｭ','hex'=>'F7AD','idx'=>0),
85=>array('bin'=>'�ｳ','hex'=>'F7B3','idx'=>74),
86=>array('bin'=>'�ﾖ','hex'=>'F6D6','idx'=>0),
87=>array('bin'=>'��','hex'=>'F799','idx'=>0),
88=>array('bin'=>'�v','hex'=>'F776','idx'=>0),
89=>array('bin'=>'�w','hex'=>'F777','idx'=>0),
90=>array('bin'=>'��','hex'=>'F790','idx'=>0),
91=>array('bin'=>'�u','hex'=>'F675','idx'=>0),
92=>array('bin'=>'��','hex'=>'F781','idx'=>0),
93=>array('bin'=>'�ｴ','hex'=>'F7B4','idx'=>51),
94=>array('bin'=>'��','hex'=>'F6EE','idx'=>68),
95=>array('bin'=>'�d','hex'=>'F664','idx'=>3),
96=>array('bin'=>'��','hex'=>'F694','idx'=>0),
97=>array('bin'=>'��','hex'=>'F782','idx'=>0),
98=>array('bin'=>'�\\','hex'=>'F65C','idx'=>0),
99=>array('bin'=>'�B','hex'=>'F642','idx'=>48),
100=>array('bin'=>'��','hex'=>'F783','idx'=>0),
101=>array('bin'=>'��','hex'=>'F784','idx'=>0),
102=>array('bin'=>'��','hex'=>'F785','idx'=>0),
103=>array('bin'=>'��','hex'=>'F786','idx'=>0),
104=>array('bin'=>'��','hex'=>'F6EF','idx'=>56),
105=>array('bin'=>'��','hex'=>'F787','idx'=>0),
106=>array('bin'=>'�v','hex'=>'F676','idx'=>65),
107=>array('bin'=>'�e','hex'=>'F665','idx'=>2),
108=>array('bin'=>'��','hex'=>'F6FA','idx'=>110),
109=>array('bin'=>'��','hex'=>'F79A','idx'=>113),
110=>array('bin'=>'��','hex'=>'F6F0','idx'=>58),
111=>array('bin'=>'��','hex'=>'F79B','idx'=>0),
112=>array('bin'=>'��','hex'=>'F684','idx'=>38),
113=>array('bin'=>'�ｽ','hex'=>'F6BD','idx'=>1056),
114=>array('bin'=>'��','hex'=>'F79C','idx'=>0),
115=>array('bin'=>'��','hex'=>'F79D','idx'=>0),
116=>array('bin'=>'�ﾗ','hex'=>'F6D7','idx'=>93),
117=>array('bin'=>'�x','hex'=>'F778','idx'=>0),
118=>array('bin'=>'�y','hex'=>'F779','idx'=>117),
119=>array('bin'=>'��','hex'=>'F6F1','idx'=>119),
120=>array('bin'=>'��','hex'=>'F6F2','idx'=>116),
121=>array('bin'=>'��','hex'=>'F788','idx'=>0),
122=>array('bin'=>'�w','hex'=>'F677','idx'=>70),
123=>array('bin'=>'��','hex'=>'F79E','idx'=>0),
124=>array('bin'=>'��','hex'=>'F6F3','idx'=>55),
125=>array('bin'=>'��','hex'=>'F68A','idx'=>33),
126=>array('bin'=>'��','hex'=>'F79F','idx'=>0),
127=>array('bin'=>'��','hex'=>'F791','idx'=>0),
128=>array('bin'=>'��','hex'=>'F792','idx'=>0),
129=>array('bin'=>'��','hex'=>'F6F4','idx'=>0),
130=>array('bin'=>'��','hex'=>'F7A0','idx'=>0),
131=>array('bin'=>'��','hex'=>'F789','idx'=>0),
132=>array('bin'=>'�z','hex'=>'F77A','idx'=>0),
133=>array('bin'=>'�ｧ','hex'=>'F6A7','idx'=>1060),
134=>array('bin'=>'�ｺ','hex'=>'F6BA','idx'=>100),
135=>array('bin'=>'�｡','hex'=>'F7A1','idx'=>0),
136=>array('bin'=>'�{','hex'=>'F77B','idx'=>0),
137=>array('bin'=>'��','hex'=>'F78A','idx'=>0),
138=>array('bin'=>'��','hex'=>'F6F5','idx'=>0),
139=>array('bin'=>'�｢','hex'=>'F7A2','idx'=>0),
140=>array('bin'=>'�ﾘ','hex'=>'F6D8','idx'=>0),
141=>array('bin'=>'�ﾙ','hex'=>'F6D9','idx'=>0),
142=>array('bin'=>'��','hex'=>'F78B','idx'=>0),
143=>array('bin'=>'�x','hex'=>'F678','idx'=>1037),
144=>array('bin'=>'�ｨ','hex'=>'F6A8','idx'=>72),
145=>array('bin'=>'��','hex'=>'F6F6','idx'=>0),
146=>array('bin'=>'��','hex'=>'F685','idx'=>50),
147=>array('bin'=>'��','hex'=>'F78C','idx'=>0),
148=>array('bin'=>'��','hex'=>'F68B','idx'=>0),
149=>array('bin'=>'�y','hex'=>'F679','idx'=>1014),
150=>array('bin'=>'�｣','hex'=>'F7A3','idx'=>0),
151=>array('bin'=>'�ｮ','hex'=>'F7AE','idx'=>0),
152=>array('bin'=>'�､','hex'=>'F7A4','idx'=>1013),
153=>array('bin'=>'�ｯ','hex'=>'F7AF','idx'=>0),
154=>array('bin'=>'�ｰ','hex'=>'F7B0','idx'=>0),
155=>array('bin'=>'��','hex'=>'F6F7','idx'=>0),
156=>array('bin'=>'��','hex'=>'F686','idx'=>39),
157=>array('bin'=>'��','hex'=>'F78D','idx'=>0),
158=>array('bin'=>'�z','hex'=>'F67A','idx'=>0),
159=>array('bin'=>'��','hex'=>'F793','idx'=>0),
160=>array('bin'=>'��','hex'=>'F69D','idx'=>0),
161=>array('bin'=>'�･','hex'=>'F7A5','idx'=>75),
162=>array('bin'=>'�ｦ','hex'=>'F7A6','idx'=>0),
163=>array('bin'=>'�ﾚ','hex'=>'F6DA','idx'=>0),
164=>array('bin'=>'�ｧ','hex'=>'F7A7','idx'=>0),
165=>array('bin'=>'��','hex'=>'F6F8','idx'=>0),
166=>array('bin'=>'��','hex'=>'F6F9','idx'=>107),
167=>array('bin'=>'�f','hex'=>'F666','idx'=>0),
168=>array('bin'=>'��','hex'=>'F68C','idx'=>37),
169=>array('bin'=>'��','hex'=>'F68D','idx'=>102),
170=>array('bin'=>'�｡','hex'=>'F6A1','idx'=>0),
171=>array('bin'=>'�ｨ','hex'=>'F7A8','idx'=>0),
172=>array('bin'=>'��','hex'=>'F68E','idx'=>30),
173=>array('bin'=>'�ｩ','hex'=>'F7A9','idx'=>0),
174=>array('bin'=>'�ｪ','hex'=>'F7AA','idx'=>0),
175=>array('bin'=>'�ｫ','hex'=>'F7AB','idx'=>0),
176=>array('bin'=>'�U','hex'=>'F655','idx'=>66),
177=>array('bin'=>'�V','hex'=>'F656','idx'=>67),
178=>array('bin'=>'�W','hex'=>'F657','idx'=>94),
179=>array('bin'=>'�X','hex'=>'F658','idx'=>0),
180=>array('bin'=>'��','hex'=>'F6FB','idx'=>125),
181=>array('bin'=>'��','hex'=>'F6FC','idx'=>126),
182=>array('bin'=>'�@','hex'=>'F740','idx'=>127),
183=>array('bin'=>'�A','hex'=>'F741','idx'=>128),
184=>array('bin'=>'�B','hex'=>'F742','idx'=>129),
185=>array('bin'=>'�C','hex'=>'F743','idx'=>130),
186=>array('bin'=>'�D','hex'=>'F744','idx'=>131),
187=>array('bin'=>'�E','hex'=>'F745','idx'=>132),
188=>array('bin'=>'�F','hex'=>'F746','idx'=>133),
189=>array('bin'=>'�G','hex'=>'F747','idx'=>0),
190=>array('bin'=>'�A','hex'=>'F641','idx'=>6),
191=>array('bin'=>'�]','hex'=>'F65D','idx'=>4),
192=>array('bin'=>'�g','hex'=>'F667','idx'=>9),
193=>array('bin'=>'�h','hex'=>'F668','idx'=>10),
194=>array('bin'=>'�i','hex'=>'F669','idx'=>11),
195=>array('bin'=>'�j','hex'=>'F66A','idx'=>12),
196=>array('bin'=>'�k','hex'=>'F66B','idx'=>13),
197=>array('bin'=>'�l','hex'=>'F66C','idx'=>14),
198=>array('bin'=>'�m','hex'=>'F66D','idx'=>15),
199=>array('bin'=>'�n','hex'=>'F66E','idx'=>16),
200=>array('bin'=>'�o','hex'=>'F66F','idx'=>17),
201=>array('bin'=>'�p','hex'=>'F670','idx'=>18),
202=>array('bin'=>'�q','hex'=>'F671','idx'=>19),
203=>array('bin'=>'�r','hex'=>'F672','idx'=>20),
204=>array('bin'=>'�s','hex'=>'F673','idx'=>0),
205=>array('bin'=>'�{','hex'=>'F67B','idx'=>43),
206=>array('bin'=>'�|','hex'=>'F67C','idx'=>45),
207=>array('bin'=>'�}','hex'=>'F67D','idx'=>49),
208=>array('bin'=>'�~','hex'=>'F67E','idx'=>47),
209=>array('bin'=>'��','hex'=>'F680','idx'=>0),
210=>array('bin'=>'��','hex'=>'F681','idx'=>0),
211=>array('bin'=>'��','hex'=>'F682','idx'=>0),
212=>array('bin'=>'��','hex'=>'F683','idx'=>42),
213=>array('bin'=>'��','hex'=>'F78E','idx'=>46),
214=>array('bin'=>'��','hex'=>'F78F','idx'=>0),
215=>array('bin'=>'��','hex'=>'F687','idx'=>1018),
216=>array('bin'=>'��','hex'=>'F688','idx'=>35),
217=>array('bin'=>'��','hex'=>'F689','idx'=>32),
218=>array('bin'=>'�C','hex'=>'F643','idx'=>1040),
219=>array('bin'=>'��','hex'=>'F68F','idx'=>25),
220=>array('bin'=>'��','hex'=>'F690','idx'=>24),
221=>array('bin'=>'��','hex'=>'F691','idx'=>1007),
223=>array('bin'=>'�E','hex'=>'F645','idx'=>0),
224=>array('bin'=>'��','hex'=>'F695','idx'=>147),
225=>array('bin'=>'��','hex'=>'F696','idx'=>0),
226=>array('bin'=>'��','hex'=>'F697','idx'=>167),
227=>array('bin'=>'��','hex'=>'F698','idx'=>0),
228=>array('bin'=>'��','hex'=>'F699','idx'=>0),
229=>array('bin'=>'�F','hex'=>'F646','idx'=>0),
230=>array('bin'=>'�G','hex'=>'F647','idx'=>0),
231=>array('bin'=>'��','hex'=>'F69E','idx'=>0),
232=>array('bin'=>'��','hex'=>'F69F','idx'=>78),
233=>array('bin'=>'��','hex'=>'F6A0','idx'=>1010),
234=>array('bin'=>'�｢','hex'=>'F6A2','idx'=>103),
235=>array('bin'=>'�｣','hex'=>'F6A3','idx'=>1061),
236=>array('bin'=>'�､','hex'=>'F6A4','idx'=>0),
237=>array('bin'=>'�･','hex'=>'F6A5','idx'=>0),
238=>array('bin'=>'�ｦ','hex'=>'F6A6','idx'=>0),
239=>array('bin'=>'�ｩ','hex'=>'F6A9','idx'=>1063),
240=>array('bin'=>'�ｪ','hex'=>'F6AA','idx'=>0),
241=>array('bin'=>'�ｫ','hex'=>'F6AB','idx'=>1055),
242=>array('bin'=>'�ｬ','hex'=>'F6AC','idx'=>0),
243=>array('bin'=>'�ｭ','hex'=>'F6AD','idx'=>0),
244=>array('bin'=>'�ｮ','hex'=>'F6AE','idx'=>1062),
246=>array('bin'=>'�H','hex'=>'F648','idx'=>0),
247=>array('bin'=>'�ｰ','hex'=>'F6B0','idx'=>0),
248=>array('bin'=>'�ｱ','hex'=>'F6B1','idx'=>1073),
249=>array('bin'=>'�ｲ','hex'=>'F6B2','idx'=>0),
250=>array('bin'=>'�ｳ','hex'=>'F6B3','idx'=>0),
251=>array('bin'=>'�ｴ','hex'=>'F6B4','idx'=>101),
252=>array('bin'=>'�ｵ','hex'=>'F6B5','idx'=>1069),
253=>array('bin'=>'�ｶ','hex'=>'F6B6','idx'=>0),
254=>array('bin'=>'�ｷ','hex'=>'F6B7','idx'=>1074),
255=>array('bin'=>'�ｻ','hex'=>'F6BB','idx'=>0),
256=>array('bin'=>'�ｼ','hex'=>'F6BC','idx'=>0),
257=>array('bin'=>'�I','hex'=>'F649','idx'=>140),
258=>array('bin'=>'�J','hex'=>'F64A','idx'=>141),
259=>array('bin'=>'�K','hex'=>'F64B','idx'=>1034),
260=>array('bin'=>'�L','hex'=>'F64C','idx'=>0),
261=>array('bin'=>'�M','hex'=>'F64D','idx'=>157),
262=>array('bin'=>'�ｾ','hex'=>'F6BE','idx'=>152),
263=>array('bin'=>'�ｿ','hex'=>'F6BF','idx'=>163),
264=>array('bin'=>'�ﾀ','hex'=>'F6C0','idx'=>1029),
265=>array('bin'=>'�O','hex'=>'F64F','idx'=>138),
266=>array('bin'=>'�P','hex'=>'F650','idx'=>139),
267=>array('bin'=>'�Q','hex'=>'F651','idx'=>0),
268=>array('bin'=>'�R','hex'=>'F652','idx'=>154),
269=>array('bin'=>'�S','hex'=>'F653','idx'=>0),
270=>array('bin'=>'�ﾁ','hex'=>'F6C1','idx'=>0),
271=>array('bin'=>'�ﾂ','hex'=>'F6C2','idx'=>0),
272=>array('bin'=>'�ﾃ','hex'=>'F6C3','idx'=>0),
273=>array('bin'=>'�ﾄ','hex'=>'F6C4','idx'=>149),
274=>array('bin'=>'�ﾅ','hex'=>'F6C5','idx'=>0),
275=>array('bin'=>'�ﾆ','hex'=>'F6C6','idx'=>0),
276=>array('bin'=>'�ﾇ','hex'=>'F6C7','idx'=>0),
277=>array('bin'=>'�ﾈ','hex'=>'F6C8','idx'=>0),
278=>array('bin'=>'�ﾉ','hex'=>'F6C9','idx'=>0),
279=>array('bin'=>'�ﾊ','hex'=>'F6CA','idx'=>1041),
280=>array('bin'=>'�ﾋ','hex'=>'F6CB','idx'=>0),
281=>array('bin'=>'�ﾌ','hex'=>'F6CC','idx'=>153),
282=>array('bin'=>'�ﾍ','hex'=>'F6CD','idx'=>164),
283=>array('bin'=>'�ﾎ','hex'=>'F6CE','idx'=>0),
284=>array('bin'=>'�ﾏ','hex'=>'F6CF','idx'=>0),
285=>array('bin'=>'�ﾐ','hex'=>'F6D0','idx'=>0),
286=>array('bin'=>'�ﾑ','hex'=>'F6D1','idx'=>0),
287=>array('bin'=>'�ﾒ','hex'=>'F6D2','idx'=>1028),
288=>array('bin'=>'�ﾛ','hex'=>'F6DB','idx'=>77),
289=>array('bin'=>'�ﾜ','hex'=>'F6DC','idx'=>57),
290=>array('bin'=>'�ﾝ','hex'=>'F6DD','idx'=>1004),
291=>array('bin'=>'�ﾞ','hex'=>'F6DE','idx'=>155),
292=>array('bin'=>'�ﾟ','hex'=>'F6DF','idx'=>0),
293=>array('bin'=>'��','hex'=>'F6E0','idx'=>0),
294=>array('bin'=>'��','hex'=>'F6E1','idx'=>61),
295=>array('bin'=>'��','hex'=>'F6E2','idx'=>1005),
296=>array('bin'=>'��','hex'=>'F6E3','idx'=>0),
297=>array('bin'=>'��','hex'=>'F6E4','idx'=>0),
298=>array('bin'=>'��','hex'=>'F794','idx'=>0),
299=>array('bin'=>'��','hex'=>'F795','idx'=>114),
300=>array('bin'=>'��','hex'=>'F6E5','idx'=>79),
301=>array('bin'=>'��','hex'=>'F6E6','idx'=>0),
302=>array('bin'=>'��','hex'=>'F6E7','idx'=>0),
303=>array('bin'=>'��','hex'=>'F6E8','idx'=>0),
304=>array('bin'=>'��','hex'=>'F6E9','idx'=>0),
305=>array('bin'=>'�ｵ','hex'=>'F7B5','idx'=>7),
306=>array('bin'=>'�ｶ','hex'=>'F7B6','idx'=>23),
307=>array('bin'=>'�ｷ','hex'=>'F7B7','idx'=>27),
308=>array('bin'=>'�ｸ','hex'=>'F7B8','idx'=>29),
309=>array('bin'=>'�ｹ','hex'=>'F7B9','idx'=>62),
310=>array('bin'=>'�ｺ','hex'=>'F7BA','idx'=>0),
311=>array('bin'=>'�ｻ','hex'=>'F7BB','idx'=>64),
312=>array('bin'=>'�ｼ','hex'=>'F7BC','idx'=>71),
313=>array('bin'=>'�ｽ','hex'=>'F7BD','idx'=>73),
314=>array('bin'=>'�ｾ','hex'=>'F7BE','idx'=>81),
315=>array('bin'=>'�ｿ','hex'=>'F7BF','idx'=>82),
316=>array('bin'=>'�ﾀ','hex'=>'F7C0','idx'=>83),
317=>array('bin'=>'�ﾁ','hex'=>'F7C1','idx'=>84),
318=>array('bin'=>'�ﾂ','hex'=>'F7C2','idx'=>85),
319=>array('bin'=>'�ﾃ','hex'=>'F7C3','idx'=>87),
320=>array('bin'=>'�ﾄ','hex'=>'F7C4','idx'=>88),
321=>array('bin'=>'�ﾅ','hex'=>'F7C5','idx'=>95),
322=>array('bin'=>'�ﾆ','hex'=>'F7C6','idx'=>96),
323=>array('bin'=>'�ﾇ','hex'=>'F7C7','idx'=>97),
324=>array('bin'=>'�ﾈ','hex'=>'F7C8','idx'=>118),
325=>array('bin'=>'�ﾉ','hex'=>'F7C9','idx'=>134),
326=>array('bin'=>'�ﾊ','hex'=>'F7CA','idx'=>135),
327=>array('bin'=>'�ﾋ','hex'=>'F7CB','idx'=>144),
328=>array('bin'=>'�ﾌ','hex'=>'F7CC','idx'=>0),
329=>array('bin'=>'�ﾍ','hex'=>'F7CD','idx'=>161),
330=>array('bin'=>'�ﾎ','hex'=>'F7CE','idx'=>162),
331=>array('bin'=>'�ﾏ','hex'=>'F7CF','idx'=>0),
332=>array('bin'=>'�ﾐ','hex'=>'F7D0','idx'=>0),
333=>array('bin'=>'�ﾑ','hex'=>'F7D1','idx'=>1065),
334=>array('bin'=>'��','hex'=>'F7E5','idx'=>120),
335=>array('bin'=>'��','hex'=>'F7E6','idx'=>1003),
336=>array('bin'=>'��','hex'=>'F7E7','idx'=>0),
337=>array('bin'=>'��','hex'=>'F7E8','idx'=>1011),
338=>array('bin'=>'��','hex'=>'F7E9','idx'=>0),
339=>array('bin'=>'��','hex'=>'F7EA','idx'=>0),
340=>array('bin'=>'��','hex'=>'F7EB','idx'=>0),
341=>array('bin'=>'��','hex'=>'F7EC','idx'=>31),
342=>array('bin'=>'��','hex'=>'F7ED','idx'=>1053),
343=>array('bin'=>'��','hex'=>'F7EE','idx'=>146),
344=>array('bin'=>'��','hex'=>'F7EF','idx'=>0),
345=>array('bin'=>'��','hex'=>'F7F0','idx'=>0),
346=>array('bin'=>'��','hex'=>'F7F1','idx'=>0),
347=>array('bin'=>'��','hex'=>'F7F2','idx'=>0),
348=>array('bin'=>'��','hex'=>'F7F3','idx'=>1030),
349=>array('bin'=>'��','hex'=>'F7F4','idx'=>1027),
350=>array('bin'=>'��','hex'=>'F7F5','idx'=>1076),
351=>array('bin'=>'��','hex'=>'F7F6','idx'=>1024),
352=>array('bin'=>'��','hex'=>'F7F7','idx'=>0),
353=>array('bin'=>'��','hex'=>'F7F8','idx'=>0),
354=>array('bin'=>'��','hex'=>'F7F9','idx'=>1015),
355=>array('bin'=>'��','hex'=>'F7FA','idx'=>0),
356=>array('bin'=>'��','hex'=>'F7FB','idx'=>0),
357=>array('bin'=>'��','hex'=>'F7FC','idx'=>0),
358=>array('bin'=>'�@','hex'=>'F340','idx'=>0),
359=>array('bin'=>'�A','hex'=>'F341','idx'=>0),
360=>array('bin'=>'�B','hex'=>'F342','idx'=>0),
361=>array('bin'=>'�C','hex'=>'F343','idx'=>0),
362=>array('bin'=>'�D','hex'=>'F344','idx'=>0),
363=>array('bin'=>'�E','hex'=>'F345','idx'=>0),
364=>array('bin'=>'�F','hex'=>'F346','idx'=>0),
365=>array('bin'=>'�G','hex'=>'F347','idx'=>0),
366=>array('bin'=>'�H','hex'=>'F348','idx'=>0),
367=>array('bin'=>'�I','hex'=>'F349','idx'=>0),
368=>array('bin'=>'�J','hex'=>'F34A','idx'=>0),
369=>array('bin'=>'�K','hex'=>'F34B','idx'=>0),
370=>array('bin'=>'�L','hex'=>'F34C','idx'=>0),
371=>array('bin'=>'�M','hex'=>'F34D','idx'=>0),
373=>array('bin'=>'�O','hex'=>'F34F','idx'=>0),
374=>array('bin'=>'�P','hex'=>'F350','idx'=>0),
375=>array('bin'=>'�Q','hex'=>'F351','idx'=>40),
376=>array('bin'=>'�R','hex'=>'F352','idx'=>41),
377=>array('bin'=>'�S','hex'=>'F353','idx'=>1051),
378=>array('bin'=>'�T','hex'=>'F354','idx'=>44),
379=>array('bin'=>'�U','hex'=>'F355','idx'=>36),
380=>array('bin'=>'�V','hex'=>'F356','idx'=>0),
381=>array('bin'=>'�W','hex'=>'F357','idx'=>0),
382=>array('bin'=>'�X','hex'=>'F358','idx'=>0),
383=>array('bin'=>'�Y','hex'=>'F359','idx'=>0),
384=>array('bin'=>'�Z','hex'=>'F35A','idx'=>0),
385=>array('bin'=>'�[','hex'=>'F35B','idx'=>115),
386=>array('bin'=>'�\\','hex'=>'F35C','idx'=>1048),
387=>array('bin'=>'�]','hex'=>'F35D','idx'=>1046),
388=>array('bin'=>'�^','hex'=>'F35E','idx'=>0),
389=>array('bin'=>'�_','hex'=>'F35F','idx'=>0),
390=>array('bin'=>'�`','hex'=>'F360','idx'=>0),
391=>array('bin'=>'�a','hex'=>'F361','idx'=>0),
392=>array('bin'=>'�b','hex'=>'F362','idx'=>0),
393=>array('bin'=>'�c','hex'=>'F363','idx'=>0),
394=>array('bin'=>'��','hex'=>'F364','idx'=>0),
395=>array('bin'=>'�e','hex'=>'F365','idx'=>76),
396=>array('bin'=>'�f','hex'=>'F366','idx'=>0),
397=>array('bin'=>'�g','hex'=>'F367','idx'=>0),
398=>array('bin'=>'�h','hex'=>'F368','idx'=>0),
399=>array('bin'=>'�i','hex'=>'F369','idx'=>0),
400=>array('bin'=>'�j','hex'=>'F36A','idx'=>1064),
401=>array('bin'=>'�k','hex'=>'F36B','idx'=>0),
402=>array('bin'=>'�l','hex'=>'F36C','idx'=>0),
403=>array('bin'=>'�m','hex'=>'F36D','idx'=>0),
404=>array('bin'=>'�n','hex'=>'F36E','idx'=>0),
405=>array('bin'=>'�o','hex'=>'F36F','idx'=>0),
406=>array('bin'=>'�p','hex'=>'F370','idx'=>0),
407=>array('bin'=>'�q','hex'=>'F371','idx'=>0),
408=>array('bin'=>'�r','hex'=>'F372','idx'=>0),
409=>array('bin'=>'�s','hex'=>'F373','idx'=>0),
410=>array('bin'=>'�t','hex'=>'F374','idx'=>0),
411=>array('bin'=>'�u','hex'=>'F375','idx'=>0),
412=>array('bin'=>'�v','hex'=>'F376','idx'=>0),
413=>array('bin'=>'�w','hex'=>'F377','idx'=>0),
414=>array('bin'=>'�x','hex'=>'F378','idx'=>80),
415=>array('bin'=>'�y','hex'=>'F379','idx'=>0),
416=>array('bin'=>'�z','hex'=>'F37A','idx'=>0),
417=>array('bin'=>'�{','hex'=>'F37B','idx'=>0),
418=>array('bin'=>'�|','hex'=>'F37C','idx'=>0),
419=>array('bin'=>'�}','hex'=>'F37D','idx'=>0),
420=>array('bin'=>'�~','hex'=>'F37E','idx'=>150),
421=>array('bin'=>'��','hex'=>'F380','idx'=>26),
422=>array('bin'=>'��','hex'=>'F381','idx'=>0),
423=>array('bin'=>'��','hex'=>'F382','idx'=>1019),
424=>array('bin'=>'��','hex'=>'F383','idx'=>1066),
425=>array('bin'=>'��','hex'=>'F384','idx'=>0),
426=>array('bin'=>'��','hex'=>'F385','idx'=>0),
427=>array('bin'=>'��','hex'=>'F386','idx'=>0),
428=>array('bin'=>'��','hex'=>'F387','idx'=>0),
429=>array('bin'=>'��','hex'=>'F388','idx'=>0),
430=>array('bin'=>'��','hex'=>'F389','idx'=>0),
431=>array('bin'=>'��','hex'=>'F38A','idx'=>0),
432=>array('bin'=>'��','hex'=>'F38B','idx'=>0),
433=>array('bin'=>'��','hex'=>'F38C','idx'=>0),
434=>array('bin'=>'��','hex'=>'F38D','idx'=>1058),
435=>array('bin'=>'��','hex'=>'F38E','idx'=>0),
436=>array('bin'=>'��','hex'=>'F38F','idx'=>0),
437=>array('bin'=>'��','hex'=>'F390','idx'=>0),
438=>array('bin'=>'��','hex'=>'F391','idx'=>0),
439=>array('bin'=>'��','hex'=>'F392','idx'=>0),
440=>array('bin'=>'��','hex'=>'F393','idx'=>1033),
441=>array('bin'=>'��','hex'=>'F394','idx'=>142),
442=>array('bin'=>'��','hex'=>'F395','idx'=>0),
443=>array('bin'=>'��','hex'=>'F396','idx'=>1032),
444=>array('bin'=>'��','hex'=>'F397','idx'=>143),
445=>array('bin'=>'��','hex'=>'F398','idx'=>0),
446=>array('bin'=>'��','hex'=>'F399','idx'=>1022),
447=>array('bin'=>'��','hex'=>'F39A','idx'=>0),
448=>array('bin'=>'��','hex'=>'F39B','idx'=>0),
449=>array('bin'=>'��','hex'=>'F39C','idx'=>0),
450=>array('bin'=>'��','hex'=>'F39D','idx'=>1026),
451=>array('bin'=>'��','hex'=>'F39E','idx'=>0),
452=>array('bin'=>'��','hex'=>'F39F','idx'=>0),
453=>array('bin'=>'��','hex'=>'F3A0','idx'=>0),
454=>array('bin'=>'�｡','hex'=>'F3A1','idx'=>1071),
455=>array('bin'=>'�｢','hex'=>'F3A2','idx'=>0),
456=>array('bin'=>'�｣','hex'=>'F3A3','idx'=>0),
457=>array('bin'=>'�､','hex'=>'F3A4','idx'=>0),
458=>array('bin'=>'�･','hex'=>'F3A5','idx'=>0),
459=>array('bin'=>'�ｦ','hex'=>'F3A6','idx'=>0),
460=>array('bin'=>'�ｧ','hex'=>'F3A7','idx'=>0),
461=>array('bin'=>'�ｨ','hex'=>'F3A8','idx'=>0),
462=>array('bin'=>'�ｩ','hex'=>'F3A9','idx'=>0),
463=>array('bin'=>'�ｪ','hex'=>'F3AA','idx'=>0),
464=>array('bin'=>'�ｫ','hex'=>'F3AB','idx'=>0),
465=>array('bin'=>'�ｬ','hex'=>'F3AC','idx'=>0),
466=>array('bin'=>'�ｭ','hex'=>'F3AD','idx'=>0),
467=>array('bin'=>'�ｮ','hex'=>'F3AE','idx'=>0),
468=>array('bin'=>'�ｯ','hex'=>'F3AF','idx'=>0),
469=>array('bin'=>'�ｰ','hex'=>'F3B0','idx'=>0),
470=>array('bin'=>'�ｱ','hex'=>'F3B1','idx'=>0),
471=>array('bin'=>'�ｲ','hex'=>'F3B2','idx'=>0),
472=>array('bin'=>'�ｳ','hex'=>'F3B3','idx'=>0),
473=>array('bin'=>'�ｴ','hex'=>'F3B4','idx'=>0),
474=>array('bin'=>'�ｵ','hex'=>'F3B5','idx'=>0),
475=>array('bin'=>'�ｶ','hex'=>'F3B6','idx'=>0),
476=>array('bin'=>'�ｷ','hex'=>'F3B7','idx'=>0),
477=>array('bin'=>'�ｸ','hex'=>'F3B8','idx'=>0),
478=>array('bin'=>'�ｹ','hex'=>'F3B9','idx'=>0),
479=>array('bin'=>'�ｺ','hex'=>'F3BA','idx'=>0),
480=>array('bin'=>'�ｻ','hex'=>'F3BB','idx'=>0),
481=>array('bin'=>'�ｼ','hex'=>'F3BC','idx'=>8),
482=>array('bin'=>'�ｽ','hex'=>'F3BD','idx'=>0),
483=>array('bin'=>'�ｾ','hex'=>'F3BE','idx'=>0),
484=>array('bin'=>'�ｿ','hex'=>'F3BF','idx'=>0),
485=>array('bin'=>'�ﾀ','hex'=>'F3C0','idx'=>0),
486=>array('bin'=>'�ﾁ','hex'=>'F3C1','idx'=>0),
487=>array('bin'=>'�ﾂ','hex'=>'F3C2','idx'=>0),
488=>array('bin'=>'�ﾃ','hex'=>'F3C3','idx'=>0),
489=>array('bin'=>'�ﾄ','hex'=>'F3C4','idx'=>0),
490=>array('bin'=>'�ﾅ','hex'=>'F3C5','idx'=>172),
491=>array('bin'=>'�ﾆ','hex'=>'F3C6','idx'=>0),
492=>array('bin'=>'�ﾇ','hex'=>'F3C7','idx'=>0),
493=>array('bin'=>'�ﾈ','hex'=>'F3C8','idx'=>0),
494=>array('bin'=>'�ﾉ','hex'=>'F3C9','idx'=>63),
495=>array('bin'=>'�ﾊ','hex'=>'F3CA','idx'=>0),
496=>array('bin'=>'�ﾋ','hex'=>'F3CB','idx'=>0),
497=>array('bin'=>'�ﾌ','hex'=>'F3CC','idx'=>0),
498=>array('bin'=>'�ﾍ','hex'=>'F3CD','idx'=>0),
499=>array('bin'=>'�ﾎ','hex'=>'F3CE','idx'=>0),
500=>array('bin'=>'�ﾒ','hex'=>'F7D2','idx'=>0),
501=>array('bin'=>'�ﾓ','hex'=>'F7D3','idx'=>0),
502=>array('bin'=>'�ﾔ','hex'=>'F7D4','idx'=>0),
503=>array('bin'=>'�ﾕ','hex'=>'F7D5','idx'=>0),
504=>array('bin'=>'�ﾖ','hex'=>'F7D6','idx'=>0),
505=>array('bin'=>'�ﾗ','hex'=>'F7D7','idx'=>0),
506=>array('bin'=>'�ﾘ','hex'=>'F7D8','idx'=>0),
507=>array('bin'=>'�ﾙ','hex'=>'F7D9','idx'=>0),
508=>array('bin'=>'�ﾚ','hex'=>'F7DA','idx'=>169),
509=>array('bin'=>'�ﾛ','hex'=>'F7DB','idx'=>0),
510=>array('bin'=>'�ﾜ','hex'=>'F7DC','idx'=>0),
511=>array('bin'=>'�ﾝ','hex'=>'F7DD','idx'=>0),
512=>array('bin'=>'�ﾞ','hex'=>'F7DE','idx'=>0),
513=>array('bin'=>'�ﾟ','hex'=>'F7DF','idx'=>105),
514=>array('bin'=>'��','hex'=>'F7E0','idx'=>0),
515=>array('bin'=>'��','hex'=>'F7E1','idx'=>0),
516=>array('bin'=>'��','hex'=>'F7E2','idx'=>0),
517=>array('bin'=>'��','hex'=>'F7E3','idx'=>0),
518=>array('bin'=>'��','hex'=>'F7E4','idx'=>0),
700=>array('bin'=>'�ﾏ','hex'=>'F3CF','idx'=>0),
701=>array('bin'=>'�ﾐ','hex'=>'F3D0','idx'=>0),
702=>array('bin'=>'�ﾑ','hex'=>'F3D1','idx'=>0),
703=>array('bin'=>'�ﾒ','hex'=>'F3D2','idx'=>0),
704=>array('bin'=>'�ﾓ','hex'=>'F3D3','idx'=>0),
705=>array('bin'=>'�ﾔ','hex'=>'F3D4','idx'=>0),
706=>array('bin'=>'�ﾕ','hex'=>'F3D5','idx'=>0),
707=>array('bin'=>'�ﾖ','hex'=>'F3D6','idx'=>0),
708=>array('bin'=>'�ﾗ','hex'=>'F3D7','idx'=>0),
709=>array('bin'=>'�ﾘ','hex'=>'F3D8','idx'=>0),
710=>array('bin'=>'�ﾙ','hex'=>'F3D9','idx'=>0),
711=>array('bin'=>'�ﾚ','hex'=>'F3DA','idx'=>0),
712=>array('bin'=>'�ﾛ','hex'=>'F3DB','idx'=>0),
713=>array('bin'=>'�ﾜ','hex'=>'F3DC','idx'=>0),
714=>array('bin'=>'�ﾝ','hex'=>'F3DD','idx'=>0),
715=>array('bin'=>'�ﾞ','hex'=>'F3DE','idx'=>0),
716=>array('bin'=>'�ﾟ','hex'=>'F3DF','idx'=>0),
717=>array('bin'=>'��','hex'=>'F3E0','idx'=>0),
718=>array('bin'=>'��','hex'=>'F3E1','idx'=>0),
719=>array('bin'=>'��','hex'=>'F3E2','idx'=>0),
720=>array('bin'=>'��','hex'=>'F3E3','idx'=>0),
721=>array('bin'=>'��','hex'=>'F3E4','idx'=>0),
722=>array('bin'=>'��','hex'=>'F3E5','idx'=>0),
723=>array('bin'=>'��','hex'=>'F3E6','idx'=>0),
724=>array('bin'=>'��','hex'=>'F3E7','idx'=>0),
725=>array('bin'=>'��','hex'=>'F3E8','idx'=>0),
726=>array('bin'=>'��','hex'=>'F3E9','idx'=>0),
727=>array('bin'=>'��','hex'=>'F3EA','idx'=>0),
728=>array('bin'=>'��','hex'=>'F3EB','idx'=>91),
729=>array('bin'=>'��','hex'=>'F3EC','idx'=>92),
730=>array('bin'=>'��','hex'=>'F3ED','idx'=>121),
731=>array('bin'=>'��','hex'=>'F3EE','idx'=>145),
732=>array('bin'=>'��','hex'=>'F3EF','idx'=>156),
733=>array('bin'=>'��','hex'=>'F3F0','idx'=>159),
734=>array('bin'=>'��','hex'=>'F3F1','idx'=>160),
735=>array('bin'=>'��','hex'=>'F3F2','idx'=>166),
736=>array('bin'=>'��','hex'=>'F3F3','idx'=>0),
737=>array('bin'=>'��','hex'=>'F3F4','idx'=>0),
738=>array('bin'=>'��','hex'=>'F3F5','idx'=>0),
739=>array('bin'=>'��','hex'=>'F3F6','idx'=>1057),
740=>array('bin'=>'��','hex'=>'F3F7','idx'=>0),
741=>array('bin'=>'��','hex'=>'F3F8','idx'=>0),
742=>array('bin'=>'��','hex'=>'F3F9','idx'=>0),
743=>array('bin'=>'��','hex'=>'F3FA','idx'=>0),
744=>array('bin'=>'��','hex'=>'F3FB','idx'=>0),
745=>array('bin'=>'��','hex'=>'F3FC','idx'=>0),
746=>array('bin'=>'�@','hex'=>'F440','idx'=>0),
747=>array('bin'=>'�A','hex'=>'F441','idx'=>0),
748=>array('bin'=>'�B','hex'=>'F442','idx'=>0),
749=>array('bin'=>'�C','hex'=>'F443','idx'=>0),
750=>array('bin'=>'�D','hex'=>'F444','idx'=>0),
751=>array('bin'=>'�E','hex'=>'F445','idx'=>0),
752=>array('bin'=>'�F','hex'=>'F446','idx'=>0),
753=>array('bin'=>'�G','hex'=>'F447','idx'=>0),
754=>array('bin'=>'�H','hex'=>'F448','idx'=>0),
755=>array('bin'=>'�I','hex'=>'F449','idx'=>0),
756=>array('bin'=>'�J','hex'=>'F44A','idx'=>0),
757=>array('bin'=>'�K','hex'=>'F44B','idx'=>0),
758=>array('bin'=>'�L','hex'=>'F44C','idx'=>0),
759=>array('bin'=>'�M','hex'=>'F44D','idx'=>0),
760=>array('bin'=>'�N','hex'=>'F44E','idx'=>0),
761=>array('bin'=>'�O','hex'=>'F44F','idx'=>0),
762=>array('bin'=>'�P','hex'=>'F450','idx'=>0),
763=>array('bin'=>'�Q','hex'=>'F451','idx'=>0),
764=>array('bin'=>'�R','hex'=>'F452','idx'=>0),
765=>array('bin'=>'�S','hex'=>'F453','idx'=>0),
766=>array('bin'=>'�T','hex'=>'F454','idx'=>0),
767=>array('bin'=>'�U','hex'=>'F455','idx'=>0),
768=>array('bin'=>'�V','hex'=>'F456','idx'=>0),
769=>array('bin'=>'�W','hex'=>'F457','idx'=>0),
770=>array('bin'=>'�X','hex'=>'F458','idx'=>0),
771=>array('bin'=>'�Y','hex'=>'F459','idx'=>0),
772=>array('bin'=>'�Z','hex'=>'F45A','idx'=>0),
773=>array('bin'=>'�[','hex'=>'F45B','idx'=>0),
774=>array('bin'=>'�\\','hex'=>'F45C','idx'=>0),
775=>array('bin'=>'�]','hex'=>'F45D','idx'=>0),
776=>array('bin'=>'�^','hex'=>'F45E','idx'=>0),
777=>array('bin'=>'�_','hex'=>'F45F','idx'=>0),
778=>array('bin'=>'�`','hex'=>'F460','idx'=>0),
779=>array('bin'=>'�a','hex'=>'F461','idx'=>1025),
780=>array('bin'=>'�b','hex'=>'F462','idx'=>0),
781=>array('bin'=>'�c','hex'=>'F463','idx'=>0),
782=>array('bin'=>'�d','hex'=>'F464','idx'=>0),
783=>array('bin'=>'�e','hex'=>'F465','idx'=>0),
784=>array('bin'=>'�f','hex'=>'F466','idx'=>106),
785=>array('bin'=>'�g','hex'=>'F467','idx'=>0),
786=>array('bin'=>'�h','hex'=>'F468','idx'=>0),
787=>array('bin'=>'�i','hex'=>'F469','idx'=>0),
788=>array('bin'=>'�j','hex'=>'F46A','idx'=>0),
789=>array('bin'=>'�k','hex'=>'F46B','idx'=>0),
790=>array('bin'=>'�l','hex'=>'F46C','idx'=>0),
791=>array('bin'=>'�m','hex'=>'F46D','idx'=>1035),
792=>array('bin'=>'�n','hex'=>'F46E','idx'=>0),
793=>array('bin'=>'�o','hex'=>'F46F','idx'=>0),
794=>array('bin'=>'�p','hex'=>'F470','idx'=>0),
795=>array('bin'=>'�q','hex'=>'F471','idx'=>0),
796=>array('bin'=>'�r','hex'=>'F472','idx'=>0),
797=>array('bin'=>'�s','hex'=>'F473','idx'=>0),
798=>array('bin'=>'�t','hex'=>'F474','idx'=>0),
799=>array('bin'=>'�u','hex'=>'F475','idx'=>0),
800=>array('bin'=>'�v','hex'=>'F476','idx'=>0),
801=>array('bin'=>'�w','hex'=>'F477','idx'=>0),
802=>array('bin'=>'�x','hex'=>'F478','idx'=>0),
803=>array('bin'=>'�y','hex'=>'F479','idx'=>137),
804=>array('bin'=>'�z','hex'=>'F47A','idx'=>0),
805=>array('bin'=>'�{','hex'=>'F47B','idx'=>1006),
806=>array('bin'=>'�|','hex'=>'F47C','idx'=>1012),
807=>array('bin'=>'�}','hex'=>'F47D','idx'=>1042),
808=>array('bin'=>'�~','hex'=>'F47E','idx'=>1049),
809=>array('bin'=>'��','hex'=>'F480','idx'=>1050),
810=>array('bin'=>'��','hex'=>'F481','idx'=>1052),
811=>array('bin'=>'��','hex'=>'F482','idx'=>1059),
812=>array('bin'=>'��','hex'=>'F483','idx'=>1067),
813=>array('bin'=>'��','hex'=>'F484','idx'=>0),
814=>array('bin'=>'��','hex'=>'F485','idx'=>1072),
815=>array('bin'=>'��','hex'=>'F486','idx'=>0),
816=>array('bin'=>'��','hex'=>'F487','idx'=>0),
817=>array('bin'=>'��','hex'=>'F488','idx'=>86),
818=>array('bin'=>'��','hex'=>'F489','idx'=>123),
819=>array('bin'=>'��','hex'=>'F48A','idx'=>0),
820=>array('bin'=>'��','hex'=>'F48B','idx'=>0),
821=>array('bin'=>'��','hex'=>'F48C','idx'=>0),
822=>array('bin'=>'��','hex'=>'F48D','idx'=>0),
	);

}

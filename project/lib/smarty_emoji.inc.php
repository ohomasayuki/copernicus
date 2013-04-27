<?php
/*
* 絵文字出力をするSmartyカスタム関数
* id属性は id="123:docomo" のように指定、キャリアを指定しない場合はDoCoMoのものが使われる。
*/

/**
 * ・Smartyにキャリアに対応した絵文字を出力する
 *   idが渡されたらそれにしたがって書くキャリアに対応した絵文字を出力する。
 *
 * @param mixed     $param    関数に渡されたパラメータ(id)
 * @param object    $smarty   Smartyオブジェクトのリファレンス
 *
 * @return string          説明
 */
function function_emoji($params)
{
	$code = $params["code"];
	return convert_emoji($code);

}

/**
 *   関数名    ：outputfilter_emoji()
 *   概要      ：絵文字変換
 *   パラメータ：$template_source
 *   戻り値    ：変換後ソース
 */
function outputfilter_emoji($source) {
//echo"outputfilter_emoji in<br>";
	$source = preg_replace_callback ( "/<\%EMOJI::([0-9]+)\%>/", "preg_emoji", $source);
        return $source;
}

/**
 *   関数名    ：covert_emoji()
 *   概要      ：絵文字変換
 *   パラメータ：$template_source
 *   戻り値    ：変換後ソース
 */
function covert_emoji($source) {
//echo"outputfilter_emoji in<br>";
	$source = preg_replace_callback ( "/<\%EMOJI::([0-9]+)\%>/", "preg_emoji_bef", $source);
        return $source;
}
function preg_emoji_bef($arr) {
//echo"outputfilter_emoji in<br>";
	$source = preg_emoji ( $arr[1]);
        return $source;
}

function preg_emoji($emoji_arr)
{
  //var_dump($emoji_arr);
  $index=$emoji_arr[0];
  //$convert_flag=$emoji_arr[1];
  //echo"conv_emoji in index($index)<br>";
  global $carrier_id;
  /*
  if( !is_numeric($index) or $index < 1 or $index > 9999 ) {
    $smarty->trigger_error("[smarty_funtion_emoji] Attribute \"code\" is incompleted or wrong format.[{$index}]");
    return("");
  }
  */
  global $is_emoji_tag;
  for($i=1;$i<count($emoji_arr);$i++) {
    $para = $emoji_arr[$i];
    if( $para == 'intag' and $carrier_id == 'e' and $is_emoji_tag == TRUE ) {
      return("");
    }
  }
  return(convert_emoji($index));
}

function convert_emoji($index)
{
//echo"convert_emoji in index($index)<br>";
global $carrier_id;
$carr_id = $carrier_id;
//if( is_admin() )	$carr_id = 'i';
//__log("carr_id($carr_id)");
$emoji_text = array( 

1 => array('i'=>'E63E', 'e'=>'44','s'=>'Gj'),
2 => array('i'=>'E63F', 'e'=>'107','s'=>'Gi'),
3 => array('i'=>'E640', 'e'=>'95','s'=>'Gk'),
4 => array('i'=>'E641', 'e'=>'191','s'=>'Gh'),
5 => array('i'=>'E642', 'e'=>'16','s'=>'E]'),
6 => array('i'=>'E643', 'e'=>'190','s'=>'Pc'),
7 => array('i'=>'E644', 'e'=>'305','s'=>NULL),
8 => array('i'=>'E645', 'e'=>'481','s'=>'P\\'),
9 => array('i'=>'E646', 'e'=>'192','s'=>'F_'),
2 => array('i'=>'E647', 'e'=>'193','s'=>'F`'),
11 => array('i'=>'E648', 'e'=>'194','s'=>'Fa'),
12 => array('i'=>'E649', 'e'=>'195','s'=>'Fb'),
13 => array('i'=>'E64A', 'e'=>'196','s'=>'Fc'),
14 => array('i'=>'E64B', 'e'=>'197','s'=>'Fd'),
15 => array('i'=>'E64C', 'e'=>'198','s'=>'Fe'),
16 => array('i'=>'E64D', 'e'=>'199','s'=>'Ff'),
17 => array('i'=>'E64E', 'e'=>'200','s'=>'Fg'),
18 => array('i'=>'E64F', 'e'=>'201','s'=>'Fh'),
19 => array('i'=>'E650', 'e'=>'202','s'=>'Fi'),
20 => array('i'=>'E651', 'e'=>'203','s'=>'Fj'),
21 => array('i'=>'E652', 'e'=>'335','s'=>'O9'),
22 => array('i'=>'E653', 'e'=>'45','s'=>'G6'),
23 => array('i'=>'E654', 'e'=>'306','s'=>'G4'),
24 => array('i'=>'E655', 'e'=>'220','s'=>'G5'),
25 => array('i'=>'E656', 'e'=>'219','s'=>'G8'),
26 => array('i'=>'E657', 'e'=>'421','s'=>'G3'),
27 => array('i'=>'E658', 'e'=>'307','s'=>'PJ'),
28 => array('i'=>'E659', 'e'=>'222','s'=>'ER'),
29 => array('i'=>'E65A', 'e'=>'308','s'=>NULL),
30 => array('i'=>'E65B', 'e'=>'172','s'=>'G>'),
31 => array('i'=>'E65C', 'e'=>'341','s'=>'PT'),
32 => array('i'=>'E65D', 'e'=>'217','s'=>'G?'),
33 => array('i'=>'E65E', 'e'=>'125','s'=>'G;'),
34 => array('i'=>'E65F', 'e'=>'125','s'=>'PN'),
35 => array('i'=>'E660', 'e'=>'216','s'=>'Ey'),
36 => array('i'=>'E661', 'e'=>'379','s'=>'F"'),
37 => array('i'=>'E662', 'e'=>'168','s'=>'G='),
38 => array('i'=>'E663', 'e'=>'112','s'=>'GV'),
39 => array('i'=>'E664', 'e'=>'156','s'=>'GX'),
40 => array('i'=>'E665', 'e'=>'375','s'=>'E!'),
41 => array('i'=>'E666', 'e'=>'376','s'=>'Eu'),
42 => array('i'=>'E667', 'e'=>'212','s'=>'Em'),
43 => array('i'=>'E668', 'e'=>'205','s'=>'Et'),
44 => array('i'=>'E669', 'e'=>'378','s'=>'Ex'),
45 => array('i'=>'E66A', 'e'=>'206','s'=>'Ev'),
46 => array('i'=>'E66B', 'e'=>'213','s'=>'GZ'),
47 => array('i'=>'E66C', 'e'=>'208','s'=>'Eo'),
48 => array('i'=>'E66D', 'e'=>'99','s'=>'En'),
49 => array('i'=>'E66E', 'e'=>'207','s'=>'PH'),
50 => array('i'=>'E66F', 'e'=>'146','s'=>'Gc'),
51 => array('i'=>'E670', 'e'=>'93','s'=>'Ge'),
52 => array('i'=>'E671', 'e'=>'52','s'=>'Gd'),
53 => array('i'=>'E672', 'e'=>'65','s'=>'Gg'),
54 => array('i'=>'E673', 'e'=>'245','s'=>'E@'),
55 => array('i'=>'E674', 'e'=>'124','s'=>'E^'),
56 => array('i'=>'E675', 'e'=>'104','s'=>'O3'),
57 => array('i'=>'E676', 'e'=>'289','s'=>'G\\'),
58 => array('i'=>'E677', 'e'=>'110','s'=>'G]'),
59 => array('i'=>'E678', 'e'=>'70','s'=>'FV'),
60 => array('i'=>'E679', 'e'=>NULL,'s'=>NULL),
61 => array('i'=>'E67A', 'e'=>'294','s'=>'O*'),
62 => array('i'=>'E67B', 'e'=>'309','s'=>'Q"'),
63 => array('i'=>'E67C', 'e'=>'494','s'=>'Q#'),
64 => array('i'=>'E67D', 'e'=>'311','s'=>'ER'),
65 => array('i'=>'E67E', 'e'=>'106','s'=>'EE'),
66 => array('i'=>'E67F', 'e'=>'176','s'=>'O.'),
67 => array('i'=>'E680', 'e'=>'177','s'=>'F('),
68 => array('i'=>'E681', 'e'=>'94','s'=>'G('),
69 => array('i'=>'E682', 'e'=>'83','s'=>'E>'),
70 => array('i'=>'E683', 'e'=>'122','s'=>'Eh'),
71 => array('i'=>'E684', 'e'=>'312','s'=>'O4'),
72 => array('i'=>'E685', 'e'=>'144','s'=>'E2'),
73 => array('i'=>'E686', 'e'=>'313','s'=>'Ok'),
74 => array('i'=>'E687', 'e'=>'85','s'=>'G)'),
75 => array('i'=>'E688', 'e'=>'161','s'=>'G*'),
76 => array('i'=>'E689', 'e'=>'395','s'=>'Eh'),
77 => array('i'=>'E68A', 'e'=>'288','s'=>'EJ'),
78 => array('i'=>'E68B', 'e'=>'232','s'=>'EK'),
79 => array('i'=>'E68C', 'e'=>'300','s'=>'EF'),
80 => array('i'=>'E68D', 'e'=>'414','s'=>'F'),
81 => array('i'=>'E68E', 'e'=>'314','s'=>'F.'),
82 => array('i'=>'E68F', 'e'=>'315','s'=>'F-'),
83 => array('i'=>'E690', 'e'=>'316','s'=>'F/'),
84 => array('i'=>'E691', 'e'=>'317','s'=>'P9'),
85 => array('i'=>'E692', 'e'=>'318','s'=>'P;'),
86 => array('i'=>'E693', 'e'=>'817','s'=>'G0'),
87 => array('i'=>'E694', 'e'=>'319','s'=>'G1'),
88 => array('i'=>'E695', 'e'=>'320','s'=>'G2'),
89 => array('i'=>'E696', 'e'=>'43','s'=>'FX'),
90 => array('i'=>'E697', 'e'=>'42','s'=>'FW'),
91 => array('i'=>'E698', 'e'=>'728','s'=>'QV'),
92 => array('i'=>'E699', 'e'=>'729','s'=>'G\''),
93 => array('i'=>'E69A', 'e'=>'116','s'=>NULL),
94 => array('i'=>'E69B', 'e'=>'178','s'=>'F*'),
95 => array('i'=>'E69C', 'e'=>'321','s'=>'F9'),
96 => array('i'=>'E69D', 'e'=>'322','s'=>NULL),
97 => array('i'=>'E69E', 'e'=>'323','s'=>NULL),
98 => array('i'=>'E69F', 'e'=>'15','s'=>'Gl'),
99 => array('i'=>'E6A0', 'e'=>NULL,'s'=>'OR'),
20 => array('i'=>'E6A1', 'e'=>'134','s'=>'Gr'),
21 => array('i'=>'E6A2', 'e'=>'251','s'=>'Go'),
22 => array('i'=>'E6A3', 'e'=>'169','s'=>'G<'),
23 => array('i'=>'E6A4', 'e'=>'234','s'=>'GS'),
24 => array('i'=>'E6A5', 'e'=>'71','s'=>'FY'),
25 => array('i'=>'E6CE', 'e'=>'513','s'=>'E$'),
26 => array('i'=>'E6CF', 'e'=>'784','s'=>'E#'),
27 => array('i'=>'E6D0', 'e'=>'166','s'=>'G+'),
28 => array('i'=>'E6D1', 'e'=>NULL,'s'=>NULL),
29 => array('i'=>'E6D2', 'e'=>NULL,'s'=>NULL),
110 => array('i'=>'E6D3', 'e'=>'108','s'=>'E#'),
111 => array('i'=>'E6D4', 'e'=>NULL,'s'=>NULL),
112 => array('i'=>'E6D5', 'e'=>NULL,'s'=>NULL),
113 => array('i'=>'E6D6', 'e'=>'109','s'=>NULL),
114 => array('i'=>'E6D7', 'e'=>'299','s'=>'F6'),
115 => array('i'=>'E6D8', 'e'=>'385','s'=>'FI'),
116 => array('i'=>'E6D9', 'e'=>'120','s'=>'G_'),
117 => array('i'=>'E6DA', 'e'=>'118','s'=>'Fl'),
118 => array('i'=>'E6DB', 'e'=>'324','s'=>NULL),
119 => array('i'=>'E6DC', 'e'=>'119','s'=>'E4'),
120 => array('i'=>'E6DD', 'e'=>'334','s'=>'F2'),
121 => array('i'=>'E6DE', 'e'=>'730','s'=>NULL),
122 => array('i'=>'E6DF', 'e'=>NULL,'s'=>'F1'),
123 => array('i'=>'E6E0', 'e'=>'818','s'=>'F0'),
124 => array('i'=>'E6E1', 'e'=>'4','s'=>'G@'),
125 => array('i'=>'E6E2', 'e'=>'180','s'=>'F<'),
126 => array('i'=>'E6E3', 'e'=>'181','s'=>'F='),
127 => array('i'=>'E6E4', 'e'=>'182','s'=>'F>'),
128 => array('i'=>'E6E5', 'e'=>'183','s'=>'F?'),
129 => array('i'=>'E6E6', 'e'=>'184','s'=>'F@'),
130 => array('i'=>'E6E7', 'e'=>'185','s'=>'FA'),
131 => array('i'=>'E6E8', 'e'=>'186','s'=>'FB'),
132 => array('i'=>'E6E9', 'e'=>'187','s'=>'FC'),
133 => array('i'=>'E6EA', 'e'=>'188','s'=>'FD'),
134 => array('i'=>'E6EB', 'e'=>'325','s'=>'FE'),
135 => array('i'=>'E70B', 'e'=>'326','s'=>'Fm'),
136 => array('i'=>'E6EC', 'e'=>'51','s'=>'GB'),
137 => array('i'=>'E6ED', 'e'=>'803','s'=>'OH'),
138 => array('i'=>'E6EE', 'e'=>'265','s'=>'GC'),
139 => array('i'=>'E6EF', 'e'=>'266','s'=>'OG'),
140 => array('i'=>'E6F0', 'e'=>'257','s'=>'G!'),
141 => array('i'=>'E6F1', 'e'=>'258','s'=>'Gy'),
142 => array('i'=>'E6F2', 'e'=>'441','s'=>'Gx'),
143 => array('i'=>'E6F3', 'e'=>'444','s'=>'P\''),
144 => array('i'=>'E6F4', 'e'=>'327','s'=>'P0'),
145 => array('i'=>'E6F5', 'e'=>'731','s'=>'FV'),
146 => array('i'=>'E6F6', 'e'=>'343','s'=>'G^'),
147 => array('i'=>'E6F7', 'e'=>'224','s'=>'EC'),
148 => array('i'=>'E6F8', 'e'=>NULL,'s'=>'F$'),
149 => array('i'=>'E6F9', 'e'=>'273','s'=>'G#'),
150 => array('i'=>'E6FA', 'e'=>'420','s'=>'ON'),
151 => array('i'=>'E6FB', 'e'=>'77','s'=>'E/'),
152 => array('i'=>'E6FC', 'e'=>'262','s'=>'OT'),
153 => array('i'=>'E6FD', 'e'=>'281','s'=>'G-'),
154 => array('i'=>'E6FE', 'e'=>'268','s'=>'O1'),
155 => array('i'=>'E6FF', 'e'=>'291','s'=>'OF'),
156 => array('i'=>'E700', 'e'=>'732','s'=>'PA'),
157 => array('i'=>'E701', 'e'=>'261','s'=>'E\\'),
158 => array('i'=>'E702', 'e'=>'2','s'=>'GA'),
159 => array('i'=>'E703', 'e'=>'733','s'=>NULL),
160 => array('i'=>'E704', 'e'=>'734','s'=>'GA'),
161 => array('i'=>'E705', 'e'=>'329','s'=>NULL),
162 => array('i'=>'E706', 'e'=>'330','s'=>'OQ'),
163 => array('i'=>'E707', 'e'=>'263','s'=>NULL),
164 => array('i'=>'E708', 'e'=>'282','s'=>'OP'),
165 => array('i'=>'E709', 'e'=>NULL,'s'=>NULL),
166 => array('i'=>'E70A', 'e'=>'735','s'=>NULL),
167 => array('i'=>'E6AC', 'e'=>'226','s'=>'OD'),
168 => array('i'=>'E6AD', 'e'=>NULL,'s'=>NULL),
169 => array('i'=>'E6AE', 'e'=>'508','s'=>'O!'),
170 => array('i'=>'E6B1', 'e'=>'80','s'=>'G!'),
171 => array('i'=>'E6B2', 'e'=>NULL,'s'=>'E?'),
172 => array('i'=>'E6B3', 'e'=>'490','s'=>'Pk'),
173 => array('i'=>'E6B7', 'e'=>NULL,'s'=>NULL),
174 => array('i'=>'E6B8', 'e'=>NULL,'s'=>NULL),
175 => array('i'=>'E6B9', 'e'=>NULL,'s'=>NULL),
176 => array('i'=>'E6BA', 'e'=>'46','s'=>'GD'),
201 => array('i'=>'E70C', 'e'=>NULL,'s'=>NULL),
202 => array('i'=>'E70D', 'e'=>NULL,'s'=>NULL),
203 => array('i'=>'E70E', 'e'=>'335','s'=>'G&'),
204 => array('i'=>'E70F', 'e'=>'290','s'=>NULL),
205 => array('i'=>'E710', 'e'=>'295','s'=>'O<'),
206 => array('i'=>'E711', 'e'=>'805','s'=>NULL),
207 => array('i'=>'E712', 'e'=>'221','s'=>'G7'),
208 => array('i'=>'E713', 'e'=>'48','s'=>'OE'),
209 => array('i'=>'E714', 'e'=>NULL,'s'=>NULL),
210 => array('i'=>'E715', 'e'=>'233','s'=>'EO'),
211 => array('i'=>'E716', 'e'=>'337','s'=>'G'),
212 => array('i'=>'E717', 'e'=>'806','s'=>'E#'),
213 => array('i'=>'E718', 'e'=>'152','s'=>'E6'),
214 => array('i'=>'E719', 'e'=>'149','s'=>'O!'),
215 => array('i'=>'E71A', 'e'=>'354','s'=>'GQ'),
216 => array('i'=>'E71B', 'e'=>'72','s'=>'GT'),
217 => array('i'=>'E71C', 'e'=>'58','s'=>NULL),
218 => array('i'=>'E71D', 'e'=>'215','s'=>'EV'),
219 => array('i'=>'E71E', 'e'=>'423','s'=>'OX'),
220 => array('i'=>'E71F', 'e'=>'25','s'=>NULL),
221 => array('i'=>'E720', 'e'=>'441','s'=>'P#'),
222 => array('i'=>'E721', 'e'=>'446','s'=>'P*'),
223 => array('i'=>'E722', 'e'=>NULL,'s'=>NULL),
224 => array('i'=>'E723', 'e'=>'351','s'=>'E('),
225 => array('i'=>'E724', 'e'=>'779','s'=>'P6'),
226 => array('i'=>'E725', 'e'=>'450','s'=>'P.'),
227 => array('i'=>'E726', 'e'=>'349','s'=>'E&'),
228 => array('i'=>'E727', 'e'=>'287','s'=>'G.'),
229 => array('i'=>'E728', 'e'=>'264','s'=>'E%'),
230 => array('i'=>'E729', 'e'=>'348','s'=>'P%'),
231 => array('i'=>'E72A', 'e'=>'446','s'=>'P-'),
232 => array('i'=>'E72B', 'e'=>'443','s'=>'P&'),
233 => array('i'=>'E72C', 'e'=>'440','s'=>'P"'),
234 => array('i'=>'E72D', 'e'=>'259','s'=>'P1'),
235 => array('i'=>'E72E', 'e'=>'791','s'=>'P3'),
236 => array('i'=>'E72F', 'e'=>NULL,'s'=>'PC'),
237 => array('i'=>'E730', 'e'=>'143','s'=>NULL),
238 => array('i'=>'E731', 'e'=>'81','s'=>'Fn'),
239 => array('i'=>'E732', 'e'=>'54','s'=>'QW'),
240 => array('i'=>'E733', 'e'=>'218','s'=>'E5'),
241 => array('i'=>'E734', 'e'=>'279','s'=>'O5'),
242 => array('i'=>'E735', 'e'=>'807','s'=>NULL),
243 => array('i'=>'E736', 'e'=>'82','s'=>'Fo'),
244 => array('i'=>'E737', 'e'=>'1','s'=>'Fr'),
245 => array('i'=>'E738', 'e'=>NULL,'s'=>NULL),
246 => array('i'=>'E739', 'e'=>'387','s'=>'FK'),
247 => array('i'=>'E73A', 'e'=>NULL,'s'=>NULL),
248 => array('i'=>'E73B', 'e'=>'386','s'=>'FJ'),
249 => array('i'=>'E73C', 'e'=>'808','s'=>NULL),
250 => array('i'=>'E73D', 'e'=>'809','s'=>NULL),
251 => array('i'=>'E73E', 'e'=>'377','s'=>'Ew'),
252 => array('i'=>'E73F', 'e'=>'810','s'=>'P^'),
253 => array('i'=>'E740', 'e'=>'342','s'=>'G['),
254 => array('i'=>'E741', 'e'=>'53','s'=>'E0'),
255 => array('i'=>'E742', 'e'=>'241','s'=>NULL),
256 => array('i'=>'E743', 'e'=>'113','s'=>'O$'),
257 => array('i'=>'E744', 'e'=>'739','s'=>NULL),
258 => array('i'=>'E745', 'e'=>'434','s'=>'Oe'),
259 => array('i'=>'E746', 'e'=>'811','s'=>NULL),
260 => array('i'=>'E747', 'e'=>'133','s'=>'E8'),
261 => array('i'=>'E748', 'e'=>'235','s'=>'GP'),
262 => array('i'=>'E749', 'e'=>'244','s'=>'Ob'),
263 => array('i'=>'E74A', 'e'=>'239','s'=>'Gf'),
264 => array('i'=>'E74B', 'e'=>'400','s'=>'O+'),
265 => array('i'=>'E74C', 'e'=>'333','s'=>'O^'),
266 => array('i'=>'E74D', 'e'=>'424','s'=>'OY'),
267 => array('i'=>'E74E', 'e'=>'812','s'=>NULL),
268 => array('i'=>'E74F', 'e'=>'78','s'=>'QA'),
269 => array('i'=>'E750', 'e'=>'252','s'=>'Gu'),
270 => array('i'=>'E751', 'e'=>'203','s'=>'G9'),
271 => array('i'=>'E752', 'e'=>'454','s'=>NULL),
272 => array('i'=>'E753', 'e'=>'814','s'=>'P$'),
273 => array('i'=>'E754', 'e'=>'248','s'=>'G:'),
274 => array('i'=>'E755', 'e'=>'254','s'=>'E+'),
275 => array('i'=>'E756', 'e'=>'12','s'=>NULL),
276 => array('i'=>'E757', 'e'=>'350','s'=>'E\''),
// コード登録絵文字
'myroom' => array('i'=>'E663', 'e'=>'112','s'=>'GV'),		// マイルーム
'profile' => array('i'=>'E689', 'e'=>'395','s'=>'Eh'),		// プロフィール
'member' => array('i'=>'E6B1', 'e'=>'80','s'=>'G!'),		// 会員
'community' => array('i'=>'E66E', 'e'=>'207','s'=>'PH'),	// コミュニティ
'diary' => array('i'=>'E6AE', 'e'=>'508','s'=>'O!'),		// 日記
'mail' => array('i'=>'E6D3', 'e'=>'108','s'=>'E#'),		// メール
'album' => array('i'=>'E683', 'e'=>'122','s'=>'Eh'),		// アルバム
'ranking' => array('i'=>'E71A', 'e'=>'354','s'=>'GQ'),		// ランキング
'comment' => array('i'=>'E6F0', 'e'=>'257','s'=>'G!'),		// コメント
'picture' => array('i'=>'E681', 'e'=>'94','s'=>'G('),		// 画像
'search' => array('i'=>'E6DC', 'e'=>'119','s'=>'E4'),		// 検索
'new' => array('i'=>'E6DD', 'e'=>'334','s'=>'F2'),		// new
'back' => array('i'=>'E6DA', 'e'=>'118','s'=>'Fl'),		// 戻る
'nodata' => array('i'=>'E6B3', 'e'=>'490','s'=>'Pk'),		// 休止、なしなど
	);
$carr_id="i";
	if(!is_null($emoji_text[$index][$carr_id])) {
		$code = $emoji_text[$index][$carr_id];
	} elseif(is_array($emoji_text[$index])) {
		return NO_CONVERT_EMOJI_STRING; //変換対象なし文字
	} else {
		return FALSE;
	}
	//if( ! $code ) return;
//echo"code($code)<br>";
//echo"carrier_id($carrier_id)<br>";
//echo"index({$emoji_text[$index][$carrier_id]})<br>";

	switch ($carr_id) {
	 case "e":
//__log("ez index($index) code({$emoji_text[$index][$carrier_id]})");
//			$code = $emoji_text[$index]['i'];
//			$hex1 = substr($code, 0, 2);     /* F8を取得 */
//			$hex2 = substr($code, 2, 2);     /* 9Fを取得 */

			/* F8,F9を10進数に変換し、pack関数でbinaryに変換する */
//			$binary = pack("c*", hexdec($hex1), hexdec($hex2));
//			return $binary;
			return "<img localsrc=\"{$code}\" />";
		break;
	
	 case "s":
			return "\x1B\$".$code."\x0F";
		break;
	
	 case "i":
	 case "":
	 default:
		//return "<%EMOJI::{$code}%>";
		//return "&#$code;";
		//return "&#x".pack('d', substr($code, 1, 2)) . pack('d', substr($code, 1, 2));
//__log("docomo index($index) code({$emoji_text[$index][$carrier_id]})");
		return "&#x$code;";
		break;
	}
}


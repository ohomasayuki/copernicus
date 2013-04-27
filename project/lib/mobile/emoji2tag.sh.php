<?
error_reporting(E_ALL);
require 'emoji_sjis.inc.php';
mb_internal_encoding('Shift_JIS');
list($emoji_uni, $emoji_tag) = loadEmojiArray();
$text = file_get_contents('/tmp/index.xhtml');
$text = docomoEmojiToTag($text); // 高精度なドコモバイナリ絵文字変換
echo $text;

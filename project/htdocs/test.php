<?
require_once 'common.inc.php';

//debug_setDisplay(0);
//debug_setComment(0);
$d = getDebugger();
echo $d->is_display_off;
echo $d->is_comment;
//echo $d->is_log;
debug("test");


var_dump($req);
$smarty->assign('v',$req['v']);
finish();


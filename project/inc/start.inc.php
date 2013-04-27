<?
/**
 * 閲覧画面の共通処理
 */
require_once dirname(__FILE__).'/core.inc.php';
$g->g['template_file'] = getStaticTemplateFile();
$g->PATH['templates'] = dirname($g->g['template_file']).'/';
$g->THIS_PAGE = getThisPage();
$smarty->assign('THIS_PAGE', $g->THIS_PAGE);
$smarty->assign('OUTPUT_ENCODING', $g->g['OUTPUT_ENCODING']);
//-----------------------
// 共通処理
//-----------------------
$g->g['useragent'] = $_SERVER['HTTP_USER_AGENT'];
$g->g['carrier'] = getCarrier($g->g['useragent']);
// コンテントヘッダ出力
//debug_setComment(1);
debug_setDisplay(1);
// ここまで機種判定
header('Content-type: text/html; charset='.$g->g['OUTPUT_ENCODING']);
//$g->g['uid'] = getCarrierUid();
//$smarty->assign('CARRIER', $g->g['carrier']);
$smarty->assign('SERVER_NAME', $_SERVER['SERVER_NAME']);
$smarty->assign('ERROR_MESSAGE_TPL','./error.inc.tpl');
$g->g['FOOTER_FILE'] = './footer.inc.tpl';
// テンプレートディレクトリ切替
$smarty->template_dir .= 'html/';
debug('templates_dir=['.$smarty->template_dir.'] file=['.$g->g['template_file'].']');
/*if( isHitaiou() ){
  //非対応機種
  debug('HITAIOU: carrier=['.$g->g['carrier'].'] ua=[' . $g->g['useragent'] . '] short['.$g->g['short_useragent'].']');
  $smarty->display('hitaiou.tpl');
  exit;
}*/
debug('start end');

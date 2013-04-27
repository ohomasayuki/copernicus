<?
global $smarty, $g;
$smarty->assign('error', $g->error);
$smarty->assign('report', $g->report);
$smarty->display('./header.inc.tpl');
debug($smarty->template_dir . $g->g['template_file'], 'tpl');
$smarty->display($g->g['template_file']);
if( $g->g['FOOTER_FILE'] ){
	$smarty->display($g->g['FOOTER_FILE']);
}
debug('take time is '.sprintf("%.5f", (microtime_float() - $g->g['start_time'])) . " sec");
exit;


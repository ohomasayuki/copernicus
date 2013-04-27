<?
// アプリ用共通ライブラリ

/**
 * サイトに対応している端末か判定
 *@return boolean 対応していればtrue→逆。非対応機種であればtrue。
 */
function isHitaiou()
{
  global $g;
  // docomo一部非対応機種
  $docomo_HITAIOU = array(
  'F900i',
  'F900iC',
  'F900iT',
  'F901iS',
  'N900iL',
  'N900iS',
  'P900i',
  'P900iV',
  'P905iTV',
  'D900i',
  'SA700iS',
  'SA702i',
  'L705i',
  'L706ie',
  'NM706i'
  );
  if(in_array($g->g['short_useragent'], $docomo_HITAIOU)){return true;}
  if( $g->g['carrier'] == 'i' && preg_match('/90[0-9]i/',$g->g['short_useragent']) || preg_match('/70[0-9]i/',$g->g['short_useragent']) ){
    // ドコモの90Xiシリーズなら対応端末
    return false;
  }
  return true;
}
function logDownload($bc_id){
	global $db, $g;
	$day = date('Y-m-d', $g->getNow() );
	$db->query('insert into log_down (day, broadcast_id, carrier, count)values(:day, :bc_id, :carrier, 1) on duplicate key update count = count + 1', array('carrier'=>$g->g['carrier'], 'bc_id'=>$bc_id, 'day'=>$day) );
	logCommon('movie_down', $bc_id);
}
function logWellcome($param){
	global $db, $g;
	if(!$param){$param=0;}
	$day = date('Y-m-d', $g->getNow() );
	$db->query('insert into log_wellcome (day, param, carrier, count)values(:day, :param, :carrier, 1) on duplicate key update count = count + 1', array('carrier'=>$g->g['carrier'], 'param'=>$param, 'day'=>$day) );
	logCommon('affiliate', $param);
}
function logPv($bc_id){
	global $db, $g;
	$day = date('Y-m-d', $g->getNow() );
	$db->query('insert into log_pv (day, broadcast_id, carrier, count)values(:day, :bc_id, :carrier, 1) on duplicate key update count = count + 1', array('carrier'=>$g->g['carrier'], 'bc_id'=>$bc_id, 'day'=>$day) );
	logCommon('movie_page', $bc_id);
}
function logCommon($type, $file_id=0){
	global $db, $g;
	$h['day']     = date('Y-m-d', $g->getNow() );
	$h['file_id'] = $file_id;
	$h['carrier'] = $g->g['carrier'];
	$h['type']    = $type;
	$h['uid']     = $g->g['uid'];
	// null にするとユニーク制限から外れるので0にする
	if( ! $h['uid'] ) $h['uid'] = 0;
	if( ! $h['file_id'] ) $h['file_id'] = 0;
	return $db->query('insert into log_common (day, file_id, carrier, uid, type, cnt)values(:day, :file_id, :carrier, :uid, :type, 1) on duplicate key update cnt = cnt + 1', $h);
}

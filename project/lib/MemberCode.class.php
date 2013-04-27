<?
class MemberCode {

/**
 * 端末毎にユニークなーキーを取得
 * 未登録なら作成
 * メルマガ登録用
 ex)
$param = this();
$param['uid']; // uid
$param['uid']; //
 */
function getOrCreateCode(){
  global $g,$db;
  $uid = getCarrierUid();
  // あれば取得
  $row = $db->select1('select * from member_code where uid=:uid order by id desc',array('uid'=>$uid));
  if( $row ){
    return $row['code'];
  }
  // なければ作成
  $code = md5($uid);
  $h = array('code'=>$code,'uid'=>$uid,'created'=>'now()','useragent'=>$g->g['useragent'],'carrier'=>$g->g['carrier']);
  $db->insert('member_code',$h);
  return $code;
}

}


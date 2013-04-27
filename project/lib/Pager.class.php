<?
/** 
 * ページング用ライブラリ
例)
$pager = new Pager(); // ページングクラス
// select対象,テーブル名～limitまで、,from,limit以降を除いた箇所
$sql_count = "id"; // countに使用するカラム(デフォルトは*)
$d = $pager->select('id,name,publish_date', 'broadcast where publish_date <= :date order by publish_date', array('date'=>'2008-06-01'), $sql_count);
//発行されるSQL
//"select id,name,publish_date from broadcast where publish_date <= now() order by publish_date limit xx offset xx"
//"select count(id) from broad..." 
$pager->count; // ページ中以外を含めた全hit件数 184など
$smarty->assign_by_ref('pager',$pager);// オブジェクトを割り当てる
-- template --
[:if $pager->hasPrev():]<a href="[:$pager->getPrevUrl():]">前へ</a>[:/if:]
[:foreach item=page from=$pager->pages:]
  <a href="[:$pager->getUrl($page):]">[:$page:]</a>
[:/foreach:]
[:if $pager->hasNext():]<a href="[:$pager->getNextUrl():]">次へ</a>[:/if:]
*/
class Pager{
  var $offset; // 取得開始件数(0～)
  var $limit; // 1ページあたりの最大件数
  var $count; // 総ヒット件数
  var $def_limit = 20; // デフォルトの1ページ最大表示件数
  var $page_now; // 現在のページ
  var $pages; // ページ番号リスト
  var $param; // リンクに追加するパラメータ
  /**
   * リクエストを読み込む
   */
  function readParam(){
    $this->limit = getDefParam('limit', $this->def_limit);
    $this->offset = getDefParam('offset', 0);
    debug('limit='.$this->limit.' offset='.$this->offset);
  }
  /**
   * 前のページがあるか
   *@return true:ある false:ない
   */
  function hasPrev(){
    // オフセットがあるなら前のページがある
    return $this->offset > 0 ? true : false;
  }
  /**
   * 次のページがあるか
   *@return true:ある false:ない
   */
  function hasNext(){
    // まだ次があるか
    return $this->count > $this->offset + $this->limit ? true : false;
  }
  /**
   * DBにセレクト文を発行して該当ページデータを取得。
   * 全体のヒット件数がcount変数に登録される。
   *@param string where "id,name from mytbl where deleted is not null"
   *@return object mysql_query戻り値(limit件数でセレクトした結果)
   *例) 
  // select とlimit,offsetを除いたSQLを引数に指定
  $res = $o->this("* from mytbl where deleted is not null order by id");
  $count = $o->count; // ページ中以外を含めた全hit件数 184など
  foreach($row = $db->fetch($res)){
    // 処理 (10件だけなど、ページ中のもののみループされる)
  }
  */
  function select($sql_before_from, $sql_after_from, $conditions=null, $sql_count='*'){
    global $db;
    $this->readParam(); // limit,offsetリクエストを取得
    $conditions['limit'] = $this->limit;
    $conditions['offset'] = $this->offset;
    // found_rowsを使うと70%速くなる可能性があるが、
    //同一コネクションで繋がなければならない,MYSQL独自機能,など依存が大きくなるため却下
    //$res = $db->select('select SQL_CALC_FOUND_ROWS ' . $where . ' limit :limit offset :offset', $conditions);
    //$res_count = $db->select('select found_rows()');
    $res = $db->select('select ' . $sql_before_from . ' from ' . $sql_after_from . ' limit :limit offset :offset', $conditions);
    $res_count = $db->select1('select count('. $sql_count .') as count from ' . $sql_after_from, $conditions); 
    $this->count = $res_count['count'];
    //現在のページ数 1,2,3...
    $this->page_now = $this->offset / $this->limit + 1;
    //最後のページ
    $page_max = round($this->count / $this->limit) + 1;
    debug('all_hit=' . $this->count . ' page_now='. $this->page_now . ' page_max='.$page_max);
    $this->pages = array();
    for($i=1;$i <= $page_max; $i++){
      $this->pages[] = $i;
    }
    return $res;
  }
  /**
   * DBセレクトなどは行わずにページングに必要な情報を直接指定
   * $h = array('limit'=>10,'offset'=>20,'count'=>41);
   * this($h);
   */
  function setManual($h){
    $this->limit = $h['limit'];
    $this->offset = $h['offset'];
    $this->count = $h['count'];
    $this->page_now = $this->offset / $this->limit + 1;
    $this->page_max = round($this->count / $this->limit) + 1;
    $this->pages = array();
    for($i=1;$i <= $page_max; $i++){
      $this->pages[] = $i;
    }
  }
  function getNextUrl(){ return $this->getUrl($this->page_now+1);}
  function getPrevUrl(){ return $this->getUrl($this->page_now-1); }
  /**
   * ページング時のURLを取得
   *@param $page 進むページ数 1,2,3...
   */
  function getUrl($page){
    global $req;
    $offset = $this->limit * ($page - 1);
    $url = addParam($_SERVER['REQUEST_URI'], $req);
    return addParam($url, array('offset'=>$offset, 'limit'=>$this->limit) );
  }
}

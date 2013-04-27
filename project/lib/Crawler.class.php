<?php
require_once dirname(__FILE__).'/Lib.inc.php';
require_once dirname(__FILE__).'/Db.class.php';
require_once dirname(__FILE__).'/Debug.class.php';
require_once dirname(__FILE__).'/Brawser.class.php';

class Crawler {
	private $m_brawser; //
	private $m_history; ///<クロールしたurl履歴
	private $m_baseurl;
	private $m_baseaddr;
	private $m_db;
	private $m_starttime; //クロール開始時刻
	private $m_id; // category id
	
	//DBから現在の時刻取得
	function getdbnow() {
		$res = $this->m_db->select("select NOW() as now");
		return $res[0]['now'];
	}
	//DB(htnlテーブル)の中にURLが存在するか
	function urlexist($url) {
		$res = $this->m_db->select("select count(id) as count from html where url = :url", array('url'=>$url));
		$this->debug('urlexist count:'.$res[0]['count']);
		return $res[0]['count'] ? true : false;
	}
	//htmlデータを挿入する
	function insert_html($html, $url, $eurl, $size=0) {
		$h['checksum_url'] = md5($eurl);
		$h['checksum_html'] = md5($html);
		$h['size'] = $size;
		$h['url'] = $eurl;
		$h['html'] = mb_convert_encoding($html, "UTF-8", mb_detect_order());
		$h['uptime'] = 'now()';
		$stmt = $this->m_db->insert("html", $h);
	}
	function gethtml_id($url) {
		$res = $this->m_db->select("select id from html where url = :url", array('url'=>$url));
		if( ! $res ) return null;
		return $result[0]['id'];
	}
	function getreport_id($url) {
		$res = $this->m_db->select("select id from report where url = :url", array('url'=>$url));
		if( ! $res ) return null;
		return $res[0]['id'];
	}
	///
	function update_error($status, $msg, $url) {
	    $this->debug("update_error($status, $msg, $url)");
		$id = $this->getreport_id($url);
		$h['type'] = $status;
		$h['message'] = $msg;
		if (empty($id)) {
			$html_id = $this->gethtml_id($url);
			$h['url'] = $url;
			$h['html_id'] = $html_id;
			$this->m_db->insert('report', $h);
		}else{
			$stmt = $this->m_db->update('report', $h, "id = :id", array('id'=>$id));
		}
	}
	//htmlデータを更新するeurl=curlで取得したEFFECTIVE URL
	function update_html($html, $url, $eurl, $size=0) {
		if (empty($size)) {
			$size = strlen($html);
		}
		if ($this->urlexist($url)) {
			$h['size'] = $size;
			$h['html'] = $html;
			$h['checksum_html'] = md5($html);
			$h['checksum_url'] = md5($url);
			$this->m_db->update("html", $h, "(checksum_html!=:checksum_html or checksum_url!=:checksum_url) and url = :url", array('url'=>$url, 'checksum_url'=>$h['checksum_url'], 'checksum_html'=>$h['checksum_html']));
		}else{
			//指定されたURLのデータはDBにない。新しく追加
			$this->insert_html($html, $url, $eurl, $size);
		}
	}
	
	function get_mysite() {
		//var_dump($this->m_db);
		//$db =& $this->m_db;
		$res = $this->m_db->select("mysite");
		if( ! $res ) return null;
		for ($i = 0; $i < count($res); $i++) {
			$ret[] = $res[$i]['url'];
		}
		return $ret;
	}

	////////////////
	function addHistory($url) {
		//print $url."\n";
		$this->m_history[]=$url;
		return false;
	}
	function getHistory() {
		return $this->m_history;
	}
	//
	function chk_size($url, $size) {
		$h['start'] = $this->m_starttime;
		$h['url'] = $url;
		$res = $this->m_db->select("select size from html where uptime >= :start and url = :url", $h);
		if(!$res) return false;
		return $res[0]['size'] == $size ? true : false;
	}
	
	//クロールが終わっているtrue, 終わっていないfalse
	function isCrawl($url) {
		for ($i = 0; $i < count($this->m_history); $i++) {
			if (strcmp($this->m_history[$i], $url)!=0) {
				continue;
			}
			return true;
		}
		//print $i;
		return false;
	}

    private function exitError($message) {
        echo $message."\n";
        $this->debug($message);
        exit;
    }

	///コンストラクタ
	public function __construct() {
		$this->m_history = array();
		$this->debug(__CLASS__." construct");
		$this->m_db = new Db();
		$this->m_db->connect();
		if( ! $this->m_db ){
            $this->exitError("can not open db");
        }
		//開始時刻を保持
		$this->m_starttime = $this->getdbnow();
		if ($this->m_starttime) {
			$this->debug("DB success(start = ".$this->m_starttime.")");
		}else{
			$this->debug("NOW() error.");
			die;
		}
		//mb_detect_order("EUC-JP, SJIS, ASCII, UTF-8");
		//mb_detect_order("eucjp-win, sjis-win, EUC-JP, SJIS, ASCII, UTF-8");
		$this->m_brawser = new Brawser();
		
	}
	///デストラクタPHP5以降
	public function __destruct() {
		$this->postcrawl($this->m_id);
		print "\n";
		print $this->gethistory();
		print 'end XIA';
	}

	/**
	 * html文内のリンクを取得
	 * 対象：href="xxx", window.open("xxx"), javascript中のurl="xxx"
	 *@param string html
	 *@param string url 現在のURL(相対URLを絶対URLに変換するのに使用)
	 *@return array urls  ex) array( 0=>'a.html', 1=>'http://xxx.xx/xx?xxx=xxx' ... )
	 */
	function getUrlsInHtml($html, $baseurl){
		$urls = array();
		if( preg_match_all('/open *\( *[\'"]([^\'"]+)[\'"]/i', $html, $regs) ){
			$urls = array_merge($urls, $regs[1]);
		}
		if( preg_match_all('/(location|href|url) *= *[\'"]([^\'"]+)[\'"]/i', $html, $regs) ){
			$urls = array_merge($urls, $regs[2]);
		}
		foreach($urls as $k => $url){
			$urls[$k] = getAbsUrl($baseurl, $url);
			$this->debug('getAbsUrl:'.$baseurl.','.$url);
		}
		$urls = array_unique($urls);
		$this->debug(__FUNCTION__.':urls...'.pr($urls));
		return $urls;
	}

	/**
	 * ヘッダ情報からcontenttypeを見て、html形式かを判断
	 *@param string $head ヘッダテキスト 
     *@return true:html形式 false:htmlではない
	 */
	function isHtmlContentType($head) {
		if( ! preg_match_all('/Content-Type\s*:\s*([a-z\/]+)/i', $head, $ret) ){
			$this->debug('no content-type. href=['.$head.']');
			return false;
		}
		$cotnenttype = $ret[1][0];
		return strpos($contenttype, 'html') !== false;
	}
	/**
	 * urlの拡張子チェック
	 *@return true:拡張子が$extと同じ false:違う
	 */
	function isExt($url, $ext) {
	    return strrpos($url, $ext) === strlen($url) - strlen($ext) ? true : false;
	}
	///urlがサイト内かをチェック（ホスト名で見る）
	function isInSite($url, $baseurl) {
		// 
		if ($this->isExt($url, ".jpg")) { return false; }
		if ($this->isExt($url, ".jpeg")) { return false; }
		if ($this->isExt($url, ".gif")) { return false; }
		// 絶対URLでなく相対URLならサイト内
		if( strpos($url, 'http') !== 0 ){
			return true;
		}
		$p = parse_url($url);
		$basep = parse_url($baseurl);
		// ドメイン名が同じであればサイト内)
		$host = $this->trimSubDomain($basep['host']);
		if ( endsWith($p['host'], $host) ) {
			return true;
		}
		return false;
	}
	/**
	 * サブドメインを外す
	 * "xxx.co.jp" = this("aaa.xxx.co.jp");
	 * "xxx.jp" = this("aaa.xxx.jp");
	 * "xxx.jp" = this("xxx.jp");
	 */
	function trimSubDomain($host){
		$host = strtolower($host);
		$sub = explode('.', $host);
		$cnt = count($sub);
		if( preg_match('/\.[a-z]{2}\.[a-z]+$/', $host) ){
			$host = $sub[$cnt-3] . '.' . $sub[$cnt-2] . '.' . $sub[$cnt-1];
		}else{
			$host = $sub[$cnt-2] . '.' . $sub[$cnt-1];
		}
		return $host;
	}
		
	function debug($message) {
		debug($message);
	}
	//ヘッダからContent-Lengthの値を返す
	function getContentLength($head) {
		// /i修飾子で大文字小文字を区別しない
		preg_match_all("/.*?Content-Length\s*:.*?([0-9]+).*?/", $head, $ret);
		return empty($ret[1][0]) ? false : $ret[1][0];
	}
	
	//クロール（再帰呼び出し用）
	function crawl_r($url) {
		$this->debug('crawl_r('.$url.')');
		list($head, $v) = $this->m_brawser->execHeader($url);
		//HTML形式かチェック
		if (strpos($v['content_type'],"html") === false) {
			if( ! preg_match('/Content\-Type *: *[a-z\/]*html/i', $head) ){
				$this->debug('content-type not html:'.$v['content_type']);
				return false;
			}
		}
		//クロール済みチェック
		if ($this->isCrawl($url)) {
			$this->debug('crawled data');
			return false;
		}
	
		$this->addhistory($url);
		list($html, $v) = $this->m_brawser->execFireFox($url);
		if ($html == NULL) {
			return false;
		}
		$status = $v['status'];
		if ($status == 200) {
			$this->update_html($html, $url, $v['url'], $v['content_length']);
			$links = $this->getUrlsInHtml($html, $url);
			foreach ($links as $i => $link) {
				if($this->isInSite($link, $url)) {
					debug($url."::::+".$link);
					$this->crawl_r($link);
				}else{
					debug($url."::::-".$link);
				}
			}
			return true;
		}else if (300 >= $status && $status <400 ) {
			//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);を指定しているので、意味なし？
			$this->debug('http status is 300 - 400:'.$status);
			die;
		}else{
			$this->debug("status=".$status."($url)");
			$this->update_error($status, $html, $url);
		}
		return false;
	}
	/**
	 * クロール
	 */
	function crawl($url, $user=NULL, $pass=NULL) {
		if (is_numeric($url)) {
			//第一引数が数値だった
			$this->m_id = $url;
			if ($url = $this->precrawl($url)) {
			}else{
				print "now crawling.\n";
				return false;
			}
		}
		$this->m_baseurl = parse_url($url);
		$pu = parse_url($url);
		$addr = gethostbyname($pu['host']);
		$this->m_baseaddr = $addr;
		$this->addhistory($url);
		$this->m_brawser->init();
		if (empty($user)==false) { //Basic認証を行う
			$this->m_brawser->setBasicIdPass($user, $pass);
		}
		list($html, $v) = $this->m_brawser->execFireFox($url);
		if ($html==NULL) {
		    $this->error("access error url=[".$url."]");
			return false;
		}
		$links = $this->getUrlsInHtml($html, $url);
		$this->debug('links[0]='.$links[0]);
		//$this->debug(pr($v));
		$status = $v['status'];
		if ($status == 200) {
			foreach ($links as $i => $link) {
				if ($this->isInSite($link, $url) ) {
					$this->crawl_r($link);
				}
			}
			$this->report('crawl finish');
			return true;
		}
		$this->report('bad status:'.$status);
		return false;
	}
	function report($message) {
		$this->debug('[report]'.$message);
	}
	function precrawl($id) {
		$res = $this->m_db->select("select * from category where id = :id", array('id'=>$id));
		if( ! $res ){
		    return false;
		}
		$this->m_db->update("category",array("status"=>1), "id=" . $id);
		return $res[0]['url'];
	}
	function postcrawl($id) {
	    if (!is_numeric($id)) return false;
		$stmt = $this->m_db->update("category", array("status"=>0), "id=".$id);
	}
}



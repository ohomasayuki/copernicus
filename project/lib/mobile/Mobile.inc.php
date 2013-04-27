<?
/**
 * 機種名を取得(例: DoCoMo=SH903i, AU=HI33(デバイスID), SoftBank=904T)
 *@param char $carrier: i:DoCoMo e:AU y:SoftBank 
 *@return string agent
 *ex) 'D902i' = this('i', 'DoCoMo/2.0 D902i(c100;TB;W23H16)')
 */
function getShortUseragent($carrier, $ua) {
	if( $carrier == CARRIER_Y ){
		$agent_param = explode("/",$ua);
		$agent = $agent_param[2];
		if( strpos($agent, ' ') !== false ) $agent = substr($agent, 0, strpos($agent,' '));
		return $agent;
	}else if( $carrier == CARRIER_I ){
		$agent_param = explode("/",$ua);
		if ($agent_param[1] == "1.0") {
			return $agent_param[2];
		} else {
			$foma_agent = explode(" ", $agent_param[1]);
			$foma_agent = explode("(", $foma_agent[1]);
			return $foma_agent[0];
		}
	}else if( $carrier == CARRIER_E ){
		$array_agent = explode( " ", $ua );
		$ez_agent = explode( "-", $array_agent[0] );
		$agent = $ez_agent[1];
		if (($agent == "SDK/11") || ($agent == "OPWV")){
			$agent = "CA22";
		}
		return $agent;
	}
	return "";
}
function isDocomo() { return $GLOBALS['g']->g['carrier'] == CARRIER_I; }
function isSoftbank(){ return $GLOBALS['g']->g['carrier'] == CARRIER_Y; }
function isAu()          { return $GLOBALS['g']->g['carrier'] == CARRIER_E; }

function getCarrierUid(){
	if( isDocomo() ){
		if( isset($_REQUEST['uid']) && $_REQUEST['uid'] != 'NULLGWDOCOMO' ){
			//公式サイト
			$uid = $_REQUEST['uid'];
		}else if( isset($_SERVER['HTTP_X_DCMGUID']) ){
			//guid
			$uid = $_SERVER['HTTP_X_DCMGUID'];
		}
		//勝手サイト
		//utn
		//ereg("ser[A-Za-z0-9]+", $_SERVER['HTTP_USER_AGENT'], $arr);
		//$uid = substr($arr[0], 3);
	}else if( isSoftbank() ){
		$uid = $_SERVER['HTTP_X_JPHONE_UID'];
		$uid = substr($uid, 1);
	}else if( isAu() ){
		$uid = $_SERVER['HTTP_X_UP_SUBNO'];
	}
	global $g;
	if( $g->isPc() ){ $uid = 'debug_uid'; }
	debug('uid=['.$uid.']');
	return $uid;
}

/**
 * HDML機種判定
 * User-AgentがUP.BrowserではじまるものはHDML機種
 */
function isHdml(){
  return stripos($GLOBALS['g']->g['useragent'], 'UP.Browser') === 0;
}

/**
 * HTML種別を取得
 * @param str carrier
 * @param str shortUA
 * @return str HTMLtype (XHTML | HTML | false)
 */
function getTypeHTML($carrier, $shortUA){

	global $docomo_XHTML_num;
	global $docomo_HTML;
	global $softbank_XHTML;
	global $softbank_HTML;
	global $db;
	global $g;
	
	// i robot
	if( strpos($g->g['useragent'], 'DoCoMo/2.0 i-robot(c10;TC)')!==false ){
		return XHTML;
	} 
	if( strpos($g->g['useragent'], 'DoCoMo/1.0/i-robot/c5/TC')!==false ){
		//return HTML;
		return false;
	}
	
	// 特殊ケース
	if($shortUA=='N2701'){	//docomo
		return false;
	}
	if($shortUA=='MIB'){	//softbank Motorola
		//return XHTML;
		return false;
	}
	
	// docomo
	if($carrier == CARRIER_I){
		//700～シリーズ、900～シリーズであっても一部対応していない機種がある
		if(in_array($shortUA, $docomo_HITAIOU)){
			return false;
		}
		foreach($docomo_XHTML_num as $num){
			if( strpos($shortUA,$num)!==false ){return XHTML;}	//N2701には701含まれる
		}
		return false;
		//if( in_array($shortUA, $docomo_HTML) ){return HTML;}
	}else

	//au 
	if($carrier == CARRIER_E){
		return false;
		/*
		if( $db->select1('select * from useragent_master where useragent = :ua and carrier = :carrier',array('ua'=>$shortUA, 'carrier'=>$carrier)) ){
			return XHTML;
		}
		return HTML;
		*/
	}else

	// softbank
	if($carrier == CARRIER_Y){
		return false;
		/*
		if( in_array($shortUA, $softbank_XHTML) ){return XHTML;}
		if( in_array($shortUA, $softbank_HTML) ){return HTML;}
		*/
	}
	return false;
}

/**
 * Flash対応機種か判定
 *@return bool true:対応 false:非対応
 */
function isNoFlash(){
	global $g;
	$carrier = $g->g['carrier'];
	$shortUA = $g->g['short_useragent'];
	if( ! $g->isXhtml() ) return true; // XHTML機種でなければ非対応とする
	//print_r($docomo_NO_SWF);
	if($carrier == CARRIER_I){
		//return in_array($shortUA, $GLOBALS['docomo_NO_SWF']);
		return in_array($shortUA, $GLOBALS['docomo_NO_SWF']);
	}
	if($carrier == CARRIER_E){
		return in_array($shortUA, $GLOBALS['au_NO_SWF']);
	}
	if($carrier == CARRIER_Y){
		return in_array($shortUA, $GLOBALS['softbank_NO_SWF']);
	}
	// ここにはこない。デバッグにアラートを記録し、非対応としte。
	debug('WARNING: '.__FUNCTION__.' no match');
	return true; // デフォルトは非対応
}

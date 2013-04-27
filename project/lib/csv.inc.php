<?php
function csv2array($csv,$is_url=true){
	// ファイル読み込み
	if($is_url){
		$csv = file_get_contents($csv);
	}
	$csv = str_replace("\r\n","\n",$csv);
	$csv = str_replace("\r","\n",$csv);
	$csv = trim($csv);
	// 行の配列
	$lines = explode("\n",$csv);
	$i = 0;
	$lc = count($lines);
	while($i<$lc){
		// 次の行に続く
		if(preg_match_all('/"/',$lines[$i],$match)%2==1){
			$lines[$i] = $lines[$i]."\n".$lines[$i+1];
			unset($lines[$i+1]);
			$lines = array_values($lines);
			$i--;
			$lc = count($lines);
		}
		$i++;
	}
	// カラムの配列
	$i = 0;
	$result=array();
	while($i<$lc){
		$columns = explode(",",$lines[$i]);
		$j = 0;
		$cc = count($columns);
		while($j<$cc){
			// 次のカラムに続く
			if(preg_match_all('/"/',$columns[$j],$match)%2==1){
				$columns[$j] = $columns[$j].",".$columns[$j+1];
				unset($columns[$j+1]);
				$columns = array_values($columns);
				$j--;
				$cc = count($columns);
			}
			$j++;
		}
		// ダブルクォーテーション
		$j=0;
		while($j<$cc){
			if(preg_match('/^"(.*)"$/s',$columns[$j],$match)){
				$columns[$j] = preg_replace('/""/','"',$match[1]);
			}
			$j++;
		}
		$result[] = $columns;
		$i++;
	}
	return $result;
}

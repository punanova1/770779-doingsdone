<?php

function calculate_project ($z, $p){
	if ($p == 'Все'){
			return count($z);
		}
	$sum = 0;
	foreach ($z as $key => $val) {			
		if ($val['category'] == $p){			
			$sum = $sum + 1;
		} 
	}
	return $sum;
}

function include_template($file, $data){
	if (!file_exists($file)) {
		return "";
	}
	ob_start();
		extract($data);
	require_once($file);
		$contents = ob_get_contents();
	ob_end_clean();
	
	return $contents;
}

?>
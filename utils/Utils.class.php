<?php
class Utils
{
	
	
	/**
	* includeRandom
	*
	*
	*
	*/
	// include a random selection from the given directory and file extention
	public static function includeRandom($directory,$extension,$fullpath=true) {
		
		$files = scandir($_SERVER['DOCUMENT_ROOT'].$directory);
		$extlen = strlen($extension);
		foreach ($files as $key => $item) {
			// If the item is a directory or doesn't have the specified file extension (optional), unset the item from the array
			if (is_dir($item) || ($extension && (substr($item,strlen($item)-$extlen) !== $extension))) unset($files[$key]);
		}
		// Include a random item from the array
		if ($files){
			$tf = $files[array_rand($files)];
			if($fullpath){
				return $directory.$tf;
			}else{
				return $tf;
			}
		}
		// If no items exist, display an error
		//else return "Error: no file(s) found.";
	}
	
	
	/**
	* parseConfig
	*
	*
	*
	*/
	
	
	public static function parseConfig($x)
	{
		$r = array();
		
		$xml = simplexml_load_string($x);
		if($xml){
			foreach($xml->option as $option){
				$t = sprintf($option['name']);
				$r[$t] = sprintf($option);
			}
		}
				
		return $r;
			
	}
	
	/**
	* checkArray
	*
	*
	*
	*/
	
	public static function checkArray($a,$vals)
	{
		foreach($a as $row){
			
			$tA = array();
			foreach($vals as $key=>$val){
				if($row[$key] == $val){
					$tA[] = true;
				}
			}
			if(count($tA) == count($vals)){
				return $row;
			}
		
		}
		return false;
	
	}
	
	/**
	* arraySort
	*
	*
	*
	*/
	
	public static function arraySort($array, $key)
	{
		for ($i = 0; $i < sizeof($array); $i++) { 
			$sort_values[$i] = $array[$i][$key]; 
		 } 
		asort ($sort_values); 
		reset ($sort_values); 
		while (list ($arr_key, $arr_val) = each ($sort_values)) { 
			$sorted_arr[] = $array[$arr_key]; 
		} 
		return $sorted_arr; 
	} 
	
	
	/**
	* formatHumanReadable
	*
	*
	*
	*/
	
	
	public static function formatHumanReadable($text)
	{
		$t = preg_replace('[_]',' ',$text);
		$t = strtoupper($t{0}) . substr($t,1);
		return $t;	
	}
	
	/**
	* removeDups
	*
	* @param	array	
	*
	* @return  array
	*
	*/
	
	
	public static function removeDups($array, $row_element) {   
		$new_array[0] = $array[0];
		foreach ($array as $current) {
		   $add_flag = 1;
		   foreach ($new_array as $tmp) {
			   if ($current[$row_element]==$tmp[$row_element]) {
				   $add_flag = 0; break;
			   }
		   }
		   if ($add_flag) $new_array[] = $current;
		}
		return $new_array;
	}
	
	/**
	* getTimeDifference
	* taken from blog posting
	*
	* @param   string   the prefix name
	*
	* @return  string   timestamp
	*
	*/
	
	function getTimeDifference($start,$end)
	{
		$uts['start']      =    strtotime( $start );
		$uts['end']        =    strtotime( $end );
		if( $uts['start']!==-1 && $uts['end']!==-1 )
		{
			if( $uts['end'] >= $uts['start'] )
			{
				$diff    =    $uts['end'] - $uts['start'];
				if( $days=intval((floor($diff/86400))) )
					$diff = $diff % 86400;
				if( $hours=intval((floor($diff/3600))) )
					$diff = $diff % 3600;
				if( $minutes=intval((floor($diff/60))) )
					$diff = $diff % 60;
				$diff    =    intval( $diff );            
				return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
			}
			else
			{
				trigger_error( "Ending date/time is earlier than the start date/time", E_USER_WARNING );
			}
		}
		else
		{
			trigger_error( "Invalid date/time data detected", E_USER_WARNING );
		}
		return( false );
	}
	
	
	/**
	* assembleDateTime
	*
	* @param   string   the prefix name
	*
	* @return  string   timestamp
	*
	*/
	
	public static function assembleDateTime($prefix,$name_space)
	{
		return self::assembleDate($prefix,$name_space) . " " . self::assembleTime($prefix,$name_space);	
	}
	
	/**
	* assembleDate
	*
	* @param   string   the prefix name
	*
	* @return  string   timestamp
	*
	*/
	
	public static function assembleDate($prefix,$name_space)
	{
		$t_y = $name_space . $prefix . '_year';
		$t_m = $name_space . $prefix . '_month';
		$t_d = $name_space . $prefix . '_day';
	
		return $_POST[$t_y] . "-" . $_POST[$t_m] . "-" . $_POST[$t_d];
	}
	
	/**
	* assembleTime
	*
	* @param   string   the prefix name
	*
	* @return  string   timestamp
	*
	*/
	
	public static function assembleTime($prefix,$name_space)
	{
		
		$t_h = $name_space . $prefix . '_hour';
		$t_ap = $name_space . $prefix . '_meridiem';
		$t_min = $name_space . $prefix . '_minute';
		
		//die(print(self::time_12_24($_POST[$t_h],$_POST[$t_ap])));
		
		return self::time12to24($_POST[$t_h],$_POST[$t_ap]) . ":" . $_POST[$t_min] . ":00";
		
	}
	


	/**
	* now
	*
	* @return  string    now() timestamp
	*
	*/
	
	public static function now()
	{
		return date("Y-m-d H:i:s");
	}
	
	/**
	* date_today
	* uses the values in the config.inc to set default hour and minute for today
	* @return  string    timestamp
	*
	*/
	
	public static function dateToday()
	{
		//return date("Y-m-d");
		$dA = explode("-",self::now());
		return date("Y-m-d H:i:s", mktime(CMS_DEFAULT_HOUR,CMS_DEFAULT_MIN,0,$dA[1],$dA[2],$dA[0]));
	}
	
	/**
	* date_now
	* returns now, with seconds to 00
	* @return  string    timestamp
	*
	*/
	
	public static function dateNow()
	{
		$mA = explode(" ",self::now());
		$dA = explode("-",$mA[0]);
		$tA = explode(":",$mA[1]);
		return date("Y-m-d H:i:s", mktime($tA[0],$tA[1],0,$dA[1],$dA[2],$dA[0]));
	}
	
	/**
	* date_future
	*
	* sets a date y number of years in the future and return timestamp
	*
	* @param   string   num of years in future
	*
	* @return  string   timestamp  
	*
	*/
	
	public static function dateFuture($y="5")
	{
		
		$dA = explode("-",self::now());
		return date("Y-m-d", mktime(0,0,0,$dA[1],$dA[2],$dA[0]+$y));
		//return date("Y-m-d", mktime(0,0,0,$dA[1],$dA[2],($dA[0] + 5)));
	}
	
	/**
	* date_new
	*
	* sets a date x number of days in the future
	*
	* @param   string   num of days in future
	*
	* @return  string   timestamp  
	*
	*/
	
	public static function dateNew($days=7)
	{
	
		$nextWeek = time() + ($days * 24 * 60 * 60);
		return date('Y-m-d', $nextWeek);
	
	}
	
	/**
	* time_24_12
	*
	* used to break down timestamps into form pulldowns with am/pm
	*
	* @param   string   hours 00-23
	*
	* @return  string   time:AM/PM  
	*
	*/
	
	public static function time24to12($time)
	{
	
		if($time > 12){
			return (12 - (24 - $time)) . ":PM";
		}else{
			return $time . ":AM";
		}
	}
	
	/**
	* time_12_24
	*
	* used to break down timestamps into form pulldowns with am/pm
	*
	* @param   string   hours 00-12
	* @param   string   AM/PM
	*
	* @return  string   00-23 hours
	*
	*/
	
	public static function time12to24($time,$am_pm)
	{
	
		if($am_pm == "PM"){
			return(12 + $time);
		}else{
			return $time;
		}
	}
	
	/**
	* human_file_size
	*
	* @param   number   the filesize in kbytes
	*
	* @return  string   filesize w/ units
	*
	*/
	
	public static function humanFileSize($size)
	{
		$filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
		return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
	}
	
	/**
	* list_directory
	*
	* @param   string   the directory to scan
	*
	* @return  array    multidimensional array of strings
	*
	*/
	
	public static function listDirectory($dir)
	{
	   $file_list = '';
	   $stack[] = $dir;
	   while ($stack) {
		 $current_dir = array_pop($stack);
		 if ($dh = opendir($current_dir)) {
		   while (($file = readdir($dh)) !== false) {
			 if ($file !== '.' AND $file !== '..') {
			   $current_file = "{$current_dir}/{$file}";
			   if (is_file($current_file)) {
				 $file_list[] = "{$current_dir}/{$file}";
			   } elseif (is_dir($current_file)) {
				 $stack[] = $current_file;
			   }
			 }
		   }
		 }
	   }
	   return $file_list;
	 }
	
	/**
	* sort_position
	*
	* @param   string   table name
	* @param   string   sql record set query
	* @param   string   record id
	* @param   string   new position
	*
	* @return  null     
	*
	*/
	
	public static function sortPosition($table,$sql,$id,$pos){
		
		$q = db_query($sql);
		
		$tA = array();
		for($i=0;$i<count($q);$i++){
			if($id != $q[$i]['id']){
				$tA[] = $q[$i]['id'];
			}
		
		}
			
		array_splice($tA,($pos-1),0,$id);
		
		for($i=0;$i<count($tA);$i++){
			$r_id = $tA[$i];
			$tpos = $i+1;
			db_query_simple("UPDATE `$table` SET position = $tpos WHERE id = '$r_id'");
		}
	
	}
		
	/**
	* setVar
	*
	* checks request to see if variable is not empty
	*
	* @param   string   the variable name
	* @param   string   default initialize = ""
	*
	* @return  null    
	*
	*/
	
	public static function setVar($var,$default="")
	{
	
		if (isset($_REQUEST[$var])){
			return $_REQUEST[$var];
		}else{
			if($default != ""){
				return $default;
			}
		}
		
	}
	
	/**
	* setPost
	*
	* checks request to see if variable is not empty
	*
	* @param   string   the variable name
	* @param   string   default initialize = ""
	*
	* @return  null    
	*
	*/
	
	//mysql_real_escape_string
	
	public static function setPost($var,$default="")
	{
	
		if (!empty($_POST[$var])){
			global $$var;
			$$var = rtrim(stripslashes($_POST[$var]));
		}else{
			global $$var;
			if($default != ""){
				$$var = $default;
			}
		}
		
	}
	
	
	/**
	* super_local
	*
	* moves super to the local scope of a public static function
	* uses mysql_real_escape_string
	*
	* @param   string   global, GET, POST, SESSION
	*
	* @return  object   the variable in the local scope    
	*
	*/
	
	public static function super_local($super)
	{
		
		global $$super;
		reset($$super);
		$r = "";
		while (list($key, $val) = each($$super)) {
			//if($addslashes){
				$r .= "\$$key = \"" . rtrim(stripslashes($val)) . "\";";
			//}else{
				//$r .= "\$$key = \"$val\";";
			//}
		}
		return $r;
	}
	
	
	
	/**
	* pad_zero
	*
	* used to add a zero to numbers under 10
	*
	* @param   string   number 0-n
	*
	* @return  string   00-09 : n
	*
	*/
	
	public static function padZero($n)
	{
	
		if($n < 10){
			return "0" . $n;
		}else{
			return $n;
		}
	}
	
	/**
	* meta_refresh
	*
	* used to meta refresh to a new page
	*
	* @param   string   url
	* @param   number   time
	*
	* @return  null
	*
	*/
	
	public static function metaRefresh($url,$time = 0)
	{	
		print '<meta http-equiv="refresh" content="'  . $time . '; url='  . $url . '" />';
	}
	
	/**
	* createthumb
	* uses GD 2 to create jpeg thumbnails
	*
	* @param   string   thumbanil name
	* @param   string   source filename
	* @param   string   des width
	* @param   string   des height
	*
	* @return  null     
	*
	*/
	
	public static function createThumb($src_name,$dst_name,$new_w,$new_h,$options)
	{
		if($options['mode'] == 'jpg'){
			$src_img=imagecreatefromjpeg($src_name);
		}
		if($options['mode'] == 'png'){
			$src_img=ImageCreateFromPNG($src_name);
		}
		
		$old_x=imageSX($src_img);
		$old_y=imageSY($src_img);
		
		if ($old_x > $old_y) {
			$thumb_w=$new_w;
			$thumb_h=ceil($old_y*($new_h/$old_x));
		}
		if ($old_x < $old_y) {
			$thumb_w=ceil($old_x*($new_w/$old_y));
			$thumb_h=$new_h;
		}
		if ($old_x == $old_y) {
			$thumb_w=$new_w;
			$thumb_h=$new_h;
		}
		
		
		$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
		
		if($options['mode'] == 'png'){
			imagealphablending($dst_img, false);
		}
		
		imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
		
		if($options['mode'] == 'png'){
			imageSaveAlpha($dst_img, true);
			imagepng($dst_img,$dst_name); 
		}
		
		if($options['mode'] == 'jpg'){
			imagejpeg($dst_img,$dst_name,$options['quality']); 
		}		
		
		imagedestroy($dst_img); 
		imagedestroy($src_img); 
	}
	

	
}
?>
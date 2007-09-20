<?php

/*

Class: Utils

Collection of general utility functions, static

*/

class Utils
{
	
	
	/*
	
	Function: includeRandom
	
	Include a random selection from the given directory and file extention
	
	Parameters:
	
		directory:String - directory to pull from
		extension:String - file extension
		fullpath:Boolean - if true, returns full path, if false just the filename
	
	*/
	
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
	
	
	/*
	
	Function: parseConfig
	
	Breaks apart standard <option name="bla">value</option> xml and returns array
	
	Parameters:
	
		x:String - xml source url or location
	
	Returns:
	
		array
	
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
	
	/*
	
	Function: checkArray
	
	Checks to see if an intersection exists for all vals
	
	Parameters:
	
		a:Array - main array
		vals:Array - array of values to check for
		
	Returns:
	
		Boolean	
	
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
	
	/*
	
	Function: arraySort
	
	Sorts an array on a supplied key
	
	Parameters:
	
		array:Array	- main array
		key:String - key name
	
	Returns:
	
		sorted array
	
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
	
	
	/*
	
	Function: formatHumanReadable
	
	Replaces underscores with spaces and uppercases first character
	
	Parameters:
	
		text:String - text
	
	Returns:
	
		formatted string	
	
	*/
	
	
	public static function formatHumanReadable($text)
	{
		$t = preg_replace('[_]',' ',$text);
		$t = strtoupper($t{0}) . substr($t,1);
		return $t;	
	}
	
	/*
	Function: removeDups
	
	Removes duplicates from a particular key of an array
	
	Parameters:
	
		array:Array - original array
		row_element:String - string of key to check duplicates for
	
	Returns:
	
		formatted array
	
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
	
	/*
	
	Function: getTimeDifference
	
	Taken from blog posting, returns an array of days, hours, minutes, and seconds for a difference
	
	Parameters:
		
		start:String - a unix timestamp
		end:String - a unix timestamp
	
	Returns:
	
		array of days, hours, minutes, and seconds for a difference
		
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
	
	
	/*
	
	Function: assembleDateTime
	
	Mashes up various form elements into a dateTime, uses internal methods <assembleDate> and <assembleTime>
	
	Parameters:
	
		prefix:String - prefix of variable
		name_space:String - namespace for variable
		
	Returns:
	
		Unix timestamp
	
	*/
	
	public static function assembleDateTime($prefix,$name_space)
	{
		return self::assembleDate($prefix,$name_space) . " " . self::assembleTime($prefix,$name_space);	
	}
	
	/*
	
	Function: assembleDate
	
	Mashes up date form elements and returns portion of timestamp
	
	Parameters:
	
		prefix:String - prefix of variable
		name_space:String - namespace for variable
		
	Returns:
	
		date portion of timestamp
	
	*/
	
	public static function assembleDate($prefix,$name_space)
	{
		$t_y = $name_space . $prefix . '_year';
		$t_m = $name_space . $prefix . '_month';
		$t_d = $name_space . $prefix . '_day';
	
		return $_POST[$t_y] . "-" . $_POST[$t_m] . "-" . $_POST[$t_d];
	}
	
	/*
	
	Function: assembleTime
	
	Mashes up time form elements and returns portion of timestamp
	
	Parameters:
	
		prefix:String - prefix of variable
		name_space:String - namespace for variable
		
	Returns:
	
		time portion of timestamp
	
	*/
	
	public static function assembleTime($prefix,$name_space)
	{
		
		$t_h = $name_space . $prefix . '_hour';
		$t_ap = $name_space . $prefix . '_meridiem';
		$t_min = $name_space . $prefix . '_minute';
				
		return self::time12to24($_POST[$t_h],$_POST[$t_ap]) . ":" . $_POST[$t_min] . ":00";
		
	}
	

	/*
	
	Function: now
	
	Generates timestamp from this very moment
	
	Returns:
	
		Unix timestamp of now
	
	*/
	
	public static function now()
	{
		return date("Y-m-d H:i:s");
	}
	
	/*
	
	Function: dateToday
	
	Uses the values in the config.inc to set default hour and minute for today
	
	Returns:
		
		Unix timestamp of today (at CMS default hours and minutes)
	
	*/
	
	public static function dateToday()
	{
		//return date("Y-m-d");
		$dA = explode("-",self::now());
		return date("Y-m-d H:i:s", mktime(CMS_DEFAULT_HOUR,CMS_DEFAULT_MIN,0,$dA[1],$dA[2],$dA[0]));
	}
	
	/*
	
	Function: dateNow
	
	Creates timestamp of now and uses 00 for seconds
	
	Returns:
		
		Unix timestamp of now with 00 seconds
	
	*/
	
	public static function dateNow()
	{
		$mA = explode(" ",self::now());
		$dA = explode("-",$mA[0]);
		$tA = explode(":",$mA[1]);
		return date("Y-m-d H:i:s", mktime($tA[0],$tA[1],0,$dA[1],$dA[2],$dA[0]));
	}
	
	/*
	
	Function: dateFuture
	
	Creates timestamp x number of years in the future
	
	Parameters:
	
		y:Number - years in the future
	
	Returns:
		
		date timestamp of a date in the future
	
	*/
	
	public static function dateFuture($y="5")
	{
		
		$dA = explode("-",self::now());
		return date("Y-m-d", mktime(0,0,0,$dA[1],$dA[2],$dA[0]+$y));
		//return date("Y-m-d", mktime(0,0,0,$dA[1],$dA[2],($dA[0] + 5)));
	}
	
	/*
	
	Function: dateNew
	
	Creates timestamp x number of days in the future
	
	Parameters:
	
		y:Number - days in the future
	
	Returns:
		
		date timestamp of a date in the future
	
	*/
	
	public static function dateNew($days=7)
	{
	
		$nextWeek = time() + ($days * 24 * 60 * 60);
		return date('Y-m-d', $nextWeek);
	
	}
	
	/*
	
	Function: time24to12
	
	used to break down timestamps into form pulldowns with am/pm
	
	Parameters:
	
		time:String - hours 00-23
	
	Returns:
	
		string of time:AM/PM  
	
	*/
	
	public static function time24to12($time)
	{
	
		if($time > 12){
			return (12 - (24 - $time)) . ":PM";
		}else{
			return $time . ":AM";
		}
	}
	
	/*
	
	Function: time12to24
	
	used to break down timestamps into form pulldowns with am/pm
	
 	Parameters:
 	
 		time:String - hours 00-12
		am_pm:String - AM/PM
	
	Returns:
	
		string 00-23 hours
	
	*/
	
	public static function time12to24($time,$am_pm)
	{
	
		if($am_pm == "PM"){
			return(12 + $time);
		}else{
			return $time;
		}
	}
	
	/*
	
	Function: humanFileSize
	
	Parameters:
	
		size:Number - the filesize in kbytes
	
	Returns:
	
		string of filesize w/ units
		
	*/
	
	public static function humanFileSize($size)
	{
		$filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
		return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
	}
	
	/*
	
	Function: listDirectory
	
	Lists directory
	
	Parameters:
	
		dir:String - the directory to scan
		
	Returns:
	
		multidimensional array of strings
		
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
				
	/*
	
	Function: setVar
	
	checks $_REQUEST to see if variable is not empty
	
	Parameters:
	
		var:String - the variable name
		default:String - default value if blank
	
	Returns:
	
		value
	
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
	
	/*
	
	Function: setPost
	
	checks $_POST to see if variable is not empty
	
	Parameters:
	
		var:String - the variable name
		default:String - the default value if blank
	
	Returns:
	
		value
		
	*/
		
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
	
	
	/*
	
	Function: super_local
	
	moves super to the local scope of a public static function
	
	Parameters:
	
		super:String - global, GET, POST, SESSION
		
	Returns:
	
		the variable in the local scope    
		
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
	
	/*
	
	Function: padZero
	
	used to add a zero to numbers under 10
	
	Parameters:
	
		n:String - number 0-n
	
	Returns:
	
		string 00-09 : n
		
	*/
	
	public static function padZero($n)
	{
	
		if($n < 10){
			return "0" . $n;
		}else{
			return $n;
		}
	}
	
	/*
	
	Function: metaRefresh
	
	used to meta refresh to a new page
	
	Parameters:
	 
	 	url:String - url
	 	time:Number - time for redirect
	
	*/
	
	public static function metaRefresh($url,$time = 0)
	{	
		print '<meta http-equiv="refresh" content="'  . $time . '; url='  . $url . '" />';
		die();
	}
	
	/*
	
	Function: createThumb
	
	uses GD 2 to create jpeg thumbnails, constrains based upon width and height
	
	Parameters:
	
		src_name:String - source name
		dst_name:String - new name
		new_w:Number - new width
		new_h:Number - new height
		options:Array - mode (jpg||png), quality (0-100)
	
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
		
		if(!(isset($options['output_mode']))){
			$options['output_mode'] = $options['mode'];
		}else{
			$options['mode'] = $options['output_mode'];
		}
		
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
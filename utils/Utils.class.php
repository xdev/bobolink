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
		$xml = new SimpleXMLElement($x);
		
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
	
	public static function checkArray($a,$vals,$all=false)
	{	
		$rA = array();
		
		foreach($a as $row){
			
			$tA = array();
			foreach($vals as $key=>$val){
				if($row[$key] == $val){
					$tA[] = true;
				}
			}
			if(count($tA) == count($vals)){
				if($all === true){
					$rA[] = $row;
				}else{
					return $row;
				}
			}
		
		}
		
		if(count($rA) > 0){
			return $rA;
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
		$sortedA = array();
		$sortvaluesA = array();
		
		for ($i = 0; $i < sizeof($array); $i++) { 
			$sortvaluesA[$i] = $array[$i][$key]; 
		}
		
		asort ($sortvaluesA); 
		reset ($sortvaluesA); 
		
		while (list ($arr_key, $arr_val) = each ($sortvaluesA)) { 
			$sortedA[] = $array[$arr_key]; 
		}
		 
		return $sortedA; 
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
		if ($t = preg_replace('[_]',' ',$text)) {
			$t = strtoupper($t{0}) . substr($t,1);
			return $t;
		} else {
			return $text;
		}
	}
	
	/*
	
	Function: niceDates
	
	Produce human-readable date format
	* DayName, Day Month Year 'l, j M Y'
	* DayName, Day Month Year, Time 'l, j M Y, g:ia'
	* DayName, Day Month Year — DayName, Day Month Year 'l, j M Y' + ' — l, j M Y'
	
	Parameters:
	
		dtstart:String - start date
		dtend:String - end date
		d:String - date format
		t:String - time format
	
	*/
	
	public static function niceDates($dtstart=null,$dtend=null,$options = null)
	{
		if ($dtstart) {
			if (!isset($options['d_format'])) $options['d_format'] = 'l, j M Y';
			if (!isset($options['d_separator'])) $options['d_separator'] = ' — ';
			if (!isset($options['t_format'])) $options['t_format'] = 'g:ia';
			if (!isset($options['t_separator'])) $options['t_separator'] = ' - ';
			if (!isset($options['dt_separator'])) $options['dt_separator'] = ', ';
			if ($dtstart && strpos($dtstart,'-')) $dtstart = strtotime($dtstart);
			if ($dtend && strpos($dtend,'-')) $dtend = strtotime($dtend);
			
			// Make sure event doesn't end before it starts!
			if ($dtstart > $dtend) $dtend = $dtstart;
			// See if event is all day or starts/ends at the same time (endless)
			if ($dtstart == $dtend || (date('Ymd',$dtstart) == date('Ymd',$dtend) && date('Hi',$dtstart) == '0000' && date('Hi',$dtend) == '2359')) {
				$r = date($options['d_format'],$dtstart).(date('Hi',$dtstart) == '0000' ? '' : date($options['dt_separator'].$options['t_format'],$dtstart));
			}
			// Same day, different start/end times
			elseif (date('Ymd',$dtstart) == date('Ymd',$dtend)) {
				$r = date($options['d_format'].$options['dt_separator'].$options['t_format'],$dtstart).date($options['t_separator'].$options['t_format'],$dtend);
			}
			// Multiple all-day event
			elseif (date('Hi',$dtstart) == '0000' && (date('Hi',$dtend) == '0000' || date('Hi',$dtend) == '2359')) {
				$dtend = date('Hi',$dtend) == '0000' ? $dtend-86400 : $dtend;
				if (date('Ymd',$dtstart) == date('Ymd',$dtend)) {
					$r = date($options['d_format'],$dtstart);
				}
				else {
					$r = date($options['d_format'],$dtstart).$options['d_separator'].date($options['d_format'],$dtend);
				}
			}
			// Multi-day event
			else {
				//$r = date($options['d_format'].$options['dt_separator'].$options['t_format'],$dtstart).$options['d_separator'].date($options['d_format'].$options['dt_separator'].$options['t_format'],$dtend);
				$r = date($options['d_format'],$dtstart).$options['d_separator'].date($options['d_format'],$dtend);
			}
			return $r;
		}
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
	
	public static function getTimeDifference($start,$end)
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
		if ($time > 12) {
			return (12 - (24 - $time)) . ":PM";
		} elseif ($time == 12) {
			return 12 . ":PM";
		} else {
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
		if ($am_pm == "PM" && $time != 12) {
			return (12 + $time);
		} elseif ($am_pm == "AM" && $time == 12) {
			return ($time - 12);
		} else {
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
		if($size > 0){
			$filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
			return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
		}else{
			return '';
		}
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
	
	Function: validateDirectory
	
	Checks if upload directory exists. If not, the directory is created.
	
	Parameters:
	
		dir:String - the directory to check
		permissions:Integer - directory permissions (defaults to 0755)
		
	Returns:
	
		dir
		
	*/
	
	public static function validateDirectory($dir,$permissions=0755)
	{
		// if the upload directory doesn't exist, create it
		if(!is_dir($dir)) mkdir($dir,$permissions,true);
		return $dir;
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
		//need to build in the checkDirectory on the destination
		
		if($options['mode'] == 'jpg'){
			$src_img=ImageCreateFromJpeg($src_name);
		}
		if($options['mode'] == 'png'){
			$src_img=ImageCreateFromPng($src_name);
		}
		if($options['mode'] == 'gif'){
			$src_img=ImageCreateFromGif($src_name);
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
				
		if($options['mode'] == 'gif'){
			imagegif($dst_img,$dst_name,$options['quality']); 
		}		
		
		imagedestroy($dst_img); 
		imagedestroy($src_img); 
	}
	
	/*
	
	Function: stringToHex
	
	Converts string to hexidecimal
	Paul Tero, July 2001
	http://www.tero.co.uk/des/
	
	Parameters:
	
		s:String - string value		
	
	Returns:
	
		hexadecimal of string    
		
	*/
	
	public static function stringToHex($s)
	{
		$r = "0x";
		$hexes = array ("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");
		for ($i=0; $i<strlen($s); $i++) {
			$r .= ($hexes [(ord($s{$i}) >> 4)] . $hexes [(ord($s{$i}) & 0xf)]);
		}
		return $r;
	}
	
	
	/*
	
	Function: hexToString
	
	Converts hex string regular characters
	Paul Tero, July 2001
	http://www.tero.co.uk/des/
	
	Parameters:
	
		h:String - hex string value		
	
	Returns:
	
		string    
		
	*/

	public static function hexToString($h)
	{
		$r = "";
		for ($i= (substr($h, 0, 2)=="0x")?2:0; $i<strlen($h); $i+=2) {
			$r .= chr (base_convert (substr ($h, $i, 2), 16, 10));
		}
		return $r;
	}
	
	public static function uploadFile($name=null,$value=null,$options)
	{
		// Set file_root
		$file_root = (isset($options['file_root']) ? $options['file_root'].'/' : WEB_ROOT);
		// Set file_path
		if (isset($options['file_path'])) $file_path = $options['file_path'].'/';
		else {
			$file_path =  defined('UPLOAD_PATH') ? UPLOAD_PATH.'/' : 'files/';
			$file_path .= (isset($options['table']) && isset($options['col_name'])) ? $options['table'].'/'.$options['col_name'].'/' : '';
		}
		// Set file_prefix
		$file_prefix = isset($options['file_prefix']) ? $options['file_prefix'] : '';
		// Set file_suffix
		$file_suffix = isset($options['file_suffix']) ? $options['file_suffix'] : '';
		// Make sure file was actually uploaded
		if (is_uploaded_file($_FILES[$name]['tmp_name'])) {
			// Set file_extension
			$file_extension = isset($options['file_extension']) ? $options['file_extension'] : substr($_FILES[$name]['name'],strrpos($_FILES[$name]['name'],'.'));
			// Set file_name
			if (isset($options['file_key'])) {
				switch ($options['file_key']):
					case 'id':
						$file_name = $options['id'];
					break;
					default:
						$file_name = substr($_FILES[$name]['name'],0,strrpos($_FILES[$name]['name'],'.'));
					break;
				endswitch;
			} else {
				$file_name = substr($_FILES[$name]['name'],0,strrpos($_FILES[$name]['name'],'.'));
			}
			// Set dir & ext
			$dir = self::validateDirectory($file_root.$file_path).'/';
			$ext = $file_extension;
			// Set file -- Check to see if file already exists on server
			
			//$file = $file_prefix.$file_name.$file_suffix;
			
			$file = self::checkFileName($dir,$file_prefix.$file_name.$file_suffix,$ext,$options);
			if (move_uploaded_file($_FILES[$name]['tmp_name'],$dir.$file.$ext)) {
				return $file.$ext;
			}
		}
	}
	
	public static function checkFileName($dir,$file,$ext,$options)
	{
		if (file_exists($dir.$file.$ext) && $q = $options['db']->query("
			SELECT ".$options['col_name']."
			FROM ".$options['table']."
			WHERE id != '".$options['id']."'
				AND ".$options['col_name']." = '".$file.$ext."'
		")) {
			if (is_numeric($i = substr($file,strrpos($file,'_')+1))) $file = substr($file,0,strrpos($file,'_')+1).($i+1);
			else $file .= '_1';
			return self::checkFileName($dir,$file,$ext,$options);
		} else {
			return $file;
		}
	}
	
	public static function checkDimensions($file,$options)
	{
		$image_dimensions = getimagesize($file['tmp_name']);
		$errorsA = array();
		
		if(isset($options['height'])){
			if($image_dimensions[1] == $options['height']){
			
			}else{
				$errorsA[] = 'Incorrect File Height. File must = '.$options['height'].' px tall.';
			}
		}
		
		if(isset($options['width'])){
			if($image_dimensions[0] == $options['width']){
				
			}else{
				$errorsA[] = 'Incorrect File Width. File must = '.$options['width'].' px wide.';
			}
			
		}
		
		if(isset($options['max_height'])){
			if($image_dimensions[1] <= $options['max_height']){
			
			}else{
				$errorsA[] = 'Incorrect File Height. File must be <= '.$options['max_height'].' px tall.';
			}
		}
		
		if(isset($options['max_width'])){
			if($image_dimensions[0] <= $options['max_width']){
				
			}else{
				$errorsA[] = 'Incorrect File Width. File must be <= '.$options['max_width'].' px wide.';
			}
			
		}
		
		if(count($errorsA) > 0){
			return $errorsA;
		}else{
			return true;
		}
		
	}
	
	public static function validateFile($file,$options)
	{
		//first check to see if it's in the valid file type
		$ext = strtolower(substr($file['name'],strrpos($file['name'],'.')+1));
				
		$errorsA = array();
		
		//File Format (extension)
		if(isset($options['formats'])){
			//split it on ,
			$format = false;
			$tB = explode(',',$options['formats']);
			if(count($tB)>0){
				//check to see if it exists
				if(in_array($ext,$tB)){
					$format = true;
				}
			}else if($ext == $options['formats']){
				$format = true;
			}			
			
			if($format == true){
			
			}else{
				$errorsA[] = 'Incorrect File Format. File must be of type ['.$options['formats'].'].';
			}
		}
		
		if(isset($options['dimensions'])){
			//loop through to see what fits
			$safe_size = false;
			$t_errors = array();
			foreach($options['dimensions'] as $dimension){
				$t = self::checkDimensions($file,$dimension);
				if($t===true){
					$safe_size = true;
					break;						
				}else if(is_array($t)){
					$t_errors = array_merge($t_errors,$t);
				}
			}
			if($safe_size === false){
				$errorsA = array_merge($errorsA,$t_errors);
			}
		}
		
		/*
		
		if(isset($tA['check_size']) && $tA['check_size'] == 'true'){
			//
		}
		*/
		if(count($errorsA) > 0){
			return $errorsA;
		}else{
			return true;
			//all good
		}
		
	}
	
	/*
	
	Function: downloadFile
	
	Sends headers to download a file. Attempts to find a matching MIME type otherwise used application/force-download
	
	Parameters:
	
		file:String - complete path and filename
	
	*/
	
	public static function downloadFile($file)
	{
	
		$file_extension = strtolower(substr(strrchr($file,"."),1));

		switch ($file_extension) {
			case "pdf": $ctype="application/pdf"; break;
			case "exe": $ctype="application/octet-stream"; break;
			case "zip": $ctype="application/zip"; break;
			case "doc": $ctype="application/msword"; break;
			case "xls": $ctype="application/vnd.ms-excel"; break;
			case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
			case "gif": $ctype="image/gif"; break;
			case "png": $ctype="image/png"; break;
			case "jpeg":
			case "jpg": $ctype="image/jpg"; break;
			default: $ctype="application/force-download";
		}

		if (!file_exists($file)) {
			die("NO FILE HERE");
		}
	
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Type: $ctype");
		header("Content-Disposition: attachment; filename=\"".basename($file)."\";");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".@filesize($file));
		set_time_limit(0);
		readfile($file);
		exit();
	}
	
	/*
	
	Function: singulizer
	
	Changes a plural string to singular (currently only changes the last character(s) in the string)
	
	Parameters:
	
		str:String - word or words
	
	*/
	
	public static function singulizer($str)
	{
		return preg_replace(
			array('/ies$/','/s$/'),
			array('y',''),
			$str
		);
	}
	
	/*
	
	Function: titleCase
	
	Attempts to convert a string to title case
	
	Based on John Gruber's Perl script and Adam Nolley's PHP interpretation
	
	http://daringfireball.net/2008/05/title_case
	http://nanovivid.com/stuff/wordpress/title-case/
	
	Parameters:
	
		str:String - word or words
	
	*/
	
	public static function titleCase($str)
	{
		// Edit this list to change what words should be lowercase
		$small_words = "a an and as at but by en for if in of on or the to v[.]? via vs[.]?";
		$small_re = str_replace(" ", "|", $small_words);
		
		// Replace HTML entities for spaces and record their old positions
		$htmlspaces = "/&nbsp;|&#160;|&#32;/";
		$oldspaces = array();
		preg_match_all($htmlspaces, $str, $oldspaces, PREG_OFFSET_CAPTURE);
		
		// Remove HTML space entities
		$words = preg_replace($htmlspaces, " ", $str);
		
		// Split around sentance divider-ish stuff
		$words = preg_split('/( [:.;?!][ ] | (?:[ ]|^)["“])/x', $words, -1, PREG_SPLIT_DELIM_CAPTURE);
		
		for ($i = 0; $i < count($words); $i++) {
			// Skip words with dots in them like del.icio.us
			$words[$i] = preg_replace_callback(
				'/\b([[:alpha:]][[:lower:].\'’(&\#8217;)]*)\b/x',
				create_function(
					'$matches',
					'return preg_match("/[[:alpha:]] [.] [[:alpha:]]/x", $matches[0]) ? $matches[0] : ucfirst($matches[0]);'
				),
				$words[$i]
			);
			// Lowercase our list of small words
			$words[$i] = preg_replace("/\b($small_re)\b/ei", "strtolower(\"$1\")", $words[$i]);
			// If the first word in the title is a small word, capitalize it
			$words[$i] = preg_replace("/\A([[:punct:]]*)($small_re)\b/e", "\"$1\" . ucfirst(\"$2\")", $words[$i]);
			// If the last word in the title is a small word, capitalize it
			$words[$i] = preg_replace("/\b($small_re)([[:punct:]]*)\Z/e", "ucfirst(\"$1\") . \"$2\"", $words[$i]);
		}
		
		$words = join($words);
		
		// Oddities
		$words = preg_replace("/ V(s?)\. /i", " v$1. ", $words);                    // v, vs, v., and vs.
		$words = preg_replace("/(['’]|&#8217;)S\b/i", "$1s", $words);               // 's
		$words = preg_replace("/\b(AT&T|Q&A)\b/ie", "strtoupper(\"$1\")", $words);  // AT&T and Q&A
		$words = preg_replace("/-ing\b/i", "-ing", $words);                         // -ing
		$words = preg_replace("/(&[[:alpha:]]+;)/Ue", "strtolower(\"$1\")", $words);// html entities
		
		// Put HTML space entities back
		$offset = 0;
		for ($i = 0; $i < count($oldspaces[0]); $i++) {
			$offset = $oldspaces[0][$i][1];
			$words = substr($words, 0, $offset) . $oldspaces[0][$i][0] . substr($words, $offset + 1);
			$offset += strlen($oldspaces[0][$i][0]);
		}
		
		return $words;
	}
	
	
}

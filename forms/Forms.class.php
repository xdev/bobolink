<?php

class Forms
{
	
	public static function listManager($name,$value,$options)
	{
		self::hidden($name,htmlentities($value));
		
		if(!isset($options['mode'])){
			$mode = 'pair';
		}else{
			$mode = $options['mode'];
		}
		
		
		
		print '
		<script type="text/javascript">
			<!-- <![CDATA[ 
			listManager_' . $name .' = new listManager(
				{
					name: \'' . $name . '\',';
					
					
					if(isset($options['label_name'])){
						print 'label_name: \'' . $options['label_name'] . '\',';
					}
					
					if(isset($options['label_value'])){
						print 'label_value: \'' . $options['label_value'] . '\',';
					}
					
					print 'mode: \'' . $mode . '\'
				}
			);
			// ]]> -->
		</script>';
		
		$listManager = 'listManager_' . $name;
				
		$r = "<ul id=\"pairset_$name\" class=\"pairset\" >";
		$tA = explode("+_+",$value);
		
		
		if($mode == 'pair'){
			if(strlen($value) > 6){
				for($i=0;$i<count($tA);$i++){
					$iA = explode("*_*",$tA[$i]);
					if(!isset($iA[1])){
						$iA[1] = '';
					}
					$r .= '
					<li id="' .$name . '_' . $i .'" class="pair">
						<div class="handle"></div>
						<label class="label" >';
						(isset($options['label_name'])) ? $r .= $options['label_name'] : $r .= 'Name';
						$r .= '</label>
						<div class="input" onclick="' . $listManager . '.editItem(this);"><pre>'.$iA[0].'</pre></div>
						<label class="label">';
						(isset($options['label_value'])) ? $r .= $options['label_value'] : $r .= 'Value';						
						$r .= '</label>
						<div class="input" onclick="'. $listManager . '.editItem(this);"><pre>'.$iA[1].'</pre></div>
						<a class="icon delete" style="float: left;" href="#" onclick="' . $listManager . '.deleteItem(this); return false;">Delete</a>
					</li>';
					
				}
				
			}
		}
		if($mode == 'single'){
			if(strlen($value) > 6){
				for($i=0;$i<count($tA);$i++){
					$r .= '
					<li id="' .$name . '_' . $i .'" class="pair single">
						<div class="handle"></div>
						<label class="label" >';
						(isset($options['label_name'])) ? $r .= $options['label_name'] : $r .= 'Name';
						$r .= '</label>
						<div class="input" onclick="' . $listManager . '.editItem(this);"><pre>'.$tA[$i].'</pre></div>						
						<a class="icon delete" style="float: left;" href="#" onclick="' . $listManager . '.deleteItem(this); return false;">Delete</a>
					</li>';
				}
			}		
		}
		
				
		$r .= '</ul><div style="clear:both;">
					<a class="icon new" href="#new_pair" onclick="' . $listManager . '.addItem(); return false;">Add Pair</a>
				</div>';
		
		self::buildElement($name,$r,$options);
		
		
		print '<script type="text/javascript">
				<!-- <![CDATA[ 
				Sortable.create(\'pairset_' . $name . '\',
     			{constraint:"vertical",
     	
     			onUpdate:function(){
     				' . $listManager .'.update();
     			}
          
     			});
     			// ]]> -->
     			</script>';
	
	
	}
	
	
	
	
	/**
	* init
	* prints html for start of form tag
	*
	* @param   string   action
	* @param   string   name
	* @param   string   type = "" (multipart)
	* @param   string   method = "post"
	*
	*/
	
	public static function init($action,$name="myform",$type="",$method="post", $target="")
	{
		if($type == "multipart"){
			$type = "enctype=\"multipart/form-data\"";
		}
		//name=\"$name\"
		print "<form id=\"$name\" $type action=\"$action\" method=\"$method\" $target >";
	
	}
	
	
	/**
	* buildElement
	* prints html for standard item div
	*	
	* @param   string   label
	* @param   string   name
	* @param   string   content
	* @param   string   tip
	*
	*/
	
	public static function buildElement($name,$content,$options)
	{
		
		printf(
			'
			<div class="form_item" id="form_item_%s">
				<label for="%s"%s>%s</label>
				<div class="input">
					%s
					%s
				</div>
			</div>
			',
			$name,
			$name,
			isset($options['validate']) && $options['validate'] ? ' class="must_validate '.$options['validate'].'"' : '',
			$options['label'],
			$content,
			isset($options['tip']) ? '<p class="tip">'.$options['tip'].'</p>' : ''
		);
		
		/*
		print "<div class=\"form_item\" id=\"form_item_".$name."\">";
		
		if(isset($options['tip'])){
			(isset($options['validate'])) ? $class = ' must_validate' : $class = '';
			print "<label class=\"tip" . $class . "\" title=\"" . $options['tip'] . "\" for=\"$name\"><span>$options[label]</span></label>";
		}else{
			(isset($options['validate'])) ? $class = 'class="must_validate ' . $options['validate'] . '"' : $class = '';
			print"<label for=\"$name\" $class>$options[label]</label>";
		}
		
		print "<div class=\"input\">$content</div>
		</div>";
		*/
	}
		
	
	public static function closeTag()
	{
		print "</form>";
	}
	
	
	public static function buttons()
	{
		global $label;
		
		print '<div class="buttons">';
		self::button("submit","Submit");
		self::button("button","Cancel","cancel()");
		print '</div>';
	
	}
	
	public static function buttonsRelated()
	{
		global $label;
		
		print '<div class="buttons">';
		self::button("submit",$label);
		self::button("button","Cancel","cancelRelated()");
		print '</div>';
	
	}
	
	
	public static function fileField($name,$value="",$options)
	{
		self::buildElement($name,"<input type=\"file\" class=\"file\" id=\"$name\" name=\"$name\" />",$options);
	}
	
	/**
	* static
	* 
	* @param   string   label
	* @param   string   value 
	*
	*/
	
	
	public static function readonly($name,$value = "",$options)
	{
		$t = $name . "_static";
		
		self::buildElement($t,"<span id=\"$t\">$value</span><input type=\"hidden\" id=\"$name\" name=\"$name\" value=\"$value\" />",$options);
		
		/*
		print("
		<div class=\"form_item\">
			<label for=\"$t\">$label</label>
			<div class=\"input\" id=\"$t\" >$value</div>
		</div>
		");
		*/
	
	}
	
	/**
	* text
	* prints html for text input
	*
	* @param   string   label
	* @param   string   variable name
	* @param   string   value
	* @param   array    options ( size , max , class , type , tip )
	*
	*/
	
	public static function text($name,$value = "",$options)
	{
		(isset($options['size'])) ? $size = $options['size'] : $size = 60;
		(isset($options['max'])) ? $max = $options['max'] : $max = 255;
		(isset($options['class'])) ? $class = $options['class'] : $class = "text";
		(isset($options['type'])) ? $type = $options['type'] : $type = "text";
		
		(isset($options['validate'])) ? $class .= ' validate ' . $options['validate'] : '';

		$value = htmlentities($value);
		self::buildElement($name,"<input type=\"$type\" class=\"$class\" name=\"$name\" id=\"$name\" value=\"$value\" size=\"$size\" maxlength=\"$max\" />",$options);
	
	}
	
	/**
	* textarea
	* prints html for textarea input
	*
	* @param   string   label
	* @param   string   variable name
	* @param   string   value
	* @param   string   rows
	* @param   string   cols
	*
	*/
	
	public static function textarea($name,$value = "",$options)
	{
		(isset($options['rows'])) ? $rows = $options['rows'] : $rows = 5;
		(isset($options['cols'])) ? $cols = $options['cols'] : $cols = 60;
		(isset($options['class'])) ? $class = $options['class'] : $class = "";
		
		(isset($options['validate'])) ? $class .= ' validate ' . $options['validate'] : '';
		
		self::buildElement($name,"<textarea name=\"$name\" id=\"$name\" class=\"$class\" rows=\"$rows\" cols=\"$cols\" >$value</textarea>",$options);
		
	}
	
	/**
	* checkbox
	* prints html for text input
	*
	* @param   string   label
	* @param   string   variable name
	* @param   string   value
	* @param   string   default = "Y"
	*
	*/
	
	public static function checkbox($name,$value = "",$options)
	{
		(isset($options['def'])) ? $def = $options['def'] : $def = "Y";
		
		
		$value == $def ? $checked = "checked=\"checked\"" : $checked = "";
		
		self::buildElement($name,"<input type=\"checkbox\" name=\"$name\" id=\"$name\" value=\"$def\" $checked />",$options);
	
	}
	
	/**
	* checkboxBasic
	* prints html for text input w/out surrounding div
	*
	* @param   string   label
	* @param   string   variable name
	* @param   string   value
	* @param   string   default = "Y"
	*
	*/
	
	public static function checkboxBasic($name,$value = "",$options)
	{
		(isset($options['def'])) ? $def = $options['def'] : $def = 'Y';
		(isset($options['class'])) ? $class = $options['class'] : $class = 'checkbox';
		$value == $def ? $checked = "checked=\"checked\"" : $checked = "";
		//$def == "Y" ? $checked = "checked=\"checked\"" : $checked = "";
		return("
		<input type=\"checkbox\" class=\"$class\" name=\"$name\" id=\"$name\" value=\"$def\" $checked />$options[label]
		");
	
	}
	
	/**
	* button
	* prints html for button
	*
	* @param   string   type (button or submit)
	* @param   string   value
	* @param   string   javascript onclick
	*
	*/
	
	public static function button($type,$value,$click="")
	{
		if($click != "") $click = "onclick=\"$click;\"";	
		print("
		<input type=\"$type\" value=\"$value\" $click />	
		");
	
	}
	
	/**
	* hidden
	* prints html for hidden form variable
	*
	* @param   string   variable name
	* @param   string   value
	*
	*/
	
	public static function hidden($name,$value,$options=null)
	{
		print '<input type="hidden" ';
		if(!isset($options['omit_id'])){
			print 'id="' . $name . '" ';
		}
		print 'name="' . $name . '" value="' . $value .'" />';
	}
	
	
	/**
	* boolean
	* prints html for boolean pull down
	*
	* @param   string   label
	* @param   string   variable name
	* @param   number   value
	* @param   number   default value = 1
	*
	*/
	
	public static function boolean($name,$value = "",$options)
	{
		(isset($options['def'])) ? $def = $options['def'] : $def = 1;
		
		
		if($value == ""){
			$value = $def;
		}
		
		if($value == 1){ $active2 = ""; $active1 = "selected=\"selected\"";}
		if($value == 0){ $active1 = ""; $active2 = "selected=\"selected\"";}
			
		$r = "
		<select name=\"$name\" id=\"$name\">
			<option value=\"1\" $active1>True</option>
			<option value=\"0\" $active2>False</option>
		</select>";
		
		self::buildElement($name,$r,$options);
		
	
	}
	
	/**
	* select 
	* prints html for select - uses self::buildHalf to print
	*
	* @param   string   label
	* @param   string   variable name
	* @param   string   sql record set query
	* @param   string   col for value
	* @param   string   col for display ( can be multiple w/ | )
	* @param   string   value
	* @param   string   javascript onchange
	*
	*/
	
	public static function selectDefault($name,$value,$options)
	{
		(isset($options['select_sql'])) ? $data_source = $options['select_sql'] : $data_source = false;
		(isset($options['col_value'])) ? $col_value = $options['col_value'] : $col_value = "id";
		(isset($options['col_display'])) ? $col_display = $options['col_display'] : $col_display = "name";
		(isset($options['onchange'])) ? $onchange = "onchange=\"$otions[onchange]\"" : $onchange = "";
		(isset($options['class'])) ? $class = $options['class'] : $class = "";
		
		(isset($options['validate'])) ? $class .= ' validate ' . $options['validate'] : '';
		
		$r = "<select name=\"$name\" id=\"$name\" class=\"$class\" $onchange>";
		$r .= '<option value="">(none)</option>';
		$q = Db::query($data_source);
		$dA = explode("|",$col_display);
		
		//$r .= "<option value=\"0\">(none)</option>";
		if($q){
			for($i=0;$i<count($q);$i++){
		
				$item = $q[$i];
				
				(trim($value) == trim($item[$col_value])) ? $selected = "selected=\"selected\"" : $selected = "";	
				$tv = $item[$col_value];
				if(count($dA) > 1){
					$td = "";
					for($j=0;$j<count($dA);$j++){
						$td .= $item[$dA[$j]];
						if($j<count($dA) - 1){
							$td .= " - ";
						}
					}
				}else{
				
					$td = $item[$col_display];
				}
				
				$r .= "<option value=\"$tv\" $selected >$td</option>";
			
			}
		}
	
		$r .= "</select>";
	
		self::buildElement($name,$r,$options);
	}
	
	/**
	* selectFiles
	* prints html for select - uses self::buildHalf to print
	*
	* @param   string   label
	* @param   string   variable name
	* @param   string   folder
	* @param   string   value
	*
	*/
	
	public static function selectFiles($name,$value,$options)
	{
		(isset($options['folder'])) ? $folder = $otions['folder'] : $folder = false;
		
		//folder
		
		$tA = (Utils::listDirectory($folder));
		$r = "<select name=\"$name\" id=\"$name\">";
		
		//find last index of the / and use that index for the substr
		$p = strrpos($folder,"/") + 2;
		$r .= '<option value="">(none)</option>';
		
		for($i=0;$i<count($tA);$i++){
			$file = substr($tA[$i],$p);
			
			if($value == $file){
				$sel = "selected=\"selected\"";
			}else{
				$sel = "";
			}
			
			$r .= "<option value=\"$file\" $sel >$file</option>";
		}
	
		$r .= "</select>";
	
		self::buildElement($name,$r,$options);
		
	}
	
	
	
	/**
	* selectStatic
	* prints html for select - uses self::buildHalf to print
	*
	* @param   string   label
	* @param   string   variable name
	* @param   string   data array
	* @param   string   value
	* @param   string   javascript onchange
	*
	*/
	
	public static function selectStatic($name,$value,$options)
	{	
		(isset($options['dataA'])) ? $dataA = $options['dataA'] : $dataA = array();
		(isset($options['onchange'])) ? $onchange = "onchange=\"$otions[onchange]\"" : $onchange = "";
		
		
		$r = "<select name=\"$name\" id=\"$name\" $onchange>";
		
		for($i=0;$i<count($dataA);$i++){
			$_label = $dataA[$i][0];
			$_value = $dataA[$i][1];
			(trim($value) == trim($_value)) ? $selected = "selected=\"selected\"" : $selected = "";	
			$r .= "<option value=\"$_value\" $selected >$_label</option>";
		}
	
		$r .= "</select>";
	
		self::buildElement($name,$r,$options);
	}
	
	/**
	* selectState
	* prints html for select - uses self::buildHalf to print
	* pulls from static file 'states.inc'
	*
	* @param   string   label
	* @param   string   variable name
	* @param   string   value
	* @param   number   country
	* @param   string   mode
	*
	*/
	
	public static function selectState($name="state",$value,$options)
	{
		(isset($options['country'])) ? $country = $options['country'] : $country = 227;
		(isset($options['mode'])) ? $mode = $options['mode'] : $mode = "";
		
		
		$q = Db::query("SELECT * FROM cms_states WHERE country_id = '$country' ORDER BY name");
		
		$r = "<select name=\"$name\" id=\"$name\">";
		$r .= "<option value=\"0\">----------</option>";
		foreach($q as $state){
			
			$item = $state['name'];
			
			$display = $state['name'];
			(trim($value) == trim($item) || (trim($value) == $state['abbreviation'])) ? $selected = "selected=\"selected\"" : $selected = "";	
			$r .= "<option value=\"$item\" $selected >$display</option>";
		}
		
		$r .= "</select>";
		
		if($mode == "simple"){
			return $r;
		}else{
			self::buildElement($name,$r,$options);
		}
	}
		
	
	/**
	* selectCountry
	* prints html for select - uses self::buildHalf to print
	* requires installation of countries table
	*
	* @param   string   label
	* @param   string   variable name
	* @param   string   value
	*
	*/
	
	public static function selectCountry($name="country",$value,$options)
	{
		(isset($options['mode'])) ? $mode = $options['mode'] : $mode = "";
		
		
		$q = Db::query("SELECT * FROM `cms_countries` WHERE c_id = 'US' OR c_id = 'GB' OR c_id = 'CA' OR c_id = 'MX' ORDER BY name DESC");
		$tl = count($q);
		$r = '<select name="'.$name.'" id="'.$name.'" onchange="toggleCountry();">';
			
		$sel = false;
		for($i=0;$i<$tl;$i++){
			$row = $q[$i];
			if($row['id'] == $value){ $selected = "selected=\"selected\""; $sel = true;}else  {$selected = "";}
			$r .= "<option value=\"$row[id]\" $selected >$row[name]</option>";
		}
		
			
		$r .= "<option value=\"0\">----------------------------------</option>";
			
		
		$q = Db::query("SELECT * FROM `cms_countries` WHERE active = 1");
		$tl = count($q);
			
		for($i=0;$i<$tl;$i++){
			$row = $q[$i];
			$selected2 = "";
			if($sel == false){ ($row['id'] == $value) ? $selected2 = "selected=\"selected\"" : $selected2 = ""; }
			$r .= "<option value=\"$row[id]\" $selected2 >$row[name]</option>";
		}
		
		$r .= "</select>";
		
		if($mode == "simple"){
			return $country;
		}else{	
			self::buildElement($name,$r,$options);
		}
	
	}
	
	
	public static function selectDateTime($name="",$value,$options)
	{
				
		if($value == ''){
			$value = Utils::dateNow();
		}
		
		$tA = explode(" ",$value);
		$tD = explode("-",$tA[0]);
		$date = array($tD[1],$tD[2],$tD[0]);
		$tD = explode(":",$tA[1]);
		$t = explode(":",Utils::time_24_12($tD[0]));
		$time = array($t[0],$tD[1],$t[1]);
	
			
		$r  = self::dateComponent($date,$name,$options['name_space']);
		$r .= self::timeComponent($time,$name,$options['name_space']);
		
		//this is only for validaton from the label for attribute
		$r .= '<span id="' . $name . '" />';
			
		self::buildElement($name,$r,$options);
	
	}
	
	public static function selectDate($name="",$value,$options)
	{
		
		if($value == ''){
			$value = Utils::dateNow();
		}
		
		$tD = explode("-",$value);
		$date = array($tD[1],$tD[2],$tD[0]);
		
		$r  = self::dateComponent($date,$name,$options['name_space']);
		self::buildElement($name,$r,$options);
	}
	
	public static function getMonths()
	{
		return array("January","February","March","April","May","June","July","August","September","October","November","December");
	}
	
	
	/**
	* dateComponent
	* prints html for select
	* uses info in config.inc for CMS_MIN and CMS_MAX_YEAR
	* 
	* @param   string   date
	* @param   string   variable name suffix
	* 
	* @return  string   html to print
	*
	*/
	
	public static function dateComponent($date,$name,$name_space)
	{

		$r = "<select name=\""  . "month_$name\" class=\"noparse\" >";
		$monthA = self::getMonths();
		for($i=1;$i<13;$i++){
			($i == $date[0]) ? $selected="selected=\"selected\"" : $selected = "";
			$v = $monthA[($i-1)];
			$r .= "<option value=\"$i\" $selected >$v</option>";
		}
		
		$r .= '</select>';
		$r .= '/';
		$r .= "<select name=\"" . "day_$name\" class=\"noparse\" >";
		
		for($i=1;$i<32;$i++){
			($i == $date[1]) ? $selected="selected=\"selected\"" : $selected = "";
			$r .= "<option value=\"$i\" $selected >$i</option>";
		}
		
		$r .= '</select>';
		$r .= '/';
		$r .= "<select name=\"" .  "year_$name\" class=\"noparse\" >";
		
		($date[2] < CMS_MIN_YEAR) ? $min = $date[2] : $min = CMS_MIN_YEAR;
		($date[2] > CMS_MAX_YEAR) ? $max = $date[2] : $max = CMS_MAX_YEAR;
		
		if($date[2] == ""){
			$min = CMS_MIN_YEAR;
			$max = CMS_MAX_YEAR;
		}
		
		for($i=$min;$i< $max;$i++){
			($i == $date[2]) ? $selected="selected=\"selected\"" : $selected = "";
			$r .= "<option value=\"$i\" $selected >$i</option>";
		}
		
		$r .= '</select>';
		
		return $r;
	}
	
	/**
	* timeComponent
	* prints html for select
	*
	* @param   string   time
	* @param   string   variable name suffix
	* 
	* @return  string   html to print
	*
	*/
	
	public static function timeComponent($time,$name,$name_space)
	{
		
		$r = "";
		$r .= "<select name=\"" . "hour_$name\" class=\"noparse\" >";
		
		for($i=1;$i<13;$i++){
			($i == $time[0]) ? $selected="selected=\"selected\"" : $selected = "";
			$r .= "<option value=\"$i\" $selected >$i</option>";
		}
		
		
		$r .= '</select>';
		$r .= ':';
		$r .= "<select name=\"" .  "minute_$name\" class=\"noparse\" >";
		
		//$ta = array("00","05","10","15","20","25","30","35","40","45","50","55");
		
		for($i=0;$i<60;$i++){
			($i == $time[1]) ? $selected="selected=\"selected\"" : $selected = "";
			$v = Utils::padZero($i);
			$r .= "<option value=\"$i\" $selected >$v</option>";
		}
		
		$r .= '</select>';
		
		$r .= "<select name=\"" .  "am_pm_$name\" class=\"noparse\" >";
		
		$ta = array("AM","PM");
		for($i=0;$i<2;$i++){
			($ta[$i] == $time[2]) ? $selected="selected=\"selected\"" : $selected = "";
			$r .= "<option value=\"$ta[$i]\" $selected >$ta[$i]</option>";
		}
		$r .= '</select>';
		return $r;
	
	}
	
	/**
	* time
	* prints html for select - uses self::buildHalf to print
	*
	* @param   string   label
	* @param   string   name
	* @param   string   value
	*
	*/
	
	public static function selectTime($name="",$value,$options)
	{
		
		
		$tA = explode(":",$value);
		$time = array($tA[0],$tA[1],$tA[2]);
	
		$r = self::timeComponent($time,$name,$options['name_space']);
		
		self::buildElement($name,$r,$options);
		
	}
	
	/**
	* date
	* prints html for select - uses self::buildHalf to print
	*
	* @param   string   label
	* @param   string   name
	* @param   string   value
	*
	*/
	
	public static function dateDefault($name="",$value,$options)
	{
		
		
		if($value == ""){
			if($name == "start_event" || $name == "event_date"){			
				$value = $utils->date_today();
			}
			if($name == "end_event"){			
				$value = $utils->date_today();
			}
		}
		
		$tA = explode("-",$value);
		$date = array($tA[1],$tA[2],$tA[0]);
		
		$r = self::dateComponent($date,$name);
		
		self::buildElement($name,$r,$options);
	
	}

}

?>
<?php

/*

$Id$

Class: Forms

Collection of functions dealing with creating form elements

Typical use is as follows
	
	(start code)
	
	formMethod($name,$value,$options)
	
	(end)

*/

class Forms
{

	/*
	
	Function: listManager
	
	prints html for start of form tag
	
	* @param   string   action
	* @param   string   name
	* @param   string   type = "" (multipart)
	* @param   string   method = "post"
	*
	*/
	
	public static function listManager($name,$value,$options)
	{
		self::hidden($name,preg_replace('["]',htmlentities('"'),$value));
		
		if(!isset($options['mode'])){
			$mode = 'pair';
		}else{
			$mode = $options['mode'];
		}
		
		
		
		print '
		<script type="text/javascript">
			
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
			
		</script>';
		
		$listManager = 'listManager_' . $name;
				
		$r = "<ul id=\"pairset_$name\" class=\"pairset\" >";
		
		/*
		<data>
			<item>
				<name>Test</name>
				<value>bla.jpg</value>
			</item>
			<item>
				<name>Something</name>
				<value>wfw.jpg</value>
			</item>
		</data>
		*/
		
		$itemsA = array();
		/*
		$xml = simplexml_load_string($value);
		if($xml){
			foreach($xml->item as $item){
				$itemsA[] = array('name'=>$item->name,'value'=>$item->value);					
			}
		}
		*/
		
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
						(isset($options['label_value'])) ? $r .= $options['label_value'] : $r .= 'Value';
						$r .= '</label>
						<div class="input" onclick="' . $listManager . '.editItem(this);"><pre>'.$tA[$i].'</pre></div>						
						<a class="icon delete" style="float: left;" href="#" onclick="' . $listManager . '.deleteItem(this); return false;">Delete</a>
					</li>';
				}
			}		
		}
		
		if($mode == 'images'){
			$i=0;
			foreach($itemsA as $item){
			
				$r .= '
				<li id="' .$name . '_' . $i .'" class="pair images">
					<div class="handle"></div>
					<label class="label" >';
					(isset($options['label_name'])) ? $r .= $options['label_name'] : $r .= 'Name';
					$r .= '</label>
					<div class="input" onclick="' . $listManager . '.editItem(this);"><pre>'.$item['name'].'</pre></div>
					<label class="label">Image</label>
					<input type="file" class="img" id="' . $name . '_' .$i . '_img" name="' . $name . '_' . $i . '_img" />				
					<a class="icon delete" style="float: left;" href="#" onclick="' . $listManager . '.deleteItem(this); return false;">Delete</a>
				</li>';
				$i++;
			}
		}
		
				
		$r .= '</ul><div style="clear:both;">
					<a class="icon new" href="#new_pair" onclick="' . $listManager . '.addItem(); return false;">Add Pair</a>
				</div>';
		
		self::buildElement($name,$r,$options);
		
		
		print '<script type="text/javascript">
				
				Sortable.create(\'pairset_' . $name . '\',
     			{constraint:"vertical",
     	
     			onUpdate:function(){
     				' . $listManager .'.update();
     			}
          
     			});
     			
     			</script>';
	
	
	}
	
	
	
	
	/*
	
	Function: init
	
	prints html for start of form tag
	
	Parameters:
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
		//
		print "<form id=\"$name\" name=\"$name\" $type action=\"$action\" method=\"$method\" $target >";
	
	}
	
	
	/*
	
	Function: buildElement
	
	prints html for standard item div
	
	Parameters:
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
			<div class="form_item" id="form_item_%s" %s>
				<label for="%s"%s>%s</label>
				<div class="input">
					%s
					%s
				</div>
			</div>
			',
			$name,
			isset($options['style_main']) ? 'style="' . $options['style_main'] . '"' : '',
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
	
	/*
	
	Function: closeTag
	
	Consider dropping.. totally useless
	
	*/
	
	public static function closeTag()
	{
		print "</form>";
	}
	
	/*
	
	Function: buttons
	
	Consider dropping.. totally useless
	
	*/
	
	public static function buttons()
	{
		global $label;
		
		print '<div class="buttons">';
		self::button("submit","Submit");
		self::button("button","Cancel","cancel()");
		print '</div>';
	
	}
	
	/*
	
	Function: buttonsRelated
	
	Consider dropping.. totally useless
	
	*/
	
	public static function buttonsRelated()
	{
		global $label;
		
		print '<div class="buttons">';
		self::button("submit",$label);
		self::button("button","Cancel","cancelRelated()");
		print '</div>';
	
	}
	
	/*
	
	Function: fileField
	
	file field input
	
	*/
	
	public static function fileField($name,$value="",$options)
	{
		// Set file options
		$options['file_prefix'] = isset($options['file_prefix']) ? $options['file_prefix'] : '';
		if (isset($options['file_key'])) {
			switch ($options['file_key']):
				case 'value':
					$options['file_key'] = $value;
				break;
				default:
					$options['file_key'] = $options['id'];
				break;
			endswitch;
		} else {
			$options['file_key'] = $options['id'];
		}
		$options['file_extension'] = isset($options['file_extension']) ? $options['file_extension'] : '';
		$options['file_preview'] = isset($options['file_preview']) ? $options['file_preview'] : '';
		// Set file URI
		$file = '/files/'.$options['table'].'/'.$options['col_name'].'/'.$options['file_prefix'].$options['file_key'].$options['file_extension'];
		// File field
		$r = sprintf(
			'<input type="file" class="file" id="%s" name="%s" />',
			$name,
			$name
		);
		// If file already uploaded, include delete and additional markup
		if ($value) {
			$r = sprintf(
				'<a href="%s">View/download file</a>&nbsp;&nbsp;|&nbsp;&nbsp;<input type="checkbox" id="%s_delete" name="%s_delete" />&nbsp;Delete file&nbsp;&nbsp;|&nbsp;&nbsp;Replace file: %s',
				$file,
				$name,
				$name,
				$r
			);
			// If preview config is set, show preview of file.
			if (isset($options['file_preview'])) {
				switch ($options['file_preview']):
					case 'jpg':
						$r .= sprintf(
							'<br /><img src="%s" alt="JPG" />',
							$file
						);
					break;
				endswitch;
			}
		}
		self::buildElement($name,$r,$options);
	}
	
	/*
	
	Function: readonly
	
	readonly value
	
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
	
	/*
	
	Function: text
	
	text input
	
	*/
	
	public static function text($name,$value = "",$options)
	{
		(isset($options['size'])) ? $size = $options['size'] : $size = 60;
		(isset($options['max'])) ? $max = $options['max'] : $max = 255;
		(isset($options['class'])) ? $class = $options['class'] : $class = "text";
		(isset($options['type'])) ? $type = $options['type'] : $type = "text";
		
		(isset($options['validate'])) ? $class .= ' validate ' . $options['validate'] : '';
		
		$value = preg_replace('["]',htmlentities('"'),$value);
		self::buildElement($name,'<input type="' . $type . '" class="' . $class . '" name="' . $name . '" id="' . $name . '" value="' . $value . '" size="' . $size . '" maxlength="' . $max . '" />',$options);
	
	}
	
	/*
	
	Function: textarea
	
	textarea input
	
	*/
	
	public static function textarea($name,$value = "",$options)
	{
		(isset($options['rows'])) ? $rows = $options['rows'] : $rows = 5;
		(isset($options['cols'])) ? $cols = $options['cols'] : $cols = 60;
		(isset($options['class'])) ? $class = $options['class'] : $class = "";
		
		(isset($options['validate'])) ? $class .= ' validate ' . $options['validate'] : '';
		
		self::buildElement($name,"<textarea name=\"$name\" id=\"$name\" class=\"$class\" rows=\"$rows\" cols=\"$cols\" >$value</textarea>",$options);
		
	}
	
	/*
	
	Function: checkbox
	
	checkbox input
	
	*/
	
	public static function checkbox($name,$value = "",$options)
	{
		(isset($options['def'])) ? $def = $options['def'] : $def = "Y";
		
		
		$value == $def ? $checked = "checked=\"checked\"" : $checked = "";
		
		self::buildElement($name,"<input type=\"checkbox\" name=\"$name\" id=\"$name\" value=\"$def\" $checked />",$options);
	
	}
	
	/*
	
	Function: checkboxBasic
	
	checkbox input w/out surrounding div

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
	
	/*
	
	Function: button
	
	input type button
	
	Parameters:
	
		type:String - type (button || submit)
		valule:String - label
		click:String - javascript or anything else to be added
		
	*/
	
	public static function button($type,$value,$click="")
	{
		if($click != "") $click = "onclick=\"$click;\"";	
		print("
		<input type=\"$type\" value=\"$value\" $click />	
		");
	
	}
	
	/*
	
	Function: hidden
	
	prints html for hidden form variable

	*/
	
	public static function hidden($name,$value,$options=null)
	{
		print '<input type="hidden" ';
		if(!isset($options['omit_id'])){
			print 'id="' . $name . '" ';
		}
		print 'name="' . $name . '" value="' . $value .'" />';
	}
	
	/*
	
	Function: boolean
	
	boolean pull down
	
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
	
	/*
	
	Function: selectDefault
	
	builds a select dropdown based upon info for a query
	
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
		$data_source = isset($options['select_sql']) ? $options['select_sql'] : false;
		$col_value = isset($options['col_value']) ? $options['col_value'] : "id";
		$col_display = isset($options['col_display']) ? $options['col_display'] : "name";
		$class = (isset($options['class']) ? $options['class'] : '') . (isset($options['validate']) ? ' validate ' . $options['validate'] : '');
		
		$r = sprintf(
			'<select name="%s" id="%s" class="%s" %s>',
			$name,
			$name,
			$class,
			isset($options['onchange']) ? "onchange=\"$options[onchange]\"" : ''
		);
		$r .= '<option value="">(none)</option>';
		$dA = explode("|",$col_display);
		
		if ($q = $options['db']->query($data_source)) {
			foreach ($q as $row) {
				
				(trim($value) == trim($row[$col_value])) ? $selected = "selected=\"selected\"" : $selected = "";
				$tv = $row[$col_value];
				if (count($dA) > 1) {
					$td = "";
					for ($j=0;$j<count($dA);$j++) {
						$td .= $row[$dA[$j]];
						if ($j<count($dA) - 1) {
							$td .= " - ";
						}
					}
				} else {
					$td = $row[$col_display];
				}
				
				$r .= "<option value=\"$tv\" $selected >$td</option>";
			
			}
		}
		
		$r .= "</select>";
		
		self::buildElement($name,$r,$options);
	}
	
	/*
	
	Function: selectParent
	
	builds a nested select dropdown based upon info for a query
	
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
	
	public static function selectParent($name,$value,$options)
	{
		$data_source = isset($options['select_sql']) ? $options['select_sql'] : false;
		$col_value = isset($options['col_value']) ? $options['col_value'] : "id";
		$col_display = isset($options['col_display']) ? $options['col_display'] : "name";
		$class = (isset($options['class']) ? $options['class'] : '') . (isset($options['validate']) ? ' validate ' . $options['validate'] : '');
		
		$r = sprintf(
			'<select name="%s" id="%s" class="%s" %s>',
			$name,
			$name,
			$class,
			isset($options['onchange']) ? "onchange=\"$options[onchange]\"" : ''
		);
		$r .= '<option value="">(none)</option>';
		
		function getChildren($name,$value,$options,$parent=0,$depth='',$parent_field_name='parent_id')
		{
			//if (isset($options['parent_field_name']) && $options['parent_field_name']) ;
			$dA = explode("|",$options['col_display']);
			$r = '';
			if ($q = $options['db']->query(str_replace('ORDER BY','WHERE '.$parent_field_name.' = '.$parent.' ORDER BY',$options['select_sql']))) {
				
				foreach ($q as $row) {
					(trim($value) == trim($row[$options['col_value']])) ? $selected = "selected=\"selected\"" : $selected = "";
					$tv = $row[$options['col_value']];
					if (count($dA) > 1) {
						$td = "";
						for ($j=0;$j<count($dA);$j++) {
							$td .= $row[$dA[$j]];
							if ($j<count($dA) - 1) {
								$td .= " - ";
							}
						}
					} else {
						$td = $row[$options['col_display']];
					}
				
					$r .= sprintf(
						'<option value="%s" %s>%s</option>',
						$tv,
						$selected,
						$depth.$td
					);
					$r .= getChildren($name,$value,$options,$row[$options['col_value']],$depth.'— ',$parent_field_name);
			
				}
			}
			return $r;
		}
		
		$r .= getChildren($name,$value,$options,0,'',$options['parent_field_name']);
		
		$r .= "</select>";
		
		self::buildElement($name,$r,$options);
	}
	
	/*
	
	Function: selectFiles
	
	Scans a folder and creates a select input
	
	*/
	
	public static function selectFiles($name,$value,$options)
	{
		(isset($options['folder'])) ? $folder = $options['folder'] : $folder = false;
		
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
	
	/*
	
	Function: selectStatic
	
	builds a select from an existing array
	
	*/
	
	public static function selectStatic($name,$value,$options)
	{	
		(isset($options['dataA'])) ? $dataA = $options['dataA'] : $dataA = array();
		(isset($options['onchange'])) ? $onchange = "onchange=\"$otions[onchange]\"" : $onchange = "";
		(isset($options['class'])) ? $class = $options['class'] : $class = "";
		
		$r = "<select name=\"$name\" id=\"$name\" class=\"$class\" $onchange>";
		
		for($i=0;$i<count($dataA);$i++){
			$_label = $dataA[$i][0];
			$_value = $dataA[$i][1];
			(trim($value) == trim($_value)) ? $selected = "selected=\"selected\"" : $selected = "";	
			$r .= "<option value=\"$_value\" $selected >$_label</option>";
		}
	
		$r .= "</select>";
	
		self::buildElement($name,$r,$options);
	}
	
	/*
	
	Function: selectState
	
	select state (uses cms_states for datasource)
	
	*/
	
	public static function selectState($name="state",$value,$options)
	{
		(isset($options['country'])) ? $country = $options['country'] : $country = 227;
		(isset($options['mode'])) ? $mode = $options['mode'] : $mode = "";
		(isset($options['onchange'])) ? $onchange = "onchange=\"$options[onchange]\"" : $onchange = "";
		(isset($options['class'])) ? $class = $options['class'] : $class = "";
		
		$q = $options['db']->query("SELECT * FROM cms_states WHERE country_id = '$country' ORDER BY name");
		
		$r = "<select name=\"$name\" id=\"$name\" class=\"$class\" $onchange>";
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
		
	/*
	
	Function: selectCountry
	
	select country (uses cms_countries for datasource)
	
	*/
	
	public static function selectCountry($name="country",$value,$options)
	{
		(isset($options['mode'])) ? $mode = $options['mode'] : $mode = "";
		(isset($options['onchange'])) ? $onchange = "onchange=\"$options[onchange]\"" : $onchange = "";
		(isset($options['class'])) ? $class = $options['class'] : $class = "";
		
		$q = $options['db']->query("SELECT * FROM `cms_countries` WHERE c_id = 'US' OR c_id = 'GB' OR c_id = 'CA' OR c_id = 'MX' ORDER BY name DESC");
		$tl = count($q);
		$r = '<select name="'.$name.'" id="'.$name.'" class="'. $class . '" '. $onchange . '>';
			
		$sel = false;
		for($i=0;$i<$tl;$i++){
			$row = $q[$i];
			if($row['id'] == $value){ $selected = "selected=\"selected\""; $sel = true;}else  {$selected = "";}
			$r .= "<option value=\"$row[id]\" $selected >$row[name]</option>";
		}
		
		$r .= "<option value=\"0\">----------------------------------</option>";
		
		$q = $options['db']->query("SELECT * FROM `cms_countries` WHERE active = 1");
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
	
	/*
	
	Function: selectDateTime
	
	select with break out for each compontent (except seconds)
	uses internal methods <dateComponent> and <timeComponent>
	
	*/
	
	public static function selectDateTime($name="",$value,$options)
	{
				
		if($value == ''){
			$value = Utils::dateNow();
		}
		
		$tA = explode(" ",$value);
		$tD = explode("-",$tA[0]);
		$date = array($tD[1],$tD[2],$tD[0]);
		$tD = explode(":",$tA[1]);
		$t = explode(":",Utils::time24to12($tD[0]));
		$time = array($t[0],$tD[1],$t[1]);
	
			
		$r  = self::dateComponent($date,$name,$options['name_space']);
		$r .= self::timeComponent($time,$name,$options['name_space']);
		
		//this is only for validaton from the label for attribute
		$r .= '<span id="' . $name . '" />';
			
		self::buildElement($name,$r,$options);
	
	}
	
	/*
	
	Function: selectDate
	
	select just date bits
	uses internal methods <dateComponent>
	
	*/
		
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
	
	
	/*
	
	Function: dateComponent
	
	internal date bits using CMS_MIN and CMS_MAX_YEAR (consider dropping this and feeding in)

	* @param   string   date
	* @param   string   variable name suffix
	* 
	* @return  string   html to print
	
	*/
	
	public static function dateComponent($date,$name,$name_space)
	{

		$r = '<select name="'. $name. '_month" class="noparse" >';
		$monthA = self::getMonths();
		for($i=1;$i<13;$i++){
			($i == $date[0]) ? $selected="selected=\"selected\"" : $selected = "";
			$v = $monthA[($i-1)];
			$r .= "<option value=\"$i\" $selected >$v</option>";
		}
		
		$r .= '</select>';
		$r .= '/';
		$r .= '<select name="' . $name . '_day" class="noparse" >';
		
		for($i=1;$i<32;$i++){
			($i == $date[1]) ? $selected="selected=\"selected\"" : $selected = "";
			$r .= "<option value=\"$i\" $selected >$i</option>";
		}
		
		$r .= '</select>';
		$r .= '/';
		$r .= '<select name="' .  $name . '_year" class="noparse" >';
		
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
	
	/*
	
	Function: timeComponent
	
	internal time bits
	
	* @param   string   time
	* @param   string   variable name suffix
	* 
	* @return  string   html to print
	*
	*/
	
	public static function timeComponent($time,$name,$name_space)
	{
		
		$r = '<select name="' . $name . '_hour" class="noparse" >';
		
		for($i=1;$i<13;$i++){
			($i == $time[0]) ? $selected="selected=\"selected\"" : $selected = "";
			$r .= "<option value=\"$i\" $selected >$i</option>";
		}
		
		
		$r .= '</select>';
		$r .= ':';
		$r .= '<select name="' .  $name . '_minute" class="noparse" >';
		
		//$ta = array("00","05","10","15","20","25","30","35","40","45","50","55");
		
		for($i=0;$i<60;$i++){
			($i == $time[1]) ? $selected="selected=\"selected\"" : $selected = "";
			$v = Utils::padZero($i);
			$r .= "<option value=\"$i\" $selected >$v</option>";
		}
		
		$r .= '</select>';
		
		$r .= '<select name="' .  $name . '_meridiem" class="noparse" >';
		
		$ta = array("AM","PM");
		for($i=0;$i<2;$i++){
			($ta[$i] == $time[2]) ? $selected="selected=\"selected\"" : $selected = "";
			$r .= "<option value=\"$ta[$i]\" $selected >$ta[$i]</option>";
		}
		$r .= '</select>';
		return $r;
	
	}
	
	/*
	
	Function: selectTime
	
	Select just time bits
	Uses internal method <timeComponent>
	
	*/
	
	public static function selectTime($name,$value,$options)
	{
		
		$tD = explode(":",$value);
		$t = explode(":",Utils::time24to12($tD[0]));
		$time = array($t[0],$tD[1],$t[1]);
	
		$r = self::timeComponent($time,$name,$options['name_space']);
		
		self::buildElement($name,$r,$options);
		
	}	

}

?>
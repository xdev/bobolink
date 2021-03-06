<?php

/*

Class: Forms

Collection of functions dealing with creating form elements

Typical use is as follows
	
	(start code)
	
	formMethod($name,$value,$options)
	
	(end)

*/

class Forms
{
	public static $markup_style = 'div';
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
		if(!isset($options['label'])){
			$options['label'] = ucfirst($name);
		}
		if(self::$markup_style == 'div'){
			
			printf(
				'
				<div class="%s" id="form_item_%s" %s>
					<label for="%s"%s>%s</label>
					<div class="input">
						%s
						%s
					</div>
				</div>
				',
				isset($options['class_main']) ? 'form_item '.$options['class_main'] : 'form_item',
				$name,
				isset($options['style_main']) ? 'style="' . $options['style_main'] . '"' : '',
				$name,
				isset($options['validate']) && $options['validate'] ? ' class="must_validate '.$options['validate'].'"' : '',
				$options['label'],
				$content,
				isset($options['tip']) ? '<p class="tip">'.$options['tip'].'</p>' : ''
			);
		}
		
		if(self::$markup_style == 'definition_list'){
			printf(
				'<dl class="field">
					<dt><label for="%s"%s>%s</label></dt>
					<dd>%s%s</dd>
				</dl>',				
				$name,
				isset($options['validate']) && $options['validate'] ? ' class="must_validate '.$options['validate'].'"' : '',
				$options['label'],
				$content,
				isset($options['tip']) ? '<p class="tip">'.$options['tip'].'</p>' : ''
			);
		}
		
		
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
		// Set file URI
		$file_path = defined('UPLOAD_PATH') ? UPLOAD_PATH : (isset($options['file_path']) ? $options['file_path'] : '/files');
		$file_path .= (isset($options['table']) && isset($options['col_name'])) ? '/'.$options['table'].'/'.$options['col_name'].'/' : '';
		$file_name = isset($options['file_prefix']) ? $options['file_prefix'] : '';
		switch (isset($options['file_key']) ? $options['file_key'] : 'value'):
			case 'value':
				$file_name .= $value;
			break;
			case 'id':
				$file_name .= $options['id'];
			break;
		endswitch;
		$file_name .= $file_name && isset($options['file_extension']) ? $options['file_extension'] : '';
		// File input
		$r = sprintf(
			'<input type="file" class="file" id="%s" name="%s" />',
			$name,
			$name
		);
		// If file already uploaded, include delete and additional markup
		if ($value) {
			$r = sprintf(
				'<input type="checkbox" id="%s_delete" name="%s_delete" />&nbsp;<label for="%s_delete">Delete file</label>&nbsp;&nbsp;|&nbsp;&nbsp;Replace file: %s',
				$name,
				$name,
				$name,
				$r
			);
			// Make sure file exists before linking to it
			if ($file_name) {
				if (file_exists($_SERVER['DOCUMENT_ROOT'].$file_path.$file_name)) {
					$r = sprintf(
						'<a href="%s" target="_blank" title="opens file in a new window">View/download file</a>&nbsp;&nbsp;|&nbsp;&nbsp;%s',
						$file_path.$file_name,
						$r
					);
					// If preview config is set, show preview of file.
					if (isset($options['file_preview'])) {
						switch ($options['file_preview']):
							case 'jpg':
								$r .= sprintf(
									'<br /><img src="%s" alt="JPG" />',
									$file_path.$file_name
								);
							break;
						endswitch;
					}
				} else {
					// Let user know if file doesn't exist
					$r .= sprintf(
						'<br /><em>FILE NOT FOUND:</em>&nbsp;<a href="%s">%s</a>',
						$file_path.$file_name,
						$file_path.$file_name
					);
				}
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
		$display_value = isset($options['display_value']) ? $options['display_value'] : $value;
		self::buildElement($t,"<span id=\"$t\">$display_value</span><input type=\"hidden\" id=\"$name\" name=\"$name\" value=\"$value\" />",$options);
		
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
		$col_display_separator = isset($options['col_display_separator']) ? $options['col_display_separator'] : ' - ';
		$class = (isset($options['class']) ? $options['class'] : '') . (isset($options['validate']) ? ' validate ' . $options['validate'] : '');
		
		$r = sprintf(
			'<select name="%s" id="%s" class="%s" %s>',
			$name,
			$name,
			$class,
			isset($options['onchange']) ? "onchange=\"$options[onchange]\"" : ''
		);
		if(!isset($options['allow_null']) || $options['allow_null'] === true){
			$r .= '<option value="&#00;">(none)</option>';
		}
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
							$td .= $col_display_separator;
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
		$col_display_separator = isset($options['col_display_separator']) ? $options['col_display_separator'] : ' - ';
		$class = (isset($options['class']) ? $options['class'] : '') . (isset($options['validate']) ? ' validate ' . $options['validate'] : '');
		
		$r = sprintf(
			'<select name="%s" id="%s" class="%s" %s>',
			$name,
			$name,
			$class,
			isset($options['onchange']) ? "onchange=\"$options[onchange]\"" : ''
		);
		if(isset($options['allow_null'])){
			$r .= '<option value="&#00;">(none)</option>';
		}
		// Todo: Move from null to 0
		if($options['db']->isNullable($options['select_table'],$options['parent_field_name'])){
			$start_value = null;
		}else{
			$start_value = 0;
		}
		
		$r .= self::getChildren($name,$value,$options,$start_value,'',$options['parent_field_name']);
		$r .= "</select>";
		
		self::buildElement($name,$r,$options);
	}
	
	private static function getChildren($name,$value,$options,$parent,$depth='',$parent_field_name='parent_id')
	{
		//die(print_r(debug_backtrace()));
		//is nullable
		
		if($options['db']->isNullable($options['select_table'],$parent_field_name) && is_null($parent)){			
			$_v = ' IS NULL ';
		}else{
			$_v = " = '$parent' ";
		}				
		
		$dA = explode("|",$options['col_display']);
		$r = '';
		if (strpos($options['select_sql'],'ORDER BY')) {
			$sql = str_replace(
				'ORDER BY',
				(strpos($options['select_sql'],'WHERE') ? 'AND ' : 'WHERE ').$parent_field_name . $_v . ' ORDER BY',
				$options['select_sql']
			);
		} else {
			$sql = $options['select_sql'].(strpos($options['select_sql'],'WHERE') ? ' AND ' : ' WHERE ').$parent_field_name . $_v;
		}
		
		if ($q = $options['db']->query($sql)) {
			
			foreach ($q as $row) {
				$tv = $row[$options['col_value']];
				
				$selected = (trim($value) == trim($tv)) ? ' selected="selected"' : '';
				//Todo: Fix disabled
				$disabled = '';//($tv == $options['id']) ? ' disabled="disabled"' : ''; // Prevent selecting self as parent
				if (count($dA) > 1) {
					$td = "";
					for ($j=0;$j<count($dA);$j++) {
						$td .= $row[$dA[$j]];
						if ($j<count($dA) - 1) {
							$td .= $col_display_separator;
						}
					}
				} else {
					$td = $row[$options['col_display']];
				}
			
				$r .= sprintf(
					'<option value="%s"%s%s>%s</option>',
					$tv,
					$selected,
					$disabled,
					$depth.$td
				);
				$r .= self::getChildren($name,$value,$options,$row[$options['col_value']],$depth.'— ',$parent_field_name);
		
			}
		}
		return $r;
	}
	
	/*
	
	Function: selectFiles
	
	Scans a folder and creates a select input
	
	*/
	
	public static function selectFiles($name,$value,$options)
	{
		
		$subfolder = (isset($options['subfolder'])) ? $options['subfolder'] : true;
		$path = (isset($options['path'])) ? $options['path'] : '';
		$level = (isset($options['level'])) ? $options['level'] : '';
		$folder = (isset($options['folder'])) ? $options['folder'] : false;
		
		if ($folder) {
			
			$r = $level ? '' : sprintf(
				'<select name="%s" id="%s"><option value="">(none)</option>',
				$name,
				$name
			);
			$scan = array_diff(scandir($folder), array('.', '..'));
			$r2 = '';
			foreach ($scan as $file) {
				
				if (substr($file,0,1) != '.') {
					
					if ($subfolder && is_dir($folder.$file)) {
						$r2 .= sprintf(
							'<option disabled="disabled">%s</option>',
							$level.$file.'/'
						);
						$options['folder'] = $folder.$file.'/';
						$options['path'] = $path.$file.'/';
						$options['level'] = $level.'&nbsp;&nbsp;&nbsp;';
						$r2 .= self::selectFiles($name,$value,$options);
					} elseif (is_file($folder.$file)) {
						$r .= sprintf(
							'<option value="%s"%s>%s</option>',
							$path.$file,
							$value == $path.$file ? ' selected="selected"' : '',
							$level.$file
						);
					}
					
				}
				
			}
			
			$r .= $r2;
			$r .= $level ? '' : "</select>";
			
			if ($level) {
				return $r;
			} else {
				self::buildElement($name,$r,$options);
			}
			
		}
	}
	
	/*
	
	Function: selectStatic
	
	builds a select from an existing array
	
	*/
	
	public static function selectStatic($name,$value,$options)
	{	
		(isset($options['dataA'])) ? $dataA = $options['dataA'] : $dataA = array();
		
		if(isset($options['data_csv'])){
			$tA = explode(',',$options['data_csv']);
			if(!isset($options['allow_null']) || $options['allow_null'] === true){
				$dataA[] = array('(none)','');
			}
			foreach($tA as $row){
				//optionally split on ('|');
				$vA = explode('|',$row);
				if(count($vA)==2){
					$dataA[] = array($vA[1],$vA[0]);
				}else{
					$dataA[] = array($row,$row);
				}
			}
		}
		
		(isset($options['onchange'])) ? $onchange = "onchange=\"$options[onchange]\"" : $onchange = "";
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
		(isset($options['datasource'])) ? $datasource = $options['datasource'] : $datasource = 'cms_states';
		
		$q = $options['db']->query("SELECT * FROM $datasource WHERE country_id = '$country' ORDER BY name");
		
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
		(isset($options['datasource'])) ? $datasource = $options['datasource'] : $datasource = 'cms_countries';
		
		$q = $options['db']->query("SELECT * FROM $datasource WHERE c_id = 'US' OR c_id = 'GB' OR c_id = 'CA' OR c_id = 'MX' ORDER BY name DESC");
		$tl = count($q);
		$r = '<select name="'.$name.'" id="'.$name.'" class="'. $class . '" '. $onchange . '>';
			
		$sel = false;
		for($i=0;$i<$tl;$i++){
			$row = $q[$i];
			if($row['id'] == $value){ $selected = "selected=\"selected\""; $sel = true;}else  {$selected = "";}
			$r .= "<option value=\"$row[id]\" $selected >$row[name]</option>";
		}
		
		$r .= "<option value=\"0\" disabled=\"disabled\">----------------------------------</option>";
		
		$q = $options['db']->query("SELECT * FROM $datasource WHERE active = 1");
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
	
			
		$r  = self::dateComponent($date,$name,$options);
		$r .= self::timeComponent($time,$name,$options);
		if(isset($options['allow_null'])){
			//this would bind to a js to deactivate fields
			$checked = '';
			if($value == '0000-00-00 00:00:00'){
				$checked = 'checked="checked"';
			}
			$r .= '<input type="checkbox" name="'.$name.'_isnull" '.$checked.' />Null';
		}
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
		
		$r  = self::dateComponent($date,$name,$options);
		if(isset($options['allow_null'])){
			$checked = '';
			if($value == '0000-00-00'){
				$checked = 'checked="checked"';
			}
			$r .= '<input type="checkbox" name="'.$name.'_isnull" '.$checked.' />Null';
		}
		self::buildElement($name,$r,$options);
	}
	
	public static function getMonths()
	{
		return array("January","February","March","April","May","June","July","August","September","October","November","December");
	}
	
	
	/*
	
	Function: dateComponent

	* @param   string   date
	* @param   string   variable name suffix
	* 
	* @return  string   html to print
	
	*/
	
	public static function dateComponent($date,$name,$options)
	{
		
		$r = '<select name="'. $name. '_month" class="noparse" >';
		$monthA = self::getMonths();
		
		if(isset($options['allow_null'])){
			($date[0] == '00') ? $selected="selected=\"selected\"" : $selected = "";
			$r .= "<option value=\"00\" $selected></option>";
		}
		
		for($i=1;$i<13;$i++){
			($i == $date[0]) ? $selected="selected=\"selected\"" : $selected = "";
			$v = $monthA[($i-1)];
			$r .= "<option value=\"$i\" $selected >$v</option>";
		}
		
		$r .= '</select>';
		$r .= '/';
		$r .= '<select name="' . $name . '_day" class="noparse" >';
		
		if(isset($options['allow_null'])){
			($date[1] == '00') ? $selected="selected=\"selected\"" : $selected = "";
			$r .= "<option value=\"00\" $selected></option>";
		}
		
		for($i=1;$i<32;$i++){
			($i == $date[1]) ? $selected="selected=\"selected\"" : $selected = "";
			$r .= "<option value=\"$i\" $selected >$i</option>";
		}
		
		$r .= '</select>';
		$r .= '/';
		
		// Text Input Mode
		//$r .= '<input style="width:40px;" name="'. $name . '_year" class="noparse" type="text" maxlength="4" size="3" value="'.$date[2].'"/>';
		
		
		$r .= '<select name="' .  $name . '_year" class="noparse" >';
		
		//set the the default range for years
		(!isset($options['min_year'])) ? $options['min_year'] = date('Y')-20 : '';
		(!isset($options['max_year'])) ? $options['max_year'] = date('Y')+21 : '';
		
		($date[2] < $options['min_year']) ? $min = $date[2] : $min = $options['min_year'];
		($date[2] > $options['max_year']) ? $max = $date[2] : $max = $options['max_year'];
		
		if($date[2] == ""){
			$min = $options['min_year'];
			$max = $options['max_year'];
		}
		
		if(isset($options['allow_null'])){
			($date[2] == '0000') ? $selected="selected=\"selected\"" : $selected = "";
			$r .= "<option value=\"0000\" $selected></option>";
		}
		
		if($min == '0000'){
			$min = date('Y')-20;
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
	
	public static function timeComponent($time,$name,$options)
	{
		
		$r = '<select name="' . $name . '_hour" class="noparse" >';
		
		$r .= "<option value=\"12\">12</option>"; // Start with 12
		for($i=1;$i<12;$i++){
			($i == $time[0] || ($time[0] == 0 && $i == 12)) ? $selected="selected=\"selected\"" : $selected = "";
			$r .= "<option value=\"$i\" $selected >".(strlen($i)==1 ? '&nbsp;' : '')."$i</option>";
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
	
		$r = self::timeComponent($time,$name,$options);
		
		if(isset($options['allow_null'])){
			//this would bind to a js to deactivate fields
			$checked = '';
			if($value == '00:00:00'){
				$checked = 'checked="checked"';
			}
			$r .= '<input type="checkbox" name="'.$name.'_isnull" '.$checked.' />Null';
		}
		
		self::buildElement($name,$r,$options);
		
	}	

}

?>
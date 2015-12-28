<?php


/*
v6.73	4/9/15	Added pluggable function get_input_html() to function db_edit_form(). This allows us to add other stuff 
				to the same table cell. Eg: javascript UI for selecting operator icon in Carplus map admin. 
				Now class Form_MCP can work with subset of database fields - eg: when table has columns that are being phased
				out, but we don't want to delete them yet.
				Function output_row() now checks for nothing returned (= null) - so we don't have to return anything for last row.
				Also provides all defaults in 'calculate_values' mode, so only need to provide changed values. 
				Also stripslashes everything. So eponymous functions can be much shorter.
				
				Added radio buttons to function db_edit_form().

v6.71	7/3/15	Changed method get_validation_MCP() to static, so can use from outside Form_MCP.
				Added lots of csv stuff to class Form_MCP.

v6.70	4/3/15	Switch class Wf_SuperTable() to using PDO. If using query, needs 4th param = $pdo.
				Ditto class Form_MCP.

v6.69	13/2/15	Found error in function get_table(). Single-line tables not getting output when source was array.

v6.62	22/9/14	Added qs_restore($ignore)

v6.61	30/8/14	Added $returns['subhead_row'] to function output_row($row_items,$rownum) in class Wf_SuperTable.				

v6.55	11/2/14	Removed cellspacing ='0' from functions get_table() and db_edit_form(). Invalid in HTML5.

v6.17 	26/6/13	Added protected $output_array to enable additional manipulation (inc csv output). This is assembled
				during get_table()

v5.4 	15/4/13	Found error in function get_table(): "$row => $rownum"!

v4.10	7/3/13	Added bailout on empty to function output_row()

v4.9 	1/2/13	Added class to MCP form

v4.8	26/2/13	Added protected $totals = array();

v4.7 	23/2/13	Added $style to get_table

v4.6	21/2/13	Added <thead> and <tbody> tags to Wf_SuperTable

v4.5	17/2/13	Added pluggable values_for_new() in WF_Form
		(?date) Added function get_options($options_array) 


*/

/**
 * Wf_SuperTable class: returns an html table
 * 
 * General class for outputting a table from a query or an array. 
 * Calculations, checks, warnings and tooltips are all handled by one instance-specific, eponymous(?) external function 
 *  
 * @param string		$instance_name		unique, used as id and as name of instance-specific external function (if required) 
 * @param array			$fields_to_display  the keys identify values obtained from $source, the values (if not empty) are the column titles
 * @param array|string	$source				either an array containing the values to output or an SQL query
 * 
 * @return string		complete html table element or html apology
 */
 
class Wf_SuperTable {
	
	public $fields_to_display;
	public $name;
	public $source;
	
	protected $outputkeys;
	protected $totals = array(); // v4.8 added = array()
	protected $query;
	protected $is_complex; // boolean: whether or not a function is used to calculate values etc
	public $output_array = array(); // v6.17 added to enable additional manipulation (inc csv output)
	
	private $result;
	private $maintable_html;
	
	public function __construct($fields_to_display,$source,$name,$pdo = null) { // v6.70
		$this->name = $name;  
		
		if(!is_array($source)) { // v6.70
			// it's a query
			$this->source = pdo_process($pdo,$source);
		} else {
			$this->source = $source;
		}
				
		$this->fields_to_display = $fields_to_display; 
		$this->outputkeys = array_keys($fields_to_display);
		$this->is_complex = function_exists($name); // if there isn't an eponymous function, it's very simple!
	}  
	
	public function getFields() {  
		return $this->fields_to_display; //  . "<br />";  
	}  
	
	public function getOutputKeys() {  
		return $this->outputkeys;  
	}  

	public function setQuery($query) {  
		$this->query = $query;  
	} 
	
	public function getTotals() {  
		return $this->totals;  
	}  
	
	public function setTotals($totals) {  
		$this->totals = $totals;  
	} 
	
	private function table_header($name_array, $blank_extra = false) { // $blank_extra (boolean) allows for edit button cell
		$html = "<thead>\n"; // v4.6 
		$html .= "<tr class='headrow'>"; 
		foreach($name_array as $key => $value) {
			if($value == ''){
				$value = $key;
			}
			$html.= "<th class ='".$key."'>".$value."</th>";
		}
		if($blank_extra) {
			$html.= "<th class ='blank'>&nbsp;</th>"; // make this one invisible
		}
		$html .= "</tr>\n";
		$html .= "</thead>\n"; // v4.6 
		return $html;
	}

	
	public function get_table($style=false) { // v4.7 added $style
		$class = ($style) ? " class='".$style."'" : ""; // v4.7
		$this->maintable_html = "
		<table id='".$this->name."'".$class.">\n".
			$this->table_header($this->fields_to_display, false)."
			<tbody>\n";
		
		foreach($this->source as $rownum => $row) { // v5.4 found error: "$row => $rownum"!
			$this->maintable_html .= $this->output_row($row,$rownum);
		}
		$rowcount = count($this->source);// v6.69 - because $rownum is 0 for single-row tables
		if($this->is_complex && $rowcount > 0) {// v6.69
			$function_name = $this->name;
			//$this->maintable_html .= $function_name('',$rownum,'last_row');
			$returned_last_row = $function_name('',$rownum,'last_row'); // v6.73
			if(!is_null($returned_last_row)) { // null means nothing was returned
				$this->maintable_html .= $returned_last_row;
			}
		}
		$this->maintable_html .= "</tbody>\n</table>\n\n";
		
		if($rowcount == 0) { // v6.69
			$this->maintable_html  = "
			<div id='mainTableDiv'>\n
				<h3>There are no relevant entries.</h3>\n
			</div>";
		}
		//Wf_Debug::stash(array('$output_array' => $this->output_array)); // v6.17
		return $this->maintable_html;
	}
	
	
	
	protected function output_row($row_items,$rownum) {
		
		if($this->is_complex) {
			$function_name = $this->name;
			$returns = $function_name($row_items,$rownum,'calculate_values');
			if(empty($returns)) // v4.10
				return ''; // bail out if empty v4.10
			/*	
			$output_values = $returns['outputs'];
			$tooltips = $returns['tooltips'];
			$cell_classes = $returns['cell_classes'];
			$row_class = $returns['row_class'];
			
			
			$this->output_array[$rownum] = $returns['outputs']; // v6.17 
			*/
			$html = (isset($returns['subhead_row'])) ? $returns['subhead_row'] : ''; // v6.61
			$row_class = isset($returns['row_class']) ? $returns['row_class'] : " class='row_".$rownum." '"; // v6.73
			$html .= "<tr".$row_class.">"; // v6.61  
			for ($n = 0; $n < count($this->fields_to_display); $n++){
				/*
				$key = $this->outputkeys[$n];
				$value = $output_values[$key];
				if ($value == '') {
					$value = '&nbsp;'; // 29/9/12
				}
				$html .= "<td".$cell_classes[$key].$tooltips[$key].">".$value."</td>";
				*/
				// v6.73
				$key = $this->outputkeys[$n];
				$value = isset($returns['outputs']) && isset($returns['outputs'][$key]) ? stripslashes($returns['outputs'][$key]) : stripslashes($row_items[$key]);
				$value = ($value == '') ? '&nbsp;' : $value;				
				$this->output_array[$rownum][$key] = $value;	
				$cell_class = isset($returns['cell_classes']) && isset($returns['cell_classes'][$key]) ? $returns['cell_classes'][$key] : " class='".$key." '";
				// v2.0 added space after cell_class to help str_replace
				$tooltip = isset($returns['tooltips']) && isset($returns['tooltips'][$key]) ? $returns['tooltips'][$key] : '';
				$html .= "<td".$cell_class.$tooltip.">".$value."</td>";
			}
		} else { // it's simple
			$html = "<tr class='row_".$rownum."'>\n";
			for ($n = 0; $n < count($this->fields_to_display); $n++){
				$key = $this->outputkeys[$n];
				$value = stripslashes($row_items[$key]);
				if ($value == '') {
					$value = '&nbsp;'; // 29/9/12
				}
				$html .= "<td class='".$key."'>".$value."</td>";
			}
		}
		$html .=  "</tr>\n";
		return $html;
	}
	
	
	static function export_csv($array, $sep = ',') {
		$csv = '';
		foreach($array as $row) {
			$csv .= implode($sep,$row)."\n";
		}
		return $csv;
	}
	
			 
}



class Form_MCP {
	
    // property declaration
    public $specification;
	public $dropdowns;
	public $field_array;
	public $field_titles;
	
	private $pdo; //v6.70
	
	protected $complete = false;
	protected $table_name;
	
	public $row_elements; // v6.71
	
	private $csv_intro = "
	<h3>Do you want to update the database table?</h3>\n
	<p>This function will...</p>\n
	<ol>\n
		<li>allow you to select a .csv file to upload</li>\n
		<li>check the uploaded file for obvious errors and display the contents</li>\n
		<li>give you the option of continuing with the update or cancelling</li>\n
		<li>create a backup .csv file of the existing database table</li>\n
		<li>add a &lsquo;cleaned up&rsquo; version of this new data onto the end of the existing table.</li>\n
	</ol>\n"; // default version
	
	//public $csv_uploaddir;
	public $csv_fieldnames;
	//public $preprocess_function_name;
	//public $validate_function_name;
	public $preprocessed_array;
	//public $csv_separator;
	
	public $csv_settings;
	/*
	$csv_settings = array(
		'uploaddir' => $_SERVER['DOCUMENT_ROOT'].'/trip_data_files/', // /home/wingfing/public_html/metacarpool.org.uk/trip_data_files/ 
		'separator' => chr(9), // tab
		'intro' => false,
		'preprocess_function' => 'mcp_csv_preprocess',
		'validate_function' => 'mcp_csv_validate',
	);	
	*/
	
	
	// constructor sets all the public properties
	public function __construct($new_specification, $table_name, $pdo) { // v6.70
		$this->specification = $new_specification; 
		$this->table_name = $table_name;  // v4.3
		$this->pdo = $pdo; // v6.70
		foreach($this -> specification as $key => $value) {
			$element = explode(',',$value);
			//$this->field_array[$key]
			$this->field_array[$key]['code']=$element[0];
			$this->field_array[$key]['validator']=$element[1];
			$this->field_array[$key]['title']=$element[2];
			$this->field_titles[$key]=$element[2];
			if(strpos($element[0],'s') !== false || strpos($element[0],'r' ) !== false) { //v6.73 added radio buttons
				$this->dropdowns[$key] = array_slice($element,3); // removes first 3 items
				$this->field_array[$key]['dropdowns'] = $this->dropdowns[$key];
			} 
		}
	}
	
	// stackoverflow.com/questions/6184337/best-practice-php-magic-methods-set-and-get
	public function __get($p) { 
		$m = "get_$p";
		if(method_exists($this, $m)) return $this->$m();
		user_error("undefined property $p");
	}
	
	
	// set undeclared property
	public function __set($property, $value) {
		$this->$property = $value;
	}
	
	
	public function get_complete() { 
		return $this->complete;
	}
	
	public function get_row_elements() { 
		return $this->row_elements;
	}
	
	
	//v4.3	$qmode is what's in the query string. $edit_mode is the internal, rationalised name
	public function process_form($qmode, $edit_modes = false) { // optional parameter: $edit_modes  ? // v4.3
		
		if(!function_exists('values_for_new')) { // v4.5
			function values_for_new() {
				return array();
			}
		}
				
		$edit_html = '';
		if(!$edit_modes) { 
			$edit_modes = array( // defining the three generic modes required 
				'new' => 'new', // adding a new entry
				'from_db' => 'from_db', // populating form from database
				'try' => 'try' // a submitted form try
				); // NB: $edit_mode => $qmode
		}
		$edit_mode = array_search($qmode,$edit_modes); // eg: 'journal_edit1' becomes 'from_db'// v4.3
		
		if($edit_mode=='new' || $edit_mode=='from_db' || $edit_mode=='try') { // v4.3
						
			switch ($edit_mode) { // v4.3
				case 'new':
					$row_elements = values_for_new(); // v4.5  usually an empty array
					break;
				case 'from_db':
					$table_field_names = array_keys($this->field_titles);
					$query = "SELECT * FROM ".$this->table_name." WHERE ".$table_field_names[0]." = '".$_GET['id']."'";
					//$result = mysql_query($query) or die(mysql_error());
					//$row_elements = mysql_fetch_assoc( $result );
					$result = pdo_process($this->pdo,$query); // v6.70
					$row_elements = $result[0]; // v6.70
					$this->row_elements = $row_elements; // v6.71
					break;
				case 'try':
					$row_elements = false;
					break;
			}	
		
			list($edit_html,$this->complete) = $this->db_edit_form($edit_mode, $edit_modes, $this->field_array, $row_elements); // v4.3 was $qmode
		}
		
		
		if($edit_mode=='try' && $this->complete) { // v4.3
			$edit_html = ''; // "<p>OK TO UPDATE!</p>";
			$table_field_names = array_keys($this->field_titles);
			$id = $_POST[$table_field_names[0]];
			if($id == '(new entry)') {
				$id ='';
			}
			$this->insertorupdate1record($id); // ($id,'site_versions',$table_field_names) assumes column 0 is index
			// $qmode = 'out'; // ie: just output table
		}
	
		return $edit_html;
	}
		
	
	
	private function db_edit_form($edit_mode, $modes_array, $db_field_array, $initial_row_elements) { // was account_interface_new() // v4.3 was $mode
	
	// No validation required when displaying initial form for new item.
	// watch out for query string parameter: renamed as qmode because clash with (?)WP variable called $mode
		
		if(!function_exists('tweak_feedback')) { 
			function tweak_feedback($check) {
				if($check =='') {
					return array('error_class'=>'', 'feedback'=>''); // ie: no positive feedback
				} else {
					return array('error_class'=>' error', 'feedback'=>$check);
				}
			}
		}
		
		if(!function_exists('decode_db')) { // use this to decode database values into <option> values
			function decode_db($key,$value) {
				return $value;
			}
		}
		
		//v6.73
		if(!function_exists('get_input_html')) { // use this to add extra html into table cell (eg: picking club icons in Carplus map admin)
			function get_input_html($key,$value,$row_elements) {
				$html = "<input type='text' name='".$key."' id='".$key."' value='".$value."' />"; // default version
				return $html;
			}
		}
		
		//v6.73 - simpler, universal version that only allows appending stuff to the end of the cell
		if(!function_exists('extra_editvalue_html')) { // use this to add extra html into table cell (eg: picking club icons in Carplus map admin)
			function extra_editvalue_html($key,$value,$row_elements) {
				$html = ''; // default version
				return $html;
			}
		}

		
		//$edit_mode = array_search($mode,$modes_array); // eg: 'journal_edit1' becomes 'from_db'// v4.3
		$db_field_names = array_keys($db_field_array);
		$row_elements = array_fill_keys($db_field_names, ''); // gets overwritten
		
		// Now set up $row_elements for each edit_mode
		if ($edit_mode=='new' || $edit_mode=='from_db') {
			$row_elements = array_merge($row_elements,$initial_row_elements);
			//var_dump($row_elements);
		}
		if ($edit_mode=='try') {
			$row_elements = array_merge($row_elements,$_POST);
			$row_elements = array_intersect_key($row_elements, $db_field_array); // values from $_POST, but only for keys found in $journal_fields
		}
		if($row_elements[$db_field_names[0]] == '') { // was 'entry_id'
			$row_elements[$db_field_names[0]] ='(new entry)';
		}
		
		$interface_html = "<form class='".$this->table_name."' method='post' action='".SELF_URL."?qmode=".$modes_array['try']."&".qs_restore(array('qmode'))."' >\n"; // v6.62
		$interface_html .= "<table id='edit_table' class='edit'>\n"; // v6.55
		
		$errors = array ('errors_html'=>'','count'=>0); // 5/9/12
		$complete = false; // 5/9/12
		
		$check = array(
			'error_class' => '',
			'feedback' => '&nbsp;'
		);
		
		foreach($row_elements as $key => $value) { 
			if(!in_array($key,$db_field_names)) { // v6.73
				continue;
			}
			$value = stripslashes($value);
			$code = $db_field_array[$key]['code'];
			
			switch(true) {
			
			case (strpos($code,'p') !== false) : // the primary index/id			
				$interface_html .= "<tr class='".$key."'>\n";
				$interface_html .= "<td class='label'><label for='".$key."'>".$db_field_array[$key]['title'].": </label></td>";
				$interface_html .= "<td class='editvalue'><input type='text' readonly='readonly' name='".$key."' id='".$key."' value='".$value."' /></td>\n";
				$interface_html .= "<td class='feedback'><p>&nbsp;</p></td>\n";
				$interface_html .= "</tr>\n";
				break;
			
			case (strpos($code,'s') !== false) : // the drop-downs
				//echo "dropdown value: ".$value;
				$option_names = $db_field_array[$key]['dropdowns'];
				//var_dump($option_names);
				if ($edit_mode=='from_db') { // need to translate from eg: 'Igoe' to 'account1'
					$knum = array_search(decode_db($key,$value), $option_names); // was: array_search($value, $option_names)
					$value = $key.$knum;
				}
				if ($edit_mode !='new') { // initial form doesn't need validating
					$check = tweak_feedback(Form_MCP::get_validation_MCP($key, $value, $db_field_array[$key])); // v6.71
				}
				$interface_html .= "<tr class='".$key.$check['error_class']."'>\n";
				$interface_html .= "<td class='label'><label for='".$key."'>".$db_field_array[$key]['title'].": </label></td>";
				$interface_html .= "<td><select name='".$key."' id='".$key."' >\n";
				foreach($option_names as $optkey => $optvalue) {
					$interface_html .= "<option value='".$key.($optkey)."' ".get_prepop($optkey, $key, $value)." >".$optvalue."</option>\n";
				}
				$interface_html .= "</select></td>\n";
				$interface_html .= "<td class='feedback'><p>".$check['feedback']."</p></td>\n";
				$interface_html .= "</tr>\n";
				break;
			
			// introduced v6.73
			// <input type="radio" name="group" id="group1" value="group1" checked="checked" /><label for="group1"> button one</label>
			case (strpos($code,'r') !== false) : // radio buttons
				//echo "dropdown value: ".$value;
				$option_names = $db_field_array[$key]['dropdowns'];
				//WFB($option_names,'$option_names');
				$radio_initial_check = ''; // by default, no radio buttons are checked when creating a new entry.
				foreach($option_names as $okey => $option_name) {
					if(strpos($option_name, '>') !== false) {
						$option_names[$okey] = substr($option_name,1);// remove the >
						$radio_initial_check = $okey; // ie: key of first option name starting '>'
						break;
					}
				}
				if ($edit_mode=='from_db') { // need to translate from eg: 'Igoe' to 'account1'
					$knum = array_search(decode_db($key,$value), $option_names); // was: array_search($value, $option_names)
					$value = $key.$knum;
				}
				if ($edit_mode !='new') { // initial form doesn't need validating
					$check = tweak_feedback(Form_MCP::get_validation_MCP($key, $value, $db_field_array[$key])); // v6.71
				} else { // new
					$value = $key.$radio_initial_check;
				}
				$interface_html .= "<tr class='".$key.$check['error_class']."'>\n";
				$interface_html .= "<td class='label'>".$db_field_array[$key]['title'].":</td>"; // not actually label
				$interface_html .= "<td class='editvalue'>"; // v6.73 now pluggable
				foreach($option_names as $optkey => $optvalue) {
					$interface_html .= "<label for='".$key.($optkey)."' class='radio' >\n";
					$interface_html .= "<input type = 'radio' name='".$key."' id='".$key.($optkey)."' value='".$key.($optkey)."' ".radioprepop2($key, $optkey, $value)." autocomplete='off' />\n"; //stackoverflow.com/a/8779735
					$interface_html .= degreaterthan($optvalue)."</label>\n";
				}
				$interface_html .= extra_editvalue_html($key,$value,$row_elements)."</td>\n";// v6.73
				$interface_html .= "<td class='feedback'><p>".$check['feedback']."</p></td>\n";
				$interface_html .= "</tr>\n";
				break;

			
			case (strpos($code,'i') !== false) : // the inputs	
				if ($edit_mode !='new') {
					$check = tweak_feedback(Form_MCP::get_validation_MCP($key, $value, $db_field_array[$key])); // v6.71 
				}
				$interface_html .= "<tr class='".$key.$check['error_class']."'>\n";
				$interface_html .= "<td class='label'><label for='".$key."'>".$db_field_array[$key]['title'].": </label></td>";
				//$interface_html .= "<td class='editvalue'><input type='text' name='".$key."' id='".$key."' value='".$value."' /></td>\n";
				$interface_html .= "<td class='editvalue'>".
										get_input_html($key,$value,$row_elements).
										extra_editvalue_html($key,$value,$row_elements).
									"</td>\n"; // v6.73 now pluggable
				$interface_html .= "<td class='feedback'><p>".$check['feedback']."</p></td>\n";
				$interface_html .= "</tr>\n";
				break;
			
			case (strpos($code,'t') !== false) : // the textareas	
				if ($edit_mode !='new') {
					$check = tweak_feedback(Form_MCP::get_validation_MCP($key, $value, $db_field_array[$key]));  // v6.71
				}
				$interface_html .= "<tr class='".$key.$check['error_class']."'>\n";
				$interface_html .= "<td class='label'><label for='".$key."'>".$db_field_array[$key]['title'].": </label></td>";
				$interface_html .= "<td class='editvalue'><textarea rows= '6' cols = '30' name='".$key."' id='".$key."' >".$value."</textarea></td>\n";
				$interface_html .= "<td class='feedback'><p>".$check['feedback']."</p></td>\n";
				$interface_html .= "</tr>\n";
				break;
				
				
			case (strpos($code,'c') !== false) : // the checkboxes	
				//if ($edit_mode !='new') {
					//$check = tweak_feedback($this->get_validation_MCP($key, $value, $db_field_array[$key])); 
				//}
				$checked = ($value == '1') ? " checked='checked' " : ""; // assuming database stores 0 or 1
				$interface_html .= "<tr class='".$key."'>\n";
				$interface_html .= "<td class='label'><label for='".$key."'>".$db_field_array[$key]['title'].": </label></td>";
				$interface_html .= "<td class='editvalue'><input type='checkbox' name='".$key."' id='".$key."' value='".$key."' ".$checked." /></td>\n";
				$interface_html .= "<td class='feedback'><p>&nbsp;</p></td>\n";
				$interface_html .= "</tr>\n";
				break;

			
			}
			$errors = add_to_message($errors, $check); // 5/9/12
		}
			
		$interface_html .= "<tr class='submit'><td colspan='3'>\n";
		$interface_html .= "<input type='submit' name='submit3'  id='submit3' value='Go' class='button' />\n";
		$interface_html .= "</td></tr>\n";
	   
		$interface_html .= "</table>";
		$interface_html .= "</form>\n";
		
		if ($errors ['count'] == 0 && $edit_mode=='try') { // 5/9/12
			$complete = true;
		}
		//var_dump($interface_html);
		return array($interface_html, $complete); // 5/9/12
	}
	

	

		
	 // v6.71  made static
	static function get_validation_MCP($key, $value, $element) { // needs to return ['error_class'] => '' or ' error', ['feedback'] => a feedback message.
		list($func_name, $arg) = parse_validator_string($element['validator']); // v3.57
		if($func_name == 'selectmin') {
			return $func_name($arg, $value, $key);
		} else {
			//WFB($func_name." ".$value, '$func_name & $value');
			return $func_name($arg, $value);
		}
	}
	
		
	
	
		
	protected function insertorupdate1record($id) { // $id,$table,$table_field_names
	// assumes first column is index for table
		
		if(!function_exists('get_post_value')) {
			function get_post_value($n,$obj) { // $fieldnames,$n
				$fieldnames = array_keys($obj->field_titles);
				$key = $fieldnames[$n]; // eg: 'account'
				if(!isset($_POST[$key])) { // assume it must be an unchecked checkbox
					return 0;
				}
				$value = $_POST[$key]; // eg: 'account3'
				if($value == $key) { 
					// it's a checkbox
					return 1;
				} elseif(strpos($value, $key) ===0) { // eg: if $key == 'account'
					$num = substr($value,strlen($key)); // in this case, $num = 3
					$option_names = $obj->dropdowns[$key];
					return str_replace('>','',$option_names[$num]); // v6.73 - to cope with pre-selected radio button
				} else { // assume it's not a select drop-down
					return $obj->clean_up($_POST[$key]);
				}
			}
		}
	
		$table_field_names = array_keys($this->field_titles);
		if($id =='') { // INSERT
			$query = "INSERT INTO ".$this->table_name. " (";
			for($n=1; $n< count($table_field_names); $n++) {
				$query .= $table_field_names[$n] . ",";
			}
			$query = substr($query,0,-1); // remove last comma
			$query .= ") VALUES (";
			for($n=1; $n< count($table_field_names); $n++) {
				$query .= "'".get_post_value($n,$this) . "',";
			}
			$query = substr($query,0,-1); // remove last comma
			$query .= ")";
			
		} else { // UPDATE
			$query = "UPDATE ".$this->table_name. " SET ";
			for($n=1; $n< count($table_field_names); $n++) {
				$query .= $table_field_names[$n] . " = '" . get_post_value($n,$this) . "',";
			}
			$query = substr($query,0,-1); // remove last comma
			$query .= " WHERE ".$table_field_names[0]." ='".$id."' LIMIT 1";
		}
		$result = pdo_process($this->pdo,$query); // v6.70
		return $result;
	}
	
	
	public function clean_up($text) {
		$text = mysql_real_escape_string($text);
		$text = preg_replace('/&(?!amp;)/i', '&amp;', $text);
		$text =str_replace("\x96", "-", $text); // windows dash?
		$text =str_replace("\x92", "\'", $text); // windows apostrophe?
		$text = str_replace("\xA0", " ", $text); // "\xA0" is &nbsp; ADDED 21/9/10
		return $text;
	}

	// This function enters the <option>s into the form field spec (eg: for $transport_modes above)
	public function get_options($options_array) { // v4.5 (?)
		//var_dump($options_array);
		$options_string ='';
		foreach($options_array as $option) {
			$options_string .= ",".$option;
		}
		return $options_string;
	}
	
	
	
	
	
	// CSV BITS
	/*
	$csv_settings = array(
		'uploaddir' => $_SERVER['DOCUMENT_ROOT'].'/trip_data_files/', // /home/wingfing/public_html/metacarpool.org.uk/trip_data_files/ 
		'separator' => chr(9), // tab
		'intro' => false,
		'preprocess_function' => 'mcp_csv_preprocess',
		'validate_function' => 'mcp_csv_validate',
	);	
	*/
	
	//public function setCsvSettings($csv_uploaddir, $csv_separator, $csv_intro = false, $preprocess_function_name = false, $validate_function_name = false) {  
		
	public function set_csv_settings($csv_settings) {
		$this->csv_settings = $csv_settings; 
		if($csv_settings['intro']) {
			$this->csv_intro = $csv_settings['intro']; 
		}
		/*
		$this->csv_uploaddir = $csv_uploaddir; 
		$this->csv_separator = $csv_separator; 
		if($csv_intro) {
			$this->csv_intro = $csv_intro; 
		}
		if($preprocess_function_name) {
			$this->preprocess_function_name = $preprocess_function_name; 
		}
		if($validate_function_name) {
			$this->validate_function_name = $validate_function_name; 
		}
		*/
	} 
	
	public function set_preprocessed_array($preprocessed_array) {
		$this->preprocessed_array = $preprocessed_array; 
	}
	
	public function check_or_insert($preprocessed_array, $check_or_insert) {
		
		if(!function_exists('tweak_feedback')) { 
			function tweak_feedback($check) {
				if($check =='') {
					return array('error_class'=>'', 'feedback'=>''); // ie: no positive feedback
				} else {
					return array('error_class'=>' error', 'feedback'=>$check);
				}
			}
		}
		
		
		
		if($this->csv_settings['validate_function']) {
			$validate_function_name = $this->csv_settings['validate_function'];
		}
		
		reset($preprocessed_array);

		$csv_html  = "<table cellspacing='0' id='csvtable'>\n";
		$csv_html  .= "<tr>\n";
		$db_table_keys = array_keys($this->field_array);
		
		$issues = '';
		
		// Is the first column an auto_index? If so, it won't exist in $preprocessed_array
		$first_column_code = $this->field_array[$db_table_keys[0]]['code'];
		$has_auto_index = (strpos($first_column_code,'p') !== false); // the primary index/id				
		$offset = ($has_auto_index) ? 1 : 0;
				
		
		// header row
		foreach($db_table_keys as $db_table_key) {
			$csv_html  .= "<th class='".$db_table_key."'>".$db_table_key."</th>";
		}
		$csv_html  .= "</tr>\n";
		
		// main body
		if($has_auto_index) {
			$first_key = array_shift($db_table_keys); // NB: $first_key has now been removed from $db_table_keys
		}
		$colmax = count($db_table_keys);

		foreach($preprocessed_array as $rownum => $row) {
			$error_class = '';
			if(count($row) != $colmax) {
				$issues .= "<li>E2: Column count for (human) row ".$rownum+1 ." is ".count($row) + $offset."</li>\n";
				$error_class = " class='major_error'";
			}
		
			$csv_html .= "<tr".$error_class.">\n";
			
			if($has_auto_index) {
				$csv_html  .= "<td class='".$db_table_keys[0]."'>(".($rownum+1).")</td>"; // human counting for rows
			}
			foreach($row as $cellnum => $value) {
				$key = $db_table_keys[$cellnum]; // first key may have already been allocated to the auto_index 
				$tooltip = '';
				$cell_check = $validate_function_name($key, $value, $this->field_array[$key]);  // v6.71
				//WFB('$key: '.$key.' $value: '.$value.' $feedback: '.$cell_check['feedback']);
				if(!empty($cell_check['feedback'])) { // ie: we have a problem
					$tooltip = " title='".$cell_check['feedback']."' ";
					$issues .= "<li>Invalid '".$key."' value in (human) row ".($rownum+1)."</li>\n";
				}
				$csv_html  .= "<td class='".$key.$cell_check['error_class']."'".$tooltip.">".$value."</td>";
			}
			$csv_html  .= "</tr>\n";
		}
		$csv_html  .= "</table>\n";
		
		if($check_or_insert == 'insert' && $issues == '') {
			$args = array_fill(0, count($preprocessed_array[0]), '?');
			//WFB($preprocessed_array[0]);
			//$query = "INSERT INTO ".$this->table_name." (".date, transaction, debit_ac, credit_ac, amount, ref, comment.") VALUES (".implode(',', $args).")";
			$query = "INSERT INTO ".$this->table_name." (".implode(',', $db_table_keys).") VALUES (".implode(',', $args).")";
			$stmt = MCP::$pdo->prepare($query);
			//WFB($query);
			foreach ($preprocessed_array as $table_row) {
			   //$stmt->execute($table_row);
			}
		}

		
		$check = array('rows' => count($preprocessed_array), 'issues' => $issues, 'show_html' => $csv_html); // TEMP!!
		return $check;
	}
	
	
	
	
	
	public function check_or_insert2($uploadfile, $check_or_insert) {
		
		if(!function_exists('tweak_feedback')) { 
			function tweak_feedback($check) {
				if($check =='') {
					return array('error_class'=>'', 'feedback'=>''); // ie: no positive feedback
				} else {
					return array('error_class'=>' error', 'feedback'=>$check);
				}
			}
		}
		
		
		$csvarray = $this->csv_to_array($uploadfile);
		//var_dump($csvarray);
		if($this->csv_settings['preprocess_function']) {
			$preprocess_function_name = $this->csv_settings['preprocess_function'];
			$csvarray = $preprocess_function_name($csvarray);	 
		}
		$this->set_preprocessed_array($csvarray);
		$preprocessed_array = $csvarray;
		
		if($this->csv_settings['validate_function']) {
			$validate_function_name = $this->csv_settings['validate_function'];
		}
		
		//reset($preprocessed_array);

		$csv_html  = "<table cellspacing='0' id='csvtable'>\n";
		$csv_html  .= "<tr>\n";
		$db_table_keys = array_keys($this->field_array);
		
		$issues = '';
		
		// Is the first column an auto_index? If so, it won't exist in $preprocessed_array
		$first_column_code = $this->field_array[$db_table_keys[0]]['code'];
		$has_auto_index = (strpos($first_column_code,'p') !== false); // the primary index/id				
		$offset = ($has_auto_index) ? 1 : 0;
				
		
		// header row
		foreach($db_table_keys as $db_table_key) {
			$csv_html  .= "<th class='".$db_table_key."'>".$db_table_key."</th>";
		}
		$csv_html  .= "</tr>\n";
		
		// main body
		if($has_auto_index) {
			$first_key = array_shift($db_table_keys); // NB: $first_key has now been removed from $db_table_keys
		}
		$colmax = count($db_table_keys);

		foreach($preprocessed_array as $rownum => $row) {
			$error_class = '';
			if(count($row) != $colmax) {
				$issues .= "<li>E2: Column count for (human) row ".$rownum+1 ." is ".count($row) + $offset."</li>\n";
				$error_class = " class='major_error'";
			}
		
			$csv_html .= "<tr".$error_class.">\n";
			
			if($has_auto_index) {
				$csv_html  .= "<td class='".$db_table_keys[0]."'>(".($rownum+1).")</td>"; // human counting for rows
			}
			foreach($row as $cellnum => $value) {
				$key = $db_table_keys[$cellnum]; // first key may have already been allocated to the auto_index 
				$tooltip = '';
				$cell_check = $validate_function_name($key, $value, $this->field_array[$key]);  // v6.71
				//WFB('$key: '.$key.' $value: '.$value.' $feedback: '.$cell_check['feedback']);
				if(!empty($cell_check['feedback'])) { // ie: we have a problem
					$tooltip = " title='".$cell_check['feedback']."' ";
					$issues .= "<li>Invalid '".$key."' value in (human) row ".($rownum+1)."</li>\n";
				}
				$csv_html  .= "<td class='".$key.$cell_check['error_class']."'".$tooltip.">".$value."</td>";
			}
			$csv_html  .= "</tr>\n";
		}
		$csv_html  .= "</table>\n";
		
		if($check_or_insert == 'insert' && $issues == '') {
			$args = array_fill(0, count($preprocessed_array[0]), '?');
			//WFB($preprocessed_array[0]);
			$query = "INSERT INTO ".$this->table_name." (".implode(',', $db_table_keys).") VALUES (".implode(',', $args).")";
			//$stmt = MCP::$pdo->prepare($query);
			$stmt = $this->pdo->prepare($query); //////////////////////////////////////////////////////////////////////////////////////// CHANGED 17/6/15
			//WFB($query);
			foreach ($preprocessed_array as $table_row) {
			   $stmt->execute($table_row);
			}
		}

		
		$check = array('rows' => count($preprocessed_array), 'issues' => $issues, 'show_html' => $csv_html); // TEMP!!
		return $check;
	}

	
	
	
	
	
	
	
	
	
	//php.net/manual/en/function.fgetcsv.php#98427
	//This is how to read a csv file into a multidimensional array.
	public function csv_to_array($uploadfile) {
		$csvarray = array();
		if(($handle = fopen($uploadfile, "r")) !== false) {
			$nn = 0; //Set the parent multidimensional array key to 0.
			while (($data = fgetcsv($handle, 1000, $this->csv_settings['separator'])) !== false) {
				$c = count($data); 
				for ($x=0;$x<$c;$x++) { 
					$csvarray[$nn][$x] = $data[$x];
				}
				$nn++;
			}
			fclose($handle);
		}
		return $csvarray;
	}
	
	
    		
	public function csv_upload($qmode) { 
	
		$check = array();
		$update_html ='';
		$upload_error = false;
		$issues = '';
		
		if($qmode=='csv0') {
			$update_html .= "
			<div id='csv_uploader'>\n".
				$this->csv_intro."
				<form enctype='multipart/form-data' method='post' action='".get_permalink()."?qmode=csv1#foot'>\n
					<div id='file_div2'>\n
						<label for='file2'>File:</label>\n
						<input type='file' name='file2' id='file2' value='".prepopulate('file2')."' />\n
					</div>\n
					<input type='submit' name='csv0_submit'  id='csv0_submit' value='Upload file' class='button' />\n
				</form>\n
			</div>";
			/*
			if($upload_error) { // mode gets reset to 'update0'
				$update_html .= "<p class='error'>There was a problem uploading the file. Please try again.</p>\n";
			}
			*/	
			//$ret = array ('update_html' => $update_html, 'upload_error' => $upload_error);
			$ret = array ('', $update_html, '', ''); // $show_html, $update_html, $upload_error, $uploadfile
		}
		
		if ($qmode=='csv1') {
			if ($_FILES["file2"]["error"] > 0) {
				$progress = "<p>Error: " . $_FILES["file2"]["error"] . "</p>";
			}
			else {
				$progress  = "
				<p>Upload: " . $_FILES["file2"]["name"] . "<br />
				Type: " . $_FILES["file2"]["type"] . "<br />
				Size: " . ($_FILES["file2"]["size"] / 1024) . " Kb<br />
				Stored in: " . $_FILES["file2"]["tmp_name"]."</p>";
			}
		
			$basename = basename($_FILES['file2']['name']);
			$uploadfile = $this->csv_settings['uploaddir'].$basename;
			
			if (move_uploaded_file($_FILES['file2']['tmp_name'], $uploadfile)) {
				$progress .= "<p>File is valid, and was successfully uploaded.</p>\n";
				
				
				/*
				$csvarray = $this->csv_to_array($uploadfile);
				//var_dump($csvarray);
				if($this->csv_settings['preprocess_function']) {
					$preprocess_function_name = $this->csv_settings['preprocess_function'];
					$csvarray = $preprocess_function_name($csvarray);	 
				}
				$this->set_preprocessed_array($csvarray);
				*/
				
				
				//$check = $this->check_or_insert($this->preprocessed_array,'check');
				$check = $this->check_or_insert2($uploadfile,'check');
				
				//$check = array('rows' => 13, 'issues' => '', 'show_html' => '<p>some html</p>'); // TEMP!!
				
				$progress .= "<p>".$check['rows']." rows</p>\n";
				//echo($issues);
				if ($check['issues'] == '') {
					$progress .= "<p>Seems to be a valid .csv file with no problems.</p>\n";
					
					$update_html .= "
				<p>Do you want to add to the existing data with a &lsquo;cleaned up&rsquo; version of the data in this file?</p>\n
				<form method='post' action='".get_permalink()."?qmode=csv2#foot'>\n
					<input type='hidden' name='basename'  id='basename' value='".$basename."' />\n
					<input type='submit' name='replace'  id='replace' value='Replace data' class='button' />\n
					<input type='submit' name='replaceCancel'  id='replaceCancel' value='Cancel' class='button' />\n
				</form>";
					
				} else {
					$progress .= "<ul>\n".$check['issues']."</ul>\n";
				}
	
				
			} else {
				$progress .= "<p>Possible file upload attack!</p>\n";
				$upload_error = true;
				$qmode='csv0'; // ie: back to where we started
			}
			$update_html = "<div id='csv_uploader'>\n".$progress.$update_html."\n</div>\n";;
			
			$ret = array ($check['show_html'], $update_html, $upload_error, $uploadfile); // $show_html, $update_html, $upload_error, $uploadfile
		}
		
		if ($qmode=='csv2') { // backup existing data for club, delete it from database, append new 'cleaned up' data, display it
			if(isset($_POST['replace'])) {
				//backup($_GET['club']);
				//delete($_GET['club']);
				$uploadfile = $this->csv_settings['uploaddir'].$_POST['basename'];
				$check = $this->check_or_insert2($uploadfile,'insert');
				//$insert = $check_function_name($uploadfile,$this->fieldnames,'insert'); // returns array(rows,issues,html)
				$update_html = "<p>Database updated.</p>\n";
			}
			if(isset($_POST['replaceCancel'])) {
				//$qmode='csv0';
				//$update_html = "<p>Update aborted.</p>\n";
				header("Location: ".SELF_URL."?qmode=csv0"); 
				exit();
			}
			//$ret = array ('update_html' => $update_html);
			$ret = array ('', $update_html, '', ''); // $show_html, $update_html, $upload_error, $uploadfile
		}
	
		return $ret;

	}




}
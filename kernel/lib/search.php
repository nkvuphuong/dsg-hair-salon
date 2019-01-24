<?php

$CMS->class->search = new class_search;

class class_search {

	/**
	 * EXAMPLE OF USAGES:
	 * 
	 * $CMS->class->search->table_name = "customer";
	 * $CMS->class->search->ignored_fields = array("cus_password");
	 * $CMS->class->search->changed_fields = array();
	 * $CMS->class->search->fields_type = array("cus_register_time" => "time");
	 * $CMS->class->search->search_type = 0;
	 * $CMS->class->search->fields_prefix = "";
	 *
	 */

	public $CMS = "";
	
	/**
	 * @param $table
	 *		The table name, ex: "user" or "customer", can be write in array mode
	 */

	public $table_name = "";
	
	/**s
	 * @param $mod_name
	 *		The module name, ex: "user" or "customer", type the value as the module name
	 */

	public $mod_name = "";
	
	/**
	 * @param $ignore_fields
	 *		Ignored field, in this array, you can remove the value of these field (What you want to protect), ex: array("user_password", "cus_password")
	 */
	 
	public $ignored_fields = array();
	
	/**
	 * @param $chnage_fields
	 *		Change field name, this will let's you write a safe name of field what showing for the web interface, ex: array("cus_username" => "username")
	 */
	 
	public $changed_fields = array();
	
	/**
	 * @param $this->fields_type
	 *		The fields type, ex: array("cus_register_time" => "time")
	 */
	 
	public $fields_type = array();
	
	/**
	 * @param $array_mode
	 *		The value is "0" or "1", if = 0, the output will be a string
	 */
	 
	public $array_mode = 0;
	
	/*
	 * @param $filter_type
	 *		Fixed Fields Type, DONT CHANGE if you are not lyhuuloi :))
	 */
	
	public $filter_type = array("time", "begin", "is", "contain", "end");
	
	/*
	 * @param $url_return
	 *		Fixed Url, DONT CHANGE if you are not lyhuuloi :))
	 */
	
	public $url_return = "";
	
	/*
	 * @param $msg_return
	 *		Fixed Message, DONT CHANGE if you are not lyhuuloi :))
	 */
	
	public $msg_return = "";
	
	/*
	 * @param $search_type
	 *		The value is "0" or "1", if = 0, query will run as AND CONDITION, if  = 1, query will run as OR CONDITION
	 */

	public $search_type = 0;
	
	/*
	 * @param $fiels_name
	 *		Fixed Fields Name, DONT CHANGE if you are not lyhuuloi :))
	 */

	public $fields_name = array();
	
	/*
	 * @param $fields_prefix
	 *		Prefix of the field name
	 */

	public $fields_prefix = "";
	
	/*
	 * @param $content_suffix
	 *		Suffix of the field content
	 */

	public $content_suffix = "";
	
	/*
	 * @param $fields_replace
	 *		Replace fields
	 */

	public $fields_replace = array();
	
	/*
	 * @param $table_alias
	 *		Help the sql query becomes the shortest
	 */

	public $table_alias = array();
	
	/*
	 * @param $table_extend
	 *		Determine the extend sql for each table, the result will be add to $sql_add
	 */

	public $table_extend = array();

	/*
	 * @param $sql_add
	 *		Extend sql for table (Multi-tables)
	 */

	public $sql_add = "";
	
	/*
	 * @param $check_duplicate
	 *		Store loaded field to prevent duplicate
	 */

	public $check_duplicate = array();
	
	/*
	 * @param $change_value
	 *		Change value of comparison, example: 
	 */

	public $change_value = array();

	//===========================================================================
	//  MAIN FUNCTION
	//===========================================================================
	
	public function get_info()
	{
		global $CMS, $DB;
		
		// If the Table Name is not available, return false
		if ( ! $this->table_name )
		{
			return false;
		}
		
		// Check if table name is an array
		if ( is_array($this->table_name) )
		{
			// Clean the value of table...
			$table_key = array_keys($this->table_name);
			
			$table_name = array();
			$cnt = 0;

			for ( $i = 0; $i <= count($table_key); $i++ )
			{
				if ( $this->table_name[$table_key[$i]] )
				{
					// Check extend sql in the table
					if ( $this->table_extend[$this->table_name[$table_key[$i]]] )
					{
						$this->sql_add .= $this->table_extend[$this->table_name[$table_key[$i]]];
					}
					
					// Clear the value
					$this->table_name[$table_key[$i]] = $CMS->class->filter->clean_value($this->table_name[$table_key[$i]]);
					
					// Create array of table(s)
					$table_name[$cnt] = $this->table_name[$table_key[$i]];
					$cnt++;
				}
			}
			
			// ... And then, re-set the value
			$this->table_name = $table_name;
		}
		else
		{
			// Clear the value
			$this->table_name = $CMS->class->filter->clean_value($this->table_name);
		
			// Convert table_name to array
			$this->table_name = array( 0 => $this->table_name );
		}

		// If the Module Name is not available, assign it to the table name
		if ( ! $this->mod_name )
		{
			$this->mod_name = $this->table_name[0];
		}

		// Set SQL Query for multi-tables
		eval("\$CMS->{$this->mod_name}->sql_table = \$this->sql_add;");
		
		// Create an Array to store the fields to search
		$search_array = array();
		
		// Fix duplicate data
		$backup_data = "";
		//$new_data = "";
		
		// Start get info		
		for ( $i = 0; $i < count($this->table_name); $i++ )
		{
			$table_name = $this->table_name[$i];
			
			// Checking for match with fields in the table
			$sql = $DB->query("SHOW FIELDS FROM ".root_table."{$table_name}");
		
			// Fetch the data
			while ( $data = $DB->fetch_array( $sql ) )
			{
				// Field name
				$field = $data[0];
				
				// Field type
				$type = $data[1];

				// Check Field Name
				$field_name = $this->changed_fields[$field] ? $this->changed_fields[$field] : $field;
				$field2 = $this->changed_fields[$field] ? $this->changed_fields[$field]."_to" : ($CMS->input[$field."_to"] ? $field."_to" : "");
				
					// Normal input field
					$input = urldecode($CMS->input[$field_name]);
		
					// Second input, using for the Time Input (dd/mm/YYYY)
					$input2 = $CMS->input[$field_name."_to"] ? urldecode($CMS->input[$field_name."_to"]) : "";
					
					// Search type
					$filter = strtolower($CMS->input[$field_name."_type"]);
					
					// Check "Search Type" in allowance types
					if ( @in_array($filter, $this->filter_type) == false )
					{
						$filter = "";
					}

				// Check and clear the input
				if ( in_array($field, $this->check_duplicate) == false AND $input != "" OR $input2 AND @in_array($field, $this->ignored_fields) == false )
				{
					// Store field
					$this->check_duplicate = array_merge($this->check_duplicate, array($field));
					
					// Check time
					if ( $this->fields_type[$field] == "time" )
					{
						// Convert From date to Unix Time
						$result = $input ? $CMS->class->date->date2time($input, 1) : "";
						
						// Convert To date to Unix Time
						$result2 = $input2 ? $CMS->class->date->date2time($input2, 1)+(24*3600) : "";
					}
					// Check int and tinyint
					else if ( preg_match("/(int|tinyint)/", strtolower($type)) == true )
					{
						$result = intval($input);
					}
					// Check float
					else if ( preg_match("/(float)/", strtolower($type)) == true )
					{
						$result = floatval($input);
					}
					// And clean the string value
					else
					{
						$result = str_replace("'", "&#39;", $input);
					}

					// SQL Field
					if ( $this->fields_replace[$field] )
					{
						$real_field = $this->fields_replace[$field];
					}
					else
					{
						// Check if there are multi-tables
						if ( count($this->table_name) > 1 )
						{
							if ( $this->table_alias[$table_name] )
							{
								$this->fields_prefix = $this->table_alias[$table_name].".";
							}
							else 
							{
								$this->fields_prefix = $this->table_name[0].".";
							}
						}
					
						$real_field = "{$this->fields_prefix}{$field}";
					}
	
					// Time Search
					if ( $this->fields_type[$field] == "time" )
					{
						// From date, To date
						if ( ! $result AND $result2 )
						{
							$search_array[$field] = "{$real_field} <= {$result2}";
						}
						else if ( $result2 )
						{
							$search_array[$field] = "{$real_field} >= {$result} AND {$real_field} <= {$result2}";
						}
						// From date only
						else
						{
							$search_array[$field] = "{$result} <= {$real_field}";
						}
					}
					// Begins with
					else if ( $filter == "begin" )
					{
						$search_array[$field] = "{$real_field} LIKE '{$result}%'";
					}
					// Extactly
					else if ( $filter == "is" )
					{
						$search_array[$field] = "{$real_field}='{$result}'";
					}
					// Ends with
					else if ( $filter == "end" )
					{
						$search_array[$field] = "{$real_field} LIKE '%{$result}'";
					}
					// Check number
					else if ( preg_match("/(int|tinyint|float)/", strtolower($type)) == true )
					{
						$search_array[$field] = "{$real_field}='{$result}'";
					}
					// Contain
					else
					{
						$search_array[$field] = "{$real_field} LIKE '%{$result}%'";
					}

					// Change value
					if ( $this->change_value[$field] )
					{
						$search_array[$field] = "{$real_field}={$this->change_value[$field]}";
					}
					
					// Content
					$realinput = "";
					if ( $this->content_prefix[$field] ) { $realinput .= "{$this->content_prefix[$field]} ";	}
					$realinput .= $input;
					if ( $this->content_suffix[$field] ) { $realinput .= " {$this->content_suffix[$field]}";	}
											
					// Fileds name
					$this->fields_name[$field] = $result;
					
					// Return URL
					$this->url_return .= "&{$field}=" . $input;
					
					if ( $field2 AND $input2 )
					{
						$this->url_return .= "&{$field2}=". $input2;
					}
					
					if ( $filter && $filter != "contain" )
					{
						$this->url_return .= "&{$field}_type={$filter}";
					}
					
					eval("\$data = \$CMS->{$this->mod_name}->searchvalue(\$this->fields_name); \$realinput = \$data[$field];");

					// Fix duplicate
					$new_data = $field;

					if ( $new_data != $backup_data )
					{
						// Message
						if ( $this->fields_type[$field] == "time" )
						{
							$this->msg_return .= "<li><u>{$CMS->lang[$field]}</u> ";
							
							if ( $input != "01/01/1970" )
							{
								$this->msg_return .= "<em>{$CMS->lang['search_from']}</em> <b>{$realinput}</b>";
							}
							
							if ( $input2 )
							{
								$this->msg_return .= " <em>{$CMS->lang['search_to']}</em> <b>{$input2}</b>";
							}
							
							$this->msg_return .= "</li>";
						}
						else if ( $filter )
						{
							$this->msg_return .= "<li><u>{$CMS->lang[$field]}</u> <em>".$CMS->lang["search_{$filter}"]."</em>: <b>{$realinput}</b></li>";
						}
						else
						{
							$this->msg_return .= "<li><u>{$CMS->lang[$field]}</u>: <b>{$realinput}</b></li>";
						}
					}
					
					$backup_data = $field;
				}
			}
		}

		// Check Array mode
		if ( $this->array_mode == 0 )
		{
			$new_data = "";
			$search_key = array_keys($search_array);
			
			for ( $i = 0; $i < count($search_array); $i++ )
			{
				$new_data .= " {$search_array[$search_key[$i]]} " . ($this->search_type == 0 ? "AND" : "OR" );
			}
			
			// Over-write data
			$search_array = $new_data;
		}

		// Decode
		//$search_array = urldecode($search_array);
		
		// Session
		$_SESSION["url_return"] = $this->url_return;

		// Output
		return $search_array;
	}
	
	//===========================================================================
	//  Repace Text
	//===========================================================================
	
	public function convertvalue( $data )
	{
		if ( ! $this->fields_name )
		{
			return $data;
		}
		
		$search = $this->fields_name;
		$search_array = array_keys($search);
		
		for ( $i = 0; $i < count($search_array); $i++ )
		{
			//$data[$search_array[$i]] = str_replace($search[$search_array[$i]], "<u><b>".$search[$search_array[$i]]."</b></u>", $data[$search_array[$i]]);
		}
		
		return $data;
	}
	
	//===========================================================================
	//  URL Fix
	//===========================================================================
	
	public function clean( $data )
	{
		global $CMS;
		
		$array_key = array_keys($data);
		
		for ( $i = 0; $i < count($data); $i++ )
		{
			$data[$array_key[$i]] = urldecode($data[$array_key[$i]]);
		}
		
		// Set url return
		$this->url_return = $CMS->class->input->geturl(array("act", "site", "view"), array("&act=search_do"), 1);
				
		return $data;
	}
	
	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS;
		
		//-----------------------------------------------------------
		// Search filter
		//-----------------------------------------------------------
		
		$data = "";
			
		$data .= "<option value='begin'>{$CMS->lang['search_begin']}</option>";
		$data .= "<option value='is'>{$CMS->lang['search_is']}</option>";
		$data .= "<option value='contain' selected>{$CMS->lang['search_contain']}</option>";
		$data .= "<option value='end'>{$CMS->lang['search_end']}</option>";

		$CMS->vars['search_type'] = $data;
	}

	//===========================================================================
	//  DETECT TABLE
	//===========================================================================
	
	public function detect( $data )
	{
		global $CMS;

		if ( $CMS->class->search->table_alias[$data] )
		{
			return $CMS->class->search->table_alias[$data].".";
		}
		
		return false;
	}
}

?>
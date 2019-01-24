<?php

$CMS->class->logs = new class_logs;

class class_logs {
	
	public $is_system = 0;
	public $key = "";
	 
	public $old_data = array();
	
	public $log_is_skip = 0; // Mark for is save logs of customer
	
	public $email_priority = 3;
	
	public $emailtpl = "system_logs_error";

	public $keep_content = 0;
	public $remove_logs_request = false;

	public function convert( $array_data )
	{
		global $CMS;
		
		$array_count = count($array_data);
		$data = serialize($array_data);
		
		$data = substr($data, 0, -1);
		$data = str_replace("a:".$array_count.":{", "", $data);
		
		return $data;
	}
	
	public function get_request()
	{
		global $CMS;
		
		// URL
		$url = parse_url($CMS->class->filter->clean_value($_SERVER['REQUEST_URI']));
        $url["query"] = isset($url["query"]) ? $url["query"] : "";
		$url = str_replace("&amp;", "&", $url["query"]);

		// String (Request)
		parse_str($url, $log_request);
		
		$log_key = array_keys($log_request);
		$new = array();
		
		for ( $i = 0; $i < count($log_request); $i++ )
		{
			$new[$CMS->class->filter->clean_value($log_key[$i])] = $CMS->class->filter->clean_value($log_request[$log_key[$i]]);
		}
	
		$log_request = serialize($new);
		
		return $log_request;
	}
	
	public function print_request( $data )
	{
		global $CMS;
		
		$request = @unserialize($data);
        $request_key = @array_keys($request);
        
        $data = "";
        
        for ( $j = 0; $j < count($request); $j++ )
        {
        	$data .= $request[$request_key[$j]] == "admin" ? "?" : "&";
        
            $data .= "{$request_key[$j]}={$request[$request_key[$j]]}";
        }
		
		return $data;
	}
	
	public function insert( $data, $content = "", $customer_id = 0 )
	{
		global $CMS, $DB, $member;
		
		// Check for admin module
		if ( $content == "admin" )
		{
			if ( $CMS->vars['is_admin_module'] == false )
			{
				return false;
			}
		}
		
		// Check for system
		if ( $this->is_system == 1 )
		{
			$type = 2;
		}
		// If admin
		else if ( isset($CMS->vars['is_admin_module']) && $CMS->vars['is_admin_module'] == true )
		{
			$type = 1;

			$user_id = isset($member['user_id']) && $member['user_id'] ? $member['user_id'] : 0;
			$cus_id = 0;
		}
		// If member
		else if ( $CMS->vars['is_member'] = true )
		{
			$type = 3;

			$cus_id = isset($member['cus_id']) && $member['cus_id'] ? $member['cus_id'] : 0;
			$user_id = 0;
		}
		// If customer
		else
		{
			$type = 0;
			
			$cus_id = isset($member['cus_id']) && $member['cus_id'] ? $member['cus_id'] : 0;
			$user_id = 0;
		}
		
		// Chec for manual cus id
		$cus_id = $customer_id ? $customer_id : $cus_id;
		
		// Check for cus name
        $member['cus_username'] = isset($member['cus_username']) ? $member['cus_username'] : null;
		$cus_name = $cus_id ? $member['cus_username'] : "";

		$data = str_replace( "'", "&#39;", $data );
		$content = str_replace( "'", "&#39;", $content );

		if ( ! $this->keep_content )
		{
			// 22-09/2017 : Lưu full content
			// $content = strlen($content) > 255 ? substr($content, 0, 255)."..." : $content;
		}
		else
		{
			$this->keep_content = 0;
		}
		
		// Check for content
		if ( ! $data )
		{
			return false;	
		}
 
		// Check for save logs of customer - hvu  27.3.2013
		if($type == 3 && $this->log_is_skip == 1)
		{
			$this->log_is_skip = 0;
			$type = 1;	 // Case 1: only show for admin 
		} 
		//$this->key = empty($this->key) ? $data : $this->key;
        $this->key = isset($this->key) ? $this->key : null;

        if($this->remove_logs_request == true)
        {
        	//Empty logs request
        	$DB->query("INSERT INTO ".root_table."logs ( log_time, log_name, log_content, log_request, user_id, cus_id, cus_name, log_ip_address, log_type, log_key ) VALUES ( '".time()."', '{$data}', '{$content}', '', '{$user_id}', '{$cus_id}', '{$cus_name}', '".$CMS->class->filter->ip_cleaner(getenv("REMOTE_ADDR"))."', '{$type}', '{$this->key}' );");

        }else
        {
        	$DB->query("INSERT INTO ".root_table."logs ( log_time, log_name, log_content, log_request, user_id, cus_id, cus_name, log_ip_address, log_type, log_key ) VALUES ( '".time()."', '{$data}', '{$content}', '".$this->get_request()."', '{$user_id}', '{$cus_id}', '{$cus_name}', '".$CMS->class->filter->ip_cleaner(getenv("REMOTE_ADDR"))."', '{$type}', '{$this->key}' );");

        }
		
		// Reset key
		unset($this->key);
		
		if (isset($member['user_id']) && $member['user_id'] )
		{
			$DB->query("UPDATE ".root_table."user SET user_last_activity='".time()."' WHERE user_id='{$member['user_id']}'");
		}
		
		if ( $type == 1 )
		{
			$data = "[{$member['user_display_name']}] {$data}";
		}
		else if ( $type == 0 )
		{
			$data = "[{$member['cus_realname']}] {$data}";
		}
		
		return $data;
	}
	
	public function insert_logs_system( $data, $content = "")
	{
		global $CMS, $DB, $member;

		$data = str_replace( "'", "&#39;", $data );
		$content = str_replace( "'", "&#39;", $content );
		$content = strlen($content) > 255 ? substr($content, 0, 255)."..." : $content;
		
		// Check for content
		if ( ! $data )
		{
			return false;	
		}
		if(preg_match("/(marked as crashed and should be repaired)/",$data) == true)
		{
			return false;
		}

		$DB->query("INSERT INTO ".root_table."logs_system ( log_time, log_name, log_content, log_request, log_ip_address, log_type, log_key ) VALUES ( '".time()."', '{$data}', '{$content}', '".$this->get_request()."', '".$CMS->class->filter->ip_cleaner(getenv("REMOTE_ADDR"))."', '2', '{$this->key}' );");
		
		// Get info
		$logs = $DB->fetch_array($DB->query("SELECT * FROM ".root_table."logs_system ORDER BY log_id DESC LIMIT 1"));
		
		// Data
		$CMS->input['log_id'] = $logs['log_id']; 
		
		// Input
		$CMS->email->email_priority = $this->email_priority;
		$CMS->email->logkey = "logs_system_{$logs['log_id']}";
		$CMS->email->email_template = $this->emailtpl;
		
		// Get user
		$CMS->user->get_info($CMS->vars['email_systemreport']); 
		
		$CMS->email->email_from = "error-logs@nhanhoa.com";
		$CMS->email->email_fromname = "Nhân Hòa Support";
		
		$CMS->email->email_to = $CMS->vars['email_systemreport'];
		$CMS->email->email_toname = $user['user_display_name'];
		
		// Send Email
		$result = $CMS->email->quick_send();

		// Reset key
		unset($this->key);
	}
	
	//===========================================================================
	//  SAVE DETAIL LOGS
	//===========================================================================
	public function save_detail($table_name="",$record_id="",$field_value=array(),$query_table = "")
	{
		global $CMS, $DB;
		
		if(!$table_name || !$record_id || count($field_value) == 0){return false;}  
		
		$data = $this->old_data;

		if(count($data) == 0)
		{
			return false;	
		}
		
		$array = array();
		
		// Create log key
		$logs_key = '';
		
		// Unset invalid field
		switch ($table_name)
		{
			case "customer":
				unset($data['cus_contract_name']);unset($data['cus_contract_phone']);unset($data['cus_contract_address']);
				unset($field_value['cus_contract_name']);unset($field_value['cus_contract_phone']);unset($field_value['cus_contract_address']);
				$logs_key = $table_name."_".$record_id;
				break;	
			case "system_security":
				$logs_key = $table_name;	
				break;
			case "config_price":
				$logs_key = $table_name;
				$table_name = $query_table;	
			break;	
			default:
				$logs_key=$this->key;
				break;
		}

		foreach($field_value as $key => $value)
		{
			if(is_string($key))
			{
				if($this->check_field_text($key,$table_name) == true)
				{
					if($data[$key] != $field_value[$key])
						{
							$array["{$key}"] = $data[$key];
							$array["{$key}_new"] = $field_value[$key];
						}
				}
			}
		}

		// Serialize
		if(count($array) > 0)
		{
			$content = serialize($array);
			// Get logs
			$logs = $DB->fetch_array($DB->query("SELECT * FROM ".root_table."logs WHERE log_key='{$logs_key}' ORDER BY log_id DESC LIMIT 1"));
			 
			// Update logs
			$DB->query("UPDATE ".root_table."logs SET log_content = '{$content}' WHERE log_id = '{$logs['log_id']}'");
		}
		
		// Unset data
		unset($this->old_data);
	}
	
	public function check_field_text($field, $table_name)
	{
		global $CMS, $DB;
		
		if(!$field || !$table_name) {return false;}
		
		// Query
		$sql = $DB->query("show columns from ".root_table."{$table_name}");
		
		while($data = $DB->fetch_array($sql))
		{
			if(trim($data['Field']) == $field)
			{
				if(trim($data['Type']) == "text") 
				{
					return false;	
				}
				return true;
			}
		}
	}
	
	public function get_info($id)
	{
		global $CMS, $DB;
		
		if(!$id) {return false;}
		
		$data = $DB->fetch_array($DB->query("SELECT * FROM ".root_table."logs_system WHERE log_id = '{$id}'"));
		
		return $data;
	}
	
	public function convertvalue($data)
	{
		global $CMS, $DB;
		
		if(count($data) == 0) {return false;}
		
		// Convert request
		$request = unserialize($data['log_request']);
		
		$output = "";
		foreach($request as $key => $value)
		{
			$output .= $key." - ".$value."; ";	 
		}
		$data['log_request'] = $output;
		
		// Log time
		$data['log_time'] = $CMS->class->date->date_format($data['log_time']);
		
		// Ip
		$data['log_ip'] = $data['log_ip_address'];
		
		return $data;
		
	}
	
	public function listing($key = null) {
		$reponse = array();
		if (! is_null($key)) {
			global $CMS, $DB;
			$sql = $DB->query("SELECT * FROM ".root_table."logs WHERE log_key='{$key}'  ORDER BY log_time DESC");
			if ($DB->num_rows($sql)) {
				while ($result = $DB->fetch_array()) {
					$reponse[] = $result;
				}
			}
		}
		return $reponse;
	}
	
}

?>
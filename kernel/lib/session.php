<?php

use \core\ezy;

$CMS->class->session = new class_session;

class class_session {

	public $data = array(); // Use for init()
	public $cache = false; // Check for use cache session
	public $cachemysql = 0; // Swich between cache file and mysql

	public function auto_run()
	{
		global $CMS, $member;

		//-----------------------------------------
		// Knock out Google Web Accelerator
 		//-----------------------------------------
        
		if ( strstr( strtolower(@$_SERVER['HTTP_X_MOZ']), 'prefetch' ) AND $member['user_id'] )
		{
			@header('HTTP/1.1 403 Forbidden');

			print "Prefetching or precaching is not allowed";
			exit();
		}

		ezy::init_session();
	}
	
	function domain_filter( $domain )
	{
		$domain = str_replace("http://www.", "", $domain);
		$domain = str_replace("https://www.", "", $domain);
		$domain = str_replace("http://", "", $domain);
		$domain = str_replace("https://", "", $domain);
		$domain = str_replace("www.", "", $domain);
		
		return $domain;
	}
	
	function get_hostname( $input )
	{
		$input = $this->domain_filter( $input );
		
		$input = explode("/", $input);

		$data = explode(".", $input[0]);

		if ( count($data) == 1 )
		{
			$data = $data[0];
		}
		else if ( strtolower($data[count($data)-1]) != "vn" )
		{
			$data = strtolower($data[count($data)-2].".".$data[count($data)-1]);
		}
		else if ( $data[count($data)-3] AND strlen($data[count($data)-2]) <= 4 AND preg_match("/(com|net|org|info|biz|us|asia|eu|me|name|tel|ws|tv|mobi|bz|mn|in|edu|pro|ac|health|gov|co|uk)/", strtolower($data[count($data)-2])) == true )
		{
			$data = strtolower($data[count($data)-3].".".$data[count($data)-2].".".$data[count($data)-1]);
		}
		else
		{
			$data = strtolower($data[count($data)-2].".".$data[count($data)-1]);
		}
		
		return $data;
	}

	//===========================================================================
	//  INIT SESSION
	//===========================================================================
	
	public function init( $session_key = 0 )
	{
		global $CMS, $DB, $_SESSION;	
		
		// System input
		$session_id = session_id();
		$ip = ezy::$ip_address;
		$session_time = time();
		$filename = str_replace(".", "", "{$session_id}_{$ip}");

		// Cache Mysql
		if ( $this->cachemysql == 1 )
		{
			$sql = $DB->query("SELECT * FROM ".root_table."sessions WHERE session_key='{$session_key}' AND session_id='{$session_id}' AND session_ip_address='{$ip}'");
		
			// Check if not exist
			if ( $DB->num_rows($sql) == 0 )
			{
				$DB->query("INSERT INTO ".root_table."sessions (session_id, session_key, session_time, session_ip_address) VALUES ('{$session_id}', '{$session_key}', '{$session_time}', '{$ip}')");
				
				$sql = $DB->query("SELECT * FROM ".root_table."sessions WHERE session_key='{$session_key}' AND session_id='{$session_id}' AND session_ip_address='{$ip}'");
			}
			
			$data = $DB->fetch_array($sql);
		
			$content = $data['session_content'] ? unserialize($data['session_content']) : array();
		}
		// Text file
		else
		{
			$CMS->class->cache->is_txtcache = 1;
			$content = $CMS->class->cache->check($filename, 1) ? unserialize($CMS->class->cache->load($filename)) : array();
			$CMS->class->cache->is_txtcache = 0;
		}

		$this->data = $content;
		$_SESSION = $this->data;
		$this->cache = true;
	}
	
	//===========================================================================
	//  SAVE SESSION
	//===========================================================================
	
	public function save( $input_data = "", $session_key = 0 )
	{
		global $CMS, $DB;

		// Check for enable or not
		if ( $this->cache == false )
		{
			return false;	
		}

		// System input
		$session_id = session_id();
		$ip = ezy::$ip_address;
		//$session_time = time();
		$filename = str_replace(".", "", "{$session_id}_{$ip}");

		// Save session
		if ( $input_data )
		{
			$this->data = array_merge($this->data, $input_data);	
		}
		else
		{
			$this->data = $_SESSION;	
		}

		$session_content = serialize($this->data);

	 	// Cache Mysql
		if ( $this->cachemysql == 1 )
		{
			$DB->query("UPDATE ".root_table."sessions SET session_content='{$session_content}' WHERE session_key='{$session_key}' AND session_id='{$session_id}' AND session_ip_address='{$ip}'");
		}
		// Text file
		else
		{
			$CMS->class->cache->is_txtcache = 1;
			$CMS->class->cache->save($filename, $session_content, 1);
			$CMS->class->cache->is_txtcache = 0;
		}

		return true;
	}

}

?>
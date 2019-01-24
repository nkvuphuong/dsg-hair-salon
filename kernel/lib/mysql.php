<?php

namespace lib;

use \core\ezy;

class db {
	    
	protected $conn;
	private $db_host;
	private $db_port;
	private $db_name;
	private $db_username;
	private $db_password;
	private $db_charset = "utf8";

 	public $query_id = 0;
	public $result = array();
	public $query_count = 0;
	public $query_time = 0;
	public $timestamp;

	public $is_error = 0; // Error appears true/false

    static public $notify_silent = false; // Don't show error
    static public $notify_slack = false; // send Slack notify
    static public $silent_msg = ""; // Output in silent mode

	public function __construct( $info = "" )
	{
		if ( ! isset($info) )
		{
			$this->error_handler ("Database Info Error");
		}
		
		$this->timestamp = time();

		// Set hostname
		$this->db_host = $info["db_host"];
		$this->db_port = $info["db_port"];
		
		// Check for remote mysql
		if ( $this->db_host != "localhost" AND $this->db_port )
		{
			$this->db_host = $this->db_host.":".$this->db_port;
		}
		
		// Set database info 
		$this->db_name = $info["db_name"];
		$this->db_username = $info["db_username"];
		$this->db_password = $info["db_password"];
		//$this->db_charset = $info["db_charset"];

		// Connect
		$this->connect();
	}
	
	/**
    * Auto close mysql
    */
	
	function __destruct()
	{
		$this->close();	
	}
	
	/**
    * Connect to mysql
    */
	
	private function connect()
    {
        global $CMS;

        $this->conn = @mysqli_connect($this->db_host, $this->db_username, $this->db_password, $this->db_name);

        //mysqli_select_db($this->db_name, $this->conn);

        // Error connection
        if (mysqli_connect_errno()) {
            $this->error_handler(mysqli_connect_error()."/".mysqli_connect_errno());
            exit();
        }

		if ( $this->db_charset )
		{
		    //get and set time zone
            $sql_check_table = "SHOW TABLES LIKE '".root_table."conf_settings'";
            $sql_check_table = $this->query($sql_check_table);
            $table_exists = $this->num_rows($sql_check_table);

            if($table_exists)
            {
                $sql = "SELECT conf_value FROM ".root_table."conf_settings WHERE conf_key = 'timezone_id'";
                $sql = $this->query($sql);
                $re = $sql->fetch_assoc();
                $timezone_id = $re['conf_value'];
            }

            $timezone_id = isset($timezone_id) ? $timezone_id : date_default_timezone_get();
            $timezone_info = date::getTimezone($timezone_id);

			$this->conn->query("SET NAMES '". $this->db_charset ."', time_zone='{$timezone_info['timezone']}'");
		}
    }

	public function query( $query_string = "" )
	{
		// dump($query_string);
        // if($_SERVER['REMOTE_ADDR'] == "116.109.68.187")
        // {
	       // print $query_string."<br/>";
        // }
        if ( $query_string )
		{
			$this->timestamp = time();
			$this->query_id = $this->conn->query($query_string) or $this->error_handler($query_string);
             
			$this->query_count++;
		}

		return $this->query_id;
	}
        
	public function get_query_count()
	{
		return $this->query_count;
	}

	public function fetch_array( $query_id= "" )
    {
        if ( $query_id != "" ) {
		    $this->query_id = $query_id;
        }
    
		$this->result = $this->query_id->fetch_array();
		return $this->result;
	}

	public function fetch_assoc( $query_id= "" )
    {
        if ( $query_id != "" ) {
		    $this->query_id=$query_id;
        }
		$this->result = $this->query_id->fetch_assoc();
		return $this->result;
	}

	public function fetch_tables_name()
	{
		if ($this->db_name!="")
		{
			$tables = mysqli_list_tables($this->db_name);
			$count = 0;
			
			while (list($table_name) = $tables->fetch_array())
			{
				$this->result[$count] = $table_name;
				$count++;
			}
		}
		return $this->result;
	}
	      		
	public function num_rows( $query_id= "" )
	{
		if ( $query_id != "" ) {
		    $this->query_id = $query_id;
        }
		return $this->query_id->num_rows;
	}
	
	public function close()
	{
		if ( $this->conn )
		{
			$this->conn->close();
		}

		return true;
	}

	/**
    * Simple fetch data
	* @param $sql_query = Query, $field = Field name;
    * @return array
    */
		
	public function fetch( $sql_query, $field = "" )
	{
		$this->query_id = $this->query( $sql_query );
		
		if ( $field )
		{
			$data = $this->query_id->fetch_array();
			return $data[$field];
		}
		else
		{
			return $this->query_id->fetch_array();
		}
	}

	public function last_insert_id()
	{
		return $this->conn->insert_id;
	}

	public function show_columns($table_name)
	{
		$return = array();

		$sql = "SHOW COLUMNS FROM ".root_table."{$table_name}";
		$sql = $this->query($sql);

		while($column = $sql->fetch_assoc())
		{
			$return[] = $column;
		}

		return $return;
	}

	public function get_column_names($table_name)
	{
		$return = array();
		$data = $this->show_columns($table_name);
		if(empty($data) || !is_array($data)) return false;

		foreach($data as $k => $v)
		{
			$return[] = $v['Field'];
		}
		return $return;
	}

    /**
     * Error handler
     * @param string $msg
     */

	public function error_handler($msg = "")
    {
        global $CMS;
 
        // Notify Slack
        if ( self::$notify_slack == true ){
            $new_msg = $msg.PHP_EOL.$this->conn->errno.PHP_EOL.$this->conn->error;
            $CMS->api->slack->sendMessage("[{$_SERVER['SERVER_NAME']}] {$new_msg}","[MySQL] Query Error");
        }

        // Check silent
        if ( self::$notify_silent  == true ){
            self::$silent_msg = $msg.PHP_EOL.$this->conn->errno.PHP_EOL.$this->conn->error;
            return true;
        }

        // Continue
        $this->is_error = 1;

        //$CMS->class->logs->insert_logs_system( $conn->error(),$msg );


        // If debugger is enable
        if ( $CMS->vars['db_debug'] == 1 )
        {
            $output = ezy::error_mysql($msg, $this->conn);
        }
        // IF debugger is disable
        else
        {
            //$output = ezy::error_mysql($msg, $this->conn);
            $output = ezy::error_maintenance();
        }

        // Output error
        exit($output);
    }

    public function affected_rows()
    {
        return $this->conn->affected_rows*1;
    }


    /**
     * Insert record sử dụng chuỗi dữ liệu truyền vào
     * @param string $tblName
     * @param array $data
     * @param array $convertData: Dùng để chuyển các key của dữ liệu truyền vào cho khớp với field của table
     * @return int
     */
    public function insert($tblName , $data, $convertData = [])
    {
        global $CMS;

        if(!$tblName || !$data) return false;

        /*
         * Convert dữ liệu đầu vào theo đúng thên field trong DB
         * key là tên key của giá trị đầu vào
         * value là tên của đúng field trong DB
         */
        if($convertData)
        {
            $tmpData = [];
            foreach ($convertData as $k => $v)
            {
                if(isset($data[$k]))
                {
                    $tmpData[$v] = $data[$k];
                }
            }

            $data = $tmpData;
        }

        $sqlValues = "";
        $sqlFields = "";

        $dbFields = $this->get_column_names($tblName);

        //Lặp vòng mảng chuyển thành chuỗi sql
        foreach ($data as $field => $value)
        {
            if(in_array($field, $dbFields))
            {
                $sqlFields .= " {$field},";

                /*if(is_numeric($value))
                {
                    $sqlValues .= " {$value},";
                }
                else*/
                {
                    $sqlValues .= " '{$value}',";
                }
            }
        }

        $sqlFields = trim($sqlFields,',');
        $sqlFields = trim($sqlFields);
        $sqlValues = trim($sqlValues,',');
        $sqlValues = trim($sqlValues);

        if(!$sqlFields || !$sqlValues) return false;

        $sql = "INSERT INTO ".root_table."{$tblName} ({$sqlFields}) VALUES({$sqlValues})";

        $result = $this->query($sql);

        //Clear cache
        $CMS->class->cache->mdelete($tblName);

        return $result;
    }

    /**
     * Update record sử dụng chuỗi dữ liệu truyền vào
     * @param string $tblName
     * @param array $data
     * @param string $conditionField: Field dùng để xác định điều kiện update
     * @param array $convertData: Dùng để chuyển các key của dữ liệu truyền vào cho khớp với field của table
     * @return bool|int
     */
    public function update($tblName ,$data, $conditionField, $convertData = [])
    {
        global $CMS;

        if(!$tblName || !$data || !$conditionField) return false;

        foreach($data as $k => $v)
        {
            if(is_numeric($k))
            {
                unset($data[$k]);
            }
        }

        /*
         * Convert dữ liệu đầu vào theo đúng thên field trong DB
         * key là tên key của giá trị đầu vào
         * value là tên của đúng field trong DB
         */
        if($convertData)
        {
            $tmpData = [];
            foreach ($convertData as $k => $v)
            {
                if(isset($data[$k]))
                {
                    $tmpData[$v] = $data[$k];
                }
            }

            $data = $tmpData;
        }

        $sqlSetValues = "";

        $dbFields = $this->get_column_names($tblName);

        //Lặp vòng mảng chuyển thành chuỗi sql
        foreach ($data as $field => $value)
        {
            if(in_array($field, $dbFields))
            {
                /*if(is_numeric($value))
                {
                    $sqlSetValues .= " {$field}={$value},";
                }
                else*/
                {
                    $sqlSetValues .= " {$field}='{$value}',";
                }
            }
        }

        $sqlSetValues =trim($sqlSetValues,',');
        $sqlSetValues =trim($sqlSetValues);

        if(!$sqlSetValues) return false;
        if(!isset($data[$conditionField])) return false;

        $conditionValue = $data[$conditionField];

        $sql = "UPDATE ".root_table."{$tblName} SET {$sqlSetValues} WHERE {$conditionField}='{$conditionValue}'";

        $result = $this->query($sql);

        //Clear cache
        $CMS->class->cache->mdelete($tblName);

        return $result;
    }

    /**
     * @param $sql: query string
     * @param $cache_prefix: use for cache file name or cache key
     * @param int $cache_enable: 0 | 1
     * @param int $debug: 0 | 1
     * @return array
     * nkvp - 2017.10.25
     */
    public function fetch_data($sql, $cache_prefix=null, $cache_enable = 1, $debug = 0)
    {
        global $CMS;

        return $CMS->class->cache->quick_load($sql, $cache_prefix, $cache_enable, $debug);
    }

    /**
     * @param $sql: query string
     * @param $per_page: number of records per page
     * @param $prefix_html: prefix paging url
     * @param $suffix_html: suffix paging url
     * @param $page: current page
     * @param $cache_prefix: prefix cache name or cache key
     * @param int $cache_enable: 0 | 1
     * @param int $debug: 0 | 1
     * @return mixed
     * nkvp - 2017.10.25
     */
    public function fetch_listing($sql,$per_page=20,$prefix_html='',$suffix_html='',$page=1,$cache_prefix='', $cache_enable=1, $debug=0)
    {
        global $CMS;
 
        return $CMS->class->cache->quick_load_listing($sql,$per_page,$prefix_html,$suffix_html,$page,$cache_prefix, $cache_enable, $debug);
    }
}
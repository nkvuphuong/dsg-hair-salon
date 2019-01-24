<?php

namespace lib;

use \core\ezy;

$CMS->class->cache = new cache;

class cache {

	public $is_record = 1;
	public $is_memcache = 0;
	public $memcache;
	public $is_txtcache = 0;
	public $whitelist = array(); // These cache will not be clear by function
    public $redis;
    public $redis_domain;

    static public $default_dir;

	//=====================================================================================================
	//	AUTO RUN
	//=====================================================================================================

	public function __construct()
	{
		global $CMS;

//        $CMS->vars['cache_type'] = 'redis'; //Test
        $CMS->vars['cache_type'] = isset($CMS->vars['cache_type']) ? $CMS->vars['cache_type'] : 'file';
        $CMS->vars['is_cache'] = isset($CMS->vars['is_cache']) ? $CMS->vars['is_cache'] : 0;

        if(!isset($CMS->vars['cache_type'])) return false;

		if ( $CMS->vars['cache_type'] == 'memcache' )
		{
			if ( $CMS->vars['memcache_server'] AND $CMS->vars['memcache_port'] && $CMS->vars["memcache_enable"] == 1 )
			{
				$this->memcache = new Memcache;
				$this->memcache->connect($CMS->vars['memcache_server'], $CMS->vars['memcache_port']) or die ("Could not connect");

				$this->is_memcache = 1;
			}			
		}
		else if ( $CMS->vars['cache_type'] == 'redis' )
        {
            $this->redis_init();
        }

        // Default cache folder
        //self::$default_dir = root_path."/cache";
	}

	//=====================================================================================================
	//	CHECK CACHE
	//=====================================================================================================

	public function check( $name = "", $session = 0 )
	{
		global $CMS;

		if (isset($CMS->vars['is_cache']) && $CMS->vars['is_cache'] != 1 AND $session == 0 )
		{
			return false;	
		}

		// Memcache
		if ( $CMS->vars['cache_type'] == 'memcache' )
		{
			if ( $this->memcache->get($name) == true )
			{
				return true;
			}
			else
			{
				return false;	
			}			
		}
		else if( $CMS->vars['cache_type'] == 'redis' )
        {
            $this->redis_init();

            try {
                return $this->redis->exists("{$this->redis_domain}.{$name}");
            }
            catch(\Exception $e) {
                return false;
            }
        }
        else
        {
            // Continue cached files
            //$name = $CMS->class->filter->clean_value( $name );
            $name = $CMS->class->seo->remove_vietnamese($name);
            $name = str_replace(" ", "_", $name);

            if ( file_exists(self::$default_dir."/{$name}.txt") == true )
            {
                return true;
            }
            else
            {
                return false;
            }
        }
	}
	
	//=====================================================================================================
	//	LOAD CACHE
	//=====================================================================================================
	
	public function load( $name = "" )
	{
		global $CMS;

//		if(!$this->check($name)) return false;
		
		// Memcache
        if($CMS->vars['cache_type'] == 'memcache')
        {
            $data = $this->memcache->get($name);
        }
		else if ($CMS->vars['cache_type'] == 'redis')
		{
            if(!$CMS->vars['is_cache']) return false;

            $this->redis_init();

            $data = $this->redis->get("{$this->redis_domain}.{$name}");
		}
		else
        {
            // Continue cached files
            //$name = $CMS->class->filter->clean_value( $name );
            $name = $CMS->class->seo->remove_vietnamese($name);
            $name = str_replace(" ", "_", $name);

            $data = @file_get_contents(self::$default_dir."/{$name}.txt");
        }

        // Json
        if (input::isJson($data)){
            $data = input::jsonDecode($data);
        }

		return $data;
	}
	
	//=====================================================================================================
	//	SAVE CACHE
	//=====================================================================================================

	public function save( $name = "", $data = "", $session = 0 )
	{
		global $CMS;

		$session = intval($session);

		if ( $CMS->vars['is_cache'] != 1 AND $session == 0 )
		{
			return false;
		}

        // encode
        if(is_array($data))
        {
            $data = input::jsonEncode($data, 0);
        }

		if($CMS->vars['cache_type'] == 'memcache')
        {
            $this->memcache->set($name, $data, 0, 3600*3);
        }
        else if($CMS->vars['cache_type'] == 'redis')
        {
            if(!$CMS->vars['is_cache']) return false;

            $this->redis_init();

            // Continue save this cache
//            if($this->check("{$this->redis_domain}.{$name}"))
//            {
//                $this->delete("{$this->redis_domain}.{$name}");
//            }

            try{
                $this->redis->set("{$this->redis_domain}.{$name}", $data);
            } catch(\Exception $e) {
                $this->redis->executeRaw(['config', 'set', 'stop-writes-on-bgsave-error', 'no']);
            }

        }
        else
        {
            // Continue cached files
            //$name = $CMS->class->filter->clean_value( $name );
            $name = $CMS->class->seo->remove_vietnamese($name);
            $name = str_replace(" ", "_", $name);
            // Auto create folder
            if ( !is_dir(self::$default_dir) ) {
                mkdir(self::$default_dir, 0755, true);
            }
            // Create cache
            file_put_contents(self::$default_dir."/{$name}.txt", $data);
        }

		return true;
	}
	
	//=====================================================================================================
	//	MULTIPLE DELETE
	//=====================================================================================================
	
	public function mdelete( $name = "" )
	{
		global $CMS;
		
		// Memcache
		if ( $CMS->vars['cache_type'] == 'memcache' )
		{
			$this->memcache->delete($name);
		}
		else if($CMS->vars['cache_type'] == 'redis')
        {
            if(!$CMS->vars['is_cache']) return false;

            $this->redis_init();

            //Get all keys with contain name

            $cache_key = $name ? "{$this->redis_domain}*.{$name}.*" : "{$this->redis_domain}.*";

            $data = $this->redis->keys($cache_key);



            try {
                $this->redis->del($data);
            }
            catch(\Exception $e)
            {
                $this->redis->executeRaw(['config', 'set', 'stop-writes-on-bgsave-error', 'no']);
            }
        }
		else
        {
            // Load dir
            $data = \scandir(self::$default_dir."/");
            $strlen = strlen($name);

            foreach ( $data as $file )
            {
                if ( preg_match("/({$name})/", $file) )
                {
                    @unlink(self::$default_dir."/{$file}");
                }
            }
        }

        return true;
	}
	
	//=====================================================================================================
	//	DELETE
	//=====================================================================================================
	
	public function delete( $name = "" )
	{
		global $CMS;

		// Memcache
		if ( $CMS->vars['cache_type'] == 'memcache')
		{
			$this->memcache->delete($name,(3600*24*30));
		}
		else if ($CMS->vars['cache_type'] == 'redis')
        {
            if(!$CMS->vars['is_cache']) return false;

            $this->redis_init();

            try {
                $this->redis->del("{$this->redis_domain}.{$name}");
            }
            catch (\Exception $e)
            {
                $this->redis->executeRaw(['config', 'set', 'stop-writes-on-bgsave-error', 'no']);
            }
        }
		else
        {
            //$name = $CMS->class->filter->clean_value( $name );
            $name = $CMS->class->seo->remove_vietnamese($name);
            $name = str_replace(" ", "_", $name);

            // Continue cached files
            @unlink(self::$default_dir."/{$name}.txt");
        }

		return true;
	}

	//=====================================================================================================
	//	CLEAR CACHE
	//=====================================================================================================

	public function clear()
	{
		global $CMS;

		// Set whitelist cache
		$this->whitelist = array(
			"board_whitelist_ip", // cron/security
			"serverlist_monitor", // cron/server
			"chat_mainroom" // cron/chat
		);
		
		// Save whitelist
		$slist = array();
		$wlist = $this->whitelist;

		for ( $i = 0; $i < count($wlist); $i++ )
		{
			if ( $wlist[$i] )
			{
				$slist = array_merge($slist, array("{$wlist[$i]}" => $this->load($wlist[$i])));	
			}
		}

		// Memcache
		if ( $CMS->vars['cache_type'] == 'memcache' )
		{
			$this->memcache->flush();

			// Update whitelist
			$ulist = array_keys($slist);

			for ( $i = 0; $i < count($ulist); $i++ )
			{
				if ( $ulist[$i] )
				{
					$this->save($ulist[$i], $slist[$ulist[$i]], 1);	
				}
			}

			return true;	
		}
		else if ( $CMS->vars['cache_type'] == 'redis' )
        {
            if(!$CMS->vars['is_cache']) return false;

            $this->redis_init();

            $this->redis->flushall();
        }
        else //file
        {
            // Auto create folder
            if ( !is_dir(self::$default_dir) ) {
                mkdir(self::$default_dir, 0755, true);
            }

            // Load dir
            $data = \scandir(self::$default_dir."/");

            foreach ( $data as $file )
            {
                if ( $file != "index.html" AND $file != ".htaccess" AND !in_array($file, $this->whitelist) )
                {
                    @unlink(self::$default_dir."/{$file}");
                }
            }
        }



        return true;
	}
	
	//=====================================================================================================
	//	Template Cache
	//=====================================================================================================

	public function tplcheck( $name = "" )
	{
		global $CMS;
		
		if ( $CMS->vars['is_cache'] != 1 )
		{
			return false;	
		}
		
		$name = $CMS->class->filter->clean_value( $name );

		if ( file_exists(self::$default_dir."/{$name}.tpl") == true )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function tplload( $name = "" )
	{
		global $CMS;
		
		$name = $CMS->class->filter->clean_value( $name );

		$data = include_once(self::$default_dir."/{$name}.tpl");
		
		return $data;
	}
	
	public function tplsave( $name = "", $data = "", $ext = "" )
	{
		global $CMS;

		$name = $CMS->class->filter->clean_value( $name );
		
		if ( $ext == "php" )
        {
			$ext = "tpl";
		}
		
		if( $fd = @fopen(self::$default_dir."/{$name}.{$ext}", "w") )
		{
			//$CMS->class->logs->insert("New cached: {$name}");
		
			@fputs( $fd, ($data), strlen($data) );
			@fclose( $fd );
			@chmod( self::$default_dir."/{$name}.{$ext}", 0644 );
		}
	}
	
	public function tplmdelete( $name = "" )
	{
		global $CMS;

        // Load dir
        $data = \scandir(self::$default_dir."/");

        foreach ( $data as $file )
        {
			if ( strlen(str_replace($name, "", $file)) != strlen($file) AND !in_array($file, $this->whitelist))
			{
				@unlink(self::$default_dir."/{$file}");
			}
		}
	}
	
	public function tpldelete( $name = "" )
	{
		global $CMS;
		
		// Name
		$name2 = substr(md5($name),0,16).".".$name;

		@unlink(self::$default_dir."/{$name}.tpl");
		@unlink(self::$default_dir."/{$name2}.tpl");
	}

	public function tplclear()
	{
		global $CMS;

        // Load dir
        $data = \scandir(self::$default_dir."/");

        foreach ( $data as $file )
        {
			if ( ! @is_dir($file) )
			{
				if ( $file != "index.html" AND $file != ".htaccess" )
				{
					@unlink(self::$default_dir."/{$file}");
				}
			}
		}
	}

	//=====================================================================================================
	//	SQL Cache
	//=====================================================================================================

	public function checksql( $name = "" )
	{
		global $DB;
		
		$name = addslashes($name);
		
		$sql = $DB->query("SELECT * FROM ".root_table."cache WHERE cache_name='{$name}'");

		if ( $DB->num_rows($sql) > 0 )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function loadsql( $name = "" )
	{
		global $DB;
		
		$name = addslashes($name);
		
		$sql = $DB->query("SELECT * FROM ".root_table."cache WHERE cache_name='{$name}'");

		if ( $DB->num_rows($sql) == 0 )
		{
			$this->is_record = 0;
			return false;
		}

        $this->is_record = 1;

		$data = $DB->fetch_array($sql);
		
		if ( $data['cache_array'] == 1 )
		{
			$output = unserialize($data['cache_value']);
		}
		else
		{
			$output = $data['cache_value'];
		}
		
		return $output;
	}
	
	public function savesql( $name = "", $data = "" )
	{
		global $DB;

		$cache_name = addslashes($name);
		
		if ( is_array($data) == true )
		{
			$cache_value = serialize($data);
			$cache_array = 1;
		}
		else
		{
			$cache_value = $data;
			$cache_array = 0;
		}			

		// Check duplicate
		$this->loadsql($name);
		
		if ( $this->is_record == 0 )
		{	
			$DB->query("INSERT INTO ".root_table."cache (cache_name, cache_value, cache_array) VALUES ('{$cache_name}', '{$cache_value}', '{$cache_array}')");
		}
		else
		{
			$DB->query("UPDATE ".root_table."cache SET cache_value='{$cache_value}', cache_array='{$cache_array}' WHERE cache_name='{$name}'");
		}
	}
	
	public function deletesql( $name = "" )
	{
		global $DB;

		$this->delete(md5($name));
        $name = addslashes($name);

		$DB->query("DELETE FROM ".root_table."cache WHERE cache_name='{$name}'");
	}
	
	public function clearsql()
	{
		global $DB;

		$DB->query("DELETE FROM ".root_table."cache WHERE cache_is_protect=0");
	}

    /**
     * Quick load cache
     * @param string $sql
     * @param string $cache_prefix: without md5($sql)
     * @param boolean $debug
     * @return array
     * nkvp - 2017.10.25
     */
	public function quick_load($sql, $cache_prefix=null, $cache_enable=1, $debug=0)
    {
        global $CMS, $DB;

        $cache_key = $cache_prefix.'.'.md5($sql);

        $cache_enable = !$cache_prefix ? 0 : $cache_enable;

        if($cache_enable)
        {
            if($this->check($cache_key))
            {
                //load cache if this cache existed
                return $this->load($cache_key);
            }
        }

//        echo $cache_key; exit;

        $data = [];

        $sql = $DB->query($sql);

        while($rs = $DB->fetch_assoc($sql))
        {
            $data[] = $rs;
        }

        if($cache_enable)
        {
            //save cache
            $this->save($cache_key, $data);

            if($debug)
            {
                echo $cache_key; exit;
            }
        }


        return $data;
    }

    /**
     * quick load cache for listing
     * @param $sql
     * @param $per_page
     * @param $prefix_html
     * @param $suffix_html
     * @param $page
     * @param $cache_prefix
     * @return array
     * nkvp - 2017.10.25
     */
    public function quick_load_listing($sql,$per_page=20,$prefix_html='',$suffix_html='',$page=1,$cache_prefix='', $cache_enable=1, $debug=0)
    {
        global $CMS, $DB;

        $page =  intval($page) ? intval($page) : 1;
        $per_page =  intval($per_page) ? intval($per_page) : 20;


        $cache_key_hash = md5($sql.'_'.$per_page.'_'.$prefix_html.'_'.$suffix_html.'_'.$page);
        $cache_key = $cache_prefix.'.listing.'.$cache_key_hash;
        $cache_key_show_page = $cache_prefix.'.listing.show_page.'.$cache_key_hash;
        $cache_key_total_row = $cache_prefix.'.listing.total_row.'.$cache_key_hash;
        $cache_enable = !$cache_prefix ? 0 : $cache_enable;
        if(!$cache_enable || !$this->check($cache_key))
        {
       

            list($show_page, $sql_query) = $CMS->class->page->create($sql,$per_page,$prefix_html,$suffix_html);

            $cacheData = [];

            while($rs = $DB->fetch_assoc($sql_query))
            {
                $cacheData[] = $rs;
            }

            if($cache_enable)
            {
                //save cache
                $CMS->class->cache->save($cache_key, $cacheData);
                $CMS->class->cache->save($cache_key_show_page, $show_page);
                $CMS->class->cache->save($cache_key_total_row, $CMS->class->page->total_row);

                if($debug)
                {
                    echo $cache_key; exit;
                }
            }
        }
        else
        {
            $cacheData = $CMS->class->cache->load($cache_key);
        }

        if($cache_enable)
        {
            $show_page = isset($show_page) ? $show_page : $this->load($cache_key_show_page);
            $CMS->class->page->total_row = $CMS->class->page->total_row ? $CMS->class->page->total_row : $this->load($cache_key_total_row);
        }

        return [$show_page, $cacheData];
    }

    function redis_init()
    {
        global $CMS, $DB;

        $this->redis_domain = $_SERVER['HTTP_HOST'];

        if($this->redis) return true;

        //Redis
        require_once(root_path.'/vendor/predis-1.1/src/Autoloader.php');
        \Predis\Autoloader::register();

        $this->redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => 'localhost',
            'port'   => 6379,
        ]);

        try {
            $this->redis->connect();
        }
        catch (\Exception $e) { //Lỗi kết nối

            //bật cache
            $DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_data, conf_value, conf_group, conf_type, conf_order, conf_protected, conf_description, conf_lisence, conf_expried) VALUES('Enable cache', 'is_cache', NULL, 1, 1, 'yes_no', 0, 0, '', '', 0) ON DUPLICATE KEY UPDATE conf_value=1;
            ");

            //chuyển cache file
            $DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_data, conf_value, conf_group, conf_type, conf_order, conf_protected, conf_description, conf_lisence, conf_expried) VALUES('Cache engine', 'cache_type', NULL, 'file', 1, 'select', 0, 0, '', '', 0) ON DUPLICATE KEY UPDATE conf_value='file';
            ");

            $DB->query("TRUNCATE ".root_table."cache");

            $CMS->vars['is_cache'] = 1;
            $CMS->vars['cache_type'] = 'file';

            //Clear all cache file
            $this->clear();

            echo "Cache error. Please, try again !"; exit;
        }
    }
}

?>
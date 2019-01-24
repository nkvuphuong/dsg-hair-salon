<?php

namespace lib;

use \core\ezy;

$CMS->class->language = new language;

class language
{
	public $cache_loaded = array();

	static public $default; // Default language
	
	public function auto_run()
	{
		global $CMS;

		include(root_path."language/lang.inc.php");

		$lang_key = array_keys($CMS->language);
		$lang_name = $CMS->language;
		$CMS->vars['default_language'] = trim($CMS->vars['default_language']);
		// Default language
		$CMS->vars['default_language'] = isset($CMS->vars['default_language']) && $lang_name[$CMS->vars['default_language']] ? $CMS->vars['default_language'] : $lang_key[0];

		// Get Cookie
		$cookie['language'] = $CMS->class->cookie->get_cookie('language');

		if ( $cookie['language'] OR isset($CMS->input['language']) )
		{
			if ( isset($CMS->input['language']) )
			{
			    // Remove all first before assign cookie
                $CMS->class->cookie->delete("language");

                // Set cookie
				$CMS->vars['default_language'] = $lang_name[$CMS->input['language']] ? $CMS->class->filter->clean_value(strtolower($CMS->input['language'])) : $CMS->vars['default_language'];
				$CMS->class->cookie->set_cookie("language", $CMS->vars['default_language'], 1);
				
				// if referer is not available
				$CMS->vars['http_referer'] = $CMS->vars['http_referer'] ? $CMS->vars['http_referer'] : $CMS->vars['root_domain'];

				header ("location: ".str_replace("&amp;", "&", $CMS->vars['http_referer']));
				exit;
			}
			else
			{
				$CMS->vars['default_language'] = $cookie['language'];
				//$CMS->class->cookie->set_cookie("language", $CMS->vars['default_language'], 1);
			}
		}

        // Overwrite by new method
      
        self::$default = $CMS->vars['default_language'];

		// Start language
		$CMS->lang = array();

		// Default language of ezyphp's framework, do not remove.
        $this->load("_global");
        //$this->load("menu");
		$this->load("global");
	}
	
	public function load( $name , $overwrite_prefix = "")
	{
		global $CMS, $DB, $tpl;

		// Check cache
		if ( in_array($name, $this->cache_loaded) )
		{ 
			return true;	
		}

		// Continue		
		if ( ! $name )
		{
			return false;
		}

		// Check framework's language
        if ( substr($name, 0, 1) == "_" )
        {
            $prefix = "";
        }
        // Admin's language
		else if ( isset($CMS->vars['is_admin_module']) && $CMS->vars['is_admin_module'] == true )
		{
			$prefix = "admin_";
		}
		// User's language
		else
		{
			$prefix = "lang_";
		}

		if($overwrite_prefix != '')
		{
            $prefix = $overwrite_prefix;
        }
		 
		// Load language
        $filename = root_path."language/".self::$default."/{$prefix}{$name}.php";

		// Check exist language
		if ( file_exists($filename) )
		{
		    // Load them all
			include($filename);

			// Merge with existed language
			$CMS->lang = $lang ? array_merge($CMS->lang, $lang) : $CMS->lang;
			
			// Save cache
			if ( ! in_array($name, $this->cache_loaded) )
			{
				$this->cache_loaded = array_merge($this->cache_loaded, array($name));
			}
			
			return true;
		}
		// If error appears while loading language
		else
		{
		    //$tpl->msg = "System could not be load the language <b>{$filename}</b>";
            //echo ezy::load_layout("error_maintenance");
			//exit;
		}
	}
	
	public function replace( $array, $lang )
	{
		global $CMS, $DB;
		
		$data = $lang;
		
		$array_key = array_keys($array);

		for ( $i = 0; $i < count($array); $i++ )
		{
			$data = str_replace("%{$array_key[$i]}%", "{$array[$array_key[$i]]}", $data);
		}
		
		return $data;
	}

    /**
     * Get Default language
     * @return string
     */

	static public function getDefaultLanguage($en = "us")
    {
        return self::$default == "en" ? $en : self::$default;
    }
}

?>
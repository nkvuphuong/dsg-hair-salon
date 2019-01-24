<?php

namespace lib;

use \core\ezy;

$CMS->class->template = new template;

class template
{
	/**
    * Auto regconize original variables
    * @param $data = array data
    * @return $data
    */
	
	public function auto_key( $data )
	{
		global $CMS;
		
		$config = array_keys( $CMS->vars );

		for ( $i = 0; $i < count( $config ); $i++ )
		{
			$data = $this->replace( "@{$config[$i]}@", $CMS->vars[$config[$i]], $data );		
		}
		
		if ( $CMS->lang )
		{
			$language = array_keys( $CMS->lang );
	
			for ( $i = 0; $i < count( $language ); $i++ )
			{
				$data = $this->replace( "@{$language[$i]}@", $CMS->vars[$language[$i]], $data );		
			}
		}

		return $data;
	}
	
	/**
    * Replace a string
    * @return $data
    */
	
	public function replace( $input, $output, $data )
	{
		$data = str_replace( $input, $output, $data);
		
		return $data;
	}

	public function load_template( $name, $mod = "" )
	{
		global $CMS, $menu;

		$mod = $mod ? $mod : ($CMS->vars['is_admin_module'] == 1 ? $CMS->input['site'] : $menu[ strtolower($CMS->input['site']) ]);
		
		include_once(root_path.ezy::$app_dir."/modules/{$mod}/templates/{$name}.php");

		return new $name;
	}

	/**
    * Load simple template
    * @return $data
    */
	
	public function load_simple( $file )
	{
		$filename = explode("/", $file);
		$filename = str_replace(".php", "", $filename[count($filename)-1]);

		include_once(root_path.$file);
		
		return new $filename;		
	}
	
	/**
    * Auto run
	* @param $data = HTML Template
    * @return $data
    */
	
	public function auto_run( $name, $template_data )
	{
		global $CMS;

		ob_start();
		
		// Name
		$name = substr(md5($name),0,16).".".$name;
		
		// Check cache
		if ( $CMS->class->cache->tplcheck($name) == true )
		{
			$CMS->class->cache->tplload($name);
		}
		else
		{
			// Variables
			$template_data = preg_replace("'%core\.([a-zA-Z0-9_]*)'", "\$CMS->vars['\\1']", $template_data);
			$template_data = preg_replace("'input\.([a-zA-Z0-9_]*)'", "\$CMS->input['\\1']", $template_data);
			$template_data = preg_replace("'html\.([a-zA-Z0-9_]*)'", "\$CMS->global->html['\\1']", $template_data);

			// valid characters
			$valid_char = "([a-zA-Z0-9.=_\-\>\'\$\[\] \!\(\)]*)";

			// if conditional
            $template_data = preg_replace("'<\!--\[if {$valid_char}\]-->'", "<?php if ( \\1 ) { ?>", $template_data);
            $template_data = preg_replace("'<\!--\[endif\]-->'", "<?php } ?>", $template_data);

			// Variables
			$template_data = preg_replace("'%var\.([a-zA-Z0-9_]*)%'", "<?php print \$CMS->vars['\\1']; ?>", $template_data);
			$template_data = preg_replace("'%lang\.([a-zA-Z0-9_]*)%'", "<?php print \$CMS->lang['\\1']; ?>", $template_data);
			$template_data = preg_replace("'%data\.([a-zA-Z0-9_]*)%'", "<?php print \$data['\\1']; ?>", $template_data);
			$template_data = preg_replace("'%member\.([a-zA-Z0-9_]*)%'", "<?php print \$member['\\1']; ?>", $template_data);
			$template_data = preg_replace("'%stats\.([a-zA-Z0-9_]*)%'", "<?php print \$CMS->global->stats['\\1']; ?>", $template_data);
			$template_data = preg_replace("'%stats\.([a-zA-Z0-9_]*)\.([a-zA-Z0-9_]*)%'", "<?php print \$CMS->global->stats['\\1']['\\2']; ?>", $template_data);
			$template_data = preg_replace("'%gui\.([a-zA-Z0-9_]*)%'", "<?php print \$CMS->global->html['\\1']; ?>", $template_data);
			$template_data = preg_replace("'%gui\.([a-zA-Z0-9_]*)\.([a-zA-Z0-9_]*)%'", "<?php print \$CMS->global->html['\\1']['\\2']; ?>", $template_data);
			
			// Save cache
			$CMS->class->cache->tplsave($name, $template_data);
			$CMS->class->cache->tplload($name);
		}
		
		$template_data = ob_get_contents();
        ob_end_clean();
		
		return $template_data;
	}


    /**
     * LHL-2017, Convert PHP code to Template format
     * @param $content
     * @return mixed|string
     */

	static public function convertPhpTpl($data)
    {
        $from =  array(
            '#\<\?=\\\core\\\ezy::(\w+)\(\'(\w+)\'\);\?\>#is',// \core\ezy::render('')
            '#\<\?=\\\core\\\ezy::(\w+)\(\'(\w+)\', \'(\w+)\'\);\?\>#is',// \core\ezy::tpl('', '')
            '#\<\?=\\\core\\\ezy::(\w+)\(\"(\w+)\"\);\?\>#is',// \core\ezy::render("")
            '#\<\?=\\\core\\\ezy::(\w+)\(\"(\w+)\", \"(\w+)\"\);\?\>#is',// \core\ezy::tpl("", "")
            '#\<\?=\\\core\\\ezy::(\w+)\(\"(\w+)\",\"(\w+)\"\);\?\>#is',// \core\ezy::tpl("","")

            '#<\?=\$CMS->vars\[\'(\w+)\'\];\?\>#is',
            '#<\?=\$CMS->vars\[\'(\w+)\'\]; \?\>#is',
            '#<\? echo \$CMS->vars\[\'(\w+)\'\]; \?\>#is',
            '#<\?=\$CMS->lang\[\'(\w+)\'\];\?\>#is',
            '#<\?=\$CMS->lang\[\'(\w+)\'\]; \?\>#is',
            '#<\? echo \$CMS->lang\[\'(\w+)\'\]; \?\>#is',
            '#<\?(.*)\?\>#is',
        );

        $to = array(
            '{{$1.$2}}',
            '{{$1.$2.$3}}',
            '{{$1.$2}}',
            '{{$1.$2.$3}}',
            '{{$1.$2.$3}}',

            '{{$1}}',
            '{{$1}}',
            '{{$1}}',
            '{{lang.$1}}',
            '{{lang.$1}}',
            '{{lang.$1}}',
            '<!--$1-->',
        );

        $data = preg_replace($from, $to, $data);

        return $data;
    }

    /**
     * Convert Template format to PHP code
     * @param $data
     * @return mixed|string
     */

    static public function convertTplPhp($data)
    {
        $from =  array(
            '#{{lang\.(\w+)}}#is',
            '#{{(\w+)}}#is',
            '#\<\?php(.*)\?\>#is',
            //'#\<\?(.*)\?\>#is',


            '#{{(\w+)\.(\w+)\.(\w+)}}#is',
            '#{{(\w+)\.(\w+)}}#is',
        );

        $to = array(
            '<?=$CMS->lang[\'$1\'];?>',
            '<?=$CMS->vars[\'$1\'];?>',
            '<--$1-->',
            //'<--$1-->',

            '<?=\core\ezy::$1("$2", "$3");?>',
            '<?=\core\ezy::$1("$2");?>',
        );

        $data = preg_replace($from, $to, $data);

        return $data;
    }
}

?>
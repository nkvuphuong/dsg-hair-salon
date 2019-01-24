<?php

namespace lib;

use \core\ezy;

$CMS->class->input = new input;

class input
{
    /**
     * Remove unnecessary or invalid data in URL (inputs)
     */

	static public function parse_incoming()
	{
		global $CMS;

    		//$this->get_magic_quotes = get_magic_quotes_gpc();

    		$return = array();

		if( is_array($_GET) )
		{
			while( list($k, $v) = each($_GET) )
			{
				if ( is_array($_GET[$k]) )
				{
					while( list($k2, $v2) = each($_GET[$k]) )
					{
						$return[ $CMS->class->filter->clean_key($k) ][ $CMS->class->filter->clean_key($k2) ] = $CMS->class->filter->clean_value($v2);
					}
				}
				else
				{
					$return[ $CMS->class->filter->clean_key($k) ] = $CMS->class->filter->clean_value($v);
				}
			}
		}

		//-----------------------------------------
		// Overwrite GET data with post data
		//-----------------------------------------

		if( is_array($_POST) )
		{
			while( list($k, $v) = each($_POST) )
			{
				if ( is_array($_POST[$k]) )
				{
					while( list($k2, $v2) = each($_POST[$k]) )
					{
						$return[ $CMS->class->filter->clean_key($k) ][ $CMS->class->filter->clean_key($k2) ] = $CMS->class->filter->clean_value($v2);
					}
				}
				else
				{
					$return[ $CMS->class->filter->clean_key($k) ] = $CMS->class->filter->clean_value($v);
				}
			}
		}

		$return['request_method'] = strtolower($_SERVER['REQUEST_METHOD']);

		return $return;
	}

	public function number_asc( $test )
	{
		$data = $test;
		$new = array();
		
		for ( $i = 0; $i < count( $data ); $i++ )
		{
			$trunggian = $data[$i];
			//$backup = 0;
			$dinhvi = 0;
			
			for ( $j = $i; $j < count( $data ); $j++ )
			{
				if ( $trunggian > $data[$j] )
				{
					$trunggian = $data[$j];
					$dinhvi = $j;
				}
			}
		
			$backup = $data[$i];
			$data[$i] = $trunggian;
			$data[$dinhvi] = $backup;
		
			$new[$i] = $data[$i];
		
			//print_r( $data );
			//exit;
		}
		
		return $new;
	}
	
	public function number( $number, $type = ",", $dec=0)
	{
		$number = number_format( round( $number ), $dec, ' ', $type );
	
		return $number;
	}
	
	public function currency( $input, $dec=0, $type=1 )
	{
		global $CMS;

		$CMS->vars['currency_separate'] = $CMS->vars['currency_separate'] ? $CMS->vars['currency_separate'] : ".";
		$CMS->vars['currency_type'] = $CMS->vars['currency_type'] ? $CMS->vars['currency_type'] : "$";

		$currency = $type==1? "{$CMS->vars['currency_type']}" : $CMS->vars['currency_type'];
        if($CMS->vars['currency_type'] == '$')
        {
        	$whole = floor($input);
        	$fraction = $input - $whole;
        	if($fraction > 0)
        	{
            	$output = $currency.number_format($input, 2, '.', ',');
        	}else
        	{
        		$output = $currency.number_format($input, 0, '.', ',');
        	}
        }
        else
        {	
        	$input =round($input);
            $output = $this->number($input, $CMS->vars['currency_separate'], $dec) . $currency;
        }
		// /{$CMS->vars['currency_type']}

		//$output = $this->number($input, $CMS->vars['currency_separate']) . "<sup>đ</sup>";
		return $output;
	}

    public function is_email( $email )
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        else{
            return true;
        }
    }

    public function is_html( $input )
	{
		$backup_output = $input;
		$output = strip_tags($input, "");
		$is_html = strlen($backup_output) > strlen($output) ? 1 : 0;
		
		return $is_html;
	}
	
	public function is_nan( $input )
	{
    	return !is_numeric($input);
	}

	public function compare( $str1, $str2, $method = 1 )
	{
		$str1 = strtolower($str1);
		$str2 = strtolower($str2);
		
		switch ( $method )
		{
			case "0": // Begins with
				if ( substr($str1, 0, strlen($str2)) == $str2 )
				{
					return true;
				}
			break;
			case "1": // Is
				if ( $str1 == $str2 )
				{
					return true;
				}
			break;
			case "2": // Contains
				if ( strlen( str_replace( $str2, "", $str1 ) ) != strlen($str1) )
				{
					return true;
				}
			break;
			case "3": // Ends with
	
				if ( substr($str1, -strlen($str2)) == $str2 )
				{
					return true;
				}
			break;
			default: // Is
				if ( $str1 == $str2 )
				{
					return true;
				}
			break;
		}
		
		return false;
	}
	
	public function grab($source_to_grab, $delimiter_start, $delimiter_stop, $search='', $replace='')
	{
		$fd = ""; 
		$start_pos = 0;
		$end_pos = 0;
        $result = "";

		$source_to_grab = fopen($source_to_grab, "rb");
	   	
	   	while(true) 
	   	{
			if($end_pos > $start_pos) 
		      	{
				$result = substr($fd, $start_pos, $end_pos-$start_pos);
		         	$result .= $delimiter_stop;
				break;
		      	}
	
			$data = fread($source_to_grab, 8192);
	
			if(strlen($data) == 0) break;
			$fd .= $data;
	
			if(!$start_pos)
			{
				$start_pos = strpos($fd, $delimiter_start);
			}
	
			if($start_pos)
			{
				$end_pos = strpos(substr($fd, $start_pos), $delimiter_stop) + $start_pos;
			}
	   	}

	   	fclose($source_to_grab);
		
		if ( $search && $replace )
		{
			return preg_replace($search, $replace, $result);
		}
		else
		{
			return $result;
		}
	}
	
	public function geturl($replace, $replace_to = "", $remove_question_mark = 0)
	{
		global $CMS;

		// Get URL
		$url = parse_url($CMS->class->filter->clean_value($_SERVER['REQUEST_URI']));
		
		// Fix wrong type
		$url = ($remove_question_mark ? "&" : "").str_replace("&amp;", "&", $url["query"]);

		// Remove duplicate variable
		if ( is_array($replace) == false )
		{
			if ( $replace )
			{
				$str_replace = "&{$replace}={$CMS->input[$replace]}";
				$url = str_replace($str_replace, $replace_to, $url);
			}
		}
		else
		{
			for ( $i = 0; $i < count($replace); $i++ )
			{				
				if ( $replace[$i] )
				{
					$str_replace = "&{$replace[$i]}={$CMS->input[$replace[$i]]}";
					$url = str_replace($str_replace, $replace_to[$i], $url);
				}
			}
		}

		// Parse url to string
		parse_str($url, $log_request);
		
		// Add question-mark 
		$url = $remove_question_mark == 0 ? "?".$url : $url;
		
		return $url;
	}
	
	//===========================================================================
	//  NUMBER TO WORD
	//===========================================================================
	
	public function read_words($input)
	{
		$array = array( "không", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy",
                      "tám", "chín", "mười", "mười một", "mười hai", "mười ba",
                      "mười bốn", "mười lăm", "mười sáu", "mười bảy", "mười tám",
                      "mười chín", "hai mươi", 30 => "ba mươi", 40 => "bốn mươi",
                      50 => "năm mươi", 60 => "sáu mươi", 70 => "bảy mươi", 80 => "tám mươi",
                      90 => "chín mươi" );

		// Complete block 3 units
		$input = strlen($input) % 3 == 0 ? $input : "#".$input;
		$input = strlen($input) % 3 == 0 ? $input : "#".$input;
		
		// Convert to array
		$number = array();
		$cnt = 0;
		
		for ( $i = 0; $i < strlen($input); $i++ )
		{
			$number[$cnt][$i-($cnt*3)] = substr($input,$i,1);
			if ( ($i+1) % 3 == 0 ) { $cnt++; }
		}
		
		// Unit to array
		$unit = array("trăm", "nghìn", "triệu", "tỷ");

		// Start convert group
		$output = "";

		for ( $i = 0; $i < count($number); $i++ )
		{
			// First class
			if ( $number[$i][0] != "#" )
			{
				// Check for zero
				if ( $number[$i][0] == 0 AND $number[$i][1] == 0 AND $number[$i][2] == 0 )
				{
					// Remove all class
					$number[$i][1] = "#";
					$number[$i][2] = "#";
				}
				else
				{
					$output .= $array[$number[$i][0]]." ".$unit[0]." "; // count($number)-1-$i
				}					
			}
				
			// Second class
			if ( $number[$i][1] != "#" )
			{
				// Check for zero
				if ( $number[$i][1] == 0 AND $number[$i][2] == 0 )
				{
					// Remove Third class
					$number[$i][2] = "#";
				}
				// Check for zero
				else if ( $number[$i][2] != 0 AND $number[$i][1] == 0 )
				{
					$output .= " lẻ ";
				}
				// Check for 1
				else if ( $number[$i][1] == 1 )
				{
					$output .= " mười ";
				}
				// Simple
				else
				{
					$output .= $array[$number[$i][1]."0"]." ";
				}
			}

			// Third class
			if ( $number[$i][2] != "#" )
			{
				// Check for previous zero
				if ( $number[$i][1] != 0 AND $number[$i][2] == 0 )
				{
						
				}
				// Check for 1
				else if ( $number[$i][1] > 0 AND $number[$i][2] == 1 )
				{
					$output .= " mốt ";
				}
				// Check for 5
				else if ( $number[$i][1] > 0 AND $number[$i][2] == 5 )
				{
					$output .= " lăm ";
				}
				// Simple
				else
				{
					$output .= $array[$number[$i][2]]." ";
				}
			}

			// Check for Unit
			if ( ($number[$i][0] != 0 OR $number[$i][1] != 0 OR $number[$i][2] != 0) AND count($number)-1-$i  )
			{
				$output .= $unit[count($number)-1-$i]." ";
			}
		}
		
		return $output;
	}
	
	public function number_to_words($input)
	{
		$output = $this->read_words($input);
		$output = trim(strtoupper(substr($output,0,1)).substr($output,1,strlen($output)));
		
		return $output;
	}
	
	//===========================================================================
	//  ARRAY SORT
	//===========================================================================
	
	public function array_sort($array, $on, $order = "asc", $i = "", $field_update = "")
	{
		$new_array = array();
		$sortable_array = array();
	
		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}
	
			switch (strtolower($order)) {
				case "asc":
					asort($sortable_array);
				break;
				case "desc":
					arsort($sortable_array);
				break;
			}
			
			// Start from sort...
			if ( $i )
			{
				$i = intval($i);
				foreach ($sortable_array as $k => $v) {
					$new_array[$i] = $array[$k];
					
					// Field update
					if ( $field_update )
					{
						$new_array[$i][$field_update] = $i;	
					}
					
					$i++;
				}
			}
			// Normal sort
			else
			{
				foreach ($sortable_array as $k => $v) {
					$new_array[$k] = $array[$k];
					
					// Field update
					if ( $field_update )
					{
						$new_array[$i][$field_update] = $i;	
					}
					
				}
			}
		}

		return $new_array;
	}
	
	//===========================================================================
	//  TRIM ALL, LHL-16/09/2010
	//===========================================================================
	
	function trimall($str, $charlist = "\t\n\r\0\x0B")
	{
		$str = strip_tags($str);
		$str = str_replace(str_split($charlist), '', $str);

		return $str;
	}
	
	//===========================================================================
	//  COMPARE URL
	//===========================================================================
	
	function compare_url($str, $str2)
	{
		global $CMS;
		
		// Remove string
		$remove = array("_do");
		
		// Continue		
		$str = parse_url($str);
		$str2 = parse_url($str2);
		
		$str = explode("&", str_replace("&amp;", "&", $str['query']));
		$str2 = explode("&", $str2['query']);

		// Set max value
		if ( str_replace($remove, array(), $CMS->input['act']) == "search" )
		{
			$maxvalue = 3;	
		}
		else
		{
			$maxvalue = 4;	
		}

		// Continue
		for ( $i = 0; $i < $maxvalue; $i++ )
		{
			$str[$i] = str_replace($remove, array(), $str[$i]);

			if ( $str[$i] != $str2[$i] )
			{
				return false;	
			}
		}

		return true;
	}
	
	//===========================================================================
	//  Removes files and non-empty directories
	//===========================================================================

	function removedir($dir)
	{
		if (is_dir($dir))
		{ 
			$objects = @scandir($dir); 
		 	foreach ($objects as $object)
			{ 
		   		if ($object != "." && $object != "..")
				{ 
					if (filetype($dir."/".$object) == "dir") $this->removedir($dir."/".$object); else unlink($dir."/".$object); 
		   		} 
		 	}
			@reset($objects);
			@chmod($dir, 0777); 
			@rmdir($dir); 
		} 
	}
	
	//===========================================================================
	//  Copies files and directories
	//===========================================================================
	
	function rcopy($src, $dst)
	{
		if (is_dir($src))
		{
    		mkdir($dst);
    		$files = scandir($src);
			
    		foreach ($files as $file)
   			{
				if ($file != "." && $file != "..")
				{
					$this->rcopy("$src/$file", "$dst/$file");
				}
			}
		}
		else if (file_exists($src))
		{
			copy($src, $dst);
		}
	}
	
	//===========================================================================
	//  Rename file or directory
	//===========================================================================
	
	function rrename($src, $dst, $chmod = "")
	{
		// Check for chmod
		if ( $chmod )
		{
			@chmod($src, $chmod);
		}
		
		$return = @rename($src, $dst);
		
		// Check for chmod
		if ( $chmod )
		{
			@chmod($dst, $chmod);
		}
		
		return $return;
	}
	
	//===========================================================================
	//  Clear files on a dir
	//===========================================================================

	function cleardir($dir)
	{
		$dir = substr($dir,-1) == "/" ? $dir : $dir."/";		
		$data = @scandir($dir);
		
		foreach ( $data as $file )
		{
			if ( @filetype($dir.$file) == "file" )
			{
				@unlink($dir.$file);
			}
		}
		
		return true;
	}

    /*
     * Scan directory and load it.
     * @Author: lyhuuloi
     * @date: 11/03/2017
     */

    static public function scandir($dir, $ext = "php", $prefix = "", $except_empty = false)
    {
        $data = \scandir($dir);

        $output = [];

        foreach ( $data as $tree )
        {
            // Except . .. (dir up)
            if ( in_array($tree, array(".", "..")) )
            {
                continue;
            }
            // If it's a folder, try to load its files
            else if ( is_dir($dir.$tree) == true )
            {
                // Only load the folders with files inside.
                if ( $treelist = self::scandir($dir.$tree, $ext, $prefix) )
                {
                    $output[$tree] = $treelist;
                }
            }
            // It's a file
            else if ( substr($tree, -strlen($ext)) == $ext && substr($tree, 0, strlen($prefix)) == $prefix )
            {
                $output[] = $tree;
            }
        }

        return $output;
    }

    /**
     * Check input is money
     * @param $str
     * @return bool
     */

	public function is_money($str)
	{
		  if(preg_match('#[^0-9]#', $str))
		  {
		   	return false;
		  }
		  return true;
 	}


 	public function generate_code( $input = "", $maxlength = 0)
    {
        $str_len = strlen($input);
        $sub_str = $maxlength - $str_len;
        $len = "";
        for($i = 1; $i <= $sub_str; $i++)
        {
            $len = $len ."0";  

        }

        return $len.$input;

    }
	
	public function check_price($price)
	{
		global $CMS;
		
		$price = intval($price);
	 
		$max_price = intval($CMS->vars['assets_max_price']) ? intval($CMS->vars['assets_max_price']) : 10000000000;
		$min_price = intval($CMS->vars['assets_min_price']) ? intval($CMS->vars['assets_min_price']) : 0;
	
		if($price < $min_price || $price > $max_price)
		{
			return false;
		}
		
		return true;	
	}

    /**
     * LHL,09/04/17: Function to get real client IP
     * @return array|false|string
     */

    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    /**
     * @param $number
     * @param int $significance
     * @return bool|float
     * Example: echo ceiling(0, 1000);     // 0
    echo ceiling(1, 1);        // 1000
    echo ceiling(1001, 1000);  // 2000
    echo ceiling(1.27, 0.05);  // 1.30
     */

    static public function ceiling($number, $significance = 1)
    {
        return ( is_numeric($number) && is_numeric($significance) ) ? (ceil($number/$significance)*$significance) : false;
    }

    /**
     * Route Get ID, use for kernel/core.php
     * @param string $input: ezybook-123
     * Return: self::$input['ezybook'] = 123;
     */

    static public function route_get_id( $input = "" )
    {

        if ( $input )
        {
            $input_data = explode("-", $input);
            $input_id = $input_data[count($input_data)-1];

            // Find the ID
            if ( is_numeric($input_id) )
            {
                // Generate key name, for example: cut "-123" from "ezybook-123", and then return ezybook
                $input_key = substr($input, 0, -(strlen($input_id)+1));

                // Set value self::$input['ezybook'] = 123;
                ezy::$input[$input_key] = $input_id;

                // Remove data for self::$act or self::$subact
                return "";
            }
            else{ 
                // Continue without get ID.
                return $input;
            }

        }
    }

    // Overflow = 0: get exacly one day, not overflow to the day after
    // 04/07/2017 hvu added, using for report
    static function getTimeString($type="",$overflow = 1)
    {
    	global $CMS;

    	$timezone = 0; //$CMS->vars['timezone']*3600
        
    	if($type == "yesterday")
    	{
    		$time_from = strtotime('yesterday')+$timezone;
			$time_to = $overflow == 1 ? $time_from + 3600*24 : $time_from;
    	}elseif($type == "today")
    	{
    		$time_from = strtotime('today')+$timezone;
			$time_to = $overflow == 1 ? $time_from + 3600*24 : $time_from;
    	}elseif($type == "last_week")
    	{
    		$time_from = strtotime('monday previous week') + $timezone;
			$time_to = strtotime('sunday previous week') + $timezone;
    	}elseif($type == "this_week")
    	{
    		$time_from = strtotime('monday this week')+$timezone;
			$time_to = strtotime('sunday this week') + $timezone;
    	}elseif($type == "last_month")
    	{
    		$time_from = strtotime('first day of previous month')+$timezone;
			$time_to = strtotime('last day of previous month') + $timezone;
    	}elseif($type == "this_month")
    	{
    		$time_from = strtotime('first day of this month')+$timezone;
			$time_to = strtotime('last day of this month') + $timezone;
    	}elseif($type == "last_year")
    	{
			$time_from = strtotime('first day of January '.date('Y',strtotime('previous year'))) + $timezone;
			$time_to = strtotime('last day of December '.date('Y',strtotime('previous year'))) + $timezone;
    	}elseif($type == "this_year")
    	{
    		$time_from = strtotime('first day of January '.date('Y')) + $timezone;
			$time_to = strtotime('last day of December '.date('Y')) + $timezone;
    	}

    	return array($time_from, $time_to);
    }

    /**
     * LHL-2017, New version of substr
     * @param $string
     * @param int $from
     * @param int $to
     * @param int $type
     * @return bool|mixed|string
     */

    static public function substr( $string, $from = 0, $to = 40, $type = 0 )
    {
        global $CMS;

        if ( $type == 1 )
        {
            $string = str_replace("&nbsp;", "", $string);
        }

        // Check for string
        if ( strlen($string) < $to )
        {
            return $string;
        }

        // Continue
        $output = substr($string, $from, $to);

        for ( $i = 0; $i < 20; $i++ )
        {
            $to += 1;

            if ( substr($output, -1) != " " )
            {
                $output = substr($string, $from, $to);
            }
            else
            {
                break;
            }
        }

        $output = substr($output, 0, strlen($output)-1);

        if ( substr($output, -1) == "." )
        {
            $output = substr($output, 0, strlen($output)-1);
        }

        //if ( strlen($output) > strlen($input) )
        if ( strlen($string) > $to )
        {
            $output .= "...";
        }

        return $output;
    }

    function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * Check image / no-phot
     * @param $img
     * @param string $nophoto
     * @return string
     */

    static public function checkImage($img, $nophoto = "custom/no-photo.png")
    {
        global $CMS;

        $imgext = ["gif", "jpg", "peg", "png"];

        if ( ! $img ) { return $nophoto; }

        if ( in_array(substr(strtolower($img),-3), $imgext) == false ) { return $nophoto; }

        return file_exists("{$CMS->vars['upload_dir']}/".$img) ? "{$CMS->vars['upload_url']}/".$img : $nophoto;
    }

    /**
     * Check invalid domain
     * @param string $domain
     * @return string|boolean
     */

    static public function is_domain($domain = "")
    {  
        return filter_var(gethostbyname($domain), FILTER_VALIDATE_IP);
    }

    /**
     * Check invalid url
     * @param string $url
     * @return string|boolean
     */

    static public function is_url($url = "")
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * Convert original url to thumbnail url
     * @param string $path relative path of image (root is uploads folder)
     * @param string $width
     * @param string $folder folder thumbnail
     * @return string
     */
    static public function getThumb($path, $width=0, $folder = 'thumbnail')
    {
 
        $thumb = \lib\image::getThumb($path, $folder, 'w'.$width.'_', 1, $width);
        return self::checkImage($thumb);
    }

    /**
     * Convert usd amount to words
     * @param $number
     * @return bool|mixed|null|string
     */
    static function usd_to_words($number) {

        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'fourty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'usd_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . self::usd_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . self::usd_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = self::usd_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::usd_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }

    /**
     * @param $data
     * @param int $echo
     * @return string
     * nkvp
     */
    static function jsonEncode($data, $echo = 1)
    {
        if($echo)
        {
            header("Content-type: application/json; charset=utf-8");
            echo @json_encode($data, JSON_UNESCAPED_UNICODE); exit;
        }
        else
        {
            return @json_encode($data, JSON_UNESCAPED_UNICODE);
        }
    }

    static function jsonDecode($data, $is_array=1)
    {
        return @json_decode($data, $is_array);
    }

    /**
     * Json Auto fixer
     * Date: LHL-2018-07-11
     * @param $match
     * @return string
     */

    static function jsonFix_replace($item)
    {
        // Data looks like: {$key:$val}
        $key = ($item[1]);
        $val = ($item[2]);

        // Detect "
        if($val[0] == '"')
        {
            $val = '"'.addslashes(substr($val, 1, -1)).'"';
        }
        // Detect '
        else if($val[0] == "'")
        {
            $val = "'".addslashes(substr($val, 1, -1))."'";
        }

        // Return fixed data
        return $key.":".$val;
    }

    /**
     * Usage: echo \lib\input::jsonFix($data); => It will return fixed data
     * @param $data
     * @return null|string|string[]
     */

    static function jsonFix($data)
    {
        $data = preg_replace_callback("#([^{:]*):([^,}]*)#i",'self::jsonFix_replace',$data);

        // Fix comma
        $data = str_replace("\", ", ",", $data);

        return $data;
    }

    /**
     * @param $data
     * @return mixed
     * nkvp
     */
    static function urlDecode($data)
    {
        if($data)
        {
            foreach($data as $k => $v)
            {
                $data[$k] = urldecode($v);
            }
        }

        return $data;
    }

    /**
     * @param $string
     * @return bool
     * nkvp
     */
    static function isJson($string) {
        if(is_array($string)) return false;
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * nkvp
     * Get value $CMS->input
     * @param $key
     * @return null
     * ex:
     * $abc = isset($CMS->input['abc']) ? $CMS->input['abc'] : null <=> $abc = \lib\input::get('abc');
     * $abc = isset($CMS->input['abc']) ? $CMS->input['abc'] : 1 <=> $abc = \lib\input::get('abc', 1);
     */
    static function get($key, $default = null) {
        global $CMS;
        return !isset($CMS->input[$key]) ? $default : $CMS->input[$key];
    }

    /**
     * nkvp
     * Get value $CMS->lang
     * @param $key
     * @return null
     * ex:
     * $abc = isset($CMS->lang['abc']) ? $CMS->lang['abc'] : null <=> $abc = \lib\input::lang('abc');
     * $abc = isset($CMS->lang['abc']) ? $CMS->lang['abc'] : 1 <=> $abc = \lib\input::lang('abc', 1);
     */
    static function lang($key, $default = null) {
        global $CMS;
        return !isset($CMS->lang[$key]) ? $default : $CMS->lang[$key];
    }

    /**
     * nkvp
     * Get value $CMS->vars
     * @param $key
     * @return null
     * ex:
     * $abc = isset($CMS->vars['abc']) ? $CMS->vars['abc'] : null <=> $abc = \lib\input::vars('abc');
     * $abc = isset($CMS->vars['abc']) ? $CMS->vars['abc'] : 1 <=> $abc = \lib\input::vars('abc', 1);
     */
    static function vars($key, $default = null) {
        global $CMS;
        return !isset($CMS->vars[$key]) ? $default : $CMS->vars[$key];
    }

    /**
     * nkvp
     * Get value of Object. If key not existed then create this key and assign with $default
     * @param $arr
     * @param $key
     * @return mixed
     * $abc = isset($obj->abc) ? $obj->abc : null <=> $abc = \lib\input::objectValue($obj, 'abc');
     * $abc = isset($obj->abc) ? $obj->abc : 1 <=> $abc = \lib\input::objectValue($obj, 'abc', 1);
     */
    static function objectValue($obj, $key, $default = null) {
        return isset($obj->$key) ? $obj->$key : $default;
    }

    /**
     * nkvp
     * Get value of array. If key not existed then create this key and assign with $default
     * @param $arr
     * @param $key
     * @return mixed
     * $abc = isset($arr['abc']) ? $arr['abc'] : null <=> $abc = \lib\input::arrayValue($arr, 'abc');
     * $abc = isset($arr['abc']) ? $arr['abc'] : 1 <=> $abc = \lib\input::arrayValue($arr, 'abc', 1);
     */
    static function arrayValue($arr, $key, $default = null) {
        $arr[$key] = isset($arr[$key]) ? $arr[$key] : $default;
        return $arr[$key];
    }

    /**
     * Trim input phone
     */
    static function trimPhone( $phone="" )
    {
        $phone = ltrim($phone, "0");
        $phone = str_replace(" ", "", $phone);
        $phone = str_replace("-", "", $phone);
        $phone = str_replace("_", "", $phone);
        $phone = str_replace("(", "", $phone);
        $phone = str_replace(")", "", $phone);
        
        // Return
        return $phone;
    }

    /**
     * nkvp - 05/10/2018
     * @param $str
     * @return null|string|string[]
     */
    static function phoneNumber($str)
    {
        $return = preg_replace('/^(\d{3})(\d{4})(\d{3,4})$/', '$1.$2.$3', $str);
        return $return ? $return : $str;
    }
}
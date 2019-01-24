<?php

$CMS->class->filter = new class_filter;

class class_filter
{
	public function clean_key($key, $type=1)
	{
		global $CMS;

    		if ($key == "")
    		{
    			return "";
    		}

    		$key = $type==1? htmlspecialchars(urldecode($key)) : htmlspecialchars($key);
    		$key = preg_replace( "/\.\./"           , ""  , $key );
    		$key = preg_replace( "/\_\_(.+?)\_\_/"  , ""  , $key );
    		$key = preg_replace( "/^([\w\.\-\_]+)$/", "$1", $key );

    		return $key;
   	}	
	
	public function clean_value($text)
	{
		global $CMS;

			if ($text == "")
	    	{
	    		return "";
	    	} else if (is_array($text)) {
			    return $text;
            }

	    	$text = str_replace( "&#032;", " ", $text );	    	
	    	$text = str_replace( "&"            , "&amp;"         , $text );
	    	$text = str_replace( "<!--"         , "&#60;&#33;--"  , $text );
	    	$text = str_replace( "-->"          , "--&#62;"       , $text );
	    	$text = preg_replace( "/<script/i"  , "&#60;script"   , $text );
	    	$text = str_replace( ">"            , "&gt;"          , $text );
	    	$text = str_replace( "<"            , "&lt;"          , $text );
	    	$text = str_replace( "\""           , "&quot;"        , $text );
	    	// $text = preg_replace( "/\n/"        , "<br />"        , $text ); // Convert literal newlines
	    	$text = preg_replace( "/\\\$/"      , "&#036;"        , $text );
	    	$text = preg_replace( "/\r/"        , ""              , $text ); // Remove literal carriage returns
	    	$text = str_replace( "!"            , "&#33;"         , $text );
	    	$text = str_replace( "'"            , "&#39;"         , $text ); // IMPORTANT: It helps to increase sql query safety.	    	
	    	$text = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $text ); 

	    	return trim($text);
	}

	public function ip_cleaner( $input )
	{
		$input = preg_replace("/([^0-9.])/", "", $input);
		
		return $input;
	}
	
	public function number_cleaner( $input )
	{
		$input = preg_replace("/([^0-9\.])/", "", $input);
		
		return $input;
	}
	
	public function md5_cleaner( $input )
	{
		$input = preg_replace("/([^a-zA-Z0-9])/", "", $input);
		
		return $input;
	}
	
	public function number_format( $number, $type = "," )
	{
		global $CMS;
	
		$number = number_format( round( $number ), 0, ' ', $type );
	
		return $number;
	}

	public function get_domain($url)
	{

		$parts = parse_url($url);

		return $parts['scheme'].'://'.$parts['host'];
	}
	
	//===========================================================================
	//  FILTER DOMAIN INTERNATIONAL INFO
	//===========================================================================
	
	public function domain($data)
	{
		global $CMS, $DB;
		
		$data = str_replace("–", "-", $data);
		$data = str_replace(array("\"", "&#092;&quot;"), array(""), $data);
		$data = $CMS->class->seo->remove_vietnamese($data);
		$data = trim($data);
		
		return $data;
	}
	
	//===========================================================================
	//  FILTER PHONE FOLLOW BY STANDARD OF INTERNATIONAL
	//===========================================================================
	
	public function phone($data)
	{
		global $CMS, $DB;
		
		// 084
		if ( substr($data,0,3) == "084" )
		{
			$data = substr($data,3,strlen($data));
		}
		// 084
		else if ( substr($data,0,1) == "0" )
		{
			$data = substr($data,1,strlen($data));	
		}
		// 0
		else if ( substr($data,0,2) == "84" )
		{
			$data = substr($data,2,strlen($data));	
		}
		
		// Dot (.)
		$data = str_replace(".", "", $data);
		
		// Slashh (-)
		$data = str_replace("-", "", $data);
		
		// Plus (+)
		$data = str_replace("+", "", $data);
		
		// Parenthese
		$data = str_replace(array("(", ")"), array(""), $data);

		// Whitespace
		$data = str_replace(" ", "", $data);
		
		return $data;
	}
	
}

?>
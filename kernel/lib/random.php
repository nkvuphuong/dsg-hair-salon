<?php
 
 
$CMS->class->random = new random;

class random {

	public function character( $count = 6 )
	{
		$random_char = "";
		$char_base = explode( " ", "a b c d e f g h i j k l m n o p q r s t u v w x y z A B C D E F G H I J K L M N O P Q R S T U V W X Y Z 0 1 2 3 4 5 6 7 8 9");

		for( $i = 0; $i < $count; $i++ )
		{
			$random_char = $random_char.$char_base[rand(9, count($char_base)-1)];
		}
		
		$length = strlen($random_char);
		
		if ( $length == $count ) return $random_char;
		else return $this->character($count);
	}
	
	 public function number()
	{
		mt_srand ((double) microtime() * 1000000);
		$random_number = mt_rand(100000,999999);
		
		return $random_number;
	}
	
	 public function md5( $data = "abc" )
	{
		global $CMS;
		
		$output = md5(md5($data).$this->number().$this->character(9).microtime().time());
		
		return $output;
	}
	
	 public function password( $count = 6, $nospec = 0 )
	{
		// Normal char
		$random_char = "";
		$char_base = explode( " ", "a b c d e f g h i j k l m n o p q r s t u v w x y z A B C D E F G H I J K L M N O P Q R S T U V W X Y Z");
		$char_count = $nospec == 0 ? floor($count/2) : floor($count*2/3);

		for( $i = 0; $i < $char_count; $i++ )
		{
			$rand = rand(0, count($char_base)-1);
			$random_char .= $char_base[$rand]." ";
		}
		
		// Number
		$random_char3 = "";
		$char_number = explode(" ", "0 1 2 3 4 5 6 7 8 9");
		$number_count = $nospec == 0 ? floor($char_count/2) : ($count-$char_count);
			
		for( $i = 0; $i < $number_count; $i++ )
		{
			$rand = rand(0, count($char_number)-1);
			$random_char3 .= $char_number[$rand]." ";
		}

		// Combine
		$random_char .= $random_char3;
		
		// Special char
		if ( $nospec == 0 )
		{
			$random_char2 = "";
			$char_spec = explode(" ", "! @ # $ % ^ *");//~ ! @ # $ ^ * _ - = | ( ) { } [ ] : ; , . ? & 
			$spec_count = $count-$char_count-$number_count;
			
			for( $i = 0; $i < $spec_count; $i++ )
			{
				$rand = rand(0, count($char_spec)-1);
				$random_char2 .= $char_spec[$rand]." ";
			}
		
			// Combine
			$random_char .= $random_char2;
		}
		
		// Convert to array and make it chaos
		$new_random = explode(" ", $random_char);
		$new_count = count($new_random);

		// Return new data
		$random_char = "";

		for ( $i = 0; $i < $new_count; $i++ )
		{
			$rand = array_rand($new_random);
			$random_char .= $new_random[$rand];	
			unset($new_random[$rand]);
		}
		
		$length = strlen($random_char);
		
		if ( $length == $count ) return $random_char;
		else return $this->character($count);
	}
	
	//===========================================================================
	//  GENERATE RANDOM DATA CARD
	//===========================================================================
	
	public function generate_card()
	{
		global $CMS;
		
		// Create random
		$num = array();
		
		for ( $i = 0; $i < 100; $i++ )
		{
			$num[$i] = substr("0{$i}", -2);
		}
		
		// Create string
		$astr = explode(" ", "a b c d e f g h i j k");
		$bstr = array_keys($astr);
		$anum = explode(" ", "1 2 3 4 5 6 7 8 9");
		$bnum = array_keys($anum);
		
		$str = array();
		
		for ( $i = 0; $i < count($astr); $i++ )
		{
			for ( $j = 0; $j < count($anum); $j++ )
			{
				// Get random
				$num_id = array_rand($num);
				
				// Set string
				$str[strtoupper($astr[$bstr[$i]].$anum[$bnum[$j]])] = $num[$num_id];
				
				// Remove that random
				unset($num[$num_id]);
			}
		}
		
		return serialize($str);
	}
	//20 01 17
	public function randomString($l=15,$c= '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
		$cl=strlen($c);
		$str='';
		for ($i=0;$i<$l;$i++) {
			$str.=$c[rand(0,$cl-1)];
		}
		return $str;
	}

	//20 01 17
	public function randomcharacter($l=15,$c= 'abcdefghijklmnopqrstuvwxyz') {
		$cl=strlen($c);
		$str='';
		for ($i=0;$i<$l;$i++) {
			$str.=$c[rand(0,$cl-1)];
		}
		return $str;
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

    public function checker_password($pwd = "")
    {
    	//You can use "\d" instead of "[a-z]" and "\W" instead of non-word characters, symbols. You can make a manual list of most used sybols like [#.-_,$%&!]. Numbers, letters, CAPS:
    	if (preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $pwd)){
		    return true;
		}else
		{
			return false;
		}
    }

    function checker_username($str) 
	{
	    return preg_match('/^[a-z][a-z0-9]*(?:[a-z0-9]+)*$/',$str);
	}

    
}

?>
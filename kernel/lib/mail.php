<?php

$CMS->class->mail = new class_mail;

class class_mail
{
	var $next_token = "";
	var $line_break = "\r\n";

    function sendmail($to, $to_name = "", $from, $from_name= "", $subject, $message, $cc = "", $bcc = "", $file = "")
    {
        global $CMS;


        if ( ! $to_name )
        {
            $to_name = explode("@", $to);
            $to_name = $to_name[0];
        }

        if ( ! $from_name )
        {
            $from_name = explode("@", $from);
            $from_name = $from_name[0];
        }

        if ( $CMS->class->input->is_html($message) == 0 )
        {
            $message = nl2br(stripslashes($message));
        }
        else
        {
            $message = stripslashes($message);
        }

        $mail = new PHPMailer;
        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        $mail->CharSet = "UTF-8";
        if ( $CMS->vars['is_smtp'] == 2 )
        {
            $SMTPSecure = "ssl";
            $port = 465;
        }
        else if ( $CMS->vars['is_smtp'] == 3 )
        {
            $SMTPSecure = "tls";
            $port = 587;
        }
        else
        {
            $SMTPSecure = "";
            $port = 25;
        }

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = $CMS->vars['smtp_server'];  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = $CMS->vars['smtp_user'];                 // SMTP username
        $mail->Password = $CMS->vars['smtp_password'];                           // SMTP password
        $mail->SMTPSecure = $SMTPSecure;                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = $port;                                    // TCP port to connect to

        $mail->setFrom($from, $from_name);

        $to = preg_split('/(;|,)/',$to);

        if(!empty($to))
        {
            foreach($to as $emailTo){
                $mail->addAddress($emailTo, $emailTo);     // Add a recipient
            }
        }

        //$mail->addReplyTo('info@example.com', 'Information');

        $cc = preg_split('/(;|,)/',$cc);
        if(!empty($cc))
        {
            foreach($cc as $emailCC)
            {
                $mail->addCC(trim($emailCC));
            }
        }

        $bcc = preg_split('/(;|,)/',$bcc);
        if(!empty($bcc))
        {
            foreach($bcc as $emailBCC)
            {
                $mail->addBCC(trim($emailBCC));
            }
        }

        if($file)
        {
            if($files = @json_decode($file))
            {
                foreach ($files as $file)
                {
                    if(is_file($file))
                    {
                        $mail->addAttachment($file);
                    }
                }
            }
            else if(is_file($file))
            {
                $mail->addAttachment($file);
            }
        }


//        $mail->addAttachment("{$CMS->vars['upload_dir']}/product/e48fe7f8d38d73a3d7e0c555c4e89f32.jpeg", 'Ngon VL');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message); //This is the body in plain text for non-HTML mail clients

        if(!$mail->send()) {
//            echo 'Message could not be sent.';
//            echo 'Mailer Error: ' . $mail->ErrorInfo;
            return false;
        } else {
//            echo 'Message has been sent';
            return true;
        }
    }

	function Tokenize($string,$separator="")
	{
		if(!strcmp($separator,""))
		{
			$separator=$string;
			$string=$this->next_token;
		}
		for($character=0;$character<strlen($separator);++$character)
		{
			if(GetType($position=strpos($string,$separator[$character]))=="integer")
				$found=(IsSet($found) ? min($found,$position) : $position);
		}
		if(IsSet($found))
		{
			$this->next_token=substr($string,$found+1);
			return(substr($string,0,$found));
		}
		else
		{
			$this->next_token="";
			return($string);
		}
	}

	function generate_messageid($sender)
	{
		$micros=$this->Tokenize(microtime()," ");
		$seconds=$this->Tokenize("");
		$local=$this->Tokenize($sender,"@");
		$host=$this->Tokenize(" @");
		if(strlen($host)
		&& $host[strlen($host)-1]=="-")
			$host=substr($host,0,strlen($host)-1);
		return("Message-ID: <".strftime("%Y%m%d%H%M%S", $seconds).substr($micros,1,5).".".preg_replace('/[^A-Za-z]/', '-', $local)."@".preg_replace('/[^.A-Za-z_-]/', '', $host).">");
	}

	/*
	 *	Vietnamese multi-byte data, these data will use for fix mb_strlen() and mb_substr() - lyhuuloi
	 */
	 
	function str_vn_data()
	{
		// A O I U E Y D
		$str_data = "";
		
		$str_data .= "ấ=a61 ầ=a62 ẩ=a63 ẫ=a64 ậ=a65 ắ=a81 ằ=a82 ẳ=a83 ẵ=a84 ặ=a85 á=a1 à=a2 ả=a3 ã=a4 ạ=a5 â=a6 ă=a8 ";
		$str_data .= "Ấ=A61 Ầ=A62 Ẩ=A63 Ẫ=A64 Ậ=A65 Ắ=A81 Ằ=A82 Ẳ=A83 Ẵ=A84 Ặ=A85 Á=A1 À=A2 Ả=A3 Ã=A4 Ạ=A5 Â=A6 Ă=A8 ";
		
		$str_data .= "ố=o61 ồ=o62 ổ=o63 ỗ=o64 ộ=o65 ớ=o71 ờ=o72 ở=o73 ỡ=o74 ợ=o75 ó=o1 ò=o2 ỏ=o3 õ=o4 ọ=o5 ô=o6 ơ=o7 ";
		$str_data .= "Ố=O61 Ồ=O62 Ổ=O63 Ỗ=O64 Ộ=O65 Ớ=O71 Ờ=O72 Ở=O73 Ỡ=O74 Ợ=O75 Ó=O1 Ò=O2 Ỏ=O3 Õ=O4 Ọ=O5 Ô=O6 Ơ=O7 ";
		
		$str_data .= "í=i1 ì=i2 ỉ=i3 ĩ=i4 ị=i5 ";
		$str_data .= "Í=I1 Ì=I2 Ỉ=I3 Ĩ=I4 Ị=I5 ";
		
		$str_data .= "ứ=u71 ừ=u72 ử=u73 ữ=u74 ự=u75 ú=u1 ù=u2 ủ=u3 ũ=u4 ụ=u5 ư=u7 ";
		$str_data .= "Ú=U71 Ừ=U72 Ử=U73 Ữ=U74 Ự=U75 Ú=U1 Ù=U2 Ủ=U3 Ũ=U4 Ụ=U5 Ư=U7 ";
		
		$str_data .= "ế=e61 ề=e62 ể=e63 ễ=e64 ệ=e65 é=e1 è=e2 ẻ=e3 ẽ=e4 ẹ=e5 ê=e6 ";
		$str_data .= "Ế=E61 Ề=E62 Ể=E63 Ễ=E64 Ệ=E65 É=E1 È=E2 Ẻ=E3 Ẽ=E4 Ẹ=E5 Ê=E6 ";
		
		$str_data .= "ý=y1 ỳ=y2 ỷ=y3 ỹ=y4 ẹ=ỵ5 ";
		$str_data .= "Ý=Y1 Ỳ=Y2 Ỷ=Y3 Ỹ=Y4 Ỵ=Y5 ";
		
		$str_data .= "đ=d9 ";
		$str_data .= "Đ=D9 ";
		
		return $str_data;
	}

	/*
	 *	Vietnamese strlen, this solution will fix mb_strlen() - lyhuuloi
	 */
	 
	function vn_strlen($string, $encoding = "UTF-8")
	{
		//return mb_strlen($string, $encoding);
	
		if ( strtoupper($encoding) == "UTF-8" )
		{
			$str_data = $this->str_vn_data();
			$array_data = explode(" ", trim($str_data));
			
			for ( $i = 0; $i < count($array_data); $i++ )
			{
				$convert = explode("=", $array_data[$i]);
				
				$string = str_replace($convert[0], $convert[1], $string);
			}
			
			return strlen($string);
		}
		else
		{
			return strlen($string);
		}
	}
	
	/*
	 *	Vietnamese substr, this solution will fix mb_substr() - lyhuuloi
	 */
	
	function vn_substr($string, $start, $length, $encoding = "UTF-8")
	{
		//return mb_substr($string, $start, $length, $encoding);
	
		if ( strtoupper($encoding) == "UTF-8" )
		{
			// Convert to base character
			$str_data = $this->str_vn_data();
			$array_data = explode(" ", trim($str_data));
			
			for ( $i = 0; $i < count($array_data); $i++ )
			{
				$convert = explode("=", $array_data[$i]);
				
				$string = str_replace($convert[0], $convert[1], $string);
			}
			
			$string = substr($string, $start, $length);
			
			// Return it to UTF-8
			for ( $i = 0; $i < count($array_data); $i++ )
			{
				$convert = explode("=", $array_data[$i]);
				
				$string = str_replace($convert[1], $convert[0], $string);
			}
			
			return $string;
		}
		else
		{
			return substr($string, $start, $length);
		}
	
		return $string;
	}
	
	/*
	 *	Encode mime header (Code snippet in PHP Manual - php.net)
	 */
	
	function encode_mimeheader2($input, $charset = "UTF-8")
	{
		preg_match_all('/(\\w*[\\x80-\\xFF]+\\w*)/', $input, $matches);
		foreach ($matches[1] as $value) {
			$replacement = preg_replace('/([\\x80-\\xFF])/e', '"=" . strtoupper(dechex(ord("\\1")))', $value);
			$input = str_replace($value, '=?' . $charset . '?Q?' . $replacement . '?=', $input);
		}
		return $input;
	}
	
	function encode_mimeheader($text, $header_charset='UTF-8', $break_lines=1, $email_header = 0)
	{
		$ln=strlen($text);
		$h=(strlen($header_charset)>0);
		if($h)
		{
			$encode = array(
				'='=>1,
				'?'=>1,
				'_'=>1,
				'('=>1,
				')'=>1,
				'<'=>1,
				'>'=>1,
				'@'=>1,
				','=>1,
				';'=>1,
				'"'=>1,
				'\\'=>1,
				'['=>1,
				']'=>1,
				':'=>1,
/*
				'/'=>1,
				'.'=>1,
*/
			);
			$s=($email_header ? $encode : array());
			$b=$space=$break_lines=0;
			for($i=0; $i<$ln; ++$i)
			{
				$c = $text[$i];
				if(IsSet($s[$c]))
				{
					$b=1;
					break;
				}
				switch($o=Ord($c))
				{
					case 9:
					case 32:
						$space=$i+1;
						$b=1;
						break 2;
					case 10:
					case 13:
						break 2;
					default:
						if($o<32
						|| $o>127)
						{
							$b=1;
							$s = $encode;
							break 2;
						}
				}
			}
			if($i==$ln)
				return($text);
			if($space>0)
				return(substr($text,0,$space).($space<$ln ? $this->encode_mimeheader(substr($text,$space), $header_charset, $break_lines, $email_header) : ""));
		}
		for($w=$e='',$n=0, $l=0,$i=0;$i<$ln; ++$i)
		{
			$c = $text[$i];
			$o=Ord($c);
			$en=0;
			switch($o)
			{
				case 9:
				case 32:
					if(!$h)
					{
						$w=$c;
						$c='';
					}
					else
					{
						if($b)
						{
							if($o==32)
								$c='_';
							else
								$en=1;
						}
					}
					break;
				case 10:
				case 13:
					if(strlen($w))
					{
						if($break_lines
						&& $l+3>75)
						{
							$e.='='.$this->line_break;
							$l=0;
						}
						$e.=sprintf('=%02X',Ord($w));
						$l+=3;
						$w='';
					}
					$e.=$c;
					if($h)
						$e.="\t";
					$l=0;
					continue 2;
				case 46:
				case 70:
				case 102:
					$en=(!$h && ($l==0 || $l+1>75));
					break;
				default:
					if($o>127
					|| $o<32
					|| !strcmp($c,'='))
						$en=1;
					elseif($h
					&& IsSet($s[$c]))
						$en=1;
					break;
			}
			if(strlen($w))
			{
				if($break_lines
				&& $l+1>75)
				{
					$e.='='.$this->line_break;
					$l=0;
				}
				$e.=$w;
				++$l;
				$w='';
			}
			if(strlen($c))
			{
				if($en)
				{
					$c=sprintf('=%02X',$o);
					$el=3;
					$n=1;
					$b=1;
				}
				else
					$el=1;
				if($break_lines
				&& $l+$el>75)
				{
					$e.='='.$this->line_break;
					$l=0;
				}
				$e.=$c;
				$l+=$el;
			}
		}
		if(strlen($w))
		{
			if($break_lines
			&& $l+3>75)
				$e.='='.$this->line_break;
			$e.=sprintf('=%02X',Ord($w));
		}
		if($h
		&& $n)
			return('=?'.$header_charset.'?q?'.$e.'?=');
		else
			return($e);
	}

	
	function encode_mimeheader3($string, $charset="UTF-8", $linefeed="\r\n")
	{
		if ( ! $charset )
		{
			$charset = mb_internal_encoding();
		}
	
		$start = "=?$charset?B?";
		$end = "?=";
		$encoded = '';
	
		/* Each line must have length <= 75, including $start and $end */
		$length = 75 - strlen($start) - strlen($end);
		
		/* Average multi-byte ratio */
		$ratio = $this->vn_strlen($string, $charset) / strlen($string);
		
		/* Base64 has a 4:3 ratio */
		$magic = $avglength = floor(3 * $length * $ratio / 4);
	
		for ($i=0; $i <= $this->vn_strlen($string, $charset); $i+=$magic) {
		
			$magic = $avglength;
			$offset = 0;
			/* Recalculate magic for each line to be 100% sure */
			do {
				$magic -= $offset;
				$chunk = $this->vn_substr($string, $i, $magic, $charset);
				$chunk = base64_encode($chunk);
				$offset++;
			} while (strlen($chunk) > $length);
			if ($chunk)
				$encoded .= ' '.$start.$chunk.$end.$linefeed;
		}
		/* Chomp the first space and the last linefeed */
		
		$encoded = substr($encoded, 1, -strlen($linefeed));

		return $encoded;
	}

	function clear_harmful_code( $input )
	{
		$output = $input;
		
		$output = str_replace('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">', "", $output);
		
		// XSS CSS
		$output = str_ireplace( "z-index" , "z -index", $output );
		$output = str_ireplace( "position" , "pos ition", $output );
		$output = str_ireplace( "display" , "dis play", $output );
		
		// XSS DOM
		$output = str_ireplace( "document.cookie" , "document. cookie", $output );
		$output = str_ireplace( "javascript" , "java script", $output );
		$output = str_replace( "alert"      , "a lert"          , $output );
		$output = str_replace( "about:"     , "a bout:"         , $output );
		$output = str_replace( "onmouseover", "on mouseover"    , $output );
		$output = str_replace( "onclick"    , "on click"        , $output );
		$output = str_replace( "onload"     , "on load"         , $output );
		$output = str_replace( "onsubmit"   , "on submit"       , $output );
								
		$output = preg_replace( "#vb(.+?)?script\:#is", "vb script:"  , $output );
		$output = str_replace(  "`"                   , "&#96;"       , $output );
		$output = preg_replace( "#moz\-binding:#is"   , "moz binding:", $output );
		$output = str_replace(  "<script"			   , "&lt;script"  , $output );
								
		$output = preg_replace( "/\r/"        , ""              , $output ); // Remove literal carriage returns
		$output = str_replace("\\", "&#92;", $output);
		$output = str_replace( "'"            , "&#39;"         , $output ); // IMPORTANT: It helps to increase sql query safety.
		
		return $output;
	}

	/*
	 *	Clean email subject
	 */
	
	function clean_subject( $mail_subject, $mail_encoding = "utf-8" )
	{
		global $CMS;
		
		$output = $mail_subject;
	
		// Convert if not utf8
		if ( $mail_encoding == "iso-8859-1" )
		{
			$output = utf8_encode( $output );
		}
		
		// Remove double white space		
		$output = trim($output);
		
		// Remove harmful code
		$output = $this->clear_harmful_code($output);
		
		return $output;
	}
	
	/*
	 *	Clean email content
	 */

	function clean_content( $mail_content, $mail_from = "", $mail_type = "html", $mail_encoding = "utf-8" )
	{
		global $CMS;
		
		// Trim
		$output = trim($mail_content);

		// Check for html type
		$mail_type = strlen(strip_tags($output)) == strlen($output) ? "text" : "html";
		
		// Convert <br> tag
		$output = $mail_type == "text" ? nl2br($output) : $output;

		// Block quote, fieldset
		/*$data = str_replace("\n", "-newline-", $data);
		$data = preg_replace('/(\<([fieldset|FIELDSET]*)\s*\/?>(.*)\<\s*\/?([fieldset|FIELDSET]*)>)/', '', $data);
		$data = str_replace("-newline-", "\n", $data);
	
		// Clear invalid html
		$data = preg_replace('/(\<([title|TITLE]*)\s*\/?>(.*)\<\s*\/?([title|TITLE]*)>)/', '', $data);
		*/
		
		// Unicode
		if ( $mail_encoding == "iso-8859-1" )
		{
			$output = utf8_encode( $output );
		}

		// Remove all harmful code					
		$output = $this->clear_harmful_code( $output );
		
		// Fix html tags
		$output = $this->table_check( $output );
		
		// All about <br> (Take me a lot of time to complete, lyhuuloi)

        $string = isset($string) ? $string : '';
		$string = preg_replace('/^\s*(?:<([br|BR]*)\s*\/?>\s*)*/i', '', $string);
		$string = preg_replace('/(\<([br|BR]*)\s*\/?>([\r\n ]*)){2,}/', '<br /><br />', $string);
		$string = preg_replace('/(\<([br|BR]*)\s*\/?>)/', '<br />', $string);

		return $output;
	}
	
	function get_ip( $ip_address )
	{
		$ip_address = preg_replace("#(.+?)([0-9]+?)\.([0-9]+?)\.([0-9]+?)\.([0-9]+?) (.+?)#i", "\\2.\\3.\\4.\\5|\\6", $ip_address);
		$ip_address = explode("|", $ip_address);
		$ip_address = $ip_address[0];
								
		if ( strlen( $ip_address ) > 20 )
		{
			$ip_address = preg_replace("#(.+?)\[([0-9]+?)\.([0-9]+?)\.([0-9]+?)\.([0-9]+?)\](.+?)#i", "\\2.\\3.\\4.\\5|\\6", $ip_address);
			$ip_address = explode("|", $ip_address);
			$ip_address = $ip_address[0];
		}
		
		return $ip_address;
	}
	
	function get_date( $mail_date )
	{
		$mail_time = explode(" ", $mail_date);
		// Replace month
		$mail_time[2] = str_replace("jan", "01", strtolower($mail_time[2]));
		$mail_time[2] = str_replace("feb", "02", strtolower($mail_time[2]));
		$mail_time[2] = str_replace("mar", "03", strtolower($mail_time[2]));
		$mail_time[2] = str_replace("apr", "04", strtolower($mail_time[2]));
		$mail_time[2] = str_replace("may", "05", strtolower($mail_time[2]));
		$mail_time[2] = str_replace("jun", "06", strtolower($mail_time[2]));
		$mail_time[2] = str_replace("jul", "07", strtolower($mail_time[2]));
		$mail_time[2] = str_replace("aug", "08", strtolower($mail_time[2]));
		$mail_time[2] = str_replace("sep", "09", strtolower($mail_time[2]));
		$mail_time[2] = str_replace("oct", "10", strtolower($mail_time[2]));
		$mail_time[2] = str_replace("nov", "11", strtolower($mail_time[2]));
		$mail_time[2] = str_replace("dec", "12", strtolower($mail_time[2]));
		// End replace month
		$mail_hour = explode(":", $mail_time[4]);
		$mail_date = @mktime($mail_hour[0], $mail_hour[1], $mail_hour[2], $mail_time[2], $mail_time[1], $mail_time[3]);

		$mail_time[5] = str_replace(array("+0", "-0"), array("+", "-"), substr($mail_time[5],0,3));
				
		if ( $mail_time[6] == "(PDT)" )
		{
			$mail_date_add = ($mail_time[5]+28)*3600;
		}
		else
		{
			$mail_date_add = $mail_time[5]*3600;
		}
							
		$mail_date = $mail_date + $mail_date_add;
		
		return $mail_date;
	}
	
	function get_lineaddress($input)
	{
       	$to_line = trim(ereg_replace(',,+', ',', $input));

		return $to_line;
	}

    public function table_check( $input )
    {
        $backup_output = $input;

        $output = strtolower($input);

        $re_data1 = round(( strlen($output) - strlen(str_replace("<div", "", $output)) ) / 4);
        $re_data2 = round(( strlen($output) - strlen(str_replace("</div>", "", $output)) ) / 6);

        if ( $re_data1 > $re_data2  )
        {
            $cnt = $re_data1 - $re_data2;

            for ( $i = 0; $i < $cnt; $i++ )
            {
                $backup_output .= "</div>";
            }
        }

        $re_data1 = round(( strlen($output) - strlen(str_replace("<table", "", $output)) ) / 6);
        $re_data2 = round(( strlen($output) - strlen(str_replace("</table>", "", $output)) ) / 8);

        if ( $re_data1 > $re_data2  )
        {
            $cnt = $re_data1 - $re_data2;

            for ( $i = 0; $i < $cnt; $i++ )
            {
                $backup_output .= "</table>";
            }
        }

        $output = $backup_output;

        return $output;
    }
}

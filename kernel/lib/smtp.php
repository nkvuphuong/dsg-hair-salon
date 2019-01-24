<?php

$CMS->class->smtp = new class_smtp;

class class_smtp {

	public $smtp_user = "";
	public $smtp_password = "";

	public function send($from, $namefrom, $to, $nameto, $subject, $message, $headers, $cc = "", $bcc = "")
	{
		global $CMS;
		
		if ( $CMS->vars['is_smtp'] == 2 )
		{
			$smtpServer = "ssl://" . $CMS->vars['smtp_server'];
			$port = 465;
		}
		else if ( $CMS->vars['is_smtp'] == 3 )
		{
			$smtpServer = "tls://" . $CMS->vars['smtp_server'];
			$port = 587;
		}
		else
		{
			$smtpServer = $CMS->vars['smtp_server'];
			$port = 25;	
			//$port = (int)$CMS->vars['smtp_port'];	
		}
		

		$timeout = "20";
		$username = $this->smtp_user ? $this->smtp_user : $CMS->vars['smtp_user'];
		$password = $this->smtp_password ? $this->smtp_password : $CMS->vars['smtp_password'];
		$localhost = $CMS->vars['smtp_server'];

		$newLine = "\r\n";

		$smtpConnect = @fsockopen($smtpServer, $port, $errno, $errstr, $timeout);
				
		
		if ( ! $smtpConnect ) { return false; }
		
		$smtpResponse = @fgets($smtpConnect, 4096);
		 
		if ( ! $smtpResponse ) { return false; }
		
		if(empty($smtpConnect))
		{
		   $output = "Failed to connect: $smtpResponse";
		   return $output;
		}
		else
		{
		   $logArray['connection'] = "Connected to: $smtpResponse";
		}

	    fputs($smtpConnect, "HELO $localhost". $newLine);
	    $smtpResponse = fgets($smtpConnect, 4096);
	    $logArray['heloresponse2'] = "$smtpResponse";
	  
		fputs($smtpConnect,"AUTH LOGIN" . $newLine);
		$smtpResponse = fgets($smtpConnect, 4096);
		$logArray['authrequest'] = "$smtpResponse";
		
		fputs($smtpConnect, base64_encode($username) . $newLine);
		$smtpResponse = fgets($smtpConnect, 4096);
		$logArray['authusername'] = "$smtpResponse";
		
		fputs($smtpConnect, base64_encode($password) . $newLine);
		$smtpResponse = fgets($smtpConnect, 4096);
		$logArray['authpassword'] = "$smtpResponse";
		
		fputs($smtpConnect, "MAIL FROM: <$from>" . $newLine);
		$smtpResponse = fgets($smtpConnect, 4096);
		$logArray['mailfromresponse'] = "$smtpResponse";
		
		fputs($smtpConnect, "RCPT TO: <$to>" . $newLine);
		$smtpResponse = fgets($smtpConnect, 4096);
		$logArray['mailtoresponse'] = "$smtpResponse";

		// CC
		if ( $cc )
		{
			$cc = explode(";", str_replace(",", ";", $cc));
			for ( $i = 0; $i < count($cc); $i++ )
			{
				$cc[$i] = trim($cc[$i]);
				if ( $cc[$i] )
				{
					fputs($smtpConnect, "RCPT TO: <$cc[$i]>" . $newLine);
					$smtpResponse = fgets($smtpConnect, 4096);
					$logArray['mailtoresponse'] = "$smtpResponse";
				}
			}
		}
		
		// BCC
		if ( $bcc )
		{
			$bcc = explode(";", str_replace(",", ";", $bcc));
			for ( $i = 0; $i < count($bcc); $i++ )
			{
				$bcc[$i] = trim($bcc[$i]);
				if ( $bcc[$i] )
				{
					fputs($smtpConnect, "RCPT TO: <$bcc[$i]>" . $newLine);
					$smtpResponse = fgets($smtpConnect, 4096);
					$logArray['mailtoresponse'] = "$smtpResponse";
				}
			}
		}
		
		fputs($smtpConnect, "DATA" . $newLine);
		$smtpResponse = fgets($smtpConnect, 4096);
		$logArray['data1response'] = "$smtpResponse";

		fputs($smtpConnect, $headers);
		$smtpResponse = @fgets($smtpConnect, 4096);
		$logArray['data2response'] = "$smtpResponse";
		
		@fputs($smtpConnect,"QUIT" . $newLine);
		$smtpResponse = fgets($smtpConnect, 4096);
		$logArray['quitresponse'] = "$smtpResponse";
		$logArray['quitcode'] = substr($smtpResponse,0,3);
		fclose($smtpConnect);

		return($logArray);
	}

}

?>
<?php

$CMS->class->network = new class_network;

class class_network {

	public $display_header = 0;
	public $user_agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)";
	public $header_info = array();

	public function open_https($url, $refer = "", $usecookie = false, $postdata = "")
	{
		$usecookie = false;
		
	   	if ($usecookie) {
		   	if (file_exists($usecookie)) {
			   	if (!is_writable($usecookie)) {
				   	return "Can't write to $usecookie cookie file, change file permission to 777 or remove read only for windows.";
			   	}
		   	} else {
			   	$usecookie = "cookie.txt";
			   	if (!is_writable($usecookie)) {
				   	return "Can't write to $usecookie cookie file, change file permission to 777 or remove read only for windows.";
			   	}
		   	}
	   	}

	   	$ch = curl_init();
	
	   	curl_setopt($ch, CURLOPT_URL, $url);
	
	   	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	
	   	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	
	   	curl_setopt($ch, CURLOPT_HEADER, $this->display_header);
	
	   	curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
	
	   	if ($usecookie) {
		   	curl_setopt($ch, CURLOPT_COOKIEJAR, $usecookie);
	
		   	curl_setopt($ch, CURLOPT_COOKIEFILE, $usecookie);
	   	}
	
	   	if ($refer != "") {
		   	curl_setopt($ch, CURLOPT_REFERER, $refer );
	   	}
	
	   	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		
		if ( $postdata )
		{
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
		}
		
		if ( $this->header_info )
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header_info); 
		}
	
	   	$result = curl_exec ($ch);
	
	   	curl_close ($ch);
	
		return $result;
	}
	
	public function open_http( $url, $referer = "" )
	{
		if ( $referer )
		{
			$host = preg_replace("#(.+?):\/\/(.+?)\/(.+?)#is", "\\2|", $url);
			$host = explode("|", $host);
			$host = $host[0];
		
			$hdrs = array( 'http' => array(
			
				'method' => "GET",
	
					"header" => "accept-language: en-us,en;q=0.5\r\n" . 
			
					"Host: $host\r\n" .
					
					"User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9) Gecko/2008051206 Firefox/3.0\r\n" .
			
					"Referer: $referer\r\n" .
			
					"Content-Type: application/x-www-form-urlencoded\r\n" .
			
					"Content-Length: 33\r\n\r\n" .
				
					"username=mustap&comment=NOCOMMENT\r\n"
			
				)
		
			);
			
			$context = stream_context_create($hdrs);

			$fp = fopen($url, "rb", false, $context);
		}
		else
		{	
			// Send request
			$fp = fopen($url, "r");
		}

		// Load Data
		if ( $fp )
		{
			$data = "";
			while ( ! feof($fp) )
			{
				$data .= fread($fp, 8192);
			}
		}
		else
		{
			print "Request failed";
		}
		
		// Close it
		@fclose($fp);
		
		return $data;
	}

	public function grab($source_to_grab, $delimiter_start, $delimiter_stop, $search='', $replace='')
	{
		$fd = ""; 
		$start_pos = 0;
		$end_pos = 0;

	   	while(true) 
	   	{
			if($end_pos > $start_pos) 
		      	{
				$result = substr($fd, $start_pos, $end_pos-$start_pos);
		         	$result .= $delimiter_stop;
				break;
		      	}
	
			$data = $source_to_grab;
	
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

		if ( $search && $replace )
		{
			return preg_replace($search, $replace, $result);
		}
		else
		{
			return $result;
		}
	}

	public function get( $url = "", $referer = "", $delimiter_start = "", $delimiter_stop = "", $search = "", $replace = "" )
	{
		// If exist URL
		if ( ! $url )
		{
			return false;
		}
		
		if ( substr($url, 0, strlen("https")) == "https" )
		{
			$data = $this->open_https( $url, $referer );
		}
		else if ( substr($url, 0, strlen("http")) == "http" )
		{
			$data = $this->open_http( $url, $referer );
		}
		
		// If exist Data loaded
		if ( ! $data )
		{
			return false;
		}
		
		// Grab it
		if ( $delimiter_start AND $delimiter_stop )
		{
			$this->grab( $data, $delimiter_start, $delimiter_stop, $search, $replace );
		}
		// Return Data

		return $data;
	}
	
	public function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	
	public function ping( $target, $timeout = 5, $port = 80 )
	{
		global $CMS;
		//Tạm thời đóng vì nó gay lỗi
		// $start_time = $this->microtime_float();

		// $file = @fsockopen($target, $port, &$errno, &$errstr, $timeout );
		// @fclose($file);

		// $end_time = $this->microtime_float();

		// if ( $file )
		// {
		// 	$time = substr($end_time - $start_time, 0, 8);
		// 	$target = str_replace(array("http://", "www."), array("",""), $target);
			
		// 	$data = "<b><font color='blue'>PING {$target}, Time={$time} ms</font></b>";
		// 	$result = true;
		// }
		// else
		// {
		// 	$time = substr($end_time - $start_time, 0, 8);
		// 	$data = "<b><font color='red'>Request timed out</font></b>";
		// 	$result = false;
		// }

		return array($result, $data, $target, $time);
	}
	
	/*
	**	Post request by Jonas John
	*/
	 
	public function post($url, $referer, $_data)
	{
		// convert variables array to string:
		$data = array();    
		while(list($n,$v) = each($_data)){
			$data[] = "$n=$v";
		}    
		$data = implode('&', $data);
		// format --> test1=a&test2=b etc.
	 
		// parse the given URL
		$url = parse_url($url);
		if ($url['scheme'] != 'http') { 
			die('Only HTTP request are supported !');
		}
	 
		// extract host and path:
		$host = $url['host'];
		$path = $url['path'];
	 
		// open a socket connection on port 80
		$fp = fsockopen($host, 80);
	 
		// send the request headers:
		fputs($fp, "POST $path HTTP/1.1\r\n");
		fputs($fp, "Host: $host\r\n");
		fputs($fp, "Referer: $referer\r\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-length: ". strlen($data) ."\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $data);
	 
		$result = ''; 
		while(!feof($fp)) {
			// receive the results of the request
			$result .= fgets($fp, 128);
		}
	 
		// close the socket connection:
		fclose($fp);
	 
		// split the result header from the content
		$result = explode("\r\n\r\n", $result, 2);
	 
		$header = isset($result[0]) ? $result[0] : '';
		$content = isset($result[1]) ? $result[1] : '';
	 
		// return as array:
		return array($header, $content);
	}
}

?>
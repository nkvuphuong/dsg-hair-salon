<?php

$CMS->class->domain = new class_libdomain;

class class_libdomain {

	public function is_available( $domain = "" )
	{
		global $CMS;
		
		if ( ! $domain )
		{
			return false;
		}
		
		if ( strtolower(substr($domain, -2)) == "vn" )
		{
			if ( preg_match("/(khong ton tai)/", $CMS->class->network->get("http://www.vnnic.vn/jsp/jsp/tracuudomainchitiet.jsp?type={$domain}", "http://www.vnnic.vn/jsp/jsp/tracuudomain1.jsp")) == false )
			{
				return false;
			}
		}
		else if ( strtolower(substr($domain, -3)) == "com" OR strtolower(substr($domain, -3)) == "net"  )
		{
			if ( preg_match("/(still available)/", $CMS->class->network->get("http://www.checkdomain.com/cgi-bin/checkdomain.pl?domain={$domain}")) == false )
			{
				return false;
			}
		}
		else
		{
			if ( preg_match("/(Available Domains)/", $CMS->class->network->get("http://www.who.is/whois-biz/ip-address/{$domain}/", "http://www.who.is")) == false )
			{
				return false;
			}
		}
		
		return true;
	}
	
	public function record_is_valid( $input )
	{
		global $CMS;

		// Remove last Dot
		if ( substr($input, -1) == "." )
		{
			$input = substr($input, 0, strlen($input)-1);
		}

		if ( $this->is_domain( $input ) == false AND $this->is_ip( $input ) == false )
		{
			return false;
		}

		return true;
	}
	
	public function record_value($name)
	{
		global $CMS;
		
		$data = stripslashes(trim(str_replace("'", "&#39;", $_POST["{$name}"]))); 
		
		return $data;	
	}
	
	public function record_value_is_valid( $input, $type = "" )
	{
		global $CMS;

		// Remove last Dot
		if ( substr($input, -1) == "." )
		{
			$input = substr($input, 0, strlen($input)-1);
		}

		$type = strtoupper($type);

		if ( $type == "A" )
		{
			if ( $this->is_ip( $input ) == false )
			{
				return false;
			}
		}
		else if ( $type == "CNAME" )
		{
			if ( $this->is_domain( $input ) == false )
			{
				return false;
			}
				
			if ( $this->is_ip( $input ) == true )
			{
				return false;
			}
		}
		else if ( $type == "MX" )
		{
			if ( $this->is_domain( $input ) == false )
			{
				return false;
			}
		}
		else if ( preg_match("/url/", strtolower($type)) == true )
		{
			if ( ! preg_match("/^http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?$/i", $input) )
			{
				return false;
			}
		}
		else if ( $type == "TXT" )
		{
			if ( substr($input,0,1) != "\"" OR substr($input,-1) != "\"" )
			{
				return false;	
			}
		}
		else
		{
			if ( $this->is_domain( $input ) == false AND $this->is_ip( $input ) == false )
			{
				return false;
			}
		}
		
		return true;
	}
	
	public function record_check( $input )
	{
		global $CMS;
		
		if ( preg_match("/\//", $input) == true )
		{
			return false;
		}
		
		// Sub domain
		if ( count(explode(".", $input)) == 1 )
		{
			return false;
		}
		
		// Have a dot
		if ( substr($input, -1) == "." )
		{
			return false;
		}
		
		// Is it IP ?
		if ( $this->is_ip( $input ) == true )
		{
			return false;
		}
		
		return true;
	}
	
	public function is_domain( $domain )
	{
		global $CMS;
		
		$domain_array = explode(".", $domain);

		if ( count($domain_array) < 2 )
		{
			//return false;
		}
		
		for ( $i = 0; $i < count($domain_array); $i++ )
		{
			if ( ! ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i]) )
			{
				if ( $domain_array[$i] == "*" AND $i == 0 )
				{
					
				}
				else
				{
					return false;
				}
			}
		}

		return true;
	}
	
	public function is_ip ( $ip )
	{
		global $CMS;
		
		/*$ip_array = explode(".", $ip);

		if ( count($ip_array) < 4 )
		{
			return false;
		}
		
		for ( $i = 0; $i < count($ip_array); $i++ )
		{
			if ( preg_match("/^[0-9]/", $ip_array[$i]) )
			{
				return false;
			}
		}
		*/
		
		$pattern = "/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/i";
        
		if (preg_match($pattern, $ip))
        {
			return true;
        }
		else
		{
            return false;
        }
	}
	
	public function domain_filter( $domain )
	{
		// Remove invalid character
		$domain = str_replace("http://www.", "", $domain);
		$domain = str_replace("https://www.", "", $domain);
		$domain = str_replace("http://", "", $domain);
		$domain = str_replace("https://", "", $domain);
		$domain = str_replace("/", "", $domain);
		return $domain;
	}
	
	public function gethostname( $input )
	{
		global $CMS;
		
		$input = $this->domain_filter( $input );
		
		$input = explode("/", $input);

		$data = explode(".", $input[0]);
		
		if ( strtolower($data[count($data)-1]) != "vn" )
		{
			$data = strtolower($data[count($data)-2].".".$data[count($data)-1]);
		}
		else
		{
			$data = strtolower($data[count($data)-3].".".$data[count($data)-2].".".$data[count($data)-1]);
		}
		
		return $data;
	}
	
	public function domain_inter( $domain )
	{
		global $CMS;
		
		$url = "http://www.who.is/whois-com/ip-address/{$domain}/";
		
		$delimiter_start = "<td width='100%' style='color:#333333;' class='wrap'>";
		$delimiter_stop = "</td>";
		$search[0] = '#\'\/#is';
		$replace[0] = "'http://www.who.is/";

		$data = $CMS->class->input->grab( $url, $delimiter_start, $delimiter_stop, $search, $replace );
		
		$data = strip_tags($data, "<br><img>");
		
		return $data;
	}
	
	public function domain_vn( $domain )
	{
		global $CMS;
		
		$data = $CMS->class->network->get("http://www.vnnic.vn/jsp/jsp/tracuudomainchitiet.jsp?type={$domain}", "http://www.vnnic.vn/jsp/jsp/tracuudomain1.jsp");
		
		$data = strip_tags($data);
		
		$data = preg_replace("#(.+?)Registration  date(.+?)Registration  date(.+?)Expiration   date(.+?)Organization(.+?)Trade name(.+?)Current Registrar(.+?)DNS Server(.+?)&nbsp;&nbsp;&nbsp;#is", "\\2|\\3|\\4|\\5|\\6|\\7|\\8|", $data);
		
		$data = explode("|", $data);

		$info = array();
		
		for ( $i = 0; $i < count($data)-1; $i++ )
		{
			$info[$i] = trim($data[$i]);
			$info[$i] = str_replace("&nbsp;", "", $info[$i]);
			$info[$i] = substr($info[$i], 1, strlen($info[$i]));
		}

		$key = array("Ngày đăng ký:", "Ngày kích hoạt:", "Ngày hết hạn:", "Tên đơn vị chủ quản:", "Tên giao dịch:", "Đăng ký tại:", "<br />Thông tin DNS:<br />");
		
		$data = "";

		for ( $i = 0; $i < count($info); $i++ )
		{
			$data .= trim($key[$i].$info[$i])."<br />";
		}

		return $data;
	}
	
	public function getdate_vn( $domain )
	{	
		global $CMS;
	
		$data = $this->domain_vn( $domain );
		$data = explode("<br />", $data);
		
		$start_date = $data[1];
		$start_date = explode(":", $start_date);
		$start_date = trim($start_date[1]);
		$start_date = explode("-", $start_date);
		$start_date = $CMS->class->date->gmt( $start_date[0], $start_date[1], $start_date[2] );
		
		$end_date = $data[2];
		$end_date = explode(":", $end_date);
		$end_date = trim($end_date[1]);
		$end_date = explode("-", $end_date);
		$end_date = $CMS->class->date->gmt( $end_date[0], $end_date[1], $end_date[2] );
		
		$date = array($start_date, $end_date);
		
		return $date;
	}
	
	public function getdate_inter( $domain )
	{
		global $CMS;
		
		// $data = $this->domain_inter( $domain );
		// $date = preg_replace("#(.+?)Creation Date:(.+?)Expiration Date:(.+?)Domain(.+?)#is", "\\2|\\3|", $data);
		
		$data = $CMS->class->network->get("http://www.who.is/whois-com/ip-address/{$domain}/");
		$date = preg_replace("#(.+?)Expiration Date:(.+?)Creation Date:(.+?)Last(.+?)#is", "\\2|\\3|", $data);

		$date = explode("|", $date);
		
		// Remove hourse, minutes and seconds
		$start_date = explode(" ", $date[1]);
		$start_date = $start_date[1];
		$end_date = explode(" ", $date[0]);
		$end_date = $end_date[1];
		
		// Get date
		$start_date = explode("-", str_replace(array(" ", "\n", "&nbsp;"), array("", "", ""), $start_date));
		$end_date = explode("-", str_replace(array(" ", "\n", "&nbsp;"), array("", "", ""), $end_date));
				
		/*$date_array = array();
		$date_array["Jan"] = "01";
		$date_array["Feb"] = "02";
		$date_array["Mar"] = "03";
		$date_array["Apr"] = "04";
		$date_array["May"] = "05";
		$date_array["Jun"] = "06";
		$date_array["Jul"] = "07";
		$date_array["Aug"] = "08";
		$date_array["Sep"] = "09";
		$date_array["Oct"] = "10";
		$date_array["Nov"] = "11";
		$date_array["Dec"] = "12";
	
		$start_date = $CMS->class->date->gmt($start_date[0], $date_array[$start_date[1]], $start_date[2]);
		$end_date = $CMS->class->date->gmt($end_date[0], $date_array[$end_date[1]], $end_date[2]);
		*/
		
		$start_date = $CMS->class->date->gmt($start_date[2], $start_date[1], $start_date[0]);
		$end_date = $CMS->class->date->gmt($end_date[2], $end_date[1], $end_date[0]);
		
		$date = array($start_date, $end_date);
		
		return $date;
	}
	
	public function whois( $domain )
	{
		global $CMS;

		$domain = $this->domain_filter($domain);

		if ( substr(strtolower($domain), -2) == "vn" )
		{
			$data = $this->domain_vn( $domain );
		}
		else
		{
			$data = $this->domain_inter( $domain );
		}
		
		return $data;
	}

	public function getdate( $domain )
	{
		global $CMS;

	
		$domain = $this->domain_filter($domain);
		
		if ( substr(strtolower($domain), -2) == "vn" )
		{
			$data = $this->getdate_vn( $domain );
		}
		else
		{
			$data = $this->getdate_inter( $domain );
		}
		
		return $data;
	}
	
	//===========================================================================
	//  GET DOMAIN EXTENSION
	//===========================================================================
	
	public function get_ext( $domain_name )
	{
		global $CMS;
		
		$domainpath = explode(".", $domain_name);
		$domainname = $domainpath[0];
		$domainext = "";
		for ( $i = 1; $i < count($domainpath); $i++ )
		{
			if ( $i == 1 ) { $domainext .= $domainpath[$i]; }
			else { $domainext .= ".".$domainpath[$i]; }
		}
				
		return array($domainname, $domainext);
	}
	
	//===========================================================================
	//  CHECK FOR VALID SUB DOMAIN
	//===========================================================================
	
	public function is_subdomain( $subdomain )
	{
		global $CMS;

		if ( ! ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $subdomain) )
		{
			return false;
		}
		
		return true;
	}


	public function filter_var_domain($domain)
	{
	    if(stripos($domain, 'http://') === 0)
	    {
	        $domain = substr($domain, 7); 
	    }
	     
	    ///Not even a single . this will eliminate things like abcd, since http://abcd is reported valid
	    if(!substr_count($domain, '.'))
	    {
	        return false;
	    }
	     
	    if(stripos($domain, 'www.') === 0)
	    {
	        $domain = substr($domain, 4); 
	    }
	     
	    $again = 'http://' . $domain;
	    return filter_var ($again, FILTER_VALIDATE_URL);
	}

}

?>
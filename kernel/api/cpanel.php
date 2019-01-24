<?php 

//***************************************//
// Project name: Nhan hoa CRM 
// Package name: Class Cpanel API
// Author: Ly Huu Loi
//***************************************//

$CMS->api->cpanel = new Cpanel;

class Cpanel
{
	public $server_info = array();
	
	public $is_ssl = 0;
	
	public $pass_is_hash = false;
	
	public $data_result = ""; // Use for debug
		
	public $url_request = ""; // Use for debug
	
	public $timeout = 0;
		
/**************************** GLOBAL COMMAND *********************************/


	public function execute($page = "")
	{
		global $CMS;

		$page = $page ? str_replace(" ", "%20", trim($page)) : "";
		
		$this->server_info['ip'] = trim($this->server_info['ip']);

		$HOST = ($this->is_ssl == 1)? 'https://' . $this->server_info['ip'] . ':2087' : 'http://' . $this->server_info['ip'] . ':2086';

		$this->server_info['pass'] = ( $this->pass_is_hash? $this->server_info['pass'] : $this->server_info['hash']);
		
		$curl = curl_init();
		
		$url = $HOST . '/json-api/' . $page;
		
		// Debug
		$this->url_request = $url;
 
		$header[0] = (( $this->server_info['hash'] == '' )? 'Authorization: Basic ' . base64_encode( $this->server_info['user'] . ':' . $this->server_info['pass'] ) : $header[0] = 'Authorization: WHM ' . $this->server_info['user'] . ":" . $this->server_info['hash']);
 
		curl_setopt( $curl , CURLOPT_SSL_VERIFYHOST , 0 );
		curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false );
		curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true ); 
		curl_setopt( $curl , CURLOPT_HEADER ,0);
		curl_setopt( $curl , CURLOPT_URL , $url );
		curl_setopt( $curl, CURLOPT_HTTPHEADER , $header);
		curl_setopt( $curl, CURLOPT_TIMEOUT, $CMS->auto->server_timeout );

		$data = curl_exec( $curl ); 
		
		curl_close($curl);
 
		if( $data == false )
		{			
			$this->timeout = 1;
			$CMS->auto->server_msg = "Timeout or no data return<br />Url: ".$url;
			return false;
		}
		else
		{
			$CMS->auto->server_msg = "";	
		}
		
		return $data;
	}


	public function execute_2($page = "")
	{
		global $CMS;

		$page = $page ? str_replace(" ", "%20", trim($page)) : "";
		
		$this->server_info['ip'] = trim($this->server_info['ip']);

		$HOST = ($this->is_ssl == 1)? 'https://' . $this->server_info['ip'] . ':2087/' : 'http://' . $this->server_info['ip'] . ':2086';

		$this->server_info['pass'] = ( $this->pass_is_hash? $this->server_info['pass'] : $this->server_info['hash']);
		
		$curl = curl_init();
		 
		$url = $HOST . 'json-api/cpanel?cpanel_jsonapi_user=root&cpanel_jsonapi_apiversion=2' . $page;
		
		// Debug
		$this->url_request = $url;

		$header[0] = (( $this->server_info['hash'] == '' )? 'Authorization: Basic ' . base64_encode( $this->server_info['user'] . ':' . $this->server_info['pass'] ) : $header[0] = 'Authorization: WHM ' . $this->server_info['user'] . ":" . $this->server_info['hash']);
// print_r ($url); 
 //print_r ($header);exit; 

		curl_setopt( $curl , CURLOPT_SSL_VERIFYHOST , 0 );
		curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false );
		curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true ); 
		curl_setopt( $curl , CURLOPT_HEADER ,0);
		curl_setopt( $curl , CURLOPT_URL , $url );
		curl_setopt( $curl, CURLOPT_HTTPHEADER , $header);
		curl_setopt( $curl, CURLOPT_TIMEOUT, 500);

		$data = curl_exec( $curl ); 
		
		curl_close($curl);
 
		if( $data == false )
		{			
			$this->timeout = 1;
			$CMS->auto->server_msg = "Timeout or no data return<br />Url: ".$url;
			return false;
		}
		else
		{
			$CMS->auto->server_msg = "";	
		}
		
		return $data;
	}


	private function get_size( $mbytes )
	{
		if( is_numeric( $mbytes ) )
		{
			if( $mbytes >= 10485764 )
			{
				$size = round( $mbytes / 10485764 , 2 ) . ' TB';
			}
			else if( $mbytes >= 1024 )
			{
				$size = round( $mbytes / 1024 , 2 ) . ' GB'; 
			}
			else
			{
				$size = $mbytes . ' MB';
			}
		} else
		{
			$size = 'Unknown';
		}
		return $size;
	}
	
	/*
	* getResult : analyse result of open function when a result index exist in rersult
	* @param string $data
	* RETURN array string object of result
	*/
	public function getResult( $data, $type = "parse" )
	{
		$this->data_result = $data;

		$object = json_decode( $data, 1 );
		
		if ( $type == "parse" )
		{
			return $object['result'][0];
		}
		else
		{
			return $object;
		}
	}
	
	/*
	* getResult2 : analyse result of open function
	* @param string $data
	* RETURN array string object of result
	*/
	public function getResult2( $data )
	{
		$object = json_decode( $data, 1 );

		return $object['acct'][0];
	}
	
	/*
	* make_random_password : Make random password
	* @param integer $length
	* RETURN string
	*/
	private function make_random_password( $length = 12 )
	{
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890!$()|';
		$chars_length = ( strlen( $chars ) - 1 );
		$string = $chars{ mt_rand( 0 , $chars_length ) };
	   
		for( $i = 1; $i < $length; $i = strlen( $string ) )
		{
			$r = $chars{ mt_rand( 0 , $chars_length ) };
			if( $r != $string{ $i - 1 } ) $string .= $r;
		}
		return $string;
	}
	
/********************************** HOSTING COMMAND ***************************************/
	public function add_hosting($params)
	{
		global $CMS, $DB, $member;
		
		// Input
		$params['domain'] = $params['hosting_domain'];
		$params['username'] = $params['hosting_username'];
		$params['password'] = $params['hosting_password'];
		$params['email'] = $params['hosting_email'];
		$params['package'] = $params['package_name'];
		
		// Process
		$params['password'] = (( $params['password'] == '' ) ? $this->make_random_password() : $params['password']);
		
		// Check for existed hosting
		$existed_domain = $this->get_hosting_info($params['username'], "domain");
		//print_r ($params);exit;
 		if($existed_domain == "")
 		{
 			//$result['errormsg'] = "Error check domain exites";
			//$result['status'] = "0";	
			//return $result;
 		}
		 
		if( ! $existed_domain && $this->timeout == 1 )
		{
			return "[Hosting] Create <b>{$params['domain']}</b> timeout";
		}
		


		if ( ! $existed_domain )
		{
			//Add zone
			//$zone = $this->execute( 'killdns?domain=' . $params['domain'] );
			
			// Add hosting
			$d = $this->execute( 'createacct?domain=' . $params['domain'] . '&username=' . $params['username'] . '&useregns=0&reseller=0&ip=n&contactemail=' . $params['email'] . '&plan=' . $params['package'] . '&password=' . $params['password'] . '&forcedns=1' );
			
			$res = $this->getResult( $d );
		
			
			$this->refresh_hosting($params);
		}
 		else
		{ 
			$result['msg'] = "existed_domain";
			$result['status'] = "0";	
			return $result;
		}

		if ( $CMS->auto->server_msg )
		{ 
			return "[Hosting] Unknown: ". $CMS->auto->server_msg;
		}
		else if( $res['status'] == 0 && ! $existed_domain )
		{ 
			//return "[Hosting 2] {$this->data_result}<br />".var_export($res, true).$res['statusmsg']."<br />".$res['messages'][0];
		 
			$result['msg'] = $res['statusmsg'];
			$result['status'] = $res['status'];
			$result['data_result'] = $this->data_result;
		 	return $result;
		}
		else
		{ 
			$accinfo = '';
			
			if ( ! $existed_domain )
			{
				if( preg_match_all( '/<pre>(.*)<\/pre>/siU' , $res['rawout'] , $n ) )
				{
					$accinfo = $n[0][1];
				}
			}
			
			$result = array();
			
			$result['domain'] = $params['domain'];
			
			$result['username'] = $params['username'];
			
			$result['password'] = $params['password'];
			
			$result['package'] = $params['package'];
			
			if ( ! $existed_domain )
			{
				$result['nameserver1'] = $res['options']['nameserver'];
			
				$result['nameserver2'] = $res['options']['nameserver2'];
				
				$result['nameserver3'] = $res['options']['nameserver3'];
				
				$result['nameserver4'] = $res['options']['nameserver4'];

				$result['account_info'] = $accinfo;
			}
			 
			$result['msg'] = $res['statusmsg'];
			$result['status'] = $res['status'];
			$result['options'] = $res['options'];
			 
			return $result;
		}
	}
	
	public function edit_hosting($params)
	{
		global $CMS, $DB, $member;

		// Input
		$params['username'] = $params['hosting_username'];
		$params['domain'] = $params['changelist']['hosting_domain'] == true ? $params['hosting_domain'] : "";
		$params['newusername'] = $params['changelist']['hosting_newusername'] == true ? $params['hosting_newusername'] : "";
		$params['password'] = $params['changelist']['hosting_password'] == true ? $params['hosting_password'] : "";
		$params['email'] = $params['hosting_email'];
		$params['package'] = $params['changelist']['package_id'] == true ? $params['package_name'] : "";
		$params['quota'] = $params['changelist']['hosting_quota'] == true ? $params['hosting_quota'] : "";
		$params['bandwidth'] = $params['changelist']['hosting_bandwidth'] == true ? $params['hosting_bandwidth'] : "";

		$url = '';
		
		if ($params['domain'])
		{
			$url .='&domain=' . $params['domain'];
		}
		
		if ($params['email'] != '')
		{
			$url .='&contactemail=' . $params['email'];
		}

		if ( $params['newusername'] )
		{
			$url .= '&newuser=' . $params['newusername'];
		}

		$d = $this->execute( 'modifyacct?user=' . $params['username'] . $url);
		
		$res = $this->getResult( $d );
		$result['msg'] = $res['statusmsg'];
		$result['status'] = $res['status'];
		$result['options'] = $res['options'];
			 
		return $result;

		//print_r ($res);
		// // Return false;
		// if ( $CMS->auto->server_msg OR $res['status'] == 0 )
		// {
		// 	$out[0] = false;
		// 	$out[1] = "[001] ". $CMS->auto->server_msg . "<br />".$res['statusmsg']
		// 	."<br />".$res['messages'][0]
		// 	."<br />".var_export($res, true)
		// 	."<br />".$this->data_result;
		// 	return $out;
		// }

		// // Update package
		// if ( $params['package'] )
		// {
		// 	$returnpackage = $this->changepackage_hosting($params);
			
		// 	// LHL - 10/06/2013, fix error update package
		// 	if ( $returnpackage[0] == false ) { return $returnpackage; }

		// 	$this->refresh_hosting($params);
		// }

		// // Update bandwidth
		// if ( ! $params['package'] AND $params['quota'] )
		// {
		// 	$this->limit_user_quota( $params['username'] , $params['quota'] );
		// }

		// // Update bandwidth
		// if ( ! $params['package'] AND $params['bandwidth'] )
		// {
		// 	$this->limit_user_bandwidth( $params['username'] , $params['bandwidth'] );
		// }
		
		// // Update password
		// if ($params['password']!='')
		// {
		// 	$this->changepassword_hosting( $params['username'], $params['password']);
		// }

		// $out = array();
		
		// if ( $CMS->auto->server_msg )
		// {
		// 	$out[0] = false;
		// 	$out[1] = "[002] ". $CMS->auto->server_msg;
		// }
		// else if( $res['status'] == 0 )
		// {
		// 	$out[0] = false;
		// 	$out[1] = "[003] ". $res['statusmsg']."<br />".$res['messages'][0];
		// }
		// else
		// {
		// 	$out[0] = true;
		// 	$out[1] = $res['statusmsg']."<br />".$res['messages'][0];
		// 	$out[2] = "[004] ". $res['cpuser'];
		// }

		return $out;
	}
	
	public function delete_hosting($params)
	{
		global $CMS, $DB, $member;

		// Input
		$params['username'] = $params['hosting_username'];
		$d = $this->execute( 'removeacct?user=' . $params['username'] );
			
		$d2 = json_decode($d,1);
 	 
		if(isset($d2['result'][0]['status']) && $d2['result'][0]['status'] == 0 && preg_match("/(does not exist|Unable to find data)/", $d2['result'][0]['statusmsg']) == true )
		{
			$result['msg'] = $d2['result'][0]['statusmsg'];
			$result['status'] = 1;
			return $result;

		}
		// If failed to delete
		if(isset($d2['result'][0]['status']) && $d2['result'][0]['status'] == 1 )
		{
			$result['msg'] = $d2['result'][0]['statusmsg'];
			$result['status'] = 1;
			 
		}
		else
		{
			$result['msg'] = $d2['result'][0]['statusmsg'];
			$result['status'] = 0;
			
		}

		if(!is_array($d2))
		{
			$result['status'] = 1;
			
		}
	 	return $result;
	}
	
	public function suspend_hosting($params)
	{
		global $CMS;
		//print_r ($params);exit;
		// Input
		$params['username'] = $params['hosting_username'];
		$params['reson'] = $CMS->class->seo->remove_vietnamese($params['hosting_suspendreason']);
		
		$d = $this->execute( 'suspendacct?user=' . $params['username'] . '&reason=' . $params['reson'] );
		
		$res = $this->getResult( $d );
		 
		$result = array();
 
		if( $res['status'] == 0 )
		{
			$result['msg'] = $res['statusmsg'];
			$result['status'] = $res['status'];

		} else
		{
			$result['msg'] = $res['statusmsg'];
			$result['status'] = $res['status'];
		}
		return $result;
	}
	
	public function unsuspend_hosting( $params )
	{
		// Input
		$params['username'] = $params['hosting_username'];
	
	
		// Check status hosting
		$info = $this->list_hosting("user",$params['username']);
		
		
		if(is_array($info) AND $info['suspended'] == 0)// xong
		{
		 
			$result['msg'] = "Unsuspend Hosting Success";
			$result['status'] = 1;
			return $result;
		}
		
		$d = $this->execute( 'unsuspendacct?user=' . $params['username'] );
		$res = $this->getResult( $d );
		
		$result = array();
		if( $res['status'] == 0 )
		{
			 
			$result['msg'] = $res['statusmsg'];
			$result['status'] = 0;
		} else
		{
			$result['msg'] = $res['statusmsg'];
			$result['status'] = 1;
		}
		return $result;
	}
	
	public function changepassword_hosting( $username, $password)
	{
		$password = (empty( $password ) ? $this->make_random_password() : $password );
		
		$d = $this->execute( 'passwd?user=' . $username . '&pass=' . $password );
		$d2 = json_decode($d,1);
		$result = array();

		if(isset($d2['passwd'][0]['status']) &&  $d2['passwd'][0]['status'] ==0 &&  $d2['passwd'][0]['statusmsg'] == true )
		{
	 
			$result['msg'] = $d2['passwd'][0]['statusmsg'];
			$result['status'] = 0;

		} 
		elseif(isset($d2['passwd'][0]['status']) && $d2['passwd'][0]['status'] == 1  )
		{
			$result['msg'] = $d2['passwd'][0]['statusmsg'];
			$result['status'] = 1;
			$result['password'] =$password;
		}
		return $result;
	}
	
	public function changepackage_hosting($params)
	{

		$d = $this->execute("changepackage?user=" . $params['username'] . "&pkg=" . $params['package']);
		$res = $this->getResult( $d );

		$out = array();
		if( $res['status'] == 0 )
		{
			$out[0] = false;
			$out[1] = $res['statusmsg']."<br />".$res['messages'][0]."<br />URL: {$this->url_request}";
		} else
		{
			$out[0] = true;
			$out[1] = $res['statusmsg']."<br />".$res['messages'][0]."<br />URL: {$this->url_request}";
		}
		return $out;
	}

	public function list_hosting( $search_type = '' , $search_word = '' )
	{
		global $CMS;
		
		$allow_search_type = array( 'domain' , 'user' , 'ip' , 'package' );
		
		if( ( $search_type != '' ) && ( in_array( $search_type , $allow_search_type ) ) )
		{
			$url = 'listaccts?searchtype=' . $search_type . '&search=' . $search_word;
		} else
		{
			$url = 'listaccts';
		}

		$d = $this->execute( $url );

		$res = $this->getResult2( $d );
		
		if ( count($res) == 0 )
		{
			return false;	
		}
		else if ( $res['domain'] )
		{
			return $res;
		}
		
		$out = array();
		$i = 0;
		
		
		
		foreach( $res as $ac )
		{
			$out[$i]['domain'] = $ac['domain'];
			$out[$i]['user'] = $ac['user'];
			$out[$i]['email'] = $ac['email'];
			$out[$i]['startdate'] = $ac['startdate'];
			$out[$i]['starttime'] = strtotime( $ac['startdate'] );
			$out[$i]['disklimit'] = $ac['disklimit'];
			$out[$i]['diskused'] = $ac['diskused'];
			$out[$i]['ip'] = $ac['ip'];
			$out[$i]['suspended'] = $ac['suspended'];
			$out[$i]['suspendreason'] = $ac['suspendreason'];
			$out[$i]['suspendtime'] = $ac['suspendtime'];
			
			$i++;
		}
		return $out;
	}
        
        public function get_list_hosting()
	{
		global $CMS;
		
		$url = 'listaccts';
                
		$d = $this->execute( $url );
                //print_r($d); exit;t2( $d );
		if ( count($d) == 0 )
		{
			return false;	
		}
                return $d;
		
	}
	
	public function get_hosting_info($username, $field="")
	{
		global $CMS;
		
		$acc = $this->list_hosting('user',$username);
		 
		if ( ! $acc )
		{
			return false;	
		}
		
		if($field)
		{
			return $acc[$field];
		}
		
		return $acc;
	}



	public function get_bandwidth_info($username, $field="")
	{
		global $CMS;
			
		$d = $this->execute( 'showbw?user='.$username);	
		return json_decode($d);
	}
	
	/*
	* search_account_by_package : Search account by package
	* @param string $package : Package keyword that you want find accounts that package equal it
	* RETURN array sting that is information of searched in account lists
	*/
	public function search_account_by_package( $package )
	{
		return $this->list_accounts( 'package' , $package );
	}
	
	/*
	* search_account_by_domain : Search account by domain
	* @param string $domain : Domain keyword that you want find accounts that domain equal it
	* RETURN array sting that is information of searched in account lists
	*/
	public function search_account_by_domain( $domain )
	{
		return $this->list_accounts( 'domain' , $domain );
	}
	
	/*
	* search_account_by_ip : Search account by ip
	* @param string $ip : IP keyword that you want find accounts that ip equal it
	* RETURN array sting that is information of searched in account lists
	*/
	public function search_account_by_ip( $ip )
	{
		return $this->list_accounts( 'ip' , $ip );
	}
	
	/*
	* search_account_by_user : Search account by user
	* @param string $ip : user keyword that you want find accounts that user equal it
	* RETURN array sting that is information of searched in account lists
	*/
	public function search_account_by_user( $user )
	{
		return $this->list_accounts( 'user' , $user );
	}
	
	/*
	* limit_user_bandwidth : Change limit of user bandwidth
	* @param string $username : account username that you want change bandwith limit
	* @param integer $new_bandwidth : enter new bandwith limit ( to megabyte )
	* RETURN array sting that is information of searched in account lists
	*/
	public function limit_user_bandwidth( $username , $new_bandwidth )
	{
		$d = $this->execute( 'limitbw?user=' . $username . '&bwlimit=' . $new_bandwidth );
		$res = $this->getResult( $d );
		
		$out = array();
		if( $res['status'] == 0 )
		{
			$out[0] = false;
			$out[1] = $res['statusmsg']."<br />".$res['messages'][0];
			$out[2] = '';
		} else
		{
			$out[0] = true;
			$out[1] = $res['statusmsg']."<br />".$res['messages'][0];
			$out[2] = $res['bwlimit']['human_bwused'];
		}
		return $out;
	}
	
	public function refresh_hosting( $params )
	{		
		// Update max list
		$d2 = $this->execute( 'modifyacct?user=' . $params['username'] . '&MAXLST=0');
		$res2 = $this->getResult( $d2 );
		
		return $res2;
	}
	
	/*
	* limit_user_quota : Change limit of user quota
	* @param string $username : account username that you want change quota limit
	* @param integer $new_quota : enter new quota limit ( to megabyte )
	* RETURN array sting that is information of searched in account lists
	*/
	public function limit_user_quota( $username , $new_quota )
	{
		$d = $this->execute( 'editquota?user=' . $username . '&quota=' . $new_quota );
		$res = $this->getResult( $d );
		
		$out = array();

		if( $res['status'] == 0 )
		{
			$out[0] = false;
			$out[1] = $res['statusmsg']."<br />".$res['messages'][0];
		} else
		{
			$out[0] = true;
			$out[1] = $res['statusmsg']."<br />".$res['messages'][0];
		}
		return $out;
	}

/************************PACKAGE COMMAND************************************/
	/*
	* list_packages : List all aded package
	*/
	public function list_packages()
	{
		$d = $this->execute( 'listpkgs' );
		$res = $this->getResult( $d, "convert" );
		
		//$pkg = $res->package;
		$pkg = $res['package'];

		$out = array();
		$i = 0;

		if ( ! $pkg )
		{
			return false;	
		}

		foreach( $pkg as $pk )
		{
			$out[$i]['package_name'] = $pk['name'];
			$out[$i]['package_bandwidth'] = ( $pk['BWLIMIT'] == 'unlimited' ) ? 'unlimited' : $this->get_size( $pk['BWLIMIT'] );
			$out[$i]['package_quota'] = ( $pk['QUOTA'] == 'unlimited' ) ? 'unlimited' : $this->get_size( $pk['QUOTA'] );
			$out[$i]['package_sqldatabase'] = $pk['MAXSQL'];
			$out[$i]['package_subdomain'] = $pk['MAXSUB'];
			$out[$i]['package_parketdomain'] = $pk['MAXPARK'];
			$out[$i]['package_addondomain'] = $pk['MAXADDON'];
			$out[$i]['package_ftpaccount'] = $pk['MAXFTP'];
			$out[$i]['package_popemail'] = $pk['MAXPOP'];
			$out[$i]['package_mailinglist'] = $pk['MAXLST'];
			$out[$i]['package_ipaddress'] = $pk['IP'];
			
			$i++;
		}

		return $out;
	}
	
	/*
	* add_package : Add new package
	* $params as array
	*/
	public function add_package($params)
	{
		global $CMS;
		
		$name = $CMS->class->seo->remove_vietnamese($params['package_name']);
		$quota = $params['package_quota'] == '9999' ? "n" : ($params['package_quota'] ? $params['package_quota'] : "n");
		$bandwidth = ($params['package_bandwidth'] ? $params['package_bandwidth'] : "n");
		$subdomain = $params['package_subdomain'] == '9999' ? "" : ($params['package_subdomain'] ? $params['package_subdomain'] : "n");
		$park = $params['package_parketdomain'] == '9999' ? 99999 : ($params['package_parketdomain'] ? $params['package_parketdomain'] : "n");
		$addon = $params['package_addondomain'] == '9999' ? 99999 : ($params['package_addondomain'] ? ($params['package_addondomain']-1) : "n");
		$ftp = $params['package_ftpaccount'] == '9999' ? "" : ($params['package_ftpaccount'] ? $params['package_ftpaccount'] : "n");
		$pop = $params['package_popemail'] == '9999' ? "" : ($params['package_popemail'] ? $params['package_popemail'] : "n");
		$list = "n"; // $params['package_mallinglist']
		$sql = $params['package_sqldatabase'] == '9999' ? "" : $params['package_sqldatabase'];
		$mssql = $params['package_mssqldatabse'] == '9999' ? "" : $params['package_mssqldatabse'];
		$feature = 'default';
		$ip = 0;
		$cgi = 0;
		$fronpage = 0;
		$lang = 'en';
		$theme = 'paper_lantern'; // x3
		$shell = 0;
		
		$d = $this->execute( 'addpkg?name=' . $name . '&quota=' . $quota . '&bwlimit=' . $bandwidth . '&maxpark=' . $park . '&maxsub=' . $subdomain . '&maxaddon=' . $addon . '&maxpop=' . $pop . '&maxftp=' . $ftp . '&maxlists=' . $list . '&maxsql=' . $sql . '&featurelist=' . $feature . '&ip=' . $ip . '&cgi=' . $cgi . '&frontpage=' . $fronpage . '&language=' . $lang . '&cpmod=' . $theme . '&hasshell=' . $shell );
		
		
		
		//$d = $this->cpanel_req(1,$this->server_info['ip'],$this->server_info['userapi'],$this->server_info['passapi'],'','','');
		
		$res = $this->getResult( $d );
		
		$out = array();
		if( $res['status'] == 0 )
		{
			$out[0] = false;
			$out[1] = $res['statusmsg']."<br />".$res['messages'][0];
		} else
		{
			$out[0] = true;
			$out[1] = $res['statusmsg']."<br />".$res['messages'][0];
		}

		return $out;
	}
	
	/*
	* add_package : Add new package
	* $params as array
	*/
	
	public function edit_package($params)
	{
		global $CMS;
			$d = $this->execute( 'editpkg?name=' . $name . '&quota=' . $quota . '&bwlimit=' . $bandwidth . '&maxpark=' . $park . '&maxsub=' . $subdomain . '&maxaddon=' . $addon . '&maxpop=' . $pop . '&maxftp=' . $ftp . '&maxlists=' . $list . '&maxsql=' . $sql . '&featurelist=' . $feature . '&ip=' . $ip . '&cgi=' . $cgi . '&frontpage=' . $fronpage . '&language=' . $lang . '&cpmod=' . $theme . '&hasshell=' . $shell );
		$res = $this->getResult( $d );


		print_r ($res);exit;
		$name = $CMS->class->seo->remove_vietnamese($params['package_name']);
		$quota = $params['package_quota'] == '9999' ? "n" : ($params['package_quota'] ? $params['package_quota'] : "n");
		$bandwidth = ($params['package_bandwidth'] ? $params['package_bandwidth'] : "n");
		$subdomain = $params['package_subdomain'] == '9999' ? "" : ($params['package_subdomain'] ? $params['package_subdomain'] : "n");
		$park = $params['package_parketdomain'] == '9999' ? 99999 : ($params['package_parketdomain'] ? $params['package_parketdomain'] : "n");
		$addon = $params['package_addondomain'] == '9999' ? 99999 : ($params['package_addondomain'] ? ($params['package_addondomain']-1) : "n");
		$ftp = $params['package_ftpaccount'] == '9999' ? "" : ($params['package_ftpaccount'] ? $params['package_ftpaccount'] : "n");
		$pop = $params['package_popemail'] == '9999' ? "" : ($params['package_popemail'] ? $params['package_popemail'] : "n");
		$list = "n"; // $params['package_mallinglist']
		$sql = $params['package_sqldatabase'] == '9999' ? "" : $params['package_sqldatabase'];
		//$mssql = $params['package_mssqldatabse'] == '9999' ? "" : $params['package_mssqldatabse'];
		
		/*
		* Backup code cũ - do not removed
		*/
		/*
		$name = $CMS->class->seo->remove_vietnamese($params['package_name']);
		$quota = $params['package_quota'] ? $params['package_quota'] : "n";
		$bandwidth = $params['package_bandwidth'] ? $params['package_bandwidth'] : "n";
		$subdomain = $params['package_subdomain'] ? $params['package_subdomain'] : "n";
		$park = $params['package_parketdomain'] ? $params['package_parketdomain'] : "n";
		$addon = $params['package_addondomain'] ? ($params['package_addondomain']-1) : "n";
		$ftp = $params['package_ftpaccount'] ? $params['package_ftpaccount'] : "n";
		$pop = $params['package_popemail'] ? $params['package_popemail'] : "n";
		$list = "n"; // $params['package_mallinglist']
		$sql = $params['package_sqldatabase'];
		*/
		$feature = 'default';
		$ip = 0;
		$cgi = 0;
		$fronpage = 0;
		$lang = 'en';
		$theme = 'paper_lantern'; // x3
		$shell = 0;
		
		$d = $this->execute( 'editpkg?name=' . $name . '&quota=' . $quota . '&bwlimit=' . $bandwidth . '&maxpark=' . $park . '&maxsub=' . $subdomain . '&maxaddon=' . $addon . '&maxpop=' . $pop . '&maxftp=' . $ftp . '&maxlists=' . $list . '&maxsql=' . $sql . '&featurelist=' . $feature . '&ip=' . $ip . '&cgi=' . $cgi . '&frontpage=' . $fronpage . '&language=' . $lang . '&cpmod=' . $theme . '&hasshell=' . $shell );
		$res = $this->getResult( $d );
		
		$out = array();
		if( $res['status'] == 0 )
		{
			$out[0] = false;
			$out[1] = $res['statusmsg']."<br />".$res['messages'][0];
		} else
		{
			$out[0] = true;
			$out[1] = $res['statusmsg']."<br />".$res['messages'][0];
		}
		return $out;
	}
	
	/*
	* delete_package : Edit package
	* $name : (string) Name of package
	*/
	public function delete_package( $name )
	{
		$d = $this->execute( 'killpkg?pkg=' . $name );
		$res = $this->getResult( $d );
		
		$out = array();
		if( $res['status'] == 0 )
		{
			$out[0] = false;
			$out[1] = $res['statusmsg']."<br />".$res['messages'][0];
		} else
		{
			$out[0] = true;
			$out[1] = $res['statusmsg']."<br />".$res['messages'][0];
		}
		return $out;
	}
 	
	/*
	* Create Mysql DB 
	* $name : (string) Name of package
	*/
	public function mysql_createdb( $db_name = "" )
	{
	 
		$d = $this->execute_2( '&cpanel_jsonapi_module=Mysql&cpanel_jsonapi_func=adddb&db='.$db_name );
		$res = $this->getResult( $d );
		print_r ($res);exit;
		$out = array();
		if( $res['status'] == 0 )
		{
			$out[0] = false;
			$out[1] = $res['statusmsg']."<br />".$res['messages'][0];
		} else
		{
			$out[0] = true;
			$out[1] = $res['statusmsg']."<br />".$res['messages'][0];
		}
		return $out;
	}
 

}
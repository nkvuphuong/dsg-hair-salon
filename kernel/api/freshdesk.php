<?php
/********************************************************
*	Copyright © Freshworks Inc. All Rights Reserved.	*
*	URL: https://freshdesk.com/							*
*	Editer: lêlong										*
*	Date: 050817										*
*	API connect freshdesk								*
*	Document: https://developer.freshdesk.com/api/		*
*********************************************************/
 
if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
} 

spl_autoload_register(function($class) {
    $location = __DIR__ . '/Freshdesk' . str_replace('\\', '/', ltrim($class, 'Freshdesk')) . '.php';
    if (is_file($location)) {
        require_once($location);
    }
});

require_once root_path . "kernel/api/Freshdesk/Api.php";

use \Freshdesk\Api;

$CMS->api->freshdesk = new Api_Freshdesk;

class Api_Freshdesk {
	public $domain;
	public $key;
	public $api;
	public function __construct() {
	}
	public function connect($domain = null, $key = null) {
		global $CMS;
		if (is_null($domain)) { 
			$this->domain = empty($this->domain) ? $CMS->vars['fd_domain'] : $this->domain;
		} else {
			$this->domain = $domain;
		}
		if (is_null($key)) {
			$this->key = empty($this->key) ? $CMS->vars['fd_key'] : $this->key;
		} else {
			$this->key = $key;
		}
		$this->api = new Api($this->key, $this->domain);
	}
	public function test() {
		dump($this->api->request('GET', '/tickets', null, array('filter'=>'all_tickets')));
		return false;
	}
	public function contactsAll() {
		return $this->api->request('GET', '/contacts');
	}
	public function contactsAllDeleted() {
		return $this->api->request('GET', '/contacts?state=deleted');
	}
	public function contactsCreate($data = null) {
		if (! is_null($data)) {
			return $this->api->request('POST', '/contacts', $data);
		}
		return false;
	}
	public function testSync() {
		return $this->api->request('GET', '/settings/helpdesk', array(), null);
	}
}
?>
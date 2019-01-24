<?php
/*********************************************
* @package: Api_Slack
* @copyright: https://github.com/maknz/slack
* @editor: lêlong
* @date: 111017
**********************************************/
require_once(root_path."vendor/autoload.php");
 
use Maknz\Slack\Client;

$CMS->api->slack = new Api_Slack;

class Api_Slack {
	// public $url = 'https://hooks.slack.com/services/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'; // NH
	public $url = 'https://hooks.slack.com/services/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'; // 3F
	public $url_website_alert = 'https://hooks.slack.com/services/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'; 
	public $client;
	function __construct() {

		$this->client = new Maknz\Slack\Client($this->url);
	}

	function init() {

		$this->client = new Maknz\Slack\Client($this->url_website_alert);
	}

	function __destruct() {
	}
	function sendAlert($message = null) {
		if (! is_null($message)) {
			$str = '';
			if (! empty($message['name'])) {
				$str .= 'Name: ' . $message['name'] . "\n";
			}
			if (! empty($message['email'])) {
				$str .= 'Email: ' . $message['email'] . "\n";
			}
			if (! empty($message['phone'])) {
				$str .= 'Phone: ' . $message['phone'] . "\n";
			}
			if (! empty($message['address'])) {
				$str .= 'Address: ' . $message['address'] . "\n";
			}
			if (! empty($message['error_no']) || ! empty($message['error_message'])) {
				$str .= 'Error: ' . $message['error_message'] . ' (' . $message['error_no'] . ')' . "\n";
			}
			try {
				$this->client->to('#monitor')->attach([
					'fallback' => 'error while signing trial',
					'text' => $str,
					'color' => 'danger',
				])->send('Lỗi trong qua trình đăng ký dùng thử dịch vụ Autom');
			} catch (Exception $e) {
				exit($e);
			}
			return true;
		}
		return false;
	}

	function sendMessage($message = null, $title = null) {
		if (! is_null($message)) {
			try {
				$this->client->to('#monitor')->attach([
					'fallback' => 'error while active website',
					'text' => $message,
					'color' => 'danger',
				])->send($title);
			} catch (Exception $e) {
				exit($e);
			}
			return true;
		}
		return false;
	}

	function sendMessage_2($message = null, $title = null) {
		$this->init();
		if (! is_null($message)) {
			try {
				$this->client->to('#monitor')->attach([
					'fallback' => 'error while active website',
					'text' => $message,
					'color' => 'danger',
				])->send($title);
			} catch (Exception $e) {
				exit($e);
			}
			return true;
		}
		return false;
	}


}
?>
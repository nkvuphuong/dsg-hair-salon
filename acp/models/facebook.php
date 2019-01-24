<?php

namespace models;

use lib\input;
use lib\db;

class facebook {
	static public $token;
	static public function init() {
    	global $CMS, $DB;
		$DB->query("CREATE TABLE IF NOT EXISTS `" . root_table . "fanpage` (`fanpage_id` int(11) NOT NULL AUTO_INCREMENT, `fanpage_fb_id` varchar(255) NOT NULL, `fanpage_name` varchar(255) DEFAULT NULL, `fanpage_category` varchar(255) DEFAULT NULL, `fanpage_token` varchar(255) DEFAULT NULL, `fanpage_time` int(11) NOT NULL DEFAULT '0', `fanpage_deleted` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`fanpage_id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
		// $DB->query("CREATE TABLE IF NOT EXISTS `" . root_table . "fanpage_message` (`fanpage_message_id` int(11) NOT NULL AUTO_INCREMENT, `fanpage_id` int(11) NOT NULL, `fanpage_message_fb_user_id` varchar(255) NOT NULL, `fanpage_message_fb_user_name` varchar(255) NOT NULL, `fanpage_message_fb_user_avartar` varchar(255) DEFAULT NULL, `fanpage_message_fb_id` varchar(255) NOT NULL, `fanpage_message_time` int(11) NOT NULL DEFAULT '0', `fanpage_message_deleted` int(11) NOT NULL DEFAULT '0', `fanpage_message_read` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`fanpage_message_id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
		// $DB->query("CREATE TABLE IF NOT EXISTS `" . root_table . "fanpage_message_sub` (`fanpage_message_sub_id` int(11) NOT NULL AUTO_INCREMENT, `fanpage_message_id` int(11) NOT NULL, `fanpage_message_sub_fb_id` varchar(255) NOT NULL, `fanpage_message_sub_fb_user_id` varchar(255) NOT NULL, `fanpage_message_sub_fb_message` text, `fanpage_message_sub_read` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`fanpage_message_sub_id`)) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;");
    }
	function getListFanpage() {
		$respone = array('status' => false, 'messages' => '');
		global $CMS;		
		if (isset($CMS->api->facebook->accessToken)) {
			try {
				$request = $CMS->api->facebook->fb->get('/me/accounts');
				$data = $request->getGraphEdge()->asArray();
				if (is_array($data) && count($data) > 0) {
					$respone['status'] = true;
					$respone['messages'] = $data;
				}
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				$respone['messages'] = 'Graph returned an error: ' . $e->getMessage();
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
				$respone['messages'] = 'Facebook SDK returned an error: ' . $e->getMessage();
			}
		}
		return $respone;
	}
	static public function saveTokenFanpage($data = null) {
		if (! is_null($data)) {
			global $CMS, $DB;
			foreach($data as $dt) {
				$DB->query("SELECT 0 FROM `" . root_table . "fanpage` WHERE `fanpage_deleted` = 0 AND `fanpage_fb_id` = '{$dt['id']}'");
				if ($DB->num_rows() > 0) {
					$DB->query("UPDATE `" . root_table . "fanpage` SET `fanpage_name`='{$dt['name']}', `fanpage_category`='{$dt['category']}', `fanpage_token`='{$dt['access_token']}', `fanpage_time`='" .time() ."' WHERE `fanpage_deleted` = 0 AND `fanpage_fb_id` = '{$dt['id']}';");
				} else {
					$DB->query("INSERT INTO `" . root_table . "fanpage` (`fanpage_fb_id`, `fanpage_name`, `fanpage_category`, `fanpage_token`, `fanpage_time`) VALUES ('{$dt['id']}', '{$dt['name']}', '{$dt['category']}', '{$dt['access_token']}', '" .time() ."');");
					$tmp['site_domain'] = $_SERVER['SERVER_NAME']; //rtrim($CMS->vars['root_domain'], '/acp'); 
					$tmp['fanpage_id'] = $dt['id'];  
					$tmp['fanpage_name'] = $dt['name'];  
					$tmp['access_token'] = $dt['access_token'];
					$CMS->api->eco->execute('fb_fanpage_add', $tmp);
				}
			}
			return true;
		}
		return false;
	}
	static public function deletedTokenFanpage($data = null) {
		if (! is_null($data)) {
			global $CMS, $DB;
			foreach($data as $dt) { 
				$DB->query("DELETE FROM `" . root_table . "fanpage` WHERE `fanpage_fb_id` = '{$dt['id']}';");
			}
			return true;
		}
		return false;
	}
	static public function checkFanpage($fanpage_id = null) {
		if (! is_null($fanpage_id)) {
			global $CMS, $DB;
			$DB->query("SELECT 0 FROM `" . root_table . "fanpage` WHERE `fanpage_deleted` = 0 AND `fanpage_fb_id` = '{$fanpage_id}'");
			if ($DB->num_rows() > 0) {
				return true;
			}
		}
		return false;
	}
	
	
	
	/****************************************************************************************/
	// static public function saveFanpageMessage($data = null) {
		// if (! is_null($data)) {
			// global $CMS, $DB;
			// $DB->query("SELECT 0 FROM `" . root_table . "fanpage_message` WHERE `fanpage_message_deleted` = 0 AND `fanpage_message_fb_id` = '{$data['id']}'");
			// if ($DB->num_rows() > 0) {
				// $DB->query("UPDATE `" . root_table . "fanpage_message` SET `fanpage_message_fb_user_id`='{$data['participants'][0]['id']}', `fanpage_message_fb_user_name`='{$data['participants'][0]['name']}', `fanpage_message_fb_user_avartar`='{$data['fanpage_message_fb_user_avartar']}', `fanpage_message_time`='" . time() . "' WHERE `fanpage_message_deleted` = 0 AND `fanpage_message_fb_id` = '{$data['id']}';");
			// } else {
				// $DB->query("INSERT INTO `" . root_table . "fanpage_message` (`fanpage_id`, `fanpage_message_fb_user_id`, `fanpage_message_fb_user_name`, `fanpage_message_fb_user_avartar`, `fanpage_message_fb_id`, `fanpage_message_time`) VALUES ('{$data['fanpage_id']}', '{$data['participants'][0]['id']}', '{$data['participants'][0]['name']}', '{$data['fanpage_message_fb_user_avartar']}', '{$data['id']}', '" . time() . "');");
			// }
			// return true;
		// }
		// return false;
	// }
	// static public function saveFanpageMessageSub($data = null) {
		// if (! is_null($data)) {
			// global $CMS, $DB;
			// $DB->query("SELECT 0 FROM `" . root_table . "fanpage_message_sub` WHERE `fanpage_message_sub_fb_id` = '{$data['id']}'");
			// if ($DB->num_rows() > 0) {
				// $DB->query("UPDATE `" . root_table . "fanpage_message_sub` SET `fanpage_message_sub_fb_user_id`='{$data['from']['id']}', `fanpage_message_sub_fb_message`='{$data['message']}' WHERE `fanpage_message_sub_fb_id` = '{$data['id']}';");
			// } else {
				// $DB->query("INSERT INTO `" . root_table . "fanpage_message_sub` (`fanpage_message_id`, `fanpage_message_sub_fb_id`, `fanpage_message_sub_fb_user_id`, `fanpage_message_sub_fb_message`) VALUES ('{$data['fanpage_message_id']}', '{$data['id']}', '{$data['from']['id']}', '{$data['message']}');");
			// }
			// return true;
		// }
		// return false;
	// }
	// static public function getInfoFanpage($id = 0) {
		// if (! empty($id)) {
			// global $CMS, $DB;
			// $DB->query("SELECT * FROM `" . root_table . "fanpage` WHERE `fanpage_deleted` = 0 AND (`fanpage_id` = '{$id}' OR `fanpage_fb_id` = '{$id}');");
			// if ($DB->num_rows() > 0) {
				// return $DB->fetch_assoc();
			// }
		// }
		// return false;
	// }
	// static public function getInfoFanpageMessage($id = 0) {
		// if (! empty($id)) {
			// global $CMS, $DB;
			// $DB->query("SELECT * FROM `" . root_table . "fanpage_message` WHERE `fanpage_message_deleted` = 0 AND (`fanpage_message_id` = '{$id}' OR `fanpage_message_fb_id` = '{$id}');");
			// if ($DB->num_rows() > 0) {
				// return $DB->fetch_assoc();
			// }
		// }
		// return false;
	// }
	// static public function fanpageMessage() {
		// $respone = array('messages' => '');
		// global $CMS;
		// if (! empty($CMS->input['page_id'])) {
			// $info = self::getInfoFanpage($CMS->input['page_id']);
			// if ($info) {
				// self::$token = $info['fanpage_token'];
				// $CMS->api->facebook->fb->setDefaultAccessToken($info['fanpage_token']);
				// try {
					// $request = $CMS->api->facebook->fb->get('/' . $info['fanpage_fb_id'] . '?fields=conversations{participants}');
					// $data = $request->getGraphNode()->asArray();
					// if (! empty($data['conversations'])) {
						// foreach ($data['conversations'] as $message) {
							// $message['fanpage_id'] = $info['fanpage_id'];
							// $message['fanpage_message_fb_user_avartar'] = self::getFbAserAvartar($message['participants'][0]['id']);
							// $respone['messages'][] = $message;
							// self::saveFanpageMessage($message);
							// $respone['messages'] = self::fanpageMessageSub($message['id'])['messages'];
						// }
					// }
				// } catch(Facebook\Exceptions\FacebookResponseException $e) {
					// $respone['messages'] = 'Graph returned an error: ' . $e->getMessage();
				// } catch(Facebook\Exceptions\FacebookSDKException $e) {
					// $respone['messages'] = 'Facebook SDK returned an error: ' . $e->getMessage();
				// }
			// } else {
				// $respone['messages'] = $CMS->lang['error_fanpage_connection'];
			// }
		// }
		// return $respone;
	// }
	// static public function fanpageMessageSub($id = null) {
		// $respone = array('messages' => '');
		// if (! is_null($id) && ! empty(self::$token)) {
			// global $CMS;
			// $info = self::getInfoFanpageMessage($id);
			// $CMS->api->facebook->fb->setDefaultAccessToken(self::$token);
			// try {
				// $request = $CMS->api->facebook->fb->get('/' . $id . '?fields=messages{message,from}');
				// $data = $request->getGraphNode()->asArray();
				// if (! empty($data['messages'])) {
					// foreach ($data['messages'] as $message) {
						// $message['fanpage_message_id'] = $info['fanpage_message_id'];
						// self::saveFanpageMessageSub($message);
					// }
				// }
			// } catch(Facebook\Exceptions\FacebookResponseException $e) {
				// $respone['messages'] = 'Graph returned an error: ' . $e->getMessage();
			// } catch(Facebook\Exceptions\FacebookSDKException $e) {
				// $respone['messages'] = 'Facebook SDK returned an error: ' . $e->getMessage();
			// }
		// }
		// return $respone;
	// }
	// static public function getFbAserAvartar($id = null) {
		// if (! is_null($id)) {
			// global $CMS;
			// try {
				// $request = $CMS->api->facebook->fb->get('/' . $id . '/?fields=picture');
				// $data = $request->getGraphNode()->asArray();
				// return $data['picture']['url'];
			// } catch(Facebook\Exceptions\FacebookResponseException $e) {
			// } catch(Facebook\Exceptions\FacebookSDKException $e) {
			// }
		// }
		// return false;
	// }
	// static public function getMessage() {
		// $respone = array('status' => false, 'messages' => '');
		// global $CMS, $DB;
		// $fanpage = self::getInfoFanpage($CMS->input['page_id']);
		// if ($fanpage) {
			// $sql1 = $DB->query("SELECT * FROM `" . root_table . "fanpage_message` WHERE `fanpage_message_deleted` = 0 AND `fanpage_id` = '{$fanpage['fanpage_id']}';");
			// if ($DB->num_rows($sql1) > 0) {
				// $respone['status'] = true;
				// while ($result1 = $DB->fetch_assoc($sql1)) {
					// $message = '';
					// $sql2 = $DB->query("SELECT `fanpage_message_sub_fb_message` FROM `" . root_table . "fanpage_message_sub` WHERE `fanpage_message_id` = '{$result1['fanpage_message_id']}' AND `fanpage_message_sub_fb_user_id`='{$result1['fanpage_message_fb_user_id']}' AND `fanpage_message_sub_read`=0 ORDER BY `fanpage_message_sub_id` DESC LIMIT 1;");
					// if ($DB->num_rows($sql2) > 0) {
						// while ($result2 = $DB->fetch_assoc($sql2)) {
							// $message = $result2['fanpage_message_sub_fb_message'];
						// }
					// }
					// $count = 0;
					// $sql3 = $DB->query("SELECT COUNT('fanpage_message_sub_id') as count FROM `" . root_table . "fanpage_message_sub` WHERE `fanpage_message_id` = '{$result1['fanpage_message_id']}' AND `fanpage_message_sub_fb_user_id`='{$result1['fanpage_message_fb_user_id']}' AND `fanpage_message_sub_read`=0;");
					// if ($DB->num_rows($sql3) > 0) {
						// while ($result3 = $DB->fetch_assoc($sql3)) {
							// $count = $result3['count'];
						// }
					// }
					// $respone['messages'][$result1['fanpage_message_id']] = array(
						// 'id' => $result1['fanpage_message_id'],
						// 'fb_user_id' => $result1['fanpage_message_fb_user_id'],
						// 'fb_user_name' => $result1['fanpage_message_fb_user_name'], 
						// 'fb_user_avartar' => $result1['fanpage_message_fb_user_avartar'], 
						// 'fb_id' => $result1['fanpage_message_fb_id'],
						// 'time' => $result1['fanpage_message_time'],
						// 'read' => $result1['fanpage_message_read'],
						// 'message' => $message,
						// 'count' => $count,
					// );
				// }
			// }
		// }
		// return $respone;
	// }
	// static public function getMessageSub($id = null) {
		// global $CMS, $DB;
		// $id = empty($id) ? $CMS->input['id_message_sub'] : $id;
		// $DB->query("UPDATE `" . root_table . "fanpage_message` SET `fanpage_message_read`='1' WHERE (`fanpage_message_id`='{$id}');");
		// $sql1 = $DB->query("SELECT * FROM `" . root_table . "fanpage_message` WHERE `fanpage_message_deleted` = 0 AND `fanpage_message_id` = '{$id}' LIMIT 1;");
		// if ($DB->num_rows($sql1) > 0) {
			// $respone['status'] = true;
			// $result1 = $DB->fetch_assoc($sql1);
			// $sql2 = $DB->query("SELECT count(0) as `count` FROM `nh_fanpage_message_sub` WHERE `fanpage_message_id` = '3' AND `fanpage_message_sub_read`=0;");
			// $limit = 10;
			// if ($DB->num_rows($sql2) > 0) {
				// $result2 = $DB->fetch_assoc($sql2);
				// $limit = $result2['count'] > $limit ? $result2['count'] : $limit;
			// }
			// $sql3 = $DB->query("SELECT * FROM `" . root_table . "fanpage_message_sub` WHERE `fanpage_message_id` = '{$result1['fanpage_message_id']}' ORDER BY `fanpage_message_sub_id` DESC LIMIT {$limit};");
			// if ($DB->num_rows($sql3) > 0) {
				// while ($result3 = $DB->fetch_assoc($sql3)) {
					// $DB->query("UPDATE `" . root_table . "fanpage_message_sub` SET `fanpage_message_sub_read`='1' WHERE (`fanpage_message_sub_id`='{$result3['fanpage_message_sub_id']}');");
					// $result1['message'][] = $result3;
				// }
			// }
			// if (count($result1['message']) > 0) {
				// asort($result1['message']);
			// }
			// return $result1;
		// }
		// return false;
	// }
	// static public function sendMessage($id = null, $message = null) {
		// $respone = array('status' => false, 'messages' => '');
		// $id = html_entity_decode($id);
		// if (! is_null($id) && ! is_null($message)) {
			// global $CMS;
			// $info = self::getInfoFanpage($CMS->input['page_id']);
			// self::$token = $info['fanpage_token'];
			// $CMS->api->facebook->fb->setDefaultAccessToken(self::$token);
			// try {
				// $CMS->api->facebook->fb->post('/' . $id . '/messages', array('message' => $message));
				// $respone['status'] = true;
			// } catch(Facebook\Exceptions\FacebookResponseException $e) {
				// $respone['messages'] = 'Graph returned an error: ' . $e->getMessage();
			// } catch(Facebook\Exceptions\FacebookSDKException $e) {
				// $respone['messages'] = 'Facebook SDK returned an error: ' . $e->getMessage();
			// }
		// }
		// return $respone;
	// }
	static public function test() {
		global $CMS;
		// dump($CMS->vars['graph_facebook_token']);die;
		// $CMS->api->facebook->fb->setDefaultAccessToken($CMS->vars['graph_facebook_token']);
		// $CMS->api->facebook->fb->setDefaultAccessToken('EAABj38qCZAFABAP1wBPWCyR7H7OlPAFNnxnMvKhu53Ls1joxZAaTO8DcJ1P5ui89k7QYRt1tT6mA6Yu4j0NnJ2uLosoHcZBGZCVeVGYPj2mvRErdDyM1BVc6oX6LutZCMKwCl78NorMZALhPnXVFJdbanv2cZAtqQjJsie8eaK0PQZDZD');
		// EAABj38qCZAFABALodADLt1eyBq25DiA4RFZBzHF8Nv1mbayhA0075DXzjZAzLvmO0QyMoXyYRoUIwGCJl9XXzUoIKOZAGUZCmXhOTe4XMEgpN9OnWjDIFtH57kev8kjZBLdA2ZANsZADdY0uSkTelNYjgw8bImPj4qgZD
		$CMS->api->facebook->fb->setDefaultAccessToken($CMS->vars['graph_facebook_token']);
		dump($CMS->vars['graph_facebook_token']);die;
		try {
			// 1453910081364679 // me
			// 1792880187396060 // vũ
			// 737112229805661 // duy
			// 1105413686255440 // thắm
			// $request = $CMS->api->facebook->fb->get('/1453910081364679/?fields=online');
			// $request = $CMS->api->facebook->fb->get('/1453910081364679/friends');
			// $data = $request->getGraphNode()->asArray();
			// $data = $request->getGraphEdge()->asArray();
			// dump($data);
			// $request = $CMS->api->facebook->fb->get('/me/accounts');
			// $request = $CMS->api->facebook->fb->get('/1312866342157514?fields=conversations');
			// $request = $CMS->api->facebook->fb->get('/t_mid.$cAATw-J41EkVkPieVmFeDN5y8S47q');
			// $data = $request->getGraphNode()->asArray();
			// $data = $request->getGraphEdge()->asArray();
			// dump($data);die;
			$CMS->api->facebook->fb->post('/t_mid.$cAATw-J41EkVkPieVmFeDN5y8S47q/messages', array('message' => '123456789'));
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			exit('Graph returned an error: ' . $e->getMessage());
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			exit('Facebook SDK returned an error: ' . $e->getMessage());
		}
		die;
	}
}  
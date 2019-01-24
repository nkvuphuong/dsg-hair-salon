<?php

namespace controller;
use \core\ezy;
use \lib\input;

ezy::load_model("addons");
new addons;

class addons {
	
	public function __construct()
	{
		global $CMS, $DB, $tpl;

		// Run init
		\models\addons::init();
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['header_home']}";

        // Main switch
        switch ( $CMS->input['act'] )
        {
			case 'test-sync':
				if ($CMS->input['module'] == 'freshdesk') {
					self::testSyncFreshdesk();
				}
				break;
			case 'sync':
				if ($CMS->input['module'] == 'freshdesk') {
					self::syncFreshdeskCustomer();
				}
				break;
			case 'logs':
				switch ($CMS->input['module']) {
					case 'chatbot_facebook':
						self::logsChatbotFacebook();
						break;
					case 'freshdesk':
						self::logsFreshdesk();
						break;
					default:
						break;
				}
				break;
			case 'setting':
				switch ($CMS->input['module']) {
					case 'chatbot_facebook':
						self::settingChatbotFacebook();
						break;
					case 'freshdesk':
						self::settingFreshdesk();
						break;
					default:
						break;
				}
				break;
        	case "edit_do":
        		self::updateStatus();
        	break;
            default:
            	self::pageDefault();
            break;
        }
	}

	static function pageDefault()
	{
		global $CMS, $tpl;

		// GetListAddons
		// $tpl->listAddons = \models\addons::getListAddons();
		
		// $CMS->api->freshdesk->domain = $CMS->input['fd_domain'];
		// $CMS->api->freshdesk->key = $CMS->input['fd_key'];
		// $CMS->api->freshdesk->connect();
		// $api = $CMS->api->freshdesk->testSync();
		// if (is_array($api) && ! isset($api['code'])) {
		// 	$tpl->freshdesk_connect = true;
		// } else {
		// 	$tpl->freshdesk_connect = false;
		// }
		// Check mobile
		$tpl->tableMobile = \models\addons::checkMobile();
        echo ezy::html();
	}
	static function updateStatus()
	{
		global $CMS;

		$conf_key = $CMS->input['conf_key'];
		$conf_val = $CMS->input['conf_val'];
		$check = \models\addons::updateStatus($conf_key, $conf_val);

		// output ajax
		print $check;exit;
	}
	
	static function settingFreshdesk() {
		global $CMS, $tpl;
		if ($CMS->input['request_method'] == 'post') {
			$CMS->class->language->load("config_general");
			$CMS->config_general->edit();
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=addons&act=setting&module=freshdesk");
		}
		$tpl->fd = false;
		$CMS->api->freshdesk->connect();
		$api = $CMS->api->freshdesk->testSync();
		if (is_array($api) && ! isset($api['code'])) {
			$tpl->fd = true;
		}
		echo ezy::html('setting_freshdesk');
	}
	static function settingChatbotFacebook() {
		global $CMS, $tpl;
		if ($CMS->input['request_method'] == 'post') {
			if ($CMS->input['change_key'] == 1) {
				$_POST['config']['input']['addon_chatbot_facebook_key'] = $CMS->class->random->character(32);
			}
			if ($CMS->config_general->edit()) {
				$_SESSION['chatbot_facebook_status'] = true;
				if ($CMS->input['change_key'] == 1) {
					$CMS->class->logs->key = "chatbot_change_key";
					$CMS->class->logs->insert("chatbot_change_key", 'Chnage key chatbot from <i>' . $CMS->vars['addon_chatbot_facebook_key'] . '</i> to <strong>' . $_POST['config']['input']['addon_chatbot_facebook_key'] .'</strong>');
				}
			} else {
				$_SESSION['chatbot_facebook_status'] = false;
			}
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=addons&act=setting&module=chatbot_facebook");
		}
		$tpl->url = $CMS->vars['parent_domain'] . '/api/autom?key=' . $CMS->vars['addon_chatbot_facebook_key'];
		echo ezy::html('setting_chatbot_facebook');
	}

    /**
     * Mô tả
     */
	public function syncFreshdeskCustomer() {
		global $CMS;
		if ($CMS->class->cache->check('customers_log_from')) {
			$customers_log_from = $CMS->class->cache->load('customers_log_from'); //json_decode($CMS->class->cache->load('customers_log_from'), true); // nkvp - Không cần json_encode/json_decode vì trong lib đã xử lý
		} else {
			$customers_log_from = array();
		}
		if ($CMS->class->cache->check('customers_log_to')) {
			$customers_log_to = $CMS->class->cache->load('customers_log_to');  //json_decode($CMS->class->cache->load('customers_log_to'), true); / nkvp - Không cần json_encode/json_decode vì trong lib đã xử lý
		} else {
			$customers_log_to = array();
		}
		$customers_to = $customers_from = array();
		$CMS->api->freshdesk->connect();
		$customers_fd = $CMS->api->freshdesk->contactsAll();
		foreach ($customers_fd as $customer_fd) {
			if (! $CMS->customer->cusCheckExist($customer_fd['email'])) {
				$data = array(
					'name' => $customer_fd['name'],
					'contact_email' => $customer_fd['email'],
					'address' => $customer_fd['address'],
					'phone' => $customer_fd['phone'],
				);
				$customer = $CMS->customer->social_add($data);
				if ($customer) {
					$customers_log_from[] = "{$customer_fd['email']} (<a href='{$CMS->vars['root_domain']}/?site=customer&act=show&id={$customer['cus_id']}'>{$customer['cus_id']}</a>)";
					$CMS->class->logs->key = "customer_{$customer['cus_id']}";
					$CMS->class->logs->insert(" *Created* customer #{$customer['cus_id']} form freshdesk");
					$CMS->class->cache->save('customers_log_from', $customers_log_from);
				}
			}
			$customers_from[] = $customer_fd['email'];
		}
		$customers_deleted = $CMS->api->freshdesk->contactsAllDeleted();
		foreach ($customers_deleted as $customer) {
			$CMS->customer->deleted($customer['email']);
		}
		$customers_to = $CMS->customer->getEmailNotInArray($customers_from);
		foreach ($customers_to as $customer) {
			$data = array(
				'email' => $customer['cus_email'],
				'name' => $customer['cus_full_name'],
			);
			if (empty($CMS->api->freshdesk->contactsCreate($data)['code'])) {
				$customers_log_to[] = "{$customer['cus_email']} (<a href='{$CMS->vars['root_domain']}/?site=customer&act=show&id={$customer['cus_id']}'>{$customer['cus_id']}</a>)";
				$CMS->class->logs->key = "customer_{$customer['cus_id']}";
				$CMS->class->logs->insert("Updated customer #{$customer['cus_id']} to freshdesk");
				$CMS->class->cache->save('customers_log_to', $customers_log_to);
			}
		}
		$CMS->class->logs->keep_content = 1;
		$CMS->class->logs->key = "freshdesk_sync_customer";
		$CMS->class->logs->insert("freshdesk_sync_customer", json_encode(array('form' => $customers_log_from, 'to' => $customers_log_to)));
		$_SESSION['msg'] = $CMS->lang['fresh_sync_success'];
		$CMS->class->cache->delete('customers_log_to');
		$CMS->class->cache->delete('customers_log_from');
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=addons&act=logs&module=freshdesk");
	}
	static function logsFreshdesk() {
		global $CMS, $tpl;
		$tpl->logs = $CMS->class->logs->listing("freshdesk_sync_customer");
		foreach ($tpl->logs as $k=>$logs) {
			$tpl->logs[$k]['log_time'] = $CMS->class->date->date_format($logs['log_time'], 1);
			$tpl->logs[$k]['log_content'] = json_decode($logs['log_content'], true);
			foreach ($tpl->logs[$k]['log_content']['form'] as $k1=>$v1) {
				$tpl->logs[$k]['log_content']['form'][$k1] = str_replace("&#39;", "'", $v1);
			}
			foreach ($tpl->logs[$k]['log_content']['to'] as $k1=>$v1) {
				$tpl->logs[$k]['log_content']['to'][$k1] = str_replace("&#39;", "'", $v1);
			}
			$tpl->logs[$k]['log_content']['to'] = implode(', ', $tpl->logs[$k]['log_content']['to']);
			$tpl->logs[$k]['log_content']['form'] = implode(', ', $tpl->logs[$k]['log_content']['form']);
		}
		echo ezy::html('logs_freshdesk');
	}
	static function logsChatbotFacebook() {
		global $CMS, $tpl;
		$tpl->logs = $CMS->class->logs->listing("chatbot_change_key");
		echo ezy::html('logs_chatbot');
	}
	static function testSyncFreshdesk() {
		$reponse = array('status' => false);
		global $CMS, $member;
		if (isset($CMS->input['fd_domain']) && isset($CMS->input['fd_key'])) {
			$CMS->api->freshdesk->connect($CMS->input['fd_domain'], $CMS->input['fd_key']);
		} else {
			$CMS->api->freshdesk->connect();
		}
		$api = $CMS->api->freshdesk->testSync();
		if (is_array($api) && ! isset($api['code'])) {
			$reponse['status'] = true;
			$fd_sync_daily = $CMS->vars['fd_sync_daily'] == 1 ? 'checked' : '';
			$reponse['html'] = <<<EOF
<h5>{$CMS->lang['fresh_sync']}</h5>
	<div class="form-group row">
		<label class="col-xl-2 form-control-label">{$CMS->lang['fresh_daily']}</label>
		<div class="col-xl-10 col-lg-12 col-sm-12 col-xs-12 checkbox-toggle">
			<input type="checkbox" id="fd_sync_daily" onchange="if($(this).is(':checked')){\$('#config_input_fd_sync_daily').val(1);}else{\$('#config_input_fd_sync_daily').val(0);}" {$fd_sync_daily}>
			<label for="fd_sync_daily"></label>
			<input type="hidden" id="config_input_fd_sync_daily" name="config[input][fd_sync_daily]" value="{$CMS->vars['fd_sync_daily']}">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-xl-2 form-control-label">{$CMS->lang['fresh_handmade']}</label>
		<div class="col-xl-10 col-lg-12 col-sm-12 col-xs-12">
			<a href="javascript:void(0)" onclick="alert_sync('{$CMS->vars['root_domain']}/?site=addons&act=sync&module=freshdesk')"><button type="button" class="btn btn-sm btn-info">{$CMS->lang['fresh_sync_now']}</button></a>
		</div>
	</div>
EOF;
			$CMS->class->logs->key = "customer_{$member['user_id']}";
			$CMS->class->logs->insert("Customer #{$member['cus_id']} test sync freshdesk true");
		} else {
			$CMS->class->logs->key = "customer_{$member['user_id']}";
			$CMS->class->logs->insert("Customer #{$member['cus_id']} test sync freshdesk false");
		}
		exit(json_encode($reponse));
	}
}

?>
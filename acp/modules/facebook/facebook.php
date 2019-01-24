<?php
namespace controller;
use \core\ezy;
use \lib\input;

ezy::load_model('facebook');

new facebook;
class facebook {
	public function __construct() {
		global $CMS, $DB, $tpl;
		if ($CMS->vars['addon_graph_facebook_enable'] != 1) {
			header("location: {$CMS->vars['root_domain']}/?site=addons");
			exit();
		}
		$CMS->vars['graph_facebook_id'] = '189442174932011';
		$CMS->vars['graph_facebook_secret'] = 'd485870dbbc7272789302f8acdb2fcc8';
		$api = $CMS->api->facebook->serverConnection();
		if ($api['status'] === 'not_login') {
			header("location: {$api['messages']}");
			exit();
		}
		if (! empty($CMS->input['code'])) {
			header("location: {$CMS->vars['root_domain']}/?site=facebook&act=fanpage");
			exit();
		}
		
		\models\facebook::init();
		
		$CMS->class->language->load('facebook');
		
		$CMS->core->page_title = "-> {$CMS->lang['header_home']}";

        // Main switch
        switch ($CMS->input['act']) {
			case 'test':
				self::test();
				break;
			case 'help':
				self::help();
				break;
			case 'message':
				self::message();
				break;
			case 'logout':
				self::logout();
				break;
			case 'fanpage':
				self::fanpage();
				break;
            default:
            	self::pageDefault();
				break;
        }
	}
	static function pageDefault() {
	}
	static function test() {
		global $CMS;
		dump(\models\facebook::fanpageMessage());
		die("test api facebook");
	}
	static function message() {
		global $CMS, $tpl;
		if (\models\facebook::checkFanpage($CMS->input['page_id'])) {
			echo ezy::html('message');
		}
	}
	static function fanpage() {
		global $CMS,$tpl;
		$api = \models\facebook::getListFanpage();
		if ($api['status'] === true) {
			\models\facebook::saveTokenFanpage($api['messages']);
			$tpl->listFanpages = $api['messages'];
		}
		echo ezy::html('fanpage');
	}
	static function logout() {
		$respone = array('status'=>'error');
		global $CMS;
		$api = \models\facebook::getListFanpage();
		if ($api['status'] === true) {
			\models\facebook::deletedTokenFanpage($api['messages']);
		}
		unset($_SESSION['graph_facebook_token']);
		$_POST['config']['input']['graph_facebook_token'] = '';
		$CMS->config_general->edit();
		$respone['status'] = 'success';
		exit(json_encode($respone));
	}
	static function help() {
		$str = <<<EOF
<div class="modal-header">
	<h3 class="modal-title">Hướng dẫn sử dụng</h3>
</div>
<div class="modal-body">
	<p>Liên hệ admin.</p>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
EOF;
		exit($str);
	}
	
	
	/***************************************************************************************
	static function connectPage() {
		global $CMS, $tpl;
		
		// \models\fanpage::fanpageMessage();
		
		$tpl->message = \models\fanpage::getMessage();
		echo ezy::html('message');
	}
	static function ajax() {
		global $CMS, $tpl;
		$reponse = array('status' => false);
		switch ($CMS->input['sub']) {
			case 'get_message_sub':
				if ($CMS->input['id_message_sub'] > 0) {
					if ($CMS->input['request_method'] == 'post') {
						\models\fanpage::sendMessage($CMS->input['fb_id'], $CMS->input['fb_message']);
						\models\fanpage::fanpageMessage();
					}
					$messageSub = \models\fanpage::getMessageSub();
					$messages = \models\fanpage::getMessage();
					$chat_list_in = '';
					foreach ($messages['messages'] as $message) {
						$read = $message['read'] == 0 ? 'online' : '';
						$active = $message['id'] == $CMS->input['id_message_sub'] ? 'active' : '';
						$chat_list_in .= <<<EOF
<div class="chat-list-item {$read} {$active}" id="{$message['id']}">
	<div class="chat-list-item-photo">
		<img src="{$message['fb_user_avartar']}">
	</div>
	<div class="chat-list-item-header">
		<div class="chat-list-item-name">
			<span class="name">{$message['fb_user_name']}</span>
		</div>
		<div class="chat-list-item-date">{$CMS->class->date->date_format($message['time'], 1)}</div>
	</div>
	<div class="chat-list-item-cont">
EOF;
						if (! empty($message['message'])) {
							$chat_list_in .= <<<EOF
		<div class="chat-list-item-txt writing">
			<div class="icon">
				<i class="font-icon font-icon-pencil-thin"></i>
			</div>
			{$message['message']}
		</div>
EOF;
						}
						if ($message['count'] > 0) {
							$chat_list_in .= <<<EOF
		<div class="chat-list-item-count">{$message['count']}</div>
EOF;
						}
						$chat_list_in .= <<<EOF
	</div>
</div>
EOF;
					}
					$read = $messages['messages'][$messageSub['fanpage_message_id']]['read'] == 0 ? 'online' : '';
					$chat_list_info = <<<EOF
<div class="chat-list-in">
	<section class="chat-user-info chat-list-item {$read}">
		<div class="chat-list-item-photo">
			<img src="{$messageSub['fanpage_message_fb_user_avartar']}">
		</div>
		<div class="chat-list-item-header">
			<div class="chat-list-item-name">
				<span class="name">{$messageSub['fanpage_message_fb_user_name']}</span>
			</div>
		</div>
		<div class="chat-list-item-cont">
			<div class="chat-list-item-txt writing">
				{$messages['messages'][$messageSub['fanpage_message_id']]['message']}
			</div>
		</div>
	</section>
	<section class="chat-settings">
		<div class="item">
			<span class="font-icon fa fa-eyedropper"></span>
				Change color
		</div>
		<div class="item change-bg-color">
			<div class="checkbox-bird blue">
				<input name="color" type="radio" id="color-blue" checked/>
				<label for="color-blue" data-color="blue"></label>
			</div>
			<div class="checkbox-bird red">
				<input name="color" type="radio" id="color-red"/>
				<label for="color-red" data-color="red"></label>
			</div>
			<div class="checkbox-bird green">
				<input name="color" type="radio" id="color-green"/>
				<label for="color-green" data-color="green"></label>
			</div>
			<div class="checkbox-bird pink">
				<input name="color" type="radio" id="color-pink"/>
				<label for="color-pink" data-color="pink"></label>
			</div>
			<div class="checkbox-bird blue-light">
				<input name="color" type="radio" id="color-blue-light"/>
				<label for="color-blue-light" data-color="blue-light"></label>
			</div>
			<div class="checkbox-bird lime">
				<input name="color" type="radio" id="color-lime"/>
				<label for="color-lime" data-color="lime"></label>
			</div>
			<div class="checkbox-bird orange">
				<input name="color" type="radio" id="color-orange"/>
				<label for="color-orange" data-color="orange"></label>
			</div>
			<div class="checkbox-bird pink-dark">
				<input name="color" type="radio" id="color-pink-dark"/>
				<label for="color-pink-dark" data-color="pink-dark"></label>
			</div>
			<div class="checkbox-bird purple">
				<input name="color" type="radio" id="color-purple"/>
				<label for="color-purple" data-color="purple"></label>
			</div>
		</div>
	</section>
	<section class="chat-profiles">
		<header>Profile on facebook</header>
		<a href="http://facebook.com/{$messageSub['fanpage_message_fb_user_id']}">http://facebook.com/{$messageSub['fanpage_message_fb_user_name']}</a>
	</section>
</div>
<script>
	$(document).ready(function() {
		$('.chat-settings .change-bg-color label').on('click', function() {
			var color = $(this).data('color');
			$('.messenger-message-container.from').each(function() {
				$(this).removeClass(function (index, css) {
					return (css.match (/(^|\s)bg-\S+/g) || []).join(' ');
				});

				$(this).addClass('bg-' + color);
			});
		});
	});
</script>
EOF;
					$chat_area = <<<EOF
<div class="chat-area-in">
	<div class="chat-area-header">
		<div class="chat-list-item {$read}">
			<div class="chat-list-item-name">
				<span class="name">{$messageSub['fanpage_message_fb_user_name']}</span>
			</div>
			<div class="chat-list-item-txt writing">{$CMS->class->date->date_format($messages['messages'][$messageSub['fanpage_message_id']]['time'], 1)}</div>
		</div>
	</div>
	<div class="chat-dialog-area scrollable-block">
		<div class="messenger-dialog-area">
EOF;
					foreach ($messageSub['message'] as $m_mid) {
						if ($m_mid['fanpage_message_sub_fb_user_id'] == $messages['messages'][$messageSub['fanpage_message_id']]['fb_user_id']) {
							$chat_area .= <<<EOF
			<div class="messenger-message-container from bg-blue">
				<div class="avatar">
					<img src="{$messages['messages'][$messageSub['fanpage_message_id']]['fb_user_avartar']}">
				</div>
				<div class="messages">
					<ul>
						<li>
							<div class="message">
								<div style="min-width: 250px;">
									{$m_mid['fanpage_message_sub_fb_message']}
								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
EOF;
						} else {
							$chat_area .= <<<EOF
			<div class="messenger-message-container">
				<div class="messages">
					<ul>
						<li>
							<div class="message">
								<div style="min-width: 250px;">
									{$m_mid['fanpage_message_sub_fb_message']}      
								</div>
							</div>
						</li>
					</ul>
				</div>
				<div class="avatar">
					<img src="assets/img/avatar-2-32.png">
				</div>
			</div>
EOF;
						}
					}
					$chat_area .= <<<EOF
			
		</div>
	</div>
	<div class="chat-area-bottom">
		<form class="write-message">
			<div class="form-group">
				<input id="fb_id" type="hidden" value="{$messageSub['fanpage_message_fb_id']}">
				<input id="id_message_sub" type="hidden" value="{$CMS->input['id_message_sub']}">
				<textarea id="fb_message" rows="1" class="form-control" placeholder="Type a message"></textarea>
				<div class="dropdown dropdown-typical attach">
					<button onclick="sendMessage()" type="button" class="btn btn-primary btn-xs"><i class="fa fa-send"></i></button>
				</div>
			</div>
		</form>
	</div>
</div>
EOF;
					// $reponse['chat_list_in'] = $chat_list_in;
					// $reponse['chat_list_info'] = $chat_list_info;
					// $reponse['chat_area'] = $chat_area;
					// $reponse['status'] = true;
				}
				break;
			default:
				break;
		}
		exit(json_encode($reponse));
	}
	static function test() {
		// \models\fanpage::test();
		exit('test');
	}
	***************************************************************************************/
}
?>
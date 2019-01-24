<?php
$CMS->setting = new classSetting;

class classSetting{
	public function add() {
		global $CMS, $DB, $member;
		switch ($CMS->input) {
			default:
				$cam_pin=(isset($CMS->input['cam']['pin'])&&$CMS->input['cam']['pin']=='on')?'1':'0';
				$cam_hide=(isset($CMS->input['cam']['hide'])&&$CMS->input['cam']['hide']=='on')?'1':'0';
				$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$member['cus_id']} AND `key`='cam_pin'");
				if ($DB->num_rows()>0) {
					$id=$DB->fetch_array();
					$DB->query("UPDATE `".root_table."customer_config` SET `value`='{$cam_pin}' WHERE `id`={$id['id']}");
				} else {
					$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`value`) VALUES ({$member['cus_id']},'".time()."','cam_pin','{$cam_pin}')");
				}
				$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$member['cus_id']} AND `key`='cam_hide'");
				if ($DB->num_rows()>0) {
					$id=$DB->fetch_array();
					$DB->query("UPDATE `".root_table."customer_config` SET `value`='{$cam_hide}' WHERE `id`={$id['id']}");
				} else {
					$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`value`) VALUES ({$member['cus_id']},'".time()."','cam_hide','{$cam_hide}')");
				}
				$msg=$CMS->lang['setting_cam_text'];
				break;
			case isset($CMS->input['fb']):
				$fb_url=$CMS->input['fb']['url'];
				if(!preg_match('/^(https?:\/\/){0,1}(www\.){0,1}facebook\.com/',$fb_url)) {
					$CMS->errormsg="{$CMS->lang['setting_fb_url_err']}";
					return FALSE;
				}
				$fb_url=substr($fb_url,0,1000);
				$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$member['cus_id']} AND `key`='fb_url'");
				if ($DB->num_rows()>0) {
					$id=$DB->fetch_array();
					$DB->query("UPDATE `".root_table."customer_config` SET `value`='{$fb_url}' WHERE `id`={$id['id']}");
				} else {
					$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`value`) VALUES ({$member['cus_id']},'".time()."','fb_url','{$fb_url}')");
				}
				$msg=$CMS->lang['setting_fb_text'];
				break;
			case isset($CMS->input['bank']):
				$bank_card=$CMS->input['bank']['card_number'];
				$bank_name=$CMS->input['bank']['fullname'];
				if (empty($bank_card)&& !(bool)preg_match("/^[0-9]+$/",$bank_card)) {
					$CMS->errormsg="{$CMS->lang['setting_bank_number_err']}";
					return false;
				}
				if (empty($bank_name)) {
					$CMS->errormsg="{$CMS->lang['setting_req_err']}";
					return false;
				}
				$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$member['cus_id']} AND `key`='bank_card'");
				if ($DB->num_rows()>0) {
					$id=$DB->fetch_array();
					$DB->query("UPDATE `".root_table."customer_config` SET `value`='{$bank_card}' WHERE `id`={$id['id']}");
				} else {
					$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`value`) VALUES ({$member['cus_id']},'".time()."','bank_card','{$bank_card}')");
				}
				$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$member['cus_id']} AND `key`='bank_name'");
				if ($DB->num_rows()>0) {
					$id=$DB->fetch_array();
					$DB->query("UPDATE `".root_table."customer_config` SET `value`='{$bank_name}' WHERE `id`={$id['id']}");
				} else {
					$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`value`) VALUES ({$member['cus_id']},'".time()."','bank_name','{$bank_name}')");
				}
				$msg=$CMS->lang['setting_bank_text'];
				break;
			case isset($CMS->input['page']):
				$privacy_title=$CMS->input['page']['privacy_title'];
				$terms_title=$CMS->input['page']['terms_title'];
				$support_title=$CMS->input['page']['support_title'];
				$privacy_content=$CMS->input['page']['privacy_content'];
				$terms_content=$CMS->input['page']['terms_content'];
				$support_content=$CMS->input['page']['support_content'];
				if (empty($privacy_title)||empty($terms_title)||empty($support_title)) {
					$CMS->errormsg="{$CMS->lang['setting_title_err']}";
					return false;
				}
				if (!(bool)preg_match("/^[0-9a-zA-Z-_ ]+$/",$privacy_title)||!(bool)preg_match("/^[0-9a-zA-Z-_ ]+$/",$terms_title)||!(bool)preg_match("/^[0-9a-zA-Z-_ ]+$/",$support_title)) {
					$CMS->errormsg = "{$CMS->lang['setting_title_incomplete']}";
					return false; 	
				}
				$privacy_content=substr($privacy_content,0,65000);
				$terms_content=substr($terms_content,0,65000);
				$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$member['cus_id']} AND `key`='page_privacy'");
				if ($DB->num_rows()>0) {
					$id=$DB->fetch_array();
					$DB->query("UPDATE `".root_table."customer_config` SET `title`='{$privacy_title}',`content`='{$privacy_content}' WHERE `id`={$id['id']}");
				} else {
					$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`title`,`content`) VALUES ({$member['cus_id']},'".time()."','page_privacy','{$privacy_title}','{$privacy_content}')");
				}
				$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$member['cus_id']} AND `key`='page_terms'");
				if ($DB->num_rows()>0) {
					$id=$DB->fetch_array();
					$DB->query("UPDATE `".root_table."customer_config` SET `title`='{$terms_title}',`content`='{$terms_content}' WHERE `id`={$id['id']}");
				} else {
					$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`title`,`content`) VALUES ({$member['cus_id']},'".time()."','page_terms','{$terms_title}','{$terms_content}')");
				}
				$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$member['cus_id']} AND `key`='page_support'");
				if ($DB->num_rows()>0) {
					$id=$DB->fetch_array();
					$DB->query("UPDATE `".root_table."customer_config` SET `title`='{$support_title}',`content`='{$support_content}' WHERE `id`={$id['id']}");
				} else {
					$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`title`,`content`) VALUES ({$member['cus_id']},'".time()."','page_support','{$support_title}','{$support_content}')");
				}
				$msg=$CMS->lang['setting_page_text'];
				break;
			case isset($CMS->input['email']):
				$timeline=$CMS->input['email']['timeline'];
				$title=$CMS->input['email']['title'];
				$content=$CMS->input['email']['content'];
				if (empty($title)) {
					$CMS->errormsg = "{$CMS->lang['setting_email_title_err']}";
					return false;
				}
				$timeline=in_array($timeline,array('12','24','36','48'))?$timeline:'12';
				$content=substr($content,0,65000);
				$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$member['cus_id']} AND `key`='email_timeline'");
				if ($DB->num_rows()>0) {
					$id=$DB->fetch_array();
					$DB->query("UPDATE `".root_table."customer_config` SET `value`='{$timeline}' WHERE `id`={$id['id']}");
				} else {
					$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`value`) VALUES ({$member['cus_id']},'".time()."','email_timeline','{$timeline}')");
				}
				$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$member['cus_id']} AND `key`='email_info'");
				if ($DB->num_rows()>0) {
					$id=$DB->fetch_array();
					$DB->query("UPDATE `".root_table."customer_config` SET `title`='{$title}',`content`='{$content}' WHERE `id`={$id['id']}");
				} else {
					$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`title`,`content`) VALUES ({$member['cus_id']},'".time()."','email_info','{$title}','{$content}')");
				}
				$msg=$CMS->lang['setting_email_text'];
				break;
			case isset($CMS->input['google']):
				$analytics=$CMS->class->editor->input(str_replace("'",'"',$_POST['google']['analytics']),'text');
				$tag=$CMS->class->editor->input(str_replace("'",'"',$_POST['google']['analytics']),'text');
				if (empty($analytics)) {
					$CMS->errormsg = "{$CMS->lang['setting_google_analytics_err']}";
					return false;
				}
				if (empty($tag)) {
					$CMS->errormsg = "{$CMS->lang['setting_google_tag_err']}";
					return false;
				}
				$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$member['cus_id']} AND `key`='google_analytics'");
				if ($DB->num_rows()>0) {
					$id=$DB->fetch_array();
					$DB->query("UPDATE `".root_table."customer_config` SET `content`='{$analytics}' WHERE `id`={$id['id']}");
				} else {
					$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`content`) VALUES ({$member['cus_id']},'".time()."','google_analytics','{$analytics}')");
				}
				$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$member['cus_id']} AND `key`='google_tag'");
				if ($DB->num_rows()>0) {
					$id=$DB->fetch_array();
					$DB->query("UPDATE `".root_table."customer_config` SET `content`='{$tag}' WHERE `id`={$id['id']}");
				} else {
					$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`content`) VALUES ({$member['cus_id']},'".time()."','google_tag','{$tag}')");
				}
				$msg=$CMS->lang['setting_google_text'];
				break;
			case isset($CMS->input['key']):
				$arr='';
				foreach($_POST['key'] as $k=>$v) {
					if (!(bool)preg_match("/^[0-9a-zA-Z-_]+$/",$v['key'])) {
						$CMS->errormsg = "{$CMS->lang['setting_key_key_err']}";
						return false; 	
					} else {
						$arr[$k]=array('key'=>$v['key'],'value'=>$CMS->class->editor->input($v['value'],'text'));
					}
				}
				foreach ($arr as $k=>$v) {						
					$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$member['cus_id']} AND `deleted`=0 AND `key`='cus_key' AND `title`='{$v['key']}'");
					if ($DB->num_rows()>0) {
						$id=$DB->fetch_array();
						$DB->query("UPDATE `".root_table."customer_config` SET `value`='{$v['value']}' WHERE `id`='{$id['id']}'");
					} else {
						$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`title`,`value`) VALUES ({$member['cus_id']},'".time()."','cus_key','{$v['key']}','{$v['value']}')");
					}
				}
				$msg=$CMS->lang['setting_key_text'];
				break;
			case isset($CMS->input['shopify']):
				if (!(bool)preg_match("/^[0-9a-zA-Z-_]+$/",$CMS->input['shopify'])||empty($CMS->input['shopify_id'])) {
					$CMS->errormsg = "{$CMS->lang['setting_shopify_error']}";
					return false;
				}
				$store=$CMS->input['shopify'];
				$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$member['cus_id']} AND `deleted`=0 AND `key`='cus_store_shopify' AND `title`='{$CMS->input['shopify_id']}'");
				if ($DB->num_rows()>0) {
					$id=$DB->fetch_array();
					$DB->query("UPDATE `".root_table."customer_config` SET `value`='{$store}' WHERE `id`='{$id['id']}'");
				} else {
					$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`title`,`value`) VALUES ({$member['cus_id']},'".time()."','cus_store_shopify','{$CMS->input['shopify_id']}','{$store}')");
				}
				break;
		}
		$CMS->errormsg = $CMS->lang['setting_success'].'<strong>'.$msg.'</strong>';
		return true;
	}
	public function acp_add($cus_id=null) {
		if (!empty($cus_id)) {
			global $CMS, $DB, $member;
			switch ($CMS->input) {
				default:
					$cam_pin=(isset($CMS->input['cam']['pin'])&&$CMS->input['cam']['pin']=='on')?'1':'0';
					$cam_hide=(isset($CMS->input['cam']['hide'])&&$CMS->input['cam']['hide']=='on')?'1':'0';
					$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$cus_id} AND `key`='cam_pin'");
					if ($DB->num_rows()>0) {
						$id=$DB->fetch_array();
						$DB->query("UPDATE `".root_table."customer_config` SET `value`='{$cam_pin}' WHERE `id`={$id['id']}");
					} else {
						$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`value`) VALUES ({$cus_id},'".time()."','cam_pin','{$cam_pin}')");
					}
					$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$cus_id} AND `key`='cam_hide'");
					if ($DB->num_rows()>0) {
						$id=$DB->fetch_array();
						$DB->query("UPDATE `".root_table."customer_config` SET `value`='{$cam_hide}' WHERE `id`={$id['id']}");
					} else {
						$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`value`) VALUES ({$cus_id},'".time()."','cam_hide','{$cam_hide}')");
					}
					$msg=$CMS->lang['setting_cam_text'];
					break;
				case isset($CMS->input['fb']):
					$fb_url=$CMS->input['fb']['url'];
					if(!preg_match('/^(https?:\/\/){0,1}(www\.){0,1}facebook\.com/',$fb_url)) {
						$CMS->errormsg="{$CMS->lang['setting_fb_url_err']}";
						return FALSE;
					}
					$fb_url=substr($fb_url,0,1000);
					$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$cus_id} AND `key`='fb_url'");
					if ($DB->num_rows()>0) {
						$id=$DB->fetch_array();
						$DB->query("UPDATE `".root_table."customer_config` SET `value`='{$fb_url}' WHERE `id`={$id['id']}");
					} else {
						$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`value`) VALUES ({$cus_id},'".time()."','fb_url','{$fb_url}')");
					}
					$msg=$CMS->lang['setting_fb_text'];
					break;
				case isset($CMS->input['bank']):
					$bank_card=$CMS->input['bank']['card_number'];
					$bank_name=$CMS->input['bank']['fullname'];
					if (empty($bank_card)&& !(bool)preg_match("/^[0-9]+$/",$bank_card)) {
						$CMS->errormsg="{$CMS->lang['setting_bank_number_err']}";
						return false;
					}
					if (empty($bank_name)) {
						$CMS->errormsg="{$CMS->lang['setting_req_err']}";
						return false;
					}
					$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$cus_id} AND `key`='bank_card'");
					if ($DB->num_rows()>0) {
						$id=$DB->fetch_array();
						$DB->query("UPDATE `".root_table."customer_config` SET `value`='{$bank_card}' WHERE `id`={$id['id']}");
					} else {
						$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`value`) VALUES ({$cus_id},'".time()."','bank_card','{$bank_card}')");
					}
					$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$cus_id} AND `key`='bank_name'");
					if ($DB->num_rows()>0) {
						$id=$DB->fetch_array();
						$DB->query("UPDATE `".root_table."customer_config` SET `value`='{$bank_name}' WHERE `id`={$id['id']}");
					} else {
						$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`value`) VALUES ({$cus_id},'".time()."','bank_name','{$bank_name}')");
					}
					$msg=$CMS->lang['setting_bank_text'];
					break;
				case isset($CMS->input['email']):
					$timeline=$CMS->input['email']['timeline'];
					$title=$CMS->input['email']['title'];
					$content=$CMS->input['email']['content'];
					if (empty($title)) {
						$CMS->errormsg = "{$CMS->lang['setting_email_title_err']}";
						return false;
					}
					$timeline=in_array($timeline,array('12','24','36','48'))?$timeline:'12';
					$content=substr($content,0,65000);
					$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$cus_id} AND `key`='email_timeline'");
					if ($DB->num_rows()>0) {
						$id=$DB->fetch_array();
						$DB->query("UPDATE `".root_table."customer_config` SET `value`='{$timeline}' WHERE `id`={$id['id']}");
					} else {
						$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`value`) VALUES ({$cus_id},'".time()."','email_timeline','{$timeline}')");
					}
					$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$cus_id} AND `key`='email_info'");
					if ($DB->num_rows()>0) {
						$id=$DB->fetch_array();
						$DB->query("UPDATE `".root_table."customer_config` SET `title`='{$title}',`content`='{$content}' WHERE `id`={$id['id']}");
					} else {
						$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`title`,`content`) VALUES ({$cus_id},'".time()."','email_info','{$title}','{$content}')");
					}
					$msg=$CMS->lang['setting_email_text'];
					break;
				case isset($CMS->input['google']):
					$analytics=$CMS->class->editor->input(str_replace("'",'"',$_POST['google']['analytics']),'text');
					$tag=$CMS->class->editor->input(str_replace("'",'"',$_POST['google']['tag']),'text');
					if (empty($analytics)) {
						$CMS->errormsg = "{$CMS->lang['setting_google_analytics_err']}";
						return false;
					}
					if (empty($tag)) {
						$CMS->errormsg = "{$CMS->lang['setting_google_tag_err']}";
						return false;
					}
					$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$cus_id} AND `key`='google_analytics'");
					if ($DB->num_rows()>0) {
						$id=$DB->fetch_array();
						$DB->query("UPDATE `".root_table."customer_config` SET `content`='{$analytics}' WHERE `id`={$id['id']}");
					} else {
						$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`content`) VALUES ({$cus_id},'".time()."','google_analytics','{$analytics}')");
					}
					$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$cus_id} AND `key`='google_tag'");
					if ($DB->num_rows()>0) {
						$id=$DB->fetch_array();
						$DB->query("UPDATE `".root_table."customer_config` SET `content`='{$tag}' WHERE `id`={$id['id']}");
					} else {
						$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`content`) VALUES ({$cus_id},'".time()."','google_tag','{$tag}')");
					}
					$msg=$CMS->lang['setting_google_text'];
					break;
				case isset($CMS->input['page']['privacy_title']):
				case isset($CMS->input['page']['privacy_content']):
					$privacy_title=$CMS->input['page']['privacy_title'];
					$privacy_content=$CMS->input['page']['privacy_content'];
					if (empty($privacy_title)) {
						$CMS->errormsg="{$CMS->lang['setting_title_err']}";
						return false;
					}
					if (!(bool)preg_match("/^[0-9a-zA-Z-_ ]+$/",$privacy_title)) {
						$CMS->errormsg = "{$CMS->lang['setting_title_incomplete']}";
						return false; 	
					}
					$privacy_content=substr($privacy_content,0,65000);
					$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$cus_id} AND `key`='page_privacy'");
					if ($DB->num_rows()>0) {
						$id=$DB->fetch_array();
						$DB->query("UPDATE `".root_table."customer_config` SET `title`='{$privacy_title}',`content`='{$privacy_content}' WHERE `id`={$id['id']}");
					} else {
						$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`title`,`content`) VALUES ({$cus_id},'".time()."','page_privacy','{$privacy_title}','{$privacy_content}')");
					}
					$msg=$CMS->lang['setting_page_text'];
					break;
				case isset($CMS->input['page']['terms_title']):
				case isset($CMS->input['page']['terms_content']):
					$terms_title=$CMS->input['page']['terms_title'];
					$terms_content=$CMS->input['page']['terms_content'];
					if (empty($terms_title)) {
						$CMS->errormsg="{$CMS->lang['setting_title_err']}";
						return false;
					}
					if (!(bool)preg_match("/^[0-9a-zA-Z-_ ]+$/",$terms_title)) {
						$CMS->errormsg = "{$CMS->lang['setting_title_incomplete']}";
						return false; 	
					}
					$terms_content=substr($terms_content,0,65000);
					$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$cus_id} AND `key`='page_terms'");
					if ($DB->num_rows()>0) {
						$id=$DB->fetch_array();
						$DB->query("UPDATE `".root_table."customer_config` SET `title`='{$terms_title}',`content`='{$terms_content}' WHERE `id`={$id['id']}");
					} else {
						$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`title`,`content`) VALUES ({$cus_id},'".time()."','page_terms','{$terms_title}','{$terms_content}')");
					}
					$msg=$CMS->lang['setting_page_text'];
					break;
				case isset($CMS->input['page']['support_title']):
				case isset($CMS->input['page']['support_content']):
					$support_title=$CMS->input['page']['support_title'];
					$support_content=$CMS->input['page']['support_content'];
					if (empty($support_title)) {
						$CMS->errormsg="{$CMS->lang['setting_title_err']}";
						return false;
					}
					if (!(bool)preg_match("/^[0-9a-zA-Z-_ ]+$/",$support_title)) {
						$CMS->errormsg = "{$CMS->lang['setting_title_incomplete']}";
						return false; 	
					}
					$support_content=substr($support_content,0,65000);
					$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$cus_id} AND `key`='page_support'");
					if ($DB->num_rows()>0) {
						$id=$DB->fetch_array();
						$DB->query("UPDATE `".root_table."customer_config` SET `title`='{$support_title}',`content`='{$support_content}' WHERE `id`={$id['id']}");
					} else {
						$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`title`,`content`) VALUES ({$cus_id},'".time()."','page_support','{$support_title}','{$support_content}')");
					}
					$msg=$CMS->lang['setting_page_text'];
					break;
				case isset($CMS->input['key']):
					$arr='';
					foreach($_POST['key'] as $k=>$v) {
						if (!(bool)preg_match("/^[0-9a-zA-Z-_]+$/",$v['key'])) {
							$CMS->errormsg = "{$CMS->lang['setting_key_key_err']}";
							return false; 	
						} else {
							$arr[$k]=array('key'=>$v['key'],'value'=>$CMS->class->editor->input($v['value'],'text'));
						}
					}
					foreach ($arr as $k=>$v) {						
						$DB->query("SELECT `id` FROM `".root_table."customer_config` WHERE `cus_id`={$cus_id} AND `deleted`=0 AND `key`='cus_key' AND `title`='{$v['key']}'");
						if ($DB->num_rows()>0) {
							$id=$DB->fetch_array();
							$DB->query("UPDATE `".root_table."customer_config` SET `value`='{$v['value']}' WHERE `id`='{$id['id']}'");
						} else {
							$DB->query("INSERT INTO `".root_table."customer_config` (`cus_id`,`time`,`key`,`title`,`value`) VALUES ({$cus_id},'".time()."','cus_key','{$v['key']}','{$v['value']}')");
						}
					}
					$msg=$CMS->lang['setting_key_text'];
					break;
			}
			$CMS->errormsg = $CMS->lang['setting_success'].' <strong>'.$msg.'</strong>';
			return true;
		}
		return false;
	}
	public function info($cus_id=null,$key=null) {
		global $CMS, $DB, $member;
		if (is_null($cus_id)) {
			$where="WHERE `cus_id`={$member['cus_id']}";
		} else {
			$where="WHERE `cus_id`={$cus_id}";
		}
		if (!is_null($key)) {
			$where.=" AND `key`='{$key}'";
		}
		$where.=" AND `deleted`=0";
		$DB->query("SELECT * FROM `".root_table."customer_config` {$where} ORDER BY `id` DESC");
		if ($DB->num_rows()>0) {
			$arr=array('page'=>array());
			while ($result=$DB->fetch_array()) {
				switch ($result['key']) {
					default:
						break;
					case 'cam_pin':
						$arr['cam']['pin']=$result['value'];
						break;
					case 'cam_hide':
						$arr['cam']['hide']=$result['value'];
						break;
					case 'fb_url':
						$arr['fb']['url']=$result['value'];
						break;
					case 'bank_card':
						$arr['bank']['card']=$result['value'];
						break;
					case 'bank_name':
						$arr['bank']['name']=$result['value'];
						break;
					case 'page_privacy':
						$arr['page']['privacy']['title']=$result['title'];
						$arr['page']['privacy']['content']=html_entity_decode($result['content']);
						break;
					case 'page_terms':
						$arr['page']['terms']['title']=$result['title'];
						$arr['page']['terms']['content']=html_entity_decode($result['content']);
						break;
					case 'page_support':
						$arr['page']['support']['title']=$result['title'];
						$arr['page']['support']['content']=html_entity_decode($result['content']);
						break;
					case 'email_timeline':
						$arr['email']['timeline']=$result['value'];
						break;
					case 'email_info':
						$arr['email']['title']=$result['title'];
						$arr['email']['content']=html_entity_decode($result['content']);
						break;
					case 'google_analytics':
						$arr['google']['analytics']=html_entity_decode($result['content']);
						break;
					case 'google_tag':
						$arr['google']['tag']=html_entity_decode($result['content']);
						break;
					case 'cus_key':
						$arr['key'][$result['id']]['key']=$result['title'];
						$arr['key'][$result['id']]['value']=$result['value'];
						break;
					case 'cus_store_shopify':
						$arr['store_shopify'][$result['title']]=$result['value'];
						break;
				}
			}
			return $arr;
		}
		return array();
	}
	public function del($id=null) {
		if (!empty($id)) {
			global $CMS, $DB, $member;
			$id=(int)$id;
			$DB->query("UPDATE `".root_table."customer_config` SET `deleted` = 1 WHERE `cus_id`='{$member['cus_id']}' AND `id`='{$id}'");
			return true;
		}
		return false;
	}
	public function delShopify($key=null,$value=null) {
		if (!empty($key)&& !empty($value)) {
			global $CMS, $DB, $member;
			$id=(int)$id;
			$DB->query("UPDATE `".root_table."customer_config` SET `deleted` = 1 WHERE `cus_id`='{$member['cus_id']}' AND `title`='{$key}' AND `value`='{$value}'");
			return true;
		}
		return false;
	}
	public function acp_del($cus_id=null,$id=null) {
		if (!empty($cus_id)&& !empty($id)) {
			global $CMS, $DB, $member;
			$id=(int)$id;
			$DB->query("UPDATE `".root_table."customer_config` SET `deleted` = 1 WHERE `cus_id`='{$cus_id}' AND `id`='{$id}'");
			return true;
		}
		return false;
	}
	public function checkStoreShopify($store=null) {
		global $CMS, $DB, $member;
		$DB->query("SELECT * FROM `".root_table."customer_config` WHERE `deleted`=0 AND `key`='cus_store_shopify' AND `value`='{$store}'");
		if ($DB->num_rows()>0) {
			return $DB->fetch_array();
		}
		return array();
	}
}
?>
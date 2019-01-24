<?php
if (!defined('IN_ROOT')) exit();

$freshdesk = new Freshdesk;
$freshdesk->autorun();

class Freshdesk{
	public $html;
	public function autorun(){
		global $CMS, $DB, $member;
		if ($CMS->vars['addon_fresh_desk_enable'] != 1) {
			exit("Fresh Desk not enable");
		}
		$CMS->class->language->load('freshdesk');
		$this->html = $CMS->class->template->load_template("skin_freshdesk");
		$CMS->core->page_title = $CMS->lang['freshdesk'];
		
		switch ($CMS->input['act']) {
			case 'sync':
				$this->sync();
				break;
			case 'test':
				$this->test();
				break;
			default:
				$this->main();
				break;
		}
	}
	public function test() {
		global $CMS;
		$CMS->api->freshdesk->connect();
		$CMS->api->freshdesk->test();
		exit("Fresh Desk test");
	}
}
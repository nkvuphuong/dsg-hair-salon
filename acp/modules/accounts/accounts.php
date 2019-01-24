<?php

$accounts = new Accounts;
$accounts->auto_run();

class Accounts {
	public $html;
	function __construct() {
		global $CMS, $DB, $member;
		if (!$CMS->vars['is_login']) $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=login");
		$CMS->class->language->load("accounts");
		$this->html = $CMS->class->template->load_template("skin_accounts");
		$CMS->core->page_title = "-> {$CMS->lang['a_title']}";
	}
	function __destruct() { }
	public function auto_run() {
		global $CMS, $DB, $member;
		switch ($CMS->input['act']) {
			case 'add':
				$this->add();
				break;
			case 'edit':
				$this->edit();
				break;
			case 'show':
				$this->show();
				break;
			case 'delete':
				$this->delete();
				break;
			case 'search':
				$this->search();
				break;
			case 'get_district':
				$this->getDistrict();
				break;
			default:
			    if(\lib\input::get('subact') == 'clear_cache')
                {
                    $CMS->class->cache->mdelete($CMS->accounts->cache_prefix);
                    $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                }
				$this->defaultPage();
				break;
		}
	}
	public function defaultPage() {
		global $CMS, $DB, $member;
		$out .= $this->html->head();
		$data = $CMS->accounts->listing();
		$count = count($data);
		if ($count) {
			$cnt = 1;
			foreach ($data as $dt) {
				$dt['keyrow'] = $cnt;
				$dt['rowtr'] = $cnt == $count ? 'last-row' : '';
				$cnt += 1;
				$out .= $this->html->mid($dt);
			}
		} else {
//			$out .= $this->html->none();
		}
		$out .= $this->html->foot();
		$CMS->output.=$out;
	}
	public function add() {
		global $CMS, $DB, $member;
		if ($CMS->input['request_method'] == 'post') {
			$a_account = $CMS->input['a_account'];
			$a_bank = $CMS->input['a_bank'];
			$a_number = $CMS->input['a_number'];
			$a_holder = $CMS->input['a_holder'];
			$a_branch = $CMS->input['a_branch'];
			$a_status = intval($CMS->input['a_status']);
			$check=true;
			if ( empty($a_account)) {
				$check=false;
				$_SESSION['a_error'] .= $CMS->lang['a_account_err'].'<br>';
			}
			if ( $a_number !=""  && ! Validate::isNum($a_number)) {
				$check=false;
			}

			if ($check) {
				$id = $CMS->accounts->add($a_account, $a_bank, $a_number, $a_holder, $a_branch, $a_status);
				$_SESSION['a_success']=$CMS->lang['a_add_success'];
				if($CMS->input['action_redirect'] == "add")
				{
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts&act=add");
				}
				elseif($CMS->input['action_redirect'] == "detail")
				{
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts&act=show&id={$id}");
				}
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts");
			}
		}
		$CMS->output.=$this->html->add();
	}
	public function edit() {
		global $CMS, $DB, $member;
		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->accounts->getInfo($CMS->input['id']);
			if ($data_info) {
				if ($CMS->input['request_method'] == 'post') {					
					$a_account = $CMS->input['a_account'];
					$a_bank = $CMS->input['a_bank'];
					$a_number = $CMS->input['a_number'];
					$a_holder = $CMS->input['a_holder'];
					$a_branch = $CMS->input['a_branch'];
					$a_status = intval($CMS->input['a_status']);
					$check=true;
					if ( empty($a_account)) {
						$check=false;
						$_SESSION['a_error'] .= $CMS->lang['a_account_err'].'<br>';
					}
					if ( $a_number !=""  && ! Validate::isNum($a_number)) {
						$check=false;
					}

					if ($check) {
						$CMS->accounts->edit($data_info, $a_account, $a_bank, $a_number, $a_holder, $a_branch, $a_status);
						$_SESSION['a_success']=$CMS->lang['a_edit_success'];
						if($CMS->input['action_redirect'] == "edit")
						{
							$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts&act=edit&id={$CMS->input['id']}");
						}
						elseif($CMS->input['action_redirect'] == "detail")
						{
							$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts&act=show&id={$CMS->input['id']}");
						}
						$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts");
					}
				}
				$CMS->output.=$this->html->edit($data_info);
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts");
	}
	public function show() {
		global $CMS, $DB, $member;
		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->accounts->getInfo($CMS->input['id']);
			if ($data_info) {
				$CMS->output.=$this->html->show($CMS->accounts->convertvalue($data_info));
				$CMS->output.=$CMS->global->logs("Edit_accounts_{$CMS->input['id']}");
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts");
	}
	public function delete() {
		global $CMS, $DB, $member;
		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->accounts->getInfo($CMS->input['id']);
			if ($data_info) {
				$CMS->accounts->deleted($data_info['accounts_id']);
				$_SESSION['a_success']=$CMS->lang['a_deleted_success'];
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts");
	}
	public function search() {
		global $CMS, $DB, $member;
		$a_id_search = $CMS->input['a_id_search'];
		$a_account_search = $CMS->input['a_account_search'];
		$a_bank_search = $CMS->input['a_bank_search'];
		$a_number_search = $CMS->input['a_number_search'];
		$a_status_search = $CMS->input['a_status_search'];
		
		$str = '';
		if ( $a_id_search !=""  && Validate::isNum($a_id_search)) {
			$str.='&a_id='.$a_id_search;
		}
		if ( $a_account_search !="" ) {
			$str.='&a_account='.$a_account_search;
		}
		if ( $a_bank_search !="" ) {
			$str.='&a_bank='.$a_bank_search;
		}
		if ( $a_number_search !=""  ) {
			$str.='&a_number='.$a_number_search;
		}
		if ( $a_status_search !=""  && in_array($a_status_search, array(0,1))) {
			$str.='&a_status='.$a_status_search;
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts{$str}");
	}
}

?>
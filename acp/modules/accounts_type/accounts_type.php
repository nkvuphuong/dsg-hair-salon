<?php
use core\ezy;
use lib\input;

$accounts_type = new AccountsType;
$accounts_type->auto_run();

class AccountsType {
	public $html;
	function __construct() {
		global $CMS, $DB, $member;
		if (!$CMS->vars['is_login']) $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=login");
		$CMS->class->language->load("accounts_type");
		$this->html = $CMS->class->template->load_template("skin_accounts_type");
		$CMS->core->page_title = "-> {$CMS->lang['at_title']}";
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
            case "import":
                $this->import();
                break;
            case "export":
                $this->export();
                break;
			default:
                if(\lib\input::get('subact') == 'clear_cache')
                {
                    $CMS->class->cache->mdelete($CMS->accounts_type->cache_prefix);
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
		$data = $CMS->accounts_type->listing();
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
			$out .= $this->html->none();
		}
		$out .= $this->html->foot();
		$CMS->output.=$out;
	}
	public function add() {
		global $CMS, $DB, $member;
		if ($CMS->input['request_method'] == 'post') {

			$at_parent = $CMS->input['at_parent'];
			$at_code = $CMS->input['at_code'];
			$at_name = $CMS->input['at_name'];
			$at_status = $CMS->input['at_status'];
			$at_group = intval($CMS->input['at_group']);
			$check=true;
			if (empty($at_code) || ! Validate::isNum($at_code)) {
				$check=false;
				$_SESSION['at_error'] .= $CMS->lang['at_code_err'].'<br>';
			}

			if(is_array($at_name))
            {
                if (empty($at_name[$CMS->vars['default_language']])) {
                    $check=false;
                    $_SESSION['at_error'] .= $CMS->lang['at_name_err'].' ('.$CMS->vars['translations'][$CMS->vars['default_language']].')<br>';
                }

                $at_name = @json_encode($at_name, JSON_UNESCAPED_UNICODE);
            }
            else
            {
                if (empty($at_name)) {
                    $check=false;
                    $_SESSION['at_error'] .= $CMS->lang['at_name_err'].'<br>';
                }
            }
			if ($check) {
				if (empty($at_parent) || ! Validate::isNum($at_parent) || ! $CMS->accounts_type->getInfo($at_parent, 'accounts_type_id')) {
					$at_parent = 0;
				}
			}

			if($CMS->accounts_type->checkExist('accounts_type_code', $at_code))
            {
                $check=false;
                $_SESSION['at_error'] .= $CMS->lang['at_duplicated'].'<br>';
            }

			if ($check) {
				$id = $CMS->accounts_type->add($at_parent, $at_code, $at_name, $at_status, $at_group);
				$_SESSION['at_success']=$CMS->lang['at_add_success'];
				if($CMS->input['action_redirect'] == "add")
				{
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts_type&act=add");
				}
				elseif($CMS->input['action_redirect'] == "detail")
				{
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts_type&act=show&id={$id}");
				}
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts_type");
			}
		}



		$CMS->output.=$this->html->add();
	}
	public function edit() {
		global $CMS, $DB, $member;

		if (isset($CMS->input['id'])) {
			$data_info = $CMS->accounts_type->getInfo($CMS->input['id']);
			if ($data_info) {
				if ($CMS->input['request_method'] == 'post') {
					$at_parent = $CMS->input['at_parent'];
					$at_code = $CMS->input['at_code'];
					$at_name = $CMS->input['at_name'];
					$at_status = $CMS->input['at_status'];
					$at_group =  intval($CMS->input['at_group']);
					$check=true;
					if (empty($at_code) || ! Validate::isNum($at_code)) {
						$check=false;
						$_SESSION['at_error'] .= $CMS->lang['at_code_err'].'<br>';
					}

                    if(is_array($at_name))
                    {
                        if (empty($at_name[$CMS->vars['default_language']])) {
                            $check=false;
                            $_SESSION['at_error'] .= $CMS->lang['at_name_err'].' ('.$CMS->vars['translations'][$CMS->vars['default_language']].')<br>';
                        }

                        $at_name = @json_encode($at_name, JSON_UNESCAPED_UNICODE);
                    }
                    else
                    {
                        if (empty($at_name)) {
                            $check=false;
                            $_SESSION['at_error'] .= $CMS->lang['at_name_err'].'<br>';
                        }
                    }
					if ($check) {
						if (empty($at_parent) || ! Validate::isNum($at_parent) || ! $CMS->accounts_type->getInfo($at_parent, 'accounts_type_id')) {
							$at_parent = 0;
						}
					}

                    if($CMS->accounts_type->checkExist('accounts_type_code', $at_code, $data_info['accounts_type_code']))
                    {
                        $check=false;
                        $_SESSION['at_error'] .= $CMS->lang['at_duplicated'].'<br>';
                    }

					if ($check) {
						$CMS->accounts_type->edit($data_info, $at_parent, $at_code, $at_name, $at_status, $at_group);
						$_SESSION['at_success']=$CMS->lang['at_edit_success'];
						if($CMS->input['action_redirect'] == "edit")
						{
							$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts_type&act=edit&id={$CMS->input['id']}");
						}
						elseif($CMS->input['action_redirect'] == "detail")
						{
							$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts_type&act=show&id={$CMS->input['id']}");
						}
						$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts_type");
					}
				}

                $data_info = $CMS->accounts_type->editValue($data_info);

				$CMS->output .= $this->html->edit($data_info);
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts_type");
	}
	public function show() {
		global $CMS, $DB, $member;
		if (isset($CMS->input['id'])) {
			$data_info = $CMS->accounts_type->getInfo($CMS->input['id']);
			if ($data_info) {
				$CMS->output .= $this->html->show($CMS->accounts_type->convertValue($data_info));
				$CMS->output .= $CMS->global->logs("Edit_accounts_type_{$CMS->input['id']}");
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts_type");
	}
	public function delete() {
		global $CMS, $DB, $member;
		if (isset($CMS->input['id'])) {
			$data_info = $CMS->accounts_type->getInfo($CMS->input['id']);
			if ($data_info) {
				$CMS->accounts_type->deleted($data_info['accounts_type_id']);
				$_SESSION['at_success']=$CMS->lang['at_deleted_success'];
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts_type");
	}
	public function search() {
		global $CMS, $DB, $member;
		$at_id_search = $CMS->input['at_id_search'];
		$at_name_search = $CMS->input['at_name_search'];
		$at_status_search = $CMS->input['at_status_search'];

		$str = '';
		if ( $at_id_search != "" && Validate::isNum($at_id_search)) {
			$str.='&at_id='.$at_id_search;
		}
		if ( $at_name_search != "" ) {
			$str.='&at_name='.$at_name_search;
		}
		if ( $at_status_search != "" && in_array($at_status_search, array(0,1))) {
			$str.='&at_status='.$at_status_search;
		}
		
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts_type{$str}");
	}

    /**
     * Import excel
     */
    public function import()
    {
        global $CMS;
        ezy::load_model("report");
        $CMS->accounts_type->importFromExcel();
        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=accounts_type");
    }

    /**
     * Export excel
     */
    public function export()
    {
        global $CMS;
        ezy::load_model("report");
        $link = $CMS->accounts_type->exportToExcel();
//        header("location: {$link}");
        ezy::load_model("download");
        \models\download::sendFile($link);
    }
}

?>
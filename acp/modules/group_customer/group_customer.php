<?php
use core\ezy;
use lib\input;

$group_customer = new GroupCustomer;
$group_customer->auto_run();

class GroupCustomer {
	public $html;
	function __construct() {
		global $CMS, $DB, $member;
		if (!$CMS->vars['is_login']) $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=login");
		$CMS->class->language->load("group_customer");
		$this->html = $CMS->class->template->load_template("skin_group_customer");
		$CMS->core->page_title = "-> {$CMS->lang['gc_title']}";
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
			case 'delete':
				$this->delete();
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
                    $CMS->class->cache->mdelete($CMS->group_customer->cache_prefix);
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
		$data = $CMS->group_customer->listing();
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
			$gc_name = isset($CMS->input['gc_name'])?$CMS->input['gc_name']:'';
			$gc_status  = isset($CMS->input['gc_status'])&&$CMS->input['gc_status']=='on'?1:0;
			$check=true;
			if (empty($gc_name)) {
				$check=false;
				$_SESSION['gc_error']=$CMS->lang['gc_name_err'];
			}
			if ($CMS->group_customer->getInfo($gc_name,'gc_id')) {
				$check=false;
				$_SESSION['gc_error']=$CMS->lang['gc_name_exist'];
			}
			if ($check) {
				$CMS->group_customer->add($gc_name,$gc_status);
				$_SESSION['gc_success']=$CMS->lang['gc_add_success'];
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=group_customer");
			}
		}
		$CMS->output.=$this->html->add();
	}
	public function edit() {
		global $CMS, $DB, $member;
		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->group_customer->getInfo($CMS->input['id']);
			if ($data_info) {
				if ($CMS->input['request_method'] == 'post') {
					$gc_name = isset($CMS->input['gc_name'])?$CMS->input['gc_name']:'';
					$gc_status = isset($CMS->input['gc_status'])&&$CMS->input['gc_status']=='on'?1:0;
					$check=true;
					if (empty($gc_name)) {
						$check=false;
						$_SESSION['gc_error']=$CMS->lang['gc_name_err'];
					}
					if ($check) {
						$CMS->group_customer->edit($data_info['gc_id'],$gc_name,$gc_status);
						$_SESSION['gc_success']=$CMS->lang['gc_edit_success'];
						$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=group_customer");
					}
				}
				$CMS->output.=$this->html->edit($data_info);
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=group_customer");
	}
	public function delete() {
		global $CMS, $DB, $member;
		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->group_customer->getInfo($CMS->input['id']);
			if ($data_info) {
				$CMS->group_customer->deleted($data_info['gc_id']);
				$_SESSION['gc_success']=$CMS->lang['gc_deleted_success'];
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=group_customer");
	}

    /**
     * Import excel
     */
    public function import()
    {
        global $CMS;
        ezy::load_model("report");
        $CMS->group_customer->importFromExcel();
        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=group_customer");
    }

    /**
     * Export excel
     */
    public function export()
    {
        global $CMS;
        ezy::load_model("report");
        $link = $CMS->group_customer->exportToExcel();
//        header("location: {$link}");
        ezy::load_model("download");
        \models\download::sendFile($link);
    }
}

?>
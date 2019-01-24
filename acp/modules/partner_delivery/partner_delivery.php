<?php
if (!defined('IN_ROOT')) exit();

$partner_delivery = new partner_delivery;
$partner_delivery->autorun();

class partner_delivery{
	public $html;
	public function autorun(){
		 
		global $CMS, $DB, $member;
	 
		$CMS->class->language->load("partner_delivery");
		
		$this->html = $CMS->class->template->load_template("skin_partner_delivery");
	
		 
		switch ($CMS->input['act']) {
			default:
                    if(\lib\input::get('subact') == 'clear_cache')
                    {
                        $CMS->class->cache->mdelete($CMS->partner_delivery->cache_prefix);
                        $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                    }
					$this->default_page();
				break;
			case 'show':
				$this->show();
				break;
			case 'edit':
				$this->edit();
				break;
			case 'edit_do':
				$this->edit_do();
				break;
			case 'delete':
				$this->del();
				break;
			case 'add':
				$this->add();
				break;
			case 'add_do':
				$this->addDo();
				break;
			case 'search':
				$this->search();
			break;	
		}
	}
	public function default_page() { 
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['partner_delivery_title']}";
		$CMS->output.=$this->html->head();
		$CMS->output.=$CMS->partner_delivery->listing();
		$CMS->output.=$this->html->foot();
	}
	public function show() {
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['partner_delivery_title']}";
		$CMS->output.=$this->html->show($CMS->partner_delivery->convertvalue($CMS->partner_delivery->get_info($CMS->input['id'])));
		$CMS->output.=$CMS->global->logs("partner_delivery_{$CMS->input['id']}");
	}
	public function del() {
		global $CMS, $DB, $member;
		$CMS->partner_delivery->delete();
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=partner_delivery");
	}
	// }
	public function edit() {
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['partner_delivery_edit']}";
		$CMS->output.=$this->html->edit($CMS->partner_delivery->get_info($CMS->input['id']));
	}
	public function edit_do() { 
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['edit_partner_delivery']}";

		$action_redirect = $CMS->input['action_redirect'];

		if ($CMS->partner_delivery->edit($CMS->input['id'])) {
			if($action_redirect == "detail")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=partner_delivery&act=show&id={$CMS->input['id']}");
			}
			elseif($action_redirect == "edit")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=partner_delivery&act=edit&id={$CMS->input['id']}");
			}
			else
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=partner_delivery");
			}
		}
		$CMS->output.=$this->html->edit($CMS->partner_delivery->get_info($CMS->input['id']));
	}
	public function add() {
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['partner_delivery_add']}";
		$CMS->output.=$this->html->add();
	}
	public function addDo() {
		global $CMS, $DB, $member;
		
		$action_redirect = $CMS->input['action_redirect'];
		$p_delivery = $CMS->partner_delivery->add();
		if (is_array($p_delivery)) {
			if($action_redirect == "add")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=partner_delivery&act=add");
			}elseif($action_redirect == "detail")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=partner_delivery&act=show&id={$p_delivery['p_delivery_id']}");
			}
			else
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=partner_delivery");
			}
		}
		$CMS->core->page_title = "{$CMS->lang['partner_delivery_add']}";
		$CMS->output.=$this->html->add($CMS->input);
	}


    public function search() {
		global $CMS, $DB, $member;
	 
		$p_delivery_name = $CMS->input['p_delivery_name'];
		$str = '';
		if (  $p_delivery_name  != "" ) {
			if(Validate::isNum($p_delivery_name))
			{
				$str.='&pd_id='.$p_delivery_name;	
			}
			else
			{
					$str.='&pd_name='.$p_delivery_name;
			}
		}

		 
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=partner_delivery{$str}");
	}

}
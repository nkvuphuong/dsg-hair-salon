<?php
if (!defined('IN_ROOT')) exit();

$supplier = new supplier;
$supplier->autorun();

class supplier{
	public $html;
	public function autorun(){
		 
		global $CMS, $DB, $member;
		$CMS->class->language->load("store_request");// Dung cho cac module ajax chuyen tu store_request qua
		$CMS->class->language->load("supplier");
		
		$this->html = $CMS->class->template->load_template("skin_supplier");
		
		// Cập nhật location trong bản khai tên miền Việt Nam khi chọn country|city|district - huv 9.12.2013
		if(\lib\input::get('subact') == "update_location")
		{
			if(isset($CMS->input['city_id']))
			{
				print $CMS->global->get_list_district($CMS->input['city_id']);
			}
			exit;
		}

		switch ($CMS->input['act']) {
			default:
				if(\lib\input::get('subact') == "ajax_add_supplier")
				{
					$this->ajax_add_supplier();
				}elseif(\lib\input::get('subact') == "ajax_get_data_supplier")
				{
					$this->ajax_get_data_supplier();
				}elseif(\lib\input::get('subact') == "ajax_edit_supplier")
				{
					$this->ajax_edit_supplier();
				}else
				{

                    if(\lib\input::get('subact') == 'clear_cache')
                    {
                        $CMS->class->cache->mdelete('supplier');
                        $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                    }

					$this->default_page();
				}
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
			case 'search':
				$CMS->supplier->search();
				break;
			case 'del':
				$this->del();
				break;
			case 'add':
				$this->add();
				break;
			case 'add_do':
				$this->addDo();
				break;
		}
	}
	public function default_page() { 
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['supplier_title']}";
		$CMS->output.=$this->html->head();
		$CMS->output.=$CMS->supplier->listing();
		$CMS->output.=$this->html->foot();
	}
	public function show() {
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['supplier_title']}";
		$CMS->output.=$this->html->show($CMS->supplier->convertvalue($CMS->supplier->get_info($CMS->input['id'])));
		$CMS->output.=$CMS->global->logs("supplier_{$CMS->input['id']}");
	}
	public function del() {
		global $CMS, $DB, $member;
		$CMS->supplier->delete();
		$CMS->global->redirect("{$CMS->vars['http_referer']}");
	}
	// }
	public function edit() {
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['supplier_edit']}";
		$CMS->output.=$this->html->edit($CMS->supplier->get_info($CMS->input['id']));
	}
	public function edit_do() { 
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['supplier_edit']}";
		if ($CMS->supplier->edit($CMS->input['id'])) {
			if($CMS->input['action_redirect'] == "edit")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=supplier&act=edit&id={$CMS->input['id']}");
			}
			elseif($CMS->input['action_redirect'] == "detail")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=supplier&act=show&id={$CMS->input['id']}");
			}
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=supplier");
		}
		$CMS->output.=$this->html->edit($CMS->supplier->get_info($CMS->input['id']));
	}
	public function add() {
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['supplier_add']}";
		$CMS->output.=$this->html->add();
	}
	public function addDo() {
		global $CMS, $DB, $member;
		if ($id = $CMS->supplier->add()) {
			if($CMS->input['action_redirect'] == "add")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=supplier&act=add");
			}
			elseif($CMS->input['action_redirect'] == "detail")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=supplier&act=show&id={$id}");
			}
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=supplier");
		}
		$CMS->core->page_title = "{$CMS->lang['supplier_add']}";
		$CMS->output.=$this->html->add($CMS->input);
	}



	public function ajax_add_supplier()
	{
		global $CMS;

		if(!lib\security::check_token())
        {
            print json_encode(array("status" => "error", "msg" => $CMS->lang['invalid_token']));exit;
        }

		if($CMS->permit['supplier_add'])
		{
			$supplier_id = $CMS->supplier->addAjax();
			if($supplier_id)
			{
				$option_supplier = $CMS->supplier->getOptionSupplier($supplier_id);
				print json_encode(array("status" => "success", "msg" => "{$CMS->lang['title_add_supplier_success']}", "data_option" => $option_supplier));exit;
			}else
			{
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['title_add_supplier_error']}"));exit;
			}
		}else
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_no_permision']}"));exit;
		}
		
	}

	public function ajax_get_data_supplier()
	{
		global $CMS;

		$supplier_id = intval($CMS->input['supplier_id']);
		$data = $CMS->supplier->get_info($supplier_id);
		print json_encode($data);exit;	
		
	}

	public function ajax_edit_supplier()
	{
		global $CMS;

        if(!lib\security::check_token())
        {
            print json_encode(array("status" => "error", "msg" => $CMS->lang['invalid_token']));exit;
        }

		if($CMS->permit['supplier_edit']) // Tam thoi ko check phan quyen
		{
			$data = $CMS->supplier->editAjax();
			if($data)
			{
				$option_supplier = $CMS->supplier->getOptionSupplier($data['supplier_id']);
				print json_encode(array("status" => "success", "msg" => "{$CMS->lang['title_edit_supplier_success']}\"{$data['supplier_name']}\"", "data" => $data, "data_option" => $option_supplier));exit;
			}else
			{
				$data = $CMS->supplier->get_info($CMS->input['supplier_id']);
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['title_edit_supplier_error']}\"{$data['supplier_name']}\"", "data" => $data));exit;
			}	
		}else
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_no_permision']}"));exit;
		}
	}
}
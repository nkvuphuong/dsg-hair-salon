<?php
if (!defined('IN_ROOT')) exit();

$store = new Store;
$store->autorun();
//
class Store{
	public $html;
	public function autorun(){
		global $CMS, $DB, $member;
		$CMS->class->language->load("store");
		$this->html = $CMS->class->template->load_template("skin_store");
		switch ($CMS->input['act']) {
			default:

                if(\lib\input::get('subact') == 'clear_cache')
                {
                    $CMS->class->cache->mdelete('store');
                    $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                }
                else if( \lib\input::get('subact') == 'ajax_add_store' )
                {
                	$this->ajax_add_store();
                }
                else if( \lib\input::get('subact') == 'ajax_get_data_store' )
                {
                	$this->ajax_get_data_store();
                }
                else if( \lib\input::get('subact') == 'ajax_edit_store' )
                {
                	$this->ajax_edit_store();
                }
                else if( \lib\input::get('subact') == 'ajax_get_option_store' )
                {
                	$this->ajax_get_option_store();
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
			case 'search':
				$CMS->store->search();
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
		$CMS->core->page_title = "{$CMS->lang['store_title']}";
		$CMS->output.=$this->html->head();
		$CMS->output.=$CMS->store->acp_listing();
		$CMS->output.=$this->html->foot();
	}
	public function show() {
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['store_title']}";
		$CMS->output.=$this->html->show($CMS->store->convertvalue($CMS->store->get_info($CMS->input['id'])));
		$CMS->output.=$CMS->global->logs("store_{$CMS->input['id']}");
	}
	public function del() {
		global $CMS, $DB, $member;
		$CMS->store->acp_del();
		$CMS->global->redirect("{$CMS->vars['http_referer']}");
	}
	// }
	public function edit() {
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['store_edit']}";
		$CMS->output.=$this->html->edit($CMS->store->acp_info($CMS->input['id']));
	}
	public function edit_do() { 
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['store_edit']}";
		if ($CMS->store->acp_edit($CMS->input['id'])) {
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store");
		}
		$CMS->output.=$this->html->edit($CMS->store->acp_info($CMS->input['id']));
	}
	public function add() {
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['store_new']}";
		$CMS->output.=$this->html->add();
	}
	public function addDo() {
		global $CMS, $DB, $member;
		if ($CMS->store->acp_add()) {
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=store");
		}
		$CMS->core->page_title = "{$CMS->lang['store_new']}";
		$CMS->output.=$this->html->add();
	}

	public function ajax_add_store()
	{
		global $CMS;

		if( ! lib\security::check_token() )
        {
            print json_encode(array("status" => "error", "msg" => $CMS->lang['invalid_token']));exit;
        }

		if( $CMS->permit['store_add'] )
		{
			$store_id = $CMS->store->acp_addAjax();
			if( $store_id )
			{
				$option_store = $CMS->store->get_list_store($store_id);
				print json_encode(array("status" => "success", "msg" => "{$CMS->lang['store_add_success']}", "data_option" => $option_store));exit;
			}
			else
			{
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['title_add_supplier_error']}"));exit;
			}
		}
		else
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_no_permision']}"));exit;
		}
		
	}

	public function ajax_get_data_store()
	{
		global $CMS;

		$store_id = intval($CMS->input['store_id']);
		$data = $CMS->store->get_info($store_id);
		print json_encode($data);exit;	
		
	}

	public function ajax_edit_store()
	{
		global $CMS;

        if( ! lib\security::check_token() )
        {
            print json_encode(array("status" => "error", "msg" => $CMS->lang['invalid_token']));exit;
        }

		if( $CMS->permit['store_edit'] )
		{
			$data = $CMS->store->acp_editAjax();
			if( $data )
			{
				$option_store = $CMS->store->get_list_store($data['store_id']);
				print json_encode(array("status" => "success", "msg" => "{$CMS->lang['store_edit_success']} \"{$data['store_name']}\"", "data_option" => $option_store, "data" => $data));exit;

				$option_supplier = $CMS->supplier->getOptionSupplier($data['supplier_id']);
				print json_encode(array("status" => "success", "msg" => "{$CMS->lang['title_edit_supplier_success']}\"{$data['supplier_name']}\"", "data" => $data, "data_option" => $option_supplier));exit;
			}
			else
			{
				$data = $CMS->supplier->get_info($CMS->input['supplier_id']);
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['title_edit_supplier_error']}\"{$data['supplier_name']}\"", "data" => $data));exit;
			}	
		}else
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_no_permision']}"));exit;
		}
	}

	public function ajax_get_option_store()
	{
		global $CMS;

		$option_store = $CMS->store->get_list_store(0, 0, 1, 1);
		print json_encode(array("status" => "success", "data_option" => $option_store));exit;	
		
	}
}
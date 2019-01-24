<?php

$config_price = new ProductGroup;
$config_price->auto_run();

class ProductGroup {
	public $html;
	function __construct() {
		global $CMS, $DB, $member;
		if (!$CMS->vars['is_login']) $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=login");
		$CMS->class->language->load("store_request");// Dung cho cac module ajax chuyen tu store_request qua
		$CMS->class->language->load("config_price");
		$this->html = $CMS->class->template->load_template("skin_config_price");
		$CMS->core->page_title = "-> {$CMS->lang['pg_title']}";
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
                        $CMS->class->cache->mdelete($CMS->config_price->cache_prefix);
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
		$data = $CMS->config_price->listing();
		if (count($data)) {
			foreach ($data as $dt) {
				$out .= $this->html->mid($dt);
			}
		} else {
			$out .= $this->html->none();
		}
		$out .= $this->html->foot();
		$CMS->output.=$out;
	}

	 

 
	public function edit() {
		global $CMS, $DB, $member;
		 
		if(\lib\input::get('subact') == "save_price_sell")
		{
			if($CMS->config_price->save_price_sell() == true)
			{
				print json_encode(array("status" => "success", "msg" => "Cập nhật dữ liệu thành công!"));exit;
			}
			else
			{
				print json_encode(array("status" => "error", "msg" => "Cập nhật dữ liệu thất bại!"));exit;
			}
		}
		elseif(\lib\input::get('subact') == "save_price")
		{	
			$product = $CMS->config_price->save_price();
			if(is_array($product))
			{
				print json_encode(array("status" => "success", "msg" => "Cập nhật dữ liệu thành công!", "data_info" => $product));exit;
			}
			else
			{
				print json_encode(array("status" => "error", "msg" => "Cập nhật dữ liệu thất bại!"));exit;
			}
		}
		return;
		 
	}

	 


	public function show() {
		global $CMS, $DB, $member;
		if (isset($CMS->input['id'])) {
			$data_info = $CMS->config_price->getInfo($CMS->input['id']);
			if ($data_info) {
				$CMS->output.=$this->html->show($CMS->config_price->convertvalue($data_info));
				$CMS->output.=$CMS->global->logs("Edit_config_price_{$CMS->input['id']}");
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_price");
	}
	public function delete() {
		global $CMS, $DB, $member;

		 
	}
	public function search() {
		global $CMS, $DB, $member;
	  
	}


	 
}

?>
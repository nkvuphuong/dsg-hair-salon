<?php

use core\ezy;

$product_group = new ProductGroup;
$product_group->auto_run();

class ProductGroup {
	public $html;
	function __construct() {
		global $CMS, $DB, $member;
		if (!$CMS->vars['is_login']) $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=login");
		$CMS->class->language->load("store_request");// Dung cho cac module ajax chuyen tu store_request qua
		$CMS->class->language->load("product_group");
		$this->html = $CMS->class->template->load_template("skin_product_group");
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
			case 'delete_all':
				$this->delete_all();
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
				if(\lib\input::get('subact') == 'search_gp_product_ajax')
				{
					$this->search_gp_product_ajax();
					break;
				}
				elseif(\lib\input::get('subact') == 'ajax_add_product_group')
				{
					$this->ajax_add_product_group();
					break;
				}
				elseif(\lib\input::get('subact') == 'ajax_get_data_product_group')
				{
					$this->ajax_get_data_product_group();
					break;
				}
				elseif(\lib\input::get('subact') == 'ajax_edit_product_group')
				{
					$this->ajax_edit_product_group();
					break;
				}elseif(\lib\input::get('subact') == "ajax_add_pg") // Tanlv chuyen tu ben store_request qua
				{
					$this->ajax_add_pg();
				}elseif(\lib\input::get('subact') == "ajax_getinfo_pg")
				{
					$this->ajax_getinfo_pg();
				}elseif(\lib\input::get('subact') == "ajax_edit_pg")
				{
					$this->ajax_edit_pg();
				}elseif(\lib\input::get('subact') == "del_img")
				{
					$this->del_img();
				}
				else
				{
                    if(\lib\input::get('subact') == 'clear_cache')
                    {
                        $CMS->class->cache->mdelete('product_group');
                        $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                    }
					$this->defaultPage();
					break;
				}
				
		}
	}
	public function defaultPage() {
		global $CMS, $DB, $member;
		$out .= $this->html->head();
		$data = $CMS->product_group->listing();
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

	public function ajax_add_product_group() {
		global $CMS, $DB, $member;


	  
	 
		 	$base64_string = urldecode($CMS->input['base64_image']);
 
			$pg_parent = $CMS->input['pg_parent'];
			$pg_name = urldecode($CMS->input['pg_name']);
			$pg_code = urldecode($CMS->input['pg_code']);
			$pg_avartar =  $_FILES['upload_img'];
	 
			$pg_status = $CMS->input['pg_status'];
			$pg_order = isset($CMS->input['pg_order']) ? intval($CMS->input['pg_order']) : 1;
			$pg_description = ($CMS->input['pg_description']);
			$cat_gallery_id = intval($CMS->input['cat_gallery_id']);
			
			$check=true;
			if (empty($pg_name)) {
				$check=false;
				 
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['pg_name_err']}" ));exit;

			}
			 
			if (empty($pg_status) || !in_array($pg_status, array(0,1))) {
				$check=false;
				 
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['pg_statupg_err']}" ));exit;

			}
			 
			if ($check) {
				if ( $pg_parent !="") {
					if (! $CMS->product_group->getInfo($pg_parent, 'product_group_id')) {
						$check=false;
						 
						print json_encode(array("status" => "error", "msg" => "{$CMS->lang['pg_parent_err']}" ));exit;
					}
				} else {
					$pg_parent = 0;
				}
				if ($CMS->product_group->checkName($pg_name)) {
					$check=false;
		 
						print json_encode(array("status" => "error", "msg" => "{$CMS->lang['pg_name_exist']}" ));exit;

				}
				if ($CMS->product_group->checkCode($pg_code)) {
					$check=false;
			 
						print json_encode(array("status" => "error", "msg" => "{$CMS->lang['pg_code_exist']}" ));exit;


				}
				if (!empty($pg_avartar['tmp_name'])) {

					$file_ext = $CMS->class->attachment->get_ext( $pg_avartar['name'] );
					if ($pg_avartar['size'] > 3*1024*1024 ||   $CMS->class->attachment->is_image($pg_avartar['name'], $file_ext) == false  ) {
						$check = false;
						 
						print json_encode(array("status" => "error", "msg" => "{$CMS->lang['pg_avartar_err']}" ));exit;
					}

				 
				}

			}
			if ($check) {
				$id = $CMS->product_group->add($pg_parent, $pg_name, $pg_code,$pg_status, $pg_description, $pg_order, $cat_gallery_id);
				 

				if (!empty($id) && !empty($pg_avartar['tmp_name'])) {
					$dir = $CMS->vars['upload_dir'].'/product';
					$CMS->class->image->is_dir($CMS->vars['upload_dir'].'/product');
					$file_name = $id."_".$CMS->class->random->randomString(5,'abcdefghijklmnopqrstuvwxyz').'.jpg';
					if (! is_dir($dir)) {
						mkdir($dir, 0755, true);
					}
					if (move_uploaded_file($pg_avartar['tmp_name'], $dir.'/'.$file_name)) {
						$CMS->product_group->upAvartar($id, $file_name);
					}


					$CMS->class->image->quality = 1;
					$CMS->class->image->is_dir($CMS->vars['upload_dir'].'/product/thumbnail');
					$CMS->class->image->resize("{$CMS->vars['upload_dir']}/product/".$file_name, "{$CMS->vars['upload_dir']}/product/thumbnail/".$file_name, 220,150);	


				}
					$option_p_product_group = "<option value=''>{$CMS->lang['select']}</option>";
					$group = $CMS->product_group->getAll($CMS->input['pg_type']);
		 
				foreach ($group as $g) {
				 
						$option_p_product_group .= "<option value='{$g['product_group_id']}'>{$g['product_group_name']}</option>";
						if(count($g['data_item']) > 0)
						{
							foreach ($g['data_item'] as $key => $value) {
								$option_p_product_group .= "<option value='{$value['product_group_id']}'> |__{$value['product_group_name']}</option>";
							}
						}
				}
				print json_encode(array("status" => "success", "msg" => "{$CMS->lang['pg_add_success']}", "data_option" => $option_p_product_group, "pg_id" => $id , "pg_type" => $CMS->input['pg_type'] ));exit;

			 
			}
	 
	}


	public function add() {
		global $CMS, $DB, $member;


		if ($CMS->input['request_method'] == 'post') {
			$pg_avartar = $_FILES['pg_avartar'];
	 
		 	$base64_string = $CMS->input['base64_image'];
 
			$pg_parent = $CMS->input['pg_parent'];
			$pg_name = $CMS->class->editor->input('pg_name');
			$pg_code = $CMS->input['pg_code'];
			$pg_avartar = $_FILES['pg_avartar'];
	 
			$pg_status = $CMS->input['pg_status'];
			$pg_description = $CMS->input['pg_description'];

			$check=true;
			if (empty($pg_name)) {
				$check=false;
				$_SESSION['error_msg'] .= $CMS->lang['pg_name_err'].'<br>';
			}
			
			if (empty($pg_code)) {
				$check=false;
				$_SESSION['error_msg'] .= $CMS->lang['pg_code_err'].'<br>';
			}

 

			if (strlen(utf8_decode("$pg_code")) > 3 ) {
				$check=false;
				$_SESSION['error_msg'] .= $CMS->lang['pg_code_string_err'].'<br>';
			}

			 
			

			if (empty($pg_status) || !in_array($pg_status, array(0,1))) {
				$check=false;
				$_SESSION['error_msg'] .= $CMS->lang['pg_statupg_err'].'<br>';
			}
			 
			if ($check) {
				if ( $pg_parent != "") {
					if (! $CMS->product_group->getInfo($pg_parent, 'product_group_id')) {
						$check=false;
						$_SESSION['error_msg'] .= $CMS->lang['pg_parent_err'].'<br>';
					}
				} else {
					$pg_parent = 0;
				}
				$check_name = $CMS->product_group->checkName($pg_name);

				if (  is_array($check_name)) {
					$check=false;
					$_SESSION['error_msg'] .= $CMS->lang['pg_name_exist']."( Xem: <a href='{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$check_name['product_group_id']}'>{$check_name['product_group_name']}</a>)<br>";
				}

				$checkCode = $CMS->product_group->checkCode($pg_code);

				if (is_array($checkCode) ) {
					$check=false;
					$_SESSION['error_msg'] .= $CMS->lang['pg_code_exist']."( Xem: <a href='{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$checkCode['product_group_id']}'>{$checkCode['product_group_name']}</a>)<br>";
				}
				if ( $pg_avartar['tmp_name'] != "") {
					$file_ext = $CMS->class->attachment->get_ext( $pg_avartar['name'] );
					if ($pg_avartar['size'] > 3*1024*1024 ||   $CMS->class->attachment->is_image($pg_avartar['name'], $file_ext) == false  ) {
						$check = false;
						 
						$_SESSION['error_msg'] .= $CMS->lang['pg_avartar_err'].'<br>';
					}

					 
				}

			}
			if ($check) {
				$id = $CMS->product_group->add($pg_parent, $pg_name, $pg_code,$pg_status, $pg_description);
				 

				if (!empty($id) && !empty($pg_avartar['tmp_name'])) {
					$dir = $CMS->vars['upload_dir'].'/product';
					$file_name = $id."_".$CMS->class->random->randomString(5,'abcdefghijklmnopqrstuvwxyz').'.jpg';
					if (! is_dir($dir)) {
						mkdir($dir, 0755, true);
					}
					if (move_uploaded_file($pg_avartar['tmp_name'], $dir.'/'.$file_name)) {
						$CMS->product_group->upAvartar($id, $file_name);
					}


					$CMS->class->image->quality = 1;
					$CMS->class->image->resize("{$CMS->vars['upload_dir']}/product/".$file_name, "{$CMS->vars['upload_dir']}/product/thumbnail/".$file_name, 220,150);	


				}

				$_SESSION['msg']=$CMS->lang['pg_add_success'];
				$action_redirect =  $CMS->input['action_redirect'];
				if($action_redirect == "list" OR $action_redirect == "" )
				{
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product_group");
				}
				elseif($action_redirect == "add")
				{
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product_group&act=add");
				}
				elseif($action_redirect == "detail")
				{
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$id}");
				}
				
				
			}
		}
		$CMS->output.=$this->html->add();
	}
	public function edit() {
		global $CMS, $DB, $member;
		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->product_group->getInfo($CMS->input['id']);
			if ($data_info) {
				if ($CMS->input['request_method'] == 'post') {
					$base64_string = $CMS->input['base64_image'];
		 			 
					$pg_parent = $CMS->input['pg_parent'];
					$pg_name = $CMS->class->editor->input('pg_name');
					$pg_code = $CMS->input['pg_code'];
					$pg_avartar = $_FILES['pg_avartar'];
					$pg_status = $CMS->input['pg_status'];
					$pg_description = $CMS->input['pg_description'];
					
					$check=true;
					if (empty($pg_name)) {
						$check=false;
						$_SESSION['error_msg'] .= $CMS->lang['pg_name_err'].'<br>';
					}
					
					if (empty($pg_code)) {
						$check=false;
						$_SESSION['error_msg'] .= $CMS->lang['pg_code_err'].'<br>';
					}

					if (strlen(utf8_decode("$pg_code")) > 3 ) {
						//$check=false;
						//$_SESSION['error_msg'] .= $CMS->lang['pg_code_string_err'].'<br>';
					}


					if (empty($pg_status) || !in_array($pg_status, array(0,1))) {
						$check=false;
						$_SESSION['error_msg'] .= $CMS->lang['pg_statupg_err'].'<br>';
					}
					 
					if ($check) {
						if (!empty($pg_parent)) {
							if (! $CMS->product_group->getInfo($pg_parent, 'product_group_id')) {
								$check=false;
								$_SESSION['error_msg'] .= $CMS->lang['pg_parent_err'].'<br>';
							}
						} else {
							$pg_parent = 0;
						}
						if ($CMS->product_group->checkName($pg_name, $data_info['product_group_id'])) {
							$check=false;
							$_SESSION['error_msg'] .= $CMS->lang['pg_name_exist'].'<br>';
						}

						$check_name = $CMS->product_group->checkName($pg_name, $data_info['product_group_id']);
						if (is_array($check_name)) { 
							$check=false;
							$_SESSION['error_msg'] .= $CMS->lang['pg_name_exist']."( Xem: <a href='{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$check_name['product_group_id']}'>{$check_name['product_group_name']}</a>)<br>";
						}

						$checkCode = $CMS->product_group->checkCode($pg_code, $data_info['product_group_id']);
						if (is_array($checkCode)) {
							$check=false;
							$_SESSION['error_msg'] .= $CMS->lang['pg_code_exist']."( Xem: <a href='{$CMS->vars['root_domain']}/?site=product_group&act=show&id={$checkCode['product_group_id']}'>{$checkCode['product_group_name']}</a>)<br>";
						}

						 
						if (!empty($pg_avartar['tmp_name'])) {
							$file_ext = $CMS->class->attachment->get_ext( $pg_avartar['name'] );
							if ($pg_avartar['size'] > 3*1024*1024 ||   $CMS->class->attachment->is_image($pg_avartar['name'], $file_ext) == false  ) {
								$check = false;
								 
								$_SESSION['error_msg'] .= $CMS->lang['pg_avartar_err'].'<br>';
							}
						}
						 
					}
					if ($check) {
						
						if (!empty($pg_avartar['tmp_name'])) {
 
							$dir = $CMS->vars['upload_dir'].'/product';

							$data_info['product_group_avatar'] =   $data_info['product_group_id']."_".$CMS->class->random->randomString(15,'abcdefghijklmnopqrstuvwxyz').'.jpg';
					 
							if (! is_dir($dir)) {
								mkdir($dir, 0755, true);
							}
							//echo  $dir.'/'.$data_info['product_group_avatar'];exit;
 
								@copy($pg_avartar['tmp_name'], $dir.'/'.$data_info['product_group_avatar']) or die ("Could not be upload.");
 
								$CMS->class->image->quality = 1;
							    $CMS->class->image->resize("{$CMS->vars['upload_dir']}/product/".$data_info['product_group_avatar'], "{$CMS->vars['upload_dir']}/product/thumbnail/".$data_info['product_group_avatar'], 220,150);	


						}

 
						$CMS->product_group->edit($data_info,$pg_parent, $pg_name, $pg_code,$pg_status, $pg_description);
						$_SESSION['msg']=$CMS->lang['pg_edit_success'];
						
						$action_redirect =  $CMS->input['action_redirect'];
						if($action_redirect == "list" OR $action_redirect == "")
						{
							$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product_group");
						}
						elseif($action_redirect == "edit")
						{
							$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product_group&act=edit&id={$CMS->input['id']}");
						}
						
						
					}
				}
				$CMS->output.=$this->html->edit($data_info);
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product_group");
	}

	 


	public function show() {
		global $CMS, $DB, $member;
		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->product_group->getInfo($CMS->input['id']);
			if ($data_info) {
				$CMS->output.=$this->html->show($CMS->product_group->convertvalue($data_info));
				$CMS->output.=$CMS->global->logs("Edit_product_group_{$CMS->input['id']}");
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product_group");
	}
	public function delete() {
		global $CMS, $DB, $member;
		$link = isset($CMS->input['pg_type']) ? "&pg_type=".$CMS->input['pg_type'] : "";
		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->product_group->getInfo($CMS->input['id']);

			// Check san pham thuoc danh muc
			if($CMS->product_group->countproduct_bygroup($CMS->input['id'], intval($CMS->input['pg_type'])) > 0)
			{
				$_SESSION['error_msg'] .= $CMS->lang['exits_product_in_cate'].'<br>';
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product_group{$link}");
			}


			//Check danh muc con thuoc danh muc gốc
			if($data_info['product_group_parent'] == 0)
			{
				if($CMS->product_group->countitem_group($CMS->input['id'],0, intval($CMS->input['pg_type'])) > 0)
				{
					$_SESSION['error_msg'] .= $CMS->lang['exist_subproduct_in_cate'].'<br>';
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product_group{$link}");
				}
			}

			if ($data_info) {

				$CMS->product_group->deleted($data_info['product_group_id']);
				$_SESSION['msg']=$CMS->lang['pg_deleted_success']." [{$data_info['product_group_name']}]<br>";
				if (!empty($data_info['product_group_avatar'])) {
					unlink($CMS->vars['upload_dir'].'/product/'.$data_info['product_group_avatar']);
				}
			}
		}

		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product_group{$link}");
	}

	function delete_all()
	{
		global $CMS;
		$deleted = 0;
		// print "<pre>";
		// print_r($CMS->input);exit;
		$_SESSION["msg"] .= "";
		$link = isset($CMS->input['pg_type']) ? "&pg_type=".$CMS->input['pg_type'] : "";
		for ( $i = 0; $i < intval( $CMS->input["data_cnt"] ); $i++ )
		{
			$id = intval( $CMS->input["id_{$i}"] );
			if($id)
			{
				$data_info = $CMS->product_group->getInfo($id);

				// Check san pham thuoc danh muc
				if($CMS->product_group->countproduct_bygroup($id, intval($CMS->input['pg_type'])) > 0)
				{
					$_SESSION['msg'] .= $CMS->lang['exits_product_in_cate'].'<br>';
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product_group{$link}");
				}


				//Check danh muc con thuoc danh muc gốc
				if($data_info['product_group_parent'] == 0)
				{
					if($CMS->product_group->countitem_group($id,0, intval($CMS->input['pg_type'])) > 0)
					{
						$_SESSION['msg'] .= $CMS->lang['exist_subproduct_in_cate'].'<br>';
						$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product_group{$link}");
					}
				}

				if ($data_info) {

					$CMS->product_group->deleted($data_info['product_group_id']);
					$_SESSION['msg'] .= $CMS->lang['pg_deleted_success']." [{$data_info['product_group_name']}]<br>";
					if (!empty($data_info['product_group_avatar'])) {
						unlink($CMS->vars['upload_dir'].'/product/'.$data_info['product_group_avatar']);
					}
				}
			}
			$deleted ++;
		}
		
		if ( $deleted == 0 )
		{
			$_SESSION["msg"] .= "{$CMS->lang['error_delete_failed_product_group']}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product_group{$link}");
		}

		// success 
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product_group{$link}");
		
	}
	public function search() {
		global $CMS, $DB, $member;
	 
		$pg_name_search = $CMS->input['pg_name_search'];
		
		$str = '';
	 
		if ( $pg_name_search != "") {
			$str.='&pg_name='.$pg_name_search;
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product_group{$str}");
	}


	public function search_gp_product_ajax() {
		global $CMS, $DB, $member;
	 	$p_type = $CMS->input['p_type'];

		$option = $CMS->product_group->getParent_bytype($p_type);
		print json_encode(array("status" => "success", "msg" => "", "data_option" => $option));exit;
	}



	public function ajax_get_data_product_group() {
		global $CMS, $DB, $member;
	 	$group_product_id = $CMS->input['group_product_id'];

		$info = $CMS->product_group->getInfo($group_product_id);
		if(is_array($info))
		{

			print json_encode(array("status" => "success",   "data" => $info));exit;
		}
		else
		{
			print json_encode(array("status" => "error", "msg" => "Dữ liệu không tồn tại!"));exit;
		}
 
	}



	public function ajax_edit_product_group() {
		global $CMS, $DB, $member;
 
		if (!empty($CMS->input['product_group_id'])) {
			$data_info = $CMS->product_group->getInfo($CMS->input['product_group_id']);	 
 
			if (is_array($data_info)) {

 
					$base64_string = $CMS->input['base64_image'];
		 		
					$pg_parent = urldecode($CMS->input['pg_parent']);
					$pg_name = urldecode($CMS->input['pg_name']);
					$pg_code = urldecode($CMS->input['pg_code']);
					$pg_avartar = $_FILES['pg_avartar'];
					$pg_status = urldecode($CMS->input['pg_status']);
					$pg_order = isset($CMS->input['pg_order']) ? intval($CMS->input['pg_order']) : $data_info['product_group_order'];
					$pg_description = urldecode($CMS->input['pg_description']);
					$cat_gallery_id = intval($CMS->input['cat_gallery_id']);
					
					$check=true;
					if (empty($pg_name)) {
						$check=false;
					 
						print json_encode(array("status" => "error", "msg" => $CMS->lang['pg_name_err'], "data_option" => $option));exit;

					}
					 
					if (!in_array($pg_status, array(0,1))) {
						$check=false;
						print json_encode(array("status" => "error", "msg" => $CMS->lang['pg_statupg_err'], "data_option" => $option));exit;

					 
					}
					 
					if ($check) {
						if (!empty($pg_parent)) {
							if (! $CMS->product_group->getInfo($pg_parent, 'product_group_id')) {
								$check=false;
							 
								print json_encode(array("status" => "error", "msg" => $CMS->lang['pg_parent_err'], "data_option" => $option));exit;

							}
						} else {
							$pg_parent = 0;
						}
						if ($CMS->product_group->checkName($pg_name, $data_info['product_group_id'])) {
							$check=false;
				 
							print json_encode(array("status" => "error", "msg" => $CMS->lang['pg_name_exist'], "data_option" => $option));exit;

						}
						if ($CMS->product_group->checkCode($pg_code, $data_info['product_group_id'])) {
							$check=false;
						 
							print json_encode(array("status" => "error", "msg" => $CMS->lang['pg_code_exist'], "data_option" => $option));exit;
						}
						if (!empty($pg_avartar['tmp_name'])) {
					 
							$file_ext = $CMS->class->attachment->get_ext( $pg_avartar['name'] );
							if ($pg_avartar['size'] > 3*1024*1024 ||   $CMS->class->attachment->is_image($pg_avartar['name'], $file_ext) == false  ) {
								$check = false;
								 
								print json_encode(array("status" => "error", "msg" => $CMS->lang['pg_avartar_err'], "data_option" => $option));exit;
							}

						}
						 
					}
					if ($check) {
						
						if (!empty($pg_avartar['tmp_name'])) {
 
							$dir = $CMS->vars['upload_dir'].'/product';

							$data_info['product_group_avatar'] = !empty($data_info['product_group_avatar']) ? $data_info['product_group_avatar'] : $data_info['product_group_id']."_".$CMS->class->random->randomString(5,'abcdefghijklmnopqrstuvwxyz').'.jpg';
					 
							if (! is_dir($dir)) {
								mkdir($dir, 0755, true);
							}
							//echo  $dir.'/'.$data_info['product_group_avatar'];exit;
 
								@copy($pg_avartar['tmp_name'], $dir.'/'.$data_info['product_group_avatar']) or die ("Could not be upload.");
 
								$CMS->class->image->quality = 1;
								$CMS->class->image->is_dir($dir."/thumbnail");
							    $CMS->class->image->resize("{$CMS->vars['upload_dir']}/product/".$data_info['product_group_avatar'], "{$CMS->vars['upload_dir']}/product/thumbnail/".$data_info['product_group_avatar'], 220,150);	


						}


						$CMS->product_group->edit($data_info,$pg_parent, $pg_name, $pg_code,$pg_status, $pg_description, $pg_order, $cat_gallery_id);
		 

						print json_encode(array("status" => "success", "msg" => $CMS->lang['pg_edit_success'], "pg_type" => $CMS->input['pg_type'], "pg_id" => $CMS->input['product_group_id']));exit;

						 
					}
		 
	 
			 
			}
			else
			{
					print json_encode(array("status" => "error", "msg" => "Thông tin nhóm sản phẩm không tồn tại!" ));exit;
			}
		}
	 
	}


	public function ajax_add_pg()
	{
		global $CMS;

		if($CMS->permit['product_group_add'])
		{
			$pg_id = $CMS->product_group->addAjax();
	 
			if($pg_id)
			{
				$data = $CMS->product_group->getInfo($pg_id);
				$data['data_option'] = $CMS->product_group->getMultiOptionCategory(1);
				print json_encode(array("status" => "success", "msg" => "{$CMS->lang['msg_add_successful_product_group']}\"{$data['product_group_name']}\"", "data" => $data));exit;
			}else
			{
				// $data = $CMS->shipment->get_info($CMS->input['shi_id']);
				$pg_name = urldecode($CMS->input['pg_name']);
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_add_error_product_group']}\"{$pg_name}\"", "data" => $data));exit;
			}
			
		}else
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_no_permision']}"));exit;
		}
	}
			
	public function ajax_getinfo_pg()
	{
		global $CMS;

		$pg_id = intval($CMS->input['pg_id']);
		$data = $CMS->product_group->getInfo($pg_id);
		$data['data_option'] = $CMS->product_group->getMultiOptionCategory(1,$pg_id,0,$data['product_group_parent']);
		print json_encode(array("status" => "success", "msg" => "", "data" => $data));exit;
		
	}

	public function ajax_edit_pg()
	{
		global $CMS;

		if($CMS->permit['product_group_edit'])
		{
			$data = $CMS->product_group->editAjax();
			if($data)
			{
				print json_encode(array("status" => "success", "msg" => "{$CMS->lang['msg_edit_successful_product_group']}\"{$data['product_group_name']}\"", "data" => $data));exit;
			}else
			{
				// $data = $CMS->shipment->get_info($CMS->input['shi_id']);
				$pg_name = urldecode($CMS->input['pg_name']);
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_edit_error_product_group']}\"{$pg_name}\"", "data" => $data));exit;
			}
			
		}else
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_no_permision']}"));exit;
		}
		
	}

    /**
     * Import excel
     */
    public function import()
    {
        global $CMS;
        ezy::load_model("report");
        $CMS->product_group->importFromExcel();
        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=product_group");
    }

    /**
     * Export excel
     */
    public function export()
    {
        global $CMS;
        ezy::load_model("report");
        $link = $CMS->product_group->exportToExcel($CMS->input['pg_type']);
//        header("location: {$link}");

        ezy::load_model("download");
        \models\download::sendFile($link);
    }

    function del_img()
    {
    	global $CMS;
    	$id = intval($CMS->input['id']);
    	$check = $CMS->product_group->del_img($id);
    	print $check;exit;
    }
}

?>
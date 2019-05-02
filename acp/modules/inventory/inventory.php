<?php

use \lib\input;

$inventory = new storecheck;
$inventory->auto_run();

class storecheck {
	public $html;
	function __construct() {
		global $CMS, $DB, $member;
		if (!$CMS->vars['is_login']) $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=login");
 
		$CMS->class->language->load("inventory");
		$this->html = $CMS->class->template->load_template("skin_inventory");
		$CMS->core->page_title = "-> {$CMS->lang['in_title']}";
	}
	function __destruct() { }
	public function auto_run() {
 
	
		global $CMS, $DB, $member;
 
		switch ($CMS->input['act']) {
			case 'add':
				if(input::get('subact') == "add_step_1")
				{
					$this->add_step_1();
				}
				else if (input::get('subact') == "step_2")
				{
					$this->step_2();
				}
				else
				{
					$this->add();
				}
				
				break;
			case 'add_do':
				$this->add_do();
			break;	
			case 'edit':
				if(\lib\input::get('subact')== "addproduct")
				{ 
					$this->addproduct();
				}
				else if(\lib\input::get('subact') == "edit_step_1")
				{
					$this->edit_step_1();
				} 
				else if(\lib\input::get('subact') == "step_2")
				{
					$this->edit_step_2();
				}	
				else if(\lib\input::get('subact')== "addproduct_do")
				{ 
					$this->addproduct_do();
				}
				else if(\lib\input::get('subact')== "edit_do")
				{ 
					$this->edit_do();
				}else if(\lib\input::get('subact')== "edit_item")
				{ 
					$this->edit_item();
				}else if(\lib\input::get('subact')== "edit_item_do")
				{ 
					$this->edit_item_do();
				}
				else
				{
					$this->edit();
				}
				
				break;
			case 'edit_do':
				$this->edit_do();
			break;		
			case 'show':
				$this->show();
				break;
			case 'delete':
				if(\lib\input::get('subact') == "del_item")
				{
					$this->del_item();
				}
				else
				{
					$this->delete();
				}
				
				break;
			case 'search':
				if(\lib\input::get('subact') == 'searchkey')
				{
					$this->searchkey();
				}else
				{
					$this->search();
				}
				break;
			case 'get_district':
				$this->getDistrict();
				break;
			case 'approve':
				$this->approve();
			break;
			case 'approve_do':
				$this->approve_do();
			break;
			default:
				 
					$this->defaultPage();
			 break;
			 
				
		}
	}
	public function defaultPage() {
		global $CMS, $DB, $member;
		unset($_SESSION['edit_inventory']);
		unset($_SESSION['add_inventory']);
		$out = $this->html->head();
		$data = $CMS->inventory->listing();
		if (count($data)) {
			foreach ($data as $dt) {
				$out .= $this->html->mid($dt);
			}
		}  
		$out .= $this->html->foot();
		$CMS->output.=$out;
	}


	public function approve() {
		global $CMS, $DB, $member;
		unset($_SESSION['edit_inventory']);
		unset($_SESSION['add_inventory']);
		$in = $CMS->inventory->get_info($CMS->input['id']);
		if(!is_array($in))
		{
			$_SESSION['error_msg'] = "Phiếu kiểm salon không tồn tại";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory");

		}
 		
 		if($in['is_balance'] == 1)
 		{
 			//Phieu da lam bu tru
 			$_SESSION['error_msg'] = "Phiếu kiểm salon này đã được bù trừ";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory");
 		}
		$CMS->output.=$this->html->approve($CMS->inventory->convertvalue($in));

		$CMS->output.=$CMS->global->logs("inventory_{$CMS->input['id']}");
	}

	public function approve_do() {
		global $CMS, $DB, $member;
		unset($_SESSION['edit_inventory']);
		unset($_SESSION['add_inventory']);
		$in = $CMS->inventory->get_info($CMS->input['id']);
		if(!is_array($in))
		{
			$_SESSION['error_msg'] = "Phiếu kiểm salon không tồn tại";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory");

		}

		$approve_return = $CMS->inventory->approve($in);
		if($approve_return['status'] == true)
		{
		 
			if($approve_return['data_output'][0] > 0 AND $approve_return['data_output'][1] > 0)
			{
				//tao phieu xuat nhap thanh cong
				$msg =", Mã phiếu xuất/nhập: <a href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei&act=show&id={$approve_return['data_output'][0]}'>REQ{$approve_return['data_output'][0]}</a>, <a href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei&act=show&id={$approve_return['data_output'][1]}'>REQ{$approve_return['data_output'][1]}</a> ";
			}
			elseif($approve_return['data_output'][0]> 0 AND $approve_return['data_output'][1] == 0) 
			{
				//tao phieu xuat nhap thanh cong
				$msg =", Mã phiếu xuất/nhập: <a href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei&act=show&id={$approve_return['data_output'][0]}'>REQ{$approve_return['data_output'][0]}</a>  ";
			}elseif($approve_return['data_output']['request_id'] == 0 AND $approve_return['data_output']['request_im_id'] > 0) 
			{
				//tao phieu xuat nhap thanh cong
				$msg =", Mã phiếu xuất/nhập: <a href='{$CMS->vars['root_domain']}/?site=store_request&stage=request_ei&act=show&id={$approve_return['data_output'][1]}'>REQ{$approve_return['data_output'][1]}</a>  ";
			}

			$_SESSION['msg'] = "Đã tạo phiếu xuất/nhập cho phiếu kiểm salon #{$in['inventory_name']} thành công  {$msg}";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory");

		}
		else
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory&act=approve&id={$in['inventory_id']}");
		}

	 
 
	}
 


	public function add() {
		global $CMS, $DB, $member;


		$CMS->output.=$this->html->add();
	}

	public function add_step_1() {
		global $CMS, $DB, $member;
		unset($_SESSION['add_inventory']);
		$store_id = intval($CMS->input['store_id']);
		$inventory_type = intval($CMS->input['inventory_type']);
		$inventory_pgroup =  $CMS->input['inventory_pgroup'];
		$inventory_note = $CMS->class->editor->input("inventory_note");
 
		$flag = true;

		if($store_id == 0 OR empty($store_id))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['in_store_err']}";
			$flag = false;
		}

		if($inventory_type == 0 OR empty($inventory_type))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['inventory_type_err']}";
			$flag = false;
		}
 
		if($inventory_type == 2  AND count($inventory_pgroup) <= 0 )
		{ 
			$_SESSION['error_msg'] = "{$CMS->lang['inventory_pgroup_err']}";

			$flag = false;
		}
 
		//Convert array pgroup to string
		$inventory_pgroup_str = "";
		if(count($inventory_pgroup) > 0)
		{
			foreach ($inventory_pgroup as $key => $value) {
				# code...
				if($value != "")
				{
					$inventory_pgroup_str .= ",".$value.",";
				}
			}
		}

		// Init session
		$_SESSION['add_inventory']['store_id'] = $store_id;
		$_SESSION['add_inventory']['inventory_type'] = $inventory_type;
		$_SESSION['add_inventory']['inventory_pgroup'] = $inventory_pgroup;
		$_SESSION['add_inventory']['inventory_note'] = $inventory_note;
		$_SESSION['add_inventory']['inventory_pgroup_str'] = $inventory_pgroup_str;


	 	if($flag == true)
	 	{
	 		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory&act=add&subact=step_2");
	 	}
	 	else
	 	{
	 		$CMS->output.=$this->html->add();
	 	}
	
	}


	public function add_do() {
		global $CMS, $DB, $member;
		$in = $CMS->inventory->add();
	 	if(is_array($in))
	 	{
	 		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory");
	 	}
	 	else
	 	{
	 		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory&act=add&subact=step_2");
	 	}
	
	}

	public function edit_item() {
	
		global $CMS, $DB, $member;

	 	
		$in = $CMS->inventory->get_info($CMS->input['id']);
		if(!is_array($in))
		{
			$_SESSION['msg'] = "Phiếu kiểm salon không tồn tại";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory");
		}

  	    $item = $CMS->inventory->get_info_item($CMS->input['ini_id']);
  	    if(!is_array($item))
		{
			$_SESSION['msg'] = "Tài sản của phiếu kiểm salon không tồn tại";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory");
		}
		$CMS->output.=$this->html->edit_item($CMS->inventory->convertvalue($in), $item  );
	 	 
	}

	public function edit_item_do() {
	
		global $CMS, $DB, $member;

	 	
		$in = $CMS->inventory->get_info($CMS->input['id']);
		if(!is_array($in))
		{
			$_SESSION['msg'] = "Phiếu kiểm salon không tồn tại";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory");
		}

  	    $item = $CMS->inventory->get_info_item($CMS->input['ini_id']);

  	    if(!is_array($item))
		{
			$_SESSION['msg'] = "Tài sản trong phiếu kiểm salon không tồn tại";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory");
		}

		if($CMS->inventory->edit_item() == true)
		{
			$_SESSION['msg'] = "Chỉnh sửa thông tin tài sản thành công";
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory&act=show&id={$CMS->input['id']}");
		}
		else
		{
			$CMS->output.=$this->html->edit_item($CMS->inventory->convertvalue($in), $item  );
		}
		
	 	 
	}

	public function edit() {
		global $CMS, $DB, $member;
 
		$in = $CMS->inventory->get_info($CMS->input['id']);
		$CMS->output.=$this->html->edit($in);
	 	 
	}



	public function edit_step_1() {
		global $CMS, $DB, $member;
		unset($_SESSION['edit_inventory']);
		$store_id = intval($CMS->input['store_id']);
		$inventory_type = intval($CMS->input['inventory_type']);
		$inventory_pgroup =  $CMS->input['inventory_pgroup'];
		$inventory_note = $CMS->class->editor->input("inventory_note");
 
		$flag = true;

		if($store_id == 0 OR empty($store_id))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['in_store_err']}";
			$flag = false;
		}

		if($inventory_type == 0 OR empty($inventory_type))
		{
			$_SESSION['error_msg'] = "{$CMS->lang['inventory_type_err']}";
			$flag = false;
		}
 
		if($inventory_type == 2  AND count($inventory_pgroup) <= 0 )
		{ 
			$_SESSION['error_msg'] = "{$CMS->lang['inventory_pgroup_err']}";

			$flag = false;
		}
 
		//Convert array pgroup to string
		$inventory_pgroup_str = "";
		if(count($inventory_pgroup) > 0)
		{
			foreach ($inventory_pgroup as $key => $value) {
				# code...
				if($value != "")
				{
					$inventory_pgroup_str .= ",".$value.",";
				}
			}
		}

		// Init session
		$_SESSION['edit_inventory']['store_id'] = $store_id;
		$_SESSION['edit_inventory']['inventory_type'] = $inventory_type;
		$_SESSION['edit_inventory']['inventory_pgroup'] = $inventory_pgroup;
		$_SESSION['edit_inventory']['inventory_note'] = $inventory_note;
		$_SESSION['edit_inventory']['inventory_pgroup_str'] = $inventory_pgroup_str;


	 	if($flag == true)
	 	{
	 		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory&act=edit&subact=step_2&id={$CMS->input['id']}");
	 	}
	 	else
	 	{
	 		$CMS->output.=$this->html->edit();
	 	}
	
	}




	public function edit_do() {
		global $CMS, $DB, $member;

	 	if($CMS->inventory->edit() == true)
	 	{
	 		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory");
	 	}
	 	else
	 	{
	 		$in = $CMS->inventory->get_info($CMS->input['id']);
	 		$CMS->output.=$this->html->edit($in);
	 	}
	
	}


	public function step_2() {
	
		global $CMS, $DB, $member;

		if(isset($_SESSION['add_inventory']) AND count($_SESSION['add_inventory']) > 0)
		{
			$in['store_id'] = $_SESSION['add_inventory']['store_id'];
			$in['inventory_type'] = $_SESSION['add_inventory']['inventory_type'];
			//$in['inventory_pgroup'] = $_SESSION['add_inventory']['inventory_pgroup'];
			$in['inventory_note'] = $_SESSION['add_inventory']['inventory_note'];
			$in['inventory_pgroup'] = $_SESSION['add_inventory']['inventory_pgroup_str'];
			 
		}
		else
		{
			$in = $CMS->inventory->get_info($CMS->input['id']);
 
			
		}

		if($in['inventory_type'] == 2 OR $in['inventory_type'] == 3)
		{
			// Load lis sp
			$list_ass = $CMS->inventory->list_asset($in);
		} 
		$CMS->output.=$this->html->addproduct($CMS->inventory->convertvalue($in), $list_ass);
	 	 
	}


	public function edit_step_2() {
	
		global $CMS, $DB, $member;
 
		if(isset($_SESSION['edit_inventory']) AND count($_SESSION['edit_inventory']) > 0)
		{
			$in['inventory_id'] = $CMS->input['id'];
			$in['store_id'] = $_SESSION['edit_inventory']['store_id'];
			$in['inventory_type'] = $_SESSION['edit_inventory']['inventory_type'];
			//$in['inventory_pgroup'] = $_SESSION['add_inventory']['inventory_pgroup'];
			$in['inventory_note'] = $_SESSION['edit_inventory']['inventory_note'];
			$in['inventory_pgroup'] = $_SESSION['edit_inventory']['inventory_pgroup_str'];
			 
		}
		else
		{
			$in = $CMS->inventory->get_info($CMS->input['id']);
 
			
		}
 
		if($in['inventory_type'] == 2 OR $in['inventory_type'] == 3)
		{
			// Load lis sp
			$list_ass = $CMS->inventory->list_asset($in);
		} 
		$CMS->output.=$this->html->editproduct($CMS->inventory->convertvalue($in), $list_ass);
	 	 
	}


	public function addproduct_do() {
	
		global $CMS, $DB, $member;
		
 
		if($CMS->inventory->addproduct_do() == true)
		{
		 	$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory");
		} 
		else
		{
			$in = $CMS->inventory->get_info($CMS->input['id']);

			if($in['inventory_type'] == 2 OR $in['inventory_type'] == 3)
			{
				// Load lis sp
				$list_ass = $CMS->inventory->list_asset($in);
			} 
			$CMS->output.=$this->html->addproduct($CMS->inventory->convertvalue($in), $list_ass);
		}
		
	 	 
	}


	public function show() {
		global $CMS, $DB, $member;
		if (!empty($CMS->input['id'])) {

			$data_info = $CMS->inventory->get_info($CMS->input['id']);
			if ($data_info) {

				$CMS->output.= $this->html->show($CMS->inventory->convertvalue($data_info));
				$CMS->output.=$CMS->global->logs("inventory_{$CMS->input['id']}");
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory");
	}

	 public function del_item() {
		global $CMS, $DB, $member;

		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->inventory->get_info($CMS->input['id']);
			if ($data_info) {

				if($CMS->inventory->del_item($data_info['inventory_id']) == true)
				{
					$_SESSION['msg'] = "Xóa sản phẩm trong phiếu kiểm salon thành công";
				}
				else
				{
					$_SESSION['error_msg'] = "Có lỗi xảy ra khi xóa sản phẩm trong phiếu kiểm salon";
				}
			 
			}
			else
			{
				$_SESSION['error_msg'] = "Phiếu kiểm salon không tồn tại";
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory");
	}


	public function delete() {
		global $CMS, $DB, $member;

		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->inventory->get_info($CMS->input['id']);

		  

			if ($data_info) {

				if($CMS->inventory->deleted($data_info['inventory_id']) == true)
				{
					$_SESSION['msg'] = "Xóa phiếu kiểm salon thành công";
				}
				else
				{
					$_SESSION['error_msg'] = "Có lỗi xảy ra khi xóa phiếu kiểm salon";
				}
			 
			}
			else
			{
				$_SESSION['error_msg'] = "Phiếu kiểm salon không tồn tại";
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory");
	}
	public function search() {
		global $CMS, $DB, $member;
	 	$search_type = $CMS->input['search_type'];
		$in_name = $CMS->input['in_name'];
		$in_type = $CMS->input['in_type'];
		$store_id = $CMS->input['store_id'];
		$user_id = $CMS->input['user_id'];
		$in_balance = $CMS->input['in_balance'];

		$ini_name = $CMS->input['ini_name'];
		$ini_amount = intval($CMS->input['ini_amount']);
		
		$str = '';
	 	if (isset($search_type)) {
			$str.='&search_type='.$search_type;
		}
		
	 	if (isset($ini_name)) {
			$str.='&ini_name='.$ini_name;
		}
		if (isset($ini_amount)) {
			$str.='&ini_amount='.$ini_amount;
		}

		if($search_type == 1)
		{
			if (isset($in_name)) {
				$str.='&ini_name='.$in_name;
			}
		}
		else
		{
			if (isset($in_name)) {
				$str.='&in_name='.$in_name;
			}
		}
		 
		if (isset($in_type)) {
			$str.='&in_type='.$in_type;
		}
		if (isset($store_id)) {
			$str.='&store_id='.$store_id;
		}
		if (isset($user_id)) {
			$str.='&user_id='.$user_id;
		}

		if (isset($CMS->input['in_time_from'])&&preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/",$CMS->input['in_time_from'])) {
			$tmp=explode('/',$CMS->input['in_time_from']);
			$str.='&time_from='.(string)(mktime(0,0,0,$tmp[1],$tmp[0],$tmp[2]));
		}
		if (isset($CMS->input['in_time_to'])&&preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/",$CMS->input['in_time_to'])) {
			$tmp=explode('/',$CMS->input['in_time_to']);
			$str.='&time_to='.(string)(mktime(24,0,0,$tmp[1],$tmp[0],$tmp[2]));
		}
		if (isset($in_balance)) {
			$str.='&in_balance='.$in_balance;
		}


		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=inventory{$str}");
	}


	public function search_gp_product_ajax() {
		global $CMS, $DB, $member;
	 	$p_type = $CMS->input['p_type'];

		$option = $CMS->inventory->getParent_bytype($p_type);
		print json_encode(array("status" => "success", "msg" => "", "data_option" => $option));exit;
	}



	public function ajax_get_data_inventory() {
		global $CMS, $DB, $member;
	 	$group_product_id = $CMS->input['group_product_id'];

		$info = $CMS->inventory->getInfo($group_product_id);
		if(is_array($info))
		{

			print json_encode(array("status" => "success",   "data" => $info));exit;
		}
		else
		{
			print json_encode(array("status" => "error", "msg" => "Dữ liệu không tồn tại!"));exit;
		}
 
	}



	public function ajax_edit_inventory() {
		global $CMS, $DB, $member;
 
		if (isset($CMS->input['inventory_id'])) {
			$data_info = $CMS->inventory->getInfo($CMS->input['inventory_id']);	 
 
			if (is_array($data_info)) {

 
					$base64_string = $CMS->input['base64_image'];
		 		
					$pg_parent = urldecode($CMS->input['pg_parent']);
					$pg_name = urldecode($CMS->input['pg_name']);
					$pg_code = urldecode($CMS->input['pg_code']);
					$pg_avartar = $_FILES['pg_avartar'];
					$pg_status = urldecode($CMS->input['pg_status']);
					$pg_description = urldecode($CMS->input['pg_description']);
					
					$check=true;
					if (empty($pg_name)) {
						$check=false;
					 
						print json_encode(array("status" => "error", "msg" => $CMS->lang['pg_name_err'], "data_option" => $option));exit;

					}
					 
					if (empty($pg_status) || !in_array($pg_status, array(0,1))) {
						$check=false;
						print json_encode(array("status" => "error", "msg" => $CMS->lang['pg_statupg_err'], "data_option" => $option));exit;

					 
					}
					 
					if ($check) {
						if ( $pg_parent != "") {
							if (! $CMS->inventory->getInfo($pg_parent, 'inventory_id')) {
								$check=false;
							 
								print json_encode(array("status" => "error", "msg" => $CMS->lang['pg_parent_err'], "data_option" => $option));exit;

							}
						} else {
							$pg_parent = 0;
						}
						if ($CMS->inventory->checkName($pg_name, $data_info['inventory_id'])) {
							$check=false;
				 
							print json_encode(array("status" => "error", "msg" => $CMS->lang['pg_name_exist'], "data_option" => $option));exit;

						}
						if ($CMS->inventory->checkCode($pg_code, $data_info['inventory_id'])) {
							$check=false;
						 
							print json_encode(array("status" => "error", "msg" => $CMS->lang['pg_code_exist'], "data_option" => $option));exit;
						}
						if ( $pg_avartar['tmp_name'] != "") {
							$file_ext = $CMS->class->attachment->get_ext( $pg_avartar['name'] );
							if ($pg_avartar['size'] > 3*1024*1024 ||   $CMS->class->attachment->is_image($pg_avartar['name'], $file_ext) == false  ) {
								$check = false;
								print json_encode(array("status" => "error", "msg" => $CMS->lang['pg_avartar_err'], "data_option" => $option));exit;
							}
 
						}
						 
					}
					if ($check) {
						
						if ( $pg_avartar['tmp_name'] !="") {
 
							$dir = $CMS->vars['upload_dir'].'/category';
							$data_info['inventory_avartar'] = !empty($data_info['inventory_avartar']) ? $data_info['inventory_avartar'] : $data_info['inventory_id']."_".$CMS->class->random->randomString(5,'abcdefghijklmnopqrstuvwxyz').'.jpg';
					 
							if (! is_dir($dir)) {
								mkdir($dir, 0755, true);
							}
							//echo  $dir.'/'.$data_info['inventory_avartar'];exit;
 
								@copy($pg_avartar['tmp_name'], $dir.'/'.$data_info['inventory_avartar']) or die ("Could not be upload.");
 
								$CMS->class->image->quality = 1;
							    $CMS->class->image->resize("{$CMS->vars['upload_dir']}/category/".$data_info['inventory_avartar'], "{$CMS->vars['upload_dir']}/category/thumbnail/".$data_info['inventory_avartar'], 220,150);	


						}


						$CMS->inventory->edit($data_info,$pg_parent, $pg_name, $pg_code,$pg_status, $pg_description);
		 

						print json_encode(array("status" => "success", "msg" => $CMS->lang['pg_edit_success'], "pg_type" => $CMS->input['pg_type'], "pg_id" => $CMS->input['inventory_id']));exit;

						 
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

		if($CMS->permit['inventory_add'])
		{
			$pg_id = $CMS->inventory->addAjax();
			if($pg_id)
			{
				$data = $CMS->inventory->getInfo($pg_id);
				$data['data_option'] = $CMS->inventory->getMultiOptionCategory(1);
				print json_encode(array("status" => "success", "msg" => "{$CMS->lang['msg_add_successful_inventory']}\"{$data['inventory_name']}\"", "data" => $data));exit;
			}else
			{
				// $data = $CMS->shipment->get_info($CMS->input['shi_id']);
				$pg_name = urldecode($CMS->input['pg_name']);
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_add_error_inventory']}\"{$pg_name}\"", "data" => $data));exit;
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
		$data = $CMS->inventory->getInfo($pg_id);
		$data['data_option'] = $CMS->inventory->getMultiOptionCategory(1,$pg_id,1,$data['inventory_parent']);
		print json_encode(array("status" => "success", "msg" => "", "data" => $data));exit;
		
	}

	public function ajax_edit_pg()
	{
		global $CMS;

		if($CMS->permit['inventory_edit'])
		{
			$data = $CMS->inventory->editAjax();
			if($data)
			{
				print json_encode(array("status" => "success", "msg" => "{$CMS->lang['msg_edit_successful_inventory']}\"{$data['inventory_name']}\"", "data" => $data));exit;
			}else
			{
				// $data = $CMS->shipment->get_info($CMS->input['shi_id']);
				$pg_name = urldecode($CMS->input['pg_name']);
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_edit_error_inventory']}\"{$pg_name}\"", "data" => $data));exit;
			}
			
		}else
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_no_permision']}"));exit;
		}
		
	}

	function searchkey()
	{
		global $CMS;

		$key_search = urldecode($CMS->input['term']);
		$data = $CMS->inventory->searchKey($key_search, 0); // search ở trang listing phiếu type = 0
		header('Content-Type: application/json');
		print json_encode($data);exit;

	}
}

?>
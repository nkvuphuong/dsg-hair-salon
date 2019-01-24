<?php

$manufacture = new Manufacture;
$manufacture->auto_run();

class Manufacture {
	public $html;
	function __construct() {
		global $CMS, $DB, $member;
		if (!$CMS->vars['is_login']) $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=login");
		$CMS->class->language->load("manufacture");
		$this->html = $CMS->class->template->load_template("skin_manufacture");
		$CMS->core->page_title = "-> {$CMS->lang['m_title']}";
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
				if(\lib\input::get('subact') == "ajax_get_data_manufacture")
				{
					$this->ajax_get_data_manufacture();
				}
				else if(\lib\input::get('subact') == "ajax_add_manufacture")
				{
					$this->ajax_add_manufacture();
				}		
				else if(\lib\input::get('subact') == "ajax_edit_manufacture")
				{
					$this->ajax_edit_manufacture();
				}
				else
				{
                    if(\lib\input::get('subact') == 'clear_cache')
                    {
                        $CMS->class->cache->mdelete($CMS->manufacture->cache_prefix);
                        $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                    }
					$this->defaultPage();
				}
				
				break;
		}
	}
	public function defaultPage() {
		global $CMS, $DB, $member;
		$out .= $this->html->head();
		$data = $CMS->manufacture->listing();
		$count = count($data);
		if ($data) {
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
			$m_manufacture_parent = $CMS->input['m_manufacture_parent'];
			$m_name = $CMS->input['m_name'];
			$m_code = $CMS->input['m_code'];
			$m_avartar = $_FILES['m_avartar'];
			$m_status = $CMS->input['m_status'];
			$m_description = $CMS->input['m_description'];
			
			$check=true;
			if (empty($m_name)) {
				$check=false;
				$_SESSION['m_error'] .= $CMS->lang['m_name_err'].'<br>';
			}
			if (empty($m_code)) {
				$check=false;
				$_SESSION['m_error'] .= $CMS->lang['m_code_err'].'<br>';
			}
			if (empty($m_status) || !in_array($m_status, array(0,1))) {
				$check=false;
				$_SESSION['m_error'] .= $CMS->lang['m_status_err'].'<br>';
			}
			if (empty($m_description)) {
				$check=false;
				$_SESSION['m_error'] .= $CMS->lang['m_description_err'].'<br>';
			}
			if ($check) {
				if (!empty($m_manufacture_parent)) {
					if (! $CMS->manufacture->getInfo($m_manufacture_parent, 'manufacture_id')) {
						$check=false;
						$_SESSION['m_error'] .= $CMS->lang['m_parent_err'].'<br>';
					}
				} else {
					$m_manufacture_parent = 0;
				}
				if ($CMS->manufacture->getInfo($m_name, 'manufacture_id')) {
					$check=false;
					$_SESSION['m_error'] .= $CMS->lang['m_name_exist'].'<br>';
				}
				if (!empty($m_avartar['tmp_name'])) {
					$file_ext = $CMS->class->attachment->get_ext( $m_avartar['name'] );
					if ($m_avartar['size'] > 3*1024*1024 ||   $CMS->class->attachment->is_image($m_avartar['name'], $file_ext) == false  ) {
						$check = false;
						$_SESSION['m_error'] .= $CMS->lang['m_avartar_err'].'<br>';
					}
				}
			}
			if ($check) {
				$id = $CMS->manufacture->add($m_manufacture_parent, $m_name, $m_code,$m_status, $m_description);
				if (!empty($id) && !empty($m_avartar['tmp_name'])) {
					$dir = $CMS->vars['upload_dir'].'/manufacture';
					$img = $id.$CMS->class->random->randomString(5,'abcdefghijklmnopqrstuvwxyz').'.jpg';
					if (! is_dir($dir)) {
						mkdir($dir, 0755, true);
					}
					if (move_uploaded_file($m_avartar["tmp_name"], $dir.'/'.$img)) {
						$CMS->manufacture->upAvartar($id, $img);
					}
				}
				$_SESSION['m_success']=$CMS->lang['m_add_success'];
				if($CMS->input['action_redirect'] == "add")
				{
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=manufacture&act=add");
				}
				elseif($CMS->input['action_redirect'] == "detail")
				{
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=manufacture&act=show&id={$id}");
				}
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=manufacture");
			}
		}
		$CMS->output.=$this->html->add();
	}
	public function edit() {
		global $CMS, $DB, $member;
		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->manufacture->getInfo($CMS->input['id']);
			if ($data_info) {
				if ($CMS->input['request_method'] == 'post') {
					$m_manufacture_parent = $CMS->input['m_manufacture_parent'];
					$m_name = $CMS->input['m_name'];
					$m_code = $CMS->input['m_code'];
					$m_avartar = $_FILES['m_avartar'];
					$m_status = $CMS->input['m_status'];
					$m_description = $CMS->input['m_description'];
					
					$check=true;
					if (empty($m_name)) {
						$check=false;
						$_SESSION['m_error'] .= $CMS->lang['m_name_err'].'<br>';
					}
					if (empty($m_code)) {
						$check=false;
						$_SESSION['m_error'] .= $CMS->lang['m_code_err'].'<br>';
					}
					if (empty($m_status) || !in_array($m_status, array(0,1))) {
						$check=false;
						$_SESSION['m_error'] .= $CMS->lang['m_status_err'].'<br>';
					}
					if (empty($m_description)) {
						$check=false;
						$_SESSION['m_error'] .= $CMS->lang['m_description_err'].'<br>';
					}
					if ($check) {
						if ( $m_manufacture_parent != "") {
							if (! $CMS->manufacture->getInfo($m_manufacture_parent, 'manufacture_id')) {
								$check=false;
								$_SESSION['m_error'] .= $CMS->lang['m_parent_err'].'<br>';
							}
						} else {
							$m_manufacture_parent = 0;
						}
						$tmp = $CMS->manufacture->getInfo($m_name, 'manufacture_id');
						if ( $tmp!="" && $tmp != $data_info['manufacture_id']) {
							$check=false;
							$_SESSION['m_error'] .= $CMS->lang['m_name_exist'].'<br>';
						}
						if ( $m_avartar['tmp_name'] != "") {
							$file_ext = $CMS->class->attachment->get_ext( $m_avartar['name'] );
							if ($m_avartar['size'] > 3*1024*1024 ||   $CMS->class->attachment->is_image($m_avartar['name'], $file_ext) == false  ) {
								$check = false;
								$_SESSION['m_error'] .= $CMS->lang['m_avartar_err'].'<br>';
							}

							 
						}
					}
					if ($check) {
						if ( $m_avartar['tmp_name'] != "") {
							$dir = $CMS->vars['upload_dir'].'/manufacture';
							$data_info['manufacture_avartar'] = !empty($data_info['manufacture_avartar']) ? $data_info['manufacture_avartar'] : $data_info['manufacture_id'].$CMS->class->random->randomString(5,'abcdefghijklmnopqrstuvwxyz').'.jpg';
							if (! is_dir($dir)) {
								mkdir($dir, 0755, true);
							}
							move_uploaded_file($m_avartar["tmp_name"], $dir.'/'.$data_info['manufacture_avartar']);
						}
						$CMS->manufacture->edit($data_info,$m_manufacture_parent, $m_name, $m_code,$m_status, $m_description);
						$_SESSION['m_success']=$CMS->lang['m_edit_success'];
						if($CMS->input['action_redirect'] == "edit")
						{
							$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=manufacture&act=edit&id={$CMS->input['id']}");
						}
						elseif($CMS->input['action_redirect'] == "detail")
						{
							$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=manufacture&act=show&id={$CMS->input['id']}");
						}
						$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=manufacture");
					}
				}
				$CMS->output.=$this->html->edit($data_info);
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=manufacture");
	}
	public function show() {
		global $CMS, $DB, $member;
		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->manufacture->getInfo($CMS->input['id']);
			if ($data_info) {
				$CMS->output.=$this->html->show($CMS->manufacture->convertvalue($data_info));
				$CMS->output.=$CMS->global->logs("Edit_manufacture_{$CMS->input['id']}");
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=manufacture");
	}
	public function delete() {
		global $CMS, $DB, $member;
		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->sell_customer->getInfo($CMS->input['id']);
			if ($data_info) {
				$CMS->sell_customer->deleted($data_info['sc_id']);
				$_SESSION['gc_success']=$CMS->lang['sc_deleted_success'];
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sell_customer");
	}
	public function search() {
		global $CMS, $DB, $member;
		$m_id_search  = $CMS->input['m_id_search'];
		$m_name_search = $CMS->input['m_name_search'];
		
		$str = '';
		if ($CMS->manufacture->getInfo($m_id_search, 'm_id')) {
			$str.='&m_id='.$m_id_search;
		}
		if (!empty($m_name_search)) {
			$str.='&m_name='.$m_name_search;
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=manufacture{$str}");
	}
	public function getDistrict() {
		global $CMS, $DB, $member;
		$str='';
		if (!empty($CMS->input['id'])) {
			$district = $CMS->sell_customer->getAllDistrictVN($CMS->input['id']);
			if (count($district) > 0) {
				foreach ($district as $d) {
					$str .= "<option value='{$d['dv_id']}'>{$d['dv_type']} {$d['dv_name']}</option>";
				}
			}
		}
		exit($str);
	}

	public function ajax_add_manufacture() {
		global $CMS, $DB, $member;
 
		$m_id = $CMS->manufacture->add_ajax();
				if($m_id)
				{
					$option_manufacture = $CMS->manufacture->getOptionManufacture($m_id);
					print json_encode(array("status" => "success", "msg" => "{$CMS->lang['title_add_manufacture_success']}", "data_option" => $option_manufacture));exit;
				}else
				{
					print json_encode(array("status" => "error", "msg" => "{$CMS->lang['title_add_manufacture_error']}"));exit;
				}
	}
	public function ajax_get_data_manufacture()
	{
		global $CMS;
		// print_r($CMS->permit);exit;
		// if($CMS->permit['product_add']) // Tam thoi ko check phan quyen
		// {
			$manufacture_id = intval($CMS->input['man_id']);
			$data = $CMS->manufacture->getInfo($manufacture_id);
			print json_encode($data);exit;	
		// }
	}

	public function ajax_edit_manufacture()
	{
		global $CMS, $DB;
		 if (!empty($CMS->input['m_manufacture_id'])) {
			$data_info = $CMS->manufacture->getInfo($CMS->input['m_manufacture_id']);
			if ($data_info) {
				if ($CMS->input['request_method'] == 'post') {
					$m_manufacture_parent = $CMS->input['m_manufacture_parent'];
					$m_name = urldecode($CMS->input['m_name']);
					$m_code = urldecode($CMS->input['m_code']);
			 
					$m_status = isset($CMS->input['m_status']) ? intval($CMS->input['m_status']) : 1;
					$m_description = $CMS->class->editor->input(urldecode($CMS->input['m_description']), "text");
					$check=true;
					if (empty($m_name)) {
						$check=false;
				 
						print json_encode(array("status" => "error", "msg" => "{$CMS->lang['m_name_err']}"));
						exit;


					}
					// if (empty($m_code)) {
					// 	$check=false;
						 
					// 	print json_encode(array("status" => "error", "msg" => "{$CMS->lang['m_code_err']}"));
					// 	exit;

					// }
					// if (empty($m_status) || !in_array($m_status, array(0,1))) {
					// 	$check=false;
					 
					// 	print json_encode(array("status" => "error", "msg" => "{$CMS->lang['m_status_err']}"));
					// 	exit;
					// }
					// if (empty($m_description)) {
					// 	$check=false;
						 
					// 		print json_encode(array("status" => "error", "msg" => "{$CMS->lang['m_description_err']}"));
					// 	exit;
					// }
					// if ($check) {
					// 	if (!empty($m_manufacture_parent)) {
					// 		if (! $CMS->manufacture->getInfo($m_manufacture_parent, 'manufacture_id')) {
								 
					// 			$_SESSION['m_error'] .= $CMS->lang['m_parent_err'].'<br>';
					// 			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['m_parent_err']}"));
					// 				exit;

					// 		}
					// 	} else {
					// 		$m_manufacture_parent = 0;
					// 	}
						 
					// }
					if ($check) 
					{

						// Check upload
						$file_tmp = isset($_FILES['upload_img']['tmp_name']) ? $_FILES['upload_img']['tmp_name'] : "";
						$file_name = isset($_FILES['upload_img']['name']) ? $_FILES['upload_img']['name'] : "";
						$file_type = isset($_FILES['upload_img']['type']) ? $_FILES['upload_img']['type'] : "";
						$file_size = isset($_FILES['upload_img']['size']) ? $_FILES['upload_img']['size'] : "";
						$file_error = isset($_FILES['upload_img']['error']) ? $_FILES['upload_img']['error'] : "";
						
						$file_ext = $CMS->class->attachment->get_ext( $file_name );

						// Check dung luong file upload
						$max = 10;
						$max_file_upload = 1024*1024*$max;
						if($file_size > $max_file_upload )
						{
							$arr_img = array("msg" => $CMS->lang['msg_maxfile_upload_img'].$max."MB" , "status" => "error");
							print json_encode($arr_img);exit;
						}

						
						$file_name = str_replace( " ", "_", $file_name );
						$file_location = strtolower(time()."_".$file_name);
						$manufacture_avartar = "";
						if ( $file_name )
						{
							if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
							{
								$arr_img = array("msg" => $CMS->lang['invalid_upload_file'] , "status" => "error");
								print json_encode($arr_img);exit;
								// return false;
							}

							$check = @copy($file_tmp, "{$CMS->vars['upload_dir']}/manufacture/{$file_location}");
							if(!$check)
							{
								$arr_img = array("msg" => $CMS->lang['msg_error_upload_img'], "status" => "error");
								print json_encode($arr_img);exit;
							}
							
							$data_info['manufacture_avartar'] = $file_location;
						}
						 
						$data = $CMS->manufacture->edit($data_info,$m_manufacture_parent, $m_name, $m_code,$m_status, $m_description);
						$option_manufacture = $CMS->manufacture->getOptionManufacture($data_info['manufacture_id']);


						print json_encode(array("status" => "success", "msg" => "{$CMS->lang['m_edit_success']}", "data_option" => $option_manufacture));
									exit;


					 
					}
				}
			 
			}
		}
	}


}

?>
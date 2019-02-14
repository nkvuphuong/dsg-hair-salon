<?php

use core\ezy;
use lib\input;
use models\dashboard;

//Load models
ezy::load_model("report");

$customer = new Customer;
$customer->auto_run();

class Customer {
	public $html;

	public function auto_run() 
	{
		global $CMS, $DB, $member;

		$CMS->class->language->load("customer");
		$this->html = $CMS->class->template->load_template("skin_customer");
		$CMS->core->page_title = "-> {$CMS->lang['cus_title']}";
		
		switch ($CMS->input['act']) {
			case 'add':
				if(\lib\input::get('subact') == "quickadd")
				{	
					$this->quickadd();
				}else
				{
					$this->add();
				}
				break;
			case 'edit':
				if(\lib\input::get('subact') == "quickedit")
				{	
					$this->quickedit();
				}else
				{
					$this->edit();
				}
				
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
            case 'import':
                $this->import();
                break;
            case 'export':
                $this->export();
                break;
            case 'service_history':
                $this->service_history();
                break;
			default:

				if(\lib\input::get('subact') == "quicksearch")
				{	
					$this->quicksearch();

				}elseif(\lib\input::get('subact') == "ajax_data_cus")
				{	
					$this->ajax_data_cus();

				}elseif(\lib\input::get('subact') == "search_customer_ajax")
				{
					$this->search_customer_ajax();
				}
                elseif(\lib\input::get('subact') == 'autocomplete')
                {
                    $this->autocomplete();
                }
				/*elseif(\lib\input::get('subact') == "export")
				{
					$this->export();
				}elseif(\lib\input::get('subact') == "import")
				{
					$this->import();
				}*/
				else
				{
                    if(\lib\input::get('subact') == 'clear_cache')
                    {
                        $CMS->class->cache->mdelete($CMS->customer->cache_prefix);
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
		// print_r($_SESSION);exit;


        if(isset($CMS->input['cus_time_from']) && $CMS->input['cus_time_from'] != '')
        {
            $CMS->input['time_from'] = $CMS->class->date->date2time($CMS->input['cus_time_from']);
        }

        if(isset($CMS->input['cus_time_to']) && $CMS->input['cus_time_to'] != '')
        {
            $CMS->input['time_to'] = $CMS->class->date->date2time($CMS->input['cus_time_to'])+(3600*24)-1;
        }

		$out .= $this->html->head();
		$data = $CMS->customer->listing();

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
            $cus_full_name = $CMS->input['cus_full_name'];
            $cus_address = $CMS->input['cus_address'];
            $cus_address2 = $CMS->input['cus_address2'];
            $cus_type = $CMS->input['cus_type'];
            $cus_phone = preg_replace('/(\D+)/', '', $CMS->input['cus_phone']) ;
            $cus_company = $CMS->input['cus_company'];
            $cus_company_address = $CMS->input['cus_company_address'];
            $cus_company_email = $CMS->input['cus_company_email'];
            $cus_company_phone = $CMS->input['cus_company_phone'];
            $cus_email_invoice = $CMS->input['cus_email_invoice'];
            $cus_tax_code = $CMS->input['cus_tax_code'];
            $cus_group = $CMS->input['cus_group'] ? input::jsonEncode($CMS->input['cus_group'], 0) : null;
            $cus_sex = $CMS->input['cus_sex'];
            $cus_birthday = $CMS->input['cus_birthday'];
            $cus_country = $CMS->input['cus_country'];
            $cus_city = $CMS->input['cus_city'];
            $cus_district = $CMS->input['cus_district'];
            $cus_email = $CMS->input['cus_email'];
            $cus_cus = $CMS->input['cus_cus'];
            $cus_note = $CMS->input['cus_note'];

            $check=true;
            if (empty($cus_full_name)) {
                $check=false;
                $_SESSION['error_msg'] .= $CMS->lang['cus_full_name_err'].'<br>';
            }


            if (empty($cus_email)) {
                $check=false;
                $_SESSION['error_msg'] .= $CMS->lang['cus_email_err'].'<br>';
            }



            if ($check) {

                if (! Validate::isEmail($cus_email)) {
                    $check=false;
                    $_SESSION['error_msg'] .= $CMS->lang['invalid_email'].'<br>';
                }

                if ($cus_company_email && ! Validate::isEmail($cus_company_email)) {
                    $check=false;
                    $_SESSION['error_msg'] .= $CMS->lang['invalid_email'].'<br>';
                }
            }

			if ($check) {
				$customer_image = "";
				 // Upload image
				$file_tmp = isset($_FILES['cus_image']['tmp_name']) ? $_FILES['cus_image']['tmp_name'] : "";
				$file_name = isset($_FILES['cus_image']['name']) ? $_FILES['cus_image']['name'] : "";
				$file_type = isset($_FILES['cus_image']['type']) ? $_FILES['cus_image']['type'] : "";
				$file_size = isset($_FILES['cus_image']['size']) ? $_FILES['cus_image']['size'] : "";
				$file_error = isset($_FILES['cus_image']['error']) ? $_FILES['cus_image']['error'] : "";
				
				$file_ext = $CMS->class->attachment->get_ext( $file_name );

				// Check dung luong file upload
				$max = 7;
				$max_file_upload = 1024*1024*$max;
				if($file_size > $max_file_upload )
				{
					$_SESSION['error_msg'] = $CMS->lang['msg_maxfile_upload'].$max."MB";
					return false;
				}

				$file_name = str_replace( " ", "_", $file_name );
				$file_location = strtolower(time()."_".$file_name);
				$cat_image = "";
		                
				if ( $file_name )
				{
					if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
					{
						$_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
						return false;
					}

					$CMS->class->image->check_folder_img("customer","",0);

					$imgPath = "{$CMS->vars['upload_dir']}/customer/{$file_location}";
					$check = @copy($file_tmp, $imgPath);
					if(!$check)
					{
						$_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
						return false;
					}

					//Create thumb
		            /*foreach ($this->thumb_size as $keySize => $valSize)
		            {
		                $CMS->class->image->resize($imgPath, \lib\image::getThumb($imgPath, $this->thumb_folder, "{$keySize}_"), $valSize);
		            }*/

					$customer_image = $file_location;
				}
				

                $id = $CMS->customer->add($cus_full_name, $cus_address, $cus_type, $cus_phone, $cus_company, $cus_tax_code, $cus_group, $cus_sex, $cus_birthday, $cus_city, $cus_district, $cus_email, $cus_cus, $cus_note, $cus_address2, $cus_country, $cus_company_address, $cus_company_email, $cus_company_phone, $cus_email_invoice, $customer_image);
				
				$_SESSION['msg']=$CMS->lang['cus_add_success'];
				if($CMS->input['action_redirect'] == "add")
				{
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=customer&act=add");
				}
				elseif($CMS->input['action_redirect'] == "detail")
				{
					$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=customer&act=show&id={$id}");
				}
                $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=customer");
			}
		}



		$CMS->output.=$this->html->form($CMS->input  );
	}
	public function edit() {
		global $CMS, $DB, $member;
		if (!empty($CMS->input['id'])) {
            $old_data = $data_info = $CMS->customer->getInfo($CMS->input['id']);
			if ($data_info) {

                $data_info['cus_group'] = input::jsonDecode($data_info['cus_group']);
                $data_info['cus_group'] = !is_array($data_info['cus_group']) ? [] : $data_info['cus_group'];

				if ($CMS->input['request_method'] == 'post') {
                    $data_info['cus_full_name'] = $cus_full_name = $CMS->input['cus_full_name'];
                    $data_info['cus_address'] = $cus_address = $CMS->input['cus_address'];
                    $data_info['cus_company_address'] = $cus_company_address = $CMS->input['cus_company_address'];
                    $data_info['cus_company_email'] = $cus_company_email = $CMS->input['cus_company_email'];
                    $data_info['cus_company_phone'] = $cus_company_phone = $CMS->input['cus_company_phone'];
                    $data_info['cus_address2']=$cus_address2 = $CMS->input['cus_address2'];
                    $data_info['cus_type'] = $cus_type = $CMS->input['cus_type'];
                    $data_info['cus_phone'] = $cus_phone = preg_replace('/(\D+)/', '', $CMS->input['cus_phone']) ;;
                    $data_info['cus_company'] = $cus_company = $CMS->input['cus_company'];
                    $data_info['cus_tax_code'] = $cus_tax_code = $CMS->input['cus_tax_code'];
                    $data_info['cus_group'] = $CMS->input['cus_group'];
                    $cus_group = input::jsonEncode($data_info['cus_group'], 0);
                    $data_info['cus_sex'] = $cus_sex = $CMS->input['cus_sex'];
                    $data_info['cus_birthday'] = $cus_birthday = $CMS->input['cus_birthday'];
                    $data_info['cus_country'] = $cus_country = $CMS->input['cus_country'];
                    $data_info['cus_city'] = $cus_city = $CMS->input['cus_city'];
                    $data_info['cus_district'] = $cus_district = $CMS->input['cus_district'];
                    $data_info['cus_email'] = $cus_email = $CMS->input['cus_email'];
                    $data_info['cus_cus'] = $cus_cus = $CMS->input['cus_cus'];
                    $data_info['cus_note'] = $cus_note = $CMS->input['cus_note'];
                    $data_info['cus_email_invoice'] = $cus_email_invoice = $CMS->input['cus_email_invoice'];
               


                    $check=true;
                    if (empty($cus_full_name)) {
                        $check=false;
                        $_SESSION['error_msg'] .= $CMS->lang['cus_full_name_err'].'<br>';
                    }

                    if (empty($cus_email)) {
                        $check=false;
                        $_SESSION['error_msg'] .= $CMS->lang['cus_email_err'].'<br>';
                    }

                    if ($check) {

                        if (! Validate::isEmail($cus_email)) {
                            $check=false;
                            $_SESSION['error_msg'] .= $CMS->lang['invalid_email'].'<br>';
                        }

                        if ($cus_company_email && ! Validate::isEmail($cus_company_email)) {
                            $check=false;
                            $_SESSION['error_msg'] .= $CMS->lang['invalid_email'].'<br>';
                        }
                    }

                    if ($check) {

                    	// Upload image
						$file_tmp = isset($_FILES['cus_image']['tmp_name']) ? $_FILES['cus_image']['tmp_name'] : "";
						$file_name = isset($_FILES['cus_image']['name']) ? $_FILES['cus_image']['name'] : "";
						$file_type = isset($_FILES['cus_image']['type']) ? $_FILES['cus_image']['type'] : "";
						$file_size = isset($_FILES['cus_image']['size']) ? $_FILES['cus_image']['size'] : "";
						$file_error = isset($_FILES['cus_image']['error']) ? $_FILES['cus_image']['error'] : "";
						
						$file_ext = $CMS->class->attachment->get_ext( $file_name );

						// Check dung luong file upload
						$max = 7;
						$max_file_upload = 1024*1024*$max;
						if($file_size > $max_file_upload )
						{
							$_SESSION['error_msg'] = $CMS->lang['msg_maxfile_upload'].$max."MB";
							return false;
						}

						$file_name = str_replace( " ", "_", $file_name );
						$file_location = strtolower(time()."_".$file_name);
						$cat_image = "";
				                
						if ( $file_name )
						{
							if ( $CMS->class->attachment->is_image($file_name, $file_ext) == false )
							{
								$_SESSION['error_msg'] = $CMS->lang['invalid_upload_file'];
								return false;
							}

							$CMS->class->image->check_folder_img("customer","",0);

							$imgPath = "{$CMS->vars['upload_dir']}/customer/{$file_location}";
							$check = @copy($file_tmp, $imgPath);
							if(!$check)
							{
								$_SESSION['error_msg'] = $CMS->lang['msg_error_upload'];
								return false;
							}
							!unlink("{$CMS->vars['upload_dir']}/customer/{$data_info['cus_image']}");
							  
							$cus_image = $file_location;
						}else
						{
							$cus_image = $data_info['cus_image'];
						}


                        $CMS->customer->edit($old_data, $cus_full_name, $cus_address, $cus_type, $cus_phone, $cus_company, $cus_tax_code, $cus_group, $cus_sex, $cus_birthday, $cus_city, $cus_district, $cus_email, $cus_cus, $cus_note, $cus_address2, $cus_country, $cus_company_address, $cus_company_email, $cus_company_phone ,$cus_email_invoice, $cus_image);
                        $_SESSION['msg']=$CMS->lang['cus_edit_success'];
                        if($CMS->input['action_redirect'] == "edit")
                        {
                            $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=customer&act=edit&id={$CMS->input['id']}");
                        }
                        elseif($CMS->input['action_redirect'] == "detail")
                        {
                            $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=customer&act=show&id={$CMS->input['id']}");
                        }
                        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=customer");
                    }
				}
                $CMS->output.=$this->html->form($data_info);
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=group_customer");
	}
	public function show() {
		global $CMS, $DB, $member;
		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->customer->getInfo($CMS->input['id']);
			if ($data_info) {
				$CMS->output.=$this->html->show($CMS->customer->convertvalue($data_info));
				$CMS->output.=$CMS->global->logs("Edit_customer_{$CMS->input['id']}");
				return;
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=group_customer");
	}
	public function delete() {
		global $CMS, $DB, $member;
		if (!empty($CMS->input['id'])) {
			$data_info = $CMS->customer->getInfo($CMS->input['id']);
			if ($data_info) {
				$CMS->customer->deleted($data_info['cus_id']);
				$_SESSION['msg']=$CMS->lang['cus_deleted_success'];
			}
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=customer");
	}
	public function search() {
		global $CMS, $DB, $member;
		$cus_id_search = isset($CMS->input['cus_id_search']) ? $CMS->input['cus_id_search'] : '';
		$cus_group_search = isset($CMS->input['cus_group_search']) ? $CMS->input['cus_group_search'] : '';
		$cus_full_name_search = isset($CMS->input['p_quick_search']) ? $CMS->input['p_quick_search'] : '';
		$cus_cus_search = isset($CMS->input['cus_cus_search']) ? $CMS->input['cus_cus_search'] : '';
		$cus_type_search = isset($CMS->input['cus_type_search']) ? $CMS->input['cus_type_search'] : '';
		$cus_birthday_search = isset($CMS->input['cus_birthday_search']) ? $CMS->input['cus_birthday_search'] : '';
		$cus_sex_search = isset($CMS->input['cus_sex_search']) ? $CMS->input['cus_sex_search'] : '';
		$cus_city_search = isset($CMS->input['cus_city_search']) ? $CMS->input['cus_city_search'] : '';
		$cus_district_search = isset($CMS->input['cus_district_search']) ? $CMS->input['cus_district_search'] : '';
		$str = '';
		if ($CMS->customer->getInfo($cus_id_search, 'cus_id')) {
			$str.='&cus_id='.$cus_id_search;
		}
		if ($cus_group_search != '') {
			$str.='&group='.$cus_group_search;
		}
		if ($cus_full_name_search != '') {
			$str.='&full_name='.$cus_full_name_search;
		}
		if ($cus_cus_search != '') {
			$str.='&cus='.$cus_cus_search;
		}
		if ($cus_type_search != '') {
			$str.='&type='.$cus_type_search;
		}
		if (Validate::isDate($cus_birthday_search)) {
			$str.='&birthday='.$cus_birthday_search;
		}
		if (in_array($cus_sex_search, array(0,1,2))) {
			$str.='&sex='.$cus_sex_search;
		}
		if ($CMS->customer->getInfoCityVN($cus_city_search, 'cv_id')) {
			$str.='&city='.$cus_city_search;
		}
		if ($CMS->customer->getInfoDistrictVN($cus_district_search, 'dv_id')) {
			$str.='&district='.$cus_district_search;
		}
		if ( isset($CMS->input['cus_time_from']) && preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/",$CMS->input['cus_time_from']) )
		{
			$tmp=explode('/',$CMS->input['cus_time_from']);
			$str.='&cus_time_from='.(string)(mktime(0,0,0,$tmp[1],$tmp[0],$tmp[2]));
		}
		if ( isset($CMS->input['cus_time_to']) && preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/",$CMS->input['cus_time_to']) )
		{
			$tmp=explode('/',$CMS->input['cus_time_to']);
			$str.='&cus_time_to='.(string)(mktime(24,0,0,$tmp[1],$tmp[0],$tmp[2]));
		}
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=customer{$str}");
	}
	public function getDistrict() {
		global $CMS, $DB, $member;
		$str="<option value>{$CMS->lang['select_district']}</option>";
		if (!empty($CMS->input['id'])) {
//			$district = $CMS->country->getOptionDistrict($CMS->input['id']);
			$district = $CMS->customer->getAllDistrictVN($CMS->input['id']);
			 if (count($district) > 0) {
			 	foreach ($district as $d) {
			 		$str .= "<option value='{$d['dv_id']}'>{$d['dv_type']} {$d['dv_name']}</option>";
			 	}
			 }
		}
		exit($str);
//		exit($district);
	}


	public function quicksearch()
	{
		global $CMS;

		$key_search = urldecode($CMS->input['term']);
		$data = $CMS->customer->searchKey($key_search);
		
		header('Content-Type: application/json');
		print json_encode($data);exit;
	}

	public function quickadd()
	{
		global $CMS;

		if(!lib\security::check_token())
        {
            print json_encode(array("status" => "error", "msg" => $CMS->lang['invalid_token'] ));exit;
        }

		$data = $CMS->customer->quickadd();
		if($data)
		{
			print json_encode(array("status" => "success", "msg" => "{$CMS->lang['cus_notify_add_success']} ({$data['cus_full_name']})", "data" => $data));exit;
		}
		else
		{
			print json_encode(array("status" => "error", "msg" => $CMS->lang['cus_notify_add_error'] ));exit;
		}
	}

	public function quickedit()
	{
		global $CMS;

        if(!lib\security::check_token())
        {
            print json_encode(array("status" => "error", "msg" => $CMS->lang['invalid_token'] ));exit;
        }

		// print "<pre>";
		// print_r($CMS->input);exit;
		$data = $CMS->customer->quickedit();
		if($data)
		{
			print json_encode(array("status" => "success", "msg" => "{$CMS->lang['cus_notify_edit_success']} ({$data['cus_full_name']})", "data" => $data));exit;
		}
		else
		{
			print json_encode(array("status" => "error", "msg" => $CMS->lang['cus_notify_edit_error'] ));exit;
		}
	}
	

	public function ajax_data_cus()
	{
		global $CMS;

		$cus_id = intval($CMS->input['cus_id']);
		$data = $CMS->customer->getInfo($cus_id);
		
		if($data)
		{
			print json_encode(array("status" => "success", "msg" => "", "data" => $data));exit;
		}
		else
		{
			print json_encode(array("status" => "error", "msg" => $CMS->lang['cus_not_found'] ));exit;
		}
	}

	public function search_customer_ajax() {
		global $CMS, $DB,$member;
		$key = isset($CMS->input['key']) ? $CMS->input['key'] : '';
		$data = $CMS->customer->searchFullName($key);
		if(count($data) > 0)
		{
 
			print json_encode(array("status" => "success",  "data_option" => $data));exit;
		}else
		{
			print json_encode(array("status" => "error"));exit;
		}
 	
	}

	function export()
	{
		global $CMS;
		$CMS->class->language->load("report");
		$link = \models\report::export_customer_list();
		// print $link;exit;
//		header("location: {$link}");
        ezy::load_model("download");
        \models\download::sendFile($link);
	}

	function import()
	{
		global $CMS;
		$CMS->customer->importCustomerList();
		// print "<pre>";
		// print_r($_SESSION);exit;
		// print $link;exit;
		// header("location: {$CMS->vars['http_referer']}");
		$CMS->global->redirect($CMS->vars['http_referer']);
		// return true;
	}

    function autocomplete()
    {
        global $CMS;
        $CMS->customer->autocomplete();
    }

    public function service_history()
    {
        global $CMS, $tpl;

        $tpl->today = \lib\date::format(time());
        $tpl->yesterday = \lib\date::format(time() - (3600 * 24));
        $tpl->this_week = $CMS->class->date->getFisrtLastInCurrentWeek('timestamp');
        $tpl->last_week = $CMS->class->date->getFisrtLastInLastWeek('timestamp');
        $tpl->this_month = $CMS->class->date->getFisrtLastInCurrentMonth('timestamp');
        $tpl->last_month = $CMS->class->date->getFisrtLastInLastMonth('timestamp');
        $tpl->this_year = $CMS->class->date->getFisrtLastInThisYear('timestamp');
        $tpl->last_year = $CMS->class->date->getFisrtLastInLastYear('timestamp');

        // Output data
        $CMS->output .= ezy::html("service_history_list");
    }
}

?>
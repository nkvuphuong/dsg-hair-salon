<?php

$shipment = new shipment;

$shipment->auto_run();

class shipment {

	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
	 
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['shipment_header']}";
		$CMS->class->language->load("store_request");// Dung cho cac module ajax chuyen tu store_request qua
		// Load the models
		$CMS->shipment->auto_run_shipment();

		// Switch
		switch( $CMS->input["act"] )
		{
			case "show":
				$this->show();
			break;
			case "add":
				$this->add();
			break;
			case "add_do":
				$this->add_do();
			break;
			case "edit":
				$this->edit();
			break;
			case "edit_do":
				$this->edit_do();
			break;
			case "delete":
				$this->delete();
			break;
			case "delete_all":
				$this->delete_all();
			break;
			case "search":
				if(\lib\input::get('subact')=="searchkey")
				{
					$this->searchkey();
				}else
				{
					$this->search();
				}
			break;
			case "search_do":
				$this->search_do();
			break;
			case "arrange":
				$this->arrange();
			break;
			default:
				if(\lib\input::get('subact') == "ajax_get_data_shipment")
				{
					$this->ajax_get_data_shipment();
				}elseif(\lib\input::get('subact') == "ajax_add_shipment")
				{
					$this->ajax_add_shipment();
				}elseif(\lib\input::get('subact') == "ajax_data_shipment")
				{
					$this->ajax_data_shipment();
				}elseif(\lib\input::get('subact') == "ajax_edit_shipment_do")
				{
					$this->ajax_edit_shipment_do();
				}else
				{
					$this->shipment_default();
				}
			
			break;
		}
	}

	//===========================================================================
	//  SHOW INFO
	//===========================================================================

	public function show()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->shipment->html->show( $CMS->shipment->convertvalue($CMS->shipment->get_info()) );
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;
		$shi_name = urldecode($CMS->input['shi_name']);
		$shi_desc = urldecode($CMS->input['shi_description']);
		$shi_description = urldecode($CMS->class->editor->input($shi_desc,"text"));

		if(empty($shi_name) )
		{
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['shipment_empty_name']}"));exit;
		}	
		$shi_time = time();
		$DB->query("INSERT INTO ".root_table."shipment (shi_name, shi_description, shi_time, user_id ) VALUES ('{$shi_name}', '{$shi_description}', '{$shi_time}', '{$member['user_id']}'  )");
		 $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['shipment_add_success']} <b>{$shi_name}</b>")."<br />"; 
		 print json_encode(array("status" => "success", "msg" => "{$CMS->lang['shipment_add_success']}"));exit;
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;
		
		if ( $shipment = $CMS->shipment->add() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=shipment&act=show&id={$shipment['shipment_id']}");
		}

		$CMS->output .= $CMS->shipment->html->add( $CMS->shipment->defaultvalue($CMS->input) );
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		 
		$shi_id =  $CMS->input['shi_id'];
		$data = $CMS->shipment->get_info($shi_id);
		if(! $data)
		{
			print json_encode(array("status" => "error", "msg" => "Không tìm thấy dữ liệu phù hợp!" ));exit;
		}

 
		$shi_name = urldecode($CMS->input['shi_name']);
		$shi_desc = urldecode($CMS->input['shi_description']);
		$shi_description = urldecode($CMS->class->editor->input($shi_desc,"text"));

		if(empty($shi_name) )
		{
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['shipment_empty_name']}"));exit;
		}	
		$shi_time_update = time();
		$DB->query("UPDATE ".root_table."shipment SET shi_name = '{$shi_name}', shi_description = '{$shi_description}' , shi_time_update = '{$shi_time_update}', user_id = '{$member['user_id']}'  WHERE shi_id = '{$shi_id}' ");
		 $_SESSION["msg"] .= $CMS->class->logs->insert("{$CMS->lang['shipment_edit_success']} <b>{$shi_name}</b>")."<br />"; 
		 print json_encode(array("status" => "success", "msg" => "{$CMS->lang['shipment_edit_success']}"));exit;


	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;
		
		if ( $shipment = $CMS->shipment->edit() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=shipment&parent_id={$shipment['parent_id']}");
		}

		$CMS->output .= $CMS->shipment->html->edit( $CMS->shipment->get_info() );
	}
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->shipment->shi_delete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=shipment");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->shipment->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=shipment");	
	}
	
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB, $member;
		$quick_search  = trim($CMS->input['quick_search']);
		if (  $quick_search != "" ) {
			if(Validate::isNum($quick_search))
			{
				$str.='&shi_id='.$quick_search;	
			}
			else
			{
					$str.='&shi_name='.$quick_search;
			}
		}

		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=shipment{$str}");
	}
	
	public function search_do()
	{
		global $CMS, $DB, $member;
		
		// Get Customer List
		$CMS->shipment->search();
		
		// Write Data
		$CMS->output .= $CMS->shipment->html();
	}
	
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function shipment_default()
	{
		global $CMS, $DB, $member;
		
		// Get Customer List
		$CMS->shipment->listing();
		
		// Write data
		$CMS->output .= $CMS->shipment->html();
	}
	
	//===========================================================================
	//  ARRANGE
	//===========================================================================
	
	public function arrange()
	{
		global $CMS, $DB, $member;
		
		// Re-arrange
		$CMS->shipment->arrange();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=shipment&shipment={$CMS->input['page']}");	
	}

 
	public function ajax_get_data_shipment()
	{
		global $CMS, $DB, $member;
		$data = $CMS->shipment->get_info();
		if($data)
		{
			print json_encode(array("status" => "success", "msg" => "", "data" => $data));exit;
		}
		else
		{
			print json_encode(array("status" => "error", "msg" => "Không tìm thấy dữ liệu phù hợp!" ));exit;
		}

	 
	}

	public function ajax_add_shipment()
	{
		global $CMS;
		
		if($CMS->permit['shipment_add'])
		{
			$return = $CMS->shipment->addAjax();
			if($return)
			{
				print json_encode(array("status" => "success", "msg" => "{$CMS->lang['title_add_shipment_success']}\"{$return['shi_name']}\"", "data" => $return));exit;
			}else
			{
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['title_add_shipment_error']}"));exit;
			}
		}else
		{
			print json_encode(array("status" => "error", "msg" => "{$CMS->lang['msg_no_permision']}"));exit;
		}
	}

	public function ajax_data_shipment()
	{
		global $CMS;

		$shi_id = intval($CMS->input['shi_id']);
		$data = $CMS->shipment->get_info($shi_id);
		print json_encode($data);exit;	
	}

	public function ajax_edit_shipment_do()
	{
		global $CMS;
		// print_r($CMS->permit);exit;
		if($CMS->permit['shipment_add']) 
		{
			$data = $CMS->shipment->editAjax();
			if($data)
			{
				print json_encode(array("status" => "success", "msg" => "{$CMS->lang['title_edit_shipment_success']}\"{$data['shi_name']}\"", "data" => $data));exit;
			}else
			{
				$data = $CMS->shipment->get_info($CMS->input['shi_id']);
				print json_encode(array("status" => "error", "msg" => "{$CMS->lang['title_edit_shipment_error']}\"{$data['shi_name']}\"", "data" => $data));exit;
			}
		}
	}

	function searchkey()
	{
		global $CMS;

		$type = intval($CMS->input['type']);
		$key_search = urldecode($CMS->input['term']);
		$data = $CMS->shipment->searchKey($key_search, $type); // search ở trang listing phiếu type = 0
		header('Content-Type: application/json');
		print json_encode($data);exit;

	}

}

?>
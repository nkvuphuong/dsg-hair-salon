<?php

$returns = new returns;

$returns->auto_run();

class returns {

	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
	 
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['returns_header']}";
		// Load the models
		$CMS->returns->auto_run_returns();
		$this->html = $CMS->class->template->load_template("skin_returns");
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
			case "approve":
				$this->approve();
			break;
			case "approve_do":
				$this->approve_do();
			break;
			case "delete":
				$this->delete();
			break;
			case "delete_all":
				$this->delete_all();
			break;
			case "search":
				if(\lib\input::get('subact') == "searchkey")
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
			case "cancel":
				$this->cancel();
			break;
			default:
				$this->returns_default();
			break;
		}
	}

	//===========================================================================
	//  SHOW INFO
	//===========================================================================

	public function show()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->returns->html->show( $CMS->returns->convertvalue($CMS->returns->get_info()) );
		$CMS->output.=$CMS->global->logs("returns_{$CMS->input['id']}");
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;
		
		if(isset($_SESSION['list_product']))
		{
			unset($_SESSION['list_product']);
		}
		$CMS->output .= $this->html->add();
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;
		// unset session
		
		if ( $returns = $CMS->returns->add() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=returns");
		}

		$CMS->output .= $CMS->returns->html->add( $CMS->returns->defaultvalue($CMS->input) );
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		 
		$ret_id =  $CMS->input['ret_id'];
		$data = $CMS->returns->get_info($ret_id);
		$CMS->output .= $this->html->edit($CMS->returns->convert_data($data));

	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;
		
		if ( $returns = $CMS->returns->edit() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=returns");
		}

		$CMS->output .= $CMS->returns->html->edit( $CMS->returns->convert_data($CMS->returns->get_info()) );
	}

	public function approve()
	{
		global $CMS, $DB, $member;
		 
		$ret_id =  $CMS->input['ret_id'];
		$data = $CMS->returns->get_info($ret_id);
		$CMS->output .= $this->html->approve($CMS->returns->convert_data($data));

	}

	public function approve_do()
	{
		global $CMS, $DB, $member;
		
		if ( $returns = $CMS->returns->edit() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=returns");
		}

		$CMS->output .= $CMS->returns->html->edit( $CMS->returns->convert_data($CMS->returns->get_info()));
	}
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->returns->delete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=returns");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->returns->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=returns");	
	}
	
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB, $member;
		
		$key = $CMS->input['quick_search'];
		if($key)
		{
			$CMS->returns->sql_add .= " AND (ret_code LIKE '%{$key}%' OR ret_assets LIKE '%{$key}%' )";
		}

		$CMS->core->page_title = "{$CMS->lang['returns_title']}";
		$CMS->output.=$CMS->returns->listing();
		$CMS->output.=$CMS->returns->html();
	}
	
	public function search_do()
	{
		global $CMS, $DB, $member;
		
		// Get Customer List
		$CMS->returns->search();
		
		// Write Data
		$CMS->output .= $CMS->returns->html();
	}
	
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function returns_default()
	{
		global $CMS, $DB, $member;
		
		// Get Customer List
		$CMS->returns->listing();
		
		// Write data
		$CMS->output .= $CMS->returns->html();
	}
	
	//===========================================================================
	//  ARRANGE
	//===========================================================================
	
	public function arrange()
	{
		global $CMS, $DB, $member;
		
		// Re-arrange
		$CMS->returns->arrange();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=returns&returns={$CMS->input['page']}");	
	}

 	public function cancel()
 	{
 		global $CMS, $member;

 		$ret_id = intval($CMS->input['id']);
 		$data = $CMS->returns->cancel_returns($ret_id);
 		if($data)
 		{
 			$_SESSION['msg'] = "[{$member['user_display_name']}] ". $CMS->lang['msg_cancel_returns']."<b>{$data['ret_code']}</b> ".$CMS->lang['msg_success'];
 		}else
 		{
 			$_SESSION['error_msg'] = "[{$member['user_display_name']}] ". $CMS->lang['msg_cancel_returns'].$CMS->lang['msg_error'];
 		}

 		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=returns");
 	}


 	function searchkey()
 	{
 		global $CMS;

		$key_search = urldecode($CMS->input['term']);
		$data = $CMS->returns->searchKey($key_search);
		header('Content-Type: application/json');
		print json_encode($data);exit;
 	}

}

?>
<?php

$email = new email;
$email->auto_run();

class email {

	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		
		// Title
		$CMS->class->language->load("email");
		$CMS->core->page_title = "-> {$CMS->lang['email_header']}";

		// Load the models
		$CMS->email->auto_run();

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
				$this->search();
			break;
			case "search_do":
				$this->search_do();
			break;
			case "preview":
				$this->preview();
			break;
			case "preview_do":
				$this->preview_do();
			break;
			default:
				$this->page_default();
			break;
		}
	}

	//===========================================================================
	//  SHOW INFO
	//===========================================================================

	public function show()
	{
		global $CMS, $DB, $member;

		$data = $CMS->email->get_info();
		$data = $CMS->email->convertvalue($data);
		$data = $CMS->email->convert_header($data);
		
		$CMS->output .= $CMS->email->html->show( $data );
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->email->html->add( $CMS->email->defaultvalue($CMS->input) );
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;
		
		if ( $email = $CMS->email->add() )
		{
			if($CMS->input['action_redirect'] == "add")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=email&act=add");
			}
			elseif($CMS->input['action_redirect'] == "detail")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=email&act=show&id={$email['email_id']}");
			}
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=email");
		}

		$CMS->output .= $CMS->email->html->add( $CMS->email->defaultvalue($CMS->input) );
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->email->html->edit( $CMS->email->editvalue($CMS->email->get_info()) );
	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;
		
		if ( $email = $CMS->email->edit() )
		{
			if($CMS->input['action_redirect'] == "edit")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=email&act=edit&id={$email['email_id']}");
			}
			elseif($CMS->input['action_redirect'] == "detail")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=email&act=show&id={$email['email_id']}");
			}
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=email");
		}

		$CMS->output .= $CMS->email->html->edit( $CMS->email->get_info() );
	}
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->email->delete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=email");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->email->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=email");	
	}
	
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB, $member;

		$CMS->output .= $CMS->email->html->search( $CMS->class->search->clean($CMS->input) );
	}
	
	public function search_do()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$CMS->email->search();
		
		// Write Data
		$CMS->output .= $CMS->email->html();
	}
	
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$CMS->email->listing();
		
		// Write data
		$CMS->output .= $CMS->email->html();
	}
	
	//===========================================================================
	//  PREVIEW
	//===========================================================================

	public function preview()
	{
		global $CMS, $DB, $member;
		
		print $CMS->email->preview();

		exit;
	}
	
	public function preview_do()
	{
		global $CMS, $DB, $member;
		
		print $CMS->email->preview_do();

		exit;
	}
}

?>
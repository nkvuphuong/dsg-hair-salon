<?php

$newstpl = new newstpl;
$newstpl->auto_run();

class newstpl {

	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['newstpl_header']}";

		// Load the models
		$CMS->newstpl->auto_run();

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
			case "arrange":
				$this->arrange();
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
		$data =$CMS->newstpl->get_info();
		$CMS->output .= $CMS->newstpl->html->show( $CMS->newstpl->convertvalue($data) );
		$CMS->output .= $CMS->global->logs("newstpl_{$data['newstpl_id']}");
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->newstpl->html->add( $CMS->newstpl->defaultvalue($CMS->input) );
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;
		
		if ( $newstpl = $CMS->newstpl->add() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=newstpl"); // &act=show&id={$newstpl['newstpl_id']}
		}

		$CMS->output .= $CMS->newstpl->html->add( $CMS->newstpl->defaultvalue($CMS->input) );
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->newstpl->html->edit( $CMS->newstpl->convertvalue($CMS->newstpl->get_info()) );
	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;
		
		if ( $newstpl = $CMS->newstpl->edit() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=newstpl");
		}

		$CMS->output .= $CMS->newstpl->html->edit( $CMS->newstpl->get_info() );
	}
	
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->newstpl->delete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=newstpl");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->newstpl->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=newstpl");	
	}
	
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB, $member;
		// Ajax

		if ( $CMS->input['is_ajax'] == 1 )
		{
			$this->ajax();
			return false;
		}
		
		$CMS->output .= $CMS->newstpl->html->search( $CMS->class->search->clean($CMS->input) );
	}
	
	public function search_do()
	{
		global $CMS, $DB, $member;
		
		
		// Get List
		$CMS->newstpl->search();
		
		// Write Data
		$CMS->output .= $CMS->newstpl->html();
	}
	
	//===========================================================================
	//  ARRANGE
	//===========================================================================
	
	public function arrange()
	{
		global $CMS, $DB, $member;
		
		// Re-arrange
		$CMS->newstpl->arrange();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=newstpl&pages={$CMS->input['page']}");	

	}
	
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$CMS->newstpl->listing();
		
		// Write data
		$CMS->output .= $CMS->newstpl->html();
	}
	
	
	//===========================================================================
	//  LIST
	//===========================================================================
	
	public function ajax()
	{
		global $CMS, $DB, $member;
		
		$CMS->newstpl->ajax($CMS->input['type']);
	}
}

?>
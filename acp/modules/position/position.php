<?php

$position = new position;
$position->auto_run();

class position {

	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['position_header']}";

		// Load the models
		$CMS->position->auto_run();

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
		$data = $CMS->position->get_info();
		$CMS->output .= $CMS->position->html->show( $CMS->position->convertvalue($data) );
		$CMS->output .= $CMS->global->logs("pos_{$data['pos_id']}");
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->position->html->add( $CMS->position->defaultvalue($CMS->input) );
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;
		
		if ( $position = $CMS->position->add() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=position");//&act=show&id={$position['pos_id']}
		}

		$CMS->output .= $CMS->position->html->add( $CMS->position->defaultvalue($CMS->input) );
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->position->html->edit( $CMS->position->editvalue($CMS->position->get_info()) );
	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;
		
		if ( $position = $CMS->position->edit() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=position");
		}

		$CMS->output .= $CMS->position->html->edit( $CMS->position->get_info() );
	}
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->position->delete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=position");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->position->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=position");	
	}
	
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB, $member;

		$CMS->output .= $CMS->position->html->search( $CMS->class->search->clean($CMS->input) );
	}
	
	public function search_do()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$CMS->position->search();
		
		// Write Data
		$CMS->output .= $CMS->position->html();
	}
	
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$CMS->position->listing();
		
		// Write data
		$CMS->output .= $CMS->position->html();
	}
}

?>
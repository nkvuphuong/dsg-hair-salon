<?php

$html = new html;
$html->auto_run();

class html {

	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['html_header']}";

		// Load the models
		$CMS->html->auto_run();

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
		
		$CMS->output .= $CMS->html->html->show( $CMS->html->convertvalue($CMS->html->get_info()) );
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->html->html->add( $CMS->html->defaultvalue($CMS->input) );
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;
		
		if ( $data = $CMS->html->add() )
		{
			if ( is_array($data) == true )
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=html&act=show&id={$data['html_id']}");
			}
			else
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=html");
			}
		}

		$CMS->output .= $CMS->html->html->add( $CMS->html->defaultvalue($CMS->input) );
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		$html = $CMS->html->get_info();
		
		$CMS->output .= $CMS->html->html->edit( $CMS->html->editvalue($html) );
	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;
		
		if ( $data = $CMS->html->edit() )
		{
			if ( is_array($data) == true )
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=html&act=show&id={$data['html_id']}");
			}
			else
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=html");
			}
		}

		$this->edit();
	}
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->html->delete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=html");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->html->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=html");	
	}
	
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB, $member;

		$CMS->output .= $CMS->html->html->search( $CMS->class->search->clean($CMS->input) );
	}
	
	public function search_do()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$CMS->html->search();
		
		// Write Data
		$CMS->output .= $CMS->html->html();
	}
	
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$CMS->html->listing();
		
		// Write data
		$CMS->output .= $CMS->html->html();
	}
}

?>
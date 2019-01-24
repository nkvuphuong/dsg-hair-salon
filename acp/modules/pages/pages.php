<?php

$pages = new pages;
$pages->auto_run();

class pages {

	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['pages_header']}";

		// Load the models
		$CMS->pages->auto_run();

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
				$this->pages_default();
			break;
		}
	}

	//===========================================================================
	//  SHOW INFO
	//===========================================================================

	public function show()
	{
		global $CMS, $DB, $member;
		if(\lib\input::get('subact') == "ajax")
		{	
			$data= $CMS->pages->get_info(intval($CMS->input['id']));
			$data['pages_name'] = json_decode($data['pages_name'], 1);
			$data['pages_content'] = json_decode($data['pages_content'], 1);
			$data['pages_shorturl'] = json_decode($data['pages_shorturl'], 1);
			// print "<pre>";print_r($data);exit;
			print json_encode($data, JSON_UNESCAPED_UNICODE);exit;

		}else
		{
			$CMS->output .= $CMS->pages->html->show( $CMS->pages->convertvalue($CMS->pages->get_info()) );
		}
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->pages->html->add( $CMS->pages->defaultvalue($CMS->input) );
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;
		
		if(\lib\input::get('subact') == "ajax")
		{
			$check = $CMS->pages->addajax();
			if($check)
			{
				print json_encode(array("status" => "success", "msg" => $CMS->lang['title_save_page_success'] , "data" => $check) , JSON_UNESCAPED_UNICODE);exit;
			}else
			{
				print json_encode(array("status" => "error", "msg" => $CMS->lang['title_save_page_error'] , "data" => []), JSON_UNESCAPED_UNICODE);exit;
			}
		}else
		{
			if ( $pages = $CMS->pages->add() )
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=pages&act=show&id={$pages['pages_id']}");
			}

			$CMS->output .= $CMS->pages->html->add( $CMS->pages->defaultvalue($CMS->input) );
		}

		
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->pages->html->edit( $CMS->pages->get_info() );
	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;
		
		if(\lib\input::get('subact') == "ajax")
		{
			$check = $CMS->pages->editajax();
			if($check)
			{
				print json_encode(array("status" => "success", "msg" => $CMS->lang['title_save_page_success'] , "data" => $check) , JSON_UNESCAPED_UNICODE);exit;
			}else
			{
				print json_encode(array("status" => "error", "msg" => $CMS->lang['title_save_page_error'] , "data" => []), JSON_UNESCAPED_UNICODE);exit;
			}
		}else
		{
			if ( $pages = $CMS->pages->edit() )
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=pages&parent_id={$pages['parent_id']}");
			}

			$CMS->output .= $CMS->pages->html->edit( $CMS->pages->get_info() );
		}
	}
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->pages->delete();
		if(\lib\input::get('subact') == "ajax")
		{
			print 1; exit;
		}
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=pages");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->pages->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=pages");	
	}
	
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB, $member;

		$CMS->output .= $CMS->pages->html->search( $CMS->class->search->clean($CMS->input) );
	}
	
	public function search_do()
	{
		global $CMS, $DB, $member;
		
		// Get Customer List
		$CMS->pages->search();
		
		// Write Data
		$CMS->output .= $CMS->pages->html();
	}
	
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function pages_default()
	{
		global $CMS, $DB, $member;
		
		// Get Customer List
		$CMS->pages->listing();
		
		// Write data
		$CMS->output .= $CMS->pages->html();
	}
	
	//===========================================================================
	//  ARRANGE
	//===========================================================================
	
	public function arrange()
	{
		global $CMS, $DB, $member;
		
		// Re-arrange
		$CMS->pages->arrange();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=pages&pages={$CMS->input['page']}");	
	}
}

?>
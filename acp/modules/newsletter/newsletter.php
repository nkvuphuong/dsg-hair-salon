<?php
use core\ezy;
use lib\input;
use models\dashboard;
//Load models
ezy::load_model("report");

$newsletter = new newsletter;
$newsletter->auto_run();

class newsletter {

	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['newsletter_header']}";
                
        $CMS->class->language->load("customer");

		// Load the models
		$CMS->newsletter->auto_run();
                
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
			case "send_mail":
				$this->send_mail();
			break;
			
			case "export":
				$this->export();
			break;
			case "import":
				$this->import();
			break;
			
			default:
                if(\lib\input::get('subact') == 'clear_cache')
                {
                    $CMS->class->cache->mdelete($CMS->newsletter->cache_prefix);
                    $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                }
				$this->page_default();
			break;
		}
        // Print tabs and content
		$CMS->output .= $this->output;
	}

	//===========================================================================
	//  SHOW INFO
	//===========================================================================

	public function show()
	{
		global $CMS, $DB, $member;

		$data = $CMS->newsletter->get_info();
		$data = $CMS->newsletter->convertvalue($data);
		$data = $CMS->newsletter->convert_header($data);
		
		$this->output .= $CMS->newsletter->html->show( $data );
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;
		
		$this->output .= $CMS->newsletter->html->add( $CMS->newsletter->defaultvalue($CMS->input) );
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;

		if ( $newsletter = $CMS->newsletter->add() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=newsletter");
		}

		$this->output .= $CMS->newsletter->html->add( $CMS->newsletter->defaultvalue($CMS->input) );
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		$this->output .= $CMS->newsletter->html->edit( $CMS->newsletter->editvalue($CMS->newsletter->get_info()) );
	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;
		
		if ( $newsletter = $CMS->newsletter->edit() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=newsletter");
		}

		$this->output .= $CMS->newsletter->html->edit( $CMS->newsletter->get_info() );
	}
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->newsletter->delete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=newsletter");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->newsletter->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=newsletter");	
	}
	
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB, $member;

		$this->output .= $CMS->newsletter->html->search( $CMS->class->search->clean($CMS->input) );
	}
	
	public function search_do()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$data = $CMS->newsletter->search();
		
		// Write Data
		$this->output .= $CMS->newsletter->html($data);
	}
	
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$data = $CMS->newsletter->listing();
		
		// Write data
		$this->output .= $CMS->newsletter->html($data);
	}
	
	//===========================================================================
	//  PREVIEW
	//===========================================================================

	public function send_mail()
	{
		global $CMS, $DB, $member;
		
		$check = $CMS->newsletter->create_send_mail();
		if($check)
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=newsletter");
		}else
		{
			$this->output .= $CMS->newsletter->html->add( $CMS->newsletter->defaultvalue($CMS->input) );
		}
	}
	

	function export()
	{
		global $CMS;
		$CMS->class->language->load("report");
		$link = \models\report::export_newsletter_list();
        ezy::load_model("download");
        \models\download::sendFile($link);
	}

	function import()
	{
		global $CMS;
		$CMS->newsletter->importNewsletterList();
		$CMS->global->redirect($CMS->vars['http_referer']);
		// return true;
	}
}

?>
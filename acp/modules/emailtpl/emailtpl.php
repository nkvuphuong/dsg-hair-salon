<?php
use core\ezy;
use lib\input;

$emailtpl = new emailtpl;
$emailtpl->auto_run();

class emailtpl {

	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['emailtpl_header']}";

		// Load the models
		$CMS->emailtpl->auto_run();

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
            case "import":
                $this->import();
                break;
            case "export":
                $this->export();
                break;
			default:
                if(\lib\input::get('subact') == 'clear_cache')
                {
                    $CMS->class->cache->mdelete($CMS->emailtpl->cache_prefix);
                    $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                }
				$this->page_default();
			break;
		}
	}

	//===========================================================================
	//  LIST
	//===========================================================================
	
	public function ajax()
	{
		global $CMS, $DB, $member;
		
		$CMS->emailtpl->ajax();
	}

	//===========================================================================
	//  SHOW INFO
	//===========================================================================

	public function show()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->emailtpl->html->show( $CMS->emailtpl->convertvalue($CMS->emailtpl->get_info()) );
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->emailtpl->html->add( $CMS->emailtpl->defaultvalue($CMS->input) );
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;
		
		if ( $emailtpl = $CMS->emailtpl->add() )
		{
			if($CMS->input['action_redirect'] == "add")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=emailtpl&act=add");
			}
			elseif($CMS->input['action_redirect'] == "detail")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=emailtpl&act=show&id={$emailtpl['emailtpl_id']}");
			}
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=emailtpl");
		}

		$CMS->output .= $CMS->emailtpl->html->add( $CMS->emailtpl->defaultvalue($CMS->input) );
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->emailtpl->html->edit( $CMS->emailtpl->editvalue($CMS->emailtpl->get_info()) );
	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;
		
		if ( $emailtpl = $CMS->emailtpl->edit() )
		{
			if($CMS->input['action_redirect'] == "edit")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=emailtpl&act=edit&id={$emailtpl['emailtpl_id']}");
			}
			elseif($CMS->input['action_redirect'] == "detail")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=emailtpl&act=show&id={$emailtpl['emailtpl_id']}");
			}
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=emailtpl");
		}

		$CMS->output .= $CMS->emailtpl->html->edit( $CMS->emailtpl->get_info() );
	}
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->emailtpl->delete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=emailtpl");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->emailtpl->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=emailtpl");	
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

		$CMS->output .= $CMS->emailtpl->html->search( $CMS->class->search->clean($CMS->input) );
	}
	
	public function search_do()
	{
		global $CMS, $DB, $member;
		
		// Write Data
		$CMS->output .= $CMS->emailtpl->html($CMS->emailtpl->search());
	}
	
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$data = $CMS->emailtpl->listing();

		// Write data
		$CMS->output .= $CMS->emailtpl->html($data);
	}

    /**
     * Import excel
     */
	public function import()
    {
        global $CMS;
        ezy::load_model("report");
        $CMS->emailtpl->importFromExcel();
        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=emailtpl");
    }

    /**
     * Export excel
     */
    public function export()
    {
        global $CMS;
        ezy::load_model("report");
        $link = $CMS->emailtpl->exportToExcel();
//        header("location: {$link}");

        ezy::load_model("download");
        \models\download::sendFile($link);
    }
}

?>
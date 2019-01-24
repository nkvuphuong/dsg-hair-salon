<?php
use core\ezy;
use lib\input;

$smstpl = new smstpl;
$smstpl->auto_run();

class smstpl {

	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['smstpl_header']}";

		// Load the models
		$CMS->smstpl->auto_run();

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
			/*case "delete":
				$this->delete();
			break;
			case "delete_all":
				$this->delete_all();
			break;*/
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
                    $CMS->class->cache->mdelete('sms_template');
                    $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                }

			    if(\lib\input::get('subact') == 'autocomplete')
                {
                    $this->autocomplete();
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
		
		$CMS->smstpl->ajax();
	}

	//===========================================================================
	//  SHOW INFO
	//===========================================================================

	public function show()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->smstpl->html->show( $CMS->smstpl->convertvalue($CMS->smstpl->get_info()) );
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->smstpl->html->add( $CMS->smstpl->defaultvalue($CMS->input) );
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;
		
		if ( $smstpl = $CMS->smstpl->add() )
		{
			if($CMS->input['action_redirect'] == "add")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=smstpl&act=add");
			}
			elseif($CMS->input['action_redirect'] == "detail")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=smstpl&act=show&id={$smstpl['smstpl_id']}");
			}
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=smstpl");
		}

        $CMS->input['smstpl_required_keys'] = array_values($CMS->input['smstpl_required_keys']);
        $CMS->input['smstpl_required_keys'] = array_unique($CMS->input['smstpl_required_keys']);
        $CMS->input['smstpl_required_keys'] = @json_encode($CMS->input['smstpl_required_keys'], JSON_UNESCAPED_UNICODE);

		$CMS->output .= $CMS->smstpl->html->add( $CMS->smstpl->defaultvalue($CMS->input) );
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;

		$CMS->output .= $CMS->smstpl->html->edit( $CMS->smstpl->editvalue($CMS->smstpl->get_info()) );
	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;

        $data = $CMS->smstpl->get_info();

        $CMS->input['smstpl_required_keys'] = @json_decode($data['smstpl_required_keys'],1);

        if ( $smstpl = $CMS->smstpl->edit() )
		{
			if($CMS->input['action_redirect'] == "edit")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=smstpl&act=edit&id={$smstpl['smstpl_id']}");
			}
			elseif($CMS->input['action_redirect'] == "detail")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=smstpl&act=show&id={$smstpl['smstpl_id']}");
			}
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=smstpl");
		}

        unset($CMS->input['smstpl_required_keys']);

        $data = array_merge($data, $CMS->input);

        /*if(is_array($data['smstpl_required_keys']))
        {
            $data['smstpl_required_keys'] = array_values($data['smstpl_required_keys']);
            $data['smstpl_required_keys'] = array_unique($data['smstpl_required_keys']);
            $data['smstpl_required_keys'] = @json_encode($data['smstpl_required_keys'], JSON_UNESCAPED_UNICODE);
        }*/

		$CMS->output .= $CMS->smstpl->html->edit($data);
	}
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->smstpl->delete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=smstpl");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->smstpl->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=smstpl");	
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

		$CMS->output .= $CMS->smstpl->html->search( $CMS->class->search->clean($CMS->input) );
	}
	
	public function search_do()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$data = $CMS->smstpl->search();
		
		// Write Data
		$CMS->output .= $CMS->smstpl->html($data);
	}
	
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;

		// Get List
		$data = $CMS->smstpl->listing();
		
		// Write data
		$CMS->output .= $CMS->smstpl->html($data);
	}

    function autocomplete()
    {
        global $CMS;

        $CMS->smstpl->autocomplete();
    }

    /**
     * Import excel
     */
    public function import()
    {
        global $CMS;
        ezy::load_model("report");
        $CMS->smstpl->importFromExcel();
        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=smstpl");
    }

    /**
     * Export excel
     */
    public function export()
    {
        global $CMS;
        ezy::load_model("report");
        $link = $CMS->smstpl->exportToExcel();
//        header("location: {$link}");

        ezy::load_model("download");
        \models\download::sendFile($link);
    }
}

?>
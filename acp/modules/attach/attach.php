<?php

$attach = new attach;
$attach->auto_run();

class attach {
	
	public $html = "";
	
	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;

		// Title
		$CMS->core->page_title = "-> {$CMS->lang['attach_header']}";

		// Load the models
		$CMS->attach->auto_run();

		// Switch
		switch( $CMS->input["act"] )
		{
			case "add_do":
				$this->add_do();
				
			break;
			case "delete":
				$this->delete();
			break;
			default:
				$this->page_default();
			break;
		}
		
		print $CMS->output;
		
		exit;
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add_do()
	{
		global $CMS, $DB, $member;

		// Add
		$CMS->attach->add();
		
		// Ouput
		$CMS->output .= $this->page_default();
	}
	

	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;

		unset($CMS->attach->is_ajax);

		if($CMS->input['ajax'])
        {
            $CMS->attach->is_ajax = 1;
        }

		// Delete
		$CMS->attach->delete();
		
		// Ouput
		$CMS->output .= $this->page_default();
	}
	
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default($type = 0)
	{
		global $CMS, $DB, $member;
		
		// Get Attach List
		$CMS->attach->listing();
		

		$CMS->output .= $CMS->attach->html();
	
	}
}

?>
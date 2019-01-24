<?php

$sms = new sms;
$sms->auto_run();

class sms {
	
	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['header']}";

		// Load the models
		$CMS->sms->auto_run();

                // Load lang gallery
                $CMS->class->language->load("sms");

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
			case "arrange":
				$this->arrange();
			break;
			case "cate_ajax":
				$this->cate_ajax();
			break;
			case "order_cat_home":
				$this->order_cat_home();
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
		$data = $CMS->sms->get_info();
		$CMS->output .= $CMS->sms->html->show( $CMS->sms->convertvalue($data) );
		$CMS->output .= $CMS->global->logs("sms_{$data['cat_id']}");
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;
                
                // Title
		$CMS->core->page_title = "-> {$CMS->lang['sms_add_header']}";
                
		$CMS->output .= $CMS->sms->html->add( $CMS->sms->defaultvalue($CMS->input) );
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;
		
		if ( $data = $CMS->sms->add() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sms");
		}

		$CMS->output .= $CMS->sms->html->add( $CMS->sms->defaultvalue($CMS->input) );
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
                
                // Title
		$CMS->core->page_title = "-> {$CMS->lang['sms_edit_header']}";
		
		$CMS->output .= $CMS->sms->html->edit( $CMS->sms->get_info() );
	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;
		
		if ( $data = $CMS->sms->edit() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sms");
		}

		$CMS->output .= $CMS->sms->html->edit( $CMS->sms->get_info() );
	}
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->sms->delete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sms");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->sms->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sms");	
	}
	
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$CMS->sms->listing();
		
		// Write data
		$CMS->output .= $CMS->sms->html();
	}
	
	//===========================================================================
	//  ARRANGE
	//===========================================================================
	
	public function arrange()
	{
		global $CMS, $DB, $member;
		// Re-arrange
		$CMS->sms->arrange();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=sms&pages={$CMS->input['page']}");	
	

	}
	
	
	//===========================================================================
	//  LOAD CATE AJAX
	//===========================================================================
	
	public function cate_ajax()
	{
		global $CMS, $DB, $member;
		
         $data =$CMS->config_parent_news->load_list_cate();
		 echo $data;exit;
	}
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function order_cat_home()
	{
		global $CMS, $DB, $member;
		
		$_SESSION['order_cat_home'] = 1;
		// Get List
		$CMS->sms->listing_home();
		
		// Write data
		$CMS->output .=<<<EOF
		
	<div class="codebox">
        <ul>
            <li>- Trang này sẽ chứa những Box tin tức theo Category chính sẽ được hiển thị ở phần giữa của trang chủ.</li>
            <li>- Tại đây, có thể sắp xếp vị trí hiển thị của các Box tin tức.</li>
        </ul>
	</div>
	
EOF;
		$CMS->output .= $CMS->sms->html_home();
	}
	

}

?>
<?php

$coupons = new coupons;
$coupons->auto_run();

class coupons {

	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['coupon_header']}";
		
		// Load the models
		$CMS->coupons->auto_run();
		$this->html = $CMS->coupons->html;
		// Switch
		switch( $CMS->input["act"] )
		{
			case "show":
				$this->show();
			break;
			case "add":
				$this->editor();
			break;
			case "add_do":
				$this->add_do();
			break;
			case "edit":
				$this->editor_edit();
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
			case "active":
				$this->active();
			break;
			case "load_tags":
				$this->load_tags();
			break;
			case "update_royalty":
				$this->update_royalty();
			break;
			case "editor":
				$this->editor();
			break;
			case "editor_edit":
				$this->editor_edit();
			break;
			case "views_logs":
				$this->views_logs();
			break;
			default:
				if(\lib\input::get('subact') == "delImg")
				{
					$this->delImg();
				}elseif(\lib\input::get('subact') == "getData")
				{
					$this->getData();
				}
                else if(\lib\input::get('subact') == 'clear_cache')
                {
                    $CMS->class->cache->mdelete($CMS->coupons->cache_prefix);
                    $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                }
				else
				{
					$this->page_default();
				}
			break;
		}
	}

	//===========================================================================
	//  SHOW INFO
	//===========================================================================

	public function show()
	{
		global $CMS, $DB, $member;
		$data = $CMS->coupons->get_info();

		$list_coupon = $CMS->coupons->load_coupon_anh($CMS->input['id'], $data['coupon_note']);
		$CMS->output .= $CMS->coupons->html->show( $CMS->coupons->convertvalue($data),$list_coupon );
		
		$CMS->output .= $CMS->global->logs("coupon_{$data['coupon_id']}");
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->coupons->html->add( $CMS->coupons->defaultvalue($CMS->input) );
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;
		    
		if ( $coupon = $CMS->coupons->add() )
		{

	 		print json_encode(array("status" => "success", "msg" => "{$CMS->lang['coupon_added']} {$coupon['coupon_name']}", "data_option" => ""));exit;
			//print 1;exit;
			// $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=coupon"); // &act=show&id={$coupon['coupon_id']}
		}
// $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=coupon");
		// $CMS->output .= $CMS->coupons->html->add( $CMS->coupons->defaultvalue($CMS->input) );
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->coupons->html->edit( $CMS->coupons->convertvalue($CMS->coupons->get_info()) );
	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;
		if ( $coupon = $CMS->coupons->edit() )
		{
			 print json_encode(array("status" => "success", "msg" => "{$CMS->lang['coupons_edited']} {$coupon['coupon_name']}", "data_option" => ""));exit;
			// $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=coupon");
		}

		// $CMS->output .= $CMS->coupons->html->edit( $CMS->coupons->get_info() );
	}
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->coupons->delete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=coupon");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->coupons->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=coupon");	
	}
	
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB, $member;

		$CMS->output .= $CMS->coupons->html->search( $CMS->class->search->clean($CMS->input) );
	}
	
	public function search_do()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$CMS->coupons->search();
		
		// Write Data
		$CMS->output .= $CMS->coupons->html();
	}
	//===========================================================================
	//  ACTIVE POST
	//===========================================================================
	
	public function active()
	{
		global $CMS, $DB, $member;
	
		// Get List
		$CMS->coupons->active();
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=coupon");
		
	}
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;
		
		// Write data
		$CMS->output .= $this->html->coupon_header();
	}
	
	
	//===========================================================================
	// UPDATE ROYALTY
	//===========================================================================
	
	public function update_royalty()
	{
		global $CMS, $DB, $member;
		// Get List
		$data =  $CMS->coupons->update_royalty();
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=coupon&act=show&id={$data['coupon_id']}");
	}
	
	//===========================================================================
	// VIEWS LOGS
	//===========================================================================
	
	public function views_logs()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->coupons->html->logs();
	}
	
	//===========================================================================
	// LOAD TAGS BY CATEGORY ID
	//===========================================================================
	
	public function load_tags()
	{
		global $CMS, $DB, $member;
		// Get List
		echo $CMS->tags->load_tags_dif(1);exit;
	}
	
	function delImg()
	{
		global $CMS;

		$gali_id = intval($CMS->input['gali_id']);
		$check = $CMS->coupons->delImg($gali_id);
		if(!$CMS->input['no_ajax'])
		{
			print $check;exit;
		}else
		{
			$CMS->global->redirect("{$_SERVER['HTTP_REFERER']}");
		}
	}

	function getData()
	{
		global $CMS;

		$coupon_id = intval($CMS->input['coupon_id']);
		$data = $CMS->coupons->get_info($coupon_id);

		if($data)
        {
            if(is_file("{$CMS->vars['upload_dir']}/coupon/{$data['coupon_image']}"))
            {
                $data['coupon_image'] = "{$CMS->vars['upload_url']}/coupon/{$data['coupon_image']}";
            }
            else
            {
                $data['coupon_image'] = '';
            }
        }


		print json_encode($data, JSON_UNESCAPED_UNICODE);exit;
	}


	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function editor()
	{
		global $CMS, $DB, $member;	
		// Write data
		$CMS->output .= $this->html->editor();
	}

	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function editor_edit()
	{
		global $CMS, $DB, $member;	
		$data = $CMS->coupons->get_info($CMS->input['id']);

		// Write data
		$CMS->output .= $this->html->editor_edit($data);
	}


}

?>
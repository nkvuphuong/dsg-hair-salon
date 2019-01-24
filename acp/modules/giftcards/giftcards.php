<?php

$giftcards = new giftcards;
$giftcards->auto_run();

class giftcards {

	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['giftcard_header']}";
		
		// Load the models
		$CMS->giftcards->auto_run();
		$this->html = $CMS->giftcards->html;
 
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
			case "active":
				$this->active();
			break;
			case "load_tags":
				$this->load_tags();
			break;
			case "update_royalty":
				$this->update_royalty();
			break;
			case "views_logs":
				$this->views_logs();
			break;
			case "editor":
				$this->editor();
			break;
			default:
				if(\lib\input::get('subact') == "delImg")
				{
					$this->delImg();
				}elseif(\lib\input::get('subact') == "getData")
				{
					$this->getData();
				}elseif(\lib\input::get('subact') == "config")
				{
					$this->config();
				}else
				{
                    if(\lib\input::get('subact') == 'clear_cache')
                    {
                        $CMS->class->cache->mdelete($CMS->giftcards->cache_prefix);
                        $CMS->class->cache->mdelete($CMS->product->cache_prefix);
                        $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                    }

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
		$data = $CMS->giftcards->get_info();

		$list_giftcard = $CMS->giftcards->load_giftcard_anh($CMS->input['id'], $data['giftcard_note']);
		$CMS->output .= $CMS->giftcards->html->show( $CMS->giftcards->convertvalue($data),$list_giftcard );
		
		$CMS->output .= $CMS->global->logs("giftcard_{$data['giftcard_id']}");
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->giftcards->html->editor( $CMS->giftcards->defaultvalue($CMS->input) );
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;
	 
		if ( $giftcard = $CMS->giftcards->add() )
		{
			print json_encode(array("status" => "success", "msg" => "{$CMS->lang['giftcard_added']}: <b>{$giftcard['product_name']}</b>", "data_option" => ""));exit;
			//$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=giftcards");
		}
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
	 
		$CMS->output .= $CMS->giftcards->html->editor_edit(  $CMS->product->get_info($CMS->input['id']) );
	}
	
	public function edit_do()
	{
		global $CMS;
		
		if(\lib\input::get('subact') == "config")
		{
			$giftcard = $CMS->giftcards->editConfig();
			$_SESSION['msg'] = $CMS->lang['update_config_successful'];
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=giftcards&subact=config");
		}else
		{
	        $giftcard = $CMS->giftcards->edit();
	        if(is_array($giftcard))
	        {
	        	print json_encode(array("status" => "success", "msg" => "{$CMS->lang['giftcards_edited']}: {$giftcard['product_name']}", "data_option" => ""));exit;
	        }
	        else
	        {
	        	print json_encode(array("status" => "error", "msg" => "{$CMS->lang['update_giftcard_faild']}", "data_option" => ""));exit;
	        }
	        

	        //$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=giftcards");
		}

    }
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->giftcards->delete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=giftcards");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->giftcards->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=giftcards");	
	}
	
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB, $member;

		$CMS->output .= $CMS->giftcards->html->search( $CMS->class->search->clean($CMS->input) );
	}
	
	public function search_do()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$CMS->giftcards->search();
		
		// Write Data
		$CMS->output .= $CMS->giftcards->html();
	}
	//===========================================================================
	//  ACTIVE POST
	//===========================================================================
	
	public function active()
	{
		global $CMS, $DB, $member;
	
		// Get List
		$CMS->giftcards->active();
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=giftcard");
		
	}
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;
		
		// Get List
		// $CMS->giftcards->listing();
		
		// Write data
		$CMS->output .= $this->html->giftcard_header();
	}
	
	
	//===========================================================================
	// UPDATE ROYALTY
	//===========================================================================
	
	public function update_royalty()
	{
		global $CMS, $DB, $member;
		// Get List
		$data =  $CMS->giftcards->update_royalty();
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=giftcard&act=show&id={$data['giftcard_id']}");
	}
	
	//===========================================================================
	// VIEWS LOGS
	//===========================================================================
	
	public function views_logs()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->giftcards->html->logs();
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
		$check = $CMS->giftcards->delImg($gali_id);
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

		$giftcard_id = intval($CMS->input['giftcard_id']);
		$data = $CMS->product->get_info($giftcard_id);

		print json_encode($data, JSON_UNESCAPED_UNICODE);exit;
	}

	function config()
	{
		global $CMS;
		
		$CMS->output .= $this->html->config();
	}

	function editor()
	{
		global $CMS;
		
		$CMS->output .= $this->html->editor();
	}

}

?>
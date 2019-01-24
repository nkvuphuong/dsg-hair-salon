<?php

$config_parent_news = new config_parent_news;
$config_parent_news->auto_run();

class config_parent_news {
	
	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
	
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['header']}";

		// Load the models
		$CMS->config_parent_news->auto_run();
		// print_r  ($CMS->input);exit;
		if($CMS->input["act"] != "order_cat_home")
		{
		// Load the models
			//$this->menu = array(
			//$CMS->permit['config_news_home_read'] ?	array("{$CMS->lang['config_news_home']}", "{$CMS->vars['root_domain']}/?site=config_parent_news&act=order_cat_home",0,1): "",
			
			//);
		}
		else
		{$this->menu = array(
			$CMS->permit['config_parent_news_read'] ?	array("{$CMS->lang['back_config']}", "{$CMS->vars['root_domain']}/?site=config_parent_news",0,1): "",
			
			);
		}

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
			case "order_cat_home":
				$this->order_cat_home();
			break;
			
			default:
                if(\lib\input::get('subact') == 'clear_cache')
                {
                    $CMS->class->cache->mdelete($CMS->config_parent_news->cache_prefix);
                    $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                }
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

		$data = $CMS->config_parent_news->get_info();
		$CMS->output .= $CMS->config_parent_news->html->show( $CMS->config_parent_news->convertvalue($data) );
		$CMS->output .= $CMS->global->logs("config_parent_news_{$data['cat_id']}");
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->config_parent_news->html->add( $CMS->config_parent_news->defaultvalue($CMS->input) );
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;
		
		if ( $data = $CMS->config_parent_news->add() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_parent_news");
		}

		$CMS->output .= $CMS->config_parent_news->html->add( $CMS->config_parent_news->defaultvalue($CMS->input) );
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->config_parent_news->html->edit( $CMS->config_parent_news->convertvalue($CMS->config_parent_news->get_info()) );
	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;
		
		if ( $data = $CMS->config_parent_news->edit() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_parent_news");
		}

		$CMS->output .= $CMS->config_parent_news->html->edit( $CMS->config_parent_news->get_info() );
	}
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->config_parent_news->delete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_parent_news");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->config_parent_news->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_parent_news");	
	}
	
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;

		// Write data
		$CMS->output .= $CMS->config_parent_news->merge_html();
	}
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function order_cat_home()
	{
		global $CMS, $DB, $member;
		
		$_SESSION['order_cat_home'] = 1;
		// Get List
		$CMS->config_parent_news->listing_home();
		
		// Write data
		$CMS->output .=<<<EOF
		
	<div class="codebox">
        <ul>
            <li>- Trang này sẽ chứa những Box tin tức theo Category chính sẽ được hiển thị ở phần giữa của trang chủ.</li>
            <li>- Tại đây, có thể sắp xếp vị trí hiển thị của các Box tin tức.</li>
        </ul>
	</div>
	
EOF;
		$CMS->output .= $CMS->config_parent_news->html_home();
	}
	
	
	
	
	//===========================================================================
	//  ARRANGE
	//===========================================================================
	
	public function arrange()
	{
		global $CMS, $DB, $member;
		
		// Re-arrange
		$CMS->config_parent_news->arrange();
		
		if(isset($_SESSION['order_cat_home']) AND $_SESSION['order_cat_home'] == 1)
		{
			unset($_SESSION['order_cat_home']);	
			// Redirect
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_parent_news&pages={$CMS->input['page']}");	
		}
		else
		{
			// Redirect
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_parent_news&pages={$CMS->input['page']}");	
		}
	}
	

}

?>
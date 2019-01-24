<?php
namespace models;

use \core\ezy;
$config_gallery = new config_gallery;
$config_gallery->auto_run();

class config_gallery {
	
	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['header']}";

		// Load the models
		$CMS->config_gallery->auto_run();

        // Load lang gallery
        $CMS->class->language->load("gallery");

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
                if(\lib\input::get('subact') == 'clear_cache')
                {
                    $CMS->class->cache->mdelete($CMS->config_gallery->cache_prefix);
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
		$data = $CMS->config_gallery->get_info();
		$CMS->output .= $CMS->config_gallery->html->show( $CMS->config_gallery->convertvalue($data) );
		$CMS->output .= $CMS->global->logs("config_gallery_{$data['cat_id']}");
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member, $tpl;
		if(ezy::$theme_key == "dsg")
		{
			 $tplformhair_custom = $CMS->config_gallery->html->list_hair_category();
 
		}

		$CMS->output .= $CMS->config_gallery->html->add( $CMS->config_gallery->defaultvalue($CMS->input) , $tplformhair_custom );
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;
		
		if ( $data = $CMS->config_gallery->add() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_gallery");
		}

		$CMS->output .= $CMS->config_gallery->html->add( $CMS->config_gallery->defaultvalue($CMS->input) );
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		$data_cate = $CMS->config_gallery->get_info();
		$data_cate = $CMS->config_gallery->convertvalue($data_cate);
		if(ezy::$theme_key == "dsg")
		{

			 $tplformhair_custom = $CMS->config_gallery->html->list_hair_category($data_cate['cat_cate']);
		}
		$CMS->output .= $CMS->config_gallery->html->edit( $data_cate, $tplformhair_custom );
	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;
		
		if ( $data = $CMS->config_gallery->edit() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_gallery");
		}
		$data_cate = $CMS->config_gallery->get_info();
		
		$CMS->output .= $CMS->config_gallery->html->edit( $data_cate, $tplformhair_custom );
	}
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->config_gallery->delete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_gallery");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->config_gallery->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_gallery");	
	}
	
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$data = $CMS->config_gallery->listing();
		
		// Write data
		$CMS->output .= $CMS->config_gallery->html($data);
	}
	
	//===========================================================================
	//  ARRANGE
	//===========================================================================
	
	public function arrange()
	{
		global $CMS, $DB, $member;
		// Re-arrange
		$CMS->config_gallery->arrange();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=config_gallery&pages={$CMS->input['page']}");	
	

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
		$CMS->config_gallery->listing_home();
		
		// Write data
		$CMS->output .=<<<EOF
		
	<div class="codebox">
        <ul>
            <li>- Trang này sẽ chứa những Box tin tức theo Category chính sẽ được hiển thị ở phần giữa của trang chủ.</li>
            <li>- Tại đây, có thể sắp xếp vị trí hiển thị của các Box tin tức.</li>
        </ul>
	</div>
	
EOF;
		$CMS->output .= $CMS->config_gallery->html_home();
	}
	

}

?>
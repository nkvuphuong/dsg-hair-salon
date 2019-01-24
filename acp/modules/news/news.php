<?php

$news = new news;
$news->auto_run();

class news {

	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
	
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['news_header']}";

		// Load the models
		$CMS->news->auto_run();
		// Load the models
		$this->html = $CMS->news->html;
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
		
			case "update_royalty":
				$this->update_royalty();
			break;
			case "views_logs":
				$this->views_logs();
			break;
			default:
				if($CMS->input['view'] == "convert_content")
				{
					$this->convert_content();
				}
				else
				{
                    if(\lib\input::get('subact') == 'clear_cache')
                    {
                        $CMS->class->cache->mdelete($CMS->news->cache_prefix);
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
		$data = $CMS->news->get_info();
		$CMS->output .= $CMS->news->html->show( $CMS->news->convertvalue($data) );
		$CMS->output .= $CMS->global->logs("news_{$data['news_id']}");
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->news->html->form( $CMS->news->defaultvalue($CMS->input) );
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;

		if ( $news = $CMS->news->add() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=news"); // &act=show&id={$news['news_id']}
		}

		if(is_array($CMS->input['news_tags']))
        {
            $CMS->input['news_tags']= array_values($CMS->input['news_tags']);
            $CMS->input['news_tags'] = @json_encode($CMS->input['news_tags'], JSON_UNESCAPED_UNICODE);
        }

		$CMS->output .= $CMS->news->html->form( $CMS->news->defaultvalue($CMS->input) );
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->news->html->form( $CMS->news->convertvalue($CMS->news->get_info() ));
	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;
		
		if ( $news = $CMS->news->edit() )
		{
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=news");
		}
		$data =  $CMS->news->convertvalue($CMS->news->get_info());

		$data_bk =  $CMS->news->get_info() ;
		$data_bk['news_tags_id_bk']= $data['news_tags_id_bk'];

		$dataForm = array_merge($data_bk, $CMS->input);

		if(is_array($dataForm['cat_id']))
        {
            $dataForm['cat_id'] = implode('|',$dataForm['cat_id']);
        }

        $dataForm['cat_id'] = "|{$dataForm['cat_id']}|";

        if(is_array($dataForm['news_tags']))
        {
            $dataForm['news_tags']= array_values($dataForm['news_tags']);
            $dataForm['news_tags'] = @json_encode($dataForm['news_tags'], JSON_UNESCAPED_UNICODE);
        }

		$CMS->output .= $CMS->news->html->form( $dataForm );
	}
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->news->delete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=news");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->news->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=news");	
	}
	
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB, $member;

		$CMS->output .= $CMS->news->html->search( $CMS->class->search->clean($CMS->input) );
	}
	
	public function search_do()
	{
		global $CMS, $DB, $member;

		// Write Data
		$CMS->output .= $CMS->news->html($CMS->news->search());
		// print $CMS->output;exit;
	}
	
	//===========================================================================
	//  ACTIVE POST
	//===========================================================================
	
	public function active()
	{
		global $CMS, $DB, $member;
	
		// Get List
		$CMS->news->active();
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=news");
		
	}
	
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;

		// Get List
		$data = $CMS->news->listing();

		// Write data
		$CMS->output .= $CMS->news->html($data);
	}
	
	
	//===========================================================================
	// UPDATE ROYALTY
	//===========================================================================
	
	public function update_royalty()
	{
		global $CMS, $DB, $member;
		// Get List
		$data =  $CMS->news->update_royalty();
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=news&act=show&id={$data['news_id']}");
	}
	
	//===========================================================================
	// VIEWS LOGS
	//===========================================================================
	
	public function views_logs()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->news->html->logs();
	}
	
	//===========================================================================
	// Convert Content
	//===========================================================================
	
	public function convert_content()
	{
		global $CMS, $DB, $member;
		$content = $_POST['news_content'];
		$content = $CMS->tags->convert_postvalue($content);
		$content = $CMS->class->editor->shortcode($content);
		echo $content;exit;
	}
	
	
}

?>
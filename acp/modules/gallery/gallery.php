<?php
namespace controller;

use \core\ezy;
use \lib\input;

// ezy::load_model("interface");
$gallery = new gallery;
$gallery->auto_run();

class gallery {

	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member, $tpl;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['gallery_header']}";

		// Load the models
		$CMS->gallery->auto_run();
		$this->html = $CMS->gallery->html;
		$tpl->url_back['list'] = "{$CMS->vars['root_domain']}/?site=gallery";
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
			default:
				if(\lib\input::get('subact') == "delImg")
				{
					$this->delImg();
				}elseif(\lib\input::get('subact') == "getData")
				{
					$this->getData();
				}elseif(\lib\input::get('subact') == "sort")
                {
                    $this->sort();
                }else
				{
                    if(\lib\input::get('subact') == 'clear_cache')
                    {
                        $CMS->class->cache->mdelete($CMS->gallery->cache_prefix);
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
		$data = $CMS->gallery->get_info();

		$list_gallery = $CMS->gallery->load_gallery_anh($CMS->input['id'], $data['gallery_note']);
		$CMS->output .= $CMS->gallery->html->show( $CMS->gallery->convertvalue($data),$list_gallery );
		
		$CMS->output .= $CMS->global->logs("gallery_{$data['gallery_id']}");
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $tpl;
		
		// $CMS->output .= $CMS->gallery->html->add( $CMS->gallery->defaultvalue($CMS->input) );
		if(ezy::$theme_key == "dsg")
		{
			 $tpl->formhair_custom = $CMS->gallery->html->list_hair_color();
 
		}

        $CMS->input['upload_type'] = isset($CMS->input['upload_type']) ? $CMS->input['upload_type'] : 'file';
        $tpl->upload_type_selected[$CMS->input['upload_type']] = 'checked';
        $tpl->data = $CMS->input;

        $tpl->data['gallery_sort_order'] *= 1;

        $CMS->output .= ezy::html();
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member, $tpl;
 
        $CMS->input['upload_type'] = isset($CMS->input['upload_type']) ? $CMS->input['upload_type'] : 'file';
        $tpl->upload_type_selected[$CMS->input['upload_type']] = 'checked';
        $tpl->data = $CMS->input;

        $tpl->data['gallery_sort_order'] *= 1;

		if ( $gallery = $CMS->gallery->add() )
		{
			// print 1;exit;
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=gallery"); // &act=show&id={$gallery['gallery_id']}
		}else
		{
			$CMS->output .= ezy::html();
		}
// $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=gallery");
		// $CMS->output .= $CMS->gallery->html->add( $CMS->gallery->defaultvalue($CMS->input) );
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		$data= $CMS->gallery->convertvalue($CMS->gallery->get_info());
		if(ezy::$theme_key == "dsg")
		{   
			 $tplformhair_custom = $CMS->gallery->html->list_hair_color($data['hair_color']);
 
		}

		$CMS->output .= $CMS->gallery->html->edit($data,$tplformhair_custom );
	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;

		if(\lib\input::get('subact') == 'change_category')
        {
            $this->change_category();
            exit;
        }

		if ( $gallery = $CMS->gallery->edit() )
		{
			print 1;exit;
			// $CMS->global->redirect("{$CMS->vars['root_domain']}/?site=gallery");
		}

		// $CMS->output .= $CMS->gallery->html->edit( $CMS->gallery->get_info() );
	}
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$check = $CMS->gallery->delete();
		if(\lib\input::get('subact') == "ajax")
		{
			if($check)
			{
				print 1;exit;
			}else
			{
				print 0; exit;
			}
		}else
		{
			// Redirect
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=gallery");	
		}
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->gallery->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=gallery");	
	}
	
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB, $member;

		$CMS->output .= $CMS->gallery->html->search( $CMS->class->search->clean($CMS->input) );
	}
	
	public function search_do()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$CMS->gallery->search();
		
		// Write Data
		$CMS->output .= $CMS->gallery->html();
	}
	//===========================================================================
	//  ACTIVE POST
	//===========================================================================
	
	public function active()
	{
		global $CMS, $DB, $member;
	
		// Get List
		$CMS->gallery->active();
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=gallery");
		
	}
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;
		 
		// Get List
		// $CMS->gallery->listing();
		
		// Write data
		$CMS->output .= $this->html->gallery_header();
	}
	
	
	//===========================================================================
	// UPDATE ROYALTY
	//===========================================================================
	
	public function update_royalty()
	{
		global $CMS, $DB, $member;
		// Get List
		$data =  $CMS->gallery->update_royalty();
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=gallery&act=show&id={$data['gallery_id']}");
	}
	
	//===========================================================================
	// VIEWS LOGS
	//===========================================================================
	
	public function views_logs()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->gallery->html->logs();
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
		$check = $CMS->gallery->delImg($gali_id);
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

		$gallery_id = intval($CMS->input['gallery_id']);
		$data = $CMS->gallery->get_info($gallery_id);
		$data = $CMS->gallery->convertvalue($data);
		
		if($data)
        {
            if(is_file("{$CMS->vars['upload_dir']}/gallery/{$data['gallery_image']}"))
            {
                $data['gallery_image'] = "{$CMS->vars['upload_url']}/gallery/{$data['gallery_image']}";
            }
            else
            {
                $data['gallery_image'] = '';
            }
        }

		print json_encode($data, JSON_UNESCAPED_UNICODE);exit;
	}

	function sort() {
	    global $CMS;

	    $result = $CMS->gallery->quickUpdateSortOrder($CMS->input['id'], $CMS->input['sort_value']);

	    input::jsonEncode($result);
    }

    /*
     * Change category for images
     */
    function change_category() {
        global $CMS;

        if($CMS->gallery->change_category($CMS->input['gallery_id'], $CMS->input['cat_id']))
        {
            $_SESSION['msg'] = $CMS->lang['change_category_success'];

            $result = [
                'status' => 'ok',
                'msg' => $CMS->lang['change_category_success'],
            ];
        }
        else
        {
            $result = [
                'status' => 'fail',
                'msg' => $CMS->lang['change_category_failed'],
            ];
        }

        input::jsonEncode($result);
    }
}

?>
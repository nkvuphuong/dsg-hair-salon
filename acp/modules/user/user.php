<?php
use \core\ezy;
ezy::load_model("user_group");
ezy::load_model("staff");
ezy::load_model("product");

$user = new user;
$user->auto_run();

class user {
	
	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['user_header']}";
		
		// Load language
		$CMS->class->language->load("comment");
		
		// Load the models
		$CMS->user->auto_run();
		
		// Create tabs
		$this->menu = array(
			array($CMS->lang['all'], "{$CMS->vars['root_domain']}/?site=user", (isset($CMS->input['act']) == 1 ? 0 : 1)),
			array($CMS->lang['user_status_1'], "{$CMS->vars['root_domain']}/?site=user&act=search_do&user_status=1", (isset($CMS->input['user_status']) && $CMS->input['user_status'] == 1 ? 1 : 0)),
			array($CMS->lang['user_status_0'], "{$CMS->vars['root_domain']}/?site=user&act=search_do&user_status=0", (isset($CMS->input['user_status']) && $CMS->input['user_status'] == 0 ? 1 : 0)),

			($CMS->permit['user_add'] == true ? array($CMS->lang['user_add'], "{$CMS->vars['root_domain']}/?site=user&act=add", ($CMS->input['act'] == "add" ? 1 : 0)) : ""),
			($CMS->permit['user_search'] == true ? array($CMS->lang['search_form'], "{$CMS->vars['root_domain']}/?site=user&act=search{$CMS->class->search->url_return}", ($CMS->input['act'] == "search" ? 1 : 0)) : ""),
		);

		// Switch
		switch( \lib\input::get('subact') )
		{
			case "comment":
				$this->add_comment();
			break;
			case "reply":
				$this->add_reply();
			break;
			case "search_user_ajax":
				$this->search_user_ajax();
			break;

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
			case "search":
				if(\lib\input::get('subact') == "search_user")
				{
					$this->search_user();
				}else
				{
					$this->search();
				}
			break;
			case "search_do":
				$this->search_do();
			break;
			case "search_do":
				$this->search_do();
			break;
			case "print":
				$this->printcard();
			break;
			case "print_do":
				$this->printcard_do();
			break;
			case "permission_ajax":
				$this->permission_ajax();
			break;
            case "config_commission":
                $this->config_commission();
                break;
			default:

                if(\lib\input::get('subact') == 'clear_cache')
                {
                    $CMS->class->cache->mdelete('user');
                    $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                }
                else if(\lib\input::get('subact') == 'set_busy')
                {
                    $this->set_busy();
                }

				$this->page_default();
			break;
		}

		$CMS->output = $CMS->output;
	}

	//===========================================================================
	//  SHOW INFO
	//===========================================================================

	public function show()
	{
		global $CMS, $DB, $member;
		
		$user = $CMS->user->get_info();

		$CMS->output .= $CMS->user->html->show( $CMS->user->convertvalue($user) );
		$CMS->output.=$CMS->global->logs("user_{$CMS->input['id']}");
		// Update message bar (count)
		// $CMS->user->update_msg_bar($user['user_id']);
	}

	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->user->html->add( $CMS->user->defaultvalue($CMS->input) );
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;

		if ( $user = $CMS->user->add() )
		{
			if($CMS->input['action_redirect'] == "add")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=user&act=add");
			}
			elseif($CMS->input['action_redirect'] == "detail")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=user&act=show&id={$user['user_id']}");
			}
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=user");
		}

		$CMS->output .= $CMS->user->html->add( $CMS->user->defaultvalue($CMS->input) );
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->user->html->edit( $CMS->user->editvalue($CMS->user->get_info()) );
	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;
		
		if ( $user = $CMS->user->edit() )
		{
			if($CMS->input['action_redirect'] == "edit")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=user&act=edit&id={$user['user_id']}");
			}
			elseif($CMS->input['action_redirect'] == "detail")
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=user&act=show&id={$user['user_id']}");
			}
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=user");
		}

		$CMS->output .= $CMS->user->html->edit( $CMS->user->editvalue($CMS->user->get_info()) );
	}
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->user->delete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=user");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->user->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=user");	
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
			$this->loadlist();
			return false;
		}

		$CMS->output .= $CMS->user->html->search( $CMS->class->search->clean($CMS->input) );
	}
	
	public function search_do()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$data = $CMS->user->search();
		
		// Write Data
		$CMS->output .= $CMS->user->html($data);
	}
	
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;

		// Get List
        $sql_add = '';

        if(! \lib\security::checkPermission('user', 'all_branches')) {
           if ($store_id = $member['store_id']) {
               $sql_add = 'store_id=' . $store_id . ' AND ';
           } else {
               $sql_add = 'store_id=0 AND ';
           }
        }

		$data = $CMS->user->listing($sql_add);
		
		// Write data
		$CMS->output .= $CMS->user->html($data);
	}
	
	//===========================================================================
	//  LIST
	//===========================================================================
	
	public function loadlist()
	{
		global $CMS, $DB, $member;
		
		$CMS->user->loadlist("ajax");
		
		exit;
	}
	
	//===========================================================================
	//  ADD Reply
	//===========================================================================
	
	public function add_reply()
	{
		global $CMS, $DB, $member;
		
		$CMS->input['id'] = intval($CMS->input['id']);
		$CMS->input['comment_name'] = $member['user_display_name'];
		$CMS->input['comment_to_user'] = $CMS->input['id'];
		
		$comment = $CMS->comment->add();
		
		// Update task
		$CMS->user->update_task_bar( $comment['comment_to_user'] );

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=user&act=show&id={$CMS->input['id']}");
	}
	
	//===========================================================================
	//  ADD Reply
	//===========================================================================
	
	public function add_comment()
	{
		global $CMS, $DB, $member;
		
		$CMS->input['id'] = intval($CMS->input['id']);
		$CMS->input['comment_name'] = $member['user_display_name'];
		$CMS->input['comment_to_user'] = $CMS->input['id'];
		
		$comment = $CMS->comment->add();
		
		// Update task
		$CMS->user->update_task_bar( $comment['comment_to_user'] );

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=user&act=show&id={$CMS->input['id']}");
	}
	
	//===========================================================================
	//  PRINT
	//===========================================================================
	
	public function printcard()
	{
		global $CMS, $DB, $member;

		$data = $CMS->user->get_info();

		// Card
		if ( $CMS->input['type'] == "front" )
		{
			$position = intval($CMS->input['position']);
			
			switch ( $position )
			{
				case "0":
					$cardname = "nhanvien";
				break;
				case "1":
					$cardname = "truongphong";
				break;
				case "2":
					$cardname = "ocm";
				break;
				default:
					$cardname = "nhanvien";
				break;
			}

			$image = $CMS->class->image->init("{$CMS->vars['img_url']}/card/{$cardname}.jpg");
			$image = $CMS->user->create_card_front($image, $data);
			$CMS->class->image->create($image);
		}
		else if ( $CMS->input['type'] == "back" )
		{
			$image = $CMS->class->image->init("{$CMS->vars['img_url']}/card/cardback.jpg");
			$image = $CMS->user->create_card_back($image, $data);
			$CMS->class->image->create($image);
		}

		$CMS->output .= $CMS->user->html->print_review($data);
	}
	
	public function printcard_do()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$data = $CMS->user->printcard();

		// Write Data
		$CMS->output .= $CMS->user->html->print_result($data);
	}	
	
	//===========================================================================
	//  UPDATE TABS
	//===========================================================================
	
	public function update_tabs( $data )
	{
		global $CMS, $DB, $member;

		// Update tab
		//$this->menu[$data['cus_type']][2] = 1;

		// Remove tabs
		//unset($this->menu[2]); // Add
		//unset($this->menu[3]); // Search

		$addarray = array();

		// Update more tabs
		if ( ! $CMS->input['act'] )
		{
			$addarray = array(
				array($CMS->lang['user_status_1'], "{$CMS->vars['root_domain']}/?site=user&act=search_do&user_status=1",0,1),
				array($CMS->lang['user_status_0'], "{$CMS->vars['root_domain']}/?site=user&act=search_do&user_status=0",0,1),
			);
		}
		
		// Update menu
		$this->menu = array_merge($this->menu, $addarray);
	}
	
	
	//===========================================================================
	//   PERMISSION AJAX
	//===========================================================================
	
	public function permission_ajax(  )
	{
		global $CMS, $DB, $member;

		$id = intval( $CMS->input["id"] );
			$DB->query("SELECT * FROM ".root_table."user_group WHERE userg_id='{$id}'");
			$data = $DB->fetch_array();
			 $out = $CMS->user->show_permission($data['userg_permission']);
			 echo $out;exit;
	}


	public function search_user_ajax() {
		global $CMS, $DB,$member;
		$key = isset($CMS->input['key']) ? $CMS->input['key'] : '';
 		$sql = $DB->query("SELECT * FROM ".root_table."user WHERE (user_name LIKE '%{$key}%' OR user_email LIKE '%{$key}%' ) AND user_deleted = 0 AND user_status = 1 ");
 		$data = array();
 		if($DB->num_rows($sql) > 0)
 		{
 			while ($dt =  $DB->fetch_array($sql)) {
 				# code...
 				$data[] = $dt;
 			}
 		}

		if(count($data) > 0)
		{
 
			print json_encode(array("status" => "success",  "data_option" => $data));exit;
		}else
		{
			print json_encode(array("status" => "error"));exit;
		}
 	
	}

	public function search_user()
	{
		global $CMS;
		$key_search = urldecode($CMS->input['term']);
		$data = $CMS->user->searchKey($key_search);
		header('Content-Type: application/json');
		print json_encode($data);exit;
		
	}

    public function set_busy()
    {
        global $CMS;

        if(\models\staff::setBusy($CMS->input))
        {
            $rs = [
                'status' => 'success',
                'payload' => $CMS->input,
                'msg' => 'Updating success',
            ];
        }
        else
        {
            $rs = [
                'status' => 'fail',
                'msg' => 'Updating failed',
            ];
        }

        \lib\input::jsonEncode($rs);
    }
}

?>
<?php

$group = new group;
$group->auto_run();

class group {
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['header']}";

		// Template
		$this->html = $CMS->class->template->load_template("skin_group");
		
		if ( $CMS->input["act"] == "add_do" )
		{
			$id = intval( $CMS->input["id"] );

			$userg_name = $CMS->input["userg_name"];
			$userg_email = $CMS->input["userg_email"];
			$userg_is_admin = $CMS->input["userg_is_admin"];
			$userg_is_root = $CMS->input["userg_is_root"];
			$userg_prefix_html = str_replace("'", "&#39;", $_POST["userg_prefix_html"]);
			$userg_suffix_html = str_replace("'", "&#39;", $_POST["userg_suffix_html"]);

			// Checking Group permission to anti escalate
			if ( $CMS->user->check_permission($userg_is_root, $userg_is_admin) == false )
			{
				return false;
			}
			
			$permission = $CMS->user->get_permission();
			
			$DB->query("INSERT INTO ".root_table."user_group (userg_email, userg_title, userg_prefix_html, userg_suffix_html, userg_is_admin, userg_is_root, userg_permission) VALUES ('{$userg_email}', '{$userg_name}', '{$userg_prefix_html}', '{$userg_suffix_html}', '{$userg_is_admin}', '{$userg_is_root}', '{$permission}');");
		
			$_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['group_added']} <b>{$userg_name}</b>");
			
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=group");
			
			exit;
		}
		
		if ( $CMS->input["act"] == "add" )
		{
			$CMS->output .= $this->html->add_group();
			
			return false;
		}
		
		if ( $CMS->input["act"] == "edit_do" )
		{
			$id = intval( $CMS->input["id"] );

			$userg_name = $CMS->input["userg_name"];
			$userg_email = $CMS->input["userg_email"];
			$userg_is_admin = $CMS->input["userg_is_admin"];
			$userg_is_root = $CMS->input["userg_is_root"];
			$userg_prefix_html = str_replace("'", "&#39;", $_POST["userg_prefix_html"]);
			$userg_suffix_html = str_replace("'", "&#39;", $_POST["userg_suffix_html"]);
			
			// Checking Group permission to anti escalate
			if ( $CMS->user->check_permission($userg_is_root, $userg_is_admin) == false )
			{
				return false;
			}
			
			$is_update = intval($CMS->input['is_update']);
			
			$permission = $CMS->user->get_permission();

			if ( $is_update )
			{
				$CMS->class->logs->insert("{$CMS->lang['updated_permission']} <b>{$userg_name}</b>");
			
				$DB->query("UPDATE ".root_table."user SET user_permission='{$permission}' WHERE userg_id='{$id}'");

                $CMS->class->cache->mdelete("user");
				
				// Delete cache user
				$sql = $DB->query("SELECT * FROM ".root_table."user WHERE userg_id='{$id}'");
				
				while ( $data = $DB->fetch_array( $sql ) )
				{
					$CMS->class->cache->delete("usertask_{$data['user_id']}");	
				}
			}

			$DB->query("UPDATE ".root_table."user_group SET userg_email='{$userg_email}', userg_title='{$userg_name}', userg_is_admin='{$userg_is_admin}', userg_is_root='{$userg_is_root}', userg_prefix_html='{$userg_prefix_html}', userg_suffix_html='{$userg_suffix_html}', userg_permission='{$permission}' WHERE userg_id={$id}");

			// Delete cache
			/*$sql = $DB->query("SELECT * FROM ".root_table."user WHERE userg_id='{$id}'");
			
			while ( $user = $DB->fetch_array($sql) )
			{
				$CMS->class->cache->delete("userprofile_{$user['user_id']}");	
			}*/
			
			// Remove cache
			$CMS->class->cache->mdelete("user");
			$CMS->class->cache->mdelete("user_group");
			
			$_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['group_edited']} <b>{$userg_name}</b>");

			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=group");
			
			exit;
		}
		
		if ( $CMS->input["act"] == "edit" )
		{
			$id = intval( $CMS->input["id"] );
			$DB->query("SELECT * FROM ".root_table."user_group WHERE userg_id='{$id}'");
			$data = $DB->fetch_array();
			
			// Checking Group permission to anti escalate
			if ( $CMS->user->check_permission($data['userg_is_root'], $data['userg_is_admin']) == false )
			{
				return false;
			}
			
			$CMS->output .= $this->html->edit_group( $data );
			
			return false;
		}
		
		if ( $CMS->input["act"] == "delete" )
		{
			$id = intval( $CMS->input["id"] );
			
			$DB->query("SELECT * FROM ".root_table."user_group WHERE userg_id='{$id}'");
			$data = $DB->fetch_array();
			
			// Checking Group permission to anti escalate
			if ( $CMS->user->check_permission($data['userg_is_root'], $data['userg_is_admin']) == false )
			{
				return false;
			}
			
			$DB->query("SELECT * FROM ".root_table."user WHERE userg_id='{$id}' AND user_deleted=0");
			
			if ( $DB->num_rows() == 0 )
			{
				$_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['group_deleted']} {$data['userg_title']}");
			
				//$DB->query("DELETE FROM ".root_table."user_group WHERE userg_id={$id}");
				$DB->query("UPDATE ".root_table."user_group SET userg_deleted=1 WHERE userg_id={$id}");
				
				// Remove cache
				$CMS->class->cache->mdelete("user");
				$CMS->class->cache->mdelete("user_group");
			}
			else
			{
				$CMS->lang['unable_delete'] = $CMS->class->language->replace(array("group" => "{$data['userg_title']}"), $CMS->lang['unable_delete']);

				$_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['unable_delete']}");
			}
			
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=group");
			
			exit;
		}
				
		$CMS->output .= $this->html->group();
	}
	
	
	
}

?>
<?php

$myaccount = new myaccount;
$myaccount->auto_run();

class myaccount {
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['header']}";

		// Load language
		$CMS->class->language->load("user");

		// Template
		$this->html = $CMS->class->template->load_template("skin_myaccount");
		
		switch ( $CMS->input["act"] )
		{
			case "status":
				$this->update_status();
			break;
			case "edit":
				$this->edit_do();
				$CMS->output .= $this->html->account( $CMS->user->editvalue($member) );
			break;
			default:
				$CMS->output .= $this->html->account( $CMS->user->editvalue($member) );
			break;
		}
	}
	
	//===========================================================================
	//  UPDATE ACCOUNT
	//===========================================================================
	
	public function edit_do()
	{
		global $CMS, $DB, $member;
		
		//$user_name = $CMS->input["user_name"];
		//$user_display_name = $CMS->input["user_display_name"];
		$user_password = $CMS->class->editor->input("user_password");
		$old_password = $CMS->class->editor->input("old_password");
		$user_repassword = $CMS->class->editor->input("user_repassword");
		$user_email = $CMS->input["user_email"];
		$user_signature = $CMS->input["user_signature"];
		
		// Extends
		$user_bdday = $CMS->input['user_bdday'];
		$user_bdmonth = $CMS->input['user_bdmonth'];
		$user_bdyear = $CMS->input['user_bdyear'];
		$user_sex = intval($CMS->input['user_sex']);
		$user_address = $CMS->input['user_address'];
		$user_phone = $CMS->input['user_phone'];
		$user_message = $CMS->class->editor->input("user_message", "textplain");

		if ( ! $old_password )
		{
            $_SESSION['msg'] .=$CMS->errormsg .= "{$CMS->lang['incomplete_oldpassword']}";
		
			return false;
		}
		
		if ( $CMS->user->log_password_checker( $member['user_id'], $old_password ) != true )
		{
            $_SESSION['msg'] .=$CMS->errormsg .= "{$CMS->lang['wrong_oldpassword']}";
			
			return false;
		}
		
		if ( $user_password )
		{
			if ( $user_password != $user_repassword )
			{
				$_SESSION['msg'] .= $CMS->errormsg .= "{$CMS->lang['wrong_repassword']}<br />";
				
				return false;
			}
			else
			{
				$CMS->user->create_new_password( $member['user_id'], $user_password );
			}
		}
		
		// Check upload
		$file_tmp = isset($_FILES['user_avatar']['tmp_name']) ? $_FILES['user_avatar']['tmp_name'] : "";
		$file_name = isset($_FILES['user_avatar']['name']) ? $_FILES['user_avatar']['name'] : "";
		$file_type = isset($_FILES['user_avatar']['type']) ? $_FILES['user_avatar']['type'] : "";
		$file_size = isset($_FILES['user_avatar']['size']) ? $_FILES['user_avatar']['size'] : "";
		$file_error = isset($_FILES['user_avatar']['error']) ? $_FILES['user_avatar']['error'] : "";
		
		$file_ext = $CMS->class->attachment->get_ext( $file_name );
		$file_name = str_replace( " ", "_", $file_name );
		$file_location = "avatar_".strtolower(time()."_".$file_name);

		if ( $file_name )
		{		
			if ( $CMS->class->attachment->is_image( $file_name, $file_ext ) == false ) { $CMS->errormsg .= "{$CMS->lang['invalid_upload_file']}"; return false; }

			// LHL-2018-07-22: Check and create if folder does not exists
            $CMS->class->image->is_dir("{$CMS->vars['upload_dir']}/avatar/");
            $CMS->class->image->is_dir("{$CMS->vars['upload_dir']}/avatar/thumbnail/");

			$result = copy($file_tmp, "{$CMS->vars['upload_dir']}/avatar/".$file_location) or die ("Could not be upload.");
			
			if ( $result )
			{
				$CMS->class->image->resize( "{$CMS->vars['upload_dir']}/avatar/".$file_location, "{$CMS->vars['upload_dir']}/avatar/thumbnail/".$file_location, 140 );
				
				@unlink("{$CMS->vars['upload_dir']}/avatar/{$member['user_avatar']}");
			}
			
			$user_avatar = $file_location;
		}
		else
		{
			$user_avatar = $member['user_avatar'];
		}
		
		/*// Check upload
		$file_tmp = isset($_FILES['user_avatarcard']['tmp_name']) ? $_FILES['user_avatarcard']['tmp_name'] : "";
		$file_name = isset($_FILES['user_avatarcard']['name']) ? $_FILES['user_avatarcard']['name'] : "";
		$file_type = isset($_FILES['user_avatarcard']['type']) ? $_FILES['user_avatarcard']['type'] : "";
		$file_size = isset($_FILES['user_avatarcard']['size']) ? $_FILES['user_avatarcard']['size'] : "";
		$file_error = isset($_FILES['user_avatarcard']['error']) ? $_FILES['user_avatarcard']['error'] : "";
		
		$file_ext = $CMS->class->attachment->get_ext( $file_name );
		$file_name = str_replace( " ", "_", $file_name );
		$file_location = "avatarc_".strtolower(time()."_".$file_name);
		
		if ( $file_name )
		{		
			if ( $CMS->class->attachment->is_image( $file_name, $file_ext ) == false ) { $CMS->errormsg .= "{$CMS->lang['invalid_upload_file']}"; return false; }

			$result = @copy($file_tmp, "{$CMS->vars['upload_dir']}/avatar/".$file_location) or die ("Could not be upload.");
			
			if ( $result )
			{
				$CMS->class->image->resize( "{$CMS->vars['upload_dir']}/avatar/".$file_location, "{$CMS->vars['upload_dir']}/avatar/thumbnail/".$file_location, 200, 260 );
				
				@unlink("{$CMS->vars['upload_dir']}/avatar/{$data['user_avatarcard']}");
			}
			
			$user_avatarcard = $file_location;
		}
		else
		{
			$user_avatarcard = $member['user_avatarcard'];
		}*/

		//user_display_name='{$user_display_name}', user_name='{$user_name}', 
		// user_phone='{$user_phone}', user_message='{$user_message}',
		// user_bdday='{$user_bdday}', user_bdmonth='{$user_bdmonth}', user_bdyear='{$user_bdyear}', user_sex='{$user_sex}', user_address='{$user_address}', , user_avatarcard='{$user_avatarcard}'user_signature='{$user_signature}',
        //
        //echo "UPDATE ".root_table."user SET user_email='{$user_email}', user_avatar='{$user_avatar}' WHERE user_id={$member['user_id']}";
        //exit;
		$DB->query("UPDATE ".root_table."user SET user_email='{$user_email}', user_avatar='{$user_avatar}' WHERE user_id={$member['user_id']}");
		
		// Delete cache
		$CMS->class->cache->mdelete("user");
		$CMS->class->cache->delete("usertask_{$member['user_id']}");
		
		$_SESSION['msg'] .= $CMS->class->logs->insert("{$CMS->lang['edited']} {$member['user_name']}");

		$CMS->global->redirect("{$CMS->vars['root_domain']}/?");
		
		exit;
	}
	
	//===========================================================================
	//  UPDATE STATUS
	//===========================================================================
	
	public function update_status()
	{
		global $CMS, $DB, $member;
		
		$user_message = strip_tags($CMS->class->editor->input("user_message"));
		$user_message = str_replace("\"", "&quot;", $user_message);

		$DB->query("UPDATE ".root_table."user SET user_message='{$user_message}' WHERE user_id={$member['user_id']}");

        $CMS->class->cache->mdelete("user");
		$CMS->class->cache->delete("usertask_{$member['user_id']}");
	
		print $user_message;
	
		exit;
	}
}

?>
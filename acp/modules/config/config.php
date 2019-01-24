<?php

$config = new config;
$config->auto_run();

class config {
	
	public function auto_run()
	{
		global $CMS, $DB;
	
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['header']}";

		// Template
		$this->html = $CMS->class->template->load_template("skin_config");

		$CMS->vars['nav'] .= " -> <a href='?site=config'><b>{$CMS->lang['header']}</b></a>";

		// Input
		$group = isset($CMS->input['group']) ? $CMS->input['group'] : 0;
		$CMS->input['code'] = isset($CMS->input['code']) ? $CMS->input['code'] : 0;
			
		$DB->query("SELECT * FROM ".root_table."conf_settings_titles WHERE conf_id='{$group}' OR conf_key='{$group}'");
		$conf_group = $DB->fetch_array();
		
		$group = $conf_group['conf_id'];
		
		if ( $group )
		{
			$CMS->vars['nav'] .= " -> <a href='?site=config&code=03&group={$conf_group['conf_id']}'><b>{$conf_group['conf_title']}</b></a>";
		}
		
		if ( $CMS->input['code'] == "01" )
		{
			if ( $CMS->input['group'] )
			{
				$group = intval( $CMS->input['group'] );
				
				$DB->query("SELECT * FROM ".root_table."conf_settings_titles WHERE conf_id='{$group}'");
				$data = $DB->fetch_array();
				
				$CMS->vars['nav'] .= " -> <a href='?site=config&code=01&group={$group}'><b>{$CMS->lang['setting_edit']}</b></a>";
				
				$CMS->output .= $this->html->skin_admin_top("{$CMS->lang['header']} -> {$CMS->lang['group_edit']}", "?site=config&code=02&group={$group}", '<a href="'.$CMS->vars['root_domain'].'/?site=config'.$CMS->class->search->url_return.'" class="cancel"><span class="font-icon font-icon-del"></span></a>', 'col-md-6');
				$CMS->output .= $this->html->skin_admin_add_group( $data , "{$CMS->lang['group_edit_submit']}");
				$CMS->output .= $this->html->skin_admin_bot("{$CMS->lang['group_edit_submit']}");
			}
			else
			{
				$CMS->vars['nav'] .= " -> <a href='?site=config&code=01'><b>{$CMS->lang['group_add']}</b></a>";
				
				$CMS->output .= $this->html->skin_admin_top("{$CMS->lang['header']} -> {$CMS->lang['group_add']}", "?site=config&code=02", '<a href="'.$CMS->vars['root_domain'].'/?site=config'.$CMS->class->search->url_return.'" class="cancel"><span class="font-icon font-icon-del"></span></a>', 'col-md-6');
				$CMS->output .= $this->html->skin_admin_add_group("", "{$CMS->lang['group_add_submit']}");
				$CMS->output .= $this->html->skin_admin_bot("{$CMS->lang['group_add_submit']}");
			}
			
			return false;
		}
		
		// print $CMS->input['code'];exit;
		if ( $CMS->input['code'] == "02" )
		{
			$title = $CMS->input['title'];
			$key = $CMS->input['key'];					
			$group = intval( $CMS->input['group'] );
			$protected = intval( $CMS->input['protected'] );
			
			if ( $group )
			{
				if ( $title AND $key )
				{
					$DB->query("SELECT * FROM ".root_table."conf_settings_titles WHERE conf_key='{$key}' AND conf_id!='{$group}' ORDER BY conf_title ASC LIMIT 1");
				
					if ( $DB->num_rows() > 0 )
					{
						$CMS->errormsg .= "<b>{$title}</b> ({$key}) {$CMS->lang['group_exist']}";
						
						return false;
					}
					
					$DB->query("UPDATE ".root_table."conf_settings_titles SET conf_title='{$title}', conf_key='{$key}', conf_protected='{$protected}' WHERE conf_id='{$group}'");

					$CMS->errormsg .= "{$CMS->lang['group_edited']} <b>{$title}</b> ({$key})";

					$CMS->class->logs->insert("{$CMS->lang['group_edited']} <b>{$title}</b> ({$key})");
					
					$CMS->class->cache->deletesql("config");
				}
				else
				{
					$CMS->errormsg .= "{$CMS->lang['group_incomplete']}";
					
					$CMS->class->logs->insert("{$CMS->lang['group_incomplete']}");
				}
			}
			else if ( $title AND $key )
			{
				$DB->query("SELECT * FROM ".root_table."conf_settings_titles WHERE conf_title='{$title}' OR conf_key='{$key}' ORDER BY conf_title ASC LIMIT 1");
				
				if ( $DB->num_rows() > 0 )
				{
					$CMS->errormsg .= "<b>{$title}</b> ({$key}) {$CMS->lang['group_exist']}";
					
					$CMS->class->logs->insert("<b>{$title}</b> ({$key}) {$CMS->lang['group_exist']}");
					
					return false;
				}
				
				$DB->query("INSERT INTO ".root_table."conf_settings_titles (conf_title, conf_key, conf_protected) VALUES ('{$title}', '{$key}', '{$protected}')");
	
				$CMS->errormsg .= "{$CMS->lang['group_added']} <b>{$title}</b> ({$key})";
				
				$CMS->class->logs->insert("{$CMS->lang['group_added']} <b>{$title}</b> ({$key})");
				
				$CMS->class->cache->deletesql("config");
			}
			else
			{
				@$CMS->global->redirect("?site=config");	
			}
		}
		
		
		if ( $CMS->input['code'] == "04" )
		{
			if ( $CMS->input["set"] )
			{
				$set = intval( $CMS->input["set"] );
				
				$CMS->vars['nav'] .= " -> <a href='?site=config&code=04&group={$group}&set={$set}'><b>{$CMS->lang['setting_edit']}</b></a>";
				
				$DB->query("SELECT * FROM ".root_table."conf_settings_titles WHERE conf_id='{$group}' ORDER BY conf_title ASC");
				$conf_group = $DB->fetch_array();
				
				$DB->query("SELECT * FROM ".root_table."conf_settings WHERE conf_id='{$set}'");
				$data = $DB->fetch_array();
				
				$CMS->output .= $this->html->skin_admin_top("{$conf_group['conf_title']} -> {$CMS->lang['setting_edit']}", "?site=config&code=05&group={$group}&set={$set}", '<a href="'.$CMS->vars['root_domain'].'/?site=config'.$CMS->class->search->url_return.'" class="cancel"><span class="font-icon font-icon-del"></span></a>', 'col-md-8');
				$CMS->output .= $this->html->skin_admin_add_setting( $data , "{$CMS->lang['setting_edit_submit']}");
				$CMS->output .= $this->html->skin_admin_bot("{$CMS->lang['setting_edit_submit']}");
				
				return false;
			}
			else
			{
				$CMS->vars['nav'] .= " -> <a href='?site=config&code=04&group={$group}'><b>{$CMS->lang['setting_add']}</b></a>";
				
				$DB->query("SELECT * FROM ".root_table."conf_settings_titles WHERE conf_id='{$group}' ORDER BY conf_title ASC");
				$conf_group = $DB->fetch_array();
							
				$CMS->output .= $this->html->skin_admin_top("{$conf_group['conf_title']} -> {$CMS->lang['setting_add']}", "?site=config&code=05&group={$group}", '<a href="'.$CMS->vars['root_domain'].'/?site=config'.$CMS->class->search->url_return.'" class="cancel"><span class="font-icon font-icon-del"></span></a>', 'col-md-8');
				$CMS->output .= $this->html->skin_admin_add_setting("", "{$CMS->lang['setting_add_submit']}");
				$CMS->output .= $this->html->skin_admin_bot("{$CMS->lang['setting_add_submit']}");
				
				return false;
			}
		}
			
			
		if ( $CMS->input['code'] == "05" )
		{
			$title = $CMS->input['title'];
			$value = $CMS->class->editor->replace($CMS->input['value']);
			$data = $CMS->class->editor->replace($CMS->input['data']);
			$key = $CMS->input['key'];
			$type = $CMS->input['type'];
			$protected = intval( $CMS->input['protected'] );

			if ( $CMS->input["set"] )
			{
				$set = intval( $CMS->input["set"] );
				$new_group = $CMS->input['new_group'];
				
				if ( $title AND $key AND $type AND $group )
				{
					$DB->query("SELECT * FROM ".root_table."conf_settings WHERE conf_key='{$key}' AND conf_id!='{$set}'");
				
					if ( $DB->num_rows() > 0 )
					{
						$CMS->errormsg .= "<b>{$title}</b> ({$key}) {$CMS->lang['setting_exist']}";
						
						$CMS->class->logs->insert("<b>{$title}</b> ({$key}) {$CMS->lang['setting_exist']}");
						
						return false;
					}
					
					$DB->query("UPDATE ".root_table."conf_settings SET conf_title='{$title}', conf_key='{$key}', conf_value='{$value}', conf_data='{$data}', conf_type='{$type}', conf_group='{$new_group}', conf_protected='{$protected}' WHERE conf_id='{$set}'");
					
					$CMS->errormsg .= "{$CMS->lang['setting_edited']} <b>{$title}</b> ({$key})";
					
					$CMS->class->logs->insert("{$CMS->lang['setting_edited']} <b>{$title}</b> ({$key})");
					
					$CMS->class->cache->deletesql("config");
				}
				else
				{
					$CMS->errormsg .= "{$CMS->lang['setting_incomplete']}";
					
					$CMS->class->logs->insert("{$CMS->lang['setting_incomplete']}");
				}
			}
			else if ( $title AND $key AND $type AND $group )
			{
				$DB->query("SELECT * FROM ".root_table."conf_settings WHERE conf_title='{$title}' OR conf_key='{$key}' ORDER BY conf_title ASC");
				
				if ( $DB->num_rows() > 0 )
				{
					$CMS->errormsg .= "<b>{$title}</b> ({$key}) {$CMS->lang['setting_exist']}";
					
					$CMS->class->logs->insert("<b>{$title}</b> ({$key}) {$CMS->lang['setting_exist']}");
					
					return false;
				}
				
				$DB->query("INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_value, conf_data, conf_group, conf_type, conf_protected) VALUES ('{$title}', '{$key}', '{$value}', '{$data}', '{$group}', '{$type}', '{$protected}')");
	
				$CMS->errormsg .= "{$CMS->lang['setting_added']} <b>{$title}</b> ({$key})";
				
				$CMS->class->logs->insert("{$CMS->lang['setting_added']} <b>{$title}</b> ({$key})");
				
				$CMS->class->cache->deletesql("config");
			}
			else
			{
				@$CMS->global->redirect("?site=config&code=03&group={$group}");	
			}
		}
		
		
		if ( $CMS->input['code'] == "03" )
		{	
			$set = $CMS->input["set"];

			if ( $set )
			{
				$DB->query("SELECT * FROM ".root_table."conf_settings WHERE conf_id='{$set}' ORDER BY conf_id ASC");
				$result = $DB->fetch_array();
				
				if ( $result['conf_protected'] == 1 )
				{
					$CMS->errormsg .= "<b>{$result['conf_title']}</b> ({$result['conf_key']}) {$CMS->lang['setting_unable_delete']}";
				}
				else
				{
					$DB->query("DELETE FROM ".root_table."conf_settings WHERE conf_id='{$set}'");
					
					$CMS->errormsg .= "{$CMS->lang['setting_deleted']} <b>{$result['conf_title']}</b> ({$result['conf_key']})";
					
					$CMS->class->logs->insert("{$CMS->lang['setting_deleted']} <b>{$result['conf_title']}</b> ({$result['conf_key']})");
					
					$CMS->class->cache->deletesql("config");
				}
			}
		}
		
		
		if ( $CMS->input['code'] == "03" OR $CMS->input['code'] == "05" )
		{
			$CMS->output .= $this->html->skin_admin_top("{$conf_group['conf_title']}", "?site=config&code=06&group={$group}", '<a href="'.$CMS->vars['root_domain'].'/?site=config&code=04&group='.$group.'" title="" class="add_bill">'.$CMS->lang['setting_add'].'</a><a href="'.$CMS->vars['root_domain'].'/?site=config'.$CMS->class->search->url_return.'" class="btn btn_add_line" style="background: #6B6B6B;border-color:#FFFFFF;padding: 7px 15px;"><span style="color:#FFFFFF;" class="font-icon font-icon-answer"></span></a>');
			
			$DB->query("SELECT * FROM ".root_table."conf_settings WHERE conf_group='{$group}' ORDER BY conf_order, conf_id ASC");

			$CMS->output .= $output = $CMS->class->editor->simple();

			$count = $DB->num_rows();
			$cnt = 1;
			while ( $result = $DB->fetch_array() )
			{
				$result['keyrow'] = $cnt;
				$result['rowtr'] = $cnt == $count ? 'last-row' : '';
				$cnt += 1;

				$result['conf_value'] = $result['conf_value'];
				$CMS->output .= $this->html->skin_admin_setting( $result, $count );
			}
			 
			$CMS->output .= $this->html->skin_admin_bot("{$CMS->lang['setting_edit_submit']}");
		 
			return false;
		}
		
		
		if ( $CMS->input['code'] == "06" )
		{
			$sql_query = $DB->query("SELECT * FROM ".root_table."conf_settings WHERE conf_group='{$group}' ORDER BY conf_id ASC");
			
			$_SESSION['msg'] = $CMS->class->logs->insert("{$CMS->lang['group_setting_edited']} {$conf_group['conf_title']}");
			
			while ( $result = $DB->fetch_array ( $sql_query ) )
			{
				if ( $result['conf_value'] != $CMS->class->editor->input($result['conf_key']) )
				{
					// Detect Select Setting
					if ( $result['conf_type'] == "select" )
					{
						$data_select = "";	
						
						$array = explode("<br />", $result['conf_data']);
						
						for ( $i = 0; $i < count($array); $i++ )
						{
							$option = explode("|", $array[$i]);
							
							$option[2] = $CMS->input["{$result['conf_key']}"] == $option[1] ? 1 : 0;
							
							if ( $option[2] == 1 )
							{
								$CMS->input["{$result['conf_key']}"] = $option[1];
							}
							
							$data_select .= "{$option[0]}|{$option[1]}|{$option[2]}";
							
							if ( $i < count($array)-1 )
							{
								$data_select .= "<br />";
							}
						}

						$conf_data = $data_select;
					}

					$CMS->class->logs->insert("{$CMS->lang['setting_value_edited']} <b>{$result['conf_title']}</b> ({$result['conf_key']})");
					
					$DB->query("UPDATE ".root_table."conf_settings SET conf_value='". $CMS->class->editor->input($result['conf_key']) ."', conf_data='". $CMS->class->editor->replace($conf_data) ."', conf_order='".intval( $CMS->input["order_{$result['conf_id']}"] )."' WHERE conf_key='{$result['conf_key']}'");
				}
				else if ( $result['conf_order'] != intval( $CMS->input["order_{$result['conf_id']}"] ) )
				{
					//$CMS->class->logs->insert("{$CMS->lang['setting_order_edited']} <b>{$result['conf_title']}</b> ({$result['conf_key']})");
					
					$DB->query("UPDATE ".root_table."conf_settings SET conf_order='".intval( $CMS->input["order_{$result['conf_id']}"] )."' WHERE conf_key='{$result['conf_key']}'");
				}
			}

			$CMS->class->cache->deletesql("config");
			
			$CMS->global->redirect("?site=config&code=03&group={$group}");
			exit;			
		}
		
		if ( ! $CMS->input['code'] )
		{
			$group = isset($CMS->input["group"]) ? $CMS->input["group"] : 0;
			
			if ( $group )
			{
				$DB->query("SELECT * FROM ".root_table."conf_settings_titles WHERE conf_id='{$group}' ORDER BY conf_id ASC");
				$result = $DB->fetch_array();
				
				if ( $DB->num_rows() == 0 )
				{
				
				}
				else if ( $result['conf_protected'] == 1 )
				{
					$CMS->errormsg .= "<b>{$result['conf_title']}</b> ({$result['conf_key']}) {$CMS->lang['group_unable_delete']}";
				}
				else
				{
					$DB->query("DELETE FROM ".root_table."conf_settings_titles WHERE conf_id='{$group}'");
					$DB->query("DELETE FROM ".root_table."conf_settings WHERE conf_group='{$group}'");
					
					$CMS->errormsg .= "{$CMS->lang['group_deleted']} <b>{$result['conf_title']}</b> ({$result['conf_key']})";
					
					$CMS->class->logs->insert("{$CMS->lang['group_deleted']} <b>{$result['conf_title']}</b> ({$result['conf_key']})");
					
					$CMS->class->cache->deletesql("config");
				}
			}
		}
		
		if ( $CMS->input['act'] != "export" )
		{
			$DB->query("SELECT * FROM ".root_table."conf_settings_titles ORDER BY conf_title ASC");

			$CMS->output .= $this->html->skin_admin_top("{$CMS->lang['header']}", "?site=config&code=01", '<a href="'.$CMS->vars['root_domain'].'/?site=config&code=01" title="" class="add_bill">'.$CMS->lang['group_add'].'</a>');
			
			$count = $DB->num_rows();
			$cnt = 1;
			while ( $result = $DB->fetch_array() )
			{
				$result['keyrow'] = $cnt;
				$result['rowtr'] = $cnt == $count ? 'last-row' : '';
				$cnt += 1;
				$CMS->output .= $this->html->skin_admin_group( $result );
			}
			
			$CMS->output .= $this->html->skin_admin_bot("{$CMS->lang['group_add']}");	
		}
		else
		{	
			$CMS->output .= "<textarea style='margin: 10px;' cols=10 rows=15>";
			
			$DB->query("SELECT * FROM ".root_table."conf_settings_titles ORDER BY conf_title ASC");
			
			$CMS->output .= "TRUNCATE TABLE ".root_table."conf_settings_titles;\n";
			
			while ( $data = $DB->fetch_array() )
			{
				$CMS->output .= "INSERT INTO ".root_table."conf_settings_titles (conf_id, conf_title, conf_key) VALUES ('{$data['conf_id']}', '{$data['conf_title']}', '{$data['conf_key']}' );\n";
			}
			
			$CMS->output .= "\n";
			
			$DB->query("SELECT * FROM ".root_table."conf_settings ORDER BY conf_title ASC");
			
			$CMS->output .= "TRUNCATE TABLE ".root_table."conf_settings;\n";
			
			while ( $data2 = $DB->fetch_array() )
			{
				$data2['conf_value'] = str_replace( "\"", "&quot;", $data2['conf_value'] );
				$data2['conf_value'] = str_replace( "'", "&#39;", $data2['conf_value'] );
				
				$CMS->output .= "INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_value, conf_group, conf_type) VALUES ('{$data2['conf_title']}', '{$data2['conf_key']}', '{$data2['conf_value']}', '{$data2['conf_group']}', '{$data2['conf_type']}' );\n";
			}
			
			$CMS->output .= "\n";
			
			$DB->query("SELECT * FROM ".root_table."mimetypes ORDER BY mime_id ASC");
			
			$CMS->output .= "TRUNCATE TABLE ".root_table."mimetypes;\n";
			
			while ( $data2 = $DB->fetch_array() )
			{
				$CMS->output .= "INSERT INTO ".root_table."mimetypes (mime_extension , mime_type, mime_img) VALUES ('{$data2['mime_extension']}', '{$data2['mime_type']}', '{$data2['mime_img']}');\n";
			}
			
			$CMS->output .= "\n";
			
			$DB->query("SELECT * FROM ".root_table."emoticons ORDER BY emo_id ASC");
			
			$CMS->output .= "TRUNCATE TABLE ".root_table."emoticons;\n";
			
			while ( $data2 = $DB->fetch_array() )
			{
				$data2['conf_value'] = str_replace( "\"", "&quot;", $data2['conf_value'] );
				$data2['conf_value'] = str_replace( "'", "&#39;", $data2['conf_value'] );
				
				$CMS->output .= "INSERT INTO ".root_table."emoticons (emo_typed, emo_image, emo_clickable, emo_set) VALUES ('{$data2['emo_typed']}', '{$data2['emo_image']}', '{$data2['emo_clickable']}', '{$data2['emo_emo_set']}');\n";
			}
			
			$CMS->output .= "</textarea>";
			
			
		}
	}	
}

?>
<?php

$optimize = new optimize;
$optimize->auto_run();

class optimize {

	public function auto_run()
	{
		global $CMS, $DB;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['header']}";

		// Template
		$this->html = $CMS->class->template->load_template("skin_optimize");
		
		if ( $CMS->input["act"] == "exe" )
		{
			$module = $CMS->input["module"];

			if ( $module == "cache" )
			{
				$CMS->class->cache->clear();
				$CMS->class->cache->tplclear();
				
				$CMS->class->logs->insert("{$CMS->lang['cache_cleaned']}");
				exit("1");
			}
			else if ( $module == "cachesql" )
			{
				$CMS->class->cache->clearsql();
				
				$CMS->class->logs->insert("{$CMS->lang['cachesql_cleaned']}");
				
				exit("1");
			}
			else if ( $module == "firewall" )
			{
				if ( @file_get_contents("{$CMS->vars['root_domain']}/firewall/empty.php") )
				{
					$CMS->class->logs->insert("{$CMS->lang['firewall_cleaned']}");
					exit("1");
				}
				else
				{
					$CMS->class->logs->insert("{$CMS->lang['firewall_failed']}");
					exit("0");
				}
			}
			else if ( $module == "htaccess" )
			{
				$file = root_path.".htaccess";
			
				$data = @file_get_contents($file);
				
				if ( ! $data )
				{
					$CMS->class->logs->insert("{$CMS->lang['htaccess_failed']}");
					exit("0");
				}
				
				$data_array = explode("\n", $data);
				
				$new_data = "";
				
				for ( $i = 0; $i < count($data_array); $i++ )
				{
					if ( substr(strtolower($data_array[$i]), 0, 4) != "deny" )
					{
						$new_data .= $data_array[$i]."\n";
					}
				}

				$new_data = ltrim(trim($new_data))."\n\n";

				if ( $fh = @fopen($file, "w") )
				{
					@fwrite($fh, $new_data);
					@fclose($fh);
					
					$CMS->class->logs->insert("{$CMS->lang['htaccess_cleaned']}");
					exit("1");
				}
				else
				{
					$CMS->class->logs->insert("{$CMS->lang['htaccess_failed']}");
					exit("0");
				}
			}
			else if ( $module == "email" )
			{			
				if ( $CMS->vars['smtp_server'] && $CMS->vars['smtp_port'] && $CMS->vars['smtp_user'] && $CMS->vars['smtp_password'] )
				{
					// Config
					$localhost = $smtpServer = ($CMS->vars['is_smtp'] == 2 ? "ssl://" : "") . $CMS->vars['smtp_server'];
					$port = (int)$CMS->vars['smtp_port'];
					$timeout = 10;
					$username = $CMS->vars['smtp_user'];
					$password = $CMS->vars['smtp_password'];
					$localhost = $_SERVER['REMOTE_ADDR'];
					$newLine = "\r\n";
					
					// Start check
					$smtpConnect = fsockopen($smtpServer, $port, $errno, $errstr, $timeout);

					if ( ! $smtpConnect ) { exit("0"); }
					
					$smtpResponse = fgets($smtpConnect, 4096);

					if ( ! $smtpResponse ) { return false; }
					
					if(empty($smtpConnect))
					{
						$output = "Failed to connect: $smtpResponse";
						
						$CMS->class->logs->insert("{$CMS->lang['email_failed']}");
						exit("0");
					}
					else
					{
					   $logArray['connection'] = "Connected to: $smtpResponse";
					}

					fputs($smtpConnect, "HELO $localhost". $newLine);
					$smtpResponse = fgets($smtpConnect, 4096);
					$logArray['heloresponse2'] = "$smtpResponse";

					fputs($smtpConnect,"AUTH LOGIN" . $newLine);
					$smtpResponse = fgets($smtpConnect, 4096);
					$logArray['authrequest'] = "$smtpResponse";
					
					fputs($smtpConnect, base64_encode($username) . $newLine);
					$smtpResponse = fgets($smtpConnect, 4096);
					$logArray['authusername'] = "$smtpResponse";
					
					fputs($smtpConnect, base64_encode($password) . $newLine);
					$smtpResponse = fgets($smtpConnect, 4096);
					$logArray['authpassword'] = "$smtpResponse";
				
					if ( preg_match("/(successful)/", $logArray[authpassword] ) == true )
					{
						$CMS->class->logs->insert("{$CMS->lang['email_cleaned']}");
						exit("1");
					}
					else
					{
						$CMS->class->logs->insert("{$CMS->lang['email_failed']}");
						exit("0");
					}
				}
				else
				{
					$CMS->class->logs->insert("{$CMS->lang['email_failed']}");
					exit("0");
				}
			}
			else if ( $module == "backup" )
			{
				// Export data
				$data = "";
			
				$DB->query("SELECT * FROM ".root_table."conf_settings_titles ORDER BY conf_title ASC");
				
				$data .= "TRUNCATE TABLE ".root_table."conf_settings_titles;\n";
				
				while ( $data = $DB->fetch_array() )
				{
					$data .= "INSERT INTO ".root_table."conf_settings_titles (conf_id, conf_title, conf_key, conf_protected) VALUES ('{$data['conf_id']}', '{$data['conf_title']}', '{$data['conf_key']}', '{$data['conf_protected']}' );\n";
				}
				
				$data .= "\n";
				
				$DB->query("SELECT * FROM ".root_table."conf_settings ORDER BY conf_title ASC");
				
				$data .= "TRUNCATE TABLE ".root_table."conf_settings;\n";
				
				while ( $data2 = $DB->fetch_array() )
				{
					$data2['conf_value'] = str_replace( "\"", "&quot;", $data2['conf_value'] );
					$data2['conf_value'] = str_replace( "'", "&#39;", $data2['conf_value'] );
					
					$data .= "INSERT INTO ".root_table."conf_settings (conf_title, conf_key, conf_data, conf_value, conf_group, conf_type, conf_order, conf_protected) VALUES ('{$data2['conf_title']}', '{$data2['conf_key']}', '{$data2['conf_data']}', '{$data2['conf_value']}', '{$data2['conf_group']}', '{$data2['conf_type']}', '{$data2['conf_order']}', '{$data2['conf_protected']}' );\n";
				}
				
				$data .= "\n";
				
				$DB->query("SELECT * FROM ".root_table."mimetypes ORDER BY mime_id ASC");
				
				$data .= "TRUNCATE TABLE ".root_table."mimetypes;\n";
				
				while ( $data2 = $DB->fetch_array() )
				{
					$data .= "INSERT INTO ".root_table."mimetypes (mime_extension , mime_type, mime_img) VALUES ('{$data2['mime_extension']}', '{$data2['mime_type']}', '{$data2['mime_img']}');\n";
				}
				
				$data .= "\n";
				
				$DB->query("SELECT * FROM ".root_table."emoticons ORDER BY emo_id ASC");
				
				$data .= "TRUNCATE TABLE ".root_table."emoticons;\n";
				
				while ( $data2 = $DB->fetch_array() )
				{
					$data2['conf_value'] = str_replace( "\"", "&quot;", $data2['conf_value'] );
					$data2['conf_value'] = str_replace( "'", "&#39;", $data2['conf_value'] );
					
					$data .= "INSERT INTO ".root_table."emoticons (emo_typed, emo_image, emo_clickable, emo_set) VALUES ('{$data2['emo_typed']}', '{$data2['emo_image']}', '{$data2['emo_clickable']}', '{$data2['emo_emo_set']}');\n";
				}
				
				// Config
				$day_of_week = $CMS->class->date->dayofweek;
				
				// Save file
				$file = root_path."cache/db/db_{$day_of_week}.sql";
				
				if ( $fh = @fopen($file, "w") )
				{
					@fwrite($fh, $data);
					@fclose($fh);
					
					$CMS->class->logs->insert("{$CMS->lang['backup_cleaned']}");
					exit("1");
				}
				else
				{
					$CMS->class->logs->insert("{$CMS->lang['backup_failed']}");
					exit("0");
				}
			}
		}

		
		$CMS->output .= $this->html->skin_optimize();
	}
    
}

?>
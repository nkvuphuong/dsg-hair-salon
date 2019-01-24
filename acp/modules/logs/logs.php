<?php

$logs = new logs;
$logs->auto_run();

class logs {
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['header']}";
		
		// Template
		$this->html = $CMS->class->template->load_template("skin_logs");
		 
		switch($CMS->input['act'])
		{
			case "detail":
				$this->detail();
			break;
			default:
				if($CMS->input['sub_act'] == "load_logs_ajax")
				{
					$this->load_logs_ajax();
				}
				else
				{
						$this->default_page();
				}
			break;
		}
	}
	
	public function detail()
	{
		global $CMS, $DB;
		
		$id = $CMS->input['id'];
		
		// Get data
		$data = $DB->fetch_array($DB->query("SELECT * FROM ".root_table."logs WHERE log_id='{$id}'"));
		
		// Convert log_content
		$content = unserialize($data['log_content']);
	//	print_r ($content);exit;

		$array_key = array_keys($content);
		
		// Convert languge
		$data['log_key_bk'] = explode("_",$data['log_key']);
		
		$lang = "";
		switch ($data['log_key_bk'][0])
		{
			case "customer":
				$lang = "customer";
			break;
		}
		
		if( $lang ){ $CMS->class->language->load("{$lang}"); }
		
		$output = "";
		$output .= "<ul style='margin:0;padding:0;list-style-type:none'>";
		
		for($i=0;$i < count($array_key);$i=$i+2) 
		{
			$key = $CMS->lang["{$array_key[$i]}"] ? $CMS->lang["{$array_key[$i]}"] : $array_key[$i]; 
			$j = $i+1;
			$output .= "<li>"."<font color='#0000FF'>".$key.": "."</font>".$content[$array_key[$i]]." => "."<font co color='#FF0000'>".$content[$array_key[$j]]."</font>"."</li>";
		}
		$output .= "</ul>";
		
		$data['log_content'] = $output;
		
		if($CMS->input['type'] == "ajax")
		{
			print $this->html->detail($data);exit;	
		}
		
		$CMS->output .= $this->html->detail($data);	
	}
	
	public function default_page()
	{
		global $CMS, $DB;

		$CMS->output .= $this->html->logs();	
	}

	public function load_logs_ajax()
	{
		global $CMS, $DB;
	
		$log_key = $CMS->input['log_key'];
		$start = intval($CMS->input['start']);

		$sql = "SELECT * FROM " . root_table . "logs".($log_key?" WHERE log_key NOT LIKE 'sms_%' AND log_key='{$log_key}' ":"").($log_key2 ? "OR log_key='{$log_key2}'" : "")." ORDER BY log_time DESC LIMIT {$start}, 10";
        $sql_query = $DB->query($sql);

         if($DB->num_rows($sql_query)  > 0)
         {
         	   $i = 0;
         	while($data = $DB->fetch_array($sql_query))
         	{
         		$user = $CMS->user->get_info($data['user_id']);
	            $detail = "";
	            $data['log_ftime'] = $CMS->class->date->date_format($data['log_time'], 1);
	            $data['log_time'] = $CMS->class->date->date_format($data['log_time'], 1);
	            $data['log_name'] = $data['log_name'];

            if ($data['log_content']) {
                $detail = "<span onclick='show_popup_detail({$data['log_id']});' log_id='{$data['log_id']}'><a>[{$CMS->lang['btn_detail']}]</a></span>";
            }
            if ($user['user_avatar'] != "") {

                $user_avatar = <<<EOF
                                                            
                                <img src="{$CMS->vars['upload_url']}/avatar/thumbnail/{$user['user_avatar']}" alt="user-img" class="img-circle user-img" >
                                
EOF;
            } else {

                $user_avatar = <<<EOF
                                                            
                                <img src="assets/img/avatar-2-64.png" alt="user-img" class="img-circle user-img">
                                
EOF;
            }
         
            $output .= <<<EOF

        <article class="activity-line-item box-typical">
            <div class="activity-line-date">
                        {$data['log_time']}<br/>
                       
             </div>
            <header class="activity-line-item-header">
                <div class="activity-line-item-user">
                    <div class="activity-line-item-user-photo">
                        <a href="{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}" class="pull-left">{$user_avatar}

                        </a>
                         
                    </div>
                    <div class="activity-line-item-user-name">{$user['user_display_name']}</div>
                    <div class="activity-line-item-user-status">{$user['userg_title']}</div>
                </div>
            </header>
            <div class="activity-line-action-list">
                <section class="activity-line-action">
                    
                        <div class="cont">
                            <div class="cont-in">
                                    <p>{$data['log_name']} {$detail} </p>
                             </div>
                        </div>
                    </section><!--.activity-line-action-->
            </div> <!-- activity-line-action-list-->

        </article><!-- activity-line-item box-typical-->

EOF;

         	}


         	print json_encode(array("status" => "success", "msg" => "", "data" => $output));exit;
         }   
         else
         {  
         	print json_encode(array("status" => "error", "msg" => "nodata" ));exit;
         }


	}



	
}

?>
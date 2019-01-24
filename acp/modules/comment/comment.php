<?php

$comment = new comment;
$comment->auto_run();

class comment {

	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['comment_header']}";

		// Load the models
		$CMS->comment->auto_run();

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
			case "approve":
				$this->approve();
			break;
			case "hide":
				$this->hide();
			break;
			case "unhide":
				$this->unhide();
			break;
			case "hide_all":
				$this->hide_all();
			break;
			default:
				if($CMS->input['sub_act'] == "load_comment_ajax")
				{
					$this->load_comment_ajax();
				}
				else
				{
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
		
		if ( $CMS->input['is_ajax'] == 1 )
		{
			$this->loadlist();
			
			return false;
		}
		
		$CMS->output .= $CMS->comment->html->show( $CMS->comment->convertvalue($CMS->comment->get_info()) );
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;
		
		$CMS->output .= $CMS->comment->html->add( $CMS->comment->defaultvalue($CMS->input) );
	}
	
	public function add_do()
	{
		global $CMS, $DB, $member;
		$comment = $CMS->comment->add();

		if( is_array($comment))
		{
			   $result = $CMS->comment->convertvalue($comment);
				  $user = $CMS->user->get_info($result['user_id']);
		 		$output .=<<<EOF
		 		
  				 <article class="activity-line-item box-typical" style="   border-radius: 0px !important;" comment_id='{$result['comment_id']}'>
                                    <div class="activity-line-date">
                                                {$result['comment_time']}<br/>
                                               
                                     </div>
                                    <header class="activity-line-item-header">
                                        <div class="activity-line-item-user">
                                            <div class="activity-line-item-user-photo">
                                                 {$result['user_avatar']}
                                                 
                                            </div>
                                            <div class="activity-line-item-user-name">{$result['comment_name']}</div>
                                            <div class="activity-line-item-user-status">{$user['userg_title']}</div>
                                        </div>
                                    </header>
                                    <div class="activity-line-action-list">
                                        <section class="activity-line-action">
                                            
                                                <div class="cont">
                                                    <div class="cont-in">
                                                            <p>{$result['comment_content']} </p>
                                                     </div>
                                                </div>
                                            </section><!--.activity-line-action-->
                                    </div> <!-- activity-line-action-list-->

             </article><!-- activity-line-item box-typical-->

     

EOF;

			print json_encode(array("status" => "success", "msg" => "Thêm ghi chú thành công!", "last_msg" => $output));exit;
		}
		else
		{
			print json_encode(array("status" => "error", "msg" => "Thêm ghi chú thất bại!"));exit;
		}
		 
	}
	
	
	//===========================================================================
	//  EDIT
	//===========================================================================
	
	public function edit()
	{
		global $CMS, $DB, $member;
		
		$comment = $CMS->comment->get_info();
		
		$CMS->output .= $CMS->comment->html->edit( $CMS->comment->editvalue($comment) );
	}
	
	public function edit_do()
	{
		global $CMS, $DB, $member;
		
		if ( $data = $CMS->comment->edit() )
		{
			if ( is_array($data) == true )
			{
				//$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=comment&act=show&id={$data['comment_id']}");
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=comment&act=show&id={$data['comment_id']}");
			}
			else
			{
				$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=comment");
			}
		}

		$CMS->output .= $CMS->comment->html->edit( $CMS->comment->get_info() );
	}
	
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete()
	{
		global $CMS, $DB, $member;
		
		// Delete
		$CMS->comment->delete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=comment");	
	}
	
	public function delete_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->comment->mdelete();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=comment");	
	}
	
	
	//===========================================================================
	//  SEARCH
	//===========================================================================
	
	public function search()
	{
		global $CMS, $DB, $member;

		$CMS->output .= $CMS->comment->html->search( $CMS->class->search->clean($CMS->input) );
	}
	
	public function search_do()
	{
		global $CMS, $DB, $member;
		
		// Get List
		$CMS->comment->search();
		
		// Write Data
		$CMS->output .= $CMS->comment->html();
	}
	
	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;
		$module_id = $CMS->input['module_id'];
		$module_name = $CMS->input['module_name'];
		// Get List
		$CMS->comment->listing();
		$CMS->comment->sql_add .= " module_name ='{$module_name}' AND module_id='{$module_id}' AND ";	
		// Write data
		$msg = $CMS->comment->html();


		print json_encode(array("status" => "success", "data" => $msg));exit;

	}
	
	//===========================================================================
	//  LIST
	//===========================================================================
	
	public function loadlist()
	{
		global $CMS, $DB, $member;
		
		$CMS->comment->loadlist("ajax");
		
		exit;
	}
	//===========================================================================
	//  ACtive
	//===========================================================================
	
	public function active()
	{
		global $CMS, $DB, $member;
		
		$CMS->comment->active();
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=comment");
		
	}
	
	//===========================================================================
	//  ACtive
	//===========================================================================
	
	public function approve()
	{
		global $CMS, $DB, $member;
		
		$CMS->comment->approve();
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=comment");
	}
	
	
	//===========================================================================
	//  hide
	//===========================================================================
	
	public function hide()
	{
		global $CMS, $DB, $member;
		
		$CMS->comment->hide();
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=comment");
	}
	//===========================================================================
	//  UNHIDE COMMENT
	//===========================================================================
	
	public function unhide()
	{
		global $CMS, $DB, $member;
	
		$CMS->comment->unhide();
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=comment");
	}
	
	//===========================================================================
	//  hide all COMMENT
	//===========================================================================
	
	public function hide_all()
	{
		global $CMS, $DB, $member;
		
		// Delete all
		$CMS->comment->mhide();
		
		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=comment");	
	}


	public function load_comment_ajax()
	{
		global $CMS, $DB;
	
		$module_name = $CMS->input['module_name'];
		$module_id = $CMS->input['module_id'];
		$start = intval($CMS->input['start']);

		 

		$sql = $DB->query("SELECT * FROM ".root_table."comment WHERE module_name='{$module_name}' AND module_id='{$module_id}' AND comment_deleted IN (0,2) AND  1=1 ORDER BY comment_time DESC LIMIT {$start},10");


       

         if($DB->num_rows($sql)  > 0)
         {
         	   $i = 0;
         	while($data = $DB->fetch_array($sql))
         	{
         		  $result = $CMS->comment->convertvalue($data);
 				  $user = $CMS->user->get_info($result['user_id']);
         
            $output .= <<<EOF

         <article class="activity-line-item box-typical" style="   border-radius: 0px !important;" comment_id='{$result['comment_id']}'>
                                    <div class="activity-line-date">
                                                {$result['comment_time']}<br/>
                                               
                                     </div>
                                    <header class="activity-line-item-header">
                                        <div class="activity-line-item-user">
                                            <div class="activity-line-item-user-photo">
                                                 {$result['user_avatar']}
                                                 
                                            </div>
                                            <div class="activity-line-item-user-name">{$result['comment_name']}</div>
                                            <div class="activity-line-item-user-status">{$user['userg_title']}</div>
                                        </div>
                                    </header>
                                    <div class="activity-line-action-list">
                                        <section class="activity-line-action">
                                            
                                                <div class="cont">
                                                    <div class="cont-in">
                                                            <p>{$result['comment_content']} </p>
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
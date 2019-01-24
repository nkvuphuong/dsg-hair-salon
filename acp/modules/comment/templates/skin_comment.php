<?php

class skin_comment {

//===========================================================================
//  HTML HEADER
//===========================================================================

public function comment_header()
{
	global $CMS, $DB, $member;
	$search_url =str_replace($CMS->class->search->url_return,"",$CMS->class->search->url_return);
	$output = "";

$output .= <<<EOF
<div class="block_wrapper">
<div class="block_top">
<a href="{$CMS->vars['root_domain']}/?site=comment">{$CMS->lang['comment_header']}</a>


EOF;
	if($CMS->input['site'] =="comment")
    {
    	$output .=<<<EOF
<form name="quick_search" id="quick_search" method="post" action="{$CMS->vars['root_domain']}/?site=comment&act=search_do{$search_url}" onSubmit="return check_form(this.id);" >
<select class="input_text" name="comment_approved" id="comment_approved" defaultvalue="{$CMS->input['comment_approved']}" onchange="this.form.submit()">
	<option value=''>Chấp nhận ...</option>
	<option value='1'>Có</option>
    <option value='0'>Không</option>
</select>
<select class="input_text" name="comment_hide" id="comment_hide" defaultvalue="{$CMS->input['comment_hide']}" onchange="this.form.submit()">
	<option value=''>Ẩn hiện...</option>
	<option value='1'>Ẩn</option>
    <option value='0'>Hiện</option>
</select>
</form>
<script language="javascript">rebuild_form("quick_search",1,1);</script>
EOF;
	}
    $output .=<<<EOF
    
    
</div>

<form method="post" name="comment" id="comment" action="{$CMS->vars['root_domain']}/?site=comment" onSubmit="return check_form(this.id);">

<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="10">
  <tr>
    <th width="3%"></th>
    <th width="5%" id="order_comment_id">{$CMS->lang['comment_id']}</th>

    <th width="12%">{$CMS->lang['module_name']}</th>
    <th width="15%">{$CMS->lang['module_id']}</th>
    <th width="20%">{$CMS->lang['comment_content']}</th>

	<th width="10%" id="order_comment_time">{$CMS->lang['comment_time']}</th>
    <th width="8%" >{$CMS->lang['comment_ip_address']}</th>
    
    <th width="15%" id="order_comment_approved">{$CMS->lang['comment_approved']}</th>
    <th width="10%" >{$CMS->lang['comment_status']}</th>
    <th width="10%">Ẩn/Hiện bình luận</th>
    <th width="1%">{$CMS->lang['edit']}</th>
    <th width="1%">{$CMS->lang['delete']}</th>
  </tr>
EOF;

	return $output;
}

//===========================================================================
//  HTML MIDDLE
//===========================================================================

public function comment_middle($result)
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
  <tr bgcolor="{$result['bgcolor']}">
    <td><input type='checkbox' name='id_{$result['record_cnt']}' value='{$result['comment_id']}' /></td>
	<td>#{$result['comment_id']}</td>

    <!--<td>{$result['comment_name']}</td>-->
    <td>{$result['module_name_bk']}</td>
    <td>{$result['module_id_bk']}</td>
    <td>{$result['comment_content_bk']}</td>
    <td>{$result['comment_time']}</td>
    <td>{$result['comment_ip_address']}</td>
    <td>{$result['comment_approved']}</td>
     <td>{$result['comment_hide_bk']}</td>
     <td align="center">
	
EOF;

			if ( $CMS->permit["comment_hide"] == true AND $result['comment_hide'] == 0)
			{
            	    $output .=<<<EOF
            	<a href="{$CMS->vars['root_domain']}/?site=comment&act=hide&id={$result['comment_id']}">Ẩn bình luận</a>
EOF;
            }
			elseif ( $CMS->permit["comment_hide"] == true AND $result['comment_hide'] == 1)
			{
            	    $output .=<<<EOF
            	<a href="{$CMS->vars['root_domain']}/?site=comment&act=unhide&id={$result['comment_id']}">Hiển thị bình luận</a>
EOF;
            }
            $output .=<<<EOF
     
     </td>

    <td align="center"><script type="text/javascript">permission_btn("edit", "comment", "{$CMS->vars['root_domain']}/?site=comment&act=edit&id={$result['comment_id']}");</script></td>
    <td align="center"><script type="text/javascript">permission_btn("delete", "comment", "{$CMS->vars['root_domain']}/?site=comment&act=delete&id={$result['comment_id']}");</script></td>
  </tr>
EOF;
	
    return $output;
}

//===========================================================================
//  NO DATA
//===========================================================================

public function comment_none()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
  <tr>
    <td colspan="10">{$CMS->lang['comment_no_data']}</td>
  </tr>
EOF;

	return $output;
}

//===========================================================================
//  HTML FOOTER
//===========================================================================

public function comment_footer()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
</table>
</div>
<input type="hidden" name="data_cnt" value="{$CMS->comment->record_cnt}">
</div>
{$CMS->comment->action_control}
<div class="block_bottom">{$CMS->comment->show_page}</div>
</form>

<script language="javascript">rebuild_form("comment");</script>
<script language="javascript">arrange_setup("{$CMS->comment->arrange_data}");</script>
EOF;

	return $output;
}

//===========================================================================
//  USER CONTROL
//===========================================================================

public function comment_control()
{
	global $CMS, $DB, $member;
    
	$output = "";

$output .= <<<EOF
<div class="block_action">
	<div class="block_action_check" onclick="javascript:form_checkall('comment');" id="checkall">
    	<input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
	</div>
	<div class="block_action_left">
        <select style="margin-left: 6px;;" class="input_text" name="act" style="width: 300px;" defaultvalue="delete_all" emsg="{$CMS->lang['comment_incomplete_action']}" ehide="1">{$CMS->vars['action_controller']}</select>
        &nbsp;
        <input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['comment_action_submit']} ">
    </div>
    <div class="block_action_right">
        <script type="text/javascript">permission_text("comment_search", '<input class="input_submit" type="button" name="submit" value="{$CMS->lang['search_form']}" onclick="javascript:window.location.href=\'{$CMS->vars['root_domain']}/?site=comment&act=search{$CMS->class->search->url_return}\'" />');</script>
    </div>
</div>
EOF;

    return $output;
}

//===========================================================================
//  HTML EDIT
//===========================================================================

public function edit( $data )
{
	global $CMS, $DB, $member;
	
	$output = "";
	
    //$output = $CMS->class->editor->simple();

$output .= <<<EOF
<form method="post" id="comment" name="comment" action="{$CMS->vars['root_domain']}/?site=comment&id={$data['comment_id']}&act=edit_do&page={$CMS->input['page']}" onSubmit="return check_form(this.id);">
<div class="block_wrapper">
<div class="block_top"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=comment{$CMS->class->search->url_return}">&laquo; {$CMS->lang['comment_header_back']}</a></p>{$CMS->lang['comment_edit_form']} <b>{$data['comment_name']}</b></div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
  	<th colspan="2">{$CMS->lang['comment_required_info']}</th>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['module_id']}</b>:</td>
    <td><input class="input_text" size="15" type="text" name="module_id" value="{$data['module_id']}" emsg="{$CMS->lang['comment_incomplete_moduleid']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['module_name']}</b>:</td>
    <td><input class="input_text" size="20" type="text" name="module_name" value="{$data['module_name']}" emsg="{$CMS->lang['comment_incomplete_modulename']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_username']}</b>:</td>
    <td><input class="input_text" size="30" type="text" name="comment_username" value="{$data['comment_username']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_email']}</b>:</td>
    <td><input class="input_text" size="30" type="text" name="comment_email" value="{$data['comment_email']}" etype="email"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_name']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="comment_name" value="{$data['comment_name']}" emsg="{$CMS->lang['comment_incomplete_name']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_content']}</b>:</td>
    <td><textarea class="input_text" name="comment_content" cols="80" rows="10" emsg="{$CMS->lang['comment_incomplete_content']}">{$data['comment_content']}</textarea></td>
  </tr>
  <tr>
  	<th colspan="2">{$CMS->lang['comment_add_info']}</th>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_approved']}</b>:</td>
    <td><input type="radio" class="input_radio" name="comment_approved" defaultvalue="{$data['comment_approved']}" value="1"><font color="blue"><b>{$CMS->lang['yes']}</b></font> &nbsp; &nbsp; <input type="radio" name="comment_approved" defaultvalue="{$data['comment_approved']}" value="0" checked="checked"> <font color="red"><b>{$CMS->lang['no']}</b></font></td>
  </tr>
</table>
</div>
</div>
<div class="block_desc_bottom_submit">
	<input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['comment_edit_submit']} "> 
</div>
<div class="block_bottom"></div>
</form>
<script language="javascript">rebuild_form("comment",1);</script>
EOF;
	
	return $output;	
}

//===========================================================================
//  HTML ADD
//===========================================================================

public function add( $data )
{
	global $CMS, $DB, $member;
	
	$output = "";
    
    //$output = $CMS->class->editor->simple();

$output .= <<<EOF
<form method="post" id="comment" name="comment" action="{$CMS->vars['root_domain']}/?site=comment&act=add_do" onSubmit="return check_form(this.id);">
<div class="block_wrapper">
<div class="block_top"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=comment{$CMS->class->search->url_return}">&laquo; {$CMS->lang['comment_header_back']}</a></p>{$CMS->lang['comment_add_form']}</div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
  	<th colspan="2">{$CMS->lang['comment_required_info']}</th>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['module_id']}</b>:</td>
    <td><input class="input_text" size="15" type="text" name="module_id" value="{$data['module_id']}" emsg="{$CMS->lang['comment_incomplete_moduleid']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['module_name']}</b>:</td>
    <td><input class="input_text" size="20" type="text" name="module_name" value="{$data['module_name']}" emsg="{$CMS->lang['comment_incomplete_modulename']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_username']}</b>:</td>
    <td><input class="input_text" size="30" type="text" name="comment_username" value="{$data['comment_username']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_email']}</b>:</td>
    <td><input class="input_text" size="30" type="text" name="comment_email" value="{$data['comment_email']}" etype="email"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_name']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="comment_name" value="{$data['comment_name']}" emsg="{$CMS->lang['comment_incomplete_name']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_content']}</b>:</td>
    <td><textarea class="input_text" name="comment_content" cols="60" rows="5" emsg="{$CMS->lang['comment_incomplete_content']}">{$data['comment_content']}</textarea></td>
  </tr>
  <tr>
  	<th colspan="2">{$CMS->lang['comment_add_info']}</th>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_approved']}</b>:</td>
    <td><input type="radio" class="input_radio" name="comment_approved" defaultvalue="{$data['comment_approved']}" value="1"><font color="blue"><b>{$CMS->lang['yes']}</b></font> &nbsp; &nbsp; <input type="radio" name="comment_approved" defaultvalue="{$data['comment_approved']}" value="0" checked="checked"> <font color="red"><b>{$CMS->lang['no']}</b></font></td>
  </tr>
</table>
</div>
</div>
<div class="block_desc_bottom_submit">
	<input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['comment_add_submit']} "> 
</div>
<div class="block_bottom"></div>
</form>
<script language="javascript">rebuild_form("comment",1);</script>
EOF;
	
	return $output;	
}

//===========================================================================
//  HTML SHOW
//===========================================================================

public function show( $data )
{
	global $CMS, $DB, $member;
	
	$output = "";
    
$output .= <<<EOF
<div class="block_wrapper">
<div class="block_top"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=comment{$CMS->class->search->url_return}">&laquo; {$CMS->lang['comment_header_back']}</a></p>{$CMS->lang['comment_show_form']} <b>{$data['comment_name']}</b></div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
  	<th colspan="2">{$CMS->lang['comment_required_info']}</th>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_id']}</b></td>
    <td>{$data['comment_id']}
	&nbsp; <script type="text/javascript">permission_btn("{$data['comment_edit']}", "comment", "{$CMS->vars['root_domain']}/?site=comment&act=edit&id={$data['comment_id']}");</script>
    &nbsp; <script type="text/javascript">permission_btn("{$data['comment_delete']}", "comment", "{$CMS->vars['root_domain']}/?site=comment&act=delete&id={$data['comment_id']}");</script></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['module_id']}</b>:</td>
    <td>{$data['module_id']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['module_name']}</b>:</td>
    <td>{$data['module_name']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_username']}</b>:</td>
    <td>{$data['comment_username']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_email']}</b>:</td>
    <td>{$data['comment_email']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_name']}</b>:</td>
    <td>{$data['comment_name']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_content']}</b>:</td>
    <td>{$data['comment_content']}</td>
  </tr>
  <tr>
  	<th colspan="2">{$CMS->lang['comment_add_info']}</th>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['user_id']}</b>:</td>
    <td>{$data['user_name']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_time']}</b>:</td>
    <td>{$data['comment_time']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_time_update']}</b>:</td>
    <td>{$data['comment_time_update']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_ip_address']}</b>:</td>
    <td>{$data['comment_ip_address']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_approved']}</b>:</td>
    <td>{$data['comment_approved']}</td>
  </tr>
  
EOF;
		if($CMS->permit["comment_approve"] == TRUE) 
        {
        
        if($data['comment_approved'] == 1)
        {
            $output .=<<<EOF
            
          <tr>
            <td class="left25"></td>
            <td>
                 <div class="block_desc_bottom_submit">
                     <a class="input_submit" href="{$CMS->vars['root_domain']}/?site=comment&act=active&id={$data['comment_id']}" style="margin-left:0px">Duyệt bình luận</a>
                </div>
            </td>
           </tr>
       
EOF;
		}
	}  
         $output .=<<<EOF
</table>
</div>
</div>
<div class="block_bottom"></div>
EOF;
	
	return $output;	
}

//===========================================================================
//  HTML SEARCH
//===========================================================================

public function search($data)
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
<form method="post" id="comment" name="comment" action="{$CMS->vars['root_domain']}/?site=comment&act=search_do" onSubmit="return check_form(this.id);">
<div class="block_wrapper">
<div class="block_top"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=comment{$CMS->class->search->url_return}">&laquo; {$CMS->lang['comment_header_back']}</a></p>{$CMS->lang['search_form']}</div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
    <td class="left25"><b>{$CMS->lang['module_id']}</b>:</td>
    <td><input class="input_text" size="15" type="text" name="module_id" value="{$data['module_id']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['module_name']}</b>:</td>
    <td><input class="input_text" size="30" type="text" name="module_name" value="{$data['module_name']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_name']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="comment_name" value="{$data['comment_name']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_content']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="comment_content" value="{$data['comment_content']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>Ẩn/hiện</b>:</td>
     <td>   <select class="input_text" name="comment_hide" id="comment_hide" defaultvalue="{$CMS->input['comment_hide']}" onchange="this.form.submit()">
            <option value=''>Ẩn hiện...</option>
            <option value='1'>Ẩn</option>
            <option value='0'>Hiện</option>
        </select>
      </td>
   </tr>     
  <tr>
    <td class="left25"><b>{$CMS->lang['comment_approved']}</b>:</td>
    <td><select class="input_text" name="comment_approved" defaultvalue="{$data['comment_approved']}"><option value="">{$CMS->lang['all']}</option><option value="0">{$CMS->lang['answer_0']}</option><option value="1">{$CMS->lang['answer_1']}</option></select></td>
  </tr>
</table>
</div>
</div>
<div class="block_desc_bottom_submit">
	<input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['search_submit']} "> 
</div>
<div class="block_bottom"></div>
</form>
<script language="javascript">rebuild_form("comment",1,1);</script>
EOF;
	
	return $output;	
}

//===========================================================================
//  HTML SEARCH
//===========================================================================

public function comment_form()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
<script type="text/javascript" src="{$CMS->vars['js_url']}/acp_comment.js"></script>
<div class="module_comment" id="module_comment"></div>
<div class="module_comment_page" id="module_comment_page"></div>        
<form name="comment" method="post" action="{$CMS->vars['root_domain']}/?site=comment&act=add_do&module_id={$CMS->comment->module_id}&module_name={$CMS->comment->module_name}" target="_blank" onSubmit="comment_insert(this);return false;">
	<input type="hidden" name="comment_name" value="Comment in {$CMS->comment->module_name} {$CMS->comment->module_id}" />
	<input type="hidden" name="comment_username" value="{$member['user_display_name']}" />
	<input type="hidden" name="comment_email" value="{$member['user_email']}" />
	<input type="hidden" name="comment_approved" value="1" />
	<textarea class="form-control" name="comment_content"  placeholder="Press Enter" data-autosize="" style="overflow: hidden; word-wrap: break-word; resize: vertical; margin-bottom:15px"></textarea>
	<div align="right">
		<input style="font-weight:normal" class="btn btn-inline btn-danger" type="reset" name="submit" value="{$CMS->lang['comment_reset']}" />
		<input style="font-weight:normal; margin-right:0" class="btn btn-inline btn-primary" type="submit" name="submit" value="{$CMS->lang['comment_send']}" /> 
   </div>
</form>
<script language="javascript">comment_load("{$CMS->comment->module_name}", "{$CMS->comment->module_id}");</script>
EOF;
	
	return $output;	
}




}

?>
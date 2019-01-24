<?php

class skin_html {

//===========================================================================
//  HTML HEADER
//===========================================================================

public function html_header()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
<form method="post" name="html" id="html" action="{$CMS->vars['root_domain']}/?site=html" onSubmit="return check_form(this.id);">
<div class="block_wrapper">
<div class="block_top">
<p class="align_right">
<script type="text/javascript">permission_text("html_add", '<a href="{$CMS->vars['root_domain']}/?site=html&act=add"><img src="{$CMS->vars['img_url']}/con_add.png" align="absmiddle" title="{$CMS->lang['html_add_form']}"></a>');</script>
</p><a href="{$CMS->vars['root_domain']}/?site=html">{$CMS->lang['html_header']}</a>

</div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="10">
  <tr>
    <th width="5%"></th>
    <th width="5%" id="order_html_id">{$CMS->lang['html_id']}</th>
	<th width="25%" id="order_html_name">{$CMS->lang['html_name']}</th>
    <th width="20%">{$CMS->lang['user_id']}</th>
	<th width="20%" id="order_html_time">{$CMS->lang['html_time']}</th>
    <th width="20%">{$CMS->lang['html_key']}</th>
    <th width="1%">{$CMS->lang['edit']}</th>
    <th width="1%">{$CMS->lang['delete']}</th>
  </tr>
EOF;

	return $output;
}

//===========================================================================
//  HTML MIDDLE
//===========================================================================

public function html_middle($result)
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
  <tr bgcolor="{$result['bgcolor']}">
    <td><input type="checkbox" name="id_{$result['record_cnt']}" value="{$result['html_id']}"></td>
	<td>#{$result['html_id']}</td>
    <td>{$result['html_name']}</td>
    <td>{$result['user_id']}</td>
    <td>{$result['html_key']}</td>
    <td>{$result['html_time']}</td>
    <td align="center"><script type="text/javascript">permission_btn("edit", "html", "{$CMS->vars['root_domain']}/?site=html&act=edit&id={$result['html_id']}");</script></td>
    <td align="center"><script type="text/javascript">permission_btn("delete", "html", "{$CMS->vars['root_domain']}/?site=html&act=delete&id={$result['html_id']}");</script></td>
  </tr>
EOF;
	
    return $output;
}

//===========================================================================
//  NO DATA
//===========================================================================

public function html_none()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
  <tr>
    <td colspan="8">{$CMS->lang['html_no_data']}</td>
  </tr>
EOF;

	return $output;
}

//===========================================================================
//  HTML FOOTER
//===========================================================================

public function html_footer()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
</table>
</div>
<input type="hidden" name="data_cnt" value="{$CMS->html->record_cnt}">
</div>
{$CMS->html->action_control}
<div class="block_bottom pagination pagination-sm">{$CMS->show_page}</div>
</form>

<script language="javascript">rebuild_form("html");</script>
<script language="javascript">arrange_setup("{$CMS->html->arrange_data}");</script>
EOF;

	return $output;
}

//===========================================================================
//  USER CONTROL
//===========================================================================

public function html_control()
{
	global $CMS, $DB, $member;
    
	$output = "";

$output .= <<<EOF
<div class="block_action">
	<div class="block_action_check" onclick="javascript:form_checkall('html');" id="checkall">
    	<input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
	</div>
	<div class="block_action_left">
        <select style="margin-left: 6px;;" class="input_text" name="act" style="width: 300px;" defaultvalue="delete_all" emsg="{$CMS->lang['html_incomplete_action']}" ehide="1">{$CMS->vars['action_controller']}</select>
        &nbsp;
        <input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['html_action_submit']} ">
    </div>
    <div class="block_action_right">
        <script type="text/javascript">permission_text("html_search", '<input class="input_submit" type="button" name="submit" value="{$CMS->lang['search_form']}" onclick="javascript:window.location.href=\'{$CMS->vars['root_domain']}/?site=html&act=search{$CMS->class->search->url_return}\'" />');</script>
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
	
    $output = $CMS->class->editor->simple();

$output .= <<<EOF
<form method="post" id="html" name="html" action="{$CMS->vars['root_domain']}/?site=html&id={$data['html_id']}&act=edit_do&page={$CMS->input['page']}" onSubmit="return check_form(this.id);">
<div class="block_wrapper">
<div class="block_top"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=html{$CMS->class->search->url_return}">&laquo; {$CMS->lang['html_header_back']}</a></p>{$CMS->lang['html_edit_form']} <b>{$data['html_name']}</b></div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
    <td class="left25"><b>{$CMS->lang['html_name']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="html_name" value="{$data['html_name']}" emsg="{$CMS->lang['html_incomplete_name']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['html_key']}</b>:</td>
    <td><input class="input_text" size="30" type="text" name="html_key" value="{$data['html_key']}" emsg="{$CMS->lang['html_incomplete_key']}"></td>
  </tr>
  <tr>
    <td colspan="2"><textarea class="input_textarea" name="html_content" cols="60" rows="5" emsg="{$CMS->lang['html_incomplete_content']}">{$data['html_content']}</textarea></td>
  </tr>
  <tr>
    <td style="width: 20%;" class="left25"><b>{$CMS->lang['upload_file']}</b></td>
    <td>
EOF;

	$output .= $CMS->attach->form("html", "{$data['html_id']}");
	
$output .= <<<EOF
	</td>
  </tr>
</table>
</div>
</div>
<div class="block_desc_bottom_submit">
	<input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['html_edit_submit']} "> 
</div>
<div class="block_bottom pagination pagination-sm"></div>
</form>
<script language="javascript">rebuild_form("html",1);</script>
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
    
    $output = $CMS->class->editor->simple();

$output .= <<<EOF
<form method="post" id="html" name="html" action="{$CMS->vars['root_domain']}/?site=html&act=add_do" onSubmit="return check_form(this.id);">
<div class="block_wrapper">
<div class="block_top"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=html{$CMS->class->search->url_return}">&laquo; {$CMS->lang['html_header_back']}</a></p>{$CMS->lang['html_add_form']}</div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
    <td class="left25"><b>{$CMS->lang['html_name']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="html_name" value="{$data['html_name']}" emsg="{$CMS->lang['html_incomplete_name']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['html_key']}</b>:</td>
    <td><input class="input_text" size="30" type="text" name="html_key" value="{$data['html_key']}" emsg="{$CMS->lang['html_incomplete_key']}"></td>
  </tr>
  <tr>
    <td colspan="2"><textarea class="input_textarea" name="html_content" cols="60" rows="5" emsg="{$CMS->lang['html_incomplete_content']}">{$data['html_content']}</textarea></td>
  </tr>
  <tr>
    <td style="width: 20%;" class="left25"><b>{$CMS->lang['upload_file']}</b></td>
    <td>
EOF;

	$output .= $CMS->attach->form("html", "{$data['html_id']}");
	
$output .= <<<EOF
	</td>
  </tr>
</table>
</div>
</div>
<div class="block_desc_bottom_submit">
	<input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['html_add_submit']} "> 
</div>
<div class="block_bottom pagination pagination-sm"></div>
</form>
<script language="javascript">rebuild_form("html",1);</script>
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
<script type="text/javascript" src="{$CMS->vars['js_url']}/acp_html.js"></script>
<div class="block_wrapper">
<div class="block_top"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=html{$CMS->class->search->url_return}">&laquo; {$CMS->lang['html_header_back']}</a></p>{$CMS->lang['html_show_form']} <b>{$data['html_name']}</b></div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
  	<th colspan="2">{$CMS->lang['html_required_info']}</th>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['html_id']}</b></td>
    <td>{$data['html_id']}
	&nbsp; <script type="text/javascript">permission_btn("edit", "html", "{$CMS->vars['root_domain']}/?site=html&act=edit&id={$data['html_id']}");</script>
    &nbsp; <script type="text/javascript">permission_btn("delete", "html", "{$CMS->vars['root_domain']}/?site=html&act=delete&id={$data['html_id']}");</script></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['html_name']}</b>:</td>
    <td>{$data['html_name']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['html_content']}</b>:</td>
    <td>{$data['html_content']}</td>
  </tr>
  <tr>
  	<th colspan="2">{$CMS->lang['html_add_info']}</th>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['user_id']}</b>:</td>
    <td>{$data['user_id']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['html_time']}</b>:</td>
    <td>{$data['html_time']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['html_time_update']}</b>:</td>
    <td>{$data['html_time_update']}</td>
  </tr>
</table>
</div>
</div>
<div class="block_bottom pagination pagination-sm"></div>
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
<form method="post" id="html" name="html" action="{$CMS->vars['root_domain']}/?site=html&act=search_do" onSubmit="return check_form(this.id);">
<div class="block_wrapper">
<div class="block_top"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=html{$CMS->class->search->url_return}">&laquo; {$CMS->lang['html_header_back']}</a></p>{$CMS->lang['search_form']}</div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
    <td class="left25"><b>{$CMS->lang['html_name']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="html_name" value="{$data['html_name']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['html_content']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="html_content" value="{$data['html_content']}"></td>
  </tr>
</table>
</div>
</div>
<div class="block_desc_bottom_submit">
	<input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['search_submit']} "> 
</div>
<div class="block_bottom pagination pagination-sm"></div>
</form>
<script language="javascript">rebuild_form("html",1,1);</script>
EOF;
	
	return $output;	
}

}

?>
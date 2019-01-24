<?php

class skin_newsletter {

//===========================================================================
//  HTML HEADER
//===========================================================================

public function header()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
<section class="add_table main_form">
    <figure class="heading">
      <h3>{$CMS->lang['newsletter_header']}</h3>
      <figure class="pull-right right">
          {$CMS->global->importExportData($CMS->input['site'],'',1)}
          <a href="{$CMS->vars['root_domain']}/?site=newsletter&act=add" title="" class="add_bill">{$CMS->lang['btn_send_mail']}</a>
          <a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
        </figure>
    </figure>
EOF;

	if ( $CMS->newsletter->js_id )
	{
		
$output .= <<<EOF
<form method="post" name="{$CMS->newsletter->js_id}" id="{$CMS->newsletter->js_id}" style="display: none;" action="{$CMS->vars['root_domain']}/?site=newsletter" onSubmit="return check_form(this.id);">
<div class="block_top tab_bar_addon">
EOF;

	}
	else
	{

$output .= <<<EOF
<form method="post" name="newsletter" id="newsletter" action="{$CMS->vars['root_domain']}/?site=newsletter" onSubmit="return check_form(this.id);">
<div class="block_top">
EOF;

	}

$output .= <<<EOF

    <section class="add_table">
        <div class="table-responsive">
          <table class="table table_cus table-hover">
            <thead>
              <tr>
                <th width="1%" >
                  <div class="checkbox" style="margin: 0;">
                    <input type="checkbox" name="checkall" id="check-1" check="0"/>
                    <label for="check-1"></label>
                  </div>
                </th>
                <th width="5%" id="order_newsletter_id">{$CMS->lang['newsletter_id']}</th>
                <th width="40%" id="order_newsletter_title">{$CMS->lang['newsletter_email']}</th>
                <th width="10%"  id="order_newsletter_time">{$CMS->lang['newsletter_time']}</th>
                <th width="5%" class="block_desc_top_right"></th>
              </tr>
            </thead>
EOF;

	return $output;
}

//===========================================================================
//  HTML MIDDLE
//===========================================================================

public function middle($result)
{
	global $CMS, $DB, $member;
	
	$output = "";

	// Group time          
  if ( $CMS->newsletter->group_time != $result['newsletter_time_short'] )
	{
        
$output .= <<<EOF
  <tr>
	<td colspan="5" style="background: #fff; padding: 6px; font-size: 12px; font-weight: bold">{$result['newsletter_time_short']}</td>
  </tr>
EOF;

		$CMS->newsletter->group_time = $result['newsletter_time_short'];
	}

  $btn_control = "";
  if($CMS->permit['newsletter_delete'])
  {
    $btn_control =<<<EOF
        <a class="edit" onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=newsletter&act=delete&id={$result['newsletter_id']}')"><i class="fa fa-trash"></i></a>
EOF;

  }

$output .= <<<EOF
  <tr bgcolor="{$result['bgcolor']}">
    <td>
      <div class="checkbox" style="margin: 0;">
        <input type="checkbox" class="checkitem" id="id_{$result['record_cnt']}" name="id_{$result['record_cnt']}" value="{$result['newsletter_id']}">
        <label for="id_{$result['record_cnt']}"></label>
      </div>
    </td>
    <td>#{$result['newsletter_id']}</td>
    <td>{$result['newsletter_email']}</td>
    <td>{$result['newsletter_time']}</td>

    <td align="center">{$btn_control}</td>
  </tr>
EOF;
	
    return $output;
}

//===========================================================================
//  NO DATA
//===========================================================================

public function none()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
  <tr>
    <td colspan="8">{$CMS->lang['newsletter_no_data']}</td>
  </tr>
EOF;

	return $output;
}

//===========================================================================
//  HTML FOOTER
//===========================================================================

public function footer()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
</table>
</div>
  <div class="fuction_table">

      <div class="pull-left">
        <p class="form-control-static ">
            {$CMS->newsletter->action_control}
        </p>
      </div>
      <nav class="pull-right">
         {$CMS->newsletter->show_page}
      </nav>
   </div>
<input type="hidden" name="data_cnt" value="{$CMS->newsletter->record_cnt}">
</section>

</div>
</form>
</section>
<script language="javascript">arrange_setup("{$CMS->newsletter->arrange_data}");</script>
<script>
  $(document).ready(function(){
      $("input[name='checkall']").click(function(){
          var check = $(this).attr("check");

          if(check == 0)
          {
            $(".checkitem").prop('checked', true);
            $(this).attr("check", 1);
          }else
          {
            $(".checkitem").prop('checked', false);
            $(this).attr("check", 0);
          }
      });
  });
</script>
EOF;

	return $output;
}

//===========================================================================
//  USER CONTROL
//===========================================================================

public function control()
{
	global $CMS, $DB, $member;
    
	$output = "";

$output .= <<<EOF
<select class="form-control" name="act" onchange="this.form.submit()">
    {$CMS->vars['action_controller']}
  </select>

<!--div class="block_action">
	<div class="block_action_check" onclick="javascript:form_checkall('newsletter');" id="checkall">
    	<input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
	</div>
	<div class="block_action_left">
        <select style="margin-left: 6px;;" class="input_text" name="act" style="width: 300px;" defaultvalue="delete_all" emsg="{$CMS->lang['newsletter_incomplete_action']}" ehide="1">{$CMS->vars['action_controller']}</select>
        &nbsp;
        <input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['newsletter_action_submit']} ">
    </div>
    <div class="block_action_right">
        <script type="text/javascript">permission_text("newsletter_search", '<input class="input_submit" type="button" name="submit" value="{$CMS->lang['search_form']}" onclick="javascript:window.location.href=\'{$CMS->vars['root_domain']}/?site=newsletter&act=search{$CMS->class->search->url_return}\'" />');</script>
    </div>
</div-->
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
<script type="text/javascript" src="{$CMS->vars['js_url']}/acp_customer.js"></script>
<script type="text/javascript" src="{$CMS->vars['js_url']}/acp_newsletter.js"></script>
<form method="post" id="newsletter" name="newsletter" action="{$CMS->vars['root_domain']}/?site=newsletter&id={$data['newsletter_id']}&act=edit_do&page={$CMS->input['page']}" onSubmit="return check_form(this.id);">
<input type="hidden" name="newsletter_header" value='{$data['newsletter_header']}' />
<div class="block_top"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=newsletter{$CMS->class->search->url_return}">&laquo; {$CMS->lang['newsletter_header_back']}</a></p>{$CMS->lang['newsletter_edit_form']} <b>{$data['newsletter_title']}</b></div>
<div class="block_middle_no_pad">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
    <td class="block_middle_left_top2" align="right" valign="top"><b>{$CMS->lang['newsletter_priority']}</b>:</td>
    <td class="block_middle_right_top2"><select class="input_text" name="newsletter_priority" defaultvalue="{$data['newsletter_priority']}">{$CMS->admin->html['priority']}</select></td>
  </tr>
  <tr>
    <td class="block_middle_left_top2" align="right" valign="top"><b>{$CMS->lang['newsletter_template']}</b>:</td>
    <td class="block_middle_right_top2"><select name="newsletter_template" class="input_text" onchange="javascript:load_newsletter_template(this);"><option value="">--------------------------------------------</option>{$CMS->newslettertpl->html_data}</select></td>
  </tr>
  <tr>
    <td class="block_middle_left_top2" align="right" valign="top"><b>{$CMS->lang['newsletter_from']}</b>:</td>
    <td class="block_middle_right_top2"><input class="input_text" size="30" type="text" name="newsletter_from" value="{$data['newsletter_from']}" etype="newsletter" emsg="{$CMS->lang['newsletter_incomplete_from']}"> &nbsp; &nbsp; &nbsp; &nbsp; <b>{$CMS->lang['newsletter_fromname']}</b>: <input class="input_text" size="20" type="text" name="newsletter_fromname" value="{$data['newsletter_fromname']}"></td>
  </tr>
  <tr>
    <td class="block_middle_left_top2" align="right" valign="top"><b>{$CMS->lang['newsletter_to']}</b>:</td>
    <td class="block_middle_right_top2"><div><input class="input_text" size="30" type="text" name="newsletter_to" value="{$data['newsletter_to']}" etype="newsletter" emsg="{$CMS->lang['newsletter_incomplete_to']}" onchange="javascript:customer_search(this,1);"/> &nbsp; &nbsp; &nbsp; &nbsp; <b>{$CMS->lang['newsletter_toname']}</b>: <input class="input_text" size="20" type="text" name="newsletter_toname" value="{$data['newsletter_toname']}"></div><div id="customer_list"></div></td>
  </tr>
  <tr>
    <td class="block_middle_left_top2" align="right" valign="top"><b>{$CMS->lang['newsletter_cc']}</b>:</td>
    <td class="block_middle_right_top2"><div><input class="input_text" size="50" type="text" name="newsletter_cc" value="{$data['newsletter_cc']}"></td>
  </tr>
  <tr>
    <td class="block_middle_left_top2" align="right" valign="top"><b>{$CMS->lang['newsletter_bcc']}</b>:</td>
    <td class="block_middle_right_top2"><div><input class="input_text" size="50" type="text" name="newsletter_bcc" value="{$data['newsletter_bcc']}"></td>
  </tr>
  <tr>
    <td class="block_middle_left_top2" align="right" valign="top"><b>{$CMS->lang['newsletter_title']}</b>:</td>
    <td class="block_middle_right_top2"><input class="input_text" size="80" type="text" name="newsletter_title" value="{$data['newsletter_title']}" emsg="{$CMS->lang['newsletter_incomplete_name']}"></td>
  </tr>
  <tr>
    <td class="block_middle_left_top2" align="right" valign="top"><b>{$CMS->lang['newsletter_content']}</b>:</td>
    <td class="block_middle_right_top2"><textarea class="input_textarea" name="newsletter_content" cols="60" rows="5" emsg="{$CMS->lang['newsletter_incomplete_content']}">{$data['newsletter_content']}</textarea></td>
  </tr>
  <tr>
    <td class="block_middle_left_top2" align="right" valign="top"><b>{$CMS->lang['newsletter_resend_now']}</b>:</td>
    <td class="block_middle_right_top2"><input type="radio" class="input_radio" name="newsletter_is_send" value="1"><font color="blue"><b>{$CMS->lang['yes']}</b></font> &nbsp; &nbsp; <input type="radio" name="newsletter_is_send" value="0" checked="checked"> <font color="red"><b>{$CMS->lang['no']}</b></font></td>
  </tr>
</table>
</div>
<div class="block_desc_bottom_submit">
	<input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['newsletter_edit_submit']} "> 
</div>
<div class="block_bottom"></div>
</form>
<script language="javascript">rebuild_form("newsletter",1);</script>
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
  $list_email = $CMS->newsletter->getListEmail();
  $option_email = "";
  foreach ($list_email as $value) {
    $option_email .= "<option value='{$value[newsletter_email]}'>{$value['newsletter_email']}</option>";
  }

  $data['email_content'] = $this->tpl1();
$output .= <<<EOF
  <script language="javascript">
  <!--
  lang_email_preview = "{$CMS->lang['email_preview']}";
  $(document).ready(function(){
    $(".list_email").select2({tags: true, placeholder: '{$CMS->lang['newsletter_incomplete_email']}'});
  });
  //-->
  </script>
  <script type="text/javascript" src="{$CMS->vars['js_url']}/acp_customer.js"></script>
  <script type="text/javascript" src="{$CMS->vars['js_url']}/acp_email.js"></script>
  <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=newsletter&act=send_mail" method="POST" enctype="multipart/form-data">
    <section class="add_form main_form">
      <figure class="heading">
        <h3 style="width:calc(100% - 21px);">{$CMS->lang['title_send_mail']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=newsletter{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>
      <div class="row">
        <div class="col-lg-8" style="float:none;margin:0px auto;" >
          <figure class="box-typical box-typical box-typical-padding border">
            <div class="row">

              <div class="col-lg-12">
                <div class="form-group">
                   <label class="form-label" >{$CMS->lang['newsletter_list']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="typeahead-query">
                    <div class="checkbox">
                      <input type="checkbox" id="send_all" name="send_all" value="1">
                      <label for="send_all">{$CMS->lang['send_all_customer']}</label>
                    </div>
                  </div>
                  <div class="typeahead-field box_list">
                    <select class="form-control ks-rounded list_email" name="email_list[]" value="{$data['email_list']}" multiple="multiple" data-validation="[NOTEMPTY]">
                      {$option_email}
                    </select>
                  </div>
                </div>
              </div>
              
              <div class="col-lg-12">
                <div class="form-group">
                  <div class="fl-flex-label">
                    <input class="form-control ks-rounded" type="text" name="email_title" value="{$data['email_title']}" placeholder="{$CMS->lang['newsletter_title']}" required data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['newsletter_incomplete_name']}">
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <fieldset class="form-group">
                  <label class="form-label" >{$CMS->lang['newsletter_content']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <p class="typeahead-field">
                    <span class="typeahead-query">
                      <textarea class="form-control editor_texarea" rows="11" name="email_content">{$data['email_content']}</textarea>
                    </span>
                  </p>
                </fieldset>
              </div>
            </div>
          </figure>
        </div><!-- end col -->
      </div><!-- end row -->
    </section>
EOF;
        
    $footer_details = array(
        'type' => 'add',
        'module' => 'newsletter',
      );
    $output .= $CMS->global->footer_details($footer_details);
    $output .= <<<EOF
    </form>
  
    <script>
    $(document).ready(function(){
      validate_form_custom("#form-signin_v1",".act_submit_save");
      $("input[name='send_all']").click(function(){
          $('.box_list').toggle();
      });
    });
    </script>
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
<div class="block_top"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=newsletter{$CMS->class->search->url_return}">&laquo; {$CMS->lang['newsletter_header_back']}</a></p>{$CMS->lang['newsletter_show_form']} <b>{$data['newsletter_title']}</b></div>
<div class="block_middle_no_pad">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
    <td class="block_middle_left_top" align="right" valign="top"><b>{$CMS->lang['user_id']}</b>:</td>
    <td class="block_middle_right_top">{$data['user_id']}</td>
  </tr>
  <tr>
    <td class="block_middle_left_top" align="right" valign="top"><b>{$CMS->lang['cus_id']}</b>:</td>
    <td class="block_middle_right_top">{$data['cus_id']}</td>
  </tr>
  <tr>
  	<td class="block_desc_top" colspan="2">{$CMS->lang['newsletter_header_info']}</td>
  </tr>
  <tr>
    <td class="block_middle_left_top" align="right" valign="top"><b>{$CMS->lang['newsletter_priority']}</b>:</td>
    <td class="block_middle_right_top">{$data['newsletter_priority']}</td>
  </tr>
  <tr>
    <td class="block_middle_left_top" align="right" valign="top"><b>{$CMS->lang['newsletter_fromname']}</b>:</td>
    <td class="block_middle_right_top"><b>{$data['newsletter_fromname']}</b> &lt;{$data['newsletter_from']}&gt;</td>
  </tr>
  <tr>
    <td class="block_middle_left_top" align="right" valign="top"><b>{$CMS->lang['newsletter_toname']}</b>:</td>
    <td class="block_middle_right_top"><b>{$data['newsletter_toname']}</b> &lt;{$data['newsletter_to']}&gt;</td>
  </tr>
  <tr>
    <td class="block_middle_left_top" align="right" valign="top"><b>{$CMS->lang['newsletter_cc']}</b>:</td>
    <td class="block_middle_right_top"><b>{$data['newsletter_cc']}</b></td>
  </tr>
  <tr>
    <td class="block_middle_left_top" align="right" valign="top"><b>{$CMS->lang['newsletter_bcc']}</b>:</td>
    <td class="block_middle_right_top"><b>{$data['newsletter_bcc']}</b></td>
  </tr>
  <tr>
  	<td class="block_desc_top" colspan="2">{$CMS->lang['newsletter_main_content']}</td>
  </tr>
  <tr>
    <td class="block_middle_left_top" align="right" valign="top"><b>{$CMS->lang['newsletter_title']}</b></td>
    <td class="block_middle_right_top">{$data['newsletter_title']}
	&nbsp; <script type="text/javascript">permission_btn("edit", "newsletter", "{$CMS->vars['root_domain']}/?site=newsletter&act=edit&id={$data['newsletter_id']}");</script>
    &nbsp; <script type="text/javascript">permission_btn("delete", "newsletter", "{$CMS->vars['root_domain']}/?site=newsletter&act=delete&id={$data['newsletter_id']}");</script></td>
  </tr>
  <tr>
    <td class="block_middle_left_top" align="right" valign="top"><b>{$CMS->lang['newsletter_content']}</b>:</td>
    <td class="block_middle_right_top">{$data['newsletter_content']}</td>
  </tr>
  <tr>
    <td class="block_middle_left_top" align="right" valign="top"><b>{$CMS->lang['newsletter_status']}</b>:</td>
    <td class="block_middle_right_top">{$data['newsletter_status']}</td>
  </tr>
  <tr>
    <td class="block_middle_left_top" align="right" valign="top"><b>{$CMS->lang['newsletter_time']}</b>:</td>
    <td class="block_middle_right_top">{$data['newsletter_time']}</td>
  </tr>
</table>
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
<script type="text/javascript" src="{$CMS->vars['js_url']}/acp_customer.js"></script>
<form method="post" id="newsletter" name="newsletter" action="{$CMS->vars['root_domain']}/?site=newsletter&act=search_do" onSubmit="return check_form(this.id);">
<div class="block_top"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=newsletter">&laquo; {$CMS->lang['newsletter_header_back']}</a></p>{$CMS->lang['search_form']}</div>
<div class="block_middle_no_pad">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
    <td class="block_middle_left_top" align="right" valign="top"><b>{$CMS->lang['newsletter_email']}</b>:</td>
    <td class="block_middle_right_top"><input class="input_text" size="30" type="text" name="newsletter_email" value="{$data['newsletter_email']}"></td>
  </tr>
  
</table>
</div>
<div class="block_desc_bottom_submit">
	<input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['search_submit']} "> 
</div>
<div class="block_bottom"></div>
</form>
<script language="javascript">rebuild_form("newsletter",1,1);</script>
EOF;
	
	return $output;	
}

//===========================================================================
//  PREVIEW
//===========================================================================

    public function tpl1()
    {
        $output = <<<EOF
        <html xmlns="http://www.w3.org/1999/xhtml"><head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title></title>
  <style type="text/css">
    
  #outlook a { padding: 0; }
  .ReadMsgBody { width: 100%; }
  .ExternalClass { width: 100%; }
  .ExternalClass * { line-height:100%; }
  body { margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
  table, td { border-collapse:collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
  img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
  p {
    display: block;
    margin: 13px 0;
  }

  </style>
  <!--[if !mso]><!-->
  <style type="text/css">
    @import url(https://fonts.googleapis.com/css?family=Ubuntu:400,500,700,300);
  </style>
  <style type="text/css">
    @media only screen and (max-width:480px) {
      @-ms-viewport { width:320px; }
      @viewport { width:320px; }
    }
  </style>
  <link href="https://fonts.googleapis.com/css?family=Ubuntu:400,500,700,300" rel="stylesheet" type="text/css">
  <!--<![endif]-->
<style type="text/css">
    @media only screen and (min-width:480px) {
    .mj-column-per-100, * [aria-labelledby="mj-column-per-100"] { width:100%!important; }
}</style></head>
<body id="YIELD_MJML" style="background: #eceff4;"><div class="mj-body" style="background-color:#eceff4;"><!--[if mso]>
      <table border="0" cellpadding="0" cellspacing="0" width="700" align="center" style="width:700px;"><tr><td>
      <![endif]--><div style="margin:0 auto;max-width:700px;"><table class="" cellpadding="0" cellspacing="0" style="width:100%;font-size:0px;" align="center"><tbody><tr><td style="text-align:center;vertical-align:top;font-size:0;padding:20px 0;padding-top:0px;padding-bottom:24px;"></td></tr></tbody></table></div><!--[if mso]>
      </td></tr></table>
      <![endif]-->
      <!--[if mso]>
      <table border="0" cellpadding="0" cellspacing="0" width="700" align="center" style="width:700px;"><tr><td>
      <![endif]--><div style="margin:0 auto;max-width:700px;background:#d8e2e7;"><table class="" cellpadding="0" cellspacing="0" style="width:100%;font-size:0px;background:#d8e2e7;" align="center"><tbody><tr><td style="text-align:center;vertical-align:top;font-size:0;padding:1px;"><!--[if mso]>
      <table border="0" cellpadding="0" cellspacing="0"><tr><td style="width:700px;">
      <![endif]--><div style="vertical-align:top;display:inline-block;font-size:13px;text-align:left;width:100%;" class="mj-column-per-100" aria-labelledby="mj-column-per-100"><table style="background:white;" width="100%"><tbody><tr><td style="font-size:0;padding:30px 30px 16px;" align="left"><div class="mj-content" style="cursor:auto;color:#000000;font-family:Proxima Nova, Arial, Arial, Helvetica, sans-serif;font-size:15px;line-height:22px;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum felis sem, sodales ut finibus vel, accumsan quis libero.</div></td></tr><tr><td style="font-size:0;padding:0 30px 6px;" align="left"><div class="mj-content" style="cursor:auto;color:#000000;font-family:Proxima Nova, Arial, Arial, Helvetica, sans-serif;font-size:15px;line-height:22px;">Aliquam id accumsan dui, in ornare sem. Cras accumsan nec diam quis tempor. Nam bibendum, purus et rutrum pulvinar, nisl nisi interdum urna, in convallis risus ligula maximus augue. Phasellus posuere, eros feugiat vehicula tempor, orci lorem malesuada nisi, at bibendum magna enim et sapien.</div></td></tr><tr><td style="font-size:0;padding:8px 16px 10px;padding-bottom:16px;padding-right:30px;padding-left:30px;" align="left"><table cellpadding="0" cellspacing="0" style="border:none;border-radius:25px;" align="left"><tbody><tr><td style="background:#00a8ff;border-radius:25px;color:white;cursor:auto;" align="center" valign="middle" bgcolor="#00a8ff"><a class="mj-content" href="#" style="display:inline-block;text-decoration:none;background:#00a8ff;border:1px solid #00a8ff;border-radius:25px;color:white;font-family:Proxima Nova, Arial, Arial, Helvetica, sans-serif;font-size:15px;font-weight:400;padding:8px 16px 10px;" target="_blank">Confirm E-Mail Adress</a></td></tr></tbody></table></td></tr><tr><td style="font-size:0;padding:0 30px 30px 30px;" align="left"><div class="mj-content" style="cursor:auto;color:#000000;font-family:Proxima Nova, Arial, Arial, Helvetica, sans-serif;font-size:15px;line-height:22px;">— Thanks you so much</div></td></tr></tbody></table></div><!--[if mso]>
      </td></tr></table>
      <![endif]--></td></tr></tbody></table></div><!--[if mso]>
      </td></tr></table>
      <![endif]-->
      <!--[if mso]>
      <table border="0" cellpadding="0" cellspacing="0" width="700" align="center" style="width:700px;"><tr><td>
      <![endif]--><div style="margin:0 auto;max-width:700px;"><table class="" cellpadding="0" cellspacing="0" style="width:100%;font-size:0px;" align="center"><tbody><tr><td style="text-align:center;vertical-align:top;font-size:0;padding:20px 0 0;"><!--[if mso]>
      <table border="0" cellpadding="0" cellspacing="0"><tr><td style="width:700px;">
      <![endif]--><div style="vertical-align:top;display:inline-block;font-size:13px;text-align:left;width:100%;" class="mj-column-per-100" aria-labelledby="mj-column-per-100"><table width="100%"><tbody><tr><td style="font-size:0;padding:0px;" align="center"><div class="mj-content" style="cursor:auto;color:#6b7a85;font-family:Proxima Nova, Arial, Arial, Helvetica, sans-serif;font-size:15px;line-height:22px;">© [website_name]</div></td></tr></tbody></table></div><!--[if mso]>
      </td></tr></table>
      <![endif]--></td></tr></tbody></table></div><!--[if mso]>
      </td></tr></table>
      <![endif]-->
      <!--[if mso]>
      <table border="0" cellpadding="0" cellspacing="0" width="700" align="center" style="width:700px;"><tr><td>
      <![endif]--><div style="margin:0 auto;max-width:700px;"><table class="" cellpadding="0" cellspacing="0" style="width:100%;font-size:0px;" align="center"><tbody><tr><td style="text-align:center;vertical-align:top;font-size:0;padding:20px 0;padding-top:0px;padding-bottom:24px;"></td></tr></tbody></table></div><!--[if mso]>
      </td></tr></table>
      <![endif]--></div>

</body></html>
EOF;

        return $output;

    }

}

?>
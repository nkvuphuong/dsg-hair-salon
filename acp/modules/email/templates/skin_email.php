<?php

class skin_email {

//===========================================================================
//  HTML HEADER
//===========================================================================

public function header()
{
	global $CMS, $DB, $member;
	
	$output = "";

  $output .= <<<EOF
  <section class="add_table main_form">
  <script type="text/javascript" src="{$CMS->vars['js_url']}/acp_email.js"></script>
  <script type="text/javascript" src="{$CMS->vars['js_url']}/acp_customer.js"></script>
  
    <figure class="heading">
      <h3>{$CMS->lang['email_header']}</h3>
      <figure class="pull-right right">
        <div class="search">
          <form method="post" id="frm_quickserch_product"  action="{$CMS->vars['root_domain']}/?site=email&act=search_do" style="display:inline-block">
            <input type="submit" class="fa-input" value="&#xf002;">
            <input name="email_title" id="email_title" type="text" value="{$CMS->input['email_title']}" autocomplete="off" minlength="2" maxlength="64" placeholder="{$CMS->lang['gsearch_quick']}" style="position: :relative;">
            <div id="suggesstion-box" class="box_result_find" style="display:none"></div>
          </form>
          <script language="javascript">rebuild_form("frm_quickserch_product",1);</script>
          <a id="expand_formsearch" title="">{$CMS->lang['gsearch_advance']}<i class="fa fa-angle-double-right"></i></a>
        </div>
        <a href="{$CMS->vars['root_domain']}/?site=email&act=add" title="" class="add_bill">{$CMS->lang['email_add_form']}</a>
      </figure>
      <section class="search_adv" >
          <form method="post" id="formsearch_adv" style="display:none"  action="{$CMS->vars['root_domain']}/?site=email&act=search_do" >
            <figure class="box-typical box-typical box-typical-padding border">
                <h5>{$CMS->lang['gsearch_advance']}</h5>
                <ul class="input_li row match-height">
                  <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                    <input type="text" name="email_from" id="email_from" value="{$email_from}" placeholder="{$CMS->lang['email_from']}" class="form-control" >
                  </li>
                  <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                    <input type="text" name="email_fromname" id="email_fromname" value="{$email_fromname}" placeholder="{$CMS->lang['email_fromname']}" class="form-control" >
                  </li>
                  <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                    <input type="text" name="email_to" id="email_to" value="{$email_to}" placeholder="{$CMS->lang['email_to']}" class="form-control" >
                  </li>
                  <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                    <input type="text" name="email_toname" id="email_toname" value="{$email_toname}" placeholder="{$CMS->lang['email_toname']}" class="form-control" >
                  </li>
                  <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                    <input type="text" name="cus_username" id="cus_username" value="{$cus_username}" placeholder="{$CMS->lang['cus_id']}" onchange="javascript:customer_search(this,0,1);" class="form-control" >
                    <div id="customer_list"></div>
                  </li>
                  <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                    <input type="text" name="email_toname" id="email_title" value="{$email_title}" placeholder="{$CMS->lang['user_id']}" class="form-control" >
                  </li>
                  <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                    <input type="text" name="email_content" id="email_content" value="{$email_content}" placeholder="{$CMS->lang['email_content']}" class="form-control" >
                  </li>
                  <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                    <select name="email_status" defaultvalue="{$data['email_status']}" class="form-control select2">
                      <option value="">{$CMS->lang['email_status']}</option>
                      <option value="0">{$CMS->lang['email_status_0']}</option>
                      <option value="1">{$CMS->lang['email_status_1']}</option>
                      <option value="2">{$CMS->lang['email_status_2']}</option>
                    </select>
                  </li>
                  <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                    <input type="submit" value="{$CMS->lang['comment_filter']}">
                  </li>
                </ul>
              </figure>
            </form>
            <script language="javascript">rebuild_form("formsearch_adv",1);</script>
        </section>
    </figure>
EOF;

	if ( $CMS->email->js_id )
	{
    $output .= <<<EOF
    <form method="post" name="{$CMS->email->js_id}" id="{$CMS->email->js_id}" style="display: none;" action="{$CMS->vars['root_domain']}/?site=email" onSubmit="return check_form(this.id);">
EOF;

	}
	else
	{
    $output .= <<<EOF
    <form method="post" name="email" id="email" action="{$CMS->vars['root_domain']}/?site=email" onSubmit="return check_form(this.id);">
EOF;

	}

  $output .= <<<EOF
  <section class="add_table">
    <div class="data_table">
      <div class="table table_cus table-responsive" style="border-top: none;">
        <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
          <thead>
            <tr role="row">
              <th data-orderable="false" data-sortable="false" style="margin:0px;padding:0px;"></th>
              <th data-orderable="false" data-sortable="false" class="table-check">
                <div class="checkbox checkbox-only" onclick="javascript:form_checkall('email');" id="checkall">
               <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
               <label for="id_{$result['record_cnt']}"></label>
                </div>
              </th>
              <th>{$CMS->lang['email_id']}</th>
              <th>{$CMS->lang['email_title']}</th>
              <th>{$CMS->lang['cus_id']}</th>
              <th>{$CMS->lang['user_id']}</th>
              <th style="text-align:center">{$CMS->lang['email_time']}</th>
              <th style="text-align:center">{$CMS->lang['email_resend_now']}</th>
              <th style="text-align:center">{$CMS->lang['email_status']}</th>
              <th data-orderable="false" data-sortable="false"></th>
            </tr>
          </thead>
          <tbody id="data_table" class="ui-sortable">
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
//   if ( $CMS->email->group_time != $result['email_time_short'] )
// 	{     
//     $output .= <<<EOF
//     <tr role="row" >
//       <td width="100%" colspan="10"  class="tt_block">{$result['email_time_short']}</td>
//     </tr>
// EOF;

// 		$CMS->email->group_time = $result['email_time_short'];
// 	}

  $style = $_SESSION['is_mobile'] == true ? "padding-right:27px;" : "";

  $output .= <<<EOF
  <tr role="row" keyrow="{$result['keyrow']}" class="row-grid {$result['old-even']}" rowtr="{$result['rowtr']}" bgcolor="{$result['bgcolor']}">
    <td class="grid-td" style="margin:0px;padding:0px;{$style}"></td>
    <td class="grid-td" >
      <div class="checkbox checkbox-only">
        <input type="checkbox"  name="id_{$result['record_cnt']}" id="id_{$result['record_cnt']}" value="{$result['email_id']}"/>
        <label for="id_{$result['record_cnt']}"></label>
      </div>
    </td>
    <td class="grid-td" ><span class="number">#{$result['email_id']}</span></td>
    <td class="grid-td mwr">{$result['email_title']}</td>
    <td class="grid-td">{$result['cus_id']}</td>
    <td class="grid-td">{$result['user_id']}</td>
    <td class="grid-td">{$result['email_time']}</td>
    <td class="grid-td">
EOF;
    
    if ( $CMS->permit['email_edit'] )
    {
      $output .= <<<EOF
      <a onclick="javascript:if ( email_confirm_resend('{$CMS->lang['email_is_resend']}') ) { document.location.href='{$CMS->vars['root_domain']}/?site=email&act=edit_do&id={$result['email_id']}&is_resend=1'; }" title="{$CMS->lang['email_resend_now']}"><i class="fa fa-envelope fa-lg" aria-hidden="true" style="color: #758c98;border: 1px solid #d8e2e7;"></i></a>
EOF;
    }

    $output .= <<<EOF
    </td>
    <td class="grid-td">{$result['email_status']}</td>
    <td class="grid-td">
EOF;
    
    if ( $CMS->permit['email_edit'] )
    {
      $output .= <<<EOF
      <a href="{$CMS->vars['root_domain']}/?site=email&act=edit&id={$result['email_id']}" class="edit"><i class="fa fa-edit" aria-hidden="true"></i></a>
EOF;
    }

    if ( $CMS->permit['email_delete'] )
    {
      $output .= <<<EOF
      <a onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=email&act=delete&id={$result['email_id']}')" class="edit"><i class="fa fa-trash-o"></i></a>
EOF;
    }

    $output .= <<<EOF
    </td>
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
    <td colspan="10"><h5 style="text-align: center;">{$CMS->lang['email_no_data']}</h5></td>
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
                </tbody>
              </table>
            </div>
          <div class="fuction_table">
            <div class="pull-left">
              <p class="form-control-static">{$CMS->email->action_control}</p>
            </div>
            <nav class="pull-right">
              <div class="block_bottom pagination pagination-sm">{$CMS->email->show_page}</div>
            </nav>
          </div>
        </div>
      </section>
      <input type="hidden" name="data_cnt" value="{$CMS->email->record_cnt}">
    </form>
  </section>
  <script language="javascript">rebuild_form("email");</script>
EOF;

  $output .= <<<EOF
  <script>
    $(function() {
      $('#example').DataTable({
        language: {
            emptyTable: 'Không tìm thấy dữ liệu!'
          },
      // order: [[ 2,"desc"]],
      // paging: false,
      // searching: false,
      // info: false,
EOF;
  
  if ( $_SESSION['is_mobile'] == true )
  {
    $output .= "responsive: { details: true},";
  }

  $output .= <<<EOF
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

  $form_name = $CMS->email->js_id ? $CMS->email->js_id : 'email';
  $output .= <<<EOF
  <div class="block_action">
    <select class="form-control select2" name="act" defaultvalue="" emsg="{$CMS->lang['incomplete_action']}" ehide="1">
      {$CMS->vars['action_controller']}
    </select>
  </div>
  <script>
    $("select[name='act']").change(function(){
      var action = $("select[name='act']").val();
      if ( action == 'delete_all' )
      {
        swal({
          title: confirm_alert_title,
          text: confirm_alert_delete,
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Ok",
          cancelButtonText: "Cancel",
          closeOnConfirm: true,
          closeOnCancel: true
         }).then(function () {
               $("#{$form_name}").submit();
                 $("select[name='act']").val('');                                 
       });

         
      }
    });
  </script>
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

  // <form method="post" id="email" name="email" action="{$CMS->vars['root_domain']}/?site=email&id={$data['email_id']}&act=edit_do&page={$CMS->input['page']}" onSubmit="return check_form(this.id);">
  //  required data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['email_incomplete_from']}" data-validation-regex="/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="{$CMS->lang['invalid_email']}"
  $output .= <<<EOF
  <script type="text/javascript" src="{$CMS->vars['js_url']}/acp_customer.js"></script>
  <script type="text/javascript" src="{$CMS->vars['js_url']}/acp_email.js"></script>
  <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=email&id={$data['email_id']}&act=edit_do&page={$CMS->input['page']}" method="POST" enctype="multipart/form-data">
    <section class="add_form main_form">
      <figure class="heading">
        <h3 style="width:calc(100% - 21px);">{$CMS->lang['email_edit_form']} #{$data['email_id']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=email{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>
      <div class="row">
        <div class="col-lg-8" style="float:none;margin:0px auto;" >
          <figure class="box-typical box-typical box-typical-padding border">
            <div class="row">
              <div class="col-lg-6">
                <fieldset class="form-group">
                  <label class="form-label" >{$CMS->lang['email_priority']}</label>
                  <p class="typeahead-field">
                    <span class="typeahead-query">
                      <select class="select2" name="email_priority" defaultvalue="{$data['email_priority']}">{$CMS->email->priority_html}</select>
                    </span>
                  </p>
                </fieldset>
              </div>
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="fl-flex-label">
                        <input class="form-control ks-rounded" type="text" name="email_from" value="{$data['email_from']}" placeholder="{$CMS->lang['email_from']}">
                        <small class="text-muted" style="font-size: 11px;">{$CMS->lang['title_use_email_default']} <b>{$CMS->vars['smtp_email_display']}</b></small>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="fl-flex-label">
                        <input class="form-control ks-rounded" type="text" name="email_fromname" value="{$data['email_fromname']}" placeholder="{$CMS->lang['email_fromname']}">
                        <small class="text-muted" style="font-size: 11px;">{$CMS->lang['title_use_email_from_name_default']} <b>{$CMS->vars['website_title']}</b></small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="fl-flex-label">
                        <input class="form-control ks-rounded" type="text" name="email_to" value="{$data['email_to']}" placeholder="{$CMS->lang['email_to']}" required data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['email_incomplete_from']}">
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="fl-flex-label">
                        <input class="form-control ks-rounded" type="text" name="email_toname" value="{$data['email_toname']}" placeholder="{$CMS->lang['email_toname']}">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="form-group">
                  <div class="fl-flex-label">
                    <input class="form-control ks-rounded" type="text" name="email_cc" value="{$data['email_cc']}" placeholder="{$CMS->lang['email_cc']} (Eg: you@domain.com, friend@domain.com, ...)">
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="form-group">
                  <div class="fl-flex-label">
                    <input class="form-control ks-rounded" type="text" name="email_bcc" value="{$data['email_bcc']}" placeholder="{$CMS->lang['email_bcc']} (Eg: you@domain.com, friend@domain.com, ...)">
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <fieldset class="form-group">
                  <label class="form-label" >{$CMS->lang['email_template']}</label>
                  <p class="typeahead-field">
                    <span class="typeahead-query">
                      <select class="select2" name="email_template" onchange="javascript:load_email_template(this);"><option value=""></option>{$CMS->emailtpl->html_data}</select>
                    </span>
                  </p>
                </fieldset>
              </div>
              <div class="col-lg-12">
                <div class="form-group">
                  <div class="fl-flex-label">
                    <input class="form-control ks-rounded" type="text" name="email_title" value="{$data['email_title']}" placeholder="{$CMS->lang['email_title']}" required data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['email_incomplete_name']}">
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <fieldset class="form-group">
                  <label class="form-label" >{$CMS->lang['email_content']}</label>
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
        'type' => 'edit',
        'module' => 'email',
        'detail_id' => $data['email_id'],
      );
    $output .= $CMS->global->footer_details($footer_details);
    $output .= <<<EOF
    </form>
    <script language="javascript">rebuild_form("form-signin_v1",1);</script>
    <script>
    $(document).ready(function(){
      validate_form_custom("#form-signin_v1",".act_submit_save");
    });
    </script>
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

  $data['email_from'] = $data['email_from']? $data['email_from'] : $CMS->vars['smtp_email_display'];
  $data['email_fromname'] = $data['email_fromname']? $data['email_fromname'] : $CMS->vars['website_title'];
  $output .= $CMS->class->editor->simple();

  // required data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['email_incomplete_from']}" data-validation-regex="/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="{$CMS->lang['invalid_email']}"
  $output .= <<<EOF
  <script language="javascript">
  <!--
  lang_email_preview = "{$CMS->lang['email_preview']}";
  //-->
  </script>
  <script type="text/javascript" src="{$CMS->vars['js_url']}/acp_customer.js"></script>
  <script type="text/javascript" src="{$CMS->vars['js_url']}/acp_email.js"></script>
  <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=email&act=add_do" method="POST" enctype="multipart/form-data">
    <section class="add_form main_form">
      <figure class="heading">
        <h3 style="width:calc(100% - 21px);">{$CMS->lang['email_add_form']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=email{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>
      <div class="row">
        <div class="col-lg-8" style="float:none;margin:0px auto;" >
          <figure class="box-typical box-typical box-typical-padding border">
            <div class="row">
              <div class="col-lg-6">
                <fieldset class="form-group">
                  <label class="form-label" >{$CMS->lang['email_priority']}</label>
                  <p class="typeahead-field">
                    <span class="typeahead-query">
                      <select class="select2" name="email_priority" defaultvalue="{$data['email_priority']}">{$CMS->email->priority_html}</select>
                    </span>
                  </p>
                </fieldset>
              </div>
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="fl-flex-label">
                        <input class="form-control ks-rounded" type="text" name="email_from" value="{$data['email_from']}" placeholder="{$CMS->lang['email_from']}">
                        <small class="text-muted" style="font-size: 11px;">{$CMS->lang['title_use_email_default']} <b>{$CMS->vars['smtp_email_display']}</b></small>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="fl-flex-label">
                        <input class="form-control ks-rounded" type="text" name="email_fromname" value="{$data['email_fromname']}" placeholder="{$CMS->lang['email_fromname']}">
                        <small class="text-muted" style="font-size: 11px;">{$CMS->lang['title_use_email_from_name_default']} <b>{$CMS->vars['website_title']}</b></small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="fl-flex-label">
                        <input class="form-control ks-rounded" type="text" name="email_to" value="{$data['email_to']}" placeholder="{$CMS->lang['email_to']}" required data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['email_incomplete_from']}">
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="fl-flex-label">
                        <input class="form-control ks-rounded" type="text" name="email_toname" value="{$data['email_toname']}" placeholder="{$CMS->lang['email_toname']}">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="form-group">
                  <div class="fl-flex-label">
                    <input class="form-control ks-rounded" type="text" name="email_cc" value="{$data['email_cc']}" placeholder="{$CMS->lang['email_cc']} (Eg: you@domain.com, friend@domain.com, ...)">
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="form-group">
                  <div class="fl-flex-label">
                    <input class="form-control ks-rounded" type="text" name="email_bcc" value="{$data['email_bcc']}" placeholder="{$CMS->lang['email_bcc']} (Eg: you@domain.com, friend@domain.com, ...)">
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <fieldset class="form-group">
                  <label class="form-label" >{$CMS->lang['email_template']}</label>
                  <p class="typeahead-field">
                    <span class="typeahead-query">
                      <select class="select2" name="email_template" onchange="javascript:load_email_template(this);"><option value=""></option>{$CMS->emailtpl->html_data}</select>
                    </span>
                  </p>
                </fieldset>
              </div>
              <div class="col-lg-12">
                <div class="form-group">
                  <div class="fl-flex-label">
                    <input class="form-control ks-rounded" type="text" name="email_title" value="{$data['email_title']}" placeholder="{$CMS->lang['email_title']}" required data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['email_incomplete_name']}">
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <fieldset class="form-group">
                  <label class="form-label" >{$CMS->lang['email_content']}</label>
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
        'module' => 'email',
      );
    $output .= $CMS->global->footer_details($footer_details);
    $output .= <<<EOF
    </form>
    <script language="javascript">
      rebuild_form("form-signin_v1",1);
      // load_email_template(document.getElementById("emailtpl_id"));
    </script>
    <script>
    $(document).ready(function(){
      validate_form_custom("#form-signin_v1",".act_submit_save");
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
    <section class="add_form main_form">
      <figure class="heading">
        <h3>{$CMS->lang['email_show_form']} #{$data['email_id']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=email{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>
      <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
          <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
            <h5 class="with-border m-t-0">{$CMS->lang['email_header_info']}</h5>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['email_fromname']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold"><b>{$data['email_fromname']}</b> &lt;{$data['email_from']}&gt;</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['email_toname']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold"><b>{$data['email_toname']}</b> &lt;{$data['email_to']}&gt;</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['email_cc']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['email_cc']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['email_bcc']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['email_bcc']}</div>  
              </div>
            </fieldset>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
            <h5 class="with-border m-t-0">{$CMS->lang['email_header_more']}</h5>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['email_priority']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['email_priority']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['user_id']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['user_id']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['cus_id']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['cus_id']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['email_status']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['email_status']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['email_time']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['email_time']}</div>  
              </div>
            </fieldset>
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <h5 class="with-border m-t-0">{$CMS->lang['email_main_content']}</h5>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['email_title']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">
                  {$data['email_title']}
                  <!--
                  <script type="text/javascript">permission_btn("edit", "email", "{$CMS->vars['root_domain']}/?site=email&act=edit&id={$data['email_id']}");</script>
                  <script type="text/javascript">permission_btn("delete", "email", "{$CMS->vars['root_domain']}/?site=email&act=delete&id={$data['email_id']}");</script>
                  -->
                </div>
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['email_content']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">
                  <div class="detail"><div class="content">{$data['email_content']}</div></div>
                </div>  
              </div>
            </fieldset>
          </div>
        </div>
      </figure>
    </section>
EOF;

    $footer_details = array(
        'type' => 'show',
        'module' => 'email',
        'detail_id' => $data['email_id'],
      );
    $output .= $CMS->global->footer_details($footer_details);

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

<header class="section-header">
        <div class="tbl">
          <div class="tbl-row">
            <div class="tbl-cell">
              <h3>Tìm kiếm Email</h3>        
            </div>
          </div>
        </div>
      </header>
      
<form method="post" id="email" name="email" action="{$CMS->vars['root_domain']}/?site=email&act=search_do" onSubmit="return check_form(this.id);">

     
 <section class="card">

      <section class="box-typical">
                    <header class="box-typical-header">
                                <div class="tbl-row">
                                    <div class="tbl-cell tbl-cell-title">
                                        <h3></h3>
                                    </div>
                                    <div class="tbl-cell tbl-cell-action-bordered">
                                        <a href="{$CMS->vars['root_domain']}/?site=mcourse{$CMS->class->search->url_return}">
                                        <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                                        </a>
                                    </div>
                                     
                                </div>
                            </header>
           </section>
        <div class="card-block">
          <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['email_priority']}</label>
              <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
               <select class='select2' name="email_priority" defaultvalue="{$data['email_priority']}"><option value="">{$CMS->lang['all']}</option><option value="1">{$CMS->lang['email_priority_1']}</option><option value="2">{$CMS->lang['email_priority_2']}</option><option value="3">{$CMS->lang['email_priority_3']}</option><option value="4">{$CMS->lang['email_priority_3']}</option><option value="5">{$CMS->lang['email_priority_5']}</option></select>
              </p>
              </div>
            </div>

           <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['email_from']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">      
              	<p class="form-control-static-input">
                   <input class='form-control' type="text" name="email_from" value="{$data['email_from']}" etype="date">
              	</p>
                </div>
          </div>    
          <div class="form-group row">  
              <label class="col-sm-3 form-control-label">{$CMS->lang['email_fromname']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                  <input class='form-control' type="text" name="email_fromname" value="{$data['email_fromname']}" etype="date">
              </p>
                </div>
            </div>


            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['email_to']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">      
              <p class="form-control-static-input">
                   <input class='form-control' type="text" name="email_to" value="{$data['email_to']}" etype="date">
              </p>
                </div>
            </div>
            
            <div class="form-group row">   
              <label class="col-sm-3 form-control-label">{$CMS->lang['email_toname']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                  <input class='form-control' type="text" name="email_toname" value="{$data['email_toname']}" etype="date">
              </p>
                </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['cus_id']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                  <input class='form-control' type="text" size="30" name="cus_username" value="{$data['cus_username']}" onchange="javascript:customer_search(this,0,1);" /><div id="customer_list"></div>
            </p>
              </div>
            </div>


            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['user_id']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                  <input class='form-control' type="text" name="email_title" value="{$data['email_title']}">
            </p>
              </div>
            </div>


            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['email_content']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                  <textarea class='form-control' type="text" name="email_content" value="{$data['email_content']}"></textarea>

            </p>
              </div>
            </div>


            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['email_status']}</label>
              <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                  <select class='form-control select2' name="email_status" defaultvalue="{$data['email_status']}">
                  <option value="">{$CMS->lang['all']}</option>
                  <option value="0">{$CMS->lang['email_status_0']}</option>
                  <option value="1">{$CMS->lang['email_status_1']}</option>
                  <option value="2">{$CMS->lang['email_status_2']}</option>
                </select>
            </p>
              </div>
            </div>


            <div class="form-group row">
              <label class="col-sm-3 form-control-label"></label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
                <p class="form-control-static-input">
                 <input class='btn btn-rounded' type="submit" name="submit" value=" {$CMS->lang['search_submit']} ">
                </p>
              </div>
            </div>

            

            </div><!-- end .card-block-->
   </section><!--card --> 
</form>
<script language="javascript">rebuild_form("email",1,1);</script>
EOF;
	
	return $output;	
}

//===========================================================================
//  PREVIEW
//===========================================================================

public function preview( $text )
{
	global $CMS, $DB;
    
 	$output = "";

$output .= <<<EOF
{$text}
EOF;

	return $output;
}

}

?>
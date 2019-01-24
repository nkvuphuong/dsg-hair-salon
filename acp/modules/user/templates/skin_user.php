<?php

class skin_user
{

//===========================================================================
//  HTML HEADER
//===========================================================================

    public function user_header()
    {
        global $CMS, $DB, $member;

        $output = "";

        $output .= <<<EOF
  <section class="add_table main_form">

    <figure class="heading">
      <h3>{$CMS->lang['user_manage']}</h3>
      <figure class="pull-right right">
        <a href="{$CMS->vars['root_domain']}/?site=user&act=add" title="" class="add_bill">{$CMS->lang['user_add']}</a>
        <a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
      </figure>
    </figure>
    <section class="tabs-section" style="margin-bottom: 15px">
            <div class="tabs-section-nav tabs-section-nav-inline">
                <ul class="nav" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="{$CMS->vars['root_domain']}/?site=user">
                            {$CMS->lang['menu_user']}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{$CMS->vars['root_domain']}/?site=user_group">
                            {$CMS->lang['menu_user_group']}
                        </a>
                    </li>
                </ul>
            </div><!--.tabs-section-nav-->
        </section>
    <form method="post" name="user" id="user" action="{$CMS->vars['root_domain']}/?site=user" onSubmit="return check_form(this.id);">
      <section class="add_table">
        <div class="data_table">
          <div class="table table_cus table-responsive" style="border-top: none;">
            <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
              <thead>
                <tr role="row">
                  <th data-orderable="false" data-sortable="false" style="margin:0px;padding:0px;"></th>
                  <th data-orderable="false" data-sortable="false" class="table-check">
                    <div class="checkbox checkbox-only" onclick="javascript:form_checkall('user');" id="checkall">
                     <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
                    
                    </div>
                  </th>
                  <th data-orderable="false" data-sortable="false">{$CMS->lang['user_name']}</th>
                  <th></th>
                  <th>{$CMS->lang['user_email']}</th>
                  <th>{$CMS->lang['user_group']}</th>
                  <th>{$CMS->lang['user_location']}</th>
                  <th style="text-align:center">{$CMS->lang['user_logs']}</th>
                  <th style="text-align:center">{$CMS->lang['user_lastvisit']}</th>
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

    public function user_middle($result)
    {
        global $CMS, $DB, $member;

        $output = "";

// 	// Group time          
//   if ( $CMS->user->group_time != $result['userg_title'] )
// 	{    
//     $output .= <<<EOF
//     <tr role="row">
// 	   <td width="100%" colspan="9"  class="tt_block">{$result['userg_title']}</td>
//     </tr>
// EOF;

// 		$CMS->user->group_time = $result['userg_title'];
// 	}

        $style = $_SESSION['is_mobile'] == true ? "padding-right:27px;" : "";

        $output .= <<<EOF
  <tr role="row" keyrow="{$result['keyrow']}" class="row-grid {$result['old-even']}" rowtr="{$result['rowtr']}" bgcolor="{$result['bgcolor']}">
    <td class="grid-td" style="margin:0px;padding:0px;{$style}"></td>
    <td class="grid-td" >
      <div class="checkbox checkbox-only">
        <input type="checkbox"  name="id_{$result['record_cnt']}" id="id_{$result['record_cnt']}" value="{$result['user_id']}"/>
        <label for="id_{$result['record_cnt']}"></label>
      </div>
    </td>
    <td class="grid-td" >{$result['user_is_leader']}</td>
    <td class="grid-td mwr">{$result['user_display_name']}</td>
    <td class="grid-td">{$result['user_email']}</td>
    <td class="grid-td">{$result['userg_prefix_html']}{$result['userg_title']}{$result['userg_suffix_html']}</td>
    <td class="grid-td">{$result['user_location']}</td>
    <td class="grid-td">
EOF;

        if ($CMS->permit['logs_read']) {
            $output .= <<<EOF
      <a href="{$CMS->vars['root_domain']}/?site=logs&act=search&content={$result['user_name']}&type=user_name&orderby=desc" title="{$CMS->lang['user_log']}"><i class="fa fa-search fa-lg" aria-hidden="true" style="color: #758c98;"></i></a>
EOF;
        }

        $output .= <<<EOF
    </td>
    <td class="grid-td">
EOF;

        if ($CMS->permit['logs_read']) {
            $output .= <<<EOF
      {$result['user_last_visit']}
EOF;
        }

        $output .= <<<EOF
    </td>
    <td class="grid-td" >
EOF;

        if ($CMS->permit['user_edit']) {
            $output .= <<<EOF
      <a href="{$CMS->vars['root_domain']}/?site=user&act=edit&id={$result['user_id']}" class="edit"><i class="fa fa-edit" aria-hidden="true"></i></a>
EOF;
        }

        if ($CMS->permit['user_delete']) {
            $output .= <<<EOF
      <a onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=user&act=delete&id={$result['user_id']}')" class="edit"><i class="fa fa-trash-o"></i></a>
EOF;
        }

        $output .= <<<EOF
    </td>
  </tr>
EOF;

        return $output;
    }

//===========================================================================
//  HTML HEADER
//===========================================================================

    public function user_header_probationary()
    {
        global $CMS, $DB, $member;

        $output = "";

        $output .= <<<EOF

<form method="post" name="user" id="user" action="{$CMS->vars['root_domain']}/?site=user" onSubmit="return check_form(this.id);">
<div class="block_wrapper">
<div class="block_top tab_bar_addon">

</div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="10">
  <tr>
    <th width="5%"></th>
    <th width="20%" colspan="2">{$CMS->lang['user_name']}</th>
    <th width="18%">{$CMS->lang['user_email']}</th>
    <th width="15%">{$CMS->lang['user_group']}</th>
    <th width="13%">{$CMS->lang['user_location']}</th>
	<th width="11%">{$CMS->lang['user_startjob']}</th>
    <th width="10%">{$CMS->lang['user_beginjob']}</th>
    <th width="12%">{$CMS->lang['user_probationary_in']}</th>
    <th width="1%">{$CMS->lang['edit']}</th>
    <th width="1%">{$CMS->lang['delete']}</th>
  </tr>
EOF;

        return $output;
    }

//===========================================================================
//  HTML MIDDLE
//===========================================================================

    public function user_middle_probationary($result)
    {
        global $CMS, $DB, $member;

        $output = "";

        $result['date'] = $result['date'] ? $CMS->class->date->date_format($result['date'], 0) : "";

        // Group time
        if ($CMS->user->group_time != $result['userg_title']) {

            $output .= <<<EOF
  <tr>
	<td width="100%" colspan="12"  class="tt_block">{$result['userg_title']}</td>
  </tr>
EOF;

            $CMS->user->group_time = $result['userg_title'];
        }

        $output .= <<<EOF
  <tr bgcolor="{$result['bgcolor']}">
    <td><script type="text/javascript">permission_text("user_{$result['user_delete']}", "<input type='checkbox' name='id_{$result['record_cnt']}' value='{$result['user_id']}'>");</script></td>
    <td align="right" width="1%">{$result['user_is_leader']}</td>
    <td>{$result['user_display_name']}</td>
    <td>{$result['user_email']}</td>
    <td>{$result['userg_prefix_html']}{$result['userg_title']}{$result['userg_suffix_html']}</td>
    <td>{$result['user_location']}</td>
	<td>{$result['user_startjob']}</td>
    <td>{$result['date']}</td>
    <td><span style="color:red;">{$result['day_end']} ngày</span></td>
    <td align="center"><script type="text/javascript">permission_btn("{$result['user_edit']}", "user", "{$CMS->vars['root_domain']}/?site=user&act=edit&id={$result['user_id']}");</script></td>
    <td align="center"><script type="text/javascript">permission_btn("{$result['user_delete']}", "user", "{$CMS->vars['root_domain']}/?site=user&act=delete&id={$result['user_id']}");</script></td>
  </tr>
EOF;

        return $output;
    }
//===========================================================================
//  NO DATA
//===========================================================================

    public function user_none()
    {
        global $CMS, $DB, $member;

        $output = "";

        $output .= <<<EOF
  <tr>
    <td colspan="11">{$CMS->lang['user_no_data']}</td>
  </tr>
EOF;

        return $output;
    }

//===========================================================================
//  HTML FOOTER
//===========================================================================

    public function user_footer()
    {
        global $CMS, $DB, $member;

        $output = "";

        $output .= <<<EOF
                </tbody>
              </table>
            </div>
          <div class="fuction_table">
            <div class="pull-left">
              <p class="form-control-static">
EOF;

//   if ( $CMS->vars['action_controller'] != "" )
//   {
//     $output .= "{$CMS->user->action_control}";
//   }

        $output .= <<<EOF
              </p>
            </div>
            <nav class="pull-right">
              <div class="block_bottom pagination pagination-sm">{$CMS->user->show_page}</div>
            </nav>
          </div>
        </div>
      </section>
      <input type="hidden" name="data_cnt" value="{$CMS->user->record_cnt}">
    </form>
    <div class="alert alert-info alert-fill alert-close alert-dismissible fade in" style="margin-top:15px">
      <ul>
        <li> <img src='{$CMS->vars['img_url']}/icon_user.png' align='absmiddle' />{$CMS->lang['icon_user']}</li>
        <li> <img src='{$CMS->vars['img_url']}/icon_leader.png' align='absmiddle' />{$CMS->lang['icon_leader']}</li>
        <li> <img src='{$CMS->vars['img_url']}/icon_locked.png' align='absmiddle' />{$CMS->lang['icon_locked']}</li>
      </ul>
    </div>
  </section>
  <script language="javascript">rebuild_form("user");</script>
EOF;

        $output .= <<<EOF
  <script>
    $(function() {
      $('#example').DataTable({
        language: {
            emptyTable: 'Không tìm thấy dữ liệu!'
          },
      order: [[ 3,"desc"]],
      paging: false,
      searching: false,
      info: false,
EOF;

        if ($_SESSION['is_mobile'] == true) {
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

    public function user_control()
    {
        global $CMS, $DB, $member;

        $output = "";

        $output .= <<<EOF
<div class="block_action">
	
</div>
EOF;

        return $output;
    }

//===========================================================================
//  HTML EDIT
//===========================================================================

    public function edit($data)
    {
        global $CMS, $DB, $member;

        $output = "";

        $data['user_signature'] = $CMS->class->editor->convert($data['user_signature']);

        $output .= <<<EOF
  <script language="javascript">
  <!--		
	var userg_id = "{$data['userg_id']}";
  var lang_change_group = "{$CMS->lang['is_change_group']}";
  var lang_changed_group = "{$CMS->lang['changed_group']}";
  //-->
  </script>
  <script type="text/javascript" src="{$CMS->vars['js_url']}/acp_user.js"></script>
EOF;

        // <form method="post" id="account" name="account" action="{$CMS->vars['root_domain']}/?site=user&act=edit_do&id={$data['user_id']}" onSubmit="return check_form(this.id)" enctype="multipart/form-data">

        $output .= <<<EOF
  <script type="text/javascript" src="{$CMS->vars['js_url']}/acp_user.js"></script>
  <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=user&act=edit_do&id={$data['user_id']}" method="POST" enctype="multipart/form-data">
    <section class="add_form main_form">
      <figure class="heading">
        <h3>{$CMS->lang['user_edit']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=user{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>
      <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
          <div class="col-lg-6">
            <div class="form-group">
              <div class="fl-flex-label">
                <input class="form-control ks-rounded" id="user_email" placeholder="{$CMS->lang['user_email']}" required type="text" name="user_email" autocomplete="off" id="user_email" value="{$data['user_email']}" emsg="{$CMS->lang['incomplete_email']}" etype="email" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['incomplete_email']}" data-validation-regex="/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="{$CMS->lang['invalid_email']}">
                <font id="sympol_user_email" color="red" style="display:none;">(*)</font>
              </div>
            </div>
            <div class="form-group">
              <div class="fl-flex-label">
                <input class="form-control ks-rounded" id="user_password" placeholder="{$CMS->lang['user_password']}" type="password" name="user_password" autocomplete="off" value="" id="user_password" maxlength="32" onkeyup="password_change(this);">
              </div>
              <span style="position: absolute; margin-left: 20px; display: none;" id="password_checker">
                <span style="font-size: 9px; line-height: 110%; color: gray;">
                  {$CMS->lang['password_strength']}<br />
                  <font  id="pwd1"><span class="password_1">&nbsp;</span></font>
                  <font  id="pwd2"><span class="password_1">&nbsp;</span></font>
                  <font  id="pwd3"><span class="password_1">&nbsp;</span></font>
                  <font  id="pwd4"><span class="password_1">&nbsp;</span></font>
                  <font  id="pwd5"><span class="password_1">&nbsp;</span></font>
                </span>
              </span>
            </div>
            <div class="form-group">
              <div class="fl-flex-label">
                <input class="form-control ks-rounded" id="user_repassword" placeholder="{$CMS->lang['user_repassword']}" type="password" name="user_repassword" value="" id="user_repassword" maxlength="32">
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group">
              <div class="fl-flex-label">
                <input class="form-control ks-rounded" id="user_name" placeholder="{$CMS->lang['user_name']}" required type="text" name="user_name" value="{$data['user_name']}" emsg="{$CMS->lang['incomplete_username']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['incomplete_username']}">
                <font id="sympol_user_name" color="red" style="display:none;">(*)</font>
              </div>
            </div>
            <div class="form-group">
              <div class="fl-flex-label">
                <input class="form-control ks-rounded" id="user_display_name" placeholder="{$CMS->lang['user_display_name']}" type="text" name="user_display_name" id="user_display_name" value="{$data['user_display_name']}">
              </div>
            </div>
            <div class="form-group">
              <div class="fl-flex-label">
                <div class="row">
                    <div class="col-md-8"><input class="form-control ks-rounded" id="user_avatar" placeholder="{$CMS->lang['user_avatar']}" type="file" name="user_avatar"></div>
                    <div class="col-md-4">{$data['user_avatar']}</div>
                </div>
              </div>
              <script>
                $(document).ready(function() {
                  $(window).on('load', function(){
                    $('label[class="fl-label"][for="user_avatar"]').css({'font-size' : '11px', 'padding-top' : '0px'});
                  });
                });
              </script>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-6">
            <fieldset class="form-group">
              <label class="form-label" >{$CMS->lang['user_group']}</label>
              <p class="typeahead-field">
                <span class="typeahead-query">
                  <select class="select2" name="userg_id" id="userg_id" defaultvalue="{$data['userg_id']}" onchange="group_change();">
EOF;

        // $sql = $CMS->vars['is_root'] ? "SELECT * FROM ".root_table."user_group WHERE userg_deleted=0 ORDER BY userg_title ASC" : "SELECT * FROM ".root_table."user_group WHERE (userg_is_root='{$user['userg_is_root']}' AND userg_is_admin='{$user['userg_is_admin']}') AND userg_deleted=0 ORDER BY userg_title ASC";
        $sql = "SELECT * FROM " . root_table . "user_group WHERE userg_deleted=0 ORDER BY userg_title ASC";
        $sql = $DB->query($sql);
        while ($result = $DB->fetch_array($sql)) {
            $selected = $data['userg_id'] == $result['userg_id'] ? "selected" : "";
            $output .= <<<EOF
      <option value="{$result['userg_id']}" {$selected}>{$result['userg_title']}</option>
EOF;
        }

        $output .= <<<EOF
                  </select>
                </span>
              </p>
              <font style="display: none;"><input type="checkbox" name="is_update" id="is_update" value="1" /></font>
            </fieldset>
          </div>
          {$this->workplace($data)}
          <div class="col-lg-3">
            <fieldset class="form-group">
              <label class="form-label" >{$CMS->lang['user_status']}</label>
              <p class="typeahead-field">
                <span class="typeahead-query">
                  <select class="form-control" name="user_status" defaultvalue="{$data['user_status']}">
                    <option value="0">{$CMS->lang['suspended']}</option>
                    <option value="1">{$CMS->lang['active']}</option>
                  </select>
                </span>
              </p>
            </fieldset>
          </div>
          <div class="col-lg-3">
            <fieldset class="form-group">
              <label class="form-label" >{$CMS->lang['user_is_staff']}</label>
              <p class="typeahead-field">
                <span class="typeahead-query">
                  <select class="form-control" name="user_is_staff" defaultvalue="{$data['user_is_staff']}">
                    <option value="0">{$CMS->lang['no']}</option>
                    <option value="1">{$CMS->lang['yes']}</option>
                  </select>
                </span>
              </p>
            </fieldset>
          </div>
          <div class="col-lg-12">
EOF;

        if ($CMS->vars['is_admin'] == true) {
            $commission_param = $CMS->user->getCommission(!empty($_POST) ? $_POST : $data['user_id'], $data['userg_id']);

            $output .= <<<EOF
    <section class="tabs-section">
        <div class="tabs-section-nav tabs-section-nav-inline">
            <ul class="nav" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="#tab_permission" role="tab" data-toggle="tab" aria-expanded="true">
                        Permission
                    </a>
                </li>
EOF;

                if($CMS->vars['enabled_commission'])
            {
                $output .= <<<EOF
                <li class="nav-item">
                    <a class="nav-link" href="#tab_commission" role="tab" data-toggle="tab" aria-expanded="false">
                        Commission
                    </a>
                </li>
EOF;
        }

        $output .= <<<EOF
            </ul>
        </div><!--.tabs-section-nav-->

        <div class="tab-content">
            
            <!-- CONFIG PERMISSION -->
            <div role="tabpanel" class="tab-pane fade in active show" id="tab_permission" aria-expanded="true">
                <fieldset class="form-group">
                    <label class="form-label" >
                        <a class="btn btn-success" onclick="setAllPermission();">{$CMS->lang['full_permission']}</a>
                        <a class="btn btn-success" onclick="unsetAllPermission();">{$CMS->lang['clear_permission']}</a>
                        <a class="btn btn-success" onclick="revertPermission();">{$CMS->lang['revert_permission']}</a>
                    </label>
                  <p class="typeahead-field">
                    <span class="typeahead-query">
                      <figure class="table-responsive"  style="margin-top:-5px">
                        <table cellspacing="0" cellpadding="0">
                          <tr>
                            <td id="update_permission">
                                {$CMS->user->show_permission($data['user_permission'])}
                            </td>
                          </tr>
                        </table>
                      </span>
                  </p>
                </fieldset>
            </div><!--.tab-pane-->
            <!-- END - CONFIG PERMISSION -->
            
            <!-- CONFIG COMMISSION -->
            <div role="tabpanel" class="tab-pane fade" id="tab_commission" aria-expanded="false">
                {$CMS->global->show_commission($commission_param,$_POST)}
            </div><!--.tab-pane-->
            <!-- END - CONFIG COMMISSION -->
        </div><!--.tab-content-->
    </section>
EOF;
        }

        $output .= <<<EOF
          </div>
        </div>
      </figure>
    </section>
EOF;
        $footer_details = array(
            'type' => 'edit',
            'module' => 'user',
            'detail_id' => $data['user_id'],
        );
        $output .= $CMS->global->footer_details($footer_details);
        $output .= <<<EOF
    </form>
    <script language="javascript">
      // rebuild_form("account");
      rebuild_form("form-signin_v1");
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
//  HTML ADD
//===========================================================================

    public function add($data)
    {
        global $CMS, $DB, $member;

        $output = "";

        // <form method="post" id="account" name="account" action="{$CMS->vars['root_domain']}/?site=user&act=add_do" onSubmit="return check_form(this.id)" enctype="multipart/form-data">

        $output .= <<<EOF
  <script type="text/javascript" src="{$CMS->vars['js_url']}/acp_user.js"></script>
  <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=user&act=add_do" method="POST" enctype="multipart/form-data">
    <section class="add_form main_form">
      <figure class="heading">
        <h3>{$CMS->lang['user_add']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=user{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>
      <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
          <div class="col-lg-6">
            <div class="form-group">
              <div class="fl-flex-label">
                <input class="form-control ks-rounded" id="user_email" placeholder="{$CMS->lang['user_email']}" required type="text" name="user_email" autocomplete="off" id="user_email" value="{$CMS->input['user_email']}" emsg="{$CMS->lang['incomplete_email']}" etype="email" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['incomplete_email']}" data-validation-regex="/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="{$CMS->lang['invalid_email']}">
              </div>
            </div>
            <div class="form-group">
              <div class="fl-flex-label">
                <input class="form-control ks-rounded" id="user_password" placeholder="{$CMS->lang['user_password']}" required type="password" name="user_password" autocomplete="off" value="{$CMS->input['user_password']}" id="user_password" maxlength="32" emsg="{$CMS->lang['incomplete_password']}" onkeyup="password_change(this);" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['incomplete_password']}">
              </div>
            </div>
            <div class="form-group">
              <div class="fl-flex-label">
                <input class="form-control ks-rounded" id="user_repassword" placeholder="{$CMS->lang['user_repassword']}" required type="password" name="user_repassword" value="{$CMS->input['user_repassword']}" id="user_repassword" maxlength="32" emsg="{$CMS->lang['incomplete_repassword']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['incomplete_repassword']}">
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group">
              <div class="fl-flex-label">
                <input class="form-control ks-rounded" id="user_name" placeholder="{$CMS->lang['user_name']}" required type="text" name="user_name" value="{$CMS->input['user_name']}" emsg="{$CMS->lang['incomplete_username']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['incomplete_username']}">
              </div>
            </div>
            <div class="form-group">
              <div class="fl-flex-label">
                <input class="form-control ks-rounded" id="user_display_name" placeholder="{$CMS->lang['user_display_name']}" type="text" name="user_display_name" id="user_display_name" value="{$CMS->input['user_display_name']}">
              </div>
            </div>
            <div class="form-group">
              <div class="fl-flex-label">
                <input class="form-control ks-rounded" id="user_avatar" placeholder="{$CMS->lang['user_avatar']}" type="file" name="user_avatar">
              </div>
              <script>
                $(document).ready(function() {
                  $(window).on('load', function(){
                    $('label[class="fl-label"][for="user_avatar"]').css({'font-size' : '11px', 'padding-top' : '0px'});
                  });
                });
              </script>
            </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <fieldset class="form-group">
            <label class="form-label" >{$CMS->lang['user_group']}</label>
            <p class="typeahead-field">
              <span class="typeahead-query">
                <select class="select2" name="userg_id" id="userg_id"  onchange="group_change_add(); approveCommissionByGroup();">
EOF;

        $sql = $CMS->vars['is_root'] ? "SELECT * FROM " . root_table . "user_group WHERE userg_deleted=0 ORDER BY userg_title ASC" : "SELECT * FROM " . root_table . "user_group WHERE (userg_is_root='{$user['userg_is_root']}' AND userg_is_admin='{$user['userg_is_admin']}') AND userg_deleted=0 ORDER BY userg_title ASC";
        $sql = $DB->query($sql);
        while ($result = $DB->fetch_array($sql)) {
            $selected = $CMS->input['userg_id'] == $result['userg_id'] ? "selected" : "";
            $output .= <<<EOF
      <option value="{$result['userg_id']}" {$selected}>{$result['userg_title']}</option>
EOF;
        }

        $output .= <<<EOF
                </select>
              </span>
            </p>
          </fieldset>
        </div>
        {$this->workplace($data)}
        <div class="col-lg-3">
          <fieldset class="form-group" style="display:none;">
            <label class="form-label" >{$CMS->lang['user_status']}</label>
            <p class="typeahead-field">
              <span class="typeahead-query">
                <select class="select2" name="user_status" defaultvalue="1">
                  <option value="0">{$CMS->lang['suspended']}</option>
                  <option value="1" selected>{$CMS->lang['active']}</option>
                </select>
              </span>
            </p>
          </fieldset>
        </div>

        <div class="col-lg-3">
          <fieldset class="form-group">
            <label class="form-label" >{$CMS->lang['user_is_staff']}</label>
            <p class="typeahead-field">
              <span class="typeahead-query">
                <select class="form-control" name="user_is_staff" defaultvalue="1">
                  <option value="0">{$CMS->lang['no']}</option>
                  <option value="1">{$CMS->lang['yes']}</option>
                </select>
              </span>
            </p>
          </fieldset>
        </div>
        <div class="col-lg-12">
EOF;

        if ($CMS->vars['is_admin'] == true) {
            $output .= <<<EOF
    <section class="tabs-section">
        <div class="tabs-section-nav tabs-section-nav-inline">
            <ul class="nav" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="#tab_permission" role="tab" data-toggle="tab" aria-expanded="true">
                        Permission
                    </a>
                </li>
EOF;

            if($CMS->vars['enabled_commission'])
            {
                $output .= <<<EOF
                <li class="nav-item">
                    <a class="nav-link" href="#tab_commission" role="tab" data-toggle="tab" aria-expanded="false">
                        Commission
                    </a>
                </li>
EOF;
            }


            $output .= <<<EOF
            </ul>
        </div><!--.tabs-section-nav-->

        <div class="tab-content">
            
            <!-- CONFIG PERMISSION -->
            <div role="tabpanel" class="tab-pane fade in active show" id="tab_permission" aria-expanded="true">
                <fieldset class="form-group">
                    <label class="form-label" >
                      <a class="btn btn-success" onclick="setAllPermission();">{$CMS->lang['full_permission']}</a>
                                    <a class="btn btn-success" onclick="unsetAllPermission();">{$CMS->lang['clear_permission']}</a>
                                    <a class="btn btn-success" onclick="revertPermission();">{$CMS->lang['revert_permission']}</a>
                    </label>
                  <p class="typeahead-field">
                    <span class="typeahead-query">
                      <figure class="table-responsive"  style="margin-top:-5px">
                        <table cellspacing="0" cellpadding="0">
                          <tr>
                            <td id="update_permission">
                                {$CMS->user->show_permission($data['user_permission'])}
                            </td>
                          </tr>
                        </table>
                      </span>
                  </p>
                </fieldset>
            </div>
            <!-- END - CONFIG PERMISSION -->
            
            <!-- CONFIG COMMISSION -->
            <div role="tabpanel" class="tab-pane fade in" id="tab_commission" aria-expanded="true">
                {$CMS->global->show_commission($CMS->user->getCommission($_POST),$_POST)}
            </div>
            <!-- END - CONFIG COMMISSION -->
        </div>
EOF;
        }

        $output .= <<<EOF
          </div>
        </div>
      </figure>
    </section>
EOF;
        $footer_details = array(
            'type' => 'add',
            'module' => 'user',
        );
        $output .= $CMS->global->footer_details($footer_details);
        $output .= <<<EOF
    </form>
    <script language="javascript">
      // rebuild_form("account");
      // rebuild_form("form-signin_v1");
    </script>
    <script>
    $(document).ready(function(){
        $("[name=userg_id]").trigger('change');
        validate_form_custom("#form-signin_v1",".act_submit_save");
    });
    </script>
EOF;

        return $output;
    }

//===========================================================================
//  HTML SHOW
//===========================================================================

    public function show($data)
    {
        global $CMS, $DB, $member;

        $output = "";

        $output .= <<<EOF
    <section class="add_form main_form">
      <figure class="heading">
        <h3>{$CMS->lang['user_show']} #{$data['user_id']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=user{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>
      <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
          <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['user_name']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">
                {$data['user_is_leader']}&nbsp;{$data['user_name']}
                <!--
                <script type="text/javascript">permission_btn("edit", "user", "{$CMS->vars['root_domain']}/?site=user&act=edit&id={$data['user_id']}", "{$data['userg_is_root']}", "{$data['userg_is_admin']}", "{$data['user_id']}", "{$data['userg_id']}");</script>
                <script type="text/javascript">permission_btn("delete", "user", "{$CMS->vars['root_domain']}/?site=user&act=delete&id={$data['user_id']}&page={$CMS->input[page]}", "{$data['userg_is_root']}", "{$data['userg_is_admin']}", "{$data['user_id']}", "{$data['userg_id']}");</script>
                -->
                </div>
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['user_display_name']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['user_display_name']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['user_email']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['user_email']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['user_group']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['userg_title']}</div>  
              </div>
            </fieldset>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
            <fieldset class="form-group row">
              <label class="form-control-label2">{$CMS->lang['user_avatarcard']}</label>
              <div class="form-control-span2"> 
                <div class="form-label semibold">{$data['user_avatar']}</div>  
              </div>
            </fieldset>
          </div>
        </div>
      </figure>
    </section>
EOF;

        $footer_details = array(
            'type' => 'show',
            'module' => 'user',
            'detail_id' => $data['user_id'],
            'act_deleted' => 'delete',
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
<form method="post" id="user" name="user" action="{$CMS->vars['root_domain']}/?site=user&act=search_do" onSubmit="return check_form(this.id);">
<div class="block_wrapper">
<div class="block_top tab_bar_addon"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=user{$CMS->class->search->url_return}">&laquo; {$CMS->lang['user_header_back']}</a></p>{$CMS->lang['search_form']}</div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
    <td class="left25"><b>{$CMS->lang['user_name']}</b></td>
    <td><input class="input_text" size="30" type="text" name="user_name" value="{$CMS->input['user_name']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['user_display_name']}</b></td>
    <td><input class="input_text" size="30" type="text" name="user_display_name" id="user_display_name"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['user_email']}</b></td>
    <td><input class="input_text" size="30" type="text" name="user_email" autocomplete="off" id="user_email" value="{$CMS->input['user_email']}" etype="email"></td>
  </tr>
</table>
</div>
</div>
<div class="block_desc_bottom_submit">
	<input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['search_submit']} "> 
</div>
<div class="block_bottom pagination pagination-sm"></div>
</form>
<script language="javascript">rebuild_form("user",1,1);</script>
EOF;

        return $output;
    }

//===========================================================================
//  PRINT REVIEW
//===========================================================================

    public function print_review($data)
    {
        global $CMS, $DB, $member;

        $output = "";

        $output .= <<<EOF
<form method="post" id="user" name="user" action="{$CMS->vars['root_domain']}/?site=user&act=print_do&id={$data['user_id']}" onSubmit="return check_form(this.id);">
<div class="block_wrapper">
<div class="block_top tab_bar_addon"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}">&laquo; {$data['user_display_name']}</a></p>
{$CMS->lang['user_print_card']}</div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
  <td class="left25"><b>{$CMS->lang['user_display_name']}</b></td>
    <td>{$data['user_display_name']}<br />
    <img src="{$CMS->vars['root_domain']}/?site=user&act=print&id={$data['user_id']}&type=front" width="508" height="319" /></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['user_email']}</b></td>
    <td>{$data['user_email']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['user_card_time']}</b></td>
    <td>{$data['user_card_time']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['user_reset_card_code']}</b></td>
    <td><input type="checkbox" name="is_reset" value="1"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['user_card_reason']}</b></td>
    <td><input class="input_text" size="80" type="text" name="user_card_reason" value="{$data['user_card_reason']}" emsg="{$CMS->lang['user_card_reason_incomplete']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['user_card_position']}</b></td>
    <td><select class="input_text" name="user_card_position">
    <option value="0">{$CMS->lang['user_card_position_0']}</option>
    <option value="1">{$CMS->lang['user_card_position_1']}</option>
    <option value="2">{$CMS->lang['user_card_position_2']}</option>
    </select></td>
  </tr>
</table>
</div>
</div>
<div class="block_desc_bottom_submit">
	<input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['user_card_submit']} "> 
</div>
<div class="block_bottom pagination pagination-sm"></div>
</form>
<script language="javascript">rebuild_form("user",1,0);</script>
EOF;

        return $output;
    }

//===========================================================================
//  PRINT RESULT
//===========================================================================

    public function print_result($data)
    {
        global $CMS, $DB, $member;

        $output = "";

        $output .= <<<EOF
<script language="javascript">
<!--
	function update_cardfront()
    {
    	document.getElementById("card_print").innerHTML = document.getElementById("card_printfront").innerHTML;
    }
    function update_cardback()
    {
    	document.getElementById("card_print").innerHTML = document.getElementById("card_printback").innerHTML;
    }
//-->
</script>
<style>
@media print
{
body * { visibility: hidden; margin:0; padding: 0; width: 0px; height: 0px; }
#card_print * { visibility: visible; }
#card_print { position: absolute; top: 0px; left: 0px; }
#card_print img { width: 86mm; height: 54mm; }
#card_printfront img { width: 86mm; height: 54mm; }
#card_printback img { width: 86mm; height: 54mm; }
.footer { display: none; }
}
@media screen
{
#card_printfront { display: none; width: 86mm; height: 54mm; }
#card_printback { display: none; width: 86mm; height: 54mm; }
#card_print { display: none; width: 86mm; height: 54mm; }
}
</style>
<div id="card_printfront"><img src="{$CMS->vars['root_domain']}/?site=user&act=print&id={$data['user_id']}&type=front&position={$CMS->input['user_card_position']}" /></div>
<div id="card_printback"><img src="{$CMS->vars['root_domain']}/?site=user&act=print&id={$data['user_id']}&type=back&position={$CMS->input['user_card_position']}" /></div>
<div id="card_print"><img src="{$CMS->vars['root_domain']}/?site=user&act=print&id={$data['user_id']}&type=back&position={$CMS->input['user_card_position']}" /></div>

<div class="block_wrapper">
<div class="block_top tab_bar_addon"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}">&laquo; {$data['user_email']}</a></p>
{$CMS->lang['user_print_card']}</div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
  <td class="left25"><b>{$CMS->lang['user_display_name']}</b></td>
    <td>{$data['user_display_name']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['user_email']}</b></td>
    <td>{$data['user_email']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['user_card_reason']}</b></td>
    <td>{$data['user_card_reason']}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['user_card_position']}</b></td>
    <td>{$CMS->lang["user_card_position_{$CMS->input['user_card_position']}"]}</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['user_print_card']}</b></td>
    <td><a class="input_submit2" href="javascript:update_cardfront(); window.print();">{$CMS->lang['user_card_printfront']}</a>
    &nbsp; &nbsp; <a class="input_submit2" href="javascript:update_cardback(); window.print();">{$CMS->lang['user_card_printback']}</a>
    </td>
  </tr>
</table>
</div>
</div>
<div class="block_bottom pagination pagination-sm"></div>
EOF;

        return $output;
    }

    function workplace($data)
    {
        global $CMS;

        $output = <<<EOF
    <div class="col-lg-6">
        <div class="row">
            <div class="col-lg-6">
                <fieldset class="form-group">
                    <label class="form-label">Tỉnh/thành</label>
                        <select onchange="filterStoreByCity(+$(this).val())" class="form-control" id="workplace_city" name="workplace_city" defaultvalue="0">
                            <option>---</option>
                        </select>
                </fieldset>
            </div>
            <div class="col-lg-6">
                <fieldset class="form-group">
                    <label class="form-label" >Cửa hàng</label>
                        <select class="form-control"  id="workplace_store" name="store_id" defaultvalue="0">
                            <option>---</option>
                        </select>
                    </fieldset>
                </fieldset>
            </div>
        </div>
    </div>
EOF;

        \core\ezy::load_model("city");
        \core\ezy::load_model("store");
        $cities = \models\city::getCitiesHaveStores();
        $stores = \models\store::getStores();

        $citiesJson = \lib\input::jsonEncode($cities, 0);
        $storesJson = \lib\input::jsonEncode($stores, 0);

        $CMS->input['store_id'] = isset($data['store_id']) ? $data['store_id'] : $CMS->input['store_id'];
        if($CMS->input['store_id']) {
            $store = \models\store::getStore($CMS->input['store_id']);
            $CMS->input['workplace_city'] = $store['city_id'];
        }

        $output .= <<<EOF
        <script>
            let storeData = {
                cities: {$citiesJson},
                stores: {$storesJson}
            }

            if(storeData.cities) {
                storeData.cities.forEach(city => {
                    $("#workplace_city").append("<option value='" + city.city_id + "'>" + city.city_name + "</option>");
                })
            }
           
            filterStoreByCity('{$CMS->input['workplace_city']}');
            
            $("#workplace_city option").prop("selected", false);
            $("#workplace_city option[value='{$CMS->input['workplace_city']}']").prop("selected", true);
            
            $("[name=store_id] option").prop("selected", false);
            $("[name=store_id] option[value='{$CMS->input['store_id']}']").prop("selected", true);
            
            function filterStoreByCity(city_id) {
                city_id = +city_id;
                $("[name=store_id] option").not(":first").remove();
                let stores = storeData.stores.filter(x => x.city_id == city_id);
                if(stores) {
                    stores.forEach(store => {
                        $("[name=store_id]").append("<option value='" + store.store_id + "'>" + store.store_name + "</option>");
                    })
                }
            }
        </script>
EOF;


        return $output;

    }
}

?>
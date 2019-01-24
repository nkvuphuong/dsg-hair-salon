<?php

class skin_emailtpl
{

//===========================================================================
//  HTML HEADER
//===========================================================================

    public function header()
    {
        global $CMS, $DB, $member;

        $output = "";

        $output .= <<<EOF
  <!-- <script type="text/javascript" src="{$CMS->vars['js_url']}/acp_emailtpl.js"></script> -->
  <section class="add_table main_form">
    <figure class="heading">
      <h3>{$CMS->lang['manage_email_template']}</h3>
      <figure class="pull-right right">
        <div class="search">
          <form method="post" id="formquicksearch_adv"  action="{$CMS->vars['root_domain']}/?site=emailtpl&act=search_do" style="display:inline-block">
            <input type="submit" class="fa-input" value="&#xf002;">
            <input name="emailtpl_name" id="emailtpl_name" type="text" value="{$CMS->input['emailtpl_name']}" autocomplete="off" minlength="2" maxlength="64" placeholder="{$CMS->lang['gsearch_quick']}" style="position: :relative;">
            <div id="suggesstion-box" class="box_result_find" style="display:none"></div>
          </form>
          <script language="javascript">rebuild_form("formquicksearch_adv",1);</script>
          <a id="expand_formsearch" title="">{$CMS->lang['gsearch_advance']}<i class="fa fa-angle-double-right"></i></a>
        </div>
        {$CMS->global->importExportData($CMS->input['site'],'',1)}
        <a href="{$CMS->vars['root_domain']}/?site=emailtpl&act=add" title="" class="add_bill">{$CMS->lang['emailtpl_add_form']}</a>
        <a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
      </figure>
      <section class="search_adv" >
          <form method="post" id="formsearch_adv" style="display:none"  action="{$CMS->vars['root_domain']}/?site=emailtpl&act=search_do">
            <figure class="box-typical box-typical box-typical-padding border">
                <h5>{$CMS->lang['gsearch_advance']}</h5>
                <ul class="input_li row match-height">
                  <div class="col-lg-12">
                    <div class="row">
                      <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                        <label class="form-label pull-left" for="supplier_id">{$CMS->lang['emailtpl_status']}</label>
                        <select class="form-control select2" name="emailtpl_status" defaultvalue="{$CMS->input['emailtpl_status']}">
                          <option value="">{$CMS->lang['all']}</option>
                          <option value="0">{$CMS->lang['emailtpl_status_0']}</option>
                          <option value="1">{$CMS->lang['emailtpl_status_1']}</option>
                        </select>
                      </li>
                      <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                        <label class="form-label pull-left" for="emailtpl_from">{$CMS->lang['emailtpl_from']}</label>
                        <input class="form-control" type="text" name="emailtpl_from" id="emailtpl_from" value="{$CMS->input['emailtpl_from']}" placeholder="{$CMS->lang['emailtpl_from']}">
                      </li>
                      <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                        <label class="form-label pull-left" for="emailtpl_fromname">{$CMS->lang['emailtpl_fromname']}</label>
                        <input class="form-control" type="text" name="emailtpl_fromname" id="emailtpl_fromname" value="{$CMS->input['emailtpl_fromname']}" placeholder="{$CMS->lang['emailtpl_fromname']}">
                      </li>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="row">
                      <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                        <label class="form-label pull-left" for="emailtpl_name">{$CMS->lang['emailtpl_name']}</label>
                        <input class="form-control" type="text" name="emailtpl_name" id="emailtpl_name" value="{$CMS->input['emailtpl_name']}" placeholder="{$CMS->lang['emailtpl_name']}">
                      </li>
                      <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                        <label class="form-label pull-left" for="emailtpl_title">{$CMS->lang['emailtpl_title']}</label>
                        <input class="form-control" type="text" name="emailtpl_title" id="emailtpl_title" value="{$CMS->input['emailtpl_title']}" placeholder="{$CMS->lang['emailtpl_title']}" >
                      </li>
                      <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                        <label class="form-label pull-left" for="emailtpl_title" style="width:100%">&nbsp;</label>
                        <input type="submit" name="submit" id="submit" value="{$CMS->lang['comment_filter']}">
                      </li>
                    </div>
                  </div>
                </ul>
              </figure>
            </form>
            <script language="javascript">rebuild_form("formsearch_adv");</script>
        </section>
    </figure>
    <form method="post" name="form_emailtpl" id="form_emailtpl" action="{$CMS->vars['root_domain']}/?site=emailtpl">
      <section class="add_table">
        <div class="data_table">
          <div class="table table_cus table-responsive" style="border-top: none;">
            <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
              <thead>
                <tr>
                  <th data-orderable="false" data-sortable="false" aria-label="" style="margin:0px;padding:0px;"></th>
                  <th data-sortable="false" data-orderable="false" aria-label="">
                    <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_emailtpl');" id="checkall">
                      <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
                      <label for="id_{$result['record_cnt']}"></label>
                    </div>
                  </th>
                  <th>{$CMS->lang['emailtpl_id']}</th>
                  <th>{$CMS->lang['emailtpl_name']}</th>
                  <th>{$CMS->lang['emailtpl_send']}</th>
                  <th>{$CMS->lang['emailtpl_code']}</th>
                  <th>{$CMS->lang['emailtpl_from']}</th>
                  <th style="text-align:center">{$CMS->lang['emailtpl_status']}</th>
                  <th style="text-align:center" data-sortable="false" data-orderable="false"></th>
                </tr>
              </thead>
              <tbody>
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

        $style = $_SESSION['is_mobile'] == true ? "padding-right:27px;" : "";

        $output .= <<<EOF
  <tr>
    <td style="margin:0px;padding:0px;{$style}"></td>
    <td>
      <div class="checkbox checkbox-only">
        <input type="checkbox"  name="id_{$result['record_cnt']}" id="id_{$result['record_cnt']}" value="{$result['emailtpl_id']}"/>
        <label for="id_{$result['record_cnt']}"></label>
      </div>
    </td>
    <td>#{$result['emailtpl_id']}</td>
    <td class="mwr">{$result['emailtpl_name']}</td>
    <td style="text-align:center">
EOF;

        if ($CMS->permit['emailtpl_edit']) {
            $output .= <<<EOF
    <a href="{$CMS->vars['root_domain']}/?site=email&act=add&emailtpl_id={$result['emailtpl_id']}"><i class="fa fa-envelope fa-lg" aria-hidden="true" style="color: #758c98;border: 1px solid #d8e2e7;"></i></a>
EOF;
        }

        $output .= <<<EOF
    </td>
    <td>{$result['emailtpl_code']}</td>
    <td>{$result['emailtpl_from']}</td>
    <td style="text-align:center">{$result['emailtpl_status']}</td>
    <td style="text-align:center">
EOF;

        if ($CMS->permit['emailtpl_edit']) {
            $output .= <<<EOF
    <a href="{$CMS->vars['root_domain']}/?site=emailtpl&act=edit&id={$result['emailtpl_id']}" class="edit"><i class="fa fa-edit" aria-hidden="true"></i></a>
EOF;
        }

        if ($CMS->permit['emailtpl_delete']) {
            $output .= <<<EOF
    <a onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=emailtpl&act=delete&id={$result['emailtpl_id']}')" class="edit"><i class="fa fa-trash-o"></i></a>
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
    <td colspan="9">{$CMS->lang['emailtpl_no_data']}</td>
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
              <p class="form-control-static">{$CMS->emailtpl->action_control}</p>
            </div>
            <nav class="pull-right">
              <div class="block_bottom pagination pagination-sm">{$CMS->emailtpl->show_page}</div>
            </nav>
          </div>
        </div>
      </section>
      <input type="hidden" name="data_cnt" value="{$CMS->emailtpl->record_cnt}">
    </form>
    <script language="javascript">rebuild_form("form_emailtpl");</script>
  </section>
EOF;

        $output .= <<<EOF
  <script>
    $(function() {
      $('#example').DataTable({
        language: {
            emptyTable: 'Không tìm thấy dữ liệu!'
          },
        order: [[ 2,"desc"]],
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

    public function control()
    {
        global $CMS, $DB, $member;

        $output = "";

        $output .= <<<EOF
  <div class="block_action">
    <select class="form-control" name="act" defaultvalue="" emsg="{$CMS->lang['incomplete_action']}" ehide="1">
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
          $("#form_emailtpl").submit();
          $("select[name='act']").val('');
        }).catch(swal.noop);
      } // end if
    });
  </script>
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

        $output .= <<<EOF
  <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=emailtpl&id={$data['emailtpl_id']}&act=edit_do&page={$CMS->input['page']}" method="POST" enctype="multipart/form-data">
    <section class="add_form main_form">
      <figure class="heading">
        <h3 style="width:calc(100% - 21px);">{$CMS->lang['emailtpl_edit_form']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=emailtpl{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>
      <div class="row">
        <div class="col-lg-8" style="float:none;margin:0px auto;" >
          <figure class="box-typical box-typical box-typical-padding border">
            <div class="row">
              <div class="col-lg-12">
                <div class="form-group">
                  <div class="fl-flex-label">
                    <input class="form-control ks-rounded" type="text" name="emailtpl_name" value="{$data['emailtpl_name']}" placeholder="{$CMS->lang['emailtpl_name']}" required data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['emailtpl_incomplete_name']}">
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="fl-flex-label">
                        <input class="form-control ks-rounded" type="text" name="emailtpl_from" value="{$data['emailtpl_from']}" placeholder="{$CMS->lang['emailtpl_from']}">
                        <small class="text-muted" style="font-size: 11px;">{$CMS->lang['title_use_email_default']} <b>{$CMS->vars['smtp_email_display']}</b></small>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="fl-flex-label">
                        <input class="form-control ks-rounded" type="text" name="emailtpl_fromname" value="{$data['emailtpl_fromname']}" placeholder="{$CMS->lang['emailtpl_fromname']}">
                        <small class="text-muted" style="font-size: 11px;">{$CMS->lang['title_use_email_from_name_default']} <b>{$CMS->vars['website_title']}</b></small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="form-group">
                  <div class="fl-flex-label">
                    <input class="form-control ks-rounded" type="text" name="emailtpl_title" value="{$data['emailtpl_title']}" placeholder="{$CMS->lang['emailtpl_title']}" required data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['emailtpl_incomplete_title']}">
                  </div>
                </div>
                <fieldset class="form-group">
                  <label class="form-label" >{$CMS->lang['emailtpl_content']}</label>
                  <p class="typeahead-field">
                    <span class="typeahead-query">
                      <textarea class="form-control editor_texarea" rows="11" name="emailtpl_content">{$data['emailtpl_content']}</textarea>
                    </span>
                  </p>
                </fieldset>
                <div class="form-group">
                  <div class="fl-flex-label">
                    <input class="form-control ks-rounded" type="text" name="emailtpl_code" value="{$data['emailtpl_code']}" placeholder="{$CMS->lang['emailtpl_code']}" required data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['emailtpl_incomplete_code']}">
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-8">
                    <fieldset class="form-group">
                      <label class="form-label" >{$CMS->lang['emailtpl_note']}</label>
                      <p class="typeahead-field">
                        <span class="typeahead-query">
                          <textarea class="form-control" rows="5" name="emailtpl_note">{$data['emailtpl_note']}</textarea>
                        </span>
                      </p>
                    </fieldset>
                  </div>
                  <div class="col-lg-4">
                     <fieldset class="form-group">
                      <label class="form-label" >{$CMS->lang['emailtpl_protected']}</label>
                      <p class="typeahead-field">
                        <span class="typeahead-query">
                          <span class="rad_box"><input type="radio" class="input_radio" name="emailtpl_protected" defaultvalue="{$data['emailtpl_protected']}" value="1"> {$CMS->lang['yes']}</span>&nbsp;&nbsp;
                          <span class="rad_box"><input type="radio" name="emailtpl_protected" defaultvalue="{$data['emailtpl_protected']}" value="0" checked="checked"> {$CMS->lang['no']}</span>
                        </span>
                      </p>
                    </fieldset>
                    <fieldset class="form-group">
                      <label class="form-label" >{$CMS->lang['emailtpl_status']}</label>
                      <p class="typeahead-field">
                        <span class="typeahead-query">
                          <span class="rad_box"><input type="radio" class="input_radio" name="emailtpl_status" defaultvalue="{$data['emailtpl_status']}" value="1"> {$CMS->lang['yes']}</span>&nbsp;&nbsp;
                          <span class="rad_box"><input type="radio" name="emailtpl_status" defaultvalue="{$data['emailtpl_status']}" value="0" checked="checked"> {$CMS->lang['no']}</span>
                        </span>
                      </p>
                    </fieldset>
                  </div>
                </div>
              </div>
            </div>
          </figure>
        </div><!-- end col -->
      </div><!-- end row -->
    </section>
EOF;

        $footer_details = array(
            'type' => 'edit',
            'module' => 'emailtpl',
            'detail_id' => $data['emailtpl_id'],
        );
        $output .= $CMS->global->footer_details($footer_details);
        $output .= <<<EOF
    </form>
    <script language="javascript">
      rebuild_form("form-signin_v1",1);
    </script>
    <script>
    tinyMCESettings = [];
    tinyMCESettings['convert_urls'] =  false;
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

        $data['emailtpl_content'] = $_POST['emailtpl_content'] ? $_POST['emailtpl_content'] : $this->tpl1();

// data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['emailtpl_incomplete_fromname']}"
// data-validation-regex="/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="{$CMS->lang['invalid_email']}"
        $output .= <<<EOF
  <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=emailtpl&act=add_do" method="POST" enctype="multipart/form-data">
    <section class="add_form main_form">
      <figure class="heading">
        <h3 style="width:calc(100% - 21px);">{$CMS->lang['emailtpl_add_form']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=emailtpl{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>
      <div class="row">
        <div class="col-lg-8" style="float:none;margin:0px auto;" >
          <figure class="box-typical box-typical box-typical-padding border">
            <div class="row">
              <div class="col-lg-12">
                <div class="form-group">
                  <div class="fl-flex-label">
                    <input class="form-control ks-rounded" type="text" name="emailtpl_name" value="{$data['emailtpl_name']}" placeholder="{$CMS->lang['emailtpl_name']}" required data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['emailtpl_incomplete_name']}">
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="fl-flex-label">
                        <input class="form-control ks-rounded" type="text" name="emailtpl_from" value="{$data['emailtpl_from']}" placeholder="{$CMS->lang['emailtpl_from']}">
                      </div>
                      <small class="text-muted" style="font-size: 11px;">{$CMS->lang['title_use_email_default']} <b>{$CMS->vars['smtp_email_display']}</b></small>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="fl-flex-label">
                        <input class="form-control ks-rounded" type="text" name="emailtpl_fromname" value="{$data['emailtpl_fromname']}" placeholder="{$CMS->lang['emailtpl_fromname']}">
                      </div>
                      <small class="text-muted" style="font-size: 11px;">{$CMS->lang['title_use_email_from_name_default']} <b>{$CMS->vars['website_title']}</b></small>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="form-group">
                  <div class="fl-flex-label">
                    <input class="form-control ks-rounded" type="text" name="emailtpl_title" value="{$data['emailtpl_title']}" placeholder="{$CMS->lang['emailtpl_title']}" required data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['emailtpl_incomplete_title']}">
                  </div>
                </div>
                <fieldset class="form-group">
                  <label class="form-label" >{$CMS->lang['emailtpl_content']}</label>
                  <p class="typeahead-field">
                    <span class="typeahead-query">
                      <textarea class="form-control editor_texarea" rows="11" name="emailtpl_content">{$data['emailtpl_content']}</textarea>
                    </span>
                  </p>
                </fieldset>
                <div class="form-group">
                  <div class="fl-flex-label">
                    <input class="form-control ks-rounded" type="text" name="emailtpl_code" value="{$data['emailtpl_code']}" placeholder="{$CMS->lang['emailtpl_code']}" required data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['emailtpl_incomplete_code']}">
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-8">
                    <fieldset class="form-group">
                      <label class="form-label" >{$CMS->lang['emailtpl_note']}</label>
                      <p class="typeahead-field">
                        <span class="typeahead-query">
                          <textarea class="form-control" rows="5" name="emailtpl_note">{$data['emailtpl_note']}</textarea>
                        </span>
                      </p>
                    </fieldset>
                  </div>
                  <div class="col-lg-4">
                     <fieldset class="form-group">
                      <label class="form-label" >{$CMS->lang['emailtpl_protected']}</label>
                      <p class="typeahead-field">
                        <span class="typeahead-query">
                          <span class="rad_box"><input type="radio" class="input_radio" name="emailtpl_protected" defaultvalue="{$data['emailtpl_protected']}" value="1"> {$CMS->lang['yes']}</span>&nbsp;&nbsp;
                          <span class="rad_box"><input type="radio" name="emailtpl_protected" defaultvalue="{$data['emailtpl_protected']}" value="0" checked="checked"> {$CMS->lang['no']}</span>
                        </span>
                      </p>
                    </fieldset>
                    <fieldset class="form-group">
                      <label class="form-label" >{$CMS->lang['emailtpl_status']}</label>
                      <p class="typeahead-field">
                        <span class="typeahead-query">
                          <span class="rad_box"><input type="radio" class="input_radio" name="emailtpl_status" defaultvalue="{$data['emailtpl_status']}" value="1"> {$CMS->lang['yes']}</span>&nbsp;&nbsp;
                          <span class="rad_box"><input type="radio" name="emailtpl_status" defaultvalue="{$data['emailtpl_status']}" value="0" checked="checked"> {$CMS->lang['no']}</span>
                        </span>
                      </p>
                    </fieldset>
                  </div>
                </div>
              </div>
            </div>
          </figure>
        </div><!-- end col -->
      </div><!-- end row -->
    </section>
EOF;

        $footer_details = array(
            'type' => 'add',
            'module' => 'emailtpl',
        );
        $output .= $CMS->global->footer_details($footer_details);
        $output .= <<<EOF
    </form>
    <script language="javascript">
      rebuild_form("form-signin_v1",1);
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

    public function show($data)
    {
        global $CMS, $DB, $member;

        $output = "";

        $output .= <<<EOF
    <section class="add_form main_form">
      <figure class="heading">
        <h3>{$CMS->lang['emailtpl_show_form']} #{$data['emailtpl_id']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=emailtpl{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>
      <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
          <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
            <h5 class="with-border m-t-0">{$CMS->lang['emailtpl_header_info']}</h5>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['emailtpl_name']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">
                  {$data['emailtpl_name']}
                  <!--
                  <script type="text/javascript">permission_btn("edit", "emailtpl", "{$CMS->vars['root_domain']}/?site=emailtpl&act=edit&id={$data['emailtpl_id']}");</script>
                  <script type="text/javascript">permission_btn("delete", "emailtpl", "{$CMS->vars['root_domain']}/?site=emailtpl&act=delete&id={$data['emailtpl_id']}");</script>
                  -->
                </div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['emailtpl_from']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['emailtpl_from']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['emailtpl_fromname']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['emailtpl_fromname']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['emailtpl_code']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['emailtpl_code']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['emailtpl_note']}</label>
              <div class="col-xl-8 form-control-span2">
                <div class="form-label semibold">{$data['emailtpl_note']}</div>  
              </div>
            </fieldset>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
            <h5 class="with-border">{$CMS->lang['emailtpl_more_info']}</h5>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['emailtpl_protected']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['emailtpl_protected']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['emailtpl_status']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['emailtpl_status']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['user_id']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['user_id']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['emailtpl_time']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['emailtpl_time']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['emailtpl_time_update']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['emailtpl_time_update']}</div>  
              </div>
            </fieldset>
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <h5 class="with-border">{$CMS->lang['emailtpl_body_info']}</h5>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['emailtpl_title']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['emailtpl_title']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['emailtpl_content']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['emailtpl_content']}</div>  
              </div>
            </fieldset>
          </div>
        </div>
      </figure>
    </section>
EOF;

        $footer_details = array(
            'type' => 'show',
            'module' => 'emailtpl',
            'detail_id' => $data['emailtpl_id'],
            'act_deleted' => "delete",
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

<header class="section-header">
        <div class="tbl">
          <div class="tbl-row">
            <div class="tbl-cell">
              <h3>Tìm kiếm Email mẫu</h3>              
            </div>
          </div>
        </div>
      </header>

<form method="post" id="emailtpl" name="emailtpl" action="{$CMS->vars['root_domain']}/?site=emailtpl&act=search_do" onSubmit="return check_form(this.id);">

 <section class="card">

      <section class="box-typical">
                    <header class="box-typical-header">
                                <div class="tbl-row">
                                    <div class="tbl-cell tbl-cell-title">
                                      <h3></h3>
                                    </div>
                                    <div class="tbl-cell tbl-cell-action-bordered">
                                        <a href="{$CMS->vars['root_domain']}/?site=emailtpl{$CMS->class->search->url_return}">
                                        <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                                        </a>
                                    </div>
                                     
                                </div>
                            </header>
           </section>



        <div class="card-block">


              <div class="form-group row">
              	<label class="col-sm-3 form-control-label">{$CMS->lang['emailtpl_from']}</label>
              	<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                  <p class="form-control-static-input">
                       <input class='form-control' type="text" name="emailtpl_from" value="{$data['emailtpl_from']}" etype="date">
                  </p>
                </div>
              </div>
              
              <div class="form-group row">  
              	<label class="col-sm-3 form-control-label">{$CMS->lang['emailtpl_fromname']}</label>
              	<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                  <p class="form-control-static-input">
                      <input class='form-control' type="text" name="emailtpl_fromname" value="{$data['emailtpl_fromname']}" etype="date">
                  </p>
                </div>
             </div>
                
              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['emailtpl_name']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                  <input class="form-control" type="text" name="emailtpl_name" value="{$data['emailtpl_name']}">
                   </p>  
                </div>
              </div>


               <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['emailtpl_title']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                  <p class="form-control-static-input">
                    <input class="form-control" type="text" name="emailtpl_title" value="{$data['emailtpl_title']}">
                  </p>            
                </div>
              </div>



              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['emailtpl_title']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                  <input class="form-control" type="text" name="emailtpl_title" value="{$data['emailtpl_title']}" emsg="{$CMS->lang['emailtpl_incomplete_title']}">
                  </p>
                </div>
              </div>


              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['emailtpl_content']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                	<textarea class="form-control" name="emailtpl_content" value="{$data['emailtpl_content']}"></textarea>
                  </p>
                </div>
              </div>



              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['emailtpl_status']}</label>
                <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                  <select class="select2" name="emailtpl_status" defaultvalue="{$data['emailtpl_status']}"><option value="">{$CMS->lang['all']}</option><option value="0">{$CMS->lang['emailtpl_status_0']}</option><option value="1">{$CMS->lang['emailtpl_status_1']}</option><option value="2">{$CMS->lang['emailtpl_status_2']}</option></select>           
                  </p>
                </div>
              </div>


            <div class="form-group row">
              <label class="col-sm-3 form-control-label"></label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                 <button class="btn btn-rounded" type="submit" >{$CMS->lang['search_submit']}</button>
                </p>
              </div>
            </div>
             



        </div><!-- end card-block -->
  </section><!-- end section card -->
</form>
<script language="javascript">rebuild_form("emailtpl",1,1);</script>
EOF;

        return $output;
    }

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
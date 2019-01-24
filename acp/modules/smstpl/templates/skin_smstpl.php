<?php

class skin_smstpl {

//===========================================================================
//  HTML HEADER
//===========================================================================

public function header()
{
	global $CMS, $DB, $member;
	
	$output = "";

	$quickSearchDisplay = $CMS->input['act'] == 'search_do' ? '' : 'display:none';

  $output .= <<<EOF
  <script type='text/javascript' src='{$CMS->vars['js_url']}/acp_smstpl.js'></script>
  <section class="add_table main_form">
    <figure class="heading">
      <h3>{$CMS->lang['manage_sms_template']}</h3>
      <figure class="pull-right right">
        <div class="search">
          <form method="post" id="formquicksearch_adv"  action="{$CMS->vars['root_domain']}/?site=smstpl&act=search_do" style="display:inline-block">
            <input type="submit" class="fa-input" value="&#xf002;">
            <input name="keyword" id="p_quick_search" type="text" value="{$CMS->input['keyword']}" autocomplete="off" minlength="2" maxlength="64" placeholder="{$CMS->lang['gsearch_quick']}" style="position: :relative;">
            <div id="suggesstion-box" class="box_result_find" style="display:none"></div>
          </form>
          <a id="expand_formsearch" title="">{$CMS->lang['gsearch_advance']}<i class="fa fa-angle-double-right"></i></a>
        </div>
        
        {$CMS->global->importExportData($CMS->input['site'],'',1)}
        
        <a href="{$CMS->vars['root_domain']}/?site=smstpl&act=add" title="" class="add_bill">{$CMS->lang['smstpl_add_form']}</a>
        <a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
      </figure>
      <section class="search_adv" >
          <form method="get" id="formsearch_adv" style="{$quickSearchDisplay}"  action="{$CMS->vars['root_domain']}/?site=smstpl&act=search_do">
            <input type="hidden" name="site" value="smstpl">
            <input type="hidden" name="act" value="search_do">
            <figure class="box-typical box-typical box-typical-padding border">
                <h5>{$CMS->lang['gsearch_advance']}</h5>
                <ul class="input_li row">
                  <div class="col-lg-12">
                    <div class="row">
                    
                    <!--
                      <li class="col-xl-3 col-lg-3 col-sm-3 col-xs-12">
                        <label class="form-label pull-left" for="supplier_id">{$CMS->lang['smstpl_status']}</label>
                        <select class="form-control select2" name="smstpl_status" defaultvalue="{$CMS->input['smstpl_status']}">
                          <option value="">{$CMS->lang['all']}</option>
                          <option value="0">{$CMS->lang['smstpl_status_0']}</option>
                          <option value="1">{$CMS->lang['smstpl_status_1']}</option>
                        </select>
                      </li>
                      -->
                      
                      <li class="col-xl-3 col-lg-3 col-sm-3 col-xs-12">
                        <label class="form-label pull-left" for="smstpl_name">{$CMS->lang['smstpl_name']}</label>
                        <input class="form-control" type="text" name="smstpl_name" id="smstpl_name" value="{$CMS->input['smstpl_name']}" placeholder="{$CMS->lang['smstpl_name']}">
                      </li>
                      <li class="col-xl-3 col-lg-3 col-sm-3 col-xs-12">
                        <label class="form-label pull-left" for="smstpl_content">{$CMS->lang['smstpl_content']}</label>
                        <textarea class="form-control" style="width:100% !important;" name="smstpl_content">{$CMS->input['smstpl_content']}</textarea>
                      </li>
                      <li class="col-xl-3 col-lg-3 col-sm-3 col-xs-12">
                        <label class="form-label" for="smstpl_content">&nbsp;</label>
                        <input type="submit" name="submit" id="submit" value="{$CMS->lang['comment_filter']}">
                      </li>
                    </div>
                  </div>
                </ul>
              </figure>
            </form>
            <script language="javascript">rebuild_form("formsearch_adv"); autocomplete_quick_search();</script>
        </section>
        
        <section class="tabs-section" style="margin-bottom: 15px">
            <div class="tabs-section-nav tabs-section-nav-inline">
                <ul class="nav" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" href="{$CMS->vars['root_domain']}/?site=sms">
                            {$CMS->lang['menu_sms']}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{$CMS->vars['root_domain']}/?site=smstpl">
                           {$CMS->lang['menu_smstpl']}
                        </a>
                    </li>
                </ul>
            </div><!--.tabs-section-nav-->
        </section>
    </figure>
    
    <form method="post" name="form_smstpl" id="form_smstpl" action="{$CMS->vars['root_domain']}/?site=smstpl">
      <section class="add_table">
        <div class="data_table">
          <div class="table table_cus table-responsive" style="border-top: none;">
            <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
              <thead>
                <tr>
                  <th data-orderable="false" data-sortable="false" aria-label="" style="margin:0px;padding:0px;"></th>
                  <th data-sortable="false" data-orderable="false" aria-label="">
                    <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_smstpl');" id="checkall">
                      <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
                      <label for="id_{$result['record_cnt']}"></label>
                    </div>
                  </th>
                  <th>{$CMS->lang['smstpl_id']}</th>
                  <th>{$CMS->lang['smstpl_name']}</th>
                  <th>{$CMS->lang['smstpl_code']}</th>
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
        <input type="checkbox"  name="id_{$result['record_cnt']}" id="id_{$result['record_cnt']}" value="{$result['smstpl_id']}"/>
        <label for="id_{$result['record_cnt']}"></label>
      </div>
    </td>
    <td>#{$result['smstpl_id']}</td>
    <td class="mwr">{$result['smstpl_name']}</td>
    <td>{$result['smstpl_code']}</td>
    <td style="text-align:center">
EOF;
    
  if ( $CMS->permit['smstpl_edit'] )
  {
    $output .= <<<EOF
    <a href="{$CMS->vars['root_domain']}/?site=smstpl&act=edit&id={$result['smstpl_id']}" class="edit"><i class="fa fa-edit" aria-hidden="true"></i></a>
EOF;
  }

  /*if ( $CMS->permit['smstpl_delete'] )
  {
    $output .= <<<EOF
    <a onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=smstpl&act=delete&id={$result['smstpl_id']}')" class="edit"><i class="fa fa-trash-o"></i></a>
EOF;
  }*/

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
    <td colspan="6">{$CMS->lang['no_data']}</td>
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
              <p class="form-control-static">{$CMS->smstpl->action_control}</p>
            </div>
            <nav class="pull-right">
              <div class="block_bottom pagination pagination-sm">{$CMS->show_page}</div>
            </nav>
          </div>
        </div>
      </section>
      <input type="hidden" name="data_cnt" value="{$CMS->smstpl->record_cnt}">
    </form>
    <script language="javascript">rebuild_form("form_smstpl");</script>
  </section>
EOF;
    if($CMS->class->page->total_row)
    {
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

        if ( $_SESSION['is_mobile'] == true )
        {
            $output .= "responsive: { details: true},";
        }


        $output .= <<<EOF
      });
    });
  </script>
EOF;
    }



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
    <!--
    <select class="form-control" name="act" defaultvalue="" emsg="{$CMS->lang['incomplete_action']}" ehide="1">
      {$CMS->vars['action_controller']}
    </select>
    -->
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
          $("#form_smstpl").submit();
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

public function edit( $data )
{
	global $CMS, $DB, $member;

	$output = "";
  
  $output .= <<<EOF
  <script type='text/javascript' src='{$CMS->vars['js_url']}/acp_smstpl.js'></script>
  <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=smstpl&id={$data['smstpl_id']}&act=edit_do&page={$CMS->input['page']}" method="POST" enctype="multipart/form-data">
    <section class="add_form main_form">
      <figure class="heading">
        <h3 style="width:calc(100% - 21px);">{$CMS->lang['smstpl_edit_form']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=smstpl{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>
      <div class="row">
        <div class="col-lg-8" style="float:none;margin:0px auto;" >
          <figure class="box-typical box-typical box-typical-padding border">
            <div class="row">
              <div class="col-lg-12">
                <div class="form-group">
                  <div class="fl-flex-label">
                    <input class="form-control ks-rounded" type="text" name="smstpl_name" value="{$data['smstpl_name']}" placeholder="{$CMS->lang['smstpl_name']}" required data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['smstpl_incomplete_name']}">
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <fieldset class="form-group">
                  <label class="form-label" >{$CMS->lang['smstpl_content']}  <em>({$CMS->lang['remain']} <span class="content-limiter"></span> {$CMS->lang['character']})</em></label>
                  <p class="typeahead-field">
                    <span class="typeahead-query">
                      <textarea class="form-control summernote" rows="11" name="smstpl_content">{$data['smstpl_content']}</textarea>
                    </span>
                  </p>
                  <script>
                    $("[name='smstpl_content']").limiter(255, $(".content-limiter"));
                  </script>
                </fieldset>
              </div>
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-8">
                    <fieldset class="form-group">
                      <label class="form-label" >{$CMS->lang['smstpl_note']}</label>
                      <p class="typeahead-field">
                        <span class="typeahead-query">
                          <textarea class="form-control" rows="5" name="smstpl_note">{$data['smstpl_note']}</textarea>
                        </span>
                      </p>
                    </fieldset>
                    <fieldset class="form-group">
                        <label class="form-label">{$CMS->lang['smstpl_required_keys']}</label>
                        <p class="typeahead-query">
                         <select name="smstpl_required_keys[]" class="tag-selector form-control" multiple="multiple">
                          </select>
                        </p>
                    </fieldset>
                  </div>
                  
                  <!--
                  <div class="col-lg-4">
                     <fieldset class="form-group">
                      <label class="form-label" >{$CMS->lang['smstpl_protected']}</label>
                      <p class="typeahead-field">
                        <span class="typeahead-query">
                          <span class="rad_box"><input type="radio" class="input_radio" name="smstpl_protected" defaultvalue="{$data['smstpl_protected']}" value="1"> {$CMS->lang['yes']}</span>&nbsp;&nbsp;
                          <span class="rad_box"><input type="radio" name="smstpl_protected" defaultvalue="{$data['smstpl_protected']}" value="0" checked="checked"> {$CMS->lang['no']}</span>
                        </span>
                      </p>
                    </fieldset>
                    <fieldset class="form-group">
                      <label class="form-label" >{$CMS->lang['smstpl_status']}</label>
                      <p class="typeahead-field">
                        <span class="typeahead-query">
                          <span class="rad_box"><input type="radio" class="input_radio" name="smstpl_status" defaultvalue="{$data['smstpl_status']}" value="1"> {$CMS->lang['yes']}</span>&nbsp;&nbsp;
                          <span class="rad_box"><input type="radio" name="smstpl_status" defaultvalue="{$data['smstpl_status']}" value="0" checked="checked"> {$CMS->lang['no']}</span>
                        </span>
                      </p>
                    </fieldset>
                  </div>
                  -->
                  
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
        'module' => 'smstpl',
        'detail_id' => $data['smstpl_id'],
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
      select2Required('{$data['smstpl_required_keys']}');
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
  
  $output .= <<<EOF
  <script type='text/javascript' src='{$CMS->vars['js_url']}/acp_smstpl.js'></script>
  <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=smstpl&act=add_do" method="POST" enctype="multipart/form-data">
    <section class="add_form main_form">
      <figure class="heading">
        <h3 style="width:calc(100% - 21px);">{$CMS->lang['smstpl_add_form']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=smstpl{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>
      <div class="row">
        <div class="col-lg-8" style="float:none;margin:0px auto;" >
          <figure class="box-typical box-typical box-typical-padding border">
            <div class="row">
              <div class="col-lg-12">
                <div class="form-group">
                  <div class="fl-flex-label">
                    <input class="form-control ks-rounded" type="text" name="smstpl_name" value="{$data['smstpl_name']}" placeholder="{$CMS->lang['smstpl_name']}" required data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['smstpl_incomplete_name']}">
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <fieldset class="form-group">
                  <label class="form-label" >{$CMS->lang['smstpl_content']} <em>({$CMS->lang['remain']} <span class="content-limiter"></span> {$CMS->lang['character']})</em></label>
                  <p class="typeahead-field">
                    <span class="typeahead-query">
                      <textarea class="form-control summernote" rows="11" name="smstpl_content">{$data['smstpl_content']}</textarea>
                    </span>
                  </p>
                  <script>
                    $("[name='smstpl_content']").limiter(255, $(".content-limiter"));
                  </script>
                </fieldset>
                <div class="form-group">
                  <div class="fl-flex-label">
                    <input class="form-control ks-rounded" type="text" name="smstpl_code" value="{$data['smstpl_code']}" placeholder="{$CMS->lang['smstpl_code']}" required data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['smstpl_incomplete_code']}">
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-8">
                    <fieldset class="form-group">
                      <label class="form-label" >{$CMS->lang['smstpl_note']}</label>
                      <p class="typeahead-field">
                        <span class="typeahead-query">
                          <textarea class="form-control" rows="5" name="smstpl_note">{$data['smstpl_note']}</textarea>
                        </span>
                      </p>
                    </fieldset>
                    
                    <fieldset class="form-group">
                        <label class="form-label">{$CMS->lang['smstpl_required_keys']}</label>
                        <p class="typeahead-query">
                         <select name="smstpl_required_keys[]" class="tag-selector form-control" multiple="multiple">
                          </select>
                        </p>
                    </fieldset>
                  </div>
                  
                  <!--
                  <div class="col-lg-4">
                     <fieldset class="form-group">
                      <label class="form-label" >{$CMS->lang['smstpl_protected']}</label>
                      <p class="typeahead-field">
                        <span class="typeahead-query">
                          <span class="rad_box"><input type="radio" class="input_radio" name="smstpl_protected" defaultvalue="{$data['smstpl_protected']}" value="1"> {$CMS->lang['yes']}</span>&nbsp;&nbsp;
                          <span class="rad_box"><input type="radio" name="smstpl_protected" defaultvalue="{$data['smstpl_protected']}" value="0" checked="checked"> {$CMS->lang['no']}</span>
                        </span>
                      </p>
                    </fieldset>
                    <fieldset class="form-group">
                      <label class="form-label" >{$CMS->lang['smstpl_status']}</label>
                      <p class="typeahead-field">
                        <span class="typeahead-query">
                          <span class="rad_box"><input type="radio" class="input_radio" name="smstpl_status" defaultvalue="{$data['smstpl_status']}" value="1"> {$CMS->lang['yes']}</span>&nbsp;&nbsp;
                          <span class="rad_box"><input type="radio" name="smstpl_status" defaultvalue="{$data['smstpl_status']}" value="0" checked="checked"> {$CMS->lang['no']}</span>
                        </span>
                      </p>
                    </fieldset>
                  </div>
                  -->
                  
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
        'module' => 'smstpl',
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
      select2Required('{$data['smstpl_required_keys']}');
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
        <h3>{$CMS->lang['smstpl_show_form']} #{$data['smstpl_id']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=smstpl{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>
      <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
          <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
            <h5 class="with-border m-t-0">{$CMS->lang['smstpl_header_info']}</h5>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['smstpl_name']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">
                  {$data['smstpl_name']}
                  <!--
                  <script type="text/javascript">permission_btn("edit", "smstpl", "{$CMS->vars['root_domain']}/?site=smstpl&act=edit&id={$data['smstpl_id']}");</script>
                  <script type="text/javascript">permission_btn("delete", "smstpl", "{$CMS->vars['root_domain']}/?site=smstpl&act=delete&id={$data['smstpl_id']}");</script>
                  -->
                </div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['smstpl_code']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['smstpl_code']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['smstpl_note']}</label>
              <div class="col-xl-8 form-control-span2">
                <div class="form-label semibold">{$data['smstpl_note']}</div>  
              </div>
            </fieldset>
          </div>
          <div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
            <h5 class="with-border">{$CMS->lang['smstpl_more_info']}</h5>
            
            <!--
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['smstpl_protected']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['smstpl_protected']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['smstpl_status']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['smstpl_status']}</div>  
              </div>
            </fieldset>
            -->
            
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['user_id']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['user_id']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['smstpl_time']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['smstpl_time']}</div>  
              </div>
            </fieldset>
            <fieldset class="form-group row">
              <label class="col-xl-4 form-control-label2" >{$CMS->lang['smstpl_time_update']}</label>
              <div class="col-xl-8 form-control-span2"> 
                <div class="form-label semibold">{$data['smstpl_time_update']}</div>  
              </div>
            </fieldset>
          </div>
          <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
            <h5 class="with-border">{$CMS->lang['smstpl_body_info']}</h5>
            <fieldset class="form-group row">
              <label class="col-xl-2 form-control-label2" >{$CMS->lang['smstpl_content']}</label>
              <div class="col-xl-10 form-control-span2"> 
                <div class="form-label semibold">{$data['smstpl_content']}</div>  
              </div>
            </fieldset>
          </div>
        </div>
      </figure>
    </section>
EOF;

    $footer_details = array(
        'type' => 'show',
        'module' => 'smstpl',
        'detail_id' => $data['smstpl_id'],
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
              <h3>Tìm kiếm SMS mẫu</h3>              
            </div>
          </div>
        </div>
      </header>

<form method="post" id="smstpl" name="smstpl" action="{$CMS->vars['root_domain']}/?site=smstpl&act=search_do" onSubmit="return check_form(this.id);">

 <section class="card">

      <section class="box-typical">
                    <header class="box-typical-header">
                                <div class="tbl-row">
                                    <div class="tbl-cell tbl-cell-title">
                                      <h3></h3>
                                    </div>
                                    <div class="tbl-cell tbl-cell-action-bordered">
                                        <a href="{$CMS->vars['root_domain']}/?site=smstpl{$CMS->class->search->url_return}">
                                        <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                                        </a>
                                    </div>
                                     
                                </div>
                            </header>
           </section>



        <div class="card-block">


              <div class="form-group row">
              	<label class="col-sm-3 form-control-label">{$CMS->lang['smstpl_from']}</label>
              	<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                  <p class="form-control-static-input">
                       <input class='form-control' type="text" name="smstpl_from" value="{$data['smstpl_from']}" etype="date">
                  </p>
                </div>
              </div>
              
              <div class="form-group row">  
              	<label class="col-sm-3 form-control-label">{$CMS->lang['smstpl_fromname']}</label>
              	<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                  <p class="form-control-static-input">
                      <input class='form-control' type="text" name="smstpl_fromname" value="{$data['smstpl_fromname']}" etype="date">
                  </p>
                </div>
             </div>
                
              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['smstpl_name']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                  <input class="form-control" type="text" name="smstpl_name" value="{$data['smstpl_name']}">
                   </p>  
                </div>
              </div>


               <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['smstpl_title']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                  <p class="form-control-static-input">
                    <input class="form-control" type="text" name="smstpl_title" value="{$data['smstpl_title']}">
                  </p>            
                </div>
              </div>



              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['smstpl_title']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                  <input class="form-control" type="text" name="smstpl_title" value="{$data['smstpl_title']}" emsg="{$CMS->lang['smstpl_incomplete_title']}">
                  </p>
                </div>
              </div>


              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['smstpl_content']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                	<textarea class="form-control" name="smstpl_content" value="{$data['smstpl_content']}"></textarea>
                  </p>
                </div>
              </div>



              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['smstpl_status']}</label>
                <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                  <select class="select2" name="smstpl_status" defaultvalue="{$data['smstpl_status']}"><option value="">{$CMS->lang['all']}</option><option value="0">{$CMS->lang['smstpl_status_0']}</option><option value="1">{$CMS->lang['smstpl_status_1']}</option><option value="2">{$CMS->lang['smstpl_status_2']}</option></select>           
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
<script language="javascript">rebuild_form("smstpl",1,1);</script>
EOF;
	
	return $output;	
}

}

?>
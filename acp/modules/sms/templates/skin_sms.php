<?php

class skin_sms {

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
      <h3>{$CMS->lang['title_list_sms']}</h3>
      <figure class="pull-right right">        
EOF;
          if($CMS->permit['sms_add'])
          {
$output .=<<<EOF

        <a href="{$CMS->vars['root_domain']}/?site=sms&act=add" title="" class="add_bill">{$CMS->lang['add_form']}</a>
EOF;
          }
$output .=<<<EOF

      </figure>
        <section class="tabs-section" style="margin-bottom: 15px">
            <div class="tabs-section-nav tabs-section-nav-inline">
                <ul class="nav" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="{$CMS->vars['root_domain']}/?site=sms">
                            {$CMS->lang['menu_sms']}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{$CMS->vars['root_domain']}/?site=smstpl">
                           {$CMS->lang['menu_smstpl']}
                        </a>
                    </li>
                </ul>
            </div><!--.tabs-section-nav-->
        </section>
    </figure>
                    
<form method="post" name="sms" id="sms" action="{$CMS->vars['root_domain']}/?site=sms">
    <section class="add_table"> 

EOF;

    if($_SESSION['is_mobile'] == true)
    {
        $output .=<<<EOF
        <div class="data_table">
          <table id="example" class="display table table_cus" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th width="1%"></th>
                  <th width="1%" class="table-check">
                      <div class="checkbox checkbox-only" onclick="javascript:form_checkall('sms');" id="checkall">
                     <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
                     <label for="id_{$result['record_cnt']}"></label>
                      </div>
                    </th>
                  <th width="7%">{$CMS->lang['sms_id']}</th>
                  <th width="10%">{$CMS->lang['sms_from']}</th>
                  <th width="10%">{$CMS->lang['sms_to']}</th>
                  <th width="10%">{$CMS->lang['sms_reply']}</th>
                  <th width="20%">{$CMS->lang['sms_content']}</th>
                  <th width="20%">{$CMS->lang['sms_time']}</th>
                  <th width="10%">{$CMS->lang['sms_status']}</th> 
                  <th width="1%"></th>
                </tr>
              </thead>
            <tbody>
EOF;
    }else
    {
        $output .=<<<EOF
        <div class="table-responsive">
          <table class="table_cus" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th width="2%" class="table-check">
                    <div class="checkbox checkbox-only" onclick="javascript:form_checkall('sms');" id="checkall">
                   <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
                   <label for="id_{$result['record_cnt']}"></label>
                    </div>
                  </th>
                <th width="7%">{$CMS->lang['sms_id']}</th>
                <th width="10%">{$CMS->lang['sms_from']}</th>
                <th width="10%">{$CMS->lang['sms_to']}</th>
                <th width="10%">{$CMS->lang['sms_reply']}</th>
                <th width="30%">{$CMS->lang['sms_content']}</th>
                <th width="20%">{$CMS->lang['sms_time']}</th>
                <th width="10%">{$CMS->lang['sms_status']}</th> 
                <th width="1%"></th>
              </tr>
            </thead>
            <tbody>
EOF;
    }
	return $output;
}


//===========================================================================
//  HTML MIDDLE
//===========================================================================

public function middle($result)
{
	global $CMS, $DB, $member;
	
	$output = "";
  $btn_control = "";
  if($CMS->permit['sms_edit'])
  {
    $btn_control .=<<<EOF
      <a class="edit" href="{$CMS->vars['root_domain']}/?site=sms&act=edit&id={$result['sms_id']}"><i class="fa fa-edit"></i></a>
EOF;

  }

  if($CMS->permit['sms_delete'])
  {
    $btn_control .=<<<EOF
      <a class="edit" onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=sms&act=delete&id={$result['sms_id']}')"><i class="fa fa-trash"></i></a>
EOF;

  }
  if($_SESSION['is_mobile'] == true)
  {
      $output_td = "<td></td>";
  }else
  {
      $output_td =<<<EOF
        
EOF;

  }
$output .= <<<EOF
  <tr bgcolor="{$result['bgcolor']}">
    {$output_td}
    <td class="table-check">
          <div class="checkbox checkbox-only">
            <input type="checkbox"  name="id_{$result['record_cnt']}" id="id_{$result['record_cnt']}" value="{$result['sms_id']}"/>
            <label for="id_{$result['record_cnt']}"></label>
          </div>
    </td>
    <td>#{$result['sms_id']}</td>
    <td>{$result['sms_from']}</td>
    <td>{$result['sms_to']}</td>
    <td>{$result['sms_reply']}</td>
    <td style="white-space: inherit;">{$result['sms_content']}</td>
    <td style="white-space: inherit;">{$result['sms_time']}</td>
    <td>{$result['sms_status']}</td>
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
  if($_SESSION['is_mobile'] == false)
  {
$output .= <<<EOF
  <tr>
    <td colspan="9">{$CMS->lang['no_data']}</td>
  </tr>
EOF;
  }
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
    <div class="fuction_table">

      <div class="pull-left">
        <p class="form-control-static ">
            {$CMS->sms->action_control}
        </p>
      </div>
      <nav class="pull-right">
         {$CMS->show_page}
      </nav>
   </div>
         
   <input name="data_cnt" value="{$CMS->sms->record_cnt}" type="hidden" />
</form>
EOF;

    if($_SESSION['is_mobile'] == true)
    {
        $output .=<<<EOF
         <script>
            $(function() {
              $('#example').DataTable({
              language: {
                      emptyTable: '{$CMS->lang['no_data']}'
                  },
                 order: [],
              responsive: true,
                columnDefs: [
                    { responsivePriority: 1, targets: 1 },
                    { responsivePriority: 2, targets: 4 },
                    
                ],
                paging: false,
                  searching: false,
                  info: false
              });
            });
          </script>
EOF;

    }
    $output .=<<<EOF

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
$CMS->sms->load_parent_category();
$output .= <<<EOF
<form method="post" id="sms" name="sms" action="{$CMS->vars['root_domain']}/?site=sms&id={$data['sms_id']}&act=edit_do&page={$CMS->input['page']}" onSubmit="return check_form(this.id);">
<section class="add_form main_form">
      <figure class="heading">
        <h3>{$CMS->lang['edit_form']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=sms{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>

      <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
          <div class="col-lg-4">
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['sms_from']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <input class="form-control" type="text" name="sms_from" value="{$data['sms_from']}" data-validation="[NUMERIC]" maxlength="12" data-validation-message="{$CMS->lang['sms_incomplete_from']}">
                  </div>
                </div>
                    
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['sms_to']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <input class="form-control" type="text" name="sms_to" value="{$data['sms_to']}" data-validation="[NUMERIC]" maxlength="12" data-validation-message="{$CMS->lang['sms_incomplete_to']}">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="form-control-label"></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <button class="btn" type="submit">{$CMS->lang['edit_submit']}</button>
                  </div>
                </div>

          </div> <!--End col-lg-4-->
                    
          <div class="col-lg-8">
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['sms_content']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <textarea class="form-control" rows="6" name="sms_content">{$data['sms_content']}</textarea>
                  </div>
                </div>
          </div>
        </div>
      </figure>
    </section>
</form>
<script language="javascript">
  $(document).ready(function(){
    validate_form_custom("#sms");
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
<form method="post" id="sms" name="sms" action="{$CMS->vars['root_domain']}/?site=sms&act=add_do" onSubmit="return check_form(this.id);">

  <section class="add_form main_form">
      <figure class="heading">
        <h3>{$CMS->lang['add_form']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=sms{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>

      <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
          <div class="col-lg-4">
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['sms_from']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <input class="form-control" type="tel" name="sms_from" value="{$data['sms_from']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['sms_incomplete_from']}">
                  </div>
                </div>
                    
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['sms_to']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <input class="form-control" type="tel" name="sms_to" value="{$data['sms_to']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['sms_incomplete_to']}">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="form-control-label"></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <button class="btn" type="submit">{$CMS->lang['add_submit']}</button>
                  </div>
                </div>

          </div> <!--End col-lg-4-->
                    
          <div class="col-lg-8">
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['sms_content']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <textarea class="form-control" rows="6" name="sms_content">{$data['sms_content']}</textarea>
                  </div>
                </div>
          </div>
        </div>
      </figure>
    </section>

</form>
<script language="javascript">
  $(document).ready(function(){
    validate_form_custom("#sms");


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
    <h3>{$CMS->lang["show_form"]}<b>{$data['cat_name']}</b></h3>
      <a href="{$CMS->vars['root_domain']}/?site=sms" title=""><span class="font-icon font-icon-del"></span></a>
  </figure>
    
  <div class="box-typical box-typical box-typical-padding border">
    <div class="row">
      <div class="col-xl-4 col-md-6 col-sm-6 col-xs-12">
        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['cat_name']}</div>
              <div class="form-control-span2">{$data['cat_name']}</div>
          </div>
        </fieldset>

        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['cat_description']}</div>
              <div class="form-control-span2">{$data['cat_description']}</div>
          </div>
        </fieldset>
        
        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['cat_status']}</div>
              <div class="form-control-span2">{$data['cat_status']}</div>
          </div>
        </fieldset>

        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['cat_time']}</div>
              <div class="form-control-span2">{$data['cat_time']}</div>
          </div>
        </fieldset>
      </div>
      <div class="col-xl-4 col-md-6 col-sm-6 col-xs-12">

        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['meta_title']}</div>
              <div class="form-control-span2">{$data['meta_title']}</div>
          </div>
        </fieldset>
        
        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['meta_description']}</div>
              <div class="form-control-span2">{$data['meta_description']}</div>
          </div>
        </fieldset>
        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['meta_keywords']}</div>
              <div class="form-control-span2">{$data['meta_keywords']}</div>
          </div>
        </fieldset>

        

      </div>
    </div>
  </div>
</section>

EOF;
    $btn_control = "";
    $btn_control_mobile = "";

    if($CMS->permit['sms_delete'])
    {
$btn_control .=<<<EOF

      <a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=sms&act=delete&id={$data['cat_id']}');" class="pull-right add_cart_2 hidden-sm-down">{$CMS->lang['delete']}</a>
EOF;

$btn_control_mobile .=<<<EOF

      <li class="hidden-xl-up"><a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=sms&act=delete&id={$data['cat_id']}');" title=""><i class="fa fa-trash-o"></i>{$CMS->lang['delete']}</a></li>
EOF;
    }

    if($CMS->permit['sms_edit'])
    {
$btn_control .=<<<EOF

      <a href="{$CMS->vars['root_domain']}/?site=sms&act=edit&id={$data['cat_id']}" class="pull-right add_cart_2 hidden-sm-down">{$CMS->lang['edit']}</a>
EOF;

$btn_control_mobile .=<<<EOF

      <li class="hidden-xl-up"><a href="{$CMS->vars['root_domain']}/?site=sms&act=edit&id={$data['cat_id']}" title=""><i class="fa fa-edit"></i>{$CMS->lang['edit']}</a></li>  
EOF;
    }

$output .=<<<EOF

<section class="add_cart_footer">
  <a href="{$CMS->vars['root_domain']}/?site=sms" class="pull-left cancel">{$CMS->lang['header_back']}</a>
  {$btn_control}

  <div class="btn-group dropup pull-right hidden-xl-up">
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <i class="fa fa-save"></i>{$CMS->lang['title_action']}
    </button>
    <div class="dropdown-menu">
    <ul>
      {$btn_control_mobile}
    </ul>
    </div>
  </div>


</section>

EOF;
	
	return $output;	
}

}

?>
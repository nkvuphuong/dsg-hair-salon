<?php

class skin_position {

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
      <h3>{$CMS->lang['title_header_listing']}</h3>
      <figure class="pull-right right">
        <a href="{$CMS->vars['root_domain']}/?site=pages" title="" class="add_bill">{$CMS->lang['title_list_pages']}</a>
        <a href="{$CMS->vars['root_domain']}/?site=position&act=add" title="" class="add_bill">{$CMS->lang['position_add_form']}</a>
      </figure>
    </figure>
   

    <section class="add_table"> 
      <form method="post" name="position" id="position" action="{$CMS->vars['root_domain']}/?site=position" onSubmit="return check_form(this.id);">
        <div class="table-responsive">
          <table class="table_cus">
            <thead>
                <tr>
                    <th width="6%" id="order_pos_id">{$CMS->lang['position_id']}</th>
                    <th width="40%" id="order_pos_name">{$CMS->lang['position_name']}</th>
                    <th width="20%" id="order_pos_time">{$CMS->lang['position_time']}</th>
                    <th width="5%" style="text-align:center"></th>
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
  $btn_control = "";
  if($CMS->permit['position_edit'])
  {
    $btn_control .= <<<EOF
      <a href="{$CMS->vars['root_domain']}/?site=position&act=edit&id={$result['pos_id']}" class="edit"><i class="fa fa-edit"></i></a>
EOF;

  }

  if($CMS->permit['position_delete'])
  {
    $btn_control .= <<<EOF
      <a onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=position&act=delete&id={$result['pos_id']}');" class="edit" aria-describedby="ui-id-17"><i class="fa fa-trash"></i></a>
EOF;

  }
$output .= <<<EOF
  <tr bgcolor="{$result['bgcolor']}">
    <td>#{$result['pos_id']}</td>
    <td>{$result['pos_name']}</td>
    <td>{$result['pos_time']}</td>
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
    <td colspan="7">{$CMS->lang['position_no_data']}</td>
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
        </div><!--.box-typical-body-->
      </section><!--.box-typical-->
    {$CMS->position->action_control}
<div class="block_bottom pagination pagination-sm">{$CMS->show_page}</div>
<input type="hidden" name="data_cnt" value="{$CMS->position->record_cnt}">
</form>
<script>image_resize(600,600);</script>
<script language="javascript">rebuild_form("position");</script>
<script language="javascript">arrange_setup("{$CMS->position->arrange_data}");</script>
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
    
$output .= <<<EOF

<section class="add_form main_form">
      <figure class="heading">
        <h3>{$CMS->lang['position_edit_form']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=position{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>

<form method="post" id="position" name="position" action="{$CMS->vars['root_domain']}/?site=position&id={$data['pos_id']}&act=edit_do&page={$CMS->input['page']}" onSubmit="return check_form(this.id);" enctype="multipart/form-data">

 <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
          <div class="col-lg-4">
              <div class="form-group row">
                <label class="form-control-label">{$CMS->lang['position_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <input class="form-control" type="text" name="pos_name" value="{$data['pos_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['position_incomplete_name']}">
                </div>
              </div>
              <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['position_display']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <select class="form-control auto_select" name="pos_status" defaultvalue="{$data['pos_status']}"  data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['status_incomplete']}">
                        {$CMS->vars['display_status']}
                    </select>
                  </div>
              </div>

              <div class="form-group row">
                <label class="form-control-label"></label>
                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <button class="btn" type="submit">{$CMS->lang['position_edit_submit']}</button>
                </div>
              </div>

          </div>
        </div>
      </figure>

</form>
</section>
<script language="javascript">
$(document).ready(function(){
    validate_form_custom("#position");


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

  <section class="add_form main_form">
      <figure class="heading">
        <h3>{$CMS->lang['position_add_form']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=position{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>

<form method="post" id="position" name="position" action="{$CMS->vars['root_domain']}/?site=position&act=add_do" onSubmit="return check_form(this.id);" enctype="multipart/form-data">

    <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
          <div class="col-lg-4">
              <div class="form-group row">
                <label class="form-control-label">{$CMS->lang['position_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <input class="form-control" type="text" name="pos_name" value="{$data['pos_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['position_incomplete_name']}">
                </div>
              </div>
              <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['position_display']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <select class="form-control auto_select" name="pos_status" defaultvalue="{$data['pos_status']}"  data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['status_incomplete']}">
                        {$CMS->vars['display_status']}
                    </select>
                  </div>
              </div>

              <div class="form-group row">
                <label class="form-control-label"></label>
                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <button class="btn" type="submit">{$CMS->lang['position_add_submit']}</button>
                </div>
              </div>

          </div>
        </div>
      </figure>

</form>
</section>

<script language="javascript">
$(document).ready(function(){
    validate_form_custom("#position");


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
 <header class="section-header">
	<div class="tbl">
	  <div class="tbl-row">
		<div class="tbl-cell">
		  <h3>{$CMS->lang['position_header']}</h3>
		</div>
	  </div>
	</div>
</header>
<section class="card">

	<section class="box-typical">
		<header class="box-typical-header">
			<div class="tbl-row">
				<div class="tbl-cell tbl-cell-title">
				  <h3>{$CMS->lang['position_header']}: <strong>{$data['position_name']}</strong></h3>
				</div>
				<div class="tbl-cell tbl-cell-action-bordered">
					<a href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}{$CMS->class->search->url_return}">
					<button type="button" class="action-btn"><i class="fa fa-mail-reply"></i></button>
					</a>
				</div>                                    
			</div>
		</header>
	</section>



	<div class="card-block">

		<h5 class="with-border">{$CMS->lang['position_required_info']}</h5>

		  <div class="form-group row">
			<label class="col-sm-3 form-control-label">{$CMS->lang['position_name']}</label>
			<div class="col-sm-9">
				<p class="form-control-static">
				  {$data['position_name']}
				  &nbsp; <script type="text/javascript">permission_btn("edit", "position", "{$CMS->vars['root_domain']}/?site=position&act=edit&id={$data['data_bk']['position_id']}");</script>
				  &nbsp; <script type="text/javascript">permission_btn("delete", "project", "{$CMS->vars['root_domain']}/?site=position&act=delete&id={$data['data_bk']['position_id']}");</script>
				</p>
			</div>
		  </div>
		  
		  <div class="form-group row">
			  <label class="col-sm-3 form-control-label">{$CMS->lang['position_key']}</label>
			  <div class="col-sm-9">
				  <p class="form-control-static">
					{$data['position_key']}
				  </p>
			  </div>
		  </div>
		  
		  <div class="form-group row">
			  <label class="col-sm-3 form-control-label">{$CMS->lang['position_display_type']}</label>
			  <div class="col-sm-9">
				  <p class="form-control-static">
					{$data['position_display_type']}
				  </p>
			  </div>
		  </div>
		  
		  <div class="form-group row">
			  <label class="col-sm-3 form-control-label">{$CMS->lang['position_width']}</label>
			  <div class="col-sm-9">
				  <p class="form-control-static">
					{$data['position_width']}
				  </p>
			  </div>
		  </div>
		  
		  <div class="form-group row">
			  <label class="col-sm-3 form-control-label">{$CMS->lang['position_height']}</label>
			  <div class="col-sm-9">
				  <p class="form-control-static">
					{$data['position_height']}
				  </p>
			  </div>
		  </div>
		 
		 <h5 class="with-border">{$CMS->lang['position_add_info']}</h5>
		  
		  <div class="form-group row">
			  <label class="col-sm-3 form-control-label">{$CMS->lang['position_time']}</label>
			  <div class="col-sm-9">
				  <p class="form-control-static">
					{$data['position_time']}
				  </p>
			  </div>
		  </div>
	</div><!-- end .card-block-->
</section><!--card --> 


<script language="javascript" src="{$CMS->vars['js_url']}/public.js"></script>

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
<form method="post" id="position" name="position" action="{$CMS->vars['root_domain']}/?site=position&act=search_do" onSubmit="return check_form(this.id);">
<div class="block_wrapper">
<div class="block_top"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=position{$CMS->class->search->url_return}">&laquo; {$CMS->lang['position_header_back']}</a></p>{$CMS->lang['search_form']}</div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
    <td class="left25"><b>{$CMS->lang['position_name']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="position_name" value="{$data['position_name']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['position_website']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="position_website" value="{$data['position_website']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['position_owner']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="position_owner" value="{$data['position_owner']}"></td>
  </tr>
</table>
</div>
</div>
<div class="block_desc_bottom_submit">
	<input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['search_submit']} "> 
</div>
<div class="block_bottom pagination pagination-sm"></div>
</form>
<script language="javascript">rebuild_form("position",1,1);</script>
EOF;
	
	return $output;	
}

}

?>
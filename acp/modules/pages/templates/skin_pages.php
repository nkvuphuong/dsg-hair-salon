<?php

class skin_pages {

//===========================================================================
//  HTML HEADER
//===========================================================================

public function pages_header()
{
	global $CMS, $DB, $member;
	$search_url =str_replace($CMS->class->search->url_return,"",$CMS->class->search->url_return);

	// $list_position = $CMS->position->getPosition();
  // print $CMS->input['pos_id'];exit;
$output = <<<EOF

<script language="javascript">rebuild_form("quick_search",1,1);</script>
<section class="add_table main_form">
    <figure class="heading">
      <h3>{$CMS->lang['mana_page']}</h3>
      <figure class="pull-right right">
        <!--div class="search">
          <form method="post" id="quick_search" action="{$CMS->vars['root_domain']}/?site=pages&act=search_do{$search_url}" style="display:inline-block; width: 200px;">
            <select class="form-control auto_select" name="pos_id" id="pos_id" defaultvalue="{$CMS->input['pos_id']}" onchange="this.form.submit()">
              {$list_position}
            </select>
          </form>
        </div-->
        
EOF;
      if($CMS->permit['pages_add'])
      {
$output .=<<<EOF

        <a href="{$CMS->vars['root_domain']}/?site=pages&act=add" title="" class="add_bill">{$CMS->lang['pages_add_form']}</a>
EOF;
      }

      
$output .=<<<EOF

      </figure>
    </figure>   
    
<form method="post" name="pages" id="pages" action="{$CMS->vars['root_domain']}/?site=pages&page={$CMS->input['page']}" onSubmit="return check_form(this.id);">

        <div class="add_table">
          <div class="table-responsive">
            <table class="table table_cus table-hover">
              <thead>
                <tr>
                <th width="6%" style="text-align:center" id="order_pages_id">{$CMS->lang['pages_id']}</th>
                <th width="25%" id="order_pages_name">{$CMS->lang['pages_name']}</th>
                <th width="20%" id="order_user_name">{$CMS->lang['pages_user_name']}</th>
                <th width="15%">{$CMS->lang['pages_moduless']}Mã trang</th>
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

public function pages_middle($result)
{
	global $CMS, $DB, $member;
	
	$output = "";
  $btn_control = "";
  if($CMS->permit['pages_edit'])
  {
    $btn_control .= <<<EOF
      <a href="{$CMS->vars['root_domain']}/?site=pages&act=edit&id={$result['pages_id']}" class="edit"><i class="fa fa-edit"></i></a>
EOF;

  }

  if($CMS->permit['pages_delete'])
  {
    $btn_control .= <<<EOF
      <a onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=pages&act=delete&id={$result['pages_id']}');" class="edit" aria-describedby="ui-id-17"><i class="fa fa-trash"></i></a>
EOF;

  }
  // bgcolor="{$result['bgcolor']}"
$output .= <<<EOF
  <tr>
    <td>#{$result['pages_id']}</td>
    <td>{$result['pages_name']}</td>
    <td>{$result['user_name']}</td>
    <td>{$result['pages_key']}</td>
    <td align="center">{$btn_control}</td>
  </tr>
EOF;
	
    return $output;
}

//===========================================================================
//  NO DATA
//===========================================================================

public function pages_none()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
  <tr>
    <td colspan="8">{$CMS->lang['pages_no_data']}</td>
  </tr>
EOF;

	return $output;
}

//===========================================================================
//  HTML FOOTER
//===========================================================================

public function pages_footer()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
  
              </tbody>
            </table>
        </div>
      </div>
    </section><!--.box-typical-->
EOF;
      if($CMS->show_page)
      {
$output .=<<<EOF

  <div class="fuction_table">

      <nav class="pull-right">
         {$CMS->show_page}
      </nav>
   </div>
EOF;
      }
$output .=<<<EOF

</form>
EOF;

	return $output;
}

//===========================================================================
//  USER CONTROL
//===========================================================================

public function pages_control()
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
	
    $list_position = $CMS->position->getPosition();

$output .= <<<EOF

<section class="add_form main_form">
      <figure class="heading">
        <h3>{$CMS->lang['pages_edit_form']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=pages{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>

<form method="post" id="pages" name="pages" action="{$CMS->vars['root_domain']}/?site=pages&id={$data['pages_id']}&act=edit_do&pages={$CMS->input['pages']}" onSubmit="return check_form(this.id);">

      <section class="tabs-section">
        <div class="tabs-section-nav tabs-section-nav-inline">
          <ul class="nav" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" href="#tabs-4-tab-1" role="tab" data-toggle="tab">
                Information
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#tabs-4-tab-2" role="tab" data-toggle="tab">
                SEO
              </a>
            </li>
          </ul>
        </div><!--.tabs-section-nav-->

        <div class="tab-content" style="margin-bottom: 10px;">
          <div role="tabpanel" class="tab-pane fade in active" id="tabs-4-tab-1">
                <figure class="box-typical-padding border">
                  <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['pages_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <input class="form-control" type="text" name="pages_name" value="{$data['pages_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['pages_incomplete_name']}">
                          </div>
                        </div>
                    <div class="row">
                      <div class="col-lg-4">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['pages_station']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <select class="form-control auto_select" defaultvalue="{$data['pos_id']}" name="pos_id" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['pages_incomplete_station']}">
                              {$list_position}
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="col-lg-4">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['pages_module']}</label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <select class="form-control auto_select" defaultvalue="{$data['pages_module']}" name="pages_module">
                              <option value="">{$CMS->lang['pages_module_']}</option>
                              <option value="1">{$CMS->lang['pages_module_1']}</option>
                              <option value="2">{$CMS->lang['pages_module_2']}</option>
                              <option value="3">{$CMS->lang['pages_module_3']}</option>  
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="col-lg-4">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['pages_key']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <input class="form-control auto_select" type="text" name="pages_key" value="{$data['pages_key']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['pages_incomplete_key']}">
                          </div>
                        </div>
                      </div>
                    </div>
                    </div><!--End col-lg-6-->
                    
                    <div class="col-lg-9">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['pages_content']}</label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <textarea class="form-control editor_texarea" name="pages_content" cols="60" rows="5">{$data['pages_content']}</textarea> 
                          </div>
                        </div>
                    </div>
                    
                  </div>
                </figure> 
          </div><!--.tab-pane-->



          <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-2">
              <figure class="box-typical-padding border">
                <div class="row">
                  <div class="col-lg-6">

                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['meta_title']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <textarea class="form-control" name="meta_title" cols="60" rows="5" >{$data['meta_title']}</textarea>
                        </div>
                      </div>

                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['meta_description']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <textarea class="form-control" name="meta_description" cols="60" rows="5" >{$data['meta_description']}</textarea>
                        </div>
                      </div>


                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['meta_keywords']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <textarea class="form-control" name="meta_keywords" cols="60" rows="5" >{$data['meta_keywords']}</textarea>
                        </div>
                      </div>

                    </div>
                </div>
              </figure>

          </div><!--.tab-pane-->
          
        </div><!--.tab-content-->
      </section><!--.tabs-section-->
      <div class="form-group row">
        <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
           <button class="btn btn-rounded" type="submit" >{$CMS->lang['pages_edit_submit']}</button>
        </div>
      </div>



       
</form>
</section>
<script language="javascript">
  $(document).ready(function(){
    validate_form_custom("#pages");


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
    $list_position = $CMS->position->getPosition();

$output .= <<<EOF

  <section class="add_form main_form">
      <figure class="heading">
        <h3>{$CMS->lang['pages_add_form']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=pages{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>
    <form method="post" id="pages" name="pages" action="{$CMS->vars['root_domain']}/?site=pages&act=add_do" onSubmit="return check_form(this.id);">
      <section class="tabs-section">
        <div class="tabs-section-nav tabs-section-nav-inline">
          <ul class="nav" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" href="#tabs-4-tab-1" role="tab" data-toggle="tab">
                Information
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#tabs-4-tab-2" role="tab" data-toggle="tab">
                SEO
              </a>
            </li>
          </ul>
        </div><!--.tabs-section-nav-->

        <div class="tab-content" style="margin-bottom: 10px;">
          <div role="tabpanel" class="tab-pane fade in active" id="tabs-4-tab-1">
                <figure class="box-typical-padding border">
                  <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['pages_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <input class="form-control" type="text" name="pages_name" value="{$data['pages_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['pages_incomplete_name']}">
                          </div>
                        </div>
                    <div class="row">
                      <div class="col-lg-4">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['pages_station']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <select class="form-control auto_select" defaultvalue="{$data['pos_id']}" name="pos_id" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['pages_incomplete_station']}">
                              {$list_position}
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="col-lg-4">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['pages_module']}</label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <select class="form-control auto_select" defaultvalue="{$data['pages_module']}" name="pages_module">
                              <option value="">{$CMS->lang['pages_module_']}</option>
                              <option value="1">{$CMS->lang['pages_module_1']}</option>
                              <option value="2">{$CMS->lang['pages_module_2']}</option>
                              <option value="3">{$CMS->lang['pages_module_3']}</option>  
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="col-lg-4">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['pages_key']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <input class="form-control auto_select" type="text" name="pages_key" value="{$data['pages_key']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['pages_incomplete_key']}">
                          </div>
                        </div>
                      </div>
                    </div>
                    </div><!--End col-lg-6-->
                    
                    <div class="col-lg-9">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['pages_content']}</label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <textarea class="form-control editor_texarea" name="pages_content" cols="60" rows="5">{$data['pages_content']}</textarea> 
                          </div>
                        </div>
                    </div>
                    
                  </div>
                </figure> 
          </div><!--.tab-pane-->



          <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-2">
              <figure class="box-typical-padding border">
                <div class="row">
                  <div class="col-lg-6">

                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['meta_title']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <textarea class="form-control" name="meta_title" cols="60" rows="5" >{$data['meta_title']}</textarea>
                        </div>
                      </div>

                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['meta_description']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <textarea class="form-control" name="meta_description" cols="60" rows="5" >{$data['meta_description']}</textarea>
                        </div>
                      </div>


                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['meta_keywords']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <textarea class="form-control" name="meta_keywords" cols="60" rows="5" >{$data['meta_keywords']}</textarea>
                        </div>
                      </div>

                    </div>
                </div>
              </figure>

          </div><!--.tab-pane-->
          
        </div><!--.tab-content-->
      </section><!--.tabs-section-->
      <div class="form-group row">
        <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
           <button class="btn btn-rounded" type="submit" >{$CMS->lang['pages_edit_submit']}</button>
        </div>
      </div>  

</form>
</section>
<script language="javascript">
  $(document).ready(function(){
    validate_form_custom("#pages");


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
    <h3>{$CMS->lang["pages_show_form"]}<b>{$data['pages_name']}</b></h3>
      <a href="{$CMS->vars['root_domain']}/?site=pages" title=""><span class="font-icon font-icon-del"></span></a>
  </figure>
    
  <div class="box-typical box-typical-padding border">
    <div class="row">
      <div class="col-xl-4 col-md-6 col-sm-6 col-xs-12">
        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['pages_name']}</div>
              <div class="form-control-span2">{$data['pages_name']}</div>
          </div>
        </fieldset>

        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['pages_station']}</div>
              <div class="form-control-span2">{$data['pos_id']}</div>
          </div>
        </fieldset>
        
        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['pages_module']}</div>
              <div class="form-control-span2">{$data['pages_module_bk']}</div>
          </div>
        </fieldset>

        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['pages_content']}</div>
              <div class="form-control-span2">{$data['pages_content']}</div>
          </div>
        </fieldset>

        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['pages_user_name']}</div>
              <div class="form-control-span2">{$data['user_name']}</div>
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

        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['pages_time']}</div>
              <div class="form-control-span2">{$data['pages_time']}</div>
          </div>
        </fieldset>

        <fieldset class="row">
          <div class="form-control-label2" >
              <div class="title_label title-label-125 pull-left">{$CMS->lang['pages_time_update']}</div>
              <div class="form-control-span2">{$data['pages_time_update']}</div>
          </div>
        </fieldset>

        

      </div>
    </div>
  </div>
</section>

EOF;
    $btn_control = "";
    $btn_control_mobile = "";

    if($CMS->permit['pages_delete'])
    {
$btn_control .=<<<EOF

      <a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=pages&act=delete&id={$data['pages_id']}');" class="pull-right add_cart_2 hidden-sm-down">{$CMS->lang['delete']}</a>
EOF;

$btn_control_mobile .=<<<EOF

      <li class="hidden-xl-up"><a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=pages&act=delete&id={$data['pages_id']}');" title=""><i class="fa fa-trash-o"></i>{$CMS->lang['delete']}</a></li>
EOF;
    }

    if($CMS->permit['pages_edit'])
    {
$btn_control .=<<<EOF

      <a href="{$CMS->vars['root_domain']}/?site=pages&act=edit&id={$data['pages_id']}" class="pull-right add_cart_2 hidden-sm-down">{$CMS->lang['edit']}</a>
EOF;

$btn_control_mobile .=<<<EOF

      <li class="hidden-xl-up"><a href="{$CMS->vars['root_domain']}/?site=pages&act=edit&id={$data['pages_id']}" title=""><i class="fa fa-edit"></i>{$CMS->lang['edit']}</a></li>  
EOF;
    }

$output .=<<<EOF

<section class="add_cart_footer">
  <a href="{$CMS->vars['root_domain']}/?site=pages" class="pull-left cancel">{$CMS->lang['pages_header_back']}</a>
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

//===========================================================================
//  HTML SEARCH
//===========================================================================

public function search($data)
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
<form method="post" id="pages" name="pages" action="{$CMS->vars['root_domain']}/?site=pages&act=search_do" onSubmit="return check_form(this.id);">
<div class="block_wrapper">
<div class="block_top"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=pages{$CMS->class->search->url_return}">&laquo; {$CMS->lang['pages_header_back']}</a></p>{$CMS->lang['search_form']}</div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
    <td class="left25"><b>{$CMS->lang['pages_name']}</b></td>
    <td><input class="input_text" size="45" type="text" name="pages_name" value="{$data['pages_name']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['pages_content']}</b></td>
    <td><textarea class="input_text" name="pages_content" cols="60" rows="5">{$data['pages_content']}</textarea></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['pages_time']}</b></td>
    <td>{$CMS->lang['search_from']} <input class="input_text" size="25" type="text" name="pages_time" value="{$data['pages_time']}" etype="date"> &nbsp; {$CMS->lang['search_to']} <input class="input_text" size="25" type="text" name="pages_time_to" value="{$data['pages_time_to']}" etype="date"></td>
  </tr>
</table>
</div>
</div>
<div class="block_desc_bottom_submit">
  <input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['search_submit']} "> 
</div>
<div class="block_bottom pagination pagination-sm"></div>
</form>
<script language="javascript">rebuild_form("pages",1,1);</script>
EOF;
	
	return $output;	
}

}

?>
<?php

class skin_group {

public function group()
{
	global $CMS, $DB, $member;
	
	$output = "";
	
$output .= <<<EOF
<header class="section-header">
        <div class="tbl">
          <div class="tbl-row">
            <div class="tbl-cell">
              <h3>Quản lý nhóm</h3>
            </div>
          </div>
        </div>
      </header>
<section class="box-typical">
        <header class="box-typical-header">
          <div class="tbl-row">
            <div class="tbl-cell tbl-cell-title">
              <h3>Danh sách nhóm</h3>
            </div>            

            <div class="tbl-cell tbl-cell-action-bordered">
               <a href="{$CMS->vars['root_domain']}/?site=group&act=add">
                <button type="button" class="action-btn"><i class="fa fa-plus-circle"></i></button>

                </a>
            </div>
         
          </div>
        </header>
        <div class="box-typical-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th width="25%">{$CMS->lang['group_name']}</th>
                  <th width="25%">{$CMS->lang['group_email']}</th>
                  <th width="10%" style="text-align:center">{$CMS->lang['group_is_root']}</th>
                  <th width="10%" style="text-align:center">{$CMS->lang['group_is_admin']}</th>
                  <th width="12%" style="text-align:center">{$CMS->lang['group_users']}</th>
                  <th width="5%" style="text-align:center">{$CMS->lang['edit']}</th>
                  <th width="5%" style="text-align:center">{$CMS->lang['delete']}</th>
                </tr>
              </thead>
              <tbody>

EOF;

  $tick[0] = "<img src='{$CMS->vars['img_url']}/icon_cancel.png'>";
    $tick[1] = "<img src='{$CMS->vars['img_url']}/icon_tick.png'>";

  $sql = $DB->query("SELECT * FROM ".root_table."user_group WHERE userg_deleted=0 ORDER BY userg_is_root DESC, userg_is_admin DESC, userg_title ASC");

if ( $DB->num_rows( $sql ) > 0 )
{
  while( $result = $DB->fetch_array( $sql ) )
  {
    $result['user'] = $DB->num_rows($DB->query("SELECT * FROM ".root_table."user WHERE userg_id={$result['userg_id']} AND user_deleted=0 AND user_status=1"));

$output .= <<<EOF
  <tr>
    <td><a href="{$CMS->vars['root_domain']}/?site=user&group={$result['userg_id']}">{$result['userg_prefix_html']}{$result['userg_title']}{$result['userg_suffix_html']}</a></td>
    <td>{$result['userg_email']}</th>
  <td style="text-align:center"><script type="text/javascript">permission_text("group_read", "{$tick[$result['userg_is_root']]}", "{$result['userg_is_root']}", "{$result['userg_is_admin']}");</script></td>
    <td style="text-align:center"><script type="text/javascript">permission_text("group_read", "{$tick[$result['userg_is_admin']]}", "{$result['userg_is_root']}", "{$result['userg_is_admin']}");</script></td>
    <td style="text-align:center"><script type="text/javascript">permission_text("group_read", "{$result['user']}", "{$result['userg_is_root']}", "{$result['userg_is_admin']}");</script></td>
    <td align="center" style="text-align:center"><script type="text/javascript">permission_btn("edit", "group", "{$CMS->vars['root_domain']}/?site=group&act=edit&id={$result['userg_id']}", "{$result['userg_is_root']}", "{$result['userg_is_admin']}");</script></td>
    <td align="center" style="text-align:center"><script type="text/javascript">permission_btn("delete", "group", "{$CMS->vars['root_domain']}/?site=group&act=delete&id={$result['userg_id']}", "{$result['userg_is_root']}", "{$result['userg_is_admin']}");</script></td>
  </tr>
EOF;

  }
}
else
{

$output .= <<<EOF
  <tr>
    <td colspan="9">{$CMS->lang['no_data']}</td>
  </tr>
EOF;

}

$output .= <<<EOF
              </tbody>
            </table>
          </div>
        </div><!--.box-typical-body-->
      </section><!--.box-typical-->
EOF;
	return $output;
}

public function edit_group( $data )
{
	global $CMS, $DB, $member;
	
	$output = "";
	
    if ( $CMS->user->check_permission( $CMS->vars['is_root'], $CMS->vars['is_admin'], 1 ) == false )
    {
    	$escalate_code = "style='display: none;'";
    }
    
$output .= <<<EOF
<script type="text/javascript" src="{$CMS->vars['js_url']}/acp_user.js"></script>
<form method="post" id="group" name="group" onSubmit="return check_form(this.id);" action="{$CMS->vars['root_domain']}/?site=group&act=edit_do&id={$data['userg_id']}">
<header class="section-header">
        <div class="tbl">
          <div class="tbl-row">
            <div class="tbl-cell">
              <h3>Chỉnh sửa nhóm</h3>              
            </div>
          </div>
        </div>
      </header>
 <section class="card">

      <section class="box-typical">
        <header class="box-typical-header">
            <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title">
                  <h3>{$CMS->lang['group_edit']} {$data['userg_title']}</h3>
                </div>
                <div class="tbl-cell tbl-cell-action-bordered">
                    <a href="{$CMS->vars['root_domain']}/?site=user{$CMS->class->search->url_return}">
                    <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                    </a>
                </div>
                 
            </div>
        </header>
	  </section>



        <div class="card-block">  
            <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['group_name']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                  <input class="form-control" size="30" type="text" name="userg_name" id="userg_name" emsg="{$CMS->lang['incomplete_name']}" value="{$data['userg_title']}">
                  </p>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['group_email']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                  <input class="form-control" size="30" type="text" name="userg_email" id="userg_email" emsg="{$CMS->lang['incomplete_email']}" value="{$data['userg_email']}">
                   </p>  
                </div>
              </div>


               <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['group_is_root']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                  <p class="form-control-static">
                  	<span class="rad_box"> 
	                    <input class="member_type_0" type="radio" name="userg_is_root" defaultvalue="{$data['userg_is_root']}" value="1"> {$CMS->lang['yes']}
                    </span>
                    <span class="rad_box">     
                         <input class="member_type_0" type="radio" name="userg_is_root" defaultvalue="{$data['userg_is_root']}" value="0" checked="checked"> {$CMS->lang['no']}
					</span>                         
                  </p>            
                </div>
              </div>

              <div class="form-group row" style="margin-bottom:1em">
                <label class="col-sm-3 form-control-label">{$CMS->lang['group_is_admin']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static">
                	<span class="rad_box"> 
                		<input class="member_type_0" type="radio"  name="userg_is_admin" defaultvalue="{$data['userg_is_admin']}" value="1"> {$CMS->lang['yes']}
                    </span>
                    <span class="rad_box">     
                        <input class="member_type_0" type="radio" name="userg_is_admin" defaultvalue="{$data['userg_is_admin']}" value="0" checked="checked"> {$CMS->lang['no']}
					</span>                        
                  </p>
                </div>
              </div>


              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['group_prefix']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                <input class="form-control" size="50" type="text" name="userg_prefix_html" value="{$data['userg_prefix_html']}">
                  </p>
                </div>
              </div>


              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['group_suffix']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                  <p class="form-control-static-input">
                    <input class="form-control" size="50" type="text" name="userg_suffix_html" value="{$data['userg_suffix_html']}">                   
                  </p>
                </div>
              </div>





              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['group_permission']}</label>
                <div class="col-sm-9">
                  <figure class="table-responsive" style="margin-top:-5px">
<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
    <td width="100%">
            <a class="btn btn-success" onclick="setAllPermission();">{$CMS->lang['full_permission']}</a>
            <a class="btn btn-success" onclick="unsetAllPermission();">{$CMS->lang['clear_permission']}</a>
            <a class="btn btn-success" onclick="revertPermission();">{$CMS->lang['revert_permission']}</a>
EOF;

    $output .= $CMS->user->show_permission($data['userg_permission']);
    
$output .= <<<EOF

    </td>
    </tr>
  </table>

                	</figure>
              </div>
            </div>

            <div class="form-group row" style="margin:10px -.9375rem 0px -.9375rem">
              <label class="col-sm-3 form-control-label">Quyền hạn Thành viên</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                  <div class="checkbox">
                      <input class="form-control" type="checkbox" name="is_update" value="1" />
                      <label for="is_update" style="font-size:14px">{$CMS->lang['update_permission']}</label>
                  </div>
              </div>
            </div>


             <div class="form-group row">
              <label class="col-sm-3 form-control-label"></label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static">
                 <input class="btn btn-rounded" type="submit" name="submit" value=" {$CMS->lang['group_edit_submit']} "> 
                </p>
              </div>
            </div>

        </div><!-- end card-block -->
  </section><!-- end section card -->

 



</form>
<script language="javascript">rebuild_form("group");</script>
EOF;
	
	return $output;	
}


public function add_group()
{
	global $CMS, $DB, $member;
	
	$output = "";
	
    if ( $CMS->user->check_permission( $CMS->vars['is_root'], $CMS->vars['is_admin'], 1 ) == false )
    {
    	$escalate_code = "style='display: none;'";
    }
    
    $CMS->input['userg_prefix_html'] = $CMS->input['userg_prefix_html'] ? $CMS->input['userg_prefix_html'] : "<font style='color:black'><b>";
    $CMS->input['userg_suffix_html'] =  $CMS->input['userg_suffix_html'] ?  $CMS->input['userg_suffix_html'] : "</b></font>";
    
$output .= <<<EOF
<script type="text/javascript" src="{$CMS->vars['js_url']}/acp_user.js"></script>
<form method="post" id="group" name="group" onSubmit="return check_form(this.id);" action="{$CMS->vars['root_domain']}/?site=group&act=add_do">
<header class="section-header">
        <div class="tbl">
          <div class="tbl-row">
            <div class="tbl-cell">
              <h3>Thêm nhóm mới</h3>              
            </div>
          </div>
        </div>
      </header>
 <section class="card">

      <section class="box-typical">
        <header class="box-typical-header">
            <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title">
                  <h3>{$CMS->lang['group_add']}</h3>
                </div>
                <div class="tbl-cell tbl-cell-action-bordered">
                    <a href="{$CMS->vars['root_domain']}/?site=group{$CMS->class->search->url_return}">
                    <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                    </a>
                </div>
                 
            </div>
        </header>
	  </section>



        <div class="card-block">  
            <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['group_name']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">  
                  <input class="form-control" size="30" type="text" name="userg_name" id="userg_name" emsg="{$CMS->lang['incomplete_name']}" value="{$CMS->input['userg_name']}">
                  </p>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['group_email']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">  
                  <input class="form-control" size="30" type="text" name="userg_email" id="userg_email" emsg="{$CMS->lang['incomplete_email']}" value="{$CMS->input['userg_email']}">
                   </p>  
                </div>
              </div>


               <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['group_is_root']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                  <p class="form-control-static"> 
                  	<span class="rad_box"> 
                    	<input class="member_type_0" type="radio" name="userg_is_root" id="userg_is_root_1" value="1"> {$CMS->lang['yes']}
                    </span>
                    <span class="rad_box">
                    	<input class="member_type_0" type="radio" name="userg_is_root" id="userg_is_root_0" value="0"  checked="checked"> {$CMS->lang['no']}
                    </span>    
                  </p>            
                </div>
              </div>



              <div class="form-group row" style="margin-bottom:1em">
                <label class="col-sm-3 form-control-label">{$CMS->lang['group_is_admin']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static"> 
                	<span class="rad_box"> 
                		<input class="member_type_0" type="radio" name="userg_is_admin" id="userg_is_admin_1" value="1"> {$CMS->lang['yes']}
                    </span>
                    <span class="rad_box">    
                        <input class="member_type_0" type="radio" name="userg_is_admin" id="userg_is_admin_0" value="0"  checked="checked"> {$CMS->lang['no']}
                    </span>    
                  </p>
                </div>
              </div>


              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['group_prefix']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">  
                <input class="form-control" size="50" type="text" name="userg_prefix_html" value="{$CMS->input['userg_prefix_html']}">
                  </p>
                </div>
              </div>


              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['group_suffix']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                  <p class="form-control-static-input">  
                    <input class="form-control" size="50" type="text" name="userg_suffix_html" value="{$CMS->input['userg_suffix_html']}">              
                  </p>
                </div>
              </div>


              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['group_permission']}</label>
                <div class="col-sm-9">
<figure class="table-responsive" style="margin-top:-5px">
<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td width="100%">
            <a class="btn btn-success" onclick="setAllPermission();">{$CMS->lang['full_permission']}</a>
            <a class="btn btn-success" onclick="unsetAllPermission();">{$CMS->lang['clear_permission']}</a>
            <a class="btn btn-success" onclick="revertPermission();">{$CMS->lang['revert_permission']}</a>
EOF;

    $output .= $CMS->user->show_permission();
    
$output .= <<<EOF
        </td>
    </tr>
  </table>
</figure>
              </div>
            </div> 


            <div class="form-group row">
              <label class="col-sm-3 form-control-label"></label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static">
                 <input class="btn btn-rounded" type="submit" name="submit" value=" {$CMS->lang['group_add_submit']} ">
                </p>
              </div>
            </div>

        </div><!-- end card-block -->
  </section><!-- end section card -->

  
</form>
<script language="javascript">rebuild_form("group");</script>
EOF;
	
	return $output;	
}

}

?>
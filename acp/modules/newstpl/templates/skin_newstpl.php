<?php

class skin_newstpl {

//===========================================================================
//  HTML HEADER
//===========================================================================

public function newstpl_header()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF

<header class="section-header">
        <div class="tbl">
          <div class="tbl-row">
            <div class="tbl-cell">
              <h3>Quản lý mẫu tin</h3>
            </div>
          </div>
        </div>
      </header>

<form method="post" name="newstpl" id="newstpl" action="{$CMS->vars['root_domain']}/?site=newstpl" onSubmit="return check_form(this.id);">



<section class="box-typical">
        <header class="box-typical-header">
          <div class="tbl-row">
            <div class="tbl-cell tbl-cell-title">
              <h3>Danh mục mẫu tin</h3>
            </div>

            <div class="tbl-cell tbl-cell-action-bordered">

            <a href="{$CMS->vars['root_domain']}/?site=newstpl&act=search"><i class="fa fa-search"></i></a>

            </div>        

            <div class="tbl-cell tbl-cell-action-bordered" act="delete_all" id="font-icon-trash">

            <i class="fa fa-trash-o"></i>

            </div>

            <div class="tbl-cell tbl-cell-action-bordered">
              <a href="{$CMS->vars['root_domain']}/?site=newstpl&act=add">
                <button type="button" class="action-btn"><i class="fa fa-plus-circle"></i></button>
              </a>


            </div>
         <input type="hidden"  name="act"  />
          </div>
        </header>

<script>
  

        $("#font-icon-trash").click(function(){

            var action = $(this).attr("act");  
            $("#act").val(action);
          
             $("#newstpl").submit();

        });
</script>

        <div class="box-typical-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>
                  <div class="checkbox checkbox-only" onclick="javascript:form_checkall('newstpl');" id="checkall">
                   <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
                   <label for="id_{$result['record_cnt']}"></label>
                    </div>
                  </th> 
                  <th width="5%" id="order_newstpl_id">{$CMS->lang['newstpl_id']}</th>
                  <th width="40%" id="order_newstpl_name">{$CMS->lang['newstpl_name']}</th> 
                  <th width="20%"  style="text-align:center" id="order_newstpl_display">{$CMS->lang['newstpl_display']}</th>
                  <th width="30%"  style="text-align:center" id="order_newstpl_time">{$CMS->lang['newstpl_time']}</th>
                  <th width="5%" style="text-align:center">{$CMS->lang['edit']}</th>
                  <th width="5%" style="text-align:center">{$CMS->lang['delete']}</th>

                </tr>
              </thead>
              <tbody>
EOF;

	return $output;
}

//===========================================================================
//  HTML MIDDLE
//===========================================================================

public function newstpl_middle($result)
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
   <tr bgcolor="{$result['bgcolor']}">
    

    <td class="table-check">
                    <div class="checkbox checkbox-only">
                      <input type="checkbox"  name="id_{$result['record_cnt']}" id="id_{$result['record_cnt']}" value="{$result['newstpl_id']}"/>
                      <label for="id_{$result['record_cnt']}"></label>
                    </div>
    </td>
    <td>#{$result['newstpl_id']}</td>
    <td>{$result['newstpl_name_bk']}</td> 
    <td style="text-align:center">{$result['newstpl_display_bk']}</td>
    <td style="text-align:center">{$result['newstpl_time_bk']}</td>
    <td align="center" style="text-align:center"><script type="text/javascript">permission_btn("edit", "newstpl", "{$CMS->vars['root_domain']}/?site=newstpl&act=edit&id={$result['newstpl_id']}");</script></td>
    <td align="center" style="text-align:center"><script type="text/javascript">permission_btn("delete", "newstpl", "{$CMS->vars['root_domain']}/?site=newstpl&act=delete&id={$result['newstpl_id']}");</script></td>
  </tr>
EOF;
	
    return $output;
}

//===========================================================================
//  NO DATA
//===========================================================================

public function newstpl_none()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
  <tr>
    <td colspan="11">{$CMS->lang['newstpl_no_data']}</td>
  </tr>
EOF;

	return $output;
}

//===========================================================================
//  HTML FOOTER
//===========================================================================

public function newstpl_footer()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
              </tbody>
            </table>
          </div>
        </div><!--.box-typical-body-->
      </section><!--.box-typical-->
<div class="block_bottom pagination pagination-sm">{$CMS->show_page}</div>
<input type="hidden" name="data_cnt" value="{$CMS->newstpl->record_cnt}">
 </form>     

<script language="javascript">rebuild_form("newstpl");</script>
<script language="javascript">arrange_setup("{$CMS->newstpl->arrange_data}");</script>
EOF;

	return $output;
}

//===========================================================================
//  USER CONTROL
//===========================================================================

public function newstpl_control()
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

    $output = $CMS->class->editor->simple();

$output .= <<<EOF

<header class="section-header">
        <div class="tbl">
          <div class="tbl-row">
            <div class="tbl-cell">
              <h3>Chỉnh sửa mẫu tin</h3>              
            </div>
          </div>
        </div>
      </header>

<form method="post" id="newstpl" name="newstpl" action="{$CMS->vars['root_domain']}/?site=newstpl&id={$data['newstpl_id']}&act=edit_do&page={$CMS->input['page']}" onSubmit="return check_form(this.id);" enctype="multipart/form-data">


 <section class="card">

      <section class="box-typical">
                    <header class="box-typical-header">
                                <div class="tbl-row">
                                    <div class="tbl-cell tbl-cell-title">
                                        <h3>{$CMS->lang['newstpl_edit_form']}<strong> {$data['newstpl_name']}</strong></h3>
                                    </div>
                                    <div class="tbl-cell tbl-cell-action-bordered">
                                        <a href="{$CMS->vars['root_domain']}/?site=newstpl{$CMS->class->search->url_return}">
                                        <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                                        </a>
                                    </div>
                                     
                                </div>
                            </header>
           </section>



        <div class="card-block">
          <h5 class="with-border">{$CMS->lang['newstpl_required_info']}</h5>
          <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['newstpl_name']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">   
              <p class="form-control-static-input">
                <input class="form-control" size="45" type="text" name="newstpl_name" value="{$data['newstpl_name']}" defaultvalue="{$data['newstpl_name']}" emsg="{$CMS->lang['newstpl_incomplete_name']}">
              </p>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['newstpl_content']}</label>
              <div class="col-sm-9">
              <p class="form-control-static-input dislpay-none">
                  <textarea class="form-control input_textarea" size="45" rows="10" type="text" name="newstpl_content">{$data['newstpl_content']}</textarea>
              </p>
              </div>
            </div>


             <div class="form-group row">
             <p class="form-control-static">
              <label class="col-sm-3 form-control-label">{$CMS->lang['upload_file']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
EOF;

            $output .= $CMS->attach->form("newstpl", "{$data['newstpl_id']}");
            
          $output .= <<<EOF
            <div class="clr"></div>
            <p class='note_post'>{$CMS->lang["note_ext_image"]}</p>
            </p>
              </div>
            </div>


            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['newstpl_image']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
              <input class="form-control" type="file" name="file_upload" size="30" accept="jpg|jpeg|png"  maxlength="1"/>
        <br />
EOF;
      if($data['newstpl_image'] != "")
            {
            $output .= <<<EOF
        <img src="{$CMS->vars['upload_url']}/newstpl/{$data['newstpl_image']}" />
EOF;
      }
           $output .= <<<EOF
              </p>
              </div>
            </div>

          <h5 class="with-border">{$CMS->lang['newstpl_add_info']}</h5>


             <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['newstpl_display']}</label>
              <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                <select class="select2" name="newstpl_display" defaultvalue="{$data['newstpl_display']}" emsg="{$CMS->lang['incomplete_display']}">{$CMS->vars['display_status']}</select>
              </p>
              </div>
            </div>


            <div class="form-group row">
              <label class="col-sm-3 form-control-label"></label>
              <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                <input class="btn btn-rounded" type="submit" name="submit" value=" {$CMS->lang['newstpl_edit_submit']} "> 
              </p>
              </div>
            </div>


      </div><!-- end card-block -->
  </section><!-- end section card -->


             

</form>
<script language="javascript">rebuild_form("newstpl",1);</script>

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

<header class="section-header">
        <div class="tbl">
          <div class="tbl-row">
            <div class="tbl-cell">
              <h3>Thêm mẫu tin</h3>              
            </div>
          </div>
        </div>
      </header>

<form method="post" id="newstpl" name="newstpl" action="{$CMS->vars['root_domain']}/?site=newstpl&act=add_do" onSubmit="return check_form(this.id);" enctype="multipart/form-data">

 <section class="card">

      <section class="box-typical">
                    <header class="box-typical-header">
                                <div class="tbl-row">
                                    <div class="tbl-cell tbl-cell-title">
                                        <h3>{$CMS->lang['newstpl_add_form']}</h3>
                                    </div>
                                    <div class="tbl-cell tbl-cell-action-bordered">
                                        <a href="{$CMS->vars['root_domain']}/?site=newstpl{$CMS->class->search->url_return}">
                                        <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                                        </a>
                                    </div>
                                     
                                </div>
                            </header>
           </section>

        <div class="card-block">
          <h5 class="with-border">{$CMS->lang['newstpl_required_info']}</h5>
          <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['newstpl_name']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                <input class="form-control" size="45" type="text" name="newstpl_name" value="{$data['newstpl_name']}" emsg="{$CMS->lang['newstpl_incomplete_name']}"> 
              </p>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['newstpl_content']}</label>
              <div class="col-sm-9">
              <p class="form-control-static-input dislpay-none">
                  <textarea class="form-control input_textarea" size="45" rows="10" type="text" name="newstpl_content">{$data['newstpl_content']}</textarea>
              </p>
              </div>
            </div>


             <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['upload_file']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              <p class="form-control-static">
EOF;

            $output .= $CMS->attach->form("newstpl", "{$data['newstpl_id']}");
            
          $output .= <<<EOF
            <div class="clr"></div>
            <p class='note_post'>{$CMS->lang["note_ext_image"]}</p>
            </p>
              </div>
            </div>


            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['newstpl_image']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
             <input class="form-control" type="file" name="file_upload" size="30" accept="jpg|jpeg|png"  maxlength="1"/>
              </p>
              </div>
            </div>

          <h5 class="with-border">{$CMS->lang['newstpl_add_info']}</h5>


             <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['newstpl_display']}</label>
              <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                <select class="select2" name="newstpl_display" defaultvalue="{$data['newstpl_display']}" emsg="{$CMS->lang['incomplete_display']}">{$CMS->vars['display_status']}</select>
              </p>
              </div>
            </div>
            
            <div class="form-group row">
              <label class="col-sm-3 form-control-label"></label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                <input class="btn btn-rounded" type="submit" name="submit" value=" {$CMS->lang['newstpl_add_submit']} ">
              </p>
              </div>
            </div>

      </div><!-- end card-block -->
  </section><!-- end section card -->


             



</form>
<script language="javascript">rebuild_form("newstpl",1);</script>



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
              <h3>Hiển thị mẫu tin</h3>        
            </div>
          </div>
        </div>
      </header>
 <section class="card">

	<section class="box-typical">
        <header class="box-typical-header">
            <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title">
                    <h3>{$CMS->lang['newstpl_show_form']} <strong>{$data['newstpl_name']}</strong></h3>
                </div>
                <div class="tbl-cell tbl-cell-action-bordered">
                    <a href="{$CMS->vars['root_domain']}/?site=newstpl{$CMS->class->search->url_return}">
                    <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                    </a>
                </div>
                 
            </div>
        </header>
	</section>



        <div class="card-block">
          <h5 class="with-border">{$CMS->lang['newstpl_required_info']}</h5>
          <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['newstpl_name']}</label>
              <div class="col-sm-9">  
              	<p class="form-control-static"> 
                {$data['newstpl_name']}
                &nbsp; <script type="text/javascript">permission_btn("edit", "newstpl", "{$CMS->vars['root_domain']}/?site=newstpl&act=edit&id={$data['newstpl_id']}");</script>
                 &nbsp; <script type="text/javascript">permission_btn("delete", "newstpl", "{$CMS->vars['root_domain']}/?site=newstpl&act=delete&id={$data['newstpl_id']}");</script>
                 </p>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['newstpl_content']}</label>
              <div class="col-sm-9">
              	<p class="form-control-static">
                  {$CMS->class->editor->shortcode($data['newstpl_content'])}
                </p>  
              </div>
            </div>


             <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['upload_file']}</label>
              <div class="col-sm-9">
EOF;
      if($data['newstpl_image'] != "")
            {
            $output .= <<<EOF
        <img src="{$CMS->vars['upload_url']}/newstpl/{$data['newstpl_image']}" />
EOF;
      }
           $output .= <<<EOF
              </div>
            </div>
           

          <h5 class="with-border">{$CMS->lang['newstpl_add_info']}</h5>
             <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['newstpl_display']}</label>
              <div class="col-sm-9">
                <p class="form-control-static">
                {$data['newstpl_display_bk']}
                </p>
              </div>
            </div>

      </div><!-- end card-block -->
  </section><!-- end section card -->


             




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
<form method="post" id="newstpl" name="newstpl" action="{$CMS->vars['root_domain']}/?site=newstpl&act=search_do" onSubmit="return check_form(this.id);">

<header class="section-header">
        <div class="tbl">
          <div class="tbl-row">
            <div class="tbl-cell">
              <h3>Mẫu tin</h3>
               
            </div>
          </div>
        </div>
      </header>
 <section class="card">

      <section class="box-typical">
                    <header class="box-typical-header">
                                <div class="tbl-row">
                                    <div class="tbl-cell tbl-cell-title">
                                        <h3>{$CMS->lang['newstpl_search_info_header']}</h3>
                                    </div>
                                    <div class="tbl-cell tbl-cell-action-bordered">
                                        <a href="{$CMS->vars['root_domain']}/?site=newstpl{$CMS->class->search->url_return}">
                                        <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                                        </a>
                                    </div>
                                     
                                </div>
                            </header>
           </section>
        <div class="card-block">

          <h5 class="with-border">{$CMS->lang['newstpl_required_info']}</h5>

          <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['newstpl_name']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
               <input class='form-control' type="text" name="newstpl_name" value="{$data['newstpl_name']}">
                </p>
              </div>
            </div>



            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['newstpl_content']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                  <textarea class='form-control' name="newstpl_content" cols="60" rows="5">{$data['newstpl_content']}</textarea>
                   </p>
              </div>
            </div>

			<div class="form-group row">
              <label class="col-sm-3 form-control-label">Thời gian gửi</label>
              <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
              	<p class="form-control-static-input">
                   <input class='form-control' type="text" name="newstpl_time" value="{$data['newstpl_time']}" etype="date" placeholder="{$CMS->lang['search_from']}">
              	</p>
              </div>
			</div>
            
            <div class="form-group row">
              <label class="col-sm-3 form-control-label hidden-xs"></label>
              <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
              	<p class="form-control-static-input">
                   <input class='form-control' type="text" name="newstpl_time_to" value="{$data['newstpl_time_to']}" etype="date" placeholder="{$CMS->lang['search_to']}">
              	</p>
              </div>
			</div>

            <h5 class="with-border">{$CMS->lang['newstpl_add_info']}</h5>

            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['newstpl_display']}</label>
              <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">             
                  <p class="form-control-static-input">
                  	<select class='select2' name="newstpl_display" defaultvalue="{$data['newstpl_display']}">{$CMS->vars['display_status']}</select>
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
<script language="javascript">rebuild_form("newstpl",1,1);</script>
<script>
$(document).ready(function(){
    $("input[name='newstpl_time'], input[name='newstpl_time_to']").datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: "dd/mm/yy",
      yearRange: "-90:+10"
    });

});
</script>
EOF;
	
	return $output;	
}

}

?>
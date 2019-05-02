<?php

class skin_tags {

//===========================================================================
//  HTML HEADER
//===========================================================================

public function tags_header()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF

<header class="section-header">
        <div class="tbl">
          <div class="tbl-row">
            <div class="tbl-cell">
              <h3>Quản lý Tags liên quan</h3>
            </div>
          </div>
        </div>
      </header>
	
		
		<div class="alert alert-info alert-fill alert-close alert-dismissible fade in">
			<ul>
				<li>Tags liên quan là gì?</li>
				<li>- Tags liên quan là danh sách những từ saloná tiêu biểu đại diện cho nội dung của 1 bài viết.</li>
				 <li>- Nếu nhiều bài viết đã đăng trong những thời gian khác nhau mà có chung nội dung. Thì những bài viết này sẽ được nhập chung vào mục 'Tin liên quan' dựa vào những Tags liên quan này.</li>
				
			</ul>
		</div>


<form method="post" name="tags" id="tags" action="{$CMS->vars['root_domain']}/?site=tags" onSubmit="return check_form(this.id);">

<section class="box-typical">
        <header class="box-typical-header">
          <div class="tbl-row">
            <div class="tbl-cell tbl-cell-title">
              <h3>Danh sách Tags</h3>
            </div>

            <div class="tbl-cell tbl-cell-action-bordered">

            <a href="{$CMS->vars['root_domain']}/?site=tags&act=search"><i class="fa fa-search"></i></a>

            </div>

            <div class="tbl-cell tbl-cell-action-bordered">

            <i class="fa fa-trash-o"></i>

            </div>
            <div class="tbl-cell tbl-cell-action-bordered">
               <a href="{$CMS->vars['root_domain']}/?site=tags&act=add">
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
                  <th class="table-check">
                    <div class="checkbox checkbox-only" onclick="javascript:form_checkall('tags');" id="checkall">
                     <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
                     <label for="id_{$result['record_cnt']}"></label>
                    </div>
                  </th>
                   
                   <th width="5%" id="order_tags_id">{$CMS->lang['tags_id']}</th>
                   <th width="50%" id="order_tags_name">{$CMS->lang['tags_name']}</th>
                   <th width="20%" style="text-align:center" id="order_tags_module">{$CMS->lang['tags_module']}</th>
                   <th width="20%" style="text-align:center" id="order_tags_time">{$CMS->lang['tags_time']}</th>
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

public function tags_middle($result)
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
  <tr bgcolor="{$result['bgcolor']}">
    <td class="table-check">
                    <div class="checkbox checkbox-only">
                      <input type="checkbox"  name="id_{$result['record_cnt']}" id="id_{$result['record_cnt']}"/>
                      <label for="id_{$result['record_cnt']}"></label>
                    </div>
      </td>
  <td>#{$result['tags_id']}</td>
    <td>{$result['tags_name']}</td>
    <td style="text-align:center">{$result['tags_module_bk']}</td>
     <td style="text-align:center">{$result['tags_time']}</td>
    <td align="center"><script type="text/javascript">permission_btn("edit", "tags", "{$CMS->vars['root_domain']}/?site=tags&act=edit&id={$result['tags_id']}");</script></td>
    <td align="center"><script type="text/javascript">permission_btn("delete", "tags", "{$CMS->vars['root_domain']}/?site=tags&act=delete&id={$result['tags_id']}");</script></td>
  </tr>
EOF;
	
    return $output;
}

//===========================================================================
//  NO DATA
//===========================================================================

public function tags_none()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
  <tr>
    <td colspan="7">{$CMS->lang['tags_no_data']}</td>
  </tr>
EOF;

	return $output;
}

//===========================================================================
//  HTML FOOTER
//===========================================================================

public function tags_footer()
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
</form>

<script language="javascript">rebuild_form("tags");</script>
<script language="javascript">arrange_setup("{$CMS->tags->arrange_data}");</script>
EOF;

	return $output;
}

//===========================================================================
//  USER CONTROL
//===========================================================================

public function tags_control()
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
              <h3>Chỉnh sửa Tags tin tức</h3>          
            </div>
          </div>
        </div>
      </header>

<form method="post" id="tags" name="tags" action="{$CMS->vars['root_domain']}/?site=tags&id={$data['tags_id']}&act=edit_do&page={$CMS->input['page']}" onSubmit="return check_form(this.id);" enctype="multipart/form-data">

 <section class="card">

      <section class="box-typical">
                    <header class="box-typical-header">
                                <div class="tbl-row">
                                    <div class="tbl-cell tbl-cell-title">
                                      <h3>{$CMS->lang['tags_edit_form']} <strong>{$data['tags_name']}</strong></h3>
                                    </div>
                                    <div class="tbl-cell tbl-cell-action-bordered">
                                        <a href="{$CMS->vars['root_domain']}/?site=tags{$CMS->class->search->url_return}">
                                        <button type="button" class="action-btn"><i class="fa fa-mail-reply"></i></button>
                                        </a>
                                    </div>    
                                </div>
                            </header>
           </section>



        <div class="card-block">
          <h5 class="with-border">{$CMS->lang['tags_required_info']}</h5>
            <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['tags_name']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                   <input class="form-control" size="45" type="text" name="tags_name" value="{$data['tags_name_bk']}" defaultvalue="{$data['tags_name_bk']}"  emsg="{$CMS->lang['tags_incomplete_name']}">
                </p> 
                </div>
              </div>


              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['tags_module']}</label>
                <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                  <select class="select2" name="tags_module" value="{$data['tags_module']}" defaultvalue="{$data['tags_module']}">
                     <option value="0">{$CMS->lang['module_0']}</option>
                     <option value="1">{$CMS->lang['module_1']}</option>
                         <option value="2">{$CMS->lang['module_2']}</option> 
                    </select>
                </p> 
                </div>
              </div>


              <div class="form-group row">
                <label class="col-sm-3 form-control-label"></label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                  <input class="btn btn-rounded" type="submit" name="submit" value=" {$CMS->lang['tags_edit_submit']} "> 
                </p> 
                </div>
              </div>

       </div><!-- end card-block -->
  </section><!-- end section card -->

  
</form>
<script language="javascript">rebuild_form("tags",1);</script>

<script type="text/javascript">
		
		$("#tags_module").change(function(e) {
            var tags_module = $(this).val();
				$(".module_cate").hide(); $("#module_"+tags_module).show();
			
        });
		
		
		$(document).ready(function(e) {
              var tags_module = $("#tags_module").val();
				$(".module_cate").hide(); $("#module_"+tags_module).show();
			
        });
		$("#add_option").click(function(){
			var cnt = $("#customer_list_table tr").size();
			var id = parseInt(cnt -1 );
			$('#customer_list_table').append("<tr id='opt_"+id+"'><td><input class='input_text' size='45' type='text' name='tags_option[]' /><input type='hidden' name='tags_count[]' value='0'/></td><td><p class='del_option' onclick='delete_option("+id+");'>Xóa đáp án</p></td></tr>");
		});

		function delete_option(id)
		{
			$("#opt_"+id).remove();
		}
		
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
    
    $output = $CMS->class->editor->simple();

$output .= <<<EOF

<header class="section-header">
        <div class="tbl">
          <div class="tbl-row">
            <div class="tbl-cell">
              <h3>Thêm Tags tin tức</h3>              
            </div>
          </div>
        </div>
      </header>
      
<form method="post" id="tags" name="tags" action="{$CMS->vars['root_domain']}/?site=tags&act=add_do" onSubmit="return check_form(this.id);" enctype="multipart/form-data">

 <section class="card">

      <section class="box-typical">
                    <header class="box-typical-header">
                                <div class="tbl-row">
                                    <div class="tbl-cell tbl-cell-title">
                                        <h3>{$CMS->lang['tags_add_form']} </h3>
                                    </div>
                                    <div class="tbl-cell tbl-cell-action-bordered">
                                        <a href="{$CMS->vars['root_domain']}/?site=tags{$CMS->class->search->url_return}">
                                        <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                                        </a>
                                    </div>           
                                </div>
                            </header>
           </section>



        <div class="card-block">
          <h5 class="with-border">{$CMS->lang['tags_required_info']}</h5>
            <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['tags_name']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">        
                   <p class="form-control-static-input">
                   	<input class="form-control" size="45" type="text" name="tags_name" value="{$data['tags_name']}" emsg="{$CMS->lang['tags_incomplete_name']}">
                   </p>
                </div>
              </div>


              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['tags_module']}</label>
                <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
                  <p class="form-control-static-input">
                  <select class="select2" name="tags_module" value="{$data['tags_module']}" defaultvalue="{$data['tags_module']}">
                     <option value="0">{$CMS->lang['module_0']}</option>
                     <option value="1">{$CMS->lang['module_1']}</option>
                         <option value="2">{$CMS->lang['module_2']}</option> 
                    </select>
                    </p>
                </div>
              </div>


              <div class="form-group row">
                <label class="col-sm-3 form-control-label"></label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                  <p class="form-control-static-input">
                  <input class="btn btn-rounded" type="submit" name="submit" value=" {$CMS->lang['tags_add_submit']} "> 
                  </p>
                </div>
              </div>

       </div><!-- end card-block -->
  </section><!-- end section card -->

  
</form>
<script language="javascript">rebuild_form("tags",1);</script>


<script type="text/javascript">

		
//		$("#tags_module").change(function(e) {
//            var tags_module = $(this).val();
//				$(".module_cate").hide(); $("#module_"+tags_module).show();
//			
//        });
//
//		$("#add_option").click(function(){
//			var cnt = $("#customer_list_table tr").size();
//			var id = parseInt(cnt -1 );
//			$('#customer_list_table').append("<tr id='opt_"+id+"'><td><input class='input_text' size='45' type='text' name='tags_option[]' /><input type='hidden' name='tags_count[]' value='0'/></td><td><p class='del_option' onclick='delete_option("+id+");'>Xóa đáp án</p></td></tr>");
//		});
//
//		function delete_option(id)
//		{
//			$("#opt_"+id).remove();
//		}
		
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
              <h3>Hiển thị Tags tin tức</h3>              
            </div>
          </div>
        </div>
      </header>
 <section class="card">

      <section class="box-typical">
        <header class="box-typical-header">
            <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title">
                  <h3>{$CMS->lang['tags_show_form']} <strong> {$data['tags_name']} </strong></h3>
                </div>
                <div class="tbl-cell tbl-cell-action-bordered">
                    <a href="{$CMS->vars['root_domain']}/?site=tags{$CMS->class->search->url_return}">
                    <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                    </a>
                </div>               
            </div>
        </header>
           </section>



        <div class="card-block">
          <h5 class="with-border">{$CMS->lang['tags_required_info']}</h5>
            <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['tags_name']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                   {$data['tags_name']}
                    &nbsp; <script type="text/javascript">permission_btn("edit", "tags", "{$CMS->vars['root_domain']}/?site=tags&act=edit&id={$data['tags_id']}");</script>
                      &nbsp; <script type="text/javascript">permission_btn("delete", "tags", "{$CMS->vars['root_domain']}/?site=tags&act=delete&id={$data['tags_id']}");</script>
                </p>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['tags_module']}</label>
                <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                  {$data['tags_module_bk']}
                </p>
                </div>
              </div>

               <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['module_cate']}</label>
                <div class="col-sm-9">
                <p class="form-control-static">
                  {$data['tags_cate_bk']}
                </p>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['tags_user_name']}</label>
                <div class="col-sm-9">
                <p class="form-control-static">
                  {$data['user_name']}
                </p>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['tags_time']}</label>
                <div class="col-sm-9">
                <p class="form-control-static">
                  {$data['tags_time']}
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
<form method="post" id="tags" name="tags" action="{$CMS->vars['root_domain']}/?site=tags&act=search_do" onSubmit="return check_form(this.id);">
 <header class="section-header">
        <div class="tbl">
          <div class="tbl-row">
            <div class="tbl-cell">
              <h3>Tìm kiếm Tags tin tức</h3>             
            </div>
          </div>
        </div>
      </header>
 <section class="card">

      <section class="box-typical">
        <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3>{$CMS->lang['tags_search_info_header']}</h3>
                        </div>
                        <div class="tbl-cell tbl-cell-action-bordered">
                            <a href="{$CMS->vars['root_domain']}/?site=tags{$CMS->class->search->url_return}">
                            <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                            </a>
                        </div>
                         
                    </div>
                </header>
		</section>
        <div class="card-block">

      <h5 class="with-border">Thông tin cần thiết</h5>

          <div class="form-group row">
          <label class="col-sm-3 form-control-label">{$CMS->lang['tags_name']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
               <input class='form-control' size="45" type="text" name="tags_name" value="{$data['tags_name']}">
               </p>
              </div>
            </div>



            <div class="form-group row">
              <label class="col-sm-3 form-control-label">Nội dung tags</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                  <textarea class="form-control" name="tags_content" cols="60" rows="5">{$data['tags_content']}</textarea>
              </p>
              </div>
            </div>
			
            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['tags_time']}</label>
              <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
              	<p class="form-control-static-input">
                   <input class='form-control' type="text" name="tags_time" value="{$data['tags_time']}" etype="date" placeholder="{$CMS->lang['search_from']}">
              	</p>
              </div>
			</div>
            
            <div class="form-group row">
              <label class="col-sm-3 form-control-label hidden-xs"></label>
              <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
              	<p class="form-control-static-input">
                   <input class='form-control' type="text" name="tags_time_to" value="{$data['tags_time_to']}" etype="date" placeholder="{$CMS->lang['search_to']}">
              	</p>
              </div>
			</div>

            <h5 class="with-border">{$CMS->lang['tags_add_info']}</h5>

            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['tags_display']}</label>
              <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                  <select class="select2" name="tags_display" defaultvalue="{$data['tags_display']}">{$CMS->vars['display_status']}</select>
              </p>
              </div>
            </div>


            <div class="form-group row">
              <label class="col-sm-3 form-control-label"></label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                <input class="btn btn-rounded" type="submit" name="submit" value=" {$CMS->lang['search_submit']} "> 
              </p>
              </div>
            </div>



            </div><!-- end .card-block-->
   </section><!--card --> 

             
</form>
<script language="javascript">rebuild_form("tags",1,1);</script>
<script>
$(document).ready(function(){
    $("input[name='tags_time'], input[name='tags_time_to']").datepicker({
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
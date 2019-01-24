<?php

class skin_transaction_terms {


//===========================================================================
//  HTML HEADER
//===========================================================================

    public function header()
    {
        global $CMS, $DB, $member;

        $output = "";

        $term_days = urldecode($CMS->input['term_days']);
        $output .= <<<EOF
 
<script>
    $(".searchpage").hide();
</script>
<section class="add_table main_form">
	<figure class="heading">
		<h3>{$CMS->lang['menu_transaction_terms']}</h3>
		<figure class="pull-right right">
			<div class="search">
				<form method="post"  action="{$CMS->vars['root_domain']}/?site=transaction_terms&act=search_do" style="display:inline-block">

					<input type="submit" class="fa-input" value="&#xf002;">
					<input type="text" name="term_days" id="term_days" placeholder="{$CMS->lang['gsearch_quick']}" value="{$term_days}">
				</form>
				 
			</div>
EOF;

        if($CMS->permit['transaction_terms_add'] == 1)
        {
            $output .=<<<EOF

			<a onclick="return add_term();" title="" class="add_bill">{$CMS->lang['add_form']}</a>
EOF;

        }
        $output .=<<<EOF
        <a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
		</figure>
	</figure>
	
<section class="add_table">
 
     
			<div class="data_table">
 

					<table id="example" class="display table table_cus" cellspacing="0" width="100%">
						<thead>
						<tr>
EOF;

        if($_SESSION['is_mobile'] == false)
        {
            $output .=<<<EOF
          
								<th  width="5%" data-sortable="false" >{$CMS->lang['term_id']}</th>
								<th width="20%" data-sortable="true">{$CMS->lang['term_days']}</th>
                                <th data-orderable="false" width="30%">{$CMS->lang['term_status']}</th>
                                <th data-sortable="false" data-orderable="false" width="30%"  >{$CMS->lang['term_time']}</th>
								<th data-sortable="false" data-orderable="false" width="6%"></th>
								
EOF;

        }
        else
        {
            $output .=<<<EOF
          
                <th width="3%" data-sortable="false"></th>
                <th width="20%" data-sortable="true" >{$CMS->lang['term_days']}</th>
                <th width="5%" data-sortable="false" data-orderable="false" >{$CMS->lang['term_id']}</th>
                <th data-orderable="false" width="20%"   >{$CMS->lang['term_status']}</th>
                <th data-sortable="false" data-orderable="false"width="30%"  >{$CMS->lang['term_time']}</th>
                <th data-sortable="false" data-orderable="false"  width="6%"></th>
                
EOF;
        }
        $output .=<<<EOF

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

        $output .= <<<EOF
  <tr>
EOF;

        if($_SESSION['is_mobile'] == false)
        {
            $output .=<<<EOF

    <td>#{$result['term_id']}</td>
    <td>
         {$result['term_days']}
    </td>
EOF;
        }
        else
        {
            $output .=<<<EOF

    <td></td>
    <td>
         {$result['term_days']}
    </td>
     <td>#{$result['term_id']}</td>

EOF;
        }
        $output .=<<<EOF
    <td>{$result['term_status']}</td>
    <td>{$result['term_time']}</td>
    <td align="center"> 
EOF;
        if($CMS->permit['transaction_terms_edit'] == 1)
        {
            $output .=<<<EOF
							<a onclick="return edit_term({$result['term_id']})"   class="edit"><i class="fa fa-edit"></i></a>
EOF;

        }

        if($CMS->permit['transaction_terms_delete'] == 1)
        {
            $output .=<<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=transaction_terms&act=delete&id={$result['term_id']}');"   class="edit"><i class="fa fa-trash-o"></i></a>
					 
EOF;

        }
        $output .=<<<EOF
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
        $term_days = urldecode($CMS->input['term_days']);
        $output .= <<<EOF
  <tr>
    <td colspan="5">
EOF;
        if($CMS->input['term_days'] != "")
        {

            $output .= <<<EOF
		   Không có kết quả theo từ khóa: {$term_days}
EOF;
        }
        else
        {
            $output .= <<<EOF
		    {$CMS->lang['no_data']}
EOF;

        }

        $output .= <<<EOF

    </td>
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

		</div><!--.box-typical-body-->
		 
	 </section>
		<div class="block_bottom pagination pagination-sm">
			{$CMS->transaction_terms->show_page}
		</div>
		 
</section>
{$CMS->transaction_terms->formAddTransactionTerm()}


  



 <script src="{$CMS->vars['js_acp']}/transaction_terms.js"></script>


 
EOF;

        if($_SESSION['is_mobile'] == true)
        {
            $output .=<<<EOF
         <script>
            $(function() {
              $('#example').DataTable({
              language: {
                      emptyTable: 'Dữ liệu không tồn tại'
                  },
                 order: [],
              responsive: true,
                columnDefs: [
                    { responsivePriority: 1, targets: 1 },
                    { responsivePriority: 2, targets: 2 },
                    
                ],
                paging: false,
                  searching: false,
                  info: false
              });
            });
          </script>
EOF;

        }
        else
        {

            $output .=<<<EOF
       
              
               <script>
                $(function() {
                $('#example').DataTable({
                 language: {
                      emptyTable: 'Dữ liệu không tồn tại'
                    },
                 order: [],
                  paging: false,
                    searching: false,
                    info: false
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

<header class="section-header">
        <div class="tbl">
          <div class="tbl-row">
            <div class="tbl-cell">
              <h3>Chỉnh sửa Banner</h3>              
            </div>
          </div>
        </div>
      </header>

<form method="post" id="shipment" name="shipment" action="{$CMS->vars['root_domain']}/?site=transaction_terms&id={$data['shipment_id']}&act=edit_do&page={$CMS->input['page']}" onSubmit="return check_form(this.id);" enctype="multipart/form-data">

 <section class="card">

      <section class="box-typical">
        <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                          <h3>{$CMS->lang['shipment_edit_form']} <strong> {$data['shipment_name']}</strong></h3>
                        </div>
                        <div class="tbl-cell tbl-cell-action-bordered">
                            <a href="{$CMS->vars['root_domain']}/?site=transaction_terms{$CMS->class->search->url_return}">
                            <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                            </a>
                        </div>   
                    </div>
                </header>
        </section>



        <div class="card-block">

          <h5 class="with-border">{$CMS->lang['shipment_required_info']}</h5>
          
            <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['shipment_name']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                  <input class="form-control" type="text" name="shipment_name" value="{$data['shipment_name']}" emsg="{$CMS->lang['shipment_incomplete_name']}">
                  </p>
                </div>
              </div>
              
              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['shipment_type']}</label>
                <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
					<select onchange="changeshipmentType($(this));" class="form-control select2" name="shipment_type" defaultvalue="{$data['shipment_type']}">
						{$CMS->transaction_terms->load_type_html()}
					</select>
			    </p>  
                </div>
              </div>

              <div class="form-group row" shipment_type="|0|">
                <label class="col-sm-3 form-control-label">{$CMS->lang['shipment_website']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                 <input class="form-control" type="text" name="shipment_website" value="{$data['shipment_website']}" emsg="{$CMS->lang['shipment_incomplete_website']}">
                   </p>  
                </div>
              </div>


               <div class="form-group row" shipment_type="|0|">
                <label class="col-sm-3 form-control-label">{$CMS->lang['shipment_target']}</label>
                <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
                  <p class="form-control-static-input">
                    <select class="select2" name="shipment_target" defaultvalue="{$data['shipment_target']}"><option value="_self">_self</option><option value="_blank">_blank</option><option value="random">random</option></select>
                  </p>            
                </div>
              </div>



              <div class="form-group row" shipment_type="|0|">
                <label class="col-sm-3 form-control-label">{$CMS->lang['shipment_path']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-noinput">
                  <input class="form-control" type="file" name="file_upload"> 
                 </p>
EOF;

        if($data['shipment_type'] == 'flash')
        {
            $output .= <<<EOF
   		{$data['shipment_path']}
   		<br />
EOF;
        }
        else if($data['shipment_type'] == 0)
        {
            $output .= <<<EOF
    	<img width="300px" src="{$data['shipment_path']}"  style="margin:5px 0px 10px 0px"/>
    	<br />
EOF;
        }

        $output .= <<<EOF
                </div>
              </div>
              
             <div class="form-group row" shipment_type="|1|">
              <label class="col-sm-3 form-control-label">{$CMS->lang['shipment_code']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                  <textarea class="form-control" name="shipment_code" emsg="{$CMS->lang['shipment_incomplete_code']}">{$data['shipment_code']}</textarea>
                </p>
              </div>
            </div>


              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['position_id']}</label>
                <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                 <select class="select2" name="position_id" defaultvalue="{$data['position_id']}" emsg="{$CMS->lang['shipment_incomplete_position']}">{$CMS->transaction_terms->get_position_list()}</select>
                  </p>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['shipment_start_time']}</label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                  <p class="form-control-static">{$data['shipment_start_time_bk']}</p>
               	</div>
			  </div>
              
              <div class="form-group row">
                <label class="col-sm-3 form-control-label hidden-xs"></label>
                <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                  <figure class="form-control-static-input" style="margin-bottom:0">                 
                  	<input type="button" id="btn_start" name="btn_start" class="btnupdate" style="font-size:12px" value="Cập nhật thời gian mới" />
                    <div style="clear:both"></div>
                    <div id="update_start" style="display:none;margin-top:10px">
                          <input class="form-control" size="45" type="text" placeholder="Click vào đây để chọn thời gian" name="shipment_start_time_bk">
                    </div>
                  </figure>
               	</div>
			  </div>   


            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['shipment_end_time']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              	<p class="form-control-static">{$data['shipment_end_time_bk']}</p>
              </div>
           	</div>
           
            <div class="form-group row">
                <label class="col-sm-3 form-control-label hidden-xs"></label>
            	<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                  <figure class="form-control-static-input" style="margin-bottom:0">                 
                    <input type="button" id="btn_end" name="btn_end" class="btnupdate" style="font-size:12px" value="Cập nhật thời gian mới" />
                    <div style="clear:both"></div>
                    <div id="update_end" style="display:none;margin-top:10px">
                          <input class="form-control" size="45" type="text" placeholder="Click vào đây để chọn thời gian" name="shipment_end_time_bk">
                </div>
              	  </figure>
            </div>
          </div>   

            <h5 class="with-border">{$CMS->lang['shipment_add_info']}</h5>


            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['shipment_owner']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                  <input class="form-control" type="text" name="shipment_owner" value="{$data['shipment_owner']}">
                </p>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['shipment_status']}</label>
              <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
              	<p class="form-control-static">
	                <span class="rad_box">
                    	<input type="radio" name="shipment_status" defaultvalue="{$data['shipment_status']}" value="1"> {$CMS->lang['yes']}
                    </span>
                    <span class="rad_box">
                    	<input type="radio" name="shipment_status" defaultvalue="{$data['shipment_status']}" value="0" checked="checked"> {$CMS->lang['no']}
                    </span>
                </p>
                  
              </div>
            </div>



            <div class="form-group row">
              <label class="col-sm-3 form-control-label"></label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
                <p class="form-control-static-input">
                 <button class="btn btn-rounded" type="submit" >{$CMS->lang['shipment_edit_submit']}</button>
                </p>
              </div>
            </div>
             
        </div><!-- end card-block -->
  </section><!-- end section card -->

</form>
<script language="javascript">rebuild_form("shipment",1);</script>
<script language="javascript">
$('#shipment_start_time_bk').datetimepicker({
	dateFormat: "dd/mm/yy",
	timeFormat: "",
});
$('#shipment_end_time_bk').datetimepicker({
	dateFormat: "dd/mm/yy",
	timeFormat: "",
});

$("#btn_start").click(function(e) {
			$("#update_start").slideToggle("slow");
		});


$("#btn_end").click(function(e) {
			$("#update_end").slideToggle("slow");
		});


		$("#shipment_type").trigger("change");
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
		<h3>{$CMS->lang['shipment_title_add']}</h3>
		 <a href="{$CMS->vars['root_domain']}/?site=transaction_terms" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>

   <figure class="box-typical box-typical box-typical-padding border">
 		<div class="row">
	 		<div class="col-md-6">
				 <fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['pg_parent']}</label>
									<select class="form-control" name="pg_parent" id="pg_parent" >
										{$option_pg_parent}
									</select>
								
						</fieldset>
			</div>	
			<div class="col-md-6">
			</div>
		</div>	
	</figure>
 
</section>				
 <section class="add_cart_footer">
			<a href="{$CMS->vars['root_domain']}/?site=transaction_terms" class="pull-left cancel">Trở về danh sách</a>
			 
			<button id="form_submit" class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label">{$CMS->lang['pg_add_button']}</span><span class="ladda-spinner"></span></button>


 </section>

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
		<h3>{$CMS->lang['shipment_info']}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site=transaction_terms" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>

	 <figure class="box-typical box-typical box-typical-padding border">
 		<div class="row">
 			<div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
 				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['shipment_text_name']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['term_days']}
						 
					</div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['shipment_text_description']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['term_description']}
						 
					</div>
				</fieldset>

 			</div>
 		</div>
 	 </figure>
 </section>
 
<section class="add_cart_footer">
			<a href="{$CMS->vars['root_domain']}/?site=transaction_terms" class="pull-left cancel">Trở về danh sách</a>
EOF;
        if($CMS->permit['transaction_terms_delete'] == 1)
        {

            $output .=<<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=transaction_terms&act=delete&id={$data['term_id']}');"  class="pull-right add_cart_2">{$CMS->lang['gdelete']}</a>

EOF;

        }

        if($CMS->permit['transaction_terms_edit'] == 1)
        {
            $output .=<<<EOF
							<a onclick="return edit_term({$data['term_id']})"   class="pull-right add_cart_2">{$CMS->lang['gedit']}</a>
EOF;

        }



        $output .=<<<EOF
		</section>

		{$CMS->transaction_terms->formAddTransactionTerm()}
 <script src="{$CMS->vars['js_acp']}/transaction_terms.js"></script>
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
<form method="post" id="shipment" name="shipment" action="{$CMS->vars['root_domain']}/?site=transaction_terms&act=search_do" onSubmit="return check_form(this.id);">
<div class="block_wrapper">
<div class="block_top"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=transaction_terms{$CMS->class->search->url_return}">&laquo; {$CMS->lang['shipment_header_back']}</a></p>{$CMS->lang['search_form']}</div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
    <td class="left25"><b>{$CMS->lang['shipment_name']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="shipment_name" value="{$data['shipment_name']}"></td>
  </tr>
   <tr>
    <td class="left25"><b>{$CMS->lang['position_id']}</b>:</td>
    <td>
    		  <select class="input_text" name="position_id" id="position_id" defaultvalue="{$CMS->input['position_id']}" onchange="this.form.submit()">
                {$CMS->transaction_terms->get_position_list()}
            </select>
    </td>
  </tr>
 <tr>
    <td class="left25"><b>{$CMS->lang['shipment_status']}</b>:</td>
    <td>
    		    <select class="input_text" name="shipment_status" id="shipment_status" defaultvalue="{$CMS->input['shipment_status']}" onchange="this.form.submit()">
                     <option value="">{$CMS->lang['shipment_status']}</option>
                    <option value="1">{$CMS->lang['yes']}</option>
                     <option value="0">{$CMS->lang['no']}</option>
                </select>
    
    </td>
  </tr>

  <tr>
    <td class="left25"><b>{$CMS->lang['shipment_website']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="shipment_website" value="{$data['shipment_website']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['shipment_owner']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="shipment_owner" value="{$data['shipment_owner']}"></td>
  </tr>
</table>
</div>
</div>
<div class="block_desc_bottom_submit">
	<input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['search_submit']} "> 
</div>
<div class="block_bottom pagination pagination-sm"></div>
</form>
<script language="javascript">rebuild_form("shipment",1,1);</script>
EOF;

        return $output;
    }
}

?>
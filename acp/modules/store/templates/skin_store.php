<?php

use core\ezy;

class skin_store
{
    public function head()
    {
        global $CMS, $DB, $member;
        //Test sync 0010
        $output = '';
        $CMS->input['title'] = str_replace('%20', ' ', $CMS->input['title']);
        $output .= <<<EOF



<section class="add_table main_form">
	<figure class="heading">
		<h3>{$CMS->lang['store_head']}</h3>
		<figure class="pull-right right">
			<div class="search">
				<form method="post"  action="{$CMS->vars['root_domain']}/?site=store&act=search" style="display:inline-block">

					<input type="submit" class="fa-input" value="&#xf002;">
					<input type="text" name="quick_search" id="quick_search" placeholder="{$CMS->lang['gsearch_quick']}" value="{$CMS->input['sname']}">
				</form>
				 
			</div>
EOF;

        if ($CMS->permit['store_add'] == 1) {
            $output .= <<<EOF

			<a href="{$CMS->vars['root_domain']}/?site=store&act=add" title="" class="add_bill">{$CMS->lang['store_new']}</a>
EOF;

        }
        $output .= <<<EOF
			<a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
		</figure>
	</figure>
	
	 

<section class="add_table">
 
     
			<div class="data_table">
 

					<table id="example" class="display table table_cus" cellspacing="0" width="100%">
						<thead>
						<tr>
EOF;

        if ($_SESSION['is_mobile'] == false) {
            $output .= <<<EOF
							 
								<th width="5%" data-orderable="false" >{$CMS->lang['store_text_id']}</th>
								<th width="15%" data-sortable="true" >{$CMS->lang['store_text_name']}</th>
								<th width="15%" data-sortable="false" >Hình đại diện</th>
								<th width="15%" data-orderable="false"  data-sortable="false" >Slogan</th>
								<th width="10%" data-orderable="false"  data-sortable="false" >{$CMS->lang['store_time']}</th>
								<th width="5%" data-orderable="false"  data-sortable="false" style="text-align:center"></th>
EOF;

        } else {
            $output .= <<<EOF
							 
								<th width="3%" data-orderable="false" ></th>
								<th width="15%" data-sortable="true" >{$CMS->lang['store_text_name']}</th>
								<th width="15%" data-sortable="false" >Hình đại diện</th>
								<th width="15%" >Slogan</th>
								<th width="10%" >{$CMS->lang['store_time']}</th>
								<th width="5%" style="text-align:center"></th>
EOF;
        }
        $output .= <<<EOF
						</tr>
						</thead>
						 
						<tbody>

 
EOF;
        return $output;
    }

    public function foot()
    {
        global $CMS, $DB, $member;
        $output = <<<EOF
		 


					</tbody>
				</table>

		</div><!--.box-typical-body-->
		 
		</section>
		<div class="block_bottom pagination pagination-sm">
			{$CMS->store->show_page}
		</div>
		 
</section>
EOF;

        if ($_SESSION['is_mobile'] == true) {
            $output .= <<<EOF
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

        } else {

            $output .= <<<EOF
 			 
							
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

    public function mid($data = NULL)
    {
        global $CMS, $DB, $member;

        if ($data['store_avatar']) {
            $img =  "<img width=\"100%\" src=\"{$data['store_avatar']}\">";
        }

        $output = <<<EOF
			<tr>

EOF;

        if ($_SESSION['is_mobile'] == true) {
            $output .= <<<EOF

				<td></td>
				<td><a href="{$CMS->vars['root_domain']}/?site=assets&store_id={$data['store_id']}">{$data['store_name']}</a></td>
				<td>{$data['store_id']}</td>
EOF;
        } else {
            $output .= <<<EOF

				<td>{$data['store_id']}</td>
				<td><a href="{$CMS->vars['root_domain']}/?site=assets&store_id={$data['store_id']}">{$data['store_name']}</a></td>
EOF;
        }
        $output .= <<<EOF
                <td>{$img}</td>
				<td>{$data['store_slogan']}</td>
				<td>{$data['store_time']}</td>
				<td align="center">
EOF;
        if ($CMS->permit['store_edit'] == 1) {
            $output .= <<<EOF
					<a href="{$CMS->vars['root_domain']}/?site=store&act=edit&id={$data['store_id']}"  class="edit"><i class="fa fa-edit"></i></a>
EOF;
        }
        if ($CMS->permit['store_delete'] == 1) {
            $output .= <<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=store&act=del&id={$data['store_id']}');"   class="edit"><i class="fa fa-trash-o"></i></a>
					 
EOF;

        }
        $output .= <<<EOF
			 		</td>
				</tr>
EOF;
        return $output;
    }

    public function none()
    {
        global $CMS, $DB, $member;
        $output = <<<EOF
				<tr>
					<td colspan="8" align="center" class="no_data"><h6>Not Store</h6></td>
				</tr>
EOF;
        return $output;
    }

    public function show($data = NULL)
    {
        global $CMS, $DB, $member;
        $output = <<<EOF
<header class="section-header">
	<div class="tbl">
		<div class="tbl-row">
            <div class="tbl-cell">
				<h3>{$CMS->lang['store_info']}</h3>
            </div>
		</div>
	</div>
</header>
<section class="card">
    <section class="box-typical">
        <header class="box-typical-header">
            <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title">
                    <h3>{$CMS->lang['store_info']}</h3>
                </div>
                <div class="tbl-cell tbl-cell-action-bordered">
					<script type="text/javascript">permission_btn("edit", "user", "{$CMS->vars['root_domain']}/?site=store&act=edit&id={$data['store_id']}");</script>
                </div>
				<div class="tbl-cell tbl-cell-action-bordered">
                    <a href="{$CMS->vars['root_domain']}/?site=store{$CMS->class->search->url_return}">
                    <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                    </a>
                </div>
            </div>
        </header>
	</section>
	<div class="card-block">
        		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label">{$CMS->lang['store_text_id']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
				<p class="form-control-static">
					<span class="typeahead-query">#{$data['store_id']}</span>
				</p>
			</div>
		</fieldset>
		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label">{$CMS->lang['store_text_name']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
				<p class="form-control-static">
					<span class="typeahead-query">{$data['store_name']}</span>
				</p>
			</div>
		</fieldset>

		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label">{$CMS->lang['store_type']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
				<p class="form-control-static">
					<span class="typeahead-query">{$data['store_type']}</span>
				</p>
			</div>
		</fieldset>
        
		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label">{$CMS->lang['user_id']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
				<p class="form-control-static">
					<span class="typeahead-query">{$data['user_name']}</span>
				</p>
			</div>
		</fieldset>

		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label">{$CMS->lang['store_time']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
				<p class="form-control-static">
					<span class="typeahead-query">{$data['store_time']}</span>
				</p>
			</div>
		</fieldset>


	</div>
</section>
<script>

</script>
EOF;
        return $output;
    }

    public function edit($data = null)
    {
        global $CMS, $DB, $member;
        if (strlen($data['city_id']) < 2) {
            $data['city_id'] = '0' . $data['city_id'];
        }

        $convertedData = $CMS->store->convertvalue($data);

        $style_1 = $data['store_type'] == 1 ? 'style="font-size:13px;color:red;font-stype:italic;"' : 'style="font-size:13px;color:red;font-stype:italic;display:none"';
        $style_2 = $data['store_type'] == 2 ? 'style="font-size:13px;color:red;font-stype:italic;"' : 'style="font-size:13px;color:red;font-stype:italic;display:none"';

        $display_status_checked = [];
        $CMS->input['store_display'] = \lib\input::get('store_display', 1);
        foreach ([0, 1] as $displayStatus) {
            $display_status_checked[$displayStatus] = $displayStatus == $data['store_display'] ? 'checked' : '';
        }

        $store_avatar = "";
        if ($convertedData['store_avatar']) {
            $store_avatar = "<img style='max-width: 100%' class='img-responsive' src='{$convertedData['store_avatar']}'>";
        }

        $output = <<<EOF
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['store_edit']}</h3>
		 	<a href="{$CMS->vars['root_domain']}/?site=store" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>

	<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=store&act=edit_do&id={$data['store_id']}" method="POST" enctype="multipart/form-data">
	  <figure class="box-typical box-typical box-typical-padding border">
	  	<div class="row">
	  		<div class="col-md-12">
	  			<h5 class="section-title no-pt">Basic</h5>	
	 			<div class="row">
	 			    <div class="col-md-8">
	 			        <fieldset class="form-group">
                                <label class="form-label" >{$CMS->lang['store_text_name']}<span style="color:red"> (*)</span></label>
                            <div style="position: relative; width: 100%;">
                                <input  class="form-control" name="store_name" id="store_name" size="45" type="text" value="{$data['store_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['store_err_title']}">	
                            </div>
                        </fieldset>
        
                        
                        
                        <div class="row">
                            <fieldset class="form-group">
                                <div class="col-md-12">
                                    <label class="form-label" >{$CMS->lang['store_address']}</label>
                                    <input  class="form-control" name="store_address" id="store_address" type="text" value="{$data['store_address']}">	
                                </div>
                            </fieldset>
                        </div>
                         <div class="row">
                            <fieldset class="form-group">
                                <div class="col-md-6">
                                    <label class="form-label" >{$CMS->lang['city_id']}</label>
                                    <select name="city_id" id="city_id" defaultvalue="{$data['city_id']}" class="form-control auto_select" >
                                        {$CMS->global->get_optioncity()}
                                    </select>
                                </div>
        
                                <div class="col-md-6">
                                    <label class="form-label" >{$CMS->lang['store_phone']}</label>
                                    <input  class="form-control inputPhone" name="store_phone" id="store_phone" size="45" type="text" value="{$data['store_phone']}">	
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <fieldset class="form-group">
                            <label class="form-label" >Hình ảnh:</label>
                            <div style="position: relative; width: 100%;">
                                <input type="file" name="store_avatar" id="store_avatar">
                                {$store_avatar}
                            </div>
                        </fieldset>
                    </div>
                </div>
                
                <fieldset class="form-group">
					<label class="form-label">Slogan</label>
					<p class="typeahead-field">
						<span class="typeahead-query">
			 				<textarea name="store_slogan" id="store_slogan" rows="3" cols="50" class="form-control" style="height: 120px;">{$data['store_slogan']}</textarea>
			 			</span>
					</p>
				</fieldset>
                
                <fieldset class="form-group">
					<label class="form-label">{$CMS->lang['googlemap_code']}</label>
					<p class="typeahead-field">
						<span class="typeahead-query">
			 				<textarea name="googlemap_code" id="googlemap_code" rows="3" cols="50" class="form-control" style="height: 120px;">{$data['googlemap_code']}</textarea>
			 			</span>
					</p>
				</fieldset>

				<fieldset class="form-group">
					<label class="form-label" >{$CMS->lang['display_status']}</label>
                    <div class="radio" style="display: inline-block; margin-right: 20px;">
                        <input type="radio" name="store_display" id="store_display_1" value="1" {$display_status_checked[1]}>
                        <label for="store_display_1">{$CMS->lang['display_1']}</label>
                    </div>
                    <div class="radio" style="display: inline-block;">
                        <input type="radio" name="store_display" id="store_display_2" value="0" {$display_status_checked[0]}>
                        <label for="store_display_2">{$CMS->lang['display_0']}</label>
                    </div>
				</fieldset>
			</div><!-- col-md-6 -->			
	 </div><!-- row -->

 	</figure>
 	
EOF;

        $footer_details = array(
            'type' => 'edit',
            'module' => 'store',
            'detail_id' => $data['store_id'],
        );
        $output .= $CMS->global->footer_details($footer_details);
        $output .= <<<EOF
 </form>



 
<script>

$(document).ready(function(){
	var is_sync = "{$data['store_backend_sync']}";

	$("[name='store_backend_sync'][value='"+is_sync+"']").prop("checked", true);

});

function update_type(type)
{
    if(type == 1)
    {
        $('#store_type_des_1_div').show();                                
        $('#store_type_des_2_div').hide();                                
    }
    else
    {
        $('#store_type_des_1_div').hide();                                
        $('#store_type_des_2_div').show();                                
    }
}

 

</script>
<script>
$(document).ready(function(){
  
	$( "form#form-signin_v1 input[name='store_type']" ).each(function( index ) {
	 		if($(this).attr("defaultvalue") == $(this).val())
	 		{
	 			$(this).attr("checked", true);
	 		}
	});
    validate_form_custom("#form-signin_v1", ".act_submit_save");
});


</script>
 
EOF;
        return $output;
    }

    public function add()
    {
        global $CMS, $DB, $member;

        $data['store_type'] = $data['store_type'] ? $data['store_type'] : 1;
        $style_1 = $data['store_type'] == 1 ? 'style="font-size:13px;color:red;font-stype:italic;"' : 'style="font-size:13px;color:red;font-stype:italic;display:none"';
        $style_2 = $data['store_type'] == 2 ? 'style="font-size:13px;color:red;font-stype:italic;"' : 'style="font-size:13px;color:red;font-stype:italic;display:none"';

        $display_status_checked = [];
        $CMS->input['store_display'] = \lib\input::get('store_display', 1);
        foreach ([0, 1] as $displayStatus) {
            $display_status_checked[$displayStatus] = $displayStatus == \lib\input::get('store_display') ? 'checked' : '';
        }


        $out = <<<EOF
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['store_new']}</h3>
		 	<a href="{$CMS->vars['root_domain']}/?site=store" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>

	<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=store&act=add_do&id={$data['store_id']}" method="POST" enctype="multipart/form-data">
	  <figure class="box-typical box-typical box-typical-padding border">
	  	<div class="row">
	  		<div class="col-md-12">	
	  			<h5 class="section-title no-pt">Basic</h5>
	  			<div class="row">
	  			    <div class="col-md-8">
	  			        <fieldset class="form-group">
                            <label class="form-label" >{$CMS->lang['store_text_name']}<span style="color:red"> (*)</span></label>
                            <div style="position: relative; width: 100%;">
                                <input  class="form-control" name="store_name" id="store_name" size="45" type="text" value="" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['store_err_title']}">	
                            </div>
                        </fieldset>
        
                        <div class="row">
                            <fieldset class="form-group">
                                <div class="col-md-12">
                                    <label class="form-label" >{$CMS->lang['store_address']}</label>
                                    <input  class="form-control" name="store_address" id="store_address" type="text" value="">	
                                </div>
                            </fieldset>
                        </div>
                         <div class="row">
                            <fieldset class="form-group">
                                <div class="col-md-6">
                                    <label class="form-label" >{$CMS->lang['city_id']}</label>
                                    <select name="city_id" id="city_id" placeholder="{$CMS->lang['city_id']}" defaultvalue="{$CMS->input['city_id']}" class="form-control auto_select" >
                                        {$CMS->global->get_optioncity()}
                                    </select>
                                </div>
        
                                <div class="col-md-6">
                                    <label class="form-label" >{$CMS->lang['store_phone']}</label>
                                    <input  class="form-control inputPhone" name="store_phone" id="store_phone" size="45" type="text" value="">	
                                </div>
                                
                            </fieldset>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <fieldset class="form-group">
                            <label class="form-label" >Hình ảnh:</label>
                            <div style="position: relative; width: 100%;">
                                <input type="file" name="store_avatar" id="store_avatar">
                            </div>
                        </fieldset>
                    </div>
                </div>
	 			
	 			<fieldset class="form-group">
					<label class="form-label">Slogan</label>
					<p class="typeahead-field">
						<span class="typeahead-query">
			 				<textarea name="store_slogan" id="store_slogan" rows="3" cols="50" class="form-control" style="height: 120px;"></textarea>
			 			</span>
					</p>
				</fieldset>
	 			
				<fieldset class="form-group">
					<label class="form-label">{$CMS->lang['googlemap_code']}</label>
					<p class="typeahead-field">
						<span class="typeahead-query">
			 				<textarea name="googlemap_code" id="googlemap_code" rows="3" cols="50" class="form-control" style="height: 120px;"></textarea>
			 			</span>
					</p>
				</fieldset>

				<fieldset class="form-group">
				        <label class="form-label" >{$CMS->lang['display_status']}</label>
                        <div class="radio" style="display: inline-block; margin-right: 20px;">
                            <input type="radio" name="store_display" id="store_display_1" value="1" {$display_status_checked[1]}>
                            <label for="store_display_1">{$CMS->lang['display_1']}</label>
                        </div>
                        <div class="radio" style="display: inline-block;">
                            <input type="radio" name="store_display" id="store_display_2" value="0" {$display_status_checked[0]}>
                            <label for="store_display_2">{$CMS->lang['display_0']}</label>
                        </div>
				</fieldset>			
	 </div><!-- row -->	
			
  
 	</figure>
 	
 	<section class="add_cart_footer">
			<a href="{$CMS->vars['root_domain']}/?site=store" class="pull-left cancel">Trở về danh sách</a>
			 
				<button type="button" id="form_submit" class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label">{$CMS->lang['store_new']}</span><span class="ladda-spinner"></span></button>
    </section>
 </form>

 </section>
<script>
       $(document).ready(function () {
           validate_form_custom("#form-signin_v1", "#form_submit");
       })

                                       
function update_type(type)
{
    if(type == 1)
    {
        $('#store_type_des_1_div').show();                                
        $('#store_type_des_2_div').hide();                                
    }
    else
    {
        $('#store_type_des_1_div').hide();                                
        $('#store_type_des_2_div').show();                                
    }
}                                        

</script>
EOF;
        return $out;
    }
}
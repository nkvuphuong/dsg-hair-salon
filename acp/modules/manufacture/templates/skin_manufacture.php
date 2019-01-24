<?php
class skin_manufacture 
{
	public function head() 
	{
		global $CMS, $DB, $member;

		$out = '';

		if (isset($_SESSION['m_error'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-danger alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['error']}</strong> {$_SESSION['m_error']}
			</div>
EOF;
			unset($_SESSION['m_error']);
		}

		if (isset($_SESSION['m_success'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['success']}</strong> {$_SESSION['m_success']}
			</div>
EOF;
			unset($_SESSION['m_success']);
		}

		$m_id_search = $CMS->input['m_id'];
		$m_name_search = $CMS->input['m_name'];
		
		$out .= <<<EOF
		<section class="add_table main_form">

			<figure class="heading">
				<h3>{$CMS->lang['m_title']}</h3>
				<figure class="pull-right right">
					<div class="search">
						<form method="post" id="frm_quickserch_product"  action="{$CMS->vars['root_domain']}/?site=manufacture&act=search" style="display:inline-block">
							<input type="submit" class="fa-input" value="&#xf002;">
							<input name="m_id_search" id="m_id_search" type="number" value="{$m_id_search}" autocomplete="off" minlength="2" maxlength="64" placeholder="ID" style="border:1px solid #e3e3e3;padding: 9px 6px 9px 45px;font-size: 13px;">
							<input name="m_name_search" id="m_name_search" type="text" value="{$m_name_search}" autocomplete="off" minlength="2" maxlength="64" placeholder="{$CMS->lang['m_name']}" style="position: :relative;">
							<div id="suggesstion-box" class="box_result_find" style="display:none"></div>
						</form>
					</div>
					<a href="{$CMS->vars['root_domain']}/?site=manufacture&act=add" title="" class="add_bill">{$CMS->lang['m_add']}</a>
				    <a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
				</figure>
			</figure>

			<section class="add_table">
	            <div class="data_table">
	                <div class="table table_cus table-responsive" style="border-top: none;">
	                    <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
	                        <thead>
	                            <tr role="row">
	                            	<th data-orderable="false" data-sortable="false" style="margin:0px;padding:0px;"></th>
	                                <th>ID</th>
									<th>{$CMS->lang['m_name']}</th>
									<th>{$CMS->lang['m_code']}</th>
									<th>{$CMS->lang['m_user']}</th>
									<th>{$CMS->lang['m_time']}</th>
									<th>{$CMS->lang['m_status']}</th>
									<th data-sortable="false" data-orderable="false"></th>
	                            </tr>
	                        </thead>
	                        <tbody id="data_table" class="ui-sortable">
EOF;

		return $out;
	}

	public function foot() 
	{
		global $CMS, $DB, $member;

		$out = <<<EOF
								</tbody>
            				</table>
          				</div>
          			<div class="fuction_table">
            			<div class="pull-left">
              				<p class="form-control-static"></p>
            			</div>
            			<nav class="pull-right">
              				<div class="block_bottom pagination pagination-sm">{$CMS->manufacture->show_page}</div>
            			</nav>
          			</div>
        		</div>
      		</section>
  		</section>
EOF;

		$out .= <<<EOF
  		<script>
    		$(function() {
      			$('#example').DataTable({
        			language: {
            			emptyTable: 'Không tìm thấy dữ liệu!'
          			},
        		order: [[ 1,"desc"]],
        		paging: false,
        		searching: false,
        		info: false,
EOF;
  
  		if ( $_SESSION['is_mobile'] == true )
  		{
    		$out .= "responsive: { details: true},";
  		}

  		$out .= <<<EOF
      		});
    	});
  	</script>
EOF;

		return $out;
	}

	public function mid($data=null) 
	{
		global $CMS, $DB, $member;

		$style = $_SESSION['is_mobile'] == true ? "padding-right:27px;" : "";
		
		$out = <<<EOF
		<tr keyrow="{$data['keyrow']}" class="row-grid" rowtr="{$data['rowtr']}">
			<td class="grid-td" style="margin:0px;padding:0px;{$style}"></td>
            <td class="grid-td" ><span class="number">#{$data['manufacture_id']}</span></td>
            <td class="grid-td mwr"><a href="{$CMS->vars['root_domain']}/?site=manufacture&act=show&id={$data['manufacture_id']}">{$data['manufacture_name']}</a></td>
			<td class="grid-td">{$data['manufacture_code']}</td>
			<td class="grid-td">{$data['user_id_c']}</td>
			<td class="grid-td">{$data['manufacture_time_c']}</td>
			<td class="grid-td">{$data['manufacture_status_c']}</td>
			<td class="grid-td" >
EOF;
    
  		if ( $CMS->permit['manufacture_edit'] )
  		{
    		$out .= <<<EOF
    		<a href="{$CMS->vars['root_domain']}/?site=manufacture&act=edit&id={$data['manufacture_id']}" class="edit"  data-toggle="tooltip" data-placement="bottom" title="{$CMS->lang['act_edit']}"><i class="fa fa-edit" aria-hidden="true"></i></a>
EOF;
  		}

//   		if ( $CMS->permit['manufacture_delete'] )
//  	 	{
//     		$out .= <<<EOF
//     		<a onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=sell_customer&act=deleted&id={$data['sc_id']}')" class="edit"><i class="fa fa-trash-o"></i></a>
// EOF;
//   		}

  		$out .= <<<EOF
			</td>
        </tr>
EOF;
		return $out;
	}

	public function none() 
	{
		global $CMS, $DB, $member;

		$out = <<<EOF
		<tr>
			<td colspan="7">
				<center><h5>{$CMS->lang['not_data']}</h5></center>
			</td>
		</tr>
EOF;
		return $out;
	}

	public function add() 
	{
		global $CMS, $DB, $member;

		$m_manufacture_parent = $CMS->input['m_manufacture_parent'];
		$m_name = $CMS->input['m_name'];
		$m_code = $CMS->input['m_code'];
		$m_status = !empty($CMS->input['m_status']) ? $CMS->input['m_status'] : 1;
		$m_description = $CMS->input['m_description'];
		
		$option_m_manufacture_parent = "<option value=''>{$CMS->lang['select']}</option>";
		$manufacture = $CMS->manufacture->getParent();
		foreach ($manufacture as $m) 
		{
			if ($m['manufacture_id'] == $m_manufacture_parent) 
			{
				$option_m_manufacture_parent .= "<option value='{$m['manufacture_id']}' selected>{$m['manufacture_name']}</option>";
			} 
			else 
			{
				$option_m_manufacture_parent .= "<option value='{$m['manufacture_id']}' >{$m['manufacture_name']}</option>";
			}
		}

		$option_m_status = "";
		for ($i = 0; $i <= 1; $i++) 
		{
			if ($i == $m_status) 
			{
				$option_m_status .= "<option value='{$i}' selected>{$CMS->lang['m_status_0'.$i]}</option>";
			} 
			else 
			{
				$option_m_status .= "<option value='{$i}'>{$CMS->lang['m_status_0'.$i]}</option>";
			}
		}

		$out = '';

		if (isset($_SESSION['m_error'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-danger alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['error']}</strong> {$_SESSION['m_error']}
			</div>
EOF;
			unset($_SESSION['m_error']);
		}

		if (isset($_SESSION['m_success'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['success']}</strong> {$_SESSION['m_success']}
			</div>
EOF;
			unset($_SESSION['m_success']);
		}

		$out .= <<<EOF
		<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=manufacture&act=add" method="POST" enctype="multipart/form-data">
		<section class="add_form main_form">
			<figure class="heading">
				<h3>{$CMS->lang['m_add']}</h3>
		  		<a href="{$CMS->vars['root_domain']}/?site=manufacture{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
			</figure>
			<figure class="box-typical box-typical box-typical-padding border">
				<div class="row">
					<div class="col-lg-6">
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['m_status']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<select class="form-control" name="m_status" id="m_status" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['m_status_err']}">
										{$option_m_status}
									</select>
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['m_manufacture_parent']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<select class="form-control" name="m_manufacture_parent" id="m_manufacture_parent" >
										{$option_m_manufacture_parent}
									</select>
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['m_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input class="form-control " type="text" name="m_name" id="m_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['m_name_err']}" value="{$m_name}">
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['m_code']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input class="form-control " type="text" name="m_code" id="m_code" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['m_code_err']}" value="{$m_code}">
								</span>
							</p>
						</fieldset>
					</div>
					<div class="col-lg-6">
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['m_avartar']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input  name="m_avartar" id="m_avartar" type="file" lass="form-control" accept="image/*">
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['m_description']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<textarea rows="11" class="form-control" name="m_description" id="m_description" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['m_description_err']}">{$m_description}</textarea>
								</span>
							</p>
						</fieldset>
					</div>
				</div>
			</figure>
		</section>
EOF;
		$footer_details = array(
				'type' => 'add',
				'module' => 'manufacture',
			);
		$out .= $CMS->global->footer_details($footer_details);
		$out .= <<<EOF
		<script>
		$(document).ready(function(){
			validate_form_custom("#form-signin_v1",".act_submit_save");
		});
		</script>
		</form>
EOF;

// 	$out .= <<<EOF
// 	<script>
// 	function inputLoading(e) 
// 	{
// 		var str  = '<div class="cssload-container" id="input_loading">';
// 			str += '<div class="cssload-progress cssload-float cssload-shadow">';
// 			str += '<div class="cssload-progress-item"></div>';
// 			str += '</div>';
// 			str += '</div>';
// 		e.parent().append(str);
// 	}
// 	function changeSCCity() 
// 	{
// 		$("#sc_district").prop('disabled', true);
// 		inputLoading($('#sc_district'));
// 		$.get( 
// 			"{$CMS->vars['root_domain']}/?site=sell_customer&act=get_district", 
// 			{ id: $('#sc_city').val() } 
// 		) .done( function(html) {
// 			$("#sc_district").html(html);
// 			$("#sc_district").prop('disabled', false);
// 			$('#input_loading').remove();
// 		} );
// 	}
// 	$("#sc_birthday").datepicker({ dateFormat: 'dd/mm/yy' });
// 	</script>
// EOF;

		return $out;
	}
	public function edit($data=null) 
	{
		global $CMS, $DB, $member;

		$m_manufacture_parent = isset($CMS->input['m_manufacture_parent']) ? $CMS->input['m_manufacture_parent'] : $data['manufacture_parent'];
		$m_name = isset($CMS->input['m_name']) ? $CMS->input['m_name'] : $data['manufacture_name'];
		$m_code = isset($CMS->input['m_code']) ? $CMS->input['m_code'] : $data['manufacture_code'];
		$m_status = isset($CMS->input['m_status']) ? $CMS->input['m_status'] : $data['manufacture_status'];
		$m_description = isset($CMS->input['m_description']) ? $CMS->input['m_description'] : $data['manufacture_description'];
		$m_avartar = isset($data['manufacture_avartar']) ? $data['manufacture_avartar'] :'no-img.jpg';
		$m_avartar = $CMS->vars['upload_url'].'/manufacture/'.$m_avartar;
		
		$option_m_manufacture_parent = "<option value=''>{$CMS->lang['select']}</option>";
		$manufacture = $CMS->manufacture->getParent($data['manufacture_id']);
		foreach ($manufacture as $m) 
		{
			if ($m['manufacture_id'] == $m_manufacture_parent) 
			{
				$option_m_manufacture_parent .= "<option value='{$m['manufacture_id']}' selected>{$m['manufacture_name']}</option>";
			} 
			else 
			{
				$option_m_manufacture_parent .= "<option value='{$m['manufacture_id']}' >{$m['manufacture_name']}</option>";
			}
		}

		$option_m_status = "";
		for ($i = 0; $i <= 1; $i++) 
		{
			if ($i == $m_status) 
			{
				$option_m_status .= "<option value='{$i}' selected>{$CMS->lang['m_status_0'.$i]}</option>";
			} 
			else 
			{
				$option_m_status .= "<option value='{$i}'>{$CMS->lang['m_status_0'.$i]}</option>";
			}
		}

		$out = '';

		if (isset($_SESSION['m_error'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-danger alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['error']}</strong> {$_SESSION['m_error']}
			</div>
EOF;
			unset($_SESSION['m_error']);
		}

		if (isset($_SESSION['m_success'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['success']}</strong> {$_SESSION['m_success']}
			</div>
EOF;
			unset($_SESSION['m_success']);
		}
		$out .= <<<EOF
		<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=manufacture&act=edit&id={$data['manufacture_id']}" method="POST" enctype="multipart/form-data">
		<section class="add_form main_form">
			<figure class="heading">
				<h3>{$CMS->lang['m_edit']}</h3>
		  		<a href="{$CMS->vars['root_domain']}/?site=manufacture{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
			</figure>
			<figure class="box-typical box-typical box-typical-padding border">
				<div class="row">
					<div class="col-lg-6">
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['m_status']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<select class="form-control" name="m_status" id="m_status" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['m_status_err']}">
										{$option_m_status}
									</select>
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['m_manufacture_parent']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<select class="form-control" name="m_manufacture_parent" id="m_manufacture_parent" >
										{$option_m_manufacture_parent}
									</select>
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['m_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input class="form-control " type="text" name="m_name" id="m_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['m_name_err']}" value="{$m_name}">
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['m_code']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input class="form-control " type="text" name="m_code" id="m_code" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['m_code_err']}" value="{$m_code}">
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['m_description']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<textarea rows="3" class="form-control" name="m_description" id="m_description" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['m_description_err']}">{$m_description}</textarea>
								</span>
							</p>
						</fieldset>
					</div>
					<div class="col-lg-6">
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['m_avartar']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input  name="m_avartar" id="m_avartar" type="file" lass="form-control" accept="image/*">
									<img class="img-responsive" src="{$m_avartar}" style="max-width: 100%;" alt="{$data['m_avartar']}"> 
								</span>
							</p>
						</fieldset>
					</div>
				</div>
			</figure>
		</section>
EOF;
				
		$footer_details = array(
				'type' => 'edit',
				'module' => 'manufacture',
				'detail_id' => $data['manufacture_id'],
			);
		$out .= $CMS->global->footer_details($footer_details);
		$out .= <<<EOF
		</form>
		<script>
		$(document).ready(function(){
			validate_form_custom("#form-signin_v1",".act_submit_save");
		});
		</script>
EOF;

// 	$out .= <<<EOF
// 	<script>
// 	function inputLoading(e) 
// 	{
// 		var str  = '<div class="cssload-container" id="input_loading">';
// 			str += '<div class="cssload-progress cssload-float cssload-shadow">';
// 			str += '<div class="cssload-progress-item"></div>';
// 			str += '</div>';
// 			str += '</div>';
// 		e.parent().append(str);
// 	}
// 	function changeSCCity() 
// 	{
// 		$("#sc_district").prop('disabled', true);
// 		inputLoading($('#sc_district'));
// 		$.get( 
// 			"{$CMS->vars['root_domain']}/?site=sell_customer&act=get_district", 
// 			{ id: $('#sc_city').val() } 
// 		) .done( function(html) {
// 			$("#sc_district").html(html);
// 			$("#sc_district").prop('disabled', false);
// 			$('#input_loading').remove();
// 		} );
// 	}
// 	$("#sc_birthday").datepicker({ dateFormat: 'dd/mm/yy' });
// 	</script>
// EOF;

		return $out;
	}

	public function show($data=null) 
	{
		global $CMS, $DB, $member;

		$m_avartar = isset($data['manufacture_avartar']) ? $data['manufacture_avartar'] :'no-img.jpg';
		$m_avartar = $CMS->vars['upload_url'].'/manufacture/'.$m_avartar;
		$out = '';
		
		if (isset($_SESSION['m_error'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-danger alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['error']}</strong> {$_SESSION['m_error']}
			</div>
EOF;
			unset($_SESSION['m_error']);
		}

		if (isset($_SESSION['m_success'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['success']}</strong> {$_SESSION['m_success']}
			</div>
EOF;
			unset($_SESSION['m_success']);
		}

		$out .= <<<EOF
		<section class="add_form main_form">
			<figure class="heading">
				<h3>{$CMS->lang['m_info']} : #{$data['manufacture_id']}</h3>
		  		<a href="{$CMS->vars['root_domain']}/?site=manufacture{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
			</figure>
			<figure class="box-typical box-typical box-typical-padding border">
 				<div class="row">
					<div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['m_manufacture_parent']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['manufacture_parent_c']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['m_name']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['manufacture_name']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['m_code']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['manufacture_code']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['m_status']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['manufacture_status_c']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['m_description']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['manufacture_description']}</div>	
							</div>
						</fieldset>
					</div>
					<div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
						<fieldset class="form-group row">
							<label class="form-control-label2" >{$CMS->lang['m_avartar']}</label>
							<div class="form-control-span2"> 
						 		<div class="form-label semibold"><img class="img-responsive" src="{$m_avartar}" style="max-width: 100%;" alt="{$data['m_name']}"> </div>	
							</div>
						</fieldset>
					</div>
				</div>
			</figure>
		</section>
EOF;

		$footer_details = array(
				'type' => 'show',
				'module' => 'manufacture',
				'detail_id' => $data['manufacture_id'],
			);
		$out .= $CMS->global->footer_details($footer_details);

		return $out;
	}
}
?>
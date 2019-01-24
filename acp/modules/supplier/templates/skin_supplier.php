<?php

class skin_supplier 
{
	public function head() 
	{
		global $CMS, $DB, $member;

		$output='';

		$output.=<<<EOF
		<section class="add_table main_form">

			<script type="text/javascript" src="{$CMS->vars['js_url']}/acp_supplier.js"></script>

			<figure class="heading">
				<h3>{$CMS->lang['supplier_head']}</h3>
				<figure class="pull-right right">
					<div class="search">
						<form method="post" id="frm_quickserch_supplier_1"  action="{$CMS->vars['root_domain']}/?site=supplier&act=search" style="display:inline-block">
							<input type="submit" class="fa-input" value="&#xf002;">
							<input name="sname" id="sname" type="text" value="{$CMS->input['sname']}" autocomplete="off" minlength="2" maxlength="64" placeholder="{$CMS->lang['supplier_name']}" style="position: :relative;">
							<div id="suggesstion-box" class="box_result_find" style="display:none"></div>
						</form>
						<script>rebuild_form('frm_quickserch_supplier_1')</script>
						<a id="expand_formsearch" title="">{$CMS->lang['gsearch_advance']}<i class="fa fa-angle-double-right"></i></a>
					</div>
					<a href="{$CMS->vars['root_domain']}/?site=supplier&act=add" title="" class="add_bill">{$CMS->lang['add_new_supplier']}</a>
					<a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
				</figure>
				<section class="search_adv" >
					<form method="post" id="formsearch_adv" style="display:none"  action="{$CMS->vars['root_domain']}/?site=supplier&act=search" >
						<figure class="box-typical box-typical box-typical-padding border">
							<h5>{$CMS->lang['gsearch_advance']}</h5>
							<ul class="input_li row match-height">
								<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
									<input type="text" name="sname" id="sname" value="{$CMS->input['sname']}" placeholder="{$CMS->lang['supplier_name']}" class="form-control" >
								</li>
								<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
									<input type="text" name="sphone" id="sphone" value="{$CMS->input['sphone']}" placeholder="{$CMS->lang['supplier_phone']}" class="form-control" >
								</li>
								<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
									<input type="text" name="user_name" id="user_name" value="{$CMS->input['user_name']}" placeholder="{$CMS->lang['user_id']}" class="form-control" >
								</li>
								<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
									<select name="status" id="status" defaultvalue="{$CMS->input['status']}" class="form-control" >
										<option value="">{$CMS->lang['select_status']}</option>
										<option value="0">{$CMS->lang['supplier_status_0']}</option>
										<option value="1">{$CMS->lang['supplier_status_1']}</option>
									</select>
								</li>
								<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
									<select name="city_id" id="city_id" onchange="update_location(this)" placeholder="{$CMS->lang['city_id']}" defaultvalue="{$CMS->input['city_id']}" class="form-control select2" >
										{$CMS->global->get_list_city()}
									</select>
								</li>
								<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
									<select name="district_id" id="district_id" class="form-control select2" placeholder="{$CMS->lang['district_id']}" defaultvalue="{$CMS->input['district_id']}">
										{$CMS->global->get_list_district()}
									</select>
								</li>
								<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
									<input type="submit" value="{$CMS->lang['comment_filter']}">
								</li>
							</ul>
						</figure>
					</form>
					<script>rebuild_form('formsearch_adv')</script>
				</section>
			</figure>

			<section class="add_table">
	            <div class="data_table">
	                <div class="table table_cus table-responsive" style="border-top: none;">
	                    <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
	                        <thead>
	                            <tr role="row">
	                            	<th data-orderable="false" data-sortable="false" aria-label="" style="margin:0px;padding:0px;"></th>
	                                <th>{$CMS->lang['supplier_id']}</th>
									<th>{$CMS->lang['supplier_name']}</th>
									<th>{$CMS->lang['supplier_code']}</th>
									<th>{$CMS->lang['supplier_type']}</th>
									<th>{$CMS->lang['supplier_phone']}</th>
									<th>{$CMS->lang['user_id']}</th>
									<th>{$CMS->lang['supplier_time']}</th>
									<th data-sortable="false" data-orderable="false"></th>
	                            </tr>
	                        </thead>
	                        <tbody id="data_table" class="ui-sortable">
EOF;

		return $output;
	}
	
	public function foot() 
	{
		global $CMS, $DB, $member;

		$output=<<<EOF
								</tbody>
            				</table>
          				</div>
          			<div class="fuction_table">
            			<div class="pull-left">
              				<p class="form-control-static"></p>
            			</div>
            			<nav class="pull-right">
              				<div class="block_bottom pagination pagination-sm">{$CMS->show_page}</div>
            			</nav>
          			</div>
        		</div>
      		</section>
  		</section>
EOF;
		$output .= <<<EOF
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
    		$output .= "responsive: { details: true},";
  		}

  		$output .= <<<EOF
      		});
    	});
  	</script>
EOF;

		return $output;
	}
	
	public function mid($data=NULL) 
	{
		global $CMS, $DB, $member;

		$style = $_SESSION['is_mobile'] == true ? "padding-right:27px;" : "";
		
		$output=<<<EOF
		<tr keyrow="{$data['keyrow']}" class="row-grid" rowtr="{$data['rowtr']}">
			<td class="grid-td" style="margin:0px;padding:0px;{$style}"></td>
            <td class="grid-td" ><span class="number">#{$data['supplier_id']}</span></td>
            <td class="grid-td mwr"><a href="{$CMS->vars['root_domain']}/?site=supplier&act=show&id={$data['supplier_id']}">{$data['supplier_name']}</a></td>
			<td class="grid-td">{$data['supplier_code']}</td>
			<td class="grid-td">{$data['supplier_type']}</td>
			<td class="grid-td">{$data['supplier_phone']}</td>
			<td class="grid-td"><a href="{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}" target="_blank">{$data['user_name']}</a></td>
			<td class="grid-td">{$data['supplier_time']}</td>
			<td class="grid-td" >
EOF;
    
  		if ( $CMS->permit['supplier_edit'] )
  		{
    		$output .= <<<EOF
    		<a href="{$CMS->vars['root_domain']}/?site=supplier&act=edit&id={$data['supplier_id']}" class="edit"><i class="fa fa-edit" aria-hidden="true"></i></a>
EOF;
  		}

  		if ( $CMS->permit['supplier_delete'] )
 	 	{
    		$output .= <<<EOF
    		<a onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=supplier&act=del&id={$data['supplier_id']}')" class="edit"><i class="fa fa-trash-o"></i></a>
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

		$output=<<<EOF
		<tr>
			<td colspan="8" align="center" class="no_data"><h6>Not supplier</h6></td>
		</tr>
EOF;

		return $output;
	}

	public function show($data=NULL) 
	{
		global $CMS, $DB, $member;

		$output = <<<EOF
		<section class="add_form main_form">
			<figure class="heading">
				<h3>{$CMS->lang['supplier_info']} : #{$data['supplier_id']}</h3>
		  		<a href="{$CMS->vars['root_domain']}/?site=supplier{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
			</figure>
			<figure class="box-typical box-typical box-typical-padding border">
 				<div class="row">
					<div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['supplier_name']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['supplier_name']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['supplier_phone']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['supplier_phone']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['supplier_email']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['supplier_email']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['supplier_address']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['supplier_address']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['supplier_bank']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['supplier_bank']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['supplier_branch']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['supplier_branch']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['supplier_bank_number']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['supplier_bank_number']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['supplier_bank_owner']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['supplier_bank_owner']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['supplier_note']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['supplier_note']}</div>	
							</div>
						</fieldset>
					</div>
					<div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['supplier_type']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['supplier_type']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['supplier_idcard_number']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['supplier_idcard_number']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['supplier_code']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['supplier_code']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['supplier_taxcode']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['supplier_taxcode']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['supplier_get_invoice']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['supplier_get_invoice']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['supplier_status']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['supplier_status']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['user_id']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['user_name']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['supplier_time']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['supplier_time']}</div>	
							</div>
						</fieldset>
					</div>
				</div>
			</figure>
		</section>
EOF;
		$footer_details = array(
				'type' => 'show',
				'module' => 'supplier',
				'detail_id' => $data['supplier_id'],
				'act_deleted' => 'del'
			);
		$output .= $CMS->global->footer_details($footer_details);

		return $output;
	}
    
	public function edit($data=null) 
	{
		global $CMS, $DB, $member;

		$supplier_get_invoice_selected[$data['supplier_get_invoice']] = 'selected';

		if ( strlen($data['city_id']) < 2 )
		{
			$data['city_id'] = '0' . $data['city_id'];
		}

		if ( strlen($data['district_id']) < 2 )
		{
			$data['district_id'] = '00' . $data['district_id'];
		}
		else if ( strlen($data['district_id']) < 3 )
		{
			$data['district_id'] = '0' . $data['district_id'];
		}

		$out = <<<EOF
		<script type="text/javascript" src="{$CMS->vars['js_url']}/acp_supplier.js"></script>
		<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=supplier&act=edit_do&id={$data['supplier_id']}" method="POST" enctype="multipart/form-data">
        <section class="add_form main_form">
			<figure class="heading">
				<h3>{$CMS->lang['supplier_edit']}</h3>
		  		<a href="{$CMS->vars['root_domain']}/?site=supplier{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
			</figure>
 			<figure class="box-typical box-typical box-typical-padding border">
 				<div class="row">
 					<div class="col-lg-4">
 						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_name" id="supplier_name" size="45" type="text" value="{$data['supplier_name']}" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['supplier_err_name']}">
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_phone']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
							 		<input name="supplier_phone" id="supplier_phone" size="45" type="text" value="{$data['supplier_phone']}" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['supplier_err_phone']}">
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_email']} <font style="margin-left:5px" color="#FF0000">(*)</font></label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_email" id="supplier_email" size="45" type="text" value="{$data['supplier_email']}" class="form-control" data-validation="[NOTEMPTY]"  data-validation-regex="/^[_A-Za-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="{$CMS->lang['supplier_err_email']}" >
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_address']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">	
							 		<input name="supplier_address" id="supplier_address" size="45" type="text" value="{$data['supplier_address']}" class="form-control" >
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['city_id']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
							 		<select name="city_id" id="city_id" class="form-control select2" onchange="change_district_global(this,'#district_id');" defaultvalue="{$data['city_id']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['supplier_err_city']}">
			                        	{$CMS->global->get_optioncity()}
									</select>
								</span>
							</p>
						</fieldset>

					
									
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['district_id']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
							 		<select name="district_id" id="district_id" class="form-control select2" defaultvalue="{$data['district_id']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['supplier_err_district']}">
				                        {$CMS->global->get_list_district($data['city_id'])}
				                    </select>
								</span>
							</p>
						</fieldset>
 					</div>
 					<div class="col-lg-4">
 						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_bank']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_bank" id="supplier_bank" size="45" type="text" value="{$data['supplier_bank']}" class="form-control" >
					 			</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_branch']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_branch" id="supplier_branch" size="45" type="text" value="{$data['supplier_branch']}" class="form-control" >
					 			</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_bank_number']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_bank_number" id="supplier_bank_number" size="45" type="text" value="{$data['supplier_bank_number']}" class="form-control" >
					 			</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_bank_owner']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_bank_owner" id="supplier_bank_owner" size="45" type="text" value="{$data['supplier_bank_owner']}" class="form-control">
					 			</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_note']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<textarea name="supplier_note" id="supplier_note" rows="3" cols="50" class="form-control">{$data['supplier_note']}</textarea>
					 			</span>
							</p>
						</fieldset>
 					</div>
 					<div class="col-lg-4">
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_code']} <font style="margin-left:5px" color="#FF0000">(*)</font></label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_code" id="supplier_code" size="45" type="text" value="{$data['supplier_code']}" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['supplier_err_code']}">
					 			</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_taxcode']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_taxcode" id="supplier_taxcode" size="45" type="text" value="{$data['supplier_taxcode']}" class="form-control" >
					 			</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_get_invoice']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
							 		<select name="supplier_get_invoice" id="supplier_get_invoice" class="form-control" defaultvale="{$data['supplier_get_invoice']}">
			                            <option value="0" {$supplier_get_invoice_selected[0]}>{$CMS->lang['no']}</option>
			                            <option value="1" {$supplier_get_invoice_selected[1]}>{$CMS->lang['yes']}</option>
									</select>
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_type']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
							 		<select name="supplier_type" id="supplier_type" class="form-control" onchange="update_type(this.value)" defaultvalue="{$data['supplier_type']}">
			                            <option value="1">{$CMS->lang['supplier_type_1']}</option>
			                            <option value="2">{$CMS->lang['supplier_type_2']}</option>
									</select>
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group" id="idcard_field" name="idcard_field">
							<label class="form-label" >{$CMS->lang['supplier_idcard_number']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_idcard_number" id="supplier_idcard_number" size="45" type="text" value="{$data['supplier_idcard_number']}" class="form-control" >
					 			</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_status']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
							 		<select name="supplier_status" id="supplier_status" class="form-control" defaultvalue="{$data['supplier_status']}">
			                            <option value="0">{$CMS->lang['supplier_status_0']}</option>
			                            <option value="1">{$CMS->lang['supplier_status_1']}</option>
									</select>
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
				'module' => 'supplier',
				'detail_id' => $data['supplier_id'],
			);
		$out .= $CMS->global->footer_details($footer_details);
		$out .= <<<EOF
		</form>
		<script>
		function update_type(type)
		{
			if(type == 1)
			{
				document.getElementById('idcard_field').style.display = "table-row";
			}
			else
			{
				document.getElementById('supplier_idcard_number').value = ""; 
				document.getElementById('idcard_field').style.display = "none";
			}
		}
		rebuild_form('form-signin_v1');
		</script>
		<script>
		$(document).ready(function(){
			validate_form_custom("#form-signin_v1",".act_submit_save");
		});
		</script>
EOF;
	
		return $out;
	}
    
	public function add($data) 
    {
		global $CMS, $DB, $member;
      

        $data['supplier_status'] = $data['supplier_status'] ? $data['supplier_status'] : 1;

        $out = <<<EOF
        <script type="text/javascript" src="{$CMS->vars['js_url']}/acp_supplier.js"></script>
		<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=supplier&act=add_do" method="POST" enctype="multipart/form-data">
        <section class="add_form main_form">
			<figure class="heading">
				<h3>{$CMS->lang['supplier_add']}</h3>
		  		<a href="{$CMS->vars['root_domain']}/?site=supplier{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
			</figure>
 			<figure class="box-typical box-typical box-typical-padding border">
 				<div class="row">
 					<div class="col-lg-4">
 						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_name" id="supplier_name" size="45" type="text" value="{$data['supplier_name']}" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['supplier_err_name']}">
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_phone']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
							 		<input name="supplier_phone" id="supplier_phone" size="45" type="text" value="{$data['supplier_phone']}" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['supplier_err_phone']}">
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_email']} <font style="margin-left:5px" color="#FF0000">(*)</font></label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_email" id="supplier_email" size="45" type="text" value="{$data['supplier_email']}" class="form-control" data-validation="[NOTEMPTY]"  data-validation-regex="/^[_A-Za-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="{$CMS->lang['supplier_err_email']}" >
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_address']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">	
							 		<input name="supplier_address" id="supplier_address" size="45" type="text" value="{$data['supplier_address']}" class="form-control" >
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['city_id']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
							 		<select name="city_id" id="city_id" class="form-control select2" onchange="change_district_global(this,'#district_id');" defaultvalue="{$data['city_id']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['supplier_err_city']}">
			                        	{$CMS->global->get_optioncity()}
									</select>
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['district_id']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
							 		<select name="district_id"  id='district_id' class="form-control select2" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['supplier_err_district']}">
			                        	<option value="">{$CMS->lang['select_district']}</option>
			                        </select>
								</span>
							</p>
						</fieldset>
 					</div>
 					<div class="col-lg-4">
 						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_bank']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_bank" id="supplier_bank" size="45" type="text" value="{$data['supplier_bank']}" class="form-control" >
					 			</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_branch']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_branch" id="supplier_branch" size="45" type="text" value="{$data['supplier_branch']}" class="form-control" >
					 			</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_bank_number']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_bank_number" id="supplier_bank_number" size="45" type="text" value="{$data['supplier_bank_number']}" class="form-control" >
					 			</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_bank_owner']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_bank_owner" id="supplier_bank_owner" size="45" type="text" value="{$data['supplier_bank_owner']}" class="form-control">
					 			</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_note']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<textarea name="supplier_note" id="supplier_note" rows="3" cols="50" class="form-control">{$data['supplier_note']}</textarea>
					 			</span>
							</p>
						</fieldset>
 					</div>
 					<div class="col-lg-4">
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_code']} <font style="margin-left:5px" color="#FF0000">(*)</font></label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_code" id="supplier_code" size="45" type="text" value="{$data['supplier_code']}" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['supplier_err_code']}">
					 			</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_taxcode']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_taxcode" id="supplier_taxcode" size="45" type="text" value="{$data['supplier_taxcode']}" class="form-control" >
					 			</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_get_invoice']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
							 		<select name="supplier_get_invoice" id="supplier_get_invoice" class="form-control" defaultvale="{$data['supplier_get_invoice']}">
			                            <option value="0">{$CMS->lang['no']}</option>
			                            <option value="1">{$CMS->lang['yes']}</option>
									</select>
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_type']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
							 		<select name="supplier_type" id="supplier_type" class="form-control" onchange="update_type(this.value)" defaultvalue="{$data['supplier_type']}">
			                            <option value="1">{$CMS->lang['supplier_type_1']}</option>
			                            <option value="2">{$CMS->lang['supplier_type_2']}</option>
									</select>
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group" id="idcard_field" name="idcard_field">
							<label class="form-label" >{$CMS->lang['supplier_idcard_number']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input name="supplier_idcard_number" id="supplier_idcard_number" size="45" type="text" value="{$data['supplier_idcard_number']}" class="form-control" >
					 			</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['supplier_status']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
							 		<select name="supplier_status" id="supplier_status" class="form-control" defaultvalue="{$data['supplier_status']}">
			                            <option value="0">{$CMS->lang['supplier_status_0']}</option>
			                            <option value="1">{$CMS->lang['supplier_status_1']}</option>
									</select>
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
				'module' => 'supplier',
			);
		$out .= $CMS->global->footer_details($footer_details);
		$out .= <<<EOF
		</form>
		<script>
		function update_type(type)
		{
			if(type == 1)
			{
				document.getElementById('idcard_field').style.display = "table-row";
			}
			else
			{
				document.getElementById('supplier_idcard_number').value = ""; 
				document.getElementById('idcard_field').style.display = "none";
			}
		}
		rebuild_form('form-signin_v1');
		</script>
		<script>
		$(document).ready(function(){
			validate_form_custom("#form-signin_v1",".act_submit_save");
		});
		</script>
EOF;
	
		return $out;
	}
}
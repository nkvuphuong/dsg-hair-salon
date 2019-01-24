<?php
class skin_accounts 
{
	public function head() 
	{
		global $CMS, $DB, $member;

		$a_id_search = isset($CMS->input['a_id']) ? $CMS->input['a_id'] : '';
		$a_account_search = isset($CMS->input['a_account']) ? $CMS->input['a_account'] : '';
		$a_bank_search =  $CMS->input['a_bank'] !=""  ? $CMS->input['a_bank'] : '';
		$a_number_search =  $CMS->input['a_number'] !=""  ? $CMS->input['a_number'] : '';
		$a_status_search =  $CMS->input['a_status'] !=""  ? $CMS->input['a_status'] : '';
		$option_a_status_search = "<option value=''>{$CMS->lang['select']}</option>";
		for ($i = 0; $i <= 1; $i++) 
		{
			if ($a_status_search != '' && $i == $a_status_search) 
			{
				$option_a_status_search .= "<option value='{$i}' selected>{$CMS->lang['a_status_0'.$i]}</option>";
			} 
			else
			{
				$option_a_status_search .= "<option value='{$i}'>{$CMS->lang['a_status_0'.$i]}</option>";
			}
		}

		$out = '';
		if ( $_SESSION['a_error'] !="" ) 
		{
			$out .= <<<EOF
			<div class="alert alert-danger alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['error']}</strong><br>
				{$_SESSION['a_error']}
			</div>
EOF;
			unset($_SESSION['a_error']);
		}

		if ( $_SESSION['a_success'] !="" ) 
		{
			$out .= <<<EOF
			<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['success']}</strong><br>
				{$_SESSION['a_success']}
			</div>
EOF;
			unset($_SESSION['a_success']);
		}

		$out .= <<<EOF
		<section class="add_table main_form">

			<figure class="heading">
				<h3>{$CMS->lang['a_title']}</h3>
				<figure class="pull-right right">
					<div class="search">
						<form method="post" id="frm_quickserch_product"  action="{$CMS->vars['root_domain']}/?site=accounts&act=search" style="display:inline-block">
							<input type="submit" class="fa-input" value="&#xf002;">
							<input name="a_account_search" id="a_account_search" type="text" value="{$a_account_search}" autocomplete="off" minlength="2" maxlength="64" placeholder="{$CMS->lang['gsearch_quick']}" style="position: :relative;">
							<div id="suggesstion-box" class="box_result_find" style="display:none"></div>
						</form>
						<a id="expand_formsearch" title="">{$CMS->lang['gsearch_advance']}<i class="fa fa-angle-double-right"></i></a>
					</div>
					<a href="{$CMS->vars['root_domain']}/?site=accounts&act=add" title="" class="add_bill">{$CMS->lang['a_add']}</a>
					<a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
				</figure>
				<section class="search_adv" >
					<form method="post" id="formsearch_adv" style="display:none"  action="{$CMS->vars['root_domain']}/?site=accounts&act=search" >
						<figure class="box-typical box-typical box-typical-padding border">
								<h5>{$CMS->lang['gsearch_advance']}</h5>
								<ul class="input_li row match-height">
									<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
										<input type="number" name="a_id_search" id="a_id_search" value="{$a_id_search}" placeholder="ID" class="form-control" >
									</li>
									<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
										<input type="text" name="a_account_search" id="a_account_search" value="{$a_account_search}" placeholder="{$CMS->lang['a_account']}" class="form-control" >
									</li>
									<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
										<input type="text" name="a_bank_search" id="a_bank_search" value="{$a_bank_search}" placeholder="{$CMS->lang['a_bank']}" class="form-control" >
									</li>
									<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
										<input type="number" name="a_number_search" id="a_number_search" value="{$a_number_search}" placeholder="{$CMS->lang['a_number']}" class="form-control" >
									</li>
									<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
										<select name="a_status_search" id="a_status_search" class="form-control select2">
											{$option_a_status_search}
										</select>
									</li>
									<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
										<input type="submit" value="{$CMS->lang['comment_filter']}">
									</li>
								</ul>
							</figure>
						 </form>
				</section>
			</figure>

			<section class="add_table">
	            <div class="data_table">
	                <div class="table table_cus table-responsive" style="border-top: none;">
	                    <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
	                        <thead>
	                            <tr role="row">
	                            	<th data-orderable="false" data-sortable="false" style="margin:0px;padding:0px;"></th>
	                                <th>ID</th>
									<th>{$CMS->lang['a_account']}</th>
									<th>{$CMS->lang['a_holder']}</th>
									<th>{$CMS->lang['a_bank']}</th>
									<th>{$CMS->lang['a_number']}</th>
									<th>{$CMS->lang['a_status']}</th>
									<th>{$CMS->lang['a_time']}</th>
									<th data-orderable="false" data-sortable="false"></th>
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
              				<div class="block_bottom pagination pagination-sm">{$CMS->accounts->show_page}</div>
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
            <td class="grid-td" ><span class="number">#{$data['accounts_id']}</span></td>
            <td class="grid-td mwr"><a href="{$CMS->vars['root_domain']}/?site=accounts&act=show&id={$data['accounts_id']}">{$data['accounts_name']}</a></td>
			<td class="grid-td">{$data['accounts_holder']}</td>
			<td class="grid-td">{$data['accounts_bank']}</td>
			<td class="grid-td">{$data['accounts_number']}</td>
			<td class="grid-td">{$data['accounts_status_c']}</td>
			<td class="grid-td">{$data['accounts_time_c']}</td>
			<td class="grid-td">
EOF;
    
  		if ( $CMS->permit['accounts_edit'] )
  		{
    		$out .= <<<EOF
    		<a href="{$CMS->vars['root_domain']}/?site=accounts&act=edit&id={$data['accounts_id']}" class="edit"><i class="fa fa-edit" aria-hidden="true"></i></a>
EOF;
  		}

  		if ( $CMS->permit['accounts_delete'] )
 	 	{
    		$out .= <<<EOF
    		<a onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=accounts&act=delete&id={$data['accounts_id']}')" class="edit"><i class="fa fa-trash-o"></i></a>
EOF;
  		}

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
			<td colspan="9">
				<center><h5>{$CMS->lang['not_data']}</h5></center>
			</td>
		</tr>
EOF;
		return $out;
	}

	public function add() 
	{
		global $CMS, $DB, $member;
		$a_account =  isset($CMS->input['a_account'])  ? $CMS->input['a_account'] : '';
		$a_bank = isset($CMS->input['a_bank']) ? $CMS->input['a_bank'] : '';
		$a_number = isset($CMS->input['a_number']) ? $CMS->input['a_number'] : '';
		$a_holder = isset($CMS->input['a_holder']) ? $CMS->input['a_holder'] : '';
		$a_branch = isset($CMS->input['a_branch']) ? $CMS->input['a_branch'] : '';
		$a_status = isset($CMS->input['a_status']) ? $CMS->input['a_status'] :1;
		
		$option_a_status = "";
		for ($i = 0; $i <= 1; $i++) {
			if ($i == $a_status) {
				$option_a_status .= "<option value='{$i}' selected>{$CMS->lang['a_status_0'.$i]}</option>";
			} else {
				$option_a_status .= "<option value='{$i}'>{$CMS->lang['a_status_0'.$i]}</option>";
			}
		}
		
		$out = '';
		
		if (isset($_SESSION['a_error'])) {
			$out .= <<<EOF
			<div class="alert alert-danger alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['error']}</strong><br>
				{$_SESSION['a_error']}
			</div>
EOF;
			unset($_SESSION['a_error']);
		}
		
		if (isset($_SESSION['a_success'])) {
			$out .= <<<EOF
			<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['success']}</strong><br>
				{$_SESSION['a_success']}
			</div>
EOF;
			unset($_SESSION['a_success']);
		}

		$out .= <<<EOF
		<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=accounts&act=add" method="POST" >
		<section class="add_form main_form col-md-8" style="margin-left:auto;margin-right:auto;float:none;">
			<figure class="heading">
				<h3>{$CMS->lang['a_add']}</h3>
		  		<a href="{$CMS->vars['root_domain']}/?site=accounts" title=""><span class="font-icon font-icon-del"></span></a>
			</figure>
 			<figure class="box-typical box-typical box-typical-padding border">
 				<div class="row">
					<div class="col-lg-6">
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['a_account']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input class="form-control " type="text" name="a_account" id="a_account" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['a_account_err']}" value="{$a_account}">
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['a_number']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input class="form-control " type="number" name="a_number" id="a_number" value="{$a_number}">
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['a_holder']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input class="form-control " type="text" name="a_holder" id="a_holder" value="{$a_holder}">
								</span>
							</p>
						</fieldset>
					</div>
					<div class="col-lg-6">
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['a_bank']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input class="form-control " type="text" name="a_bank" id="a_bank" value="{$a_bank}">
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['a_branch']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input class="form-control " type="text" name="a_branch" id="a_branch" value="{$a_branch}">
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['a_status']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<select class="form-control select2" name="a_status" id="a_status" data-validation="[NOTEMPTY]">
										{$option_a_status}
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
				'module' => 'accounts',
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

		return $out;
	}

	public function edit($data=null) 
	{
		global $CMS, $DB, $member;

		$a_account = isset($CMS->input['a_account']) ? $CMS->input['a_account'] : $data['accounts_name'];
		$a_bank = isset($CMS->input['a_bank']) ? $CMS->input['a_bank'] : $data['accounts_bank'];
		$a_number = isset($CMS->input['a_number']) ? $CMS->input['a_number'] : $data['accounts_number'];
		$a_holder = isset($CMS->input['a_holder']) ? $CMS->input['a_holder'] : $data['accounts_holder'];
		$a_branch = isset($CMS->input['a_branch']) ? $CMS->input['a_branch'] : $data['accounts_branch'];
		$a_status = isset($CMS->input['a_status']) ? $CMS->input['a_status'] : $data['accounts_status'];
		
		$option_a_status = "";
		for ($i = 0; $i <= 1; $i++) {
			if ($i == $a_status) {
				$option_a_status .= "<option value='{$i}' selected>{$CMS->lang['a_status_0'.$i]}</option>";
			} else {
				$option_a_status .= "<option value='{$i}'>{$CMS->lang['a_status_0'.$i]}</option>";
			}
		}

		$out = '';

		if (isset($_SESSION['a_error'])) {
			$out .= <<<EOF
			<div class="alert alert-danger alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['error']}</strong><br>
				{$_SESSION['a_error']}
			</div>
EOF;
			unset($_SESSION['a_error']);
		}

		if (isset($_SESSION['a_success'])) {
			$out .= <<<EOF
			<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['success']}</strong><br>
				{$_SESSION['a_success']}
			</div>
EOF;
			unset($_SESSION['a_success']);
		}

		$out .= <<<EOF
		<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=accounts&act=edit&id={$data['accounts_id']}" method="POST" >
		<section class="add_form main_form col-md-8" style="margin-left:auto;margin-right:auto;float:none;">
			<figure class="heading">
				<h3>{$CMS->lang['a_edit']}</h3>
		  		<a href="{$CMS->vars['root_domain']}/?site=accounts" title=""><span class="font-icon font-icon-del"></span></a>
			</figure>
 			<figure class="box-typical box-typical box-typical-padding border">
 				<div class="row">
					<div class="col-lg-6">
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['a_account']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input class="form-control " type="text" name="a_account" id="a_account" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['a_account_err']}" value="{$a_account}">
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['a_number']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input class="form-control " type="number" name="a_number" id="a_number" value="{$a_number}">
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['a_holder']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input class="form-control " type="text" name="a_holder" id="a_holder" value="{$a_holder}">
								</span>
							</p>
						</fieldset>
					</div>
					<div class="col-lg-6">
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['a_bank']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input class="form-control " type="text" name="a_bank" id="a_bank" value="{$a_bank}">
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['a_branch']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<input class="form-control " type="text" name="a_branch" id="a_branch" value="{$a_branch}">
								</span>
							</p>
						</fieldset>
						<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['a_status']}</label>
							<p class="typeahead-field">
								<span class="typeahead-query">
					 				<select class="form-control select2" name="a_status" id="a_status" data-validation="[NOTEMPTY]">
										{$option_a_status}
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
				'module' => 'accounts',
				'detail_id' => $data['accounts_id'],
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

		return $out;
	}

	public function show($data=null) 
	{
		global $CMS, $DB, $member;

		$out = <<<EOF
		<section class="add_form main_form">
			<figure class="heading">
				<h3>{$CMS->lang['a_info']}: #{$data['accounts_id']}</h3>
		  		<a href="{$CMS->vars['root_domain']}/?site=accounts" title=""><span class="font-icon font-icon-del"></span></a>
			</figure>
			<figure class="box-typical box-typical box-typical-padding border">
 				<div class="row">
					<div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['a_account']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">
									{$data['accounts_name']}
								</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['a_bank']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">
									{$data['accounts_bank']}
								</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['a_number']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">
									{$data['accounts_number']}
								</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['a_holder']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">
									{$data['accounts_holder']}
								</div>	
							</div>
						</fieldset>
					</div>
					<div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['a_branch']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">
									{$data['accounts_branch']}
								</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['a_status']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">
									{$data['accounts_status_c']}
								</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['a_time']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">
									{$data['accounts_time_c']}
								</div>	
							</div>
						</fieldset>
					</div>
				</div>
			</figure>
		</section>
EOF;
		$footer_details = array(
				'type' => 'show',
				'module' => 'accounts',
				'detail_id' => $data['accounts_id'],
			);
		$out .= $CMS->global->footer_details($footer_details);

		return $out;
	}
}
?>
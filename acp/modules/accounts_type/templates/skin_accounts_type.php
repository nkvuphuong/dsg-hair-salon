<?php
class skin_accounts_type 
{
	public function head() 
	{
		global $CMS, $DB, $member;

		$at_id_search = isset($CMS->input['at_id']) ? $CMS->input['at_id'] : '';
		$at_name_search = isset($CMS->input['at_name']) ? $CMS->input['at_name'] : '';
		$at_status_search = isset($CMS->input['at_status']) ? $CMS->input['at_status'] : '';
		$option_at_status_search = "<option value=''>{$CMS->lang['at_status']}</option>";
		for ($i = 0; $i <= 1; $i++) 
		{
			if ($at_status_search != '' && $i == $at_status_search) 
			{
				$option_at_status_search .= "<option value='{$i}' selected>{$CMS->lang['at_status_0'.$i]}</option>";
			} 
			else
			{
				$option_at_status_search .= "<option value='{$i}'>{$CMS->lang['at_status_0'.$i]}</option>";
			}
		}

		$out = '';
		if (isset($_SESSION['at_error'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-danger alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['error']}</strong><br>
				{$_SESSION['at_error']}
			</div>
EOF;
			unset($_SESSION['at_error']);
		}

		if (isset($_SESSION['at_success'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['success']}</strong><br>
				{$_SESSION['at_success']}
			</div>
EOF;
			unset($_SESSION['at_success']);
		}

		$out .= <<<EOF
		<section class="add_table main_form">

			<figure class="heading">
				<h3>{$CMS->lang['at_title']}</h3>
				<figure class="pull-right right">
					<div class="search">
						<form method="post" id="frm_quickserch_product"  action="{$CMS->vars['root_domain']}/?site=accounts_type&act=search" style="display:inline-block">
							<input type="submit" class="fa-input" value="&#xf002;">
							<input name="at_name_search" id="at_name_search" type="text" value="{$at_name_search}" autocomplete="off" minlength="2" maxlength="64" placeholder="{$CMS->lang['gsearch_quick']}" style="position: :relative;">
							<div id="suggesstion-box" class="box_result_find" style="display:none"></div>
						</form>
						<a id="expand_formsearch" title="">{$CMS->lang['gsearch_advance']}<i class="fa fa-angle-double-right"></i></a>
					</div>
					
					{$CMS->global->importExportData($CMS->input['site'],'',1)}
					
					<a href="{$CMS->vars['root_domain']}/?site=accounts_type&act=add" title="" class="add_bill">{$CMS->lang['act_add']}</a>
					
					<a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
				</figure>
				<section class="search_adv" >
					<form method="post" id="formsearch_adv" style="display:none"  action="{$CMS->vars['root_domain']}/?site=accounts_type&act=search" >
						<figure class="box-typical box-typical box-typical-padding border">
								<h5>{$CMS->lang['gsearch_advance']}</h5>
								<ul class="input_li row match-height">
									<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
										<input type="number" name="at_id_search" id="at_id_search" value="{$at_id_search}" placeholder="ID" class="form-control" >
									</li>
									<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
										<input type="text" name="at_name_search" id="at_name_search" value="{$at_name_search}" placeholder="{$CMS->lang['at_name']}" class="form-control" >
									</li>
									<li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
										<select name="at_status_search" id="at_status_search" class="form-control select2">
											{$option_at_status_search}
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
	            
	                {$CMS->global->languageTab('langTab')}
	                
	                <div class="table table_cus table-responsive" style="border-top: none;">
	                    <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
	                        <thead>
	                            <tr role="row">
	                            	<th data-orderable="false" data-sortable="false" style="margin:0px;padding:0px;"></th>
	                                <th>ID</th>
									<th>{$CMS->lang['at_code']}</th>
									<th>{$CMS->lang['at_name']}</th>
									<th>{$CMS->lang['at_parent']}</th>
									<th>{$CMS->lang['at_status']}</th>
									<th>{$CMS->lang['at_time']}</th>
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
              				<div class="block_bottom pagination pagination-sm">{$CMS->accounts_type->show_page}</div>
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
//        		order: [[ 1,"desc"]],
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

//        $data = $CMS->accounts_type->convertValue($data);

		$style = $_SESSION['is_mobile'] == true ? "padding-right:27px;" : "";

		$out = <<<EOF
		<tr keyrow="{$data['keyrow']}" class="row-grid" rowtr="{$data['rowtr']}">
			<td class="grid-td" style="margin:0px;padding:0px;{$style}"></td>
            <td class="grid-td" ><span class="number">#{$data['accounts_type_id']}</span></td>
            <td class="grid-td"><a href="{$CMS->vars['root_domain']}/?site=accounts_type&act=show&id={$data['accounts_type_id']}">{$data['accounts_type_code']}</a></td>
            <td class="grid-td">
EOF;
		if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $out .= <<<EOF
			<span class="langTab" lang="{$langCode}">{$data['accounts_type_name'][$langCode]}</span>
EOF;
            }
        }
        else
        {
            $out .= <<<EOF
			{$data['accounts_type_name']}
EOF;
        }


		$out .= <<<EOF
		    </td>
			<td class="grid-td">{$data['accounts_type_parent_c']}</td>
			<td class="grid-td">{$data['accounts_type_status_c']}</td>
			<td class="grid-td">{$data['accounts_type_time_c']}</td>
			<td class="grid-td">
EOF;
    
  		if ( $CMS->permit['accounts_type_edit'] )
  		{
    		$out .= <<<EOF
    		<a href="{$CMS->vars['root_domain']}/?site=accounts_type&act=edit&id={$data['accounts_type_id']}" class="edit"><i class="fa fa-edit" aria-hidden="true"></i></a>
EOF;
  		}

  		if ( $CMS->permit['accounts_type_delete'] )
 	 	{
    		$out .= <<<EOF
    		<a onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=accounts_type&act=delete&id={$data['accounts_type_id']}')" class="edit"><i class="fa fa-trash-o"></i></a>
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
EOF;
		return $out;
	}

	public function add() 
	{
		global $CMS, $DB, $member;
		$at_parent = isset($CMS->input['at_parent']) ? $CMS->input['at_parent'] : '';
		$at_code = isset($CMS->input['at_code']) ? $CMS->input['at_code'] : '';
		$at_name = isset($CMS->input['at_name']) ? $CMS->input['at_name'] : '';
		$at_status = isset($CMS->input['at_status']) ? $CMS->input['at_status'] : 1;
		
		$option_at_parent = "<option value=''>{$CMS->lang['select']}</option>";
		$parent = $CMS->accounts_type->getParent();
		foreach ($parent as $p) {

            $p = $CMS->accounts_type->convertValue($p);
            $p['accounts_type_name'] = $p['accounts_type_name'][$CMS->vars['default_language']];

			if ($p['accounts_type_id'] == $at_parent) {
				$option_at_parent .= "<option value='{$p['accounts_type_id']}' selected>{$p['accounts_type_name']}</option>";
			} else {
				$option_at_parent .= "<option value='{$p['accounts_type_id']}' >{$p['accounts_type_name']}</option>";
			}
		}
		
		$option_at_status = "";
		for ($i = 0; $i <= 1; $i++) {
			if ($i == $at_status) {
				$option_at_status .= "<option value='{$i}' selected>{$CMS->lang['at_status_0'.$i]}</option>";
			} else {
				$option_at_status .= "<option value='{$i}'>{$CMS->lang['at_status_0'.$i]}</option>";
			}
		}
		
		$out = '';
		
		if (isset($_SESSION['at_error'])) {
			$out .= <<<EOF
			<div class="alert alert-danger alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['error']}</strong><br>
				{$_SESSION['at_error']}
			</div>
EOF;
			unset($_SESSION['at_error']);
		}

		if (isset($_SESSION['at_success'])) {
			$out .= <<<EOF
			<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['success']}</strong><br>
				{$_SESSION['at_success']}
			</div>
EOF;
			unset($_SESSION['at_success']);
		}

		$out .= <<<EOF
		<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=accounts_type&act=add" method="POST" >
		<section class="add_form main_form">
		<div class="row">
		<div class="col-md-6" style="float:none;margin-left:auto;margin-right:auto;">
			<figure class="heading">
				<h3>{$CMS->lang['at_add']}</h3>
		  		<a href="{$CMS->vars['root_domain']}/?site=accounts_type{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
			</figure>
			<figure class="box-typical box-typical box-typical-padding border">
			
			    {$CMS->global->languageTab('langTab')}
			    
				<fieldset class="form-group row">
					<label class="col-lg-3 form-label" >{$CMS->lang['at_parent']}</label>
					<p class="col-lg-9">
						<span class="typeahead-query">
			 				<select class="form-control select2" name="at_parent" id="at_parent">
								{$option_at_parent}
							</select>
						</span>
					</p>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-lg-3 form-label" >{$CMS->lang['at_group']}</label>
					<p class="col-lg-9">
						<span class="typeahead-query">
			 				<select class="form-control select2" name="at_group" id="at_group">
								{$CMS->accounts_type->loadGroupOption($CMS->input['at_group'])}
							</select>
						</span>
					</p>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-lg-3 form-label" >{$CMS->lang['at_code']}</label>
					<p class="col-lg-9">
						<span class="typeahead-query">
			 				<input class="form-control " type="number" name="at_code" id="at_code" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['at_code_err']}" value="{$at_code}">
						</span>
					</p>
				</fieldset>
EOF;

		if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $validate = $langCode == $CMS->vars['default_language'] ? "data-validation=\"[NOTEMPTY]\" data-validation-message=\"{$CMS->lang['at_name_err']}\"" : "";

                $out .= <<<EOF
				<fieldset class="form-group row langTab" lang="{$langCode}">
					<label class="col-lg-3 form-label" >{$CMS->lang['at_name']}</label>
					<p class="col-lg-9">
						<span class="typeahead-query">
			 				<input class="form-control " type="text" name="at_name[{$langCode}]" id="at_name[{$langCode}]" {$validate} value="{$at_name[$langCode]}">
						</span>
					</p>
				</fieldset>
EOF;
            }
        }
        else
        {
            $out .= <<<EOF
				<fieldset class="form-group row">
					<label class="col-lg-3 form-label" >{$CMS->lang['at_name']}</label>
					<p class="col-lg-9">
						<span class="typeahead-query">
			 				<input class="form-control " type="text" name="at_name" id="at_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['at_name_err']}" value="{$at_name}">
						</span>
					</p>
				</fieldset>
EOF;
        }


		$out .= <<<EOF
				<fieldset class="form-group row">
					<label class="col-lg-3 form-label" >{$CMS->lang['at_status']}</label>
					<p class="col-lg-9">
						<span class="typeahead-query">
			 				<select class="form-control select2" name="at_status" id="at_status" data-validation="[NOTEMPTY]">
								{$option_at_status}
							</select>
						</span>
					</p>
				</fieldset>
			</figure>
		</div>
		</div>
		</section>
EOF;
				
		$footer_details = array(
				'type' => 'add',
				'module' => 'accounts_type',
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
	public function edit($data = null) 
	{
		global $CMS, $DB, $member;
		$at_parent = isset($CMS->input['at_parent']) ? $CMS->input['at_parent'] : $data['accounts_type_parent'];
		$at_code = isset($CMS->input['at_code']) ? $CMS->input['at_code'] : $data['accounts_type_code'];
		$at_name = isset($CMS->input['at_name']) ? $CMS->input['at_name'] : $data['accounts_type_name'];
		$at_status = isset($CMS->input['at_status']) ? $CMS->input['at_status'] : $data['accounts_type_status'];
		$at_group = isset($CMS->input['at_group']) ? $CMS->input['at_group'] : $data['accounts_group'];

		$option_at_parent = "<option value=''>{$CMS->lang['select']}</option>";
		$parent = $CMS->accounts_type->getParent($data['accounts_type_id']);
		foreach ($parent as $p) {

            $p = $CMS->accounts_type->convertValue($p);
            $p['accounts_type_name'] = $p['accounts_type_name'][$CMS->vars['default_language']];

			if ($p['accounts_type_id'] == $at_parent) {
				$option_at_parent .= "<option value='{$p['accounts_type_id']}' selected>{$p['accounts_type_name']}</option>";
			} else {
				$option_at_parent .= "<option value='{$p['accounts_type_id']}' >{$p['accounts_type_name']}</option>";
			}
		}
		
		$option_at_status = "";
		for ($i = 0; $i <= 1; $i++) {
			if ($i == $at_status) {
				$option_at_status .= "<option value='{$i}' selected>{$CMS->lang['at_status_0'.$i]}</option>";
			} else {
				$option_at_status .= "<option value='{$i}'>{$CMS->lang['at_status_0'.$i]}</option>";
			}
		}
		
		$out = '';
		
		if (isset($_SESSION['at_error'])) {
			$out .= <<<EOF
			<div class="alert alert-danger alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['error']}</strong><br>
				{$_SESSION['at_error']}
			</div>
EOF;
			unset($_SESSION['at_error']);
		}

		if (isset($_SESSION['at_success'])) {
			$out .= <<<EOF
			<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['success']}</strong><br>
				{$_SESSION['at_success']}
			</div>
EOF;
			unset($_SESSION['at_success']);
		}

		$out .= <<<EOF
		<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=accounts_type&act=edit&id={$data['accounts_type_id']}" method="POST" >
		<section class="add_form main_form">
		<div class="row">
		<div class="col-md-6" style="float:none;margin-left:auto;margin-right:auto;">
			<figure class="heading">
				<h3>{$CMS->lang['at_edit']}</h3>
		  		<a href="{$CMS->vars['root_domain']}/?site=accounts_type{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
			</figure>
			<figure class="box-typical box-typical box-typical-padding border">
			
			    {$CMS->global->languageTab('langTab')}
			
				<fieldset class="form-group row">
					<label class="col-lg-3 form-label" >{$CMS->lang['at_parent']}</label>
					<p class="col-lg-9">
						<span class="typeahead-query">
			 				<select class="form-control select2" name="at_parent" id="at_parent">
								{$option_at_parent}
							</select>
						</span>
					</p>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-lg-3 form-label" >{$CMS->lang['at_group']}</label>
					<p class="col-lg-9">
						<span class="typeahead-query">
			 				<select class="form-control select2" name="at_group" id="at_group">
								{$CMS->accounts_type->loadGroupOption($at_group)}
							</select>
						</span>
					</p>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-lg-3 form-label" >{$CMS->lang['at_code']}</label>
					<p class="col-lg-9">
						<span class="typeahead-query">
			 				<input class="form-control " type="number" name="at_code" id="at_code" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['at_code_err']}" value="{$at_code}">
						</span>
					</p>
				</fieldset>
EOF;

		if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $validate = $langCode == $CMS->vars['default_language'] ? "data-validation=\"[NOTEMPTY]\" data-validation-message=\"{$CMS->lang['at_name_err']}\"" : "";

                $out .= <<<EOF
				<fieldset class="form-group row langTab" lang="{$langCode}">
					<label class="col-lg-3 form-label" >{$CMS->lang['at_name']}</label>
					<p class="col-lg-9">
						<span class="typeahead-query">
			 				<input class="form-control " type="text" name="at_name[{$langCode}]" id="at_name[{$langCode}]" {$validate} value="{$at_name[$langCode]}">
						</span>
					</p>
				</fieldset>
EOF;
            }
        }
        else
        {
            $out .= <<<EOF
				<fieldset class="form-group row">
					<label class="col-lg-3 form-label" >{$CMS->lang['at_name']}</label>
					<p class="col-lg-9">
						<span class="typeahead-query">
			 				<input class="form-control " type="text" name="at_name" id="at_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['at_name_err']}" value="{$at_name}">
						</span>
					</p>
				</fieldset>
EOF;
        }


		$out .= <<<EOF
				<fieldset class="form-group row">
					<label class="col-lg-3 form-label" >{$CMS->lang['at_status']}</label>
					<p class="col-lg-9">
						<span class="typeahead-query">
			 				<select class="form-control select2" name="at_status" id="at_status" data-validation="[NOTEMPTY]">
								{$option_at_status}
							</select>
						</span>
					</p>
				</fieldset>
			</figure>
		</div>
		</div>
		</section>
EOF;
		
		$footer_details = array(
				'type' => 'edit',
				'module' => 'accounts_type',
				'detail_id' => $data['accounts_type_id'],
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

	public function show($data = null) 
	{
		global $CMS, $DB, $member;

		$out = <<<EOF
		<section class="add_form main_form">
		<div class="row">
		<div class="col-md-6" style="float:none;margin-left:auto;margin-right:auto;">
			<figure class="heading">
				<h3>{$CMS->lang['at_info']} : #{$data['accounts_type_id']}</h3>
		  		<a href="{$CMS->vars['root_domain']}/?site=accounts_type{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
			</figure>
			<figure class="box-typical box-typical box-typical-padding border">
			    {$CMS->global->languageTab('langTab')}
 				<div class="row">
					<div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['at_parent']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['accounts_type_parent_c']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['at_group']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$CMS->lang['at_group_'.$data['accounts_group']]}</div>	
							</div>
                        </fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['at_code']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['accounts_type_code']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['at_name']}</label>
							<div class="col-xl-8 form-control-span2"> 
EOF;
		if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $out .= <<<EOF
                            <div class="form-label semibold langTab"  lang="{$langCode}">{$data['accounts_type_name'][$langCode]}</div>	
EOF;
            }

        }
        else
        {
            $out .= <<<EOF
                            <div class="form-label semibold">{$data['accounts_type_name']}</div>	
EOF;
        }


		$out .= <<<EOF
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['at_status']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['accounts_type_status_c']}</div>	
							</div>
						</fieldset>
						<fieldset class="form-group row">
							<label class="col-xl-4 form-control-label2" >{$CMS->lang['at_time']}</label>
							<div class="col-xl-8 form-control-span2"> 
						 		<div class="form-label semibold">{$data['accounts_type_time_c']}</div>	
							</div>
						</fieldset>
					</div>
				</div>
			</figure>
		</div>
		</div>
		</section>
EOF;

		$footer_details = array(
				'type' => 'show',
				'module' => 'accounts_type',
				'detail_id' => $data['accounts_type_id'],
			);
		$out .= $CMS->global->footer_details($footer_details);

		return $out;
	}
}
?>
<?php
class skin_group_customer 
{
	public function head() 
	{
		global $CMS, $DB, $member;

		$out = '';

		if (isset($_SESSION['gc_error'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-danger alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['error']}</strong> {$_SESSION['gc_error']}
			</div>
EOF;
			unset($_SESSION['gc_error']);
		}

		if (isset($_SESSION['gc_success'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['success']}</strong> {$_SESSION['gc_success']}
			</div>
EOF;
			unset($_SESSION['gc_success']);
		}

		$out .= <<<EOF
		<section class="add_table main_form">

			<figure class="heading">
                    <h3>{$CMS->lang['gc_title']}</h3>
				<figure class="pull-right right">
                    {$CMS->global->importExportData($CMS->input['site'],'',1)}
					<a href="{$CMS->vars['root_domain']}/?site=group_customer&act=add" title="" class="add_bill">{$CMS->lang['gc_add']}</a>
					<a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
				</figure>
			</figure>
			<section class="tabs-section">
			    <div class="tabs-section-nav tabs-section-nav-inline">
			        <ul class="nav" role="tablist">
			            <li class="nav-item">
			                <a class="nav-link" href="{$CMS->vars['root_domain']}/?site=customer" >
			                    {$CMS->lang['cus_list']}
			                </a>
			            </li>
			            <li class="nav-item">
			                <a class="nav-link active" href="{$CMS->vars['root_domain']}/?site=group_customer">
			                    {$CMS->lang['cus_category_list']}
			                </a>
			            </li>
			        </ul>
			    </div><!--.tabs-section-nav-->
			</section><!--.tabs-section-->
			<section class="add_table">
	            <div class="data_table">
	                <div class="table table_cus table-responsive" style="border-top: none;">
	                    <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
	                        <thead>
	                            <tr role="row">
	                            	<th data-orderable="false" data-sortable="false" aria-label="" style="margin:0px;padding:0px;"></th>
	                                <th>ID</th>
									<th>{$CMS->lang['gc_name']}</th>
									<th>{$CMS->lang['customer']}</th>
									<th width="5%"></th>
									<th>{$CMS->lang['gc_status']}</th>
									<th>{$CMS->lang['gc_time']}</th>
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
	              			<div class="block_bottom pagination pagination-sm">{$CMS->group_customer->show_page}</div>
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
			<td style="margin:0px;padding:0px;{$style}"></td>
            <td class="grid-td" ><span class="number">#{$data['gc_id']}</span></td>
            <td class="grid-td mwr">{$data['gc_name']}</td>
            <td class="grid-td"><a href="{$CMS->vars['root_domain']}/?site=customer&group={$data['gc_id']}">{$data['cus_nums']}</a></td>
            <td class="grid-td"><a class="btn btn-success" href="{$CMS->vars['root_domain']}/?site=customer&group={$data['gc_id']}"><i class="fa fa-search" aria-hidden="true"></i></a></td>
			<td class="grid-td">{$data['gc_status_c']}</td>
			<td class="grid-td">{$data['gc_time_c']}</td>
			<td class="grid-td" >
EOF;
    
  		if ( $CMS->permit['user_edit'] )
  		{
    		$out .= <<<EOF
    		<a href="{$CMS->vars['root_domain']}/?site=group_customer&act=edit&id={$data['gc_id']}" class="edit"><i class="fa fa-edit" aria-hidden="true"></i></a>
EOF;
  		}

  		if ( $CMS->permit['user_delete'] )
  		{
    		$out .= <<<EOF
    		<a onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=group_customer&act=delete&id={$data['gc_id']}')" class="edit"><i class="fa fa-trash-o"></i></a>
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

		$out = '';

		if (isset($_SESSION['gc_error'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-danger alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['error']}</strong> {$_SESSION['gc_error']}
			</div>
EOF;
			unset($_SESSION['gc_error']);
		}

		if (isset($_SESSION['gc_success'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['success']}</strong> {$_SESSION['gc_success']}
			</div>
EOF;
			unset($_SESSION['gc_success']);
		}

		$out .= <<<EOF
		<section class="add_form main_form col-md-6" style="margin-left:auto;margin-right:auto;float:none;">
			<figure class="heading">
				<h3>{$CMS->lang['gc_info']}</h3>
		  		<a href="{$CMS->vars['root_domain']}/?site=group_customer" title=""><span class="font-icon font-icon-del"></span></a>
			</figure>
			<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=group_customer&act=add" method="POST">
 			<figure class="box-typical box-typical box-typical-padding border">
 				<div class="row">
					<div class="col-lg-9">
			 			<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['gc_name']}</label>
					 		<input class="form-control " type="text" name="gc_name" id="gc_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['gc_name_err']}" value="{$CMS->input['gc_name']}">
						</fieldset>
			 		</div>
			 		<div class="col-lg-3">
			 			<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['gc_status']}</label>
					 		<div class="checkbox-toggle">
								<input type="checkbox" id="check-toggle-2" name="gc_status" checked>
								<label for="check-toggle-2">{$CMS->lang['gc_status_01']}</label>
							</div>
						</fieldset>
			 		</div>
			 	</div>
			 	<div class="row">
					<div class="col-md-12">
						<fieldset class="form-group" style="margin-bottom: 0px;">
							<button class="btn btn_add_line" style="margin: 0 auto;display: block;" type="submit">{$CMS->lang['gc_add_button']}</button>
						</fieldset>
					</div>
				</div>
			</figure>
		</section>
EOF;

		return $out;
	}

	public function edit($data=null) 
	{
		global $CMS, $DB, $member;

		if ($data['gc_status'] == 1) {
			$checked = 'checked';
		} else {
			$checked = '';
		}

		$out = '';

		if (isset($_SESSION['gc_error'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-danger alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['error']}</strong> {$_SESSION['gc_error']}
			</div>
EOF;
			unset($_SESSION['gc_error']);
		}

		if (isset($_SESSION['gc_success'])) 
		{
			$out .= <<<EOF
			<div class="alert alert-aquamarine alert-border-left alert-close alert-dismissible fade in" role="alert">
				<strong>{$CMS->lang['success']}</strong> {$_SESSION['gc_success']}
			</div>
EOF;
			unset($_SESSION['gc_success']);
		}

		$out .= <<<EOF
		<section class="add_form main_form col-md-6" style="margin-left:auto;margin-right:auto;float:none;">
			<figure class="heading">
				<h3>{$CMS->lang['gc_add']}</h3>
		  		<a href="{$CMS->vars['root_domain']}/?site=group_customer" title=""><span class="font-icon font-icon-del"></span></a>
			</figure>
			<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=group_customer&act=edit&id={$data['gc_id']}" method="POST">
 			<figure class="box-typical box-typical box-typical-padding border">
 				<div class="row">
					<div class="col-lg-9">
			 			<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['gc_name']}</label>
					 		<input class="form-control " type="text" name="gc_name" id="gc_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['gc_name_err']}" value="{$data['gc_name']}">
						</fieldset>
			 		</div>
			 		<div class="col-lg-3">
			 			<fieldset class="form-group">
							<label class="form-label" >{$CMS->lang['gc_status']}</label>
					 		<div class="checkbox-toggle">
								<input type="checkbox" id="check-toggle-2" name="gc_status" {$checked}>
								<label for="check-toggle-2">{$CMS->lang['gc_status_01']}</label>
							</div>
						</fieldset>
			 		</div>
			 	</div>
			 	<div class="row">
					<div class="col-md-12">
						<fieldset class="form-group" style="margin-bottom: 0px;">
							<button class="btn btn_add_line" style="margin: 0 auto;display: block;" type="submit">{$CMS->lang['gc_edit_button']}</button>
						</fieldset>
					</div>
				</div>
			</figure>
		</section>
EOF;
		return $out;
	}
}
?>
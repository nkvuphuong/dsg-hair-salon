<?php

class skin_partner_delivery 
{
	public function head() 
	{
		global $CMS, $DB, $member;

		$output='';

		$output.=<<<EOF
		<section class="add_table main_form">

			<script type="text/javascript" src="{$CMS->vars['js_url']}/acp_partner_delivery.js"></script>

			<figure class="heading">
				<h3>{$CMS->lang['partner_delivery_head']}</h3>
				<figure class="pull-right right">
					<div class="search">
						<form method="post" id="frm_quickserch_partner_delivery_1"  action="{$CMS->vars['root_domain']}/?site=partner_delivery&act=search" style="display:inline-block">
							<input type="submit" class="fa-input" value="&#xf002;">
							<input name="p_delivery_name" id="p_delivery_name" type="text" value="{$CMS->input['p_delivery_name']}" autocomplete="off" minlength="2" maxlength="64" placeholder="{$CMS->lang['partner_delivery_namecode']}" style="position: :relative;">
							<div id="suggesstion-box" class="box_result_find" style="display:none"></div>
						</form>
					 
					</div>
					<a href="{$CMS->vars['root_domain']}/?site=partner_delivery&act=add" title="" class="add_bill">{$CMS->lang['add_partner_delivery']}</a>
					<a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
				</figure>
			 
			</figure>

<section class="add_table">
	 <div class="data_table">
			<table id="example" class="display table table_cus" cellspacing="0" width="100%">
				<thead>
					 
					<tr>
						
EOF;

					if($_SESSION['is_mobile'] == true)
					{
						$output .=<<<EOF
						<th  data-orderable="false" data-sortable="false" width="1%"></th>
						
						<th width="10%"  data-sortable="true" >{$CMS->lang['p_delivery_name']}</th>
						<th  width="3%"   data-orderable="false"  >ID</th>
					


EOF;

					}
					else
					{
						$output .=<<<EOF
					 	<th  width="3%"   data-orderable="false"  >ID</th>
						<th width="10%"  data-sortable="true" >{$CMS->lang['p_delivery_name']}</th>

EOF;
					}
					$output .=<<<EOF
						 
						<th  width="10%"  data-orderable="false"  >{$CMS->lang['p_delivery_code']}</th>
						<th width="7%"  data-orderable="false" >{$CMS->lang['p_delivery_type']}</th>
						<th width="12%"  data-orderable="false" >{$CMS->lang['p_delivery_phone']}</th>
	 
						<th width="6%" data-orderable="false" >{$CMS->lang['p_delivery_email']}</th>
						<th width="5%"  data-orderable="false"  data-sortable="false">{$CMS->lang['p_delivery_time']}</th>
						<th width="6%"  data-orderable="false"> </th>
		 
					</tr>
				</thead>
				<tbody>
EOF;

		return $output;
	}
	
	public function foot() 
	{
		global $CMS, $DB, $member;

		$output=<<<EOF
						
					</tbody>
				</table>

		</div><!--.box-typical-body-->
		 
	 </section>
		<div class="block_bottom pagination pagination-sm">
			{$CMS->partner_delivery->show_page}
		</div>
		 
</section>

EOF;
	 if($_SESSION['is_mobile'] == true)
		{
				$output .=<<<EOF
				 <script>
						$(function() {
							$('#example').DataTable({
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
								      emptyTable: 'Không tìm thấy dữ liệu!'
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
	
	public function mid($data=NULL) 
	{
		global $CMS, $DB, $member;
		
		$output .=<<<EOF
		<tr>

EOF;

		if($_SESSION['is_mobile'] == true)
		{
			$output .=<<<EOF
			<td></td>
			
			<td class="add_pro">
			 
					{$data['p_delivery_name_bk']}
			</td>
			<td>
				#{$data['p_delivery_id']}
			</td>
EOF;
		}
		else
		{
			$output .=<<<EOF

			<td>
				#{$data['p_delivery_id']}
			</td>
			<td class="add_pro">
			 
					{$data['p_delivery_name_bk']}
			</td>
EOF;
		}
		$output .=<<<EOF
			<td>
				{$data['p_delivery_code']}
			</td>
			<td>
				{$data['p_delivery_type_bk']}
			</td>
			<td>
				{$data['p_delivery_phone']}
			</td>
			<td>
				{$data['p_delivery_email']}
			</td>
			<td>
				 {$data['p_delivery_time_bk']}
			</td>
			<td>
				 
EOF;
						if($CMS->permit['partner_delivery_edit'] == 1)
						{
							$output .=<<<EOF
							<a href="{$CMS->vars['root_domain']}/?site=partner_delivery&act=edit&id={$data['p_delivery_id']}" title""="" class="edit"><i class="fa fa-edit"></i></a>
EOF;

						}

 
						if($CMS->permit['partner_delivery_delete'] == 1)
						{
							$output .=<<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=partner_delivery&act=delete&id={$data['p_delivery_id']}');"  class="edit"><i class="fa fa-trash-o"></i></a>

EOF;

						}
					$out .=<<<EOF
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
			<td colspan="8" align="center" class="no_data"><h6>Not partner_delivery</h6></td>
		</tr>
EOF;

		return $output;
	}

	public function show($data=NULL) {
		global $CMS, $DB, $member;
			$out = <<<EOF


<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['partner_delivery_info']}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site=partner_delivery" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>

	 <figure class="box-typical box-typical box-typical-padding border">
 		<div class="row">
 			<div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_delivery_type']}</label>
					<div class="col-xl-9 form-control-span2"> 
 

							{$data['p_delivery_type_bk']}
				 
					</div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_delivery_name']}</label>
					<div class="col-xl-9 form-control-span2"> 
				 
							{$data['p_delivery_name']}
		 
				    </div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_delivery_code']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['p_delivery_code']} 
					</div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_delivery_website']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['p_delivery_website']}
						 
					</div>
				</fieldset>

				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >
						<span>{$CMS->lang['shipping_tracking_url']}</span>
						<br>
						<span class="note-text">{$CMS->lang['shipping_tracking_url_note']}</span>
					</label>
					<div class="col-xl-9 form-control-span2"> {$data['shipping_tracking_url']}</div>
				</fieldset>

				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_delivery_phone']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['p_delivery_phone']} 
					</div>
				</fieldset>
			</div>
			
			<div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">	
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_delivery_email']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['p_delivery_email']}
						 
					</div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_delivery_address']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['p_delivery_address']}
						 
					</div>
				</fieldset>
				
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_delivery_time']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['p_delivery_time_bk']}
						 
					</div>
				</fieldset>

				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['p_delivery_note']}</label>
					<div class="col-xl-9 form-control-span2"> 
						  {$data['p_delivery_note']}
						 
					 
					</div>
				</fieldset>
			</div> 
	 </div>
 	</figure>	
</section>

		<section class="add_cart_footer">
			 {$CMS->global->footer_back(array("list" =>"{$CMS->vars['root_domain']}/?site=partner_delivery"))} 

			  {$CMS->global->footer_show(array("list" =>"{$CMS->vars['root_domain']}/?site=partner_delivery", "detail_id" => $data['p_delivery_id']))} 
 
		</section>


EOF;
		return $out;
	}
    
	public function edit($data=null) {
		global $CMS, $DB, $member;



		$data['partner_delivery_status'] = $data['partner_delivery_status'] ? $data['partner_delivery_status'] : 1;
        if(! $data['p_delivery_type']) { $p_delivery_type =1; }
        else
        {
        	$p_delivery_type = $data['p_delivery_type'];
        }
		$option_p_delivery_type ="";
		for ($i = 1; $i <= 2; $i++) {
			if ($i == $p_delivery_type) {
				$option_p_delivery_type .= "
    			 <div class='radio w25'><input type='radio' checked  name='p_delivery_type' id='radio-{$i}' value='{$i}'><label for='radio-{$i}'>{$CMS->lang['p_delivery_type_'.$i]}</label></div>	

				";
			} else {
				$option_p_delivery_type .= "  <div class='radio w25'><input type='radio' name='p_delivery_type' id='radio-{$i}' value='{$i}'><label for='radio-{$i}'>{$CMS->lang['p_delivery_type_'.$i]}</label></div>	";
			}
		}



		$out=<<<EOF

 
 <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=partner_delivery&act=edit_do&id={$data['p_delivery_id']}" method="POST" enctype="multipart/form-data">
  <section class="add_form main_form"> 
	<figure class="heading">
		<h3>{$CMS->lang['edit_partner_delivery']}</h3>
		 <a href="{$CMS->vars['root_domain']}/?site=partner_delivery" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>

   <figure class="box-typical box-typical box-typical-padding border">
 
 
	<div class="row">
 
	 	<div class="col-md-6">
	 
			  
	 				<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_delivery_type']}</label>
							<div class="form-control-wrapper">	
								 {$option_p_delivery_type}
							</div>	
							
					</fieldset>
			 		<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_delivery_name']}<span style="color:red">(*)</span></label>
							<div class="form-control-wrapper">	
								<input class="form-control" type="text" name="p_delivery_name" id="p_delivery_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['partner_delivery_empty_name']}" value="{$data['p_delivery_name']}"   >
							</div>	
							
					</fieldset>

					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_delivery_code']} <span style="color:red">(*)</span></label>
							 <div class="form-control-wrapper">	
								<input class="form-control" type="text" name="p_delivery_code" id="pg_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['partner_delivery_empty_code']}" value="{$data['p_delivery_code']}"   >
							</div>	
							
					</fieldset>
					 <fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_delivery_phone']} </label>
							  
								<input class="form-control" type="text" name="p_delivery_phone" id="p_delivery_phone" maxlength="15" value="{$data['p_delivery_phone']}"   >
							 
							
					</fieldset>
				 	
				 
			 	 	 <fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_delivery_address']}</label>
							  
								<input class="form-control" type="text" name="p_delivery_address" id="p_delivery_address"   value="{$data['p_delivery_address']}"   >
							 
							
					</fieldset>
 
	 	</div><!-- col-md-6 -->
	 	


	 	<div class="col-md-6">
	 		<fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['p_delivery_website']}</label>
					<input class="form-control" type="text" name="p_delivery_website" id="p_delivery_website" placeholder="example.com"  value="{$data['p_delivery_website']}"   >						
		 	</fieldset>
	 		 <fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_delivery_email']}</label>
							  
								<input class="form-control" type="text" name="p_delivery_email" id="p_delivery_email"   data-validation="[EMAIL]" data-validation-message="{$CMS->lang['partner_delivery_email_valid']}"  value="{$data['p_delivery_email']}"   >						
			 </fieldset>
			 	
			  <fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_delivery_group']}</label>
							  
								<input class="form-control" type="text" name="p_delivery_group" id="p_delivery_group"   value="{$data['p_delivery_group']}"   >						
			 </fieldset>

			 <fieldset class="form-group">
			 	<label class="form-label">{$CMS->lang['shipping_tracking_url']}</label>
			 	<div class="note-text">{$CMS->lang['shipping_tracking_url_note']}</div>
			 	<input class="form-control" type="text" name="shipping_tracking_url" id="shipping_tracking_url"   value="{$data['shipping_tracking_url']}">
			 </fieldset>
			 	
	 		 <fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['p_delivery_note']}</label>
	
						<textarea rows="3" class="form-control" name="p_delivery_note" id="p_delivery_note" >{$data['p_delivery_note']}</textarea> 
				 
			</fieldset>
	 		 
		 
			
			
	 	</div>	<!-- col-md-6 -->

	
		
		

	 

	</div>

	</figure>
</section>
	<section class="add_cart_footer">
		 
 
		 {$CMS->global->footer_back(array("list" =>"{$CMS->vars['root_domain']}/?site=partner_delivery", "detail_id" => "{$data['p_delivery_id']}"))}
		 {$CMS->global->footer_edit()}

		</section>

 
</form>
 <script>
 $(document).ready(function(){

	validate_form_custom("#form-signin_v1",".act_submit_save");
});

</script>
 
 <script src="{$CMS->vars['js_acp']}/partner_delivery.js"></script>
EOF;
		return $out;
	}
    
	public function add($data = "") 
    {
		global $CMS, $DB, $member;
        
        $data['partner_delivery_status'] = $data['partner_delivery_status'] ? $data['partner_delivery_status'] : 1;
        if(! $data['p_delivery_type']) { $p_delivery_type =1; }
		$option_p_delivery_type ="";
		for ($i = 1; $i <= 2; $i++) {
			if ($i == $p_delivery_type) {
				$option_p_delivery_type .= "
    			 <div class='radio w25'><input type='radio' checked  name='p_delivery_type' id='radio-{$i}' value='{$i}'><label for='radio-{$i}'>{$CMS->lang['p_delivery_type_'.$i]}</label></div>	

				";
			} else {
				$option_p_delivery_type .= "  <div class='radio w25'><input type='radio' name='p_delivery_type' id='radio-{$i}' value='{$i}'><label for='radio-{$i}'>{$CMS->lang['p_delivery_type_'.$i]}</label></div>	";
			}
		}



		$out=<<<EOF

 
 <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=partner_delivery&act=add_do" method="POST" enctype="multipart/form-data">
  <section class="add_form main_form"> 
	<figure class="heading">
		<h3>{$CMS->lang['add_partner_delivery']}</h3>
		 <a href="{$CMS->vars['root_domain']}/?site=partner_delivery" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>

   <figure class="box-typical box-typical box-typical-padding border">
 
 
	<div class="row">
 
	 	<div class="col-md-6">
	 		 
			  
	 				<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_delivery_type']}</label>
							<div class="form-control-wrapper">	
								 {$option_p_delivery_type}
							</div>	
							
					</fieldset>
			 		<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_delivery_name']}<span style="color:red">(*)</span></label>
							<div class="form-control-wrapper">	
								<input class="form-control" type="text" name="p_delivery_name" id="p_delivery_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['partner_delivery_empty_name']}" value="{$data['p_delivery_name']}"   
							</div>	
							
					</fieldset>

					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_delivery_code']} <span style="color:red">(*)</span></label>
							 <div class="form-control-wrapper">	
								<input class="form-control" type="text" name="p_delivery_code" id="pg_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['partner_delivery_empty_code']}" value="{$data['p_delivery_code']}"   >
							</div>	
							
					</fieldset>
					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_delivery_phone']} </label>
						<input class="form-control" type="text" name="p_delivery_phone" id="p_delivery_phone" maxlength="15" value="{$data['p_delivery_phone']}"   >
							 
							
					</fieldset>
				 	
				 
			 	 	 <fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_delivery_address']}</label>
							  
								<input class="form-control" type="text" name="p_delivery_address" id="p_delivery_address"   value="{$data['p_delivery_address']}"   >
							 
							
					</fieldset>
			 	
		 
		  

	 	</div><!-- col-md-6 -->
 

	 	<div class="col-md-6">
	 		<fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['p_delivery_website']}</label>
					<input class="form-control" type="text" name="p_delivery_website" id="p_delivery_website" placeholder="example.com"  value="{$data['p_delivery_website']}"   >						
		 	</fieldset>
	 		 <fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_delivery_email']}</label>
							  
								<input class="form-control" type="text" name="p_delivery_email" id="p_delivery_email" placeholder="example@email.com"  value="{$data['p_delivery_email']}"   >						
			 </fieldset>
			 	
			  <fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['p_delivery_group']}</label>
							  
								<input class="form-control" type="text" name="p_delivery_group" id="p_delivery_group"   value="{$data['p_delivery_group']}"   >						
			 </fieldset>

			 <fieldset class="form-group">
			 	<label class="form-label">{$CMS->lang['shipping_tracking_url']}</label>
			 	<div class="note-text">{$CMS->lang['shipping_tracking_url_note']}</div>
			 	<input class="form-control" type="text" name="shipping_tracking_url" id="shipping_tracking_url"   value="{$data['shipping_tracking_url']}">
			 </fieldset>
			 	
	 		 <fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['p_delivery_note']}</label>
	
						<textarea rows="3" class="form-control" name="p_delivery_note" id="p_delivery_note" >{$data['p_delivery_note']}</textarea> 
				 
			</fieldset>
 		
			
	 	</div>	<!-- col-md-6 -->
 
	</div>

	</figure>
</section>
	<section class="add_cart_footer">
		 
 
		 {$CMS->global->footer_back(array("list" =>"{$CMS->vars['root_domain']}/?site=partner_delivery"))}
		 {$CMS->global->footer_save()}

		</section>

 
</form>
 <script>
 $(document).ready(function(){

	validate_form_custom("#form-signin_v1",".act_submit_save");
});

</script>
 
 <script src="{$CMS->vars['js_acp']}/partner_delivery.js"></script>
EOF;
		return $out;
	}
}
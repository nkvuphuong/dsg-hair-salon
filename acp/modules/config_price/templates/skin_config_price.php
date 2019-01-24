<?php
class skin_config_price{
	public function head() {
		global $CMS, $DB, $member;
		$pg_id_search = $CMS->input['pg_id'];
		$pg_name_search = $CMS->input['pg_name'];
		 
		$out = '';
		 
		$out .= <<<EOF

<section class="add_table main_form">
	<figure class="heading">
		<h3>{$CMS->lang['c_price_title']}</h3>
		<figure class="pull-right right">
			  <a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
		</figure>
	</figure>
  			
<section class="add_table">
	 <div class="data_table"  onclick="return tablesave_calc(this);" >
			<table id="example"class="display table table_cus" cellspacing="0" width="100%">
				<thead>
					 
					<tr>
EOF;
					if($_SESSION['is_mobile'] == true)
					{
						$out .=<<<EOF
						<th data-orderable="false" data-sortable="false" width="1%"></th>
							<th width="15%" data-orderable="false" data-sortable="false"  >{$CMS->lang['product_code']}</th>
EOF;
					}
					else{
						$out .=<<<EOF
							<th width="15%" data-orderable="true" data-sortable="true"  >{$CMS->lang['product_code']}</th>
EOF;
					}	
					$out .=<<<EOF
					
						<th  width="20%"  data-orderable="false"  >{$CMS->lang['product_name']}</th>
						<th width="10%"  data-orderable="false" >{$CMS->lang['product_price']}</th>
						<th width="10%"  data-orderable="false" >{$CMS->lang['product_price_sell']}</th>
						<th width="15%" data-orderable="false" >{$CMS->lang['product_price_input']}</th>

					</tr>
				</thead>
				<tbody>
EOF;
		return $out;
	}
	public function foot() {
		global $CMS, $DB, $member;
		$out .= <<<EOF
 

					</tbody>
				</table>

		</div><!--.box-typical-body-->
		 
	 </section>
 	 
</section>

{$CMS->global->logs("config_price")}
EOF;
	if($_SESSION['is_mobile'] == true)
	{	 
		$out .=<<<EOF
	 	 <script>
				$(function() {
					$('#example').DataTable({
					language: {
								emptyTable: '{$CMS->lang['no_data']}',
								search: '{$CMS->lang['search']}',
						},
					order: [],
					responsive: true,
				    columnDefs: [
				        { responsivePriority: 1, targets: 1 },
	 
				        
				    ],
			 		    paging: true,
			 		    pageLength:25,
						searching: false,
						info: false
					});
				});


				 

		 </script>
EOF;
	}
	else
	{
		 
		$out .=<<<EOF

		 <script>
								$(function() {
								$('#example').DataTable({
								language: {
								      emptyTable: '{$CMS->lang['no_data']}',
								      search: '{$CMS->lang['search']}',
								    },
	 							 order: [],
						 		  paging: true,
						 		  pageLength:25,
									  searching: true,
									  info: false
								});
							});
		</script>
EOF;
	}
	$out .=<<<EOF

 <script>
		$(document).ready(function(){
			$("input.demo3").TouchSpin();

			$('.dropdown-cprice').on('click', function(e) {
				console.log("vao");
				  e.stopPropagation();
		    });

		  
		});
	</script>
 
  <script src="{$CMS->vars['js_acp']}/config_price.js"></script>

EOF;
		return $out;
	}
	public function mid($data=null) {
		global $CMS, $DB, $member;
		$out = <<<EOF
					<tr>	
EOF;
					if($_SESSION['is_mobile'] == true)
					{
						$out .=<<<EOF
						<td></td>
EOF;
					}
					$out .=<<<EOF
						<td>
							{$data['product_code']}
						</td>
						<td>
							{$data['product_name']}
						</td>
						<td> 
							{$data['product_price_bk']}
						</td>
					 	<td> 
							{$data['product_price_sell_bk']}
						</td>
						<td class="special_dropdown">

							  <div class="dropdown">
 									<input type="text" class="form-control dropdown-toggle inputdefault_calc_price"   data-toggle="dropdown" aria-expanded="false" onfocus="return active_input_calc_price(this);" name="product_price_input" defaultvalue="{$data['product_price_sell']}" product_id="{$data['product_id']}" value=""/>

 									 <div class="cfprice-dropdown-menu dropdown-menu dropdown-menu-right dropdown-cprice dropdown-menu-notif" style="width:300px;z-index:9999" >
				                          <div class="wrap-cfprice-dropdown" onclick="return prevent_dropdown_cfprice(event);">
				                            <div class="row" style="margin-bottom:5px">
				                            	 
				                            		<div>Giá mới:<span id="price_default_{$data['product_id']}">{$CMS->class->input->currency($data['product_price_sell'])}</span></div>
				                            	 
				                            </div>
				                            <div class="row" style="margin-bottom:5px">
				                             	 
				                            
				                            	  <select class="form-control sm-cf-price" name="tmp_p_price_{$data['product_id']}" onchange="change_price(this,{$data['product_id']})" style="width:165px" >
				                            		<option value="0" price="{$data['product_price_sell']}">{$CMS->lang['price_sell']}</option>
				                            		<option value="1"  price="{$data['product_price']}">{$CMS->lang['price_import']}</option>
				                            		</select>
				                            		<input type="hidden" name="p_price_{$data['product_id']}" value="0" />

				                            		<button type="button" class="btn btn-inline btn-success calc_{$data['product_id']}" onclick="return calc_action(this,{$data['product_id']})" calc="plus"><i class="fa fa-plus"></i></button>
						 						 		<button type="button" class="btn btn-inline btn-default calc_{$data['product_id']}" onclick="return calc_action(this,{$data['product_id']})" calc="minus"><i class="fa fa-minus"></i></button>
						 						 		<input type="hidden" name="calc_type_{$data['product_id']}" value="plus" />

				                            	 
				                            </div>	
				                            <div class="row" style="margin-bottom:5px">	
				                            	 
						 						 		<input type="text" class="form-control sm-cf-price" name="tmp_calc_price_input_{$data['product_id']}" product_id="{$data['product_id']}" value="0" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_cprice(this,0);" />

						 						 		<input type="hidden" name="calc_price_input_{$data['product_id']}" value="0" />


														<button type="button" class="btn btn-inline btn-success calc_valuetype_{$data['product_id']}"  onclick="return calc_valueaction(this,{$data['product_id']})" valuetype="{$CMS->vars['currency_type']}">{$CMS->vars['currency_type']}</button>
						 						 		<button type="button" class="btn btn-inline btn-default  calc_valuetype_{$data['product_id']}"  onclick="return calc_valueaction(this,{$data['product_id']})" valuetype="%">%</button> 
						 						 		<input type="hidden" name="calc_valuetype_{$data['product_id']}" value="{$CMS->vars['currency_type']}" />
				 										<button type="button" onclick="return save_config_price(this,{$data['product_id']});" class="btn btn-inline btn-primary ladda-button" data-style="expand-right" data-size="xs"><span class="ladda-label">Ok</span><span class="ladda-spinner"></span><span class="ladda-spinner"></span></button>
				                            	 
				                            	 
				                            	 	
				                            </div><!-- end class row -->
				                     	</div>         
				                     </div>       

 							 </div>		
						</td>
 					</tr>
EOF;

		return $out;
	}
	public function none() {
		global $CMS, $DB, $member;
		$out = <<<EOF
					<tr>
						<td colspan="6">
							<center><p style="font-size:14px">{$CMS->lang['not_data']}</p></center>
						</td>
					</tr>
EOF;
		return $out;
	}
	public function add() {
		global $CMS, $DB, $member;

		//Check exit param parent_id
		if(isset($CMS->input['parent_id']) )
		{
			$parent = $CMS->config_price->getInfo($CMS->input['parent_id']);

		}

		$pg_parent = isset($CMS->input['pg_parent']) ? $CMS->input['pg_parent'] : '';
		$pg_name = isset($CMS->input['pg_name']) ? $CMS->input['pg_name'] : '';
		$pg_code = isset($CMS->input['pg_code']) ? $CMS->input['pg_code'] : '';
		$pg_status = isset($CMS->input['pg_status']) ? $CMS->input['pg_status'] : 1;
		$pg_description = isset($CMS->input['pg_description']) ? $CMS->input['pg_description'] : '';
		
		$option_pg_parent = "<option value=''>{$CMS->lang['select']}</option>";

		$pg_type = isset($CMS->input['pg_type']) ? $CMS->input['pg_type'] : 0;

		if(isset($CMS->input['parent_id']) )
		{
			//Overwrite default field follow parent
			$pg_parent = $parent['config_price_id'];
			$pg_type = $parent['config_price_type'];
		}


		$option_pg_type ="";
		for ($i = 0; $i <= 1; $i++) {
			if ($i == $pg_type) {
				$option_pg_type .= "
     <div class='radio w25'><input type='radio' checked  name='pg_type' id='radio-{$i}' value='{$i}'><label for='radio-{$i}'>{$CMS->lang['pg_type_'.$i]}</label></div>	

				";
			} else {
				$option_pg_type .= "  <div class='radio w25'><input type='radio' name='pg_type' id='radio-{$i}' value='{$i}'><label for='radio-{$i}'>{$CMS->lang['pg_type_'.$i]}</label></div>	";
			}
		}

	
		$category = $CMS->config_price->getParent_bytype($pg_type);
		foreach ($category as $c) {
			if ($c['config_price_id'] == $pg_parent) {
				$option_pg_parent .= "<option value='{$c['config_price_id']}' selected>{$c['config_price_name']}</option>";
			} else {
				$option_pg_parent .= "<option value='{$c['config_price_id']}' >{$c['config_price_name']}</option>";
			}
		}
		$option_pg_status = "";
		for ($i = 1; $i >= 0; $i--) {
			if ($i == $pg_status) {
				$option_pg_status .= "   <div class='radio w25'><input type='radio' checked  name='pg_status' id='radio-status-{$i}' value='{$i}'><label for='radio-status-{$i}'>{$CMS->lang['pg_statupg_0'.$i]}</label></div>";
			} else {
				$option_pg_status .= "   <div class='radio w25'><input type='radio'    name='pg_status' id='radio-status-{$i}' value='{$i}'><label for='radio-status-{$i}'>{$CMS->lang['pg_statupg_0'.$i]}</label></div>";
			}
		}
		$out = '';
		 
	$out .= <<<EOF

 
 <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=config_price&act=add" method="POST" enctype="multipart/form-data">
  <section class="add_form main_form"> 
	<figure class="heading">
		<h3>{$CMS->lang['pg_add']}</h3>
		 <a href="{$CMS->vars['root_domain']}/?site=config_price" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>

   <figure class="box-typical box-typical box-typical-padding border">
 
 
	<div class="row">
 



	 	<div class="col-md-6">
	 		<div class="row">
			 	<div class="col-xl-6">

			 		<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['pg_parent']}</label>
								<select class="form-control" name="pg_parent" id="pg_parent" >
									{$option_pg_parent}
								</select>
							
					</fieldset>

					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['pg_name']} <span style="color:red">(*)</span></label>
							 <div class="form-control-wrapper">	
								<input class="form-control" type="text" name="pg_name" id="pg_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['pg_name_err']}" value="{$pg_name}" onfocusout="generate_pg_code(this,'#pg_code');"  >
							</div>	
							
					</fieldset>
					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['pg_code']} <span style="color:red">(*)</span></label>
							 <div class="form-control-wrapper">	
								<input class="form-control " type="text" name="pg_code" id="pg_code" maxlength="3" data-validation="[NOTEMPTY,L<=3]" data-validation-message="{$CMS->lang['pg_code_err']}. Nhóm sản phẩm tối đa 3 ký tự! " value="{$pg_code}">
							</div>	
							
					</fieldset>
				</div><!-- col-xl-6-->
				 
			 	<div class="col-xl-6">
			 		<fieldset class="form-group">
						<label class="form-label  pull-left" >{$CMS->lang['pg_avartar']}</label>
									 <div class="actionButtons pull-right">
		                                <ul>
		                                    <li onclick="return performClick('ufile');">
		                                        <i tabindex="0" class="fa fa-pencil" ></i>
		                                    </li>
		                                    <li>
		                                        <span class="text-left">|</span>
		                                    </li>
		                                    <li onclick="return delete_fileToAttach();">
		                                        <i class="fa fa-trash-o"></i>
		                                    </li>
		                                </ul>
										<input  type="hidden" id="ufile_output_b64" name="base64_image" />
									  </div>	
							 
								    <div class="drop-zone fileinput-button">
			                              <img id="upload_img_show" width="205" />

			                              <i class="font-icon font-icon-cloud-upload-2"></i>
			                               <div class="drop-zone-caption">Drag file to upload</div>
			                                 <input type="file"  name="pg_avartar" id="ufile" accept="image/*">
			                         </div><!--.drop-zone-->
			                  			
			                        
			                 

					</fieldset>
			 	</div><!-- col-xl-6-->
			 	
			</div> 	<!-- row-->	
		  

	 	</div><!-- col-md-6 -->
	 	


	 	<div class="col-md-6">
	 		 <fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['pg_description']}</label>
	
						<textarea rows="3" class="form-control" name="pg_description" id="pg_description" >{$pg_description}</textarea> 
				 
			</fieldset>
	 		 <fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['pg_type']}</label>
						 
							{$option_pg_type}

			</fieldset>
			
			<fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['pg_status']}</label>	 
							{$option_pg_status}
				
			</fieldset>
			
			
	 	</div>	<!-- col-md-6 -->

	
		
		

	 

	</div>

	</figure>
</section>
	<section class="add_cart_footer">
		 
 
		 {$CMS->global->footer_back(array("list" =>"{$CMS->vars['root_domain']}/?site=config_price"))}
		 {$CMS->global->footer_save()}

		</section>

 
</form>
 <script>
 $(document).ready(function(){

	validate_form_custom("#form-signin_v1",".act_submit_save");
});

</script>
 
 <script src="{$CMS->vars['js_acp']}/config_price.js"></script>
EOF;
		return $out;
	}
	public function edit($data=null) {
		global $CMS, $DB, $member;
		$pg_parent = isset($CMS->input['pg_parent']) ? $CMS->input['pg_parent'] : $data['config_price_parent'];
		$pg_name = isset($CMS->input['pg_name']) ? $CMS->input['pg_name'] : $data['config_price_name'];
		$pg_code = isset($CMS->input['pg_code']) ? $CMS->input['pg_code'] : $data['config_price_code'];
		$pg_status = isset($CMS->input['pg_status']) ? $CMS->input['pg_status'] : $data['config_price_status'];
		$pg_description = isset($CMS->input['pg_description']) ? $CMS->input['pg_description'] : $data['config_price_description'];
		$pg_avartar = isset($data['config_price_avartar']) ? $data['config_price_avartar'] :'no-img.jpg';
		$pg_avartar = file_exists($CMS->vars['upload_dir'].'/category/'.$pg_avartar) ? $CMS->vars['upload_url'].'/category/'.$pg_avartar : $CMS->vars['upload_url'].'/category/no-img.jpg';
		$option_pg_parent = "<option value=''>{$CMS->lang['select']}</option>";


		$pg_type = isset($CMS->input['pg_type']) ? $CMS->input['pg_type'] : $data['config_price_type'];
 
		$option_pg_type ="";
		for ($i = 0; $i <= 1; $i++) {
			if ($i == $pg_type) {
				$option_pg_type .= "
     <div class='radio w25'><input type='radio' checked  name='pg_type' id='radio-{$i}' value='{$i}'><label for='radio-{$i}'>{$CMS->lang['pg_type_'.$i]}</label></div>	

				";
			} else {
				$option_pg_type .= "  <div class='radio w25'><input type='radio' name='pg_type' id='radio-{$i}' value='{$i}'><label for='radio-{$i}'>{$CMS->lang['pg_type_'.$i]}</label></div>	";
			}
		}



		$parent = $CMS->config_price->getParent_bytype($pg_type);
		foreach ($parent as $p) {
			if ($p['config_price_id'] == $pg_parent) {
				$option_pg_parent .= "<option value='{$p['config_price_id']}' selected>{$p['config_price_name']}</option>";
			} else {
				$option_pg_parent .= "<option value='{$p['config_price_id']}' >{$p['config_price_name']}</option>";
			}
		}
		$option_pg_status = "";
		for ($i = 1; $i >= 0; $i--) {
			if ($i == $pg_status) {
				$option_pg_status .= "   <div class='radio w25'><input type='radio' checked  name='pg_status' id='radio-status-{$i}' value='{$i}'><label for='radio-status-{$i}'>{$CMS->lang['pg_statupg_0'.$i]}</label></div>";
			} else {
				$option_pg_status .= "   <div class='radio w25'><input type='radio'    name='pg_status' id='radio-status-{$i}' value='{$i}'><label for='radio-status-{$i}'>{$CMS->lang['pg_statupg_0'.$i]}</label></div>";
			}
		}

		$out = '';
		 
	$out .= <<<EOF

 
 <form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=config_price&act=edit&id={$data['config_price_id']}" method="POST" enctype="multipart/form-data">
<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['pg_edit']}</h3>
		 	<a href="{$CMS->vars['root_domain']}/?site=config_price" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>


  <figure class="box-typical box-typical box-typical-padding border">
  	<div class="row">
 
 


		<div class="col-md-6">	
			<div class="row">
			 	<div class="col-xl-6">
			    	<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['pg_parent']}</label>
						
								<select class="form-control" name="pg_parent" id="pg_parent" >
									{$option_pg_parent}
								</select>
							
					</fieldset>
					<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['pg_name']} <span style="color:red">(*)</span></label>
							<div class="form-control-wrapper">	
								<input class="form-control " type="text" name="pg_name" id="pg_name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['pg_name_err']}" value="{$pg_name}" onfocusout="generate_pg_code(this,'#pg_code');">
							</div>	
							
					</fieldset>
						<fieldset class="form-group">
						<label class="form-label" >{$CMS->lang['pg_code']} <span style="color:red">(*)</span></label>
							<div class="form-control-wrapper">	
								<input class="form-control " type="text" name="pg_code" id="pg_code" data-validation="[NOTEMPTY, L<=3]" data-validation-message="{$CMS->lang['pg_code_err']}. Nhóm sản phẩm tối đa 3 ký tự." value="{$pg_code}"  >
							</div>	
							
					</fieldset>
					
				</div>

				<div class="col-xl-6">
						
						<fieldset class="form-group">
							<label class="form-label  pull-left" >{$CMS->lang['pg_avartar']}</label>
								 <div class="actionButtons pull-right">
		                                <ul>
		                                    <li onclick="return performClick('ufile');">
		                                        <i tabindex="0" class="fa fa-pencil" ></i>
		                                    </li>
		                                    <li>
		                                        <span class="text-left">|</span>
		                                    </li>
		                                    <li onclick="return delete_fileToAttach();">
		                                        <i class="fa fa-trash-o"></i>
		                                    </li>
		                                </ul>
										<input  type="hidden" id="ufile_output_b64" name="base64_image" />
									  </div>	
								 
									 <div class="drop-zone fileinput-button">
				                              <img id="upload_img_show" width="205" />

				                              <i class="font-icon font-icon-cloud-upload-2"></i>
				                               <div class="drop-zone-caption">Drag file to upload</div>
				                                 <input type="file"  name="pg_avartar" id="ufile" accept="image/*">
				                         </div><!--.drop-zone-->
				                  		 
								 
									<img class="img-responsive" src="{$pg_avartar}" style="max-width: 100%;" alt="{$data['config_price_name']}"> 
								
						</fieldset>
				</div>
			</div><!-- row -->	
			

		</div><!-- col-md-6 -->
		
	 	<div class="col-md-6">
	 		<fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['pg_description']}</label>
				
						<textarea rows="3" class="form-control" name="pg_description" id="pg_description">{$pg_description}</textarea> 
					
			</fieldset>
	 		<fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['pg_type']}</label>
					 
							{$option_pg_type}
			</fieldset>
	 		<fieldset class="form-group">
				<label class="form-label" >{$CMS->lang['pg_status']}</label>
				 
							{$option_pg_status}
					 
			
			</fieldset>
	 		
	 	</div>	<!-- col-md-6 -->


 	
		 
	
	 </div>
 	</figure>
</section>
 	<section class="add_cart_footer">

 
			 
				{$CMS->global->footer_back(array("list" =>"{$CMS->vars['root_domain']}/?site=config_price", "detail_id" => "{$data['config_price_id']}" ))}
				{$CMS->global->footer_edit()}
		</section>


	</form>
<script>
 $(document).ready(function(){

	validate_form_custom("#form-signin_v1",".act_submit_save");
});

</script>
 <script src="{$CMS->vars['js_acp']}/config_price.js"></script>
EOF;
		return $out;
	}
	public function show($data=null) {
		global $CMS, $DB, $member;
		$data['config_price_parent_c'] = $data['config_price_parent_c'] != 0 ? $data['config_price_parent_c'] : "Danh mục gốc";
		$data['config_price_name'] = $data['config_price_name'] != "" ? $data['config_price_name'] : "N/A";
		$data['config_price_code'] = $data['config_price_code'] != "" ? $data['config_price_code'] : "N/A";
		$data['config_price_description'] = $data['config_price_description'] != "" ? $data['config_price_description'] : "N/A";
 
		$out = <<<EOF


<section class="add_form main_form">
	<figure class="heading">
		<h3>{$CMS->lang['pg_info']}</h3>
		  <a href="{$CMS->vars['root_domain']}/?site=config_price" title=""><span class="font-icon font-icon-del"></span></a>
	</figure>

	 <figure class="box-typical box-typical box-typical-padding border">
 		<div class="row">
 			<div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['pg_parent']}</label>
					<div class="col-xl-9 form-control-span2"> 
 

							{$data['config_price_parent_c']}
				 
					</div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['pg_name']}</label>
					<div class="col-xl-9 form-control-span2"> 
				 
							{$data['config_price_name']}
		 
				    </div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['pg_code']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['config_price_code']} 
					</div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['pg_description']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['config_price_description']} 
					</div>
				</fieldset>
			</div>
			
			<div class="col-xl-6 col-md-6 col-sm-12 col-xs-12">	
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['pg_type']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['config_price_type_bk']}
						 
					</div>
				</fieldset>
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['pg_status']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['config_price_statupg_c']}
						 
					</div>
				</fieldset>
				
				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['pg_time']}</label>
					<div class="col-xl-9 form-control-span2"> 
						 
							{$data['config_price_time_c']}
						 
					</div>
				</fieldset>

				<fieldset class="form-group row">
					<label class="col-xl-3 form-control-label2" >{$CMS->lang['pg_avartar']}</label>
					<div class="col-xl-9 form-control-span2"> 
					 
							<img class="img-responsive" src="{$data['config_price_avartar_c']}" style="max-width: 100%;" alt="{$data['config_price_name']}"> 
					 
					</div>
				</fieldset>
			</div> 
	 </div>
 	</figure>	
</section>

		<section class="add_cart_footer">
			 {$CMS->global->footer_back(array("list" =>"{$CMS->vars['root_domain']}/?site=config_price"))} 
EOF;
		
		 
						if($CMS->permit['config_price_delete'] == 1)
						{
							$out .=<<<EOF
							<a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=config_price&act=delete&id={$data['config_price_id']}');"  class="pull-right add_cart_2">{$CMS->lang['gdelete']}</a>

EOF;

						}

						if($CMS->permit['config_price_edit'] == 1)
						{
							$out .=<<<EOF
							<a href="{$CMS->vars['root_domain']}/?site=config_price&act=edit&id={$data['config_price_id']}" class="pull-right add_cart_2">{$CMS->lang['gedit']}</a>
EOF;

						}

						 
						
					$out .=<<<EOF
		</section>


EOF;
		return $out;
	}
}
?>
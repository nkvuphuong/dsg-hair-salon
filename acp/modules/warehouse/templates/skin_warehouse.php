<?php

class skin_warehouse {
	public function head() {
		global $CMS, $DB, $member;
		$dis='';$free='';
		if($CMS->input['type']!=''&&$CMS->input['type']==1) $dis='selected';
		if($CMS->input['type']!=''&&$CMS->input['type']==0) $free='selected';
		$output='';
		$output.=<<<EOF
<header class="section-header">
	<div class="tbl">
		<div class="tbl-row">
			<div class="tbl-cell">
				<h3>{$CMS->lang['warehouse_manage']}</h3>
			</div>
		</div>
	</div>
</header>
<!--<form name="quick_search" id="quick_search" method="post" class="quick_search promotion" action="{$CMS->vars['root_domain']}/?site=promotion&act=search" >
<div class="row">
	<div class="col-xl-3 col-lg-3 col-sm-3 col-xs-12"> 
		<p class="typeahead-field">
			<span class="typeahead-query">
				<input name="promotion_seach_code" id="promotion_seach_code" size="45" type="text" class="form-control" placeholder="{$CMS->lang['promotion_code']}" value="{$CMS->input['code']}" >
			</span>
		</p>
	</div>
	<div class="col-xl-3 col-lg-3 col-sm-3 col-xs-12"> 
		<p class="typeahead-field">
			<span class="typeahead-query">
				<input name="promotion_seach_user" id="promotion_seach_user" size="45" type="text" class="form-control" placeholder="{$CMS->lang['promotion_user']}" value="{$CMS->input['user']}" >
			</span>
		</p>
	</div>
	<div class="col-xl-3 col-lg-3 col-sm-3 col-xs-12"> 
		<p class="typeahead-field">
			<span class="typeahead-query">
				<select class="form-control" name="promotion_seach_type" id="promotion_seach_type">
					<option value='' hidden>{$CMS->lang['promotion_type']}</option>
					<option value=''></option>
					<option value='1' {$dis}>{$CMS->lang['promotion_dis']}</option>
					<option value='0' {$free}>{$CMS->lang['promotion_free']}</option>
				</select>	
			</span>
		</p>
	</div>
	<div class="col-xl-3 col-lg-3 col-sm-3 col-xs-12"> 
		<p class="typeahead-field">
			<span class="typeahead-query">
				<button class="search btn btn-rounded" type="submit"><i class="fa fa-search" aria-hidden="true"></i>Search</button>
			</span>
		</p>
	</div>
</div>
</form>-->
<section class="box-typical">
	<header class="box-typical-header">
        <div class="tbl-row">
            <div class="tbl-cell tbl-cell-title">
				<h3>{$CMS->lang['section_list']}</h3>
            </div>
            <div class="tbl-cell tbl-cell-action-bordered">
				<a href="{$CMS->vars['root_domain']}/?site=warehouse&act=add">
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
						<th width="5%" id="order_section_id">ID</th>
						<th width="15%" id="order_section_name">{$CMS->lang['section_name']}</th>
						<th width="15%" id="order_section_quantity">{$CMS->lang['section_note']}</th>
						<th width="15%">{$CMS->lang['section_quantity']}</th>
						<th width="15%" id="order_time">{$CMS->lang['section_time']}</th>
						<th width="10%" style="text-align:center">Edit</th>
						<th width="3%" style="text-align:center">Delete</th>
					</tr>
				</thead>
				<tbody>
EOF;
		return $output;
	}
	
	public function foot() {
		global $CMS, $DB, $member;
		$output=<<<EOF
				</tbody>
			</table>
		</div>
	</div>
</section>
<div class="block_bottom pagination pagination-sm">
	{$CMS->warehouse->show_page}
</div>
<script language="javascript">arrange_setup("{$CMS->warehouse->arrange_data}");</script>
EOF;
		return $output;
	}
	
	public function mid($data=NULL) {
		global $CMS, $DB, $member;
		$data['time']=$CMS->class->date->date_format($data['time'],1);
		$data['quantity']=empty($data['quantity'])?0:$data['quantity'];
		$output=<<<EOF
					<tr>
						<td>{$data['section_id']}</td>
						<td>
							<a href="{$CMS->vars['root_domain']}/?site=warehouse&act=show&id={$data['section_id']}">{$data['section_name']}</a>
						</td>
						<td>{$data['section_note']}</td>
						<td>{$data['quantity']}</td>
						<td>{$data['time']}</td>
						<td align="center">
							<script type="text/javascript">permission_btn("edit", "user", "{$CMS->vars['root_domain']}/?site=warehouse&act=edit&id={$data['section_id']}");</script>
						</td>
						<td align="center">
							<script type="text/javascript">permission_btn("delete", "user", "{$CMS->vars['root_domain']}/?site=warehouse&act=delete&id={$data['section_id']}");</script>
						</td>
					</tr>
EOF;
		return $output;
	}
	
	public function none() {
		global $CMS, $DB, $member;
		$output=<<<EOF
				<tr>
					<td colspan="7" align="center">
						<h5>{$CMS->lang['section_not']}</h5>
					</td>
				</tr>
EOF;
		return $output;
	}
	
	public function edit($data=null) {
		global $CMS, $DB, $member;
		$list_product=$CMS->exproduct->get_list_exproduct();
		$option="<option value=\"\" hidden></option>";
		foreach ($list_product as $product) {
			$option.="<option value=\"{$product['exp_id']}\">{$product['exp_name']}</option>";
		}
		$out=<<<EOF
<div id="warehouse_body">
<header class="section-header">
	<div class="tbl">
		<div class="tbl-row">
            <div class="tbl-cell">
				<h3>{$CMS->lang['section_edit']}</h3>
            </div>
		</div>
	</div>
</header>
<section class="card">
    <section class="box-typical">
        <header class="box-typical-header">
            <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title">
                    <h3>{$CMS->lang['section_info']}</h3>
                </div>
                <div class="tbl-cell tbl-cell-action-bordered">
                    <a href="{$CMS->vars['root_domain']}/?site=warehouse{$CMS->class->search->url_return}">
                    <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                    </a>
                </div>
            </div>
        </header>
	</section>
	<div class="card-block">
		<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=warehouse&act=edit&id={$data['section_id']}" method="POST">
		<fieldset class="form-group row">
			<label class="col-sm-2 form-control-label" for="promotion_exp">{$CMS->lang['section_name']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-10 col-xs-12"> 
				<p class="form-control-static-input">
					<input class="form-control" size="45" type="text" name="name" id="name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['section_name_err']}" data-validation-regex="/^[0-9a-zA-Z-_ ]*$/" data-validation-regex-message="{$CMS->lang['section_name_err']}" value="{$data['section_name']}"> 
				</p>
			</div>
		</fieldset>
		<fieldset class="form-group row">
			<label class="col-sm-2 form-control-label" for="promotion_exp">{$CMS->lang['section_note']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-10 col-xs-12"> 
				<p class="form-control-static-input">
					<textarea name="note" id="note" class="form-control" rows="5">{$data['section_note']}</textarea>
				</p>
			</div>
		</fieldset>
		<fieldset class="form-group row">
			<label class="col-sm-2 form-control-label" for="promotion_exp">{$CMS->lang['item_in_section']}</label>
		</fieldset>
		<fieldset class="form-group row">
			<div> 
				<p class="form-control-static-input">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<th>{$CMS->lang['item_name']}</th>
								<th>{$CMS->lang['item_note']}</th>
								<th>{$CMS->lang['item_quantity']}</th>
								<th>{$CMS->lang['item_color']}</th>
								<th>{$CMS->lang['item_size']}</th>
								<th>
									<button type="button" class="btn btn-default" onclick="addRow()"><i class="fa fa-plus" aria-hidden="true"></i></button>
								</th>
							</thead>
							<tbody>
EOF;
		foreach ($data['content'] as $dt) {
			$option_name=$option_color=$option_size='';
			foreach ($list_product as $product) {
				if ($product['exp_id']==$dt['item_name']) {
					$item_name=$product['exp_name'];
					$option_name.="<option value=\"{$product['exp_id']}\" selected>{$product['exp_name']}</option>";
				} else {
					$option_name.="<option value=\"{$product['exp_id']}\">{$product['exp_name']}</option>";
				}
			}
			$tmp=$CMS->exproduct->get_info($dt['item_name']);
			$color=$CMS->attribute->getColorSize($tmp['group_attr']);
			foreach($color as $k=>$v) {
				if ($k==$dt['item_color']) {
					$option_color.="<option value='{$k}' style='background-color: #{$v[0]}' selected>{$v[1]}</option>";
				} else {
					$option_color.="<option value='{$k}' style='background-color: #{$v[0]}'>{$v[1]}</option>";
				}
			}
			$size=$CMS->attribute->getColorSize($tmp['group_attr_size']);
			foreach($size as $k=>$v) {
				if ($k==$dt['item_size']) {
					$option_size.="<option value='{$k}' selected>{$v[0]}</option>";
				} else {
					$option_size.="<option value='{$k}'>{$v[0]}</option>";
				}
			}
			$dt['item_color']=$CMS->attribute->get_info($dt['item_color']);
			$dt['item_size']=$CMS->attribute->get_info($dt['item_size'],'attr_name');
			$out.=<<<EOF
								<tr>
									<td>
										<input type="text" class="form-control" disabled value="{$item_name}">
									</td>
									<td>
										<input type="text" class="form-control" disabled value="{$dt['item_note']}">
									</td>
									<td>
										<input type="text" class="form-control" disabled value="{$dt['item_quantity']}">
									</td>
									<td>
										<input type="text" class="form-control" disabled value="{$dt['item_color']['attr_name']}" style="border: solid 3px #{$dt['item_color']['attr_value']}">
									</td>
									<td>
										<input type="text" class="form-control" disabled value="{$dt['item_size']}">
									</td>
									<td>
										<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editSection{$dt['ware_con_id']}">
											<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
										</button>
										<button type="button" class="btn btn-danger" onclick="deletedItem({$dt['ware_con_id']})">
											<i class="fa fa-trash-o" aria-hidden="true"></i>
										</button>
									</td>
								</tr>
								<div id="editSection{$dt['ware_con_id']}" class="modal fade" role="dialog">
									<div class="modal-dialog" style="max-width: 350px;">
										<div class="modal-content">
											<div class="modal-header">
												<h5>{$CMS->lang['edit_item']}</h5>
											</div>
											<div class="modal-body">
												<div class="form-group">
													<label for="usr">{$CMS->lang['item_name']}:
														<select class="form-control" id="item_name{$dt['ware_con_id']}" onchange="getColorSizeItem(this,'{$dt['ware_con_id']}')">
															{$option_name}
														</select>
													</label>
												</div>
												<div class="form-group">
													<label for="usr">{$CMS->lang['item_note']}:
														<input type="text" class="form-control" value="{$dt['item_note']}" id="item_note{$dt['ware_con_id']}">
													</label>
												</div>
												<div class="form-group">
													<label for="usr">{$CMS->lang['item_quantity']}:
														<input type="number" class="form-control" value="{$dt['item_quantity']}" id="item_quantity{$dt['ware_con_id']}">
													</label>
												</div>
												<div class="form-group">
													<label for="usr">{$CMS->lang['item_color']}:
														<select class="form-control" id="item_color{$dt['ware_con_id']}">
															{$option_color}
														</select>
													</label>
												</div>
												<div class="form-group">
													<label for="usr">{$CMS->lang['item_size']}:
														<select class="form-control" id="item_size{$dt['ware_con_id']}">
															{$option_size}
														</select>
													</label>
												</div>
												<br>
												<center class="form-group" id="edit_item{$dt['ware_con_id']}">
													<button type="button" class="btn btn-default" onclick="editItem('{$dt['ware_con_id']}')">
														{$CMS->lang['edit_item']}
													</button>
												</center>
											</div>
										</div>
									</div>
								</div>
EOF;
		}
		$out.=<<<EOF
							</tbody>
						</table>
					</div>
				</p>
			</div>
		</fieldset>
		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label hidden-xs-down"></label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
				<p class="form-control-static-input">
					<button class="btn btn-rounded" type="submit" >{$CMS->lang['section_edit']}</button>
				</p>
			</div>
		</fieldset>
		</form>
	</div>
</section>
</div>
<script>
var point=1;
function addRow() {
	var str='<tr>'
		+ '<td>'
			+ '<select class="form-control" id="section_name'+point+'" onchange="getColorSize(this,\''+point+'\')">'
				+ '{$option}'
			+ '</select>'
		+ '</td>'
		+ '<td>'
			+ '<input type="text" class="form-control" id="section_note'+point+'">'
		+ '</td>'
		+ '<td>'
			+ '<input type="number" class="form-control" id="section_quantity'+point+'">'
		+ '</td>'
		+ '<td>'
			+ '<select class="form-control" id="section_color'+point+'">'
			+ '</select>'
		+ '</td>'
		+ '<td>'
			+ '<select class="form-control" id="section_size'+point+'" >'
			+ '</select>'
		+ '</td>'
		+ '<td id="section'+point+'">'
			+ '<button type="button" class="btn btn-success" onclick="addSection('+point+')">'
				+ '<i class="fa fa-plus-circle" aria-hidden="true"></i>'
			+ '</button>'
		+ '</td>'
	+ '<td>';
	$(str).hide().prependTo('table tbody').fadeIn("slow");
	point++;
}
function addSection(e) {
	var name=$('#section_name'+e);
	var note=$('#section_note'+e);
	var quantity=$('#section_quantity'+e);
	var color=$('#section_color'+e);
	var size=$('#section_size'+e);
	var check=true;
	if (name.val()=='') {
		name.addClass("form-control-red-fill");
		check=false;
	}
	if (quantity.val()==''||quantity.val()<=0) {
		quantity.addClass("form-control-red-fill");
		check=false;
	}
	if (check) {
		$.post( "{$CMS->vars['root_domain']}/?site=warehouse&act=additem&id={$data['section_id']}",
			{ name:name.val(),note:note.val(),quantity:quantity.val(),color:color.val(),size:size.val() },
			{ func:"getNameAndTime" },
			'json'
		)
		.done(function(data) {
			if (data.status=='success') {
				$.notify({
					icon: 'font-icon font-icon-check-circle',
					title: '<strong>'+SUCCESS+'</strong>',
					message: data.message
				},{
					type: 'success'
				});
				getPage();
			} else {
				$.notify({
					icon: 'font-icon font-icon-warning',
					title: '<strong>'+ERROR+'</strong>',
					message: data.message
				},{
					type: 'danger'
				});
			}
			
		});
	}
}
function editItem(e) {
	var item_name=$('#item_name'+e);
	var item_note=$('#item_note'+e);
	var item_quantity=$('#item_quantity'+e);
	var item_color=$('#item_color'+e);
	var item_size=$('#item_size'+e);
	var check=true;
	if (item_name.val()=='') {
		name.addClass("form-control-red-fill");
		check=false;
	}
	if (item_quantity.val()==''||item_quantity.val()<=0) {
		item_quantity.addClass("form-control-red-fill");
		check=false;
	}
	if (check) {
		$('#editSection'+e).modal('hide');
		$.post( "{$CMS->vars['root_domain']}/?site=warehouse&act=edititem&id={$data['section_id']}&item="+e,
			{ item_name:item_name.val(),item_note:item_note.val(),item_quantity:item_quantity.val(),item_color:item_color.val(),item_size:item_size.val() },
			{ func:"getNameAndTime" },
			'json'
		)
		.done(function(data) {
			if (data.status=='success') {
				$.notify({
					icon: 'font-icon font-icon-check-circle',
					title: '<strong>'+SUCCESS+'</strong>',
					message: data.message
				},{
					type: 'success'
				});
				getPage();
			} else {
				$.notify({
					icon: 'font-icon font-icon-warning',
					title: '<strong>'+ERROR+'</strong>',
					message: data.message
				},{
					type: 'danger'
				});
			}
			
		});
	}
}
function deletedItem(e) {
	swal({
		title: "{$CMS->lang['deleted_item_title']}",
		text: "{$CMS->lang['deleted_item_text']}",
		type: "warning",
		showCancelButton: true,
		confirmButtonClass: "btn-danger",
		confirmButtonText: "{$CMS->lang['deleted_item_yes']}",
		cancelButtonText: "{$CMS->lang['deleted_item_no']}",
		closeOnConfirm: true,
		closeOnCancel: true
	}).then(function () {
        deleted(e);
                                           
 		});
	 
}
function deleted(e) {
	$.post( "{$CMS->vars['root_domain']}/?site=warehouse&act=deleteditem&id={$data['section_id']}&item="+e,
		{ item:e },
		{ func:"getNameAndTime" },
		'json'
	)
	.done(function(data) {
		if (data.status=='success') {
			$.notify({
				icon: 'font-icon font-icon-check-circle',
				title: '<strong>'+SUCCESS+'</strong>',
				message: data.message
			},{
				type: 'success'
			});
			getPage();
		} else {
			$.notify({
				icon: 'font-icon font-icon-warning',
				title: '<strong>'+ERROR+'</strong>',
				message: data.message
			},{
				type: 'danger'
			});
		}
		
	});
}
function getPage() {
 	var str='';
	for (i=1; i<8; i++) {
		str+="<div id='fountainG_"+i+"' class='fountainG'></div>";
	}
	$('#warehouse_body').html("<div id='fountainG'>"+str+"</div>");
	$.get(site_root_domain+"?site=warehouse&act=edit&ajax=true&id={$data['section_id']}", function(html) {
		$('#warehouse_body').html(html);
    });
}
function getColorSize(e,i) {
	console.log(i);
	$.get(
		site_root_domain+"?site=warehouse&act=getcolorsize&ajax=true&exp_id="+e.value,
		function(json) {
			$('#section_color'+i).html(json.color);
			$('#section_size'+i).html(json.size);
		},
		'json'
	);
}
function getColorSizeItem(e,i) {
	console.log(i);
	$.get(
		site_root_domain+"?site=warehouse&act=getcolorsize&ajax=true&exp_id="+e.value,
		function(json) {
			$('#item_color'+i).html(json.color);
			$('#item_size'+i).html(json.size);
		},
		'json'
	);
}
</script>
EOF;
		return $out;
	}
	public function show($data=null) {
		global $CMS, $DB, $member;
		$list_product=$CMS->exproduct->get_list_exproduct();
		$option="<option value=\"\" hidden></option>";
		foreach ($list_product as $product) {
			$option.="<option value=\"{$product['exp_id']}\">{$product['exp_name']}</option>";
		}
		$data['time']=$CMS->class->date->date_format($data['time'],1);
		$out=<<<EOF
<header class="section-header">
	<div class="tbl">
		<div class="tbl-row">
            <div class="tbl-cell">
				<h3>{$CMS->lang['section_info']}</h3>
            </div>
		</div>
	</div>
</header>
<section class="card">
    <section class="box-typical">
        <header class="box-typical-header">
            <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title">
                    <h3>{$CMS->lang['section_info']}</h3>
                </div>
                <div class="tbl-cell tbl-cell-action-bordered">
                    <a href="{$CMS->vars['root_domain']}/?site=warehouse{$CMS->class->search->url_return}">
                    <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                    </a>
                </div>
            </div>
        </header>
	</section>
	<div class="card-block">
		<form>
		<fieldset class="form-group row">
			<label class="col-sm-2 form-control-label" for="promotion_exp">{$CMS->lang['section_name']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-10 col-xs-12"> 
				<p class="form-control-static-input">
					<span>{$data['section_name']}</span>
				</p>
			</div>
		</fieldset>
		<fieldset class="form-group row">
			<label class="col-sm-2 form-control-label" for="promotion_exp">{$CMS->lang['section_time']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-10 col-xs-12"> 
				<p class="form-control-static-input">
					<span>{$data['time']}</span>
				</p>
			</div>
		</fieldset>
		<fieldset class="form-group row">
			<label class="col-sm-2 form-control-label" for="promotion_exp">{$CMS->lang['section_note']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-10 col-xs-12"> 
				<p class="form-control-static-input">
					<span>{$data['section_note']}</span>
				</p>
			</div>
		</fieldset>
		<fieldset class="form-group row">
			<label class="col-sm-2 form-control-label" for="promotion_exp">{$CMS->lang['item_in_section']}</label>
			<div class="col-sm-10"> 
				<p class="form-control-static-input">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<th>{$CMS->lang['item_name']}</th>
								<th>{$CMS->lang['item_note']}</th>
								<th>{$CMS->lang['item_quantity']}</th>
								<th>{$CMS->lang['item_color']}</th>
								<th>{$CMS->lang['item_size']}</th>
							</thead>
							<tbody>
EOF;
		foreach ($data['content'] as $dt) {
			// $tmp='';
			foreach ($list_product as $product) {
				if ($product['exp_id']==$dt['item_name']) {
					$product_name=$product['exp_name'];
					// $tmp.="<option selected>{$product['exp_name']}</option>";
					break;
				}
			}
			$dt['item_color']=$CMS->attribute->get_info($dt['item_color']);
			$dt['item_size']=$CMS->attribute->get_info($dt['item_size'],'attr_name');
			$out.=<<<EOF
								<tr>
									<td>
										<input type="text" class="form-control" disabled value="{$product_name}">
									</td>
									<td>
										<input type="text" class="form-control" disabled value="{$dt['item_note']}">
									</td>
									<td>
										<input type="text" class="form-control" disabled value="{$dt['item_quantity']}">
									</td>
									<td>
										<input type="text" class="form-control" disabled value="{$dt['item_color']['attr_name']}" style="border: solid 3px #{$dt['item_color']['attr_value']}">
									</td>
									<td>
										<input type="text" class="form-control" disabled value="{$dt['item_size']}">
									</td>
								</tr>
EOF;
		}
		$out.=<<<EOF
							</tbody>
						</table>
					</div>
				</p>
			</div>
		</fieldset>
		</form>
	</div>
</section>
EOF;
		return $out;
	}
	public function add() {
		global $CMS, $DB, $member;
		$out=<<<EOF
<header class="section-header">
	<div class="tbl">
		<div class="tbl-row">
            <div class="tbl-cell">
				<h3>{$CMS->lang['section_new']}</h3>
            </div>
		</div>
	</div>
</header>
<section class="card">
    <section class="box-typical">
        <header class="box-typical-header">
            <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title">
                    <h3>{$CMS->lang['section_info']}</h3>
                </div>
                <div class="tbl-cell tbl-cell-action-bordered">
                    <a href="{$CMS->vars['root_domain']}/?site=warehouse{$CMS->class->search->url_return}">
                    <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                    </a>
                </div>
            </div>
        </header>
	</section>
	<div class="card-block">
		<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=warehouse&act=add" method="POST">
		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label" for="promotion_exp">{$CMS->lang['section_name']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
				<p class="form-control-static-input">
					<input class="form-control" size="45" type="text" name="name" id="name" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['section_name_err']}" data-validation-regex="/^[0-9a-zA-Z-_ ]*$/" data-validation-regex-message="{$CMS->lang['section_name_err']}"> 
				</p>
			</div>
		</fieldset>
		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label" for="promotion_exp">{$CMS->lang['section_note']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
				<p class="form-control-static-input">
					<textarea name="note" id="note" class="form-control" rows="5"></textarea>
				</p>
			</div>
		</fieldset>
		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label hidden-xs-down"></label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
				<p class="form-control-static-input">
					<button class="btn btn-rounded" type="submit" >{$CMS->lang['section_add']}</button>
				</p>
			</div>
		</fieldset>
	</div>
	</form>
</section>
<script>
</script>
EOF;
		return $out;
	}
}
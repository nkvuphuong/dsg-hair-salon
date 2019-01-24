<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['title_header_listing'];?></h3>
    </figure>
    <form method="POST" id="form_shipping_fee" name="form_shipping_fee" action="<?=$CMS->vars['root_domain'];?>/?site=config_general&act=shipping_fee&subact=add">
        <section class="tabs-section">
            <?=\core\ezy::render('shipping_fee_tab', 'config_general');?>
            <div class="tab-content" style="margin-bottom: 10px;">
                <div role="tabpanel" class="tab-pane fade active in" id="tabs-4-tab-11" aria-expanded="true">

<header class="section-header">
	<div class="d-flex">
		<h3 style="font-size: 25px;border-bottom: 3px solid #f5f8f9;padding-bottom: 5px;"><?=$CMS->lang['ship_fee_new'];?></h3>
	</div>
</header>
<div class="box-typical zp-form" style="border: none;">
	<fieldset class="form-group zp-multiselect">
		<label class="form-label">
			<?=$CMS->lang['ship_fee_choose_product'];?><font style="margin-left:5px" color="#FF0000">(*)</font>
		</label>
		<div class="row">
			<div class="col-sm-5">
				<input id="multiselect_search" name="multiselect_search" class="form-control" placeholder="Search..." style="border-radius: 0;" type="text">
				<select name="from" id="multiselect" class="form-control multiselect" size="8" multiple="multiple" style="border-top-left-radius: 0px;border-top-right-radius: 0px;">
					<?=$tpl->option_product_list;?>
				</select>
			</div>
			<div class="col-sm-2 text-center btn-group-multi">
				<div class="btn-item">
					<button type="button" id="multiselect_rightAll" class="btn change_selected"><i class="fa fa-angle-double-right"></i></button>
				</div>
				<div class="btn-item">
					<button type="button" id="multiselect_rightSelected" class="btn change_selected"><i class="fa fa-angle-right"></i></button>
				</div>
				<div class="btn-item">
					<button type="button" id="multiselect_leftSelected" class="btn change_selected"><i class="fa fa-angle-left"></i></button>
				</div>
				<div class="btn-item">
					<button type="button" id="multiselect_leftAll" class="btn change_selected"><i class="fa fa-angle-double-left"></i></button>
				</div>
			</div>
			<div class="col-sm-5">
				<input id="multiselect_to_search" name="multiselect_to_search" class="form-control" placeholder="Search..." style="border-radius: 0;" type="text">
				<select id="multiselect_to" class="form-control multiselect"  name="product_id[]" multiple="multiple" style="border-top-left-radius: 0px;border-top-right-radius: 0px;">
				</select>
			</div>
		</div>
	</fieldset>
	<div class="form-group zp-shipping">
		<label class="form-label"><?=$CMS->lang['ship_fee_cost'];?></label>
		<section class="box-typical scrollable">
			<div class="box-typical-body">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-zp">
						<thead>
							<tr>
								<th class="text-center" rowspan="2" colspan="2"><?=$CMS->lang['ship_fee_service'];?></th>
								<th class="text-center" colspan="4"><?=$CMS->lang['ship_fee_rates'];?></th>
							</tr>
							<tr>
								<td class="text-center"><?=$CMS->lang['ship_fee_domestics'];?></td>
							    <td class="text-center"><?=$CMS->lang['ship_fee_extra'];?></td>
							    <td class="text-center"><?=$CMS->lang['ship_fee_international'];?></td>
							    <td class="text-center"><?=$CMS->lang['ship_fee_extra'];?></td>
							</tr>
						</thead>
						<tbody id="shipping_services">
							<tr class="row-grid" >
								<td width="30">
									<a class="text-danger delline_service" onclick="delline(this);"><i class="fa fa-trash"></i>
								</td>
								<td>
									<select class="form-control defaultvalue_0" name="ship_type_service[]">
									 	<?=$tpl->option_shipping_service;?>
									</select>
								</td>
								<td>
									<input class="form-control" type="text" name="ship_price_domestics[]" value="" maxlength="7" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
								</td>
								<td>
									<input class="form-control" type="text" name="ship_extra_domestics[]" value=""  maxlength="7" onkeypress="return check_enter_number(event,this);"   onfocusout="check_enter_number_2(this,0);" >
								</td>
								<td><input class="form-control" type="text" name="ship_price_intl[]" maxlength="7" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" ></td>
								
								<td><input class="form-control" maxlength="7" type="text" name="ship_extra_intl[]" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);"></td>
							</tr>
							<tr class="row-grid" >
								<td width="30">
									<a class="text-danger delline_service" onclick="delline(this);"><i class="fa fa-trash"></i>
								</td>
								<td>
									<select class="form-control defaultvalue_1" name="ship_type_service[]">
									 	<?=$tpl->option_shipping_service;?>
									</select>
								</td>
								<td>
									<input class="form-control" type="text" name="ship_price_domestics[]" value="" maxlength="7" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
								</td>
								<td>
									<input class="form-control" type="text" name="ship_extra_domestics[]" value=""  maxlength="7" onkeypress="return check_enter_number(event,this);"   onfocusout="check_enter_number_2(this,0);" >
								</td>
								<td><input class="form-control" type="text" name="ship_price_intl[]" maxlength="7" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" ></td>
								
								<td><input class="form-control" maxlength="7" type="text" name="ship_extra_intl[]" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);"></td>
							</tr>
						</tbody>
					</table>
					<div class="table-pagination">
						<div class="paging-left">
							<div class="d-flex paging-ver">
								<button type="button" class="btn btn-green" id="newelem_shipping" style="margin: 15px;"><?=$CMS->lang['ship_fee_btn_more'];?></button>														
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>		
</div>

<section class="add_cart_footer">
    <a href="<?=$CMS->vars['root_domain'];?>/?site=config_general&act=shipping_fee" class="pull-left cancel"><?=$CMS->lang['back'];?></a>
    <button type="submit" class="btn btn-inline btn-primary ladda-button pull-right add_cart" id="form_submit">
    	<span class="ladda-label"><?=$CMS->lang['ship_fee_btn_add'];?></span>
    </button>
</section>

                </div>
            </div>
        </section>
    </form>
</section>
<script src="<?=$CMS->vars['js_acp'];?>/multiselect.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('.multiselect').multiselect({
			afterMoveToRight: function($left, $right, $options) {
				$("#multiselect_to").find("option").prop("selected", true);
			},
			afterMoveToLeft: function($left, $right, $options) {
				$("#multiselect_to").find("option").prop("selected", true);
			}
		});
		$('#multiselect_search').on('input keyup change', function(){
			multiselectSearch($(this).val(), '#multiselect');
		});
		$('#multiselect_to_search').on('input keyup change', function(){
			multiselectSearch($(this).val(), '#multiselect_to');
		});

		validate_form_custom("#form_shipping_fee", "#form_submit", 'add_shipping_fee');

		$("#newelem_shipping").click(function(){
			$( "#shipping_services tr" ).first().clone().appendTo( "#shipping_services" );
		});

		$('.defaultvalue_0 option[value="0"]').prop('selected', true);
		$('.defaultvalue_1 option[value="1"]').prop('selected', true);
	});

	function delline(obj)
	{
		if($(".row-grid").length > 1)
		{
			$(obj).parents("tr").first().remove();
		}
	}

	function multiselectSearch( key, element )
	{
		if( !key )
		{
			$(element).find('option').show();
		}
		else
		{
			$(element).find('option').hide();
			$(element).find('option').filter(function () { return convertVietnamese($(this).html(), 1).indexOf(convertVietnamese(key.toString(), 1)) < 0 ? false : true; }).show();
		}
	}
	
	function convertVietnamese(str, type) 
	{   
	    str = str.toString().toLowerCase();
	    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");
	    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e");
	    str = str.replace(/ì|í|ị|ỉ|ĩ/g,"i");
	    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o");
	    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");
	    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");
	    str = str.replace(/đ/g,"d");
	    if( type )
	    {
	    	str = str.replace(/\s/g,"");
	    }
	    return str;
	}
 </script>
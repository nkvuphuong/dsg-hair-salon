<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['title_header_listing'];?></h3>
    </figure>
    <form method="POST" id="form_shipping_fee" name="form_shipping_fee" action="<?=$CMS->vars['root_domain'];?>/?site=config_general&act=shipping_fee&subact=edit&id=<?=$tpl->data['ship_id'];?>">
        <section class="tabs-section">
            <?=\core\ezy::render('shipping_fee_tab', 'config_general');?>
            <div class="tab-content" style="margin-bottom: 10px;">
                <div role="tabpanel" class="tab-pane fade active in" id="tabs-4-tab-11" aria-expanded="true">

<header class="section-header">
	<div class="d-flex">
		<h3 style="font-size: 25px;border-bottom: 3px solid #f5f8f9;padding-bottom: 5px;"><?=$CMS->lang['ship_fee_update'];?></h3>
	</div>
</header>
<div class="box-typical zp-form" style="border: none;">
	<div class="form-group zp-shipping">
		<label class="form-label">
			<b><?=$tpl->data['product_url'];?></b>
			<div><small class="text-small"><i class="fa fa-barcode"></i> <?=$tpl->data['product_barcode'];?></small></div>
		</label>
		<section class="box-typical scrollable">
			<div class="box-typical-body">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-zp">
						<thead>
							<tr>
								<th class="text-center" rowspan="2"><?=$CMS->lang['ship_fee_service'];?></th>
								<th class="text-center" colspan="3"><?=$CMS->lang['ship_fee_rates'];?></th>
							</tr>
							<tr>
								<td class="text-center"><?=$CMS->lang['ship_fee_location'];?></td>
							    <td class="text-center"><?=$CMS->lang['ship_fee_price'];?></td>
							    <td class="text-center"><?=$CMS->lang['ship_fee_extra'];?></td>
							</tr>
						</thead>
						<tbody id="shipping_services">
							<tr class="row-grid" >
								<td>
									<?=$tpl->data['shipping_type'];?>
								</td>
								<td>
									<?=$tpl->data['shipping_location'];?>
								</td>
								<td>
									<input type="text" class="form-control" name="ship_price" value="<?=$tpl->data['ship_price'];?>" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
								</td>
								<td>
									<input type="text" class="form-control" name="ship_price_extra" value="<?=$tpl->data['ship_price_extra'];?>" onkeypress="return check_enter_number(event,this);" onfocusout="check_enter_number_2(this,0);" >
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</section>
	</div>		
</div>

<section class="add_cart_footer">
    <a href="<?=$CMS->vars['root_domain'];?>/?site=config_general&act=shipping_fee" class="pull-left cancel"><?=$CMS->lang['back'];?></a>
    <button type="submit" class="btn btn-inline btn-primary ladda-button pull-right add_cart" id="form_submit">
    	<span class="ladda-label"><?=$CMS->lang['ship_fee_btn_edit'];?></span>
    </button>
</section>

                </div>
            </div>
        </section>
    </form>
</section>

<!-- Logs -->
<?=$tpl->logs;?>

<script type="text/javascript">
	$(document).ready(function(){
		validate_form_custom("#form_shipping_fee","#form_submit");
	});
 </script>
<section class="add_table">
	<h4 class="heading"><i class="fa fa-caret-down"></i><span><?=$CMS->lang['abandoned_items'];?></span></h4>
	<div>
		<div class="table-responsive" style="overflow-x: initial;">
			<table class="table_cus" width="100%">
				<thead>
					<tr>
						<th><?=$CMS->lang['item_no.'];?></th>
						<th><?=$CMS->lang['item_name'];?></th>
						<th><?=$CMS->lang['item_price'];?></th>
						<th><?=$CMS->lang['item_quantity'];?></th>
						<th><?=$CMS->lang['item_total'];?></th>
					</tr>
				</thead>
				<tbody>
					<?
					if( is_array($tpl->cart_content) )
					{
						$i=0;
						foreach( $tpl->cart_content as $item ) 
						{
							$i++;
					?>
					<tr>
						<td>#<?=$i;?></td>
						<td class="special_dropdown">
							<img class="imgrps pull-left" src="<?=$item['image'];?>" width="100">
							<p><?=$item['product_name'];?></p>
							<?if( $item['tax'] > 0 ){?>
							<span><?=$CMS->lang['item_tax'];?>: </span><span><?=$item['tax_show'];?></span>
							<?}?>
						</td>
						<td><?=$item['price_show'];?></td>
						<td><?=$item['quantity'];?></td>
						<td><?=$item['total_show'];?></td>
					</tr>
					<?}}?>
					<tr>
						<td colspan="4"><div class="text-right"><b><?=$CMS->lang['item_subtotal'];?></b></div></td>
						<td><?=$tpl->cart_total;?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</section>
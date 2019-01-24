<?if( !empty($tpl->dataShipFee) ){?>
<table id="example" class="display table table_cus tbl-typical dataTable no-footer" role="grid" style="margin-top: 0 !important;">
    <thead class="vertical-middle">
        <tr>
            <th>ID</th>
            <th><?=$CMS->lang['ship_fee_price'];?></th>
            <th><?=$CMS->lang['ship_fee_price_extra'];?></th>
            <th><?=$CMS->lang['ship_fee_type'];?></th>
            <th><?=$CMS->lang['ship_fee_location'];?></th>
            <th width="100"></th>
        </tr>
    </thead>
    <tbody>
        <?
        if( is_array($tpl->dataShipFee) )
        {
            foreach($tpl->dataShipFee as $dataShipFee)
            {
        ?>
        <tr>
            <td><b>#<?=$dataShipFee['ship_id'];?></b></td>
            <td><b><?=$dataShipFee['us_shipping_c'];?></b></td>
            <td><b><?=$dataShipFee['us_extra_c'];?></b></td>
            <td><b style="color: <?=$dataShipFee['text_color'];?>"><?=$dataShipFee['shipping_type'];?></b></td>
            <td><b style="color: <?=$dataShipFee['text_color'];?>;"><?=$dataShipFee['shipping_location'];?></b></td>
            <td>
                <div class="clearfix nowrap">
                    <a class="edit pointer" onclick="openEditShipFee('<?=$dataShipFee['ship_id'];?>', '<?=$dataShipFee['product_id'];?>');"><i class="fa fa-edit"></i></a>
                    <a class="edit pointer" onclick="openDeleteShipFee('<?=$dataShipFee['ship_id'];?>', '<?=$dataShipFee['product_id'];?>');"><i class="fa fa-trash-o"></i></a>
                </div>
            </td>   
        </tr>
        <?}}?>
    </tbody>
</table>

<?}else{?>
<p>...</p>
<?}?>
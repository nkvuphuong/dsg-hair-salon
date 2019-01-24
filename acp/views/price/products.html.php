<section class="add_table">
    <div class="data_table">
        <div class="table-responsive">
            <table class="table table_cus">
                <thead>
                <tr>
                    <th width="5%" onclick="Price.removeAllItems()"><i class="fa fa-times" aria-hidden="true"></i></th>
                    <th width="35%">
                        Name
                    </th>
                    <th width="20%">General price</th>
                    <th width="20%">Old price</th>
                    <th width="20%">New price</th>
                </tr>
                <tr>
                    <th></th>
                    <th>
                        <select onchange="Price.addItemByProductId($(this).val())" name="choose_product" id="choose_product"></select>
                    </th>
                    <th colspan="3"></th>
                </tr>
                </thead>
                <tbody id="priceItems">
                </tbody>
            </table>
        </div>
    </div>
</section>
<script src="/acp/jsacp/price.js"></script>
<script>
    $(document).ready(function() {
        Price.setProducts(<?= $tpl->products ? \lib\input::jsonEncode($tpl->products, 0): '[]' ?>);

        <? if($tpl->items) { ?>
        Price.setItems(<?=\lib\input::jsonEncode($tpl->items,0)?>);
        <?} ?>
    })
</script>

<div class="row"><?=$CMS->global->languageTab('langTab');?></div>
<form name="form_product" id="form_product" method="POST" action="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}";?>"  >
    <section class="add_table" style="margin-top: 0px;">
        <div class="data_table">
            <div class="table-responsive" style="overflow: auto;">
                <table id="example" class="display table table_cus" cellspacing="0" width="100%" style="margin-top: 0px !important;">
                    <thead>
                    <?if ($_SESSION['is_mobile'] == true) {?>
                        <tr>
                            <th data-orderable="false" data-sortable="false" width="5%"></th>
                            <th data-orderable="false" data-sortable="false" width="1%" style="position: relative;z-index: 1;">
                                <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_product');" id="checkall">
                                    <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
                                    <label></label>
                                </div>
                                <div class="initEventClickWrap" style="position: absolute;z-index: 2;width: 100%;height: 100%;top: 0; left: 0; right: 0; bottom: 0;"></div>
                            </th>
                            <th  width="15%" data-sortable="false" ><?=$CMS->lang['p_name']?></th>
                            <?if ($CMS->input['site'] == "product") {?>
                            <th  width="15%" data-sortable="false" ><?=$CMS->lang['p_store']?></th>
                            <th width="10%"  ><?=$CMS->lang['p_barcode']?></th>
                            <?}?>
                            <!-- <th data-orderable="false" width="3%"><?=$CMS->lang['p_code_short']?></th> -->
                            <th width="10%" data-sortable="true"><?=$CMS->lang['p_category']?></th>
                            <!-- <th width="10%" data-sortable="true"><?=$CMS->lang['p_price']?></th> -->
                            <th width="10%" data-sortable="true"><?=$CMS->lang['p_price_sell']?></th>
                            <?if ($CMS->input['site'] == "product"){?>
                                <th width="10%" data-orderable="false" ><?=$CMS->lang['p_inventory']?></th>
                            <?}?>
                            <th data-orderable="false" width="6%"></th>
                        </tr>
                    <?} else {?>
                        <tr>
                            <th data-orderable="false" data-sortable="false" width="3%" style="position: relative;z-index: 1;">
                                <div style="margin-right: 10px;" class="checkbox checkbox-only" onclick="javascript:form_checkall('form_product');" id="checkall">
                                    <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
                                    <label></label>
                                </div>
                                <div class="initEventClickWrap" style="position: absolute;z-index: 2;width: 100%;height: 100%;top: 0; left: 0; right: 0; bottom: 0;"></div>
                            </th>
                            <!-- <th width="3%"><?=$CMS->lang['p_code_short']?></th> -->
                            <th width="15%" data-orderable="true" ><?=$CMS->lang['p_name']?></th>
                            <?if ($CMS->input['site'] == "product") {?>
                            <th  width="15%" data-sortable="false" ><?=$CMS->lang['p_store']?></th>
                            <th width="10%"><?=$CMS->lang['p_barcode']?></th>
                            <?}?>
                            <th width="10%" data-sortable="true"><?=$CMS->lang['p_category']?></th>
                            <!-- <th width="10%" ><?=$CMS->lang['p_price']?></th> -->
                            <th width="10%" data-sortable="false" data-orderable="false"><?=$CMS->lang['p_price_sell']?></th>
                            <?if ($CMS->input['site'] == "product"){?>
                                <th width="10%" data-orderable="false" ><?=$CMS->lang['p_inventory']?></th>
                            <?}?>
                            <th data-orderable="false" width="6%"></th>
                        </tr>
                    <?}?>
                    </thead>
                    <?
                    foreach($tpl->data as $data){
                        $price_tr = $data['product_price_sell'] ? $data['product_price_sell'] : $data['product_price'];
                        ?>
                        <tr>
                            <?if ($_SESSION['is_mobile'] == true) {?>
                                <td></td>
                                <td style="position: relative;z-index: 1;">
                                    <div class="checkbox checkbox-only">
                                        <input type="checkbox"  name="id_<?=$data['record_cnt']?>" id="id_<?=$data['record_cnt']?>" value="<?=$data['product_id']?>"/>
                                        <label for="id_<?=$data['record_cnt']?>"></label>
                                    </div>

                                    <input type="hidden" name="name[_<?=$data['record_cnt']?>]" value="<?=$data['product_name']?>" />
                                    <input type="hidden" name="barcode[_<?=$data['record_cnt']?>]" value="<?=$data['product_barcode']?>" />
                                    <input type="hidden" name="price[_<?=$data['record_cnt']?>]" value="<?=$price_tr?>" />

                                    <div class="initEventClickWrap" style="position: absolute;z-index: 2;width: 100%;height: 100%;top: 0; left: 0; right: 0; bottom: 0;"></div>
                                </td>
                                <td class="short_info_td">
                                    <?=$data['product_name_bk']?>
                                    <div class="clearfix text-left"><?=$data['product_code'];?></div>
                                </td>
                                <?if ($CMS->input['site'] == "product") {?>
                                <td class="short_info_td">
                                    <div class="store-name-text">
                                    <a href="<?="{$CMS->vars['root_domain']}/?site=store&act=show&id={$data['store_id']}"?>" title="<?=$data['store_name']?>"  style="border: none;">
                                        <?=$data['store_name']?>
                                    </a>
                                    </div>
                                </td>
                                <td>
                                    <?=$data['product_barcode']?>
                                </td>
                                <?}?>
                                <!-- <td>
                                    <?=$data['product_code']?>
                                </td> -->
                                <td>
                                    <?=$data['product_group_c']?>
                                </td>
                                <!-- <td>
                                    <?=$data['product_price_c']?>
                                </td> -->
                                <td>
                                    <?=$data['product_price_sell_c']?>
                                </td>
                                <?if ($CMS->input['site'] == "product") {?>
                                    <td>
                                        <?=$data['ass_inventory']?>
                                    </td>
                                <?}?>
                            <?} else {
                                $name_show = is_array($data['product_name']) ? $data['product_name'][$CMS->vars['default_language']] :$data['product_name'];
                                ?>
                                <td style="position: relative;z-index: 1;">
                                    <div style="margin-right: 18px;" class="checkbox checkbox-only">
                                        <input type="checkbox"  name="id_<?=$data['record_cnt']?>" id="id_<?=$data['record_cnt']?>" value="<?=$data['product_id']?>"/>
                                        <label for="id_<?=$data['record_cnt']?>"></label>
                                    </div>
                                    <input type="text" maxlength="3" style="width:30px;border: solid 1px rgba(197,214,222,.7);box-shadow: none;border-radius: .2rem;text-align:center;z-index: 3;position: relative;" name="order_<?=$data['product_id']?>" id="order_<?=$data['product_id']?>" value="<?=$data['product_order']?>"/>
                                    
                                    <input type="hidden" name="name[_<?=$data['record_cnt']?>]" value="<?=$name_show?>" />
                                    <input type="hidden" name="barcode[_<?=$data['record_cnt']?>]" value="<?=$data['product_barcode']?>" />
                                    <input type="hidden" name="price[_<?=$data['record_cnt']?>]" value="<?=$price_tr?>" />

                                    <div class="initEventClickWrap" style="position: absolute;z-index: 2;width: 100%;height: 100%;top: 0; left: 0; right: 0; bottom: 0;"></div>
                                </td>
                                <!-- <td>
                                    <?=$data['product_code']?>
                                </td> -->
                                <td style="white-space: normal;">
                                    <?
                                    if($CMS->vars['translations']){
                                        foreach ($CMS->vars['translations'] as $langCode => $langName){
                                            ?>
                                            <span class="langTab" lang="<?=$langCode?>">
                                                            <a href="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=show&id={$data['product_id']}"?>"  title="<?=$data['product_name'][$langCode]?>">
                                                                    <?=$data['product_name'][$langCode]?>
                                                            </a>
                                                        </span>
                                        <?}} else {?>
                                        <span><?=$data['product_name_bk']?></span>
                                    <?}?>
                                    <span class="btn-inline btn-outline" style="float: left; margin: 0 5px 0 0;"><?=$data['product_code']?></span>
                                    <span class="btn-inline btn-outline" style="float: left; margin: 0 5px"><?=$data['product_type_c']?></span>
                                    <span class="btn-inline btn-outline" style="float: left; margin: 0 5px"><?=$data['product_show_bk']?></span>
                                </td>
                                <? if ($CMS->input['site'] == "product") {?>
                                <td class="short_info_td">
                                    <div class="store-name-text">
                                        <a href="<?="{$CMS->vars['root_domain']}/?site=store&act=show&id={$data['store_id']}"?>" title="<?=$data['store_name']?>" style="border: none;">
                                            <?=$data['store_name']?>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <?=$data['product_barcode']?>
                                </td>
                                <?}?>
                                <td>
                                    <?=$data['product_group_c']?>
                                </td>
                            <?}?>

                            <?if ($_SESSION['is_mobile'] == false) {?>
                                <!-- <td>
                                    <?=$data['product_price_c']?>
                                </td> -->
                                <td>
                                    <?=$data['product_price_sell_c']?>
                                </td>
                                <?if ($CMS->input['site'] == "product") {?>
                                    <td>
                                        <?=$data['ass_inventory']?>
                                    </td>
                                <?}?>
                            <?}?>
                            <td align="center">
                                <div class="dropdown">
                                    <button class="btn dropdown-toggle" id="dd-header-add" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Action
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dd-header-add" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 30px, 0px); top: 0px; right: 0px; will-change: transform;">
                                        <?if (!defined("is_web_us")) {?>
                                            <?if ($CMS->permit['product_is_root'] || $CMS->permit['product_preview_barcode']) {?>
                                                <a onclick="print_barcode_popup('<?=$data['product_id']?>')"  class="dropdown-item"><i class="fa fa-barcode"></i> <?=$CMS->lang['title_print_barcode']?></a>
                                            <?}?>
                                        <?}?>

                                        <? if ($CMS->permit['order_add'] == 1) {?>
                                            <a href="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=order_add&p_id={$data['product_id']}"?>" title="<?=$CMS->lang['title_add_order']?>" class="dropdown-item">
                                                <i class="fa fa-cart-plus"></i> <?=$CMS->lang['title_add_order']?></a>
                                        <?}?>

                                        <?  if ($CMS->permit['product_edit'] == 1) {?>
                                            <a href="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=edit&id={$data['product_id']}"?>" title="" class="dropdown-item"><i class="fa fa-edit"></i> <?=$CMS->lang['title_edit_ps']?></a>
                                            <? if($tpl->popupForm) {?>
                                                <a title="" class="dropdown-item" onclick="$('#quickActionForm').attr({'act':'edit_do','product_id':'<?=$data['product_id']?>'}) ; loadProductQuickAction('<?=$data['product_id']?>')"><i class="fa fa-edit"></i> <?=$CMS->lang['title_quick_edit_ps']?></a>
                                            <?} ?>
                                        <?} ?>

                                        <? if ($CMS->permit['product_delete'] == 1) {?>
                                            <a onclick="delete_confirm('<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&act=delete&id={$data['product_id']}"?>');" class="dropdown-item"><i class="fa fa-trash-o"></i> <?=$CMS->lang['title_delete_product']?></a>
                                        <?} ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?}?>
                </table>
            </div>
        </div>
        <div class="fuction_table">
            <div class="pull-left">
                <p class="form-control-static ">
                    <?=$CMS->product->action_control?>
                </p>
            </div>
            <nav class="pull-right">
                <?=$CMS->product->show_page?>
            </nav>
        </div>
    </section>
    <input type="hidden" name="data_cnt" value="<?=$CMS->product->record_cnt?>">
</form>

<?if ($_SESSION['is_mobile'] == true) {?>
    <script>
        $(function() {
            $('#example').DataTable({
                order: [],
                language: {
                    emptyTable: '<?=$CMS->lang['no_result']?>: <?=$tpl->p_name_convert?>'
                },
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
<?} else {?>
    <?if ($CMS->input['p_name'] != "") { $p_name_convert = urldecode($CMS->input['p_name']);?>
        <script>
            $(function() {
                $('#example').DataTable({
                    language: {
                        emptyTable: '<?=$CMS->lang['no_result']?>: <?=$p_name_convert?>'
                    },
                    order: [],
                    paging: false,
                    searching: false,
                    info: false
                });
            });
        </script>
    <?} else {?>
        <script>
            $(function() {
                $('#example').DataTable({
                    language: {
                        emptyTable: '<?=$CMS->lang['no_data'];?>'
                    },
                    order: [],
                    paging: false,
                    searching: false,
                    info: false
                });
            });
        </script>
    <?}?>
<?}?>
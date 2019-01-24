<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['title_header_listing'];?></h3>
    </figure>
    <section class="tabs-section">
        <?=\core\ezy::render('shipping_fee_tab', 'config_general');?>
        <div class="tab-content" style="margin-bottom: 10px;">
            <div role="tabpanel" class="tab-pane fade active in" id="tabs-4-tab-11" aria-expanded="true">

<!-- Shipping fee -->
<form method="post" name="form_shipping_fee" id="form_shipping_fee" action="<?=$CMS->vars['root_domain'];?>/?site=config_general&act=shipping_fee">
<input type="hidden" name="subact" value="">
<section class="box-typical scrollable" style="border: none;">
    <div class="box-typical-body">
        <div class="table-responsive">
            <table id="example" class="display table table_cus tbl-typical dataTable no-footer" role="grid">
                <thead class="vertical-middle">
                    <tr>
                        <th>
                            <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_shipping_fee');" id="checkall">
                                <input id="id_record_cnt" type="checkbox" name="all" onmoudiscountver="on_mouse=0;" onmoudiscountut="on_mouse=1;">
                                <label for="id_record_cnt"></label>
                            </div>
                        </th>
                        <th>ID</th>
                        <th><?=$CMS->lang['ship_fee_product'];?></th>
                        <th><?=$CMS->lang['ship_fee_price'];?></th>
                        <th><?=$CMS->lang['ship_fee_price_extra'];?></th>
                        <th><?=$CMS->lang['ship_fee_type'];?></th>
                        <th><?=$CMS->lang['ship_fee_location'];?></th>
                        <th width="100"></th>
                    </tr>
                    <tr id="search_shipping_fee">
                        <th></th>
                        <th></th>
                        <th>
                            <div class="clearfix box_result_find_wrap qs_ship_wrap">
                                <input class="form-control qs_ship qs_ship_loading relative" type="text" name="keywords" value="<?=$tpl->keywords;?>" style="width: 100%;">
                                <div class="box_result_find qs_ship_suggestion" style="display:none"></div>
                            </div>
                            <script>
                                $(document).ready(function(){
                                    var qsShipTimer = 0; //timer identifier
                                    autocompleteNew('.qs_ship', qsShipTimer, '<?=$CMS->vars['root_domain'];?>/?site=config_general&act=shipping_fee&subact=quicksearch');
                                    setEventMouseUpOutSize('.qs_ship_wrap', '.qs_ship_suggestion');
                                });
                            </script>
                        </th>
                        <th>
                            <input class="form-control" type="text" name="price" value="<?=$tpl->price;?>" style="width: 100%;max-width: 100px;" placeholder="From-To">
                        </th>
                        <th>
                            <input class="form-control" type="text" name="price_extra" value="<?=$tpl->price_extra;?>" style="width: 100%;max-width: 100px;" placeholder="From-To">
                        </th>
                        <th>
                            <select class="form-control" name="ship_type_service">
                                <?=$tpl->option_shipping_service;?>
                            </select>
                        </th>
                        <th>
                            <select class="form-control" name="ship_location">
                                <?=$tpl->option_ship_location;?>
                            </select>
                        </th>
                        <th width="100">
                            <a class="pointer btn btn-gray" onclick="doGeneralSearch('#search_shipping_fee', '<?=$CMS->vars['root_domain'];?>/?site=config_general&act=shipping_fee');" style="width: 100%;"><i class="fa fa-search" style="color: #fff;"></i></a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    if( is_array($tpl->data) )
                    {
                        foreach ($tpl->data as $data)
                        {
                    ?>
                    <tr>
                        <td>
                            <div class="checkbox checkbox-only">
                                <input type="checkbox"  name="id_<?=$data['record_cnt'];?>" id="id_<?=$data['record_cnt'];?>" value="<?=$data['ship_id'];?>"/>
                                <label for="id_<?=$data['record_cnt'];?>"></label>
                            </div>
                        </td>
                        <td><b>#<?=$data['ship_id'];?></b></td>
                        <td class="tbl-cell-photo">
                            <span><?=$data['product_url'];?></span>
                            <?if($data['product_barcode']){?>
                            <div><small class="text-small"><i class="fa fa-barcode"></i> <?=$data['product_barcode'];?></small></div>
                            <?}?>
                        </td>
                        <td><b><?=$data['us_shipping_c'];?></b></td>
                        <td><b><?=$data['us_extra_c'];?></b></td>
                        <td><b style="color: <?=$data['text_color'];?>"><?=$data['shipping_type'];?></b></td>
                        <td><b style="color: <?=$data['text_color'];?>;"><?=$data['shipping_location'];?></b></td>
                        <td>
                            <div class="clearfix nowrap">
                                <a href="<?=$CMS->vars['root_domain'];?>/?site=config_general&act=shipping_fee&subact=edit&id=<?=$data['ship_id'];?>" class="edit" data-toggle="tooltip" data-placement="bottom" title="<?=$CMS->lang['act_edit'];?>"><i class="fa fa-edit"></i></a>
                                <a onclick="delete_confirm('<?=$CMS->vars['root_domain'];?>/?site=config_general&act=shipping_fee&subact=delete&id=<?=$data['ship_id'];?>');" class="edit"  data-toggle="tooltip" data-placement="bottom" title="<?=$CMS->lang['act_delete'];?>"><i class="fa fa-trash-o"></i></a>
                            </div>
                        </td>   
                    </tr>
                    <?}}?>
                </tbody>
            </table>
            <div class="fuction_table">
                <div class="pull-left">
                    <input type="hidden" name="data_cnt" value="<?=\models\shipping_services::$record_cnt?>">
                    <a onclick="submit_confirm('#form_shipping_fee', {'subact':'delete_all'});" class="btn btn-gray"><?=$CMS->lang['ship_fee_delete_all'];?></a>
                    &nbsp;&nbsp;
                    <a href="<?=$CMS->vars['root_domain'];?>/?site=config_general&act=shipping_fee&subact=add" class="btn btn-green"><?=$CMS->lang['ship_fee_new'];?></a>
                    &nbsp;&nbsp;
                    <a href="<?=$CMS->vars['root_domain'];?>/?site=config_general&act=shipping_fee&subact=clear_cache" class="btn btn-warning">Clear cache</a>
                </div>
                <nav class="pull-right">
                     <div id="block_page">
                        <?=$tpl->show_page;?>
                    </div>
                </nav>
            </div>
        </div>
    </div><!--.box-typical-body-->
</section>
</form>

            </div>
        </div>
    </section>
</section>

<!-- Shipping fee, shipping location default -->
<?=\core\ezy::render('shipping_default', 'config_general');?>
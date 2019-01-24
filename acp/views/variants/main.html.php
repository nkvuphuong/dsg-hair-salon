<script src="\acp\jsacp\variants.js"></script>
<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['var_header'];?></h3>
        <figure class="pull-right right">
            <!--a href="<?=$CMS->vars['root_domain']?>/?site=variants&act=add" title="" class="add_bill"><?=$CMS->lang['add_new_variants']?></a-->
            <a class="btn btn-warning" href="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache"?>"><?=$CMS->lang['clear_cache']?></a>
        </figure>
    </figure>

    <form method="post" name="form_variants" id="form_variants" action="">
        <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
        <input type="hidden" name="act" value="">
        <section class="add_table">
            <div class="data_table">
                <div class="table table_cus table-responsive">
                    <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
                        <thead>
                        <tr>
                            <th width="5%" data-sortable="false" data-orderable="false" aria-label="">
                                <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_variants');" id="checkall">
                                    <input type="checkbox" name="all" onmoudiscountver="on_mouse=0;" onmoudiscountut="on_mouse=1;">
                                    <label for="id_<?=\lib\input::arrayValue($result, 'record_cnt')?>"></label>
                                </div>
                            </th>
                            <th width="20%" class="three-dots"><?=$CMS->lang['var_title'];?></th>
                            <th width="15%" class="three-dots"><?=$CMS->lang['var_sku'];?></th>
                            <th width="10%" class="three-dots"><?=$CMS->lang['var_option'];?> 1</th>
                            <th width="10%" class="three-dots"><?=$CMS->lang['var_option'];?> 2</th>
                            <th width="10%" class="three-dots"><?=$CMS->lang['var_option'];?> 3</th>
                            <th width="10%" class="three-dots"><?=$CMS->lang['var_price'];?></th>
                            <th width="10%" style="text-align:center" data-sortable="false" data-orderable="false"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? if($tpl->data) {?>
                            <?foreach($tpl->data as $result){ $result = \models\variants::convertValue($result)?>
                                <tr>
                                    <td>
                                        <div class="checkbox checkbox-only">
                                            <input type="checkbox"  name="id_<?=$result['record_cnt']?>" id="id_<?=$result['record_cnt']?>" value="<?=$result['var_id']?>"/>
                                            <label for="id_<?=$result['record_cnt']?>">#<?=$result['var_id']?></label>
                                        </div>
                                    </td>
                                    <td class="threedots"><?=$result['var_title']?></td>
                                    <td class="threedots"><?=$result['var_sku']?></td>
                                    <td class="threedots"><?=$result['var_option1']?></td>
                                    <td class="threedots"><?=$result['var_option2']?></td>
                                    <td class="threedots"><?=$result['var_option3']?></td>
                                    <td class="threedots"><?=$result['var_price_c']?></td>
                                    <td style="text-align:center">
                                        <? if($CMS->permit['variants_edit'] || $CMS->permit['variants_is_root']){?>
                                            <!--a href="<?=$CMS->vars['root_domain']?>/?site=variants&act=edit&id=<?=$result['data_bk']['var_id']?>" title""="" class="edit"><i class="fa fa-edit"></i></a-->
                                        <?}?>

                                        <? if($CMS->permit['variants_delete'] || $CMS->permit['variants_is_root']){?>
                                            <a onclick="delete_confirm('<?=$CMS->vars['root_domain']?>/?site=variants&act=delete&id=<?=$result['data_bk']['var_id']?>');"   class="edit"><i class="fa fa-trash-o"></i></a>
                                        <?}?>
                                    </td>
                                </tr>
                            <?}?>
                        <?} else {?>
                        <?}?>
                        </tbody>
                    </table>
                </div>
                <div class="fuction_table">
                    <div class="pull-left">
                        <p class="form-control-static">
                            <select class="form-control select2" name="subact" onchange="return submit_action_control(this,$(this).parents('form:first').attr('id'));" defaultvalue="delete_all" emsg="Please choose a action" ehide="1">
                                <option value="">-- <?=$CMS->lang['choose_action']?> --</option>
                                <option value="delete_all"><?=$CMS->lang['act_delete']?></option>
                            </select>
                        </p>
                    </div>
                    <nav class="pull-right">
                        <div class="block_bottom pagination pagination-sm"><?=$CMS->show_page?></div>
                    </nav>
                </div>
            </div>
        </section>
        <input type="hidden" name="data_cnt" value="<?=\models\variants::$record_cnt?>">
    </form>
</section>
<script>
    $('.table-responsive').on('show.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "inherit" );
    });

    $('.table-responsive').on('hide.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "auto" );
    })
</script>

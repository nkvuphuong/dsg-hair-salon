<script src="\acp\jsacp\shift_work.js"></script>
<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['shift_work_header'];?></h3>
        <figure class="pull-right right">
            <a href="<?=$CMS->vars['root_domain']?>/?site=shift_work&act=add" title="" class="add_bill"><?=$CMS->lang['add_new_shift_work']?></a>
            <a class="btn btn-warning" href="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache"?>"><?=$CMS->lang['clear_cache']?></a>
        </figure>
    </figure>

    <form method="post" name="form_shift_work" id="form_shift_work" action="">
        <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
        <input type="hidden" name="act" value="">
        <section class="add_table">
            <div class="data_table">
                <div class="table table_cus table-responsive">
                    <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
                        <thead>
                        <tr>
                            <th width="5%" data-sortable="false" data-orderable="false" aria-label="">
                                <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_shift_work');" id="checkall">
                                    <input type="checkbox" name="all" onmoudiscountver="on_mouse=0;" onmoudiscountut="on_mouse=1;">
                                    <label for="id_<?=\lib\input::arrayValue($result, 'record_cnt')?>"></label>
                                </div>
                            </th>
                            <th width="20%" class="three-dots"><?=$CMS->lang['shift_work_name']?></th>
                            <th width="20%" class="three-dots"><?=$CMS->lang['shift_work_in']?></th>
                            <th width="10%" class="three-dots"><?=$CMS->lang['shift_work_out']?></th>
                            <th width="10%" class="three-dots"><?=$CMS->lang['shift_work_order']?></th>
                            <th width="10%" style="text-align:center" data-sortable="false" data-orderable="false"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? if($tpl->data) {?>
                            <?foreach($tpl->data as $result){ $result = \models\shift_work::convertValue($result)?>
                                <tr>
                                    <td>
                                        <div class="checkbox checkbox-only">
                                            <input type="checkbox"  name="id_<?=$result['record_cnt']?>" id="id_<?=$result['record_cnt']?>" value="<?=$result['shift_work_id']?>"/>
                                            <label for="id_<?=$result['record_cnt']?>">#<?=$result['shift_work_id']?></label>
                                        </div>
                                    </td>
                                    <td class="threedots"><?=$result['shift_work_name']?></td>
                                    <td class="threedots"><?=$result['shift_work_in']?></td>
                                    <td class="threedots"><?=$result['shift_work_out']?></td>
                                    <td class="threedots"><input onchange="shiftWorkQuickUpdateSortOrder(<?=$result['shift_work_id']?>)" type="number" name="shift_work_order[<?=$result['shift_work_id']?>]" id="sort_input_<?=$result['shift_work_id']?>" class="form-control" value="<?=$result['shift_work_order']?>"></td>
                                    <td style="text-align:center">
                                        <? if($CMS->permit['shift_work_edit'] || $CMS->permit['shift_work_is_root']){?>
                                            <a href="<?=$CMS->vars['root_domain']?>/?site=shift_work&act=edit&id=<?=$result['data_bk']['shift_work_id']?>" title""="" class="edit"><i class="fa fa-edit"></i></a>
                                        <?}?>

                                        <? if($CMS->permit['shift_work_delete'] || $CMS->permit['shift_work_is_root']){?>
                                            <a onclick="delete_confirm('<?=$CMS->vars['root_domain']?>/?site=shift_work&act=delete&id=<?=$result['data_bk']['shift_work_id']?>');"   class="edit"><i class="fa fa-trash-o"></i></a>
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
                            <select class="form-control select2" name="subact" onchange="return submit_action_control(this,$(this).parents('form:first').attr('id'));" defaultvalue="delete_all" emsg="Bạn phải chọn một Hành Động !" ehide="1">
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
        <input type="hidden" name="data_cnt" value="<?=\models\shift_work::$record_cnt?>">
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

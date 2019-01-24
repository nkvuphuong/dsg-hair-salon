<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['rating_header'];?></h3>
        <figure class="pull-right right">
            <?=$CMS->global->headerQuickSearch("{$CMS->vars['root_domain']}/?site=rating&subact=autocomplete");?>
            <a href="<?=$CMS->vars['root_domain']?>/?site=rating&act=add" title="" class="add_bill"><?=$CMS->lang['add_new_rating']?></a>
            <a class="btn btn-warning" href="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache"?>"><?=$CMS->lang['clear_cache']?></a>
        </figure>

        <section class="search_adv" >
            <form method="get" id="formsearch_adv" style="<?=$tpl->quickSearchDisplay?>"  action="">
                <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
                <figure class="box-typical box-typical box-typical-padding border">
                    <?=\lib\tpl::get('search_input','');?>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="submit" name="submit" id="submit" value="<?=$CMS->lang['filter']?>">
                            <a href="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>"><label class="btn btn-warning">Reset</label></a>
                        </div>
                    </div>
                </figure>
            </form>
        </section>
    </figure>

    <form method="post" name="form_rating" id="form_rating" action="">
        <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
        <input type="hidden" name="act" value="">
        <section class="add_table">
            <div class="data_table">
                <div class="table table_cus table-responsive">
                    <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
                        <thead>
                        <tr>
                            <th width="5%" data-sortable="false" data-orderable="false" aria-label="">
                                <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_rating');" id="checkall">
                                    <input type="checkbox" name="all" onmoudiscountver="on_mouse=0;" onmoudiscountut="on_mouse=1;">
                                    <label for="checkall"></label>
                                </div>
                            </th>
                            <th width="20%" class="three-dots"><?=$CMS->lang['rating_name']?></th>
                            <th width="20%" class="three-dots"><?=$CMS->lang['rating_label']?></th>
                            <th width="10%" class="three-dots"><?=$CMS->lang['rating_value']?></th>
                            <th width="10%" class="three-dots"><?=$CMS->lang['rating_order']?></th>
                            <th width="10%"><?=$CMS->lang['rating_status']?></th>
                            <th width="10%" style="text-align:center" data-sortable="false" data-orderable="false"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? if($tpl->data) {?>
                            <?foreach($tpl->data as $result){ $result = \models\rating::convertValue($result)?>
                                <tr>
                                    <td>
                                        <div class="checkbox checkbox-only">
                                            <input type="checkbox"  name="id_<?=$result['record_cnt']?>" id="id_<?=$result['record_cnt']?>" value="<?=$result['rating_id']?>"/>
                                            <label for="id_<?=$result['record_cnt']?>">#<?=$result['rating_id']?></label>
                                        </div>
                                    </td>
                                    <td class="threedots"><?=$result['rating_name']?></td>
                                    <td class="threedots"><?=$result['rating_label']?></td>
                                    <td class="threedots"><?=$result['rating_value']?></td>
                                    <td class="threedots"><input onchange="ratingQuickUpdateSortOrder(<?=$result['rating_id']?>)" type="number" name="rating_order[<?=$result['rating_id']?>]" id="sort_input_<?=$result['rating_id']?>" class="form-control" value="<?=$result['rating_order']?>"></td>
                                    <td class="threedots"><strong style="color: <?=$CMS->lang['rating_status_color_'.$result['data_bk']['rating_status']];?>"><?=$result['rating_status']?></strong></td>
                                    <td style="text-align:center">
                                        <? if($CMS->permit['rating_edit'] || $CMS->permit['rating_is_root']){?>
                                            <a href="<?=$CMS->vars['root_domain']?>/?site=rating&act=edit&id=<?=$result['data_bk']['rating_id']?>" title""="" class="edit"><i class="fa fa-edit"></i></a>
                                        <?}?>

                                        <? if($CMS->permit['rating_delete'] || $CMS->permit['rating_is_root']){?>
                                            <a onclick="delete_confirm('<?=$CMS->vars['root_domain']?>/?site=rating&act=delete&id=<?=$result['data_bk']['rating_id']?>');"   class="edit"><i class="fa fa-trash-o"></i></a>
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
        <input type="hidden" name="data_cnt" value="<?=\models\rating::$record_cnt?>">
    </form>
</section>
<script>
        $(function() {

            <? if(!$tpl->data){?>
            $('#example').DataTable({
                language: {
                    emptyTable: '<?=$CMS->lang['data_not_found']?>'
                },
                order: [[ 2,"desc"]],
                paging: false,
                searching: false,
                info: false,
                <?=$_SESSION['is_mobile'] ? "responsive: { details: true}," : ""?>
            });
            <?} ?>

//            autocomplete_quick_search();
        });
</script>

<script>
    $('.table-responsive').on('show.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "inherit" );
    });

    $('.table-responsive').on('hide.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "auto" );
    })
</script>

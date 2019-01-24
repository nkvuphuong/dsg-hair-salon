<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['embed_header'];?></h3>
        <figure class="pull-right right">
            <?=$CMS->global->headerQuickSearch("{$CMS->vars['root_domain']}/?site=embed&subact=autocomplete");?>
            <a href="<?=$CMS->vars['root_domain']?>/?site=embed&act=add" title="" class="add_bill"><?=$CMS->lang['add_new_embed']?></a>
            <a class="btn btn-warning" href="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache"?>"><?=$CMS->lang['clear_cache']?></a>
        </figure>

        <section class="search_adv" >
            <form method="get" id="formsearch_adv" style="<?=$tpl->quickSearchDisplay?>"  action="">
                <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
                <figure class="box-typical box-typical box-typical-padding border">
                    <?=$tpl->search_input;?>
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

    <form method="post" name="form_embed" id="form_embed" action="">
        <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
        <input type="hidden" name="act" value="">
        <section class="add_table">
            <div class="data_table">
                <div class="table table_cus table-responsive">
                    <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
                        <thead>
                        <tr>
                            <th  data-orderable="false" data-sortable="false" aria-label="" style="margin:0px;padding:0px;"></th>
                            <th width="5%" data-sortable="false" data-orderable="false" aria-label="">
                                <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_embed');" id="checkall">
                                    <input type="checkbox" name="all" onmoudiscountver="on_mouse=0;" onmoudiscountut="on_mouse=1;">
                                    <label for="checkall"></label>
                                </div>
                            </th>
                            <th width="20%" class="three-dots"><?=$CMS->lang['embed_name']?></th>
                            <th width="10%" class="three-dots"><?=$CMS->lang['embed_code']?></th>
                            <th width="10%" class="three-dots"><?=$CMS->lang['embed_interval']?> (<?= $CMS->lang['minute']; ?>)</th>
                            <th class="three-dots"><?=$CMS->lang['embed_start_date']?></th>
                            <th class="three-dots"><?=$CMS->lang['embed_end_date']?></th>
                            <th width="10%"><?=$CMS->lang['embed_status']?></th>
                            <th width="10%" style="text-align:center" data-sortable="false" data-orderable="false"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? if($tpl->data) {?>
                            <?foreach($tpl->data as $result){ $result = \models\embed::convertValue($result)?>
                                <tr>
                                    <td style="margin:0px;padding:0px;<?=$_SESSION['is_mobile'] ? "padding-right:27px;" : ""?>"></td>
                                    <td>
                                        <div class="checkbox checkbox-only">
                                            <input type="checkbox"  name="id_<?=$result['record_cnt']?>" id="id_<?=$result['record_cnt']?>" value="<?=$result['embed_id']?>"/>
                                            <label for="id_<?=$result['record_cnt']?>"></label>
                                        </div>
                                    </td>
                                    <td class="threedots"><?=$result['embed_name']?></td>
                                    <td>{{embed.<?=$result['embed_code']?>}}</td>
                                    <td class="threedots"><?=$result['embed_interval']?></td>
                                    <td class="threedots"><?=$result['embed_start_date']?></td>
                                    <td class="threedots"><?=$result['embed_end_date']?></td>
                                    <td class="threedots"><strong style="color: <?=$CMS->lang['embed_status_color_'.$result['data_bk']['embed_status']];?>"><?=$result['embed_status']?></strong></td>
                                    <td style="text-align:center">
                                        <? if($CMS->permit['embed_edit'] || $CMS->permit['embed_is_root']){?>
                                            <a href="<?=$CMS->vars['root_domain']?>/?site=embed&act=edit&id=<?=$result['data_bk']['embed_id']?>" title""="" class="edit"><i class="fa fa-edit"></i></a>
                                        <?}?>

                                        <? if($CMS->permit['embed_delete'] || $CMS->permit['embed_is_root']){?>
                                            <a onclick="delete_confirm('<?=$CMS->vars['root_domain']?>/?site=embed&act=delete&id=<?=$result['data_bk']['embed_id']?>');"   class="edit"><i class="fa fa-trash-o"></i></a>
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
        <input type="hidden" name="data_cnt" value="<?=\models\embed::$record_cnt?>">
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

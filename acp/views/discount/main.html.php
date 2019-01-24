<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['discount_header'];?></h3>
        <figure class="pull-right right">
            <?=$CMS->global->headerQuickSearch("{$CMS->vars['root_domain']}/?site=discount&subact=autocomplete");?>
            <?=$CMS->global->importExportData('discount', "", 1, null, 0, 1)?>
            <a href="<?=$CMS->vars['root_domain']?>/?site=discount&act=add" title="" class="add_bill"><?=$CMS->lang['add_new_discount']?></a>
            <a class="btn btn-warning" href="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache"?>"><?=$CMS->lang['clear_cache']?></a>
        </figure>

        <section class="search_adv" >
            <form method="get" id="formsearch_adv" style="<?=$tpl->quickSearchDisplay?>"  action="">
                <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
                <figure class="box-typical box-typical box-typical-padding border">
                    <h5><?=$CMS->lang['gsearch_advance']?></h5>
                    <ul class="input_li row match-height">
                        <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                            <label class="form-label pull-left" for="discount_name"><?=$CMS->lang['discount_name']?></label>
                            <input class="form-control" type="text" name="discount_name" id="discount_name" value="<?=$CMS->input['discount_name']?>" placeholder="<?=$CMS->lang['discount_name']?>">
                        </li>
                        <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                            <label class="form-label pull-left" for="discount_type"><?=$CMS->lang['discount_type']?></label>
                            <select class="form-control" name="discount_type">
                                <option value=""><?=$CMS->lang['discount_type']?></option>
                                <?=$tpl->type_options;?>
                            </select>
                        </li>
                        <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                            <label class="form-label pull-left" for="discount_status"><?=$CMS->lang['discount_status']?></label>
                            <select class="form-control" name="discount_status">
                                <option value=""><?=$CMS->lang['discount_status']?></option>
                                <?=$tpl->status_options;?>
                            </select>
                        </li>
                        <li class="col-xl-3 col-lg-3 col-sm-3 col-xs-12">
                            <label class="form-label pull-left" for="discount_start_time"><?="{$CMS->lang['discount_start_time']} ({$CMS->lang['from']})"?></label>
                            <input type="text" name="discount_start_time" class="form-control date-picker" value="<?=$CMS->input['discount_start_time']?>">
                        </li>
                        <li class="col-xl-3 col-lg-3 col-sm-3 col-xs-12">
                            <label class="form-label pull-left" for="discount_start_time_to"><?="{$CMS->lang['discount_start_time']} ({$CMS->lang['to']})"?></label>
                            <input type="text" name="discount_start_time_to" class="form-control date-picker" value="<?=$CMS->input['discount_start_time_to']?>">
                        </li>
                        <li class="col-xl-3 col-lg-3 col-sm-3 col-xs-12">
                            <label class="form-label pull-left" for="discount_end_time"><?="{$CMS->lang['discount_end_time']} ({$CMS->lang['from']})"?></label>
                            <input type="text" name="discount_end_time" class="form-control date-picker" value="<?=$CMS->input['discount_end_time']?>">
                        </li>
                        <li class="col-xl-3 col-lg-3 col-sm-3 col-xs-12">
                            <label class="form-label pull-left" for="discount_end_time_to"><?="{$CMS->lang['discount_end_time']} ({$CMS->lang['to']})"?></label>
                            <input type="text" name="discount_end_time_to" class="form-control date-picker" value="<?=$CMS->input['discount_end_time_to']?>">
                        </li>
                        <li class="col-xs-12">
                            <input type="submit" name="submit" id="submit" value="<?=$CMS->lang['filter']?>">
                            <a href="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>"><label class="btn btn-warning">Reset</label></a>
                        </li>
                    </ul>
                </figure>
            </form>
        </section>

        <section class="tabs-section" style="margin-bottom: 15px">
            <div class="tabs-section-nav tabs-section-nav-inline">
                <ul class="nav" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?=$CMS->vars['root_domain']?>/?site=discount">
                            <?=$CMS->lang['menu_discount']?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?=$CMS->vars['root_domain']?>/?site=discount_items">
                            <?=$CMS->lang['menu_discount_items']?>
                        </a>
                    </li>
                </ul>
            </div><!--.tabs-section-nav-->
        </section>

    <form method="post" name="form_discount" id="form_discount" action="">
        <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
        <input type="hidden" name="act" value="">
        <section class="add_table">
            <div class="data_table">
                <div class="table table_cus table-responsive">
                    <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
                        <thead>
                        <tr>
                            <th data-orderable="false" data-sortable="false" aria-label="" style="margin:0px;padding:0px;"></th>
                            <th data-sortable="false" data-orderable="false" aria-label="">
                                <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_discount');" id="checkall">
                                    <input type="checkbox" name="all" onmoudiscountver="on_mouse=0;" onmoudiscountut="on_mouse=1;">
                                    <label for="id_<?=$result['record_cnt']?>"></label>
                                </div>
                            </th>
                            <th width="20%" class="three-dots"><?=$CMS->lang['discount_name']?></th>
                            <th width="5%" class="three-dots"><?=$CMS->lang['di_code']?></th>
                            <th width="20%"><?=$CMS->lang['discount']?></th>
                            <th width="20%"><?=$CMS->lang['discount_status']?></th>
                            <th width="20%"><?=$CMS->lang['discount_time']?></th>
                            <th style="text-align:center" data-sortable="false" data-orderable="false"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? if($tpl->data) {?>
                            <?foreach($tpl->data as $result){ $result = \models\discount::convertValue($result)?>
                                <tr>
                                    <td style="margin:0px;padding:0px;<?=$_SESSION['is_mobile'] ? "padding-right:27px;" : ""?>"></td>
                                    <td>
                                        <div class="checkbox checkbox-only">
                                            <input type="checkbox"  name="id_<?=$result['record_cnt']?>" id="id_<?=$result['record_cnt']?>" value="<?=$result['discount_id']?>"/>
                                            <label for="id_<?=$result['record_cnt']?>"></label>
                                        </div>
                                    </td>
                                    <td class="mwr threedots"><?=$result['discount_name']?>
                                        <span class="btn-success btn-sm"><i class="fa fa-calendar" aria-hidden="true"></i> <?=$result['discount_period_time']?></span>
                                    </td>
                                    <td>

                                        <div class="btn-group">
                                            <button type="button" class="btn btn-inline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span id="discountCodeNums_<?=$result['discount_id']?>" style="display: inline-block"><?=$result['discount_num_items']?></span> <?=$CMS->lang['code']?>
                                            </button>
                                            <div class="dropdown-menu" style="right:inherit !important">
                                                <a class="dropdown-item" onclick="openAddItemsPopup(<?=$result['discount_id']?>)"><i class="fa fa-plus-square" aria-hidden="true"></i> <?=$CMS->lang['add_codes']?></a>
                                                <a class="dropdown-item" href="<?="{$CMS->vars['root_domain']}/?site=discount_items&discount_id={$result['data_bk']['discount_id']}";?>"><i class="fa fa-search" aria-hidden="true"></i> <?=$CMS->lang['search_codes']?></a>
                                                <a class="dropdown-item" onclick="delete_confirm('<?="{$CMS->vars['root_domain']}/?site=discount_items&act=delete&discount_id={$result['data_bk']['discount_id']}";?>');"><i class="fa fa-trash" aria-hidden="true"></i> <?=$CMS->lang['delete_codes']?></a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="threedots"><?=$result['discount_value']?></td>
                                    <td class="threedots"><?=$result['discount_status']?></td>
                                    <td class="threedots"><?=$result['discount_time']?></td>
                                    <td style="text-align:center">
                                        <? if($CMS->permit['discount_edit'] || $CMS->permit['discount_is_root']){?>
                                            <a href="<?=$CMS->vars['root_domain']?>/?site=discount&act=edit&id=<?=$result['data_bk']['discount_id']?>" title""="" class="edit"><i class="fa fa-edit"></i></a>
                                        <?}?>

                                        <? if($CMS->permit['discount_delete'] || $CMS->permit['discount_is_root']){?>
                                            <a onclick="delete_confirm('<?=$CMS->vars['root_domain']?>/?site=discount&act=delete&id=<?=$result['data_bk']['discount_id']?>');"   class="edit"><i class="fa fa-trash-o"></i></a>
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
        <input type="hidden" name="data_cnt" value="<?=\models\discount::$record_cnt?>">
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

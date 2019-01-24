<section class="add_table main_form">
    <figure class="heading">
        <div class="row">
            <div class="col-md-6 col-xs-12">
                <h3><?=$CMS->lang['title'];?></h3>
            </div>
            <div class="col-md-6 col-xs-12">
                <figure class="pull-right right">
                    <div class="search">
                        <form method="post" id="formquicksearch_adv"  action="<?=$CMS->vars['root_domain']?>/?site=custom_status&act=search_do" style="display:inline-block">
                            <input type="submit" class="fa-input" value="&#xf002;">
                            <input name="keyword" id="p_quick_search" type="text" value="<?=urldecode($CMS->input['keyword'])?>" autocomplete="off" minlength="2" maxlength="64" placeholder="<?=$CMS->lang['gsearch_quick']?>" style="position: :relative;">
                            <div id="suggesstion-box" class="box_result_find" style="display:none"></div>
                        </form>
                    </div>
                    <a href="<?=$CMS->vars['root_domain']?>/?site=custom_status&act=add" title="" class="add_bill"><?=$CMS->lang['add_new_custom_status']?></a>
                </figure>
            </div>
        </div>





    <form method="post" name="form_custom_status" id="form_custom_status" action="">
        <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
        <input type="hidden" name="act"  value="">
        <input type="hidden" name="subact" id="act" value="">

        <section class="add_table">
            <div class="data_table">
                <div class="table table_cus table-responsive" style="border-top: none;">
                    <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
                        <thead>
                        <tr>
                            <th data-orderable="false" data-sortable="false" aria-label="" style="margin:0px;padding:0px;"></th>
                            <th data-sortable="false" data-orderable="false" aria-label="">
                                <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_custom_status');" id="checkall">
                                    <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
                                    <label for="id_<?=$result['record_cnt']?>"></label>
                                </div>
                            </th>
                            <th><?=$CMS->lang['custom_status_name']?></th>
                            <th><?=$CMS->lang['title_status_order']?></th>
                            <th><?=$CMS->lang['custom_status_description']?></th>
                            <th><?=$CMS->lang['custom_status_display']?></th>
                            <th><?=$CMS->lang['custom_status_sort']?></th>
                            <th style="text-align:center" data-sortable="false" data-orderable="false"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? if($tpl->data) {?>
                            <?foreach($tpl->data as $result){ $result = \models\custom_status::convertValue($result);?>
                                <tr>
                                    <td style="margin:0px;padding:0px;<?=$_SESSION['is_mobile'] ? "padding-right:27px;" : ""?>"></td>
                                    <td>
                                        <div class="checkbox checkbox-only">
                                            <input type="checkbox"  name="id_<?=$result['record_cnt']?>" id="id_<?=$result['record_cnt']?>" value="<?=$result['status_id']?>"/>
                                            <label for="id_<?=$result['record_cnt']?>"></label>
                                        </div>
                                    </td>
                                    <td class="mwr"><?=$result['status_name']?></td>
                                    <td><?=$CMS->lang['order_status_'.$result['ord_status']]?></td>
                                    <td><?=$result['status_description']?></td>
                                    <td><?=$CMS->lang['custom_status_display_'.$result['status_display']];?></td>
                                    <td><?=$result['status_sort'];?></td>
                                    <td style="text-align:center">
                                        <? if($CMS->permit['custom_status_edit'] || $CMS->permit['custom_status_is_root']){?>
                                            <a href="<?=$CMS->vars['root_domain']?>/?site=custom_status&act=edit&id=<?=$result['data_bk']['status_id']?>" title""="" class="edit"><i class="fa fa-edit"></i></a>
                                        <?}?>

                                        <? if($CMS->permit['custom_status_delete'] || $CMS->permit['custom_status_is_root']){?>
                                            <a onclick="delete_confirm('<?=$CMS->vars['root_domain']?>/?site=custom_status&act=delete&id=<?=$result['data_bk']['status_id']?>');"   class="edit"><i class="fa fa-trash-o"></i></a>
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
                            <select class="form-control select2" name="subact" onchange="return submit_action_control(this,$(this).parents('form:first').attr('id'));" defaultvalue="delete_all" emsg="<?=$CMS->lang['title_please_choose_action'];?>" ehide="1">
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
        <input type="hidden" name="data_cnt" value="<?=\models\custom_status::$record_cnt?>">
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

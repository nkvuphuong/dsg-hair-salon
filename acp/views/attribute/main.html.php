<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['group_name'];?></h3>
        <figure class="pull-right right">
            <div class="search">
                <form method="post" id="formquicksearch_adv"  action="<?=$CMS->vars['root_domain']?>/?site=attribute&act=search_do" style="display:inline-block">
                    <input type="submit" class="fa-input" value="&#xf002;">
                    <input name="keyword" id="p_quick_search" type="text" value="<?=urldecode($CMS->input['keyword'])?>" autocomplete="off" minlength="2" maxlength="64" placeholder="<?=$CMS->lang['gsearch_quick']?>" style="position: :relative;">
                    <div id="suggesstion-box" class="box_result_find" style="display:none"></div>
                </form>
            </div>
            <a href="<?=$CMS->vars['root_domain']?>/?site=attribute&act=add" title="" class="add_bill"><?=$CMS->lang['add_new_attribute_group']?></a>
        </figure>


    <form method="post" name="form_attribute" id="form_attribute" action="">
        <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
        <input type="hidden" name="act" value="">
        <section class="add_table">
            <div class="data_table">
                <div class="table table_cus table-responsive" style="border-top: none;">
                    <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
                        <thead>
                        <tr>
                            <th data-orderable="false" data-sortable="false" aria-label="" style="margin:0px;padding:0px;"></th>
                            <th data-sortable="false" data-orderable="false" aria-label="">
                                <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_attribute');" id="checkall">
                                    <input type="checkbox" name="all" onmouattributever="on_mouse=0;" onmouattributeut="on_mouse=1;">
                                    <label for="id_<?=$result['record_cnt']?>"></label>
                                </div>
                            </th>
                            <th width="20%"><?=$CMS->lang['group_name']?></th>
                            <th width="25%"><?=$CMS->lang['group_description']?></th>
                            <th width="25%"><?=$CMS->lang['group_time']?></th>
                            <th width="25%"><?=$CMS->lang['group_status']?></th>
                            <th style="text-align:center" data-sortable="false" data-orderable="false"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? if($tpl->data) {?>
                            <?foreach($tpl->data as $result){ $result = \models\attribute::convertValue($result)?>
                                <tr>
                                    <td style="margin:0px;padding:0px;<?=$_SESSION['is_mobile'] ? "padding-right:27px;" : ""?>"></td>
                                    <td>
                                        <div class="checkbox checkbox-only">
                                            <input type="checkbox"  name="id_<?=$result['record_cnt']?>" id="id_<?=$result['record_cnt']?>" value="<?=$result['group_id']?>"/>
                                            <label for="id_<?=$result['record_cnt']?>"></label>
                                        </div>
                                    </td>
                                    <td width="20%" class="mwr threedots"><?=$result['group_name']?></td>
                                    <td width="25%" class="threedots"><?=$result['group_description']?></td>
                                    <td width="25%" class="threedots"><?=$result['group_time']?></td>
                                    <td width="25%" class="threedots"><?=$result['group_status']?></td>
                                    <td style="text-align:center">
                                        <? if($CMS->permit['attribute_edit'] || $CMS->permit['attribute_is_root']){?>
                                            <a href="<?=$CMS->vars['root_domain']?>/?site=attribute&act=edit&id=<?=$result['group_id']?>" title""="" class="edit"><i class="fa fa-edit"></i></a>
                                        <?}?>

                                        <? if($CMS->permit['attribute_delete'] || $CMS->permit['attribute_is_root']){?>
                                            <a onclick="delete_confirm('<?=$CMS->vars['root_domain']?>/?site=attribute&act=delete&id=<?=$result['group_id']?>');"   class="edit"><i class="fa fa-trash-o"></i></a>
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
                            <select class="form-control select2" name="subact" onchange="return submit_action_control(this,$(this).parents('form:first').attr('id'));" defaultvalue="delete_all" emsg="Please choose an action" ehide="1">
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
        <input type="hidden" name="data_cnt" value="<?=\models\attribute::$record_cnt?>">
    </form>
</section>
<script>
        $(function() {

            <? if(!$tpl->data){?>
            $('#example').DataTable({
                language: {
                    emptyTable: '<?=$CMS->lang['data_not_found'];?>'
                },
                // order: [[ 2,"desc"]],
                paging: false,
                searching: false,
                info: false,
                <?=$_SESSION['is_mobile'] ? "responsive: { details: true}," : "";?>
            });
            <?} ?>

//            autocomplete_quick_search();
        });
</script>

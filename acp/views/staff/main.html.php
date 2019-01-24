<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['title_staff'];?></h3>
        <figure class="pull-right right">
          <?if($CMS->permit['staff_add']) {?>
            <a href="<?=$CMS->vars['root_domain']?>/?site=staff&act=add" title="" class="add_bill"><?=$CMS->lang['add_staff'];?></a>
          <? } ?>
            <a class="btn btn-warning" href="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache"?>"><?=$CMS->lang['clear_cache']?></a>
        </figure>
    </figure>
    <form method="post" name="form_staff" id="form_staff" action="">
        <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
        <input type="hidden" name="act" value="">
        <section class="add_table">
            <div class="<?=$tpl->tableMobile['large_div'];?>">
                <div class="" style="border-top: none;">
                    <table id="<?=$tpl->tableMobile['id_table'];?>" class="table_cus <?=$tpl->tableMobile['class_table'];?>" cellspacing="0" width="100%" style="margin-top:0px;">
                        <thead>
                        <tr>
                            <?=$tpl->tableMobile['td_mobile'] == 1 ? '<th width="1%"></th>' : ""; ?>
                            <th><div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_staff');" id="checkall">
                                   <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
                                   <label></label>
                                </div>
                            </th>
                            <th style="text-align:center">#ID</th>
                            <th><?=$CMS->lang['staff_name']?></th>
                            <th><?=$CMS->lang['staff_image']?></th>
                            <th><?=$CMS->lang['staff_time']?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody class="box_list_staff">
                        <? if($tpl->data) { ?>
                            <?foreach($tpl->data as $result) {?>
                                <tr>
                                    <?=$tpl->tableMobile['td_mobile'] == 1 ? '<th width="1%"></th>' : "";?>
                                    <td>
                                        <div class="checkbox checkbox-only">
                                            <input type="checkbox" name="id_<?=$result['record_cnt'];?>" id="id_<?=$result['record_cnt'];?>" value="<?=$result['user_id'];?>"/>
                                            <label for="id_<?=$result['record_cnt'];?>"></label>
                                         </div>
                                    </td>
                                    <td style="text-align:center">#<?=$result['user_id'];?></td>
                                    <td><?=$result['user_display_name'];?></td>
                                    <td><img src="<?=$result['image'];?>" style="max-width: 200px; padding: 5px;"/></td>
                                    <td><?=!$result['user_time'] ? "N/A" : \lib\date::format($result['user_time'],"M d, Y g:i a");?></td>
                                    <td style="text-align:center">
                                        <? if($CMS->permit['staff_edit']){?>
                                            <a href="<?=$CMS->vars['root_domain'];?>/?site=staff&act=edit&id=<?=$result['user_id'];?>" class="edit"><i class="fa fa-edit"></i></a>
                                        <?}?>
                                        <? if($CMS->permit['staff_delete']) { ?>
                                            <a onclick="delete_confirm('<?=$CMS->vars['root_domain'];?>/?site=staff&act=delete&id=<?=$result['user_id'];?>');"   class="edit"><i class="fa fa-trash-o"></i></a>
                                        <? } ?>
                                    </td>
                                </tr>
                            <?}?>
                        <?} else {?>
                            <?= $tpl->tableMobile['script_mobile'] == 1 ? "" : "<tr><td colspan=\"8\">{$CMS->lang['no_data']}</td></tr>";?>
                        <?}?>
                        </tbody>
                    </table>
                </div>
                <div class="fuction_table">
                    <div class="pull-left">
                        <p class="form-control-static ">
                            <select class="form-control" name="act" onchange="return check_submit_form('<?=$CMS->lang['gnotice_confirm_action'];?>', 'form_staff', this);" defaultvalue="delete_all" emsg="<?=$CMS->lang['incomplete_action'];?>" ehide="1">
                                <option value=""><?=$CMS->lang['choose_action'];?></option>
                                <?=$tpl->control;?>
                            </select>
                        </p>
                    </div>
                    <nav class="pull-right">
                        <div class="block_bottom pagination pagination-sm"><?=$CMS->show_page;?></div>
                    </nav>
                </div>
            </div>
        </section>
        <input type="hidden" name="data_cnt" value="<?=\models\staff::$record_cnt;?>">
    </form>

</section>
<script src="<?=$CMS->vars['root_domain'];?>/jsacp/app_footer.js"></script>
<script>
    $(document).ready(function(){
        
        // validate_form_custom("#staff_form", "button[name='add_staff']");
        
        <? if($tpl->tableMobile['script_mobile'] == 1) { ?> 
        $('#example').DataTable({
          language: {
                  emptyTable: 'No data'
              },
             order: [],
          responsive: true,
            columnDefs: [
                { responsivePriority: 1, targets: 1 },
                { responsivePriority: 2, targets: 2 },
                { responsivePriority: 3, targets: 5 },
                { responsivePriority: 4, targets: -1 },
            ],
            paging: false,
              searching: false,
              info: false
          });

        <? } ?>

        
    });
</script>

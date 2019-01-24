<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['con_header'];?></h3>
        <figure class="pull-right right">
            <?=$CMS->global->headerQuickSearch("{$CMS->vars['root_domain']}/?site=contact&subact=autocomplete");?>
            <a class="btn btn-warning" href="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache"?>"><?=$CMS->lang['clear_cache']?></a>
        </figure>
        
        <!-- Search form -->
        <section class="search_adv" >
            <form method="get" id="formsearch_adv" style="<?=( $CMS->input['act'] == 'search_do' ) ? '' : 'display: none';?>" action="">
                <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
                <figure class="box-typical box-typical box-typical-padding border">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="with-border m-t-0"><?= $CMS->lang['con_info']; ?></h4>
                            <?=\core\ezy::render('inputs_search', 'contact');?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="submit" name="submit" id="submit" value="<?=$CMS->lang['filter']?>">
                            <a href="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>"><label class="btn btn-warning">Reset</label></a>
                        </div>
                    </div>
                </figure>
            </form>
        </section><!-- End search form -->
    </figure>

    <form method="post" name="form_contact" id="form_contact" action="">
        <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
        <input type="hidden" name="act" value="">

        <section class="add_table">
            <div class="data_table">
                <div class="table table-responsive table_cus">
                    <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
                        <thead>
                        <tr>
                            <th  data-orderable="false" data-sortable="false" aria-label="" style="margin:0px;padding:0px;"></th>
                            <th width="5%" data-sortable="false" data-orderable="false" aria-label="">
                                <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_contact');" id="checkall">
                                    <input type="checkbox" name="all" onmoudiscountver="on_mouse=0;" onmoudiscountut="on_mouse=1;" id="id_all">
                                    <label for="id_all"></label>
                                </div>
                            </th>
                            <th width="40%" class="three-dots"><?=$CMS->lang['con_subject']?></th>
                            <th width="15%" class="three-dots"><?=$CMS->lang['con_name']?></th>
                            <th width="20%" class="three-dots"><?=$CMS->lang['con_email']?></th>
                            <th width="10%" class="three-dots"><?=$CMS->lang['con_time']?></th>
                            <th width="5%" class="three-dots"><?=$CMS->lang['con_status']?></th>
                            <th width="5%" class="three-dots" style="text-align:center" data-sortable="false" data-orderable="false"></th>
                        </tr>
                        </thead>

                        <tbody>
                            <?
                            if( !empty($tpl->data) ){
                                foreach( $tpl->data as $result ){ 
                                    $result = \models\contact::convertValue($result);
                            ?>
                            <tr>
                                <td style="margin:0px;padding:0px;<?=$_SESSION['is_mobile'] ? "padding-right:27px;" : ""?>"></td>
                                <td>
                                    <div class="checkbox checkbox-only">
                                        <input type="checkbox"  name="id_<?=$result['record_cnt']?>" id="id_<?=$result['record_cnt']?>" value="<?=$result['con_id']?>"/>
                                        <label for="id_<?=$result['record_cnt']?>"></label>
                                    </div>
                                </td>
                                <td class="threedots"><?=$result['con_subject']?></td>
                                <td class="threedots"><?=$result['con_name']?></td>
                                <td class="threedots"><?=$result['con_email']?></td>
                                <td class="threedots"><?=$result['con_time']?></td>
                                <td class="threedots">
                                    <strong style="color: <?=$result['con_status_color']?>"><?=$result['con_status']?></strong>
                                </td>
                                <td style="text-align:center">
                                    <?if( $CMS->permit['contact_delete'] || $CMS->permit['contact_is_root']){?>
                                    <a onclick="delete_confirm('<?=$CMS->vars['root_domain']?>/?site=contact&act=delete&id=<?=$result['data_bk']['con_id']?>');" class="edit"><i class="fa fa-trash-o"></i></a>
                                    <?}?>
                                </td>
                            </tr>
                            <?}}?>
                        </tbody>
                    </table>
                </div>
                <div class="fuction_table">
                    <div class="pull-left">
                        <p class="form-control-static">
                            <select class="form-control select2" name="subact" onchange="return submit_action_control(this,$(this).parents('form:first').attr('id'));" defaultvalue="delete_all" emsg="Bạn phải chọn một Hành Động !" ehide="1">
                                <option value="">-- <?=$CMS->lang['choose_action'];?> --</option>
                                <?if( $CMS->permit['contact_delete'] || $CMS->permit['contact_is_root']){?>
                                <option value="delete_all"><?=$CMS->lang['act_delete'];?></option>
                                <?}?>
                            </select>
                        </p>
                    </div>
                    <nav class="pull-right">
                        <div class="block_bottom pagination pagination-sm"><?=$CMS->show_page;?></div>
                    </nav>
                </div>
            </div>
        </section>
        <input type="hidden" name="data_cnt" value="<?=\models\contact::$record_cnt;?>">
    </form>
</section>
<script>
    $(function() {
        <?if( empty($tpl->data) ){?>
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
        <?}?>
    });
</script>
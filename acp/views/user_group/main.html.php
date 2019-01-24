<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['title'];?></h3>
        <figure class="pull-right right">
           <!--
            <div class="search">
                <form method="post" id="formquicksearch_adv"  action="<?/*=$CMS->vars['root_domain']*/?>/?site=user_group&act=search_do" style="display:inline-block">
                    <input type="submit" class="fa-input" value="&#xf002;">
                    <input name="keyword" id="p_quick_search" type="text" value="<?/*=urldecode($CMS->input['keyword'])*/?>" autocomplete="off" minlength="2" maxlength="64" placeholder="<?/*=$CMS->lang['gsearch_quick']*/?>" style="position: :relative;">
                    <div id="suggesstion-box" class="box_result_find" style="display:none"></div>
                </form>
                <a id="expand_formsearch" title=""><?/*=$CMS->lang['gsearch_advance']*/?><i class="fa fa-angle-double-right"></i></a>
            </div>
            -->
            <a href="<?=$CMS->vars['root_domain']?>/?site=user_group&act=add" title="" class="add_bill"><?=$CMS->lang['group_add']?></a>
            <a class="btn btn-warning" href="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache"?>"><?=$CMS->lang['clear_cache']?></a>
        </figure>

        <section class="tabs-section" style="margin-bottom: 15px">
            <div class="tabs-section-nav tabs-section-nav-inline">
                <ul class="nav" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" href="<?=$CMS->vars['root_domain']?>/?site=user">
                            <?=$CMS->lang['menu_user']?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?=$CMS->vars['root_domain']?>/?site=user_group">
                            <?=$CMS->lang['menu_user_group']?>
                        </a>
                    </li>
                </ul>
            </div><!--.tabs-section-nav-->
        </section>

        <section class="search_adv" >
            <form method="get" id="formsearch_adv" style="<?=$tpl->quickSearchDisplay?>"  action="">
                <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
                <input type="hidden" name="act" value="search_do">
                <figure class="box-typical box-typical box-typical-padding border">
                    <h5><?=$CMS->lang['gsearch_advance']?></h5>
                    <ul class="input_li row match-height">
                        <li class="col-xl-3 col-lg-3 col-sm-3 col-xs-12">
                            <label class="form-label pull-left" for="pos_name"><?=$CMS->lang['pos_name']?></label>
                            <input class="form-control" type="text" name="pos_name" id="pos_name" value="<?=urldecode($CMS->input['pos_name'])?>" placeholder="<?=$CMS->lang['pos_name']?>">
                        </li>
                        <li class="col-xl-3 col-lg-3 col-sm-3 col-xs-12">
                            <label class="form-label pull-left" for="pos_key"><?=$CMS->lang['pos_key']?></label>
                            <input class="form-control" type="text" name="pos_key" id="pos_key" value="<?=urldecode($CMS->input['pos_key'])?>" placeholder="<?=$CMS->lang['pos_key']?>">
                        </li>
                        <li class="col-xs-12">
                            <input type="submit" name="submit" id="submit" value="<?=$CMS->lang['filter']?>">
                        </li>
                    </ul>
                </figure>
            </form>
        </section>

    <form method="post" name="form_user_group" id="form_user_group" action="">
        <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
        <input type="hidden" name="act" value="">
        <section class="add_table">
            <div class="data_table">
                <div class="table table_cus table-responsive" style="border-top: none;">
                    <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
                        <thead>
                        <tr>
                            <th data-orderable="false" data-sortable="false" aria-label="" style="margin:0px;padding:0px;"></th>
                            <th  width="5%" data-sortable="false" data-orderable="false" aria-label="">
                                <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_user_group');" id="checkall">
                                    <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
                                    <label for="id_<?=$result['record_cnt']?>"></label>
                                </div>
                            </th>
                            <th><?=$CMS->lang['group_name']?></th>
                            <th><?=$CMS->lang['group_is_root']?></th>
                            <th><?=$CMS->lang['group_is_admin']?></th>
                            <th><?=$CMS->lang['group_users']?></th>
                            <th style="text-align:center" data-sortable="false" data-orderable="false"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? if($tpl->data) {?>
                            <?foreach($tpl->data as $result){ $result = \models\user_group::convertValue($result);?>
                                <tr>
                                    <td style="margin:0px;padding:0px;<?=$_SESSION['is_mobile'] ? "padding-right:27px;" : ""?>"></td>
                                    <td>
                                        <div class="checkbox checkbox-only">
                                            <input type="checkbox"  name="id_<?=\models\user_group::$record_cnt?>" id="id_<?=\models\user_group::$record_cnt?>" value="<?=$result['userg_id']?>"/>
                                            <label for="id_<?=\models\user_group::$record_cnt?>"></label>
                                        </div>
                                    </td>
                                    <td class="mwr"><?=$result['userg_title']?></td>
                                    <td><?=$result['userg_is_root']?></td>
                                    <td><?=$result['userg_is_admin']?></td>
                                    <td><?=$result['count_user']?></td>
                                    <td style="text-align:center">
                                        <? if($CMS->permit['user_group_edit'] || $CMS->permit['user_group_is_root']){?>
                                            <a href="<?=$CMS->vars['root_domain']?>/?site=user_group&act=edit&id=<?=$result['data_bk']['userg_id']?>" title""="" class="edit"><i class="fa fa-edit"></i></a>
                                        <?}?>

                                        <? if($CMS->permit['user_group_delete'] || $CMS->permit['user_group_is_root']){?>
                                            <a onclick="delete_confirm('<?=$CMS->vars['root_domain']?>/?site=user_group&act=delete&id=<?=$result['data_bk']['userg_id']?>');"   class="edit"><i class="fa fa-trash-o"></i></a>
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
        <input type="hidden" name="data_cnt" value="<?=\models\user_group::$record_cnt?>">
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

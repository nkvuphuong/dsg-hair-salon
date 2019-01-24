<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['title'];?></h3>
        <figure class="pull-right right">
            <div class="search">
                <form method="post" id="formquicksearch_adv"  action="<?=$CMS->vars['root_domain']?>/?site=seo&act=search_do" style="display:inline-block">
                    <input type="submit" class="fa-input" value="&#xf002;">
                    <input name="keyword" id="p_quick_search" type="text" value="<?=urldecode($CMS->input['keyword'])?>" autocomplete="off" minlength="2" maxlength="64" placeholder="<?=$CMS->lang['gsearch_quick']?>" style="position: :relative;">
                    <div id="suggesstion-box" class="box_result_find" style="display:none"></div>
                </form>
                <a id="expand_formsearch" title=""><?=$CMS->lang['gsearch_advance']?><i class="fa fa-angle-double-right"></i></a>
            </div>
            <a href="<?=$CMS->vars['root_domain']?>/?site=seo&act=add" title="" class="add_bill"><?=$CMS->lang['add_new_seo']?></a>
        </figure>

        <section class="search_adv" >
            <form method="get" id="formsearch_adv" style="<?=$tpl->quickSearchDisplay?>"  action="">
                <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
                <input type="hidden" name="act" value="search_do">
                <figure class="box-typical box-typical box-typical-padding border">
                    <h5><?=$CMS->lang['gsearch_advance']?></h5>
                    <ul class="input_li row match-height">
                        <li class="col-xl-3 col-lg-3 col-sm-3 col-xs-12">
                            <label class="form-label pull-left" for="seo_url"><?=$CMS->lang['seo_url']?></label>
                            <input class="form-control" type="text" name="seo_url" id="seo_url" value="<?=urldecode($CMS->input['seo_url'])?>" placeholder="<?=$CMS->lang['seo_url']?>">
                        </li>
                        <li class="col-xl-3 col-lg-3 col-sm-3 col-xs-12">
                            <label class="form-label pull-left" for="seo_title"><?=$CMS->lang['seo_title']?></label>
                            <input class="form-control" type="text" name="seo_title" id="seo_title" value="<?=urldecode($CMS->input['seo_title'])?>" placeholder="<?=$CMS->lang['seo_title']?>">
                        </li>
                        <li class="col-xl-3 col-lg-3 col-sm-3 col-xs-12">
                            <label class="form-label pull-left" for="seo_keywords"><?=$CMS->lang['seo_keywords']?></label>
                            <input class="form-control" type="text" name="seo_keywords" id="seo_keywords" value="<?=urldecode($CMS->input['seo_keywords'])?>" placeholder="<?=$CMS->lang['seo_keywords']?>">
                        </li>
                        <li class="col-xl-3 col-lg-3 col-sm-3 col-xs-12">
                            <label class="form-label pull-left" for="seo_description"><?=$CMS->lang['seo_description']?></label>
                            <input class="form-control" type="text" name="seo_description" id="seo_description" value="<?=urldecode($CMS->input['seo_description'])?>" placeholder="<?=$CMS->lang['seo_description']?>">
                        </li>
                        <li class="col-xs-12">
                            <input type="submit" name="submit" id="submit" value="<?=$CMS->lang['filter']?>">
                        </li>
                    </ul>
                </figure>
            </form>
        </section>

    <form method="post" name="form_seo" id="form_seo" action="">
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
                                <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_seo');" id="checkall">
                                    <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
                                    <label for="id_<?=$result['record_cnt']?>"></label>
                                </div>
                            </th>
                            <th width="20%" class="three-dots"><?=$CMS->lang['seo_url']?></th>
                            <th width="25%"><?=$CMS->lang['seo_title']?></th>
                            <th width="25%"><?=$CMS->lang['seo_keywords']?></th>
                            <th width="25%"><?=$CMS->lang['seo_description']?></th>
                            <th style="text-align:center" data-sortable="false" data-orderable="false"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? if($tpl->data) {?>
                            <?foreach($tpl->data as $result){ $result = \models\seo::convertValue($result)?>
                                <tr>
                                    <td style="margin:0px;padding:0px;<?=$_SESSION['is_mobile'] ? "padding-right:27px;" : ""?>"></td>
                                    <td>
                                        <div class="checkbox checkbox-only">
                                            <input type="checkbox"  name="id_<?=$result['record_cnt']?>" id="id_<?=$result['record_cnt']?>" value="<?=$result['seo_id']?>"/>
                                            <label for="id_<?=$result['record_cnt']?>"></label>
                                        </div>
                                    </td>
                                    <td width="20%" class="mwr threedots"><?=$result['seo_url']?></td>
                                    <td width="25%" class="threedots"><?=$result['seo_title']?></td>
                                    <td width="25%" class="threedots"><?=$result['seo_keywords']?></td>
                                    <td width="25%" class="threedots"><?=$result['seo_description']?></td>
                                    <td style="text-align:center">
                                        <? if($CMS->permit['seo_edit'] || $CMS->permit['seo_is_root']){?>
                                            <a href="<?=$CMS->vars['root_domain']?>/?site=seo&act=edit&id=<?=$result['data_bk']['seo_id']?>" title""="" class="edit"><i class="fa fa-edit"></i></a>
                                        <?}?>

                                        <? if($CMS->permit['seo_delete'] || $CMS->permit['seo_is_root']){?>
                                            <a onclick="delete_confirm('<?=$CMS->vars['root_domain']?>/?site=seo&act=delete&id=<?=$result['data_bk']['seo_id']?>');"   class="edit"><i class="fa fa-trash-o"></i></a>
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
        <input type="hidden" name="data_cnt" value="<?=\models\seo::$record_cnt?>">
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

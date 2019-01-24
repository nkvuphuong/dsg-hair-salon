<script type="text/javascript" src="<?=$CMS->vars['js_url']?>/acp_user.js"></script>
<form enctype="multipart/form-data" method="post" id="form_<?=$CMS->input['site']?>" action="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>&act=<?=$tpl->act?><?=$CMS->input['id'] ? "&id={$CMS->input['id']}" : "";?>" onsubmit="return check_form(this.id);">

    <section class="add_form main_form">
        <figure class="heading">
            <h3><?=$tpl->header_title?></h3>
            <a href="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>" title=""><span class="font-icon font-icon-del"></span></a>
        </figure>

        <figure class="box-typical box-typical box-typical-padding border">
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group row">
                        <label class="form-control-label"><?=$CMS->lang['group_name']?><font style="margin-left:5px" color="#FF0000">(*)</font></label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <input class="form-control" type="text" name="userg_title" value="<?=$tpl->data['userg_title']?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_name']?>">
                        </div>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input name="userg_is_root" class="form-check-input" type="checkbox" value="1" <?=$tpl->isRootChecked?> >
                            <?=$CMS->lang['group_is_root']?>
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input name="userg_is_admin" class="form-check-input" type="checkbox" value="1" <?=$tpl->isAdminChecked?> >
                            <?=$CMS->lang['group_is_admin']?>
                        </label>
                    </div>
                </div> <!--End col-lg-6-->
                <div class="col-lg-9">
                    <section class="tabs-section">
                        <div class="tabs-section-nav tabs-section-nav-inline">
                            <ul class="nav" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#tab_permission" role="tab" data-toggle="tab" aria-expanded="true">
                                        Permission
                                    </a>
                                </li>
                                <? if($CMS->vars['enabled_commission']){ ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#tab_commission" role="tab" data-toggle="tab" aria-expanded="false">
                                            Commission
                                        </a>
                                    </li>
                                <? } ?>
                            </ul>
                        </div><!--.tabs-section-nav-->

                        <div class="tab-content">
                            <!-- CONFIG PERMISSION -->
                            <div role="tabpanel" class="tab-pane fade in active show" id="tab_permission" aria-expanded="true">
                                <div class="form-group row">
                                    <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                        <a class="btn btn-success" onclick="setAllPermission();"><?=$CMS->lang['full_permission']?></a>
                                        <a class="btn btn-success" onclick="unsetAllPermission();"><?=$CMS->lang['clear_permission']?></a>
                                        <a class="btn btn-success" onclick="revertPermission();"><?=$CMS->lang['revert_permission']?></a>
                                        <?=$CMS->user->show_permission($tpl->data['userg_permission'])?>
                                    </div>
                                </div>
                            </div>
                            <!-- END - CONFIG PERMISSION -->
                            <!-- CONFIG COMMISSION -->
                            <div role="tabpanel" class="tab-pane fade in" id="tab_commission" aria-expanded="true">
                                <?=$CMS->global->show_commission($tpl->commissionData,$_POST);?>
                            </div>
                            <!-- END - CONFIG COMMISSION -->
                        </div>
                    </section>
                </div>
            </div>
        </figure>
    </section>

    <section class="add_cart_footer">
            <?=$CMS->global->footer_back(['list' => "{$CMS->vars['root_domain']}/?site={$CMS->input['site']}"])?>
            <?= $CMS->input['act'] == 'add' || $CMS->input['act'] == 'add_do' ? $CMS->global->footer_save("{$CMS->input['site']}") : $CMS->global->footer_edit()?>
    </section>
    <input type="hidden" name="apply_permission" value="0">
    <input type="hidden" name="apply_commission" value="0">

</form>
<script language="javascript">

    function applyOption(mainObj, applyObj)
    {
        let value = mainObj.is(':checked') ? 1 : 0;
        console.log(value);
        applyObj.val(value);
    }

    $(document).ready(function () {
        validate_form_custom("#form_<?=$CMS->input['site']?>", "a.act_submit_save, a.act_submit_save, a.act_submit_save, [type='submit']");

        $('.act_submit_save').click(function(e){
            e.preventDefault();

            let html = `
            <p><strong>Bạn có muốn áp dụng các thay đổi cho tất cả các thành viên trong nhóm?</strong></p>
            <div class="row" style='color: rgb(48, 133, 214)'>
                <div class='col-md-6'><label><input type="checkbox" onchange="applyOption($(this), $('[name=apply_permission]'))"> Phân quyền</label></div>
                <div class='col-md-6'><label><input type="checkbox" onchange="applyOption($(this), $('[name=apply_commission]'))"> Huê hồng</label></div>
            </div>
            `;

            swal({
                title: 'Xác nhận',
                type: 'info',
                html: html,
                showCloseButton: true,
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                $("#form_<?=$CMS->input['site']?>").submit();
            }).catch(swal.noop)
        })

    });
</script>
<link rel="stylesheet" href="/acp/assets/js/lib/tinymce/plugins/codemirror/codemirror-4.8/lib/codemirror.css">
<link rel="stylesheet" href="/acp/assets/js/lib/tinymce/plugins/codemirror/codemirror-4.8/theme/<?=$tpl->mirror_theme?>.css">
<link rel="stylesheet" href="/acp/assets/js/lib/tinymce/plugins/codemirror/codemirror-4.8/addon/hint/show-hint.css">
<link rel="stylesheet" href="/acp/assets/js/lib/tinymce/plugins/codemirror/codemirror-4.8/addon/display/fullscreen.css">
<script src="/acp/assets/js/lib/tinymce/plugins/codemirror/codemirror-4.8/lib/codemirror.js"></script>
<script src="/acp/assets/js/lib/tinymce/plugins/codemirror/codemirror-4.8/addon/edit/matchbrackets.js"></script>
<script src="/acp/assets/js/lib/tinymce/plugins/codemirror/codemirror-4.8/addon/edit/closebrackets.js"></script>
<script src="/acp/assets/js/lib/tinymce/plugins/codemirror/codemirror-4.8/addon/search/searchcursor.js"></script>
<script src="/acp/assets/js/lib/tinymce/plugins/codemirror/codemirror-4.8/addon/search/search.js"></script>
<script src="/acp/assets/js/lib/tinymce/plugins/codemirror/codemirror-4.8/addon/hint/show-hint.js"></script>
<script src="/acp/assets/js/lib/tinymce/plugins/codemirror/codemirror-4.8/addon/hint/javascript-hint.js"></script>
<script src="/acp/assets/js/lib/tinymce/plugins/codemirror/codemirror-4.8/addon/hint/css-hint.js"></script>
<script src="/acp/assets/js/lib/tinymce/plugins/codemirror/codemirror-4.8/mode/javascript/javascript.js"></script>
<script src="/acp/assets/js/lib/tinymce/plugins/codemirror/codemirror-4.8/mode/css/css.js"></script>
<script src="/acp/assets/js/lib/tinymce/plugins/codemirror/codemirror-4.8/addon/selection/active-line.js"></script>
<script src="/acp/assets/js/lib/tinymce/plugins/codemirror/codemirror-4.8/addon/display/fullscreen.js"></script>

<script src="/acp/javascript/acp_embled.js"></script>

<form enctype="multipart/form-data" method="post" id="form_<?=$CMS->input['site']?>" action="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>&act=<?=$tpl->act?><?=$CMS->input['id'] ? "&id={$CMS->input['id']}" : "";?>" onsubmit="return check_form(this.id);">
    <section class="add_form main_form">
        <figure class="heading">
            <h3><?=$tpl->header_title?></h3>
            <a href="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>" title=""><span class="font-icon font-icon-del"></span></a>
        </figure>
        <figure class="box-typical box-typical-padding border col-md-10" style="display: inline-block;">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="with-border m-t-0"><?=$CMS->lang['embed_information']?></h4>
                    <div class="row match-height">
                        <div class="col-md-4">
                            <fieldset class="form-group">
                                <label class="form-label"><?=$CMS->lang['embed_name']?> <span style="color:red">(*)</span></label>
                                <input class="form-control " type="text" name="embed_name" id="embed_name" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_name']?>" value="<?=\lib\input::arrayValue($tpl->data, 'embed_name')?>">
                            </fieldset>
                        </div>
                        <div class="col-md-3">
                            <fieldset class="form-group">
                                <label class="form-label"><?=$CMS->lang['embed_code']?> <span style="color:red">(*)</span></label>
                                <input class="form-control " type="text" name="embed_code" id="embed_code" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_code']?>" value="<?=\lib\input::arrayValue($tpl->data, 'embed_code')?>">
                            </fieldset>
                        </div>
                        <div class="col-md-2">
                            <fieldset class="form-group">
                                <label class="form-label"><?=$CMS->lang['embed_interval']?></label>
                                <div class="input-group">
                                    <input class="form-control " type="number" min="0" name="embed_interval" id="embed_interval" value="<?=$tpl->data['embed_interval']?>">
                                    <span class="input-group-addon"><?=$CMS->lang['minute']?></span>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row match-height">
                        <div class="col-md-3">
                            <fieldset class="form-group">
                                <label class="form-label"><?=$CMS->lang['embed_start_date']?></label>
                                <div class="input-group">
                                    <input class="form-control date-picker" type="text" name="embed_start_date" id="embed_start_date" value="<?= \lib\input::arrayValue($tpl->data, 'embed_start_date')?>">
                                    <span class="input-group-addon">
                                    <i class="font-icon font-icon-calend"></i>
                                </span>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-md-3">
                            <fieldset class="form-group">
                                <label class="form-label"><?=$CMS->lang['embed_end_date']?></label>
                                <div class="input-group">
                                    <input class="form-control date-picker" type="text" name="embed_end_date" id="embed_end_date" value="<?=\lib\input::arrayValue($tpl->data, 'embed_end_date')?>">
                                    <span class="input-group-addon">
                                    <i class="font-icon font-icon-calend"></i>
                                </span>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-md-3">
                            <fieldset class="form-group">
                                <label class="form-label"><?=$CMS->lang['embed_status']?></label>
                                <select class="form-control" name="embed_status" id="embed_status">
                                    <?=$tpl->status_options;?>
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-md-3">
                            <fieldset class="form-group">
                                <label class="form-label">Sample codes</label>
                                <select class="form-control" onchange="chooseSampleEmbled($(this).val())" name="sample_code" id="sample_code">
                                    <option value="">---</option>
                                    <option value="magnific_popup">Magnific Popup</option>
                                    <option value="magnific_popup_first_closed_then_open_second">Magnific Popup first closed then open second</option>
                                    <option value="magnific_popup_first_closed_then_open_second_5_times">Magnific Popup first closed then open second ... 5 times</option>
                                    <option value="subcribe_form">Subscribe/Signup to receive coupon</option>
                                    <option value="instagram_v1">Instagram</option>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label"><?=$CMS->lang['embed_css']?> (Press F11 to fullscreen)</label>
                            <textarea name="embed_css" id="embed_css" class="form-control" rows="10"><?=\lib\input::arrayValue($tpl->data, 'embed_css')?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?=$CMS->lang['embed_js']?> (Press F11 to fullscreen)</label>
                            <textarea name="embed_js" id="embed_js" class="form-control" rows="10"><?=\lib\input::arrayValue($tpl->data, 'embed_js')?></textarea>
                        </div>
                        <div class="col-md-12" style="margin-top: 15px">
                            <label class="form-label"><?=$CMS->lang['embed_html']?></label>
                            <textarea name="embed_html" id="embed_html" class="form-control editor_texarea"><?=\lib\input::arrayValue($tpl->data, 'embed_html')?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </figure>
    </section>
    <section class="add_cart_footer">
            <?=$CMS->global->footer_back(['list' => "{$CMS->vars['root_domain']}/?site={$CMS->input['site']}"])?>
            <?= $CMS->input['act'] == 'add' || $CMS->input['act'] == 'add_do' ? $CMS->global->footer_save("{$CMS->input['site']}") : $CMS->global->footer_edit()?>
    </section>

</form>
<script language="javascript">
    $(document).ready(function () {
        validate_form_custom("#form_<?=$CMS->input['site']?>", "a.act_submit_save, a.act_submit_save, a.act_submit_save, [type='submit']");

        embedMirrorJS = codeMirrorTextAreaCustom('embed_js', 'javascript', '<?=$tpl->mirror_theme?>');
        embedMirrorCSS = codeMirrorTextAreaCustom('embed_css', 'text/x-scss', '<?=$tpl->mirror_theme?>');
    });
</script>
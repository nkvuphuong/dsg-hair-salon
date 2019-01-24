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

<style>
    .CodeMirror {
        border: 1px solid #eee;
        height: auto;
    }
</style>

<section class="add_form main_form">
    <figure class="heading">
        <h3><?= $CMS->lang['menu_embed']; ?>: <?= $tpl->data['embed_name']; ?></h3>
        <a href="<?= $CMS->vars['root_domain']; ?>/?site=embed<?= $CMS->class->search->url_return; ?>" title=""><span
                    class="font-icon font-icon-del"></span></a>
    </figure>
    <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
            <div class="col-xl-8 col-md-6 col-sm-12 col-xs-12">
                <h4 class="with-border m-t-0"><?=$CMS->lang['embed_information'];?></h4>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['embed_name']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['embed_name']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['embed_interval']; ?> (<?= $CMS->lang['minute']; ?>)</label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['embed_interval']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['embed_code']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold">{{embed.<?= $tpl->data['embed_code']; ?>}}</div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['embed_start_date']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['embed_start_date']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['embed_end_date']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['embed_end_date']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['embed_css']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold codemirror"><?= $tpl->data['embed_css']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['embed_js']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold codemirror"><?= $tpl->data['embed_js']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['embed_html']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold codemirror"><?= $tpl->data['embed_html']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['embed_status']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold" style="color: <?=$CMS->lang['embed_status_color_'.$tpl->data['data_bk']['embed_status']]?>"><?= $tpl->data['embed_status']; ?></div>
                    </div>
                </fieldset>
            </div>
            <div class="col-xl-4 col-md-6 col-sm-12 col-xs-12">
                <h4 class="with-border m-t-0"><?=$CMS->lang['other_information'];?></h4>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['user_id']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['user_id']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['embed_time']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['embed_time']; ?></div>
                    </div>
                </fieldset>
                <fieldset class="form-group row">
                    <label class="col-xl-4 form-control-label2"><?= $CMS->lang['embed_update_update']; ?></label>
                    <div class="col-xl-8 form-control-span2">
                        <div class="form-label semibold"><?= $tpl->data['embed_update_time']; ?></div>
                    </div>
                </fieldset>
            </div>
        </div>
    </figure>
</section>


<section class="add_cart_footer">
    <?php if ($_SESSION['is_mobile'] == true) { ?>

        <div class="btn-group dropup pull-left hidden-xl-up">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-mail-reply"></i><?= $CMS->lang['gaction']; ?>
            </button>
            <div class="dropdown-menu">
                <ul>
                    <li>
                        <a onclick="delete_confirm('<?= $CMS->vars['root_domain']; ?>/?site=embed&act=delete&id=<?= $tpl->data['embed_id']; ?>');" title=""><i class="fa  fa-mail-reply"></i><?= $CMS->lang['delete']; ?></a></li>
                    <li>
                        <a href="<?= $CMS->vars['root_domain']; ?>/?site=embed&act=edit&id=<?= $tpl->data['embed_id']; ?>"><i class="fa fa-file-text-o"></i><?= $CMS->lang['edit']; ?></a></li>
                </ul>
            </div>
        </div>

    <?php } else { ?>
        <a onclick="delete_confirm('<?= $CMS->vars['root_domain']; ?>/?site=embed&act=delete&id=<?= $tpl->data['embed_id']; ?>');"
           class="btn btn-inline btn-primary ladda-button pull-right add_cart"><?= $CMS->lang['delete']; ?></a>
        <a href="<?= $CMS->vars['root_domain']; ?>/?site=embed&act=edit&id=<?= $tpl->data['embed_id']; ?>"
           class="btn btn-inline btn-primary ladda-button pull-right add_cart"><?= $CMS->lang['edit']; ?></a>
    <? } ?>
</section>

<?= $tpl->comment; ?>
<?= $tpl->logs; ?>

<script>
    $('.codemirror').each(function() {
        var $this = $(this),
            $code = $this.html();
        $this.empty();
        var myCodeMirror = CodeMirror(this, {
            value: $code,
            mode: 'javascript',
            readOnly: true,
        });

    });
</script>

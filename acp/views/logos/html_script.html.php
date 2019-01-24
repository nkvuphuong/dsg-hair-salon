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
        <h3><?= $CMS->lang['menu_logos_html_script']; ?>: #<?= $tpl->data['logo_name']; ?></h3>
        <a href="<?= $CMS->vars['root_domain']; ?>/?site=logos<?= $CMS->class->search->url_return; ?>" title=""><span class="font-icon font-icon-del"></span></a>
    </figure>
    <figure class="box-typical box-typical box-typical-padding border">
        <h4 class="with-border m-t-0"><?=$CMS->lang['logos_html_script_information'];?></h4>
        <fieldset class="form-group row">
            <div class="form-label semibold codemirror">
                <!-- Advertisement -->
                <div class="clearfix" id="wad_<?=$tpl->data['logo_id'];?>" style="display:block;width:100%;height:100%;overflow: hidden;">
                    <section id="sad_<?=$tpl->data['logo_id'];?>" style="display:table;width:100%;height:100%;">
                        <iframe style="display:table-cell;width: 100%;height:100%;" name="iad_<?=$tpl->data['logo_id'];?>" id="iad_<?=$tpl->data['logo_id'];?>" src="<?=$tpl->data['logo_src'];?>" allowfullscreen="no" marginwidth="0" allowtransparency="true" marginheight="0" hspace="0" vspace="0" scrolling="no" frameborder="0"></iframe>
                    </section>
                    <script>
                    $(document).ready(function() {
                        function init_wad_<?=$tpl->data['logo_id'];?>() {setTimeout(function(){$("#wad_<?=$tpl->data['logo_id'];?>").height($("#iad_<?=$tpl->data['logo_id'];?>").contents().find("body").height()+"px");}, 250);}
                        $(window).on('resize', function(){clearTimeout(window.resizedFinished_init_wad_<?=$tpl->data['logo_id'];?>);window.resizedFinished_init_wad_<?=$tpl->data['logo_id'];?> = setTimeout(function(){init_wad_<?=$tpl->data['logo_id'];?>();}, 250);
                        });
                        $(window).on('load', function(){$(window).trigger('resize');});
                    });
                    </script>
                </div><!-- End advertisement -->

            </div>
        </fieldset>
    </figure>
</section>

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

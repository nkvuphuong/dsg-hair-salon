<div class="page_color">
    <center style="max-width: 800px">

    </center>
    <form action="<?=$CMS->vars['root_domain'];?>/?site=config_general&act=update_color" method="post">

        <div class="row">
            <div class="col-xl-12"><?=$CMS->vars['web_free_title_color_1'];?></div>
            <div class="col-xl-6">
                <fieldset class="form-group">
                    <input id="inpt_color_1" type="text" class="colorChange_1 form-control" name="free_theme_color_1" value="<?=$CMS->vars['free_theme_color_1'];?>" autocomplete="off">
                    <label id="color_1"  for="inpt_color_1" class="colorSelector"><div style="background-color: <?=$CMS->vars['free_theme_color_1'];?>"></div></label>
                </fieldset>
            </div>

        </div>

        <div class="row">
            <div class="col-xl-12"><?=$CMS->vars['web_free_title_color_4'];?></div>
            <div class="col-xl-6">
                <fieldset class="form-group">
                    <input id="inpt_color_4" type="text" class="colorChange_4 form-control" name="free_theme_color_4" value="<?=$CMS->vars['free_theme_color_4'];?>" autocomplete="off">
                    <label id="color_4"  for="inpt_color_4" class="colorSelector"><div style="background-color: <?=$CMS->vars['free_theme_color_4'];?>"></div></label>
                </fieldset>
            </div>

        </div>


        <div class="row">
            <div class="col-xl-12"><?=$CMS->vars['web_free_title_color_2'];?></div>
            <div class="col-xl-6">
                <fieldset class="form-group">
                    <input id="inpt_color_2" type="text" class="colorChange_2 form-control" name="free_theme_color_2" value="<?=$CMS->vars['free_theme_color_2'];?>" autocomplete="off">
                    <label id="color_2"  for="inpt_color_2" class="colorSelector"><div style="background-color: <?=$CMS->vars['free_theme_color_2'];?>"></div></label>
                </fieldset>
            </div>

        </div>
        <div class="row">
            <div class="col-xl-12"><?=$CMS->vars['web_free_title_color_3'];?></div>
            <div class="col-xl-6">
                <fieldset class="form-group">
                    <input id="inpt_color_3" type="text" class="colorChange_3 form-control" name="free_theme_color_3" value="<?=$CMS->vars['free_theme_color_3'];?>" autocomplete="off">
                    <label id="color_3" for="inpt_color_3" class="colorSelector"><div style="background-color: <?=$CMS->vars['free_theme_color_3'];?>"></div></label>
                </fieldset>
            </div>
        </div>





    <section class="add_cart_footer">
        <button class="btn btn-inline btn-primary pull-right" >Save</button>
    </section>
</div>
</form>
<script type="text/javascript">
    $(document).ready(function() {
        $(".colorChange_1").ColorPicker({
            color: '#0000ff',
            onShow: function (colpkr) {
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function (colpkr) {
                $(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                $('#color_1 div').css('backgroundColor', '#' + hex);
                $(".colorChange_1").val('#' + hex);
            },onSubmit: function(hsb, hex, rgb, el) {
                $(el).val('#' + hex);
                $(el).ColorPickerHide();
            },
        });
        $(".colorChange_2").ColorPicker({
            color: '#0000ff',
            onShow: function (colpkr) {
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function (colpkr) {
                $(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                $('#color_2 div').css('backgroundColor', '#' + hex);
                $(".colorChange_2").val('#' + hex);
            },onSubmit: function(hsb, hex, rgb, el) {
                $(el).val('#' + hex);
                $(el).ColorPickerHide();
            },
        });
        $(".colorChange_3").ColorPicker({
            color: '#0000ff',
            onShow: function (colpkr) {
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function (colpkr) {
                $(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                $('#color_3 div').css('backgroundColor', '#' + hex);
                $(".colorChange_3").val('#' + hex);
            },onSubmit: function(hsb, hex, rgb, el) {
                $(el).val('#' + hex);
                $(el).ColorPickerHide();
            },
        });
        $(".colorChange_4").ColorPicker({
            color: '#0000ff',
            onShow: function (colpkr) {
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function (colpkr) {
                $(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                $('#color_4 div').css('backgroundColor', '#' + hex);
                $(".colorChange_4").val('#' + hex);
            },onSubmit: function(hsb, hex, rgb, el) {
                $(el).val('#' + hex);
                $(el).ColorPickerHide();
            },
        });

    });


</script>
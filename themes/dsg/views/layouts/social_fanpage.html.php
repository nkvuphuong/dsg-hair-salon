<aside>
    <?if( $CMS->vars['facebook_id_fanpage'] ){?>
    <!-- facebook fanpage -->
    <div id="fanpage_fb_container"></div>
    <!-- End facebook fanpage -->

    <?}else if( $CMS->vars['google_id_fanpage'] ){?>
    <!-- google fanpage -->
    <div id="fanpage_google_container"></div>
    <!--End google fanpage -->

    <?}else if ( $CMS->vars['twitter_id_fanpage'] ){?>
    <!-- twitter fanpage -->
    <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
    <div id="fanpage_twitter_container"></div>
    <!-- End twitter fanpage -->
    <?}?>

    <!-- use for calculator width -->
    <div id="social_block_width" style="width:100% !important; height: 1px !important"></div>
</aside>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml" itemscope itemtype="http://schema.org/NailSalon">

<head>

    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta itemprop="description" name="description" content="<?=$tpl->seo['description'];?>" />
    <meta name="keywords" content="<?=$tpl->seo['keywords'];?>" />
    <meta name="author" content="<?=$tpl->seo['author'];?>" />

    <!-- OG -->
    <meta property="og:title" content="<?=$tpl->seo['og_title'];?>" />
    <meta property="og:description" content="<?=$tpl->seo['og_description'];?>" />
    <meta property="og:image" content="<?=$tpl->seo['og_image'];?>" />
    <meta property="og:url" content="<?="{$CMS->vars['root_domain']}{$CMS->vars['request_url']}";?>"/>
    <meta property="og:type" content="<?=$CMS->vars['og_type'];?>"/>
    <meta property="og:site_name" content="<?=$CMS->vars['website_title'];?>"/>

    <!-- Dublin Core -->
    <link rel="schema.DC" href="http://purl.org/dc/elements/1.1/">
    <meta name="DC.title" content="<?=$tpl->seo['dc_title'];?>">
    <meta name="DC.identifier" content="<?="{$CMS->vars['root_domain']}{$CMS->vars['request_url']}";?>">
    <meta name="DC.description" content="<?=$tpl->seo['dc_description'];?>">
    <meta name="DC.subject" content="<?=$tpl->seo['dc_subject'];?>">
    <meta name="DC.language" scheme="UTF-8" content="<?=$CMS->vars['dc_language'];?>">

    <!-- GEO meta -->
    <meta name="geo.region" content="<?=$CMS->vars['geo_region'];?>">
    <meta name="geo.placename" content="<?=$CMS->vars['geo_placename'];?>">
    <meta name="geo.position" content="<?=$CMS->vars['geo_position'];?>">
    <meta name="ICBM" content="<?=$CMS->vars['geo_icbm'];?>">

    <meta itemprop="priceRange" name="priceRange" content="<?=$CMS->vars['price_range'];?>">
    <meta itemprop="logo" name="logo" content="<?=$tpl->logo_website;?>">
    <meta itemprop="image" name="image" content="<?=$tpl->logo_website;?>">
    <meta itemprop="telephone" name="telephone" content="<?=$CMS->vars['company_phone'];?>">

    <!-- Page Title -->
    <title  itemprop="name"><?=$tpl->seo['title'];?></title>
    <link rel="canonical" href="<?=$CMS->vars['root_domain'];?>">
    <base href="<?=\core\ezy::$web_assets;?>">

    <!-- Favicons -->
    <link rel="icon" href="<?=$tpl->favIcon;?>" type="image/x-icon">
    <link rel="shortcut icon" href="<?=$tpl->favIcon;?>" type="image/x-icon">

    <!-- Webmaster tools -->
    <?=$CMS->vars['webmaster_tools'];?>

    <!-- CSS -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&amp;subset=vietnamese" rel="stylesheet">
    <?=\lib\assets::generate(array(
        "css/bootstrap.min.css", 
        "css/font-awesome.min.css", 
        "css/ie10-viewport-bug-workaround.css", // IE10 viewport hack for Surface/desktop Windows 8 bug

        // Custom styles for this template
        "css/dropdown-submenu.css", 
        "css/slider-pro.min.css", 
        "css/meanmenu.css", 
        "css/dropdown-submenu.css", 
        "css/style.css", 
        "css/responsive.css", 

        // Custom
        "custom/css/jquery-ui/jquery-ui.min.css", 
        "custom/css/jquery.flex.label.css",
        "custom/css/bootstrap-datetimepicker.min.css",
        "custom/css/pnotify.css", 
        "custom/css/lightbox.css", 
        "custom/css/jquery.validation.css",
        "custom/css/jquery.magnific-popup.css", 
        "custom/css/custom.css",

    ), "css");?>

    <!-- Css Extends -->
    <style type="text/css"><?=html_entity_decode(\core\ezy::tpl("css", "layouts"), ENT_QUOTES | ENT_XML1, 'UTF-8');?></style>

    <!-- Go to www.addthis.com/dashboard to customize your tools --> 
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-59b8fec5dd14e0f2"></script>
    <script type="text/javascript">
    var addthis_share =
    {
       url: "<?="{$CMS->vars['root_domain']}{$CMS->vars['request_url']}";?>",
       title: "<?=$tpl->seo['title'];?>",
       description: "<?= $tpl->seo['og_description'];?>",
       media: "<?= $tpl->seo['og_image'];?>"
    }
    </script>

    <!-- JS -->
    <script>
    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.9&appId=1651763725063604";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    </script>

    <script type="text/javascript">
    var dateFormatBooking = "<?=$CMS->vars['date_format'];?>";
    var posFormat = "<?=$CMS->vars['position_date_format'][$CMS->vars['date_format']];?>";
    var timeMorning = '<?=$CMS->vars['booking_hours_morning'];?>';
    var timeAfternoon = '<?=$CMS->vars['booking_hours_afternoon'];?>';
    var bookLogin = "<?=$tpl->booking_login;?>";
    var beforeTime = "<?=$CMS->vars['booking_before_hours'];?>";
    var beforeDay = <?=$CMS->vars['booking_before_day'];?>;
        var currDateT = "<?=date("Y/m/d");?>";
        var checktimebooking = 1;
    var enableRecaptcha = <?=$CMS->vars['recaptcha_google'];?>;
    var hoursTimeFormat = <?=$CMS->vars['hours_time_format'];?>;
    var phoneFormat = "<?=$CMS->vars['phone_format'];?>";
    var company_phone = "<?=$CMS->vars['company_phone'];?>";
    var facebook_id_fanpage = "<?=urlencode($CMS->vars['facebook_id_fanpage']);?>";
    var google_id_fanpage = "<?=$CMS->vars['google_id_fanpage'];?>";
    var twitter_id_fanpage = "<?=$CMS->vars['twitter_id_fanpage']?>";
    var num_paging = "<?=$CMS->vars['pagination_number'];?>";
    var site = "<?=\core\ezy::$site;?>";
    var site_act = "<?=\core\ezy::$act;?>";
    var currency_type = "<?=$CMS->vars['currency_type'];?>";
    var price_comestic_advance = "100000";
    var price_staff_advance = "100000";
    var price_hairlength_advance = "100000";
    </script>
    
    <?=\lib\assets::generate(array(
        "custom/js/jquery.min.js", 
        "custom/js/jquery-ui.min.js", 
        "custom/js/jquery-migrate.min.js", 

        "js/bootstrap.min.js", 
        "js/jquery.sliderPro.min.js", 
        "js/jquery.meanmenu.js", 
        "js/countdown.js", 
        
        // Custom plugin
        "custom/js/jquery.flex.label.js", 
        "custom/js/jquery.mask.min.js", 
        "custom/js/moment-with-locales.js", 
        "custom/js/bootstrap-datetimepicker.js", 
        "custom/js/lightbox.js",  
        "custom/js/pnotify.js",  
        "custom/js/jquery.validation.min.js", 
        "custom/js/jquery.magnific-popup.min.js", 
        "custom/js/app_2.js", 
        "custom/js/js_php.js",
    ), "js");?>
    
    <!-- fb retargeting -->
    <?=html_entity_decode($CMS->vars['fb_retageting'], ENT_QUOTES | ENT_XML1, 'UTF-8');?>
</head>
<body>



    <!-- h2 seo and facebook -->
    <? if($tpl->seo['h1_content'] != "") {?>    
     <h1 itemprop="name" style="display: none"><?=$tpl->seo['h1_content']?></h1>
    <? } ?>
   
    <div id="fb-root" style="display: none"></div>

    <div class="top-bar">
        <!-- tpl header_top -->
        <?=\core\ezy::tpl('header_top', 'layouts');?>
    </div>

    <header class="header-wrap">
        <!-- tpl header_wrap -->
        <?=\core\ezy::tpl('header_main', 'layouts');?>
    </header>
    <section class="main-wrap"><?=$tpl->yield;?></section>
    <footer class="footer footer-wrap">
        <!-- tpl footer_main -->
        <?=\core\ezy::tpl('footer_main', 'layouts');?>

        <!-- tpl footer_copyright -->
        <?=\core\ezy::tpl('footer_copyright', 'layouts');?>
    </footer>
    <script type="text/javascript">
    
    $(document).ready(function(){
        var city_id_footer = $("#city_id_footer").val();
        get_storebycity_footer(city_id_footer);

    });
    $("#city_id_footer").on('change',function(){
        var city_id_footer = $("#city_id_footer").val();
        get_storebycity_footer(city_id_footer);
    });
    function get_storebycity_footer(id)
    {
        $("#liststore_footer").html("");
        $.ajax({
          type: "post",
          url: "/salon/optionstore/id-"+id,
          data: {},
          success: function(response)
          {
              $("#liststore_footer").html(response);
          }
        });
    }
      
</script>

    <!-- JS -->
    <?=\lib\assets::generate(array(
        // This theme
        "js/scripts.js", 

        // Custom
        "custom/js/app_script.js", 
        "custom/js/app.js", 
    ), "js");?>

    <script type="text/javascript">
    var enable_booking = "<?=$CMS->vars['booking_enable'];?>";
    if ( enable_booking == 0 ) {
        $(".btn_make_appointment").remove();
    }
    </script>

    <!-- Google analytics -->
    <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', '<?=$CMS->vars['google_analytic'];?>', 'auto');
    ga('send', 'pageview');
    </script>
    
    <!-- gg adwords remarketing -->
    <?=html_entity_decode($CMS->vars['gg_adwords'], ENT_QUOTES | ENT_XML1, 'UTF-8');?>
    
    <!-- Js Extends -->
    <script type="text/javascript"><?=html_entity_decode(\core\ezy::tpl("script", "layouts"), ENT_QUOTES | ENT_XML1, 'UTF-8');?></script>

    <!-- Popup -->
    <?=\core\ezy::render("popup", "layouts");?>

    <!-- Js pnotify -->
    <?=\core\ezy::render('pnotify', 'layouts');?>
</body>
</html>
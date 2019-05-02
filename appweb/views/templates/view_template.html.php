<!DOCTYPE html>
<html lang="vi" dir="ltr">
<head>

    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta name="description" content="<?=(empty($CMS->lang['seo_description'])) ? $CMS->vars['seo_description'] : $CMS->lang['seo_description'];?>" />
    <meta name="keywords" content="<?=(empty($CMS->lang['seo_keyword'])) ? $CMS->vars['seo_keyword'] : $CMS->lang['seo_keyword'];?>" />
    <meta name="author" content="<?=(empty($CMS->lang['seo_author'])) ? $CMS->vars['seo_author'] : $CMS->lang['seo_author'];?>" />

    <!-- OG -->
    <meta property="og:title" content="<?=$CMS->vars['og_title'];?>" />
    <meta property="og:description" content="<?=$CMS->vars['og_description'];?>" />
    <meta property="og:image" content="<?=$CMS->vars['og_image'];?>" />
    <meta property="og:url" content="<?=$CMS->vars['root_domain'] . $CMS->vars['request_url'];?>"/>
    <meta property="og:type" content="site"/>
    <meta property="og:site_name" content="<?=(empty($CMS->lang['website_title'])) ? $CMS->vars['website_title'] : $CMS->lang['website_title'];?>"/>

    <!-- Dublin Core -->
    <link rel="schema.DC" href="http://purl.org/dc/elements/1.1/">
    <meta name="DC.title" content="<?=$CMS->vars['dc_title'];?>">
    <meta name="DC.identifier" content="<?="{$CMS->vars['root_domain']}{$CMS->vars['request_url']}"?>">
    <meta name="DC.description" content="<?=$CMS->vars['dc_description'];?>">
    <meta name="DC.subject" content="<?=$CMS->vars['dc_subject'];?>">
    <meta name="DC.language" scheme="UTF-8" content="<?=$CMS->vars['dc_language'];?>">

    <!-- GEO meta -->
    <meta name="geo.region" content="<?=$CMS->vars['geo_region'];?>">
    <meta name="geo.placename" content="<?=$CMS->vars['geo_placename'];?>">
    <meta name="geo.position" content="<?=$CMS->vars['geo_position'];?>">
    <meta name="ICBM" content="<?=$CMS->vars['geo_icbm'];?>">

    <!-- SEO -->
    <meta name="google-site-verification" content="72b2STB7Pipq4Qfj7GNx08rEPBYHOL4i1o3bohIthnw" />

    <!-- Page Title -->
    <title  itemprop="name"><?=(empty($CMS->lang['website_title'])) ? $CMS->vars['website_title'] : $CMS->lang['website_title'];?></title>
    <link rel="canonical" href="<?=$CMS->vars['root_domain'];?>">
    <base href="<?=\core\ezy::$web_assets;?>">

    <!-- Favicons -->
    <link rel="icon" href="<?=($CMS->vars['favicon'])? $CMS->vars['favicon'] : 'images/favicon.ico';?>" type="image/x-icon">
    <link rel="shortcut icon" href="<?=($CMS->vars['favicon'])? $CMS->vars['favicon'] : 'images/favicon.ico';?>" type="image/x-icon">

    <!-- css -->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">  
    <?=\lib\assets::generate(array(
        "appweb/assets/css/fonts.css",
        "appweb/assets/css/bootstrap.min.css",
        "appweb/assets/css/demo-template.css", 
    ), "css");?>

    <!-- js -->
    <?=\lib\assets::generate(array(
        "appweb/assets/js/jquery.min.js",
        "appweb/assets/js/bootstrap.min.js",
        "appweb/assets/js/demo-template.js", 
    ), "js")?>
    <!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <div id="theme-demo">
        <div class="preview-toolbar">
            <div class="container">
                <div class="row">
                    <div class="col-xs-10 col-sm-6 col-md-4 toolbar-left">
                        <a class="logo-bar" href="/">
                            <img src="images/logo_web4s_2.png" alt="Demo Page" style="max-height: 20px">
                        </a>
                        <a class="btn btn-black" href="/kho-giao-dien-thiet-ke-moi.html">
                            <i class="fa fa-mail-reply"></i> Quay về salon giao diện
                        </a>
                    </div>
                    <div class="hidden-xs hidden-sm col-md-4 toolbar-center">
                        <a id="target-desktop" class="btn btn-black active hidden-xs hidden-sm hidden-md"><i class="fa fa-desktop"></i>Desktop</a>
                        <a id="target-tablet" class="btn btn-black hidden-xs"><i class="fa fa fa-tablet"></i>Tablet</a>
                        <a id="target-mobile" class="btn btn-black hidden-xs"><i class="fa fa-phone"></i>Mobile</a>
                    </div>
                    <div class="col-xs-2 col-sm-6 col-md-4 toolbar-right">
                        <?if ( !empty($tpl->template['code']) ) {?>
                        <a class="btn btn-orange ani hidden-xs" href="/dang-ky-dung-thu/webstep1/?theme=<?=$tpl->template['code'];?>">Sử dụng giao diện này</a>
                        <?}?>

                        <?if ( !empty($tpl->template['link_demo']) ) {?>
                        <a class="btn btn-close-bar btn-black" href="<?=($tpl->template['link_demo']);?>"><i class="fa fa-close"></i></a>
                        <?}?>
                    </div>
                </div>
            </div>  
        </div>  
        <div id="theme-wrapper">
            <div id="theme-container">
                <?if ( empty($tpl->template['link_demo']) ) {?>
                <section id="page-404">
                    <img src="images/404/info.png" class="img-responsive">
                </section>

                <?}else{?>
                <iframe style="max-width: 100%; max-height: 100%; margin: 0px; top: 0px; left: 0px;" id="frame" src="<?=$tpl->template['link_demo'];?>"></iframe>
                <?}?>
            </div>
        </div>  
    </div>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>ACP <?= $CMS->core->page_title; ?></title>
    <link rel="shortcut icon" href="<?= $CMS->vars['img_url']; ?>/logos/favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?= $CMS->vars['img_url']; ?>/logos/favicon.ico" type="image/x-icon">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <?=\lib\assets::generate(array(
    "jsacp/ie9.html5shiv.min.js",
    "jsacp/ie9.respond.min.js"
    ));?>
    <![endif]-->
    <?=\lib\assets::generate(array(
        "assets/css/lib/font-awesome/font-awesome.min.css",
        "css/magnific-popup.css",
        "css/tooltipster.bundle.min.css",
        "css/jquery_ui.css",
        "assets/css/lib/charts-c3js/c3.min.css",
        "css/animate.css",
        "assets/css/lib/ladda-button/ladda-themeless.min.css",
        "{$CMS->vars['public_url']}/js/jquery-ui/jquery-ui.min.css", // 08/02/17 Jquery UI
        "{$CMS->vars['public_url']}/js/jquery-ui/jquery-ui.structure.min.css", // 08/02/17 Jquery UI
        "{$CMS->vars['public_url']}/js/jquery-ui/jquery-ui.theme.min.css", // 08/02/17 Jquery UI
        //"assets/css/lib/summernote/summernote.css", // 10/02/17
        //"assets/css/separate/pages/editor.css", // Summernote, 02/05/17
        "assets/css/lib/datatables-net/datatables.min.css",// 10/02/17
        "assets/css/separate/vendor/datatables-net.min.css",
        "assets/css/lib/flex-label/jquery.flex.label.css",
        "assets/css/separate/vendor/sweetalert2.min.css",
        "assets/css/separate/vendor/bootstrap-daterangepicker.min.css",
        "css/blockui.min.css",
        "assets/css/separate/pages/chat.min.css",
        "assets/css/separate/pages/messenger.min.css",
        "assets/css/main.css?22022018",
        "assets/css/custom.css",
        "assets/css/invoice.min.css",
        "css/colorpicker.css",
        "css/layout.css"
    ), "css");?>
    <script language="javascript">
        var scrollable_block;
        var api_scrollable_block;
        var example_select2_photo;
    </script>

    <?=\lib\assets::generate(array(
        "assets/js/plugins.min.js",
        "assets/js/lib-ext.js",
        "assets/js/app.js",
        "assets/js/lib/select2/select2.full.min.js",
        "assets/js/lib/sweetalert2/sweetalert2.min.js",
        "assets/js/lib/daterangepicker/daterangepicker.js",
        "assets/js/loader.js",
        "jsacp/jquery.magnific-popup.min.js",
        "jsacp/tooltipster.bundle.min.js",
        "{$CMS->vars['public_url']}/js/jquery-ui/jquery-ui.min.js", // 08/02/17 Jquery UI
//        "assets/js/lib/summernote/summernote.min.js", // 10/02/17
        "assets/js/lib/datatables-net/datatables.min.js",
        "assets/js/lib/flex-label/jquery.flex.label.js",
        "assets/js/lib/nestable/jquery.nestable.js",
        "jsacp/jquery.mask.min.js",
        "assets/js/lib/typeahead/bootstrap3-typeahead.min.js",
        "jsacp/gallery.js",
        "jsacp/rating.js?20170308",
        "assets/js/dropzone.js",
        "jsacp/jquery.blockUI.js",
        "assets/js/jquery.matchHeight.js",
        "jsacp/dashboard.js",
        "jsacp/colorpicker.js",
        "jsacp/crawling.js?23022018",
        "jsacp/commission.js?27022018",
        "jsacp/config_general.js?28022018"
    ), "js");?>

    <? if (\lib\input::vars('checkin_enabled')) { ?>
        <script type="text/javascript" src="<?=$CMS->vars['npm_url'];?>/socket.io-client/dist/socket.io.js"></script>
        <script type="text/javascript" src="/acp/assets/js/client.js"></script>
    <? } ?>

    <script type="text/javascript" src="<?=$CMS->vars['public_url'];?>/js/js_var.php"></script>
    <script type="text/javascript" src="/acp/assets/js/lib/tinymce/tinymce.min.js"></script>
    <script type="text/javascript" >
        var cms_lang =  <?= json_encode($CMS->lang); ?>;
        var cms_translations =  '<?= json_encode($CMS->vars['translations']); ?>';
        if(cms_translations) {
            cms_translations = cms_translations == "null" ? '[]' : cms_translations;
            cms_translations = $.parseJSON(cms_translations);
            if(cms_translations.length == 0) cms_translations =  null;
        }
        else {
            cms_translations = null;
        }
        var site_parent_domain = "<?= $CMS->vars['parent_domain']; ?>";
        var site_root_domain = "<?= $CMS->vars['root_domain']; ?>";
        var site_img_url = "<?= $CMS->vars['img_url']; ?>";
        var currency_type = "<?= $CMS->vars['currency_type']; ?>";
        var confirm_alert_delete = "<?= $CMS->lang['confirm_alert_delete']; ?>";
        var confirm_alert_title = "<?= $CMS->lang['confirm_alert_title']; ?>";
        var is_mobile = <?= $_SESSION['is_mobile']; ?>;
        var lang_field_required = "<?= $CMS->lang['msg_field_required']; ?>";
        var site_user_id = "<?= $tpl->member['user_id']; ?>";
        var site_is_root = "<?= $CMS->vars['is_root']; ?>";
        var site_is_admin = "<?= $CMS->vars['is_admin']; ?>";
        var site_is_leader = "<?= $CMS->vars['is_leader']; ?>";
        var site_group_id = "<?= $tpl->member['userg_id']; ?>";
        var site_page = "<?=intval($CMS->input['page']) ?>";
        var upload_url = "<?= $CMS->vars['upload_url']; ?>";
        var site_url_return = "<?= $CMS->class->search->url_return; ?>";
        var lang_loading = "<?= $CMS->lang['lang_loading']; ?>";
        var dateFormatBooking = "<?=$CMS->vars['date_format'];?>";
        var checktimebooking = 1;
        var phoneFormat = "<?=$CMS->vars['phone_format'];?>";
        var dateFormat = "<?=$CMS->vars['date_format'];?>";
        var default_language = "<?=$CMS->vars['default_language'];?>";
        <?=$CMS->user->show_jspermission();?>

        /* Used for js embed instagram demo */
        var instagramUserId = '<?=$CMS->vars['instagram_user_id'];?>';
        var instagramAccessToken = '<?=$CMS->vars['instagram_access_token'];?>';
    </script>
    <?=\lib\assets::generate(array(
        "{$CMS->vars['public_url']}/js/js_global.js",
        "{$CMS->vars['js_acp']}/acp_core.js?201803081521",
        "{$CMS->vars['public_url']}/js/js_php.js",
        "assets/js/lib/charts-c3js/c3.min.js",
        "assets/js/lib/d3/d3.min.js",
        "{$CMS->vars['js_acp']}/jquery.validate.js",
        "{$CMS->vars['js_acp']}/waiting-modal-dialog.js",
        "jsacp/bootstrap-notify.min.js",
        "assets/js/lib/input-mask/jquery.mask.min.js", // 27/02/2017
        "javascript/acp_editor.js", // 09/06/17
        "javascript/acp_order.js", // 18/04/18
        "javascript/acp_attribute.js", // 14/05/18
    ), "js"); ?>
</head>
<? if($CMS->input['site'] == "dashboard" ) { ?>
<?php
if( $CMS->vars['site_routed_app'] == "web" )
{
?>
<body class="with-side-menu control-panel control-panel-compact chrome-browser">
<? } else{ ?>
<body class="with-side-menu  chrome-browser">
<? } ?>
<? } else { ?>
<body class="with-side-menu chrome-browser">
<?php } ?>
<header class="site-header">
    <div class="container-fluid">
        <a href="?" class="site-logo">
            <? if( defined("is_web_us") == true ) { ?>
                <img alt="" class="hidden-sm-down" src="<?= $CMS->vars['img_url']; ?>/logos/logo-fb.png">
            <? } else if ( defined("is_web_vn") == true ) { ?>
                <img alt="" class="hidden-sm-down" src="<?= $CMS->vars['img_url']; ?>/logos/logo-autom.png">
            <? } else { ?>
                <img alt="" class="hidden-sm-down" src="<?= $CMS->vars['img_url']; ?>/logos/logo.png">
            <? } ?>
            <img alt="" class="hidden-md-up mobile" src="<?= $CMS->vars['img_url']; ?>/logos/m_logo.png">
        </a>

        <button id="show-hide-sidebar-toggle" class="show-hide-sidebar hidden-md-down">
            <span>toggle menu</span>
        </button>

        <button class="hamburger hamburger--htla">
            <span>toggle menu</span>
        </button>

        <div class="site-header-content">
            <div class="site-header-content-in">
                <div class="site-header-shown">
                    <? /*LHL-2018-07-22: Disable website link when app = acp*/
                    if ($CMS->vars['site_routed_app'] == "web") { ?>
                        <a href="<?=$CMS->vars['parent_domain'];?>" target="_blank" class="message action btn_view_website">
                            <i class="fa fa-eye" aria-hidden="true"></i> <?=$CMS->lang['title_view_website'];?>
                        </a>
                    <? } ?>
                    <? // Tạm thời đóng menu này: add  ?>
                    <? if(1==0) { ?>
                        <div class="dropdown">
                            <a href class="dropdown-toggle message action" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-plus-circle"></i><small class="hidden-xs-down"><?= $CMS->lang['header_icon_add']; ?></small>
                            </a>
                            <div class="top-dropdown-menu dropdown-menu dropdown-menu-right dropdown-menu-notif">
                                <figure class="top_menu_store distable">
                                    <h3 class="roboto_light"><?= $CMS->lang['header_icon_add']; ?><!--<a href="#" title="" class="roboto_regular">Cấu hình</a>--></h3>
                                </figure>
                                <div class="row">
                                    <div class="col-md-4 col-sm-4">
                                        <section class="block_store">
                                            <h4 class="roboto_bold"><?=$CMS->lang['menua_customer'];?></h4>
                                            <ul>
                                                <li><a href="?site=customer&act=add" title=""><?=$CMS->lang['menua_customer'];?></a></li>
                                                <li><a href="?site=transactions&type=1&sub=1&act=add" title=""><?=$CMS->lang['menua_invoice'];?></a></li>
                                                <li><a href="?site=transactions&type=1&sub=2&act=add" title=""><?=$CMS->lang['menua_received'];?></a></li>
                                                <li><a href="?site=transactions&type=1&sub=3&act=add" title=""><?=$CMS->lang['menua_receipt'];?></a></li>
                                                <li><a href="?site=transactions&type=1&sub=4&act=add" title=""><?=$CMS->lang['menua_estimate'];?></a></li>
                                                <li><a href="?site=order&act=add" title=""><?=$CMS->lang['menua_order'];?></a></li>
                                            </ul>
                                        </section>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <section class="block_store">
                                            <h4 class="roboto_bold"><?=$CMS->lang['menua_supplier'];?></h4>
                                            <ul>
                                                <li><a href="?site=supplier&act=add" title=""><?=$CMS->lang['menua_supplier'];?></a></li>
                                                <li><a href="?site=transactions&type=1&sub=5&act=add" title=""><?=$CMS->lang['menua_bill'];?></a></li>
                                                <li><a href="?site=transactions&type=1&sub=6&act=add" title=""><?=$CMS->lang['menua_payment'];?></a></li>
                                                <li><a href="?site=transactions&type=1&sub=7&act=add" title=""><?=$CMS->lang['menua_expense'];?></a></li>
                                            </ul>
                                        </section>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <section class="block_store">
                                            <h4 class="roboto_bold"><?=$CMS->lang['menua_warehouse'];?></h4>
                                            <ul>
                                                <li><a href="?site=store_request" title=""><?=$CMS->lang['menua_import'];?></a></li>
                                                <li><a href="?site=returns" title=""><?=$CMS->lang['menua_export'];?></a></li>
                                                <li><a href="?site=inventory" title=""><?=$CMS->lang['menua_inventory'];?></a></li>
                                                <li><a href="?site=assets&act=add" title=""><?=$CMS->lang['menua_assets'];?></a></li>
                                            </ul>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? } ?>


                    <!--<div class="dropdown">
                        <a href class="dropdown-toggle message" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-search"></i><small class="hidden-xs-down"><?= $CMS->lang['header_icon_search']; ?></small>
                        </a>
                        <div class="top-dropdown-menu dropdown-menu search_store">
                            <figure class="block_search distable">
                                <h3 class="roboto_light"><?= $CMS->lang['header_search_title']; ?></h3>
                                <div class="input_search">
                                    <input type="text" placeholder="<?= $CMS->lang['header_search_placeholder']; ?>" />
                                    <input type="submit" value="&#xf002;" class="fa-input" />
                                </div>
                                <a href="#" title="">{$CMS->lang['gsearch_advance']}<i class="fa fa-angle-double-right"></i></a>
                            </figure>
                            <<figure class="block_table_search distable">
                                <h3 class="roboto_light">Giao dịch gần đây</h3>
                                <div class="table-responsive">
                                    <table>
                                        <tr>
                                            <td>PX09534</td>
                                            <td>Nguyễn Doãn Thành Duy</td>
                                            <td>đ 26.000.000</td>
                                            <td>18/02/2017, 2h04 pm</td>
                                        </tr>
                                        <tr>
                                            <td>PX09534</td>
                                            <td>Nguyễn Doãn Thành Duy</td>
                                            <td>đ 26.000.000</td>
                                            <td>18/02/2017, 2h04 pm</td>
                                        </tr>
                                        <tr>
                                            <td>PX09534</td>
                                            <td>Nguyễn Doãn Thành Duy</td>
                                            <td>đ 26.000.000</td>
                                            <td>18/02/2017, 2h04 pm</td>
                                        </tr>
                                        <tr>
                                            <td>PX09534</td>
                                            <td>Nguyễn Doãn Thành Duy</td>
                                            <td>đ 26.000.000</td>
                                            <td>18/02/2017, 2h04 pm</td>
                                        </tr>
                                    </table>
                                </div>
                                <a href="#" title="">Xem tất cả</a>
                            </figure>
                        </div>
                    </div>-->
                    <!--Begin change color-->
                    <? if(isset($CMS->vars['web_free']) and $CMS->vars['web_free'] == 2) { ?>
                        <a href="/acp/?site=config_general&act=color" class="message action btn_view_website" >
                            <i class="panel-control-icon glyphicon glyphicon-refresh" style="font-size: 16px;"></i><small class="hidden-xs-down"><?=$CMS->lang['header_change_color_website']; ?></small>
                        </a>
                    <? }else if(isset($CMS->vars['web_free']) and $CMS->vars['web_free'] == 1) { ?>
                        <div class="dropdown">
                            <a href= class="dropdown-toggle message" data-toggle="dropdown" aria-expanded="false" style="position:relative;">
                            <i class="panel-control-icon glyphicon glyphicon-refresh" style="font-size: 16px;"></i><small class="hidden-xs-down"><?=$CMS->lang['header_change_color_website']; ?></small>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-notif" aria-labelledby="dd-notification">
                                <? if(isset($CMS->vars['list_color_theme']) and is_array($CMS->vars['list_color_theme']) and count(is_array($CMS->vars['list_color_theme']) > 0)) {?>

                                    <div class="dropdown-menu-notif-list">
                                        <ul class="list_color">
                                            <li>
                                                <a href="/acp/?site=dashboard&act=change_color_theme&colr=default" class="name_color">
                                                    <div class="box_color" style="background: none;" c_active="default"></div>
                                                    Default
                                                </a>
                                            </li>
                                            <? foreach ($CMS->vars['list_color_theme'] as $value) { ?>

                                                <li>
                                                    <a href="/acp/?site=dashboard&act=change_color_theme&colr=<?=$value;?>" class="name_color">
                                                        <div c_active="<?=$value;?>" class="box_color" style="background: <?=$value;?>"></div>
                                                        <?=$value;?>
                                                    </a>
                                                </li>
                                            <? } ?>
                                        </ul>
                                    </div>
                                <? } ?>
                            </div>
                        </div>
                    <? } ?>
                    <!--End change color-->




                    <? if(isset($CMS->vars['web_free']) == 1) { ?>
                        <div class="dropdown user-menu">
                            <button class="dropdown-toggle message" id="dd-user-menu" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="position:relative;">
                                <img src="<?=$tpl->avatar;?>" alt="">
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-user-menu" x-placement="bottom-end" >
                                <div class="dropdown-menu-col" style="width: 100%">
                                    <a class="dropdown-item title_user"><span class="font-icon fa fa-user"></span><?=$tpl->member['user_display_name'];?></a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="?site=myaccount"><span class="font-icon glyphicon glyphicon-cog"></span>Settings</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="?site=login&amp;act=logout"><span class="font-icon glyphicon glyphicon-log-out"></span><?=$CMS->lang['menuh_logout'];?></a>
                                </div>
                            </div>
                        </div>


                    <? } else { ?>
                        <div class="dropdown">
                            <a href class="dropdown-toggle message" data-toggle="dropdown" aria-expanded="false" style="position:relative">
                                <i class="fa fa-cog"></i><small class="hidden-xs-down"><?= $CMS->lang['header_icon_setting']; ?></small>
                                <!--<img src="/assets/img/avatar-2-64.png" alt="user-img" class="img-circle user-img">-->
                            </a>
                            <div class="top_menu_right dropdown-menu acc_menu">
                                <? // Tạm thời đóng menu này: top-dropdown-menu  ?>
                                <? if(1==0) { ?>
                                    <div class="left">
                                        <h3 class="roboto_light">
                                            <?= $CMS->vars['website_title']; ?>

                                            <? if($CMS->vars['siteInfo']['site_license_package'] && $CMS->vars['siteInfo']['site_license_expired']) { ?>
                                                <span class="btn btn-inline btn-primary btn-sm ladda-button"><?=$CMS->vars['siteInfo']['site_license_package']?>
                                                <? if($CMS->vars['siteInfo']['site_license_package'] && $CMS->vars['siteInfo']['site_license_package']!=$CMS->subscription->getPackageList()[0]['package_name']) { ?>
                                                    (<?=$CMS->class->date->date_format($CMS->vars['siteInfo']['site_license_expired']);?>)</span>
                                                    <a onclick="cancelPackage('<?=$CMS->vars['root_domain'];?>/?site=subscription&act=cancel_package')"><span class="btn btn-inline btn-danger btn-sm ladda-button">Cancel</span></a>
                                                <? } ?>
                                            <? } ?>
                                        </h3>
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6">
                                                <ul>
                                                    <li><a href="?site=user&act=add" title=""><?=$CMS->lang['menuh_adduser'];?></a></li>
                                                    <li><a href="?site=user" title=""><?=$CMS->lang['menuh_listuser'];?></a></li>
                                                    <li><a href="?site=group" title=""><?=$CMS->lang['menuh_listgroup'];?></a></li>
                                                    <li><a href="?site=accounts" title=""><?=$CMS->lang['menuh_listbank'];?></a></li>
                                                    <li><a href="?site=accounts_type" title=""><?=$CMS->lang['menuh_listaccount'];?></a></li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <ul>
                                                    <li><a href="?site=config_general" title=""><?=$CMS->lang['menuh_configsite'];?></a></li>
                                                    <li><a href="?site=printtemplates" title=""><?=$CMS->lang['menuh_listprint'];?></a> X</li>
                                                    <li><a href="?site=logs" title=""><?=$CMS->lang['menuh_history'];?></a></li>
                                                    <li><a href="?site=subscription" title=""><?=$CMS->lang['menuh_subscription'];?></a> X</li>
                                                </ul>
                                            </div>

                                        </div>
                                    </div>
                                <? } ?>
                                <div class="right">
                                    <h3 class="roboto_bold"><?=$tpl->member['user_display_name'];?></h3>
                                    <ul>
                                        <li><a href="?site=myaccount" title=""><?=$CMS->lang['menuh_accountinfo'];?></a></li>
                                        <li><a  href="?site=login&amp;act=logout" title=""><i class="fa fa-sign-out"></i><?=$CMS->lang['menuh_logout'];?></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <? }// End menu account ?>

                    <!--<div class="dropdown hidden-xs-down">
                        <a href class="dropdown-toggle message" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-question"></i>
                        </a>
                    </div>-->
                    <div class="dropdown">
                        <a href class="dropdown-toggle message" data-toggle="dropdown" aria-expanded="false" style="position:relative;">
                            <i class="fa fa-globe" style="font-size: 16px;"></i><small class="hidden-xs-down">Language</small>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-menu-col">
                                <a class="dropdown-item <?= \lib\language::$default=='vn' ? 'current' : ''; ?>" href="?language=vn">Vietnamese</a>
                                <a class="dropdown-item <?= \lib\language::$default=='en' ? 'current' : ''; ?>" href="?language=en">English</a>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="mobile-menu-right-overlay"></div>
                <div class="site-header-collapsed">
                    <div class="site-header-collapsed-in">
                        <? if(isset($CMS->vars['pos_enabled']) && $CMS->vars['pos_enabled']) {?>
                            <a class="btn btn-nav btn-rounded btn-inline" href="../pos/">
                                <?=$CMS->lang['selling_machine']?>
                            </a>
                        <? } ?>
                        <!--<div class="dropdown dropdown-typical">

                            <a class="dropdown-toggle" id="dd-header-marketing" data-target="#" href="http://example.com" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="font-icon font-icon-cogwheel"></span>
                                <span class="lbl">Marketing automation</span>
                            </a>

                            <div class="dropdown-menu" aria-labelledby="dd-header-marketing">
                                <a class="dropdown-item" href="#">Current Search</a>
                                <a class="dropdown-item" href="#">Search for Issues</a>
                                <div class="dropdown-divider"></div>
                                <div class="dropdown-header">Recent issues</div>
                                <a class="dropdown-item" href="#"><span class="font-icon font-icon-home"></span>Quant and Verbal</a>
                                <a class="dropdown-item" href="#"><span class="font-icon font-icon-cart"></span>Real Gmat Test</a>
                                <a class="dropdown-item" href="#"><span class="font-icon font-icon-speed"></span>Prep Official App</a>
                                <a class="dropdown-item" href="#"><span class="font-icon font-icon-users"></span>CATprer Test</a>
                                <a class="dropdown-item" href="#"><span class="font-icon font-icon-comments"></span>Third Party Test</a>
                                <div class="dropdown-more">
                                    <div class="dropdown-more-caption padding">more...</div>
                                    <div class="dropdown-more-sub">
                                        <div class="dropdown-more-sub-in">
                                            <a class="dropdown-item" href="#"><span class="font-icon font-icon-home"></span>Quant and Verbal</a>
                                            <a class="dropdown-item" href="#"><span class="font-icon font-icon-cart"></span>Real Gmat Test</a>
                                            <a class="dropdown-item" href="#"><span class="font-icon font-icon-speed"></span>Prep Official App</a>
                                            <a class="dropdown-item" href="#"><span class="font-icon font-icon-users"></span>CATprer Test</a>
                                            <a class="dropdown-item" href="#"><span class="font-icon font-icon-comments"></span>Third Party Test</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Import Issues from CSV</a>
                                <div class="dropdown-divider"></div>
                                <div class="dropdown-header">Filters</div>
                                <a class="dropdown-item" href="#">My Open Issues</a>
                                <a class="dropdown-item" href="#">Reported by Me</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Manage filters</a>
                                <div class="dropdown-divider"></div>
                                <div class="dropdown-header">Timesheet</div>
                                <a class="dropdown-item" href="#">Subscribtions</a>
                            </div>
                        </div>
                        <div class="dropdown dropdown-typical">
                            <a class="dropdown-toggle" id="dd-header-social" data-target="#" href="http://example.com" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="font-icon font-icon-share"></span>
                                <span class="lbl">Social media</span>
                            </a>

                            <div class="dropdown-menu" aria-labelledby="dd-header-social">
                                <a class="dropdown-item" href="#"><span class="font-icon font-icon-home"></span>Quant and Verbal</a>
                                <a class="dropdown-item" href="#"><span class="font-icon font-icon-cart"></span>Real Gmat Test</a>
                                <a class="dropdown-item" href="#"><span class="font-icon font-icon-speed"></span>Prep Official App</a>
                                <a class="dropdown-item" href="#"><span class="font-icon font-icon-users"></span>CATprer Test</a>
                                <a class="dropdown-item" href="#"><span class="font-icon font-icon-comments"></span>Third Party Test</a>
                            </div>
                        </div>
                        <div class="dropdown dropdown-typical">
                            <a href="#" class="dropdown-toggle no-arr">
                                <span class="font-icon font-icon-page"></span>
                                <span class="lbl">Projects</span>
                                <span class="label label-pill label-danger">35</span>
                            </a>
                        </div>

                        <div class="dropdown dropdown-typical">
                            <a class="dropdown-toggle" id="dd-header-form-builder" data-target="#" href="http://example.com" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="font-icon font-icon-pencil"></span>
                                <span class="lbl">Form builder</span>
                            </a>

                            <div class="dropdown-menu" aria-labelledby="dd-header-form-builder">
                                <a class="dropdown-item" href="#"><span class="font-icon font-icon-home"></span>Quant and Verbal</a>
                                <a class="dropdown-item" href="#"><span class="font-icon font-icon-cart"></span>Real Gmat Test</a>
                                <a class="dropdown-item" href="#"><span class="font-icon font-icon-speed"></span>Prep Official App</a>
                                <a class="dropdown-item" href="#"><span class="font-icon font-icon-users"></span>CATprer Test</a>
                                <a class="dropdown-item" href="#"><span class="font-icon font-icon-comments"></span>Third Party Test</a>
                            </div>
                        </div>
                        <div class="help-dropdown">
                            <button type="button">
                                <i class="font-icon font-icon-help"></i>
                            </button>
                            <div class="help-dropdown-popup">
                                <div class="help-dropdown-popup-side">
                                    <ul>
                                        <li><a href="#">Getting Started</a></li>
                                        <li><a href="#" class="active">Creating a new project</a></li>
                                        <li><a href="#">Adding customers</a></li>
                                        <li><a href="#">Settings</a></li>
                                        <li><a href="#">Importing data</a></li>
                                        <li><a href="#">Exporting data</a></li>
                                    </ul>
                                </div>
                                <div class="help-dropdown-popup-cont">
                                    <div class="help-dropdown-popup-cont-in">
                                        <div class="jscroll">
                                            <a href="#" class="help-dropdown-popup-item">
                                                Lorem Ipsum is simply
                                                <span class="describe">Lorem Ipsum has been the industry's standard dummy text </span>
                                            </a>
                                            <a href="#" class="help-dropdown-popup-item">
                                                Contrary to popular belief
                                                <span class="describe">Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC</span>
                                            </a>
                                            <a href="#" class="help-dropdown-popup-item">
                                                The point of using Lorem Ipsum
                                                <span class="describe">Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text</span>
                                            </a>
                                            <a href="#" class="help-dropdown-popup-item">
                                                Lorem Ipsum
                                                <span class="describe">There are many variations of passages of Lorem Ipsum available</span>
                                            </a>
                                            <a href="#" class="help-dropdown-popup-item">
                                                Lorem Ipsum is simply
                                                <span class="describe">Lorem Ipsum has been the industry's standard dummy text </span>
                                            </a>
                                            <a href="#" class="help-dropdown-popup-item">
                                                Contrary to popular belief
                                                <span class="describe">Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC</span>
                                            </a>
                                            <a href="#" class="help-dropdown-popup-item">
                                                The point of using Lorem Ipsum
                                                <span class="describe">Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text</span>
                                            </a>
                                            <a href="#" class="help-dropdown-popup-item">
                                                Lorem Ipsum
                                                <span class="describe">There are many variations of passages of Lorem Ipsum available</span>
                                            </a>
                                            <a href="#" class="help-dropdown-popup-item">
                                                Lorem Ipsum is simply
                                                <span class="describe">Lorem Ipsum has been the industry's standard dummy text </span>
                                            </a>
                                            <a href="#" class="help-dropdown-popup-item">
                                                Contrary to popular belief
                                                <span class="describe">Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC</span>
                                            </a>
                                            <a href="#" class="help-dropdown-popup-item">
                                                The point of using Lorem Ipsum
                                                <span class="describe">Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text</span>
                                            </a>
                                            <a href="#" class="help-dropdown-popup-item">
                                                Lorem Ipsum
                                                <span class="describe">There are many variations of passages of Lorem Ipsum available</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>--><!--.help-dropdown-->
                        <? if ( !defined("is_web") ) { ?>
                            <!--a class="buynow btn btn-nav btn-rounded btn-inline btn-danger-outline" href="?site=subscription&act=upgrade_package">
                            <?=$CMS->lang['menul_upgrade'];?>
                        </a-->
                        <? } ?>
                    </div><!--.site-header-collapsed-in-->
                </div><!--.site-header-collapsed-->



            </div><!--site-header-content-in-->
        </div><!--.site-header-content-->
    </div><!--.container-fluid-->
</header><!--.site-header-->

<div class="mobile-menu-left-overlay"></div>
<nav class="side-menu">
    <ul class="side-menu-list">
        <li class="with-sub">
            <a href="/acp">
                <i class="fa fa-home"></i>
                <span class="lbl"><?= $CMS->lang['menu_dashboard']; ?></span>
            </a>
        </li>

        <li class="with-sub adv" for="menu_sell">
                <span>
                    <i class="fa fa-file-text-o"></i>
                    <span class="lbl" id="menu_sell"><?= $CMS->lang['menu_sell']; ?></span>
                </span>
            <ul for="menu_sell">
                <?=\models\dashboard::renderHTMLMenu("order_add",$CMS->lang['menu_order_add'], "?site=order&act=add", "fa fa-plus");?>
                <?=\models\dashboard::renderHTMLMenu("order_read",$CMS->lang['menu_order'], "?site=order");?>
                <?if( $CMS->vars['type_web'] == "ecommerce" ){?>
                    <?=\models\dashboard::renderHTMLMenu("abandoned_read",$CMS->lang['menu_abandoned'], "?site=abandoned");?>
                <?}?>

                <?if(!\lib\input::arrayValue($CMS->vars, 'web_free')) { ?>
                    <?=\models\dashboard::renderHTMLMenu("product_read",$CMS->lang['menu_product'], "?site=product");?>
                <? } ?>
                <?=\models\dashboard::renderHTMLMenu("service_read",$CMS->lang['menu_service'], "?site=service");?>

                <!-- <?=\models\dashboard::renderHTMLMenu("variants_read",$CMS->lang['menu_variants'], "?site=variants");?>-->

                <?=\models\dashboard::renderHTMLMenu("manufacture_read",$CMS->lang['menu_manufacture'], "?site=manufacture");?>

                <?if(defined("is_web_vn") AND $CMS->vars['addon_goods_enable'] == 1 ){?>
                    <?=\models\dashboard::renderHTMLMenu("store_read",$CMS->lang['menu_list_store'], "?site=store");?>
                <?}?>

                <? if(\lib\input::vars('price_book_enabled'))  { ?>
                    <?=\models\dashboard::renderHTMLMenu("price_read",$CMS->lang['menu_price'], "?site=price");?>
                    <?=\models\dashboard::renderHTMLMenu("price_read",$CMS->lang['act_set_default'], "?site=price&act=set_default");?>
                <? } ?>
                <?if($CMS->vars['addon_goods_enable'] == 1) { ?>
                    <?=\models\dashboard::renderHTMLMenu("config_price_read",$CMS->lang['menu_config_price'], "?site=config_price");?>
                <?}?>

            </ul>
        </li>
        <li class="with-sub" for="menu_partner">
                <span>
                    <i class="fa fa-users"></i>
                    <span class="lbl" id="menu_sell"><?= $CMS->lang['menu_partner']; ?></span>
                </span>
            <ul for="menu_partner">
                <?=\models\dashboard::renderHTMLMenu("customer_read",$CMS->lang['menu_customer'], "?site=customer");?>
                <?=\models\dashboard::renderHTMLMenu("group_customer_read",$CMS->lang['menu_group_customer'], "?site=group_customer");?>
                <?=\models\dashboard::renderHTMLMenu("supplier_read",$CMS->lang['menu_supplier'], "?site=supplier");?>
                <?=\models\dashboard::renderHTMLMenu("partner_delivery_read",$CMS->lang['menu_partner_delivery'], "?site=partner_delivery");?>

                <?=\models\dashboard::renderHTMLMenu("contact_read",$CMS->lang['menu_contact'], "?site=contact");?>
            </ul>

        </li>
        <? if($CMS->vars['addon_goods_enable'] == 1) { ?>
            <li class="with-sub" for="menu_goods">
                    <span>
                        <i class="fa fa-cubes"></i>
                        <span class="lbl" id="menu_assets"><?= $CMS->lang['menu_goods']; ?></span>
                    </span>
                <ul for="menu_goods">
                    <?=\models\dashboard::renderHTMLMenu("store_request_read_request",$CMS->lang['menu_import_goods'], "?site=store_request", "fa fa-plus");?>
                    <?=\models\dashboard::renderHTMLMenu("store_request_read_request_eis",$CMS->lang['menu_export_goods'], "?site=store_request&stage=request_eis");?>
                    <?=\models\dashboard::renderHTMLMenu("inventory_read",$CMS->lang['menu_inventory'], "?site=inventory", "fa fa-search");?>
                    <?=\models\dashboard::renderHTMLMenu("returns_read",$CMS->lang['menu_list_returns'], "?site=returns");?>
                    <?=\models\dashboard::renderHTMLMenu("assets_read",$CMS->lang['menu_assets'], "?site=assets");?>
                    <?=\models\dashboard::renderHTMLMenu("shipment_read",$CMS->lang['menu_shipment'], "?site=shipment");?>
                    <?=\models\dashboard::renderHTMLMenu("store_read",$CMS->lang['menu_list_store'], "?site=store");?>
                </ul>
            </li>
        <?}?>

        <?if($CMS->permit['transactions_read']){?>
            <li class="with-sub" for="menu_transactions">
                <span>
                    <i class="fa fa-money" aria-hidden="true"></i>
                    <span class="lbl" id="menu_transactions"><?= $CMS->lang['menu_transactions']; ?></span>
                </span>
                <ul for="menu_transactions">
                    <?=\models\dashboard::renderHTMLMenu("transactions_read",$CMS->lang['menu_transactions_sales'], "?site=transactions&type=1");?>
                    <?=\models\dashboard::renderHTMLMenu("transactions_read",$CMS->lang['menu_transactions_expenses'], "?site=transactions&type=2");?>
                    <?=\models\dashboard::renderHTMLMenu("transactions_read",$CMS->lang['menu_transactions_recurring'], "?site=transactions&is_recurring=1");?>
                </ul>
            </li>
        <?}?>

        <?if($CMS->permit['email_read'] or $CMS->permit['newsletter_read'] or $CMS->permit['emailtpl_read']){?>
            <li class="with-sub" for="menu_marketing">
            <span>
                <i class="fa fa-shopping-bag"></i>
                <span class="lbl" id="menu_store"><?= $CMS->lang['menu_marketing']; ?></span>
            </span>
                <ul for="menu_marketing">
                    <?=\models\dashboard::renderHTMLMenu("email_add",$CMS->lang['menu_email_add'], "?site=email&act=add");?>
                    <?=\models\dashboard::renderHTMLMenu("email_read",$CMS->lang['menu_email_list'], "?site=email");?>
                    <?=\models\dashboard::renderHTMLMenu("newsletter_read",$CMS->lang['menu_newsletter'], "?site=newsletter");?>
                    <?=\models\dashboard::renderHTMLMenu("emailtpl_read",$CMS->lang['menu_emailtpl'], "?site=emailtpl");?>
                </ul>
            </li>
        <? } ?>

        <? if (\lib\input::arrayValue($CMS->vars,'addon_graph_facebook_enable') == 1 && \lib\input::arrayValue($CMS->permit,'fanpage_read') == 1) { ?>
            <!-- Manage Facebook -->
            <li class="with-sub" for="manage_facebook">
			<span>
				<i class="fa fa-facebook" aria-hidden="true"></i>
				<span class="lbl" id="menu_store"><?= $CMS->lang['menu_facebook']; ?></span>
			</span>
                <ul for="manage_facebook">
                    <li>
                        <a href="?site=facebook&act=fanpage">
						<span class="lbl">
							<i class="fa fa-circle-thin"></i><?= $CMS->lang['menu_fanpage']; ?>
						</span>
                        </a>
                    </li>
                </ul>
            </li>
        <? } ?>

        <?if( $CMS->vars['type_web'] != "ecommerce" ){?>
            <li class="with-sub" for="menu_hr">
            <span>
                <i class="fa fa-user"></i>
                <span class="lbl" id="menu_store"><?= $CMS->lang['menu_hr']; ?></span>
            </span>
                <ul for="menu_hr">
                    <?=\models\dashboard::renderHTMLMenu("user_read",$CMS->lang['menu_user'], "?site=user");?>
                    <?if(\lib\input::arrayValue($CMS->vars, 'booking_page') || \lib\input::arrayValue($CMS->vars, 'pos_enabled')) { ?>
                        <?=\models\dashboard::renderHTMLMenu("work_schedule_read",$CMS->lang['menu_work_schedule'], "?site=work_schedule");?>
                        <?=\models\dashboard::renderHTMLMenu("shift_work_read",$CMS->lang['menu_shift_work'], "?site=shift_work");?>
                    <? } ?>
                    <?=\models\dashboard::renderHTMLMenu("staff_read",$CMS->lang['menu_staff'], "?site=staff");?>
                </ul>
            </li>
        <?}?>

        <!--Begin webiste-->
        <? if($CMS->vars['addon_website_enable'] == 1) { ?>
            <li class="with-sub" for="menu_promotion">
            <span>
                <i class="fa fa-gift"></i>
                <span class="lbl" id="menu_promotion"><?= $CMS->lang['menu_promotion']; ?></span>
            </span>
                <ul for="menu_promotion">
                    <?=\models\dashboard::renderHTMLMenu("redeem_read",$CMS->lang['menu_redeem'], "?site=redeem");?>
                    <?=\models\dashboard::renderHTMLMenu("giftcards_read",$CMS->lang['menu_giftcards'], "?site=giftcards");?>
                    <?=\models\dashboard::renderHTMLMenu("coupons_read",$CMS->lang['menu_coupons'], "?site=coupons");?>
                    <?if($CMS->vars['discount_code']){?>
                        <?=\models\dashboard::renderHTMLMenu("discount_read",$CMS->lang['menu_discount'], "?site=discount");?>
                    <?}?>
                </ul>
            </li>
            <li class="with-sub" for="menu_website">
                <span>
                    <i class="fa fa-globe"></i>
                    <span class="lbl" id="menu_store"><?= $CMS->lang['menu_website']; ?></span>
                </span>
                <ul for="menu_website">
                    <?=\models\dashboard::renderHTMLMenu("news_read",$CMS->lang['menu_news'], "?site=news");?>
                    <?=\models\dashboard::renderHTMLMenu("gallery_read",$CMS->lang['menu_gallery'], "?site=gallery");?>
                    <?=\models\dashboard::renderHTMLMenu("sms_read",$CMS->lang['menu_sms'], "?site=sms");?>
                    <?=\models\dashboard::renderHTMLMenu("seo_read",$CMS->lang['menu_seo'], "?site=seo");?>
                    <?=\models\dashboard::renderHTMLMenu("logos_read",$CMS->lang['menu_logos'], "?site=logos");?>
                    <?=\models\dashboard::renderHTMLMenu("interface_read",$CMS->lang['menu_interface'], "?site=interface");?>
                    <?=\models\dashboard::renderHTMLMenu("embed_read",$CMS->lang['menu_embed'], "?site=embed");?>
                </ul>
            </li>
        <? } ?>
        <!--End webiste-->
        <li class="with-sub" for="menu_config">
                <span>
                    <i class="fa fa-cogs" aria-hidden="true"></i>
                    <span class="lbl" id="menu_config"><?= $CMS->lang['menu_config']; ?></span>
                </span>
            <ul for="menu_config">
                <? if($CMS->vars['addon_goods_enable'] == 1) { ?>
                    <?=\models\dashboard::renderHTMLMenu("accounts_read",$CMS->lang['menu_accounts'], "?site=accounts");?>
                    <?=\models\dashboard::renderHTMLMenu("accounts_type_read",$CMS->lang['menu_accounts_type'], "?site=accounts_type");?>
                    <?=\models\dashboard::renderHTMLMenu("addons_read",$CMS->lang['menu_addons'], "?site=addons");?>
                <? } ?>
                <?=\models\dashboard::renderHTMLMenu("config_general_read",$CMS->lang['menu_settings'], "?site=config_general");?>
                <?=\models\dashboard::renderHTMLMenu("config_general_read",$CMS->lang['menu_payment'], "?site=config_general&tab=tabs-4-tab-8");?>

                <?if( $CMS->vars['type_web'] == "ecommerce" ){?>
                    <?=\models\dashboard::renderHTMLMenu("config_general_read",$CMS->lang['menu_shipping_fee'], "?site=config_general&act=shipping_fee");?>
                <?}?>

                <?if( $CMS->vars['type_web'] == "ecommerce" ){?>
                    <?=\models\dashboard::renderHTMLMenu("user_read",$CMS->lang['menu_user'], "?site=user");?>
                <?}?>
            </ul>
        </li>



        <?if(\lib\input::arrayValue($CMS->permit,'report_read')){?>
            <li class="with-sub" for="menu_report">
                <span>
                    <i class="fa fa-bar-chart" aria-hidden="true"></i>
                    <span class="lbl" id="menu_config"><?= $CMS->lang['menu_report']; ?></span>
                </span>
                <ul for="menu_report">
                    <?=\models\dashboard::renderHTMLMenu("report_sales",$CMS->lang['menu_report_invoice'], "?site=report&act=sales");?>
                </ul>
            </li>
        <? } ?>
    </ul>
</nav><!--.side-menu-->
<div class="page-content">
    <div class="container-fluid messenger" id="blockui-element-container-dark" style="zoom: 1;">

        <?php
        echo \core\ezy::$html->init_error();

        echo \core\ezy::$html->init_search($CMS->class->search->msg_return);

        // Module content
        echo $tpl->yield;

        ?>
    </div><!--.container-fluid-->
</div><!--.page-content-->

<?php
if($CMS->input['site'] == "order"){
    ?>


    <div class="control-panel-container" style="z-index: 10003;display: none">

        <header class="drawer-panel"><button class="drawer-close first-focus" aria-label="close"><span class="fa fa-close"></span></button></header>

        <h3 style="margin: 10px 45px 10px 10px;font-weight: 400;font-size:20px"><?=$CMS->lang['product_service_info'];?></h3>
        <span style="font-weight: 400; line-height: 25px;margin-left:10px"><?=$CMS->lang['product_service_type'];?></span>
        <ul>
            <li class="tasks" id="panel_add_service">
                <div class="control-item-header">
                    <a class="icon-toggle">
                        <span class="caret-down fa fa-caret-down"></span>
                        <span class="icon fa fa-tasks"></span>
                    </a>
                    <span class="text"><?=$CMS->lang['service_title_1'];?></span>
                </div>
                <div class="control-item-content" style="display: block;cursor: pointer;">
                    <div class="control-item-content-text">
                        <?=$CMS->lang['service_description_title_meta'];?>
                    </div>
                </div>
            </li>
            <li class="tasks" id="panel_add_product">
                <div class="control-item-header">
                    <a class="icon-toggle">
                        <span class="caret-down fa fa-caret-down"></span>
                        <span class="icon fa fa-tasks"></span>
                    </a>
                    <span class="text"><?=$CMS->lang['product_title_1'];?></span>
                </div>
                <div class="control-item-content" style="display: block;cursor: pointer;">
                    <div class="control-item-content-text">
                        <?=$CMS->lang['product_description_title_meta'];?>
                    </div>
                </div>
            </li>
            <li class="tasks" id="panel_add_assets">
                <div class="control-item-header">
                    <a class="icon-toggle">
                        <span class="caret-down fa fa-caret-down"></span>
                        <span class="icon fa fa-tasks"></span>
                    </a>
                    <span class="text"><?=$CMS->lang['assets_title_1'];?></span>
                </div>
                <div class="control-item-content" style="display: block;cursor: pointer;">
                    <div class="control-item-content-text">
                        <?=$CMS->lang['assets_description_title_meta'];?>
                    </div>
                </div>
            </li>
        </ul>

    </div>


    <div class="ha-underlay" tabindex="-1" aria-hidden="true" style="display:none;z-index: 10002;"></div>

<? } ?>
<script>
    /*$(document).ready(function(){
        $("#control-panel-toggle-open").trigger("click");
    })*/
</script>

<?=\lib\assets::generate(array(
    "jsacp/custom.js?201803081521",
    "assets/js/lib/ladda-button/spin.min.js",
    "assets/js/lib/ladda-button/ladda.min.js",
    "assets/js/lib/ladda-button/ladda-button-init.js",
    "jsacp/app_footer.js", // 03/07/2017
    "assets/js/lib/notie/notie.js", // 24/02/2017
), "js");?>
<?
// check xo menu
switch ($CMS->input['site']) {
    default:
        $check_ul = "";
        break;
    case "order":
    case "product":
    case "service":
    case "variants":
    case "product_group":
    case "config_price":
    case "manufacture":
    case "store":
    case "custom_status":
    case "abandoned":
        $check_ul = "menu_sell";
        break;
    case "customer":
    case "group_customer":
    case "supplier":
    case "manufacture":
    case "partner_delivery":
    case "contact":
        $check_ul = "menu_partner";
        break;
    case "store_request":
    case "inventory":
    case "returns":
    case "assets":
    case "shipment":
    case "store":
        $check_ul = "menu_goods";
        break;
    case "transactions":
        $check_ul = "menu_transactions";
        break;
    case "email":
    case "emailtpl":
    case "newsletter":
        $check_ul = "menu_marketing";
        break;
    case "news":
    case "pages":
    case "gallery":
    case "booking":
    case "giftcards":
    case "coupons":
    case "sms":
    case "interface":
    case "config_parent_news":
    case "config_gallery":
    case "position":
    case "redeem":
    case "seo":
    case "embed":
    case "discount":
    case "staff":
    case "logos":
        $check_ul = "menu_website";
        break;
    case "sites":
    case "user":
    case "accounts":
    case "accounts_type":
    case "config_general":
    case "addons":
        $check_ul = "menu_config";
        break;
    case "report":
        $check_ul = "menu_report";
        break;
    case "facebook":
        $check_ul = "manage_facebook";
        break;
} ?>
<script>
    var check_ul = "<?= $check_ul; ?>";
    $("ul.side-menu-list li[for='"+check_ul+"']").addClass("opened");
    $("ul.side-menu-list li[for='"+check_ul+"'] ul").css("display","block");
</script>
<script>

    $(document).ready(function () {
        $(".match-height>[class^=col-").matchHeight();
        $(".date-picker").datetimepicker({ format:'<?=$CMS->vars['date_format']?>', });
        $(".date-picker").mask("<?=$tpl->maskFormat?>", {placeholder: "<?=$tpl->maskPlaceHolder?>"});
        csrf_token();

        // Active color themes
        $(".box_color").each(function(){
            var color_c = "<?=\lib\input::arrayValue($CMS->vars, 'color_theme');?>";
            var color_active = $(this).attr("c_active");

            if(color_c == color_active)
            {
                if(color_active != "default")
                {
                    $(this).html("<span style='color:white'><i class='fa fa-check'></i></span>");
                }else
                {
                    $(this).html("<span style='color:black'><i class='fa fa-check'></i></span>");
                }

            }
        });
    });
</script>
</body>
</html>
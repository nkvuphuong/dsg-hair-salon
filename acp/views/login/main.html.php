<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>ACP <?= $CMS->core->page_title; ?></title>
    <link rel="shortcut icon" href="<?= $CMS->vars['img_url']; ?>/favicon.png">

    <?= \lib\assets::generate(
        array(
            "assets/css/lib/font-awesome/font-awesome.min.css",
            "assets/css/main.css",
            "assets/css/custom.css",
        ), "css"); ?>

    <?= \lib\assets::generate(
    array(
    "assets/js/plugins.min.js",
    "assets/js/app.js?201803081521",
    ), "js"); ?>

    <?=\views\layouts::google_recaptcha_header();?>
</head>
<body>
<!--Thông báo lỗi--->

<div class="page-center">
    <div class="page-center-in">
        <div class="container-fluid">
            <form id="ezylogin" class="sign-box" action="<?=$CMS->vars['root_domain'];?>/?site=login&act=do"  name="login" method="post">
                <header class="sign-title">Login</header>
                <? if($CMS->errormsg) { ?>
                <div class="form-error-text-block"><?=$CMS->errormsg;?></div>
                <? } ?>
                <div class="form-group">
                    <input type="text" class="form-control" name="name" id="name" autocomplete="off" value="" emsg="<?=$CMS->lang['incomplete_username'];?>" placeholder="<?=$CMS->lang['login_username'];?>"/>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control"  name="password" id="password" size="32" maxlength="32" autocomplete="off" emsg="<?=$CMS->lang['incomplete_password'];?>" placeholder="<?=$CMS->lang['login_password'];?>"/>
                </div>
                <div class="form-group">
                    <div class="checkbox float-left">
                        <input type="checkbox"   name="is_remember" id="is_remember" value="1" />
                        <label for="is_remember"><?=$CMS->lang['login_is_remember'];?></label>
                    </div>
                    <div class="float-right reset">
                    </div>
                </div>
                <input type="hidden" name="returnUrl" value="<?=\lib\input::get('returnUrl')?>">
                <button type="submit" class="btn btn-rounded <?=\views\layouts::google_recaptcha_form();?>" type="submit"><?=$CMS->lang['login_submit'];?></button>
            </form>
        </div>
    </div>
</div><!--.page-center-->

</body>
</html>
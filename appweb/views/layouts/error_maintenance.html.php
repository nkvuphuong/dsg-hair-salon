<html>
<head>
    <title><?=  $CMS->lang['site_offline']; ?></title>
</head>
<style type="text/css">
    body
    {
        font-family: tahoma;
        line-height: 150%;
    }
    .sql_error {width: 40%; margin: 5% auto; border:1px solid #ccc; padding: 20px;}
    .sql_error h3 {color:#F00; margin: 0px; padding: 0px; }
    .sql_error span {}

    .sql_error ul { border: dashed #808080 1px; margin: 0px; padding: 4px 4px 4px 24px; background: #e1edf7; list-style-type: circle; }
    .sql_error ul li {font-size: 14px;}
    .sql_error ul li span {text-decoration: underline; font-size: 12px;}

    .sql_error div.back { margin-bottom: 20px; font-size: 11px; }
    .sql_error div.copyright { width: 100%; text-align: right; font-size: 11px; font-color: #808080; margin-top: 10px; }

    /* Ipad/Iphone */
    @media only screen
    and (max-width: 480px) { .sql_error { width: 80%; margin: 5% auto; } }
</style>
<body>

    <div style="text-align: center;"><img src="<?=  $CMS->vars['root_domain']; ?>/app/assets/images/webunderconstruction.jpg"></div>

</body>
</html>
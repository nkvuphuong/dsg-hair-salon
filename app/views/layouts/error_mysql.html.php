<html>
<head>
    <title><?=  $CMS->lang['site_mysql_error']; ?></title>
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
<div class="sql_error">
    <h3><?=  $CMS->lang['site_mysql_error']; ?></h3>
    <div class="back"><a href="<?=  $CMS->vars['root_domain']; ?>">&laquo; <?=  $CMS->lang['site_back']; ?></a></div>
    <ul>
        <li><span>SQL</span>: <b><?=  $tpl->msg; ?></b></li>
        <li><span>Error</span>: <?php if ( isset($tpl->conn->errno) ) { echo $tpl->conn->errno . " " . $tpl->conn->error; } ?></li>
        <li><span>Date</span>: <?=  date("m/d/Y H:i:s a"); ?></li>
        <li><span>IP</span>: <?=  getenv("REMOTE_ADDR"); ?></li>
        <li><span>Script</span>: <?=  getenv("REQUEST_URI"); ?></li>
        <li><span>Referer</span>: <?=  getenv("HTTP_REFERER"); ?></li>
        <li><span>PHP&nbsp;Version</span>: <?=  PHP_VERSION; ?></li>
        <!--<li><span>Browser</span>: <?=  getenv("HTTP_USER_AGENT"); ?></li>
        <li><span>OS</span>: <?=  PHP_OS; ?></li>
        <li><span>Server</span>: <?=  getenv("SERVER_SOFTWARE"); ?></li>
        <li><span>Server&nbsp;Name</span>: <?=  getenv("SERVER_NAME"); ?></li>-->
    </ul>

<!--    <div class="copyright"><i>--><?//=  $CMS->lang['site_copyright']; ?><!--</i></div>-->
</div>
</body>
</html>
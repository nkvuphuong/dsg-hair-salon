<?php

namespace views;

use \core\ezy;

class layouts
{
    /**
     * Facebook comment widget
     * @param $url
     * @return string
     */

    static public function facebook_comment($url)
    {
        /*<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.9";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>*/

        return <<<EOF
<!-- Start Facebook comment -->
<div class="fb-comments" data-href="{$url}" data-width="100%" data-numposts="5"></div>
<!-- End Facebook comment -->
EOF;
    }

    /**
     * Google ReCaptcha HTML Generator
     * @return string: html to integrate with form
     */

    static public function google_recaptcha_header($form_name = "ezylogin", $is_submit = 1)
    {
        global $CMS;

        $lang = $CMS->vars['default_language'] == "vn" ? "vi" : "en";

        //Fix loi dau '-' vi function khong duoc chua dau nay - nkvp - 2017.11.27
        $fnName = str_replace('-','_', $form_name);

        $output = "";

        if ( ezy::$google_recaptcha_defined == false )
        {
            $output .= <<<EOF
<script src='https://www.google.com/recaptcha/api.js?hl={$lang}' async defer></script>
EOF;
            ezy::$google_recaptcha_defined = true;
        }


$output .= <<<EOF
<!-- Google reCaptcha -->
<script langauge="javascript">
<!--
    function ezyCaptcha_{$fnName}(token, is_submit) 
    {
        is_submit={$is_submit};
        if ( $("#password").length )
        {
            //$("input:password").val(md5(clean_input($("#password").val())));
        }
EOF;
        if(isset($CMS->vars['is_admin_module']) && intval($CMS->vars['is_admin_module'])==1)
        {
$output .=<<<EOF

        if(is_submit)
        {
            $('#{$form_name}').submit();
        }

EOF;

        }

$output .=<<<EOF

        return true;
    }
//-->
</script>
EOF;

        return $output;
    }

    static public function google_recaptcha_form($form_name = "ezylogin")
    {
        global $CMS;

        //Fix loi dau '-' vi function khong duoc chua dau nay - nkvp - 2017.11.27
        $fnName = str_replace('-','_', $form_name);

        if( $CMS->vars['recaptcha_google'] )
        {
        return <<<EOF
    g-recaptcha" data-sitekey="{$CMS->vars['gg_sitekey']}" data-callback="ezyCaptcha_{$fnName}
EOF;
        }
    }

    /**
     * @param $url
     */

    public function redirect($url)
    {
        global $CMS, $DB;

        $page = intval($CMS->input["page"]);

        if ($page > 1 AND $CMS->is_error == 1) {
            $page = $page - 1;
        }

        $page = $page > 1 ? "&page={$page}" : "";

        if ($_SESSION["url_return"]) {
            $url_return = "&act=search_do{$_SESSION["url_return"]}";

            unset($_SESSION["url_return"]);
        }

        $CMS->class->session->save();

        header("location: {$url}{$page}{$url_return}");
        exit;
    }

    /**
     * @param $msg: Message to output
     * @param string $page: Redirect to
     *
     */

    public function page_transfer($msg, $page = "index.php")
    {
        global $CMS;

        $page_transfer = $page;

        print <<<EOF
<html>
<head>
<title>{$CMS->vars['website_title']}</title>
<link rel="stylesheet" href="acp/assets/css/lib/font-awesome/font-awesome.min.css">
<link href="../sell/css/style.css" rel="stylesheet" type="text/css" />
<meta http-equiv="refresh" content="2; url={$page}">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<div class="page-center-in">
		<div class="container-fluid">
			<div class="page_transfer">
			{$msg}
				<p style="color: #009aff;">
					<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
				</p>
				<p><a href="{$page_transfer}">{$CMS->lang['page_transfer']}</a></p>
			</div>
		</div>
	</div>
</body>
</html>
EOF;
        exit;
    }
}

?>
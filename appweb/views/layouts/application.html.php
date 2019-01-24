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
	<?=\lib\assets::generate(array(
		"appweb/assets/css/fonts.css",
		"appweb/assets/css/bootstrap.min.css",
		"appweb/assets/css/dropdown-submenu.css", 
		"appweb/assets/css/owl.carousel.min.css", 
		"appweb/assets/css/tippy.css",
		"appweb/assets/css/bigSlide.css",
		"appweb/assets/css/style.css",
		"appweb/assets/css/responsive.css",
	), "css")?>
	<!-- font awesome -->
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

	<!-- Css Extends -->
	<style type="text/css">
        <?=html_entity_decode(\core\ezy::tpl("css", "layouts"));?>
    </style>

    <!-- Js Vars -->
    <script type="text/javascript" >
        var site_root_domain = "<?= $CMS->vars['root_domain']; ?>";
        var currency_type = "<?= $CMS->vars['currency_type']; ?>";
        var storename_domain =  "<?=$CMS->vars['storename_domain'];?>";
    </script>

	<!-- js -->
	<?=\lib\assets::generate(array(
		"appweb/assets/js/jquery.min.js",
		"appweb/assets/js/bootstrap.min.js",
		"appweb/assets/js/owl.carousel.min.js", 
		"appweb/assets/js/bootstrap-hover-dropdown.js",
		"appweb/assets/js/tippy.js",
		"appweb/assets/js/slideout.min.js",
		"appweb/assets/js/notify.js",
	), "js")?>

	<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Facebook Pixel Code -->
	<script>
	  !function(f,b,e,v,n,t,s)
	  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
	  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
	  n.queue=[];t=b.createElement(e);t.async=!0;
	  t.src=v;s=b.getElementsByTagName(e)[0];
	  s.parentNode.insertBefore(t,s)}(window, document,'script',
	  'https://connect.facebook.net/en_US/fbevents.js');
	  fbq('init', '159480441314678');
	  fbq('track', 'PageView');
	</script>
	<noscript><img height="1" width="1" style="display:none"
	  src="https://www.facebook.com/tr?id=159480441314678&ev=PageView&noscript=1"
	/></noscript>
	<!-- End Facebook Pixel Code -->
</head>
<body data-spy="scroll" data-target="#fly-menu">

    <!-- mobilemenu -->
    <nav id="mobilemenu" class="menu slideout-menu slideout-menu-left">
    	<button class="js-slideout-close"><span class="close-btn"></span></button>
    	<a class="main-logo" href="/" title="Home">
			<img src="images/logo_web4s_2.png" height="30" alt="Demo Page">
		</a>
      	<ul class="nav navbar-nav dark-theme ">
      		<? if (in_array(\core\ezy::$site, array('client', 'tai-khoan', 'tai-khoan.html'))) { 
				echo(core\ezy::tpl('menu_mobile', 'client'));
			} else {
				// if ($CMS->vars['is_login'] == 1) {
				// 	echo('<li class="active"><a href="tai-khoan.html">' . $_SESSION['member']['cus_email'] . '</a></li>');
				// } else {
				// 	echo('<li class="active"><a href="/login">' . $CMS->lang['login'] . '</a></li>');
				// }
				echo(core\ezy::tpl('menu_mobile', 'layouts'));
			} ?>
	    	<li>
	    		<?if( ! in_array( \core\ezy::$site, array('client', 'tai-khoan', 'tai-khoan.html') ) ){ if( $CMS->vars['is_login'] == 1 ){?>
	    		<a href="/tai-khoan.html" style="text-decoration:none;padding:0px;margin:0px;">
	    			<input type="button" class="btn btn-outline-1 ani" style="font-size:12px; margin-top:10px;text-transform: uppercase;" value="<?=$_SESSION['member']['cus_email'];?>">
	    		</a>

	    		<?}else{?>
	    		<a href="/login" style="text-decoration:none;padding:0px;margin:0px;">
	    			<input type="button" class="btn btn-outline-1 ani" style="font-size:12px; margin-top:10px;text-transform: uppercase;" value="<?=$CMS->lang['login'];?>">
	    		</a>
	    		<?}}?>
	    		<a href="/dang-ky-dung-thu-website.html" style="text-decoration:none;padding:0px;margin:0px;">
	    			<input type="button" class="btn btn-outline-1 btn-orange ani" style="font-size:12px; margin-top:10px" value="TRẢI NGHIỆM MIỄN PHÍ"><!-- data-toggle="modal" data-target="#myModalRegisterTrial" -->
	    		</a>
	    	</li>
      	</ul>
    </nav><!-- End mobilemenu -->

	<!-- main-content -->
    <div id="maincontent" class="maincontent">

		<!-- Desktop menu -->
	    <nav class="main-nav navbar navbar-fixed-top">
	      	<div class="container">
	        	<div class="navbar-header">
					<button type="button" class="navbar-toggle js-slideout-toggle">
		            	<span class="sr-only">Toggle navigation</span>
		            	<span class="icon-bar"></span>
		            	<span class="icon-bar"></span>
		            	<span class="icon-bar"></span>
		         	</button>
					<a class="main-logo" href="/" title="Home">
						<img src="images/logo_web4s.png" alt="Demo Page">
					</a>
	        	</div>
	        	<div id="navbar" class="collapse navbar-collapse">
	        		<? if (in_array(\core\ezy::$site, array('client', 'tai-khoan', 'tai-khoan.html'))) { ?>
					<ul class="nav navbar-nav">
						<?=\core\ezy::tpl('menu_desktop', 'client');?>
					</ul>
					<ul class="nav navbar-nav pull-right">
						<li class="dropdown contain-user"> 
							<a href="/tai-khoan.html" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
								<div class="user-avartar">
									<img class="img-responsive" src="images/fa-user.jpg">
								</div>
								<?=$_SESSION['member']['cus_email'];?>
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<li><a href="/login/logout"><?=$CMS->lang['logout']?></a></li>
							</ul>						
						</li>
					</ul>

					<? } else { ?>
					<ul class="nav navbar-nav">
						<?=\core\ezy::tpl('menu_desktop', 'layouts');?>
					</ul>
					<ul class="nav navbar-nav pull-right">
						<?php if ($CMS->vars['is_login'] == 1) { ?>
						<li>
							<a href="/tai-khoan.html" style="text-decoration:none;padding:0px;margin:0px;">
								<input type="button" class="btn btn-outline-1 ani" value="KHÁCH HÀNG">
							</a>
						</li>
						<?php } else { ?>
						<li>
							<a href="/login" style="text-decoration:none;padding:0px;margin:0px;">
								<input type="button" class="btn btn-outline-1 ani" value="ĐĂNG NHẬP">
							</a>
						</li>
						<?php } ?>
						<li>
							<a href="/dang-ky-dung-thu-website.html" style="text-decoration:none;padding:0px;margin:0px;">
			          			<input type="button" class="btn btn-outline-1 btn-orange ani" value="TRẢI NGHIỆM MIỄN PHÍ"><!--  data-toggle="modal" data-target="#myModalRegisterTrial" -->
			          		</a>
			          	</li>
					</ul>
					<? } ?>
			    </div>
			</div>
		</nav><!-- End Desktop menu -->
		
		<!-- main-container --> 
    	<div id="maincontent" class="main-container">
    		<?=$tpl->yield;?>		
    	</div>
    	<!-- End main-container -->

    	<!-- main-footer -->
	    <footer id="main-footer">
	    	<? if (\core\ezy::$site == 'client') { ?>
			<? } else {
				echo(\core\ezy::tpl("footer", "layouts"));
			} ?>
	    </footer>
	</div><!-- End main-content -->
	
	<!-- Help -->
	<?=\core\ezy::render('help', 'layouts');?>
	
	<!-- Fly menu -->
	<?if( (in_array(\core\ezy::$site, array('pricing', 'bao-gia-thiet-ke-web', 'bao-gia-thiet-ke-web.html')) AND in_array(\core\ezy::$act, array('ecommerce_website', 'website-ban-hang', 'website-ban-hang.html', 'real_estate_website', 'website-bat-dong-san', 'website-bat-dong-san.html', 'business_website', 'website-doanh-nghiep', 'website-doanh-nghiep.html'))) OR ( in_array(\core\ezy::$site, array('bang-bao-gia-thiet-ke-web', 'bang-bao-gia-thiet-ke-web.html')) ) ){?>
	<?=\core\ezy::render('fly_menu', 'layouts');?>
	<?}?>

	<!-- Popup Free trial -->
	<?=\core\ezy::render('popup_register_trial', 'layouts');?>

    <!-- Login Form -->
	<?=(\core\ezy::$site == 'login') ? \core\ezy::render('nhanhoa_form', 'login') : '';?>

	<!-- popup re-call -->
	<?=\core\ezy::render('popup_recall', 'layouts');?>

	<!-- popup register content services -->
	<?=\core\ezy::render('popup_register_content_services', 'layouts');?>

	<!-- Js -->
	<?=\lib\assets::generate(array(
		"appweb/assets/js/main.js",
	), "js")?>

	<!-- Js Extends -->
	<script type="text/javascript">
		<?=html_entity_decode(\core\ezy::tpl("script", "layouts"), ENT_QUOTES | ENT_XML1, 'UTF-8');?>
	</script>

	<?php if($_SESSION['is_mobile'] == false) { ?>
	<!-- Live chat -->
	<div id='laPlaceholder'></div>
	<script type="text/javascript">
	(function(d,t) {
	var script = d.createElement(t); script.id = 'la_x2s6df8d'; script.async = true;
	script.src = 'https://liveonline.nhanhoa.com/scripts/track.js';
	var image = d.createElement('img'); script.async = true;
	image.src = 'https://liveonline.nhanhoa.com/scripts/pix.gif';
	script.onload = script.onreadystatechange = function() {
	var rs = this.readyState; if (rs && (rs != 'complete') && (rs != 'loaded')) return;
	LiveAgentTracker.createButton('bb2947ef', this);
	};
	var placeholder = document.getElementById('laPlaceholder');
	placeholder.parentNode.insertBefore(script, placeholder);
	placeholder.parentNode.insertBefore(image, placeholder);
	placeholder.parentNode.removeChild(placeholder);
	})(document, 'script');
	</script>
	<div id='laPlaceholder'></div>
	<script type="text/javascript">
	(function(d,t) {
	var script = d.createElement(t); script.id = 'la_x2s6df8d'; script.async = true;
	script.src = 'https://liveonline.nhanhoa.com/scripts/track.js';
	var placeholder = document.getElementById('laPlaceholder');
	placeholder.parentNode.insertBefore(script, placeholder);
	placeholder.parentNode.removeChild(placeholder);
	})(document, 'script');
	</script>
	<?php } ?>
	<!-- Global site tag (gtag.js) - Google Analytics - 231017 -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-27415423-3"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', 'UA-27415423-3');
	</script>
    
    <!-- use for debug themes load -->
    <?if( !empty($tpl->themesKeyForGetCodeList) ){ foreach( $tpl->themesKeyForGetCodeList as $key => $data ){?>
    <meta name="debug-<?=$key;?>" content="<?=$data;?>">
    <?}}?>
</body>
</html>
<?php
    //Tam dong trang chu//////

    if(!in_array($_SERVER['REMOTE_ADDR'],array("1.54.136.60","162.158.178.171")))
    {
       // exit "deny from all";
    }
    
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Template</title>

    <base href="/app/assets/">

    <link href="img/favicon.144x144.png" rel="apple-touch-icon" type="image/png" sizes="144x144">
    <link href="img/favicon.114x114.png" rel="apple-touch-icon" type="image/png" sizes="114x114">
    <link href="img/favicon.72x72.png" rel="apple-touch-icon" type="image/png" sizes="72x72">
    <link href="img/favicon.57x57.png" rel="apple-touch-icon" type="image/png">
    <link href="img/favicon.png" rel="icon" type="image/png">
    <link href="img/favicon.ico" rel="shortcut icon">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link rel="stylesheet" href="css/default.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <script src="js/main.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/jquery.magnific-popup.js"></script>
    

</head>
<body>

<header class="site-header-container">
    <div class="site-header">
        <div class="site-header-collapsed">
            <div class="site-header-collapsed-in">
                <div class="container">
                    <div class="site-logo">
                        <a href="/" title="">
                            <img src="content/logo.png" alt=""/>
                        </a>
                    </div>
                    <div class="site-header-right">

                        <nav class="site-menu" id="page-nav">
                            <ul>
                                <li><a href="/#section-1"><span>Modules Chính</span></a></li>
                                <li><a href="/#section-2"><span>Ưu điểm</span></a></li>
                                <li><a href="/#section-3"><span>Bảng giá</span></a></li>
                                <li><a href="/contact"><span>Liên hệ</span></a></li>
                            </ul>
                        </nav>
                        <a href="/#" class="btn btn-sm btn-fill">Đặt mua</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="site-header-clone">
            <div class="container">
                <div class="site-logo">
                    <a href="/#" title="">
                        <img src="content/logo.png" alt=""/>
                    </a>
                </div>
                <button type="button" class="burger">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </div>
</header>

<?php

    if($_SESSION['error_msg'] OR $_SESSION['error_msg'] != "" )
    {
            echo "<section>".$_SESSION['error_msg']. " </section>";
            unset($_SESSION['error_msg'] );
    }
?>

    



<?= $tpl->yield; ?>

<footer class="site-footer">
    <section class="footer-bottom">
        <div class="container">
            <div class="copy">Copyright ® 2017 EZYBOOK. All Rights Reserved.</div>
        </div>
    </section>
</footer>

</body>
</html>
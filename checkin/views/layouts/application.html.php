<!DOCTYPE html>
<html>
<head lang="en">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autom - Phần mềm quản lý toàn diện doanh nghiệp bán lẻ đa kênh tự động</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:100,400,700,800&amp;subset=vietnamese"
          rel="stylesheet">
    <base href="/checkin/assets/" target="_blank">
    <link href="../../../node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../../node_modules/pnotify/dist/PNotifyBrightTheme.css" rel="stylesheet">
    <link href="../../../node_modules/jquery-easy-loading/dist/jquery.loading.min.css" rel="stylesheet">
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="css/ie10-viewport-bug-workaround.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="../../../node_modules/jquery/dist/jquery.min.js"></script>
    <script src="../../../node_modules/popper.js/dist/umd/popper.min.js"></script>
    <script src="../../../node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../../../node_modules/pnotify/dist/umd/PNotify.js"></script>
    <script src="../../../node_modules/jquery-easy-loading/dist/jquery.loading.min.js"></script>
</head>
<body class="body-autom">
<? if ($logo = \lib\input::checkImage("attach/{$CMS->vars['logo_checkin']}", "")) { ?>
    <header>
        <div class="container">
            <div class="col-md-12 autom-navbar-center-wrapper">
                <div class="navbar-header-center">
                    <a href="../" class=""><img style="max-width: 400px" class="main-logo" src="<?= $logo ?>"></a>
                </div>
            </div>
        </div>
    </header>
<? } ?>
<section class="autom-section autom-present-section">
    <div class="container address-wrap">
        <?= $tpl->yield ?>
    </div>
</section>

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <p>Bấm OK để bật full màn hình (Không hoạt động trên các thiết bị dùng IOS)</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal" onclick="openFullscreen()">OK</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<script>
    $(document).ready(function() {
        $("#myModal").modal('show');
    })
</script>

</body>
</html>
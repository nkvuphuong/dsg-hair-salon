<div class="container">
    <div class="row">
        <div class="col-xs-6 col-sm-3 col-md-6">
            <a href="<?=$CMS->vars['link_facebook'];?>" class="social-switch-icon-v1">
                <i><img src="images/fb-icon-1.png"></i>
                <i><img src="images/fb-icon-2.png"></i>
            </a>
            <a href="https://zalo.me" class="social-switch-icon-v1">
                <i><img src="images/zalo-icon-1.png"></i>
                <i><img src="images/zalo-icon-2.png"></i>
            </a>
            <a href="<?=$CMS->vars['link_youtube'];?>" class="social-switch-icon-v1">
                <i><img src="images/youtube-icon-1.png"></i>
                <i><img src="images/youtube-icon-2.png"></i>
            </a>                    
        </div>
        <div class="col-xs-6 col-sm-3 col-md-6 visible-xs">
                <a href="/dat-lich-hen" class="pull-right">
                <i class='fa fa-calendar'></i>   
                Đặt lịch hẹn</a>    
        </div>
        <div class="col-xs-6 col-sm-9 col-md-6 mobile-top-right text-right">
            <?=\core\ezy::render('menu_top_main', 'layouts');?>
        </div>
        <div class="col-xs-12 visible-xs">
            <?=\core\ezy::render('menu_top_mobile', 'layouts');?>
        </div>
    </div>
</div>
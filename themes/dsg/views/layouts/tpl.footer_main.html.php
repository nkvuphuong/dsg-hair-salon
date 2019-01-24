<div class="container footer-wrapper">
    <div class="row">
        <div class="col-sm-6 col-md-3">
            <h3>Về Dũng Sài Gòn</h3>
            <div class="icon-div">
                <p>
                    TRỤ SỞ CHÍNH: <br>
                    <b><?=$CMS->vars['company_address'];?></b>
                </p>
            </div>
            <div class="icon-div phone">
                <p>
                    ĐT : <a href="tel:<?=$CMS->vars['company_phone'];?>"><?=$CMS->vars['company_phone'];?></a> - <a href="tel:<?=$CMS->vars['company_phone2'];?>"><?=$CMS->vars['company_phone2'];?></a>
                </p>
            </div>
           <p>Hệ thống Salon:</p>
            <div class="form-group">

                <select class="form-control input-with-right-icon" id="city_id_footer">
                       <?=\core\ezy::render('option_city', 'layouts');?> 
                 </select>
                <span class="fa fa-caret-down right-icon-input"></span>
            </div>
            
            <div class="form-group">
                <select class="form-control input-with-right-icon" name="salon_select" value="" id="liststore_footer">
                    <option>Hệ thống Salon</option>
                      
                </select>
                <span class="fa fa-caret-down right-icon-input"></span>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <h3>Dịch vụ nổi bật</h3>
            <?=\core\ezy::render('footer_service_hot', 'layouts');?>
        </div>
        <div class="col-sm-6 col-md-3">
            <h3>Tin tức tiêu biểu</h3>
            <?=\core\ezy::render('footer_news_hot', 'layouts');?>
        </div>
        <div class="col-sm-6 col-md-3">
            <h3>MXH & Ứng dụng</h3>
            <div class="social-footer-main">
                <a href="https://www.facebook.com/" class="social-switch-v1">
                    <span class="social-switch-icon-v1">
                        <i><img src="images/fb-icon-1.png"></i>
                        <i><img src="images/fb-icon-2.png"></i>
                    </span>
                    <span class="text">Connect to Facebook</span>
                </a>
                <a href="https://zalo.me/pc" class="social-switch-v1">
                    <span class="social-switch-icon-v1">
                        <i><img src="images/zalo-icon-1.png"></i>
                        <i><img src="images/zalo-icon-2.png"></i>
                    </span>
                    <span class="text">Connect to Zalo Page</span>
                </a>
                <a href="https://www.youtube.com" class="social-switch-v1">
                    <span class="social-switch-icon-v1">
                        <i><img src="images/youtube-icon-1.png"></i>
                        <i><img src="images/youtube-icon-2.png"></i>
                    </span>
                    <span class="text">Subscribe Youtube</span>
                </a>
            </div>    
            <a href="https://www.apple.com/ios/app-store" class="store-icon">
                <img src="images/appstore.png">
            </a>
            <a href="https://store.google.com" class="store-icon">
                <img src="images/googlestore.png">
            </a>
        </div>
    </div>
</div>


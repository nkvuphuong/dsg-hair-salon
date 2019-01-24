<div class="footer-copyright">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-md-6 hidden-xs">
                <?=\core\ezy::render('logo', 'layouts');?>
            </div>
            <div class="col-sm-6 col-md-6 footer-copyright-text">
                <p style="font-size: 13px;">© Copyright by DungSG</p>
                <p style="font-size: 11px;">Made with love for great people.</p>
            </div>
        </div>
    </div>
</div>

    <!-- BOTTOM BAR -->
    <div class="bottom-bar">
        <a href="tel://<?=$CMS->vars['company_phone'];?>" class="bottom-bar-left">
            <span class="bottom-bar-text">ĐẶT LỊCH HẸN</span>
            <span class="bottom-bar-phone"><b><?=$CMS->vars['company_phone'];?></b></span>
        </a>
        <a href="<?=$CMS->vars['link_facebook'];?>" title="Gửi tin nhắn cho chúng tôi trên Facebook" class="bottom-bar-mesenger">
        </a>
    </div>
    <!-- END BOTTOM BAR -->
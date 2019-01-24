<ul class="nav navdsg navbar-nav nav-main menu_main">
    <li class="dropdown">
        <a itemprop="url" href="/service" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span itemprop="name">Dịch vụ</span><span class="caret"></span></a>
        <ul class="dropdown-menu pull-left">
            <?=\core\ezy::render('service_category', 'layouts');?>
        </ul>
    </li>
    <li>
        <a itemprop="url" href="/p/price"><span itemprop="name">Bảng giá</span></a>
    </li>
    <li>
        <a href="/salon"><span itemprop="name">Salon</span></a>
    </li>
    <li>
        <a itemprop="url" href="/p/educate"><span itemprop="name">Đào tạo</span></a>
    </li>
    <li>
        <a itemprop="url" href="/product"><span itemprop="name">Mỹ phẩm</span></a>
    </li>
</ul>

 <ul class="nav navbar-nav navbar-nav-right pull-right">
     <?=\core\ezy::render('login_cart_info', 'layouts');?>
    <li><a class="nav-btn" href="/book">đặt lịch hẹn</a></li>
</ul>

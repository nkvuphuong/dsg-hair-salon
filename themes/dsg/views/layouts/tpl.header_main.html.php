<div class="container">
    <div class="row">
        <div class="col-md-12 text-center">
            <div class="top-logo">
                <?=\core\ezy::render('logo', 'layouts');?>
            </div>
        </div>
    </div>
</div>
<div class="menu_mobile_v1 hidden-md hidden-lg">
    <div class="mobile_menu_container_v1">
        <div class="mobile_logo"></div>
        <?=\core\ezy::render('menu_mobile', 'layouts');?>
    </div>
</div>
<nav class="navbar main-nav hidden-sm hidden-xs ">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?=\core\ezy::render('menu_main', 'layouts');?>
            </div>
        </div>
    </div>
</nav>
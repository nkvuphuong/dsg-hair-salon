 
<div class="box-payment bottom">
    <div class="container">

        <div class="col-md-12 text-center">
                  <h1>Đơn hàng</h1>
        </div>
        <!-- Payment -->
        <div class="row"> 
            <?if( isset($_SESSION['member']) AND $CMS->vars['is_login'] == 1 ){?>
            <div class="col-sm-8"><?=\core\ezy::render('payment_form', 'payment');?></div>
            <?}else{?>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-6 mlr-auto panel-account"><?=\core\ezy::render('login_form', 'login');?></div>
                </div>    
            </div>
            <?}?>
            <div class="col-sm-4">
                <?=\core\ezy::render('cart_info', 'payment');?>
                <figure class="note">
                    <?=\core\ezy::tpl('payment_question_answer_ship', 'payment');?>
                </figure>
            </div>
        </div>
    </div>
</div>

<div style="margin-bottom:10px"></div>
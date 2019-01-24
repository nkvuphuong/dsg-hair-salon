<?if( !empty($tpl->data) ){
global $CMS;
$link = $CMS->vars['root_domain'].$_SERVER[REQUEST_URI];
    ?>
<section class="service-detail">
    <div class="container">
        <div class="row section-title-wrapper" style="padding-top: 0">
            <div class="col-md-12 text-center">
                <h1 class="mTitle" itemprop="name"><?=$tpl->data['name'];?></h1>
            </div>
        </div>
        <div class="row" style="padding-bottom: 20px;">
            <div class="visible-lg col-lg-1">
            </div>
            <div class="col-md-6 col-lg-5 top-edu-img-wrap" style="padding-top: 30px;padding-bottom: 25px;">
                <img src="<?=\lib\input::getThumb( $tpl->data['pathUpload'], 555);?>" alt="<?=$tpl->data['product_name'];?>">
            </div>
            <div class="visible-sm col-sm-1"></div>
            <div class="col-sm-10 col-md-6 col-lg-5 right-col" style="padding-top: 30px;padding-bottom: 25px;">
                <div style="line-height: 22px;margin-bottom: 15px;margin-top: 15px;"><?=$tpl->data['description_ori'];?></div>
                <div class="addthis_toolbox addthis_default_style addthis_32x32_style pull-right">
                  <a class="addthis_button_facebook"></a>
                    <div class="zalo-share-button" data-href="<?=$link;?>" data-oaid="<?=$CMS->vars['zalo_officical_account'];?>" data-layout="2" data-color="blue" data-customize="true" style="float:right;padding-left:10px;cursor: pointer" > <img alt="Chia sẻ zalo" title="Chia sẻ zalo" src="images/sharezalo.png" width="32" height="32"/> </div>
                    <script src="https://sp.zalo.me/plugins/sdk.js"></script> 
                </div>
                <p style="color:#e4e426;font-size:16px;font-weight: bold;"><?=$tpl->data['price_sell'];?></p>
                <a href="/cart/addcart/<?=$tpl->data['id'];?>" class="btn btn-md btn-primary" style="margin-right: 10px;">THÊM VÀO GIỎ HÀNG</a>

            </div>
            <div class="visible-sm col-sm-1"></div>
            <div class="visible-lg col-lg-1"></div>
        </div>
    </div>
     
    <div class="container">
        <div class="mContent"><?=$tpl->data['information_1'];?></div>
    </div>
</section>
<?}?>
<?if( !empty($tpl->data) ){
global $CMS;
$link = $CMS->vars['root_domain'].$_SERVER[REQUEST_URI];
?>
<?if( !empty($tpl->data) ){?>
<section class="service-detail">
    <div class="container">
        <div class="row section-title-wrapper" style="padding-top: 0">
            <div class="col-md-12 text-center">
                <h1 class="mTitle" itemprop="name"><?=$tpl->data['product_name'];?></h1>
            </div>
        </div>
        <div class="row" style="padding-bottom: 20px;">
            <div class="visible-lg col-lg-1">
            </div>
            <div class="col-md-6 col-lg-5 top-edu-img-wrap" style="padding-top: 30px;padding-bottom: 25px;">
                <img src="<?=\lib\input::getThumb( $tpl->data['uploadPath'], 555);?>" alt="<?=$tpl->data['product_name'];?>">
            </div>
            <div class="visible-sm col-sm-1"></div>
            <div class="col-sm-10 col-md-6 col-lg-5 right-col" style="padding-top: 30px;padding-bottom: 25px;">
                <div style="line-height: 22px;margin-bottom: 15px;margin-top: 15px;"><?=$tpl->data['product_description'];?></div>
               
                 <div class="addthis_toolbox addthis_default_style addthis_32x32_style pull-right">
                  <a class="addthis_button_facebook"></a>
                    <div class="zalo-share-button" data-href="<?=$link;?>" data-oaid="<?=$CMS->vars['zalo_officical_account'];?>" data-layout="2" data-color="blue" data-customize="true" style="float:right;padding-left:10px;cursor: pointer" > <img alt="Chia sẻ zalo" title="Chia sẻ zalo" src="images/sharezalo.png" width="32" height="32"/> </div>
                    <script src="https://sp.zalo.me/plugins/sdk.js"></script> 
                </div>


            </div>
            <div class="visible-sm col-sm-1"></div>
            <div class="visible-lg col-lg-1"></div>
        </div>
    </div>

    <?php 
    if(count($tpl->data['product_gallery']) > 0)
    {
    ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h2 style="margin-bottom: 15px;">Bộ sưu tập</h2>
            </div>
            <?foreach( $tpl->data['product_gallery'] as $gallery ){?>
            <div class="col-sm-6 col-md-3">
                <a class="gallery-item image-magnific-popup" data-group="service-<?=$tpl->data['product_id'];?>" title="<?=$tpl->data['product_name'];?>" href="<?=\lib\input::getThumb( $gallery );?>">
                    <span class="service-div">
                        <span class="service-div-bg" style="background-image: url('<?=\lib\input::getThumb( $gallery, 555);?>')"></span>
                        <span class="view-icon"><i class="fa fa-search "></i></span>
                    </span>
                </a>
            </div>
            <?}?>
        </div>  
    </div>
    <? } ?>
    <div class="container">
        <div class="mContent"><?=$tpl->data['product_information_1'];?></div>
    </div>
</section>
<? } } ?>
<?=\core\ezy::render('why_choose_us', 'layouts');?>
<?=\core\ezy::render('testimonitals', 'layouts');?>
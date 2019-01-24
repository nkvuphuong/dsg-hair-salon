<?if( !empty($tpl->dataListGallery) ){?>
<div class="row">
    <?
    $i=0;
    foreach( $tpl->dataListGallery as $data ){

        $i++;
    ?>
    <div class="<?=( $i > 2 )? 'col-sm-6 col-md-3' : 'col-sm-12 col-md-6' ?>">
       <div class="wrapper-gallery" style="position: relative;">
            <a class="gallery-item image-magnific-popup" data-group="gallery-<?=$data['name'];?>" title="<?=$data['image_alt'];?>" href="<?=\lib\input::getThumb($data['pathUpload']);?>">
                <span class="service-div">

                    <span class="service-div-bg" style="background-image: url('<?=\lib\input::getThumb($data['pathUpload'],555);?>')"></span>
                    <span class="view-icon"><i class="fa fa-search "></i></span>
                </span>
            </a>
            <div class="info" style="position: absolute;  bottom: 0; width: 100%;">    
                  <div class="price" style="text-align: center;margin-bottom:10px">
                    <span class="price-well" ><?=$data['image_alt'];?></span>
                  </div>   
            </div>
        </div>
    </div>
    <?}?>
</div>
<div class="gallery-paging text-right"><?=\core\ezy::render('paging', 'layouts');?></div>
<?}?>
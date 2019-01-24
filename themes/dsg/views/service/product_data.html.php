<?if( !empty($tpl->dataListProduct) ){?>
<div class="row">
    <?$data = $tpl->dataListProduct[0]; unset($tpl->dataListProduct[0]);?>
    <div class="col-sm-12 col-md-6">
        <div class="service-div">
            <div class="service-div-bg" style="background-image: url('<?=\lib\input::getThumb( $data['pathUpload'], 555);?>')"></div>
            <div class="service-div-detail">
                <div class="service-name"><?=$data['name'];?></div>
                <div class="service-div-detail-text">
                    <div class="description"><?=$data['description'];?></div>
                    <a href="<?=$data['url'];?>" class="btn btn-default">XEM CHI TIẾT</a>
                </div>
            </div>
        </div>
    </div>
    
    <?foreach( $tpl->dataListProduct as $data ){?>
    <div class="col-sm-6 col-md-3">
        <div class="service-div">
            <div class="service-div-bg" style="background-image: url('<?=\lib\input::getThumb( $data['pathUpload'], 555);?>')"></div>
            <div class="service-div-detail">
                <div class="service-name"><?=$data['name'];?></div>
                <div class="service-div-detail-text">
                    <div class="description"><?=$data['description'];?></div>
                    <a href="<?=$data['url'];?>" class="btn btn-default">XEM CHI TIẾT</a>
                </div>
            </div>
        </div>
    </div>
    <?}?>
</div>
<?}?>

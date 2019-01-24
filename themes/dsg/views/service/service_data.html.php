<?if( !empty($tpl->serviceNews) ){?>
<div class="row">
    <?$data = $tpl->serviceNews[0]; unset($tpl->serviceNews[0]);?>
    <div class="col-sm-12 col-md-6">
        <div class="service-div">
            <div class="service-div-bg" style="background-image: url('<?=\lib\input::getThumb( $data['upload_path'], 600);?>')"></div>
            <div class="service-div-detail">
                <div class="service-name"><?=$data['news_name'];?></div>
                <div class="service-div-detail-text">
                    <div class="description"><?=$data['news_description'];?></div>
                    <a   href="<?=$data['detail_href'];?>" class="btn btn-default">XEM CHI TIẾT</a>
                </div>
            </div>
        </div>
    </div>
    
    <?foreach( $tpl->serviceNews as $data ){?>
    <div class="col-sm-6 col-md-3">
        <div class="service-div">
            <div class="service-div-bg" style="background-image: url('<?=\lib\input::getThumb( $data['upload_path'], 600);?>')"></div>
            <div class="service-div-detail">
                <div class="service-name"><?=$data['news_name'];?></div>
                <div class="service-div-detail-text">
                    <div class="description"><?=$data['news_description'];?></div>
                    <a  href="<?=$data['detail_href'];?>" class="btn btn-default">XEM CHI TIẾT</a>
                </div>
            </div>
        </div>
    </div>
    <?}?>
</div>
<?}?>

<!-- page -->
<div class="row">
    <div class="col-sm-12 col-md-12 paging-service">
       
    </div>
</div>
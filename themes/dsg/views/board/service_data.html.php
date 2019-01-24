<?if( !empty($tpl->serviceNews) ){?>
<div class="row">
    <?foreach( $tpl->serviceNews as $key => $data ){
        if($key <= 3) {
        ?>
    <div class="col-sm-6 col-md-3">
        <div class="service-div">
            <div class="service-div-bg" style="background-image: url('<?=\lib\input::getThumb( $data['upload_path'], 555);?>')"></div>
            <div class="service-div-detail">
                <div class="service-name"><?=$data['news_name'];?></div>
                <div class="service-div-detail-text">
                    <div class="description"><?=$data['news_description'];?></div>
                    <a href="<?=$data['detail_href'];?>" class="btn btn-default">XEM CHI TIẾT</a>
                </div>
            </div>
        </div>
    </div>
    <?}}?>
</div>
<?}?>
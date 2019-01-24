<div class="slider-pro bg-dark-alfa-30" id="my-slider">
    <div class="sp-slides">
        <?if( empty($tpl->banner['slider']) ){?>
        <div class="sp-slide">
            <div class="sp-layer sp-static"><img class="sp-image" src="images/slider-1.jpg"></div>
            <div class="sp-layer sp-static" data-height="100%" data-width="100%"></div>
            <div class="sp-caption">
                <div class="sp-slide-text-wrapper">
                    <h3 class="">Chương trình Khuyến mãi Tháng 12</h3>
                    <p>Trong tháng 12, Dũng Sài Gòn sẽ áp dụng khuyến mãi giảm 20% cho khách hàng thành viên khi tham gia tất cả các dịch vụ và trên toàn bộ ...</p>    
                </div>
            </div>
        </div>
        <div class="sp-slide">
            <div class="sp-layer sp-static"><img class="sp-image" src="images/slider-2.jpg"></div>
            <div class="sp-layer sp-static" data-height="100%" data-width="100%"></div>
            <div class="sp-caption">
                <div class="sp-slide-text-wrapper">
                    <h3 class="">Lorem ipsum dolor sit amet,</h3>
                    <p>Consectetur adipiscing elit. Phasellus metus enim</p>        
                </div>
            </div>
        </div>
        <div class="sp-slide">
            <div class="sp-layer sp-static"><img class="sp-image" src="images/slider-1.jpg"></div>
            <div class="sp-layer sp-static" data-height="100%" data-width="100%"></div>
            <div class="sp-caption">
                <div class="sp-slide-text-wrapper">
                    <h3 class="">Chương trình Khuyến mãi Tháng 12</h3>
                    <p>Trong tháng 12, Dũng Sài Gòn sẽ áp dụng khuyến mãi giảm 20% cho khách hàng thành viên khi tham gia tất cả các dịch vụ và trên toàn bộ ...</p>    
                </div>
            </div>
        </div>
        <div class="sp-slide">
            <div class="sp-layer sp-static"><img class="sp-image" src="images/slider-2.jpg"></div>
            <div class="sp-layer sp-static" data-height="100%" data-width="100%"></div>
            <div class="sp-caption">
                <div class="sp-slide-text-wrapper">
                    <h3 class="">Lorem ipsum dolor sit amet,</h3>
                    <p>Consectetur adipiscing elit. Phasellus metus enim</p>        
                </div>
            </div>
        </div>

        <?}else{ foreach( $tpl->banner['slider'] as $data ){

            ?>
        <div class="sp-slide">
            <div class="sp-layer sp-static"><img class="sp-image" alt="<?=$data['logo_src_alt'];?>" src="<?=\lib\input::getThumb($data['logoOri']);?>"></div>
            <div class="sp-layer sp-static" data-height="100%" data-width="100%"></div>
            <div class="sp-caption">
                <div class="sp-slide-text-wrapper">
                    <?if( $data['logo_name'] ){?><h3><?=$data['logo_name'];?></h3><?}?>
                    <?if( $data['logo_desc'] ){?><p><?=$data['logo_desc'];?></p><?}?>
                </div>
            </div>
        </div>
        <?}}?>
    </div>
</div>
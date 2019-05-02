<?if( !empty($tpl->list_store ) ){?>
    <?foreach( $tpl->list_store as $key => $value ){
    	   $value['googlemap_code'] = html_entity_decode($value['googlemap_code']);
    	?>
 
    	  <div class="row" id="salon-<?=$value['store_id'];?>">
            <div class="col-md-12">
                <div class="contact-brand-container">
                    <div class="row">   
                        <div class="col-md-6 col-lg-5 contact-brand-item">
                            <? if ($value['store_avatar']) { ?>
                                <img class="full-width" src="<?= $value['store_avatar'] ?>">
                            <? } ?>

                            <h3> <?=$value['store_name'];?></h3>
                            <div class="icon-div">
                                <p>
                                    <b><?=$value['store_address'];?></b>
                                </p>
                            </div>
                            <div class="icon-div phone">
                                <p>ĐT : <a href="tel:<?=$value['store_phone'];?>"><?=$value['store_phone'];?></a> - <a href="tel:<?=$value['store_phone'];?>"><?=$value['store_phone'];?></a></p>
                            </div>
                            <a href="/book/store_id-<?=$value['store_id'];?>" class="btn btn-md btn-primary" style="margin-left:25px;margin-top:10px;height: 40px;line-height: 40px;font-size: 16px">đặt lịch hẹn 123</a>
                        </div>
                        <div class="col-md-6 col-lg-7 contact-brand-item">
                           <?=$value['googlemap_code'];?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?} }?>

<div class="right-panel" style="margin-top:15px">
      <div class="right-panel-title">
          Sản phẩm HOT
      </div>
      <div class="right-panel-content">
    <?if( ! empty($tpl->dataListTopProduct) ){?> 
   
      <?$i=0; foreach( $tpl->dataListTopProduct as $data ){ ?>
        <a href="<?=$data['url_none_html'];?>"  class="post-thumbnail">
            <span class="post-image"><img src="<?=$data['image_M'];?>" alt="<?=$data['image_alt']?>" ></span>
            <span class="post-description"><span class="title-product"><?=$data['name'];?></span>
             <?if( $data['price_old'] ){?><span class="price-old"><?=$data['price_old'];?></span><?}?><p class="price-well"><?=$data['price_sell'];?></p></span>
        </a> 
      <?}?>
    
    <?}?>
    <!-- End Item -->
  </div>
</div>
 
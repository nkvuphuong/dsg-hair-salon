<?if( !empty($tpl->dataListProduct) ){?>
<div class="row">
    
    
    <?foreach( $tpl->dataListProduct as $data ){  ?>

   <div class="col-xs-6 col-md-4 list-item">
        <div class="item-product item-product-height hover-action-product style-view-2 clearfix">
          <div class="status-product">
            <?if( $data['price_old'] ){?><span class="status-product-item promotional">Khuyến mãi</span><?}?>
            <?if( in_array('1', $data['option']) ){?><span class="status-product-item hot">Nổi bật</span><?}?>
            <?if( in_array('2', $data['option']) ){?><span class="status-product-item viewmore">Xem nhiều</span><?}?>
            <?if( in_array('3', $data['option']) ){?><span class="status-product-item markettrend">Xu hướng</span><?}?>
            <?if( in_array('4', $data['option']) ){?><span class="status-product-item bestseller">Bán chạy</span><?}?>
            <?if( in_array('5', $data['option']) ){?><span class="status-product-item new">Mới</span><?}?>
          </div>
            <div class="img border">
              <a href="<?=$data['url_none_html'];?>" title="<?=$data['name'];?>">
                <img src="<?=$data['image_L'];?>" alt="<?=$data['image_alt']?>">
              </a>
              <div class="action-product bg-main">
                <a href="<?=$data['image_L'];?>" data-lightbox="product" class="item-action"><i class="fa fa-search"></i></a>
                <? if($data['allow_cart'] == 1) { ?>
                <a href="/cart/addcart/<?=$data['id'];?>" class="item-action"><i class="fa fa-shopping-cart"></i></a>
                <? } ?>
              </div>
            </div>
            <div class="info">
              <h4 class="title-product">
                <a  href="<?=$data['url_none_html'];?>"  title="<?=$data['name'];?>"><?=$data['name'];?></a>
              </h4>
             
              <div class="price" style="min-height:20px">
                <?if($data['price_old']){?><span class="price-old"><?=$data['price_old'];?></span><?}?><span class="price-well"><?=$data['price_sell'];?></span>
              </div>
              <div class="show-list">
                
                <a href="/cart/addcart/<?=$data['id'];?>" class="btn btn-main-2 btn-black-2">Thêm vào giỏ hàng</a>
              </div>
            </div>
          </div>
        </div>

 
    <?}?>
</div>
<?}?>

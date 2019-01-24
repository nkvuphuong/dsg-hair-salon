<div class="row">
    <!-- Start list giftcards -->
    <?$i=0;foreach ( $tpl->dataListGiftcards as $data ){$i++;?>
    <?if( $i > 1 AND ($i-1)%2==0 ){?></div><div class="row"><?}?>
    <div class="col-sm-6 col-md-6 cards-item">
        <div class="mb-15 pointer image-magnific-popup" data-group="" href="<?=\lib\input::getThumb( isset($data['uploadPath']) ? $data['uploadPath'] : null);?>" title="<?=$data['name'];?>">
            <img itemprop="image" class="cards-item-image img-responsive" src="<?=\lib\input::getThumb($data['uploadPath'],650);?>" alt="<?=$data['product_name'];?>"/>
        </div>
        <div class="text-center">
            <span itemprop="name"><?=$data['product_name'];?></span>
            <a itemprop="url" href="<?=$data['link_cart'];?>" style="text-decoration:none;" type="button" class="btn btn-sm">ADD TO CARD</a>
        </div>
    </div>
    <?}?><!-- End list giftcards -->
</div>
<div class="row">
    <div class="col-xs-12 text-center">
        <?=\core\ezy::render("paging", "layouts");?>
    </div>
</div>
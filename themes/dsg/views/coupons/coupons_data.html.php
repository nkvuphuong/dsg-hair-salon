<div class="row">
    <!-- Start list coupons -->
    <?foreach ($tpl->dataListCoupons as $data){?>
    <div class="col-sm-6 col-md-6 cards-item pointer image-magnific-popup" data-group="" href="<?=\lib\input::getThumb( isset($data['uploadPath']) ? $data['uploadPath'] : null);?>" title="<?=$data['name'];?>">
        <img class="cards-item-image img-responsive" src="<?=\lib\input::getThumb( $data['uploadPath'], 650);?>" alt="<?=$data['name'];?>"/>
    </div>
    <?}?><!-- End list coupons -->
</div>
<div class="row">
    <div class="col-md-12">
        <?=\core\ezy::render("paging", "layouts");?>
    </div>
</div>
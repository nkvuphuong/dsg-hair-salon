<section class="small-section bg-dark-lighter mybreadcrumb">
    <div class="relative container align-left">
        <div class="row">
            <div class="col-md-12 align-center">
                <h2 class="pull-left"> Tin tức
                    <?if( \core\ezy::$act == "category" ){?>
                    <span class="mysubitem"> / <?=$tpl->title;?></span> 

                    <?} else if( \core\ezy::$act == "detail" ){?>
                    <?if( $tpl->data['cat_name'] ){?>
                    <a href="<?=$tpl->data['cat_url_none_html'];?>" title="<?=$tpl->data['cat_name'];?>">
                        <span class="mysubitem"> / <?=$tpl->data['cat_name'];?></span>
                    </a>
                    <?}?>
                    <span class="mysubitem"> / <?=$tpl->data['name'];?></span>
                    <?}?>   
                </h2>
            </div>
        </div>
    </div>
</section>
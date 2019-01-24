<div class="fci-wrap">
    <?if( $CMS->vars['company_address'] ){?>
    <div class="fci-row" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
        <span class="fci-title"><i class="fa fa-map-marker icon-address"></i></span>
        <span class="fci-content">
            <span itemprop="streetAddress" class="address"><?=$CMS->vars['company_address'];?></span>

            <?if( $CMS->vars['company_address2'] ){?>
            <br>
            <span itemprop="streetAddress" class="address"><?=$CMS->vars['company_address2'];?></span>
            <?}?>
        </span>
    </div>
    <?}?>

    <?if( $CMS->vars['company_phone'] ){?>
    <div class="fci-row">
        <span class="fci-title"><i class="fa fa-phone icon-phone"></i></span>
        <span class="fci-content">
            <a href="tel:<?=$CMS->vars['company_phone'];?>" title="Call Us">
                <span itemprop="telephone" class="phone"><?=$CMS->vars['company_phone'];?></span>
            </a>

            <?if( $CMS->vars['company_phone2'] ){?>
            <br>
            <a href="tel:<?=$CMS->vars['company_phone2'];?>" title="Call Us">
                <span itemprop="telephone" class="phone"><?=$CMS->vars['company_phone2'];?></span>
            </a>
            <?}?>
        </span>
    </div>
    <?}?>

    <?if( $CMS->vars['company_email'] ){?>
    <div class="fci-row">
        <span class="fci-title"><i class="fa fa-envelope icon-email"></i></span>
        <span class="fci-content">
            <a href="mailto:<?=$CMS->vars['company_email'];?>">
                <span itemprop="email" class="email"><?=$CMS->vars['company_email'];?></span>
            </a>
            <?if( $CMS->vars['company_email2'] ){?>
            <br>
            <a href="mailto:<?=$CMS->vars['company_email2'];?>">
                <span itemprop="email" class="email"><?=$CMS->vars['company_email2'];?></span>
            </a>
            <?}?>
        </span>
    </div>
    <?}?>

    <?if( $CMS->vars['company_fax'] ){?>
    <div class="fci-row">
        <span class="fci-title"><i class="fa fa-fax icon-fax"></i></span>
        <span class="fci-content">
            <span itemprop="faxNumber" class="fax"><?=$CMS->vars['company_fax'];?></span>
        </span>
    </div>
    <?}?>
</div>
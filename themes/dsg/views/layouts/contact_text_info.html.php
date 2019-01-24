<div class="fci-wrap">
    <?if( $CMS->vars['company_address'] ){?>
    <div class="fci-row" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
        <span class="fci-title">Address:</span>
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
        <span class="fci-title">Phone:</span>
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
        <span class="fci-title">Email:</span>
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
        <span class="fci-title">Fax:</span>
        <span class="fci-content">
            <span itemprop="faxNumber" class="fax"><?=$CMS->vars['company_fax'];?></span>
        </span>
    </div>
    <?}?>
</div>
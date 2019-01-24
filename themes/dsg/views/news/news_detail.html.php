<?php
global $CMS;
$link = $CMS->vars['root_domain'].$_SERVER[REQUEST_URI];
?>
<div class="row">
    <div itemscope itemtype="http://schema.org/NewsArticle" class="col-md-12">
        <meta itemprop="headline" content="<?=$tpl->data['name'];?>">
        <meta itemprop="author" content="<?=$CMS->vars['company_name'];?>">
        <font itemprop="publisher" itemscope itemtype="http://schema.org/Organization" style="display: none;">
            <meta itemprop="logo" content="<?=$tpl->logo_website;?>">
            <meta itemprop="name" content="<?=$CMS->vars['company_name'];?>">
        </font>
        <span itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
            <img itemprop="image" class="img-responsive" src="<?=\lib\input::getThumb($tpl->data['pathUpload']);?>">
            <meta itemprop="url" content="<?=\lib\input::getThumb($tpl->data['pathUpload']);?>">
            <meta itemprop="width" content="auto">
            <meta itemprop="height" content="auto">
        </span>
        <p class="posted">
            <i class="fa fa-info-circle"></i>
            <time itemprop="datePublished" datetime="<?=\lib\date::format($tpl->data['time'], 'Y-m-d');?>">
                <?=\lib\date::format($tpl->data['time'], 'Y M d');?>
            </time> 
            Posted in <a itemprop="url" href="<?=$tpl->data['cat_url_none_html'];?>">
                <font itemprop="name"><?=$tpl->data['cat_name'];?></font>
            </a> 
            with <?=$tpl->data['views'];?> views
        </p>
        <h1 itemprop="name"><?=$tpl->data['name'];?></h1>
        <div itemprop="text">
            <div class="content-page">
                <?=$tpl->data['content'];?>
                    
            </div>
        </div>        
        <div class="text-right"> 
               <div class="addthis_toolbox addthis_default_style addthis_32x32_style pull-right">
                  <a class="addthis_button_facebook"></a>
                    <div class="zalo-share-button" data-href="<?=$link;?>" data-oaid="<?=$CMS->vars['zalo_officical_account'];?>" data-layout="2" data-color="blue" data-customize="true" style="float:right;padding-left:10px;cursor: pointer" > <img alt="Chia sẻ zalo" title="Chia sẻ zalo" src="images/sharezalo.png" width="32" height="32"/> </div>
                    <script src="https://sp.zalo.me/plugins/sdk.js"></script> 
                </div>
        </div>
    </div>
     
</div>
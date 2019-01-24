<?=\core\ezy::render("banner", "news");?>
<section class="page-section pt-40 pb-60">
    <div class="container">
        <div class="row">
            <?=\core\ezy::tpl('news_list', 'news');?>
        </div>
    </div>
</section>
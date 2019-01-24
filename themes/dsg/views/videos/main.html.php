<?=\core\ezy::render("banner", "videos");?> 
<section class="page-section pt-40 pb-60">
    <div class="container">
        <div class="row">
            <?=\core\ezy::tpl('videos_list', 'videos');?>
        </div>
    </div>
</section>
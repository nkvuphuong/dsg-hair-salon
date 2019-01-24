<section class="p-pages">
    <!-- tpl main -->
    <?=\core\ezy::tpl('main', 'pages');?>

    <!-- use for scroll to salon when loaded -->
    <input type="hidden" name="salon_current" value="<?=\core\ezy::$input['salon'];?>">
</section>
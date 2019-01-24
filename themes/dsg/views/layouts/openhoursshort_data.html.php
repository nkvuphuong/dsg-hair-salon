<!-- support render openhoursshort_data -->
<div class="foh-wrap">
    <?
    foreach( $tpl->openHoursShort as $day => $time ) {
        if( $time['checked'] ){
    ?>
    <!-- Normal Day -->
    <div class="foh-row short" itemprop="openingHours" content="<?=ucfirst($day);?> <?=strtolower($time['open']);?> - <?=strtolower($time['close']);?>">
        <span class="foh-date"><?=ucfirst($day);?>:</span>
        <span class="foh-time"><?=strtolower($time['open']);?> - <?=strtolower($time['close']);?></span>
    </div>

    <?}else{?>
    <!-- Close Day -->
    <div class="foh-row short">
        <span class="foh-date"><?=ucfirst($day);?>:</span>
        <span class="foh-time">Closed</span>
    </div>
    <?}}?>
</div>
<? if($tpl->banner['global_popup']) {?>
    <div id="global_popup" class="white-popup border-style">
        <div class="white-popup-container">
            <? foreach ($tpl->banner['global_popup'] as $logo){?>
            <div>
                <a itemprop="url" href="<?=$logo['logo_link']?>" title="<?=$logo['logo_desc']?>" target="__blank"><img itemprop="image" src="<?=$logo['logo_src']?>"></a>
            </div>
            <?}?>
        </div>
    </div>


    <script>
        $.magnificPopup.open({
            items: {
                src: '#global_popup',
                type: 'inline'
            }
        });
    </script>
<?}?>
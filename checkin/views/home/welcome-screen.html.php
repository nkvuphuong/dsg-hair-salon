<div id="welcome-screen" class="display-block">
    <? if(\lib\input::vars('checkin_welcome_screen') == 'video' && !empty(\lib\input::vars('checkin_welcome_screen_video')))  { ?>
    <div id="myNav" class="overlay">
        <div class="row">
            <div class="col-md-12">
                <div class="embed-responsive embed-responsive-16by9">
                    <?= \lib\input::vars('checkin_welcome_screen_video') ?>
                </div>
            </div>
        </div>
    </div>
        <script>
            $(document).ready(function() {
                document.getElementById("myNav").style.width = "100%";
            })
        </script>

    <? } else { ?>
        <div class="row">
            <div class="col-md-12">
                <h4 class="title-h4-light text-center ">Bạn đang ở Salon:</h4>
                <p class="mt-05 text-center addr-text"><?= \lib\input::arrayValue($tpl->data->store, 'name') ?></b></p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 text-center mb-10">
                <h4 class="mb-20 addr-note">Bạn có thể ghé các Chi nhánh khác của Salon Autom</h4>
            </div>
        </div>
        <div class="row mb-20">
            <? if (\lib\input::objectValue($tpl->data, 'cities')) { ?>
                <? foreach ($tpl->data->cities as $key => $city) { ?>
                    <div class="col-md">
                        <p class="add-title"><?= $city['city_name'] ?></p>
                        <? if (\lib\input::arrayValue($city, 'stores')) { ?>
                            <? foreach ($city['stores'] as $store) { ?>
                                <p><?= $store['name'] ?></p>
                            <? } ?>
                        <? } ?>
                    </div>
                    <? if ($key % 2 == 1 && $key != count($tpl->data->cities)) { ?>
                        <div class="w-100"></div>
                    <? } ?>
                <? } ?>
            <? } ?>
        </div>
        <div class="row mb-20">
            <div class="col-md-12 text-center mb-20">
                <a class="btn btn-green-sm" href="">HOTLINE: <?= \lib\input::vars('company_phone') ?></a>
                <p class="mt-15">Cám ơn Quý Khách đã sử dụng dịch vụ tại <?= \lib\input::vars('company_name') ?></p>
            </div>
        </div>
    <? } ?>
</div>
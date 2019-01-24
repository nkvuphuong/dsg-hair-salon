<div class="row">
    <div class="col-md-12">
        <h4 class="with-border m-t-0"><?= $CMS->lang['embed_information']; ?></h4>
        <ul class="input_li row match-height">
            <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                <label class="form-label pull-left" for="embed_name"><?= $CMS->lang['embed_name'] ?></label>
                <input class="form-control" type="text" name="embed_name" id="embed_name"
                       value="<?= \lib\input::get('embed_name') ?>" placeholder="<?= $CMS->lang['embed_name'] ?>">
            </li>
            <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                <label class="form-label pull-left" for="embed_name"><?= $CMS->lang['embed_code'] ?></label>
                <input class="form-control" type="text" name="embed_code" id="embed_code"
                       value="<?= \lib\input::get('embed_code') ?>" placeholder="<?= $CMS->lang['embed_code'] ?>">
            </li>
            <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                <label class="form-label pull-left" for="embed_status"><?= $CMS->lang['embed_status'] ?></label>
                <select class="form-control" name="embed_status">
                    <option value="">---</option>
                    <?= $tpl->status_options; ?>
                </select>
            </li>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <h4 style="margin-top: 10px !important;"
            class="with-border m-t-0"><?= $CMS->lang['embed_start_date']; ?></h4>
        <ul class="input_li row match-height">
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                <label class="form-label pull-left" for="embed_start_date"><?= $CMS->lang['from'] ?></label>
                <input class="form-control date-picker" type="text" name="embed_start_date" id="embed_start_date"
                       value="<?= \lib\input::get('embed_start_date') ?>" placeholder="<?= $CMS->lang['from'] ?>">
            </li>
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                <label class="form-label pull-left" for="embed_start_date_to"><?= $CMS->lang['to'] ?></label>
                <input class="form-control date-picker" type="text" name="embed_start_date_to"
                       id="embed_start_date_to" value="<?= \lib\input::get('embed_start_date_to') ?>"
                       placeholder="<?= $CMS->lang['to'] ?>">
            </li>
        </ul>
    </div>
    <div class="col-md-4">
        <h4 style="margin-top: 10px !important;"
            class="with-border m-t-0"><?= $CMS->lang['embed_end_date']; ?></h4>
        <ul class="input_li row match-height">
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                <label class="form-label pull-left" for="embed_end_date"><?= $CMS->lang['from'] ?></label>
                <input class="form-control date-picker" type="text" name="embed_end_date"
                       id="embed_end_date" value="<?= \lib\input::get('embed_end_date') ?>"
                       placeholder="<?= $CMS->lang['from'] ?>">
            </li>
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                <label class="form-label pull-left" for="embed_end_date_to"><?= $CMS->lang['to'] ?></label>
                <input class="form-control date-picker" type="text" name="embed_end_date_to"
                       id="embed_end_date_to" value="<?= \lib\input::get('embed_end_date_to') ?>"
                       placeholder="<?= $CMS->lang['to'] ?>">
            </li>
        </ul>
    </div>
    <div class="col-md-4">
        <h4 style="margin-top: 10px !important;" class="with-border m-t-0"><?= $CMS->lang['embed_time']; ?></h4>
        <ul class="input_li row match-height">
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                <label class="form-label pull-left" for="embed_time"><?= $CMS->lang['from'] ?></label>
                <input class="form-control date-picker" type="text" name="embed_time" id="embed_time"
                       value="<?= \lib\input::get('embed_time') ?>" placeholder="<?= $CMS->lang['from'] ?>">
            </li>
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                <label class="form-label pull-left" for="embed_time_to"><?= $CMS->lang['to'] ?></label>
                <input class="form-control date-picker" type="text" name="embed_time_to" id="embed_time_to"
                       value="<?= \lib\input::get('embed_time_to') ?>" placeholder="<?= $CMS->lang['to'] ?>">
            </li>
        </ul>
    </div>
</div>
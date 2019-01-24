<div class="row">
    <div class="col-md-12">
        <h4 class="with-border m-t-0"><?= $CMS->lang['rating_information']; ?></h4>
        <ul class="input_li row match-height">
            <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                <label class="form-label pull-left" for="rating_name"><?= $CMS->lang['rating_name'] ?></label>
                <input class="form-control" type="text" name="rating_name" id="rating_name"
                       value="<?= $CMS->input['rating_name'] ?>" placeholder="<?= $CMS->lang['rating_name'] ?>">
            </li>
            <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                <label class="form-label pull-left" for="rating_name"><?= $CMS->lang['rating_code'] ?></label>
                <input class="form-control" type="text" name="rating_code" id="rating_code"
                       value="<?= $CMS->input['rating_code'] ?>" placeholder="<?= $CMS->lang['rating_code'] ?>">
            </li>
            <li class="col-xl-4 col-lg-4 col-sm-4 col-xs-12">
                <label class="form-label pull-left" for="rating_status"><?= $CMS->lang['rating_status'] ?></label>
                <select class="form-control" name="rating_status">
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
            class="with-border m-t-0"><?= $CMS->lang['rating_start_date']; ?></h4>
        <ul class="input_li row match-height">
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                <label class="form-label pull-left" for="rating_start_date"><?= $CMS->lang['from'] ?></label>
                <input class="form-control date-picker" type="text" name="rating_start_date" id="rating_start_date"
                       value="<?= $CMS->input['rating_start_date'] ?>" placeholder="<?= $CMS->lang['from'] ?>">
            </li>
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                <label class="form-label pull-left" for="rating_start_date_to"><?= $CMS->lang['to'] ?></label>
                <input class="form-control date-picker" type="text" name="rating_start_date_to"
                       id="rating_start_date_to" value="<?= $CMS->input['rating_start_date_to'] ?>"
                       placeholder="<?= $CMS->lang['to'] ?>">
            </li>
        </ul>
    </div>
    <div class="col-md-4">
        <h4 style="margin-top: 10px !important;"
            class="with-border m-t-0"><?= $CMS->lang['rating_end_date']; ?></h4>
        <ul class="input_li row match-height">
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                <label class="form-label pull-left" for="rating_end_date"><?= $CMS->lang['from'] ?></label>
                <input class="form-control date-picker" type="text" name="rating_end_date"
                       id="rating_end_date" value="<?= $CMS->input['rating_end_date'] ?>"
                       placeholder="<?= $CMS->lang['from'] ?>">
            </li>
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                <label class="form-label pull-left" for="rating_end_date_to"><?= $CMS->lang['to'] ?></label>
                <input class="form-control date-picker" type="text" name="rating_end_date_to"
                       id="rating_end_date_to" value="<?= $CMS->input['rating_end_date_to'] ?>"
                       placeholder="<?= $CMS->lang['to'] ?>">
            </li>
        </ul>
    </div>
    <div class="col-md-4">
        <h4 style="margin-top: 10px !important;" class="with-border m-t-0"><?= $CMS->lang['rating_time']; ?></h4>
        <ul class="input_li row match-height">
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                <label class="form-label pull-left" for="rating_time"><?= $CMS->lang['from'] ?></label>
                <input class="form-control date-picker" type="text" name="rating_time" id="rating_time"
                       value="<?= $CMS->input['rating_time'] ?>" placeholder="<?= $CMS->lang['from'] ?>">
            </li>
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
                <label class="form-label pull-left" for="rating_time_to"><?= $CMS->lang['to'] ?></label>
                <input class="form-control date-picker" type="text" name="rating_time_to" id="rating_time_to"
                       value="<?= $CMS->input['rating_time_to'] ?>" placeholder="<?= $CMS->lang['to'] ?>">
            </li>
        </ul>
    </div>
</div>
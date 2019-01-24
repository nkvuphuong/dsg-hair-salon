<ul class="input_li row match-height">
    <li class="<?=( $CMS->vars['theme'] == "dsg" )? 'col-xl-3 col-lg-3 col-md-3 col-sm-6 col-xs-12' : 'col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12';?>">
        <label class="form-label pull-left" for="con_subject"><?=$CMS->lang['con_subject'];?></label>
        <input class="form-control" type="text" name="con_subject" id="con_subject"
               value="<?=$CMS->input['con_subject'];?>" placeholder="<?=$CMS->lang['con_subject'];?>">
    </li>
    <li class="<?=( $CMS->vars['theme'] == "dsg" )? 'col-xl-3 col-lg-3 col-md-3 col-sm-6 col-xs-12' : 'col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12';?>">
        <label class="form-label pull-left" for="con_name"><?=$CMS->lang['con_name'];?></label>
        <input class="form-control" type="text" name="con_name" id="con_name"
               value="<?=$CMS->input['con_name'];?>" placeholder="<?=$CMS->lang['con_name'];?>">
    </li>
    <li class="<?=( $CMS->vars['theme'] == "dsg" )? 'col-xl-3 col-lg-3 col-md-3 col-sm-6 col-xs-12' : 'col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12';?>">
        <label class="form-label pull-left" for="con_email"><?=$CMS->lang['con_email'];?></label>
        <input class="form-control" type="text" name="con_email" id="con_email"
               value="<?=$CMS->input['con_email'];?>" placeholder="<?=$CMS->lang['con_email'];?>">
    </li>

    <?if( $CMS->vars['theme'] == "dsg" ){?>
    <li class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <label class="form-label pull-left" for="con_type"><?= $CMS->lang['con_type'] ?></label>
        <select class="form-control" name="con_type">
            <option value="">---</option>
            <option value="0"><?=$CMS->lang['con_type_0'];?></option>
            <option value="1"><?=$CMS->lang['con_type_1'];?></option>
        </select>
    </li>
    <?}?>
</ul>
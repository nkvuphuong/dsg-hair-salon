<?if( !empty(\core\ezy::$page['data']) ){?>
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?foreach( \core\ezy::$page['data'] as $data => $value ){?>

            <?if( $value['status'] == "first" ){?>
            <li>
                <a class="hover-main-color" href="<?=isset($tpl->modLink)?$tpl->modLink:'';?>/page-<?=$value['page'];?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <?}else if ( $value['status'] == "last" ){?>
            <li>
                <a class="hover-main-color" href="<?=isset($tpl->modLink)?$tpl->modLink:'';?>/page-<?=$value['page'];?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>

            <?}else if( $value['status'] == "active" ){?>
            <li class="active">
                <a class="hover-main-color active"><span><?=$value['page'];?></span></a>
            </li>

            <?}else{?>
            <li><a class="hover-main-color" href="<?=isset($tpl->modLink)?$tpl->modLink:'';?>/page-<?=$value['page'];?>"><span><?=$value['page'];?></span></a></li>
            <?}?>

            <?}?>
        </ul>
    </nav>
<?}?>
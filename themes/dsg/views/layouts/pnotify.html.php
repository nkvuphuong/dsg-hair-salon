<?
    if ( (isset($tpl->msg['status']) && $tpl->msg['status']) OR (isset($tpl->alert['status']) && $tpl->alert['status']) )
    {
        $title = 'Notification';
        $msg  = isset($tpl->msg['message']) && $tpl->msg['message'] ? "{$tpl->msg['message']}<br/>" : '';
        $msg .= isset($tpl->alert['msg']) && $tpl->alert['msg'] ? $tpl->alert['msg'] : '';
        $type = ( (isset($tpl->msg['status']) && $tpl->msg['status'] == 'success') OR (isset($tpl->alert['status']) && $tpl->alert['status'] == 'ok') ) ? 'success' : 'error';
        $icon = $type == 'success' ? 'fa fa-check-circle' : 'fa fa-exclamation-circle';
        ?>
            <script type="text/javascript">
                $(document).ready(function(){
                    $(window).load(function() {
                        if ( $('.pnotify-scroll-to-here').length ) 
                        {
                            $("html, body").animate({
                                scrollTop: ($('.pnotify-scroll-to-here').offset().top - 50),
                            }, 1000);
                        }
                        new PNotify({
                            title: '<?=$title?>',
                            text: '<?=$msg?>',
                            type: '<?=$type?>',
                            icon: '<?=$icon?>',
                            addclass: 'alert-with-icon'
                        });
                    });
                });
            </script>
        <?
    }
?>
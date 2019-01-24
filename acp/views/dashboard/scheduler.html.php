<link rel="stylesheet" href="/acp/assets/css/separate/vendor/bootstrap-datetimepicker.min.css">
<link rel="stylesheet" href="/acp/assets/css/lib/fullcalendar/fullcalendar.min.css">
<link rel="stylesheet" href="/acp/assets/css/separate/pages/calendar.min.css">
<style>.btn-sm-reset{border: solid 1px #00a8ff;background: #00a8ff;padding: .25rem .5rem;font-size: .875rem;line-height: 1.25;} @media (max-width: 1199px) and (min-width: 768px){.calendar-page-side-section#calendar-day-events{width:66.666666%;}} @media (min-width: 1200px){.calendar-page-side-section-in.pre-scrollable{max-height:317px;}}</style>
<div class="box-typical">
    <div class="calendar-page" id="calendar-page">
        <div class="calendar-page-content">
            <div class="calendar-page-title"><?=$CMS->lang['calendar']?></div>
            <div class="calendar-page-content-in">
                <div id='calendar'></div>
            </div><!--.calendar-page-content-in-->
        </div><!--.calendar-page-content-->

        <div class="calendar-page-side" style="">
            <section class="calendar-page-side-section" id="calendar-day-events">
                <header class="box-typical-header-sm"></header>
                <div class="calendar-page-side-section-in">
                    <ul class="exp-timeline">
                    </ul>
                </div>
            </section>

            <!--
            <section class="calendar-page-side-section">
                <header class="box-typical-header-sm">Filters</header>
                <div class="calendar-page-side-section-in">
                    <ul class="colors-guide-list">
                        <li>
                            <div class="color-double green"><div></div></div>
                            Appointments
                        </li>
                        <li>
                            <div class="color-double"><div></div></div>
                            Meetings
                        </li>
                        <li>
                            <div class="color-double orange"><div></div></div>
                            Supervision
                        </li>
                        <li>
                            <div class="color-double red"><div></div></div>
                            Surgey
                        </li>
                        <li>
                            <div class="color-double coral"><div></div></div>
                            Training
                        </li>
                    </ul>
                </div>
            </section>
            -->

        </div><!--.calendar-page-side-->
    </div><!--.calendar-page-->
</div><!--.box-typical-->
<script>
    var calendarLangData = new Array();
    calendarLangData['vn'] = 'vi';

    var calendarLang = typeof calendarLangData["<?=$CMS->vars['default_language']?>"] != "undefined" ? calendarLangData["<?=$CMS->vars['default_language']?>"] : "<?=$CMS->vars['default_language']?>";
</script>
<script src="/acp/assets/js/lib/match-height/jquery.matchHeight.min.js"></script>
<script src="/acp/assets/js/lib/moment/moment-with-locales.min.js"></script>
<script src="/acp/assets/js/lib/eonasdan-bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
<script src="/acp/assets/js/lib/fullcalendar/fullcalendar.min.js"></script>
<!--<script src="/acp/assets/js/lib/fullcalendar/locale-all.js"></script>-->
<script src="/acp/assets/js/lib/fullcalendar/fullcalendar-init.js"></script>
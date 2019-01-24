$(document).ready(function(){




/* ==========================================================================
    Side datepicker
    ========================================================================== */

    $('#side-datetimepicker').datetimepicker({
        inline: true,
        format: 'DD/MM/YYYY'
    });

/* ========================================================================== */

});


/* ==========================================================================
    Calendar page grid
    ========================================================================== */

(function($, viewport){
    $(document).ready(function() {

        if(viewport.is('>=lg')) {
            $('.calendar-page-content, .calendar-page-side').matchHeight();
        }

        // Execute code each time window size changes
        $(window).resize(
            viewport.changed(function() {
                if(viewport.is('<lg')) {
                    $('.calendar-page-content, .calendar-page-side').matchHeight({ remove: true });
                }
            })
        );
    });
})(jQuery, ResponsiveBootstrapToolkit);



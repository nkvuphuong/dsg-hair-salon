$(document).ready(function () {
    $(".chosen-select").chosen({})
    $(function () {
        $('[data-toggle="popover"]').popover({
            container: 'body',
        })
    })
})

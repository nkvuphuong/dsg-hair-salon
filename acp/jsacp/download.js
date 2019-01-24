function getCookie( name ) {
    var parts = document.cookie.split(name + "=");
    if (parts.length == 2) return parts.pop().split(";").shift();
}

function expireCookie( cName ) {
    document.cookie =
        encodeURIComponent(cName) + "=deleted; expires=" + new Date( 0 ).toUTCString();
}

function setFormToken(frmSelector) {
    var downloadToken = new Date().getTime();
    frmSelector.find('#downloadToken').val(downloadToken);
    return downloadToken;
}

var downloadTimer;
var attempts = 30;
var loadingEffect;

// Prevents double-submits by waiting for a cookie from the server.
function blockResubmit(btnSelector, frmSelector,loadingSelector) {
    loadingSelector.show();
    btnSelector.prop('disabled', true);
    var downloadToken = setFormToken(frmSelector);
    downloadTimer = window.setInterval( function() {
        var token = getCookie( "downloadToken" );
        if( (token == downloadToken) || (attempts == 0) ) {
            unblockSubmit(btnSelector,loadingSelector);
        }
        attempts--;
    }, 500 );
}

function unblockSubmit(btnSelector,loadingSelector) {
    window.clearInterval( downloadTimer );
    expireCookie( "downloadToken" );
    attempts = 30;
    loadingSelector.hide();
    btnSelector.prop('disabled', false);
}

function submitDownLoadFrm(btnSelector, frmSelector,loadingSelector){
    blockResubmit(btnSelector, frmSelector,loadingSelector);
    frmSelector.submit();
    unSetFrmExport(frmSelector);
}

function downloadConfirm(btnSelector, frmSelector, loadingSelector)
{
    var html = `
    <div class="checkbox">
        <input id="no_export_image" name="no_export_image" value="1" type="checkbox">
        <label for="no_export_image">`+cms_lang['p_no_export_image']+`</label>
    </div>
    `;
    swal({
        title: cms_lang['confirm'],
        text: cms_lang['confirm_export'],
        type: "warning",
        html : html,
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: cms_lang['gnotice_ok'],
        cancelButtonText: cms_lang['gnotice_cancel'],
        closeOnConfirm: true,
        closeOnCancel: true
    }).then(function () {
        if( frmSelector.find('input[name="no_export_image"]').length <= 0 )
        {
            frmSelector.append('<input type="hidden" name="no_export_image" value="0"/>');
        }

        if ($("#no_export_image").prop( "checked" ) ) 
        {
            $('input[name="no_export_image"]').val(1).attr("value", 1);
        }
        else
        {
            $('input[name="no_export_image"]').val(0).attr("value", 0);
        }

        submitDownLoadFrm(btnSelector, frmSelector,loadingSelector);
    });
}

function downloadExportInit(btnSelector=$('.downloadBtn'), frmSelector=$('#downloadFrm'), loadingSelector=$('#loading_export')) {
    btnSelector.click(function(){
        frmSelector.find('.custom_filter').remove();
        downloadConfirm(btnSelector, frmSelector, loadingSelector);
    })
}

function downloadExportInitFilter(btnSelectorFilter=$('.downloadBtnFilter'), frmSelectorFilter=$('#formsearch_adv'), btnSelector=$('.downloadBtn'), frmSelector=$('#downloadFrm'), loadingSelector=$('#loading_export'))
{
    btnSelectorFilter.click(function(){
        var data = frmSelectorFilter.serializeArray();
        if( data.length > 0 )
        {
            frmSelector.find('.custom_filter').remove();
            for(x in data)
            {
                if( data[x].name != 'token' )
                {
                    frmSelector.append('<input type="hidden" class="custom_filter" name="' + data[x].name + '" value="' + data[x].value + '">');
                }
            }
        }

        downloadConfirm(btnSelector, frmSelector, loadingSelector);
    });
}

$(document).ready(function(){
    downloadExportInit();
    downloadExportInitFilter();
})
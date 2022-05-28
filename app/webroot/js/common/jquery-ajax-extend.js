$(document).ajaxSend(function(event, jqxhr, settings) {
    if(settings.block != undefined && settings.block != null) {
        if($.fn.block != undefined && $.fn.block != null) {
            $("#" + settings.block).block({
                message: /*'Doing action...'*/'<img src="../img/loading.gif" />', 
                css:{
                    backgroundColor:'transparent', 
                    border:'0px'
                }, 
                overlayCSS:{
                    backgroundColor:'transparent'
                }
            });
        }
    }
});

$(document).ajaxComplete(function(event, jqxhr, settings) {
    if(settings.block != undefined && settings.block != null) {
        if($.fn.block != undefined && $.fn.block != null) {
            $("#" + settings.block).unblock();
        }
    }
});
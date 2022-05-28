$(document).ready(function() {
    /*var manager = */new DatablockManager(); 
    
    _ajaxifyForm($("#PowerSourceViewForm"), null, "power_source");
    _ajaxifyForm($("#ProjectViewForm"), null, "project", function(obj) {
        $('#project-name-label').text(obj.name);
        $('#project-form, #project-header').toggle();
        //$('#project-header').show();
    });
    
    //var show = true
    $('.edit-project, .cancel-edit-project').click(function() {
        $('#project-form, #project-header').toggle();
    });
});
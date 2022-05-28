$(document).ready(function() {
    
    // Make all .info elements show their info, according to their data-type attribute
    $('.info').each(function(k, v) {
        $(v).attr('title', _extractInfoFromObject(window.app[$(v).data('type')])).tooltip();
    });
    
    $('.parameter').tooltip();

    /*var analisis = */new PQMAnalisis(window.app.analisisContext);
	
    $('#tags').editable({
        inputclass: 'input-large form-control',
        mode:'inline',
        emptytext: 'No tags',
        select2: {
            tags: ['unbalance', 'distortion'],
            tokenSeparators: [", "/*, " "*/]
        }
    });
});



/*
 * ANALISIS
 */
function PQMAnalisis(data) {
	
    // The data retrieved from server. The data is updated everytime a request is made
    this.mainData = data;
	
    this.chartBuilder = new ChartBuilder2({});
	
    // Cached tabs (user already opened them). Used to avoid charts recreation.
    this.cachedTabs = [];

    /* The ids/classes of the elements where you want to put some parts of the analisis environment */
    this._places = {
        tabs: "analisis-tabs"
    };
	
    this.showData(data);
}

PQMAnalisis.prototype = {
    showData: function(data) {
        var _this = this;
	
        if(data.reports) alert("Isc: " + data.reports.Isc);
		
        // Create tabs
        this._addAnalisisTabs(data.analisis);
				
        // Draw defaul chart
        for (var first in data.analisis)break;
        this._drawChartAndWidgets(first, first, "panel-" + first);
		
        // Reset
        this.cachedTabs = [];
    },
	
    /*
	This function generates one tab for each type of analisis of the current parameter.
	It generates one <li> element to create the tab, and one <div> element with the id of
	the analisis to print the analisis chart (the <div> works as a chart panel).
	It also binds the tabs selection event to one specific function that performs the
	calculations of the analisis.
	*/
    _addAnalisisTabs: function(analisis) {
        var _this = this;
		
        // Temporally create tabs to prevent 'unstyled content flash'
        var tabs = $("#" + this._places.tabs);
        tabs.tabs({
            selected : 0 // 'Main' selected by default
        });
	
        // Clear tabs and panels (delete previously created temporal <li>s and <div>s)
        tabs = $("#" + this._places.tabs);
        var ul = $("ul", tabs);
        $('div', tabs).remove('.temp'); // remove temp <div>s
        $('li', ul).remove('.temp'); // remove temp <li>s
	
        // Setup handlebars
        var capitalize = function(str) {
            return str[0].toUpperCase() + str.substring(1);
        }
        Handlebars.registerHelper('capitalize', function() {
            return new Handlebars.SafeString(
                capitalize(this.name)
            );
        });
        Handlebars.registerHelper('idfy', function() {
            //var newName = this.name.replace(' ', '').replace('(', '-').replace(')', '');
            return new Handlebars.SafeString(
                this.name.replace(' ', '').replace('(', '-').replace(')', '')
            );
        });
        var analisisTabTemplate = Handlebars.compile($("#analisis-tab-template").html());
        var analisisPanelTemplate = Handlebars.compile($("#analisis-panel-template").html());
        
        // Add tabs according to analisis specified for the current parameter.
        if(analisis != undefined) {
            for(aName in analisis) {
                var tab = analisis[aName].name != undefined? analisis[aName].name:capitalize(aName)
                ul.append(analisisTabTemplate({name: aName, tab: tab }));
                tabs.append(analisisPanelTemplate({name: aName, title: analisis[aName].title}));
            }
        }
		
        // (re)Create tabs
        tabs.tabs("destroy").tabs({
            selected : 0, // 'Main' selected by default
            show: function(e, ui) {
                var analisisName = $(ui.tab).data("analisis-name");
                // Bind tabs selection to a function in ChartAnalyser
                if(ui.index > 0) {
                    // Only recreate the chart if its tab is not cached (wasn't opened previously)
                    if($.inArray(ui.index, _this.cachedTabs) == -1) {
                        _this._drawChartAndWidgets(analisisName, analisisName, $(ui.panel).attr('id'));
                        _this.cachedTabs.push(ui.index);
                    }
                }
                return true;
            }
        });
    },
	
    _drawChartAndWidgets: function(analisisName, whereChart, whereWidgets) {
        var _this = this;
		
        /* 
         * CHART
         */
        chartConfig = {
            type: _this.mainData.options.TYPE,
            where: whereChart, 
            title: _this.mainData.title,
            indicators: _this.mainData.analisis[analisisName].indicators, 
            magnitude:_this.mainData.analisis[analisisName].chart.magnitude,
            min_time: _this.mainData.options["MINTIME"]
        };
        if(_this.mainData.analisis[analisisName].normalization) {// Testing normalization
            if(_this.mainData.analisis[analisisName].normalization.type == "threshold-line") {
                chartConfig.extras = [{
                    type:"threshold-line", 
                    value:_this.mainData.analisis[analisisName].normalization.value,
                    label:_this.mainData.analisis[analisisName].normalization.label
                }];
            }	
        }
        if(_this.mainData.analisis[analisisName].chart.options) {
            $.extend(chartConfig, _this.mainData.analisis[analisisName].chart.options)
        }
        var ch = _this.chartBuilder[_this.mainData.analisis[analisisName].chart.type](_this.mainData.data, chartConfig);
		
                
        //<div class='panel panel-default'><div class='panel-body'></div></div>
        
        /* 
         * CHART TOOLBAR 
         */
        $('#' + whereChart).wrap("<div class='box thick-border' id='box-" + whereChart + "'><div style='margin:20px 20px 0px 20px;border:1px'></div></div>");
        var toolbar = $("<div class='tools'></div>");
        
        // Interval stats
        if(_this.mainData.analisis[analisisName].chart.type == 'multiDatasetStockChart') {
            var checkedAverage = false;
            var intervalStatsButton = $("<div class='bouton'><a title='Show min, max and average values for an interval' href='#!' class='unchecked'><i class='glyphicon glyphicon-stats'></i> View Interval Stats</a></div>").click(function() {
                checkedAverage = !checkedAverage;
                if(checkedAverage) {
                    ch.showStatsOnInterval = true;
                    if(ch.showStatsInCurrentInterval()) ch.validateData();

                    $(this).find('a').removeClass('unchecked').addClass('checked');
                } else {
                    ch.showStatsOnInterval = false;
                    if(ch.hideStatsInCurrentInterval()) {
                        ch.validateData();
                        //ch.validateNow();
                    }
                    $(this).find('a').removeClass('checked').addClass('unchecked');
                }

            });
            toolbar.append(intervalStatsButton);
        }        
        
        // Highlight region
        var checkedWithoutZooming = false;
        var selectWithoutZoomingButton = $("<div class='bouton'><a title='Select chart area' href='#!' class='unchecked'><i class='glyphicon glyphicon-comment'></i> Select Chart Area</a></div>").click(function() {
            checkedWithoutZooming = !checkedWithoutZooming;
            if(checkedWithoutZooming) {
                ch.chartCursorSettings.selectWithoutZooming = true;
                ch.validateNow();
                $(this).find('a').removeClass('unchecked').addClass('checked');
            } else {
                ch.chartCursorSettings.selectWithoutZooming = false;
                ch.validateNow();
                $(this).find('a').removeClass('checked').addClass('unchecked');
            }
            
        });
        toolbar.append(selectWithoutZoomingButton);
        
        var toolbox = $('#box-' + whereChart);
        toolbox.append(toolbar);
    }
};

/**
*   AUXILIARY FUNCTIONS
*/
	
function _extractInfoFromObject(obj) {
    var info = '';
    //var separator = '<p>';
    for(var attr in obj) {
        if(attr == 'name' || attr == 'id' || attr.substr(-3, 3) == '_id' || attr.substr(-6, 6) == '_count') continue; // Skip attributes 'name', 'id' and references ending in '_id'
        info += '<p><b>' + attr[0].toUpperCase() + attr.substring(1) + ":</b> " + obj[attr] + '</p>';
    //separator = ' | '
    }
    return info;
}
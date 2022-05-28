/**
Add some extensions to AmCharts objects
*/
(function() {

    // add a function to add events of one type
    AmCharts.DataSet.prototype.addEvents = function(type, events) {
        var typeIndex = EventsBuilder.indexOf(type);
        this.eventsMapping[typeIndex] = new Array(events.length);
        for(var i=0;i<events.length;i++) {
            this.stockEvents.push(events[i]);
            this.eventsMapping[typeIndex][i] = this.stockEvents.length - 1;
        }
    };
	
    // add a function to remove all events of one type
    AmCharts.DataSet.prototype.removeEvents = function(type) {
        var typeIndex = EventsBuilder.indexOf(type);
		
        if(this.eventsMapping[typeIndex] && this.eventsMapping[typeIndex].length != 0) {
            var nEvents = this.eventsMapping[typeIndex].length;
            var firstEvent = this.eventsMapping[typeIndex][0];
            var higher = firstEvent + nEvents - 1;
			
            // remove event
            this.stockEvents.splice(firstEvent, nEvents);
			
            // update mapping
            $.each(this.eventsMapping, function(eId, events) {
                if(events == undefined) return;
                for(var k=0;k<events.length;k++) {
                    if(events[k] < higher) break;
                    events[k] = events[k] - nEvents;
                }
            });
            this.eventsMapping[typeIndex].splice(0, nEvents);
        }
    };
	
    /*
            Stock Chart
    */
    AmCharts.AmStockChart.prototype.addEvents = function(type) {
        var dataSets = this.dataSets;
        for(var i=0;i<dataSets.length;i++) {
            var graph = this.panels[0].stockGraphs[0];//TODO: This works only for charts with one panel and one graph. Find out how to extend this!!!
            dataSets[i].addEvents(type, EventsBuilder[type](dataSets[i].dataProvider, dataSets[i].title, graph));
        }
    };
	
    AmCharts.AmStockChart.prototype.removeEvents = function(type) {
        var dataSets = this.dataSets;
        for(var i=0;i<dataSets.length;i++) {
            dataSets[i].removeEvents(type);
        }
    };
	
    AmCharts.AmStockChart.prototype.addStatGuide = function(type, label, startDate, endDate) {	
        var dataProvider = this.mainDataSet.dataProvider;// For main dataset only
        var indicator = this.mainDataSet.fieldMappings[0].fromField;
		
        startDate = startDate || dataProvider[0]["DATETIME"];
        endDate = endDate || dataProvider[dataProvider.length - 1]["DATETIME"];
				
        //Draw guide for average value
        var value = roundNumber(EventsBuilder[type](dataProvider, indicator, startDate, endDate), 2);
        var guide = new AmCharts.Guide();
        guide.above = true;
        guide.value = value;
        guide.lineColor = /*this.mainDataSet.color*/"#999999";
        guide.label = label + ": " + value;
        guide.inside = false;
        guide.lineAlpha = 1;
        guide.lineThickness = 2;
        guide.inside = false;
        guide.dashLength = 0;
        guide.above = true;
        guide.labelRotation = 0;
        guide.balloonText = value;
		
        var panel = this.panels[0];
        panel.valueAxes[0].addGuide(guide);
		
        return guide;
    };
	
    AmCharts.AmStockChart.prototype.removeStatGuide = function(guide) {
        var panel = this.panels[0];
        panel.valueAxes[0].removeGuide(guide);
    };
	
	
    AmCharts.AmStockChart.prototype.showStatsInCurrentInterval = function() {
        var _this = this;
		
        //Validate
        var OK = !_this.currentAverageValueGuide // No guide already shown
        //&& _this.comparedDataSets.length == 0; // No datasets being compared

        if(OK) {
            var startDate = _this.currentStartDate;
            var endDate = _this.currentEndDate;
			
            var dataProvider = _this.mainDataSet.dataProvider;
            var indicator = _this.mainDataSet.fieldMappings[0].fromField;
			
            var graph = _this.panels[0].stockGraphs[0];//TODO: This works only for charts with one panel and one graph. Find out how to extend this!!!
            _this.mainDataSet.addEvents("max", EventsBuilder.max(dataProvider, indicator, graph, startDate, endDate));
            _this.mainDataSet.addEvents("min", EventsBuilder.min(dataProvider, indicator, graph, startDate, endDate));
            
            _this.currentAverageValueGuide = _this.addStatGuide("avg", "Average Value", startDate, endDate);
			
            return true;
        }   
		
        return false; 
    };
	
    AmCharts.AmStockChart.prototype.hideStatsInCurrentInterval = function() {
        var _this = this;
	    
        //Validate
        var OK = _this.currentAverageValueGuide // The guide is shown
        //&& _this.comparedDataSets.length == 0; // No datasets being compared
		
        if(OK) {
            _this.mainDataSet.removeEvents("max");
            _this.mainDataSet.removeEvents("min");
	    
            _this.removeStatGuide(_this.currentAverageValueGuide);
            _this.currentAverageValueGuide = undefined;
	        
            return true;
        }
	    
        return false;
    };
	
    AmCharts.AmStockChart.prototype.setCursorPan = function() {
        this.chartCursorSettings.pan = true;
        this.chartCursorSettings.zoomable = false;
        this.validateNow();
    };
	
    AmCharts.AmStockChart.prototype.setCursorZoom = function() {
        this.chartCursorSettings.pan = false;
        this.chartCursorSettings.zoomable = true;
        this.validateNow();
    };
	
    AmCharts.AmStockChart.prototype.addPanSelectSwitch = function() {
        this.panSelectSwitch = true;
    };
	
    AmCharts.AmStockChart.prototype.draw = function(where) {
        var _this = this;
		
        this.placement = where;
		
        // Loading message
        $("#" + where).block({
            message: 'Loading chart...'/*'<h1><img src="2.gif" /></h1>'*/, 
            css:{
                backgroundColor:'transparent', 
                border:"0px"
            }, 
            overlayCSS:{
                backgroundColor:'transparent'
            }
        });	
        setTimeout(function() {// We must wait some time to start painting the chart, since it freezes the screen and a bad effect is seen otherwise
            _this.write(where);
            
            // Custom styles
            /*$('.amChartsDataSetSelector select').addClass('form-control');
            $('.amChartsCompareList').addClass('rounded-corners container widden');
            if(_this.dataSetSelector != null) _this.dataSetSelector.addListener('dataSetSelected', function(e) {
                $('.amChartsDataSetSelector select').addClass('form-control');
                $('.amChartsCompareList').addClass('rounded-corners container widden');
            });
            if(_this.dataSetSelector != null) _this.dataSetSelector.addListener('dataSetCompared', function(e) {
                $('.amChartsDataSetSelector select').addClass('form-control');
                $('.amChartsCompareList').addClass('rounded-corners container widden');
            });
            if(_this.dataSetSelector != null) _this.dataSetSelector.addListener('dataSetUncompared', function(e) {
                $('.amChartsDataSetSelector select').addClass('form-control');
                $('.amChartsCompareList').addClass('rounded-corners container widden');
            });
            $('.amChartsInputField').addClass('rounded-corners');*/
            
            $("#" + where).unblock();
            _this.animationPlayed = true; // animate only on first drawing            
        }, 500);
		
        // Add listener to 'zoomed'
        var justValidated = false;
        _this.addListener("zoomed", function(e) {
            /**
            * Since we may invoke validateNow() function inside the zoomed event, these functions may start
            * calling each other recursively. The following line is a workaround to prevent this to happen.  
            */
            if(justValidated) {
                justValidated = false;
                return;
            }
		    
            _this.currentStartDate = e.startDate;
            _this.currentEndDate = e.endDate;
		    
            if(_this.showStatsOnInterval) {
                var hid = _this.hideStatsInCurrentInterval();
                var showed = _this.showStatsInCurrentInterval();
	            
                if(hid || showed) {
                    justValidated = true;
                    _this.validateData();
                //_this.validateData();
                }
            }            
        });

        /*_this.addListener("clickGraphItem", function(e) {
           alert('clicked');           
        });*/
	    
        if(_this.dataSetSelector) _this.dataSetSelector.addListener("dataSetSelected", function(e) {
            //_this.zoom(_this.currentStartDate, _this.currentEndDate);
        });
		
        // Add pan/select selectors
        /*if(this.panSelectSwitch) {
            var radioGroupName = "selpan-" + where;// we get here a unique name, since 'where' should be an id
            var select = $("<input type='radio' name='" + radioGroupName + "' checked='true'><span>Select</span>");
            var pan = $("<input type='radio' name='" + radioGroupName + "'><span>Pan</span>");
            select.click(function() {
                    _this.setCursorZoom();
            });
            pan.click(function() {
                    _this.setCursorPan();
            });			

            var panSelectBox = $("<div class='pan-select-box'></>").append(select).append(pan);

            // Remove any previously created selectors and add the new one
            var parent = $("#" + where).parent();
            $("div", parent).remove('.pan-select-box');

            parent.append(panSelectBox);
        }*/
    };
})();

// Create extension for AmCharts DataSet. This extension adds an array that represents the mapping for each type
// of event in the dataset. This mapping takes the form: ... TODO
function ExtendedDataSet() {
    AmCharts.DataSet.call(this);
    this.eventsMapping = [];
}

function ExtendedStockChart(options) {
    AmCharts.AmStockChart.call(this);
	
    var _this = this;
	
    this.maxPlots = (options != null && options.maxPlots != null)?options.maxPlots:'all';
    this.maxPlotsErrorTolerance = (options != null && options.maxPlotsErrorTolerance != null)?options.maxPlotsErrorTolerance:0;
	
    // When true, some stats are shown for the current x-interval.
    this.showStatsOnInterval = false;
	
    // Interval average guide
    this.currentAverageValueGuide = undefined;
    this.currentStartDate = undefined;
    this.currentEndDate = undefined;
	
    // Add listener to 'dataUpdated' to setup some default values for the chart
    this.addListener('dataUpdated', function(e) {
        if(_this.updated) return; // avoid this if chart was already updated the first time
        
        // Zoom to max number of plots
        if(_this.maxPlots != 'all') {
            var dataProvider = _this.dataSets[0].dataProvider; // TODO: is it ok to use the first dataset???
            if(dataProvider.length > _this.maxPlots && 
                dataProvider.length - _this.maxPlots > _this.maxPlotsErrorTolerance) {
                _this.zoom(dataProvider[0]["DATETIME"], dataProvider[_this.maxPlots]["DATETIME"]);
                
                // Show warning telling the user that the chart has been zoomed down
                var whereNotice = $('#' + _this.placement + ' .amChartsPanel :first'); // Put the message in the first panel
                var container = $("<div id='chart-flash-notice' style='display:none;position:absolute;top:0;opacity:0.9;width:" + whereNotice.width() + "px;text-align:center'></div>");
                var notice = $("<div class='alert alert-info alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>This chart is showing the first <b>" + _this.maxPlots + " samples only</b>.</div>");
                var fullzoom = $("<a href='#!' class='alert-link'>&ndash; Show all</a>.").click(function() {
                    _this.zoom(dataProvider[0]["DATETIME"],  dataProvider[dataProvider.length - 1]["DATETIME"]); // TODO: [OPTIMIZE] Is there a better method to zoom the chart
                    container.slideUp(300);
                });
                
                notice.append(fullzoom);                
                container.append(notice);
                whereNotice.append(container);
                
                setTimeout(function(){
                    container.slideDown(300);
                    
                    setTimeout(function(){
                        container.slideUp(300);
                    }, 10000);
                }, 3000);
                
                
            }
        }
        
        _this.updated = true;
    });
	
    // Whether to draw a pan/select box
    this.panSelect = false;
}

// create custom dataset that inherits from DataSet
ExtendedDataSet.prototype = new AmCharts.DataSet();

//create custom stock chart that inherits from AmStockChart
ExtendedStockChart.prototype = new AmCharts.AmStockChart();


/** 
Events Builder 
*/
var EventsBuilder = {
	
    indexOf: function(eType) {
        if(eType == "max") return 0;
        else if(eType == "min") return 1;
        return -1;
    },

    max: function(dataProvider, indicator, graph, startDate, endDate) {
        // the events to return
        var events = [];
		
        extremeData = findExtremePoints(dataProvider, indicator, "max", startDate, endDate);
        var maxIndexes = extremeData.indexes;
        var maxValue = extremeData.value;
		
        //TODO: move the creation of the events to the outside and only record the indexes (return maxIndexes)
        for(var j=0;j<maxIndexes.length;j++){
            var value = Number(maxValue);
            var eMax = {
                date: dataProvider[maxIndexes[j]]["DATETIME"],
                type: "arrowDown",
                backgroundColor: "#333300",
                graph: graph,
                //color: "#FFFFFF",
                text: "Max: " + value,
                description: "Max. value: " + value + "\nTime: " + dataProvider[maxIndexes[j]]["DATE"] + " (" + dataProvider[maxIndexes[j]]["TIME"] + ")"
            };
            events.push(eMax);
        }
		
        return events;
    },
	
    min: function(dataProvider, indicator, graph, startDate, endDate) {
        // the events to return
        var events = [];
		
        extremeData = findExtremePoints(dataProvider, indicator, "min", startDate, endDate);
        var minIndexes = extremeData.indexes;
        var minValue = extremeData.value;
		
        //TODO: move the creation of the events to the outside and only record the indexes (return minIndexes)
        for(var j=0;j<minIndexes.length;j++){
            var value = Number(minValue);
            var eMin = {
                date: dataProvider[minIndexes[j]]["DATETIME"],
                type: "arrowUp",
                backgroundColor: "#CCCCCC",
                graph: graph,
                //color: "#FFFFFF",
                text: "Min: " + value,
                description: "Min. value: " + value + "\nTime: " + dataProvider[minIndexes[j]]["DATE"] + " (" + dataProvider[minIndexes[j]]["TIME"] + ")"
            };
            events.push(eMin);
        }
		
        return events;
    },
	
    avg: function(dataProvider, indicator, startDate, endDate) {
        startDate = startDate || dataProvider[0]["DATETIME"];
        endDate = endDate || dataProvider[dataProvider.length - 1]["DATETIME"];
	    
        var avg = 0;
        var n = 0;
        for(var i=0,l=dataProvider.length; i<l;i++) {
            if(dataProvider[i]["DATETIME"] >= startDate) {
                var v = Number(dataProvider[i][indicator]);
                avg += Number(dataProvider[i][indicator]);
                n++;
            }
            if(dataProvider[i]["DATETIME"] >= endDate) {
                break; 
            }
        }
        avg /= n;
        return avg; 
    },
	
    stats: function(dataProvider, indicator, startDate, endDate) {
	    
    }
};

function findExtremePoints(dataProvider, indicator, extremeType, startDate, endDate) {
    startDate = startDate || dataProvider[0]["DATETIME"];
    endDate = endDate || dataProvider[dataProvider.length - 1]["DATETIME"];

    // find all max values for indicator
    var currentExtremeIndexes = [];
    currentExtremeIndexes[0] = 0;
    var currentExtremes = [];
    currentExtremes[0] = extremeType == "max"?-1000000:1000000/*dataProvider[0][indicator]*/;// Big Values
    for(var j=0;j<dataProvider.length;j++) {
        if(dataProvider[j]["DATETIME"] >= startDate) {
            if((extremeType == "max" && Number(dataProvider[j][indicator]) > Number(currentExtremes[0])) ||
                (extremeType == "min" && Number(dataProvider[j][indicator]) < Number(currentExtremes[0]))) {
                currentExtremeIndexes.splice(0, currentExtremeIndexes.length)
                currentExtremes.splice(0, currentExtremes.length)
                currentExtremeIndexes[0] = j;
                currentExtremes[0] = dataProvider[j][indicator];
            } else if(Number(dataProvider[j][indicator]) == Number(currentExtremes[0])) {
                currentExtremeIndexes.push(j);
                currentExtremes.push(dataProvider[j][indicator]);
            }
        } 
        if(dataProvider[j]["DATETIME"] >= endDate) {
            break; 
        }
    }
    //alert(extremeType + ": " + currentExtremes[0]);
    return {
        indexes:currentExtremeIndexes, 
        value: currentExtremes[0]
        };
}

function roundNumber(number, decimals) { // Arguments: number to round, number of decimal places
    var newnumber = new Number(number+'').toFixed(parseInt(decimals));
    return newnumber;
}

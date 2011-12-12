<?php echo $this->html->script(array('highcharts.js')); ?>
<div class="grid_16">
	<h2 id="page-heading">Reports</h2>
</div>

<div class="clear"></div>
	
<script type="text/javascript" charset="utf-8">
	var chart; // global
	/**
	 * Request data from the server, add it to the graph and set a timeout to request again
	 */
	function requestData() {
	    $.ajax({
	        url: '/reports/cart',
	        success: function(point) {
	            var series = chart.series[0];
	            var shift = series.data.length > 20; // shift if the series is longer than 20
	            // add the point
	            chart.series[0].addPoint(eval(point), true, shift);

	            // call it again after one second
	            setTimeout(requestData, 1000);
	        },
	        cache: false
	    });
	}
$(document).ready(function() {
    chart = new Highcharts.Chart({
        chart: {
            renderTo: 'chart-container',
            defaultSeriesType: 'line',
            events: {
                load: requestData
            }
        },
        title: {
            text: 'Live cart data'
        },
        xAxis: {
            type: 'datetime',
            tickPixelInterval: 150,
            maxZoom: 20 * 1000
        },
        yAxis: {
            minPadding: 0.2,
            maxPadding: 0.2,
            title: {
                text: 'Value',
                margin: 80
            }
        },
        series: [{
            name: 'Active Carts',
            data: []
        }]
    });
});
</script>
<div id="chart-container" style="width: 100%; height: 400px"></div>
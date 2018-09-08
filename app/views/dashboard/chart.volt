{% extends "layouts/base.volt" %}

{% block jsfile %}
{{ javascript_include("/assets/lib/flot/jquery.flot.js") }}
{{ javascript_include("/assets/lib/flot/jquery.flot.time.js") }}
{{ javascript_include("/assets/lib/flot/jquery.flot.crosshair.js") }}
{{ javascript_include("/assets/lib/pickadate/picker.js") }}
{{ javascript_include("/assets/lib/pickadate/picker.date.js") }}
{{ javascript_include("/assets/app/js/script.js") }}
{% endblock %}

{% block cssfile %}
  {{ stylesheet_link("/pickadate/themes/classic.css") }}
  {{ stylesheet_link("/pickadate/themes/classic.date.css") }}
{% endblock %}

{% block main %}
<style>
  #statsbox .numval { font-size: 24px; text-align: right; }
  #statsbox .label  { font-size: 12px; text-align: right; }
  .bg-box1 { border: 5px solid #eee; }
  .bg-box2 { border: 5px solid #eee; }
  .bg-box3 { border: 5px solid #eee; }
  .bg-box4 { border: 5px solid #eee; }
</style>

<div id="statsbox" class="w3-row-padding w3-margin-bottom">
  <div class="w3-col" style="width:33%">
    <div class="w3-container bg-box1">
      <div class="w3-right w3-padding-12">
        <div class="numval">{{ data['project_count'] }}</div>
        <div class="label">Total Number of Generators</div>
      </div>
    </div>
  </div>
  <div class="w3-col" style="width:33%">
    <div class="w3-container bg-box1">
      <div class="w3-left"><i class="fa fa-line-chart w3-xxxlarge w3-opacity w3-margin-top"></i></div>
      <div class="w3-right w3-padding-12">
        <div class="numval">{{ data['generators'] }}</div>
        <div class="label">Number of Running Generators</div>
      </div>
    </div>
  </div>
  <div class="w3-col" style="width:33%">
    <div class="w3-container bg-box2">
      <div class="w3-left"><i class="fa fa-bar-chart w3-xxxlarge w3-opacity w3-margin-top"></i></div>
      <div class="w3-right w3-padding-12">
        <div class="numval">{{ data['power'] }}</div>
        <div class="label">Total Generator Power, MWAC</div>
      </div>
    </div>
  </div>
</div>

<div class="w3-container">
  <div id="chart-content">
    {#
    <div id="header">
      <h2>{{ pageTitle }}</h2>
	</div>
    #}
    <div id="chart1">
      <div class="chart-container">
        <div id="placeholder1" class="chart-placeholder"></div>
      </div>
    </div>
  </div>
</div>
{% endblock %}

{% block csscode %}
h2 {
    border: 1px solid lightgrey;
    background-color: rgb(240,240,240);
    color: black;
    padding: 5px;
    text-align: center;
}
.chart-tooltip {
    float: right;
    background-color: rgb(54, 162, 235);
    color: white;
    padding: 3px 10px;
    border: 1px solid #fe0;
}
{% endblock %}

{% block jscode %}
var updateLegendTimeout = null;
var latestPosition = null;
var currentTarget;

function updateLegend() {

    updateLegendTimeout = null;

    var plot;
    var tooltip;

    if (currentTarget == 'placeholder1') {
        plot = plot1;
        tooltip = $("#tooltip1");
    } else {
        plot = plot2;
        tooltip = $("#tooltip2");
    }

    var pos = latestPosition;

    var axes = plot.getAxes();
    if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max ||
        pos.y < axes.yaxis.min || pos.y > axes.yaxis.max) {
        return;
    }

    var i, j, ts, vals = [], dataset = plot.getData();
    for (i = 0; i < dataset.length; ++i) {

        var series = dataset[i];

        // Find the nearest points, x-wise

        for (j = 0; j < series.data.length; ++j) {
            if (series.data[j][0] > pos.x) {
                break;
            }
        }

        // Now Interpolate

        var y,
            ts = series.data[j][0],
            p1 = series.data[j - 1],
            p2 = series.data[j];

        if (p1 == null) {
            y = p2[1];
        } else if (p2 == null) {
            y = p1[1];
        } else {
            y = p1[1] + (p2[1] - p1[1]) * (pos.x - p1[0]) / (p2[0] - p1[0]);
        }
        vals[i] = y.toFixed(0);
    }

    var date = new Date();
    date.setTime(ts);
    timeStr = date.toUTCString().substr(-12, 5);

    var valstr = timeStr + " Power=" + vals[0] + ", " + "Irradiance=" + vals[1];
    tooltip.text(valstr);
}
{% endblock %}

{% block domready %}

var line1 = {
    label: "Total Power, kW",
    data: {{ power }},
    color: "#c00000",
    shadowSize: 0,
    yaxis: 2,
    lines: { show: true, lineWidth: 1 }
}

var options = {
    series: {
        shadowSize: 0	// Drawing is faster without shadows
    },
    crosshair: {
        mode: "x"
    },
    grid: {
        //hoverable: true,
        //clickable: true
        autoHighlight: false
    },
    yaxes: [
        { position: "left", min: 0 },
    ],
    xaxis: {
        mode: 'time',
        show: true
    }
}

plot1 = $.plot("#placeholder1", [ line1 ], options);

$(".chart-placeholder").bind("plothover", function (event, pos, item) {
    currentTarget = event.currentTarget.id;
    latestPosition = pos;
    if (!updateLegendTimeout) {
        //updateLegendTimeout = setTimeout(updateLegend, 50);
    }
});

{% endblock %}

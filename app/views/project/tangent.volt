<!DOCTYPE html>
<html>
<head>
<meta charset=utf-8>
<title>Project Details</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<style>
  .w3-bordered td { border: 1px solid #ccc; padding: 5px; vertical-align: middle; }
  .w3-bordered th { border: 1px solid #ccc; padding: 5px; vertical-align: middle; }
  #my-table th, #my-table td { border: none; background-color: #222b2e; color: white; }
  #my-table th.brgrey { background-color: #2f3b40; color: white; }
  .ltgrey { background-color: #273236; color: white; }
  td span { display: block; font-size: 8px; text-align: right; color: #888; }
  td div { text-align: right; }
  .textfield, .label { text-align: center; }
  .label { font-size: 16px; font-weight: bold; position: relative; top: -20px; }
  .textfield { position: relative; top: -80px; }
</style>
</head>
<body>
  <h1 class="w3-center">Project Details</h1>
  <div class="w3-container w3-padding-24">
    <table class="w3-table w3-bordered">
      <tr>
        <th>Project Details</th>
        <td colspan="2">&nbsp;</td>
        <td rowspan="24" class="ltgrey">
          <div class="w3-row">
            <div class="w3-col w3-twothird">
              <div class="w3-row">
                <div class="w3-third w3-center w3-container gauge" id="gauge1">
                  <canvas width=200 height=200 id="gauge1-canvas"></canvas>
                  <div class="textfield"></div>
                  <div class="label">Utility Power</div>
                </div>
                <div class="w3-third w3-center w3-container gauge" id="gauge2">
                  <canvas width=200 height=200 id="gauge2-canvas"></canvas>
                  <div class="textfield"></div>
                  <div class="label">Utility Voltage</div>
                </div>
                <div class="w3-third w3-center w3-container gauge" id="gauge3">
                  <canvas width=200 height=200 id="gauge3-canvas"></canvas>
                  <div class="textfield"></div>
                  <div class="label">Generator Voltage</div>
                </div>
              </div>

              <div class="w3-row">
                <div class="w3-third w3-center w3-container gauge" id="gauge4">
                  <canvas width=200 height=200 id="gauge4-canvas"></canvas>
                  <div class="textfield"></div>
                  <div class="label">Generator Frequency</div>
                </div>
                <div class="w3-third w3-center w3-container gauge" id="gauge5">
                  <canvas width=200 height=200 id="gauge5-canvas"></canvas>
                  <div class="textfield"></div>
                  <div class="label">Battery Voltage</div>
                </div>
                <div class="w3-third w3-center w3-container gauge" id="gauge6">
                  <canvas width=200 height=200 id="gauge6-canvas"></canvas>
                  <div class="textfield"></div>
                  <div class="label">RPM</div>
                </div>
              </div>

              <div class="w3-row">
                <div class="w3-third w3-center w3-container gauge" id="gauge7">
                  <canvas width=200 height=200 id="gauge7-canvas"></canvas>
                  <div class="textfield"></div>
                  <div class="label">Oil Pressure</div>
                </div>
                <div class="w3-third w3-center w3-container gauge" id="gauge8">
                  <canvas width=200 height=200 id="gauge8-canvas"></canvas>
                  <div class="textfield"></div>
                  <div class="label">Oil Temperature</div>
                </div>
                <div class="w3-third w3-center w3-container gauge" id="gauge9">
                  <canvas width=200 height=200 id="gauge9-canvas"></canvas>
                  <div class="textfield"></div>
                  <div class="label">Coolant Temperature</div>
                </div>
              </div>
            </div>

            <div class="w3-col w3-third dkgrey">
              <table id="my-table">
                <tr>
                  <th colspan="2" class="brgrey">System Status</th>
                </tr><tr>
                  <th>Control Switch Poistion</th>
                  <td>
                    <div>Auto</div>
                    <span>01/13/2020 02:42 PM</span>
                  </td>
                </tr><tr>
                  <th>Utility Breaker Position</th>
                  <td>
                    <div>Closed</div>
                    <span>01/13/2020 02:42 PM</span>
                  </td>
                </tr><tr>
                  <th>Generator Breaker Position</th>
                  <td>
                    <div>Open</div>
                    <span>01/13/2020 02:42 PM</span>
                  </td>
                </tr><tr>
                  <th>Utility Voltage</th>
                  <td>
                    <div>610.0 V</div>
                    <span>01/13/2020 02:42 PM</span>
                  </td>
                </tr><tr>
                  <th>Utility Power</th>
                  <td>
                    <div>377.0 kW</div>
                    <span>01/13/2020 02:42 PM</span>
                  </td>
                </tr><tr>
                  <th>Generator Voltage</th>
                  <td>
                    <div>0.0 V</div>
                    <span>01/13/2020 02:42 PM</span>
                  </td>
                </tr><tr>
                  <th>Generator Power</th>
                  <td>
                    <div>0.0 kW</div>
                    <span>01/13/2020 02:42 PM</span>
                  </td>
                </tr><tr>
                  <th>Generator Frequency</th>
                  <td>
                    <div>0.0 Hz</div>
                    <span>01/13/2020 02:42 PM</span>
                  </td>
                </tr><tr>
                  <th>Battery Voltage</th>
                  <td>
                    <div>26.8 V</div>
                    <span>01/13/2020 02:42 PM</span>
                  </td>
                </tr><tr>
                  <th>RPM</th>
                  <td>
                    <div>0.0 RPM</div>
                    <span>01/13/2020 02:42 PM</span>
                  </td>
                </tr><tr>
                  <th>Oil Pressure</th>
                  <td>
                    <div>0.0 psi</div>
                    <span>01/13/2020 02:42 PM</span>
                  </td>
                </tr><tr>
                  <th>Oil Temperature</th>
                  <td>
                    <div>77.0 F</div>
                    <span>01/13/2020 02:42 PM</span>
                  </td>
                </tr><tr>
                  <th>Coolan Temperature</th>
                  <td>
                    <div>118.4 F</div>
                    <span>01/13/2020 02:42 PM</span>
                  </td>
                </tr><tr>
                  <th>Retransfer Timer</th>
                  <td>
                    <div>1260.0 s</div>
                    <span>01/13/2020 02:42 PM</span>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </td>
      </tr>

      <tr><th>Project Name</th><td colspan="2">&nbsp;</td></tr>
      <tr><th>Project Address</th><td colspan="2">&nbsp;</td></tr>
      <tr><th>Project Size</th><td colspan="2">&nbsp;</td></tr>
      <tr><th>Store Number</th><td colspan="2">&nbsp;</td></tr>
      <tr><th>Operation Mode</th><td colspan="2">&nbsp;</td></tr>

      <tr>
          <th>Description</th>
          <th class="w3-center">Utility Main</th>
          <th class="w3-center">Generator</th>
      </tr><tr>
          <th>Total Power</th>
          <td class="w3-center">kW</td>
          <td class="w3-center">kW</td>
      </tr><tr>
          <th>Average Voltage</th>
          <td class="w3-center">V</td>
          <td class="w3-center">V</td>
      </tr><tr>
          <th>Power Factor</th>
          <td class="w3-center">pF</td>
          <td class="w3-center">pF</td>
      </tr><tr>
          <th>Last Run's Real Energy</th>
          <td class="w3-center">kWh</td>
          <td class="w3-center">kWh</td>
      </tr><tr>
          <th>Yesterday's Real Energy</th>
          <td class="w3-center">kWh</td>
          <td class="w3-center">kWh</td>
      </tr><tr>
          <th>Month to Date Real Energy</th>
          <td class="w3-center">kWh</td>
          <td class="w3-center">kWh</td>
      </tr><tr>
          <th>Total Real Energy</th>
          <td class="w3-center">kWh</td>
          <td class="w3-center">kWh</td>
      </tr><tr>
          <th>Urea Level</th>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
      </tr><tr>
          <th>Site</th>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
      </tr><tr>
          <th>Control Switch Position</th>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
      </tr><tr>
          <th>Utility Breaker Position</th>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
      </tr><tr>
          <th>Generator Breaker Position</th>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
      </tr><tr>
          <th>RPM</th>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
      </tr><tr>
          <th>Oil Pressure</th>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
      </tr><tr>
          <th>Oil Temperature</th>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
      </tr><tr>
          <th>Oil Temperature</th>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
      </tr><tr>
          <th>Coolan Temperature</th>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
      </tr>
    </table>
  </div>

{% block jsfile %}
{{ javascript_include("/assets/lib/gauge/gauge.min.js") }}
{% endblock %}

<script type='text/javascript' src='/assets/lib/jquery/jquery-2.1.0.min.js'></script>

<script>
  function initGauge(canvas, textfield) {
    gauge = new Gauge(canvas);
    var opts = {
      angle: -0.25,
      lineWidth: 0.02,
      radiusScale:0.9,
      pointer: {
        length: 0.5,
        strokeWidth: 0.01,
        color: '#cccccc'
      },
      staticLabels: {
        font: "10px sans-serif",
        labels: [200, 500, 2100, 2800],
        fractionDigits: 0
      },
      staticZones: [
         {strokeStyle: "#F03E3E", min: 0, max: 200},
         {strokeStyle: "#FFDD00", min: 200, max: 500},
         {strokeStyle: "#30B32D", min: 500, max: 2100},
         {strokeStyle: "#FFDD00", min: 2100, max: 2800},
         {strokeStyle: "#F03E3E", min: 2800, max: 3000}
      ],
      limitMax: false,
      limitMin: false,
      highDpiSupport: true
    };
    gauge.setOptions(opts);
    gauge.setTextField(textfield);
    gauge.minValue = 0;
    gauge.maxValue = 3000;
    gauge.set(Math.random()*2500);
  };
</script>

<script>
  $(document).ready(function() {
    $('.gauge').each(function() {
      var canvas = $(this).find('canvas')[0];
      var textfield = $(this).find('.textfield')[0];
      initGauge(canvas, textfield);
    })
  });
</script>

</body>
</html>

{% extends "layouts/public.volt" %}

{% block csscode %}
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
{% endblock %}

{% block main %}
  <!-- h2 class="w3-center">Project Details</h2 -->
  <div class="w3-container">
    <table class="w3-table w3-bordered">
      <tr>
        <th>Project Details</th>
        <td colspan="2">{{ project.siteName }}</td>
        <td rowspan="26" class="ltgrey">
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
                  <th>Control Switch Position</th>
                  <td>
                    <div>Auto</div>
                    <span>{{ data['time_utc'] }}</span>
                  </td>
                </tr><tr>
                  <th>Utility Breaker Position</th>
                  <td>
                    <div>Closed</div>
                    <span>{{ data['time_utc'] }}</span>
                  </td>
                </tr><tr>
                  <th>Generator Breaker Position</th>
                  <td>
                    <div>Open</div>
                    <span>{{ data['time_utc'] }}</span>
                  </td>
                </tr><tr>
                  <th>Utility Voltage</th>
                  <td>
                    <div>{{ data['Util_VLL_Avg'] }} V</div>
                    <span>{{ data['time_utc'] }}</span>
                  </td>
                </tr><tr>
                  <th>Utility Power</th>
                  <td>
                    <div>{{ data['Util_kW']}} kW</div>
                    <span>{{ data['time_utc'] }}</span>
                  </td>
                </tr><tr>
                  <th>Generator Voltage</th>
                  <td>
                    <div>{{ data['Gen_VLL_Avg'] }} V</div>
                    <span>{{ data['time_utc'] }}</span>
                  </td>
                </tr><tr>
                  <th>Generator Power</th>
                  <td>
                    <div>{{ data['Gen_Total_kW']}} kW</div>
                    <span>{{ data['time_utc'] }}</span>
                  </td>
                </tr><tr>
                  <th>Generator Frequency</th>
                  <td>
                    <div>{{ data['Gen_Frequency'] }}  Hz</div>
                    <span>{{ data['time_utc'] }}</span>
                  </td>
                </tr><tr>
                  <th>Battery Voltage</th>
                  <td>
                    <div>{{ data['Battery_Voltage'] }} V</div>
                    <span>{{ data['time_utc'] }}</span>
                  </td>
                </tr><tr>
                  <th>RPM</th>
                  <td>
                    <div>{{ data['Engine_RPM'] }} RPM</div>
                    <span>{{ data['time_utc'] }}</span>
                  </td>
                </tr><tr>
                  <th>Oil Pressure</th>
                  <td>
                    <div>{{ data['Oil_Pressure'] }} psi</div>
                    <span>{{ data['time_utc'] }}</span>
                  </td>
                </tr><tr>
                  <th>Oil Temperature</th>
                  <td>
                    <div>{{ data['Oil_Temp'] }} F</div>
                    <span>{{ data['time_utc'] }}</span>
                  </td>
                </tr><tr>
                  <th>Coolan Temperature</th>
                  <td>
                    <div>{{ data['Coolant_Temp'] }} F</div>
                    <span>{{ data['time_utc'] }}</span>
                  </td>
                </tr><tr>
                  <th>Retransfer Timer</th>
                  <td>
                    <div>1260.* s</div>
                    <span>{{ data['time_utc'] }}</span>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </td>
      </tr>

      <tr><th>Project Address</th><td colspan="2">{{ project.address }}</td></tr>
      <tr><th>Project Size</th><td colspan="2">{{ project.projectSize }}</td></tr>
      <tr><th>Store Number</th><td colspan="2">{{ project.storeNumber }}</td></tr>
      <tr><th>Operation Mode</th><td colspan="2">{{ project.operationMode }}</td></tr>

      <tr><td colspan="3">&nbsp;</td></tr>

      <tr>
          <th>Description</th>
          <th class="w3-center">Utility Main</th>
          <th class="w3-center">Generator</th>
      </tr><tr>
          <th>Total Power</th>
          <td class="w3-center">{{ data['Util_kW'] }} kW</td>
          <td class="w3-center">{{ data['Gen_Total_kW'] }} kW</td>
      </tr><tr>
          <th>Average Voltage</th>
          <td class="w3-center">{{ data['Util_VLL_Avg'] }} V</td>
          <td class="w3-center">{{ data['Gen_VLL_Avg'] }} V</td>
      </tr><tr>
          <th>Power Factor</th>
          <td class="w3-center">{{ data['Util_PF'] }} pF</td>
          <td class="w3-center">{{ data['Gen_Total_PF'] }} pF</td>
      </tr>
      <tr><td colspan="3">&nbsp;</td></tr>
      <tr>
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
      </tr>
      <tr><td colspan="3">&nbsp;</td></tr>
      <tr>
          <th>Urea Level</th>
          <td colspan="2" class="w3-center">{{ data['urea_level'] }}</td>
      </tr>
      <tr><td colspan="3">&nbsp;</td></tr>
      <tr>
          <th>Site</th>
          <td colspan="2">&nbsp;</td>
      </tr><tr>
          <th>Control Switch Position</th>
          <td colspan="2" class="w3-center">{{ data['Control_Switch_Pos'] }}</td>
      </tr><tr>
          <th>Utility Breaker Position</th>
          <td colspan="2" class="w3-center">{{ data['Util_CB_Pos'] }}</td>
      </tr><tr>
          <th>Generator Breaker Position</th>
          <td colspan="2" class="w3-center">{{ data['Gen_CB_Pos'] }}</td>
      </tr><tr>
          <th>RPM</th>
          <td colspan="2" class="w3-center">{{ data['Engine_RPM'] }}</td>
      </tr><tr>
          <th>Oil Pressure</th>
          <td colspan="2" class="w3-center">{{ data['Oil_Pressure'] }}</td>
      </tr><tr>
          <th>Oil Temperature</th>
          <td colspan="2" class="w3-center">{{ data['Oil_Temp'] }}</td>
      </tr><tr>
          <th>Coolan Temperature</th>
          <td colspan="2" class="w3-center">{{ data['Coolant_Temp'] }}</td>
      </tr>
    </table>
  </div>
  <p>&nbsp;</p>
{% endblock %}

{% block jsfile %}
{{ javascript_include("/assets/lib/gauge/gauge.min.js") }}
{% endblock %}

{% block jscode %}
  function initGauge(canvas, textfield, data) {
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
        font: "11px sans-serif",
        color: "#7F7F7F",
        labels: [
          data.maxVal*1/5,
          data.maxVal*2/5,
          data.maxVal*3/5,
          data.maxVal*4/5,
          data.maxVal*5/5,
        ],
        fractionDigits: 0
      },
      staticZones: [
         {strokeStyle: "#F03E3E", min: 0,               max: data.maxVal/5},
         {strokeStyle: "#FFDD00", min: data.maxVal*1/5, max: data.maxVal*2/5},
         {strokeStyle: "#30B32D", min: data.maxVal*2/5, max: data.maxVal*3/5},
         {strokeStyle: "#FFDD00", min: data.maxVal*3/5, max: data.maxVal*4/5},
         {strokeStyle: "#F03E3E", min: data.maxVal*4/5, max: data.maxVal*5/5}
      ],
      limitMax: true,
      limitMin: true,
      highDpiSupport: true
    };
    gauge.setOptions(opts);
    gauge.setTextField(textfield);
    gauge.minValue = 0;
    gauge.maxValue = data.maxVal;
    gauge.set(data.curVal);
  }
{% endblock %}

{% block domready %}
  var guageData = [
    { label: "Utility Power",        maxVal:  1000,  curVal: {{ data['Util_kW'] }}         },
    { label: "Utility Voltage",      maxVal:   800,  curVal: {{ data['Util_VLL_Avg'] }}    },
    { label: "Generator Voltage",    maxVal:  1000,  curVal: {{ data['Gen_VLL_Avg'] }}     },
    { label: "Generator Frequency",  maxVal:   100,  curVal: {{ data['Gen_Frequency'] }}   },
    { label: "Battery Voltage",      maxVal:   100,  curVal: {{ data['Battery_Voltage'] }} },
    { label: "RPM",                  maxVal:  3000,  curVal: {{ data['Engine_RPM'] }}      },
    { label: "Oil Pressure",         maxVal: 10000,  curVal: {{ data['Oil_Pressure'] }}    },
    { label: "Oil Temperature",      maxVal:   200,  curVal: {{ data['Oil_Temp'] }}        },
    { label: "Coolant Temperature",  maxVal:   200,  curVal: {{ data['Coolant_Temp'] }}    },
  ];

  $('.gauge').each(function(i, e) {
    var canvas = $(this).find('canvas')[0];
    var textfield = $(this).find('.textfield')[0];
    initGauge(canvas, textfield, guageData[i]);
  })
{% endblock %}

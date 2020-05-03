{% extends "layouts/base.volt" %}

{% block main %}
<style type="text/css">
  table, th, td { border: 1px solid #ccc; }
  .w3-table td { padding: 3px; vertical-align: middle; }
  .w3-table th { padding: 3px; vertical-align: middle; }
  tr td:not(:first-child) { text-align: center; }
  tr th:not(:first-child) { text-align: center; }
  .w3-table td.noL { border-left: none; }
  .w3-table td.noR { border-right: none; }
  .w3-table th.noL { border-left: none; }
  .w3-table th.noR { border-right: none; }
</style>

<style>
  #statsbox .numval { font-size: 24px; text-align: right; }
  #statsbox .label  { font-size: 12px; text-align: right; }
  .bg-box1 { border: 5px solid #eee; }
  .bg-box2 { border: 5px solid #eee; }
  .bg-box3 { border: 5px solid #eee; }
  .bg-box4 { border: 5px solid #eee; }
</style>

<div id="statsbox" class="w3-row-padding w3-margin-bottom">
  <div class="w3-col" style="width:25%">
    <div class="w3-container bg-box1">
      <div class="w3-left"><i class="fa fa-pie-chart w3-xxxlarge w3-opacity w3-margin-top"></i></div>
      <div class="w3-right w3-padding-12">
        <div class="numval">{{ data['project_count'] }}</div>
        <div class="label">Total Number of Generators</div>
      </div>
    </div>
  </div>
  <div class="w3-col" style="width:25%">
    <a href="/dashboard/chart/generators" target="_blank">
      <div class="w3-container bg-box1">
        <div class="w3-left"><i class="fa fa-line-chart w3-xxxlarge w3-opacity w3-margin-top"></i></div>
        <div class="w3-right w3-padding-12">
          <div class="numval">{{ data['generators'] }}</div>
          <div class="label">Number of Running Generators</div>
        </div>
      </div>
    </a>
  </div>
  <div class="w3-col" style="width:25%">
    <a href="/dashboard/chart/power" target="_blank">
      <div class="w3-container bg-box2">
        <div class="w3-left"><i class="fa fa-bar-chart w3-xxxlarge w3-opacity w3-margin-top"></i></div>
        <div class="w3-right w3-padding-12">
          <div class="numval">{{ data['power'] }}</div>
          <div class="label">Total Generator Power, MWAC</div>
        </div>
      </div>
    </a>
  </div>
  <div class="w3-col" style="width:25%">
    <div class="w3-container bg-box2">
      <div class="w3-left"><i class="fa fa-area-chart w3-xxxlarge w3-opacity w3-margin-top"></i></div>
      <div class="w3-right w3-padding-12">
        <div class="numval">{{ data['peak_energy'] }} MW</div>
        <div class="label">Forecast Peak @{{ data['peak_hour'] }}:00 EST</div>
      </div>
    </div>
  </div>
</div>

{%- macro Green1_Red0(val) %}
  {% if val == 1 %}
    <img src="/assets/app/img/green.png" width="32">
  {% elseif val == 0 %}
    <img src="/assets/app/img/red.png" width="32">
  {% endif %}
{%- endmacro %}

{%- macro Green0_Red1(val) %}
  {% if val == 0 %}
    <img src="/assets/app/img/green.png" width="32">
  {% elseif val == 1 %}
    <img src="/assets/app/img/red.png" width="32">
  {% endif %}
{%- endmacro %}

{%- macro GreenClose1_RedOpen0(val) %}
  {% if val == 1 %}
    <img src="/assets/app/img/green-close.png" width="40">
  {% elseif val == 0 %}
    <img src="/assets/app/img/red-open.png" width="32">
  {% endif %}
{%- endmacro %}

{%- macro RedClose0_GreenOpen1(val) %}
  {% if val == 0 %}
    <img src="/assets/app/img/close-red.jpg" width="40">
  {% elseif val == 1 %}
    <img src="/assets/app/img/open-green.jpg" width="40">
  {% endif %}
{%- endmacro %}

{%- macro Green1_Red0_NA(val) %}
  {% if val == 0 %}
    <img src="/assets/app/img/green.png" width="32">
  {% elseif val == 1 %}
    <img src="/assets/app/img/red.png" width="32">
  {% elseif val == 9 %}
    <img src="/assets/app/img/black-na.png" width="32">
  {% endif %}
{%- endmacro %}

<div class="w3-container">
<table id="snapshot" class="w3-table w3-white w3-bordered w3-border">
{# if data is not empty #}
  <tr class="w3-light-gray">
    <th>Project</th>
    <th class="noL">Generator Status</th>
    <th class="noL noR">Emergency Start<br>Initiated</th>
    <th class="noR">Generator Power<br>kW</th>
    <th class="noL">Store Load<br>kW</th>
    <th class="noR">Generator Breaker<br>(52G) Status</th>
    <th class="noL">Main Breaker<br>(52U) Status</th>
    <th>P&C Alarm</th>
    <th>UREA Level</th>
    <th>Time, EST</th>
  </tr>
  {% for row in data['snapshot'] %}
  <tr>
    {% if (row['devtype'] == 'gcp') %}
    <td><a href="/project/detail/{{ row['project_id'] }}" target="_blank">{{ row['project_name'] }}</a></td>
    {% elseif (row['devtype'] == 'Tangent') %}
    <td>
      <a href="/project/tangent/{{ row['project_id'] }}" target="_blank">{{ row['project_name'] }}</a>
      {% if row['primary_ip'] is not empty %}
        <a href="/tangent/index/{{ row['project_id'] }}" target="_blank" class="w3-right w3-margin-right">
         <!-- i class="fa fa-camera"></i -->
         <img src="/assets/app/img/generator.png" width="24">
        </a>
      {% endif %}
    </td>
    {% else %}
    <td>{{ row['project_name'] }}</td>
    {% endif %}
    <td class="noL">{{ Green0_Red1(row['generator_status']) }}</td>
    <td class="noL noR">{{ Green1_Red0(row['emergency_start']) }}</td>
    <td class="noR">{{ row['generator_power'] }}</td>
    <td class="noL">{{ row['store_load'] }}</td>
    <td class="noR">{{ RedClose0_GreenOpen1(row['generator_breaker_status']) }}</td>
    <td class="noL">{{ RedClose0_GreenOpen1(row['main_breaker_status']) }}</td>
    <td>{{ Green0_Red1(row['project_alarm']) }}</td>
    {% if row['urea_level'] == 0 %}
    <td>N/A</td>
    {% elseif row['urea_level'] < 51 %}
    <td class="w3-text-red" style="font-weight:bold">{{ row['urea_level'] }}%</td>
    {% else %}
    <td>{{ row['urea_level'] }}%</td>
    {% endif %}
    <td>{{ row['time'] }}</td>
  </tr>
  {% endfor %}
{# endif #}
</table>
</div>
{% endblock %}

{% block jscode %}
  function AutoRefresh(t) {
    setTimeout("location.reload(true);", t);
  }
  window.onload = AutoRefresh(1000*60*1);
{% endblock %}

{% block domready %}
{% endblock %}

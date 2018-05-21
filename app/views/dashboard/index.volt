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

{%- macro Green0_Red1_NA(val) %}
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
    <th class="noR">Generator Run<br>Status</th>
    <th class="noL noR">Emergency Start<br>Initiated</th>
    <th class="noL">Remote Start<br>Initiated</th>
    <th class="noR">Generator Power<br>kW</th>
    <th class="noL">Store Load<br>kW</th>
    <th class="noR">Generator Breaker<br>(52U) Status</th>
    <th class="noL">Main Breaker<br>(52G) Status</th>
    <th>Generator Running<br>in Parallel</th>
    <th class="noR">Start Inhibit<br>Status</th>
    <th class="noL noR">Utility Connect<br>Permission</th>
    <th class="noL noR">Utility Allow<br>Connect</th>
    <th class="noL noR">Utility Trip<br>Disconnect Command</th>
    <th class="noL">Utility Block<br>Connect</th>
    <th>Project Alarm</th>
    <th>UREA Level</th>
  </tr>
  {% for row in data %}
  <tr>
    <td><a href="/project/detail/{{ row['project_id'] }}" target="_blank">{{ row['project_name'] }}</a></th>
    <td class="noR">{{ Green1_Red0(row['Genset_Status']) }}</th>
    <td class="noL noR">{{ Green1_Red0(row['Emergency_Mode']) }}</th>
    <td class="noL">{{ Green0_Red1(row['M_Start_Auto']) }}</th>
    <td class="noR">{{ row['M_Gen_real_enrg'] }}</th>
    <td class="noL">{{ row['M_Total_Main_po'] }}</th>
    <td class="noR">{{ GreenClose1_RedOpen0(row['M_Brkr52MAux']) }}</th>
    <td class="noL">{{ GreenClose1_RedOpen0(row['Dig_Input_0']) }}</th>
    <td>{{ Green0_Red1_NA(row['EZ_G_13']) }}</th>
    <td class="noR">{{ Green0_Red1(row['M_Start_Inhibit']) }}</th>
    <td class="noL noR">{{ Green0_Red1(row['RTAC_Perm_Stat']) }}</th>
    <td class="noL noR">{{ Green0_Red1(row['RTAC_Allow']) }}</th>
    <td class="noL noR">{{ Green0_Red1(row['RTAC_Trip']) }}</th>
    <td class="noL">{{ Green0_Red1(row['RTAC_Block']) }}</th>
    <td>{{ Green0_Red1(row['project_alarm']) }}</th>
    <td>{{ row['urea_level'] }}%</th>
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

{% extends "layouts/base.volt" %}

{% block main %}
<style type="text/css">
  table, th, td { border: 1px solid #ddd; }
  .w3-table td { padding: 3px; vertical-align: middle; }
  .w3-table th { padding: 3px; vertical-align: middle; }
  tr td:not(:first-child) { text-align: center; }
  tr th:not(:first-child) { text-align: center; }
</style>

{%- macro Green1_Red0(val) %}
  {% if val == 1 %}
    <img src="/img/green.png" width="40">
  {% elseif val == 0 %}
    <img src="/img/red.png" width="40">
  {% endif %}
{%- endmacro %}

{%- macro GreenClose1_RedOpen0(val) %}
  {% if val == 1 %}
    <img src="/img/green-close.png" width="40">
  {% elseif val == 0 %}
    <img src="/img/red-open.png" width="40">
  {% endif %}
{%- endmacro %}

{%- macro Green0_Red1(val) %}
  {% if val == 0 %}
    <img src="/img/green.png" width="40">
  {% elseif val == 1 %}
    <img src="/img/red.png" width="40">
  {% endif %}
{%- endmacro %}

<div class="w3-container">
<table id="snapshot" class="w3-table w3-white w3-bordered w3-border">
{# if data is not empty #}
  <tr class="w3-light-gray">
    <th>Project</th>
    <th>Generator Run<br>Status</th>
    <th>Emergency Start<br>Initiated</th>
    <th>Remote Start<br>Initiated</th>
    <th>Generator Power<br>kW</th>
    <th>Store Load<br>kW</th>
    <th>Generator Breaker<br>(52U) Status</th>
    <th>Main Breaker<br>(52G) Status</th>
    <th>Generator Running<br>in Parallel</th>
    <th>Start Inhibit<br>Status</th>
    <th>Utility Connect<br>Permission</th>
    <th>Utility Allow<br>Connect</th>
    <th>Utility Trip<br>Disconnect Command</th>
    <th>Utility Block<br>Connect</th>
    <th>Project Alarm</th>
    <th>UREA Level</th>
  </tr>
  {% for row in data %}
  <tr>
    <td>{{ row['project_name'] }}</th>
    <td>{{ row['Genset_Status'] }}</th>
    <td>{{ row['Emergency_Mode'] }}</th>
    <td>{{ row['M_Start_Auto'] }}</th>
    <td>{{ row['Total_Gen_Power'] }}</th>
    <td>{{ row['Total_mains_pow'] }}</th>
    <td>{{ row['Dig_Input_1'] }}</th>
    <td>{{ row['Dig_Input_0'] }}</th>
    <td>{{ row['EZ_G_13'] }}</th>
    <td>{{ row['M_Start_Inhibit'] }}</th>
    <td>{{ row['RTAC_Perm_Stat'] }}</th>
    <td>{{ row['RTAC_Allow'] }}</th>
    <td>{{ row['RTAC_Trip'] }}</th>
    <td>{{ row['RTAC_Block'] }}</th>
    <td>&nbsp;</th>
    <td>&nbsp;</th>
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

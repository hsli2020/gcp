{% extends "layouts/public.volt" %}

{% block main %}
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

{%- macro Green_Red(val, expected) %}
  {% if val == expected %}
    <img src="/assets/app/img/green.png" width="32">
  {% else %}
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

{%- macro Green0_Red1_NA(val) %}
  {% if val == 0 %}
    <img src="/assets/app/img/green.png" width="32">
  {% elseif val == 1 %}
    <img src="/assets/app/img/red.png" width="32">
  {% elseif val == 9 %}
    <img src="/assets/app/img/black-na.png" width="32">
  {% endif %}
{%- endmacro %}

<div class="container">
  <div class="w3-modal" style="display: block;">
    <div class="w3-modal-content w3-card-8 w3-padding" style="max-width:900px">
      <h2>Project Details</h2>

      <table id="details" class="w3-table padding0">
        <tr>
          <td width="20%">Project Name</td>
          <td width="80%">{{ project.siteName }}</td>
        </tr>
        <tr>
          <td>Project Address</td>
          <td>{{ project.address }}</td>
        </tr>
        <tr>
          <td>Project Size</td>
          <td>{{ project.projectSize }}</td>
        </tr>
        <tr>
          <td>Store Number</td>
          <td>{{ project.storeNumber }}</td>
        </tr>
      </table>

      <table class="w3-table w3-bordered w3-border w3-margin-top compact">
        <tr>
          <th width="33%">Description</th>
          <th width="33%">Utility Main</th>
          <th width="33%">Generator</th>
        </tr>
        <tr>
          <td>Total Power, kW</td>
          <td data-tag="20684">{{ data['M_Total_Main_po'] }}</td>
          <td data-tag="20680 ">{{ data['M_Gen_real_enrg'] }}</td>
        </tr>
        <tr>
          <td>Average Current, A</td>
          <td data-tag="20676">{{ data['M_Av_Main_Curnt'] }}</td>
          <td data-tag="20678">{{ data['M_Av_Gen_Crnt'] }}</td>
        </tr>
        <tr>
          <td>Average Voltage, V</td>
          <td data-tag="20670">{{ data['M_Av_Main_Del_V'] }}</td>
          <td data-tag="20664">{{ data['M_Av_Gen_DeltaV'] }}</td>
        </tr>
        <tr>
          <td>Power Factor, pF</td>
          <td data-tag="20668">{{ data['M_Main_power_pf'] }}</td>
          <td data-tag="20660">{{ data['M_Gen_Power_fac'] }}</td>
        </tr>
        <tr><td colspan="3" class="w3-light-gray"></td></tr>
        <tr>
          <td>Last Run's Real Energy, kWh</td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td>Yesterday's Real Energy, kWh</td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td>Month-To-Date Real Energy, kWh</td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td>Total Real Energy, kWh</td>
          <td></td>
          <td></td>
        </tr>
        <tr><td colspan="3" class="w3-light-gray"></td></tr>
        <tr>
          <td>Urea Level</td>
          <td>N/A</td>
          <td>{{ data['urea_level'] }}%</td>
        </tr>
        <tr>
          <td>Hours till Next Maintenance</td>
          <td>N/A</td>
          <td data-tag="20546">{{ data['Hrs_until_maint'] }}</td>
        </tr>
      </table><br>

      <table class="w3-table w3-bordered w3-border w3-margin-top compact">
        <tr>
          <th colspan="3" class="center">Site Status</th>
        </tr>
        <tr>
          <td width="33%">Generator Status</td>
          <td width="33%">{{ Green0_Red1(data['M_Start_Auto']) }}</td>
          <td width="33%"></td>
        </tr>
        <tr>
          <td>Main Breaker Status</td>
          <td>{{ RedClose0_GreenOpen1(data['M_SLD_Brkr52MAux']) }}</td>
          <td></td>
        </tr>
        <tr>
          <td>Generator Breaker Status</td>
          <td>{{ RedClose0_GreenOpen1(data['M_SLD_Gen_Brkr52GAux']) }}</td>
          <td></td>
        </tr>
        <tr>
          <td>Remote Start Initiated</td>
          <td>{{ Green0_Red1(data['M_Start_Inhibit']) }}</td>
          <td></td>
        </tr>
        <tr>
          <td>Engine Status</td>
          <td>{{ Green0_Red1(data['Engin_Fault']) }}</td>
          <td></td>
        </tr>
        <tr>
          <td>Emergency Mode Initiated</td>
          <td>{{ Green1_Red0(data['Emergency_Mode']) }}</td>
          <td></td>
        </tr>
      </table><br>

      <table class="w3-table w3-bordered w3-border w3-margin-top compact">
        <tr>
          <th colspan="3" class="center">Lockout Status</th>
        </tr>
        <tr>
          <td width="33%">86G Lockout</td>
          <td width="33%">{{ Green0_Red1(data['M_86GLo_Tr']) }}</td>
          <td width="33%"></td>
        </tr>
        <tr>
          <td width="33%">86U Lockout</td>
          <td width="33%">{{ Green1_Red0(data['M_86MLo_Tr']) }}</td>
          <td width="33%"></td>
        </tr>
        <tr>
          <td width="33%">Start Inhibit Status</td>
          <td width="33%">{{ Green1_Red0(data['M_Start_Inhibit']) }}</td>
          <td width="33%"></td>
        </tr>
      </table><br>

      <table class="w3-table w3-bordered w3-border w3-margin-top compact">
        <tr>
          <th colspan="3" class="center">RTAC Status</th>
        </tr>
        <tr>
          <td width="33%">Utility Connect Permission</td>
          <td width="33%">{{ Green1_Red0(data['RTAC_Perm_Stat']) }}</td>
          <td width="33%"></td>
        </tr>
        <tr>
          <td>Utility Allow Connect</td>
          <td>{{ Green1_Red0(data['RTAC_Allow']) }}</td>
          <td></td>
        </tr>
        <tr>
          <td>Utility Trip Disconnect Command</td>
          <td>{{ Green1_Red0(data['RTAC_Trip']) }}</td>
          <td></td>
        </tr>
        <tr>
          <td>Utility Block Connect</td>
          <td>{{ Green1_Red0(data['RTAC_Block']) }}</td>
          <td></td>
        </tr>
        <tr><th colspan="3" class="center">Communication Devices Status</th></tr>
        <tr>
          <td>SEL_Com_Status</td>
          <td>{{ Green0_Red1(data['SEL_Com_Status']) }}</td>
          <td></td>
        </tr>
        <tr>
          <td>EZ_Com_Status</td>
          <td>{{ Green1_Red0(data['EZ_Com_Status']) }}</td>
          <td></td>
        </tr>
        <tr>
          <td>ACMG_Com_Status</td>
          <td>{{ Green1_Red0(data['ACMG_Com_Status']) }}</td>
          <td></td>
        </tr>
        <tr>
          <td>Primary Modem State</td>
          <td>{{ Green_Red(data['COM4_ModemState'], 7) }}</td>
          <td></td>
        </tr>
        <tr>
          <td>EMCP_Status</td>
          <td>{{ Green1_Red0(data['EMCP_Status']) }}</td>
          <td></td>
        </tr>
      </table><br>

      <div class="w3-margin-top" style="height: 300px; overflow-y: scroll;">
      <table id="alarms" class="w3-table w3-bordered w3-border compact">
        <tr>
          <th colspan="3" class="center">ALARMS</th>
        </tr>
        <tr>
          <td>Start Time (EST)</td>
          <td>End Time (EST)</td>
          <td>Descripton</td>
        </tr>
        {% for alarm in alarms %}
        <tr>
          <td width="25%">{{ alarm['start_time'] }}</td>
          <td width="25%">{{ alarm['end_time'] ? alarm['end_time'] : 'Now' }}</td>
          <td width="50%">{{ alarm['description'] }}</td>
        </tr>
        {% endfor %}
      </table>
      </div>
      <br><br>

    </div>
    <p>&nbsp;</p>
  </div>
</div>
{% endblock %}

{% block csscode %}
.w3-modal { padding: 20px; }
.w3-bordered th { border: 1px solid #ddd; }
.w3-bordered td { border: 1px solid #ddd; }
.w3-table.padding0 td { padding: 0 2px; }
.w3-table.padding0 th { padding: 0 2px; }
.w3-table.compact td { padding: 2px; }
.w3-table.compact th { padding: 2px; }
.w3-table td.center { text-align: center; }
.w3-table th.center { text-align: center; }
.w3-bordered tr td:not(:first-child) { text-align: center; }
.w3-bordered tr th:not(:first-child) { text-align: center; }
#alarms tr td:not(:first-child) { text-align: left; }
#alarms tr th:not(:first-child) { text-align: left; }
{% endblock %}

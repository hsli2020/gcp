{% extends "layouts/public.volt" %}

{% block main %}
<div class="container">
  <div class="w3-modal" style="display: block;">
    <div class="w3-modal-content w3-card-8 w3-padding" style="max-width:900px">
      <h2>Detailed Site Information</h2>

      <table class="w3-table compact">
        <tr>
          <td width="20%">Project Name</td>
          <td width="80%">Whitby</td>
        </tr>
        <tr>
          <td>Project Address</td>
          <td>200 Taunton Rd W</td>
        </tr>
        <tr>
          <td>Project Size</td>
          <td>1000</td>
        </tr>
        <tr>
          <td>Store Number</td>
          <td>1058</td>
        </tr>
        <tr>
          <td>Operation Mode</td>
          <td>Closed Transition</td>
        </tr>
      </table>

      <table class="w3-table w3-margin-top compact">
        <tr class="w3-light-gray">
          <th width="33%">Description</th>
          <th width="33%">Utility Main</th>
          <th width="33%">Generator</th>
        </tr>
        <tr>
          <td>Total Power, kW</td>
          <td>20684</td>
          <td>20684</td>
        </tr>
        <tr>
          <td>Average Current, A</td>
          <td>20684</td>
          <td>20684</td>
        </tr>
        <tr>
          <td>Average Voltage, V</td>
          <td>20684</td>
          <td>20684</td>
        </tr>
        <tr>
          <td>Power Factor, pF</td>
          <td>20684</td>
          <td>20684</td>
        </tr>
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
        <tr>
          <td>Urea Level</td>
          <td>N/A</td>
          <td>97%</td>
        </tr>
        <tr>
          <td>Hours till Next Maintenance</td>
          <td>N/A</td>
          <td>20546</td>
        </tr>
      </table>

      <table class="w3-table w3-margin-top">
        <tr>
          <th colspan="2">Site Status</th>
        </tr>
        <tr>
          <td width="33%">Meter Number</td>
          <td width="66%"></td>
        </tr>
        <tr>
          <td>Main Breaker Status</td>
          <td></td>
        </tr>
        <tr>
          <td>Generator Breaker Status</td>
          <td></td>
        </tr>
        <tr>
          <td>Operation Mode: Parallel</td>
          <td></td>
        </tr>
        <tr>
          <td>Remote Start Initiated</td>
          <td></td>
        </tr>
        <tr>
          <td>86G Lockout</td>
          <td></td>
        </tr>
        <tr>
          <td>86U Lockout</td>
          <td></td>
        </tr>
        <tr>
          <td>Emergency Mode Initiated</td>
          <td></td>
        </tr>
      </table>

      <table class="w3-table w3-margin-top">
        <tr class="w3-light-gray">
          <th colspan="2">RTAC Status</th>
        </tr>
        <tr>
          <td width="33%">Utility Connect Permission</td>
          <td width="66%"></td>
        </tr>
        <tr>
          <td>Utility Allow Connect</td>
          <td></td>
        </tr>
        <tr>
          <td>Utility Trip Disconnect Command</td>
          <td></td>
        </tr>
        <tr>
          <td>Utility Block Connect</td>
          <td></td>
        </tr>
        <tr>
          <td>EMCP_Status</td>
          <td></td>
        </tr>
        <tr><td colspan="2">Communication Devices Status</td></tr>
        <tr>
          <td>SEL_Com_Status</td>
          <td></td>
        </tr>
        <tr>
          <td>EZ_Com_Status</td>
          <td></td>
        </tr>
        <tr>
          <td>ACMG_Com_Status</td>
          <td></td>
        </tr>
        <tr>
          <td>Primary Modem State</td>
          <td></td>
        </tr>
        <tr>
          <td>EMCP_Status</td>
          <td></td>
        </tr>
      </table>

      <table class="w3-table w3-margin-top">
        <tr class="w3-light-gray">
          <td colspan="3">RTAC ALARMS</td>
        </tr>
        <tr>
          <td>Start Time</td>
          <td>End Time</td>
          <td>Descripton</td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      </table>
      <br>

    </div>
    <p>&nbsp;</p>
  </div>
</div>
{% endblock %}

{% block csscode %}
.w3-modal { padding: 20px; }
.w3-table.compact td { padding: 0; }
.w3-table.compact th { padding: 8px 0; }
{% endblock %}

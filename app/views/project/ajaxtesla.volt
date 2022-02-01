{% extends "layouts/base.volt" %}

{% block main %}
<style>
  table, th, td { border: 1px solid #ccc; }
</style>

<div class="w3-container">
  <h3>TESLA INVERTER</h3>

  <table class="w3-table w3-border">
    <tr>
      <th colspan="2" class="w3-center">LOAD METER DATA</th>
      <th colspan="2" class="w3-center">BATTERY METER DATA</th>
      <th colspan="2" class="w3-center">SITE METER DATA</th>
    </tr>
    <tr>
      <th>TOTAL 3 PHASE REAL POWER</th>
      <td class="w3-text-red">{{ data['LOAD_METER_PWR'] }}</td>

      <th>TOTAL 3 PHASE REAL POWER</th>
      <td class="w3-text-red">{{ data['BATT_METER_PWR'] }}</td>

      <th>TOTAL 3 PHASE REAL POWER</th>
      <td class="w3-text-red">{{ data['SITE_METER_PWR'] }}</td>
    </tr>
  </table><br>

  <h3>SEL700G RELAY</h3>

  <table class="w3-table w3-border">
    <tr>
        <th>UTILITY IAX</th>
        <td class="w3-text-red">{{ data['IAX_CURRENT'] }}</td>
        <th>BATTERY IAY</th>
        <td class="w3-text-red">{{ data['IAY_CURRENT'] }}</td>
    </tr>
    <tr>
        <th>UTILITY IBX</th>
        <td class="w3-text-red">{{ data['IBX_CURRENT'] }}</td>
        <th>BATTERY IBY</th>
        <td class="w3-text-red">{{ data['IBY_CURRENT'] }}</td>
    </tr>
    <tr>
        <th>UTILITY ICX</th>
        <td class="w3-text-red">{{ data['ICX_CURRENT'] }}</td>
        <th>BATTERY ICY</th>
        <td class="w3-text-red">{{ data['ICY_CURRENT'] }}</td>
    </tr>
    <tr>
        <th>UTILITY P3X</th>
        <td class="w3-text-red">{{ data['P3X_REAL_PWR'] }}</td>
        <th>BATTERY P3Y</th>
        <td class="w3-text-red">{{ data['P3Y_REAL_PWR'] }}</td>
    </tr>
    <tr>
        <th>UTILITY X FREQUENCY</th>
        <td class="w3-text-red">{{ data['X_SIDE_FREQUENCY'] }}</td>
        <th>BATTERY Y FREQUENCY</th>
        <td class="w3-text-red">{{ data['Y_SIDE_FREQUENCY'] }}</td>
    </tr>
  </table>
</div>
{% endblock %}

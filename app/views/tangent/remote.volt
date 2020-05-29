{% extends "layouts/base.volt" %}

{% block main %}
<style type="text/css">
  table, th, td { border: 1px solid #ccc; }
  .w3-table td { vertical-align: middle; }
  .w3-tiny { padding: 4px 16px; }
  .w3-hoverable tbody tr:hover {background-color:#eee}
</style>

<div class="w3-container">
<table class="w3-table w3-white w3-bordered w3-border w3-hoverable">
{# if iplist is not empty #}
  <tr class="w3-light-gray">
    <th class="w3-center"><input class="w3--check" type="checkbox" id="check-all"></th>
    <th>Name</th>
    <th>State</th>
    <th>Start</th>
    <th>Stop</th>
  </tr>
  {% for row in projects %}
  <tr>
    <td class="w3-center"><input class="w3--check" type="checkbox" name="sites[]" value="{{ row['project_id'] }}"></td>
    <td>{{ row['site_name'] }}</td>
    <td class="w3-center">On/Off</td>
    <td class="w3-center">
      <button
        class="w3-button w3-white w3-border w3-border-red w3-text-red w3-round-large w3-tiny"
        onclick="start({{ row['project_id'] }})">Start</button>
    </td>
    <td class="w3-center">
      <button
        class="w3-button w3-white w3-border w3-border-green w3-text-green w3-round-large w3-tiny"
        onclick="stop({{ row['project_id'] }})">Stop</button>
    </td>
  </tr>
  {% endfor %}
{# endif #}
</table>

<div class="w3-row-padding">
  <div class="w3-container w3-col m2">
    <button class="w3-btn-block w3-red w3-section w3-padding" type="button" id="start-selected">Start Selected</button>
  </div>
  <div class="w3-container w3-col m2">
    <button class="w3-btn-block w3-blue w3-section w3-padding" type="button" id="stop-selected">Stop Selected</button>
  </div>

  <div class="w3-container w3-col m2">&nbsp;</div>
  <div class="w3-container w3-col m2">&nbsp;</div>

  <div class="w3-container w3-col m2">
    <button class="w3-btn-block w3-pink w3-section w3-padding" type="button" id="start-all">Start All</button>
  </div>
  <div class="w3-container w3-col m2">
    <button class="w3-btn-block w3-indigo w3-section w3-padding" type="button" id="stop-all">Stop All</button>
  </div>
</div>

</div>
{% endblock %}

{% block jscode %}
function start(id) {
  console.log("Start " + id);
}

function stop(id) {
  console.log("Stop " + id);
}

function startAll() {
  console.log("Start ALL");
}

function stopAll() {
  console.log("Stop ALL");
}

function startSelected() {
  console.log("Start SELECTED");
}

function stopSelected() {
  console.log("Stop SELECTED");
}
{% endblock %}

{% block domready %}
  $("#check-all").click(function(){
    $('input:checkbox').not(this).prop('checked', this.checked);
  });

  $("#start-all").click(function(){
    startAll();
  });

  $("#stop-all").click(function(){
    stopAll();
  });

  $("#start-selected").click(function(){
    startSelected();
  });

  $("#stop-selected").click(function(){
    stopSelected();
  });
{% endblock %}

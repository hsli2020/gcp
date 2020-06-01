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
{# if projects is not empty #}
  <tr class="w3-light-gray">
    <th class="w3-center"><input class="w3--check" type="checkbox" id="check-all" value="0"></th>
    <th>Name</th>
    <th>WebRelay State</th>
    <th>Start</th>
    <th>Stop</th>
  </tr>
  {% for row in projects %}
  <tr id="{{ row['project_id'] }}">
    <td class="w3-center"><input class="w3--check" type="checkbox" name="sites[]" value="{{ row['project_id'] }}"></td>
    <td>{{ row['site_name'] }}</td>
    <td class="w3-center state">-</td>
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

{% include "partials/authcheck.volt" %}

{% endblock %}

{% block jscode %}
  var authorized = false;
  var action = null;
  var projectList = [ {{ ids }} ];
  var projectId = 0;

  function getState(projectId) {
    var url = '/tangent/getstate/' + projectId;
    $.get(url, function(res) {
      updateState(projectId, res.data);
    });
  }

  function turnOn(projectId) {
    var url = '/tangent/turnon/' + projectId;
    $.get(url, function(res) {
      console.log("Turn On  " + projectId);
      updateState(projectId, res.data);
    });
  }

  function turnOff(projectId) {
    var url = '/tangent/turnoff/' + projectId;
    $.get(url, function(res) {
      console.log("Turn Off " + projectId);
      updateState(projectId, res.data);
    });
  }

  function updateState(id, res) {
    console.log('updateState ' + id, res);
    if (res.relay1state == 1) {
      $('#'+id + ' .state').text("ON");
    } else {
      $('#'+id + ' .state').text("OFF");
    }
    authorized = false;
  }

  function checkAuth() {
    var password = $('#password').val();
    $.post('/tangent/checkauth', { password }, function(res) {
      if (res.status != 'OK') {
        $('#toast').text("Wrong Password").fadeIn(400).delay(3000).fadeOut(400);
      } else {
        authorized = true;
        if (action) {
          action(projectId);
          action = null;
        }
      }
    });
    closeModal();
  }

  // Event Handler

  function start(id) {
    if (!authorized) {
        openModal();
        action = start;
        projectId = id;
        return;
    }
    turnOn(id);
  }

  function stop(id) {
    if (!authorized) {
        openModal();
        action = stop;
        projectId = id;
        return;
    }
    turnOff(id);
  }

  function startAll() {
    if (!authorized) {
        openModal();
        action = startAll;
        return;
    }
    for (i=0; i<projectList.length; i++) {
        var id = projectList[i];
        if (id > 0) {
            turnOn(id);
        }
    }
  }

  function stopAll() {
    if (!authorized) {
        openModal();
        action = stopAll;
        return;
    }
    for (i=0; i<projectList.length; i++) {
        var id = projectList[i];
        if (id > 0) {
            turnOff(id);
        }
    }
  }

  function startSelected() {
    if (!authorized) {
        openModal();
        action = startSelected;
        return;
    }

    $('input[type=checkbox]:checked').each(function() {
        var id = $(this).val();
        if (id > 0) {
            turnOn(id);
        }
    })
  }

  function stopSelected() {
    if (!authorized) {
        openModal();
        action = stopSelected;
        return;
    }

    $('input[type=checkbox]:checked').each(function() {
        var id = $(this).val();
        if (id > 0) {
            turnOff(id);
        }
    })
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

  for (i=0; i<projectList.length; i++) {
    var id = projectList[i];
    getState(id);
  }
{% endblock %}

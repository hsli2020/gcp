{% extends "layouts/base.volt" %}

{% block main %}
<style type="text/css">
#box { font-size:24px; padding-top: 16px; }
#box button { font-size:24px; }
#box span { margin-right: 40px; }
#start { margin-right: 4em; }
</style>

<div class="w3-container">
{#
  <div class="w3-margin-bottom">
    <b>Primary IP</b>: {{ webRelay['primary_ip'] }},
    <b>Backup IP</b>: {{ webRelay['backup_ip'] }}
  </div>
#}
  <table class="w3-table w3-border w3-padding">
    <tr>
      <td><button id="start" class="w3-button w3-white w3-border w3-xlarge" onclick="start()">Start</button></td>
      <td id="box">
        <span>Remote Start Status: </span>
        <span id="state"></span>
      </td>
      <td><button id="stop"  class="w3-button w3-white w3-border w3-xlarge w3-right" onclick="stop()">Stop</button></td>
    </tr>
  </table>
</div>

{% include "partials/authcheck.volt" %}

{% endblock %}

{% block jscode %}
  var timer = 0;
  var working = false;
  var authorized = false;
  var action = null;
  var projectId = {{ projectId }};

  function start() {
    if (!authorized) {
        openModal();
        action = start;
        return;
    }
    if (!working) {
      working = true;
      turnOn();
      timer = setInterval(getState, 1000*30);
      $('#start').removeClass('w3-white').addClass('w3-green');
    }
  }

  function stop() {
    if (!authorized) {
        openModal();
        action = stop;
        return;
    }
    working = false;
    turnOff();
    clearInterval(timer);
    $('#start').removeClass('w3-green').addClass('w3-white');
  }

  function getState() {
    var url = '/tangent/getstate/' + projectId;
    $.get(url, function(res) {
      updateState(res.data);
    });
  }

  function turnOn() {
    if (!authorized) {
        openModal();
        action = turnOn;
        return;
    }
    var url = '/tangent/turnon/' + projectId;
    $.get(url, function(res) {
      updateState(res.data);
    });
  }

  function turnOff() {
    if (!authorized) {
        openModal();
        action = turnOff;
        return;
    }
    var url = '/tangent/turnoff/' + projectId;
    $.get(url, function(res) {
      updateState(res.data);
    });
  }

  function updateState(res) {
    console.log('updateState', res);
    if (res.relay1state == 1) {
      $('#state').text("ON");
    } else if (res.relay1state == 0) {
      $('#state').text("OFF");
    } else {
      $('#state').text("Unreachable");
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
          action();
          action = null;
        }
      }
    });
    closeModal();
  }
{% endblock %}

{% block domready %}
  getState();
{% endblock %}

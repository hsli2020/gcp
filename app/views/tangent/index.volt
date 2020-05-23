{% extends "layouts/base.volt" %}

{% block main %}
<style type="text/css">
#box { font-size:24px; }
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
        <span>Generator Status: </span>
        <button id="btnon"  onclick="turnOn()">ON <i class="fa fa-circle"></i></button>
        <button id="btnoff" onclick="turnOff()">OFF <i class="fa fa-circle-o"></i></button>
      </td>
      <td><button id="stop"  class="w3-button w3-white w3-border w3-xlarge w3-right" onclick="stop()">Stop</button></td>
    </tr>
  </table>
</div>

<div id="toast" class="error" style="display:none;"></div>

<div class="container">
  <div id="password-modal" class="w3-modal">
    <div class="w3-modal-content w3-card-8" style="max-width:450px;margin-top:150px">
      <header class="w3-container w3-teal">
        <span class="w3-button w3-teal w3-display-topright" onclick="closeModal()">&times;</span>
        <h5>Authorization</h5>
      </header>

      <p style="text-align: center;">
        <img src="/assets/app/img/gcs-logo-name-223x38.png">
      </p>

      <form class="w3-container" onsubmit="checkAuth(); return false;">
        <div class="w3-section">

          <div class="w3-row-padding">
            <div class="w3-third w3-padding-8">
              <label><b>Password</b></label>
            </div>
            <div class="w3-twothird">
              <input type="password" id="password"
                class="w3-input w3-border w3-margin-bottom"
                placeholder="Enter Your Password"
                autocomplete="off"
                autofocus>
            </div>
          </div>

          <button class="w3-btn-block w3-teal w3-section w3-padding" type="button" onclick="checkAuth()">Verify Password</button>
        </div>
      </form>
    </div>
  </div>
</div>
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
      $('#btnon').css("color", "red");
    } else {
      $('#btnon').css("color", "");
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

  function openModal() {
    $('#password').val('');
    document.getElementById('password-modal').style.display='block';
  }

  function closeModal() {
    $('#password').val('');
    document.getElementById('password-modal').style.display='none';
  }
{% endblock %}

{% block domready %}
  getState();
{% endblock %}

{% extends "layouts/base.volt" %}

{% block main %}
<style type="text/css">
#box {
  border: 1px solid #ccc; padding: 20px; font-size:24px;
}
#box button {
  font-size:24px;
}
#box span {
  margin-right: 40px;
}
</style>

<div class="w3-container">
  <div class="w3-cell-row w3-padding w3-margin-bottom">
    <div class="w3-cell">
       <button id="start" class="w3-button w3-white w3-border" onclick="start()">Start</button>
       <button id="stop"  class="w3-button w3-white w3-border" onclick="stop()">Stop</button>
    </div>
  </div>

  <div id="box">
    <span>Relay 1 State: </span>
    <button id="btnon"  onclick="turnOn()">ON <i class="fa fa-circle"></i></button>
    <button id="btnoff" onclick="turnOff()">OFF <i class="fa fa-circle-o"></i></button>
  </div>

</div>
{% endblock %}

{% block jscode %}
  var timer = 0;
  var working = false;
  var projectId = {{ projectId }};

  function start() {
    if (!working) {
      working = true;
      turnOn();
      timer = setInterval(getState, 1000*60);
      $('#start').removeClass('w3-white').addClass('w3-green');
    }
  }

  function stop() {
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
    var url = '/tangent/turnon/' + projectId;
    $.get(url, function(res) {
      updateState(res.data);
    });
  }

  function turnOff() {
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
  }
{% endblock %}

{% block domready %}
{% endblock %}

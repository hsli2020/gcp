{% extends "layouts/base.volt" %}

{% block main %}
<style type="text/css">
img {
  width: 90%; margin-left: auto; margin-right: auto; display: block;
}
#pic {
  border: 1px solid #ccc; padding: 20px;
}
.dot {
  width: 25px;
  height: 25px;
  border: 1px solid gray;
  border-radius: 50%;
  background-color: white;
  vertical-align: middle;
  display: inline-block;
}
.green {
  background-color: lightgreen;
}
</style>

<div class="w3-container">
  <div class="w3-cell-row w3-padding w3-margin-bottom">
    <div class="w3-cell">
       <button id="start" class="w3-button w3-white w3-border" onclick="start()">Start</button>
       <button id="stop"  class="w3-button w3-white w3-border" onclick="stop()">Stop</button>
    </div>
  </div>
  <div id="pic"><img src=""></div>
</div>
{% endblock %}

{% block jscode %}
  var timer = 0;
  var working = false;
  var projectId = {{ projectId }};

  function start() {
    $('#start').removeClass('w3-white').addClass('w3-green');
    if (!working) {
      working = true;
      getState();
      timer = setInterval(getState, 5000);
    }
  }

  function stop() {
    $('#start').removeClass('w3-green').addClass('w3-white');
    working = false;
    clearInterval(timer);
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
  }
{% endblock %}

{% block domready %}
{% endblock %}

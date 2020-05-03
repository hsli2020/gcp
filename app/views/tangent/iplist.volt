{% extends "layouts/base.volt" %}

{% block main %}
<style type="text/css">
  table, th, td { border: 1px solid #ccc; }
</style>

<div class="w3-container">
<table class="w3-table w3-white w3-bordered w3-border">
{# if iplist is not empty #}
  <tr class="w3-light-gray">
    <th>#</th>
    <th>Name</th>
    <th>Primary IP</th>
    <th>Backup IP</th>
  </tr>
  {% for row in iplist %}
  <tr>
    <td>{{ row['project_id'] }}</td>
    <td>{{ row['site_name'] }}</td>
    <td>{{ row['primary_ip'] }}</td>
    <td>{{ row['backup_ip'] }}</td>
  </tr>
  {% endfor %}
{# endif #}
</table>
</div>
{% endblock %}

{% block jscode %}
{% endblock %}

{% block domready %}
{% endblock %}

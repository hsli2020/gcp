{% extends "layouts/base.volt" %}

{% block main %}
<div class="container">
  <div style="display: block;margin: 0 auto;width: 500px;">
  </div>
</div>
{% endblock %}

{% block csscode %}
table, th, td { border: 1px solid #ddd; }
.w3-table td { padding: 3px; vertical-align: middle; }
.w3-table th { padding: 3px; vertical-align: middle; }
tr td:not(:first-child) { text-align: center; }
tr th:not(:first-child) { text-align: center; }
{% endblock %}

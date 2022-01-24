{% extends "layouts/base.volt" %}

{% block main %}
<div class="container">
  <div style="margin: 2em auto;width: 800px;">
    <form class="w3-container" method="POST">
      <table class="w3-table w3-margin-top">
        <tr class="w3-light-gray">
          <th>Name</th>
          <td>Address</th>
          <td width="25%">Status</th>
          <th>Action</th>
        </tr>
        <tr>
          <td class="w3-light-gray">Coil</td>
          <td>5</td>
          <td>{{ coil }}</td>
          <td>
            <button class="w3-button w3-indigo w3-padding w3-half" type="submit" name="btn" value="write0">Write 0</button>
            <button class="w3-button w3-blue   w3-padding w3-half" type="submit" name="btn" value="write1">Write 1</button>
          </td>
        </tr>
        <tr>
          <td class="w3-light-gray">Holding Registers</td>
          <td>49001</td>
          <td>{{ register }}</td>
          <td>
            <button class="w3-button w3-green w3-padding w3-block" type="submit" name="btn" value="readreg">Read</button>
          </td>
        </tr>
      </table>
    </form>
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

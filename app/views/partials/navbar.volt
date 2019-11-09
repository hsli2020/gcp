<div class="w3-container w3-top w3-teal w3-medium" style="z-index:4">
<ul class="w3-navbar">
  <li><a href="/" class="">Home</a></li>
  <li><a href="/dashboard" class="">Dashboard</a></li>
{#
  <li class="w3-dropdown-hover">
    <a href="javascript:;" class="">Report <i class="fa fa-caret-down"></i></a>
    <div class="w3-dropdown-content w3-white w3-card-4">
      <a href="/report/daily" class="">Daily Report</a>
      <a href="/report/monthly" class="">Monthly Report</a>
    </div>
  </li>
#}
  <li class="w3-dropdown-hover">
    <a href="javascript:;" class="">Tools <i class="fa fa-caret-down"></i></a>
    <div class="w3-dropdown-content w3-white w3-card-4">
      <a href="/project/export" class="">Data Exporting</a>
{#
      <a href="/project/compare" class="">Analytic Tool</a>
      <hr style="margin:0.5em;">
      <a href="#" class="">User Settings</a>
      <a href="#" class="">Smart Alert Settings</a>
#}
    </div>
  </li>

  <li class="w3-dropdown-hover w3-right">
    <a href="javascript:;" class="">Profile <i class="fa fa-caret-down"></i></a>
    <div class="w3-dropdown-content w3-white w3-card-4" style="right:0">
      <a href="#" class="">Settings</a>
      {% if auth['role'] == 1 -%}
      <a href="/user/add" class="">Add New User</a>
      <a href="/user/change-password" class="">Change Password</a>
      {% endif -%}
      <a href="/user/logout" class="">Log out</a>
    </div>
  </li>
  <li class="w3-right"><a href="#" class="">{{ auth['username'] }}</a></li>

</ul>
</div>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <style>
    table { border-collapse: collapse; }
    table, td, th { border: 1px solid gray; padding: 5px 10px; text-align: center; }
    .center { text-align: center; }
    .left { text-align: left; }
    .right { text-align: right; }
  </style>
</head>
<body>

<?php foreach ($alerts as $alert) { ?>
<p>
  <b><?= $alert['subject']; ?></b><br>
</p>
<table>
<tr>
  <th class="center">Time</th>
  <th class="center">Generator Power (kW)</th>
  <th class="center">Store Load (kW)</th>
</tr>
<tr>
  <td class="center"><?= $alert['data']['time_old'] ?></td>
  <td class="center"><?= $alert['data']['gen_power_old'] ?></td>
  <td class="center"><?= $alert['data']['store_load_old'] ?></td>
</tr>
<tr>
  <td class="center"><?= $alert['data']['time_new'] ?></td>
  <td class="center"><?= $alert['data']['gen_power_new'] ?></td>
  <td class="center"><?= $alert['data']['store_load_new'] ?></td>
</tr>
</table>
<br>
<?php } ?>

</body>
</html>

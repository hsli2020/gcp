<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <style>
    table { border-collapse: collapse; }
    table, td, th { border: 1px solid gray; padding: 5px 10px; text-align: center; }
    .center { text-align: center; }
    .left { text-align: left; }
  </style>
</head>
<body>

<p>Following is the Erthmeter Report of <b><?= $date; ?></b></p>

<table>
<tr>
  <th>Store Number</th>
  <th>Store Name</th>
  <th>Generation</th>
  <th>Amount</th>
</tr>

<?php $index = 1; ?>
<?php foreach ($report as $project) { ?>
<tr>
  <td class="center"><?= $project->storeNumber; ?></td>
  <td class="left"><?= $project->siteName; ?></td>
  <td class="center"><?= $project->totalPower; ?></td>
  <td class="center"><?= $project->totalAmount; ?></td>
</tr>
<?php } ?>
</table>

<p>The Report is also attached in Microsoft Excel format.</p>

</body>
</html>

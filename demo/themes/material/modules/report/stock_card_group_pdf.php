<table>
  <thead>
    <tr>
      <th width="1">Group</th>
      <th>Description</th>
      <th>Part Number</th>
      <th>Alt. P/N</th>
      <th width="1">Initial Qty</th>
      <th width="1">Received Qty</th>
      <th width="1">Issued Qty</th>
      <th width="1">Balance Qty</th>
      <th width="1">Minimum Qty</th>
      <th width="1">Unit</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($entities as $entity):?>
      <?php
      $initial = $entity['initial'];
      $received = $entity['received'];
      $issued = $entity['issued'];
      $balance = ($initial + $received) - $issued;
      ?>
      <tr>
        <td class="no-space"><?=$entity['group'];?></td>
        <td class="no-space"><?=$entity['description'];?></td>
        <td><?=$entity['part_number'];?></td>
        <td><?=$entity['alternate_part_number'];?></td>
        <td align="right"><?=number_format($initial, 2);?></td>
        <td align="right"><?=number_format($received, 2);?></td>
        <td align="right"><?=number_format($issued, 2);?></td>
        <td align="right"><?=number_format($balance, 2);?></td>
        <td align="right"><?=number_format($entity['minimum_quantity'], 2);?></td>
        <td><?=$entity['unit'];?></td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

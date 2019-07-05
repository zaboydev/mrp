<table>
  <thead>
    <tr>
      <th width="1" data-sortable="false">Group</th>
      <th data-sortable="false">Description</th>
      <th data-sortable="false">Part Number</th>
      <th>Alt. P/N</th>
      <th>Serial Number</th>
      <th width="1" data-sortable="false">Received</th>
      <th width="1" data-sortable="false">Issued</th>
      <th width="1" data-sortable="false">Balance</th>
      <th width="1" data-sortable="false">Unit</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($entities as $key => $entity):?>
      <?php
      $received[$key] = $entity['received'];
      $issued[$key] = $entity['issued'];
      $balance[$key] = $received[$key] - $issued[$key];
      ?>

      <tr>
        <td><?=$entity['group'];?></td>
        <td><?=$entity['description'];?></td>
        <td><?=$entity['part_number'];?></td>
        <td><?=$entity['alternate_part_number'];?></td>
        <td><?=$entity['serial_number'];?></td>
        <td align="right"><?=number_format($entity['received'], 2);?></td>
        <td align="right"><?=number_format($entity['issued'], 2);?></td>
        <td align="right"><?=number_format($entity['received'] - $entity['issued'], 2);?></td>
        <td><?=$entity['unit'];?></td>
      </tr>
    <?php endforeach;?>
  </tbody>
  <tfoot>
    <tr>
      <th colspan="5">
        Total
      </th>
      <th class="text-right" align="right">
        <?=number_format(array_sum($received), 2);?>
      </th>
      <th class="text-right" align="right">
        <?=number_format(array_sum($issued), 2);?>
      </th>
      <th class="text-right" align="right">
        <?=number_format(array_sum($balance), 2);?>
      </th>
    </tr>
  </tfoot>
</table>

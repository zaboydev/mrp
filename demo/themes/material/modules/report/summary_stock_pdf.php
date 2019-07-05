<?php
/**
 * @var $entities array
 * @var $auth_role
 * @var $locations
 */
?>

<table>
  <thead>
    <tr>
      <th>Group</th>
      <th>Description</th>
      <th>Part Number</th>
      <th>Alt. P/N</th>
      <th>Serial Number</th>
      <th>Warehouse</th>
      <th>Stores</th>
      <th>Cond.</th>
      <th width="1">Qty</th>
      <th>Unit</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($entities as $entity):?>
      <tr>
        <td class="no-space"><?=$entity['group'];?></td>
        <td class="no-space"><?=$entity['description'];?></td>
        <td><?=$entity['part_number'];?></td>
        <td><?=$entity['alternate_part_number'];?></td>
        <td><?=$entity['serial_number'];?></td>
        <td><?=$entity['warehouse'];?></td>
        <td class="no-space"><?=$entity['stores'];?></td>
        <td><?=$entity['condition'];?></td>
        <td class="no-space" align="right"><?=number_format($entity['quantity'], 2);?></td>
        <td class="no-space"><?=$entity['unit'];?></td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

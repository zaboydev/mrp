<?php
/**
 * @var $entities array
 * @var $auth_role
 * @var $locations
 */
?>

<table id="table-data" data-leftColumnsFixed="3" data-pageOrientation="landscape" data-order="[[ 1, &quot;asc&quot; ],[ 2, &quot;asc&quot; ]]">
    <thead>
    <tr>
        <th></th>
        <th>Group</th>
        <th>Description</th>
        <th>Part Number</th>
        <th>Alt. P/N</th>
        <th>Serial Number</th>
        <th>Model</th>
        <th>Warehouse</th>
        <th>Stores</th>
        <th>Cond.</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Unit</th>
    </tr>
    </thead>
    <tbody>
      <?php
      $quantity = array();
      $price = array();
      ?>
    <?php foreach ($entities as $key => $entity):?>
      <?php
      $quantity[$key] = $entity['quantity'];
      $price[$key] = 0;
      ?>
        <tr>
            <td></td>
            <td><?=$entity['group'];?></td>
            <td><?=$entity['description'];?></td>
            <td><?=$entity['part_number'];?></td>
            <td><?=$entity['alternate_part_number'];?></td>
            <td><?=$entity['serial_number'];?></td>
            <td><?=implode(';', $entity['aircraft_types']);?></td>
            <td><?=$entity['warehouse'];?></td>
            <td><?=$entity['stores'];?></td>
            <td><?=$entity['condition'];?></td>
            <td align="right"><?=number_format($quantity[$key], 2);?></td>
            <td align="right"><?=number_format($price[$key], 2);?></td>
            <td><?=$entity['unit'];?></td>
        </tr>
    <?php endforeach;?>
    </tbody>
    <tfoot>
    <tr>
        <th></th>
        <th>Total</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th align="right" class="text-right"><?=number_format(array_sum($quantity), 2);?></th>
        <th align="right" class="text-right"><?=number_format(array_sum($price), 2);?></th>
        <th></th>
    </tr>
  </tfoot>
</table>

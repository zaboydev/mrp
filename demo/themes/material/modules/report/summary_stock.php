<table id="table-data" data-leftColumnsFixed="2" data-pageOrientation="landscape" data-order="[[ 1, &quot;asc&quot; ],[ 2, &quot;asc&quot; ]]">
    <thead>
    <tr>
        <th rowspan="2"></th>
        <th rowspan="2">Group</th>
        <th rowspan="2">Warehouse</th>
        <th rowspan="2">Condition</th>
        <th colspan="2">Current Month</th>
        <th colspan="2">Initial</th>
        <th colspan="2">Balance</th>
    </tr>
    <tr>
        <th>Qty</th>
        <th>Price</th>
        <th>Init. Qty</th>
        <th>Init. Price</th>
        <th>Bal. Qty</th>
        <th>Bal. Price</th>
    </tr>
    </thead>
    <tbody>
      <?php
      $quantity = array();
      $initial = array();
      $balance = array();
      $price = array();
      ?>
    <?php foreach ($entities as $key => $entity):?>
      <?php
      $class  = 'clickable';
      $target = site_url($module['route'] .'/stock_general/').'?group='. $entity['group'] .'&warehouse='. $entity['warehouse'] .'&condition='. $entity['condition'] .'&year='. $year .'&month='. $month;

      $price[$key] = 0;
      $quantity[$key] = $entity['quantity'];
      $initial[$key] = $entity['initial'];
      $balance[$key] = $quantity[$key] + $initial[$key];
      ?>
        <tr class="<?=$class;?>" data-target="<?=$target;?>">
            <td></td>
            <td><?=$entity['description'];?></td>
            <td><?=$entity['warehouse'];?></td>
            <td><?=config_item('condition')[$entity['condition']];?></td>
            <td><?=number_format($quantity[$key], 2);?></td>
            <td><?=number_format($price[$key], 2);?></td>
            <td><?=number_format($initial[$key], 2);?></td>
            <td><?=number_format($price[$key], 2);?></td>
            <td><?=number_format($balance[$key], 2);?></td>
            <td><?=number_format($price[$key], 2);?></td>
        </tr>
    <?php endforeach;?>
    </tbody>
    <tfoot>
    <tr>
        <th></th>
        <th>Total</th>
        <th></th>
        <th></th>
        <th><?=number_format(array_sum($quantity), 2);?></th>
        <th><?=number_format(array_sum($price), 2);?></th>
        <th><?=number_format(array_sum($initial), 2);?></th>
        <th><?=number_format(array_sum($price), 2);?></th>
        <th><?=number_format(array_sum($balance), 2);?></th>
        <th><?=number_format(array_sum($price), 2);?></th>
    </tr>
  </tfoot>
</table>

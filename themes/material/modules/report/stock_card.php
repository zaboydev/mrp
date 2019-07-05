<table id="table-data" data-leftColumnsFixed="2" data-pageOrientation="landscape" data-order="[[ 1, &quot;desc&quot; ],[ 2, &quot;desc&quot; ]]">
  <thead>
    <tr>
      <th rowspan="2"></th>
      <th rowspan="2">Group</th>
      <th colspan="2">Initial</th>
      <th colspan="2">Received</th>
      <th colspan="2">Issued</th>
      <th colspan="2">Balance</th>
    </tr>
    <tr>
      <th>Init. Qty</th>
      <th>Init. Price</th>
      <th>Rec. Qty</th>
      <th>Rec. Price</th>
      <th>Iss. Qty</th>
      <th>Iss. Price</th>
      <th>Bal. Qty</th>
      <th>Bal. Price</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $initial  = array();
    $received = array();
    $issued   = array();
    $balance  = array();
    $price  = array();
    ?>
    <?php foreach ($entities as $key => $entity):?>
      <?php
      if (is_granted($acl['stock_general']['index'])){
        $class  = 'clickable';
        $target = site_url($module['route'] .'/stock_card_group/').'?group='. $entity['group'] .'&year='. $year .'&month='. $month;
      } else {
        $class  = '';
        $target = '';
      }

      $price[$key] = 0;
      $initial[$key] = $entity['initial'];
      $received[$key] = $entity['received'];
      $issued[$key] = $entity['issued'];
      $balance[$key] = ($initial[$key] + $received[$key]) - $issued[$key];
      ?>

      <tr class="<?=$class;?>" data-target="<?=$target;?>">
        <td></td>
        <td><?=$entity['description'];?></td>
        <td align="right"><?=number_format($initial[$key], 2);?></td>
        <td align="right"><?=number_format($price[$key], 2);?></td>
        <td align="right"><?=number_format($received[$key], 2);?></td>
        <td align="right"><?=number_format($price[$key], 2);?></td>
        <td align="right"><?=number_format($issued[$key], 2);?></td>
        <td align="right"><?=number_format($price[$key], 2);?></td>
        <td align="right"><?=number_format($balance[$key], 2);?></td>
        <td align="right"><?=number_format($price[$key], 2);?></td>
      </tr>
    <?php endforeach;?>
  </tbody>
  <tfoot>
    <tr>
      <th></th>
      <th>
        Total
      </th>
      <th align="right" class="text-right">
        <?=number_format(array_sum($initial), 2);?>
      </th>
      <th align="right" class="text-right">
        <?=number_format(array_sum($price), 2);?>
      </th>
      <th align="right" class="text-right">
        <?=number_format(array_sum($received), 2);?>
      </th>
      <th align="right" class="text-right">
        <?=number_format(array_sum($price), 2);?>
      </th>
      <th align="right" class="text-right">
        <?=number_format(array_sum($issued), 2);?>
      </th>
      <th align="right" class="text-right">
        <?=number_format(array_sum($price), 2);?>
      </th>
      <th align="right" class="text-right">
        <?=number_format(array_sum($balance), 2);?>
      </th>
      <th align="right" class="text-right">
        <?=number_format(array_sum($price), 2);?>
      </th>
    </tr>
  </tfoot>
</table>

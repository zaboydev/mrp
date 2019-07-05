<div class="table-responsive">
  <table id="table-data" data-leftColumnsFixed="3" data-pageOrientation="landscape" data-order="[[ 5, &quot;asc&quot; ]]">
    <thead>
      <tr>
        <th rowspan="2"></th>
        <th rowspan="2" width="1" data-sortable="false">Group</th>
        <th rowspan="2" data-sortable="false">Description</th>
        <th rowspan="2" data-sortable="false">Part Number</th>
        <th rowspan="2">Alt. P/N</th>
        <th rowspan="2">Serial Number</th>
        <th colspan="2" width="1" data-sortable="false">Initial</th>
        <th colspan="2" width="1" data-sortable="false">Received</th>
        <th colspan="2" width="1" data-sortable="false">Issued</th>
        <th colspan="2" width="1" data-sortable="false">Balance</th>
        <th rowspan="2" width="1" data-sortable="false">Unit</th>
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
      $price    = array();
      $initial  = array();
      $received = array();
      $issued   = array();
      $balance  = array();
      ?>
      <?php foreach ($entities as $key => $entity):?>
        <?php
        $price[$key]    = 0;
        $initial[$key]  = $entity['initial'];
        $received[$key] = $entity['received'];
        $issued[$key]   = $entity['issued'];
        $balance[$key]  = ($initial[$key] + $received[$key]) - $issued[$key];

        if (is_granted($acl['stock_general']['index'])){
          $class  = 'clickable';
          $target = site_url($module['route'] .'/stock_card_show/'. $entity['part_number'] .'/'. $entity['serial_number']).'?year='.$year .'&month='. $month;
        } else {
          $class  = '';
          $target = '';
        }
        ?>

        <tr class="<?=$class;?>" data-target="<?=$target;?>">
          <td></td>
          <td><?=$entity['group'];?></td>
          <td><?=$entity['description'];?></td>
          <td><?=$entity['part_number'];?></td>
          <td><?=$entity['alternate_part_number'];?></td>
          <td><?=$entity['serial_number'];?></td>
          <td align="right"><?=number_format($initial[$key], 2);?></td>
          <td align="right"><?=number_format($price[$key], 2);?></td>
          <td align="right"><?=number_format($received[$key], 2);?></td>
          <td align="right"><?=number_format($price[$key], 2);?></td>
          <td align="right"><?=number_format($issued[$key], 2);?></td>
          <td align="right"><?=number_format($price[$key], 2);?></td>
          <td align="right"><?=number_format($balance[$key], 2);?></td>
          <td align="right"><?=number_format($price[$key], 2);?></td>
          <td><?=$entity['unit'];?></td>
        </tr>
      <?php endforeach;?>
    </tbody>
    <tfoot>
      <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th>
          Total
        </th>
        <th class="text-right" align="right">
          <?=number_format(array_sum($initial), 2);?>
        </th>
        <th class="text-right" align="right">
          <?=number_format(array_sum($price), 2);?>
        </th>
        <th class="text-right" align="right">
          <?=number_format(array_sum($received), 2);?>
        </th>
        <th class="text-right" align="right">
          <?=number_format(array_sum($price), 2);?>
        </th>
        <th class="text-right" align="right">
          <?=number_format(array_sum($issued), 2);?>
        </th>
        <th class="text-right" align="right">
          <?=number_format(array_sum($price), 2);?>
        </th>
        <th class="text-right" align="right">
          <?=number_format(array_sum($balance), 2);?>
        </th>
        <th class="text-right" align="right">
          <?=number_format(array_sum($price), 2);?>
        </th>
        <th></th>
      </tr>
    </tfoot>
  </table>
</div>

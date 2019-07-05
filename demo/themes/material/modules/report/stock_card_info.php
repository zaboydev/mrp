<table id="table-data" data-leftColumnsFixed="2" data-pageOrientation="landscape" data-order="[[ 1, &quot;asc&quot; ]]">
  <thead>
    <tr>
      <th rowspan="2"></th>
      <th width="1" rowspan="2">Date</th>
      <th rowspan="2" data-sortable="false">Reference Doc.</th>
      <th rowspan="2" data-sortable="false">Warehouse</th>
      <th colspan="2">Receipt</th>
      <th colspan="2">Issuance</th>
      <th class="text-right" align="right" width="1" rowspan="2" data-sortable="false">Qty</th>
      <th class="text-right" align="right" width="1" rowspan="2" data-sortable="false">Price</th>
      <th rowspan="2" width="1" data-sortable="false">Condition</th>
      <th rowspan="2" data-sortable="false">Stores</th>
      <th rowspan="2" data-sortable="false">notes</th>
    </tr>
    <tr>
      <th data-sortable="false">Received From</th>
      <th data-sortable="false">Received By</th>
      <th data-sortable="false">Issued To</th>
      <th data-sortable="false">Issued By</th>
    </tr>
  </thead>
  <tbody>
    <?php $quantity = array();?>
    <?php $price = array();?>
    <?php foreach ($entities as $key => $entity):?>
      <?php
      $quantity[$key] = $entity['quantity'];
      $price[$key] = 0;
      ?>
      <tr>
        <td></td>
        <td><?=$entity['document_date'];?></td>
        <td><?=$entity['document_type'];?>#<?=$entity['document_number'];?></td>
        <td><?=$entity['warehouse'];?></td>
        <td><?=$entity['vendor'];?></td>
        <td><?=$entity['received_by'];?></td>
        <td><?=$entity['issued_to'];?></td>
        <td><?=$entity['issued_by'];?></td>
        <td class="text-right" align="right"><?=number_format($quantity[$key], 2);?></td>
        <td class="text-right" align="right"><?=number_format($price[$key], 2);?></td>
        <td><?=config_item('condition')[$entity['condition']];?></td>
        <td><?=$entity['stores'];?></td>
        <td><?=$entity['notes'];?></td>
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
      <th></th>
      <th></th>
      <th>
        Sub Total
      </th>
      <th class="text-right" align="right" id="subtotal">
        <?=number_format(array_sum($quantity), 2);?>
      </th>
      <th class="text-right" align="right" id="subtotal">
        <?=number_format(array_sum($price), 2);?>
      </th>
      <th></th>
      <th></th>
      <th></th>
    </tr>
    <tr>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th>
        Initial Quantity
      </th>
      <th class="text-right" align="right" id="initial">
        <?=number_format($initial_quantity, 2);?>
      </th>
      <th class="text-right" align="right" id="subtotal">
        <?=number_format(array_sum($price), 2);?>
      </th>
      <th></th>
      <th></th>
      <th></th>
    </tr>
    <tr>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th>
        Total (Balance)
      </th>
      <th class="text-right" align="right" id="grandtotal">
        <?=number_format($initial_quantity + array_sum($quantity), 2);?>
      </th>
      <th class="text-right" align="right" id="subtotal">
        <?=number_format(array_sum($price), 2);?>
      </th>
      <th></th>
      <th></th>
      <th></th>
    </tr>
  </tfoot>
</table>

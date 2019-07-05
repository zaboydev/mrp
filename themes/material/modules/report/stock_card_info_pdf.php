<table>
  <thead>
    <tr>
      <th rowspan="2">Date</th>
      <th rowspan="2">Reference Doc.</th>
      <th colspan="2">Receipt</th>
      <th colspan="2">Issuance</th>
      <th rowspan="2">Qty</th>
      <th rowspan="2">Cond</th>
      <th rowspan="2">notes</th>
    </tr>
    <tr>
      <th>From</th>
      <th>Received By</th>
      <th>To</th>
      <th>Issued By</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($entities as $entity):?>
      <?php $quantity[] = $entity['quantity'];?>
      <tr>
        <td><?=$entity['document_date'];?></td>
        <td><?=$entity['document_type'];?>#<?=$entity['document_number'];?></td>
        <td><?=$entity['vendor'];?></td>
        <td class="no-space"><?=$entity['received_by'];?></td>
        <td><?=$entity['issued_to'];?></td>
        <td class="no-space"><?=$entity['issued_by'];?></td>
        <td align="right"><?=number_format($entity['quantity'], 2);?></td>
        <td><?=$entity['condition'];?></td>
        <td class="no-space"><?=$entity['notes'];?></td>
      </tr>
    <?php endforeach;?>
  </tbody>
  <tfoot>
    <tr>
      <th colspan="6">Total</th>
      <th id="subtotal" align="right"><?=number_format(array_sum($quantity), 2);?></th>
      <th></th>
    </tr>
  </tfoot>
</table>

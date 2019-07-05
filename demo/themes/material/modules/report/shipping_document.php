<table id="table-data" data-leftColumnsFixed="2" data-pageOrientation="landscape" data-order="[[ 1, &quot;desc&quot; ],[ 2, &quot;desc&quot; ]]">
  <thead>
    <tr>
      <th></th>
      <th>CI#</th>
      <th>Date</th>
      <th>From</th>
      <th>Destination</th>
      <th>Description</th>
      <th>P/N</th>
      <th>S/N</th>
      <th>Cond.</th>
      <th>Qty</th>
      <th>Value</th>
      <th>Total</th>
      <th>notes</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($entities as $entity):?>
      <tr class="no-space">
        <td></td>
        <td><?=$entity['document_number'];?></td>
        <td><?=$entity['document_date'];?></td>
        <td><?=$entity['origin_warehouse'];?></td>
        <td><?=$entity['destination_warehouse'];?></td>
        <td><?=$entity['description'];?></td>
        <td><?=$entity['part_number'];?></td>
        <td><?=$entity['serial_number'];?></td>
        <td><?=$entity['condition'];?></td>
        <td><?=$entity['quantity'];?></td>
        <td><?=$entity['unit_value'];?></td>
        <td><?=number_format($entity['unit_value'] * $entity['quantity'], 2);?></td>
        <td><?=$entity['notes'];?></td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

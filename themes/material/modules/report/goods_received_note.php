<table id="table-data" data-leftColumnsFixed="2" data-pageOrientation="landscape" data-order="[[ 1, &quot;desc&quot; ],[ 2, &quot;desc&quot; ]]">
  <thead>
    <tr>
      <th></th>
      <th>GRN#</th>
      <th>Date</th>
      <th>Consignor</th>
      <th>Description</th>
      <th>P/N</th>
      <th>S/N</th>
      <th>Qty</th>
      <th>Cond.</th>
      <th>Order No.</th>
      <th>DN/Invoice</th>
      <th>Location</th>
      <th>AWB</th>
      <th>notes</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($entities as $entity):?>
      <tr>
        <td></td>
        <td><?=$entity['document_number'];?></td>
        <td><?=$entity['received_date'];?></td>
        <td><?=$entity['vendor'];?></td>
        <td><?=$entity['description'];?></td>
        <td><?=$entity['part_number'];?></td>
        <td><?=$entity['serial_number'];?></td>
        <td><?=$entity['quantity'];?></td>
        <td><?=$entity['condition'];?></td>
        <td><?=$entity['order_number'];?></td>
        <td><?=$entity['reference_number'];?></td>
        <td><?=$entity['stores'];?></td>
        <td><?=$entity['awb_number'];?></td>
        <td><?=$entity['notes'];?></td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

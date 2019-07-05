<table id="table-data" data-leftColumnsFixed="2" data-pageOrientation="landscape" data-checkbox="true" data-order="[[ 1, &quot;desc&quot; ],[ 2, &quot;desc&quot; ]]">
  <thead>
    <tr>
      <th></th>
      <th>Date</th>
      <th>MS#</th>
      <th>Description</th>
      <th>Aircraft</th>
      <th>P/N</th>
      <th>S/N</th>
      <th>Qty</th>
      <th>Base</th>
      <th>Ref.</th>
      <th>notes</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($entities as $entity):?>
      <tr>
        <td></td>
        <td><?=$entity['document_date'];?></td>
        <td><?=$entity['document_number'];?></td>
        <td><?=$entity['description_required'];?></td>
        <td>
          <?=($entity['aircraft'] == '') ? 'ALL A/C' : $entity['aircraft'];?>
        </td>
        <td><?=$entity['part_number'];?></td>
        <td><?=$entity['serial_number'];?></td>
        <td><?=$entity['quantity'];?></td>
        <td><?=$entity['warehouse'];?></td>
        <td><?=$entity['requestition_reference'];?></td>
        <td><?=$entity['notes'];?></td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

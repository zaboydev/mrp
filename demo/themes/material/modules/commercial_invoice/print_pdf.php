<?php
$origin = get_warehouse_info($entity['warehouse']);
$destination = get_vendor_info($entity['issued_to']);
?>

<div class="clear"></div>

<table class="table table-striped">
  <thead>
    <tr>
      <th valign="bottom">No</th>
      <th valign="bottom">Description</th>
      <th valign="bottom">Part Number</th>
      <th valign="bottom">S/N</th>
      <th valign="bottom">Condition</th>
      <th valign="bottom" colspan="2">Quantity</th>
      <th valign="bottom" colspan="2">Unit Value</th>
      <th valign="bottom">Total Value</th>
      <th valign="bottom">AWB Number</th>
      <th valign="bottom">Remarks</th>
    </tr>
  </thead>
  <tbody>
    <?php $n = 0;?>
    <?php foreach ($entity['items'] as $i => $detail):?>
      <?php $n++;?>
      <tr>
        <td class="no-space" valign="top" align="right">
          <?=$n;?>.
        </td>
        <td valign="top">
          <?=print_string($detail['description']);?>
        </td>
        <td valign="top">
          <?=print_string($detail['part_number']);?>
        </td>
        <td valign="top">
          <?=print_string($detail['serial_number']);?>
        </td>
        <td valign="top">
          <?=print_string($detail['condition']);?>
        </td>
        <td valign="top">
          <?=number_format($detail['issued_quantity'], 2);?>
        </td>
        <td valign="top">
          <?=print_string($detail['unit']);?>
        </td>
        <td valign="top">
          <?=print_string($detail['insurance_currency']);?>
        </td>
        <td valign="top">
          <?=number_format($detail['insurance_unit_value'], 2);?>
        </td>
        <td valign="top">
          <?=number_format(($detail['insurance_unit_value'] * $detail['issued_quantity']), 2);?>
        </td>
        <td valign="top">
          <?=print_string($detail['awb_number']);?>
        </td>
        <td valign="top">
          <?=$detail['remarks'];?>
        </td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

<div class="clear"></div>

<p>
  <?=nl2br($entity['notes']);?>
</p>

<p>
  This spare part is being shipped to:
</p>

<p>
  <strong><?=$entity['issued_to'];?></strong>
  <br />
  <?=nl2br($entity['issued_address']);?>
</p>

<p>
  This spare part to <?=$entity['issued_to'];?> is not for resale and remains the property of PT. Bali Widya Dirgantara.
</p>

<p>
  Sincerely,
  <br />
  <br />
  <br /><?=$entity['issued_by'];?>
  <br />Procurement Manager
  <br />PT. Bali Widya Dirgantara
  <br />Mobile. +62 081333312392
</p>

<?php
$origin = get_warehouse_info($entity['warehouse']);
$destination = get_warehouse_info($entity['issued_to']);
?>

<table class="table-no-strip condensed">
  <tr>
    <td width="35%" valign="top">
      From:
      <br />
      <strong>Bali International Flight Academy</strong>
      <br />
      <?=nl2br($origin['address']);?>
    </td>

    <td width="35%" valign="top">
      Ship To:
      <br />
      <strong>Bali International Flight Academy</strong>
      <br />
      <?=$destination['address'];?>
    </td>

    <td width="30%" valign="top" style="padding-left: 60px;">
      Document No.: <?=$entity['document_number'];?>
      <br />Date : <?=nice_date($entity['issued_date'], 'F d, Y');?>
      <?=(!empty($entity['received_by'])) ? '<br /><br /><em>Received on '. nice_date($entity['received_date'], 'F d, Y') .' by '. $entity['received_by'] .'</em>' : '';?>
    </td>
  </tr>
</table>

<div class="clear"></div>

<table class="table table-striped">
  <thead>
    <tr>
      <th valign="bottom">No</th>
      <th valign="bottom">Description</th>
      <th valign="bottom">Part Number</th>
      <th valign="bottom">Serial Number</th>
      <th valign="bottom">Condition</th>
      <th valign="bottom" colspan="2" width="1">Quantity</th>
      <th valign="bottom" colspan="2" width="1">Unit Value</th>
      <th valign="bottom" colspan="2" width="1">Total Value</th>
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
        <td valign="top" width="1">
          <?=number_format($detail['issued_quantity'], 2);?>
        </td>
        <td valign="top" width="1">
          <?=print_string($detail['unit']);?>
        </td>
        <td valign="top" width="1">
          <?=print_string($detail['insurance_currency'], 'IDR');?>
        </td>
        <td valign="top" width="1">
          <?=number_format($detail['insurance_unit_value'], 2);?>
        </td>
        <td valign="top" width="1">
          <?=print_string($detail['insurance_currency'], 'IDR');?>
        </td>
        <td valign="top" width="1">
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

<table class="condensed" style="margin-top: 20px;">
  <tr>
    <td width="20%" valign="top">
      Packed by,
      <br />
      <br />
      <br />
      <br />
      <?=$entity['sent_by'];?>
    </td>

    <td width="20%" valign="top">
      Released by,
      <br />
      <br />
      <br />
      <br />
      <?=$entity['issued_by'];?>
    </td>

    <td width="25%" valign="top">
      Date Received:
      <?=(empty($entity['received_date'])) ? '' : print_date($entity['received_date']);?>
      <br />

      Received by,
      <br />
      <br />
      <br />
      <?=$entity['received_by'];?>
    </td>

    <td width="35%" valign="top">
      <ol>
        <li>
          Return to sender:
        </li>
        <li>
          Copy for receiver:
        </li>
        <li>
          Copy for I.S.C:
        </li>
        <li>
          Copy for Accounting:
        </li>
        <li>
          File for sender:
        </li>
      </ol>
    </td>
  </tr>
</table>

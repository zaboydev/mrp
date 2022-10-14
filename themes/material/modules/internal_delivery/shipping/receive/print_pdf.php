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
      <th valign="bottom">Group</th>
      <th valign="bottom">Description</th>
      <th valign="bottom">P/N</th>
      <th valign="bottom">S/N</th>
      <th valign="bottom">Condition</th>
      <th valign="bottom" colspan="2" width="1">Quantity</th>
      <th valign="bottom" colspan="2" width="1">Unit Value</th>
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
          <?=print_string($detail['group']);?>
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
  Notes or Special Instruction : <?=nl2br($entity['notes']);?>
</p>

<table class="condensed" style="margin-top: 20px;">
  <tr>
    <td width="25%" valign="top" align="center">
      <p>
        Released by:
        <br />
        <br /><?=$entity['issued_by'];?>
      </p>
    </td>
    <td width="25%" valign="top" align="center">
      <p>
        Packed by:
        <br />
        <br /><?=$entity['sent_by'];?>
      </p>
    </td>
    <td width="25%" valign="top" align="center">
      <p>
        Known by:
        <br />
        <br /><?=$entity['known_by'];?>
      </p>
    </td>
    <td width="25%" valign="top" align="center">
      <p>
        Approved by:
        <br />
        <br /><?=$entity['approved_by'];?>
      </p>
    </td>
  </tr>
</table>

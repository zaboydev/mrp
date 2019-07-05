<table class="table table-striped">
  <thead>
    <tr>
      <th colspan="3">Date: <?=nice_date($entity['issued_date'], 'F d, Y');?></th>
      <th colspan="4">Issued to: <?=$entity['issued_to'];?></th>
      <th colspan="3" align="right">Doc. No.: <?=$entity['document_number'];?></th>
    </tr>
    <tr>
      <th valign="bottom">No</th>
      <th valign="bottom">Group</th>
      <th valign="bottom">Description</th>
      <th valign="bottom">P/N</th>
      <th valign="bottom">S/N</th>
      <th valign="bottom">Qty</th>
      <th valign="bottom">Unit</th>
      <th valign="bottom">Cond.</th>
      <th valign="bottom">Stores</th>
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
          <?=number_format($detail['issued_quantity'], 2);?>
        </td>
        <td valign="top">
          <?=print_string($detail['unit']);?>
        </td>
        <td valign="top">
          <?=print_string($detail['condition']);?>
        </td>
        <td valign="top">
          <?=print_string($detail['stores']);?>
        </td>
        <td valign="top">
          <?=$detail['remarks'];?>
        </td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

<div class="clear"></div>

<?=(empty($entity['notes'])) ? '' : '<p>Requisition Ref.: '.$entity['requisition_reference'].'</p>';?>
<?=(empty($entity['notes'])) ? '' : '<p>Note: '.nl2br($entity['notes']).'</p>';?>

<div class="clear"></div>

<table class="condensed">
  <tr>
    <td width="35%" valign="top" align="center">
      Requested by,
      <br>
      <br>
      <br><?=print_string($entity['required_by'], '');?>
    </td>
    <td width="30%" valign="top" align="center">
      Approved by,
      <br>
      <br>
      <br><?=print_string($entity['approved_by'], '');?>
    </td>
    <td width="35%" valign="top" align="center">
      Issued by,
      <br>
      <br>
      <br><?=print_string($entity['issued_by'], '');?>
    </td>
  </tr>
</table>

<table class="condensed" style="margin-top: 20px;">
  <tr>
    <td width="50%" valign="top">
      <p>
        Note/IPC Ref. :
        <br /><?=$entity['notes'];?>
      </p>
    </td>
    <td valign="top">
      This document is valid when completely signed.
      <br />Form distribution:
      <ol>
        <li>Store</li>
        <li>Maintenance</li>
        <li>Accounting</li>
      </ol>
    </td>
  </tr>
</table>

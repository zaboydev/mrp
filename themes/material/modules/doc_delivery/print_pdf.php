<table class="table table-striped">
  <thead>
    <tr>
      <th colspan="3">Date of received: <?=nice_date($entity['received_date'], 'F d, Y');?></th>
      <th colspan="6">Consignor: <?=$entity['received_from'];?></th>
      <th colspan="3" align="right">Doc. No.: <?=$entity['document_number'];?></th>
    </tr>
    <tr>
      <th valign="bottom">No</th>
      <th valign="bottom">Group</th>
      <th valign="bottom">Description</th>
      <th valign="bottom">P/N</th>
      <th valign="bottom">Alt. P/N</th>
      <th valign="bottom">S/N</th>
      <th valign="bottom">Order No.</th>
      <th valign="bottom">Qty</th>
      <th valign="bottom">Cond.</th>
      <th valign="bottom">DN/INV</th>
      <th valign="bottom">Location</th>
      <th valign="bottom">Notes</th>
    </tr>
  </thead>
  <tbody>
    <?php $n = 0;?>
    <?php foreach ($entity['items'] as $i => $detail):?>
      <?php $n++;?>
      <tr>
        <td class="no-space" valign="top">
          <?=$n;?>
        </td>
        <td valign="top">
          <?=$detail['group'];?>
        </td>
        <td valign="top">
          <?=$detail['description'];?>
        </td>
        <td valign="top">
          <?=$detail['part_number'];?>
        </td>
        <td valign="top">
          <?=$detail['alternate_part_number'];?>
        </td>
        <td valign="top">
          <?=$detail['serial_number'];?>
        </td>
        <td valign="top">
          <?=$detail['order_number'];?>
        </td>
        <td valign="top">
          <?=number_format($detail['received_quantity'], 2);?>
        </td>
        <td valign="top">
          <?=$detail['condition'];?>
        </td>
        <td valign="top">
          <?=$detail['reference_number'];?>
        </td>
        <td valign="top">
          <?=$detail['stores'];?>
        </td>
        <td valign="top">
          <?=$detail['notes'];?>
        </td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

<div class="clear"></div>

<?=(empty($entity['notes'])) ? '' : '<p>Note: '.nl2br($entity['notes']).'</p>';?>

<div class="clear"></div>

<table class="condensed" style="margin-top: 20px;">
  <tr>
    <td width="50%" valign="top">
      <p>
        The above stores have been received damage or shortage report
        <br>No. :
        <br>Applies to this consignment.
        <br>Signature :
      </p>
    </td>
    <td valign="top">
      <p>
        The above stores are in accordance with the terms of order as regard and are fit to use.
        <br>Signature :
      </p>
    </td>
  </tr>
</table>

<table class="condensed" style="margin-top: 20px;">
  <tr>
    <td width="50%" valign="top">
      <p>
        The stock record has been posted.
        <br>Signature :
      </p>
    </td>
    <td valign="top">
      <ol>
        <li>File</li>
        <li>Accounting</li>
      </ol>
    </td>
  </tr>
</table>

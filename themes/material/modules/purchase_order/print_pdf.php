<div class="clear">
  <div class="pull-left">
    To :
    <br />
    <strong><?=$entity['vendor'];?></strong>
    <br />
    <?=nl2br($entity['vendor_address']);?>
    <br />
    <?=$entity['vendor_country'];?>
    <br />
    <?=$entity['vendor_attention'];?>
  </div>

  <div class="well pull-right">
	<p>
	  Deliver To :
	  <br />
	  <strong><?=$entity['deliver_company'];?></strong>
	  <br />
	  <?=nl2br($entity['deliver_address']);?>
	  <br />
	  <?=$entity['deliver_country'];?>
	  <br />
	  <?=$entity['deliver_attention'];?>
	</p>
  </div>
</div>

<p>
  Dear Sir/Madame,
  <br /><br />This is to confirm of the order of the followings:
</p>

<table class="table" style="margin-top: 20px;">
  <thead>
    <tr>
      <th class="middle-alignment"></th>
      <th class="middle-alignment">Description</th>
      <th class="middle-alignment">Part Number</th>
      <th class="middle-alignment">Alt. P/N</th>
      <th class="middle-alignment">Serial Number</th>
      <th class="middle-alignment" colspan="2">Quantity</th>
      <th class="middle-alignment">Unit Price <?=$entity['default_currency'];?></th>
      <th class="middle-alignment">Core Charge <?=$entity['default_currency'];?></th>
      <th class="middle-alignment">Total Amount <?=$entity['default_currency'];?></th>
      <th class="middle-alignment">POE Number</th>
      <th class="middle-alignment">Remarks</th>
    </tr>
  </thead>
  <tbody>
    <?php $n = 0;?>
    <?php $total_amount = array();?>
    <?php foreach ($entity['items'] as $i => $detail):?>
      <?php $total_amount[] = $detail['total_amount'];?>
      <?php $n++;?>
      <tr id="row_<?=$i;?>">
        <td width="1">
          <?=$n;?>
        </td>
        <td>
          <?=print_string($detail['description']);?>
        </td>
        <td class="no-space">
          <?=print_string($detail['part_number']);?>
        </td>
        <td class="no-space">
          <?=print_string($detail['alternate_part_number']);?>
        </td>
        <td class="no-space">
          <?=print_string($detail['serial_number']);?>
        </td>
        <td>
          <?=print_number($detail['quantity'], 2);?>
        </td>
        <td>
          <?=print_string($detail['unit']);?>
        </td>
        <td>
          <?=print_number($detail['unit_price'], 2);?>
        </td>
        <td>
          <?=print_number($detail['core_charge'], 2);?>
        </td>
        <td>
          <?=print_number($detail['total_amount'], 2);?>
        </td>
        <td>
          <?=print_string(find_poe_number($detail['purchase_order_evaluation_items_vendors_id']));?>
        </td>
        <td>
          <?=print_string($detail['remarks']);?>
        </td>
      </tr>
    <?php endforeach;?>
  </tbody>
  <tfoot>
    <tr>
      <th></th>
      <th>Grand Total <?=$entity['default_currency'];?></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th><?=print_number(array_sum($total_amount), 2);?></th>
      <th></th>
      <th></th>
    </tr>
  </tfoot>
</table>

<div class="clear"></div>

<?=(empty($entity['notes'])) ? '' : '<p>Note: '.nl2br($entity['notes']).'</p>';?>

<div class="clear"></div>

<table class="condensed" style="margin-top: 20px;">
  <tr>
    <td width="35%" valign="top" align="center">
      <p>
        Issued by,
        <br />Procurement
        <br />
        <?php if($entity['issued_by']!=''):?>
        <img src="<?=base_url('ttd_user/'.get_ttd($entity['issued_by']));?>" width="100">
        <?php endif;?>
        <br />
        <br /><?=$entity['issued_by'];?>
      </p>
    </td>
    <td width="30%" valign="top" align="center">
      <p>
        Checked by,
        <br />Budget Control
        <br />
        <br />
        <br /><?=$entity['checked_by'];?>
      </p>
    </td>
    <td width="35%" valign="top" align="center">
      <p>
        Approved by,
        <br />CFO
        <br />
        <br />
        <br /><?=$entity['approved_by'];?>
      </p>
    </td>
  </tr>
</table>

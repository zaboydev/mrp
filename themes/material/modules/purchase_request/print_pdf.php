<table class="table-no-strip condensed">
  <tr>
    <td>Request By</td>
    <th widtd="40%">: <?=print_person_name($entity['created_by']);?></th>
    <td>PR No.</td>
    <th>: <?=print_string($entity['pr_number']);?></th>
  </tr>
  <tr>
    <td>Inventory</td>
    <th>: <?=print_string($entity['item_category']);?></th>
    <td>PR Date.</td>
    <th>: <?=print_date($entity['pr_date']);?></th>
  </tr>
  <tr>
    <td>Suggested Supplier</td>
    <th>: <?=print_string($entity['suggested_supplier']);?></th>
    <td>Required Date</td>
    <th>: <?=print_date($entity['required_date']);?></th>
  </tr>
  <tr>
    <td>Deliver to</td>
    <th>: <?=print_string($entity['deliver_to']);?></th>
    <td>Status</td>
    <th>: <?=($entity['status'] == 'approved') ? 'BUDGETED' : strtoupper($entity['status']);?></th>
  </tr>
  <tr>
    <td></td>
    <th></th>
    <td>Approval Status</td>
    <?php if($entity['approved_date']!=null):?>
    <th>: APPROVED by <?=print_person_name($entity['approved_by']);?></th>
    <?php elseif($entity['rejected_date']!=null):?>
    <th>: REJECTED by <?=print_person_name($entity['rejected_by']);?></th>
    <?php elseif($entity['canceled_date']!=null):?>
    <th>: CANCELED by <?=print_person_name($entity['canceled_by']);?></th>
    <?php endif;?>
  </tr>
</table>

<div class="clear"></div>

<table class="table" style="margin-top: 20px;">
  <thead>
    <tr>
      <th align="right" width="1">No</th>
      <th width="10">Description</th>
      <th width="10">Part Number</th>
      <th align="right" width="1">Qty</th>
      <th width="1">Unit</th>
      <th align="right" width="10">On Hand Stock</th>
      <th align="right" width="10">Min. Qty</th>
      <th align="right" width="10">Balance Budget Year to Date (Qty)</th>
      <th align="right" width="10">Budget Status</th>
    </tr>
  </thead>
  <tbody>
    <?php $n = 0;?>
    <?php $total_qty = array();?>
    <?php foreach ($entity['items'] as $i => $detail):?>
      <?php $n++;?>
      <?php $total_qty[] = $detail['quantity'];?>
      <tr>
        <td align="right">
          <?=print_number($n);?>
        </td>
        <td>
          <?=print_string($detail['product_name']);?>
        </td>
        <td>
          <?=print_string($detail['part_number']);?>
        </td>
        <td align="right">
          <?=print_number($detail['quantity'], 2);?>
        </td>
        <td>
          <?=print_string($detail['unit']);?>
        </td>
        <td align="right">
          <?=print_number($detail['on_hand_qty'], 2);?>
        </td>
        <td align="right">
          <?=print_number($detail['minimum_quantity'], 2);?>
        </td>
        <td align="right">
          <?=print_number($detail['ytd_quantity'] - $detail['ytd_used_quantity'], 2);?>
        </td>
        <td align="right">
          <?=print_string(strtoupper($detail['budget_status']));?>
        </td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

<div class="clear"></div>

<?php if($entity['approved_date']!=null):?>
  <p>Approaval Notes: <?=nl2br($entity['approved_notes']);?></p>
  <?php elseif($entity['rejected_date']!=null):?>
  <p>Approved Notes: <?=nl2br($entity['rejected_notes']);?></p>
  <?php elseif($entity['canceled_date']!=null):?>
  <p>Canceled Notes: <?=nl2br($entity['canceled_notes']);?></p>
<?php endif;?>

<?=(empty($entity['notes'])) ? '' : '<p>Note: '.nl2br($entity['notes']).'</p>';?>

<div class="clear"></div>

<table class="condensed" style="margin-top: 20px;">
  <tr>
    <td width="25%" valign="top" align="center">
      <p>
        Request by:
        <br />Inventory
        <br />&nbsp;<br>
        <?php if($entity['created_by']!=''):?>
        <img src="<?=base_url('ttd_user/'.get_ttd($entity['created_by']));?>" width="100">
        <?php endif;?>
        <br />
        <br /><?=$entity['created_by'];?>
      </p>
    </td>
    <?php if($entity['item_category']!='BAHAN BAKAR'):?>
    <td width="50%" valign="top" align="center">
    &nbsp;
    </td>
    <?php endif;?>
    <td width="25%" valign="top" align="center">
      <p>
        Approved by:
        <br />Chief Of Maintenance
        <?php if($entity['approved_by']!=''):?>
        <br /><?=print_date($entity['approved_date'])?><br>
        <img src="<?=base_url('ttd_user/'.get_ttd($entity['created_by']));?>" width="100">
        <?php endif;?>
        <br />
        <br /><?=$entity['approved_by'];?>
      </p>
    </td>
    <?php if($entity['item_category']=='BAHAN BAKAR'):?>
    <td width="25%" valign="top" align="center">
      <p>Acknowledged by:
        <br />Operation Support
        <?php if($entity['operation_review_by']!=''):?>
        <br /><?=print_date($entity['approved_date'])?><br>
        <img src="<?=base_url('ttd_user/'.get_ttd($entity['operation_review_by']));?>" width="100">
        <?php endif;?>
        <br />
        <br /><?=$entity['operation_review_by'];?>
      </p>
    </td>
    <?php endif;?>
  </tr>
</table>

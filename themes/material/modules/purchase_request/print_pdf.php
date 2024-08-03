<style>
  @media print {
    .new-page {
      page-break-before: always;
    }
  }
</style>
<table class="table-no-strip condensed">
  <tr>
    <td>Request By</td>
    <th widtd="40%">: <?= print_person_name($entity['created_by']); ?></th>
    <td>PR No.</td>
    <th>: <?= print_string($entity['pr_number']); ?></th>
  </tr>
  <tr>
    <td>Inventory</td>
    <th>: <?= print_string($entity['item_category']); ?></th>
    <td>PR Date.</td>
    <th>: <?= print_date($entity['pr_date']); ?></th>
  </tr>
  <tr>
    <td>Suggested Supplier</td>
    <th>: <?= print_string($entity['suggested_supplier']); ?></th>
    <td>Required Date</td>
    <th>: <?= print_date($entity['required_date']); ?></th>
  </tr>
  <tr>
    <td>Deliver to</td>
    <th>: <?= print_string($entity['deliver_to']); ?></th>
    <td>Status</td>
    <th>: <?= ($entity['status'] == 'approved') ? 'BUDGETED' : strtoupper($entity['status']); ?></th>
  </tr>
  <tr>
    <td></td>
    <th></th>
    <td>Approval Status</td>
    <?php if ($entity['approved_date'] != null) : ?>
      <th>: APPROVED by <?= print_person_name($entity['approved_by']); ?></th>
    <?php elseif ($entity['rejected_date'] != null) : ?>
      <th>: REJECTED by <?= print_person_name($entity['rejected_by']); ?></th>
    <?php elseif ($entity['canceled_date'] != null) : ?>
      <th>: CANCELED by <?= print_person_name($entity['canceled_by']); ?></th>
    <?php endif; ?>
  </tr>
</table>

<div class="clear"></div>

<table class="table" style="margin-top: 20px;">
  <thead>
    <tr>
      <th align="right" width="1">No</th>
      <th width="10">Description</th>
      <th width="10">Part Number</th>
      <th width="10">Serial Number</th>
      <th align="right" width="1">Qty</th>
      <th width="1">Unit</th>
      <th align="right" width="10">On Hand Stock</th>
      <th align="right" width="10">Min. Qty</th>
      <th align="right" width="10">Balance Budget Year to Date (Qty)</th>
      <th align="right" width="10">Budget Status</th>
    </tr>
  </thead>
  <tbody>
    <?php $n = 0;
    $unbudgeted = 0; ?>
    <?php $total_qty = array(); ?>
    <?php foreach ($entity['items'] as $i => $detail) : ?>
      <?php $n++; ?>
      <?php $total_qty[] = $detail['quantity']; ?>
      <tr>
        <td align="right">
          <?= print_number($n); ?>
        </td>
        <td>
          <?= print_string($detail['product_name']); ?>
        </td>
        <td>
          <?= print_string($detail['part_number']); ?>
        </td>
        <td>
          <?= print_string($detail['serial_number']); ?>
        </td>
        <td align="right">
          <?= print_number($detail['quantity'], 2); ?>
        </td>
        <td>
          <?= print_string($detail['unit']); ?>
        </td>
        <td align="right">
          <?= print_number($detail['on_hand_qty'], 2); ?>
        </td>
        <td align="right">
          <?= print_number($detail['min_qty'], 2); ?>
        </td>
        <td align="right">
          <?= print_number($detail['ytd_quantity'] - $detail['ytd_used_quantity'], 2); ?>
        </td>
        <td align="right">
          <?= print_string(strtoupper($detail['budget_status'])); ?><?php if ($detail['budget_status'] == 'unbudgeted') {
                                                                        $unbudgeted++;
                                                                      } ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div class="clear"></div>

<?php if ($entity['approved_date'] != null) : ?>
  <p>Approaval Notes: <?= nl2br($entity['approved_notes']); ?></p>
<?php elseif ($entity['rejected_date'] != null) : ?>
  <p>Rejected Notes: <?= nl2br($entity['rejected_notes']); ?></p>
<?php elseif ($entity['canceled_date'] != null) : ?>
  <p>Canceled Notes: <?= nl2br($entity['canceled_notes']); ?></p>
<?php endif; ?>

<?= (empty($entity['notes'])) ? '' : '<p>Note: ' . nl2br($entity['notes']) . '</p>'; ?>

<div class="clear"></div>

<table class="condensed" style="margin-top: 20px;">
  <tr>
    <td width="25%" valign="top" align="center">
      <p>
        Request by:
        <br />Inventory
        <br />&nbsp;<br>
        <?php if ($entity['created_by'] != '') : ?>
          <img src="<?= base_url('ttd_user/' . get_ttd($entity['created_by'])); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?= $entity['created_by']; ?>
      </p>
    </td>

    <?php if ($unbudgeted != 0) : ?>
      <td width="25%" valign="top" align="center">
        <p>
          Checked by:
          <br />Finance Manager
          <?php if ($entity['finance_approve_at'] != '') : ?>
            <br /><?= print_date($entity['finance_approve_at']) ?><br>
            <img src="<?= base_url('ttd_user/' . get_ttd($entity['finance_approve_by'])); ?>" width="auto" height="50">
          <?php endif; ?>
          <br />
          <br /><?= $entity['finance_approve_by']; ?>
        </p>
      </td>
    <?php endif; ?>

    <?php if ($entity['item_category'] != 'BAHAN BAKAR') : ?>
      <td width="25%" valign="top" align="center">
        &nbsp;
      </td>
    <?php endif; ?>

    <td width="25%" valign="top" align="center">
      <p>
        Approved by:
        <br />Chief Of Maintenance
        <?php if ($entity['approved_by'] != '') : ?>
          <br /><?= print_date($entity['approved_date']) ?><br>
          <img src="<?= base_url('ttd_user/' . get_ttd($entity['approved_by'])); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?= $entity['approved_by']; ?>
      </p>
    </td>

    <?php if ($entity['item_category'] == 'BAHAN BAKAR') : ?>
      <td width="25%" valign="top" align="center">
        <p>Acknowledged by:
          <br />Operation Support
          <?php if ($entity['operation_review_by'] != '') : ?>
            <br /><?= print_date($entity['approved_date']) ?><br>
            <img src="<?= base_url('ttd_user/' . get_ttd($entity['operation_review_by'])); ?>" width="auto" height="50">
          <?php endif; ?>
          <br />
          <br /><?= $entity['operation_review_by']; ?>
        </p>
      </td>
    <?php endif; ?>
  </tr>
</table>
<p class="new-page">On Hand Stock</p>
<table class="table" style="margin-top: 20px;">
  <thead>
    <tr>
      <th align="right" width="1">No</th>
      <th>Description</th>
      <th>Part Number</th>
      <th>Min. Qty</th>
      <th>Base</th>
      <th>On Hand Stock</th>
      <th>Unit</th>
    </tr>
  </thead>
  <tbody>
    <?php $n = 0;
    $unbudgeted = 0; ?>
    <?php $total_qty = array(); ?>
    <?php foreach ($entity['items'] as $i => $detail) : ?>
      <?php $n++; ?>
      <?php $total_qty[] = $detail['quantity']; ?>
      <tr>
        <td align="right" rowspan="<?= $detail['info_on_hand_qty']['items_count'] + 1 ?>">
          <?= print_number($n); ?>
        </td>
        <td rowspan="<?= $detail['info_on_hand_qty']['items_count'] + 1 ?>">
          <?= print_string($detail['product_name']); ?>
        </td>
        <td rowspan="<?= $detail['info_on_hand_qty']['items_count'] + 1 ?>">
          <?= print_string($detail['part_number']); ?>
        </td>
        <td rowspan="<?= $detail['info_on_hand_qty']['items_count'] + 1 ?>">
          <?= print_string($detail['minimum_quantity']); ?>
        </td>
        <?php if ($detail['info_on_hand_qty']['items_count'] == 0) : ?>
          <td colspan="3">
            No Data Available
          </td>
        <?php endif; ?>
      </tr>
      <?php if ($detail['info_on_hand_qty']['items_count'] > 0) : ?>
        <?php foreach ($detail['info_on_hand_qty']['items'] as $i => $info) : ?>
          <tr>
            <td>
              <?= print_string($info['warehouse']); ?>
            </td>
            <td>
              <?= print_number($info['on_hand_stock'], 2); ?>
            </td>
            <td>
              <?= print_string($info['unit']); ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>

      <tr>
        <td colspan="7">&nbsp;</td>
      </tr>
    <?php endforeach; ?>

  </tbody>
</table>
<h5 class="new-page">History Purchase</h5>
<table class="table table-striped table-nowrap">
  <thead id="table_header">
    <tr>
      <th>No</th>
      <th>Tanggal</th>
      <th>Purchase Number</th>
      <th>Qty</th>
      <th>Unit</th>
      <th>Price</th>
      <th>Total</th>
      <th>POE Qty</th>
      <th>POE Value</th>
      <th>PO Qty</th>
      <th>PO Value</th>
      <th>GRN Qty</th>
      <th>GRN Value</th>
    </tr>
  </thead>
  <tbody id="table_contents">
    <?php $n = 0;?>
              
    <?php foreach ($entity['items'] as $i => $detail):?>
    <?php 
      $n++;
    ?>
    <tr>
      <td align="right">
        <?=print_number($n);?>
      </td>
      <td colspan="12">
        <?=print_string($detail['product_name']);?>
      </td>
    </tr>
    <?php 
      $total_qty        = array();
      $total            = array();
      $total_qty_poe    = array();
      $total_value_poe  = array();
      $total_qty_po     = array();
      $total_value_po   = array();
      $total_qty_grn    = array();
      $total_value_grn  = array();
    ?>
    <?php foreach ($detail['history'] as $i => $history):?>
    <tr>
      <?php 
        $total_qty[]        = $history['quantity'];
        $total[]            = $history['total'];
        $total_qty_poe[]    = $history['poe_qty'];
        $total_value_poe[]  = $history['poe_value'];
        $total_qty_po[]     = $history['po_qty'];
        $total_value_po[]   = $history['po_value'];
        $total_qty_grn[]    = $history['grn_qty'];
        $total_value_grn[]  = $history['grn_value'];
      ?>
      <td></td>
      <td>
        <?=print_date($history['pr_date']);?>
      </td>
      <td>
        <?=print_string($history['pr_number']);?>
      </td>
      <td align="right">
        <?=print_number($history['quantity'], 2);?>
      </td>
      <td>
        <?=print_string($detail['unit']);?>
      </td>
      <td align="right">
        <?=print_number($history['price'], 2);?>
      </td>
      <td align="right">
        <?=print_number($history['total'], 2);?>
      </td>
      <td align="right">
        <?=print_number($history['poe_qty'], 2);?>
      </td>
      <td align="right">
        <?=print_number($history['poe_value'], 2);?>
      </td>
      <td align="right">
        <?=print_number($history['po_qty'], 2);?>
      </td>
      <td align="right">
        <?=print_number($history['po_value'], 2);?>
      </td>     
      <td align="right">
        <?=print_number($history['grn_qty'], 2);?>
      </td>
      <td align="right">
        <?=print_number($history['grn_value'], 2);?>
      </td>               
    </tr>                
    <?php endforeach;?>
    <?php endforeach;?>
  </tbody>
  <tfoot>
    <tr>
      <th>Total</th>
      <th></th>
      <th></th>
      <th style="text-align: right;"><?=print_number(array_sum($total_qty), 2);?></th>
      <th></th>
      <th></th>
      <th style="text-align: right;"><?=print_number(array_sum($total), 2);?></th>
      <th style="text-align: right;"><?=print_number(array_sum($total_qty_po), 2);?></th>
      <th style="text-align: right;"><?=print_number(array_sum($total_value_poe), 2);?></th>
      <th style="text-align: right;"><?=print_number(array_sum($total_qty_po), 2);?></th>
      <th style="text-align: right;"><?=print_number(array_sum($total_value_po), 2);?></th>
      <th style="text-align: right;"><?=print_number(array_sum($total_qty_grn), 2);?></th>
      <th style="text-align: right;"><?=print_number(array_sum($total_value_grn), 2);?></th>
    </tr>
  </tfoot>
</table>
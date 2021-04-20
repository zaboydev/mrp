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
    <td>Cost Center</td>
    <th>: <?= print_string($entity['cost_center_name']); ?></th>
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
      <th align="right" width="1">Qty</th>
      <th width="1">Unit</th>
      <th align="right" width="10">Price</th>
      <th align="right" width="10">Total</th>
      <th align="right" width="10">Balance Budget Year to Date (Qty)</th>
    </tr>
  </thead>
  <tbody>
    <?php 
      $n = 0;
      $unbudgeted = 0; 
      $total_qty = array();
      $total = array();
    ?>
    <?php foreach ($entity['items'] as $i => $detail) : ?>
      <?php 
        $n++; 
        $total_qty[] = $detail['quantity']; 
        $total[] = $detail['total']; 
      ?>
      <tr>
        <td align="right">
          <?= print_number($n); ?>
        </td>
        <td>
          <?= print_string($detail['product_name']); ?>
        </td>
        <td>
          <?= print_string($detail['product_code']); ?>
        </td>
        <td align="right">
          <?= print_number($detail['quantity'], 2); ?>
        </td>
        <td>
          <?= print_string($detail['unit']); ?>
        </td>
        <td align="right">
          <?= print_number($detail['price'], 2); ?>
        </td>
        <td align="right">
          <?= print_number($detail['total'], 2); ?>
        </td>
        <td align="right">
          <?= print_number($detail['ytd_quantity'] - $detail['ytd_used_quantity'], 2); ?>
        </td>        
      </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot>
    <tr>
      <th>Total</th>
      <th></th>
      <th></th>
      <th><?=print_number(array_sum($total_qty), 2);?></th>
      <th></th>
      <th></th>
      <th><?=print_number(array_sum($total), 2);?></th>
      <th></th>
      <!-- <th></th> -->
    </tr>
  </tfoot>
</table>

<div class="clear"></div>

<?= (empty($entity['notes'])) ? '' : '<p>Note: ' . nl2br($entity['notes']) . '</p>'; ?>

<?php if ($entity['approved_date'] != null) : ?>
  <?= (empty($entity['approved_notes'])) ? '' : '<p>Note: ' . nl2br($entity['approved_notes']) . '</p>'; ?>
<?php elseif ($entity['rejected_date'] != null) : ?>
  <?= (empty($entity['rejected_notes'])) ? '' : '<p>Note: ' . nl2br($entity['rejected_notes']) . '</p>'; ?>
<?php elseif ($entity['canceled_date'] != null) : ?>
  <?= (empty($entity['canceled_notes'])) ? '' : '<p>Note: ' . nl2br($entity['canceled_notes']) . '</p>'; ?>
<?php endif; ?>

<div class="clear"></div>

<table class="condensed" style="margin-top: 20px;">
  <tr>
    <td width="25%" valign="top" align="center">
      <p>
        Request by:
        <br /><?= print_string($entity['cost_center_name']); ?>
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

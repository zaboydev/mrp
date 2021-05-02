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
    <?php if($entity['status']=='approved') : ?>
      <th>: APPROVED by <?=print_person_name($entity['head_approved_by']);?></th>
    <?php elseif($entity['status']=='rejected') : ?>
      <th>: REJECTED by <?=print_person_name($entity['rejected_by']);?></th>
    <?php elseif ($entity['canceled_date'] != null) : ?>
      <th>: CANCELED by <?= print_person_name($entity['canceled_by']); ?></th>
    <?php elseif($entity['status']=='WAITING FOR HEAD DEPT') : ?>
      <th>: BUDGETCONTROL APPROVED by <?=print_person_name($entity['approved_by']); ?></th>
    <?php elseif($entity['status']=='WAITING FOR BUDGETCONTROL') : ?>
      <th>: WAITING FOR BUDGETCONTROL</th>
    <?php endif; ?>
  </tr>
</table>

<div class="clear"></div>

<table class="table" style="margin-top: 20px;" width="100%">
  <thead>
    <tr>
      <th style="text-align: center;" width="2%">#</th>
      <th style="text-align: center;" width="17%">Description</th>
      <th style="text-align: center;" width="5%">P/N</th>
      <th style="text-align: center;" width="5%">Qty</th>
      <th style="text-align: center;" width="5%">Unit</th>
      <th style="text-align: center;" width="10%">Price</th>
      <th style="text-align: center;" width="12%">Total</th>
      <th style="text-align: center;" width="10%">Balance Quantity Month to Date</th>
      <th style="text-align: center;" width="12">Balance Budget Month to Date</th>
      <th style="text-align: center;" width="10%">Balance Quantity Year to Date</th>
      <th style="text-align: center;" width="12%">Balance Budget Year to Date</th>
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
        <td style="text-align: center;">
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
          <?=print_number($detail['balance_mtd_quantity'], 2);?>
        </td>
        <td align="right">
          <?=print_number($detail['balance_mtd_budget'], 2);?>
        </td>
        <td align="right">
          <?=print_number($detail['balance_ytd_quantity'], 2);?>
        </td>
        <td align="right">
          <?=print_number($detail['balance_ytd_budget'], 2);?>
        </td>        
      </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot>
    <tr>
      <th colspan="3">Total</th>
      <th style="text-align: right;"><?=print_number(array_sum($total_qty), 2);?></th>
      <th></th>
      <th></th>
      <th style="text-align: right;"><?=print_number(array_sum($total), 2);?></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
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
    <td width="30%" valign="top" align="center">
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

    <td width="5%" valign="top" align="center">
      &nbsp;
    </td>
    <td width="30%" valign="top" align="center">
      <p>
        Checked by:
        <br />Budgetcontrol
        <?php if ($entity['approved_by'] != '') : ?>
          <br /><?= print_date($entity['approved_date']) ?><br>
          <img src="<?= base_url('ttd_user/' . get_ttd($entity['approved_by'])); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?= $entity['approved_by']; ?>
      </p>
    </td>

    <td width="5%" valign="top" align="center">
      &nbsp;
    </td>

    <td width="30%" valign="top" align="center">
      <p>
        Approved by:
        <br /><?= print_string($entity['cost_center_name']); ?> Head Dept.
        <?php if ($entity['head_approved_by'] != '') : ?>
          <br /><?= print_date($entity['head_approved_date']) ?><br>
          <img src="<?= base_url('ttd_user/' . get_ttd($entity['head_approved_by'])); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?= $entity['head_approved_by']; ?>
      </p>
    </td>
  </tr>
</table>

<h5 class="new-page">History Purchase</h5>
<table class="table table-striped table-nowrap">
  <thead id="table_header">
    <tr>
      <th align="right" width="1">No</th>
      <th>Tanggal</th>
      <th>Purchase Number</th>
      <th align="right">Qty</th>
      <th>Unit</th>
      <th align="right">Price</th>
      <th align="right">Total</th>
      <th align="right">Created By</th>
      <!-- <th align="right" width="10">Budget Status</th> -->
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
      <td colspan="7">
        <?=print_string($detail['product_name']);?>
      </td>
    </tr><?php $total_qty = array();$total = array();?>
    <?php foreach ($detail['history'] as $i => $history):?>
    <tr>
      <?php 
        $total_qty[] = $history['quantity'];
        $total[] = $history['total'];
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
        <?=print_string($history['created_by'], 2);?>
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
      <th></th>
      <!-- <th></th> -->
    </tr>
  </tfoot>
</table>

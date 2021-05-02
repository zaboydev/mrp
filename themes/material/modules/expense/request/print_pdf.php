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
    <th>: BUDGETED</th>
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
      <th style="text-align: center;" width="5%">#</th>
      <th style="text-align: center;" width="15%">Account Name</th>
      <th style="text-align: center;" width="15%">Account Code</th>
      <th style="text-align: center;" width="15%">Amount</th>
      <th style="text-align: center;" width="15%">Total</th>
      <th style="text-align: center;" width="15%">Balance Budget Month to Date</th>
      <th style="text-align: center;" width="15%">Balance Budget Year to Date</th>
    </tr>
  </thead>
  <tbody>
    <?php 
      $n = 0;
      $total = array();
    ?>
    <?php foreach ($entity['items'] as $i => $detail) : ?>
      <?php 
        $n++; 
        $total[] = $detail['total']; 
      ?>
      <tr>
        <td style="text-align: center;">
          <?= print_number($n); ?>
        </td>
        <td>
          <?= print_string($detail['account_name']); ?>
        </td>
        <td style="text-align: center;">
          <?= print_string($detail['account_code']); ?>
        </td>
        <td align="right">
          <?= print_number($detail['amount'], 2); ?>
        </td>
        <td align="right">
          <?= print_number($detail['total'], 2); ?>
        </td>
        <td align="right">
          <?=print_number($detail['balance_mtd_budget'], 2);?>
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
      <th></th>
      <th style="text-align: right;"><?=print_number(array_sum($total), 2);?></th>
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

<table class="condensed" style="margin-top: 20px;" width="100%">
  <tr>
    <td width="<?php (array_sum($total)<15000000)? '20%':'16%' ?>" valign="top" align="center">
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
    <td width="<?php (array_sum($total)<15000000)? '20%':'16%' ?>" valign="top" align="center">
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
    <td width="<?php (array_sum($total)<15000000)? '20%':'16%' ?>" valign="top" align="center">
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
    <td width="<?php (array_sum($total)<15000000)? '20%':'16%' ?>" valign="top" align="center">
      <p>
        Approved by:
        <br />Finance
        <?php if ($entity['finance_approved_by'] != '') : ?>
          <br /><?= print_date($entity['finance_approved_date']) ?><br>
          <img src="<?= base_url('ttd_user/' . get_ttd($entity['finance_approved_by'])); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?= $entity['finance_approved_by']; ?>
      </p>
    </td>

    <td width="<?php (array_sum($total)<15000000)? '20%':'16%' ?>" valign="top" align="center">
      <p>
        Approved by:
        <br />Head of School
        <?php if ($entity['hos_approved_by'] != '') : ?>
          <br /><?= print_date($entity['hos_approved_date']) ?><br>
          <img src="<?= base_url('ttd_user/' . get_ttd($entity['hos_approved_by'])); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?= $entity['hos_approved_by']; ?>
      </p>
    </td>
    <?php  if (array_sum($total)>15000000) :  ?>
    <td width="<?php (array_sum($total)<15000000)? '20%':'16%' ?>" valign="top" align="center">
      <p>
        Approved by:
        <br />Chief Operation Officer
        <?php if ($entity['ceo_approved_by'] != '') : ?>
          <br /><?= print_date($entity['ceo_approved_date']) ?><br>
          <img src="<?= base_url('ttd_user/' . get_ttd($entity['ceo_approved_by'])); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?= $entity['ceo_approved_by']; ?>
      </p>
    </td>
    <?php endif; ?>
  </tr>
</table>

<h5 class="new-page">History Purchase</h5>
<table class="table table-striped table-nowrap">
  <thead id="table_header">
    <tr>
      <th style="text-align: center;">No</th>
      <th style="text-align: center;">Tanggal</th>
      <th style="text-align: center;">Purchase Number</th>
      <th style="text-align: center;">Amount</th>
      <th style="text-align: center;">Total</th>
      <th style="text-align: center;">Created By</th>
    </tr>
  </thead>
  <tbody id="table_contents">
    <?php $n = 0;?>
              
    <?php foreach ($entity['items'] as $i => $detail):?>
    <?php 
      $n++;
    ?>
    <tr>
      <td style="text-align: center;">
        <?=print_number($n);?>
      </td>
      <td colspan="5">
        (<?=print_string($detail['account_code']);?>) <?=print_string($detail['account_name']);?>
      </td>
    </tr><?php $total = array();?>
    <?php foreach ($detail['history'] as $i => $history):?>
    <tr>
      <?php 
        $total[] = $history['total'];
      ?>
      <td></td>
      <td style="text-align: left;">
        <?=print_date($history['pr_date']);?>
      </td>
      <td style="text-align: left;">
        <?=print_string($history['pr_number']);?>
      </td>
      <td style="text-align: right;">
        <?=print_number($history['amount'], 2);?>
      </td>
      <td style="text-align: right;">
        <?=print_number($history['total'], 2);?>
      </td>
      <td style="text-align: center;">
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
      <th></th>
      <th style="text-align: right;"><?=print_number(array_sum($total), 2);?></th>
      <th></th>
      <!-- <th></th> -->
    </tr>
  </tfoot>
</table>

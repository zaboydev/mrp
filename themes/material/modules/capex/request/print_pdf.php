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
    <th>
      : <?= ($entity['status'] != 'pending') ? 'BUDGETED' : 'BUDGETED'; ?> 
      <?php if($entity['status']!='pending') : ?>
        Budgetcontrol Review by : <?= $entity['approved_by']; ?> at : <?= print_date($entity['approved_date']) ?>
      <?php endif; ?>
    </th>
  </tr>
  <tr>
    <td></td>
    <th></th>
    <td>Approval Status</td>
    <?php if($entity['status']=='approved') : ?>
      <th>: APPROVED by <?=print_person_name($entity['head_approved_by']);?> as Head Department</th>
    <?php elseif($entity['status']=='rejected') : ?>
      <th>: REJECTED by <?=print_person_name($entity['rejected_by']);?></th>
    <?php elseif ($entity['canceled_date'] != null) : ?>
      <th>: CANCELED by <?= print_person_name($entity['canceled_by']); ?></th>
    <?php elseif($entity['status']=='WAITING FOR HEAD DEPT') : ?>
      <th>: BUDGETCONTROL APPROVED by <?=print_person_name($entity['approved_by']); ?></th>
    <?php elseif($entity['status']=='WAITING FOR BUDGETCONTROL'||$entity['status']=='pending') : ?>
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

<table class="condensed" style="margin-top: 20px;" width="100%">
  <tr>
    <td valign="top" align="center">
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

    <?php if($entity['base']=='BANYUWANGI'):?>
    <td valign="top" align="center">
      <p>
        Known by:
        <br />Assistant HOS
        <?php if ($entity['ahos_approved_by'] != '') : ?>
          <br /><?= print_date($entity['ahos_approved_date']) ?><br>
          <img src="<?= base_url('ttd_user/' . get_ttd($entity['ahos_approved_by'])); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?= $entity['ahos_approved_by']; ?>
      </p>
    </td>
    <?php endif;?>

    <td valign="top" align="center">
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

    <?php if($entity['with_po']=='f'):?>

    <td valign="top" align="center">
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

    <?php if(!in_array($entity['cost_center_id'],$this->config->item('head_office_cost_center_id'))&&$created_by['auth_level']!='23'&&$created_by['auth_level']!='25'):?>

    <td valign="top" align="center">
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

    <td valign="top" align="center">
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
    <?php endif; ?>

    <?php if(in_array($entity['cost_center_id'],$this->config->item('head_office_cost_center_id'))||$created_by['auth_level']=='23'||$created_by['auth_level']=='25'):?>
    <td valign="top" align="center">
      <p>
        Approved by:
        <br />VP Finance
        <?php if ($entity['hos_approved_by'] != '') : ?>
          <br /><?= print_date($entity['hos_approved_date']) ?><br>
          <img src="<?= base_url('ttd_user/' . get_ttd($entity['hos_approved_by'])); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?= $entity['hos_approved_by']; ?>
      </p>
    </td>
    <?php  if (array_sum($total)>15000000) :  ?>
    <td valign="top" align="center">
      <p>
        Approved by:
        <br />Chief Of Finance
        <?php if ($entity['ceo_approved_by'] != '') : ?>
          <br /><?= print_date($entity['ceo_approved_date']) ?><br>
          <img src="<?= base_url('ttd_user/' . get_ttd($entity['ceo_approved_by'])); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?= $entity['ceo_approved_by']; ?>
      </p>
    </td>
    <?php endif; ?>
    <?php endif; ?>
    <?php endif; ?>
  </tr>
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

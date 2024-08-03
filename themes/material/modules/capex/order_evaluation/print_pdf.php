<style>
  @media print {
    .new-page {
      page-break-before: always;
    }
  }
</style>
<table class="table-no-strip condensed">
  <tr>
    <td widtd="15%">Created By</td>
    <th widtd="40%">: <?= $entity['created_by']; ?></th>
    <td widtd="15%">Document No.</td>
    <th widtd="30%">: <?= print_string($entity['evaluation_number']); ?></th>
  </tr>
  <tr>
    <td>Inventory</td>
    <th>: <?= print_string($entity['tipe']); ?></th>
    <td>Date</td>
    <th>: <?= print_date($entity['document_date']); ?></th>
  </tr>
  <tr>
    <td></td>
    <th></th>
    <td>Status</td>
    <th>: <?= ($entity['status'] == 'evaluation') ? 'EVALUATION' : strtoupper($entity['status']); ?></th>
  </tr>
</table>

<div class="clear"></div>

<table class="table" style="margin-top: 20px;">
  <thead>
    <tr>
      <th class="middle-alignment" align="center" rowspan="2"></th>
      <th class="middle-alignment" align="center" rowspan="2">Description</th>
      <th class="middle-alignment" align="center" rowspan="2">Part Number</th>
      <th class="middle-alignment" align="center" rowspan="2">Alt. Part Number</th>
      <th class="middle-alignment" align="center" rowspan="2">Ref. IPC</th>
      <th class="middle-alignment" align="right" rowspan="2">Qty</th>
      <th class="middle-alignment" align="center" rowspan="2">Unit</th>
      <th class="middle-alignment" align="center" rowspan="2">Remarks</th>
      <!-- <th class="middle-alignment" align="center">PR Number</th>
      <th class="middle-alignment" align="center" colspan="4">Vendor Detail</th> -->
      <?php foreach ($entity['vendors'] as $key => $vendor) : ?>
        <th class="middle-alignment text-center" colspan="3"><?= $vendor['vendor']; ?></th>
      <?php endforeach; ?>
    </tr>
    <tr>
      <?php for ($v = 0; $v < count($entity['vendors']); $v++) : ?>
        <th class="middle-alignment text-center">Unit Price <?= $entity['vendors'][$v]['vendor_currency'] ?></th>
        <th class="middle-alignment text-center">Core Charge <?= $entity['vendors'][$v]['vendor_currency'] ?></th>
        <th class="middle-alignment text-center">Total <?= $entity['vendors'][$v]['vendor_currency'] ?></th>
      <?php endfor; ?>
    </tr>
  </thead>
  <tbody id="table_contents">
    <?php $n = 0; ?>
    <?php foreach ($entity['request'] as $i => $detail) : ?>
      <?php $n++; ?>
      <tr id="row_<?= $i; ?>">
        <td width="1">
          <?= $n; ?>
        </td>
        <td>
          <?= print_string($detail['description']); ?>
        </td>
        <td class="no-space">
          <?= print_string($detail['part_number']); ?>
        </td>
        <td class="no-space">
          <?= print_string($detail['alternate_part_number']); ?>
        </td>
        <td class="no-space">
          <?= print_string($detail['reference_ipc']); ?>
        </td>
        <td>
          <?= print_number($detail['quantity'], 2); ?>
        </td>
        <td>
          <?= print_string($detail['unit']); ?>
        </td>
        <td>
          <?= print_string($detail['remarks']); ?>
        </td>
        <?php foreach ($entity['vendors'] as $key => $vendor) : ?>
          <?php
              if ($detail['vendors'][$key]['is_selected'] == 't') {
                $style = 'background-color: green; color: white;text-align: right';
                $label = number_format($vendor['unit_price'], 2);
              } else {
                $style = 'text-align: right';
                $label = number_format($vendor['unit_price'], 2);
              }
              ?>
          <td style="<?= $style; ?>">
            <?= number_format($detail['vendors'][$key]['unit_price'], 2); ?>
          </td>
          <td style="<?= $style; ?>">
            <?= number_format($detail['vendors'][$key]['core_charge'], 2); ?>
          </td>
          <td style="<?= $style; ?>">
            <?= number_format($detail['vendors'][$key]['total'], 2); ?>
          </td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>


<div class="clear"></div>

<?= (empty($entity['notes'])) ? '' : '<p>Note: ' . nl2br($entity['notes']) . '</p>'; ?>

<div class="clear"></div>

<table class="condensed" style="margin-top: 20px;">
  <tr>
    <td width="50%" valign="top" align="center">
      <p>
        Prepared by:
        <br>Procurement
        <br><?= print_date($entity['created_at']); ?>
        <br>
        <?php if ($entity['created_by'] != '') : ?>
          <img src="<?= base_url('ttd_user/' . get_ttd($entity['created_by'])); ?>" width="auto" height="50">
        <?php endif; ?>
        <br>
        <br><?= $entity['created_by']; ?>
      </p>
    </td>
    <td width="50%" valign="top" align="center">
      <p>Approved by:
        <br>Procurement Manager
        <br><?= print_date($entity['updated_at']); ?>
        <br>
        <?php if ($entity['checked_by'] != '' & $entity['approved_by'] != 'without_approval') : ?>
          <img src="<?= base_url('ttd_user/' . get_ttd($entity['checked_by'])); ?>" width="auto" height="50">
        <?php endif; ?>
        <?php if ($entity['approved_by'] == 'without_approval') : ?>
          <img src="<?= base_url('ttd_user/mark.png'); ?>" width="100">
        <?php endif; ?>
        <br>
        <br><?php if ($entity['approved_by'] != 'without_approval') : ?>
          <?= $entity['checked_by']; ?>
        <?php endif; ?></p>
    </td>
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
      <!-- <th>Price</th> -->
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
              
    <?php foreach ($entity['request'] as $i => $detail):?>
    <?php 
      $n++;
    ?>
    <tr>
      <td align="right">
        <?=print_number($n);?>
      </td>
      <td>
        <?=print_string($detail['part_number']);?>
      </td>
      <td colspan="11">
        <?=print_string($detail['description']);?>
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
        <?=print_string($history['unit']);?>
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
      <!-- <th></th> -->
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
<style>
  @media print {
    .new-page {
      page-break-before: always;
    }
  }
</style>
<table class="table-no-strip condensed">
    <tr>
        <td>TRANSACTION NO </td>
        <th>: <?= print_string($entity['no_transaksi']); ?></th>
        <td>Payment to</td>
        <th>: <?= $entity['vendor']; ?></th>
        <td>Paid at</td>
        <th>: <?= ($entity['paid_at']!='')? print_date($entity['paid_at']):'-'; ?></th>
    </tr>
    <tr>
        <td>Date.</td>
        <th>: <?= print_date($entity['tanggal']); ?></th>
        <td>Payment Status</td>
        <th>: <?= $entity['status']; ?></th>
        <td>No. Konfirmasi</td>
        <th>: <?= ($entity['status']=='PAID')? $entity['no_konfirmasi']:'n/b'; ?></th>
    </tr>
    <tr>
        <td>Purposed Date</td>
        <th>: <?= print_date($entity['purposed_date']); ?></th>
        <td>Account</td>
        <th>: <?= $entity['coa_kredit']; ?> <?= $entity['group']; ?></th>
        <td>No. Cheque</td>
        <th>: <?= ($entity['status']=='PAID')? $entity['no_cheque']:'n/b'; ?></th>
    </tr>
</table>

<div class="clear"></div>

<table class="table" style="margin-top: 20px;" width="100%">
    <thead>
        <tr>
            <th style="text-align: center;">No</th>
            <th style="text-align: center;">PO#</th>
            <th style="text-align: center;">Due Date</th>
            <th style="text-align: center;">Currency</th>
            <th style="text-align: center;">POE#</th>
            <th style="text-align: center;">Request Number</th>
            <th style="text-align: center;">Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php $n = 0; ?>
        <?php $amount_paid = array(); ?>
        <?php foreach ($entity['po'] as $i => $po) : ?>
        <?php $n++; ?>
        <tr>
            <td style="font-weight: bold;" class="no-space">
                <?= print_number($n); ?>
            </td>
            <td style="font-weight: bold;">
                <?=print_string($po['document_number'])?>
            </td>
            <td style="font-weight: bold;text-align: center;">
                <?= print_date($po['due_date'],'d/m/Y'); ?>
            </td>
            <td style="font-weight: bold;text-align: center;">
                <?= print_string($entity['currency']); ?>
            </td>
            <td style="font-weight: bold;">
                
            </td>
            <td style="font-weight: bold;">
                
            </td>
            <td style="text-align: right;font-weight: bold;">
                <?= print_number($po['amount_paid'], 2); ?>
                <?php $amount_paid[] = $po['amount_paid']; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6">Total</th>
            <th style="text-align: right;"><?= print_number(array_sum($amount_paid), 2); ?></th>
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
                <?php if($entity['type']!='CASH'): ?>
                Prepared by:
                <?php else:?>
                Prepared & Approved
                <?php endif; ?>
                <br />AP STAFF
                <br />&nbsp;<br>
                <?php if ($entity['created_by'] != '') : ?>
                <img src="<?= base_url('ttd_user/' . get_ttd($entity['created_by'])); ?>" width="auto" height="50">
                <?php endif; ?>
                <br />
                <br /><?= $entity['created_by']; ?>
            </p>
        </td>
        <?php if($entity['type']!='CASH'): ?>
        <td valign="top" align="center">
            <p>
                Approved by:
                <br />Finance Manager
                <?php if ($entity['review_by'] != '') : ?>
                    <br /><?= print_date($entity['review_at']) ?><br>
                <img src="<?= base_url('ttd_user/' . get_ttd($entity['review_by'])); ?>" width="auto" height="50">
                <?php endif; ?>
                <br />
                <br /><?= $entity['review_by']; ?>
            </p>
        </td>
        <?php endif;?>
    </tr>
</table>

<?php if($entity['status']=='PAID'):?>
<p class="new-page">Jurnal</p>
<div class="clear"></div>
<table class="table" style="margin-top: 20px;" width="100%">
    <thead>
        <tr>
            <th style="text-align: center;">No</th>
            <th style="text-align: center;">Account</th>
            <th style="text-align: center;">Debet</th>
            <th style="text-align: center;">Kredit</th>
        </tr>
    </thead>
    <tbody>
        <?php $n = 0; ?>
        <?php $amount_paid = array(); ?>
        <?php foreach ($entity['jurnalDetail'] as $i => $jurnal) : ?>
        <?php $n++; ?>
        <tr>
            <td class="no-space">
                <?= print_number($n); ?>
            </td>
            <td>
                <?= print_string($jurnal['kode_rekening'])?> - <?= print_string($jurnal['jenis_transaksi'])?>
            </td> 
            <td>
                <?= print_number($jurnal['trs_debet'], 2); ?>
            </td>
            <td>
                <?= print_number($jurnal['trs_kredit'], 2); ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif;?>

<p class="new-page">Detail</p>
<div class="clear"></div>
<table class="table" style="margin-top: 20px;" width="100%">
    <thead>
        <tr>
            <th style="text-align: center;">No</th>
            <th style="text-align: center;">PO#</th>
            <th style="text-align: center;">Due Date</th>
            <th style="text-align: center;">Currency</th>
            <th style="text-align: center;">POE#</th>
            <th style="text-align: center;">Request Number</th>
            <th style="text-align: center;">Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php $n = 0; ?>
        <?php $amount_paid = array(); ?>
        <?php foreach ($entity['po'] as $i => $po) : ?>
        <?php $n++; ?>
        <tr>
            <td style="font-weight: bold;" class="no-space">
                <?= print_number($n); ?>
            </td>
            <td style="font-weight: bold;">
                <?=print_string($po['document_number'])?>
            </td>
            <td style="font-weight: bold;text-align: center;">
                <?= print_date($po['due_date'],'d/m/Y'); ?>
            </td>
            <td style="font-weight: bold;text-align: center;">
                <?= print_string($entity['currency']); ?>
            </td>
            <td style="font-weight: bold;">
                
            </td>
            <td style="font-weight: bold;">
                
            </td>
            <td style="text-align: right;font-weight: bold;">
                <?= print_number($po['amount_paid'], 2); ?>
                <?php $amount_paid[] = $po['amount_paid']; ?>
            </td>
        </tr>
        <?php foreach ($po['items'] as $i => $item) : ?>
        <tr>
            <td></td>
            <td colspan="3" style="font-weight: normal;">
                <?= print_string($item['description']); ?>
            </td>
            <td style="font-weight: normal;text-align: center;">
                <?=print_string($item['poe_number'])?>
            </td>
            <td style="font-weight: normal;text-align: center;">
                <?=print_string($item['request_number'])?>
            </td>
            <td style="text-align: right;font-weight: normal;">
                <?= print_number($item['amount_paid'], 2); ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6">Total</th>
            <th style="text-align: right;"><?= print_number(array_sum($amount_paid), 2); ?></th>
        </tr>
    </tfoot>
</table>

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
        <th>: <?= print_string($entity['document_number']); ?></th>
        <td>Payment to</td>
        <th>: <?= $entity['vendor']; ?></th>
        <td>Paid at</td>
        <th>: <?= ($entity['paid_at']!='')? print_date($entity['paid_at']):'-'; ?></th>
    </tr>
    <tr>
        <td>Date.</td>
        <th>: <?= print_date($entity['tanggal']); ?></th>
        <td>Payment No</td>
        <th>: <?= $entity['payment_number']; ?></th>
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
            <th>No</th>
            <th>SPD#</th>
            <th>Date</th>
            <th>Person in Charge</th>
            <th style="text-align:center;">Remarks</th>
            <th style="text-align:right;">Amount Request</th>
        </tr>
    </thead>
    <tbody>
        <?php $n = 0; ?>
        <?php $amount_paid = array(); ?>
        <?php foreach ($entity['request'] as $i => $request) : ?>
        <?php $n++; ?>
        <tr>
            <td class="no-space">
                <?= print_number($n); ?>
            </td>
            <td>
                <?=print_string($request['spd_number'])?>
            </td>                 
                                
            <td>
                <?= print_date($request['spd_date']); ?>
            </td>
            <td>
                <?= print_string($request['spd_person_incharge']); ?>
            </td>
            <td>
                <?= print_string($request['remarks']); ?>
            </td>
            <td style="text-align:right;">
                <?= print_number($request['amount_paid'],2); ?>
                <?php $amount_paid[] = $request['amount_paid']; ?>
            </td>
        </tr>                                
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5">Total</th>
            <th style="text-align: right;"><?= print_number(array_sum($amount_paid), 2); ?></th>
        </tr>
        <tr>
            <th colspan="5">Paid</th>
            <th style="text-align: right;"><?= print_number($entity['amount_paid'], 2); ?></th>
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
                Request by
                <br /><?=$entity['signers']['created by']['roles'];?>
                <br />&nbsp;
                <?php if ($entity['signers']['created by']['sign']) : ?>
                <?=print_date($entity['signers']['created by']['date'],'d M Y');?>
                <br>
                <img src="<?= base_url('ttd_user/' . $entity['signers']['created by']['sign']); ?>" width="auto" height="50">
                <?php endif; ?>
                <br />
                <br /><?=$entity['signers']['created by']['person_name'];?>
            </p>
        </td>
        <td valign="top" align="center">
            <p>
                Review by:
                <br /><?=$entity['signers']['review by']['role'];?>
                <?php if ($entity['signers']['review by']['sign']) : ?>
                <?=print_date($entity['signers']['review by']['date'],'d M Y');?>
                <br>
                <img src="<?= base_url('ttd_user/' . $entity['signers']['review by']['sign']); ?>" width="auto" height="50">
                <?php endif; ?>
                <br />
                <br /><?=$entity['signers']['review by']['person_name'];?>
            </p>
        </td>
        <td valign="top" align="center">
            <p>
                Approved by:
                <br /><?=$entity['signers']['approved by']['role'];?>
                <?php if ($entity['signers']['approved by']['sign']) : ?>
                <?=print_date($entity['signers']['approved by']['date'],'d M Y');?>
                <br>
                <img src="<?= base_url('ttd_user/' . $entity['signers']['approved by']['sign']); ?>" width="auto" height="50">
                <?php endif; ?>
                <br />
                <br /><?=$entity['signers']['approved by']['person_name'];?>
            </p>
        </td>
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
        <?php $total_debet = array(); $total_kredit = array(); ?>
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
        <?php $total_debet[] = $jurnal['trs_debet']; $total_kredit[] = $jurnal['trs_kredit']; ?>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">Total</th>
            <th style="text-align: right;"><?= print_number(array_sum($total_debet), 2); ?></th>
            <th style="text-align: right;"><?= print_number(array_sum($total_kredit), 2); ?></th>
        </tr>
    </tfoot>
</table>

<?php endif;?>

<p class="new-page">Detail</p>

<div class="clear"></div>

<table class="table" style="margin-top: 20px;" width="100%">
    <thead>
        <tr>
            <th style="text-align: center;">No</th>
            <th style="text-align: center;">ER#</th>
            <th style="text-align: center;">Description</th>
            <th style="text-align: right;">Amount Request Payment</th>
        </tr>
    </thead>
    <tbody>
        <?php $n = 0; ?>
        <?php $amount_paid = array(); ?>
        <?php foreach ($entity['request'] as $i => $request) : ?>
        <?php $n++; ?>
        <tr>
            <th class="no-space">
                <?= print_number($n); ?>
            </th>
            <th>
                <?=print_string($request['pr_number'])?>
            </th> 
            <th>
                <?= print_string($request['remarks']); ?>
            </th>
            <th style="text-align: right;">
                <?= print_number($request['amount_paid'], 2); ?>
                <?php $amount_paid[] = $request['amount_paid']; ?>
            </th>
        </tr>
        <?php foreach ($request['items'] as $j => $item) : ?>
                
        <tr>
            <td class="no-space"></td>
            <td colspan="2">
                <?= print_string($item['deskripsi']); ?>
            </td>
            <td style="text-align: right;">
                <?= print_number($item['amount_paid'], 2); ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">Total</th>
            <th style="text-align: right;"><?= print_number(array_sum($amount_paid), 2); ?></th>
        </tr>
    </tfoot>
</table>

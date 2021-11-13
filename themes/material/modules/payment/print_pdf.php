<style>
  @media print {
    .new-page {
      page-break-before: always;
    }
  }
</style>
<table class="table-no-strip condensed">
    <tr>
        <td>Payment to</td>
        <th widtd="40%">: <?= $entity['vendor']; ?></th>
        <td>TRANSACTION NO </td>
        <th>: <?= print_string($entity['no_transaksi']); ?></th>
    </tr>
    <tr>
        <td></td>
        <th></th>
        <td>Date.</td>
        <th>: <?= print_date($entity['tanggal']); ?></th>
    </tr>
    <tr>
        <td></td>
        <th></th>
        <td>Purposed Date</td>
        <th>: <?= print_date($entity['purposed_date']); ?></th>
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
            <th style="text-align: center;">Description</th>
            <th style="text-align: center;">POE#</th>
            <th style="text-align: center;">Request Number</th>
            <th style="text-align: center;">Amount Request Payment</th>
        </tr>
    </thead>
    <tbody>
        <?php $n = 0; ?>
        <?php $amount_paid = array(); ?>
        <?php foreach ($entity['items'] as $i => $detail) : ?>
        <?php $n++; ?>
        <tr>
            <td class="no-space">
                <?= print_number($n); ?>
            </td>
            <td>
                <?=print_string($detail['document_number'])?>
            </td>
            <td>
                <?= print_date($detail['due_date'],'d/m/Y'); ?>
            </td>
            <td>
                <?= print_string($detail['default_currency']); ?>
            </td>
            <td>
                <?= print_string($detail['description']); ?>
            </td>
            <td>
                <?=print_string($detail['poe_number'])?>
            </td>
            <td>
                <?=print_string($detail['request_number'])?>
            </td>
            <td style="text-align: right;">
                <?= print_number($detail['amount_paid'], 2); ?>
                <?php $amount_paid[] = $detail['amount_paid']; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="7">Total</th>
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
                Request by:
                <br />AP STAFF
                <br />&nbsp;<br>
                <?php if ($entity['created_by'] != '') : ?>
                <img src="<?= base_url('ttd_user/' . get_ttd($entity['created_by'])); ?>" width="auto" height="50">
                <?php endif; ?>
                <br />
                <br /><?= $entity['created_by']; ?>
            </p>
        </td>

        <td valign="top" align="center">
            <p>
                Checked by:
                <br />Finance Supervisor
                <?php if ($entity['checked_by'] != '') : ?>
                    <br /><?= print_date($entity['checked_at']) ?><br>
                <img src="<?= base_url('ttd_user/' . get_ttd($entity['checked_by'])); ?>" width="auto" height="50">
                <?php endif; ?>
                <br />
                <br /><?= $entity['checked_by']; ?>
            </p>
        </td>

        <td valign="top" align="center">
            <p>
                Review by:
                <br />Finance Manager
                <?php if ($entity['review_by'] != '') : ?>
                    <br /><?= print_date($entity['review_at']) ?><br>
                <img src="<?= base_url('ttd_user/' . get_ttd($entity['review_by'])); ?>" width="auto" height="50">
                <?php endif; ?>
                <br />
                <br /><?= $entity['review_by']; ?>
            </p>
        </td>

        <td valign="top" align="center">
            <p>
                <?php if(($entity['currency']=='IDR'&&$amount_paid>15000000)||($entity['currency']!='IDR'&&$amount_paid>1500)):?>
                Known by:
                <?php else:?>
                Approved by:
                <?php endif;?>
                <br /> <?= ($entity['base']=='JAKARTA') ? 'VP Finance' : 'Head of School'; ?>
                <?php if ($entity['known_by'] != '') : ?>
                <br /><?= print_date($entity['known_at']) ?><br>
                <img src="<?= base_url('ttd_user/' . get_ttd($entity['known_by'])); ?>" width="auto" height="50">
                <?php endif; ?>
                <br />
                <br /><?= $entity['known_by']; ?>
            </p>
        </td>

        <?php if(($entity['currency']=='IDR'&&$amount_paid>15000000)||($entity['currency']!='IDR'&&$amount_paid>1500)):?>
        <td valign="top" align="center">
            <p>
                Approved by:
                <br /> 
                <?= ($entity['base']=='JAKARTA') ? 'Chief of Finance' : 'Chief Operation Officer'; ?>
                <?php if ($entity['approved_by'] != '') : ?>
                <br /><?= print_date($entity['approved_at']) ?><br>
                <img src="<?= base_url('ttd_user/' . get_ttd($entity['approved_by'])); ?>" width="auto" height="50">
                <?php endif; ?>
                <br />
                <br /><?= $entity['approved_by']; ?>
            </p>
        </td>
        <?php endif;?>
    </tr>
</table>

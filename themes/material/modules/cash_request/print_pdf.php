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
        <td>Status</td>
        <th>
            : <?php if($entity['status']=='PAID'):?>
              PAID
              <?php endif;?>
              <?php if($entity['status']=='APPROVED'):?>
              WAITING PAYMENT
              <?php endif;?>
              <?php if($entity['status']=='REJECTED'):?>
              REJECTED by <?= $entity['rejected_by']; ?> at <?= print_date($entity['rejected_at'],'d/m/Y'); ?>
              <?php endif;?>
              <?php if($entity['status']!='APPROVED' && $entity['status']!='PAID' && $entity['status']!='REJECTED'):?>
              Purpose Review
              <?php endif;?>
        </th>
    </tr>
    <tr>
        <td>Date.</td>
        <th>: <?= print_date($entity['tanggal']); ?></th>
        <td>Paid at</td>
        <th>: <?= ($entity['paid_at']!='')? print_date($entity['paid_at']):'n/b'; ?></th>
    </tr>
    <tr>
        <td>Request By</td>
        <th>: <?= print_string($entity['request_by']); ?></th>
        <td>Paid By</td>
        <th>: <?= (empty($entity['paid_by'])) ? 'n/b' : print_string($entity['paid_by']); ?></th>
    </tr>
    <tr>
        <td>Cash Account.</td>
        <th>: <?= $entity['cash_account_code']; ?> <?= $entity['cash_account_name']; ?></th>
        <td>Transfer From</td>
        <th>: <?= $entity['coa_kredit']; ?> <?= $entity['akun_kredit']; ?></th>
    </tr>
    <tr>
        <td>Amount</td>
        <th>: <?= print_number($entity['request_amount'],2); ?></th>
        <td>No. Cheque</td>
        <th>: <?= ($entity['status']=='PAID')? $entity['no_cheque']:'n/b'; ?></th>
    </tr>
    <tr>
        <td>Notes</td>
        <th>: <?= (empty($entity['notes'])) ? '' : nl2br($entity['notes']); ?></th>
        <td>No. Konfirmasi</td>
        <th>: <?= ($entity['status']=='PAID')? $entity['no_konfirmasi']:'n/b'; ?></th>
    </tr>
</table>

<div class="clear"></div>

<div class="clear"></div>


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

        <?php if($entity['base']!='JAKARTA'): ?>
        <!-- <td valign="top" align="center">
            <p>
                Approved by:
                <br />Finance Supervisor
                <?php if ($entity['checked_by'] != '') : ?>
                    <br /><?= print_date($entity['checked_at']) ?><br>
                <img src="<?= base_url('ttd_user/' . get_ttd($entity['checked_by'])); ?>" width="auto" height="50">
                <?php endif; ?>
                <br />
                <br /><?= $entity['checked_by']; ?>
            </p>
        </td> -->
        <?php endif; ?>

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

        <?php if($entity['base']=='JAKARTA'): ?>
        <td valign="top" align="center">
            <p>
                Approved by:
                <br /> VP Finance
                <?php if ($entity['known_by'] != '') : ?>
                <br /><?= print_date($entity['known_at']) ?><br>
                <img src="<?= base_url('ttd_user/' . get_ttd($entity['known_by'])); ?>" width="auto" height="50">
                <?php endif; ?>
                <br />
                <br /><?= $entity['known_by']; ?>
            </p>
        </td>
        <?php endif; ?>
    </tr>
</table>

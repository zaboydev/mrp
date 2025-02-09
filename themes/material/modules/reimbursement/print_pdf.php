<style>
  @media print {
    .new-page {
      page-break-before: always;
    }
  }

  .table-spd th,
  .table-spd td {
    padding: 5px 5px;
    font-size: 12px;
  }
</style>
<table class="table-spd" width="100%">
<tr>
    <th width="30%"> Date </th>
    <td width="70%">: <?=print_date($entity['date'],'d M Y');?></td>
  </tr>
  <tr>
    <th width="30%"> NO </th>
    <td width="70%">: <?= print_string($entity['document_number']); ?></td>
  </tr>
  <tr>
    <th width="30%"> Expense Number </th>
    <td width="70%">: <?= print_string($entity['pr_number']); ?></td>
  </tr>
  <tr>
    <th> ID. Nbr/No Karyawan </th>
    <td>: <?= print_string($entity['employee_number']); ?></td>
  </tr>
  <tr>
    <th> Name/Name </th>
    <td>: <?= print_string($entity['person_name']); ?></td>
  </tr>
  <tr>
    <th> Job Title/Jabatan </th>
    <td>: <?= print_string($entity['occupation']); ?></td>
  </tr>
  <tr>
    <th> Dept. Name </th>
    <td>: <?= print_string($entity['department_name']); ?></td>
  </tr>
</table>

<div class="clear"></div>

<table class="table table-striped table-nowrap">
    <thead id="table_header">
        <tr>
            <th colspan="6">Realization</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Expense Detail</th>
            <th style="text-align:right;">Description</th>
            <th style="text-align:right;">Amount</th>
            <th style="text-align:right;">Date of invoice / provide</th>
            <th style="text-align:right;">Amount</th>
            <th style="text-align:right;">Paid Amount</th>
        </tr>
    </thead>
    <tbody id="table_contents">
        <?php $n = 1;?>
        <?php $total = array();?>
        <?php $total_real = array();?>
        <?php foreach ($entity['items'] as $item) :?>
        <tr>
            <td><?=$n++;?></td>
            <td><?=print_string($item['description']);?></td>
            <td><?=print_string($item['notes']);?></td>
            <td><?=print_string($item['account_code']);?></td>
            <td style="text-align:right;"><?=print_date($item['transaction_date']);?></td>
            <td style="text-align:right;"><?=print_number($item['amount'],2);?></td>
            <td style="text-align:right;"><?=print_number($item['paid_amount'],2);?></td>

        </tr>
        <?php $total[] = $item['paid_amount'];?>
        <?php endforeach;?>
    </tbody>
    <tfoot>
        <tr>
            <th>Total Claim</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th style="text-align:right;"><?=print_number(array_sum($total), 2);?></th>
        </tr>
        
    </tfoot>
</table>

<?php if ($entity['signers']['rejected by']['person_name']) : ?>
Rejected by : <?=$entity['signers']['rejected by']['person_name'];?> , at : <?=print_date($entity['signers']['rejected by']['date'],'d M Y');?>
<?php endif; ?>
<div class="clear"></div>

<?php if($entity['status']!='REJECTED' && $entity['status']!='REVISED'):?>

<table class="condensed" style="margin-top: 20px;" width="100%">
  <tr>
    <td valign="top" style="text-align:center;">
      <p>
        Requested by
        <br />Employee<br />
        <?php if ($entity['signers']['requested by']['sign']) : ?>
          <?=print_date($entity['signers']['requested by']['date'],'d M Y');?>
          <br>
          <img src="<?= base_url('ttd_user/' . $entity['signers']['requested by']['sign']); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?=$entity['signers']['requested by']['person_name'];?>
      </p>
    </td>
    <?php if ($entity['type'] != "SANTUNAN DUKA") : ?>
      <td valign="top" style="text-align:center;">
      <p>
        HR Approved by
        <br />
        <?php if ($entity['signers']['hr approved by']['sign']) : ?>
          <?=print_date($entity['signers']['hr approved by']['date'],'d M Y');?>
          <br>
          <img src="<?= base_url('ttd_user/' . $entity['signers']['hr approved by']['sign']); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?=$entity['signers']['hr approved by']['person_name'];?>
      </p>
    </td>
    <td valign="top" style="text-align:center;">
      <p>
        Validated by
        <?php if ($entity['occupation'] == "HEAD OF SCHOOL" || $entity['occupation'] == "VP FINANCE") : ?>
        <br />CFO/COO<br />
        <?php else:?>
          <br />Head Dept / VP Finance<br />
        <?php endif; ?>
        <?php if ($entity['signers']['validated by']['sign']) : ?>
          <?=print_date($entity['signers']['validated by']['date'],'d M Y');?>
          <br>
          <img src="<?= base_url('ttd_user/' . $entity['signers']['validated by']['sign']); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?=$entity['signers']['validated by']['person_name'];?>
      </p>
    </td>
    <?php if ($entity['type'] == "SANTUNAN DUKA") : ?>
    <td valign="top" style="text-align:center;">
      <p>
        Finance Approved by
        <br />
        <br />&nbsp;<br>
        <?php if ($entity['signers']['finance approved by']['sign']) : ?>
          <?=print_date($entity['signers']['finance approved by']['date'],'d M Y');?>
          <br>
          <img src="<?= base_url('ttd_user/' . $entity['signers']['finance approved by']['sign']); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?=$entity['signers']['finance approved by']['person_name'];?>
      </p>
    </td>
    <?php endif; ?>
    <?php endif; ?>

    <?php if ($entity['type'] == "SANTUNAN DUKA") : ?>
    <td valign="top" style="text-align:center;">
      <p>
        Validated by
        <br />Head Dept/ VP Finance<br />
        <?php if ($entity['signers']['validated by']['sign']) : ?>
          <?=print_date($entity['signers']['validated by']['date'],'d M Y');?>
          <br>
          <img src="<?= base_url('ttd_user/' . $entity['signers']['validated by']['sign']); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?=$entity['signers']['validated by']['person_name'];?>
      </p>
    </td>
    <?php endif; ?>

    

    
  </tr>
</table>
<?php endif; ?>



<style>
  @media print {
    .new-page {
      page-break-before: always;
    }
  }

  .table-spd th,
  .table-spd td {
    padding: 5px 5px;
    font-size: 13px;
  }
</style>
<table class="table-spd" width="100%">
  <tr>
    <th width="30%"> NO </th>
    <td width="70%">: <?= print_string($entity['document_number']); ?></td>
  </tr>
  <tr>
    <th> Supervisor/Atasan </th>
    <td>: <?= print_string($entity['head_dept_name']); ?></td>
  </tr>
  <tr>
    <th> Name/Name </th>
    <td>: <?= print_string($entity['person_name']); ?></td>
  </tr>
  <tr>
    <th> ID. Number/No Identitas </th>
    <td>: <?= print_string($entity['id_number']); ?></td>
  </tr>
  <tr>
    <th> Phone Number/No HP </th>
    <td>: <?= print_string($entity['phone_number']); ?></td>
  </tr>
  <tr>
    <th> From/Kota Asal </th>
    <td>: <?= print_string($entity['from_base']); ?></td>
  </tr>
  <tr>
    <th> Destination/Tujuan </th>
    <td>: <?= print_string($entity['business_trip_destination']); ?></td>
  </tr>
  <tr>
    <th> Duration/Lama Perjalanan </th>
    <td>: <?= print_string($entity['duration']); ?> Days from <?= print_date($entity['start_date'],'d M Y')?> to <?= print_date($entity['end_date'],'d M Y')?></td>
  </tr>
  <tr>
    <th> Purpose of Travel on Duty / Maksud perjalanan dinas </th>
    <td>: <?= print_string($entity['notes']); ?></td>
  </tr>
</table>

<div class="clear"></div>

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

    <td valign="top" style="text-align:center;">
      <p>
        Approved by
        <br />Supervisor
        <?php if ($entity['signers']['known by']['sign']) : ?>
          <?=print_date($entity['signers']['known by']['date'],'d M Y');?>
          <br>
          <img src="<?= base_url('ttd_user/' . $entity['signers']['known by']['sign']); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?=$entity['signers']['known by']['person_name'];?>
      </p>
    </td>

    <td valign="top" style="text-align:center;">
      <p>
        Approved by
        <br />HR Manager
        <br />&nbsp;<br>
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
<?php endif; ?>

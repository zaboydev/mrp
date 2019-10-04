<table class="table-no-strip condensed">
  <tr>
    <td widtd="15%">Created By</td>
    <th widtd="40%">: <?= $entity['created_by']; ?></th>
    <td widtd="15%">Document No.</td>
    <th widtd="30%">: <?= print_string($entity['evaluation_number']); ?></th>
  </tr>
  <tr>
    <td>Inventory</td>
    <th>: <?= print_string($entity['category']); ?></th>
    <td>Date</td>
    <th>: <?= print_date($entity['document_date']); ?></th>
  </tr>
  <tr>
    <td></td>
    <th></th>
    <td>Status</td>
    <th>: <?= ($entity['status'] == 'evaluation') ? 'DRAFT' : strtoupper($entity['status']); ?></th>
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
      <th class="middle-alignment" align="right" rowspan="2">Qty</th>
      <th class="middle-alignment" align="center" rowspan="2">Unit</th>
      <!-- <th class="middle-alignment" align="center">Remarks</th>
      <th class="middle-alignment" align="center">PR Number</th>
      <th class="middle-alignment" align="center" colspan="4">Vendor Detail</th> -->
      <?php foreach ($entity['vendors'] as $key => $vendor) : ?>
        <th class="middle-alignment text-center" colspan="4"><?= $vendor['vendor']; ?></th>
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
        <td>
          <?= print_number($detail['quantity'], 2); ?>
        </td>
        <td>
          <?= print_string($detail['unit']); ?>
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
        <br>
        <br>
        <?php if ($entity['created_by'] != '') : ?>
          <img src="<?= base_url('ttd_user/' . get_ttd($entity['created_by'])); ?>" width="100">
        <?php endif; ?>
        <br>
        <br><?= $entity['created_by']; ?>
      </p>
    </td>
    <td width="50%" valign="top" align="center">
      <p>Approved by:
        <br>
        <?= print_date($entity['updated_at']); ?>
        <br>
        <?php if ($entity['approved_by'] != '' & $entity['approved_by'] != 'without_approval') : ?>
          <img src="<?= base_url('ttd_user/' . get_ttd($entity['created_by'])); ?>" width="100">
        <?php endif; ?>
        <?php if ($entity['approved_by'] == 'without_approval') : ?>
          <img src="<?= base_url('ttd_user/mark.png'); ?>" width="100">
        <?php endif; ?>
        <br>
        <br><?php if ($entity['approved_by'] != 'without_approval') : ?>
          <?= $entity['approved_by']; ?>
        <?php endif; ?></p>
    </td>
  </tr>
</table>
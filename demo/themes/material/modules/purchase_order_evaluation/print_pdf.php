<table class="table-no-strip condensed">
  <tr>
    <td widtd="15%">Created By</td>
    <th widtd="40%">: <?=$entity['created_by'];?></th>
    <td widtd="15%">Document No.</td>
    <th widtd="30%">: <?=print_string($entity['evaluation_number']);?></th>
  </tr>
  <tr>
    <td>Inventory</td>
    <th>: <?=print_string($entity['category']);?></th>
    <td>Date</td>
    <th>: <?=print_date($entity['document_date']);?></th>
  </tr>
  <tr>
    <td></td>
    <th></th>
    <td>Status</td>
    <th>: <?=($entity['status'] == 'evaluation') ? 'DRAFT' : strtoupper($entity['status']);?></th>
  </tr>
</table>

<div class="clear"></div>

<table class="table" style="margin-top: 20px;">
  <thead>
    <tr>
      <th class="middle-alignment" align="center" rowspan="2"></th>
      <th class="middle-alignment" align="center" rowspan="2">Description</th>
      <th class="middle-alignment" align="center" rowspan="2">Part Number</th>
      <th class="middle-alignment" align="right" rowspan="2">Qty</th>
      <th class="middle-alignment" align="center" rowspan="2">Unit</th>
      <th class="middle-alignment" align="center" rowspan="2">Remarks</th>
      <th class="middle-alignment" align="center" rowspan="2">PR Number</th>

      <?php foreach ($entity['vendors'] as $v => $vendor):?>
        <th class="middle-alignment" align="center" colspan="4"><?=$vendor['vendor'];?></th>
      <?php endforeach;?>
    </tr>
    <tr>
      <?php foreach ($entity['vendors'] as $key => $value):?>
      <th class="middle-alignment" align="center">Alt. P/N</th>
        <th class="middle-alignment" align="center">Unit Price</th>
        <th class="middle-alignment" align="center">Core Charge</th>
        <th class="middle-alignment" align="center">Total</th>
      <?php endforeach;?>
    </tr>
  </thead>
  <tbody>
    <?php $n = 0;?>
    <?php foreach ($entity['request'] as $i => $detail):?>
      <?php $n++;?>
      <tr id="row_<?=$i;?>">
        <td width="1">
          <?=$n;?>
        </td>
        <td>
          <?=print_string($detail['description']);?>
        </td>
        <td class="no-space">
          <?=print_string($detail['part_number']);?>
        </td>
        <td>
          <?=print_number($detail['quantity'], 2);?>
        </td>
        <td>
          <?=print_string($detail['unit']);?>
        </td>
        <td>
          <?=print_string($detail['remarks']);?>
        </td>
        <td>
          <?=print_string($detail['purchase_request_number']);?>
        </td>

                  <?php foreach ($entity['vendors'] as $key => $vendor):?>
                    <?php
                    if ($vendor['is_selected'] == 't'){
                      $style = 'background-color: green; color: white';
                    } else {
                      $style = '';
                    }
                    ?>
                    <td style="<?=$style;?>">
                      <?=$detail['vendors'][$key]['alternate_part_number'];?>
                    </td>
                    <td style="<?=$style;?>">
                      <?=number_format($detail['vendors'][$key]['unit_price'], 2);?>
                    </td>
                    <td style="<?=$style;?>">
                      <?=number_format($detail['vendors'][$key]['core_charge'], 2);?>
                    </td>
                    <td style="<?=$style;?>">
                      <?=number_format($detail['vendors'][$key]['total'], 2);?>
                    </td>
                  <?php endforeach;?>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

<div class="clear"></div>

<?=(empty($entity['notes'])) ? '' : '<p>Note: '.nl2br($entity['notes']).'</p>';?>

<div class="clear"></div>

<table class="condensed" style="margin-top: 20px;">
  <tr>
    <td width="50%" valign="top" align="center">
      <p>Created by:</p>
    </td>
    <td width="50%" valign="top" align="center">
      <p>Approved by:</p>
    </td>
  </tr>
</table>

<style>
  @media print {
    .new-page {
      page-break-before: always;
    }
  }
</style>

<table class="table-no-strip condensed">
  <tr>
    <td>Periode</td>
    <th widtd="40%">: <?= $periode ?></th>
    <td></td>
    <th></th>
  </tr>
  <tr>
    <td>Vendor</td>
    <th>: <?= print_string($vendor); ?></th>
    <td></td>
    <th></th>
  </tr>
</table>

<table class="table" style="margin-top: 20px;" width="100%">
  <thead>
    <tr>
      <th class="middle-alignment">No</th>
      <th class="middle-alignment">PO#</th>
      <th class="middle-alignment">No Nota</th>
      <th class="middle-alignment">Date</th>
      <th class="middle-alignment">Amount PO</th>
      <th class="middle-alignment">Amount Nota</th>
    </tr>
  </thead>
  <tbody>
    <?php 
      $no = 1; 
      $total_po = array();
      $total_nota = array();
    ?>
    <?php foreach ($entities as $i => $entity) : ?>
    <tr>
      <td style="font-weight:bold;"><?= $no++;?></td>
      <td style="font-weight:bold;"><?= print_string($entity['document_number']);?> </td>
      <td style="font-weight:bold;"></td>
      <td style="font-weight:bold;"></td>
      <td style="font-weight:bold;text-align:right;"><?= print_number($entity['grand_total'],2);?></td>
      <td style="font-weight:bold;"></td>
    </tr>
    <?php foreach ($entity['receipt'] as $r => $receipt) : ?>
    <tr>
      <td></td>
      <td></td>
      <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= print_string($receipt['reference_number']);?></td>
      <td>
        <?= ($receipt['tgl_nota']!=null)? print_date($receipt['tgl_nota']):'';?>
      </td>
      <td></td>
      <td style="text-align:right;"><?= print_number($receipt['received_total_value_idr'],2);?></td>
    </tr>
    <?php  
      $total_nota[] = $receipt['received_total_value_idr'];
    ?>
    <?php endforeach; ?>
    <?php  
      $total_po[] = $entity['grand_total'];
    ?>
    <?php endforeach; ?>
  </tbody>
  <tfoot>
    <tr>
      <th>Total</th>
      <th></th>
      <th></th>
      <th></th>
      <th style="text-align:right;"><?= print_number(array_sum($total_po), 2); ?></th>
      <th style="text-align:right;"><?= print_number(array_sum($total_nota), 2); ?></th>
    </tr>
  </tfoot>
</table>



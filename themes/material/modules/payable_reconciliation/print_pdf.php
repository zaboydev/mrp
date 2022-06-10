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
  <tr>
    <td>Currency</td>
    <th>: <?= print_string($currency); ?></th>
    <td></td>
    <th></th>
  </tr>  
</table>

<table class="table" style="margin-top: 20px;" width="100%">
  <thead>
    <tr>
      <th width="5%" class="middle-alignment">No</th>
      <th width="17%" class="middle-alignment">Name</th>
      <th width="8%" class="middle-alignment">Currency</th>
      <th width="14%" class="middle-alignment">Total Due</th>
      <th width="14%" class="middle-alignment">0-30 Days</th>
      <th width="14%" class="middle-alignment">31-60 Days</th>
      <th width="14%" class="middle-alignment">61-90 Days</th>
      <th width="14%" class="middle-alignment">90+ Days</th>
    </tr>
  </thead>
  <tbody>
    <?php 
      $no = 1; 
      $total_due_date = array();
      $total_a = array();
      $total_b = array();
      $total_c = array();
      $total_d = array();
    ?>
    <?php foreach ($items as $i => $detail) : ?>
        <?php
            $total_qty = array();
            $total_amount = array();
            $a = 0;
            $b = 0;
            $c = 0;
            $d = 0; 
        ?>
        <?php 
            if ($detail['currency'] == 'USD') {
                $total_due = $detail['usd'];
            } else {
                $total_due = $detail['idr'];
            }
        ?>
        <?php if ($total_due > 0) : ?>
        <?php foreach ($detail['po']['po_detail'] as $i => $info) : ?>
            <?php
                if ($info['ket'] < 31) {
                    $a += $info['a'];
                }
                if ($info['ket'] >= 31 && $info['ket'] <= 60) {
                    $b += $info['a'];
                }
                if ($info['ket'] >= 61 && $info['ket'] <= 90) {
                    $c += $info['a'];
                }
                if ($info['ket'] > 90) {
                    $d += $info['a'];
                }
                $total_amount[] = $info['a'];
            ?>
        <?php endforeach; ?>
        
            <tr>
                <td style="font-weight:bolder" align="left">
                    <?= print_string($no); ?>
                </td>
                <td style="font-weight:bolder" align="left">
                    <?= print_string($detail['vendor']); ?>
                </td>
                <td style="text-align:center;font-weight:bolder;" align="left">
                    <?= print_string($detail['currency']); ?>
                </td>
                <td style="font-weight:bolder" align="right">
                    
                
                <?= print_number($total_due - $detail['payment'], 2); ?>

                </td>
                <td align="right">
                    <?= print_number($a, 2); ?>
                </td>
                <td align="right">
                    <?= print_number($b, 2); ?>
                </td>
                <td align="right">
                    <?= print_number($c, 2); ?>
                </td>
                <td align="right">
                    <?= print_number($d, 2); ?>
                </td>
            </tr>
            <?php 
              $no++; 
              $total_due_date[] = $total_due - $detail['payment'];
              $total_a[] = $a;
              $total_b[] = $b;
              $total_c[] = $c;
              $total_d[] = $d;
            ?>
        <?php endif; ?>
    <?php endforeach; ?>
  </tbody>
  <tfoot>
    <tr>
      <th colspan="3">Total</th>
      <th align="right"><?= print_number(array_sum($total_due_date), 2); ?></th>
      <th align="right"><?= print_number(array_sum($total_a), 2); ?></th>
      <th align="right"><?= print_number(array_sum($total_b), 2); ?></th>
      <th align="right"><?= print_number(array_sum($total_c), 2); ?></th>
      <th align="right"><?= print_number(array_sum($total_d), 2); ?></th>
    </tr>
  </tfoot>
</table>



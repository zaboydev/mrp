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
    <td>Item</td>
    <th widtd="40%">: <?= $item ?></th>
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
        <th rowspan="2" width="40%" colspan="2" class="middle-alignment" style="text-align:center">Supplier Name</th>
        <th rowspan="2" width="10%" class="middle-alignment" style="text-align:center">Quantity</th>
        <th rowspan="2" width="10%" class="middle-alignment" style="text-align:center">Unit</th>
        <th colspan="2" width="40%" class="middle-alignment" style="text-align:center">Amount</th>
    </tr>
    <tr>
        <th width="20%" class="middle-alignment">USD</th>
        <th width="20%" class="middle-alignment">IDR</th>
    </tr>
  </thead>
  <tbody>
    <?php $no = 1; ?>
    <?php
    $grand_total_quantity = array();
    $grand_total_amount_idr = array();
    $grand_total_amount_usd = array();
    ?>
    <?php foreach ($items as $i => $detail) : ?>
        <?php $n++; ?>
        <?php
            $total_quantity = array();
            $total_amount_idr = array();
            $total_amount_usd = array();
            ?>
        <?php //if ($detail['po']['po_count'] > 0) : 
            ?>
        <tr>
            <td align="left" style="font-weight:bolder">
                <?= print_string($detail['part_number']); ?>
            </td>
            <td align="left" style="font-weight:bolder">
                <?= print_string($detail['description']); ?>
            </td>
            <td colspan="4"></td>
        </tr>
        <?php foreach ($detail['base'] as $i => $info_base) : ?>
            <tr>
                <td colspan="2" style="font-weight:bolder">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?= print_string($info_base['warehouse']); ?>
                </td>
                <td colspan="4"></td>
            </tr>
            <?php foreach ($info_base['items_grn']['grn_items'] as $i => $info) : ?>
            <tr>
                <td colspan="2">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?= print_string($info['received_from']); ?>
                </td>

                <td>
                    <?= print_number($info['quantity'], 2); ?>
                </td>
                <td align="center">
                    <?= print_string($info['unit']); ?>
                </td>
                <td>
                    <?= $info['kurs_dollar'] > 1 ? print_number($info['total_value_usd'], 2) : print_number(0, 2); ?>
                </td>
                <td>
                    <?= $info['kurs_dollar'] == 1 ? print_number($info['total_value_idr'], 2) : print_number(0, 2); ?>
                </td>

            </tr>
                <?php
                    if ($info['kurs_dollar'] > 1) {
                        $total_amount_usd[] = $info['total_value_usd'];
                        $grand_total_amount_usd[] = $info['total_value_usd'];
                    } else {
                        $total_amount_idr[] = $info['total_value_idr'];
                        $grand_total_amount_idr[] = $info['total_value_idr'];
                    }
                    $total_quantity[] = $info['quantity'];
                    $grand_total_quantity[] = $info['quantity'];
                ?>
            <?php endforeach; ?>
        <?php endforeach; ?>

        <tr>
            <th colspan="2" align="right" style="font-weight:bold;"><?= print_string($detail['description']); ?> TOTAL</th>
            <th style="font-weight:bold;"><?= print_number(array_sum($total_quantity), 2); ?></th>
            <th style="font-weight:bold;"></th>
            <th style="font-weight:bold;"><?= print_number(array_sum($total_amount_usd), 2); ?></th>
            <th style="font-weight:bold;"><?= print_number(array_sum($total_amount_idr), 2); ?></th>
        </tr>
        <tr>
            <td style="background-color: #f0f0f0;" colspan="6">&nbsp;</td>
        </tr>
    <?php endforeach; ?>
    
  </tbody>
  <tfoot>
    <tr>
        <th colspan="2" align="right" style="font-weight:bold;">GRAND TOTAL</th>
        <th style="font-weight:bold;"><?= print_number(array_sum($grand_total_quantity), 2); ?></th>
        <th style="font-weight:bold;"></th>
        <th style="font-weight:bold;"><?= print_number(array_sum($grand_total_amount_usd), 2); ?></th>
        <th style="font-weight:bold;"><?= print_number(array_sum($grand_total_amount_idr), 2); ?></th>
    </tr>
  </tfoot>
</table>



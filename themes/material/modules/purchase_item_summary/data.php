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
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= print_string($info_base['warehouse']); ?>
            </td>
            <td colspan="4"></td>
        </tr>
        <?php foreach ($info_base['items_grn']['grn_items'] as $i => $info) : ?>
            <td colspan="2">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= print_string($info['received_from']); ?>
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
        <td colspan="2" align="right" style="font-weight:bolder"><?= print_string($detail['description']); ?> TOTAL</td>
        <td style="font-weight:bolder"><?= print_number(array_sum($total_quantity), 2); ?></td>
        <td style="font-weight:bolder"></td>
        <td style="font-weight:bolder"><?= print_number(array_sum($total_amount_usd), 2); ?></td>
        <td style="font-weight:bolder"><?= print_number(array_sum($total_amount_idr), 2); ?></td>
    </tr>
    <tr>
        <td style="background-color: #f0f0f0;" colspan="6">&nbsp;</td>
    </tr>
<?php endforeach; ?>
<tr>
    <td colspan="2" align="right" style="font-weight:bolder">GRAND TOTAL</td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_total_quantity), 2); ?></td>
    <td style="font-weight:bolder"></td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_total_amount_usd), 2); ?></td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_total_amount_idr), 2); ?></td>
</tr>
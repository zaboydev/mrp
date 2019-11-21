<?php $no = 1; ?>
<?php
$grand_total_qty = array();
$grand_total_amount_idr = array();
$grand_total_amount_usd = array();
?>
<?php foreach ($items as $i => $detail) : ?>
    <?php $n++; ?>
    <?php
        $total_qty = array();
        $total_amount_idr = array();
        $total_amount_usd = array();
        ?>
    <?php if ($detail['items_po']['po_items_count'] > 0) : ?>
        <tr>
            <td style="font-weight: bolder;" align="left">
                <?= print_string($detail['part_number']); ?>
            </td>
            <td style="font-weight: bolder;" align="left" colspan="8">
                <?= print_string($detail['description']); ?>
            </td>
        </tr>
        <?php foreach ($detail['items_po']['po_items'] as $i => $info) : ?>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= print_string($info['vendor']); ?>
                </td>
                <td>
                    <?= print_string($info['document_number']); ?>
                </td>
                <td>
                    <?= print_date($info['document_date']); ?>
                </td>
                <td>
                    <?= print_number($info['quantity'], 2); ?>
                </td>
                <td align="center">
                    <?= print_string($info['unit']); ?>
                </td>
                <td>
                    <?= $info['kurs_dollar'] > 1 ? print_number($info['total_amount'], 2) : print_number(0, 2); ?>
                </td>
                <td>
                    <?= $info['kurs_dollar'] == 1 ? print_number($info['total_amount'], 2) : print_number(0, 2); ?>
                </td>
                <td style="text-align: center;">
                    <?= print_string($info['status']); ?>
                </td>
                <td>
                    <?= print_date($info['due_date']); ?>
                </td>
            </tr>
            <?php
                        $total_qty[] = $info['quantity'];
                        $grand_total_qty[] = $info['quantity'];
                        if ($info['kurs_dollar'] > 1) {
                            $total_amount_usd[] = $info['total_amount'];
                            $grand_total_amount_usd[] = $info['total_amount'];
                        } else {
                            $total_amount_idr[] = $info['total_amount'];
                            $grand_total_amount_idr[] = $info['total_amount'];
                        }
                        ?>
        <?php endforeach; ?>
        <tr>
            <td colspan="3" align="right" style="font-weight:bolder"><?= print_string($detail['description']); ?></td>
            <td style="font-weight:bolder"><?= print_number(array_sum($total_qty), 2); ?></td>
            <td style="font-weight:bolder"></td>
            <td style="font-weight:bolder"><?= print_number(array_sum($total_amount_usd), 2); ?></td>
            <td style="font-weight:bolder"><?= print_number(array_sum($total_amount_idr), 2); ?></td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td style="background-color: #f0f0f0;" colspan="9">&nbsp;</td>
        </tr>
    <?php endif; ?>


<?php endforeach; ?>
<tr>
    <td colspan="3" align="right" style="font-weight:bolder">GRAND TOTAL</td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_total_qty), 2); ?></td>
    <td style="font-weight:bolder"></td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_total_amount_usd), 2); ?></td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_total_amount_idr), 2); ?></td>
    <td colspan="4"></td>
</tr>
<?php $no = 1; ?>
<?php
$grand_total_remaining_idr = array();
$grand_total_amount_idr = array();
$grand_total_remaining_usd = array();
$grand_total_amount_usd = array();
?>
<?php foreach ($items as $i => $detail) : ?>
    <?php $n++; ?>
    <?php
        $total_remaining_idr = array();
        $total_amount_idr = array();
        $total_remaining_usd = array();
        $total_amount_usd = array();
        ?>
    <?php if ($detail['po']['po_count'] > 0) : ?>
        <tr>
            <td style="font-weight: bolder;" align="left" colspan="11">
                <?= print_string($detail['vendor']); ?>
            </td>
        </tr>
        <?php foreach ($detail['po']['po_detail'] as $i => $info) : ?>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;<?= print_string($info['document_number']); ?>
                </td>
                <td>
                    <?= print_date($info['document_date']); ?>
                </td>
                <td>
                    <?= print_string($info['default_currency']); ?>
                </td>
                <td>
                    <?= $info['default_currency'] == 'USD' ? print_number($info['grand_total'], 2) : print_number(0, 2); ?>
                </td>
                <td>
                    <?= $info['default_currency'] == 'IDR' ? print_number($info['grand_total'], 2) : print_number(0, 2); ?>
                </td>
                <td>
                    <?= $info['default_currency'] == 'USD' ? print_number($info['grand_total'] * ($info['tax'] / 100), 2) : print_number(0, 2); ?>
                </td>
                <td>
                    <?= $info['default_currency'] == 'IDR' ? print_number($info['grand_total'] * ($info['tax'] / 100), 2) : print_number(0, 2); ?>
                </td>
                <td>
                    <?= $info['default_currency'] == 'USD' ? print_number($info['remaining_payment'], 2) : print_number(0, 2); ?>
                </td>
                <td>
                    <?= $info['default_currency'] == 'IDR' ? print_number($info['remaining_payment'], 2) : print_number(0, 2); ?>
                </td>
                <td>
                    <?= print_string($info['status']); ?>
                </td>
                <td>
                    <?= print_date($info['due_date']); ?>
                </td>
            </tr>
            <?php
                        if ($info['default_currency'] == 'IDR') {
                            $total_remaining_idr[] = $info['remaining_payment'];
                            $total_amount_idr[] = $info['grand_total'];
                            $grand_total_remaining_idr[] = $info['remaining_payment'];
                            $grand_total_amount_idr[] = $info['grand_total'];
                        } else {
                            $total_remaining_usd[] = $info['remaining_payment'];
                            $total_amount_usd[] = $info['grand_total'];
                            $grand_total_remaining_usd[] = $info['remaining_payment'];
                            $grand_total_amount_usd[] = $info['grand_total'];
                        }
                        ?>
        <?php endforeach; ?>
        <tr>
            <td colspan="3" align="right" style="font-weight:bolder"><?= print_string($detail['description']); ?></td>
            <td style="font-weight:bolder"><?= print_number(array_sum($total_amount_usd), 2); ?></td>
            <td style="font-weight:bolder"><?= print_number(array_sum($total_amount_idr), 2); ?></td>
            <td style="font-weight:bolder"><?= print_number(0, 2); ?></td>
            <td style="font-weight:bolder"><?= print_number(0, 2); ?></td>
            <td style="font-weight:bolder"><?= print_number(array_sum($total_remaining_usd), 2); ?></td>
            <td style="font-weight:bolder"><?= print_number(array_sum($total_remaining_idr), 2); ?></td>
            <td colspan="8"></td>
        </tr>
        <tr>
            <td style="background-color: #f0f0f0;" colspan="14">&nbsp;</td>
        </tr>
    <?php endif; ?>

<?php endforeach; ?>
<tr>
    <td colspan="3" align="right" style="font-weight:bolder">GRAND TOTAL</td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_total_amount_usd), 2); ?></td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_total_amount_idr), 2); ?></td>
    <td style="font-weight:bolder"><?= print_number(0, 2); ?></td>
    <td style="font-weight:bolder"><?= print_number(0, 2); ?></td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_total_remaining_usd), 2); ?></td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_total_remaining_idr), 2); ?></td>
    <td colspan="8"></td>
</tr>
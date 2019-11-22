<?php $no = 1; ?>
<?php foreach ($items as $i => $detail) : ?>
    <?php $n++; ?>
    <?php
        $total_amount_idr = array();
        $total_amount_usd = array();
        ?>
    <?php if ($detail['po']['po_count'] > 0) : ?>
        <tr>
            <td style="font-weight:bolder" align="left" colspan="8">
                <?= print_string($detail['vendor']); ?>
            </td>
        </tr>
        <?php foreach ($detail['po']['po_detail'] as $i => $info) : ?>
            <tr>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;<?= print_string($info['no_cheque']); ?>
                </td>
                <td>
                    <?= print_date($info['tanggal']); ?>
                </td>
                <td>
                    <?= print_string($info['document_number']); ?>
                </td>
                <td>
                    <?= print_date($info['document_date']); ?>
                </td>
                <td>
                    <?= $info['default_currency'] == 'USD' ? print_number($info['grand_total'], 2) : print_number(0, 2); ?>
                </td>
                <td>
                    <?= $info['default_currency'] == 'IDR' ? print_number($info['grand_total'], 2) : print_number(0, 2); ?>
                </td>
                <td>
                    <?= $info['default_currency'] == 'USD' ? print_number($info['amount_paid'], 2) : print_number(0, 2); ?>
                </td>
                <td>
                    <?= $info['default_currency'] == 'IDR' ? print_number($info['amount_paid'], 2) : print_number(0, 2); ?>
                </td>
            </tr>
            <?php
                        if ($info['default_currency'] == 'USD') {
                            $total_amount_usd[] = $info['amount_paid'];
                        } else {
                            $total_amount_idr[] = $info['amount_paid'];
                        }
                        ?>
        <?php endforeach; ?>
        <tr>
            <td colspan="6" align="right" style="font-weight:bolder">Total Payment</td>
            <td style="font-weight:bolder"><?= print_number(array_sum($total_amount_usd), 2); ?></td>
            <td style="font-weight:bolder"><?= print_number(array_sum($total_amount_idr), 2); ?></td>
        </tr>
    <?php endif; ?>


<?php endforeach; ?>
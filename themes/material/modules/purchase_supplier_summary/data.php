<?php $no = 1; ?>
<?php foreach ($items as $i => $detail) : ?>
    <?php $n++; ?>
    <?php
        $total_remaining = array();
        $total_amount = array();
        ?>
    <?php if ($detail['po']['po_count'] > 0) : ?>
        <tr>
            <td align="left">
                <?= print_string($detail['vendor']); ?>
            </td>
        </tr>
        <?php foreach ($detail['po']['po_detail'] as $i => $info) : ?>
            <?php
                        $total_remaining_detail = array();
                        $total_amount_detail = array();
                        ?>
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
                    <?= print_number($info['grand_total'], 2); ?>
                </td>
                <td>
                    <?= print_number($info['grand_total'] * ($info['taxe'] / 100), 2); ?>
                </td>
                <td>
                    <?= print_number($info['remaining_payment'], 2); ?>
                </td>
                <td>
                    <?= print_string($info['status']); ?>
                </td>
                <td>
                    <?= print_date($info['due_date']); ?>
                </td>
            </tr>
            <?php
                        $total_remaining[] = $info['remaining_payment'];
                        $total_amount[] = $info['grand_total'];
                        $total_remaining_detail[] = $info['remaining_payment'];
                        $total_amount_detail[] = $info['grand_total'];
                        ?>
        <?php endforeach; ?>
        <tr>
            <td colspan="2" align="right" style="font-weight:bolder"><?= print_string($detail['description']); ?></td>
            <td style="font-weight:bolder"><?= print_number(array_sum($total_amount), 2); ?></td>
            <td style="font-weight:bolder">&nbsp;</td>
            <td style="font-weight:bolder"><?= print_number(array_sum($total_remaining), 2); ?></td>
            <td colspan="2"></td>
        </tr>
    <?php endif; ?>


<?php endforeach; ?>
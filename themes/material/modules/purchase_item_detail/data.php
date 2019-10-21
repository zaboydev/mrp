<?php $no = 1; ?>
<?php foreach ($items as $i => $detail) : ?>
    <?php $n++; ?>
    <?php
        $total_qty = array();
        $total_amount = array();
        ?>
    <?php if ($detail['items_po']['po_items_count'] > 0) : ?>
        <tr>
            <td align="left">
                <?= print_string($detail['part_number']); ?>
            </td>
            <td align="left" colspan="5">
                <?= print_string($detail['description']); ?>
            </td>
        </tr>
        <?php foreach ($detail['items_po']['po_items'] as $i => $info) : ?>
            <tr>
                <td>
                    <?= print_string($info['vendor']); ?>
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
                <td>
                    <?= print_number($info['total_amount'], 2); ?>
                </td>
                <td>
                    <?= print_string($info['status']); ?>
                </td>
                <td>
                    <?= print_date($info['due_date']); ?>
                </td>
            </tr>
            <?php
                        $total_qty[] = $info['quantity'];
                        $total_amount[] = $info['total_amount'];
                        ?>
        <?php endforeach; ?>
        <tr>
            <td colspan="3" align="right" style="font-weight:bolder"><?= print_string($detail['description']); ?></td>
            <td style="font-weight:bolder"><?= print_number(array_sum($total_qty), 2); ?></td>
            <td style="font-weight:bolder"><?= print_number(array_sum($total_amount), 2); ?></td>
            <td colspan="2"></td>
        </tr>
    <?php endif; ?>


<?php endforeach; ?>
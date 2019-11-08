<?php $no = 1; ?>
<?php foreach ($items as $i => $detail) : ?>
    <?php $n++; ?>
    <?php
        $total_qty = array();
        $total_amount = array();
        ?>
    <tr>
        <?php $a = 0;
            $b = 0;
            $c = 0;
            $d = 0; ?>
        <?php foreach ($detail['po']['po_detail'] as $i => $info) : ?>
            <?php
                    if ($info['ket'] < 31) {
                        $a = $info['a'];
                    }
                    if ($info['ket'] >= 31 && $info['ket'] <= 60) {
                        $b = $info['a'];
                    }
                    if ($info['ket'] >= 61 && $info['ket'] <= 90) {
                        $c = $info['a'];
                    }
                    if ($info['ket'] > 90) {
                        $d = $info['a'];
                    }
                    ?>
        <?php endforeach; ?>
        <td align="left">
            <?= print_string($detail['vendor']); ?>
        </td>
        <td align="left">
            <?= print_string($detail['currency']); ?>
        </td>
        <td align="left">
            <?php if ($detail['currency'] == 'USD') {
                    $total_due = $detail['usd'];
                } else {
                    $total_due = $detail['idr'];
                }
                ?>
            <?= print_number($total_due - $detail['payment'], 2); ?>

        </td>
        <td>
            <?= print_number($a, 2); ?>
        </td>
        <td>
            <?= print_number($b, 2); ?>
        </td>
        <td>
            <?= print_number($c, 2); ?>
        </td>
        <td>
            <?= print_number($d, 2); ?>
        </td>
    </tr>

<?php endforeach; ?>
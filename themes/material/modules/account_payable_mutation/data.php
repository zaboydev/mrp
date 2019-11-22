<?php $no = 0; ?>
<?php
$grand_saldo_awal_usd = array();
$grand_pembelian_usd = array();
$grand_payment_usd = array();
$grand_saldo_akhir_usd = array();

$grand_saldo_awal_idr = array();
$grand_pembelian_idr = array();
$grand_payment_idr = array();
$grand_saldo_akhir_idr = array();
?>
<?php foreach ($items as $i => $detail) : ?>
    <?php $no++; ?>
    <?php
        $saldo_akhir_usd = $detail['saldo_awal_usd'] - $detail['payment_saldo_awal_usd'] + $detail['pembelian_usd'] - $detail['payment_usd'];
        $saldo_akhir_idr = $detail['saldo_awal_idr'] - $detail['payment_saldo_awal_idr'] + $detail['pembelian_idr'] - $detail['payment_idr'];

        $grand_saldo_awal_usd[]     = $detail['saldo_awal_usd'] - $detail['payment_saldo_awal_usd'];
        $grand_pembelian_usd[]      = $detail['pembelian_usd'];
        $grand_payment_usd[]        = $detail['payment_usd'];
        $grand_saldo_akhir_usd[]    = $saldo_akhir_usd;

        $grand_saldo_awal_idr[]     = $detail['saldo_awal_idr'] - $detail['payment_saldo_awal_idr'];
        $grand_pembelian_idr[]      = $detail['pembelian_idr'];
        $grand_payment_idr[]        = $detail['payment_idr'];
        $grand_saldo_akhir_idr[]    = $saldo_akhir_idr;
    ?>
    <tr>
        <td style="font-weight: bolder;" align="left">
            <?= $no; ?>
        </td>
        <td style="font-weight: bolder;" align="left">
            <?= print_string($detail['vendor']); ?>
        </td>
        <td align="left">
            <?= print_number(($detail['saldo_awal_usd'] - $detail['payment_saldo_awal_usd']), 2) ?>
        </td>
        <td align="left">
            <?= print_number(($detail['saldo_awal_idr'] - $detail['payment_saldo_awal_idr']), 2) ?>
        </td>
        <td align="left">
            <?= print_number($detail['pembelian_usd'], 2) ?>
        </td>
        <td align="left">
            <?= print_number($detail['pembelian_idr'], 2) ?>
        </td>
        <td align="left">
            <?= print_number(0, 2) ?>
        </td>
        <td align="left">
            <?= print_number(0, 2) ?>
        </td>
        <td align="left">
            <?= print_number($detail['payment_usd'], 2) ?>
        </td>
        <td align="left">
            <?= print_number($detail['payment_idr'], 2) ?>
        </td>
        <td align="left">
            <?= print_number(0, 2) ?>
        </td>
        <td align="left">
            <?= print_number($saldo_akhir_usd, 2) ?>
        </td>
        <td align="left">
            <?= print_number($saldo_akhir_idr, 2) ?>
        </td>
    </tr>


<?php endforeach; ?>
<tr>
    <td colspan="2" align="right" style="font-weight:bolder">GRAND TOTAL</td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_saldo_awal_usd), 2); ?></td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_saldo_awal_idr), 2); ?></td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_pembelian_usd), 2); ?></td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_pembelian_idr), 2); ?></td>
    <td style="font-weight:bolder"><?= print_number(0, 2); ?></td>
    <td style="font-weight:bolder"><?= print_number(0, 2); ?></td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_payment_usd), 2); ?></td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_payment_idr), 2); ?></td>
    <td style="font-weight:bolder"><?= print_number(0, 2); ?></td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_saldo_akhir_usd), 2); ?></td>
    <td style="font-weight:bolder"><?= print_number(array_sum($grand_saldo_akhir_idr), 2); ?></td>
</tr>
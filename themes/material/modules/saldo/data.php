<?php 
    $no = 1; 
    $saldo_awal = $saldo_awal['saldo_awal'];
    $total_debet = array();
    $total_kredit = array();
?>
<table width="100%">
    <tr>
        <th width="50%">Saldo Akhir per Tanggal <?= print_date($tanggal_saldo_akhir,'d/m/Y'); ?></th>
        <th width="50%" style="text-align:right;"><?= print_number($saldo_akhir['saldo_akhir'],2)?></th>
    </tr>
</table>
<table class="table table-bordered table-nowrap" id="table-document">
    <thead>
        <tr>
            <th>Date</th>
            <th>Document Number</th>
            <th>Name</th>
            <th>Description</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Saldo</th>
        </tr>
                                                    
    </thead>
    <tbody>
        <tr>
            <td>
                <?= print_date($tanggal_saldo_awal,'d/m/Y'); ?>
            </td>
            <td>
                
            </td>
            <td>
                
            </td>
            <td>
                <?= print_string('Opening Balance'); ?>
            </td>
            <td>
                <?= print_number($saldo_awal,2); ?>
            </td>
            <td>
                <?= print_number(0,2); ?>
            </td>
            <td>
                <?= print_number(0); ?>
            </td>
        </tr> 
        <?php foreach ($items as $i => $item) : ?>
            <?php 
                $saldo_awal = $saldo_awal+$item['trs_debet']-$item['trs_kredit'];
                $total_debet[] = $item['trs_debet'];
                $total_kredit[] = $item['trs_kredit'];
            ?>
            <tr>
                <td>
                    <?= print_date($item['tanggal_jurnal'],'d/m/Y'); ?>
                </td>
                <td>
                    <?= print_string($item['no_jurnal']); ?>
                </td>
                <td>
                    <?= print_string($item['vendor']); ?>
                </td>
                <td>
                    <?= print_string($item['keterangan']); ?>
                </td>
                <td>
                    <?= print_number($item['trs_debet'],2); ?>
                </td>
                <td>
                    <?= print_number($item['trs_kredit'],2); ?>
                </td>
                <td>
                    <?= print_number($saldo_awal,2); ?>
                </td>
            </tr>   

        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4">Total</th>
            <th><?= print_number(array_sum($total_debet),2); ?></th>
            <th><?= print_number(array_sum($total_kredit),2); ?></th>
            <th><?= print_number($saldo_awal,2); ?></th>
        </tr>
    </tfoot>
</table>

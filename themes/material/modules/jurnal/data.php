<div class="newoverlay" id="loadingScreen2" style="display: none;">
    <i class="fa fa-refresh fa-spin"></i>
</div>
<table class="table table-hover table-bordered" id="table-document">
    <thead>
        <tr>
            <th width="5%"></th>
            <th width="10%">Date</th>
            <th width="15%">Document Number</th>
            <th width="40%">Account</th>
            <th width="15%">Debit</th>
            <th width="15%">Credit</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($entity as $i => $jurnal) : ?>
        <tr>
            <td></td>
            <td><?= print_date($jurnal['tanggal_jurnal'],'d/m/Y'); ?></td>
            <td colspan="4"><?= print_string($jurnal['keterangan']); ?></td>
            
        </tr>
        <?php foreach ($jurnal['items'] as $j => $item) : ?>
        <tr>
            <td></td>
            <td colspan="2" style="text-align:right;"><?= print_string($jurnal['no_jurnal']); ?></td>
            <td><?= print_string($item['kode_rekening']); ?> <?= print_string($item['jenis_transaksi']); ?></td>
            <td><?= print_number($item['trs_debet'],2); ?></td>
            <td><?= print_number($item['trs_kredit'],2); ?></td>
        </tr>
        <?php endforeach;?>
        <?php endforeach;?>
    </tbody>
</table>
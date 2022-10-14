<table>
    <tr>
        <td>Periode : <?= $periode; ?></td>
    </tr>
    <tr>
        <td>Vendor : <?= strtoupper($vendor); ?></td>
    </tr>
</table>
<table class="tg table-nowrap" id="table-document">
    <thead>
        <tr>
            <th class="middle-alignment">No</th>
            <th class="middle-alignment">PO#</th>
            <th class="middle-alignment">No Nota</th>
            <th class="middle-alignment">Date</th>
            <th class="middle-alignment">Amount PO</th>
            <th class="middle-alignment">Amount Nota</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1; 
            $total_po = array();
            $total_nota = array();
        ?>
        <?php foreach ($entities as $i => $entity) : ?>
        <tr>
            <td style="font-weight:bold;"><?= $no++;?></td>
            <td style="font-weight:bold;"><?= print_string($entity['document_number']);?> </td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"><?= print_number($entity['grand_total'],2);?></td>
            <td style="font-weight:bold;"></td>
        </tr>
        <?php foreach ($entity['receipt'] as $r => $receipt) : ?>
        <tr>
            <td></td>
            <td></td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= print_string($receipt['reference_number']);?></td>
            <td>
                <?= ($receipt['tgl_nota']!=null)? print_date($receipt['tgl_nota']):'';?>
            </td>
            <td></td>
            <td><?= print_number($receipt['received_total_value_idr'],2);?></td>
        </tr>
        <?php  
            $total_nota[]   = $receipt['received_total_value_idr'];
        ?>
        <?php endforeach; ?>
        <?php  
            $total_po[] = $entity['grand_total'];
        ?>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <th></th>
            <th></th>
            <th></th>
            <th><?= print_number(array_sum($total_po), 2); ?></th>
            <th><?= print_number(array_sum($total_nota), 2); ?></th>
        </tr>
    </tfoot>
</table>  

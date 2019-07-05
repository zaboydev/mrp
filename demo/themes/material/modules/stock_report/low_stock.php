<?php
/**
 * @var $entities array
 * @var $auth_role
 */
?>
<table>
    <thead>
    <tr>
        <th>No.</th>
        <th>Group</th>
        <th>Description</th>
        <th>Part Number</th>
        <th>Alternate P/N</th>
        <th>Serial Number</th>
        <th>Qty</th>
        <th>Unit</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 0;?>
    <?php foreach ($entities as $entity):?>
        <?php if ($entity['total'] <= $entity['minimum_stock']):?>
            <?php $no++;?>
            <tr>
                <td align="right"><?=$no;?></td>
                <td><?=$entity['group'];?></td>
                <td><?=$entity['description'];?></td>
                <td><?=$entity['part_number'];?></td>
                <td><?=$entity['alternate_part_number'];?></td>
                <td><?=$entity['serial_number'];?></td>
                <td align="right"><?=number_format($entity['total'],2);?></td>
                <td><?=$entity['unit_measurement'];?></td>
            </tr>
        <?php endif;?>
    <?php endforeach;?>
    </tbody>
    <tfoot>
    <tr>
        <th></th>
        <th colspan="5">Total</th>
        <th align="right"><?=number_format($total_quantity,2);?></th>
        <th></th>
    </tr>
    </tfoot>
</table>

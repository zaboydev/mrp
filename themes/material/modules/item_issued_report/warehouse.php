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
        <th>Description</th>
        <th>Part Number</th>
        <th>Serial Number</th>
        <th>Aircraft</th>
        <th>Qty</th>
        <th>Base</th>
        <th>Stores</th>
        <th>notes</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 0;?>
    <?php foreach ($entities as $entity):?>
        <?php $no++;?>
        <tr>
            <td align="right"><?=$no;?></td>
            <td><?=$entity['item_description'];?></td>
            <td><?=$entity['part_number_issued'];?></td>
            <td><?=$entity['item_serial_issued'];?></td>
            <td><?=$entity['aircraft'];?></td>
            <td align="right"><?=number_format($entity['quantity_issued'],2);?></td>
            <td><?=$entity['warehouse'];?></td>
            <td><?=$entity['stores_issued'];?></td>
            <td><?=$entity['notes'];?></td>
        </tr>
    <?php endforeach;?>
    </tbody>
    <tfoot>
    <tr>
        <th></th>
        <th colspan="4">Total</th>
        <th align="right"><?=number_format($total_quantity,2);?></th>
        <th colspan="3"></th>
    </tr>
    </tfoot>
</table>

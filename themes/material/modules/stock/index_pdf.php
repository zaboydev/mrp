<?php
/**
 * @var $entities array
 * @var $auth_role
 */
?>
<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Group</th>
        <th>Description</th>
        <th>Part Number</th>
        <th>Alt. P/N</th>
        <th>Serial Number</th>
        <th>Model</th>
        <th>Warehouse</th>
        <th>Stores</th>
        <th>Qty</th>
        <th>Unit</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1;?>
    <?php foreach ($entities as $entity):?>
        <tr>
            <td><?=$no;?></td>
            <td><?=$entity['group'];?></td>
            <td><?=$entity['description'];?></td>
            <td><?=$entity['part_number'];?></td>
            <td><?=$entity['alternate_part_number'];?></td>
            <td><?=$entity['serial_number'];?></td>
            <td><?=implode(';', $entity['aircraft_types']);?></td>
            <td><?=$entity['warehouse'];?></td>
            <td><?=$entity['stores'];?></td>
            <td><?=number_format($entity['quantity']);?></td>
            <td><?=$entity['unit'];?></td>
        </tr>
        <?php $no++;?>
    <?php endforeach;?>
    </tbody>
</table>

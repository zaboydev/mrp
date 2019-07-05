<?php
/**
 * @var $entities array
 * @var $auth_role
 * @var $countries
 */
?>

<table id="table-data" data-leftColumnsFixed="2" data-pageOrientation="landscape" data-order="[[ 1, &quot;desc&quot; ]]">
    <thead>
    <tr>
        <th></th>
        <th>PO#</th>
        <th>Date</th>
        <th>POE</th>
        <th>Vendor</th>
        <th>Notes</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($entities as $entity):?>
        <tr data-id="<?=$entity->id;?>">
            <td></td>
            <td><?=$entity->po_number;?></td>
            <td><?=$entity->po_date;?></td>
            <td><?=$entity->reference_poe;?></td>
            <td><?=$entity->vendor_name;?></td>
            <td><?=$entity->notes;?></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

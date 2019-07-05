<?php
/**
 * @var $entities array
 * @var $auth_role
 */
?>
<table autosize="1">
    <thead>
    <tr>
        <th>Name</th>
        <th>Code</th>
        <th>Address</th>
        <th>Country</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Contact</th>
        <th>Notes</th>
        <th>Last Update</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($entities as $entity):?>
        <tr>
            <td><?=$entity->vendor_name;?></td>
            <td><?=$entity->vendor_code;?></td>
            <td><?=$entity->address;?></td>
            <td><?=$entity->country;?></td>
            <td><?=$entity->phone;?></td>
            <td><?=$entity->email;?></td>
            <td><?=$entity->contact;?></td>
            <td><?=$entity->notes;?></td>
            <td><?=$entity->updated_at;?></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

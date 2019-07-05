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
        <th>Vendor</th>
        <th>Address</th>
        <th>Email</th>
        <th>Phone</th>
        <th>notes</th>
        <th>Last Update</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1;?>
    <?php foreach ($entities as $entity):?>
        <tr>
            <td><?=$no;?></td>
            <td><?=$entity['vendor'];?></td>
            <td><?=$entity['address'];?></td>
            <td><?=$entity['email'];?></td>
            <td><?=$entity['phone'];?></td>
            <td><?=$entity['notes'];?></td>
            <td><?=$entity['updated_at'];?></td>
        </tr>
        <?php $no++;?>
    <?php endforeach;?>
    </tbody>
</table>

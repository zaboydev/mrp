<form action="<?=current_url();?>" method="POST">
    <label>Select Items</label>

    <table id="table-data" data-order="[[ 1, &quot;desc&quot; ]]">
        <thead>
        <tr>
            <th data-orderable="false"></th>
            <th>Vendor</th>
            <th>Address</th>
            <th>Phone</th>
            <th>Email</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($entities as $entity):?>
            <tr class="no-space">
                <td>
                    <input type="radio" name="vendor" id="vendor[<?=$entity->vendor_id;?>]" value="<?=$entity->vendor_id;?>" required>
                </td>
                <td><label for="vendor[<?=$entity->vendor_id;?>]"><?=$entity->vendor_name;?></label></td>
                <td><label for="vendor[<?=$entity->vendor_id;?>]"><?=$entity->address;?></label></td>
                <td><?=$entity->phone;?></td>
                <td><?=$entity->email;?></td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>

    <div class="box-footer">
        <input type="submit" name="continue" value="Continue" class="btn btn-primary">

        <?php
        if (is_granted($acl[$module]['index']))
            echo anchor(site_url($module['route'] .'/discard/', LINK_PROTOCOL), 'Discard', 'class="btn btn-default"');
        ?>
    </div>
</form>

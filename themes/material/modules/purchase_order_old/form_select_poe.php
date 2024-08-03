<form action="<?=current_url();?>" method="POST">
    <label>Select Purchase Order Evaluation (POE)</label>
    
    <table id="table-data" data-order="[[ 1, &quot;desc&quot; ]]">
        <thead>
        <tr>
            <th data-orderable="false"></th>
            <th>POE#</th>
            <th>Date</th>
            <th>Reference</th>
            <th>Notes</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($entities as $entity):?>
            <tr class="no-space">
                <td>
                    <input type="radio" name="reference_poe" id="reference_poe[<?=$entity->poe_no;?>]" value="<?=$entity->poe_no;?>" required>
                </td>
                <td><label for="reference_poe[<?=$entity->poe_no;?>]"><?=$entity->poe_no;?></label></td>
                <td><?=$entity->poe_date;?></td>
                <td><?=$entity->reference;?></td>
                <td><?=$entity->notes;?></td>
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

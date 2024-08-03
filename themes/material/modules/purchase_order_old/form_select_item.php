<form action="<?=current_url();?>" method="POST">
    <label>Select Items</label>
    
    <table id="table-data" data-order="[[ 1, &quot;desc&quot; ]]">
        <thead>
        <tr>
            <th></th>
            <th>Item</th>
            <th>Part#</th>
            <th>Unit</th>
            <th>Price</th>
            <th>Balance</th>
            <th>Left Qty</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($entities as $entity):?>
            <?php if ($entity->ytd_quantity > 0):?>
            <tr class="no-space">
                <td>
                    <input type="checkbox" name="monthly_budget_id[<?=$entity->id;?>]" id="monthly_budget_id[<?=$entity->id;?>]" value="<?=$entity->id;?>">
                    <input type="hidden" name="detail[<?=$entity->id;?>][inventory_monthly_budget_id]" value="<?=$entity->id;?>">
                    <input type="hidden" name="detail[<?=$entity->id;?>][additional_info]" value="">
                    <input type="hidden" name="detail[<?=$entity->id;?>][part_number]" value="<?=$entity->part_number;?>">
                    <input type="hidden" name="detail[<?=$entity->id;?>][unit]" value="<?=$entity->measurement_symbol;?>">
                    <input type="hidden" name="detail[<?=$entity->id;?>][price]" value="<?=$entity->current_price;?>">
                    <input type="hidden" name="detail[<?=$entity->id;?>][quantity]" value="1">
                    <input type="hidden" name="detail[<?=$entity->id;?>][total]" value="<?=$entity->current_price;?>">
                </td>
                <td><label for="monthly_budget_id[<?=$entity->id;?>]"><?=$entity->product_name;?></label></td>
                <td><label for="monthly_budget_id[<?=$entity->id;?>]"><?=$entity->part_number;?></label></td>
                <td><?=$entity->measurement_symbol;?></td>
                <td><?=number_format($entity->current_price, 2);?></td>
                <td><?=number_format($entity->ytd_budget, 2);?></td>
                <td><?=number_format($entity->ytd_quantity, 2);?></td>
            </tr>
            <?php endif;?>
        <?php endforeach;?>
        </tbody>
        <tfoot>
        <tr>
            <th></th>
            <th>Item</th>
            <th>Part#</th>
            <th>Unit</th>
            <th>Price</th>
            <th>Balance</th>
            <th>Left Qty</th>
        </tr>
        </tfoot>
    </table>

    <div class="box-footer">
        <input type="hidden" name="id" value="<?=$_SESSION['pr']['id'];?>">
        <input type="submit" name="continue" value="Continue" class="btn btn-primary">

        <?php
        if (is_granted($acl[$module]['index']))
            echo anchor(site_url($module['route'] .'/discard/', LINK_PROTOCOL), 'Discard', 'class="btn btn-default"');
        ?>
    </div>
</form>

<form action="<?=current_url();?>" method="POST" id="form_update_item">
    <label>Update Items</label>

    <table>
        <tr>
            <th>Ref</th>
            <td><input type="text" name="reference" class="form-control" value="<?=(isset($_SESSION['poe']['reference'])) ? $_SESSION['poe']['reference'] : '';?>"></td>
        </tr>
        <tr>
            <th>Notes</th>
            <td><input type="text" name="notes" class="form-control" value="<?=(isset($_SESSION['poe']['notes'])) ? $_SESSION['poe']['notes'] : '';?>"></td>
        </tr>
    </table>
    
    <table id="table-data" data-order="[[ 5, &quot;asc&quot; ]]">
        <thead>
        <tr>
            <th rowspan="2"></th>
            <th rowspan="2">Item</th>
            <th rowspan="2">Part#</th>
            <th rowspan="2">Alt. Part#</th>
            <th rowspan="2">Qty</th>
            <th rowspan="2">PR#</th>

            <?php foreach ($_SESSION['poe']['vendor'] as $key => $value):?>
                <th colspan="2"><?=$value['vendor_name'];?></th>
            <?php endforeach;?>

            <th rowspan="2">notes</th>
        </tr>
        <tr>
            <?php foreach ($_SESSION['poe']['vendor'] as $value):?>
                <th>Unit Price</th>
                <th>Core Charge</th>
            <?php endforeach;?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($_SESSION['poe']['request'] as $key => $request):?>
            <?php foreach ($request['item'] as $k => $item):?>
            <tr>
                <td class="no-space">
                    <a href="<?=site_url($module['route'] .'/delete_item/'. $key);?>" class="text-danger">
                        <i class="fa fa-trash-o"></i>
                    </a>
                </td>
                <td>
                    <?=$item['item_name'];?>
                </td>
                <td>
                    <?=$item['item_part_number'];?>
                </td>
                <td>
                    <input type="text" name="request[<?=$key;?>][item][<?=$k;?>][item_alternate_part_number]" class="form-control" value="<?=$item['item_alternate_part_number'];?>">
                </td>
                <td>
                    <?=number_format($item['item_quantity'], 2);?>
                </td>
                <td>
                    <?=$request['pr_number'];?>
                </td>

                <?php foreach ($_SESSION['poe']['vendor'] as $v => $vendor):?>
                    <td>
                        <input type="hidden" name="request[<?=$key;?>][item][<?=$k;?>][vendor][<?=$v;?>][vendor_id]" value="<?=$vendor['vendor_id'];?>">
                        <input type="text" name="request[<?=$key;?>][item][<?=$k;?>][vendor][<?=$v;?>][unit_price]" class="form-control" value="<?=(isset($item['vendor'][$v]['unit_price'])) ? $item['vendor'][$v]['unit_price'] : 0;?>">
                    </td>
                    <td>
                        <input type="text" name="request[<?=$key;?>][item][<?=$k;?>][vendor][<?=$v;?>][core_charge]" class="form-control" value="<?=(isset($item['vendor'][$v]['core_charge'])) ? $item['vendor'][$v]['core_charge'] : 0;?>">
                    </td>
                <?php endforeach;?>

                <td>
                    <input type="text" name="request[<?=$key;?>][item][<?=$k;?>][notes]" class="form-control" value="<?=$item['notes'];?>">
                </td>
            </tr>
            <?php endforeach;?>
        <?php endforeach;?>
        </tbody>
    </table>

    <div class="box-footer">
        <a href="<?=site_url($module['route'] .'/select_vendor');?>" class="btn btn-danger">Add Vendor</a>

        <input type="hidden" name="id" value="<?=(isset($_SESSION['poe']['poe_no'])) ? $_SESSION['poe']['poe_no'] : null;?>">
        <input type="submit" name="continue" value="Continue" class="btn btn-primary">

        <?php
        if (is_granted($acl[$module]['index']))
            echo anchor(site_url($module['route'] .'/discard/', LINK_PROTOCOL), 'Discard', 'class="btn btn-default"');
        ?>
    </div>
</form>

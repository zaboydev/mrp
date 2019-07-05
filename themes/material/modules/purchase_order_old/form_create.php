<form action="<?=current_url();?>" method="POST" id="form_update_item">
    <label>Finishing Purchase Order Evaluation</label>

    <table>
        <tr>
            <th>Date</th>
            <td><?=(isset($_SESSION['po']['po_date'])) ? date('F d, Y') : '';?></td>
        </tr>
        <tr>
            <th>Reference POE</th>
            <td>POE#<?=$_SESSION['po']['reference_poe'];?>
                <input type="hidden" name="reference_poe" value="<?=$_SESSION['po']['reference_poe'];?>">
            </td>
        </tr>
        <tr>
            <th>PO No.</th>
            <td><input type="text" name="po_number" class="form-control" value="<?=(isset($_SESSION['po']['po_number'])) ? $_SESSION['po']['po_number'] : '';?>" required></td>
        </tr>
        <tr>
            <th>Reference Quotation</th>
            <td><input type="text" name="reference_quotation" class="form-control" value="<?=(isset($_SESSION['po']['reference_quotation'])) ? $_SESSION['po']['reference_quotation'] : '';?>"></td>
        </tr>
        <tr>
            <th>Vendor (to)</th>
            <td>
                <textarea name="vendor_address" class="form-control"><?=(isset($_SESSION['po']['vendor_address'])) ? $_SESSION['po']['vendor_address'] : '';?></textarea>
                <input type="hidden" name="vendor_name" value="<?=$_SESSION['po']['vendor_name'];?>">
                <input type="hidden" name="vendor_id" value="<?=$_SESSION['po']['vendor_id'];?>">
            </td>
        </tr>
        <tr>
            <th>Bill to</th>
            <td>
                <textarea name="bill_to" class="form-control"><?=(isset($_SESSION['po']['bill_to'])) ? $_SESSION['po']['bill_to'] : config_item('bill_to');?></textarea>
            </td>
        </tr>
        <tr>
            <th>Delivery to</th>
            <td>
                <textarea name="delivery_to" class="form-control"><?=(isset($_SESSION['po']['delivery_to'])) ? $_SESSION['po']['delivery_to'] : config_item('delivery_to');?></textarea>
            </td>
        </tr>
    </table>
    
    <table id="table-data" data-order="[[ 5, &quot;asc&quot; ]]">
        <thead>
        <tr>
            <th>Item</th>
            <th>Part#</th>
            <th>Alt. Part#</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Core Charge</th>
            <th>Total Amount</th>
            <th>Notes</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($_SESSION['po']['item'] as $i => $item):?>
            <?php $total = ($item['unit_price'] * $item['quantity']) + ($item['core_charge'] * $item['quantity']);?>
            <tr>
                <td>
                    <?=$item['item_name'];?>
                    <input type="hidden" name="item[<?=$i;?>][item_name]" value="<?=(isset($item['item_name'])) ? $item['item_name'] : '';?>">
                    <input type="hidden" name="item[<?=$i;?>][imb_id]" value="<?=(isset($item['imb_id'])) ? $item['imb_id'] : '';?>">
                </td>
                <td>
                    <?=$item['part_number'];?>
                    <input type="hidden" name="item[<?=$i;?>][part_number]" value="<?=(isset($item['part_number'])) ? $item['part_number'] : '';?>">
                </td>
                <td>
                    <?=$item['alternate_part_number'];?>
                    <input type="hidden" name="item[<?=$i;?>][alternate_part_number]" value="<?=(isset($item['alternate_part_number'])) ? $item['alternate_part_number'] : '';?>">
                </td>
                <td>
                    <?=number_format($item['quantity'], 2);?>
                    <input type="hidden" name="item[<?=$i;?>][quantity]" value="<?=(isset($item['quantity'])) ? $item['quantity'] : 0;?>">
                </td>
                <td>
                    <?=number_format($item['unit_price'], 2);?>
                    <input type="hidden" name="item[<?=$i;?>][unit_price]" value="<?=(isset($item['unit_price'])) ? $item['unit_price'] : 0;?>">
                </td>
                <td>
                    <?=number_format($item['core_charge'], 2);?>
                    <input type="hidden" name="item[<?=$i;?>][core_charge]" value="<?=(isset($item['core_charge'])) ? $item['core_charge'] : 0;?>">
                </td>
                <td>
                    <?=number_format($total, 2);?>
                </td>

                <td>
                    <input type="text" name="item[<?=$i;?>][notes]" class="form-control" value="<?=(isset($item['notes'])) ? $item['notes'] : '';?>">
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>

    <div class="box-footer">
        <a href="<?=site_url($module['route'] .'/select_vendor');?>" class="btn btn-danger">Change Vendor</a>
        <a href="<?=site_url($module['route'] .'/select_poe');?>" class="btn btn-danger">Change POE</a>

        <input type="submit" name="save" value="Finish" class="btn btn-primary">

        <?php
        if (is_granted($acl[$module]['index']))
            echo anchor(site_url($module['route'] .'/discard/', LINK_PROTOCOL), 'Discard', 'class="btn btn-default"');
        ?>
    </div>
</form>

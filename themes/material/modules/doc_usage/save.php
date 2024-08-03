<?php if (isset($_SESSION['sd']['items']) == FALSE or $_SESSION['sd']['items'] == null):?>
    <p class="lead text-center">
        <a href="<?=site_url($module['route'] .'/add_item/?source=stores');?>" class="text-muted">
            Add item from stores
        </a>
        |
        <a href="<?=site_url($module['route'] .'/add_item/?source=delivery');?>" class="text-muted">
            Add item from int. delivery
        </a>
    </p>
    <p class="lead text-center">
        <a href="<?=site_url($module['route'] .'/discard');?>" class="text-muted">
            <i class="fa fa-remove"></i>
            Discard
        </a>
    </p>
<?php else:?>
<form action="<?=current_url();?>" method="POST" id="form_edit">
    <table>
        <tr>
            <th>Document No.</th>
            <td>
                <input type="text" name="document_number" value="<?=$_SESSION['sd']['document_number'];?>" class="form-control" required>
            </td>
        </tr>
        <tr>
            <th>Document Date</th>
            <td>
                <input type="text" name="document_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-end-date="0d" value="<?=$_SESSION['sd']['document_date'];?>" class="form-control">
            </td>
        </tr>
        <tr>
            <th>Sent by</th>
            <td>
                <input type="text" name="sent_by" value="<?=$_SESSION['sd']['sent_by'];?>" class="form-control" required>
            </td>
        </tr>
        <tr>
            <th>Released by</th>
            <td>
                <input type="text" name="released_by" value="<?=$_SESSION['sd']['released_by'];?>" class="form-control">
            </td>
        </tr>
        <tr>
            <th>Origin Company</th>
            <td>
                <input type="text" name="origin_company" value="<?=$_SESSION['sd']['origin_company'];?>" class="form-control" required>
            </td>
        </tr>
        <tr>
            <th>Origin Address</th>
            <td>
                <textarea name="origin_address" id="origin_address" class="form-control" required><?=$_SESSION['sd']['origin_address'];?></textarea>
            </td>
        </tr>
        <tr>
            <th>Destination Base</th>
            <td>
                <select name="warehouse_options" id="warehouse_options" rel="warehouse_options" class="form-control" required>
                    <option value="">-- Select one --</option>
                    <?php foreach ($warehouses as $warehouse):?>
                        <option value="<?=$warehouse['warehouse'];?>" description="<?=$warehouse['description'];?>" <?=($_SESSION['sd']['destination_warehouse'] === $warehouse['warehouse']) ? 'selected' : '';?>>
                            <?=$warehouse['warehouse'];?>
                        </option>
                    <?php endforeach;?>
                </select>
                <input type="text" name="destination_warehouse" id="destination_warehouse" value="<?=$_SESSION['sd']['destination_warehouse'];?>">
            </td>
        </tr>
        <tr>
            <th>Destination Company</th>
            <td>
                <input type="text" name="destination_company" value="<?=$_SESSION['sd']['destination_company'];?>" class="form-control" required>
            </td>
        </tr>
        <tr>
            <th>Destination Address</th>
            <td>
                <textarea name="destination_address" id="destination_address" class="form-control" required><?=$_SESSION['sd']['destination_address'];?></textarea>
            </td>
        </tr>
        <tr>
            <th>Received by</th>
            <td>
                <input type="text" name="received_by" value="<?=$_SESSION['sd']['received_by'];?>" class="form-control" readonly>
            </td>
        </tr>
        <tr>
            <th>Received Date</th>
            <td>
                <input type="text" name="received_date" value="<?=$_SESSION['sd']['received_date'];?>" class="form-control" readonly>
            </td>
        </tr>
    </table>
    
    <table id="table-data" data-order="[[ 2, &quot;asc&quot; ],[ 3, &quot;asc&quot; ]]">
        <thead>
        <tr>
            <th data-sortable="false">Edit</th>
            <th data-sortable="false">Del</th>
            <th>Description</th>
            <th>P/N</th>
            <th>S/N</th>
            <th>Cond.</th>
            <th>Qty</th>
            <th>Unit Value</th>
            <th>Total Value</th>
            <th>notes</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($_SESSION['sd']['items'] as $i => $items):?>
            <tr>
                <td width="1">
                    <a href="<?=site_url($module['route'] .'/edit_item/'. $i);?>">
                        <i class="fa fa-edit"></i>
                    </a>
                </td>
                <td width="1">
                    <a href="<?=site_url($module['route'] .'/delete_item/'. $i);?>">
                        <i class="fa fa-trash"></i>
                    </a>
                </td>
                <td>
                    <?=$items['description'];?>
                    <input type="hidden" name="items[<?=$i;?>][description]" value="<?=(isset($items['description'])) ? $items['description'] : '';?>">
                    <input type="hidden" name="items[<?=$i;?>][reference_document]" value="<?=(isset($items['reference_document'])) ? $items['reference_document'] : '';?>">
                    <input type="hidden" name="items[<?=$i;?>][reference_number]" value="<?=(isset($items['reference_number'])) ? $items['reference_number'] : '';?>">
                </td>
                <td class="no-space">
                    <?=$items['part_number'];?>
                    <input type="hidden" name="items[<?=$i;?>][part_number]" value="<?=(isset($items['part_number'])) ? $items['part_number'] : '';?>">
                </td>
                <td>
                    <?=$items['serial_number'];?>
                    <input type="hidden" name="items[<?=$i;?>][item_serial]" value="<?=(isset($items['serial_number'])) ? $items['serial_number'] : '';?>">
                </td>
                <td>
                    <?=$items['condition'];?>
                    <input type="hidden" name="items[<?=$i;?>][condition]" value="<?=(isset($items['condition'])) ? $items['condition'] : '';?>">
                </td>
                <td>
                    <?=number_format($items['quantity'], 2);?>
                    <input type="hidden" name="items[<?=$i;?>][quantity]" value="<?=(isset($items['quantity'])) ? $items['quantity'] : 0;?>">
                </td>
                <td>
                    <?=number_format($items['unit_value'], 2);?>
                    <input type="hidden" name="items[<?=$i;?>][unit_value]" value="<?=(isset($items['unit_value'])) ? $items['unit_value'] : 0;?>">
                </td>
                <td>
                    <?=number_format($items['total_value'], 2);?>
                    <input type="hidden" name="items[<?=$i;?>][total_value]" value="<?=(isset($items['total_value'])) ? $items['total_value'] : 0;?>">
                </td>
                <td>
                    <?=$items['notes'];?>
                    <input type="hidden" name="items[<?=$i;?>][notes]" value="<?=(isset($items['notes'])) ? $items['notes'] : '';?>">
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>

    <div class="box-footer">
        <a href="<?=site_url($module['route'] .'/add_item/?source=stores');?>" class="btn btn-info">Add Item from Stores</a>
        <a href="<?=site_url($module['route'] .'/add_item/?source=delivery');?>" class="btn btn-info">Add Item from Int. Delivery</a>

        <input type="hidden" name="id" value="<?=$_SESSION['sd']['id'];?>" class="btn btn-primary">
        <input type="submit" name="save" value="Save DP" class="btn btn-primary">

        <?php
        echo anchor(site_url($module['route'] .'/discard/', LINK_PROTOCOL), 'Discard DP', 'class="btn btn-danger pull-right"');
        ?>
    </div>
</form>
<?php endif;?>

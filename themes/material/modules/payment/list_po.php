<?php $no = 1;
$no_item = 1; ?>
<?php foreach ($po as $detail) : ?>
    <tr id="row_<?= $no ?>">
        <td><input id="sel_<?= $no ?>" value="<?= $detail['id'] ?>" type="hidden"><?= print_string($detail['document_number']) ?></td>
        <td><?= print_string($detail['status']) ?></td>
        <td><?= print_date($detail['due_date']) ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><?= print_number($detail['grand_total'], 2) ?></td>
        <td><?= print_number($detail['payment'], 2) ?></td>
        <td><input id="sis_<?= $no ?>" value="<?= $detail['remaining_payment'] ?>" type="hidden"><?= print_number($detail['remaining_payment'], 2) ?></td>
        <td><input id="in_<?= $no ?>" data-row="<?= $no ?>" type="number" class="sel_applied" value="0"></td>
        <td><button type="button" class="btn btn-xs btn-info btn-sm"><i class="fa fa-eye btn_view_detail" id="btn_<? $no ?>" data-row="<?= $no ?>" data-tipe="view"></i></button></td>
    </tr>
    <div id="list_detail_po">
        <?php foreach ($po['items'][$detail['id']] as $i => $detail_po) : ?>
            <tr id="row_item_<?= $no_item ?>" class="hide detail_<?= $no ?>">
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input id="sel_item_<?= $no ?>_<?= $no_item ?>" value="<?= $detail_po['id'] ?>" type="hidden"><input id="sel_item_2_<?= $no ?>_<?= $no_item ?>" value="<?= $detail_po['purchase_order_id'] ?>" type="hidden"><?= print_string($detail_po['part_number']) ?></td>
                <td><?= print_string($detail_po['description']) ?><input id="desc_item_<?= $no ?>_<?= $no_item ?>" value="<?= $detail_po['description'] ?>" type="hidden"></td>
                <td><?= print_date($detail_po['due_date']) ?></td>
                <td><?= print_number($detail_po['quantity_received'], 2) ?></td>
                <td><?= print_number($detail_po['quantity_received'] * ($detail_po['unit_price'] + $detail_po['core_charge']), 2) ?></td>
                <td><?= print_number($detail_po['total_amount'], 2) ?></td>
                <td><?= print_number($detail_po['total_amount'] - $detail_po['left_paid_amount'], 2) ?></td>
                <td><input id="sis_item_<?= $no ?>_<?= $no_item ?>" value="<?= $detail_po['left_paid_amount'] ?>" type="hidden"><?= print_number($detail_po['left_paid_amount'], 2) ?></td>
                <td><input id="in_item_<?= $no ?>_<?= $no_item ?>" data-parent="<?= $no ?>" data-row="<?= $no_item ?>" type="number" class="sel_applied_item sel_applied_<?= $no ?>" value="0"></td>
                <td></td>
                <?php $no_item++; ?>
            </tr>
        <?php endforeach; ?>
        <?php if ($detail['additional_price_remaining'] > 0) : ?>
            <tr id="row_item_<?= $no_item ?>" class="hide detail_<?= $no ?>">
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input id="sel_item_<?= $no ?>_<?= $no_item ?>" value="0" type="hidden"><input id="sel_item_2_<?= $no ?>_<?= $no_item ?>" value="<?= $detail['id'] ?>" type="hidden">Additional Price</td>
                <td>Additional Price (PPN, DISC, SHIPPING COST)<input id="desc_item_<?= $no ?>_<?= $no_item ?>" value="Additional Price (PPN, DISC, SHIPPING COST)" type="hidden"></td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td><?= print_number($detail['additional_price'], 2) ?></td>
                <td><?= print_number($detail['additional_price'] - $detail['additional_price_remaining'], 2) ?></td>
                <td><input id="sis_item_<?= $no ?>_<?= $no_item ?>" value="<?= $detail['additional_price_remaining'] ?>" type="hidden"><?= print_number($detail['additional_price_remaining'], 2) ?></td>
                <td><input id="in_item_<?= $no ?>_<?= $no_item ?>" data-parent="<?= $no ?>" data-row="<?= $no_item ?>" type="number" class="sel_applied_item sel_applied_<?= $no ?>" value="0"></td>
                <td></td>
                <?php $no_item++; ?>
            </tr>
        <?php endif; ?>
    </div>
    <?php $no++; ?>
<?php endforeach; ?>
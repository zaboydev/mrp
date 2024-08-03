<?php $no = 1;
$no_item = 1; ?>
<?php foreach ($po as $detail) : ?>
    <tr id="row_<?= $no ?>">
        <td><?= $no ?></td>
        <td><input id="sel_<?= $no ?>" value="<?= $detail['id'] ?>" type="hidden"><?= print_string($detail['document_number']) ?></td>
        <td><?= print_string($detail['status']) ?></td>
        <td><?= print_date($detail['due_date'],'d/m/Y') ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><?= print_number($detail['grand_total'], 2) ?></td>
        <td><?= print_number($detail['payment'], 2) ?></td>
        <td><input id="sis_<?= $no ?>" value="<?= $detail['remaining_payment_request'] ?>" type="hidden"><?= print_number($detail['remaining_payment_request'], 2) ?></td>
        <td></td>
        <td><input id="in_<?= $no ?>" data-row="<?= $no ?>" type="number" class="sel_applied form-control-payment" value="0"></td>
        <td><button title="View Detail PO" type="button" class="btn btn-xs btn-primary btn_view_detail" id="btn_<? $no ?>" data-row="<?= $no ?>" data-tipe="view"><i class="fa fa-angle-right"></i></button></td>
        <td><a title="View Attachment PO" onClick="return popup(this, 'attachment')"  href="<?= site_url($module['route'] . '/view_manage_attachment_po/' . $detail['id'].'/'.$detail['tipe_po']); ?>" type="button" class="btn btn-xs btn-info" id="btn_attachment_<? $no ?>" data-row="<?= $no ?>" data-tipe="view"><i class="md md-attach-file"></i></a></td>
        <td></td>
    </tr>
    <div id="list_detail_po">
        <?php foreach ($detail['items'] as $i => $detail_po) : ?>
            <tr id="row_item_<?= $no_item ?>" class="hide detail_<?= $no ?>">
                <td><?=$no_item?></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input name="po_item_id[]" id="sel_item_<?= $no ?>_<?= $no_item ?>" value="<?= $detail_po['id'] ?>" type="hidden">
                    <input name="po_id[]" id="sel_item_2_<?= $no ?>_<?= $no_item ?>" value="<?= $detail_po['purchase_order_id'] ?>" type="hidden">
                    <?= print_string($detail_po['part_number']) ?>
                </td>
                <td>
                    <?= print_string($detail_po['description']) ?>
                    <input name="desc[]" id="desc_item_<?= $no ?>_<?= $no_item ?>" value="<?= $detail_po['description'] ?>" type="hidden">
                </td>
                <td>
                    <?= $detail_po['due_date'] ?>
                </td>
                <td>
                    <?= print_number($detail_po['quantity_received'], 2) ?>
                </td>
                <td>
                    <?= print_number($detail_po['quantity_received'] * ($detail_po['unit_price'] + $detail_po['core_charge']), 2) ?>
                </td>
                <td>
                    <?= print_number($detail_po['total_amount'], 2) ?>
                </td>
                <td>
                    <?= print_number($detail_po['total_amount'] - $detail_po['left_paid_request'], 2) ?>
                </td>
                <td>
                    <input id="sis_item_<?= $no ?>_<?= $no_item ?>" value="<?= $detail_po['left_paid_request'] ?>" type="hidden">
                    <?= print_number($detail_po['left_paid_request'], 2) ?>
                </td>
                <td>
                    <input name="qty_paid[]" id="in_qty_paid_<?= $no ?>_<?= $no_item ?>" data-parent="<?= $no ?>" data-row="<?= $no_item ?>" type="number" class="in_qty_paid_<?= $no ?> form-control-payment" value="<?= $detail_po['quantity']-$detail_po['quantity_paid'] ?>">
                </td>
                <td>
                    <input name="value[]" id="in_item_<?= $no ?>_<?= $no_item ?>" data-parent="<?= $no ?>" data-row="<?= $no_item ?>" type="number" class="sel_applied_item sel_applied_<?= $no ?> form-control-payment" value="0">
                </td>
                <td>
                    <input type="checkbox" id="cb_<?= $no ?>_<?= $no_item ?>" data-row="<?= $no_item ?>" data-id="<?= $no ?>_<?= $no_item ?>" name="" style="display: inline;" class="check_adj">
                </td>
                <td></td>
                <td>
                    <input name="adj_value[]" id="in_adj_<?= $no ?>_<?= $no_item ?>" data-parent="<?= $no ?>" data-row="<?= $no_item ?>" type="number" class="hide  form-control-payment sel_applied_adj sel_applied_adj<?= $no ?>" value="0" style="display: inline;">
                </td>
                <?php $no_item++; ?>
            </tr>
        <?php endforeach; ?>
        <?php if ($detail['additional_price_remaining_request'] != 0) : ?>
            <tr id="row_item_<?= $no_item ?>" class="hide detail_<?= $no ?>">
                <td><?=$no_item?></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input name="po_item_id[]" id="sel_item_<?= $no ?>_<?= $no_item ?>" value="0" type="hidden">
                    <input name="po_id[]" id="sel_item_2_<?= $no ?>_<?= $no_item ?>" value="<?= $detail['id'] ?>" type="hidden">
                    Additional Price
                </td>
                <td>
                    Additional Price (PPN, DISC, SHIPPING COST)
                    <input name="desc[]" id="desc_item_<?= $no ?>_<?= $no_item ?>" value="Additional Price (PPN, DISC, SHIPPING COST)" type="hidden">
                </td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td><?= print_number($detail['additional_price'], 2) ?></td>
                <td><?= print_number($detail['additional_price'] - $detail['additional_price_remaining_request'], 2) ?></td>
                <td>
                    <input id="sis_item_<?= $no ?>_<?= $no_item ?>" value="<?= $detail['additional_price_remaining_request'] ?>" type="hidden"><?= print_number($detail['additional_price_remaining_request'], 2) ?>
                </td>
                <td>
                    <input name="qty_paid[]" id="in_qty_paid_<?= $no ?>_<?= $no_item ?>" data-parent="<?= $no ?>" data-row="<?= $no_item ?>" type="number" class="in_qty_paid_<?= $no ?> form-control-payment" value="1" readonly>
                </td>
                <td>
                    <input name="value[]" id="in_item_<?= $no ?>_<?= $no_item ?>" data-parent="<?= $no ?>" data-row="<?= $no_item ?>" type="number" class=" form-control-payment sel_applied_item sel_applied_<?= $no ?>" value="0">
                </td>
                <td>
                    <input type="checkbox" id="cb_<?= $no ?>_<?= $no_item ?>" data-row="<?= $no_item ?>" data-id="<?= $no ?>_<?= $no_item ?>" name="" style="display: inline;" class="check_adj">
                </td>
                <td></td>
                <td>
                    <input name="adj_value[]" id="in_adj_<?= $no ?>_<?= $no_item ?>" data-parent="<?= $no ?>" data-row="<?= $no_item ?>" type="number" class="hide  form-control-payment sel_applied_adj sel_applied_adj<?= $no ?>" value="0" style="display: inline;">
                </td>
                <?php $no_item++; ?>
            </tr>
        <?php endif; ?>
    </div>
    <?php $no++; ?>
<?php endforeach; ?>
<section class="invoice">
    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header">
                PO #<?=$entity['po_number'];?>
                <small class="pull-right">Date: <?=$entity['po_date'];?></small>
            </h2>
        </div>
    </div>

    <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
            <address>
                <strong>Bill to:</strong><br>
                <?=nl2br($entity['bill_to']);?>
            </address>
        </div>
        <div class="col-sm-4 invoice-col">
            <address>
                <strong>Delivery to:</strong><br>
                <?=nl2br($entity['delivery_to']);?>
            </address>
        </div>
        <div class="col-sm-4 invoice-col">
            <address>
                <strong>To:</strong><br>
                <?=nl2br($entity['vendor_address']);?>
            </address>
        </div>
    </div>

    <p>
        Reference POE: <?=$entity['reference_poe'];?><br>
        Reference Quotation: <?=$entity['reference_quotation'];?>
    </p>

    <div class="row">
        <div class="col-xs-12">
            <table class="table table-striped" id="table-data">
                <thead>
                <tr>
                    <th>No</th>
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
                <?php $n = 0;?>
                <?php foreach ($entity['item'] as $i => $item):?>
                    <?php $n++;?>
                    <?php $total = ($item['unit_price'] * $item['quantity']) + ($item['core_charge'] * $item['quantity']);?>
                    <tr>
                        <td class="no-space">
                            <?=$n;?>
                        </td>
                        <td>
                            <?=$item['item_name'];?>
                        </td>
                        <td>
                            <?=$item['part_number'];?>
                        </td>
                        <td>
                            <?=$item['alternate_part_number'];?>
                        </td>
                        <td>
                            <?=number_format($item['quantity'], 2);?>
                        </td>
                        <td>
                            <?=number_format($item['unit_price'], 2);?>
                        </td>
                        <td>
                            <?=number_format($item['core_charge'], 2);?>
                        </td>
                        <td>
                            <?=number_format($total, 2);?>
                        </td>
                        <td>
                            <?=$item['notes'];?>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row no-print" style="margin-top: 20px;">
        <div class="col-xs-12">
            <a href="<?=site_url($module['route'] .'/print_pdf/'. $entity['id']);?>" target="_blank" class="btn btn-primary pull-right">
                <i class="fa fa-print"></i> Print
            </a>
        </div>
    </div>
</section>

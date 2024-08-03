<?= form_open(site_url($module['route'] . '/save_change_item'), array(
    'autocomplete'  => 'off',
    'id'            => 'form-edit-data',
    'class'         => 'form form-validate form-xhr ui-front',
    'role'          => 'form'
)); ?>

<div class="card style-default-bright">
    <div class="card-head style-primary-dark">
        <header>Info On Hand Stock</header>

        <div class="tools">
            <div class="btn-group">
                <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-label="Close" title="close">
                    <i class="md md-close"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12 col-lg-12">
                        <div class="form-group">
                            <input type="text" name="pr_number" id="pr_number" class="form-control" required value="<?= ($entity['pr_number']); ?>" readonly>
                            <label for="description">Document No.</label>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-12">
                        <div class="form-group">
                            <input type="text" name="description" id="description" class="form-control" autofocus required value="<?= htmlspecialchars($entity['product_name']); ?>">
                            <label for="description">Description</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="part_number" id="part_number" class="form-control" required data-validation-rule="unique" data-validation-url="<?= site_url('ajax/part_number_validation'); ?>" data-validation-exception="<?= $entity['part_number']; ?>" value="<?= $entity['part_number']; ?>">
                            <label for="part_number">Part Number</label>
                        </div>
                        <div class="form-group">
                            <input type="text" name="min_qty" id="min_qty" class="form-control" required data-validation-rule="unique" data-validation-url="<?= site_url('ajax/part_number_validation'); ?>" data-validation-exception="<?= $entity['minimum_quantity']; ?>" value="<?= $entity['minimum_quantity']; ?>">
                            <label for="part_number">Min. Qty</label>
                        </div>
                    </div>
                </div>
                <div class="row" id="document_details">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-nowrap">
                                <thead id="table_header">
                                    <th align="right" width="1">No</th>
                                    <th>Base</th>
                                    <th>On Hand Stock</th>
                                    <th></th>
                                </thead>
                                <tbody id="table_contents">
                                    <?php $n = 1;
                                    $open = 0; ?>
                                    <?php $total_qty = array(); ?>
                                    <?php if ($entity['items_count'] > 0) : ?>
                                        <?php foreach ($entity['items'] as $i => $detail) : ?>
                                            <?php $total_qty[] = $detail['on_hand_stock']; ?>
                                            <tr>
                                                <td align="right">
                                                    <?= print_number($n++); ?>
                                                </td>
                                                <td>
                                                    <?= print_string($detail['warehouse']); ?>
                                                </td>
                                                <td align="right">
                                                    <?= print_number($detail['on_hand_stock'], 2); ?>
                                                </td>
                                                <td>
                                                    <?= print_string($entity['unit']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td align="center" colspan="4">
                                                No data available
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Total</th>
                                        <th><?= print_number(array_sum($total_qty), 2); ?></th>
                                        <th><?= print_string($entity['unit']); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?= form_close(); ?>
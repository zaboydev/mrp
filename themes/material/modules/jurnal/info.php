<div class="card card-underline style-default-bright">
    <div class="card-head style-primary-dark">
        <header><?= strtoupper($module['label']); ?></header>

        <div class="tools">
            <div class="btn-group">
                <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-label="Close" title="close">
                    <i class="md md-close"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row" id="document_master">
            <div class="col-sm-12 col-md-4">
                <div class="well">
                    <div class="clearfix">
                        <div class="pull-left">DOCUMENT NO. : </div>
                        <div class="pull-right"> <?= print_string($entity['no_jurnal']); ?></div>
                    </div>
                    <div class="clearfix">
                        <div class="pull-left">DATE : </div>
                        <div class="pull-right"> <?= print_date($entity['tanggal_jurnal']); ?></div>
                    </div>
                </div>
            </div>

            <!-- <div class="col-sm-12 col-md-8 col-md-pull-4">
                <dl class="dl-inline">
                    <dt>Received From/Consignor</dt>
                    <dd><?= $entity['received_from']; ?></dd>

                    <dt>Received By/Consignee</dt>
                    <dd><?= $entity['received_by']; ?></dd>

                    <dt>Known By</dt>
                    <dd><?= $entity['known_by']; ?></dd>

                    <dt>Notes</dt>
                    <dd><?= $entity['notes']; ?></dd>
                </dl>
            </div> -->
        </div>

        <div class="row" id="document_details">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-striped table-nowrap">
                        <thead id="table_header">
                            <tr>
                                <th>No</th>
                                <th>Description</th>
                                <th>P/N</th>
                                <th>S/N</th>
                                <th>Location</th>
                                <th>Qty</th>
                                <th>Unit Value</th>
                                <th>Amount</th>
                                <th>Account</th>
                            </tr>
                        </thead>
                        <tbody id="table_contents">
                            <?php $n = 0; ?>
                            <?php $received_quantity = array(); ?>
                            <?php foreach ($entity['items'] as $i => $detail) : ?>
                                <?php $n++; ?>
                                <tr>
                                    <td class="no-space">
                                        <?= print_number($n); ?>
                                    </td>
                                    <td>
                                        <?= print_string($detail['description']); ?>
                                    </td>
                                    <td>
                                        <?= print_string($detail['part_number']); ?>
                                    </td>
                                    <td>
                                        <?= print_string($detail['serial_number']); ?>
                                    </td>
                                    <td>
                                        <?= print_string($detail['warehouse']); ?>
                                    </td>
                                    <td>
                                        <?= print_number(($detail['trs_kredit'] / $detail['unit_value']), 2); ?>
                                    </td>
                                    <td>
                                        <?= print_number($detail['unit_value'], 2); ?>
                                    </td>
                                    <td>
                                        <?= print_number($detail['trs_kredit'], 2); ?>
                                    </td>
                                    <td>
                                        <?= print_string($detail['kode_pemakaian']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card-foot">
        <?php
        $today    = date('Y-m-d');
        $date     = strtotime('-2 day', strtotime($today));
        $data     = date('Y-m-d', $date);
        ?>

        <div class="pull-right">
            <?php if (is_granted($module, 'document')) : ?>
                <a href="<?= site_url($module['route'] . '/edit/' . $entity['id']); ?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
                    <i class="md md-edit"></i>
                    <small class="top right">edit</small>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
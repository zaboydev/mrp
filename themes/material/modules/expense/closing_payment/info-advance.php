<div class="modal-content">
    <div class="modal-header style-primary-dark">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modal-show-advance-label">Advance List</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-striped table-nowrap">
                        <thead id="table_header">
                            <tr>
                                <th>No</th>
                                <th>ADV#</th>
                                <!-- <th>Payment#</th> -->
                                <th>Paid at</th>
                                <th>SPD#</th>
                                <th>SPPD#</th>
                                <th align="right">Amount Paid</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($entity as $request) : ?>
                            <?php $n++; ?>
                            <tr>
                                <td class="no-space">
                                    <?= print_number($n); ?>
                                </td>
                                <td class="no-space">
                                    <a class="link" href="<?= site_url('spd_payment/print_pdf/' . $request['id']) ?>" target="_blank"><?=print_string($request['document_number'])?></a>
                                </td>
                                <td class="hide no-space">
                                    <?= print_string($request['payment_number']); ?>
                                </td>
                                <td class="no-space">
                                    <?= print_date($request['paid_at']); ?>
                                </td>
                                <td class="no-space">
                                    <a class="link" href="<?= site_url('business_trip_request/print_pdf/' . $request['spd_id']) ?>" target="_blank"><?=print_string($request['spd_number'])?></a>
                                </td>
                                <td class="no-space">
                                    <a class="link" href="<?= site_url('sppd/print_pdf/' . $request['sppd_id']) ?>" target="_blank"><?=print_string($request['sppd_number'])?></a>
                                </td>
                                <td class="no-space">
                                    <?= print_number($request['amount_paid'],2); ?>
                                </td>
                                <td class="no-space">
                                    <a href="<?= site_url('spd_payment/manage_attachment/'.$request['id']);?>" title="show Attachment advance" class="btn btn-icon-toggle btn-info btn-xs" onClick="return popup(this, 'attachment')"><i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
    </div>
</div>
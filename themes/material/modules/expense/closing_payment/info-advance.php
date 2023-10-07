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
                                <th>Payment#</th>
                                <th>Paid at</th>
                                <th>SPD#</th>
                                <th>SPPD#</th>
                                <th align="right">Amount Paid</th>
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
                                    <?= print_string($request['document_number']); ?>
                                </td>
                                <td class="no-space">
                                    <?= print_string($request['payment_number']); ?>
                                </td>
                                <td class="no-space">
                                    <?= print_date($request['paid_at']); ?>
                                </td>
                                <td class="no-space">
                                    <?= print_string($request['spd_number']); ?>
                                </td>
                                <td class="no-space">
                                    <?= print_string($request['sppd_number']); ?>
                                </td>
                                <td class="no-space">
                                    <?= print_number($request['amount_paid'],2); ?>
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
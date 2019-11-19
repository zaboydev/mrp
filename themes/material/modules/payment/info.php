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
      <div class="col-sm-12 col-md-4 col-md-push-8">
        <div class="well">
          <div class="clearfix">
            <div class="pull-left">TRANSACTION NO.: </div>
            <div class="pull-right"><?= print_string($entity['no_transaksi']); ?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">DATE: </div>
            <div class="pull-right"><?= print_date($entity['tanggal']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-8 col-md-pull-4">
        <dl class="dl-inline">
          <dt>Payment To</dt>
          <dd><?= $entity['vendor']; ?></dd>

          <dt>Created By</dt>
          <dd><?= $entity['created_by']; ?></dd>

          <!-- <dt>Known By</dt>
          <dd><?= $entity['known_by']; ?></dd>

          <dt>Notes</dt>
          <dd><?= $entity['notes']; ?></dd> -->
        </dl>
      </div>
    </div>

    <div class="row" id="document_details">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-striped table-nowrap">
            <thead id="table_header">
              <tr>
                <th>No</th>
                <th>PO#</th>
                <th>Currency</th>
                <th>P/N</th>
                <th>Description</th>
                <th align="right">Amount Paid</th>
              </tr>
            </thead>
            <tbody id="table_contents">
              <?php $n = 0; ?>
              <?php $amount_paid = array(); ?>
              <?php foreach ($entity['items'] as $i => $detail) : ?>
                <?php $n++; ?>
                <tr>
                  <td class="no-space">
                    <?= print_number($n); ?>
                  </td>
                  <td>
                    <?= print_string($detail['document_number']); ?>
                  </td>
                  <td>
                    <?= print_string($detail['default_currency']); ?>
                  </td>
                  <td>
                    <?= print_string($detail['part_number']); ?>
                  </td>
                  <td>
                    <?= print_string($detail['description']); ?>
                  </td>
                  <td>
                    <?= print_number($detail['amount_paid'], 2); ?>
                    <?php $amount_paid[] = $detail['amount_paid']; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <th></th>
                <th>Total</th>
                <th></th>
                <th></th>
                <th></th>
                <th><?= print_number(array_sum($amount_paid), 2); ?></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
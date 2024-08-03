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
            <div class="pull-left">DOCUMENT NO.: </div>

            <div class="pull-right"><?= $entity['document_number'] ?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">DATE: </div>
            <div class="pull-right"><?= $entity['document_date'] ?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">VENDOR: </div>
            <div class="pull-right"><?= $entity['vendor'] ?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">Term of Payment: </div>
            <div class="pull-right"><?= print_number($entity['term_payment']); ?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">Due Date: </div>
            <div class="pull-right"><?= print_date($entity['due_date']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-8 col-md-pull-4">
        <dl class="dl-inline">
          <dt>Order Status</dt>
          <dd><?= print_string($entity['status']); ?></dd>

          <dt>Issued By</dt>
          <dd><?= print_string($entity['issued_by'], 'N/A'); ?></dd>

          <dt>Checked By</dt>
          <dd><?= print_string($entity['checked_by'], 'N/A'); ?></dd>

          <dt>Approved By</dt>
          <dd><?= print_string($entity['approved_by'], 'N/A'); ?></dd>

          <dt>Notes</dt>
          <dd><?= print_string($entity['notes'], '-'); ?></dd>
        </dl>
      </div>
    </div>
    <div class="row" id="document_details">
      <div class="col-sm-6">
        <h5>Item Order</h5>
        <div class="table-responsive">
          <table class="table table-striped table-nowrap">
            <thead id="table_header">
              <tr>
                <th class="middle-alignment"></th>
                <th class="middle-alignment">Description</th>
                <th class="middle-alignment">Part Number</th>
                <th class="middle-alignment">Alt. P/N</th>
                <th class="middle-alignment" colspan="2">Quantity</th>
                <th class="middle-alignment">Total Amount <?= $entity['default_currency']; ?></th>
              </tr>
            </thead>
            <tbody id="table_contents">
              <?php $n = 0; ?>
              <?php $total_amount = array(); ?>
              <?php foreach ($entity['items'] as $i => $detail) : ?>
                <?php $total_amount[] = $detail['total_amount']; ?>
                <?php $n++; ?>
                <tr id="row_<?= $i; ?>">
                  <td width="1">
                    <?= $n; ?>
                  </td>
                  <td>
                    <?= print_string($detail['description']); ?>
                  </td>
                  <td class="no-space">
                    <?= print_string($detail['part_number']); ?>
                  </td>
                  <td class="no-space">
                    <?= print_string($detail['alternate_part_number']); ?>
                  </td>
                  <td>
                    <?= print_number($detail['quantity'], 2); ?>
                  </td>
                  <td>
                    <?= print_string($detail['unit']); ?>
                  </td>
                  <td>
                    <?= print_number($detail['total_amount'], 2); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php $subtotal       = array_sum($total_amount); ?>
              <?php $after_discount = $subtotal - $entity['discount']; ?>
              <?php $total_taxes    = $after_discount * ($entity['taxes'] / 100); ?>
              <?php $after_taxes    = $after_discount + $total_taxes; ?>
              <?php $grandtotal     = $after_taxes + $entity['shipping_cost']; ?>
            </tbody>
            <tfoot>
              <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th style="background-color: #eee;">Subtotal <?= $entity['default_currency']; ?></th>
                <th style="background-color: #eee;"><?= print_number($subtotal, 2); ?></th>
              </tr>
              <?php if ($entity['discount'] > 0) : ?>
                <tr>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th style="background-color: #eee;">Discount</th>
                  <th style="background-color: #eee;"><?= print_number($entity['discount'], 2); ?></th>
                </tr>
              <?php endif; ?>
              <?php if ($entity['taxes'] > 0) : ?>
                <tr>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th style="background-color: #eee;">VAT <?= $entity['taxes']; ?> %</th>
                  <th style="background-color: #eee;"><?= print_number($total_taxes, 2); ?></th>
                </tr>
              <?php endif; ?>
              <?php if ($entity['shipping_cost'] > 0) : ?>
                <tr>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th style="background-color: #eee;">Shipping Cost</th>
                  <th style="background-color: #eee;"><?= print_number($entity['shipping_cost'], 2); ?></th>
                </tr>
              <?php endif; ?>
              <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th style="background-color: #eee;">Grand Total</th>
                <th style="background-color: #eee;"><?= print_number($grandtotal, 2); ?></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="col-sm-6">
        <h5>Payment</h5>
        <div class="table-responsive">
          <table class="table table-striped table-nowrap">
            <thead id="table_header">
              <tr>
                <th class="middle-alignment"></th>
                <th class="middle-alignment">No Transaksi</th>
                <th class="middle-alignment">Tanggal</th>
                <th class="middle-alignment">No Cheque</th>
                <th class="middle-alignment" colspan="2">Amount</th>
              </tr>
            </thead>
            <tbody id="table_contents">
              <?php $n = 0; ?>
              <?php $total_amount_payment = array(); ?>
              <?php if ($entity['count_payment'] > 0) : ?>
                <?php foreach ($entity['payments'] as $i => $detail_payment) : ?>
                  <?php $total_amount_payment[] = $detail_payment['amount_paid']; ?>
                  <?php $n++; ?>
                  <tr id="row_<?= $i; ?>">
                    <td width="1">
                      <?= $n; ?>
                    </td>
                    <td>
                      <?= print_string($detail_payment['no_transaksi']); ?>
                    </td>
                    <td class="no-space">
                      <?= print_string($detail_payment['tanggal']); ?>
                    </td>
                    <td class="no-space">
                      <?= print_string($detail_payment['no_cheques']); ?>
                    </td>
                    <td colspan="2">
                      <?= print_number($detail_payment['amount_paid'], 2); ?>
                      </td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr>
                  <td colspan="6" align="center">No Payment</td>
                </tr>
              <?php endif; ?>
              <?php $total_payment       = array_sum($total_amount_payment); ?>
            </tbody>
            <tfoot>
              <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th style="background-color: #eee;">Total <?= $entity['default_currency']; ?></th>
                <th style="background-color: #eee;"><?= print_number($total_payment, 2); ?></th>
              </tr>
              <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th style="background-color: #eee;">&nbsp;</th>
                <th style="background-color: #eee;">&nbsp;</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="card-foot">

  </div>
</div>
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
            <div class="pull-right"><?= print_string($entity['document_number']); ?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">VENDOR : </div>
            <div class="pull-right"><?= print_string($entity['vendor']); ?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">DATE: </div>
            <div class="pull-right"><?= print_date($entity['document_date']); ?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">BASE: </div>
            <div class="pull-right"><?= strtoupper($entity['warehouse']); ?></div>
          </div>
          <div class="clearfix hide">
            <div class="pull-left">INVENTORY: </div>
            <div class="pull-right"><?= print_string($entity['category']); ?></div>
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

          <dt>Reference POE</dt>
          <dd><?= print_string($entity['evaluation_number'], 'N/A'); ?></dd>

          <dt>Notes</dt>
          <dd><?= print_string($entity['notes'], '-'); ?></dd>
        </dl>
      </div>
    </div>

    <div class="row" id="document_details">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-striped table-nowrap">
            <thead id="table_header">
              <tr>
                <th class="middle-alignment"></th>
                <th class="middle-alignment">Description</th>
                <th class="middle-alignment">Part Number</th>
                <th class="middle-alignment">Alt. P/N</th>
                <th class="middle-alignment">Remarks</th>
                <th class="middle-alignment">POE Number</th>
                <th class="middle-alignment">PR Number</th>
                <th class="middle-alignment" colspan="2">Quantity</th>
                <th class="middle-alignment">Unit Price <?= $entity['default_currency']; ?></th>
                <th class="middle-alignment">Core Charge <?= $entity['default_currency']; ?></th>
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
                    <?= print_string($detail['remarks']); ?>
                  </td>
                  <td>
                    <?= print_string($detail['poe_number']); ?>
                  </td>
                  <td>
                    <?= print_string($detail['purchase_request_number']); ?>
                  </td>
                  <td>
                    <?= print_number($detail['quantity'], 2); ?>
                  </td>
                  <td>
                    <?= print_string($detail['unit']); ?>
                  </td>
                  <td>
                    <?= print_number($detail['unit_price'], 2); ?>
                  </td>
                  <td>
                    <?= print_number($detail['core_charge'], 2); ?>
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
    </div>
    <?php if (is_granted($module, 'order') && $entity['review_status'] == 'APPROVED' && $entity['status'] == 'PURPOSED' && $tipe != 'report') : ?>
      <?= form_open(current_url(), array(
          'class' => 'form-xhr-order pull-left',
        )); ?>
      <div class="form-group col-xs-3">
        <div class="input-group">
          <span class="input-group-addon">Term of Payment</span>
          <div class="input-group-content">
            <input type="text" name="term_payment" id="term_payment" class="form-control" value="<?= $entity['term_payment'] ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_term_payment'); ?>" required>
            <!-- <label for="document_number">Term of Payment</label> -->
          </div>
          <span class="input-group-addon">Days</span>
        </div>
      </div>
      <input type="hidden" name="id" id="id" value="<?= $entity['id']; ?>">

      <a href="<?= site_url($module['route'] . '/order/'); ?>" data-tipe="order" class="btn btn-floating-action btn-danger btn-xhr-order btn-tooltip ink-reaction" id="modal-order-data-button">
        <i class="md md-shopping-cart"></i>
        <small class="top left">Order</small>
      </a>
      <?= form_close(); ?>
    <?php endif; ?>
    <?php if (strpos($entity['document_number'], 'W') !== FALSE) : ?>
      <?php if (is_granted($module, 'order') && $entity['review_status'] == 'APPROVED' && $tipe != 'report') : ?>
        <?php if ($entity['status'] == 'ORDER') : ?>
          <?= form_open(current_url(), array(
                  'class' => 'form-xhr-order pull-left',
                )); ?>
          <div class="form-group col-xs-3">
            <div class="input-group">
              <span class="input-group-addon">Notes</span>
              <div class="input-group-content">
                <textarea name="notes" id="notes" class="form-control"></textarea>
                <!-- <label for="document_number">Term of Payment</label> -->
              </div>
            </div>
          </div>
          <input type="hidden" name="id" id="id" value="<?= $entity['id']; ?>">

          <a href="<?= site_url($module['route'] . '/close_wo/'); ?>" data-tipe="closing" class="btn btn-floating-action btn-success btn-xhr-order btn-tooltip ink-reaction" id="modal-order-data-button">
            <i class="md md-check"></i>
            <small class="top left">Close</small>
          </a>
          <?= form_close(); ?>
        <?php endif; ?>
      <?php endif; ?>
    <?php endif; ?>

  </div>

  <div class="card-foot">
    <div class="pull-left">

    </div>
    <div class="pull-right">
      <?php if (is_granted($module, 'payment') && $tipe != 'report') : ?>
        <!-- <a href="<?= site_url($module['route'] . '/payment/' . $entity['id']); ?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-payment-data-button">
          <i class="md md-attach-money"></i>
          <small class="top right">payment</small>
        </a> -->
      <?php endif; ?>



      <?php if (is_granted($module, 'document') && empty($entity['document_number']) && $tipe != 'report') : ?>
        <a href="<?= site_url($module['route'] . '/approve/' . $entity['id']); ?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-approval-data-button">
          <i class="md md-edit"></i>
          <small class="top right">Create PO</small>
        </a>
      <?php endif; ?>
      <?php if (is_granted($module, 'document') && $tipe != 'report') : ?>
        <?php if (strpos($entity['document_number'], 'R') === FALSE) : ?>
          <?php if ($entity['status'] != 'CLOSED' || $entity['status'] != 'ADVANCE') : ?>
            <a href="<?= site_url($module['route'] . '/edit/' . $entity['id']); ?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
              <i class="md md-edit"></i>
              <small class="top right">Revisi</small>
            </a>
          <?php endif; ?>
        <?php endif; ?>
      <?php endif; ?>

      <?php if (is_granted($module, 'print') && $entity['document_number'] != null) : ?>
        <a href="<?= site_url($module['route'] . '/print_pdf/' . $entity['id']); ?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" target="_blank" id="modal-print-data-button">
          <i class="md md-print"></i>
          <small class="top right">print</small>
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php //startblock('page_modals') 
?>

<?php //endblock() 
?>
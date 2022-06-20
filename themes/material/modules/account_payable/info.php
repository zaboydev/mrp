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
                <th class="middle-alignment">Request Number</th>
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
                  <td class="no-space">
                    <a href="<?= site_url('account_payable/print_prl/' . $detail['request_id'].'/'.$entity['tipe_po']) ?>" target="_blank"><?=print_string($detail['purchase_request_number'])?></a>
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
                <th style="background-color: #eee;">Grand Total</th>
                <th style="background-color: #eee;"><?= print_number($grandtotal, 2); ?></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="row">
          <div class="col-sm-12">
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
                    <th class="middle-alignment">Status</th>
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
                        <td class="no-space">
                          <?= print_string($detail_payment['status']); ?>
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
          <div class="col-sm-12">
            <h5>Receipt Item (GRN)</h5>
            <div class="table-responsive">
              <table class="table table-striped table-nowrap">
                <thead id="table_header">
                  <tr>
                    <th class="middle-alignment"></th>
                    <th class="middle-alignment">Document Number</th>
                    <th class="middle-alignment">Received Date</th>
                    <th class="middle-alignment">Received Quantity</th>
                    <th class="middle-alignment">Received Unit Value</th>
                    <th class="middle-alignment">Received Total Value</th>
                    <th class="middle-alignment">Received By</th>
                  </tr>
                </thead>
                <tbody id="table_contents">
                  <?php $n = 0; ?>
                  <?php $total_amount = array(); ?>
                  <?php foreach ($entity['items'] as $i => $detail) : ?>
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
                      <td class="no-space"></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <?php if(count($detail['receipts'])>0):?>
                    <?php foreach ($detail['receipts'] as $i => $receipt) : ?>
                    <?php $n++; ?>
                    <tr id="row_<?= $i; ?>">
                      <td width="1"></td>
                      <td>
                        <a href="<?= site_url('goods_received_note/print_pdf/' . $receipt['id']) ?>">
                          <?= print_string($receipt['document_number']); ?>
                        </a>
                        <?php if(isAttachementExists($receipt['id'],'GRN')):?>
                        <a href="<?= site_url('goods_received_note/manage_attachment/' . $receipt['id']); ?>" onClick="return popup(this, 'attachment')" data-id="<?=$grn['id']?>" class="btn btn-icon-toggle btn-info btn-sm btn-show-att-grn">
                          <i class="fa fa-eye"></i>
                        </a>
                        <?php endif;?>   
                      </td>
                      <td class="no-space">
                        <?= print_date($receipt['received_date']); ?>
                      </td>
                      <td class="no-space">
                        <?= print_number($receipt['received_quantity'],2); ?>
                      </td>                      
                      <td class="no-space">
                        <?= print_number($receipt['received_unit_value'],2); ?>
                      </td>                      
                      <td class="no-space">
                        <?= print_number($receipt['received_total_value'],2); ?>
                      </td>
                      <td>
                        <?= print_string($receipt['received_by']); ?>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                    <td colspan="6" style="text-align:center;">No Receipt</td>
                    </tr>
                    <?php endif;?>
                  <?php endforeach; ?>
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
                  </tr>                  
                </tfoot>
              </table>
            </div>
          </div>
        </div>
        
      </div>
    </div>
  </div>

  <div class="card-foot">
    <div class="pull-left">
    <?php if($entity['tipe_po']!='INVENTORY MRP'):?>
      <a href="<?= site_url(strtolower($entity['tipe_po']).'_purchase_order'.'/manage_attachment/' . $entity['id'].'/payment'); ?>" onClick="return popup(this, 'attachment')" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction">
        <i class="md md-attach-file"></i>
        <small class="top right">Attachment</small>
      </a>      
    <?php endif;?>
    <?php if($entity['tipe_po']=='INVENTORY MRP'):?>
      <a href="<?= site_url('purchase_order'.'/manage_attachment/' . $entity['id'].'/payment'); ?>" onClick="return popup(this, 'attachment')" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction">
        <i class="md md-attach-file"></i>
        <small class="top right">Attachment</small>
      </a>      
    <?php endif;?>
    </div>
    <div class="pull-right">
      <?php if($entity['tipe_po']=='INVENTORY MRP'):?>
      <a href="<?= site_url('purchase_order/print_pdf/' . $entity['id']); ?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" target="_blank" id="modal-print-data-button">
        <i class="md md-print"></i>
        <small class="top right">print</small>
      </a>
      <?php elseif($entity['tipe_po']=='INVENTORY'):?>
      <a href="<?= site_url('inventory_purchase_order/print_pdf/' . $entity['id']); ?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" target="_blank" id="modal-print-data-button">
        <i class="md md-print"></i>
        <small class="top right">print</small>
      </a>
      <?php elseif($entity['tipe_po']=='CAPEX'):?>
      <a href="<?= site_url('capex_purchase_order/print_pdf/' . $entity['id']); ?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" target="_blank" id="modal-print-data-button">
        <i class="md md-print"></i>
        <small class="top right">print</small>
      </a>
      <?php elseif($entity['tipe_po']=='EXPENSE'):?>
      <a href="<?= site_url('expense_purchase_order/print_pdf/' . $entity['id']); ?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" target="_blank" id="modal-print-data-button">
        <i class="md md-print"></i>
        <small class="top right">print</small>
      </a>
      <?php endif;?>
    </div>
  </div>
</div>
<script type="text/javascript">
  function popup(mylink, windowname) {
    var height = window.innerHeight;
    var widht;
    var href;

    if (screen.availWidth > 768) {
      width = 769;
    } else {
      width = screen.availWidth;
    }

    var left = (screen.availWidth / 2) - (width / 2);
    var top = 0;
    // var top = (screen.availHeight / 2) - (height / 2);

    if (typeof(mylink) == 'string') href = mylink;
    else href = mylink.href;

    window.open(href, windowname, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + width + ', height=' + height + ', top=' + top + ', left=' + left);

    if (!window.focus) return true;
    else return false;
  }
</script>
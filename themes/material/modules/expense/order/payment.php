<div class="card card-underline style-default-bright">
  <form class="form" action="<?=site_url($module['route'] .'/payment_save/'. $entity['id']);?>" method="post">
    <div class="card-head style-primary-dark">
      <header><?=strtoupper($module['label']);?></header>

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
              <div class="pull-left">ORDER NO.: </div>
              <div class="pull-right"><?=print_string($entity['document_number']);?></div>
            </div>
            <div class="clearfix">
              <div class="pull-left">POE NO.: </div>
              <div class="pull-right"><?=print_string($entity['evaluation_number']);?></div>
            </div>
            <div class="clearfix">
              <div class="pull-left">REQUEST NO.: </div>
              <div class="pull-right"><?=print_string($entity['purchase_request_number']);?></div>
            </div>
            <div class="clearfix">
              <div class="pull-left">DATE: </div>
              <div class="pull-right"><?=print_date($entity['document_date']);?></div>
            </div>
            <div class="clearfix">
              <div class="pull-left">BASE: </div>
              <div class="pull-right"><?=strtoupper($entity['warehouse']);?></div>
            </div>
            <div class="clearfix">
              <div class="pull-left">INVENTORY: </div>
              <div class="pull-right"><?=print_string($entity['category']);?></div>
            </div>
            <div class="clearfix">
              <div class="pull-left">ISSUED BY: </div>
              <div class="pull-right"><?=print_string($entity['issued_by']);?></div>
            </div>
          </div>
        </div>

        <div class="col-sm-12 col-md-8 col-md-pull-4">
          <dl class="dl-inline">
            <dt>Description</dt>
            <dd><?=$entity['description'];?></dd>

            <dt>Part Number</dt>
            <dd><?=$entity['part_number'];?></dd>

            <dt>Alt. Part Number</dt>
            <dd><?=print_string($entity['alternate_part_number'], 'N/A');?></dd>

            <dt>Currency</dt>
            <dd><?=$entity['default_currency'];?></dd>

            <dt>Exchange Rate</dt>
            <dd><?=$entity['exchange_rate'];?></dd>

            <dt>Quantity</dt>
            <dd><?=number_format($entity['quantity'], 2);?></dd>

            <dt>Unit Price</dt>
            <dd><?=$entity['default_currency'];?> <?=number_format($entity['unit_price'], 2);?></dd>

            <dt>Core Charge</dt>
            <dd><?=$entity['default_currency'];?> <?=number_format($entity['core_charge'], 2);?></dd>

            <dt>Total Amount</dt>
            <dd><?=$entity['default_currency'];?> <?=number_format($entity['total_amount'], 2);?></dd>

            <dt>Total Paid Amount</dt>
            <dd><?=$entity['default_currency'];?> <?=number_format($entity['total_amount'] - $entity['left_paid_amount'], 2);?></dd>

            <dt>Left Paid Amount</dt>
            <dd><?=$entity['default_currency'];?> <?=number_format($entity['left_paid_amount'], 2);?></dd>
          </dl>

          <?php if ($entity['left_paid_amount'] > 0):?>
            <div class="form-group">
              <label class="control-label" for="amount_paid">Amount to paid</label>
              <input type="number" id="amount_paid" name="amount_paid" class="form-control" value="<?=$entity['left_paid_amount'];?>">
            </div>

            <div class="form-group">
              <label class="control-label" for="remarks">Paid Remarks</label>
              <input type="text" id="remarks" name="remarks" class="form-control">
            </div>
          <?php endif;?>
        </div>
      </div>
    </div>

    <div class="card-foot">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

      <?php if ($entity['left_paid_amount'] > 0):?>
        <div class="pull-right">
          <button type="submit" class="btn btn-primary">Paid</button>
        </div>
      <?php endif;?>
    </div>
  </form>
</div>

<div class="card card-underline style-default-bright">
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
            <div class="pull-left">DOCUMENT NO.: </div>
            <div class="pull-right"><?=print_string($entity['pr_number']);?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">PR DATE: </div>
            <div class="pull-right"><?=print_date($entity['pr_date']);?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">BASE: </div>
            <div class="pull-right"><?=config_item('main_warehouse');?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">INVENTORY: </div>
            <div class="pull-right"><?=print_string($entity['category_name']);?></div>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-8 col-md-pull-4">
        <dl class="dl-inline">
          <dt>Required Date</dt>
          <dd><?=print_date($entity['required_date']);?></dd>

          <dt>Required By</dt>
          <dd><?=print_person_name($entity['created_by']);?></dd>

          <dt>Status</dt>
          <dd><?=($entity['status'] == 'approved') ? 'BUDGETED' : strtoupper($entity['status']);?></dd>

          <dt>Suggested Supplier</dt>
          <dd><?=print_string($entity['suggested_supplier']);?></dd>

          <dt>Deliver To</dt>
          <dd><?=print_string($entity['deliver_to']);?></dd>

          <dt>Notes</dt>
          <dd><?=$entity['notes'];?></dd>
        </dl>
      </div>
    </div>

    <div class="row" id="document_details">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-striped table-nowrap">
            <thead id="table_header">
              <tr>
                <th rowspan="3" class="text-center">Act</th>
                <th rowspan="3" class="text-center">No</th>
                <th rowspan="3" class="text-center">Description</th>
                <th rowspan="3" class="text-center">Part Number</th>
                <th rowspan="3" class="text-center">Request Budget</th>
                <th rowspan="3" class="text-center">Available Budget</th>
                <th rowspan="3" class="text-center">Qty</th>
                <th rowspan="3" class="text-center">Unit</th>
                <th rowspan="3" class="text-center">Price</th>
                <th rowspan="3" class="text-center">Subtotal</th>
                <th colspan="6" class="text-center">Month to Date</th>
                <th colspan="6" class="text-center">Year to Date</th>
                <th colspan="4" class="text-center">Full Year</th>
              </tr>
              <tr>
                <th colspan="2" class="text-center">Plan</th>
                <th colspan="2" class="text-center">Actual</th>
                <th colspan="2" class="text-center">Balance</th>
                <th colspan="2" class="text-center">Plan</th>
                <th colspan="2" class="text-center">Actual</th>
                <th colspan="2" class="text-center">Balance</th>
                <th colspan="2" class="text-center">Plan</th>
                <th colspan="2" class="text-center">Balance</th>
              </tr>
              <tr>
                <th class="text-center">Qty</th>
                <th class="text-center">Price</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Price</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Price</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Price</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Price</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Price</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Price</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Price</th>
              </tr>
            </thead>
            <tbody id="table_contents">
              <?php $n = 0;?>
              <?php $grand_total = array();?>
              <?php foreach ($entity['items'] as $i => $detail):?>
                <?php $n++;?>
                <?php $grand_total[] = $detail['total'];?>
                <tr>
                  <td>
                    <a href="<?=site_url($module['route'] .'/relocate/'. $entity['id']);?>" class="btn btn-floating-action btn-small btn-danger btn-tooltip ink-reaction" id="modal-edit-data-button">
                      <i class="md md-refresh"></i>
                      <small class="top right">Relocate</small>
                    </a>
                  </td>
                  <td class="no-space">
                    <?=print_number($n);?>
                  </td>
                  <td>
                    <?=print_string($detail['product_name']);?>
                  </td>
                  <td>
                    <?=print_string($detail['part_number']);?>
                  </td>
                  <td></td>
                  <td></td>
                  <td>
                    <?=print_number($detail['quantity'], 2);?>
                  </td>
                  <td>
                    <?=print_string($detail['unit']);?>
                  </td>
                  <td>
                    <?=print_number($detail['price'], 2);?>
                  </td>
                  <td>
                    <?=print_number($detail['total'], 2);?>
                  </td>
                  <td>
                    <?=print_number($detail['mtd_quantity'], 2);?>
                  </td>
                  <td>
                    <?=print_number($detail['mtd_budget'], 2);?>
                  </td>
                  <td>
                    <?=print_number($detail['mtd_used_quantity'], 2);?>
                  </td>
                  <td>
                    <?=print_number($detail['mtd_used_budget'], 2);?>
                  </td>
                  <td>
                    <?=print_number($detail['mtd_quantity'] - $detail['mtd_used_quantity'], 2);?>
                  </td>
                  <td>
                    <?=print_number($detail['mtd_budget'] - $detail['mtd_used_budget'], 2);?>
                  </td>
                  <td>
                    <?=print_number($detail['ytd_quantity'], 2);?>
                  </td>
                  <td>
                    <?=print_number($detail['ytd_budget'], 2);?>
                  </td>
                  <td>
                    <?=print_number($detail['ytd_used_quantity'], 2);?>
                  </td>
                  <td>
                    <?=print_number($detail['ytd_used_budget'], 2);?>
                  </td>
                  <td>
                    <?=print_number($detail['ytd_quantity'] - $detail['ytd_used_quantity'], 2);?>
                  </td>
                  <td>
                    <?=print_number($detail['ytd_budget'] - $detail['ytd_used_budget'], 2);?>
                  </td>
                  <td>
                    <?=print_number($detail['fyp_quantity'], 2);?>
                  </td>
                  <td>
                    <?=print_number($detail['fyp_budget'], 2);?>
                  </td>
                  <td>
                    <?=print_number($detail['fyp_quantity'] - $detail['fyp_used_quantity'], 2);?>
                  </td>
                  <td>
                    <?=print_number($detail['fyp_budget'] - $detail['fyp_used_budget'], 2);?>
                  </td>
                </tr>
              <?php endforeach;?>
            </tbody>
            <tfoot>
              <tr>
                <th></th>
                <th>Total</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th><?=print_number(array_sum($grand_total), 2);?></th>
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

  <div class="card-foot">
    <div class="pull-right">
      <?php if (is_granted($module, 'document') && $entity['status'] == 'pending'):?>
        <a href="<?=site_url($module['route'] .'/edit/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
          <i class="md md-edit"></i>
          <small class="top right">edit</small>
        </a>
      <?php endif;?>
       <?php if (is_granted($module, 'document') && $entity['status'] == 'approved'):?>
        <a href="<?=site_url($module['route'] .'/cancel/'. $entity['id']);?>" class="btn btn-floating-action btn-danger btn-tooltip ink-reaction" id="modal-edit-data-button">
          <i class="md md-cancel"></i>
          <small class="top right">Cancel</small>
        </a>
      <?php endif;?>
      <?php if (is_granted($module, 'print') && $entity['status'] == 'approved'):?>
        <a href="<?=site_url($module['route'] .'/print_pdf/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" target="_blank" id="modal-print-data-button">
          <i class="md md-print"></i>
          <small class="top right">print</small>
        </a>
      <?php endif;?>
    </div>
  </div>
</div>

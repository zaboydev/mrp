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
          <!--  -->
          <div class="clearfix">
            <div class="pull-left">COST CENTER: </div>
            <div class="pull-right"><?=print_string($entity['cost_center_name']);?></div>
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
          <dd><?=strtoupper($entity['status']);?></dd>

          <dt>Approval Status</dt>
          <?php if($entity['status']=='approved'):?>
          <dd> APPROVED by <?=print_person_name($entity['head_approved_by']);?></dd>
          <?php elseif($entity['status']=='rejected'):?>
          <dd> REJECTED by <?=print_person_name($entity['rejected_by']);?></dd>
          <?php elseif($entity['status']=='cancel'):?>
          <dd> CANCELED by <?=print_person_name($entity['canceled_by']);?></dd>
          <?php elseif($entity['status']=='WAITING FOR HEAD DEPT'):?>
          <dd> BUDGETCONTROL APPROVED by <?=print_person_name($entity['approved_by']);?></dd>
          <?php endif;?>

          <dt>Suggested Supplier</dt>
          <dd><?=print_string($entity['suggested_supplier']);?></dd>

          <dt>Deliver To</dt>
          <dd><?=($entity['deliver_to']==null)? 'N/A':print_string($entity['deliver_to']);?></dd>

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
              <th align="right" width="1">No</th>
              <th width="10">Description</th>
              <th width="10">Part Number</th>
              <th align="right" width="1">Qty</th>
              <th width="1">Unit</th>
              <th align="right" width="10">Price</th>
              <th align="right" width="10">Total</th>
              <th align="right" width="10">Balance Budget Year to Date (Qty)</th>
              <!-- <th align="right" width="10">Budget Status</th> -->
            </thead>
            <tbody id="table_contents">
              <?php $n = 0; $open=0;?>
              <?php $total_qty = array();$total = array();?>
              <?php foreach ($entity['items'] as $i => $detail):?>
                <?php 
                  $n++;
                  $total_qty[] = $detail['quantity'];
                  $total[] = $detail['total'];
                ?>
                <tr>
                  <td align="right">
                    <?=print_number($n);?>
                  </td>
                  <td>
                    <?=$detail['product_name'];?>
                  </td>
                  <td>
                    <?=print_string($detail['product_code']);?>
                  </td>
                  <td align="right">
                    <?=print_number($detail['quantity'], 2);?>
                  </td>
                  <td>
                    <?=print_string($detail['unit']);?>
                  </td>
                  <td align="right">
                    <?=print_number($detail['price'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_number($detail['total'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_number($detail['ytd_quantity'] - $detail['ytd_used_quantity'], 2);?>
                  </td>
                  
                </tr>
              <?php endforeach;?>
            </tbody>
            <tfoot>
              <tr>
                <th>Total</th>
                <th></th>
                <th></th>
                <th><?=print_number(array_sum($total_qty), 2);?></th>
                <th></th>
                <th></th>
                <th><?=print_number(array_sum($total), 2);?></th>
                <th></th>
                <!-- <th></th> -->
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="col-sm-12">
        <?php if($entity['approved_date']!=null):?>
          <dl class="dl-inline">
            <dt>Approval Notes</dt>
            <dd><?=$entity['approved_notes'];?></dd>
          </dl>
          <?php elseif($entity['rejected_date']!=null):?>
          <dl class="dl-inline">
            <dt>Rejected Notes</dt>
            <dd><?=$entity['rejected_notes'];?></dd>
          </dl>
          <?php elseif($entity['canceled_date']!=null):?>
          <dl class="dl-inline">
            <dt>Canceled Notes</dt>
            <dd><?=$entity['canceled_notes'];?></dd>
          </dl>
        <?php endif;?>
      </div>
    </div>
  </div>

  <div class="card-foot">
    <div class="pull-right">
      <?php if (is_granted($module, 'document') && $entity['rejected_date']!=null):?>
        <!-- <a href="<?=site_url($module['route'] .'/edit/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
          <i class="md md-edit"></i>
          <small class="top right">edit</small>
        </a> -->
      <?php endif;?>
       <?php if (is_granted($module, 'document') && $open==0 && $entity['rejected_date']==null):?>
        <!-- <a href="<?=site_url($module['route'] .'/cancel/'. $entity['id']);?>" class="btn btn-floating-action btn-danger btn-tooltip ink-reaction" id="modal-edit-data-button">
          <i class="md md-cancel"></i>
          <small class="top right">Cancel</small>
        </a> -->
      <?php endif;?>
      <?php if (is_granted($module, 'print')):?>
        <a href="<?=site_url($module['route'] .'/print_pdf/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" target="_blank" id="modal-print-data-button">
          <i class="md md-print"></i>
          <small class="top right">print</small>
        </a>
      <?php endif;?>
    </div>
  </div>
</div>

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
          <dd><?=strtoupper($entity['status']);?></dd>

          <dt>Approval Status</dt>
          <?php if($entity['approved_date']!=null):?>
          <dd> APPROVED by <?=print_person_name($entity['approved_by']);?></dd>
          <?php elseif($entity['rejected_date']!=null):?>
          <dd> REJECTED by <?=print_person_name($entity['rejected_by']);?></dd>
          <?php elseif($entity['canceled_date']!=null):?>
          <dd> CANCELED by <?=print_person_name($entity['canceled_by']);?></dd>
          <?php endif;?>

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
              <th align="right" width="1">No</th>
              <th width="10">Description</th>
              <th width="10">Part Number</th>
              <th align="right" width="1">Qty</th>
              <th width="1">Unit</th>
              <th align="right" width="10">On Hand Stock</th>
              <th align="right" width="10">Min. Qty</th>
              <th align="right" width="10">Balance Budget Year to Date (Qty)</th>
              <th align="right" width="10">Budget Status</th>
            </thead>
            <tbody id="table_contents">
              <?php $n = 0; $open=0;?>
              <?php $total_qty = array();?>
              <?php foreach ($entity['items'] as $i => $detail):?>
                <?php if($detail['status']=='open' || $detail['status']=='close' || $detail['status']=='review operation support ' || $detail['status']=='rejected'){$open++;}?>
                <?php $n++;?>
                <?php $total_qty[] = $detail['quantity'];?>
                <tr>
                  <td align="right">
                    <?=print_number($n);?>
                  </td>
                  <td>
                    <?=print_string($detail['product_name']);?>
                  </td>
                  <td>
                    <?=print_string($detail['part_number']);?>
                  </td>
                  <td align="right">
                    <?=print_number($detail['quantity'], 2);?>
                  </td>
                  <td>
                    <?=print_string($detail['unit']);?>
                  </td>
                  <td align="right">
                    <?=print_number($detail['on_hand_qty'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_number($detail['min_qty'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_number($detail['ytd_quantity'] - $detail['ytd_used_quantity'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_string(strtoupper($detail['budget_status']));?>
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
                <th><?=print_number(array_sum($total_qty), 2);?></th>
                <th></th>
                <th></th>
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
      <div class="col-sm-12">
        <h3>History Purchase</h3>
        <div class="table-responsive">
          <table class="table table-striped table-nowrap">
            <thead id="table_header">
              <th>No</th>
              <th>Tanggal</th>
              <th>Purchase Number</th>
              <th>Qty</th>
              <th>Unit</th>
              <th>Price</th>
              <th>Total</th>
              <th>POE Qty</th>
              <th>POE Value</th>
              <th>PO Qty</th>
              <th>PO Value</th>
              <th>GRN Qty</th>
              <th>GRN Value</th>
              <!-- <th align="right" width="10">Budget Status</th> -->
            </thead>
            <tbody id="table_contents">
              <?php $n = 0;?>
              
              <?php foreach ($entity['items'] as $i => $detail):?>
                <?php 
                  $n++;
                ?>
                <tr>
                  <td align="right">
                    <?=print_number($n);?>
                  </td>
                  <td colspan="12">
                    <?=print_string($detail['product_name']);?>
                  </td>
                </tr>
                <?php 
                  $total_qty        = array();
                  $total            = array();
                  $total_qty_poe    = array();
                  $total_value_poe  = array();
                  $total_qty_po     = array();
                  $total_value_po   = array();
                  $total_qty_grn    = array();
                  $total_value_grn  = array();
                ?>
                <?php foreach ($detail['history'] as $i => $history):?>
                <tr>
                  <?php 
                    $total_qty[]        = $history['quantity'];
                    $total[]            = $history['total'];
                    $total_qty_poe[]    = $history['poe_qty'];
                    $total_value_poe[]  = $history['poe_value'];
                    $total_qty_po[]     = $history['po_qty'];
                    $total_value_po[]   = $history['po_value'];
                    $total_qty_grn[]    = $history['grn_qty'];
                    $total_value_grn[]  = $history['grn_value'];
                  ?>
                  <td></td>
                  <td>
                    <?=print_date($history['pr_date']);?>
                  </td>
                  <td>
                    <?=print_string($history['pr_number']);?>
                  </td>
                  <td align="right">
                    <?=print_number($history['quantity'], 2);?>
                  </td>
                  <td>
                    <?=print_string($detail['unit']);?>
                  </td>
                  <td align="right">
                    <?=print_number($history['price'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_number($history['total'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_number($history['poe_qty'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_number($history['poe_value'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_number($history['po_qty'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_number($history['po_value'], 2);?>
                  </td>     
                  <td align="right">
                    <?=print_number($history['grn_qty'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_number($history['grn_value'], 2);?>
                  </td>                     
                </tr>                
                <?php endforeach;?>
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
                <th><?=print_number(array_sum($total_qty_po), 2);?></th>
                <th><?=print_number(array_sum($total_value_poe), 2);?></th>
                <th><?=print_number(array_sum($total_qty_po), 2);?></th>
                <th><?=print_number(array_sum($total_value_po), 2);?></th>
                <th><?=print_number(array_sum($total_qty_grn), 2);?></th>
                <th><?=print_number(array_sum($total_value_grn), 2);?></th>
                <!-- <th></th> -->
              </tr>
            </tfoot>
          </table>
        </div>
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

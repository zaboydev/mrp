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
            <div class="pull-left">DOCUMENT NO. : </div>
            <div class="pull-right"><?=print_string($entity['pr_number']);?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">PR DATE : </div>
            <div class="pull-right"><?=print_date($entity['pr_date']);?></div>
          </div>
          <!--  -->
          <div class="clearfix">
            <div class="pull-left">COST CENTER : </div>
            <div class="pull-right"><?=print_string($entity['cost_center_name']);?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">HEAD DEP : </div>
            <div class="pull-right"><?=print_string($entity['head_dept']);?></div>
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
          <?php elseif($entity['status']=='WAITING FOR BUDGETCONTROL'):?>
          <dd> WAITING FOR BUDGETCONTROL</dd>
          <?php endif;?>

          <dt>Suggested Supplier</dt>
          <dd><?=($entity['suggested_supplier']==null)? 'N/A':print_string($entity['suggested_supplier']);?></dd>

          <dt>Deliver To</dt>
          <dd><?=($entity['deliver_to']==null)? 'N/A':print_string($entity['deliver_to']);?></dd>

          <dt>Notes</dt>
          <dd><?=$entity['notes'];?></dd>
          <dd>
          <?php if ($entity['with_po']=='f'):?>
            Capex Request ini merupakan expense request tanpa PO.
          <?php endif;?>
          <?php if ($entity['with_po']=='t'):?>
            Capex Request ini merupakan expense request dengan PO.
          <?php endif;?>
          </dd>
        </dl>
      </div>
    </div>

    <div class="row" id="document_details">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-striped table-nowrap">
            <thead id="table_header">
              <th align="right" width="1">No</th>
              <th>Description</th>
              <th>Part Number</th>
              <th align="right">Qty</th>
              <th width="1">Unit</th>
              <th align="right">Price</th>
              <th align="right">Total</th>
              <th align="right">Balance Quantity Month to Date</th>
              <th align="right">Balance Budget Month to Date</th>
              <th align="right">Balance Quantity Year to Date</th>
              <th align="right">Balance Budget Year to Date</th>
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
                    <?=print_number($detail['balance_mtd_quantity'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_number($detail['balance_mtd_budget'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_number($detail['balance_ytd_quantity'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_number($detail['balance_ytd_budget'], 2);?>
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
    <div class="pull-left">
      <a href="<?= site_url($module['route'] . '/manage_attachment/' . $entity['id']); ?>" onClick="return popup(this, 'attachment')" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction">
        <i class="md md-attach-file"></i>
        <small class="top right">Manage Attachment</small>
      </a>
    </div>
    <div class="pull-right">
      <?php if (is_granted($module, 'document_change')):?>
        <?php if ($entity['status']!='rejected' && $entity['status']!='canceled' && $entity['status']!='revisi' && $entity['status']!='close'):?>
        <?=form_open(current_url(), array(
            'class' => 'form-xhr-change pull-left',
          ));?>
          <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">
          <input type="hidden" name="change_notes" id="change_notes" class="form-control">

          <a data-type-po="<?=$entity['with_po']?>" href="<?=site_url($module['route'] .'/change_ajax/');?>" class="btn btn-floating-action btn-danger btn-xhr-change btn-tooltip ink-reaction" id="modal-cancel-data-button">
            <!-- <i class="md md-shuffle"></i> -->
            <i class="md md-swap-horiz"></i>
            <small class="top left">Change Type PO</small>
          </a>
        <?=form_close();?>
        <?php endif;?>
      <?php endif;?>
      <?php if ($entity['with_po'] == 'f'):?>
        <?php if ($entity['status'] == 'approved'):?>
        <?php if (is_granted($module, 'payment')):?>
        <a href="<?= site_url($modules['capex_closing_payment']['route'] . '/create/' . $entity['id']); ?>" class="hide btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-approval-data-button">
          <i class="md md-check"></i>
          <small class="top right">Closing Request</small>
        </a>
        <?php endif;?>
        <?php endif;?>
      <?php endif;?>

      <?php if (is_granted($module, 'document')):?>
        <?php if ($entity['status']=='rejected' || $entity['status']=='pending'):?>
        <a href="<?=site_url($module['route'] .'/edit/'. $entity['id']);?>" class="hide btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
          <i class="md md-edit"></i>
          <small class="top right">edit</small>
        </a>
        <?php endif;?>
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

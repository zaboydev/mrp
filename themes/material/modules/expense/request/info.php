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
            <div class="pull-left">DOCUMENT  NO.: </div>
            <div class="pull-right"><?=print_string($entity['pr_number']);?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">PR DATE: </div>
            <div class="pull-right"><?=print_date($entity['pr_date']);?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">BASE: </div>
            <div class="pull-right"><?=print_string($entity['base']);?></div>
          </div>
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
          <dd><?=strtoupper($entity['status']);?> 
          <?php if($entity['status']=='rejected'):?> by <?=$entity['rejected_by']?> at <?=print_date($entity['rejected_date'], 'd M Y')?><?php endif;?>
          <?php if($entity['status']=='canceled'):?> by <?=$entity['canceled_by']?> at <?=print_date($entity['canceled_date'], 'd M Y')?><?php endif;?>
          </dd>

          <dt>Head Dept</dt>
          <dd><?=($entity['head_dept']==null)? 'N/A':print_string($entity['head_dept']);?></dd>

          <dt>Suggested Supplier</dt>
          <dd><?=($entity['suggested_supplier']==null)? 'N/A':print_string($entity['suggested_supplier']);?></dd>

          <dt>Deliver To</dt>
          <dd><?=($entity['deliver_to']==null)? 'N/A':print_string($entity['deliver_to']);?></dd>

          <dt>Notes</dt>
          <dd><?=($entity['notes']==null)? 'N/A':print_string($entity['notes']);?></dd>
          <?php if($entity['status']=='rejected'||$entity['status']=='canceled'):?>
          <dt><?= ucwords($entity['status']);?> Notes</dt>
          <dd><?=($entity['status']=='rejected')? print_string($entity['rejected_notes']):print_string($entity['canceled_notes']);?></dd>
          <?php endif;?>
          <dd>
          <?php if ($entity['with_po']=='f'):?>
            Expense Request ini merupakan expense request tanpa PO.
          <?php endif;?>
          <?php if ($entity['with_po']=='t'):?>
            Expense Request ini merupakan expense request dengan PO.
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
              <th>No</th>
              <th>Account Name</th>
              <th>Account Code</th>
              <th>Amount</th>
              <th>Total</th>
              <th>Balance Budget Month to Date</th>
              <th>Balance Budget Year to Date</th>
              <th>Reference IPC</th>
            </thead>
            <tbody id="table_contents">
              <?php $n = 0; $open=0;?>
              <?php $total = array();?>
              <?php foreach ($entity['items'] as $i => $detail):?>
                <?php 
                  $n++;
                  $total[] = $detail['total'];
                ?>
                <tr>
                  <td align="right">
                    <?=print_number($n);?>
                  </td>
                  <td>
                    <?=print_string($detail['account_name']);?>
                  </td>
                  <td>
                    <?=print_string($detail['account_code']);?>
                  </td>
                  
                  <td align="right">
                    <?=print_number($detail['amount'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_number($detail['total'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_number($detail['balance_mtd_budget'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_number($detail['balance_ytd_budget'], 2);?>
                  </td>
                  <td align="right">
                    <?=print_string($detail['reference_ipc']);?>
                  </td>
                  
                </tr>
              <?php endforeach;?>
            </tbody>
            <tfoot>
              <tr>
                <th>Total</th>
                <th></th>
                <th></th>
                <th></th>
                <th><?=print_number(array_sum($total), 2);?></th>
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
              <th style="text-align: center;">No</th>
              <th style="text-align: center;">Tanggal</th>
              <th style="text-align: center;">Purchase Number</th>
              <th style="text-align: center;">Amount</th>
              <th style="text-align: center;">Total</th>
              <th style="text-align: center;">Created By</th>
            </thead>
            <tbody id="table_contents">
              <?php $n = 0;?>
              
              <?php foreach ($entity['items'] as $i => $detail):?>
                <?php 
                  $n++;
                ?>
                <tr>
                  <td style="text-align: center;">
                    <?=print_number($n);?>
                  </td>
                  <td colspan="7">
                    (<?=print_string($detail['account_code']);?>) <?=print_string($detail['account_name']);?>
                  </td>
                </tr><?php $total = array();?>
                <?php foreach ($detail['history'] as $i => $history):?>
                <tr>
                  <?php 
                    $total[] = $history['total'];
                    ?>
                  <td></td>
                  <td>
                    <?=print_date($history['pr_date']);?>
                  </td>
                  <td>
                    <?=print_string($history['pr_number']);?>
                  </td>
                  <td style="text-align: right;">
                    <?=print_number($history['amount'], 2);?>
                  </td>
                  <td style="text-align: right;">
                    <?=print_number($history['total'], 2);?>
                  </td>
                  <td style="text-align: center;">
                    <?=print_string($history['created_by'], 2);?>
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
                <th></th>
                <th><?=print_number(array_sum($total), 2);?></th>
                <th></th>
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
      <?php if (is_granted($module, 'cancel') && $entity['status']=='approved') : ?>
      <?php if ($entity['cancel']) : ?>
      <?=form_open(current_url(), array(
        'class' => 'form-xhr-cancel pull-left',
      ));?>
        <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">
        <input type="hidden" name="cancel_notes" id="cancel_notes" class="form-control">

        <a href="<?=site_url($module['route'] .'/cancel_ajax/');?>" class="btn btn-floating-action btn-danger btn-xhr-cancel btn-tooltip ink-reaction" id="modal-cancel-data-button">
          <i class="md md-close"></i>
          <small class="top left">Cancel</small>
        </a>
      <?=form_close();?>
      <?php endif; ?>
      <?php endif; ?>
    </div>
    <div class="pull-right">
      <?php if (is_granted($module, 'document_change')):?>
        <?php if ($entity['status']!='rejected' && $entity['status']!='canceled' && $entity['status']!='revisi' && $entity['status']!='close'):?>
        <?php if ($entity['cancel']) : ?>
        <?=form_open(current_url(), array(
            'class' => 'form-xhr-change pull-left',
          ));?>
          <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">
          <input type="hidden" name="change_notes" id="change_notes" class="form-control">

          <a data-type-po="<?=$entity['with_po']?>" href="<?=site_url($module['route'] .'/change_ajax/');?>" class="btn btn-floating-action btn-danger btn-xhr-change btn-tooltip ink-reaction" id="modal-change-data-button">
            <!-- <i class="md md-shuffle"></i> -->
            <i class="md md-swap-horiz"></i>
            <small class="top left">Change Type PO</small>
          </a>
        <?=form_close();?>
        <?php endif;?>
        <?php endif;?>
      <?php endif;?>
      <?php if (is_granted($module, 'document')):?>
        <?php if ($entity['status']=='rejected' && $entity['revisi']==0):?>
        <a href="<?=site_url($module['route'] .'/edit/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
          <i class="md md-edit"></i>
          <small class="top right">Revisi</small>
        </a>
        <?php endif;?>
      <?php endif;?>
       <?php if (is_granted($module, 'document') && $open==0 && $entity['rejected_date']==null):?>
        <!-- <a href="<?=site_url($module['route'] .'/cancel/'. $entity['id']);?>" class="btn btn-floating-action btn-danger btn-tooltip ink-reaction" id="modal-edit-data-button">
          <i class="md md-cancel"></i>
          <small class="top right">Cancel</small>
        </a> -->
      <?php endif;?>
      <?php if ($entity['with_po'] == 'f'):?>
        <?php if ($entity['status'] == 'approved'):?>
        <?php if (is_granted($module, 'payment')):?>
        <a href="<?= site_url($modules['expense_closing_payment']['route'] . '/closing_payment/' . $entity['id']); ?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-approval-data-button">
          <i class="md md-check"></i>
          <small class="top right">Closing Request</small>
        </a>
        <?php endif;?>
        <?php endif;?>
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

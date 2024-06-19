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
            <div class="pull-right"><?=print_string($entity['document_number']);?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">DATE: </div>
            <div class="pull-right"><?=print_date($entity['date']);?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">BASE: </div>
            <div class="pull-right"><?=print_string($entity['warehouse']);?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">COST CENTER: </div>
            <div class="pull-right"><?=print_string($entity['cost_center_name']);?></div>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-8 col-md-pull-4">
        <dl class="dl-inline">
            <dt>Status</dt>
            <dd><?=$entity['status'];?></dd>

            <?php if($entity['type']=='expense' && $entity['reference_document']!=null):?>
            <dt>Exp. No</dt>
            <dd><a href="<?=$entity['url_expense'];?>"><?=$entity['pr_number'];?></a></dd>
            <?php endif;?>

            <?php if($entity['status']=='REJECTED'):?>
              <dt>Rejected By</dt>
              <dd><?=($entity['rejected_by']==null)? 'N/A':print_string($entity['rejected_by']);?></dd>
            <?php else:?>
              <dt>Known By Head Dept</dt>
              <dd><?=($entity['known_by']==null)? 'N/A':print_string($entity['known_by']);?></dd>

              <dt>Approved By HR</dt>
              <dd><?=($entity['approved_by']==null)? 'N/A':print_string($entity['approved_by']);?></dd>
            <?php endif;?>

            <dt>Supervisor/Atasan</dt>
            <dd><?=$entity['head_dept_name'];?></dd>

            <dt>Name/Nama</dt>
            <dd><?=$entity['person_name'];?></dd>

            <dt>Occupation/Jabatan</dt>
            <dd><?=$entity['occupation'];?></dd>

            <dt>From / Kota Asal</dt>
            <dd><?=$entity['from_base'];?></dd>

            <dt>Destination / Kota Tujuan</dt>
            <dd><?=strtoupper($entity['business_trip_destination']);?></dd>

            <dt>Duration / Lama Perjalanan</dt>
            <dd><?=$entity['duration'];?> days from <?= print_date($entity['start_date'],'d M Y')?> to <?= print_date($entity['end_date'],'d M Y')?></dd>

            <dt>Notes</dt>
            <dd><?=$entity['notes'];?></dd>

            <dt>Requested By</dt>
            <dd><?=strtoupper($entity['request_by']);?></dd>

            <dt>Payment Status</dt>
            <dd><?=$entity['payment_status'];?> <?= ($entity['paid_at'])? 'at '.print_date($entity['paid_at'],'d M Y'): '';?></dd> 
        </dl>
      </div>
    </div>

    <?php if (is_granted($module, 'approval')):?>
    <div class="row" id="document_details">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-striped table-nowrap">
            <thead id="table_header">
              <tr>
                <th>No</th>
                <th>Budget Description</th>
                <th style="text-align:center;">Days</th>
                <th style="text-align:right;">Amount</th>
                <th style="text-align:right;">Total</th>
              </tr>
            </thead>
            <tbody id="table_contents">
              <?php $n = 1;?>
              <?php $total = array();?>
              <?php foreach ($entity['items'] as $item) :?>
                <tr>
                  <td><?=$n++;?></td>
                  <td><?=print_string($item['expense_name']);?></td>
                  <td style="text-align:center;"><?=number_format($item['qty']);?></td>
                  <td style="text-align:right;"><?=print_number($item['amount'],2);?></td>
                  <td style="text-align:right;"><?=print_number($item['total'],2)?></td>
                </tr>
                <?php $total[] = $item['total'];?>
              <?php endforeach;?>
            </tbody>
            <tfoot>
              <tr>
                <th>Total</th>
                <th></th>
                <th></th>
                <th></th>
                <th><?=print_number(array_sum($total), 2);?></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
    <p><strong>Note: Ticket tidak termasuk</strong></p>
    <?php endif;?>    
  </div>

    <div class="card-foot">
        <?php
            $today    = date('Y-m-d');
            $date     = strtotime('-20 day',strtotime($today));
            $data     = date('Y-m-d',$date);
        ?>
        
        <?php if (is_granted($module, 'delete') && $entity['date'] >= $data):?>
        <?=form_open(current_url(), array(
            'class' => 'form-xhr pull-left',
        ));?>
        <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">

        <a href="<?=site_url($module['route'] .'/delete_ajax/');?>" class="btn btn-floating-action btn-danger btn-xhr-delete btn-tooltip ink-reaction" id="modal-delete-data-button">
            <i class="md md-delete"></i>
            <small class="top left">delete</small>
        </a>
        <?=form_close();?>
        <?php endif;?>
        <a href="<?= site_url($module['route'] . '/manage_attachment/' . $entity['id']); ?>" onClick="return popup(this, 'attachment')" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction">
            <i class="md md-attach-file"></i>
            <small class="top right">Manage Attachment</small>
        </a>

        <div class="pull-right">
            <?php if (is_granted($module, 'create') && $entity['date'] >= $data && in_array($entity['status'],['WAITING APPROVAL BY HEAD DEPT','REJECTED'])):?>
            <a href="<?=site_url($module['route'] .'/edit/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
                <i class="md md-edit"></i>
                <small class="top right">edit</small>
            </a>
            <?php endif;?>

            <?php if (is_granted($module, 'approval') && $entity['status']=='WAITING APPROVAL BY HEAD DEPT' && $entity['head_dept']==config_item('auth_username')):?>
            <a href="<?=site_url($module['route'] .'/edit_approve/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
                <!-- <i class="material-symbols-outlined">edit_square</i> -->
                <i class="md md-border-color"></i>
                <small class="top right">Edit</small>
            </a>
            <?php endif;?>
            <?php if (is_granted($module, 'approval') && $entity['status']=='WAITING APPROVAL BY HR MANAGER'):?>
            <a href="<?=site_url($module['route'] .'/hr_approve/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
                <i class="md md-spellcheck"></i>
                <small class="top right">HR Approve</small>
            </a>
            <?php endif;?>
        

            <?php if (is_granted($module, 'print')):?>
            <a href="<?=site_url($module['route'] .'/print_pdf/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" target="_blank" id="modal-print-data-button">
                <i class="md md-print"></i>
                <small class="top right">print</small>
            </a>
            <?php endif;?>
            <?php if ($entity['status']=='APPROVED' && in_array(11,config_item('auth_department_id'))):?>
            <?=form_open(current_url(), array(
                'class' => 'form-xhr-create-expense pull-left',
            ));?>
            <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">

            <a href="<?=site_url($module['route'] .'/create_expense_ajax/');?>" class="btn btn-floating-action btn-primary btn-xhr-create-expense btn-tooltip ink-reaction" id="btn-xhr-create-expense">
                <i class="fa fa-money"></i>
                <small class="top left">Create Expense</small>
            </a>
            <?=form_close();?>
            <?php endif;?>

            <?php if (is_granted($module, 'create') && $entity['status']=='APPROVED'):?>
            <a href="<?=site_url($module['route'] .'/extend/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
                <!-- <i class="material-symbols-outlined">edit_square</i> -->
                <i class="md md-border-color"></i>
                <small class="top right">Extend</small>
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

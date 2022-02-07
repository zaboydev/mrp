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
            <div class="pull-left">TRANSACTION NO : </div>
            <div class="pull-right"><?= print_string($entity['document_number']); ?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left"> DATE: </div>
            <div class="pull-right"><?= print_date($entity['tanggal']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-8 col-md-pull-4">
        <dl class="dl-inline">
          <dt>Status</dt>
          <dd>
          <?php if($entity['status']=='PAID'):?>
          PAID by <?=$entity['paid_by']?> at <?= print_date($entity['paid_at'],'d/m/Y'); ?>
          <?php endif;?>
          <?php if($entity['status']=='APPROVED'):?>
          WAITING PAYMENT
          <?php endif;?>
          <?php if($entity['status']=='REJECTED'):?>
          REJECTED by <?= $entity['rejected_by']; ?> at <?= print_date($entity['rejected_at'],'d/m/Y'); ?>
          <?php endif;?>
          <?php if($entity['status']!='APPROVED' && $entity['status']!='PAID' && $entity['status']!='REJECTED'):?>
          Purpose Review
          <?php endif;?>
          </dd>

          <dt>Request By</dt>
          <dd><?= $entity['request_by']; ?></dd>

          <dt>Cash Account</dt>
          <dd><?= $entity['cash_account_code']; ?> <?= $entity['cash_account_name']; ?> </dd>

          <dt>Request Amount</dt>
          <dd><?= print_number_left($entity['request_amount'],2); ?></dd>

          <?php if($entity['status']=='PAID'):?>
          <dt>Paid Account</dt>
          <?php else: ?>
          <dt>Paid Account</dt>
          <?php endif;?>
          <dd> <?= ($entity['coa_kredit']!='')? '('.$entity['coa_kredit'].')':'n/b'; ?> <?= $entity['akun_kredit']; ?></dd>
          
          <?php if($entity['status']=='PAID'):?>
          <dt>No Konfirmasi</dt>
          <dd><?= ($entity['no_konfirmasi']!='')? $entity['no_konfirmasi']:'-'; ?></dd>

          <?php endif;?>

          <dt>Notes</dt>
          <dd><?= $entity['notes']; ?></dd>
        </dl>
      </div>
    </div>
    <div class="row" id="document_details">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-striped table-nowrap">
            <thead id="table_header">
              <th>No</th>
              <th>No. Transaction</th>
              <th>Date</th>
              <th>Vendor</th>
              <th>Amount</th>
            </thead>
            <tbody id="table_contents">
              <?php $n = 0; $total_amount = array();?>
              <?php foreach ($entity['items'] as $i => $detail):?>
                <?php 
                  $n++;
                  $total_amount[] = $detail['amount'];
                ?>
                <tr>
                  <td align="right">
                    <?=print_number($n);?>
                  </td>
                  <td>
                    <?=print_string($detail['no_transaksi']);?>
                  </td>
                  <td>
                    <?=print_date($detail['date']);?>
                  </td>
                  <td>
                    <?=print_string($detail['vendor']);?>
                  </td>
                  <td align="right">
                    <?=print_number($detail['amount'], 2);?>
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
                <th><?=print_number(array_sum($total_amount), 2);?></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      
    </div>
  </div>
  <?php
      $today    = date('Y-m-d');
      $date     = strtotime('-2 day',strtotime($today));
      $data     = date('Y-m-d',$date);
  ?>
  <div class="card-foot">
    <div class="pull-left">
      <a href="<?= site_url($module['route'] . '/manage_attachment/' . $id); ?>" onClick="return popup(this, 'attachment')" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction">
        <i class="md md-attach-file"></i>
        <small class="top right">Manage Attachment</small>
      </a>
      <?php if (is_granted($module, 'cancel')) : ?>
      <?php if ($entity['status']!='PAID' && $entity['status']!='APPROVED' && $entity['status']!='REJECTED' && $entity['status']!='CANCELED' && $entity['status']!='REVISI') : ?>
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
      <?php if ($entity['revisi']=='t') : ?>        
        <?php if ($entity['status'] != 'PAID' && $entity['status'] != 'APPROVED' && $entity['status'] != 'REVISI') : ?>
          <?php if (is_granted($module, 'document')) : ?>
            <a href="<?= site_url($module['route'] . '/edit/' . $id); ?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
              <i class="md md-edit"></i>
              <small class="top right">edit</small>
            </a>
          <?php endif;  ?>
        <?php endif;  ?>
      <?php endif;  ?>
      <?php if (is_granted($module, 'payment') && $entity['status'] == 'APPROVED') : ?>
        <a href="<?= site_url($module['route'] . '/bayar/' . $id); ?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-payment-data-button">
          <i class="md md-attach-money"></i>
          <small class="top right">payment</small>
        </a>
      <?php endif; ?>
      <a href="<?=site_url($module['route'] .'/print_pdf/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" target="_blank" id="modal-print-data-button">
        <i class="md md-print"></i>
        <small class="top right">print</small>
      </a>
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
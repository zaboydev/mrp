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
            <div class="pull-right"><?= print_string($entity['no_transaksi']); ?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left"> DATE: </div>
            <div class="pull-right"><?= print_date($entity['tanggal']); ?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left"> PURPOSED DATE: </div>
            <div class="pull-right"><?= print_date($entity['purposed_date']); ?></div>
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
          <?php if($entity['status']!='APPROVED' && $entity['status']!='PAID'):?>
          Purpose Review
          <?php endif;?>
          </dd>

          <dt>Payment To</dt>
          <dd><?= $entity['vendor']; ?></dd>

          <dt>Created By</dt>
          <dd><?= $entity['created_by']; ?></dd>

          <dt>Currency</dt>
          <dd><?= $entity['currency']; ?></dd>

          <dt>Transaction By</dt>
          <dd><?= ($entity['type']=='BANK')? 'BANK TRANSFER':'CASH';?></dd>

          <?php if($entity['status']=='PAID'):?>
          <dt>Account</dt>
          <?php else: ?>
          <dt>Request Selected Account</dt>
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
              <tr>
                <th>No</th>
                <th>PO#</th>
                <th>Att Invoice/Other</th>
                <th>Due Date</th>
                <th>Currency</th>
                <th>POE#</th>
                <th>Request Number</th>
                <th align="right">Qty Request Payment</th>
                <th align="right">Amount Request Payment</th>
              </tr>
            </thead>
            <tbody id="table_contents">
              <?php $n = 0; ?>
              <?php $amount_paid = array(); ?>
              <?php foreach ($entity['po'] as $i => $detail) : ?>
                <?php $n++; ?>
                <tr>
                  <td class="no-space">
                    <?= print_number($n); ?>
                  </td>
                  <td>
                    <a  href="javascript:;" title="View Detail PO" class="btn btn-icon-toggle btn-info btn-xs btn_view_detail" id="btn_<? $n ?>" data-row="<?= $n ?>" data-tipe="view"><i class="fa fa-angle-right"></i>
                    </a>
                    <a class="link" href="<?= site_url('payment/print_po/' . $detail['id_po'].'/'.$detail['tipe_po']) ?>" target="_blank"><?=print_string($detail['document_number'])?></a>
                    <span style="display:block;font-size:10px;font-style:italic;"><?= print_string($detail['tipe']); ?></span>
                  </td>                  
                  <td>
                  <?php if($detail['id_po']!=0 && $detail['id_po']!=null):?>
                  <?php //if(isAttachementExists($detail['id_po'],'PO')):?>
                    <a href="<?= site_url('purchase_order/manage_attachment/' . $detail['id_po'].'/payment'); ?>" onClick="return popup(this, 'attachment')" data-id="<?=$grn['id']?>" class="btn btn-icon-toggle btn-info btn-sm btn-show-att-grn">
                      <i class="fa fa-eye"></i>
                    </a>
                  <?php //endif;?>
                  <?php endif;?>
                  </td>
                  <td>
                    <?php if($detail['due_date']!=null):?>
                    <?= print_date($detail['due_date'],'d/m/Y'); ?>
                    <?php endif;?>
                  </td>
                  <td>
                    <?= print_string($entity['currency']); ?>
                  </td>
                  <td>
                    <?php if($detail['poe_number']!=null):?>
                    <a class="link" href="<?= site_url('payment/print_poe/' . $detail['poe_id'].'/'.$detail['poe_type']) ?>" target="_blank"><?=print_string($detail['poe_number'])?></a>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if($detail['request_number']!=null):?>
                    <a class="link" href="<?= site_url('payment/print_prl/' . $detail['request_id'].'/'.$detail['tipe_po']) ?>" target="_blank"><?=print_string($detail['request_number'])?></a>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?= print_number($detail['quantity_paid'], 2); ?>
                  </td>
                  <td>
                    <?= print_number($detail['amount_paid'], 2); ?>
                    <?php $amount_paid[] = $detail['amount_paid']; ?>
                  </td>
                </tr>
                <?php foreach ($detail['items'] as $j => $item) : ?>
                
                <tr class="detail_<?=$n?> hide">                  
                  <td></td>
                  <td colspan="4">
                    <?= print_string($item['description']); ?>
                  </td>
                  <td>
                    <?php if($item['poe_number']!=null):?>
                    <a class="link" href="<?= site_url('payment/print_poe/' . $item['poe_id'].'/'.$item['poe_type']) ?>" target="_blank"><?=print_string($item['poe_number'])?></a>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if($item['request_number']!=null):?>
                    <a class="link" href="<?= site_url('payment/print_prl/' . $item['request_id'].'/'.$item['tipe_po']) ?>" target="_blank"><?=print_string($item['request_number'])?></a>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?= print_number($item['quantity_paid'], 2); ?>
                  </td>
                  <td>
                    <?= print_number($item['amount_paid'], 2); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <th></th>
                <th>Total</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <!-- <th></th> -->
                <th><?= print_number(array_sum($amount_paid), 2); ?></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <?php if($entity['status']=='PAID'):?>
      <div class="col-sm-12">
        <h3>Jurnal</h3>
        <div class="table-responsive">
          <table class="table table-striped table-nowrap">
            <thead id="table_header">
              <tr>
                <th>No</th>
                <th>Account</th>
                <th>Debit</th>
                <th>Kredit</th>
              </tr>
            </thead>
            <tbody id="table_contents">
              <?php $n = 0; ?>
              <?php $totalDebet = array(); $totalKredit = array();?>
              <?php foreach ($entity['jurnalDetail'] as $i => $jurnal) : ?>
                <?php $n++; ?>
                <tr>
                  <td class="no-space">
                    <?= print_number($n); ?>
                  </td>
                  <td>
                    <?= print_string($jurnal['kode_rekening'])?> - <?= print_string($jurnal['jenis_transaksi'])?>
                  </td> 
                  <td>
                    <?= print_number($jurnal['trs_debet'], 2); ?>
                  </td>
                  <td>
                    <?= print_number($jurnal['trs_kredit'], 2); ?>
                  </td>
                  <?php 
                    $totalDebet[] = $jurnal['trs_debet'];
                    $totalKredit[] = $jurnal['trs_kredit'];
                  ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="2">Total</th>
                <th><?= print_number(array_sum($totalDebet), 2); ?></th>
                <th><?= print_number(array_sum($totalKredit), 2); ?></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <?php endif;?>
      <div class="col-sm-12">
        <h3>History Payment Request</h3>
        <div class="table-responsive">
          <table class="table table-striped table-nowrap">
            <thead id="table_header">
              <th style="text-align: center;">No</th>
              <th style="text-align: center;">Tanggal</th>
              <th style="text-align: center;">Purpose Payment Number</th>
              <th style="text-align: center;">Currency</th>
              <th style="text-align: center;">Amount</th>
              <th style="text-align: center;">Status</th>
            </thead>
            <tbody id="table_contents">
              <?php $n = 0;$grandtotal = array();?>              
              <?php foreach ($entity['po'] as $i => $item):?>
              <?php foreach ($item['items'] as $j => $detail) : ?>
                <?php 
                  $n++;                  
                ?>
                <tr>
                  <td style="text-align: center;">
                    <?=print_number($n);?>
                  </td>
                  <td colspan="7">
                    <?=print_string($detail['description']);?> <?php if($detail['id_po']!=0 && $detail['id_po']!=null):?> - <?=print_string($detail['document_number']);?> <?php endif; ?>
                  </td>
                </tr><?php $total = array();?>
                <?php if(count($detail['history'])>0):?>
                
                <?php foreach ($detail['history'] as $i => $history):?>
                <tr>
                  <?php 
                    $total[] = $history['amount_paid'];
                    $grandtotal[] = $history['amount_paid'];
                  ?>
                  <td></td>
                  <td style="text-align: center;">
                    <?=print_date($history['tanggal']);?>
                  </td>
                  <td style="text-align: center;">
                    <?=print_string($history['document_number']);?>
                  </td>
                  <td style="text-align: center;">
                    <?=print_string($history['currency']);?>
                  </td>
                  <td style="text-align: right;">
                    <?=print_number($history['amount_paid'], 2);?>
                  </td>
                  <td style="text-align: center;">
                    <?=print_string($history['status']);?>
                  </td>                  
                </tr>                
                <?php endforeach;?> 
                <?php else: ?> 
                <tr>
                <td colspan="6" style="text-align:center;">No Historical Payment</td>
                </tr>
                <?php endif;?> 
              <?php endforeach;?>              
              <?php endforeach;?>
            </tbody>
            <tfoot>
              <tr>
                <th>Total</th>
                <th></th>
                <th></th>
                <th></th>
                <th><?=print_number(array_sum($grandtotal), 2);?></th>
                <th></th>
                <!-- <th></th> -->
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="col-sm-12">
        <h3>GRN INFO</h3>
        <div class="table-responsive">
          <table class="table table-striped table-nowrap">
            <thead id="table_header">
              <th style="text-align: center;">No</th>
              <th style="text-align: center;">PO#</th>
              <th style="text-align: center;">Att</th>
              <th style="text-align: center;">P/N</th>
              <th style="text-align: center;">Desc</th>
              <th style="text-align: center;">Qty Order</th>
              <th style="text-align: center;">Value Order</th>
              <th style="text-align: center;">Qty Receipt</th>
              <th style="text-align: center;">Value Receipt</th>
              <th style="text-align: center;">Left Received Qty</th>
              <th style="text-align: center;">Left Received Value</th>
              <th style="text-align: center;">Over Received Qty</th>
              <th style="text-align: center;">Over Received Value</th>
            </thead>
            <tbody id="table_contents">
              <?php 
                $n = 0;
                $total_quantity_order     = array();
                $total_value_order        = array();
                $total_quantity_receipt   = array();
                $total_value_receipt      = array();
                $total_quantity_remaining = array();
                $total_value_remaining    = array();
                $total_quantity_over      = array();
                $total_value_over         = array();
              ?>              
              <?php foreach ($entity['po'] as $i => $item):?>
              <?php foreach ($item['items'] as $j => $detail) : ?>
                <?php 
                  $n++;
                  $total_quantity_order[]     = $detail['item']['quantity'];
                  $total_value_order[]        = $detail['item']['total_amount'];
                  $total_quantity_receipt[]   = $detail['item']['grn_qty'];
                  $total_value_receipt[]      = $detail['item']['grn_qty']*$detail['item']['unit_price'];
                  if($detail['item']['left_received_quantity']>=0){
                    $total_quantity_remaining[] = $detail['item']['left_received_quantity'];
                    $total_value_remaining[]    = $detail['item']['left_received_quantity']*$detail['item']['unit_price'];
                  }else{
                    $total_quantity_over[] = $detail['item']['left_received_quantity']*-1;
                    $total_value_over[]    = $detail['item']['left_received_quantity']*$detail['item']['unit_price']*-1;
                  }
                  
                ?>
                <tr>
                  <td style="text-align: center;">
                    <?=print_number($n);?>
                  </td>
                  <td colspan="2">
                    <?= print_string($detail['document_number']); ?>
                  </td>
                  <td>
                    <?= print_string($detail['item']['part_number']); ?>
                  </td>
                  <td>
                    <?= print_string($detail['description']); ?>
                  </td>
                  <td>
                    <?= print_number($detail['item']['quantity'], 2); ?>
                  </td>
                  <td>
                    <?= print_number($detail['item']['total_amount'], 2); ?>
                  </td>
                  <td>
                    <?= print_number($detail['item']['grn_qty'], 2); ?>
                  </td>
                  <td>
                    <?= print_number($detail['item']['grn_qty']*$detail['item']['unit_price'], 2); ?>
                  </td>
                  <td>
                    <?= ($detail['item']['left_received_quantity']>=0) ? print_number($detail['item']['left_received_quantity'], 2) : print_number(0,2); ?>
                  </td>
                  <td>
                    <?= ($detail['item']['left_received_quantity']>=0) ? print_number($detail['item']['left_received_quantity']*$detail['item']['unit_price'], 2) : print_number(0,2); ?>
                    
                  </td>
                  <td>
                    <?= ($detail['item']['left_received_quantity']<0) ? print_number($detail['item']['left_received_quantity']*-1, 2) : print_number(0,2); ?>
                  </td>
                  <td>
                    <?= ($detail['item']['left_received_quantity']<0) ? print_number($detail['item']['left_received_quantity']*$detail['item']['unit_price']*-1, 2) : print_number(0,2); ?>
                    
                  </td>
                </tr>
                <?php if(count($detail['item']['grn'])>0):?> 
                <?php foreach ($detail['item']['grn'] as $i => $grn):?>
                <tr>
                  <td></td>
                  <td>
                    <a class="link" href="<?= site_url('goods_received_note/print_pdf/' . $grn['id']) ?>" target="_blank">
                      <?= print_string($grn['document_number']); ?>
                    </a> 
                       
                  </td>
                  <td colspan="5">
                  <?php if(isAttachementExists($grn['id'],'GRN')):?>
                    <a href="<?= site_url('goods_received_note/manage_attachment/' . $grn['id']); ?>" onClick="return popup(this, 'attachment')" data-id="<?=$grn['id']?>" class="btn btn-icon-toggle btn-info btn-sm btn-show-att-grn">
                      <i class="fa fa-eye"></i>
                    </a>
                    <?php endif;?>  
                  </td>
                  <td><?= print_number($grn['quantity_order'], 2); ?></td>
                  <td><?= print_number($grn['quantity_order']*$detail['item']['unit_price'], 2); ?></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>  
                <?php endforeach;?>
                <?php else: ?> 
                <tr>
                <td colspan="13" style="text-align:center;">No GRN Receipt</td>
                </tr>
                <?php endif;?>           
              <?php endforeach;?>           
              <?php endforeach;?>
            </tbody>
            <tfoot>
              <tr>
                <th>Total</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th><?=print_number(array_sum($total_quantity_order), 2);?></th>
                <th><?=print_number(array_sum($total_value_order), 2);?></th>
                <th><?=print_number(array_sum($total_quantity_receipt), 2);?></th>
                <th><?=print_number(array_sum($total_value_receipt), 2);?></th>
                <th><?=print_number(array_sum($total_quantity_remaining), 2);?></th>
                <th><?=print_number(array_sum($total_value_remaining), 2);?></th>
                <th><?=print_number(array_sum($total_quantity_over), 2);?></th>
                <th><?=print_number(array_sum($total_value_over), 2);?></th>
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
      <?php if ($entity['status']!='PAID' && $entity['status']!='APPROVED' && $entity['status']!='REJECTED' && $entity['status']!='CANCELED') : ?>
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
        <?php if ($entity['type']=='CASH') : ?>
          <?php if($entity['tanggal'] >= $data):?>
            <a href="<?= site_url($module['route'] . '/edit/' . $id); ?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
              <i class="md md-edit"></i>
              <small class="top right">edit</small>
            </a>
          <?php endif;  ?>
        <?php else: ?>
          <?php if ($entity['status'] != 'PAID' && $entity['status'] != 'APPROVED' && $entity['status'] != 'REVISI') : ?>
            <?php if (is_granted($module, 'document')) : ?>
              <a href="<?= site_url($module['route'] . '/edit/' . $id); ?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
                <i class="md md-edit"></i>
                <small class="top right">edit</small>
              </a>
            <?php endif;  ?>
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

  //klik icon mata utk lihat item po
  $("#table_contents").on("click", ".btn_view_detail", function() {
    console.log('klik detail');
    var selRow = $(this).data("row");
    var tipe = $(this).data("tipe");
    if (tipe == "view") {
      $(this).data("tipe", "hide");
      $('.detail_' + selRow).removeClass('hide');
    } else {
      $(this).data("tipe", "view");
      $('.detail_' + selRow).addClass('hide');
    }
  })
</script>
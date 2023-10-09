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
                        <div class="pull-left">PAYMENT VOUCHER NO : </div>
                        <div class="pull-right"><?= print_string($entity['payment_number']); ?></div>
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
                    <?php if($entity['status']=='PAID' || $entity['status']=='OPEN'):?>
                    <?=$entity['status']?> by <?=$entity['paid_by']?> at <?= print_date($entity['paid_at'],'d/m/Y'); ?>
                    <?php endif;?>
                    <?php if($entity['status']=='APPROVED'):?>
                    WAITING PAYMENT
                    <?php endif;?>
                    <?php if($entity['status']!='APPROVED' && $entity['status']!='PAID' && $entity['status']!='OPEN'):?>
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

                    <?php if($entity['status']=='PAID' || $entity['status']=='OPEN'):?>
                    <dt>Account</dt>
                    <?php else: ?>
                    <dt>Request Selected Account</dt>
                    <?php endif;?>
                    <dd> <?= ($entity['coa_kredit']!='')? '('.$entity['coa_kredit'].') '.$entity['akun_kredit']:'n/b'; ?> </dd>
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
                                <th>SPD#</th>
                                <th>Date</th>
                                <th>Person in Charge</th>
                                <th style="text-align:center;">Remarks</th>
                                <th style="text-align:right;">Amount Request</th>
                            </tr>
                        </thead>
                        <tbody id="table_contents">
                            <?php $n = 0; ?>
                            <?php $amount_paid = array(); ?>
                            <?php foreach ($entity['request'] as $i => $request) : ?>
                                <?php $n++; ?>
                            <tr>
                                <td class="no-space">
                                    <?= print_number($n); ?>
                                </td>
                                <td>
                                    <a  href="javascript:;" title="View Detail" class="btn btn-icon-toggle btn-info btn-xs btn_view_detail" id="btn_<? $n ?>" data-row="<?= $n ?>" data-tipe="view"><i class="fa fa-angle-right"></i>
                                    </a>
                                    <a class="link" href="<?= site_url('business_trip_request/print_pdf/' . $request['document_id']) ?>" target="_blank"><?=print_string($request['spd_number'])?></a>
                                </td>                  
                                
                                <td>
                                    <?= print_date($request['spd_date']); ?>
                                </td>
                                <td>
                                    <?= print_string($request['spd_person_incharge']); ?>
                                </td>
                                <td>
                                    <?= print_string($request['remarks']); ?>
                                </td>
                                <td>
                                    <?= print_number($request['amount_paid'],2); ?>
                                    <?php $amount_paid[] = $request['amount_paid']; ?>
                                </td>
                            </tr>
                            <?php foreach ($request['items_spd'] as $j => $items_spd) : ?>
                            <tr class="detail_<?=$n?> hide">
                                <td class="no-space">
                                    
                                </td> 
                                <td class="no-space">
                                <?= print_string($items_spd['expense_name']); ?>
                                </td>  
                                <td>
                                    
                                </td>
                                <td>
                                    
                                </td>
                                <td>
                                    
                                </td>
                                <td>
                                    <?= print_number($items_spd['total'],2); ?>
                                </td>   
                            </tr>
                            <?php endforeach; ?>
                                
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="5">Total</th>
                            <th><?= print_number(array_sum($amount_paid), 2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="5">Paid</th>
                            <th><?= print_number($entity['amount_paid'], 2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="5">Left</th>
                            <th><?= print_number((array_sum($amount_paid)-$entity['amount_paid']), 2); ?></th>
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
        
        <?php if (is_granted($module, 'payment') && $entity['status'] == strtoupper('Waiting For Payment')) : ?>
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
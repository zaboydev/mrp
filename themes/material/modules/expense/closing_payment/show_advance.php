<?php include 'themes/material/simple.php' ?>
<?php startblock('body') ?>
<div class="container-fluid">
    <h4 class="page-header"><?=$page['title']?></h4>
    <div class="row">
      <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-nowrap">    
                <thead id="table_header">
                    <tr>
                        <th>No</th>
                        <th>ADV#</th>
                        <th>Payment#</th>
                        <th>Paid at</th>
                        <th>SPD#</th>
                        <th>SPPD#</th>
                        <th align="right">Amount Paid</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entity as $request) : ?>
                    <?php $n++; ?>
                    <tr>
                        <td class="no-space">
                            <?= print_number($n); ?>
                        </td>
                        <td class="no-space">
                            <a class="link" href="<?= site_url('spd_payment/print_pdf/' . $request['id']) ?>" target="_blank"><?=print_string($request['document_number'])?></a>
                        </td>
                        <td class="hide no-space">
                            <?= print_string($request['payment_number']); ?>
                        </td>
                        <td class="no-space">
                            <?= print_date($request['paid_at']); ?>
                        </td>
                        <td class="no-space">
                            <a class="link" href="<?= site_url('business_trip_request/print_pdf/' . $request['spd_id']) ?>" target="_blank"><?=print_string($request['spd_number'])?></a>
                        </td>
                        <td class="no-space">
                            <a class="link" href="<?= site_url('sppd/print_pdf/' . $request['sppd_id']) ?>" target="_blank"><?=print_string($request['sppd_number'])?></a>
                        </td>
                        <td class="no-space">
                            <?= print_number($request['amount_paid'],2); ?>
                        </td>
                        <td class="no-space">
                            <a href="<?= site_url('spd_payment/manage_attachment/'.$request['id']);?>" title="show Attachment advance" class="btn btn-icon-toggle btn-info btn-xs" onClick="return popup(this, 'attachment')"><i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
      </div>
    </div>
    
    <div class="clearfix">
        <button type="button" class="btn btn-default pull-right" onclick="popupClose()">Close</button>
        </div>
    <div class="clearfix"></div>
    <hr>
    <p>
        Material Resource Planning - PT Bali Widya Dirgantara
    </p>
</div>
<?php endblock() ?>

<?php startblock('simple_styles') ?>
<?=link_tag('themes/material/assets/css/theme-default/libs/toastr/toastr.css') ?>
<?php endblock() ?>

<?php startblock('simple_scripts')?>
<?=html_script('themes/material/assets/js/libs/jquery-validation/dist/jquery.validate.min.js') ?>
<?=html_script('themes/material/assets/js/libs/jquery-validation/dist/additional-methods.min.js') ?>
<?=html_script('themes/material/assets/js/libs/toastr/toastr.js') ?>
<script>
$(document).ready(function(){
  

})
</script>
<?php endblock() ?>

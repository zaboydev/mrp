<?php include 'themes/material/simple.php' ?>

<?php startblock('body') ?>
<style>
.table-info {
    width: 100%;
    max-width: 100%;
    margin-bottom: 24px;
}
.table-info > thead > tr > th, .table-info > tbody > tr > th, .table-info > tfoot > tr > th, .table-info > thead > tr > td, .table-info > tbody > tr > td, .table-info > tfoot > tr > td {
    padding: 10px 8px;
    line-height: 1.846153846;
    vertical-align: top;
    /* border-top: 1px solid rgba(189, 193, 193, 0.2); */
}
</style>
<div class="container-fluid">

  <h4 class="page-header">Confirmation Request</h4>

  <form id="form_update_request" class="form form-validate ui-front" role="form" method="post" action="<?= site_url($module['route'] . '/save'); ?>">
    <div class="row">
        <div class="col-sm-4">
            <div class="table-responsive">
                <table class="table-info">
                    <tr>
                        <td style="font-weight:bolder;">Date</td>
                        <td><?=$_SESSION['payment_request']['date'];?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bolder;">Purposed Date</td>
                        <td><?=$_SESSION['payment_request']['purposed_date'];?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="table-responsive">
                <table class="table-info">
                    <tr>
                        <td style="font-weight:bolder;">Currency</td>
                        <td><?=$_SESSION['payment_request']['currency'];?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bolder;">Vendor</td>
                        <td><?=$_SESSION['payment_request']['vendor'];?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bolder;">Total Payment</td>
                        <td><?=$_SESSION['payment_request']['total_amount'];?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="table-responsive">
                <table class="table-info">
                    <tr>
                        <td style="font-weight:bolder;">Account</td>
                        <td><?=$_SESSION['payment_request']['coa_kredit'];?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bolder;">Notes</td>
                        <td><?=$_SESSION['payment_request']['notes'];?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-hover table-bordered table-nowrap" id="listView">
            <thead>
              <tr>
                <th>No.</th>
                <th>PO#</th>
                <th>Description Item</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Received Qty</th>
                <th>Received Val.</th>
                <th>Amount</th>
                <th>Remaining Purposed</th>
                <th>Qty Paid</th>
                <th>Purposed Amount</th>
                <th colspan="2">Adjustment</th>
              </tr>
            </thead>
            <tbody>
            <?php 
                $total_amount = array();
                $total_qty = array();
            ?>
                <?php foreach ($_SESSION['payment_request']['items'] as $id => $request) : ?>
                <?php 
                    $total_amount[] = $request['amount_paid'];
                    $total_qty[]    = $request['qty_paid'];
                ?>
                <tr id="row_<?= $id; ?>">
                    <td>
                        <?= $request['po_id']; ?><?= $request['purchase_order_item_id']; ?>
                    </td>
                    <td>
                        <?= $request['po_number']; ?>
                    </td>
                    <td>
                        <?= $request['deskripsi']; ?>
                    </td>
                    <td class="no-space">
                        <?= $request['status']; ?>
                    </td> 
                    <td>
                        <?= print_date($request['due_date'],'d/m/Y') ?>
                    </td>                 
                    <td class="no-space">
                        <?= print_number($request['quantity_received'],2) ?>
                    </td>
                    <td>
                        <?= print_number($request['amount_received'],2) ?>
                    </td>
                    <td>
                        <?= print_number($request['total_amount'],2) ?>
                    </td>
                    <td>
                        <?= print_number($request['left_paid_request'],2) ?>
                        <input id="sis_item_<?= $id; ?>" type="number" rel="left_paid_request" name="item[<?= $id; ?>][left_paid_request]" value="<?= $request['left_paid_request']; ?>" class="hide form-control sel_applied_item">
                    </td>
                    <td>
                        <?= print_number($request['qty_paid'],2) ?>
                        <input id="in_qty_item_<?= $id; ?>" data-id="<?= $id; ?>" type="number" rel="qty_paid" name="item[<?= $id; ?>][qty_paid]" value="<?= $request['qty_paid']; ?>" class="hide form-control sel_applied_qty_item">
                    </td>
                    <td>
                        <?= print_number($request['amount_paid'],2) ?>
                        <input id="in_item_<?= $id; ?>" data-id="<?= $id; ?>" type="number" rel="amount_paid" name="item[<?= $id; ?>][amount_paid]" value="<?= $request['amount_paid']; ?>" class="hide form-control sel_applied_item">
                    </td>
                    <td class="hide">
                        <input type="checkbox" id="cb_<?= $id ?>" data-row="<?= $id ?>" data-id="<?= $id ?>" name="" style="display: inline;" class="hide check_adj" <?= ($request['adj_value']!=0) ? 'checked' : ''; ?>>
                    </td>
                    <td>
                        <?php if($request['adj_value']!=0):?>
                        <?= print_number($request['adj_value'],2) ?>
                        <?php endif; ?>
                        <input id="in_adj_<?= $id ?>" name="item[<?= $id; ?>][adj_value]" data-parent="<?= $no ?>" data-row="<?= $id ?>" type="hidden" class="<?= ($request['adj_value']==0) ? 'hide' : ''; ?>  form-control sel_applied_adj sel_applied_adj<?= $id ?>" value="<?= $request['adj_value']; ?>" style="display: inline;">
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="9">Total</th>
                    <th><?= print_number(array_sum($total_qty),2) ?></th>
                    <th><?= print_number(array_sum($total_amount),2) ?></th>
                </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <div class="clearfix">
      <div class="pull-right">
        <a href="<?=site_url($module['route'] .'/save');?>" type="button" id="submit_button" class="btn btn-primary">Next</a>
      </div>

      <button type="button" class="btn btn-default" onclick="popupClose()">Cancel</button>
    </div>
  </form>

  <div class="clearfix"></div>
  <hr>

  <p>
    Material Resource Planning - PT Bali Widya Dirgantara
  </p>
</div>
<?php endblock() ?>

<?php startblock('simple_styles') ?>
<?= link_tag('themes/material/assets/css/theme-default/libs/toastr/toastr.css') ?>
<?php endblock() ?>

<?php startblock('simple_scripts') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/jquery.validate.min.js') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/additional-methods.min.js') ?>
<?= html_script('themes/material/assets/js/libs/toastr/toastr.js') ?>
<script>
  $(function() {
    var formDocument = $('#form_update_request');
    $('#submit_button').on('click', function(e){
        e.preventDefault();
        $('#submit_button').attr('disabled', true);

        var url = $(this).attr('href');

        $.post(url, formDocument.serialize(), function(data){
            var obj = $.parseJSON(data);

            if ( obj.success == false ){
                toastr.options.timeOut = 10000;
                toastr.options.positionClass = 'toast-top-right';
                toastr.error(obj.message);
            } else {
                toastr.options.timeOut = 4500;
                toastr.options.closeButton = false;
                toastr.options.progressBar = true;
                toastr.options.positionClass = 'toast-top-right';
                toastr.success(obj.message);

                popupClose();

                window.opener.location.href = '<?=site_url($module['route']);?>';
            }

            $(buttonSubmitDocument).attr('disabled', false);
        });
    });

    $("#listView").on("change", ".sel_applied_item", function() {
        id = $(this).data('id');
        sisa = parseFloat($("#sis_item_" + id).val())
        input = parseFloat($(this).val());
        // alert('sisa '+sisa)
        if (sisa < input) {            
            // alert('request lebih besar dari sisa ')
            var selisih = parseFloat(input)-parseFloat(sisa);
            $("#in_adj_" + id).val(selisih.toFixed(2));
            $("#in_adj_" + id).removeClass('hide');
            if($("#cb_" + id).prop('checked')===false){
                $("#cb_" + id).prop('checked',true);
            }
        }else if (sisa > input) {            
            // alert('request lebih kecil dari sisa ')
            var selisih = parseFloat(input)-parseFloat(sisa);
            $("#in_adj_" + id).val(0);
            if($("#in_adj_" + id).hasClass('hide')===false){
                $("#in_adj_" + id).addClass('hide');
            }
            if($("#cb_" + id).prop('checked')){
                $("#cb_" + id).prop('checked',false);
            }
        }else{
            $("#in_adj_" + id).val(0);
            if($("#in_adj_" + id).hasClass('hide')===false){
                $("#in_adj_" + id).addClass('hide');
            }
        }
    });

    $("#listView").on("change", ".check_adj", function() {
        console.log('checkbox');
        var id = $(this).data('id');
        sisa = parseFloat($("#sis_item_" + id).val())
        input = parseFloat($("#in_item_" + id).val())
        var selisih = parseFloat(input)-parseFloat(sisa)
        if($(this).prop('checked')){
            console.log('checkbox-check');
            $("#in_adj_" + id).val(selisih.toFixed(2));
            $("#in_adj_" + id).removeClass('hide');
        }else{
            console.log('checkbox-uncheck');
            $("#in_adj_" + id).val(0);
            $("#in_adj_" + id).addClass('hide');
        }
    });
  });
</script>
<?php endblock() ?>
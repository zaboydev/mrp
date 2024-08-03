<?php include 'themes/material/simple.php' ?>

<?php startblock('body') ?>
<div class="container-fluid">

    <h4 class="page-header">Update SPD</h4>

    <form id="form_update_request" class="form form-validate ui-front" role="form" method="post" action="<?= site_url($module['route'] . '/update_item'); ?>">
        <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-nowrap">
                    <thead>
                        <tr>                            
                            <th>No</th>
                            <th>SPD#</th>
                            <th>Date</th>
                            <th>Person in Charge</th>
                            <th>Amount</th>
                            <th>Amount Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no=1;
                        ?>
                    <?php foreach ($_SESSION['spd_payment']['items'] as $id => $spd) : ?>
                        <tr id="row_<?= $id; ?>">
                            <td>
                                <?=$no++;?>
                            </td>
                            <td>
                                <input type="text" rel="spd_number" name="spd[<?= $id; ?>][spd_number]" value="<?= $_SESSION['spd_payment']['items'][$id]['spd_number']; ?>" class="form-control">
                                
                            </td>
                            <td>
                                <input type="date" rel="spd_date" name="spd[<?= $id; ?>][spd_date]" value="<?= $_SESSION['spd_payment']['items'][$id]['spd_date']; ?>" class="form-control">
                            </td> 
                            <td>
                                <input type="text" rel="spd_date" name="spd[<?= $id; ?>][spd_person_incharge]" value="<?= $_SESSION['spd_payment']['items'][$id]['spd_person_incharge']; ?>" class="form-control">
                            </td>  
                            <td>
                                <input type="number" rel="spd_date" name="spd[<?= $id; ?>][spd_amount]" value="<?= $_SESSION['spd_payment']['items'][$id]['spd_amount']; ?>" class="form-control">
                            </td>  
                            <td>
                                <input type="number" rel="spd_date" max="<?= $_SESSION['spd_payment']['items'][$id]['spd_amount']; ?>" name="spd[<?= $id; ?>][amount_paid]" value="<?= $_SESSION['spd_payment']['items'][$id]['amount_paid']; ?>" class="form-control">
                            </td>                        
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        </div>

        <div class="clearfix">
        <div class="pull-right">
            <button type="submit" id="submit_button" class="btn btn-primary">Next</button>
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
<style>
  .form-control{
    font-size:12px;
    padding:4.5px 4px;
  }
</style>
<?php endblock() ?>

<?php startblock('simple_scripts') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/jquery.validate.min.js') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/additional-methods.min.js') ?>
<?= html_script('themes/material/assets/js/libs/toastr/toastr.js') ?>
<?= html_script('themes/script/jquery.number.js') ?>
<script>
  $('.number').number(true, 2, '.', ',');
  $(function() {
    $('#submit_button').on('click', function(e) {
      e.preventDefault();

      var button = $(this);
      var form = $('#form_update_request');
      var action = form.attr('action');

      button.prop('disabled', true);

      if (form.valid()) {
        $.post(action, form.serialize()).done(function(data) {
          var obj = $.parseJSON(data);

          if (obj.success == false) {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error(obj.message);
          } else {
            refreshParent();
            popupClose();
          }
        });
      }

      button.prop('disabled', false);
    });
  });
</script>
<?php endblock() ?>
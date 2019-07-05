<?php include 'themes/material/simple.php' ?>

<?php startblock('body') ?>
<div class="container">

  <h4 class="page-header">Receive Item</h4>

  <form id="form_receive_item" class="form form-validate ui-front" role="form" method="post" action="<?=site_url($module['route'] .'/update_item/'. $key);?>">
    <div class="row">
      <div class="col-sm-6">
        <fieldset>
          <div class="form-group">
            <label for="part_number">Part Number</label>
            <input type="text" name="part_number" id="part_number" class="form-control" value="<?=$_SESSION['receive']['items'][$key]['part_number'];?>" readonly>
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <input type="text" name="description" id="description" class="form-control" value="<?=$_SESSION['receive']['items'][$key]['description'];?>" readonly>
          </div>

          <div class="form-group">
            <label for="alternate_part_number">Alt. Part Number</label>
            <input type="text" name="alternate_part_number" id="alternate_part_number" class="form-control" value="<?=$_SESSION['receive']['items'][$key]['alternate_part_number'];?>" readonly>
          </div>

          <div class="form-group">
            <label for="serial_number">Serial Number</label>
            <input type="text" name="serial_number" id="serial_number" class="form-control" value="<?=$_SESSION['receive']['items'][$key]['serial_number'];?>" readonly>
          </div>
        </fieldset>
      </div>

      <div class="col-sm-3">
        <fieldset>
          <div class="form-group">
            <label for="issued_quantity">Qty Sent</label>
            <input type="text" name="issued_quantity" id="issued_quantity" class="form-control" value="<?=$_SESSION['receive']['items'][$key]['issued_quantity'];?>" readonly>
          </div>

          <div class="form-group">
            <label for="unit">Unit</label>
            <input type="text" name="unit" id="unit" class="form-control" value="<?=$_SESSION['receive']['items'][$key]['unit'];?>" readonly>
          </div>

          <div class="form-group">
            <label for="condition">Condition</label>
            <input type="text" name="condition" id="condition" class="form-control" value="<?=$_SESSION['receive']['items'][$key]['condition'];?>" readonly>
          </div>
        </fieldset>
      </div>

      <div class="col-sm-3">
        <fieldset>
          <div class="form-group">
            <label for="quantity">Qty Received</label>
            <input type="text" name="received_quantity" id="received_quantity" class="form-control" value="" required>
          </div>

          <div class="form-group">
            <label for="unit">Unit of Measurement</label>
            <input type="text" name="unit" id="unit" class="form-control" value="<?=$_SESSION['receive']['items'][$key]['unit'];?>" readonly>
          </div>
        </fieldset>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>
                  Vendor
                </th>
                <th>
                  Unit Price
                </th>
                <th>
                  Core Charge
                </th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($_SESSION['receive']['vendors'] as $v => $vendor):?>
                <tr>
                  <th>
                    <?=$vendor;?>
                    <input type="hidden" name="vendor[<?=$v;?>][vendor]" value="<?=$vendor;?>">
                  </th>
                  <th>
                    <input type="text" rel="unit_price" name="vendor[<?=$v;?>][unit_price]" value="<?=$_SESSION['receive']['items'][$key]['vendors'][$v]['unit_price'];?>" class="form-control">
                  </th>
                  <th>
                    <input type="text" name="vendor[<?=$v;?>][core_charge]" value="<?=$_SESSION['receive']['items'][$key]['vendors'][$v]['core_charge'];?>" class="form-control">
                  </th>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="text-right">
      <button type="button" class="btn btn-default" onclick="popupClose()">Cancel</button>
      <button type="submit" id="submit_button" class="btn btn-primary">Update</button>
    </div>
  </form>
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
$(function(){
  $('#submit_button').on('click', function(e){
    e.preventDefault();

    var button  = $(this);
    var form    = $('#form_edit_item');
    var action  = form.attr('action');

    button.prop('disabled', true);

    if (form.valid()){
      $.post( action, form.serialize() ).done( function(data){
        var obj = $.parseJSON(data);

        if ( obj.success == false ){
          toastr.options.timeOut = 10000;
          toastr.options.positionClass = 'toast-top-right';
          toastr.error( obj.info );
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

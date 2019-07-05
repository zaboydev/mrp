<?php include 'themes/material/simple.php' ?>

<?php startblock('body') ?>
<div class="container-fluid">

  <h4 class="page-header">Update Request</h4>

  <form id="form_update_request" class="form form-validate ui-front" role="form" method="post" action="<?=site_url($module['route'] .'/update_request');?>">
    <div class="row">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th class="middle-alignment" rowspan="2">Description</th>
                <th class="middle-alignment" rowspan="2">P/N</th>
                <th class="middle-alignment" rowspan="2">Remarks</th>
                <th class="middle-alignment" rowspan="2">PR Number</th>
                <th class="middle-alignment text-right" rowspan="2">Qty</th>

                <?php foreach ($_SESSION['poe']['vendors'] as $key => $vendor):?>
                  <th class="middle-alignment text-center" colspan="3">
                    <?=$vendor['vendor'];?>
                  </th>
                <?php endforeach;?>
              </tr>

              <tr>
                <?php for ($v = 0; $v < count($_SESSION['poe']['vendors']); $v++):?>
                  <th class="middle-alignment text-center">Alt. P/N</th>
                  <th class="middle-alignment text-center">Unit Price</th>
                  <th class="middle-alignment text-center">Core Charge</th>
                <?php endfor;?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($_SESSION['poe']['request'] as $id => $request):?>
                <tr id="row_<?=$id;?>">
                  <td>
                    <?=$request['description'];?>
                  </td>
                  <td class="no-space">
                    <?=$request['part_number'];?>
                  </td>
                  <td>
                    <?=$request['remarks'];?>
                  </td>
                  <td>
                    <?=$request['purchase_request_number'];?>
                  </td>
                  <td>
                    <?=number_format($request['quantity_requested'], 2);?>
                  </td>

                  <?php foreach ($_SESSION['poe']['vendors'] as $key => $vendor):?>
                    <td>
                      <input type="text" rel="alternate_part_number" name="request[<?=$id;?>][vendors][<?=$key;?>][alternate_part_number]" value="<?=$_SESSION['poe']['request'][$id]['vendors'][$key]['alternate_part_number'];?>" class="form-control">
                    </td>

                    <td>
                      <input type="text" rel="unit_price" name="request[<?=$id;?>][vendors][<?=$key;?>][unit_price]" value="<?=$_SESSION['poe']['request'][$id]['vendors'][$key]['unit_price'];?>" class="form-control">
                    </td>

                    <td>
                      <input type="text" rel="core_charge" name="request[<?=$id;?>][vendors][<?=$key;?>][core_charge]" value="<?=$_SESSION['poe']['request'][$id]['vendors'][$key]['core_charge'];?>" class="form-control">
                    </td>
                  <?php endforeach;?>
                </tr>
              <?php endforeach;?>
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
    var form    = $('#form_update_request');
    var action  = form.attr('action');

    button.prop('disabled', true);

    if (form.valid()){
      $.post( action, form.serialize() ).done( function(data){
        var obj = $.parseJSON(data);

        if ( obj.success == false ){
          toastr.options.timeOut = 10000;
          toastr.options.positionClass = 'toast-top-right';
          toastr.error( obj.message );
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

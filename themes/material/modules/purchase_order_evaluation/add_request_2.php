<?php include 'themes/material/simple.php' ?>

<?php startblock('body') ?>
<div class="container-fluid">

  <h4 class="page-header">Select Request</h4>

  <form id="form_add_request" class="form" role="form" method="post" action="<?=site_url($module['route'] .'/add_selected_request');?>">
    <div class="row">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>
                  No.
                </th>
                <th>
                  Description
                </th>
                <th>
                  Part Number
                </th>
                <th>
                  Additional Info
                </th>
                <th>
                  Quantity
                </th>
                <th>
                  Required Date
                </th>
                <th>
                  PR Number
                </th>
                <th>
                  PR Date
                </th>
                <th>
                  Suggested Supplier
                </th>
                <th>
                  Created By
                </th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($entities as $e => $entity):?>
                <tr>
                  <td>
                    <div class="checkbox">
                      <input type="checkbox" name="request_id[]" id="request_id_<?=$e;?>" value="<?=$entity['id'];?>" <?=(in_array($entity['id'], array_keys($_SESSION['poe']['request']))) ? 'checked' : '';?>>
                      <label for="request_id_<?=$e;?>">
                        <?=$e+1;?>
                      </label>
                    </div>
                  </td>
                  <td>
                    <label for="request_id_<?=$e;?>">
                      <?=print_string($entity['product_name']);?>
                    </label>
                  </td>
                  <td>
                    <label for="request_id_<?=$e;?>">
                      <?=print_string($entity['product_code']);?>
                    </label>
                  </td>
                  <td>
                    <label for="request_id_<?=$e;?>">
                      <?=print_string($entity['additional_info']);?>
                    </label>
                  </td>
                  <td>
                    <label for="request_id_<?=$e;?>">
                      <?=print_number($entity['quantity'], 2);?>
                    </label>
                  </td>
                  <td>
                    <label for="request_id_<?=$e;?>">
                      <?=print_date($entity['required_date']);?>
                    </label>
                  </td>
                  <td>
                    <label for="request_id_<?=$e;?>">
                      <?=print_string($entity['pr_number']);?>
                    </label>
                  </td>
                  <td>
                    <label for="request_id_<?=$e;?>">
                      <?=print_date($entity['pr_date']);?>
                    </label>
                  </td>
                  <td>
                    <label for="request_id_<?=$e;?>">
                      <?=print_string($entity['suggested_supplier']);?>
                    </label>
                  </td>
                  <td>
                    <label for="request_id_<?=$e;?>">
                      <?=print_string($entity['created_by']);?>
                    </label>
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
    var form    = $('#form_add_request');
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
          window.location.href = '<?=site_url($module['route'] . '/add_vendor');?>';
        }
      });
    }

    button.prop('disabled', false);
  });
});
</script>
<?php endblock() ?>

<?php include 'themes/material/simple.php' ?>

<?php startblock('body') ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="text-center">
        <img src="<?=base_url('themes/admin_lte/assets/images/logo.png');?>" height="100">
      </div>

      <h4><span class="text-primary">ADJUSTMENT</span> REQUEST APPROVAL</h4>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <?php if ($success):?>
        <?=$success;?>
      <?php else:?>
        <div class="alert alert-info">
          <p>No request to making approval.</p>
        </div>
      <?php endif;?>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <div class="text-right">
        <button type="button" class="btn btn-primary" onclick="popupClose()">Close</button>
      </div>

      <hr />

      <p class="text-muted">
        PT Bali Widya Dirgantara - 2017
      </p>
    </div>
  </div>
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
// $(function(){
//   $('#submit_button').on('click', function(e){
//     e.preventDefault();
//
//     var button  = $(this);
//     var form    = $('#form_edit_item');
//     var action  = form.attr('action');
//
//     button.prop('disabled', true);
//
//     if (form.valid()){
//       $.post( action, form.serialize() ).done( function(data){
//         var obj = $.parseJSON(data);
//
//         if ( obj.success == false ){
//           toastr.options.timeOut = 10000;
//           toastr.options.positionClass = 'toast-top-right';
//           toastr.error( obj.info );
//         } else {
//           refreshParent();
//           popupClose();
//         }
//       });
//     }
//
//     button.prop('disabled', false);
//   });
// });
</script>
<?php endblock() ?>

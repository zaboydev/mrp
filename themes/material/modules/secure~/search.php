<?php include 'themes/material/simple.php' ?>

<?php startblock('body') ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="text-center">
        <img src="<?=base_url('themes/admin_lte/assets/images/logo.png');?>" height="100">
      </div>

      <h4>Search Result for "<span class="text-primary"><?=$_GET['keywords'];?></span>"</h4>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <?php if ($entities):?>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
            <tr>
              <th class="text-right">No.</th>
              <th>Description</th>
              <th>P/N</th>
              <th>Alt. P/N</th>
              <th>S/N</th>
              <th>Base</th>
              <th>Stores</th>
              <th>Condition</th>
              <th class="text-right">Quantity</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;?>
            <?php foreach ($entities as $key => $value):?>
              <tr>
                <th class="text-right"><?=$no;?></th>
                <th><?=$value['description'];?></th>
                <th><?=$value['part_number'];?></th>
                <th><?=$value['alternate_part_number'];?></th>
                <th><?=$value['serial_number'];?></th>
                <th><?=$value['warehouse'];?></th>
                <th><?=$value['stores'];?></th>
                <th><?=$value['condition'];?></th>
                <th class="text-right">
                  <?=$value['quantity'];?>
                  <?=$value['unit'];?>
                </th>
              </tr>
              <?php $no++;?>
            <?php endforeach;?>
            </tbody>
          </table>
        </div>
      <?php else:?>
        <div class="alert alert-info">
          <p>No Result for "<span class="text-warning"><?=$_GET['keywords'];?></span>"</p>
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

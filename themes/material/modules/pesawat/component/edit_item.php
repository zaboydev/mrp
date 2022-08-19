<?php include 'themes/material/simple.php' ?>

<?php startblock('body') ?>
<div class="container-fluid">

  <h4 class="page-header">Update Item</h4>

  <form id="form_update_request" class="form form-validate ui-front" role="form" method="post" action="<?= site_url($module['route'] . '/update_selected_item'); ?>">
    <div class="row">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-hover" id="table-document">
            <thead>
                <tr>
                    <th>No</th>
                    <th class="middle-alignment">P/N</th>
                    <th class="middle-alignment">S/N</th>
                    <th class="middle-alignment">Alt. P/N</th>
                    <th class="middle-alignment">Description</th>
                    <th class="middle-alignment">Interval</th>
                    <th class="middle-alignment">Installation Date</th>
                    <th class="middle-alignment">AF TSN</th>
                    <th class="middle-alignment">Equip TSN</th>
                    <th class="middle-alignment">TSO</th>
                    <th class="middle-alignment">Remarks</th>
                </tr>              
            </thead>
            <tbody>
              <?php $no = 1;?>
              <?php foreach ($_SESSION['component']['items'] as $id => $item) : ?>
                
                <tr id="row_2_<?= $id; ?>" style="<?=$color;?>">
                    <td> <?= $no++; ?></td>
                    <td> <?= $item['part_number']?></td>
                    <td> <?= $item['serial_number']?></td>
                    <td> <?= $item['alternate_part_number']?></td>
                    <td> <?= $item['description']?></td>
                    <td> 
                        <input type="text" data-search-for="interval" name="items[<?= $id; ?>][interval]" class="form-control input-sm" value="<?=$item['interval']?>" required>
                    </td>
                    <td> 
                        <input type="date" data-search-for="installation_date" name="items[<?= $id; ?>][installation_date]" class="form-control input-sm" value="<?=$item['installation_date']?>" required>
                    </td>
                    <td> 
                        <input type="text" data-search-for="af_tsn" name="items[<?= $id; ?>][af_tsn]" class="form-control input-sm" value="<?=$item['af_tsn']?>" required>
                    </td>
                    <td> 
                        <input type="text" data-search-for="equip_tsn" name="items[<?= $id; ?>][equip_tsn]" class="form-control input-sm" value="<?=$item['equip_tsn']?>" required>
                    </td>
                    <td> 
                        <input type="text" data-search-for="tso" name="items[<?= $id; ?>][tso]" class="form-control input-sm" value="<?=$item['tso']?>" required>
                    </td>
                    <td> 
                        <input type="text" data-search-for="remarks" name="items[<?= $id; ?>][remarks]" class="form-control input-sm" value="<?=$item['remarks']?>" required>
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
<?php endblock() ?>

<?php startblock('simple_scripts') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/jquery.validate.min.js') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/additional-methods.min.js') ?>
<?= html_script('themes/material/assets/js/libs/toastr/toastr.js') ?>
<script>
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

    $.ajax({
      url: $('[data-search-for="stores"]').data('source'),
      dataType: "json",
      success: function (data) {
        $('[data-search-for="stores"]').autocomplete({
          source: function (request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $.ajax({
      url: $('[data-search-for="unit"]').data('source'),
      dataType: "json",
      success: function (data) {
        $('[data-search-for="unit"]').autocomplete({
          source: function (request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $.ajax({
      url: $('[data-search-for="unit_pakai"]').data('source'),
      dataType: "json",
      success: function (data) {
        $('[data-search-for="unit_pakai"]').autocomplete({
          source: function (request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $("#table-document").on("change", "[data-tag-name='isi']", function() {
      var isi = $(this).val();
      var row = $(this).data('tag-row');
      var quantity_order = $('[name="item['+row+'][quantity_order]"]').val();
      var value_order = $('[name="item['+row+'][value_order]"]').val();

      var received_quantity = parseFloat(isi)*parseFloat(quantity_order);
      var received_unit_value = parseFloat(value_order)/parseFloat(isi);
      $('[name="item['+row+'][received_quantity]"]').val(received_quantity);
      $('[name="item['+row+'][received_unit_value]"]').val(received_unit_value);

      console.log('input isi '+isi);
      console.log('row '+row);      
      console.log('quantity_order '+quantity_order);   
      console.log('received_quantity '+received_quantity);   
      console.log('value_order '+value_order);         
      console.log('received_unit_value '+received_unit_value);       

    });

    $("#table-document").on("change", "[data-tag-name='unit_pakai']", function() {
      var unit_pakai = $(this).val();
      var row = $(this).data('tag-row');
      $('[name="item['+row+'][unit_used]"]').html(unit_pakai);

    });
  });
</script>
<?php endblock() ?>
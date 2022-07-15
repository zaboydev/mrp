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
                    <th class="middle-alignment">Item's Description</th>
                    <th class="middle-alignment" colspan="2">Storage</th>
                    <th class="middle-alignment" colspan="2">Optional</th>
                    <!-- <th class="middle-alignment">P/N</th>
                    <th class="middle-alignment">S/N</th>
                    <th class="middle-alignment">Alt. P/N</th>
                    <th class="middle-alignment">Description</th>
                    <th class="middle-alignment">Group</th>
                    <th class="middle-alignment">Category</th>

                    <th class="middle-alignment">On Hand Stock</th>
                    <th class="middle-alignment">Unit</th>
                    <th class="middle-alignment">Condition</th>
                    <th class="middle-alignment">Stores</th>
                    <th class="middle-alignment">Qty</th>
                    <th class="middle-alignment">Remarks</th>                 -->
                </tr>              
            </thead>
            <tbody>
              <?php $no = 1;?>
              <?php foreach ($_SESSION['receipt']['items'] as $id => $item) : ?>
                <?php 
                  if($no % 2 == 0){
                    $color = 'background-color: #ffffff';
                  }else{
                    $color = 'background-color: #ebebeb';
                  }
                ?>
                <tr id="row_1_<?= $id; ?>" style="<?=$color;?>">
                    <td rowspan="10"><?= $no++?></td>
                    <td rowspan="10">
                      PO# : <?= $item['purchase_order_number']; ?> <br/>
                      Part Number : <?= $item['part_number']; ?> <br/>
                      Serial Number : <?= ($item['serial_number']!=null)?$item['serial_number']:' - '; ?> <br/>
                      Description : <?= $item['description']; ?> <br/>
                      Group :   <?= $item['group']; ?>
                    </td>
                    <td style="font-weight:bold;" colspan="1">
                      Condition*
                    </td>
                    <td style="font-weight:bold;" colspan="1">
                      Stores*
                    </td> 
                    <td style="font-weight:bold;">
                      Tgl Inv/Nota*
                    </td>                 
                    <td style="font-weight:bold;">
                      No Ref/Invoice*
                    </td>
                </tr>
                <tr id="row_2_<?= $id; ?>" style="<?=$color;?>">
                  <td colspan="1">
                    <select name="item[<?= $id; ?>][condition]" id="item[<?= $id; ?>][condition]" class="form-control input-sm">
                    <?php foreach (available_conditions() as $key => $condition) : ?>
                      <option value="<?= $condition; ?>" <?= ($condition === $item['condition']) ? 'selected' : ''; ?> ><?= $condition; ?></option>
                    <?php endforeach; ?>
                    </select>
                    <!-- <input type="text" rel="condition" name="item[<?= $id; ?>][condition]" value="<?= $item['condition']; ?>" class="form-control"> -->
                  </td>
                  <td colspan="1">
                    
                    <input data-search-for="stores" type="text" rel="stores" name="item[<?= $id; ?>][stores]" value="<?= $item['stores']; ?>" class="form-control input-sm" data-source="<?= site_url($modules['ajax']['route'] . '/json_stores/' . $_SESSION['receipt']['category']); ?>" required>
                  </td>
                  <td>
                    <input type="date" rel="tgl_nota" name="item[<?= $id; ?>][tgl_nota]" value="<?= $item['tgl_nota']; ?>" class="form-control input-sm" required>
                  </td>
                  <td>
                    <input type="text" rel="reference_number" name="item[<?= $id; ?>][reference_number]" value="<?= $item['reference_number']; ?>" class="form-control input-sm" required>
                  </td>
                </tr>
                <tr id="row_3_<?= $id; ?>" style="<?=$color;?>">
                  <td style="font-weight:bold;" colspan="1">
                    Received Qty*
                  </td>
                  <td style="font-weight:bold;" colspan="1">
                    Received Unit*
                  </td>
                  <td style="font-weight:bold;">
                    Min. Quantity*
                  </td>
                  <td style="font-weight:bold;">
                    AWB Numbers*
                  </td>
                </tr>
                <tr id="row_4_<?= $id; ?>" style="<?=$color;?>">
                  <td colspan="1">
                    <input type="number" step="0.01" name="item[<?= $id; ?>][quantity_order]" id="item[<?= $id; ?>][quantity_order]" data-tag-row="<?= $id; ?>" data-tag-name="quantity_order" class="form-control input-sm" value="<?= $item['quantity_order']; ?>" required>
                  </td>
                  <td colspan="1">
                  <input value="<?= $item['unit']; ?>" type="text" name="item[<?= $id; ?>][unit]" id="item[<?= $id; ?>][unit]" data-tag-name="unit" data-search-for="unit" data-source="<?= site_url($modules['ajax']['route'] . '/search_item_units/'); ?>" class="form-control input-sm" placeholder="Unit" required>
                  </td>
                  <td>
                    <input type="number" step="0.01" name="item[<?= $id; ?>][minimum_quantity]" id="item[<?= $id; ?>][minimum_quantity]" data-tag-name="item[<?= $id; ?>][minimum_quantity]" class="form-control input-sm" value="<?= $item['minimum_quantity']; ?>" required>
                  </td>
                  <td>
                    <input type="text" name="item[<?= $id; ?>][awb_number]" id="item[<?= $id; ?>][awb_number]" data-tag-name="awb_number" class="form-control input-sm" placeholder="Awb Number" value="<?= $item['awb_number']; ?>" required>
                  </td>
                </tr>
                <tr id="row_5_<?= $id; ?>" style="<?=$color;?>">
                  <td style="font-weight:bold;" colspan="1">
                    Quantity Stock
                  </td>
                  <td style="font-weight:bold;" colspan="1">
                    Unit Stock
                  </td>
                  <td style="font-weight:bold;">
                    Kode Stock
                  </td>
                  <td style="font-weight:bold;">
                    Remarks
                  </td>
                </tr>
                <tr id="row_7_<?= $id; ?>" style="<?=$color;?>">
                  <td colspan="1">
                    <input type="number" step="0.01" name="item[<?= $id; ?>][received_quantity]" id="item[<?= $id;?>][received_quantity]" data-tag-row="<?= $id; ?>" data-tag-name="received_quantity" class="form-control input-sm" value="<?= $item['received_quantity']; ?>" readonly>
                  </td>
                  <td colspan="1">
                    <input type="text" name="item[<?= $id; ?>][unit_pakai]" id="item[<?= $id; ?>][unit_pakai]" value="<?= $item['unit_pakai']; ?>" data-tag-row="<?= $id; ?>" data-tag-name="unit_pakai" data-search-for="unit_pakai" data-source="<?= site_url($modules['ajax']['route'] . '/search_item_units/'); ?>" class="form-control input-sm" placeholder="Unit Pakai" required>
                  </td>
                  <td>
                    <input type="text" name="item[<?= $id; ?>][kode_stok]" id="item[<?= $id; ?>][kode_stok]" data-tag-name="item[<?= $id; ?>][kode_stok]" class="form-control input-sm" value="<?= $item['kode_stok']; ?>">
                  </td>
                  <td>
                    <input type="text" name="item[<?= $id; ?>][remarks]" id="item[<?= $id; ?>][remarks]" data-tag-name="remarks" class="form-control input-sm" placeholder="remarks" value="<?= $item['remarks']; ?>">
                  </td>
                </tr>
                <tr id="row_7_<?= $id; ?>" style="<?=$color;?>">
                  <td style="font-weight:bold;" colspan="1">
                    Konversi Satuan
                  </td>
                  <td style="font-weight:bold;" colspan="1">
                     
                  </td>
                  <td style="font-weight:bold;">
                    Expired Date
                  </td>
                  <td style="font-weight:bold;">
                    
                  </td>
                </tr>
                <tr id="row_8_<?= $id; ?>" style="<?=$color;?>">
                  <td>
                  <!-- <input type="text" name="satuan" id="satuan" data-tag-name="received_quantity" class="form-control input-sm" value="1" readonly="readonly"> <span><?=$item['received_unit']?></span> -->
                    <div class="col-md-6">
                      <input type="text" name="item[<?= $id; ?>][satuan]" id="item[<?= $id; ?>][satuan]" data-tag-name="item[<?= $id; ?>][satuan]" class="form-control input-sm" value="1" readonly="readonly">
                    </div>
                    <div class="col-md-6">
                      <span name="item[<?= $id; ?>][received_unit]" id="item[<?= $id; ?>][received_unit]"><?=$item['received_unit']?></span>
                      
                    </div>
                  </td>
                  <td>
                    <div class="col-md-6">
                      <input type="number" name="item[<?= $id; ?>][isi]" id="item[<?= $id; ?>][isi]" data-tag-row="<?= $id; ?>" data-tag-name="isi" class="form-control input-sm" value="<?=$item['isi']?>" required>
                    </div>
                    <div class="col-md-6">
                      <span name="item[<?= $id; ?>][unit_used]" id="item[<?= $id; ?>][unit_used]"><?=$item['unit_pakai']?></span>                      
                    </div>
                  </td>
                  <td>
                    <input type="date" rel="expired_date" name="item[<?= $id; ?>][expired_date]" value="<?= $item['expired_date']; ?>" class="form-control input-sm">
                  </td>
                  <td>
                    
                  </td>
                </tr>
                <tr id="row_9_<?= $id; ?>" style="<?=$color;?>">
                  <td style="font-weight:bold;" colspan="1">
                  <?=(config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'SUPER ADMIN')?'Price per Unit Pakai':''?>
                    
                  </td>
                  <td style="font-weight:bold;" colspan="1">
                     
                  </td>
                  <td style="font-weight:bold;">
                    
                  </td>
                  <td style="font-weight:bold;">
                    
                  </td>
                </tr>
                <tr id="row_10_<?= $id; ?>" style="<?=$color;?>">
                  <td>
                    <select class="form-control input-sm <?=(config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'SUPER ADMIN')?'':'hide'?>" id="item[<?= $id; ?>][kurs]" name="item[<?= $id; ?>][kurs]" required>
                      <option value="rupiah" <?= ('rupiah' === $item['kurs']) ? 'selected' : ''; ?>>Rupiah</option>
                      <option value="dollar" <?= ('dollar' === $item['kurs']) ? 'selected' : ''; ?>>USD Dollar</option>
                    </select>
                  </td>
                  <td>
                    <input type="number" step="0.01" name="item[<?= $id; ?>][received_unit_value]" id="item[<?= $id; ?>][received_unit_value]" data-tag-name="received_unit_value" class="form-control input-sm <?=(config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'SUPER ADMIN')?'':'hide'?>" value="<?=$item['received_unit_value']?>">
                    <input name="item[<?= $id; ?>][value_order]" type="hidden" id="item[<?= $id; ?>][value_order]" data-tag-name="value_order" class="form-control input-sm" value="<?=$item['value_order']?>">
                    
                  </td>
                  <td>
                    
                  </td>
                  <td>
                    
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
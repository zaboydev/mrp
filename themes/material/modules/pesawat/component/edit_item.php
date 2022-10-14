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
                    <!-- <th>No</th> -->
                    <?php if ($_SESSION['component']['source']=='change'):?>
                    <th class="middle-alignment">Item yg off</th>
                    <?php endif; ?>
                    <th class="middle-alignment">P/N</th>
                    <th class="middle-alignment">S/N</th>
                    <th class="middle-alignment">Alt. P/N</th>
                    <th class="middle-alignment">Description</th>
                    <th class="middle-alignment">Unit</th>
                    <th class="middle-alignment">Group</th>
                    <!-- <th class="middle-alignment">Interval</th> -->
                    <th class="middle-alignment">Installation Date</th>
                    <th class="middle-alignment">Historical</th>
                    <!-- <th class="middle-alignment">Equip TSN</th>
                    <th class="middle-alignment">TSO</th>
                    <th class="middle-alignment">Remarks</th> -->
                    <th class="middle-alignment"></th>
                </tr>              
            </thead>
            <tbody>
              <?php $no = 1;?>
              <?php foreach ($_SESSION['component']['items'] as $id => $item) : ?>
              <tr id="row_2_<?= $id; ?>">
                <!-- <td rowspan="1"> <?= $no++; ?></td> -->
                <?php if ($_SESSION['component']['source']=='change'):?>
                <td rowspan="2"> 
                  <input type="text" name="previous_component_id[]" class="form-control input-sm" value="">
                </td>
                <?php endif; ?>
                <td rowspan="1">
                  <input value="<?= $item['part_number']?>" type="text" name="part_number[]" data-tag-name="part_number" data-search-for="part_number" class="form-control input-sm" placeholder="Part Number" required>
                  <input value="<?= $item['item_id']?>" type="hidden" name="item_id[]" class="form-control input-sm">
                  <input value="<?= $item['issuance_document_number']?>" type="hidden" name="issuance_document_number[]" class="form-control input-sm">
                  <input value="<?= $item['issuance_item_id']?>" type="hidden" name="issuance_item_id[]" class="form-control input-sm">
                </td>
                <td rowspan="1">
                  <input value="<?= $item['serial_number']?>" type="text" name="serial_number[]" data-tag-name="serial_number" data-search-for="serial_number" class="form-control input-sm" placeholder="Serial Number">
                </td>
                <td rowspan="1">
                  <input value="<?= $item['alternate_part_number']?>" type="text" name="alternate_part_number[]" data-tag-name="alternate_part_number" data-search-for="alternate_part_number" class="form-control input-sm" placeholder="Alt Part Number">
                </td>
                <td rowspan="1">
                  <input value="<?= $item['description']?>" type="text" name="description[]" data-tag-name="description" data-search-for="description" class="form-control input-sm" placeholder="Description" required>
                </td>
                <td>
                  <input value="<?= $item['unit']?>" type="text" name="unit[]" data-tag-name="unit" data-search-for="unit" class="form-control input-sm" placeholder="unit" required>
                </td>
                <td>                  
                  <input value="<?= $item['group']?>" type="text" name="group[]" data-tag-name="group" data-search-for="description" class="form-control input-sm" placeholder="Description" required>
                </td>
                <td> 
                  <input type="date" name="installation_date[]" class="form-control input-sm" value="<?=$item['installation_date']?>" required>
                </td>
                <td>                  
                  <input value="<?= $item['historical']?>" type="text" name="historical[]" data-tag-name="historical" data-search-for="historical" class="form-control input-sm" placeholder="Historical" required>
                </td>
                <!-- <td> 
                  <input type="number" data-search-for="interval" name="items[<?= $id; ?>][interval]" class="form-control input-sm" value="<?=$item['interval']?>" required>
                </td>
                <td rowspan="2"> 
                  <input type="date" data-search-for="installation_date" name="items[<?= $id; ?>][installation_date]" class="form-control input-sm" value="<?=$item['installation_date']?>" required>
                </td>
                <td> 
                  <input type="number" data-search-for="af_tsn" name="items[<?= $id; ?>][af_tsn]" class="form-control input-sm" value="<?=$item['af_tsn']?>" required>
                </td>
                <td> 
                  <input type="number" data-search-for="equip_tsn" name="items[<?= $id; ?>][equip_tsn]" class="form-control input-sm" value="<?=$item['equip_tsn']?>" required>
                </td>
                <td> 
                  <input type="number" data-search-for="tso" name="items[<?= $id; ?>][tso]" class="form-control input-sm" value="<?=$item['tso']?>" required>
                </td>
                <td rowspan="2"> 
                  <input type="text" data-search-for="remarks" name="items[<?= $id; ?>][remarks]" class="form-control input-sm" value="<?=$item['remarks']?>" required>
                </td> -->
                <td></td>
              </tr>
              <!-- <tr id="row_2_<?= $id; ?>">
                <td> 
                  <input type="number" data-search-for="interval" name="items[<?= $id; ?>][interval]" class="form-control input-sm" value="<?=$item['interval']?>" required>
                </td>
                <td> 
                  <input type="number" data-search-for="af_tsn" name="items[<?= $id; ?>][af_tsn]" class="form-control input-sm" value="<?=$item['af_tsn']?>" required>
                </td>
                <td> 
                  <input type="number" data-search-for="equip_tsn" name="items[<?= $id; ?>][equip_tsn]" class="form-control input-sm" value="<?=$item['equip_tsn']?>" required>
                </td>
                <td> 
                  <input type="number" data-search-for="tso" name="items[<?= $id; ?>][tso]" class="form-control input-sm" value="<?=$item['tso']?>" required>
                </td>
                  
              </tr> -->
                
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
      <button type="button" class="btn btn-primary ink-reaction btn-add">
        Add
      </button>
    </div>
  </form>
  <table class="table-row-item hide">
    <tbody>
      <tr>
        <!-- <td rowspan="1"> <?= $no++; ?></td> -->
        <?php if ($_SESSION['component']['source']=='change'):?>
        <td rowspan="2"> 
          <input type="text" name="previous_component_id[]" class="form-control input-sm" value="">
        </td>
        <?php endif; ?>
        <td rowspan="1">
          <input value="" type="hidden" name="issuance_document_number[]" class="form-control input-sm">
          <input value="" type="hidden" name="issuance_item_id[]" class="form-control input-sm">
          <input value="" type="hidden" name="item_id[]" data-tag-name="item_id" data-search-for="item_id" class="form-control input-sm" placeholder="item_id">
          <input value="" type="text" name="part_number[]" data-tag-name="part_number" data-search-for="part_number" class="form-control input-sm" placeholder="Part Number" required>
        </td>
        <td rowspan="1">
          <input value="" type="text" name="serial_number[]" data-tag-name="serial_number" data-search-for="serial_number" class="form-control input-sm" placeholder="Serial Number">
        </td>
        <td rowspan="1">
          <input value="" type="text" name="alternate_part_number[]" data-tag-name="alternate_part_number" data-search-for="alternate_part_number" class="form-control input-sm" placeholder="Alt Part Number">
        </td>
        <td rowspan="1">
          <input value="" type="text" name="description[]" data-tag-name="description" data-search-for="description" class="form-control input-sm" placeholder="Description" required>
        </td>
        <td>
          <input value="<?= $item['unit']; ?>" type="text" name="item[<?= $id; ?>][unit]" id="item[<?= $id; ?>][unit]" data-tag-name="unit" data-search-for="unit" data-source="<?= site_url($modules['ajax']['route'] . '/search_item_units/'); ?>" class="form-control input-sm" placeholder="Unit" required>
        </td>
        <td>
          <select name="group[]" id="group" data-tag-name="group" class="form-control input-sm" required style="width: 100%">
            <option>-- Select One --</option>
            <?php foreach (available_item_groups_2() as $group) : ?>
            <option value="<?= $group['group']; ?>">
              <?= $group['group']; ?> - <?= $group['coa']; ?>
            </option>
            <?php endforeach; ?>
          </select>
        </td>
        <td> 
          <input type="date" name="installation_date[]" class="form-control input-sm" value="<?=$item['installation_date']?>" required>
        </td>  
        <td>                  
          <input value="<?= $item['historical']?>" type="text" name="historical[]" data-tag-name="historical" data-search-for="historical" class="form-control input-sm" placeholder="Historical" required>
        </td>
        <td class="item-list">
          <center>
            <a  href="javascript:;" title="Delete" class="btn btn-danger btn-xs btn-row-delete-item" data-tipe="delete"> Delete
            </a>
          </center>                      
        </td> 
      </tr>
    </tbody>
  </table>

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
<?= html_script('vendors/select2-4.0.3/dist/js/select2.min.js') ?>
<?= html_script('vendors/select2-pmd/js/pmd-select2.js') ?>
<script>
  $(function() {
    $('.select2').select2({
      theme: "bootstrap",
    });
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

    $('.btn-add').on('click', function(e) {
      var row_payment = $('.table-row-item tbody').html();
      var el = $(row_payment);
      $('#table-document tbody').append(el);
      $('#table-document tbody tr:last').find('select[name="group[]"]').select2();

      btn_row_delete_item();
    });

    function btn_row_delete_item() {
      $('.btn-row-delete-item').click(function () {
        $(this).parents('tr').remove();
      });
    }
  });
</script>
<?php endblock() ?>
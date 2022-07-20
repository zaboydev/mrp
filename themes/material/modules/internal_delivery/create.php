<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
  <section class="has-actions style-default">
    <div class="section-body">
      <?=form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate floating-label', 'id' => 'form-create-document'));?>
        <div class="card">
          <div class="card-head style-primary-dark">
            <header><?=PAGE_TITLE;?></header>
          </div>
          <div class="card-body no-padding">
            <?php
            if ( $this->session->flashdata('alert') )
              render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
            ?>

            <div class="document-header force-padding">
              <div class="row">
                <div class="col-sm-6 col-lg-3">
                  <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-content">
                        <input type="text" name="document_number" id="document_number" class="form-control" maxlength="6" value="<?=$_SESSION['delivery']['document_number'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_doc_number');?>" required>
                        <label for="document_number">Document No.</label>
                      </div>
                      <span class="input-group-addon"><?=delivery_format_number();?></span>
                    </div>
                  </div>

                  <div class="form-group">
                    <input type="text" name="received_date" id="received_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control" value="<?=$_SESSION['delivery']['received_date'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_received_date');?>" required>
                    <input type="hidden" name="opname_start_date" id="opname_start_date" data-date-format="yyyy-mm-dd" class="form-control" value="<?=last_publish_date();?>" readonly>
                    <label for="received_date">Date</label>
                  </div>

                  <div class="form-group">
                    <input type="text" name="received_by" id="received_by" class="form-control" value="<?=$_SESSION['delivery']['received_by'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_received_by');?>" required>
                    <label for="received_by">Received By</label>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                  <div class="form-group">
                    <select name="received_from" id="received_from" class="form-control" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_received_from');?>" required>
                      <option value=""></option>
                      <?php foreach (pesawat() as $pesawat):?>
                      <option value="<?=$pesawat;?>" <?=($_SESSION['delivery']['received_from'] == $pesawat) ? 'selected' : '';?>>
                        <?=$pesawat;?>
                      </option>
                      <?php endforeach; ?>
                    </select>
                    <!-- <input type="text" name="received_from" id="received_from" class="form-control" value="<?=$_SESSION['delivery']['received_from'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_received_from');?>" required> -->
                    <label for="received_from">Received From</label>
                    <p class="help-block">
                      Example: PK-ROA
                    </p>
                  </div>

                  <div class="form-group">
                    <input type="text" name="sent_by" id="sent_by" class="form-control" value="<?=$_SESSION['delivery']['sent_by'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_sent_by');?>" required>
                    <label for="sent_by">Sent/Delivered By</label>
                  </div>

                  <div class="form-group">
                    <input type="text" name="approved_by" id="approved_by" class="form-control" value="<?=$_SESSION['delivery']['approved_by'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_approved_by');?>">
                    <label for="approved_by">Approved By</label>
                  </div>
                </div>

                <div class="col-sm-12 col-lg-5">
                  <div class="form-group">
                    <textarea name="notes" id="notes" class="form-control" rows="4" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_notes');?>"><?=$_SESSION['delivery']['notes'];?></textarea>
                    <label for="notes">Notes</label>
                  </div>
                </div>
              </div>
            </div>

            <?php if (isset($_SESSION['delivery']['items'])):?>
              <div class="document-data table-responsive">
                <table class="table table-hover" id="table-document">
                  <thead>
                  <tr>
                    <th></th>
                    <th>Group</th>
                    <th>Description</th>
                    <th>P/N</th>
                    <th>Alt. P/N</th>
                    <th>S/N</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Condition</th>
                    <th>Stores</th>
                    <th>Remarks</th>
                  </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($_SESSION['delivery']['items'] as $i => $items):?>
                      <tr id="row_<?=$i;?>">
                        <td width="1">
                          <a href="<?=site_url($module['route'] .'/del_item/'. $i);?>" class="btn btn-icon-toggle btn-danger btn-sm btn_delete_document_item">
                            <i class="fa fa-trash"></i>
                          </a>
                          <a class="btn btn-icon-toggle btn-info btn-sm btn_edit_document_item" data-todo='{"todo":<?=$i;?>}'>
                            <i class="fa fa-edit"></i>
                          </a>                        
                        </td>
                        <td>
                          <?=$items['group'];?>
                        </td>
                        <td>
                          <?=$items['description'];?>
                        </td>
                        <td class="no-space">
                          <?=$items['part_number'];?>
                        </td>
                        <td class="no-space">
                          <?=$items['alternate_part_number'];?>
                        </td>
                        <td>
                          <?=$items['serial_number'];?>
                        </td>
                        <td>
                          <?=number_format($items['quantity'], 2);?>
                        </td>
                        <td>
                          <?=$items['unit'];?>
                        </td>
                        <td>
                          <?=$items['condition'];?>
                        </td>
                        <td>
                          <?=$items['stores'];?>
                        </td>
                        <td>
                          <?=$items['remarks'];?>
                        </td>
                      </tr>
                    <?php endforeach;?>
                  </tbody>
                </table>
              </div>
            <?php endif;?>
          </div>
          <div class="card-actionbar">
            <div class="card-actionbar-row">
              <a href="#modal-add-item" data-toggle="modal" data-target="#modal-add-item" class="btn btn-primary ink-reaction btn-open-offcanvas pull-left">
                Add Item
              </a>

              <a href="<?=site_url($module['route'] .'/discard');?>" class="btn btn-flat btn-danger ink-reaction">
                Discard
              </a>
            </div>
          </div>
        </div>
      <?=form_close();?>
    </div>

    <div id="modal-add-item" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-add-item-label" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header style-primary-dark">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="modal-add-item-label">Add Item</h4>
          </div>

        <?=form_open(site_url($module['route'] .'/add_item'), array(
          'autocomplete' => 'off',
          'id'    => 'ajax-form-create-document',
          'class' => 'form form-validate ui-front',
          'role'  => 'form'
        ));?>

          <div class="modal-body">
            <div class="row">
              <div class="col-sm-12 col-lg-8">
                <div class="row">
                  <div class="col-sm-6 col-lg-8">
                    <fieldset>
                      <legend>General</legend>

                      <div class="form-group">
                        <input type="text" name="part_number" id="part_number" class="form-control input-sm input-autocomplete" data-source="<?=site_url($module['route'] .'/search_items_by_part_number/');?>" required>
                        <label for="part_number">Part Number</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="serial_number" id="serial_number" class="form-control input-sm input-autocomplete" data-source="<?=site_url($module['route'] .'/search_items_by_serial/');?>">
                        <label for="serial_number">Serial Number</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="description" id="description" data-tag-name="item_description" data-search-for="item_description" class="form-control input-sm" data-source="<?=site_url($modules['ajax']['route'] .'/json_item_description/'. $_SESSION['delivery']['category']);?>" required>
                        <label for="description">Description</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="alternate_part_number" id="alternate_part_number" data-tag-name="alternate_part_number" data-source="<?=site_url($modules['ajax']['route'] .'/json_alternate_part_number/'. $_SESSION['delivery']['category']);?>" class="form-control input-sm">
                        <label for="alternate_part_number">Alt. Part Number</label>
                      </div>

                      <div class="form-group">
                        <select name="group" id="group" data-tag-name="group" class="form-control input-sm" required>
                          <option>-- Select One --</option>
                          <?php foreach (available_item_groups($_SESSION['delivery']['category']) as $group):?>
                            <option value="<?=$group;?>">
                              <?=$group;?>
                            </option>
                          <?php endforeach;?>
                        </select>
                        <label for="group">Item Group</label>
                      </div>
                    </fieldset>
                  </div>
                  <div class="col-sm-6 col-lg-4">
                    <fieldset>
                      <legend>Storage</legend>

                      <div class="form-group">
                        <input type="text" name="received_quantity" id="received_quantity" data-tag-name="received_quantity" class="form-control input-sm" value="1" required>
                        <label for="received_quantity">Quantity</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="minimum_quantity" id="minimum_quantity" data-tag-name="minimum_quantity" class="form-control input-sm" value="0" required>
                        <label for="minimum_quantity">Minimum Quantity</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="unit" id="unit" data-tag-name="unit" data-search-for="unit" data-source="<?=site_url($modules['ajax']['route'] .'/search_item_units/');?>" class="form-control input-sm" required>
                        <label for="unit">Unit of Measurement</label>
                      </div>

                      <div class="form-group">
                        <select name="condition" id="condition" class="form-control input-sm">
                          <?php foreach (available_conditions() as $key => $condition) : ?>
                            <option value="<?= $condition; ?>"><?= $condition; ?></option>
                          <?php endforeach; ?>
                        </select>
                        <!-- <input type="text" name="condition" id="condition" class="form-control input-sm" value="UNSERVICEABLE" readonly> -->
                        <label for="condition">Item Condition</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="stores" id="stores" data-tag-name="stores" data-search-for="stores" data-source="<?=site_url($modules['ajax']['route'] .'/json_stores/'. $_SESSION['delivery']['category']);?>" class="form-control input-sm">
                        <label for="stores">Stores</label>
                      </div>
                    </fieldset>
                  </div>
                </div>
              </div>

              <div class="col-sm-12 col-lg-4">
                <fieldset>
                  <legend>Optional</legend>

                  <div class="form-group">
                    <textarea name="remarks" id="remarks" data-tag-name="remarks" class="form-control input-sm"></textarea>
                    <label for="remarks">Remarks</label>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <input type="hidden" id="received_unit_value" name="received_unit_value" value="1">
            <input type="hidden" id="item_id" name="item_id">

            <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>

            <button type="submit" id="modal-add-item-submit" class="btn btn-primary btn-create ink-reaction">
              Add Item
            </button>

            <input type="reset" name="reset" class="sr-only">
          </div>

        <?=form_close();?>
        </div>
      </div>
    </div>

    <!-- <div id="modal-edit-item" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-edit-item-label" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header style-primary-dark">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="modal-add-item-label">Edit Item</h4>
          </div>

        <?=form_open(site_url($module['route'] .'/edit_item'), array(
          'autocomplete' => 'off',
          'id'    => 'ajax-form-create-document',
          'class' => 'form form-validate ui-front',
          'role'  => 'form'
        ));?>

          <div class="modal-body">
            <div class="row">
              <div class="col-sm-12 col-lg-8">
                <div class="row">
                  <div class="col-sm-6 col-lg-8">
                    <fieldset>
                      <legend>General</legend>

                      <div class="form-group">
                        <input type="text" name="edit_serial_number" id="edit_serial_number" class="form-control input-sm input-autocomplete" data-source="<?=site_url($module['route'] .'/search_items_by_serial/');?>">
                        <label for="edit_serial_number">Serial Number</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="edit_part_number" id="edit_part_number" class="form-control input-sm input-autocomplete" data-source="<?=site_url($module['route'] .'/search_items_by_part_number/');?>" required>
                        <label for="edit_part_number">Part Number</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="edit_description" id="edit_description" data-tag-name="item_description" data-search-for="item_description" class="form-control input-sm" data-source="<?=site_url($modules['ajax']['route'] .'/json_item_description/'. $_SESSION['delivery']['category']);?>" required>
                        <label for="edit_description">Description</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="edit_alternate_part_number" id="edit_alternate_part_number" data-tag-name="alternate_part_number" data-source="<?=site_url($modules['ajax']['route'] .'/json_alternate_part_number/'. $_SESSION['delivery']['category']);?>" class="form-control input-sm">
                        <label for="edit_alternate_part_number">Alt. Part Number</label>
                      </div>

                      <div class="form-group">
                        <select name="edit_group" id="edit_group" data-tag-name="group" class="form-control input-sm" required>
                          <option>-- Select One --</option>
                          <?php foreach (available_item_groups($_SESSION['delivery']['category']) as $group):?>
                            <option value="<?=$group;?>">
                              <?=$group;?>
                            </option>
                          <?php endforeach;?>
                        </select>
                        <label for="group">Item Group</label>
                      </div>
                    </fieldset>
                  </div>
                  <div class="col-sm-6 col-lg-4">
                    <fieldset>
                      <legend>Storage</legend>

                      <div class="form-group">
                        <input type="text" name="edit_received_quantity" id="edit_received_quantity" data-tag-name="received_quantity" class="form-control input-sm" value="1" required>
                        <label for="edit_received_quantity">Quantity</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="edit_minimum_quantity" id="edit_minimum_quantity" data-tag-name="minimum_quantity" class="form-control input-sm" value="0" required>
                        <label for="minimum_quantity">Minimum Quantity</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="edit_unit" id="edit_unit" data-tag-name="unit" data-search-for="unit" data-source="<?=site_url($modules['ajax']['route'] .'/search_item_units/');?>" class="form-control input-sm" required>
                        <label for="edit_unit">Unit of Measurement</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="edit_condition" id="edit_condition" class="form-control input-sm" value="UNSERVICEABLE" readonly>
                        <label for="edit_condition">Item Condition</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="edit_stores" id="edit_stores" data-tag-name="stores" data-search-for="stores" data-source="<?=site_url($modules['ajax']['route'] .'/json_stores/'. $_SESSION['delivery']['category']);?>" class="form-control input-sm" required>
                        <label for="edit_stores">Stores</label>
                      </div>
                    </fieldset>
                  </div>
                </div>
              </div>

              <div class="col-sm-12 col-lg-4">
                <fieldset>
                  <legend>Optional</legend>

                  <div class="form-group">
                    <textarea name="edit_remarks" id="edit_remarks" data-tag-name="remarks" class="form-control input-sm"></textarea>
                    <label for="edit_remarks">Remarks</label>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <input type="hidden" id="edit_received_unit_value" name="edit_received_unit_value" value="1">
            <input type="text" id="edit_item_id" name="edit_item_id">

            <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>

            <button type="submit" id="modal-add-item-submit" class="btn btn-primary btn-create ink-reaction">
              Add Item
            </button>

            <input type="reset" name="reset" class="sr-only">
          </div>

        <?=form_close();?>
        </div>
      </div>
    </div> -->

    <div class="section-action style-default-bright">
      <div class="section-floating-action-row">
        <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?=site_url($module['route'] .'/save');?>">
          <i class="md md-save"></i>
          <small class="top right">Save Document</small>
        </a>
      </div>
    </div>
  </section>
<?php endblock() ?>

<?php startblock('scripts')?>
<?=html_script('vendors/pace/pace.min.js') ?>
<?=html_script('vendors/jQuery/jQuery-2.2.1.min.js') ?>
<?=html_script('themes/material/assets/js/libs/jquery-ui/jquery-ui.min.js') ?>
<?=html_script('themes/material/assets/js/libs/bootstrap/bootstrap.min.js') ?>
<?=html_script('themes/material/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js') ?>
<?=html_script('themes/material/assets/js/libs/spin.js/spin.min.js') ?>
<?=html_script('themes/material/assets/js/libs/autosize/jquery.autosize.min.js') ?>
<?=html_script('themes/material/assets/js/libs/toastr/toastr.js') ?>
<?=html_script('themes/material/assets/js/libs/jquery-validation/dist/jquery.validate.min.js') ?>
<?=html_script('themes/material/assets/js/libs/jquery-validation/dist/additional-methods.min.js') ?>
<?=html_script('vendors/bootstrap-daterangepicker/moment.min.js') ?>
<?=html_script('vendors/bootstrap-daterangepicker/daterangepicker.js') ?>
<?=html_script('themes/material/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js') ?>
<script>
Pace.on('start', function(){
  $('.progress-overlay').show();
});

Pace.on('done', function(){
  $('.progress-overlay').hide();
});

(function ( $ ) {
  $.fn.reset = function() {
    this.find('input:text, input[type="email"], input:password, select, textarea').val('');
    this.find('input:radio, input:checkbox').prop('checked', false);
    return this;
  }

  $.fn.redirect = function(target) {
    var url = $(this).data('href');

    if (target == '_blank'){
      window.open(url, target);
    } else {
      window.document.location = url;
    }
  }

  $.fn.popup = function() {
    var popup   = $(this).data('target');
    var source  = $(this).data('source');

    $.get(source, function(data){
      var obj = $.parseJSON(data);

      if (obj.type == 'denied'){
        toastr.options.timeOut = 10000;
        toastr.options.positionClass = 'toast-top-right';
        toastr.error( obj.info, 'ACCESS DENIED!' );
      } else {
        $( popup )
          .find('.modal-body')
          .empty()
          .append(obj.info);

        $( popup ).modal('show');

        $( popup ).on('click', '.modal-header:not(a)', function(){
          $( popup ).modal('hide');
        });

        $( popup ).on('click', '.modal-footer:not(a)', function(){
          $( popup ).modal('hide');
        });
      }
    })
  }
}( jQuery ));

function submit_post_via_hidden_form(url, params) {
  var f = $("<form target='_blank' method='POST' style='display:none;'></form>").attr('action', url).appendTo(document.body);

  $.each( params, function( key, value ) {
    var hidden = $('<input type="hidden" />').attr({
      name: key,
      value: JSON.stringify(value)
    });

    hidden.appendTo(f);
  });

  f.submit();
  f.remove();
}

function numberFormat(nStr)
{
  nStr += '';
  x = nStr.split('.');
  x1 = x[0];
  x2 = x.length > 1 ? '.' + x[1] : '';
  var rgx = /(\d+)(\d{3})/;
  while (rgx.test(x1)) {
      x1 = x1.replace(rgx, '$1' + ',' + '$2');
  }
  return x1 + x2;
}

$(document).on('keydown', function(event) {
  if ((event.metaKey || event.ctrlKey) && (
    String.fromCharCode(event.which).toLowerCase() === '0' ||
    String.fromCharCode(event.which).toLowerCase() === 'a' ||
    String.fromCharCode(event.which).toLowerCase() === 'd' ||
    String.fromCharCode(event.which).toLowerCase() === 'e' ||
    String.fromCharCode(event.which).toLowerCase() === 'i' ||
    String.fromCharCode(event.which).toLowerCase() === 'o' ||
    String.fromCharCode(event.which).toLowerCase() === 's' ||
    String.fromCharCode(event.which).toLowerCase() === 'x')
  ) {
    event.preventDefault();
  }
});

$(function(){
  // GENERAL ELEMENTS
  var formDocument              = $('#form-document');
  var buttonSubmitDocument      = $('#btn-submit-document');
  var buttonDeleteDocumentItem  = $('.btn_delete_document_item');
  var buttonEditDocumentItem    = $('.btn_edit_document_item');
  var autosetInputData          = $('[data-input-type="autoset"]');

  toastr.options.closeButton = true;

  $('[data-toggle="redirect"]').on('click', function(e){
    e.preventDefault;

    var url = $(this).data('url');

    window.document.location = url;
  });

  $('[data-toggle="back"]').on('click', function(e){
    e.preventDefault;

    history.back();
  });

  var startDate = new Date(<?=config_item('period_year');?>, <?=config_item('period_month');?>-1, 1);
  var lastDate = new Date(<?=config_item('period_year');?>, <?=config_item('period_month');?>, 0);
  var last_publish = $('[name="opname_start_date"]').val();
  var today       = new Date();
  today.setDate(today.getDate() - 2);

  $('[data-provide="datepicker"]').datepicker({
    autoclose: true,
    todayHighlight: true,
    format: 'yyyy-mm-dd',
    startDate: today,
  });

  $(document).on('click', '.btn-xhr-submit', function(e){
    e.preventDefault();

    var button  = $( this );
    var form    = $( '.form-xhr' );
    var action  = form.attr('action');

    button.attr('disabled', true);

    if (form.valid()){
      $.post( action, form.serialize() ).done( function(data){
        var obj = $.parseJSON(data);

        if ( obj.type == 'danger' ){
          toastr.options.timeOut = 10000;
          toastr.options.positionClass = 'toast-top-right';
          toastr.error( obj.info );
        } else {
          toastr.options.positionClass = 'toast-top-right';
          toastr.success( obj.info );

          form.reset();

          $('[data-dismiss="modal"]').trigger('click');

          if ( datatable ){
            datatable.ajax.reload( null, false );
          }
        }
      });
    }

    button.attr('disabled', false);
  });

  $(buttonSubmitDocument).on('click', function(e){
    e.preventDefault();
    $(buttonSubmitDocument).attr('disabled', true);

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

        window.setTimeout(function(){
          window.location.href = '<?=site_url($module['route']);?>';
        }, 5000);
      }

      $(buttonSubmitDocument).attr('disabled', false);
    });
  });

  $(buttonDeleteDocumentItem).on('click', function(e){
    e.preventDefault();

    var url = $(this).attr('href');
    var tr  = $(this).closest('tr');

    $.get( url );

    $(tr).remove();

    if ($("#table-document > tbody > tr").length == 0){
      $(buttonSubmit).attr('disabled', true);
    }
  });

  $( autosetInputData ).on('change', function(){
    var val = $(this).val();
    var url = $(this).data('source');

    $.get( url, { data: val });
  });

  $.ajax({
    url: $('input[id="serial_number"]').data('source'),
    dataType: "json",
    success: function (resource) {
      $('input[id="serial_number"]').autocomplete({
        autoFocus: true,
        minLength: 2,

        source: function (request, response) {
          var results = $.ui.autocomplete.filter(resource, request.term);
          response(results.slice(0, 5));
        },

        focus: function( event, ui ) {
          return false;
        },

        select: function( event, ui ) {
          $('input[id="serial_number"]').val(ui.item.serial_number);
          $('input[id="part_number"]').val(ui.item.part_number);
          $('input[id="alternate_part_number"]').val(ui.item.alternate_part_number);
          $('input[id="description"]').val(ui.item.description);
          $('select[id="group"]').val(ui.item.group);
          $('input[id="unit"]').val(ui.item.unit);
          $('input[id="minimum_quantity"]').val(ui.item.minimum_quantity);

          $('input[id="received_quantity"]').val( 1 ).prop('readonly', true);

          $('input[id="stores"]').focus();

          return false;
        }
      })
      .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
        $( ul ).addClass('list divider-full-bleed');

        return $( "<li class='tile'>" )
        .append( '<a class="tile-content ink-reaction"><div class="tile-text">' + item.label + '</div></a>' )
        .appendTo( ul );
      };
    }
  });

  $.ajax({
    url: $('input[id="part_number"]').data('source'),
    dataType: "json",
    success: function (resource) {
      $('input[id="part_number"]').autocomplete({
        autoFocus: true,
        minLength: 2,

        source: function (request, response) {
          var results = $.ui.autocomplete.filter(resource, request.term);
          response(results.slice(0, 5));
        },

        focus: function( event, ui ) {
          return false;
        },

        select: function( event, ui ) {
          $('input[id="part_number"]').val(ui.item.part_number);
          $('input[id="serial_number"]').val(ui.item.serial_number);
          $('input[id="alternate_part_number"]').val(ui.item.alternate_part_number);
          $('input[id="description"]').val(ui.item.description);
          $('select[id="group"]').val(ui.item.group);
          $('input[id="unit"]').val(ui.item.unit);
          $('input[id="minimum_quantity"]').val(ui.item.minimum_quantity);

          $('input[id="received_quantity"]').focus();

          return false;
        }
      })
      .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
        $( ul ).addClass('list divider-full-bleed');

        return $( "<li class='tile'>" )
        .append( '<a class="tile-content ink-reaction"><div class="tile-text">' + item.label + '</div></a>' )
        .appendTo( ul );
      };
    }
  });

  // input item description autocomplete
  $.ajax({
    url: $( 'input[id="item_description"]' ).data('source'),
    dataType: "json",
    success: function (data) {
      $( 'input[id="item_description"]' ).autocomplete({
        source: function (request, response) {
          var results = $.ui.autocomplete.filter(data, request.term);
          response(results.slice(0, 10));
        }
      });
    }
  });

  // input alt part number autocomplete
  $.ajax({
    url: $( 'input[id="alternate_part_number"]' ).data('source'),
    dataType: "json",
    success: function (data) {
      $( 'input[id="alternate_part_number"]' ).autocomplete({
        source: function (request, response) {
          var results = $.ui.autocomplete.filter(data, request.term);
          response(results.slice(0, 10));
        }
      });
    }
  });

  // input unit autocomplete
  $.ajax({
    url: $( 'input[id="unit"]' ).data('source'),
    dataType: "json",
    success: function (data) {
      $( 'input[id="unit"]' ).autocomplete({
        source: function (request, response) {
          var results = $.ui.autocomplete.filter(data, request.term);
          response(results.slice(0, 10));
        }
      });
    }
  });

  // input stores autocomplete
  $.ajax({
    url: $( 'input[id="stores"]' ).data('source'),
    dataType: "json",
    success: function (data) {
      $( 'input[id="stores"]' ).autocomplete({
        source: function (request, response) {
          var results = $.ui.autocomplete.filter(data, request.term);
          response(results.slice(0, 10));
        }
      });
    }
  });

  $.ajax({
    url: $( 'input[id="edit_stores"]' ).data('source'),
    dataType: "json",
    success: function (data) {
      $( 'input[id="edit_stores"]' ).autocomplete({
        source: function (request, response) {
          var results = $.ui.autocomplete.filter(data, request.term);
          response(results.slice(0, 10));
        }
      });
    }
  });

  // input serial number
  $( 'input[id="serial_number"]' ).on('change', function(){
    if ($(this).val() != ''){
      $('input[id="received_quantity"]').val('1').attr('readonly', true);
    } else {
      $('input[id="received_quantity"]').attr('readonly', false);
    }
  });

  $(buttonEditDocumentItem).on('click', function(e){
    e.preventDefault();

    var id = $(this).data('todo').todo;
    var data_send = {
                id: id
            };
    var save_method;

    save_method = 'update';
    /*$('#ajax-form-create-document')[0].reset(); // reset form on modals*/


    $.ajax({
      url : "<?=site_url($module['route'] .'/ajax_editItem/')?>/"+id,
      type: "GET",
      data: data_send,
      dataType: "JSON",
      success: function(response)
      { 
        var action = "<?=site_url($module['route'] .'/edit_item')?>";
        console.log(JSON.stringify(response));
        $('[name="serial_number"]').val(response.serial_number);
        $('[name="part_number"]').val(response.part_number);
        $('[name="description"]').val(response.description);
        $('[name="alternate_part_number"]').val(response.alternate_part_number);
        $('[name="group"]').val(response.group);
        $('[name="received_quantity"]').val(response.quantity);
        $('[name="minimum_quantity"]').val(response.minimum_quantity);
        $('[name="unit"]').val(response.unit);
        $('[name="received_unit_value"]').val(response.unit_price);
        $('[name="condition"]').val(response.condition);
        $('[name="stores"]').val(response.stores);
        // $('[name="expired_date"]').val(response.expired_date);
        // $('[name="purchase_order_number"]').val(response.purchase_order_number);
        // $('[name="reference_number"]').val(response.reference_number);
        // $('[name="awb_number"]').val(response.awb_number);
        $('[name="remarks"]').val(response.remarks);
        $('[name="item_id"]').val(id);
        // $('[name="document_number_receipts_items"]').val(response.document_number);
        // $('[name="category_receipts_items"]').val(response.category);
        // $('[name="warehouse_receipts_items"]').val(response.warehouse);
        // $('[name="master_items_id"]').val(i);
        
 
 
        $('#modal-add-item').modal('show'); // show bootstrap modal when complete loaded
        $('.modal-title').text('Edit Item'); // Set title to Bootstrap modal title
        $('#modal-add-item form').attr('action', action);
 
      },
      error: function (jqXHR, textStatus, errorThrown)
      {
        alert('Error get data from ajax');
      }
    });  
  });

});
</script>

<?=html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock()?>

<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
  <section class="has-actions style-default">
    <div class="section-body">
      <?=form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form-create-document'));?>
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
                        <input type="text" name="document_number" id="document_number" class="form-control" maxlength="6" value="<?=$_SESSION['return']['document_number'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_doc_number');?>" required>
                        <label for="document_number">Document No.</label>
                      </div>
                      <span class="input-group-addon"><?=return_format_number();?></span>
                    </div>
                  </div>

                  <div class="form-group">
                    <input type="text" name="issued_date" id="issued_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control" value="<?=$_SESSION['return']['issued_date'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_issued_date');?>" required>
                    <input type="hidden" name="opname_start_date" id="opname_start_date" data-date-format="yyyy-mm-dd" class="form-control" value="<?=last_publish_date();?>" readonly>
                    <label for="issued_date">Date</label>
                  </div>

                  <div class="form-group">
                    <input type="text" name="issued_by" id="issued_by" class="form-control" value="<?=$_SESSION['return']['issued_by'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_issued_by');?>" required>
                    <label for="issued_by">Released By</label>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                  <div class="form-group">
                    <select name="issued_to" id="issued_to" class="form-control" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_issued_to');?>" required>
                      <option value="">-- select destination</option>
                      <?php foreach (available_vendors(config_item('auth_inventory')) as $v => $vendor):?>
                        <option value="<?=$vendor;?>" <?=($_SESSION['return']['issued_to'] == $vendor) ? 'selected' : '';?>>
                          <?=$vendor;?>
                        </option>
                      <?php endforeach;?>
                    </select>
                    <label for="issued_to">Sent To</label>
                  </div>

                  <div class="form-group">
                    <textarea name="issued_address" id="issued_address" class="form-control" rows="5" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_issued_address');?>"><?=$_SESSION['return']['issued_address'];?></textarea>
                    <label for="issued_address">Address</label>
                  </div>
                </div>

                <div class="col-sm-12 col-lg-5">
                  <div class="form-group">
                    <textarea name="notes" id="notes" class="form-control" rows="4" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_notes');?>"><?=$_SESSION['return']['notes'];?></textarea>
                    <label for="notes">Notes</label>
                  </div>
                </div>
              </div>
            </div>

            <?php if (isset($_SESSION['return']['items'])):?>
              <div class="document-data table-responsive">
                <table class="table table-hover" id="table-document">
                  <thead>
                  <tr>
                    <th></th>
                    <th>Description</th>
                    <th>P/N</th>
                    <th>S/N</th>
                    <th colspan="2">Quantity</th>
                    <th colspan="2">Unit Value</th>
                    <th>Total Value</th>
                    <th>Condition</th>
                    <th>Remarks</th>
                  </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($_SESSION['return']['items'] as $i => $items):?>
                      <tr id="row_<?=$i;?>">
                        <td width="1">
                          <a href="<?=site_url($module['route'] .'/del_item/'. $i);?>" class="btn btn-icon-toggle btn-danger btn-sm btn_delete_document_item">
                            <i class="fa fa-trash"></i>
                          </a>
                        </td>
                        <td>
                          <?=$items['description'];?>
                        </td>
                        <td class="no-space">
                          <?=$items['part_number'];?>
                        </td>
                        <td>
                          <?=$items['serial_number'];?>
                        </td>
                        <td>
                          <?=number_format($items['issued_quantity'], 2);?>
                        </td>
                        <td>
                          <?=$items['unit'];?>
                        </td>
                        <td>
                          <?=$items['insurance_currency'];?>
                        </td>
                        <td>
                          <?=number_format($items['insurance_unit_value'], 2);?>
                        </td>
                        <td>
                          <?=number_format(($items['insurance_unit_value'] * $items['issued_quantity']), 2);?>
                        </td>
                        <td>
                          <?=$items['condition'];?>
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
              <div class="col-xs-12">
                <div class="form-group">
                  <div class="input-group">
                    <div class="input-group-content">
                      <input type="text" id="search_stock_in_stores" class="form-control" data-target="<?=site_url($module['route'] .'/search_stock_in_stores/');?>">
                      <label for="search_stock_in_stores">Search item by S/N, P/N, Description</label>
                    </div>
                    <span class="input-group-addon">
                      <i class="md md-search"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12 col-lg-8">
                <div class="row">
                  <div class="col-sm-6 col-lg-8">
                    <fieldset>
                      <legend>General</legend>

                      <div class="form-group">
                        <input type="text" name="serial_number" id="serial_number" class="form-control input-sm" readonly>
                        <label for="serial_number">Serial Number</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="part_number" id="part_number" class="form-control input-sm" readonly>
                        <label for="part_number">Part Number</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="description" id="description" class="form-control input-sm" readonly>
                        <label for="description">Description</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="alternate_part_number" id="alternate_part_number" class="form-control input-sm" readonly>
                        <label for="alternate_part_number">Alt. Part Number</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="group" id="group" class="form-control input-sm" readonly>
                        <label for="group">Item Group</label>
                      </div>
                    </fieldset>
                  </div>
                  <div class="col-sm-6 col-lg-4">
                    <fieldset>
                      <legend>Storage</legend>

                      <div class="form-group">
                        <input type="number" name="issued_quantity" id="issued_quantity" class="form-control input-sm" value="1" required>
                        <label for="issued_quantity">Quantity</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="maximum_quantity" id="maximum_quantity" data-tag-name="maximum_quantity" class="form-control input-sm" value="0" readonly>
                        <label for="maximum_quantity">Maximum Quantity</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="unit" id="unit" class="form-control input-sm" readonly>
                        <label for="unit">Unit of Measurement</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="condition" id="condition" class="form-control input-sm" readonly>
                        <label for="condition">Item Condition</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="stores" id="stores" class="form-control input-sm" readonly>
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
                    <input type="text" name="insurance_unit_value" id="insurance_unit_value" class="form-control input-sm" value="1">
                    <label for="insurance_unit_value">Insurance Unit Value</label>
                  </div>

                  <div class="form-group">
                    <input type="text" name="insurance_currency" id="insurance_currency" class="form-control input-sm" value="IDR">
                    <label for="insurance_currency">Insurance Currency</label>
                  </div>

                  <div class="form-group">
                    <input type="text" name="awb_number" id="awb_number" class="form-control input-sm">
                    <label for="awb_number">AWB Number</label>
                  </div>

                  <div class="form-group">
                    <textarea name="remarks" id="remarks" data-tag-name="remarks" class="form-control input-sm"></textarea>
                    <label for="remarks">Remarks</label>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <input type="hidden" id="stock_in_stores_id" name="stock_in_stores_id">
            <input type="hidden" id="issued_unit_value" name="issued_unit_value">

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
    url: $('#search_stock_in_stores').data('target'),
    dataType: "json",
    success: function (resource) {
      $('#search_stock_in_stores').autocomplete({
        autoFocus: true,
        minLength: 3,

        source: function (request, response) {
          var results = $.ui.autocomplete.filter(resource, request.term);
          response(results.slice(0, 5));
        },

        focus: function( event, ui ) {
          return false;
        },

        select: function( event, ui ) {
          $('#part_number').val( ui.item.part_number );
          $('#serial_number').val( ui.item.serial_number );
          $('#description').val( ui.item.description );
          $('#alternate_part_number').val( ui.item.alternate_part_number );
          $('#group').val( ui.item.group );
          $('#maximum_quantity').val( ui.item.quantity );
          $('#unit').val( ui.item.unit );
          $('#condition').val( ui.item.condition );
          $('#stores').val( ui.item.stores );
          $('#stock_in_stores_id').val( ui.item.id );
          $('#issued_unit_value').val( ui.item.unit_value );

          $('input[id="issued_quantity"]').attr('data-rule-max', parseInt(ui.item.quantity)).attr('data-msg-max', 'max available '+ ui.item.quantity);

          $('#issued_quantity').attr('max', ui.item.quantity).focus();

          $('#search_stock_in_stores').val('');

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

  $('input[id="issued_quantity"]').on('change', function (e) {
    if (parseInt($(this).val()) > parseInt($('input[id="maximum_quantity"]').val())){
      alert('Maximum limit is ' + max_quantity);
      $(this).val(max_quantity);
      $(this).focus();
    }

    return !(e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46);
  });
});
</script>

<?=html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock()?>

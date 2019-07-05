<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
  <section class="has-actions style-default">
    <div class="section-body">
      <?=form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form-create-document'));?>
        <div class="card">
          <div class="card-head style-primary-dark">
            <header><?=PAGE_TITLE." ".strtoupper( $_SESSION['request']['request_to'] == 0 ? "budget control":"mrp");?></header>
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
                        <input type="text" name="pr_number" id="pr_number" class="form-control" value="<?=$_SESSION['request']['pr_number'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_doc_number');?>" >
                        <label for="pr_number">Document No.</label>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <input type="text" name="required_date" id="required_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-end-date="0d" class="form-control" value="<?=$_SESSION['request']['required_date'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_required_date');?>" required>
                    <label for="required_date">Required Date</label>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                  <div class="form-group">
                    <input type="text" name="suggested_supplier" id="suggested_supplier" class="form-control" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_suggested_supplier');?>" data-autocomplete="<?=site_url($module['route'] .'/get_available_vendors');?>" value="<?=$_SESSION['request']['suggested_supplier'];?>" required>
                    <label for="suggested_supplier">Suggested Supplier</label>
                  </div>

                  <div class="form-group">
                    <input type="text" name="deliver_to" id="deliver_to" class="form-control" value="<?=$_SESSION['request']['deliver_to'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_deliver_to');?>" required>
                    <label for="deliver_to">Deliver To</label>
                  </div>
                </div>

                <div class="col-sm-12 col-lg-5">
                  <div class="form-group">
                    <textarea name="notes" id="notes" class="form-control" rows="3" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_notes');?>"><?=$_SESSION['request']['notes'];?></textarea>
                    <label for="notes">Notes</label>
                  </div>
                </div>
              </div>
            </div>

            <?php if (isset($_SESSION['request']['items'])):?>
              <?php $grand_total = array();?>
              <?php $total_quantity = array();?>
              <div class="document-data table-responsive">
                <table class="table table-hover" id="table-document">
                  <thead>
                  <tr>
                    <th></th>
                    <th>Description</th>
                    <th>P/N</th>
                    <th>Additional Info</th>
                    <th class="text-right">Qty</th>
                    <th>Unit</th>
                  </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($_SESSION['request']['items'] as $i => $items):?>
                      <?php $grand_total[] = $items['total'];?>
                      <?php $total_quantity[] = $items['quantity'];?>
                      <tr id="row_<?=$i;?>">
                        <td width="1">
                          <a href="<?=site_url($module['route'] .'/del_item/'. $i);?>" class="btn btn-icon-toggle btn-danger btn-sm btn_delete_document_item">
                            <i class="fa fa-trash"></i>
                          </a>
                        </td>
                        <td>
                          <?=print_string($items['product_name']);?>
                        </td>
                        <td class="no-space">
                          <?=print_string($items['part_number']);?>
                        </td>
                        <td class="no-space">
                          <?=print_string($items['additional_info']);?>
                        </td>
                        <td>
                          <?=print_number($items['quantity'], 2);?>
                        </td>
                        <td>
                          <?=print_string($items['unit']);?>
                        </td>
                      </tr>
                    <?php endforeach;?>
                  </tbody>
                  <tfoot>
                    <th></th>
                    <th>Total</th>
                    <th></th>
                    <th></th>
                    <th><?=print_number(array_sum($total_quantity), 2);?></th>
                    <th></th>
                  </tfoot>
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
                      <input type="text" id="search_budget" class="form-control" data-target="<?=site_url($module['route'] .'/search_budget/');?>">
                      <label for="search_budget">Search item by P/N, Description</label>
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
                        <input type="text" name="part_number" id="part_number" class="form-control input-sm" data-source="<?=site_url($module['route'] .'/search_items_by_part_number/');?>">
                        <label for="part_number">Part Number</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="product_name" id="product_name" class="form-control input-sm" data-source="<?=site_url($module['route'] .'/search_items_by_product_name/');?>">
                        <label for="product_name">Description</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="group_name" id="group_name" class="form-control input-sm" data-source="<?=site_url($module['route'] .'/search_item_groups/');?>">
                        <label for="group_name">Group</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="unit" id="unit" class="form-control input-sm">
                        <label for="unit">Unit of Measurement</label>
                      </div>
                    </fieldset>
                  </div>
                  <div class="col-sm-6 col-lg-4">
                    <fieldset>
                      <legend>Required</legend>

                      <div class="form-group">
                        <input type="text" name="quantity" id="quantity" class="form-control input-sm" value="1" required>
                        <label for="quantity">Quantity</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="maximum_quantity" id="maximum_quantity" class="form-control input-sm" value="0" readonly>
                        <label for="maximum_quantity">Max. Quantity</label>

                        <input type="hidden" name="price" id="price" value="1">
                        <input type="hidden" name="total" id="total" value="0">
                        <input type="hidden" name="maximum_price" id="maximum_price" value="0">
                      </div>

                      <div class="form-group">
                        <input type="text" name="on_hand_quantity" id="on_hand_quantity" class="form-control input-sm" value="1" readonly>
                        <label for="on_hand_quantity">On Hand Quantity</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="minimum_quantity" id="minimum_quantity" class="form-control input-sm" value="1" readonly>
                        <label for="minimum_quantity">Min. Quantity</label>
                      </div>
                    </fieldset>
                  </div>
                </div>
              </div>

              <div class="col-sm-12 col-lg-4">
                <fieldset>
                  <legend>Optional</legend>

                  <div class="form-group">
                    <textarea name="additional_info" id="additional_info" data-tag-name="additional_info" class="form-control input-sm"></textarea>
                    <label for="additional_info">Additional Info/Remarks</label>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <input type="hidden" id="inventory_monthly_budget_id" name="inventory_monthly_budget_id">
            <input type="hidden" id="item_source" name="item_source">
            <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>

            <button type="submit" id="modal-add-item-submit" class="btn btn-primary btn-create ink-reaction" disabled>
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

  $('[data-provide="datepicker"]').datepicker({
    autoclose: true,
    todayHighlight: true,
    format: 'yyyy-mm-dd'
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
      console.log(data);
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
    url: $('#suggested_supplier').data('autocomplete'),
    dataType: "json",
    success: function (data) {
      $('#suggested_supplier').autocomplete({
        source: function (request, response) {
          var results = $.ui.autocomplete.filter(data, request.term);
          response(results.slice(0, 10));
        }
      });
    }
  });

  $.ajax({
    url: $('#search_budget').data('target'),
    dataType: "json",
    error: function(xhr,response,results){
      console.log(xhr.responseText);
    },
    success: function (resource) {
      $('#search_budget').autocomplete({
        autoFocus: true,
        minLength: 3,

        source: function (request, response) {
          var results = $.ui.autocomplete.filter(resource, request.term);
          response(results.slice(0, 5));
          console.log(results);
        },

        focus: function( event, ui ) {
          return false;
        },

        select: function( event, ui ) {
          var maximum_quantity  = parseInt(ui.item.ytd_quantity) - parseInt(ui.item.ytd_used_quantity);
          var maximum_price     = parseInt(ui.item.ytd_budget) - parseInt(ui.item.ytd_used_budget);

          $('#product_name').val( ui.item.product_name );
          $('#part_number').val( ui.item.product_code );
          $('#group_name').val( ui.item.group_name );
          $('#unit').val( ui.item.measurement_symbol );
          $('#additional_info').val( ui.item.additional_info );
          $('#inventory_monthly_budget_id').val( ui.item.id );
          $('#item_source').val(ui.item.source);
          $('#price').val( parseInt(ui.item.price) );
          $('#maximum_price').val( maximum_price );
          $('#maximum_quantity').val( maximum_quantity );
          $('#total').val( parseInt(ui.item.price) ).trigger('change');

          $('input[id="quantity"]').attr('data-rule-max', maximum_quantity).attr('data-msg-max', 'max available '+ maximum_quantity);

          // $('input[id="price"]').attr('data-rule-max', maximum_price).attr('data-msg-max', 'max allowed '+ maximum_price);

          // $('input[id="total"]').attr('data-rule-max', maximum_price).attr('data-msg-max', 'max allowed '+ maximum_price);

          $('#quantity').attr('max', maximum_quantity).focus();
          // $('#total').attr('max', maximum_price);

          $('#search_budget').val('');

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
      console.log(resource);
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
          $('input[id="additional_info"]').val('Alternate P/N: ' + ui.item.alternate_part_number);
          $('input[id="product_name"]').val(ui.item.description);
          $('input[id="group_name"]').val(ui.item.group);
          $('input[id="unit"]').val(ui.item.unit);
          $('input[id="quantity"]').val(1);
          $('input[id="minimum_quantity"]').val(ui.item.minimum_quantity);
          $('input[id="on_hand_quantity"]').val(ui.item.on_hand_quantity);
          $('input[id="price"]').val(ui.item.price);
          $('input[id="total"]').val(ui.item.price).trigger('change');;

          $('input[id="quantity"]').focus();

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
    url: $( 'input[id="product_name"]' ).data('source'),
    dataType: "json",
    success: function (data) {
      $( 'input[id="product_name"]' ).autocomplete({
        source: function (request, response) {
          var results = $.ui.autocomplete.filter(data, request.term);
          response(results.slice(0, 10));
        }
      });
    }
  });

  $.ajax({
    url: $( 'input[id="group_name"]' ).data('source'),
    dataType: "json",
    success: function (data) {
      $( 'input[id="group_name"]' ).autocomplete({
        source: function (request, response) {
          var results = $.ui.autocomplete.filter(data, request.term);
          response(results.slice(0, 10));
        }
      });
    }
  });

  $("#quantity").on("keydown keyup", function(){
    var max_price   = parseInt($("#maximum_price").val()) / parseInt($("#quantity").val());

    if ( parseInt($("#price").val()) > max_price ){
      $("#price").val(max_price);
    }

    sum();
  });

  $("#price").on("keydown keyup", sum);

  $("#total").on("change", function(){
    if ( parseInt($(this).val()) > parseInt($("#maximum_price").val()) ){

      $("#modal-add-item-submit").prop("disabled", true);

      $("#price").closest("div").addClass("has-error").append('<p class="help-block total-error">Not allowed!</p>').focus();

      // toastr.options.timeOut = 10000;
      // toastr.options.positionClass = 'toast-top-right';
      // toastr.error('Price or total price is over maximum price allowed! You can not add this item.');
    } else {
      console.log(321)
      $("#price").closest("div").removeClass("has-error");
      $(".total-error").remove();
      $("#modal-add-item-submit").prop("disabled", false);
    }
  })
});

function sum(){
  var total = parseInt($("#quantity").val()) * parseInt($("#price").val());

  $("#total").val(total).trigger("change");
}
</script>

<?=html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock()?>

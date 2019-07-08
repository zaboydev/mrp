<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="has-actions style-default">
  <div class="section-body">

    <?=form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form_approval'));?>

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
                    <input type="hidden" name="category" id="category" value="<?=$entity['category'];?>">
                    <input type="text" name="document_number" id="document_number" class="form-control" maxlength="6" value="<?=$document_number;?>" required>
                    <label for="document_number">Document No.</label>
                  </div>
                  <span class="input-group-addon"><?=order_format_number($entity['category']);?></span>
                </div>
              </div>

              <div class="form-group">
                <input type="text" name="document_date" id="document_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-end-date="0d" class="form-control" value="<?=$entity['document_date'];?>" required>
                <label for="document_date">Date</label>
              </div>

              <div class="form-group">
                <input type="text" name="issued_by" id="issued_by" class="form-control" value="<?=$issued_by;?>" required>
                <label for="issued_by">Issued By</label>
              </div>
              <div class="form-group">
                <select name="payment_type" id="payment_type" class="form-control" data-input-type="autoset" required>
                  <option value="CREDIT" <?=('CREDIT' == $tipe) ? 'selected' : '';?>>Credit</option>
                  <option value="CASH" <?=('CASH' == $tipe) ? 'selected' : '';?>>Cash</option>
                </select>
                <input type="hidden" name="discount" id="discount" class="form-control" value="<?=$entity['discount'];?>">
                <label for="discount">Payment Type</label>
              </div>

              <div class="form-group">
                <input type="hidden" name="taxes" id="taxes" class="form-control" value="<?=$entity['taxes'];?>">
                <!-- <label for="taxes">Taxes</label> -->
              </div>

              <div class="form-group">
                <input type="hidden" name="shipping_cost" id="shipping_cost" class="form-control" value="<?=$entity['shipping_cost'];?>">
                <!-- <label for="shipping_cost">Shipping Cost</label> -->
              </div>

              <div class="form-group">
                <textarea name="notes" id="notes" class="form-control"><?=$entity['notes'];?></textarea>
                <label for="notes">Notes</label>
              </div>
            </div>

            <div class="col-sm-6 col-lg-3">
              <div class="form-group">
                <input type="text" name="vendor" id="vendor" class="form-control" value="<?=$entity['vendor'];?>" readonly>
                <label for="vendor">Vendor</label>
              </div>

              <div class="form-group">
                <textarea name="vendor_address" id="vendor_address" class="form-control"><?=$entity['vendor_address'];?></textarea>
                <label for="vendor_address">Address</label>
              </div>

              <div class="form-group">
                <input type="text" name="vendor_country" id="vendor_country" class="form-control" value="<?=$entity['vendor_country'];?>" required>
                <label for="vendor_country">Country</label>
              </div>

              <div class="form-group">
                <input type="text" name="vendor_attention" id="vendor_attention" class="form-control" value="<?=$entity['vendor_attention'];?>" required>
                <label for="vendor_attention">Phone/Email/PIC</label>
              </div>

              <div class="form-group">
                <input type="text" name="reference_quotation" id="reference_quotation" class="form-control" value="<?=$entity['reference_quotation'];?>" required>
                <label for="reference_quotation">Ref. Quotation</label>
              </div>
            </div>

            <div class="col-sm-6 col-lg-3">
              <div class="form-group">
                <input type="text" name="deliver_company" id="deliver_company" class="form-control" value="<?=$entity['deliver_company'];?>" data-target="<?=site_url($module['route'] .'/search_deliver/');?>" required>
                <label for="deliver_company">Deliver To</label>
              </div>

              <div class="form-group">
                <textarea name="deliver_address" id="deliver_address" class="form-control"><?=$address;?></textarea>
                <label for="deliver_address">Address</label>
              </div>

              <div class="form-group">
                <input type="text" name="deliver_country" id="deliver_country" class="form-control" value="<?=$country;?>" required>
                <label for="deliver_country">Country</label>
              </div>

              <div class="form-group">
                <input type="text" name="deliver_attention" id="deliver_attention" class="form-control" value="<?=$attention;?>" required>
                <label for="deliver_attention">Phone/Email/PIC</label>
              </div>
            </div>

            <div class="col-sm-6 col-lg-3">
              <div class="form-group">
                <input type="text" name="bill_company" id="bill_company" class="form-control" value="<?=$entity['bill_company'];?>" required>
                <label for="bill_company">Bill To</label>
              </div>

              <div class="form-group">
                <textarea name="bill_address" id="bill_address" class="form-control"><?=$address;?></textarea>
                <label for="bill_address">Address</label>
              </div>

              <div class="form-group">
                <input type="text" name="bill_country" id="bill_country" class="form-control" value="<?=$country;?>" required>
                <label for="bill_country">Country</label>
              </div>

              <div class="form-group">
                <input type="text" name="bill_attention" id="bill_attention" class="form-control" value="<?=$attention;?>" required>
                <label for="bill_attention">Phone/Email/PIC</label>
              </div>
            </div>
          </div>
        </div>

        <div class="document-data table-responsive">
          <table class="table table-hover" id="table-document">
            <thead>
              <tr>
                <th class="middle-alignment">No.</th>
                <th class="middle-alignment">Description</th>
                <th class="middle-alignment">Part Number</th>
                <th class="middle-alignment">Alt. P/N</th>
                <th class="middle-alignment text-center" colspan="2">Quantity</th>
                <th class="middle-alignment">Unit Price <?=$entity['default_currency'];?></th>
                <th class="middle-alignment">Core Charge <?=$entity['default_currency'];?></th>
                <th class="middle-alignment">Total Amount <?=$entity['default_currency'];?></th>
                <th class="middle-alignment">Ref. POE</th>
                <th class="middle-alignment">Remarks</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($entity['request'] as $i => $item):?>
                <tr id="row_<?=$i;?>">
                  <td width="1">
                    <?=$i+1;?>
                  </td>
                  <td>
                    <?=$item['description'];?>
                  </td>
                  <td class="no-space">
                    <?=$item['part_number'];?>
                  </td>
                  <td class="no-space">
                    <?=$item['alternate_part_number'];?>
                  </td>
                  <td class="text-right">
                    <?=number_format($item['quantity'], 2);?>
                  </td>
                  <td>
                    <?=$item['unit'];?>
                  </td>
                  <td>
                    <?=number_format($item['unit_price'], 2);?>
                  </td>
                  <td>
                    <?=number_format($item['core_charge'], 2);?>
                  </td>
                  <td>
                    <?=number_format($item['total_amount'], 2);?>
                  </td>
                  <td>
                    <?=print_string($entity['evaluation_number']);?>
                  </td>
                  <td>
                    <?=$item['remarks'];?>
                  </td>
                </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?=form_close();?>
  </div>

  <div class="section-action style-default-bright">
    <div class="section-floating-action-row">
      <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?=site_url($module['route'] .'/save/'. $entity['id']);?>">
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

function popup(mylink, windowname){
  var height = window.innerHeight;
  var widht;
  var href;

  if (screen.availWidth > 768){
    width = 769;
  } else {
    width = screen.availWidth;
  }

  var left = (screen.availWidth / 2) - (width / 2);
  var top = 0;
  // var top = (screen.availHeight / 2) - (height / 2);

  if (typeof(mylink) == 'string') href = mylink;
  else href = mylink.href;

  window.open(href, windowname, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+width+', height='+height+', top='+top+', left='+left);

  if (! window.focus) return true;
  else return false;
}

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
$.ajax({
    url: $('#deliver_company').data('target'),
    dataType: "json",
    error: function(xhr,response,results){
      console.log(xhr.responseText);
    },
    success: function (resource) {
      console.log(resource);
      $('#deliver_company').autocomplete({
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
          $('#deliver_address').val( ui.item.address );        

          return false;
        }
      })
      .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
        console.log(item);
        $( ul ).addClass('list divider-full-bleed');

        return $( "<li class='tile'>" )
        .append( '<a class="tile-content ink-reaction"><div class="tile-text">' + item.warehouse + '</div></a>' )
        .appendTo( ul );
      };
    }
  });
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
  var formDocument              = $('#form_approval');
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

  $('#vendor').on('focusin', function(){
    $(this).data('val', $(this).val());
  });

  $('#vendor').on('change', function(){
    var prev = $(this).data('val');
    var current = $(this).val();
    var url = $(this).data('source');

    if (prev != ''){
      var conf = confirm("Changing the vendor will remove the items that have been added. Continue?");

      if (conf == false){
        return false;
      }
    }

    window.location.href = url + '/' + current;
  });

  $.ajax({
    url: $('#search_poe_item').data('target'),
    dataType: "json",
    success: function (resource) {
      $('#search_poe_item').autocomplete({
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
          $('#description').val( ui.item.description );
          $('#quantity').val( ui.item.quantity );
          $('#unit_price').val( ui.item.unit_price );
          $('#core_charge').val( ui.item.core_charge );
          $('#total_amount').val( (ui.item.core_charge * ui.item.quantity) + (ui.item.unit_price * ui.item.quantity) );
          $('#unit').val( ui.item.unit );
          $('#purchase_order_evaluation_items_vendors_id').val( ui.item.id );

          $('input[rel="unit_price"]').val( ui.item.unit_price );

          $('#alternate_part_number').focus();

          $('#search_poe_item').val('');

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
});
</script>

<?=html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock()?>

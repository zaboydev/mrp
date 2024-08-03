<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>

  <section class="has-actions style-default">
    <div id="dialog" title="Processing Stock Opname">
      <div class="progress-label">Processing...</div>
      <div id="progressbar"></div>
    </div>
    <div class="section-body col-lg-6 col-md-5 col-lg-offset-3">
      <?=form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form-create-document'));?>
          <div class="card">
              <div class="card-head style-primary-dark">
                <header>Select Date</header>
              </div>
              <div class="card-body no-padding">
              <?php
                if ( $this->session->flashdata('alert') )
                  render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
              ?>  
                <div class="document-header force-padding">              
                  <div class="row">
                    <div class="col-lg-12 col-sm-12">
                      <div class="form-group">
                       <input type="text" name="opname_start_date" id="opname_start_date" data-provide="datepicker_opname" data-date-format="yyyy-mm-dd" class="form-control" placeholder="Start Date" value="<?=last_publish_date();?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_opname_start_date');?>" required readonly>    

                        <input type="text" name="opname_end_date" id="opname_end_date" data-provide="datepicker_opname_end" data-date-format="yyyy-mm-dd" class="form-control" placeholder="End Date" value="<?=$_SESSION['opname']['opname_end_date'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_opname_end_date');?>" required>   
                      </div>
                    </div>                           
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <a href="<?=site_url($module['route'] .'/opname_stock');?>" class="btn btn-block btn-primary ink-reaction" id="btn-submit-document">
                        Start Stock Opname
                      </a>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <a class="btn btn-block btn-danger ink-reaction" href="<?=site_url($module['route']);?>" id="cancel-btn">
                        Back
                      </a>
                    </div>
                  </div>
                </div>
                <div id="progressBar">
                  
                </div>                
              </div>

            </div>
              </div>
            </div>
          </div>

            <div class="modal-footer">
              
              
            </div>
          <?=form_close();?>
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

  //progressbar
  var progressTimer,
      progressbar = $( "#progressbar" ),
      progressLabel = $( ".progress-label" ),
      dialogButtons = [{
        text: "Cancel Download",
        click: closeDownload
      }],
      dialog = $( "#dialog" ).dialog({
        autoOpen: false,
        closeOnEscape: false,
        resizable: false,
        // buttons: dialogButtons,
        open: function() {
          progressTimer = setTimeout( progress, 2000 );
        },
        beforeClose: function() {
          downloadButton.button( "option", {
            disabled: false,
            label: "Start Download"
          });
        }
      }),
      downloadButton = $( "#btn-submit-document" )
        .button()
        .on( "click", function() {
          $( this ).button( "option", {
            disabled: true,
            label: "Downloading..."
          });
          dialog.dialog( "open" );
        });
 
    progressbar.progressbar({
      value: false,
      change: function() {
        progressLabel.text( "Current Progress: " + progressbar.progressbar( "value" ) + "%" );
      },
      complete: function() {
        progressLabel.text( "Complete!" );
        dialog.dialog( "option", "buttons", [{
          text: "Close",
          click: closeDownload
        }]);
        $(".ui-dialog button").last().trigger( "focus" );
       
      }
    });
 
    function progress() {
      var val = progressbar.progressbar( "value" ) || 0;
      // var waktu = $('#waktu').val();
 
      progressbar.progressbar( "value", val + Math.floor( Math.random() * 3 ) );
 
      if ( val <= 99 ) {
        progressTimer = setTimeout( progress, 4000 );
      }
    }
 
    function closeDownload() {
      clearTimeout( progressTimer );
      dialog
        .dialog( "option", "buttons", dialogButtons )
        .dialog( "close" );
      progressbar.progressbar( "value", false );
      progressLabel
        .text( "Starting download..." );
      downloadButton.trigger( "focus" );
    }
  //progress bar

  var startDate = new Date(<?=config_item('period_year');?>, <?=config_item('period_month');?>-1, 1);
  var lastDate = new Date(<?=config_item('period_year');?>, <?=config_item('period_month');?>, 0);

  var last_opname = $('[name="opname_start_date"]').val();
  var today       = new Date();
  today.setDate(today.getDate() - 3);

  $('[data-provide="datepicker_opname"]').datepicker({
    autoclose: true,
    // todayHighlight: true,
    format: 'yyyy-mm-dd',
    startDate: last_opname,
    endDate: last_opname
  });

  $('[data-provide="datepicker_opname_end"]').datepicker({
    autoclose: true,
    // todayHighlight: true,
    format: 'yyyy-mm-dd',
    startDate: last_opname,
    endDate: today,
  });

  $('[data-provide="datepicker"]').datepicker({
    autoclose: true,
    todayHighlight: true,
    format: 'yyyy-mm-dd'
  });

  $('#expired_date').datepicker({
    autoclose: true,
    todayHighlight: true,
    format: 'yyyy-mm-dd'
    //startDate: '0d'
  });

  $('#edit_expired_date').datepicker({
    autoclose: true,
    todayHighlight: true,
    format: 'yyyy-mm-dd'
    //startDate: '0d'
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

  $('#search_purchase_order').on('click focus', function(){
    $.ajax({
      url: $('#search_purchase_order').data('source'),
      dataType: "json",
      success: function (resource) {
        $('#search_purchase_order').autocomplete({
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
            if (ui.item.default_currency == 'USD'){
              var unit_value = parseInt(ui.item.unit_price) * parseInt(ui.item.exchange_rate);
            } else {
              var unit_value = parseInt(ui.item.unit_price);
            }

            $('#consignor').val( ui.item.vendor );
            $('#serial_number').val( ui.item.serial_number );
            $('#part_number').val( ui.item.part_number );
            $('#description').val( ui.item.description );
            $('#alternate_part_number').val( ui.item.alternate_part_number );
            $('#group').val( ui.item.group );
            $('#received_quantity').val( parseInt(ui.item.quantity) );
            $('#unit').val( ui.item.unit );
            $('#received_unit_value').val( parseInt(unit_value) );
            $('#purchase_order_item_id').val( ui.item.id );
            $('#purchase_order_number').val( ui.item.document_number );
            $('#kode_stok').val( ui.item.kode_stok );
            if(ui.item.default_currency == 'USD'){
              $('[name="kurs"]').val('dollar');
              
            }else{
              $('[name="kurs"]').val('rupiah');

            }

            $('#received_quantity').data('rule-max', parseInt(ui.item.quantity)).data('msg-max', 'max available '+ ui.item.quantity);

            // if (ui.item.serial_number != null){
            //   $( inputIssuedQuantity ).val(1).attr('readonly', true);
            // }

            $('#search_purchase_order').val('');

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
          $('#kode_stok').val( ui.item.kode_stok );

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
          $('input[id="alternate_part_number"]').val(ui.item.alternate_part_number);
          $('input[id="description"]').val(ui.item.description);
          $('select[id="group"]').val(ui.item.group);
          $('input[id="unit"]').val(ui.item.unit);
          $('input[id="minimum_quantity"]').val(ui.item.minimum_quantity);
          $('#kode_stok').val( ui.item.kode_stok );

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

  

  $('input[id="unit"]').keyup(function() {
    var unit_terima = $('input[id="unit"]').val();
    $('input[id="unit_terima"]').val(unit_terima);
  });

  $('input[id="no_expired_date"]').change(function() {
    if($('[id="no_expired_date"]').is(':checked')){
      $('input[id="expired_date"]').prop('readonly', true);
      $('input[id="expired_date"]').prop('required', false);
    }else{
      $('input[id="expired_date"]').prop('readonly', false);
      $('input[id="expired_date"]').prop('required', true);
    }
    
  });

  $('input[id="expired_date"]').change(function() {
    if($('input[id="expired_date"]').val() != ''){
      $('input[id="no_expired_date"]').prop('disabled', true);
      $('input[id="no_expired_date"]').prop('required', false);
    }else{
      $('input[id="no_expired_date"]').prop('disabled', false);
      $('input[id="no_expired_date"]').prop('required', true);
    }
    
  });

  $.ajax({
    url: $( 'input[id="edit_unit"]' ).data('source'),
    dataType: "json",
    success: function (data) {
      $( 'input[id="edit_unit"]' ).autocomplete({
        source: function (request, response) {
          var results = $.ui.autocomplete.filter(data, request.term);
          response(results.slice(0, 10));
        }
      });
    }
  });

  $.ajax({
    url: $( 'input[id="unit_pakai"]' ).data('source'),
    dataType: "json",
    success: function (data) {
      $( 'input[id="unit_pakai"]' ).autocomplete({
        source: function (request, response) {
          var results = $.ui.autocomplete.filter(data, request.term);
          response(results.slice(0, 10));
        }
      });
    }
  });

  $('input[id="unit_pakai"]').keyup(function() {
    var unit_used = $('input[id="unit_pakai"]').val();
    $('input[id="unit_used"]').val(unit_used);
  });

  $.ajax({
    url: $( 'input[id="edit_unit_pakai"]' ).data('source'),
    dataType: "json",
    success: function (data) {
      $( 'input[id="edit_unit_pakai"]' ).autocomplete({
        source: function (request, response) {
          var results = $.ui.autocomplete.filter(data, request.term);
          response(results.slice(0, 10));
        }
      });
    }
  });

  // input stores autocomplete
  $( 'input[id="stores"]' ).on('focus', function(){
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
  });

  $( 'input[id="edit_stores"]' ).on('focus', function(){
    $.ajax({
      url: $( 'input[id="edit_stores"]' ).data('source'),
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
  });

  // input serial number
  $( 'input[id="serial_number"]' ).on('change', function(){
    if ($(this).val() != ''){
      $('input[id="received_quantity"]').val('1').attr('readonly', false);
    } else {
      $('input[id="received_quantity"]').attr('readonly', false);
    }
  });

  //hitung qty konversi
  $('input[name="isi"]').keyup(function() {
    var isi = $(this).val();

    if(isi !=='' || isi > 0){
      var qty = $('[name="received_quantity"]').val();
      var qty_konversi = parseInt(qty) * parseInt(isi);
      $('[name="qty_konversi"]').val(qty_konversi);
    }
  });

  $('input[name="received_quantity"]').keyup(function() {
    var qty = $(this).val();

    if(qty !=='' || qty > 0){
      var isi = $('[name="isi"]').val();
      var qty_konversi = parseInt(qty) * parseInt(isi);
      $('[name="qty_konversi"]').val(qty_konversi);
    }
  });

  $('input[name="edit_isi"]').keyup(function() {
    var isi = $(this).val();

    if(isi !=='' || isi > 0){
      var qty = $('[name="received_quantity"]').val();
      var qty_konversi = parseInt(qty) * parseInt(isi);
      $('[name="edit_qty_konversi"]').val(qty_konversi);
    }
  });

  $(buttonEditDocumentItem).on('click', function(e){
    e.preventDefault();

    //var id = $(this).data('todo').id;
    var id = $(this).data('todo').todo;
    var data_send = {
                id: id
                //i: i
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
        console.log(JSON.stringify(response));
        $('[name="serial_number"]').val(response.serial_number);
        $('[name="part_number"]').val(response.part_number);
        $('[name="description"]').val(response.description);
        $('[name="alternate_part_number"]').val(response.alternate_part_number);
        $('[name="group"]').val(response.group);
        $('[name="received_quantity"]').val(response.received_quantity);
        $('[name="minimum_quantity"]').val(response.minimum_quantity);
        $('[name="unit"]').val(response.unit);
        // $('[name="received_unit_value"]').val(response.received_unit_value);
        $('[name="condition"]').val(response.condition);
        $('[name="stores"]').val(response.stores);
        $('[name="expired_date"]').val(response.expired_date);
        $('[name="purchase_order_number"]').val(response.purchase_order_number);
        $('[name="reference_number"]').val(response.reference_number);
        $('[name="awb_number"]').val(response.awb_number);
        $('[name="remarks"]').val(response.remarks);
        $('[name="item_id"]').val(id);
        $('[name="edit_kode_akunting"]').val(response.kode_akunting);
        $('[name="edit_kurs"]').val('rupiah');
        $('[name="edit_unit_pakai"]').val(response.unit_pakai);
        $('[name="edit_qty_konversi"]').val(response.hasil_konversi);
        $('[name="edit_kode_stok"]').val(response.kode_stok);
        if(response.isi){
          $('[name="edit_isi"]').val(response.isi); 
        }else{
          $('[name="edit_isi"]').val(response.qty_konversi);
        }
        // $('[name="edit_isi"]').val(response.isi);
        $('[name="qty_konversi"]').val(response.hasil_konversi);
        if(response.unit_value_dollar > 0){
          $('[name="edit_kurs"]').val('dollar');
          $('[name="received_unit_value"]').val(response.unit_value_dollar);

        }else{
          $('[name="edit_kurs"]').val('rupiah');
          $('[name="received_unit_value"]').val(response.received_unit_value);

        }
        if(response.kurs){
          $('[name="edit_kurs"]').val(response.kurs);
        }
        
        
 
 
        $('#modal-edit-item').modal('show'); // show bootstrap modal when complete loaded
        $('.modal-title').text('Edit Item'); // Set title to Bootstrap modal title
 
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
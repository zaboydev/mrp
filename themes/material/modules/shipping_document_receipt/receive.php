<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
  <section class="has-actions style-default">
    <div class="section-body">

      <?=form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form_receiving'));?>
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

                  <input type="hidden" name="category" value="<?=$entity['category'];?>">
                  <input type="hidden" name="received_from" value="<?=$entity['warehouse'];?>">
                  <input type="hidden" name="warehouse" value="<?=$entity['issued_to'];?>">

                  <div class="form-group">
                    <input type="text" name="document_number" id="document_number" class="form-control" value="<?=$entity['document_number'];?>" readonly>
                    <label for="document_number">Document No.</label>
                  </div>

                  <div class="form-group">
                    <input type="text" name="issued_date" id="issued_date" class="form-control" value="<?=$entity['issued_date'];?>" readonly>
                    <label for="issued_date">Issued Date</label>
                  </div>

                  <div class="form-group">
                    <input type="text" name="issued_by" id="issued_by" class="form-control" value="<?=$entity['issued_by'];?>" readonly>
                    <label for="issued_by">Released By</label>
                  </div>

                  <div class="form-group">
                    <input type="text" name="sent_by" id="sent_by" class="form-control" value="<?=$entity['sent_by'];?>" readonly>
                    <label for="sent_by">Packed By</label>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                  <div class="form-group">
                    <input type="text" name="received_date" id="received_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control" value="<?=date('Y-m-d');?>" required>
                    <input type="hidden" name="opname_start_date" id="opname_start_date" data-date-format="yyyy-mm-dd" class="form-control" value="<?=last_publish_date();?>" readonly>
                    <label for="received_date">Received Date</label>
                  </div>

                  <div class="form-group">
                    <input type="text" name="received_by" id="received_by" class="form-control" value="<?=config_item('auth_person_name');?>" required>
                    <label for="received_by">Received By</label>
                  </div>

                  <div class="form-group">
                    <input type="text" name="warehouse" id="warehouse" class="form-control" value="<?=$entity['issued_to'];?>" readonly>
                    <label for="warehouse">Base</label>
                  </div>
                </div>

                <div class="col-sm-12 col-lg-5">
                  <div class="form-group">
                    <textarea name="notes" id="notes" class="form-control" rows="4" readonly><?=$entity['notes'];?></textarea>
                    <label for="notes">Notes</label>
                  </div>
                </div>
              </div>
            </div>

            <?php if (isset($entity['items'])):?>
              <div class="document-data table-responsive">
                <table class="table table-hover" id="table-document">
                  <thead>
                  <tr>
                    <th>Group</th>
                    <th>Description</th>
                    <th>P/N</th>
                    <th>Alt. P/N</th>
                    <th>S/N</th>
                    <th>Condition</th>
                    <th>Unit</th>
                    <th>Sent</th>
                    <th>Received</th>
                    <th>Left</th>
                    <th>Stores</th>
                    <th>Remarks</th>                    
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($entity['items'] as $i => $items):?>
                      <?php if ($items['left_received_quantity'] > 0):?>
                      <tr id="row_<?=$i;?>">
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
                          <?=$items['condition'];?>
                        </td>
                        <td>
                          <?=$items['unit'];?>
                        </td>
                        <td>
                          <?=number_format($items['issued_quantity'], 2);?>
                        </td>
                        <td>
                          <?=number_format($items['issued_quantity']-$items['left_received_quantity'], 2);?>
                        </td>
                        <td>
                          <input type="number" name="items[<?=$items['id'];?>][received_quantity]" value="<?=$items['left_received_quantity'];?>" class="form-control input-sm" required>
                        </td>
                        <td>
                          <input type="text" data-search-for="stores" name="items[<?=$items['id'];?>][stores]" class="form-control input-sm" data-source="<?=site_url($module['route'] .'/search_stores?warehouse='. $entity['issued_to'] .'&category='. $entity['category']);?>" required>
                        </td>
                        <td>
                          <input type="text" name="items[<?=$items['id'];?>][remarks]" class="form-control input-sm">
                          <input type="hidden" name="items[<?=$items['id'];?>][last_stores]" class="form-control input-sm" value="<?=$items['stores'];?>">

                          <input type="hidden" name="items[<?=$items['id'];?>][last_warehouse]" class="form-control input-sm" value="<?=$entity['warehouse'];?>">
                           <input type="hidden" name="items[<?=$items['id'];?>][stock_in_stores_id]" class="form-control input-sm" value="<?=$items['stock_in_stores_id'];?>">

                          <input type="hidden" name="items[<?=$items['id'];?>][received_unit_value]" value="<?=$items['issued_unit_value'];?>">

                          <input type="hidden" name="items[<?=$items['id'];?>][issuance_item_id]" value="<?=$items['id'];?>">

                          <input type="hidden" name="items[<?=$items['id'];?>][part_number]" value="<?=$items['part_number'];?>">

                          <input type="hidden" name="items[<?=$items['id'];?>][description]" value="<?=$items['description'];?>">

                          <input type="hidden" name="items[<?=$items['id'];?>][serial_number]" value="<?=$items['serial_number'];?>">

                          <input type="hidden" name="items[<?=$items['id'];?>][condition]" value="<?=$items['condition'];?>">
                          <input type="hidden" name="items[<?=$items['id'];?>][id_stores_sementara]" value="<?=$items['id_stores_sementara'];?>">
                          <input type="hidden" name="items[<?=$items['id'];?>][issued_items_id]" value="<?=$items['id'];?>">
                        </td>
                        <td>
                          <?php if ($items['left_received_quantity'] > 0):?>
                          <a class="btn btn-danger btn-xs" href="<?=site_url($module['route'] .'/send_back/'. $id);?>" id="btn-send_back">Send Back</a>
                          <?php endif;?>
                        </td>
                      </tr>
                      <?php endif;?>
                    <?php endforeach;?>
                  </tbody>
                </table>
              </div>
            <?php endif;?>
          </div>
          <div class="card-actionbar">
            <div class="card-actionbar-row">
              <a href="<?=site_url($module['route'] .'/discard');?>" class="btn btn-flat btn-danger ink-reaction">
                Discard
              </a>
            </div>
          </div>
        </div>
      <?=form_close();?>
    </div>

    <div class="section-action style-default-bright">
      <div class="section-floating-action-row">
        <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?=site_url($module['route'] .'/save/'. $id);?>">
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

  $('[data-provide="datepicker"]').datepicker({
    autoclose: true,
    todayHighlight: true,
    format: 'yyyy-mm-dd',
    startDate: last_publish,
    // endDate: lastDate
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

  $('#btn-submit-document').on('click', function(e){
    e.preventDefault();
    $('#btn-submit-document').attr('disabled', true);

    var url = $(this).attr('href');
    var frm = $('#form_receiving');

    $.post(url, frm.serialize(), function(data){
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

      $('#btn-submit-document').attr('disabled', false);
    });
  });

  $('#btn-send_back').on('click', function(e){
    e.preventDefault();
    // $('#btn-submit-document').attr('disabled', true);

    var url = $(this).attr('href');
    var frm = $('#form_receiving');

    $.post(url, frm.serialize(), function(data){
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

      $('#btn-submit-document').attr('disabled', false);
    });
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

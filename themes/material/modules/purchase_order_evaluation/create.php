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
                    <input type="text" name="document_number" id="document_number" class="form-control" maxlength="6" value="<?=$_SESSION['poe']['document_number'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_doc_number');?>" required>
                    <label for="document_number">Document No.</label>
                  </div>
                  <span class="input-group-addon"><?=poe_format_number();?></span>
                </div>
              </div>

              <div class="form-group">
                <input type="text" name="document_date" id="document_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-end-date="0d" class="form-control" value="<?=$_SESSION['poe']['document_date'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_document_date');?>" required>
                <label for="document_date">Date</label>
              </div>

              <div class="form-group">
                <input type="text" name="document_reference" id="document_reference" class="form-control" value="<?=$_SESSION['poe']['document_reference'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_document_reference');?>" required>
                <label for="document_reference">Reference Document</label>
              </div>
            </div>

            <div class="col-sm-6 col-lg-4">
              <div class="form-group">
                <input type="text" name="created_by" id="created_by" class="form-control" value="<?=$_SESSION['poe']['created_by'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_created_by');?>" required>
                <label for="created_by">Created By</label>
              </div>

              <div class="form-group">
                <input type="text" name="approved_by" id="approved_by" class="form-control" value="<?=$_SESSION['poe']['approved_by'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_approved_by');?>" required>
                <label for="approved_by">Approved/Rejected By</label>
              </div>
			  
              <div class="form-group">
                <select name="default_currency" id="default_currency" class="form-control" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_default_currency');?>" required>
                  <option value="USD" <?=('USD' == $_SESSION['poe']['default_currency']) ? 'selected' : '';?>>USD (US Dolar)</option>
                  <option value="IDR" <?=('IDR' == $_SESSION['poe']['default_currency']) ? 'selected' : '';?>>IDR (Indonesian Rupiah)</option>
                </select>
                <label for="default_currency">Currency</label>
              </div>

              <div class="form-group">
                <input type="number" name="exchange_rate" id="exchange_rate" class="form-control" value="<?=$_SESSION['poe']['exchange_rate'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_exchange_rate');?>" required>
                <label for="exchange_rate">Exchange Rate IDR to USD</label>
              </div>
            </div>

            <div class="col-sm-12 col-lg-5">
              <div class="form-group">
                <textarea name="notes" id="notes" class="form-control" rows="4" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_notes');?>"><?=$_SESSION['poe']['notes'];?></textarea>
                <label for="notes">Notes</label>
              </div>
            </div>
          </div>
        </div>

        <?php if (isset($_SESSION['poe']['request'])):?>
          <div class="document-data table-responsive">
            <table class="table table-hover table-bordered">
              <thead>
                <tr>
                  <th class="middle-alignment" rowspan="2"></th>
                  <th class="middle-alignment" rowspan="2">Description</th>
                  <th class="middle-alignment" rowspan="2">P/N</th>
                  <th class="middle-alignment" rowspan="2">Remarks</th>
                  <th class="middle-alignment" rowspan="2">PR Number</th>
                  <th class="middle-alignment text-right" rowspan="2">Qty</th>

                  <?php foreach ($_SESSION['poe']['vendors'] as $key => $vendor):?>
                    <th class="middle-alignment text-center" colspan="4">
                      <?=anchor($module['route'] .'/set_selected_vendor/'. $key, $vendor['vendor'], 'style="color: blue"');?>
                    </th>
                  <?php endforeach;?>
                </tr>

                <tr>
                  <?php for ($v = 0; $v < count($_SESSION['poe']['vendors']); $v++):?>
                    <th class="middle-alignment text-center">Alt. P/N</th>
                    <th class="middle-alignment text-center">Unit Price</th>
                    <th class="middle-alignment text-center">Core Charge</th>
                    <th class="middle-alignment text-center">Total Amount</th>
                  <?php endfor;?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_SESSION['poe']['request'] as $id => $request):?>
                  <tr id="row_<?=$id;?>">
                    <td width="1">
                      <a href="<?=site_url($module['route'] .'/delete_request/'. $id);?>" class="btn btn-icon-toggle btn-danger btn-sm btn_delete_request">
                        <i class="fa fa-trash"></i>
                      </a>
                    </td>
                    <td>
                      <?=$request['description'];?>
                    </td>
                    <td class="no-space">
                      <?=$request['part_number'];?>
                    </td>
                    <td>
                      <?=$request['remarks'];?>
                    </td>
                    <td>
                      <?=$request['purchase_request_number'];?>
                    </td>
                    <td>
                      <?=number_format($request['quantity'], 2);?>
                    </td>

                    <?php foreach ($_SESSION['poe']['vendors'] as $key => $vendor):?>
                      <?php
                      if ($vendor['is_selected'] == 't'){
                        $style = 'background-color: green; color: white';
                      } else {
                        $style = '';
                      }
                      ?>

                      <td style="<?=$style;?>">
                        <?=$_SESSION['poe']['request'][$id]['vendors'][$key]['alternate_part_number'];?>
                      </td>

                      <td style="<?=$style;?>">
                        <?=$_SESSION['poe']['request'][$id]['vendors'][$key]['unit_price'];?>
                      </td>

                      <td style="<?=$style;?>">
                        <?=$_SESSION['poe']['request'][$id]['vendors'][$key]['core_charge'];?>
                      </td>

                      <td style="<?=$style;?>">
                        <?=$_SESSION['poe']['request'][$id]['vendors'][$key]['total'];?>
                      </td>
                    <?php endforeach;?>
                  </tr>
                <?php endforeach;?>
              </tbody>
            </table>
          </div>
        <?php endif;?>
      </div>
      <div class="card-actionbar">
        <div class="card-actionbar-row">
          <div class="pull-left">
            <a href="<?=site_url($module['route'] .'/add_request');?>" onClick="return popup(this, 'add_request')" class="btn btn-primary ink-reaction">
              Select Request
            </a>

            <?php if (!empty($_SESSION['poe']['request'])):?>
              <a href="<?=site_url($module['route'] .'/edit_request');?>" onClick="return popup(this, 'edit_request')" class="btn btn-primary ink-reaction">
                Edit Request
              </a>

              <a href="<?=site_url($module['route'] .'/add_vendor');?>" onClick="return popup(this, 'add_vendor')" class="btn btn-primary ink-reaction">
                Select Vendor
              </a>
            <?php endif;?>
          </div>

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
  var buttonDeleteDocumentItem  = $('.btn_delete_request');
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

  $.ajax({
    url: $('#search_request_item').data('target'),
    dataType: "json",
    success: function (resource) {
      $('#search_request_item').autocomplete({
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
          $('#description').val( ui.item.product_name );
          $('#additional_info').val( ui.item.additional_info );
          $('#quantity').val( ui.item.quantity );
          $('#price').val( ui.item.price );
          $('#total').val( ui.item.total );
          $('#unit').val( ui.item.unit );
          $('#pr_number').val( ui.item.pr_number );
          $('#inventory_purchase_request_detail_id').val( ui.item.id );

          $('input[rel="unit_price"]').val( ui.item.price );

          $('#alternate_part_number').focus();

          $('#search_request_item').val('');

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

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
                    <input readonly type="text" name="document_number" id="document_number" class="form-control" maxlength="6" value="[auto]" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_doc_number');?>" required>
                    <label for="document_number">Purpose Number</label>
                  </div>
                  <span class="input-group-addon"><?=payment_request_format_number();?></span>
                </div>
              </div>

              <div class="form-group">
                <input type="text" name="document_date" id="document_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-end-date="0d" class="form-control" value="<?=$_SESSION['payment_request']['date'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_date');?>" required>
                <label for="document_date">Date</label>
              </div>

              <div class="form-group">
                <input type="text" name="purposed_date" id="purposed_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-end-date="0d" class="form-control" value="<?=$_SESSION['payment_request']['purposed_date'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_purposed_date');?>" required>
                <label for="purposed_date"> Purposed Date</label>
              </div>

              
            </div>
            <div class="col-sm-6 col-lg-4">               

                <div class="form-group">
                    <select name="default_currency" id="default_currency" class="form-control" data-source="<?= site_url($module['route'] . '/set_default_currency'); ?>" required>
                    <?php foreach ($this->config->item('currency') as $key => $value) : ?>
                    <option value="<?=$key?>" <?= ($key == $_SESSION['payment_request']['currency']) ? 'selected' : ''; ?>><?=$value?></option>
                    <?php endforeach; ?>
                    </select>
                    <label for="default_currency">Currency</label>
                </div>

                <div class="form-group">
                    <select name="vendor" id="vendor" class="form-control" data-source="<?= site_url($module['route'] . '/set_vendor/'); ?>" required>
                    <option value="">-- SELECT VENDOR</option>
                    <?php foreach (available_vendors(config_item('auth_inventory')) as $v => $vendor) : ?>
                        <option value="<?= $vendor; ?>" <?= ($vendor == $_SESSION['payment_request']['vendor']) ? 'selected' : ''; ?>>
                        <?= $vendor; ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                    <label for="vendor">Vendor</label>
                </div>

                <div class="form-group">
                    <input type="number" name="amount" id="amount" class="form-control" value="<?= $_SESSION['payment_request']['total_amount']; ?>" readonly="readonly">
                    <label for="amount">Amount</label>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="form-group">
                    <textarea name="notes" id="notes" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_notes'); ?>"><?= $_SESSION['payment_request']['notes']; ?></textarea>
                    <label for="notes">Notes</label>
                </div>
            </div>
          </div>
        </div>

        <?php if (isset($_SESSION['payment_request']['items'])):?>
          <div class="document-data table-responsive">
            <table class="table table-hover table-bordered">
              <thead>
                <tr>
                  <th class="middle-alignment" rowspan="2"></th>
                  <th class="middle-alignment">PO#</th>
                  <th class="middle-alignment">Description Item</th>
                  <th class="middle-alignment">Status</th>
                  <th class="middle-alignment">Due Date</th>
                  <th class="middle-alignment">Received Qty</th>
                  <th class="middle-alignment">Received Val.</th>
                  <th class="middle-alignment">Amount</th>
                  <th class="middle-alignment">Remaining Purposed</th>
                  <th class="middle-alignment">Purposed Amount</th>
                  <th class="middle-alignment">Adjustment</th>                  
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_SESSION['payment_request']['items'] as $id => $request):?>
                  <tr id="row_<?=$id;?>">
                    <td width="1">
                      <a href="<?=site_url($module['route'] .'/delete_request/'. $id);?>" class="hide btn btn-icon-toggle btn-danger btn-sm btn_delete_request">
                        <i class="fa fa-trash"></i>
                      </a>
                    </td>
                    <td>
                      <?=$request['po_number'];?>
                    </td>
                    <td>
                      <?=$request['deskripsi'];?>
                    </td>
                    <td class="no-space">
                      <?=$request['status'];?>
                    </td>
                    <td class="no-space">
                      <?= print_date($request['due_date'],'d/m/Y') ?>
                    </td>
                    <td>
                      <?= print_number($request['quantity_received'],2) ?>
                    </td>
                    <td>
                      <?= print_number($request['amount_received'],2) ?>
                    </td>
                    <td>
                      <?= print_number($request['total_amount'],2) ?>
                    </td>
                    <td>
                      <?= print_number($request['left_paid_request'],2) ?>
                    </td>
                    <td>
                      <?= print_number($request['amount_paid'],2) ?>
                    </td>
                    <td>
                      <?= print_number($request['adj_value'],2) ?>
                    </td>                   
                  </tr>
                <?php endforeach;?>
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="9">Total</th>
                  <th><?= print_number($_SESSION['payment_request']['total_amount'],2) ?></th>
                  <th></th>
                </tr>
              </tfoot>
            </table>
            
          </div>
        <?php endif;?>
      </div>
      <div class="card-actionbar">
        <div class="card-actionbar-row">
          <div class="pull-left">
            <a href="<?=site_url($module['route'] .'/add_item');?>" onClick="return popup(this, 'add_item')" class="btn btn-primary ink-reaction">
              Select Item PO
            </a>

            <?php if (!empty($_SESSION['payment_request']['items'])):?>
              <a href="<?=site_url($module['route'] .'/edit_item');?>" onClick="return popup(this, 'edit_request')" class="btn btn-primary ink-reaction">
                Edit Request
              </a>

              <a href="<?=site_url($module['route'] .'/attachment');?>" onClick="return popup(this, 'attachment')" class="hide btn btn-primary ink-reaction">
                Attachment
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

  $('#vendor').on('focusin', function() {
    $(this).data('val', $(this).val());
  });

  $('#vendor').on('change', function() {
      var prev = $(this).data('val');
      var current = $(this).val();
      var url = $(this).data('source');

      if (prev != '') {
        var conf = confirm("Changing the vendor will remove the items that have been added. Continue?");

        if (conf == false) {
          return false;
        }
      }

      window.location.href = url + '/' + current;
  });

  $('#default_currency').on('focusin', function() {
    $(this).data('val', $(this).val());
  });

  $('#default_currency').on('change', function() {
      var prev = $(this).data('val');
      var current = $(this).val();
      var url = $(this).data('source');

      if (prev != '') {
        var conf = confirm("Changing the currency will remove the items that have been added. Continue?");

        if (conf == false) {
          return false;
        }
      }

      window.location.href = url + '/' + current;
  });
});
</script>

<?=html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock()?>

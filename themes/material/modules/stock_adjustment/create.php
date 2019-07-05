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
                <div class="col-sm-6">
                  <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-content">
                        <input type="text" name="document_number" id="document_number" class="form-control" maxlength="6" value="<?=$_SESSION['adj']['document_number'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_doc_number');?>" required>
                        <label for="document_number">Document No.</label>
                      </div>
                      <span class="input-group-addon"><?=adj_format_number();?></span>
                    </div>
                  </div>
                  <div class="form-group">
                    <select name="warehouse" id="warehouse" class="form-control" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_warehouse');?>" required>
                      <?php foreach (available_warehouses() as $w => $warehouse):?>
                        <option value="<?=$warehouse;?>" <?=($_SESSION['adj']['warehouse'] == $warehouse) ? 'selected' : '';?>>
                          <?=$warehouse;?>
                        </option>
                      <?php endforeach;?>
                    </select>
                    <label for="warehouse">Warehouse</label>
                  </div>
				  <div class="form-group">
                    <input type="text" name="issued_date" id="issued_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control" value="<?=$_SESSION['adj']['date'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_date');?>" required>
                    
                    <label for="issued_date">Date</label>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <textarea name="remarks" id="remarks" class="form-control" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_notes');?>"><?=$_SESSION['adj']['notes'];?></textarea>
                    <label for="remarks">Remarks</label>
                  </div>
                </div>
              </div>
            </div>

            <?php if (isset($_SESSION['adj']['items'])):?>
              <div class="document-data table-responsive">
                <table class="table table-hover" id="table-document">
                  <thead>
                  <tr>
                    <th></th>
                    <th>Description</th>
                    <th>P/N</th>
                    <th>S/N</th>
                    <th>Condition</th>
                    <th>Stores</th>
                    <th>Qty Adj</th>
                    <th>Unit Value Adj</th>
                  </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($_SESSION['adj']['items'] as $i => $items):?>
                      <tr id="row_<?=$i;?>">
                        <td width="1">
                          <a href="" class="btn btn-icon-toggle btn-danger btn-sm btn_delete_document_item">
                            <i class="fa fa-trash"></i>
                          </a>
                          <!-- <a class="btn btn-icon-toggle btn-info btn-sm btn_edit_document_item" data-todo='{"todo":<?=$i;?>}'>
                            <i class="fa fa-edit"></i>
                          </a> --> 
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
                          <?=$items['condition'];?>
                        </td>
                        <td>
                          <?=$items['stores'];?>
                        </td>
                        <td>
                          <?=number_format($items['adj_quantity'], 2);?>
                        </td>
                        <td>
                          <?=number_format($items['adj_value'], 2);?>
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

              <a href="<?=site_url($module['route'] .'/mix_discard');?>" class="btn btn-flat btn-danger ink-reaction">
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

        <?=form_open(site_url($module['route'] .'/adj_add_item'), array(
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
              <div class="col-sm-12 col-lg-12">
                <div class="row">
                  <div class="col-sm-6 col-lg-6">
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
                    </fieldset>
                  </div>
                  <div class="col-sm-6 col-lg-6">
                    <fieldset>
                      <legend>Storage</legend>

                      <div class="form-group">
                        <!-- <input type="text" name="stores" id="stores" class="form-control input-sm"> -->
                        <input type="text" name="stores" id="stores" data-tag-name="stores" data-search-for="stores" data-source="<?=site_url($modules['ajax']['route'] .'/json_stores/'. $_SESSION['adj']['category']);?>" class="form-control input-sm" required>
                        <label for="stores">Stores</label>
                      </div>

                      <div class="form-group">
                        <select name="condition" id="condition" class="form-control input-sm" required>
                          <?php foreach (available_conditions() as $key => $condition):?>
                            <option value="<?=$condition;?>"><?=$condition;?></option>
                          <?php endforeach;?>
                        </select>
                        <label for="condition">Condition</label>
                      </div>
                    </fieldset>
                  </div>
                </div>
              </div>

              <div class="col-sm-12 col-lg-12">
                <div class="row">
                  <div class="col-sm-6 col-lg-6">
                    <fieldset>
                      <legend>Adjustments Quantity</legend>
                      <div class="form-group">
                        <input type="text" name="adj_quantity" id="adj_quantity" data-tag-name="adj_quantity" class="form-control input-sm" value="0" required>
                        <label for="adj_quantity">Quantity</label>
                      </div>                      
                      <input type="hidden" name="item_id" id="item_id" />
					  <input type="hidden" name="stock_in_stores_id" id="stock_in_stores_id" />
                    </fieldset>
                  </div>
                  
                  <div class="col-sm-6 col-lg-6">
                    <fieldset>
                      <legend>Adjustments Unit Value</legend>
                      <div class="form-group">
                        <input type="text" name="adj_value" id="adj_value" data-tag-name="adj_value" class="form-control input-sm" value="0" required>
                        <label for="adj_value">Value</label>
                      </div>
					  <div class="form-group">
						<div class="row">
							<div class="col-lg-6 col-sm-6">
								<input type="text" name="harga_idr" id="harga_idr" data-tag-name="harga_idr" class="form-control input-sm" value="" readonly>
							</div>
							<div class="col-lg-6 col-sm-6">
								<input type="text" name="harga_usd" id="harga_usd" data-tag-name="harga_usd" class="form-control input-sm" value="" readonly>
							</div>
						</div>                      
						
                        <label for="adj_value">Harga Sebelumnya IDR-USD</label>
                      </div>
					  <div class="form-group">
						<div class="row">
							<div class="col-lg-6 col-sm-6">
								<input type="text" name="currency" id="currency" data-tag-name="currency" class="form-control input-sm" value="" readonly>
							</div>
							<div class="col-lg-6 col-sm-6">
								<input type="text" name="kurs" id="kurs" data-tag-name="kurs" class="form-control input-sm" value="" readonly>
							</div>
						</div>                      
						
                        <label for="adj_value">Currency</label>
                      </div>
                    </fieldset>
                  </div>
                </div>                
              </div>
            </div>
          </div>

          <div class="modal-footer">
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
        <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?=site_url($module['route'] .'/adjustment_save');?>">
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
    var qty = $(this).data('qty');
    var tot = $('#mixing_quantity').val();

    $.get( url );

    $(tr).remove();
    $('#mixing_quantity').val( tot - qty );

    if ($("#table-document > tbody > tr").length == 0){
      $(buttonSubmit).attr('disabled', true);
    }
  });

  $( autosetInputData ).on('change', function(){
    var val = $(this).val();
    var url = $(this).data('source');

    $.get( url, { data: val });
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

  $('#search_stock_in_stores').on('click focus', function(){
  $.ajax({
    url: $('#search_stock_in_stores').data('target'),
    dataType: "json",
    success: function (resource) {
      $('#search_stock_in_stores').autocomplete({
        autoFocus: true,
        minLength: 1,

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
          $('#mixed_quantity').val( ui.item.quantity );
          $('#condition').val( ui.item.condition );
          $('#stores').val( ui.item.stores );
          $('#item_id').val( ui.item.id );
		  $('#stock_in_stores_id').val( ui.item.id );
		  $('#harga_idr').val( ui.item.unit_value );
		  $('#harga_usd').val( ui.item.unit_value_dollar );
          // $('#adj_value').val( ui.item.unit_value );
		  if(ui.item.kurs_dollar>1){
			$('#currency').val('USD');
			$('#kurs').val(ui.item.kurs_dollar);
		  }else{
			$('#currency').val('IDR');  
			$('#kurs').val(ui.item.kurs_dollar);
		  }

          $('input[id="mixed_quantity"]').attr('data-rule-max', parseInt(ui.item.quantity)).attr('data-msg-max', 'max available '+ ui.item.quantity);

          $('#mixed_quantity').attr('max', ui.item.quantity).focus();
          $('#unit').val( ui.item.unit );
          $('.unit-addon').text( ui.item.unit );

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
  });
  
  $('#adj_value').on('click focus', function(){
	$('#adj_quantity').attr('readonly',true); 
	$('#adj_quantity').val(0);
	$('#adj_value').attr('readonly',false); 
  });
  $('#adj_quantity').on('click focus', function(){
	$('#adj_value').attr('readonly',true);  
	$('#adj_value').val(0);
	$('#adj_quantity').attr('readonly',false);
  });
});
</script>

<?=html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock()?>

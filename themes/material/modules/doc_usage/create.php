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
                <div class="col-sm-12 col-lg-8">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <div class="input-group">
                          <div class="input-group-content">
                            <input type="text" name="document_number" id="document_number" class="form-control" maxlength="6" value="<?=$_SESSION['usage']['document_number'];?>" data-source="<?=site_url($module['route'] .'/set_document_number');?>" data-input-type="autoset" autofocus required>
                            <label for="document_number">Document No.</label>
                          </div>
                          <span class="input-group-addon"><?=usage_format_number();?></span>
                        </div>
                      </div>

                      <div class="form-group">
                        <input type="text" name="issued_by" id="issued_by" class="form-control" value="<?=$_SESSION['usage']['issued_by'];?>" data-source="<?=site_url($module['route'] .'/set_document_issued_by');?>" data-input-type="autoset" required>
                        <label for="issued_by">Released/Issued By</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="approved_by" id="approved_by" class="form-control" value="<?=$_SESSION['usage']['approved_by'];?>" data-source="<?=site_url($module['route'] .'/set_document_approved_by');?>" data-input-type="autoset" required>
                        <label for="approved_by">Approved By</label>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <input type="text" name="issued_date" id="issued_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-end-date="0d" class="form-control datepicker" value="<?=$_SESSION['usage']['issued_date'];?>" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_document_issued_date');?>" data-input-type="autoset" required>
                        <label for="issued_date">Date of Issued</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="required_by" id="required_by" class="form-control" value="<?=$_SESSION['usage']['required_by'];?>" data-source="<?=site_url($module['route'] .'/set_document_required_by');?>" data-input-type="autoset" required>
                        <label for="required_by">Required By</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="issued_to" id="issued_to" class="form-control" value="<?=$_SESSION['usage']['issued_to'];?>" data-source="<?=site_url($module['route'] .'/set_document_issued_to');?>" data-input-type="autoset" required>
                        <label for="issued_to">Issued To</label>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-sm-12 col-lg-4">
                  <div class="form-group">
                    <textarea name="notes" id="notes" class="form-control" rows="4" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_document_notes');?>"><?=$_SESSION['usage']['notes'];?></textarea>
                    <label for="notes">Notes</label>
                  </div>
                </div>
              </div>
            </div>

            <?php if (isset($_SESSION['usage']['items'])):?>
              <div class="document-data table-responsive">
                <table class="table table-hover" id="table-document">
                  <thead>
                  <tr>
                    <th></th>
                    <th>Group</th>
                    <th>Description</th>
                    <th>P/N</th>
                    <th>S/N</th>
                    <th>Condition</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Remarks</th>
                  </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($_SESSION['usage']['items'] as $i => $items):?>
                      <tr id="row_<?=$i;?>">
                        <td width="1">
                          <a href="<?=site_url($module['route'] .'/del_item/'. $i);?>" class="btn btn-icon-toggle btn-danger btn-sm btn_delete_document_item">
                            <i class="fa fa-trash"></i>
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
                          <?=$items['serial_number'];?>
                        </td>
                        <td>
                          <?=$items['condition'];?>
                        </td>
                        <td>
                          <?=number_format($items['issued_quantity'], 2);?>
                        </td>
                        <td>
                          <?=$items['unit'];?>
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
          'class' => 'form form-validate floating-label ui-front',
          'role'  => 'form'
        ));?>

          <div class="modal-body">
            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <div class="input-group">
                    <div class="input-group-content">
                      <input type="text" id="search_available_stock" data-search-for="available_stock" class="form-control" data-source="<?=site_url($modules['ajax']['route'] .'/get_available_stock/'. config_item('auth_warehouse') .'/'. $_SESSION['usage']['category']);?>">
                      <label for="search_item">Search item by S/N, P/N, Description</label>
                    </div>
                    <span class="input-group-addon">
                      <i class="md md-search"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6 col-lg-5">
                <fieldset>
                  <legend>Item</legend>

                  <div class="form-group">
                    <input type="text" name="group" id="group" data-tag-name="group" class="form-control input-sm" readonly>
                    <label for="group">Item Group</label>
                  </div>

                  <div class="form-group">
                    <input type="text" name="description" id="description" data-tag-name="item_description" class="form-control input-sm" readonly>
                    <label for="description">Description</label>
                  </div>

                  <div class="form-group">
                    <input type="text" name="part_number" id="part_number" data-tag-name="part_number" class="form-control input-sm" readonly>
                    <label for="part_number">Part Number</label>
                  </div>

                  <div class="form-group">
                    <textarea name="remarks" id="remarks" data-tag-name="remarks" class="form-control input-sm"></textarea>
                    <label for="remarks">Remarks</label>
                  </div>
                </fieldset>
              </div>

              <div class="col-sm-6 col-lg-7">
                <div class="row">
                  <div class="col-sm-12 col-lg-4">
                    <fieldset>
                      <legend>Quantity</legend>

                      <div class="form-group">
                        <input type="text" name="issued_quantity" id="issued_quantity" class="form-control input-sm" value="1" data-valid-quantity="true" data-rule-max="1" data-msg-max="Max AVAILABLE 1" required>
                        <label for="quantity">Quantity</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="in_stores_quantity" id="in_stores_quantity" data-tag-name="in_stores_quantity" class="form-control input-sm" readonly>
                        <label for="in_stores_quantity">Available Quantity</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="on_hand_quantity" id="on_hand_quantity" data-tag-name="on_hand_quantity" class="form-control input-sm" readonly>
                        <label for="on_hand_quantity">On Hand Quantity</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="unit" id="unit" data-tag-name="item_unit" class="form-control input-sm" readonly>
                        <label for="unit">Unit</label>
                      </div>
                    </fieldset>
                  </div>

                  <div class="col-sm-12 col-lg-8">
                    <fieldset>
                      <legend>Stores</legend>

                      <div class="form-group">
                        <input type="text" name="serial_number" id="serial_number" data-tag-name="serial_number" class="form-control input-sm" readonly>
                        <label for="serial_number">Serial Number</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="stores" id="stores" data-tag-name="stores" class="form-control input-sm" readonly>
                        <label for="stores">Stores</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="condition" id="condition" data-tag-name="condition" class="form-control input-sm" readonly>
                        <label for="condition">Item Condition</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="estimated_unit_value" id="estimated_unit_value" data-tag-name="estimated_unit_value" class="form-control input-sm" value="1">
                        <label for="estimated_unit_value">Unit Value</label>
                      </div>

                      <input type="hidden" name="unit_value" id="unit_value" data-tag-name="unit_value">

                      <input type="hidden" name="inventory_stores_id" id="inventory_stores_id" data-tag-name="inventory_stores_id">
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
        <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?=site_url($module['route'] .'/save');?>">
          <i class="md md-save"></i>
          <small class="top right">Save Document</small>
        </a>
      </div>
    </div>
  </section>
<?php endblock() ?>

<?php startblock('scripts') ?>
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
    // ALL ELEMENTS
    var dataItemGroup             = $('[data-tag-name="group"]');
    var dataItemDescription       = $('[data-tag-name="item_description"]');
    var dataPartNumber            = $('[data-tag-name="part_number"]');
    var dataSerialNumber          = $('[data-tag-name="serial_number"]');
    var dataStores                = $('[data-tag-name="stores"]');
    var dataUnitOfMeasurement     = $('[data-tag-name="item_unit"]');
    var dataItemCondition         = $('[data-tag-name="condition"]');
    var dataIssuedQuantity        = $('[data-tag-name="issued_quantity"]');
    var dataMinimumQuantity       = $('[data-tag-name="minimum_quantity"]');
    var dataAvailableQuantity     = $('[data-tag-name="available_quantity"]');
    var dataInStoresQuantity      = $('[data-tag-name="in_stores_quantity"]');
    var dataOnHandQuantity        = $('[data-tag-name="on_hand_quantity"]');
    var dataUnitValue             = $('[data-tag-name="unit_value"]');
    var dataReferenceNumber       = $('[data-tag-name="reference_number"]');
    var dataReceivedDate          = $('[data-tag-name="received_date"]');

    // GENERAL ELEMENTS
    var tableDocumentItems        = $('#table-document-items');
    var formDocument              = $('#form-document');
    var buttonSubmitDocument      = $('#btn-submit-document');
    var buttonDeleteDocumentItem  = $('.btn_delete_document_item');
    var autosetInputData          = $('[data-input-type="autoset"]');

    // AUTOCOMPLETE ELEMENT
    var searchAvailableStock      = $('[data-search-for="available_stock"]');
    var searchStores              = $('[data-search-for="stores"]');
    var searchItemCategory        = $('[data-search-for="category"]');
    var searchItemGroup           = $('[data-search-for="group"]');
    var searchItemDescription     = $('[data-search-for="item_description"]');
    var searchItemCondition       = $('[data-search-for="condition"]');
    var searchPartNumber          = $('[data-search-for="part_number"]');
    var searchAltPartNumber       = $('[data-search-for="alternate_part_number"]');
    var searchSerialNumber        = $('[data-search-for="serial_number"]');
    var searchUnitOfMeasurement   = $('[data-search-for="item_unit"]');

    // FORM ELEMENT
    var inputItemGroup          = $('[name="group"]');
    var inputItemDescription    = $('[name="description"]');
    var inputPartNumber         = $('[name="part_number"]');
    var inputAltPartNumber      = $('[name="alternate_part_number"]');
    var inputSerialNumber       = $('[name="serial_number"]');
    var inputIssuedQuantity     = $('[name="issued_quantity"]');
    var inputMinimumQuantity    = $('[name="minimum_quantity"]');
    var inputUnitValue          = $('[name="unit_value"]');
    var inputStores             = $('[name="stores"]');
    var inputUnitOfMeasurement  = $('[name="unit"]');
    var inputItemCondition      = $('[name="condition"]');
    var inputInventoryStoresId  = $('[name="inventory_stores_id"]');

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
      url: $( searchAvailableStock ).data('source'),
      dataType: "json",
      success: function (resource) {
        $( searchAvailableStock ).autocomplete({
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
            $( dataItemGroup ).val( ui.item.group ).before( dataItemGroup.next() );
            $( dataItemDescription ).val( ui.item.description ).before( dataItemDescription.next() );
            $( dataPartNumber ).val( ui.item.part_number ).before( dataPartNumber.next() );
            $( dataSerialNumber ).val( ui.item.serial_number ).before( dataSerialNumber.next() );
            $( dataStores ).val( ui.item.stores ).before( dataStores.next() );
            $( dataUnitOfMeasurement ).val( ui.item.unit ).before( dataUnitOfMeasurement.next() );
            $( dataItemCondition ).val( ui.item.condition ).before( dataItemCondition.next() );
            $( dataIssuedQuantity ).val( ui.item.quantity ).before( dataIssuedQuantity.next() );
            $( dataMinimumQuantity ).val( ui.item.minimum_quantity ).before( dataMinimumQuantity.next() );
            $( dataInStoresQuantity ).val( ui.item.available_quantity ).before( dataInStoresQuantity.next() );
            $( dataOnHandQuantity ).val( ui.item.on_hand_quantity ).before( dataOnHandQuantity.next() );
            $( dataUnitValue ).val( ui.item.unit_value ).before( dataUnitValue.next() );
            $( inputInventoryStoresId ).val( ui.item.id ).before( inputInventoryStoresId.next() );

            $( inputIssuedQuantity ).data('rule-max', ui.item.available_quantity).data('msg-max', 'max available '+ ui.item.available_quantity);
            $( 'input[data-valid-quantity="true"]' ).data('rule-max', ui.item.available_quantity).data('msg-max', 'max available '+ ui.item.available_quantity);

            // if (ui.item.serial_number != null){
            //   $( inputIssuedQuantity ).val(1).attr('readonly', true);
            // }

            $(searchAvailableStock).val('');

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
      url: $( '[data-search-for="item_description"]' ).data('source'),
      dataType: "json",
      success: function (data) {
        $( '[data-search-for="item_description"]' ).autocomplete({
          source: function (request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $.ajax({
      url: $( '[data-search-for="part_number"]' ).data('source'),
      dataType: "json",
      success: function (data) {
        $( '[data-search-for="part_number"]' ).autocomplete({
          source: function (request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $.ajax({
      url: $( '[data-search-for="alternate_part_number"]' ).data('source'),
      dataType: "json",
      success: function (data) {
        $( '[data-search-for="alternate_part_number"]' ).autocomplete({
          source: function (request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $.ajax({
      url: $( '[data-search-for="serial_number"]' ).data('source'),
      dataType: "json",
      success: function (data) {
        $( '[data-search-for="serial_number"]' ).autocomplete({
          source: function (request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $.ajax({
      url: $( '[data-search-for="item_unit"]' ).data('source'),
      dataType: "json",
      success: function (data) {
        $( '[data-search-for="item_unit"]' ).autocomplete({
          source: function (request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $.ajax({
      url: $( '[data-search-for="stores"]' ).data('source'),
      dataType: "json",
      success: function (data) {
        $( '[data-search-for="stores"]' ).autocomplete({
          source: function (request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $( inputSerialNumber ).on('change', function(){
      if ( $( this ).val() != '' ){
        $( inputQuantity ).val('1').attr('readonly', true);
        $( inputIssuedQuantity ).val('1').attr('readonly', true);
      } else {
        $( inputQuantity ).attr('readonly', false);
        $( inputIssuedQuantity ).attr('readonly', false);
      }
    });
  });
  </script>

  <?=html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>

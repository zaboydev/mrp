<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="has-actions style-default">
  <div class="section-body">
    <?= form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form-create-document')); ?>
    <div class="card">
      <div class="card-body no-padding">
        <?php
        if ($this->session->flashdata('alert'))
          render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
        ?>
        <div class="document-header force-padding">
          <div class="row">
            <div class="col-sm-6 col-lg-3">
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-content">
                    <input type="text" name="order_number" id="order_number" class="form-control" value="[auto]" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_doc_number'); ?>" readonly>
                    <label for="order_number">Document No.</label>
                  </div>
                  <span class="input-group-addon"><?= request_format_number($_SESSION['capex']['cost_center_code']); ?></span>
                </div>
              </div>

              <div class="form-group">
                <input type="text" name="required_date" id="required_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control" value="<?= $_SESSION['capex']['required_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_required_date'); ?>" required>
                <label for="required_date">Required Date</label>
              </div>

              <div class="form-group">
                <input type="text" name="deliver_to" id="deliver_to" class="form-control" value="<?= $_SESSION['capex']['deliver_to']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_deliver_to'); ?>" required>
                <label for="deliver_to">Deliver To</label>
              </div>
            </div>

            <div class="col-sm-12 col-lg-5">
              <div class="form-group">
                <input type="text" name="suggested_supplier" id="suggested_supplier" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_suggested_supplier'); ?>" value="<?= $_SESSION['capex']['suggested_supplier']; ?>" required>
                <label for="suggested_supplier">Suggested Supplier</label>
              </div>
              <div class="form-group">
                <textarea name="notes" id="notes" class="form-control" rows="3" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_notes'); ?>"><?= $_SESSION['capex']['notes']; ?></textarea>
                <label for="notes">Notes</label>
              </div>
            </div>
          </div>
        </div>

        <?php if (isset($_SESSION['capex']['items'])) : ?>
          <?php $grand_total = array(); ?>
          <?php $total_quantity = array(); ?>
          <div class="document-data table-responsive">
            <table class="table table-hover" id="table-document">
              <thead>
                <tr>
                  <th></th>
                  <th>Item</th>
                  <th>P/N</th>
                  <th>Unit</th>
                  <th class="text-right">QTY</th>
                  <th class="text-right">Price</th>
                  <th class="text-right">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_SESSION['capex']['items'] as $i => $items) : ?>
                  <?php $grand_total[] = $items['total']; ?>
                  <?php $total_quantity[] = $items['quantity']; ?>
                  <tr id="row_<?= $i; ?>">
                    <td width="100">
                      <a href="<?= site_url($module['route'] . '/del_item/' . $i); ?>" class="btn btn-icon-toggle btn-danger btn-sm btn_delete_document_item">
                        <i class="fa fa-trash"></i>
                      </a>
                      <a class="btn btn-icon-toggle btn-primary btn-sm btn_edit_document_item" data-todo='{"todo":<?= $i; ?>}'>
                        <i class="fa fa-edit"></i>
                      </a>
                    </td>
                    <td>
                      <?= print_string($items['product_name']); ?> || <?= print_string($items['additional_info']); ?>
                    </td>
                    <td class="no-space">
                      <?= print_string($items['part_number']); ?>
                    </td>
                    <td>
                      <?= print_string($items['unit']); ?>
                    </td>
                    <td>
                      <?= print_number($items['quantity'], 2); ?>
                    </td>
                    <td>
                      <?= print_number($items['price'], 2); ?>
                    </td>
                    <td>
                      <?= print_number($items['total'], 2); ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <th></th>
                <th>Total</th>
                <th></th>
                <th></th>
                <th><?= print_number(array_sum($total_quantity), 2); ?></th>
                <th></th>
              </tfoot>
            </table>
          </div>
        <?php endif; ?>
      </div>
      <div class="card-actionbar">
        <div class="card-actionbar-row">          
          <div class="pull-left">
            <a href="#modal-add-item" data-toggle="modal" data-target="#modal-add-item" class="btn btn-primary ink-reaction btn-open-offcanvas">
              Add Item
            </a>
            <?php if (!empty($_SESSION['capex']['items'])):?>
              <a href="<?=site_url($module['route'] .'/attachment');?>" onClick="return popup(this, 'attachment')" class="btn btn-primary ink-reaction">
                Attachment
              </a>
            <?php endif;?>
          </div>
          <a href="<?= site_url($module['route'] . '/discard'); ?>" class="btn btn-flat btn-danger ink-reaction">
            Discard
          </a>
        </div>
      </div>
    </div>
    <?= form_close(); ?>
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

        <?= form_open(site_url($module['route'] . '/add_item'), array(
          'autocomplete' => 'off',
          'id'    => 'ajax-form-create-document',
          'class' => 'form form-validate ui-front',
          'role'  => 'form'
        )); ?>

        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12">
              <div class="row">
                <div class="col-xs-12">
                  <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-content">
                        <input type="text" id="search_budget" class="form-control" data-target="<?= site_url($module['route'] . '/search_budget/'); ?>">
                        <label for="search_budget">Search item Budgeted</label>
                      </div>
                      <span class="input-group-addon">
                        <i class="md md-search"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-12 col-lg-7">
              <div class="row">
                <div class="col-sm-6 col-lg-6">
                  <fieldset>
                    <legend>General</legend>

                    <div class="form-group">
                      <input type="text" name="part_number" id="part_number" class="form-control input-sm" data-source="<?= site_url($module['route'] . '/search_items_by_part_number/'); ?>" readonly>
                      <label for="part_number">Part Number</label>
                    </div>
                    <div class="form-group">
                      <input type="text" name="product_name" id="product_name" class="form-control input-sm" data-source="<?= site_url($module['route'] . '/search_items_by_product_name/'); ?>" readonly>
                      <label for="product_name">Product</label>
                    </div>
                    <div class="form-group">
                      <!-- <input type="text" name="unit" id="unit" class="form-control input-sm"> -->
                      <input type="text" name="unit" id="unit" data-tag-name="unit" data-search-for="unit" data-source="<?= site_url($modules['ajax']['route'] . '/search_item_units/'); ?>" class="form-control input-sm" placeholder="Unit" required>
                      <label for="unit">Unit of Measurement</label>
                    </div>
                    <div class="form-group">
                      <!-- <input type="text" name="unit" id="unit" class="form-control input-sm"> -->
                      <input type="text" name="group_name" id="group_name" data-tag-name="group_name" data-search-for="group_name" data-source="<?= site_url($modules['ajax']['route'] . '/search_item_groups/'); ?>" class="form-control input-sm" placeholder="Unit" required readonly>
                      <label for="unit">Group</label>
                    </div>
                  </fieldset>
                </div>
                <div class="col-sm-6 col-lg-6">
                  <fieldset>
                    <legend>Balance</legend>
                    <div class="form-group">
                      <input type="text" name="mtd_quantity" id="mtd_quantity" class="form-control input-sm" value="0" readonly>
                      <label for="mtd_quantity">Month to Date Quantity</label>
                    </div>

                    <div class="form-group">
                      <input type="number" name="mtd_budget" id="mtd_budget" value="1" class="form-control input-sm" readonly="readonly">
                      <label for="mtd_budget">Month to Date Budget</label>
                    </div>
                    <div class="form-group">
                      <input type="text" name="maximum_quantity" id="maximum_quantity" class="form-control input-sm" value="0" readonly>
                      <label for="maximum_quantity">Year to Date Quantity</label>
                    </div>

                    <div class="form-group">
                      <input type="number" name="maximum_price" id="maximum_price" value="1" class="form-control input-sm" readonly="readonly">
                      <label for="max_value">Year to Date Budget</label>
                    </div>
                  </fieldset>
                </div>
              </div>
            </div>

            <div class="col-sm-12 col-lg-5">
              <fieldset>
                <legend>Required</legend>
                <div class="form-group">
                  <input type="text" name="quantity" id="quantity" class="form-control input-sm" value="1" required>
                  <label for="quantity">Quantity</label>
                </div>
                <div class="form-group">
                  <input type="text" name="price" id="price" class="form-control input-sm" value="1" required>
                  <label for="price">Price</label>
                </div>
                <div class="form-group">
                  <input type="text" name="total" id="total" class="form-control input-sm" value="" readonly="">
                  <label for="total">Total</label>
                </div>
              </fieldset>
              <fieldset>
                <legend>Additional</legend>
                <div class="form-group">
                      <!-- <input type="text" name="unit" id="unit" class="form-control input-sm"> -->
                  <input type="reference_ipc" name="reference_ipc" id="reference_ipc" data-tag-name="reference_ipc" class="form-control input-sm">
                  <label for="unit">Reference IPC</label>
                </div>
                <div class="form-group">
                  <textarea name="additional_info" id="additional_info" data-tag-name="additional_info" class="form-control input-sm"></textarea>
                  <label for="additional_info">Additional Info/Remarks</label>
                </div>
              </fieldset>
            </div>
            <div class="col-sm-12 col-lg-5 hide form-unbudgeted">
              <fieldset>
                <legend>Unbudgeted</legend>

                <div class="form-group">
                  <input name="xx" id="xx" data-tag-name="xx" class="form-control input-sm" value="Unbudgeted" readonly="readonly"></input>
                  <label for="additional_info">Unbudgeted</label>
                </div>
              </fieldset>
            </div>
            <div class="col-sm-12 col-lg-5 hide form-relokasi">
              <fieldset>
                <legend>Relocation Form</legend>

                <div class="form-group">
                  <input name="origin_budget" id="origin_budget" data-tag-name="origin_budget" class="form-control input-sm"></input>
                  <label for="additional_info">Origin Budget</label>
                </div>
                <div class="form-group">
                  <input name="budget_value" id="budget_value" data-tag-name="budget_value" class="form-control input-sm" readonly="readonly"></input>
                  <label for="budget_value">Budget Value</label>
                </div>
                <div class="form-group">
                  <input name="relocation_budget" id="relocation_budget" data-tag-name="relocation_budget" class="form-control input-sm" readonly="readonly"></input>
                  <input type="hidden" name="need_budget" id="need_budget" data-tag-name="need_budget" class="form-control input-sm"></input>
                  <label for="additional_info">Relocation Value</label>
                </div>
              </fieldset>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <input type="hidden" id="annual_cost_center_id" name="annual_cost_center_id">
          <input type="hidden" id="unbudgeted_item" name="unbudgeted_item">
          <input type="hidden" id="relocation_item" name="relocation_item">
          <input type="hidden" id="item_source" name="item_source">
          <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>

          <button type="submit" id="modal-add-item-submit" class="btn btn-primary btn-create ink-reaction">
            Add Item
          </button>

          <input type="reset" name="reset" class="sr-only">
        </div>

        <?= form_close(); ?>
      </div>
    </div>
  </div>

  <div class="section-action style-default-bright">
    <div class="section-floating-action-row">
      <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?= site_url($module['route'] . '/save'); ?>">
        <i class="md md-save"></i>
        <small class="top right">Save Document</small>
      </a>
    </div>
  </div>
</section>
<?php endblock() ?>

<?php startblock('scripts') ?>
<?= html_script('vendors/pace/pace.min.js') ?>
<?= html_script('vendors/jQuery/jQuery-2.2.1.min.js') ?>
<?= html_script('themes/material/assets/js/libs/jquery-ui/jquery-ui.min.js') ?>
<?= html_script('themes/material/assets/js/libs/bootstrap/bootstrap.min.js') ?>
<?= html_script('themes/material/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js') ?>
<?= html_script('themes/material/assets/js/libs/spin.js/spin.min.js') ?>
<?= html_script('themes/material/assets/js/libs/autosize/jquery.autosize.min.js') ?>
<?= html_script('themes/material/assets/js/libs/toastr/toastr.js') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/jquery.validate.min.js') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/additional-methods.min.js') ?>
<?= html_script('vendors/bootstrap-daterangepicker/moment.min.js') ?>
<?= html_script('vendors/bootstrap-daterangepicker/daterangepicker.js') ?>
<?= html_script('themes/material/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js') ?>
<script>
  Pace.on('start', function() {
    $('.progress-overlay').show();
  });

  Pace.on('done', function() {
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

  (function($) {
    $.fn.reset = function() {
      this.find('input:text, input[type="email"], input:password, select, textarea').val('');
      this.find('input:radio, input:checkbox').prop('checked', false);
      return this;
    }

    $.fn.redirect = function(target) {
      var url = $(this).data('href');

      if (target == '_blank') {
        window.open(url, target);
      } else {
        window.document.location = url;
      }
    }

    $.fn.popup = function() {
      var popup = $(this).data('target');
      var source = $(this).data('source');

      $.get(source, function(data) {
        var obj = $.parseJSON(data);

        if (obj.type == 'denied') {
          toastr.options.timeOut = 10000;
          toastr.options.positionClass = 'toast-top-right';
          toastr.error(obj.info, 'ACCESS DENIED!');
        } else {
          $(popup)
            .find('.modal-body')
            .empty()
            .append(obj.info);

          $(popup).modal('show');

          $(popup).on('click', '.modal-header:not(a)', function() {
            $(popup).modal('hide');
          });

          $(popup).on('click', '.modal-footer:not(a)', function() {
            $(popup).modal('hide');
          });
        }
      })
    }
  }(jQuery));

  function submit_post_via_hidden_form(url, params) {
    var f = $("<form target='_blank' method='POST' style='display:none;'></form>").attr('action', url).appendTo(document.body);

    $.each(params, function(key, value) {
      var hidden = $('<input type="hidden" />').attr({
        name: key,
        value: JSON.stringify(value)
      });

      hidden.appendTo(f);
    });

    f.submit();
    f.remove();
  }

  function numberFormat(nStr) {
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
        String.fromCharCode(event.which).toLowerCase() === 'x')) {
      event.preventDefault();
    }
  });

  $(function() {
    // GENERAL ELEMENTS
    var formDocument = $('#form-document');
    var buttonSubmitDocument = $('#btn-submit-document');
    var buttonDeleteDocumentItem = $('.btn_delete_document_item');
    var buttonEditDocumentItem = $('.btn_edit_document_item');
    var autosetInputData = $('[data-input-type="autoset"]');

    toastr.options.closeButton = true;

    $('[data-toggle="redirect"]').on('click', function(e) {
      e.preventDefault;

      var url = $(this).data('url');

      window.document.location = url;
    });

    $('[data-toggle="back"]').on('click', function(e) {
      e.preventDefault;

      history.back();
    });

    // var today       = new Date();
    var today = $('[name="required_date"]').val();
    var today_2 = $('[name="pr_date"]').val();
    // today.setDate(today.getDate() + 30);
    $('#required_date').datepicker({
      autoclose: true,
      todayHighlight: true,
      format: 'yyyy-mm-dd',
      startDate: today,
    });

    $('[data-provide="datepicker"]').datepicker({
      autoclose: true,
      todayHighlight: true,
      format: 'yyyy-mm-dd',
      startDate: today_2,
    });

    $(document).on('click', '.btn-xhr-submit', function(e) {
      e.preventDefault();

      var button = $(this);
      var form = $('.form-xhr');
      var action = form.attr('action');

      button.attr('disabled', true);

      if (form.valid()) {
        $.post(action, form.serialize()).done(function(data) {
          var obj = $.parseJSON(data);

          if (obj.type == 'danger') {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error(obj.info);
          } else {
            toastr.options.positionClass = 'toast-top-right';
            toastr.success(obj.info);

            form.reset();

            $('[data-dismiss="modal"]').trigger('click');

            if (datatable) {
              datatable.ajax.reload(null, false);
            }
          }
        });
      }

      button.attr('disabled', false);
    });

    $(buttonSubmitDocument).on('click', function(e) {
      e.preventDefault();
      $(buttonSubmitDocument).attr('disabled', true);

      var url = $(this).attr('href');
      if (confirm('Are you sure want to save this request and sending email? Continue?')) {
        $.post(url, formDocument.serialize(), function(data) {
          console.log(data);
          var obj = $.parseJSON(data);

          if (obj.success == false) {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error(obj.message);
          } else {
            toastr.options.timeOut = 4500;
            toastr.options.closeButton = false;
            toastr.options.progressBar = true;
            toastr.options.positionClass = 'toast-top-right';
            toastr.success(obj.message);

            window.setTimeout(function() {
              window.location.href = '<?= site_url($module['route']); ?>';
            }, 5000);
          }

          $(buttonSubmitDocument).attr('disabled', false);
        });
      }


    });

    $(buttonEditDocumentItem).on('click', function(e) {
      e.preventDefault();

      //var id = $(this).data('todo').id;
      var id = $(this).data('todo').todo;
      var data_send = {
        id: id
        //i: i
      };
      var save_method;

      save_method = 'update';


      $.ajax({
        url: "<?= site_url($module['route'] . '/ajax_editItem/') ?>/" + id,
        type: "GET",
        data: data_send,
        dataType: "JSON",
        success: function(response) {
          var action = "<?= site_url($module['route'] . '/edit_item/') ?>/" + id;
          console.log(JSON.stringify(action));
          console.log(JSON.stringify(response));
          var maximum_quantity = parseFloat(response.maximum_quantity);
          var maximum_price = parseFloat(response.maximum_price);
          var mtd_quantity = parseFloat(response.mtd_quantity);
          var mtd_budget = parseFloat(response.mtd_budget);
          var price = response.price;
          if(response.price==null){
            price = 0;
          }

          $('#product_name').val(response.product_name);
          $('#part_number').val(response.part_number);
          $('#unit').val(response.unit);
          $('#group_name').val(response.group);
          $('#additional_info').val(response.additional_info);
          $('#annual_cost_center_id').val(response.annual_cost_center_id);
          $('#price').val(parseFloat(response.price));
          $('#maximum_price').val(maximum_price);
          $('#maximum_quantity').val(maximum_quantity);
          $('#mtd_quantity').val(mtd_quantity);
          $('#mtd_budget').val(mtd_budget);
          $('#quantity').val(response.quantity);
          $('#reference_ipc').val(response.reference_ipc);
          // $('#total').val(parseFloat(price)).trigger('change');

          $('input[id="total"]').attr('data-rule-max', parseFloat(response.maximum_price)).attr('data-msg-max', 'max available '+ parseInt(response.maximum_price));
          $('#total').attr('max', parseFloat(response.maximum_price));
          $('#total').val(response.total).trigger('change');

          $('#unbudgeted_item').val(0);

          $('#search_budget').val('');

          $('#modal-add-item form').attr('action', action);
          $('#modal-add-item').modal('show'); // show bootstrap modal when complete loaded
          $('.modal-title').text('Edit Item'); // Set title to Bootstrap modal title

        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
        }
      });
    });

    $(buttonDeleteDocumentItem).on('click', function(e) {
      e.preventDefault();

      var url = $(this).attr('href');
      var tr = $(this).closest('tr');

      $.get(url);

      $(tr).remove();

      if ($("#table-document > tbody > tr").length == 0) {
        $(buttonSubmit).attr('disabled', true);
      }
    });

    $(autosetInputData).on('change', function() {
      var val = $(this).val();
      var url = $(this).data('source');

      $.get(url, {
        data: val
      });
    });

    $.ajax({
      url: $('#search_budget').data('target'),
      dataType: "json",
      error: function(xhr, response, results) {
        console.log(xhr.responseText);
      },
      success: function(resource) {
        $('#search_budget').autocomplete({
            autoFocus: true,
            minLength: 1,

            source: function(request, response) {
              var results = $.ui.autocomplete.filter(resource, request.term);
              response(results.slice(0, 5));
              console.log(results);
            },

            focus: function(event, ui) {
              return false;
            },

            select: function(event, ui) {
              var maximum_quantity = parseFloat(ui.item.maximum_quantity);
              var maximum_price = parseFloat(ui.item.maximum_price);
              var mtd_quantity = parseFloat(ui.item.mtd_quantity);
              var mtd_budget = parseFloat(ui.item.mtd_budget);
              var price = ui.item.current_price;
              if(ui.item.current_price==null){
                price = 0;
              }

              $('#product_name').val(ui.item.product_name);
              $('#part_number').val(ui.item.product_code);
              $('#unit').val(ui.item.measurement_symbol);
              $('#group_name').val(ui.item.group_name);
              $('#annual_cost_center_id').val(ui.item.annual_cost_center_id);
              $('#price').val(parseFloat(price));
              $('#maximum_price').val(maximum_price);
              $('#maximum_quantity').val(maximum_quantity);
              $('#mtd_budget').val(mtd_budget);
              $('#mtd_quantity').val(mtd_quantity);
              $('#quantity').val(0);
              $('#total').val(0);
              // $('#total').val(parseFloat(price)).trigger('change');

              $('input[id="total"]').attr('data-rule-max', parseFloat(ui.item.maximum_price)).attr('data-msg-max', 'max available '+ parseInt(ui.item.maximum_price));
              $('#total').attr('max', parseFloat(ui.item.maximum_price));

              $('#unbudgeted_item').val(0);

              $('#search_budget').val('');

              return false;
            }
          })
          .data("ui-autocomplete")._renderItem = function(ul, item) {
            $(ul).addClass('list divider-full-bleed');

            return $("<li class='tile'>")
              .append('<a class="tile-content ink-reaction"><div class="tile-text">' + item.label + '</div></a>')
              .appendTo(ul);
          };
      }
    });

    // input unit autocomplete
    $.ajax({
      url: $('input[id="unit"]').data('source'),
      dataType: "json",
      success: function(data) {
        $('input[id="unit"]').autocomplete({
          source: function(request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    // $.ajax({
    //   url: $( 'input[id="group_name"]' ).data('source'),
    //   dataType: "json",
    //   success: function (data) {
    //     $( 'input[id="group_name"]' ).autocomplete({
    //       source: function (request, response) {
    //         var results = $.ui.autocomplete.filter(data, request.term);
    //         response(results.slice(0, 10));
    //       }
    //     });
    //   }
    // });

    $('input[id="quantity"],input[id="price"]').on('change', function (e) {
      sum();
    });

    $("#total").on("change", function() {
      if (parseFloat($(this).val()) > parseFloat($('input[id="maximum_price"]').val())){
        alert('This Products Year to Date is ' + $('input[id="maximum_price"]').val())+'. Please Request Relocation';
        $("#modal-add-item-submit").prop("disabled", true);
        $(this).val($('input[id="maximum_price"]').val());
        $(this).focus();
      }else{
        $("#modal-add-item-submit").prop("disabled", false);
      }
    });
  });

  function sum() {
    var total = parseInt($("#quantity").val()) * parseInt($("#price").val());

    $("#total").val(total).trigger("change");
  }

  function unbudgeted() {
    var status = $('#inventory_monthly_budget_id').val();

    if (status == null) {
      $('.form-unbudgeted').removeClass('hide');
    }
  }
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
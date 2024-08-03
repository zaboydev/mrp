<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="has-actions style-default">
  <div class="section-body">

    <?= form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form-create-document')); ?>

    <div class="card">
      <div class="card-head style-primary-dark">
        <header><?= PAGE_TITLE; ?></header>
      </div>

      <div class="card-body no-padding">
        <?php
        if ($this->session->flashdata('alert'))
          render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
        ?>

        <div class="document-header force-padding">
        <div class="row">
            <div class="col-sm-6 col-lg-3">
              <div class="form-group">
                <select name="annual_cost_center_id" id="annual_cost_center_id" class="form-control" data-source="<?= site_url($module['route'] . '/set_annual_cost_center_id'); ?>" required>
                  <option value="">--Select Department--</option>
                  <?php foreach (getAllAnnualCostCenters() as $annual_cost_center) : ?>
                    <option value="<?= $annual_cost_center['id']; ?>" <?= ($_SESSION['capex_poe']['annual_cost_center_id'] == $annual_cost_center['id']) ? 'selected' : ''; ?>>
                      <?= $annual_cost_center['cost_center_name']; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <label for="source">Department</label>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="form-group">
                <select name="head_dept_select" id="head_dept_select" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_head_dept'); ?>" required>
                  <option value="">--Select Head Dept--</option>
                  
                </select>
                <label for="notes">Head Dept.</label>
                <input type="hidden" name="head_dept" id="head_dept" class="form-control" value="<?= $_SESSION['capex_poe']['head_dept']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_head_dept'); ?>">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6 col-lg-3">
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-content">
                    <input type="text" name="document_number" id="document_number" class="form-control" maxlength="6" value="<?= $_SESSION['capex_poe']['document_number']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_doc_number'); ?>" required>
                    <label for="document_number">Document NO.</label>
                  </div>
                  <span class="input-group-addon"><?= poe_format_number(); ?></span>
                </div>
              </div>

              <div class="form-group">
                <input type="text" name="document_date" id="document_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-end-date="0d" class="form-control" value="<?= $_SESSION['capex_poe']['document_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_document_date'); ?>" required>
                <label for="document_date">Date</label>
              </div>

              <div class="form-group">
                <input type="text" name="document_reference" id="document_reference" class="form-control" value="<?= $_SESSION['capex_poe']['document_reference']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_document_reference'); ?>" required>
                <label for="document_reference">Reference Document</label>
              </div>
            </div>

            <div class="col-sm-6 col-lg-4">
              <div class="form-group">
                <input type="text" name="created_by" id="created_by" class="form-control" value="<?= $_SESSION['capex_poe']['created_by']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_created_by'); ?>" required>
                <label for="created_by">Created By</label>
              </div>

              <div class="form-group">
                <input type="text" name="approved_by" id="approved_by" class="form-control" value="<?= $_SESSION['capex_poe']['approved_by']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_approved_by'); ?>" required>
                <label for="approved_by">Approved/Rejected By</label>
              </div>

              <div class="form-group hide">
                <select name="default_currency" id="default_currency" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_default_currency'); ?>" required>
                  <option value="USD" <?= ('USD' == $_SESSION['capex_poe']['default_currency']) ? 'selected' : ''; ?>>USD (US Dolar)</option>
                  <option value="IDR" <?= ('IDR' == $_SESSION['capex_poe']['default_currency']) ? 'selected' : ''; ?>>IDR (Indonesian Rupiah)</option>
                </select>
                <label for="default_currency">Currency</label>
              </div>

              <div class="form-group hide">
                <input type="number" name="exchange_rate" id="exchange_rate" class="form-control" value="<?= $_SESSION['capex_poe']['exchange_rate']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_exchange_rate'); ?>" required>
                <label for="exchange_rate">Exchange Rate IDR to USD</label>
              </div>
            </div>

            <div class="col-sm-12 col-lg-5">
              <div class="form-group">
                <textarea name="notes" id="notes" class="form-control" rows="3" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_notes'); ?>"><?= $_SESSION['capex_poe']['notes']; ?></textarea>
                <label for="notes">Notes</label>
              </div>
              <div class="form-group">
                <select name="approval" id="approval" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_default_approval'); ?>" required>
                  <option value="with_approval" <?= ('with_approval' == $_SESSION['capex_poe']['approval']) ? 'selected' : ''; ?>>With Approval</option>
                  <option value="without_approval" <?= ('without_approval' == $_SESSION['capex_poe']['approval']) ? 'selected' : ''; ?>>Without Approval</option>
                </select>
                <label for="approval">Approval</label>
              </div>
            </div>
          </div>
        </div>

        <?php if (isset($_SESSION['capex_poe']['request'])) : ?>
          <div class="document-data table-responsive">
            <table class="table table-hover table-bordered">
              <thead>
                <tr>
                  <th class="middle-alignment" rowspan="2"></th>
                  <th class="middle-alignment" rowspan="2">Description</th>
                  <th class="middle-alignment" rowspan="2">P/N</th>
                  <th class="middle-alignment" rowspan="2">Alt. P/N</th>
                  <th class="middle-alignment" rowspan="2">Remarks</th>
                  <th class="middle-alignment" rowspan="2">PR Number</th>
                  <th class="middle-alignment text-right" rowspan="2">Qty</th>


                  <?php foreach ($_SESSION['capex_poe']['vendors'] as $key => $vendor) : ?>
                    <th class="middle-alignment text-center" colspan="3">
                      <?= $vendor['vendor'] ?>
                    </th>
                  <?php endforeach; ?>
                </tr>

                <tr>
                  <?php for ($v = 0; $v < count($_SESSION['capex_poe']['vendors']); $v++) : ?>
                    <th class="middle-alignment text-center">Unit Price <span class="currency"><?= $_SESSION['capex_poe']['vendors'][$v]['vendor_currency']; ?></span></th>
                    <th class="middle-alignment text-center">Core Charge <span class="currency"><?= $_SESSION['capex_poe']['vendors'][$v]['vendor_currency']; ?></span></th>
                    <th class="middle-alignment text-center">Total Amount <span class="currency"><?= $_SESSION['capex_poe']['vendors'][$v]['vendor_currency']; ?></span></th>
                  <?php endfor; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_SESSION['capex_poe']['request'] as $id => $request) : ?>
                  <tr id="row_<?= $id; ?>">
                    <td width="1">
                      <a href="<?= site_url($module['route'] . '/delete_request/' . $id); ?>" class="btn btn-icon-toggle btn-danger btn-sm btn_delete_request">
                        <i class="fa fa-trash"></i>
                      </a>
                    </td>
                    <td>
                      <?= $request['description']; ?>
                    </td>
                    <td class="no-space">
                      <?= $request['part_number']; ?>
                    </td>
                    <td class="no-space">
                      <?= $request['alternate_part_number']; ?>
                    </td>
                    <td>
                      <?= $request['remarks']; ?>
                    </td>
                    <td>
                      <?= $request['purchase_request_number']; ?>
                    </td>
                    <td>
                      <?= number_format($request['quantity'], 2); ?>
                    </td>
                    
                    <?php foreach ($_SESSION['capex_poe']['vendors'] as $key => $vendor) : ?>
                      <?php
                        if ($_SESSION['capex_poe']['request'][$id]['vendors'][$key]['is_selected'] == 't') {
                          $style = 'background-color: green; color: white';
                        } else {
                          $style = '';
                        }
                      ?>

                      <td style="<?= $style; ?>">
                        <?= anchor($module['route'] . '/set_selected_vendor/' . $id . '/' . $key, number_format($_SESSION['capex_poe']['request'][$id]['vendors'][$key]['unit_price'], 2), 'style="color: black"'); ?>

                      </td>

                      <td style="<?= $style; ?>">
                        <?= number_format($_SESSION['capex_poe']['request'][$id]['vendors'][$key]['core_charge'], 2); ?>
                      </td>

                      <td style="<?= $style; ?>">
                        <?= number_format($_SESSION['capex_poe']['request'][$id]['vendors'][$key]['total'], 2); ?>
                      </td>
                    <?php endforeach; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
      <div class="card-actionbar">
        <div class="card-actionbar-row">
          <div class="pull-left">
            <?php if (!isset($_SESSION['capex_poe']['edit'])) : ?>
            <a href="<?= site_url($module['route'] . '/add_request'); ?>" onClick="return popup(this, 'add_request')" class="hide btn btn-primary ink-reaction">
              Select Request
            </a>
            <a href="#modal-add-item" data-toggle="modal" data-target="#modal-add-item" class="btn btn-primary ink-reaction btn-open-offcanvas">
              Add Item
            </a>
            <?php endif; ?>

            <?php if (!empty($_SESSION['capex_poe']['request'])) : ?>
              <a href="<?= site_url($module['route'] . '/edit_request'); ?>" onClick="return popup(this, 'edit_request')" class="btn btn-primary ink-reaction">
                Edit Request
              </a>

              <a href="<?= site_url($module['route'] . '/add_vendor'); ?>" onClick="return popup(this, 'add_vendor')" class="btn btn-primary ink-reaction">
                Select Vendor
              </a>
              <a href="<?= site_url($module['route'] . '/attachment'); ?>" onClick="return popup(this, 'attachment')" class="btn btn-primary ink-reaction">
                Attachment
              </a>
            <?php endif; ?>
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
                      <input type="text" id="search_request_item" class="form-control" data-target="<?=site_url($module['route'] .'/search_request_item/');?>">
                      <label for="search_request_item">Search Capex Request Items</label>
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
                        <input type="text" name="group" id="group" class="form-control input-sm" readonly>
                        <label for="group">Group</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="part_number" id="part_number" class="form-control input-sm" data-source="<?=site_url($module['route'] .'/search_items_by_part_number/');?>" required>
                        <label for="part_number">Part Number</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="description" id="description" class="form-control input-sm" data-source="<?= site_url($module['route'] . '/search_items_by_part_number/'); ?>" required>
                        <label for="description">Description</label>
                      </div>

                      <div class="form-group">
                        <input type="text" name="unit" id="unit" class="form-control input-sm" data-source="<?= site_url($modules['ajax']['route'] . '/search_item_units/'); ?>" required>
                        <label for="unit">Unit</label>
                      </div>

                      <div class="form-group hide">
                        <input type="text" name="on_hand_quantity" id="on_hand_quantity" class="form-control input-sm" value="1" readonly>
                        <label for="on_hand_quantity">On Hand Quantity</label>
                      </div>

                      <div class="form-group hide">
                        <input type="text" name="minimum_quantity" id="minimum_quantity" class="form-control input-sm" value="1" readonly>
                        <label for="minimum_quantity">Min. Quantity</label>
                      </div>
                    </fieldset>
                  </div>
                  <div class="col-sm-6 col-lg-4">
                    <div class="row">
                      <div class="col-sm-12 col-lg-12">
                        <fieldset>
                          <legend>Request</legend>

                          <div class="form-group">
                            <input type="text" name="quantity_request" id="quantity_request" class="form-control input-sm" value="0" readonly>
                            <label for="total">Qantity Request</label>
                          </div>

                          <div class="form-group">
                            <input type="text" name="total" id="total_request" class="form-control input-sm" value="0" readonly>
                            <label for="total">Total Request</label>
                          </div>                          
                        </fieldset>
                      </div>
                    </div>                    
                  </div>
                </div>
              </div>

              <div class="col-sm-12 col-lg-4">
                <div class="row">
                  <div class="col-sm-12 col-lg-12">
                    <fieldset>
                      <legend>Order</legend>

                      <div class="form-group">
                        <input type="text" name="quantity" id="quantity" class="form-control input-sm" value="1" required>
                        <label for="quantity">Quantity</label>
                      </div>
                      <div class="form-group">
                        <input type="text" name="price" id="price" class="form-control input-sm" value="0" required>
                        <label for="quantity">Price</label>
                      </div>
                      <div class="form-group">
                        <input type="text" name="total" id="total" class="form-control input-sm" value="0" readonly="readonly">
                        <label for="quantity">Total</label>
                      </div>

                      <div class="form-group">
                        <textarea name="remarks" id="remarks" data-tag-name="remarks" class="form-control input-sm"></textarea>
                        <label for="remarks">Additional Info/Remarks</label>
                      </div>
                    </fieldset>
                  </div>
                </div>                
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <input type="hidden" id="inventory_purchase_request_detail_id" name="inventory_purchase_request_detail_id">
            <input type="hidden" id="purchase_request_number" name="purchase_request_number"> 

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

  function popup(mylink, windowname) {
    var height = window.innerHeight;
    var widht;
    var href;

    if (screen.availWidth > 768) {
      width = 769;
    } else {
      width = screen.availWidth;
    }

    var left = (screen.availWidth / 2) - (width / 2);
    var top = 0;
    // var top = (screen.availHeight / 2) - (height / 2);

    if (typeof(mylink) == 'string') href = mylink;
    else href = mylink.href;

    window.open(href, windowname, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + width + ', height=' + height + ', top=' + top + ', left=' + left);

    if (!window.focus) return true;
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
    var buttonDeleteDocumentItem = $('.btn_delete_request');
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

    $('[data-provide="datepicker"]').datepicker({
      autoclose: true,
      todayHighlight: true,
      format: 'yyyy-mm-dd'
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
      } else {
        $(buttonSubmitDocument).attr('disabled', false);
      }

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

    $('#default_currency').on('change', function() {
      var currency = $(this).val();
      console.log(currency);
      $('.currency').html(currency);
    });

    $.ajax({
      url: $('#search_request_item').data('target'),
      dataType: "json",
      success: function(resource) {
        $('#search_request_item').autocomplete({
            autoFocus: true,
            minLength: 1,

            source: function(request, response) {
              var results = $.ui.autocomplete.filter(resource, request.term);
              response(results.slice(0, 5));
            },

            focus: function(event, ui) {
              return false;
            },

            select: function(event, ui) {
              var sisa_request = parseFloat(ui.item.total)-parseFloat(ui.item.process_amount);
              var left_quantity_request = parseFloat(ui.item.quantity)-parseFloat(ui.item.process_qty);
              $('#part_number').val(ui.item.product_code);
              $('#description').val(ui.item.product_name);
              $('#group').val('CAPEX');
              $('#additional_info').val("");
              $('#quantity_request').val(parseFloat(left_quantity_request));
              $('#quantity').val(0);
              $('#price').val(0);
              $('#total').val(0);
              $('#total_request').val(parseFloat(sisa_request));
              $('#unit').val("");
              $('#pr_number').val(ui.item.pr_number);
              $('#inventory_purchase_request_detail_id').val(ui.item.id);
              $('#purchase_request_number').val(ui.item.pr_number);

              $('input[rel="unit_price"]').val(ui.item.price);

              $('#alternate_part_number').focus();

              $('#search_request_item').val('');

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

    $.ajax({
      url: $('input[id="part_number"]').data('source'),
      dataType: "json",
      success: function(resource) {
        $('input[id="part_number"]').autocomplete({
            autoFocus: true,
            minLength: 2,

            source: function(request, response) {
              var results = $.ui.autocomplete.filter(resource, request.term);
              response(results.slice(0, 5));
            },

            focus: function(event, ui) {
              return false;
            },

            select: function(event, ui) {
              $('input[id="part_number"]').val(ui.item.part_number);
              $('input[id="description"]').val(ui.item.description);
              $('input[id="unit"]').val(ui.item.unit);
              $('input[id="minimum_quantity"]').val(ui.item.minimum_quantity);

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

    $('input[id="quantity"],input[id="price"]').on('change', function (e) {
      sum();
    });

    function sum(){
      var total = parseFloat($("#quantity").val()) * parseFloat($("#price").val());

      $("#total").val(total).trigger("change");
    }

    $("#total").on("change", function(){
      if(parseFloat($(this).val()) >0){
        $("#modal-add-item-submit").prop("disabled", false);
      }else{
        $("#modal-add-item-submit").prop("disabled", true);
      }
    });

    $('#annual_cost_center_id').on('change', function() {
      var prev = $(this).data('val');
      var val = $(this).val();
      var url = $(this).data('source');

      if (prev != ''){
        var conf = confirm("You have changing Department. Continue?");

        if (conf == false){
          return false;
        }
      }

      window.location.href = url + '/' + val;

    });

    $('#head_dept_select').on('change', function() {
      var val = $(this).val();
      var url = $(this).data('source');

      $.get(url, {
        data: val
      });
      $('#head_dept').val(val).trigger('change');

    });

    function get_head_dept_user() {
      $('#head_dept').html('');

      var head_dept = $('#head_dept').val();

      $.ajax({
        url: "<?= site_url($module['route'] . '/get_head_dept_user'); ?>",
        dataType: "json",
        success: function(resource) {
          console.log(resource);
          $('#head_dept_select').html('');
          $("#head_dept_select").append('<option value="">--Select Head Dept--</option>');
          $.each(resource, function(i, item) {
            if(head_dept==item.username){
              var text = '<option value="' +item.username+'" selected>' +item.person_name+'</option>';
            }else{
              var text = '<option value="' +item.username+'">' +item.person_name+'</option>';
            }            
            $("#head_dept_select").append(text);
          });
          
        }
      });
    }

    get_head_dept_user()
    
  });
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
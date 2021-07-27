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
                <div class="row">
                  <div class="col-xs-12">
                    <label for="document_number">Document No.</label>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-6">
                    <select name="format_number" id="format_number" class="form-control" value="<?= $_SESSION['order']['format_number']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_format_number'); ?>" required>
                      <option value="POM" <?= ('POM' == $_SESSION['order']['format_number']) ? 'selected' : ''; ?>>POM</option>
                      <option value="WOM" <?= ('WOM' == $_SESSION['order']['format_number']) ? 'selected' : ''; ?>>WOM</option>
                    </select>

                  </div>
                  <div class="col-xs-6">
                    <input type=" text" name="document_number" id="document_number" class="form-control" maxlength="6" value="<?= $_SESSION['order']['document_number']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_doc_number'); ?>" required>
                    <input type="hidden" name="pom_number" id="pom_number" value="<?= $_SESSION['order']['pom_document_number']; ?>">
                    <input type="hidden" name="wom_number" id="wom_number" value="<?= $_SESSION['order']['wom_document_number']; ?>">
                    <!-- <label for="document_number">Document No.</label> -->
                  </div>

                </div>
              </div>

              <div class="form-group">
                <input type="text" name="document_date" id="document_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-end-date="0d" class="form-control" value="<?= $_SESSION['order']['document_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_document_date'); ?>" required>
                <label for="document_date">Date</label>
              </div>

              <div class="form-group">
                <input type="text" name="issued_by" id="issued_by" class="form-control" value="<?= $_SESSION['order']['issued_by']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_issued_by'); ?>" required>
                <label for="issued_by">Issued By</label>
              </div>

              <div class="form-group hide">
                <input type="text" name="checked_by" id="checked_by" class="form-control" value="<?= $_SESSION['order']['checked_by']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_checked_by'); ?>">
                <label for="checked_by">Checked By</label>
              </div>

              <div class="form-group hide">
                <input type="text" name="known_by" id="known_by" class="form-control" value="<?= $_SESSION['order']['known_by']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_known_by'); ?>">
                <label for="known_by">Approved (HOS)</label>
              </div>

              <div class="form-group hide">
                <input type="text" name="approved_by" id="approved_by" class="form-control" value="<?= $_SESSION['order']['approved_by']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_approved_by'); ?>">
                <label for="approved_by">Approved (CFO)</label>
              </div>

              <div class="form-group">
                <select name="default_currency" id="default_currency" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_default_currency'); ?>" required>
                  <option value="USD" <?= ('USD' == $_SESSION['order']['default_currency']) ? 'selected' : ''; ?>>USD (US Dolar)</option>
                  <option value="IDR" <?= ('IDR' == $_SESSION['order']['default_currency']) ? 'selected' : ''; ?>>IDR (Indonesian Rupiah)</option>
                </select>
                <label for="default_currency">Currency</label>
              </div>

              <div class="form-group hide">
                <input type="number" name="exchange_rate" id="exchange_rate" class="form-control" value="<?= $_SESSION['order']['exchange_rate']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_exchange_rate'); ?>" required>
                <label for="exchange_rate">Exchange Rate IDR to USD</label>
              </div>

              <div class="form-group">
                <input type="number" name="discount" id="discount" class="form-control" value="<?= $_SESSION['order']['discount']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_discount'); ?>">
                <label for="discount">Discount</label>
              </div>

              <div class="form-group">
                <input type="number" name="taxes" id="taxes" class="form-control" value="<?= $_SESSION['order']['taxes']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_taxes'); ?>">
                <label for="taxes">Taxes</label>
              </div>

              <div class="form-group">
                <input type="number" name="shipping_cost" id="shipping_cost" class="form-control" value="<?= $_SESSION['order']['shipping_cost']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_shipping_cost'); ?>">
                <label for="shipping_cost">Shipping Cost</label>
              </div>

              <div class="form-group">
                <textarea name="notes" id="notes" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_notes'); ?>"><?= $_SESSION['order']['notes']; ?></textarea>
                <label for="notes">Notes</label>
              </div>
            </div>

            <div class="col-sm-6 col-lg-3">
              <div class="form-group">
                <select name="vendor" id="vendor" class="form-control" data-source="<?= site_url($module['route'] . '/set_vendor/'); ?>" required>
                  <option value="">-- SELECT VENDOR</option>
                  <?php foreach (available_vendors(config_item('auth_inventory')) as $v => $vendor) : ?>
                    <option value="<?= $vendor; ?>" <?= ($vendor == $_SESSION['order']['vendor']) ? 'selected' : ''; ?>>
                      <?= $vendor; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <label for="vendor">Vendor</label>
              </div>

              <div class="form-group">
                <textarea name="vendor_address" id="vendor_address" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_vendor_address'); ?>"><?= $_SESSION['order']['vendor_address']; ?></textarea>
                <label for="vendor_address">Address</label>
              </div>

              <div class="form-group">
                <input type="text" name="vendor_country" id="vendor_country" class="form-control" value="<?= $_SESSION['order']['vendor_country']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_vendor_country'); ?>" required>
                <label for="vendor_country">Country</label>
              </div>

              <div class="form-group">
                <input type="text" name="vendor_attention" id="vendor_attention" class="form-control" value="<?= $_SESSION['order']['vendor_attention']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_vendor_attention'); ?>" required>
                <label for="vendor_attention">Phone/Email/PIC</label>
              </div>

              <div class="form-group">
                <input type="text" name="reference_quotation" id="reference_quotation" class="form-control" value="<?= $_SESSION['order']['reference_quotation']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_reference_quotation'); ?>" required>
                <label for="reference_quotation">Ref. Quotation</label>
              </div>
            </div>

            <div class="col-sm-6 col-lg-3">
              <div class="form-group">
                <input type="text" name="deliver_company" id="deliver_company" class="form-control" value="<?= $_SESSION['order']['deliver_company']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_deliver_company'); ?>" required>
                <label for="deliver_company">Deliver To</label>
              </div>

              <div class="form-group">
                <textarea name="deliver_address" id="deliver_address" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_deliver_address'); ?>"><?= $_SESSION['order']['deliver_address']; ?></textarea>
                <label for="deliver_address">Address</label>
              </div>

              <div class="form-group">
                <input type="text" name="deliver_country" id="deliver_country" class="form-control" value="<?= $_SESSION['order']['deliver_country']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_deliver_country'); ?>" required>
                <label for="deliver_country">Country</label>
              </div>

              <div class="form-group">
                <input type="text" name="deliver_attention" id="deliver_attention" class="form-control" value="<?= $_SESSION['order']['deliver_attention']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_deliver_attention'); ?>" required>
                <label for="deliver_attention">Phone/Email/PIC</label>
              </div>

              <div class="form-group">
                <select name="payment_type" id="payment_type" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_payment_type'); ?>" required>
                  <option value="CREDIT" <?= ('CREDIT' == $_SESSION['order']['payment_type']) ? 'selected' : ''; ?>>CREDIT</option>
                  <option value="CASH" <?= ('CASH' == $_SESSION['order']['payment_type']) ? 'selected' : ''; ?>>CASH</option>
                </select>
                <label for="payment_type">Payment Type</label>
              </div>
            </div>

            <div class="col-sm-6 col-lg-3">
              <div class="form-group">
                <input type="text" name="bill_company" id="bill_company" class="form-control" value="<?= $_SESSION['order']['bill_company']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_bill_company'); ?>" required>
                <label for="bill_company">Bill To</label>
              </div>

              <div class="form-group">
                <textarea name="bill_address" id="bill_address" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_bill_address'); ?>"><?= $_SESSION['order']['bill_address']; ?></textarea>
                <label for="bill_address">Address</label>
              </div>

              <div class="form-group">
                <input type="text" name="bill_country" id="bill_country" class="form-control" value="<?= $_SESSION['order']['bill_country']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_bill_country'); ?>" required>
                <label for="bill_country">Country</label>
              </div>

              <div class="form-group">
                <input type="text" name="bill_attention" id="bill_attention" class="form-control" value="<?= $_SESSION['order']['bill_attention']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_bill_attention'); ?>" required>
                <label for="bill_attention">Phone/Email/PIC</label>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-content">
                    <input type="text" name="term_payment" id="term_payment" class="form-control" value="<?= $_SESSION['order']['term_payment']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_term_payment'); ?>" required>
                    <label for="document_number">Term of Payment</label>
                  </div>
                  <span class="input-group-addon">Days</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php if (isset($_SESSION['order']['items'])) : ?>
          <div class="document-data table-responsive">
            <table class="table table-hover" id="table-document">
              <thead>
                <tr>
                  <th class="middle-alignment"></th>
                  <th class="middle-alignment">Description</th>
                  <th class="middle-alignment">P/N</th>
                  <th class="middle-alignment">Alt. P/N</th>
                  <th class="middle-alignment text-center" colspan="2">Quantity</th>
                  <th class="middle-alignment">Unit Price
                    <!-- <?= $_SESSION['order']['default_currency']; ?> -->
                  </th>
                  <th class="middle-alignment">Core Charge
                    <!-- <?= $_SESSION['order']['default_currency']; ?> -->
                  </th>
                  <th class="middle-alignment">Total Amount
                    <!-- <?= $_SESSION['order']['default_currency']; ?> -->
                  </th>
                  <th class="middle-alignment">Ref. POE</th>
                  <th class="middle-alignment">Remarks</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_SESSION['order']['items'] as $i => $item) : ?>
                  <tr id="row_<?= $i; ?>">
                    <td width="1">
                      <a href="<?= site_url($module['route'] . '/del_item/' . $i); ?>" class="btn btn-icon-toggle btn-danger btn-sm btn_delete_document_item">
                        <i class="fa fa-trash"></i>
                      </a>
                      <a href="<?= site_url($module['route'] . '/edit_item/' . $i); ?>" onClick="return popup(this, 'edit')"  class="btn btn-icon-toggle btn-info btn-sm">
                        <i class="fa fa-pencil"></i>
                      </a>
                    </td>
                    <td>
                      <a href="<?= site_url($module['route'] . '/edit_item/' . $i); ?>" onClick="return popup(this, 'edit')">
                        <?= $item['description']; ?>
                      </a>
                    </td>
                    <td class="no-space">
                      <?= $item['part_number']; ?> | S.N : <?= $item['serial_number']; ?>
                    </td>
                    <td class="no-space">
                      <?= $item['alternate_part_number']; ?>
                    </td>
                    <td class="text-right">
                      <?= number_format($item['quantity'], 2); ?>
                    </td>
                    <td>
                      <?= $item['unit']; ?>
                    </td>
                    <td>
                      <?= number_format($item['unit_price'], 2); ?>
                    </td>
                    <td>
                      <?= number_format($item['core_charge'], 2); ?>
                    </td>
                    <td>
                      <?= number_format($item['total_amount'], 2); ?>
                    </td>
                    <td>
                      <?= $item['evaluation_number']; ?>
                    </td>
                    <td>
                      <?= $item['purchase_request_number']; ?>
                    </td>
                    <td>
                      <?= $item['remarks']; ?>
                    </td>
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
            <?php if (empty($_SESSION['order']['vendor']) === FALSE) : ?>
              <!-- <a href="#modal-add-item" data-toggle="modal" data-target="#modal-add-item" class="btn btn-primary ink-reaction btn-open-offcanvas">
                Add Item
              </a> -->
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

        <?= form_open(site_url($module['route'] . '/add_item'), array(
          'autocomplete' => 'off',
          'id'    => 'ajax-form-create-document',
          'class' => 'form form-validate ui-front',
          'role'  => 'form'
        )); ?>

        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12">
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-content">
                    <input type="text" id="search_poe_item" class="form-control" data-target="<?= site_url($module['route'] . '/search_poe_item/'); ?>">
                    <label for="search_poe_item">Search item from POE</label>
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
              <fieldset>
                <legend>General</legend>

                <div class="form-group">
                  <input type="text" name="part_number" id="part_number" class="form-control input-sm" readonly>
                  <label for="part_number">Part Number</label>
                </div>

                <div class="form-group">
                  <input type="text" name="alternate_part_number" id="alternate_part_number" class="form-control input-sm">
                  <label for="alternate_part_number">Alt. Part Number</label>
                </div>

                <div class="form-group">
                  <input type="text" name="description" id="description" class="form-control input-sm" readonly>
                  <label for="description">Description</label>
                </div>

                <div class="form-group">
                  <textarea name="remarks" id="remarks" class="form-control input-sm"></textarea>
                  <label for="remarks">Remarks</label>
                </div>
              </fieldset>
            </div>

            <div class="col-sm-12 col-lg-4">
              <fieldset>
                <legend>Required</legend>

                <div class="form-group">
                  <input type="text" name="quantity" id="quantity" class="form-control input-sm" readonly>
                  <label for="quantity">Quantity</label>
                </div>

                <div class="form-group">
                  <input type="text" name="unit" id="unit" class="form-control input-sm" readonly>
                  <label for="unit">Unit of Measurement</label>
                </div>

                <div class="form-group">
                  <input type="number" name="unit_price" id="unit_price" class="form-control input-sm" readonly>
                  <label for="unit_price">Unit Price <?= $_SESSION['order']['default_currency']; ?></label>
                </div>

                <div class="form-group">
                  <input type="number" name="core_charge" id="core_charge" class="form-control input-sm" readonly>
                  <label for="core_charge">Core Charge <?= $_SESSION['order']['default_currency']; ?></label>
                </div>

                <div class="form-group">
                  <input type="number" name="total_amount" id="total_amount" class="form-control input-sm" readonly>
                  <label for="total_amount">Total Amount <?= $_SESSION['order']['default_currency']; ?></label>
                </div>
              </fieldset>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <input type="hidden" id="purchase_order_evaluation_items_vendors_id" name="purchase_order_evaluation_items_vendors_id">
          <input type="hidden" id="evaluation_number" name="evaluation_number">

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
      <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?= site_url($module['route'] . '/save_po'); ?>">
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
    var buttonDeleteDocumentItem = $('.btn_delete_document_item');
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

    var today = new Date();
    var end_date = new Date();
    today.setDate(today.getDate() - 30);
    end_date.setDate(today.getDate() + 30);

    $('[data-provide="datepicker"]').datepicker({
      autoclose: true,
      todayHighlight: true,
      format: 'yyyy-mm-dd',
      startDate: today,
      endDate: end_date
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

    $.ajax({
      url: $('#search_poe_item').data('target'),
      dataType: "json",
      success: function(resource) {
        $('#search_poe_item').autocomplete({
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
              $('#part_number').val(ui.item.part_number);
              $('#description').val(ui.item.description);
              $('#quantity').val(ui.item.quantity);
              $('#unit_price').val(ui.item.unit_price);
              $('#core_charge').val(ui.item.core_charge);
              $('#total_amount').val((ui.item.core_charge * ui.item.quantity) + (ui.item.unit_price * ui.item.quantity));
              $('#unit').val(ui.item.unit);
              $('#purchase_order_evaluation_items_vendors_id').val(ui.item.id);
              $('#evaluation_number').val(ui.item.evaluation_number);

              $('input[rel="unit_price"]').val(ui.item.unit_price);

              $('#alternate_part_number').focus();

              $('#search_poe_item').val('');

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

    $('#format_number').on('change', function() {
      var format = $(this).val();
      if (format == 'POM') {
        var number = $('#pom_number').val();
        <?php $_SESSION['order']['document_number'] = $_SESSION['order']['pom_document_number']; ?>
      }
      if(format == 'WOM') {
        var number = $('#wom_number').val();
        <?php $_SESSION['order']['document_number'] = $_SESSION['order']['wom_document_number']; ?>
      }
      $('#document_number').val(number);

    });


  });
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
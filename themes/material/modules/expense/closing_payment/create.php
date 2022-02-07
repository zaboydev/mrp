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
                    <input type="text" name="pr_number" id="pr_number" class="form-control" value="[auto]" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_doc_number'); ?>" readonly>
                    <label for="pr_number">Document No.</label>
                  </div>
                  <span class="input-group-addon" id="format_number"><?=request_payment_format_number($_SESSION['request_closing']['type']);?></span>
                </div>
              </div>

              <div class="form-group">
                <input type="text" name="date" id="date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control" value="<?= $_SESSION['request_closing']['date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_date'); ?>" required>
                <label for="date">Date</label>
              </div>

              <div class="form-group">
                <input type="text" name="purposed_date" id="purposed_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control" value="<?= $_SESSION['request_closing']['purposed_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_purposed_date'); ?>" required>
                <label for="purposed_date">Purposed Date</label>
              </div>
              
            </div>

            <div class="col-sm-12 col-lg-4">
              

              <div class="form-group">
                <select name="default_currency" id="default_currency" class="form-control" data-source="<?= site_url($module['route'] . '/set_default_currency'); ?>" required>
                <?php foreach ($this->config->item('currency') as $key => $value) : ?>
                  <option value="<?=$key?>" <?= ($key == $_SESSION['request_closing']['currency']) ? 'selected' : ''; ?>><?=$value?></option>
                <?php endforeach; ?>
                </select>
                <label for="default_currency">Currency</label>
              </div>

              <div class="form-group <?= (config_item('auth_role') == 'PIC STAFF') ? 'hide' : ''; ?>">
                    <select name="type" id="type" class="form-control" data-source="<?= site_url($module['route'] . '/set_type_transaction/'); ?>" required>
                      <option value="CASH" <?= ('CASH' == $_SESSION['request_closing']['type']) ? 'selected' : ''; ?>>Cash</option>
                      <option value="BANK" <?= ('BANK' == $_SESSION['request_closing']['type']) ? 'selected' : ''; ?>>Bank Transfer</option>
                    </select>
                    <label for="vendor">Transaction Type</label>
              </div>

              <div class="form-group">
                <select name="account" id="account" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_account'); ?>" required>
                    <option value="">-- SELECT Account --</option>
                    <?php foreach (getAccount($_SESSION['request_closing']['type']) as $key => $account) : ?>
                        <option value="<?= $account['coa']; ?>" <?= ($account['coa'] == $_SESSION['request_closing']['coa_kredit']) ? 'selected' : ''; ?>>
                        <?= $account['coa']; ?> <?= $account['group']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="vendor">Account</label>
              </div>
            </div>
            <div class="col-sm-12 col-lg-4">
              <div class="form-group">
                <input type="vendor" name="vendor" id="vendor" class="form-control" value="<?= $_SESSION['request_closing']['vendor']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_vendor'); ?>" required="required">
                <label for="vendor">Pay To</label>
              </div>

              <div class="form-group">
                <textarea name="notes" id="notes" class="form-control" rows="2" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_notes'); ?>"><?= $_SESSION['request_closing']['closing_notes']; ?></textarea>
                <label for="notes">Notes</label>
              </div>
            </div>
          </div>
        </div>

        <?php if (isset($_SESSION['request_closing']['items'])) : ?>
          <?php $grand_total = array(); ?>
          <?php $total_quantity = array(); ?>
          <div class="document-data table-responsive">
            <table class="table table-hover" id="table-document">
              <thead>
                <tr>
                  <th></th>
                  <th>PR Number</th>
                  <th>Akun</th>
                  <th>Ref. IPC</th>
                  <th>Expense Notes</th>
                  <th class="text-right">Amount</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_SESSION['request_closing']['items'] as $i => $items) : ?>
                  <?php $grand_total[] = $items['amount']; ?>
                  <tr id="row_<?= $i; ?>">
                    <td><?= print_string($items['request_id']); ?> - <?= print_string($items['id']); ?></td>
                    <td class="">
                      <?= print_string($items['pr_number']); ?>
                    </td>
                    <td class="">
                      <?= print_string($items['account_code']); ?> - <?= print_string($items['account_name']); ?>
                    </td>
                    <td class="">
                      <?= print_string($items['reference_ipc']); ?> 
                    </td>
                    <td class="">
                      <?= print_string($items['notes']); ?> 
                    </td>
                    <td>
                      <?= print_number($items['amount'], 2); ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <th></th>
                <th>Total</th>
                <th></th>
                <th></th>
                <th><?= print_number(array_sum($grand_total), 2); ?></th>
              </tfoot>
            </table>
          </div>
        <?php endif; ?>
      </div>
      <div class="card-actionbar">
        <div class="card-actionbar-row">
          <div class="pull-left">
            
          </div>
          

          <a href="<?= site_url($module['route'] . '/discard'); ?>" class="btn btn-flat btn-danger ink-reaction">
            Discard
          </a>
        </div>
      </div>
    </div>
    <?= form_close(); ?>
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
          var maximum_price = parseFloat(response.maximum_price);
          var mtd_budget = parseFloat(response.mtd_budget);
              

          $('#account_id').val(response.account_id);
          $('#account_name').val(response.account_name);
          $('#account_code').val(response.account_code);
          $('#annual_cost_center_id').val(response.annual_cost_center_id);
          $('#maximum_price').val(maximum_price);
          $('#mtd_budget').val(mtd_budget);
          $('#expense_monthly_budget_id').val(response.expense_monthly_budget_id);
          $('#additional_info').val(response.additional_info);
          $('#amount').val(response.amount);
          $('#reference_ipc').val(response.reference_ipc);

          $('input[id="amount"]').attr('data-rule-max', parseFloat(response.maximum_price)).attr('data-msg-max', 'max available '+ parseInt(response.maximum_price));

          // $('#issued_quantity').attr('max', parseInt(ui.item.qty_konvers)).focus();
          $('#amount').attr('max', parseFloat(response.maximum_price)).focus();

          $('#unbudgeted_item').val(0);

          $('#account_name').prop("readonly", true);
          $('#account_code').prop("readonly", true);

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

    $('#type').change(function() {
      type_trs = $(this).val();
      var account_view = $('#account');
      var format_number_view = $('#format_number');
      account_view.html('');    

      $.ajax({
        type: "post",
        url: '<?= base_url() . "expense_closing_payment/get_accounts" ?>',
        data: {
          'type': type_trs
        },
        cache: false,
        success: function(response) {
          var data = jQuery.parseJSON(response);
          account_view.html(data.account);

          format_number_view.html('');
          format_number_view.html(data.format_number);
        }
      });

      var val = $(this).val();
      var url = $(this).data('source');

      $.get( url, { data: val });

    });

    $('#default_currency').change(function() {

      var val = $(this).val();
      var url = $(this).data('source');

      $.get( url, { data: val });

      $('#account').val('').trigger('change');

    });

  });
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
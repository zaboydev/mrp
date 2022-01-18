<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="has-actions style-default">
  <div class="section-body">
    <?= form_open(site_url($module['route'] . '/save_payment/'.$id), array('autocomplete' => 'off', 'class' => 'form-xhr-submit form form-validate', 'id' => 'form-create-document')); ?>
    <div class="card">
      <div class="card-body no-padding">
        <?php
        if ($this->session->flashdata('alert'))
          render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
        ?>
        <div class="document-header force-padding">
          <div class="row">
            <div class="col-sm-12 col-lg-6">
              <div class="form-group">
                <input type="text" name="order_number" id="order_number" class="form-control" value="<?= $entity['document_number']?>" readonly>
                <label for="order_number">Document No.</label>
                <input type="hidden" name="cash_request_id" value="<?=$id?>">
              </div>

              <div class="form-group">
                <input type="text" name="date" id="date" class="form-control" value="<?= date('Y-m-d',strtotime($entity['tanggal'])); ?>" readonly>
                <label for="required_date">Request Date</label>
              </div>

              <div class="form-group">
                <input type="text" name="request_by" id="request_by" class="form-control" value="<?= $entity['request_by']; ?>" readonly>
                <label for="required_date">Request By</label>
              </div>

              <div class="form-group">
                <input type="text" name="cash_account" id="cash_account" class="form-control" value="<?= $entity['cash_account_code']; ?> <?= $entity['cash_account_name']; ?> " readonly>
                <label for="required_date">Cash Account</label>
              </div>
              <div class="form-group">
                <input type="text" name="request_amount" id="request_amount" class="form-control" value="<?= $entity['request_amount']?>" readonly>
                <label for="amount">Request Amount</label>
              </div>
              <div class="form-group">
                <textarea name="notes" id="notes" class="form-control" rows="3" readonly><?=$entity['notes']?></textarea>
                <label for="notes">Notes</label>
              </div>
            </div>

            <div class="col-sm-12 col-lg-6"> 
              <div class="form-group">
                <input type="text" name="paid_by" id="paid_by" class="form-control" value="<?= config_item('auth_person_name'); ?>" required>
                <label for="required_date">paid By</label>
              </div>             
              <div class="form-group">
                <input type="text" name="paid_at" id="paid_at" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                <label for="required_date">Paid Date</label>
              </div>
              <div class="form-group">
                <select name="coa_kredit" id="coa_kredit" class="form-control" required>
                  <option value="">-- SELECT Account --</option>
                  <?php foreach (getAccount($entity['type']) as $key => $account) : ?>
                  <option value="<?= $account['coa']; ?>">
                    <?= $account['coa']; ?> <?= $account['group']; ?>
                  </option>
                  <?php endforeach; ?>
                </select>
                <label for="vendor">Bank Account</label>
              </div>
              <div class="form-group">
                <input type="text" name="no_cheque" id="no_cheque" class="form-control" value="">
                <label for="no_cheque">No Cheque</label>
              </div>
              <div class="form-group">
                <input type="text" name="no_konfirmasi" id="no_konfirmasi" class="form-control" value="">
                <label for="amount">No Konfirmasi</label>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card-actionbar">
        <div class="card-actionbar-row">
          <div class="pull-left">
            <a href="<?=site_url($module['route'] .'/attachment');?>" onClick="return popup(this, 'attachment')" class="btn btn-primary ink-reaction">
              Attachment
            </a>
          </div>
          <a href="<?= site_url($module['route'] . '/discard'); ?>" class="btn btn-flat btn-danger ink-reaction">
            Discard
          </a>
        </div>
      </div>
    </div>
    
  </div>

  <div class="section-action style-default-bright">
    <div class="section-floating-action-row">
      <button type="button" class="btn btn-floating-action btn-lg btn-danger btn-xhr-submit btn-tooltip ink-reaction" id="btn-xhr-submit">
        <i class="md md-save"></i>
        <small class="top right">Save Document</small>
      </button>
    </div>
  </div>
  <?= form_close(); ?>
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
    var buttonXhrSubmit = $('#btn-xhr-submit');
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

    // $(document).on('click', '.btn-xhr-submit', function(e) {
    $(buttonXhrSubmit).on('click', function(e) {
      e.stopImmediatePropagation();

      var button = $(this);
      var form = $('.form-xhr-submit');
      var action = form.attr('action');

      // button.attr('disabled', true);
      $(buttonXhrSubmit).attr('disabled', true);

      if (form.valid()) {
        $.post(action, form.serialize()).done(function(data) {
          var obj = $.parseJSON(data);

          if (obj.type == 'danger') {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error(obj.info);
            $(buttonXhrSubmit).attr('disabled', false);
          } else {
            toastr.options.timeOut = 4500;
            toastr.options.closeButton = false;
            toastr.options.progressBar = true;
            toastr.options.positionClass = 'toast-top-right';
            toastr.success(obj.info);

            window.setTimeout(function(){
              window.location.href = '<?=site_url($module['route']);?>';
            }, 5000);
          }
        });
      }else{
        // button.attr('disabled', false);
        $(buttonXhrSubmit).attr('disabled', false);
      }

      
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
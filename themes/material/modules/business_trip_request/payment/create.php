<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<style>
  /* .form-control-payment {
    padding: 0;
    height: 25px;
    border-left: none;
    border-right: none;
    border-top: none;
    border-bottom-color: rgba(12, 12, 12, 0.12);
    background: transparent;
    color: #0c0c0c;
    font-size: 16px;
    -webkit-box-shadow: none;
    box-shadow: none;
  } */

  .form-control-payment {
    display: block;
    width: 100%;
    height: 30px;
    /* padding: 4.5px 14px; */
    font-size: 13px;
    line-height: 1.846153846;
    color: #0c0c0c;
    background-color: #ffffff;
    background-image: none;
    border: 1px solid rgba(12, 12, 12, 0.12);
    border-radius: 2px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
    -webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
  }
  
  .form-control-payment[readonly]{
    background-color : #f0f0f0;
  }

  .form-control-payment:focus{
    border: 1px solid rgba(12, 12, 12, 0.12);
  }

  .price-number{
    text-align:right;
  }

  @media print {

    html,
    body {
      display: block;
      font-family: "Tahoma";
      margin: 0px 0px 0px 0px;
    }

    /*@page {
                size: Faktur Besar;
                }*/
    #footer {
      position: fixed;
      bottom: 0;
    }

  }
</style>
<section class="has-actions style-default">
  <div class="section-body">
    <?= form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form-document')); ?>
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
                    <input type="text" name="pr_number" maxlength="6" id="pr_number" class="form-control" value="<?= $_SESSION['spd_payment']['document_number']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_doc_number'); ?>">
                    <label for="pr_number">Document No.</label>
                  </div>
                  <span class="input-group-addon" id="format_number"><?=$_SESSION['spd_payment']['format_number'];?></span>
                </div>
              </div>

              <div class="form-group">
                <input type="text" name="date" id="date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control" value="<?= $_SESSION['spd_payment']['date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_date'); ?>" required>
                <label for="date">Date</label>
              </div>

              <div class="form-group">
                <input type="text" name="purposed_date" id="purposed_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control" value="<?= $_SESSION['spd_payment']['purposed_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_purposed_date'); ?>" required>
                <label for="purposed_date">Purposed Date</label>
              </div>
              
            </div>

            <div class="col-sm-12 col-lg-4">
              

              <div class="form-group">
                <select name="default_currency" id="default_currency" class="form-control" data-source="<?= site_url($module['route'] . '/set_default_currency'); ?>" required>
                <?php foreach ($this->config->item('currency') as $key => $value) : ?>
                  <option value="<?=$key?>" <?= ($key == $_SESSION['spd_payment']['currency']) ? 'selected' : ''; ?>><?=$value?></option>
                <?php endforeach; ?>
                </select>
                <label for="default_currency">Currency</label>
              </div>

              <div class="form-group <?= (config_item('auth_role') == 'PIC STAFF') ? 'hide' : ''; ?>">
                    <select name="type" id="type" class="form-control" data-source="<?= site_url($module['route'] . '/set_type_transaction/'); ?>" required>
                      <option value="CASH" <?= ('CASH' == $_SESSION['spd_payment']['type']) ? 'selected' : ''; ?>>Cash</option>
                      <option value="BANK" <?= ('BANK' == $_SESSION['spd_payment']['type']) ? 'selected' : ''; ?>>Bank Transfer</option>
                    </select>
                    <label for="vendor">Transaction Type</label>
              </div>

              <div class="form-group">
                <select name="account" id="account" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_account'); ?>" required>
                    <option value="">-- SELECT Account --</option>
                    <?php foreach (getAccount($_SESSION['spd_payment']['type']) as $key => $account) : ?>
                        <option value="<?= $account['coa']; ?>" <?= ($account['coa'] == $_SESSION['spd_payment']['coa_kredit']) ? 'selected' : ''; ?>>
                        <?= $account['coa']; ?> <?= $account['group']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="vendor">Account</label>
              </div>
            </div>
            <div class="col-sm-12 col-lg-4">
              <div class="form-group">
                <input type="text" name="vendor" id="vendor" class="form-control" value="<?= $_SESSION['spd_payment']['vendor']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_vendor'); ?>" required="required">
                <label for="vendor">Pay To</label>
              </div>

              <div class="form-group">
                <textarea name="notes" id="notes" class="form-control" rows="2" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_notes'); ?>"><?= $_SESSION['spd_payment']['notes']; ?></textarea>
                <label for="notes">Notes</label>
              </div>
            </div>
          </div>
        </div>

        <?php if (isset($_SESSION['spd_payment']['items'])) : ?>
          <?php $grand_total = array(); ?>
          <?php $total_quantity = array(); ?>
          <div class="document-data table-responsive">
            <table class="table table-hover" id="table-document">
              <thead>
                <tr>
                  <th>No</th>
                  <th>SPD#</th>
                  <th>Date</th>
                  <th>Person in Charge</th>
                  <th style="text-align:center;">Remarks</th>
                  <th style="text-align:right;">Amount</th>
                  <th style="text-align:right;">Amount Paid</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_SESSION['spd_payment']['items'] as $i => $item) : ?>
                  <?php $grand_total[] = $item['amount_paid']; ?>
                  <tr id="row_<?= $i; ?>">
                    <td>
                      <a href="javascript:;" title="show detail" class="hide btn btn-icon-toggle btn-info btn-xs btn_view_detail" id="btn_<? $i ?>" data-row="<?= $i ?>" data-tipe="view"><i class="fa fa-angle-right"></i>
                      </a>
                    </td>
                    <td>
                      <input name="spd_id[]" id="spd_id_<?= $i ?>" type="hidden" class="form-control-payment" value="<?=$item['spd_id']?>">
                      <input name="account_code[]" id="account_code_<?= $i ?>" type="hidden" class="form-control-payment" value="<?=$item['account_code']?>">
                      <input name="spd_number[]" id="spd_number_<?= $i ?>" type="hidden" class="form-control-payment" value="<?=$item['spd_number']?>">
                      <?= print_string($item['spd_number']); ?>
                    </td>
                    <td>
                      <?= print_string($item['spd_date']); ?> 
                    </td>
                    <td>
                      <?= print_string($item['spd_person_incharge']); ?> 
                    </td>  
                    <td>
                      <input name="remarks[]" id="remarks_<?= $i ?>" type="text" class="form-control-payment" value="<?=$item['remarks']?>">                      
                    </td>  
                    <td>
                      <?= print_number($item['spd_amount']); ?> 
                    </td>   
                    <td>
                      <input name="amount_paid[]" id="in_item_<?= $i ?>" type="number" class="price-number sel_applied_item sel_applied_<?= $i ?> form-control-payment sel_applied_item_add" value="<?=$item['amount_paid']?>">
                     
                    </td>                 
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <th></th>
                <th>Total</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th style="text-align:right;"><span id="total_general"><?= print_number(array_sum($grand_total), 2); ?></span></th>
              </tfoot>
            </table>
          </div>
        <?php endif; ?>
      </div>
      <div class="card-actionbar">
        <div class="card-actionbar-row">
          <div class="pull-left">
            <?php if (!isset($_SESSION['spd_payment']['edit'])) : ?>
            <a href="<?=site_url($module['route'] .'/add_item');?>" onClick="return popup(this, 'add_item')" class="btn btn-primary ink-reaction">
              Select SPD
            </a>            
            <?php endif; ?>
            <button type="button" href="" onClick="addRow()" class="hide btn btn-primary ink-reaction">
              Add
            </button>
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
      <button type="submit" class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" data-href="<?= site_url($module['route'] . '/save');?>">
        <i class="md md-save"></i>
        <small class="top right">Save Document</small>
      </button>
    </div>
  </div>
</section>
<?= form_close(); ?>
<table class="table-row-item hide">
  <tbody>
    <tr>
      <td class="item-list">
        <center>
          <a  href="javascript:;" title="Delete" class="btn btn-icon-toggle btn-danger btn-xs btn-row-delete-item" data-tipe="delete"><i class="fa fa-trash"></i>
          </a>
        </center>                      
      </td>
      <td class="account_code item-list">
        <!-- <input type="text" name="account_code[]" class="form-control-payment"> -->
        <select name="account_code[]" class="form-control-payment" style="width: 100%">
          <option value="">-- SELECT Account --</option>
          <?php foreach (getAccounts() as $key => $account) : ?>
          <option value="<?= $account['coa']; ?>">
          <?= $account['coa']; ?> <?= $account['group']; ?>
          </option>
          <?php endforeach; ?>
        <select>
      </td> 
      <td class="remarks item-list" colspan="4">
        <input type="hidden" name="request_item_id[]" class="form-control-payment">
        <input type="text" name="remarks[]" class="form-control-payment" placeholder="Input Description">
      </td>
      <td class="value item-list">
        <input type="number" name="value[]" class="form-control-payment sel_applied_item_add">
      </td>   
    </tr>
  </tbody>
</table>
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
  <?= html_script('vendors/select2-4.0.3/dist/js/select2.min.js') ?>
  <?= html_script('vendors/select2-pmd/js/pmd-select2.js') ?>
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

    $('.select2').select2({
      theme: "bootstrap",
    });

    sel_applied_item_add();

    function addRow() {
      var row_payment = $('.table-row-item tbody').html();
      var el = $(row_payment);
      $('#table-document tbody').append(el);
      $('#table-document tbody tr:last').find('select[name="account_code[]"]').select2();

      btn_row_delete_item();
      sel_applied_item_add();
      
      // setAddValue();
    }

    function btn_row_delete_item() {
      $('.btn-row-delete-item').click(function () {
        $(this).parents('tr').remove();
        changeTotal2();
      });
    }

    //jika mengisi input add item 
    function sel_applied_item_add(){
      $("#table-document").on("change", ".sel_applied_item_add", function() {
        // console.log('test');
        changeTotal2();

      });
    }
      

    function changeTotal2() {
      var sum = 0
      $('[name="amount_paid[]"]').each(function (key, val) {
        var val = $(this).val();
          
        if(val!=''){
          console.log(val);
          sum = parseFloat(sum) + parseFloat(val);
        }
          
      });

      var currency = parseFloat(sum).toLocaleString('id-ID', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
      $("#total_general").html(currency);
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

      // $('#vendor').on('click focus', function() {
        $.ajax({
          url: $('#vendor').data('search'),
          dataType: "json",
          success: function(resource) {
            $( "#vendor" ).autocomplete({
              source: resource
            });
          }
        });
      // });

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

        var url = $(this).data('href');
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
            $('#pr_number').val(data.document_number).trigger('change');
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
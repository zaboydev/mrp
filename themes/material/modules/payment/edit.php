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
    padding: 4.5px 14px;
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

    <?= form_open(site_url($module['route'] . '/update'), array('autocomplete' => 'off', 'class' => 'form-xhr-submit form form-validate', 'id' => 'form_purposed_payment')); ?>

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
            <div class="newoverlay" id="loadingScreen2" style="display: none;">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-content">
                    <input readonly type="text" name="document_number" id="document_number" class="form-control" maxlength="6" value="[auto]" data-input-type="autoset" data-source="<?=site_url($module['route'] .'/set_doc_number');?>" required>
                    <label for="document_number">Purpose Number</label>
                  </div>
                  <span class="input-group-addon"><?=payment_request_format_number($_SESSION['payment_request']['type']);?></span>
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
                    <?php foreach (available_vendors_by_currency($_SESSION['payment_request']['currency']) as $v => $vendor) : ?>
                        <option value="<?= $vendor; ?>" <?= ($vendor == $_SESSION['payment_request']['vendor']) ? 'selected' : ''; ?>>
                        <?= $vendor; ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                    <label for="vendor">Vendor</label>
                </div>

                <div class="form-group">
                    <select name="type" id="type" class="form-control" data-source="<?= site_url($module['route'] . '/set_type_transaction/'); ?>" required>
                      <option value="CASH" <?= ('CASH' == $_SESSION['payment_request']['type']) ? 'selected' : ''; ?>>Cash</option>
                      <option value="BANK" <?= ('BANK' == $_SESSION['payment_request']['type']) ? 'selected' : ''; ?>>Bank Transfer</option>
                    </select>
                    <label for="vendor">Transaction Type</label>
                </div>

                <div class="form-group">
                    <select name="account" id="account" class="form-control" data-source="<?= site_url($module['route'] . '/set_account/'); ?>" required data-input-type="autoset">
                    <option value="">-- SELECT Account --</option>
                    <?php foreach (getAccount($_SESSION['payment_request']['type']) as $key => $account) : ?>
                        <option value="<?= $account['coa']; ?>" <?= ($account['coa'] == $_SESSION['payment_request']['coa_kredit']) ? 'selected' : ''; ?>>
                        <?= $account['coa']; ?> <?= $account['group']; ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                    <label for="vendor">Account</label>
                </div>

                
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="form-group">
                    <input type="number" name="amount" id="amount" class="form-control" value="<?= $_SESSION['payment_request']['total_amount']; ?>" readonly="readonly">
                    <label for="amount">Amount</label>
                </div>
                <div class="form-group">
                    <textarea name="notes" id="notes" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_notes'); ?>"><?= $_SESSION['payment_request']['notes']; ?></textarea>
                    <label for="notes">Notes</label>
                </div>
            </div>
            <!-- <button class="btn btn-danger" id="add_item" type="button">Add Item</button> -->
          </div>
        </div>

        <div class="document-data table-responsive">
          <table class="table table-hover table-bordered table-nowrap" id="table-document">
            <thead>
              <tr>
                <!-- <th class="middle-alignment">No.</th> -->
                <th width="15%" class="middle-alignment">No PO</th>
                <th width="13%" class="middle-alignment">Description</th>
                <th width="7%" class="middle-alignment">Due Date</th>
                <th width="5%" class="middle-alignment">GRN Qty</th>
                <th width="7%" class="middle-alignment">GRN Val.</th>
                <th width="7%" class="middle-alignment">Amount</th>
                <th width="7%" class="middle-alignment">Purposed Amount</th>
                <th width="7%" class="middle-alignment">Remaining Purposed</th>
                <th width="8%" class="middle-alignment">Qty Paid</th>
                <th width="8%" class="middle-alignment">Amount Purposed</th>
                <th width="5%" class="middle-alignment"></th>
                <th width="8%" class="middle-alignment">Adjustment</th>
              </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['payment_request']['items'] as $id => $items):?> 
                <tr>
                <td>
                  <?=$items['document_number']?> <input id="po_item_id_<?= $id; ?>" name="po_item_id[]" value="<?=$items['purchase_order_item_id']?>" type="hidden" class="form-control-payment">
                </td>
                <td><?=$items['description']?> <input id="desc_<?= $id; ?>" name="desc[]" value="<?=$items['description']?>" type="hidden" class="form-control-payment"></td>
                <td><?=$items['due_date']?> <input id="po_id_<?= $id; ?>" name="po_id[]" value="<?=$items['id_po']?>" type="hidden" class="form-control-payment"></td>
                <td><?=print_number($items['item']['quantity_received'],2)?></td>
                <td>
                  <?= print_number($items['item']['quantity_received'] * ($items['item']['unit_price'] + $items['item']['core_charge']), 2) ?>
                </td>
                <td><?=print_number($items['item']['total_amount'],2)?></td>
                <td>
                <?php if($items['purchase_order_item_id']!=null):?>
                  <?=print_number($items['item']['total_amount']-$items['item']['left_paid_request']-$items['amount_paid'],2)?>
                <?php else:?>
                  <?php if($items['id_po']!=0):?>
                    <?=print_number($items['item']['additional_price']-$items['item']['additional_price_remaining_request']-$items['amount_paid'],2)?>
                  <?php else:?>
                    <?=print_number(0,2)?>                
                  <?php endif; ?>
                <?php endif; ?>
                </td>
                <td>
                <?php if($items['purchase_order_item_id']!=null):?>
                  <?=print_number($items['item']['left_paid_request']+$items['amount_paid'],2)?>
                  <input id="sis_item_<?= $id; ?>" type="number" rel="left_paid_request" name="item[<?= $id; ?>][left_paid_request]" value="<?= $items['item']['left_paid_request']+$items['amount_paid']; ?>" class="hide form-control sel_applied_item">
                <?php else:?>
                  <?php if($items['id_po']!=0):?>
                    <?=print_number($items['item']['additional_price_remaining_request']+$items['amount_paid'],2)?>
                    <input id="sis_item_<?= $id; ?>" type="number" rel="left_paid_request" name="item[<?= $id; ?>][left_paid_request]" value="<?= $items['item']['additional_price_remaining_request']+$items['amount_paid']; ?>" class="hide form-control sel_applied_item">
                  <?php else:?>
                    <?=print_number(0,2)?>
                    <input id="sis_item_<?= $id; ?>" type="number" rel="left_paid_request" name="item[<?= $id; ?>][left_paid_request]" value="0" class="hide form-control sel_applied_item">
                                  
                  <?php endif; ?>
                <?php endif; ?>
                </td>
                <td>
                    <input name="qty_paid[]" id="in_qty_paid_<?= $id; ?>" data-id="<?= $id; ?>" type="number" class="form-control-payment sel_applied_item" value="<?=$items['quantity_paid']?>">
                </td>
                <td>
                    <input name="value[]" id="in_item_<?= $id; ?>" data-id="<?= $id; ?>" type="number" class="form-control-payment sel_applied_item" value="<?=$items['amount_paid']?>">
                </td>
                <td>
                  <input type="checkbox" id="cb_<?= $id ?>" data-row="<?= $id ?>" data-id="<?= $id ?>" style="display: inline;" class="check_adj" <?php if($items['adj_value']!=0):?> checked <?php endif;?>>
                </td>
                <td>
                    <input name="adj_value[]" id="in_adj_<?= $id ?>" type="number" class="<?php if($items['adj_value']==0):?> hide <?php endif;?> form-control-payment sel_applied_adj" value="<?=$items['adj_value']?>" style="display: inline;">
                </td>
                </tr>
                <?php endforeach;?>         
            </tbody>
            <tfoot>
              <tr>
                <td colspan="8" style="text-align: right;">Total Applied</td>
                <td></td>
                <td id="total_general"><?= print_number($_SESSION['payment_request']['total_amount'],2); ?></td>
                <td></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="card-actionbar">
        <div class="card-actionbar-row">
          <div class="pull-left">
            <button type="button" href="" onClick="addRow()" class="btn btn-primary ink-reaction">
              Add
            </button>
          </div>
          <a href="<?= site_url($module['route']); ?>" class="btn btn-flat btn-danger ink-reaction">
            Discard
          </a>
        </div>
      </div>
    </div>
    
  </div>

  <div class="section-action style-default-bright">
    <div class="section-floating-action-row">
      <button type="button" class="btn btn-floating-action btn-lg btn-danger btn-xhr-submit btn-tooltip ink-reaction" id="btn-submit-document">
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

  function addRow() {
    var row = '<tr>'+
    '<td colspan="2"><input name="desc[]" type="text" class="form-control-payment"></td>'+
    '<td><input type="hidden" value="0" name="po_item_id[]"></td>'+
    '<td><input type="hidden" value="0" name="po_id[]"></td>'+
    '<td></td>'+
    '<td></td>'+
    '<td></td>'+
    '<td></td>'+
    '<td><input name="value[]" type="number" class="form-control-payment"></td>'+
    '<td></td>'+
    '<td><input name="adj_value[]" type="number" class="hide sel_applied_adj sel_applied_adj" value="0" style="display: inline;"></td>'+
    '</tr>';
    $("#table-document").append(row);
    setAddValue();
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
  $("#loadingScreen2").attr("style", "display:none");
  $('[data-provide="datepicker"]').datepicker({
    autoclose: true,
    todayHighlight: true,
    format: 'yyyy-mm-dd'
  });

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
  var row_num = 0;
  var row = []
  var suplier = ""
  $("#add_item").click(function() {
    if (id_po.length > 0) {
      row_num += 1;
      row.push(row_num)
      var option = "<option>No Po</option>";
      $.each(id_po, function(i, item) {
        option += "<option value='" + item + "'>" + arr_po["id_" + item].document_number + "</option>"
      })
      var text = '<tr id="row_' + row_num + '">' +
        '<td><a href="" class="btn btn-icon-toggle btn-danger btn-sm btn_delete_item" data-row="' + row_num + '"><i class="fa fa-trash" ></i></a> ' + row.length + '</td>' +
        '<td><select id="sel_' + row_num + '" data-row="' + row_num + '" class="form-control sel_item">' + option + '</select></td>' +
        '<td id="sta_' + row_num + '"></td>' +
        '<td id="date_' + row_num + '"></td>' +
        '<td id="sis_' + row_num + '"></td>' +
        '<td><input id="in_' + row_num + '" data-row="' + row_num + '" type="number" class="sel_applied" value="0"></td>' +
        '</tr>';
      $("#listView").append(text);
    } else {
      toastr.options.timeOut = 10000;
      toastr.options.positionClass = 'toast-top-right';
      toastr.error("This vendor dont have PO");
    }

  });
  
  $('#default_currency').change(function() {
    currency = $(this).val();
    var supplier_view = $('#vendor');
    supplier_view.html('');    

    $.ajax({
      type: "POST",
      url: '<?= base_url() . "payment/get_supplier" ?>',
      data: {
        'currency': currency
      },
      cache: false,
      success: function(response) {
        var data = jQuery.parseJSON(response);
        supplier_view.html(data);
      }
    });

    suplier = $("#vendor").val();
    // currency = $("#currency_select").val();
    $("#total_general").html(0);
    $("#amount").val(0);
    // row_num = 0;
    $("#listView").html("");
    row = [];
    row_detail = [];
    getPo()

    var val = $(this).val();
    var url = $(this).data('source');

    $.get( url, { data: val });

  });

  $("#vendor").change(function(e) {
    suplier = $("#vendor").val();
    currency = $("#default_currency").val();
    $("#total_general").html(0);
    $("#amount").val(0);
    // row_num = 0;
    $("#listView").html("");
    row = [];
    row_detail = [];

    getPo()

    var val = $(this).val();
    var url = $(this).data('source');

    $.get( url, { data: val });

    // }
  });

  $("#tipe_select").change(function(e) {
    // if (suplier != "") {
    //   if (confirm("If you change suplier the items will be reset")) {
    //     suplier = $("#suplier_select").val()
    //     currency = $("#currency_select").val()
    //     getPo()
    //     row_num = 0;
    //     $("#listView").html("");
    //     row = []
    //   } else {
    //     $("#suplier_select").val(suplier)
    //   }
    // } else {
    // changeTotal();
    suplier = $("#suplier_select").val();
    currency = $("#currency_select").val();
    tipe = $("#tipe_select").val();
    $("#total_general").html(0);
    $("#amount").val(0);
    // row_num = 0;
    $("#listView").html("");
    row = [];
    row_detail = [];

    getPo()

    // }
  });

  var arr_po = []
  id_po = []

  function getPo() {
    $("#loadingScreen2").attr("style", "display:block");

    $.ajax({
      type: "POST",
      url: '<?= base_url() . "payment/getPo" ?>',
      data: {
        'currency': currency,
        'vendor': suplier,
        'tipe': 'credit'
      },
      cache: false,
      success: function(response) {
        $("#loadingScreen2").attr("style", "display:none");
        var data = jQuery.parseJSON(response);
        $("#listView").html(data.info);
        // console.log(data.count);
        for (i = 1; i <= data.count_po; i++) {
          row.push(i);
        }
        for (i = 1; i <= data.count_detail + data.count_po_additional; i++) {
          row_detail.push(i);
        }
        changeTotal();
      }
    });
  }

  function removeRow(selRow) {

    for (var i = 0; i < row.length; i++) {
      if (row[i] == selRow) {
        row.splice(i, 1);
      }
    }
    console.log(row)
  }

  setAddValue();

  $("#table-document").on("change", ".sel_applied_item", function() {
        id = $(this).data('id');
        sisa = parseFloat($("#sis_item_" + id).val())
        input = parseFloat($(this).val());
        po_id = $("#po_id_" + id).val()
        // alert('sisa '+sisa)
        if(po_id!=0){
          if (sisa < input) {            
              // alert('request lebih besar dari sisa ')
              var selisih = parseFloat(input)-parseFloat(sisa);
              $("#in_adj_" + id).val(selisih.toFixed(2));
              $("#in_adj_" + id).removeClass('hide');
              if($("#cb_" + id).prop('checked')===false){
                  $("#cb_" + id).prop('checked',true);
              }
          }else if (sisa > input) {            
              // alert('request lebih kecil dari sisa ')
              var selisih = parseFloat(input)-parseFloat(sisa);
              $("#in_adj_" + id).val(0);
              if($("#in_adj_" + id).hasClass('hide')===false){
                  $("#in_adj_" + id).addClass('hide');
              }
              if($("#cb_" + id).prop('checked')){
                  $("#cb_" + id).prop('checked',false);
              }
          }else{
              $("#in_adj_" + id).val(0);
              if($("#in_adj_" + id).hasClass('hide')===false){
                  $("#in_adj_" + id).addClass('hide');
              }
          }
        }
        
        setAddValue()
  });

  function setAddValue() {
    $('[name="value[]"]').change(function () {
      changeTotal();
    });
  }

  function changeTotal() {
    var sum = 0
    // $.each(row, function(i, item) {
    //   sum += parseFloat($("#in_" + item).val())
    // });
    $('[name="value[]"]').each(function (key, val) {
      var val = $(this).val();
      sum = parseFloat(sum) + parseFloat(val);
    });
    // console.log(row_detail)
    // $('.sel_applied_' + parent).each(function(key, val) {
    //   var val = $(this).val();
    //   sum = parseFloat(sum) + parseFloat(val);
    // });
    $("#total_general").html(sum);
    $("#amount").val(sum);
  }

  $("#amount").change(function() {
    if ($(this).val() === "") {
      $(this).val("0")
    }
    if (parseFloat($(this).val()) < 0) {
      $(this).val("0")
    }
  })

  $("#btn-submit-document_2").click(function(e) {
    e.preventDefault();
    $("#btn-submit-document").attr('disabled', true);
    if ($("#suplier_select").val() === "" || $("#date").val() === "" || $("#amount").val() === 0) {
      
      $("#btn-submit-document").attr('disabled', false);
      toastr.options.timeOut = 10000;
      toastr.options.positionClass = 'toast-top-right';
      toastr.error("All field must be fill");
      return
    }
    if (parseFloat($("#amount").val()) != parseFloat($("#total_general").html())) {
      $("#btn-submit-document").attr('disabled', false);
      toastr.options.timeOut = 10000;
      toastr.options.positionClass = 'toast-top-right';
      toastr.error("Check value and item value not match");
      return
    }
    var postData = []
    // $.each(row, function(i, item) {
    //   if (parseFloat($("#in_" + item).val()) === 0) {

    //     toastr.options.timeOut = 10000;
    //     toastr.options.positionClass = 'toast-top-right';
    //     toastr.error("All field must be fill");
    //     return
    //   }
    //   var data = {}
    //   data["document_number"] = $("#sel_" + item).val()
    //   data["value"] = parseInt($("#in_" + item).val())
    //   postData.push(data);
    // });
    $.each(row, function(i, po) {
      $.each(row_detail, function(i, item) {
        var value = parseFloat($("#in_item_" + po + "_" + item).val());
        if(value!=0 && value!=''){
          var data = {}
          data["document_number"] = $("#sel_item_" + po + "_" + item).val();
          data["id_po"] = $("#sel_item_2_" + po + "_" + item).val();
          data["desc"] = $("#desc_item_" + po + "_" + item).val();
          data["value"] = parseFloat($("#in_item_" + po + "_" + item).val());
          data["adj"] = parseFloat($("#in_adj_" + po + "_" + item).val());
          postData.push(data);
        }
        
      });
    });
    if(postData.length==0){
      toastr.options.timeOut = 10000;
      toastr.options.positionClass = 'toast-top-right';
      toastr.error("Check value and item value not match");
      return
    }
    $("#loadingScreen2").attr("style", "display:block");
    $.ajax({
      type: "POST",
      url: '<?= base_url() . "payment/save_2" ?>',
      data: {
        // 'account'       : $("#account_select").val(),
        // "vendor"        : $("#suplier_select").val(),
        // "currency"      : $("#currency_select").val(),
        // "tipe"          : $("#tipe_select").val(),
        // "no_cheque"     : $("#no_cheque").val(),
        // "date"          : $("#date").val(),
        // "purposed_date" : $("#purposed_date").val(),
        // "amount"        : $("#amount").val(),
        // "notes"         : $("#notes").val(),
        "item"          : postData
      },
      cache: false,
      success: function(response) {
        $("#loadingScreen2").attr("style", "display:none");
        var data = jQuery.parseJSON(response);
        if (data.status == "success") {
          clearForm()
          toastr.options.timeOut = 4500;
          toastr.options.progressBar = true;
          toastr.options.positionClass = 'toast-top-right';
          toastr.success("Your data has been saved");
          window.setTimeout(function() {
            window.location.href = '<?= site_url($module['route']); ?>';
          }, 5000);

        } else {
          $("#btn-submit-document").attr('disabled', false);
          toastr.options.timeOut = 10000;
          toastr.options.positionClass = 'toast-top-right';
          toastr.error("Failed to save data");
        }
      },
      error: function(xhr, ajaxOptions, thrownError) {
        $("#loadingScreen2").attr("style", "display:none");
        $("#btn-submit-document").attr('disabled', false);
        console.log(xhr.status);
        console.log(xhr.responseText);
        console.log(thrownError);
      }
    });

  })

  $(document).on('click', '.btn-xhr-submit', function(e) {
      e.preventDefault();

      var button = $('.btn-xhr-submit');
      var form = $('#form_purposed_payment');
      var action = form.attr('action');

      $(button).addClass('hide');

      if (form.valid()) {
        $.post(action, form.serialize()).done(function(data) {
          var obj = $.parseJSON(data);

          if ( obj.success == false ){
            $(button).removeClass('hide');
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
        });
      }
  });

  function clearForm() {
    console.log(123)
    $("input[type=text]").val("");
    $("input[type=number]").val("0");
    $("select").val($("select option:first").val());
    row = []
    row_num = 0;
    arr_po = []
    id_po = []
    suplier = ""
    $("#listView").html("");
    $("#total_general").html("0");
  }

  $("listView").on("change", ".sel_applied", function() {
    if ($(this).val() === "") {
      $(this).val("0")
    }
    if (parseFloat($(this).val()) < 0) {
      $(this).val("0")
    }
  })

  var autosetInputData          = $('[data-input-type="autoset"]');
  $( autosetInputData ).on('change', function(){
    var val = $(this).val();
    var url = $(this).data('source');

    $.get( url, { data: val });
  });
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
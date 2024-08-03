<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="has-actions style-default">
    <div class="section-body">

        <?= form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form_approval')); ?>

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
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group">
                                <div class="input-group-content">
                                    <input type="text" name="pr_number" maxlength="6" id="pr_number" class="form-control" value="<?= $_SESSION['bayar']['payment_number']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_doc_number'); ?>">
                                    <input type="hidden" name="format_number" id="format_number_input" class="form-control" value="<?= $_SESSION['bayar']['format_number']; ?>">
                                    <label for="pr_number">Payment Voucher No.</label>
                                </div>
                                <span class="input-group-addon" id="format_number"><?=$_SESSION['bayar']['format_number'];?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <input readonly value="<?= $_SESSION['bayar']['document_number'] ?>" type="text" name="no_transaksi" id="no_transaksi" class="form-control">
                                <input value="<?= $_SESSION['bayar']['po_payment_id'] ?>" type="hidden" name="po_payment_id" id="po_payment_id" class="form-control">

                                <label for="suplier_select">Advance Number</label>
                            </div>
                            <div class="form-group">
                                <input type="text" name="date" id="date" class="form-control" value="<?= $_SESSION['bayar']['purposed_date'] ?>" disabled>
                                <label for="date">Purposed Date</label>
                            </div>
                            <div class="form-group">
                                <input type="text" name="date" id="date" class="form-control" value="<?= date('Y-m-d') ?>">
                                <label for="date">Payment Date</label>
                            </div>
                            
                            <div class="form-group hide">
                                <select id="tipe_select" class="form-control">
                                    <option value="OPEN">OPEN</option>
                                    <option value="ORDER">ORDER</option>
                                </select>
                                <label for="suplier_select">Tipe</label>
                            </div>
                            <div class="form-group">
                                <input value="<?= $_SESSION['bayar']['vendor'] ?>" type="text" name="suplier_select" id="suplier_select" class="form-control">
                                <label for="suplier_select">Pay To</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <input readonly value="<?= $_SESSION['bayar']['currency'] ?>" type="text" name="currency_select" id="currency_select" class="form-control">
                                <label for="currency">Currency</label>
                            </div>

                            <div class="form-group <?= (config_item('auth_role') == 'PIC STAFF') ? 'hide' : ''; ?>">
                                <select name="type" id="type" class="form-control" data-source="<?= site_url($module['route'] . '/set_type_transaction/'); ?>" required>
                                    <option value="CASH" <?= ('CASH' == $_SESSION['bayar']['type']) ? 'selected' : ''; ?>>Cash</option>
                                    <option value="BANK" <?= ('BANK' == $_SESSION['bayar']['type']) ? 'selected' : ''; ?>>Bank Transfer</option>
                                </select>
                                <label for="vendor">Transaction Type</label>
                            </div>
                            
                            <div class="form-group">
                                <select id="account_select" class="form-control" name="account" required>
                                    <option value="">No Account</option>
                                    <option value="">-- SELECT Account</option>
                                    <?php foreach (getAccount($_SESSION['bayar']['type']) as $key => $account) : ?>
                                    <option value="<?= $account['coa']; ?>" <?= ($account['coa'] == $_SESSION['bayar']['coa_kredit']) ? 'selected' : ''; ?>>
                                    (<?= $account['coa']; ?>) <?= $account['group']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="account_select">Account</label>
                            </div>
                            <div class="form-group">
                                <input type="text" name="no_cheque" id="no_cheque" class="form-control" value="" required>
                                <label for="no_cheque">No Cheque</label>
                            </div>                            
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="text" name="no_konfirmasi" id="no_konfirmasi" class="form-control" value="">
                                <label for="amount">No Konfirmasi</label>
                            </div>
                            <div class="form-group">
                                <select name="paid_base" id="paid_base" class="form-control" required>
                                <?php foreach (available_warehouses() as $w => $warehouse) : ?>
                                    <option value="<?= $warehouse; ?>" <?= ($_SESSION['bayar']['base'] == $warehouse) ? 'selected' : ''; ?>>
                                    <?= $warehouse; ?>
                                    </option>
                                <?php endforeach; ?>
                                </select>
                                <label for="warehouse">Warehouse</label>
                            </div>
                            <div class="form-group">
                                <input type="number" name="amount" id="amount" class="form-control" value="<?= $_SESSION['bayar']['total_amount'] ?>">
                                <label for="amount">Amount</label>
                            </div>
                        </div>
                        <!-- <button class="btn btn-danger" id="add_item" type="button">Add Item</button> -->
                    </div>
                </div>

                <?php if (isset($_SESSION['bayar']['request'])) : ?>
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
                                <?php foreach ($_SESSION['bayar']['request'] as $i => $request) : ?>
                                    <?php $n++; ?>
                                <tr>
                                    <td class="no-space">
                                        <?= print_number($n); ?>
                                    </td>
                                    <td>
                                        <a class="link" href="<?= site_url('business_trip_request/print_pdf/' . $request['document_id']) ?>" target="_blank"><?=print_string($request['spd_number'])?></a>
                                    </td>                  
                                    
                                    <td>
                                        <?= print_date($request['spd_date']); ?>
                                    </td>
                                    <td>
                                        <?= print_string($request['spd_person_incharge']); ?>
                                    </td>
                                    <td>
                                        <?= print_string($request['remarks']); ?>
                                    </td>
                                    <td>
                                        <?= print_number($request['spd_amount'],2); ?>
                                    </td>
                                    <td>
                                        <?= print_number($request['amount_paid'],2); ?>
                                        <?php $amount_paid[] = $request['amount_paid']; ?>
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
        <?= form_close(); ?>
    </div>

    <div class="section-action style-default-bright">
        <div class="section-floating-action-row">
            <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="">
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
    $("#loadingScreen2").attr("style", "display:none");
    $('#date').datepicker({
        autoclose: true,
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
    

    $("#btn-submit-document").click(function(e) {
        e.preventDefault()
        $("#btn-submit-document").attr('disabled', true);
        if ($("#account_select").val() === "" || $("#suplier_select").val() === "" || $("#date").val() === "" || $("#amount").val() === 0) {
            $("#btn-submit-document").attr('disabled', false);
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error("All field must be fill");
            return
        }
        var postData = []
        $("#loadingScreen2").attr("style", "display:block");
        $.ajax({
            type: "POST",
            url: '<?= base_url() . "spd_payment/save_pembayaran" ?>',
            data: {
                'account': $("#account_select").val(),
                "vendor": $("#suplier_select").val(),
                "no_transaksi": $("#no_transaksi").val(),
                "currency": $("#currency_select").val(),
                "tipe": $("#tipe_select").val(),
                "no_cheque": $("#no_cheque").val(),
                "date": $("#date").val(),
                "amount": $("#amount").val(),
                "po_payment_id": $("#po_payment_id").val(),
                "no_konfirmasi": $("#no_konfirmasi").val(),
                "paid_base": $("#paid_base").val(),
                "payment_number": $("#pr_number").val(),
                "payment_format_number": $("#format_number_input").val(),
                // "item": postData
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

    //klik icon mata utk lihat item po
    $("#table-document").on("click", ".btn_view_detail", function() {
        console.log('klik detail');
        var selRow = $(this).data("row");
        var tipe = $(this).data("tipe");
        if (tipe == "view") {
            $(this).data("tipe", "hide");
            $('.detail_' + selRow).removeClass('hide');
        } else {
            $(this).data("tipe", "view");
            $('.detail_' + selRow).addClass('hide');
        }
    })

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

    $('#type').change(function() {
        type_trs = $(this).val();
        var account_view = $('#account_select');
        var format_number_view = $('#format_number');
        var format_number_input = $('#format_number_input');
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
            format_number_input.val(data.format_number);
            $('#pr_number').val(data.document_number).trigger('change');
          }
        });

        var val = $(this).val();
        var url = $(this).data('source');

        $.get( url, { data: val });

    });
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
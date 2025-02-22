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
                                <input readonly value="<?= $_SESSION['payment']['no_transaksi'] ?>" type="text" name="no_transaksi" id="no_transaksi" class="form-control">
                                <input value="<?= $_SESSION['payment']['po_payment_id'] ?>" type="hidden" name="po_payment_id" id="po_payment_id" class="form-control">

                                <label for="suplier_select">Purpose Number</label>
                            </div>

                            <div class="form-group">
                                <input type="text" name="date" id="date" class="form-control" value="<?= $_SESSION['payment']['purposed_date'] ?>" disabled>
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
                                <input readonly value="<?= $_SESSION['payment']['no_transaksi'] ?>" type="hidden" name="no_transaksi" id="no_transaksi" class="form-control">
                                <input readonly value="<?= $_SESSION['payment']['vendor'] ?>" type="text" name="suplier_select" id="suplier_select" class="form-control">
                                <label for="suplier_select">Suplier</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <input readonly value="<?= $_SESSION['payment']['currency'] ?>" type="text" name="currency_select" id="currency_select" class="form-control">
                                <label for="currency">Currency</label>
                            </div>
                            <div class="form-group">
                                <select id="account_select" class="form-control" required>
                                    <option value="">No Account</option>
                                    <option value="">-- SELECT Account</option>
                                    <?php foreach (getAccount($_SESSION['payment']['type']) as $key => $account) : ?>
                                    <option value="<?= $account['coa']; ?>" <?= ($account['coa'] == $_SESSION['payment']['coa_kredit']) ? 'selected' : ''; ?>>
                                    <?= $account['group']; ?>
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
                                    <option value="<?= $warehouse; ?>" <?= ($_SESSION['payment']['base'] == $warehouse) ? 'selected' : ''; ?>>
                                    <?= $warehouse; ?>
                                    </option>
                                <?php endforeach; ?>
                                </select>
                                <label for="warehouse">Warehouse</label>
                            </div>
                            <div class="form-group">
                                <input type="number" name="amount" id="amount" class="form-control" value="<?= $_SESSION['payment']['total_amount'] ?>" readonly="readonly">
                                <label for="amount">Amount</label>
                            </div>
                        </div>
                        <!-- <button class="btn btn-danger" id="add_item" type="button">Add Item</button> -->
                    </div>
                </div>

                <?php if (isset($_SESSION['payment']['po'])) : ?>
                    <div class="document-data table-responsive">
                        <table class="table table-hover" id="table-document">
                            <thead>
                                <tr>
                                    <!-- <th class="middle-alignment"></th> -->
                                    <th class="middle-alignment"></th>
                                    <th class="middle-alignment">PO#</th>
                                    <th class="middle-alignment">Description</th>
                                    <th class="middle-alignment text-center">Qty Paid</th>
                                    <th class="middle-alignment text-center">Amount Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['payment']['po'] as $i => $po) : ?>
                                    <tr>
                                        <td></td>
                                        <td style="font-weight: bolder;" class="middle-alignment">
                                            <a  href="javascript:;" title="View Detail PO" class="btn btn-icon-toggle btn-info btn-xs btn_view_detail" id="btn_<? $n ?>" data-row="<?= $i ?>" data-tipe="view"><i class="fa fa-angle-right"></i>
                                            </a>
                                            <?= $po['document_number']; ?>
                                        </td>
                                        <td style="font-weight: bolder;" class="middle-alignment">
                                            
                                        </td>
                                        <td style="font-weight: bolder;" class="text-center">
                                            <?= number_format($po['quantity_paid'], 2); ?>
                                        </td>
                                        <td style="font-weight: bolder;" class="text-center">
                                            <?= number_format($po['amount_paid'], 2); ?>
                                        </td>
                                    </tr>
                                <?php foreach ($po['items'] as $j => $item) : ?>
                                    <tr class="detail_<?= $i; ?> hide">
                                        <td></td>
                                        <td class="text-center" colspan="2">
                                            <?= $item['description']; ?>
                                        </td>
                                        <td class="text-center">
                                            <?= number_format($item['quantity_paid'], 2); ?>
                                        </td>
                                        <td class="text-center">
                                            <?= number_format($item['amount_paid'], 2); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th style="text-align: center;">Total</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: center;"><?= print_number($_SESSION['payment']['total_amount'],2)?></th>
                                </tr>
                            </tfoot>
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
    // $('#currency_select').change(function() {
    //     currency = $(this).val();

    //     var akun_view = $('#account_select');
    //     var supplier_view = $('#suplier_select');
    //     akun_view.html('');
    //     supplier_view.html('');
    //     $.ajax({
    //         type: "POST",
    //         url: '<?= base_url() . "payment/get_akun" ?>',
    //         data: {
    //             'currency': currency
    //         },
    //         cache: false,
    //         success: function(response) {
    //             var data = jQuery.parseJSON(response);
    //             akun_view.html(data);
    //         }
    //     });

    //     $.ajax({
    //         type: "POST",
    //         url: '<?= base_url() . "payment/get_supplier" ?>',
    //         data: {
    //             'currency': currency
    //         },
    //         cache: false,
    //         success: function(response) {
    //             var data = jQuery.parseJSON(response);
    //             supplier_view.html(data);
    //         }
    //     });

    //     suplier = $("#suplier_select").val();
    //     // currency = $("#currency_select").val();
    //     tipe = $("#tipe_select").val();
    //     $("#total_general").html(0);
    //     $("#amount").val(0);
    //     // row_num = 0;
    //     $("#listView").html("");
    //     row = [];
    //     row_detail = [];

    //     getPo()

    // });

    $("#suplier_select").change(function(e) {
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
        // $.ajax({
        //   type: "POST",
        //   url: '<?= base_url() . "payment/getPo" ?>',
        //   data: {
        //     'vendor': suplier,
        //     'currency': currency
        //   },
        //   cache: false,
        //   success: function(response) {
        //     $("#loadingScreen2").attr("style", "display:none");
        //     var data = jQuery.parseJSON(response);
        //     arr_po = []
        //     id_po = []
        //     $.each(data, function(i, item) {
        //       arr_po["id_" + item.id] = item;
        //       id_po.push(item.id)
        //     });
        //   },
        //   error: function(xhr, ajaxOptions, thrownError) {
        //     $("#loadingScreen2").attr("style", "display:none");
        //     console.log(xhr.status);
        //     console.log(xhr.responseText);
        //     console.log(thrownError);
        //   }
        // });

        $.ajax({
            type: "POST",
            url: '<?= base_url() . "payment/getPo" ?>',
            data: {
                'currency': currency,
                'vendor': suplier,
                'tipe': tipe
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
                for (i = 1; i <= data.count_detail; i++) {
                    row_detail.push(i);
                }
            }
        });
    }

    $("#listView").on("change", ".sel_item", function() {
        var selData = arr_po["id_" + $(this).val()]
        var selRow = $(this).data("row");
        $("#sta_" + selRow).html(selData.status);
        $("#date_" + selRow).html(selData.document_date);
        $("#sis_" + selRow).html(selData.remaining_payment);
    })

    $("#listView").on("click", ".btn_delete_item", function(e) {
        e.preventDefault();
        var selRow = $(this).data("row");
        console.log(selRow)
        $("#row_" + selRow).remove();
        removeRow(selRow)
    })

    function removeRow(selRow) {

        for (var i = 0; i < row.length; i++) {
            if (row[i] == selRow) {
                row.splice(i, 1);
            }
        }
        console.log(row)
    }

    $("#listView").on("change", ".sel_applied", function() {
        // console.log('test');
        var selRow = $(this).data("row");
        sisa = parseFloat($("#sis_" + selRow).val())
        input = parseFloat($(this).val())
        if (input < sisa) {
            $('.detail_' + selRow).removeClass('hide');
            $.each(row_detail, function(i, po) {
                sisa_item = parseFloat($("#sis_item_" + selRow + "_" + po).val())
                $("#in_item_" + selRow + "_" + po).val(0)
                $("#in_" + selRow).attr('readonly', true);
            });
        } else {
            $.each(row_detail, function(i, po) {
                sisa_item = parseFloat($("#sis_item_" + selRow + "_" + po).val())
                $("#in_item_" + selRow + "_" + po).val(sisa_item)
            });
            $('.detail_' + selRow).removeClass('hide');
        }
        changeTotal();

    })

    $("#listView").on("click", ".btn_view_detail", function() {
        console.log('klik detail');
        var selRow = $(this).data("row");
        var tipe = $(this).data("tipe");
        if (tipe == "view") {
            $(this).data("tipe", "hide");
            $('.detail_' + selRow).removeClass('hide');
            $("#in_" + selRow).attr('readonly', true);
        } else {
            $(this).data("tipe", "view");
            $('.detail_' + selRow).addClass('hide');
            $("#in_" + selRow).attr('readonly', false);
        }
    })

    $("#listView").on("change", ".sel_applied_item", function() {
        // console.log('test');
        var selRow = $(this).data("row");
        var parent = $(this).data("parent");
        var parent_total = $("#in_" + parent).val();
        sisa = parseFloat($("#sis_" + selRow).val())
        input = parseFloat($(this).val())
        var sum = 0;
        $('.sel_applied_' + parent).each(function(key, val) {
            var val = $(this).val();
            sum = parseFloat(sum) + parseFloat(val);
        });
        $("#in_" + parent).val(sum)
        changeTotal();

    })

    $("#listView").on("keyup", ".sel_applied", function() {
        var selRow = $(this).data("row");
        sisa = parseFloat($("#sis_" + selRow).val())
        input = parseFloat($(this).val())
        if (sisa < input) {
            console.log('lebih');
            var text = $(this).val();
            $(this).val(sisa);
        }

    })

    $("#listView").on("keyup", ".sel_applied_item", function() {
        var selRow = $(this).data("row");
        var parent = $(this).data("parent");
        var parent_total = $("#in" + parent).val()
        sisa = parseFloat($("#sis_item_" + parent + "_" + selRow).val())
        input = parseFloat($(this).val())
        if (sisa < input) {
            console.log('lebih');
            var text = $(this).val();
            $(this).val(sisa);
            input = sisa;
        }
        $("#in" + parent).val(parent_total + input);
    })

    $("#listView").on("keydown", ".sel_applied", function(e) {
        var selRow = $(this).data("row");
        if ($("#sis_" + selRow).val() === "") {
            // toastr.options.timeOut = 10000;
            // toastr.options.positionClass = 'toast-top-right';
            // toastr.error("There's no PO");
            // e.preventDefault()
            $("#sis_" + selRow).val(0);
        }

    })

    function changeTotal() {
        var sum = 0
        $.each(row, function(i, item) {
            sum += parseFloat($("#in_" + item).val())
        });
        console.log(row_detail)
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
        if (parseInt($(this).val()) < 0) {
            $(this).val("0")
        }
    })
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
        // if (parseInt($("#amount").val()) != parseInt($("#total_general").html())) {
        //     toastr.options.timeOut = 10000;
        //     toastr.options.positionClass = 'toast-top-right';
        //     toastr.error("Check value and item value not match");
        //     return
        // }
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
        // $.each(row, function(i, po) {
        //     $.each(row_detail, function(i, item) {
        //         var data = {}
        //         data["document_number"] = $("#sel_item_" + po + "_" + item).val()
        //         data["value"] = parseInt($("#in_item_" + po + "_" + item).val())
        //         postData.push(data);
        //     });
        // });
        $("#loadingScreen2").attr("style", "display:block");
        $.ajax({
            type: "POST",
            url: '<?= base_url() . "payment/save_pembayaran" ?>',
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
        if (parseInt($(this).val()) < 0) {
            $(this).val("0")
        }
    })
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
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
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-9">
                                    <div class="row">
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <input class="form-control input-sm filter_daterange" data-column="2" id="date">
                                                <label for="currency">Date</label>
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <select id="suplier_select" class="form-control">
                                                    <option value="all">All Suplier</option>
                                                    <?php foreach ($suplier as $key) {
                                                        ?>
                                                        <option value="<?= $key->id ?>"><?= $key->id ?> - <?= $key->vendor ?></option>
                                                    <?php
                                                    } ?>
                                                </select>
                                                <label for="currency">Supplier</label>
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="form-group">
                                                <select id="currency_select" class="form-control">
                                                    <option value="all">All Currency</option>
                                                    <option value="IDR">IDR</option>
                                                    <option value="USD">USD</option>
                                                </select>
                                                <label for="currency">Currency</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-3">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-sm btn-danger btn-block btn-print-report" data-tipe="print">Print</button>
                                            </div>
                                        </div>
                                        <div class="col-xs-3">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-sm btn-info btn-block btn-print-report" data-tipe="excel">Excel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class=" document-data table-responsive" id="view_data">
                                    <table class=" table table-hover" id="table-document" width="100%">
                                        <thead>
                                            <tr>
                                                <th width="15%" class="middle-alignment" style="text-align:center">Cheque #</th>
                                                <th width="15%" class="middle-alignment" style="text-align:center">Chq Date</th>
                                                <th width="15%" class="middle-alignment" style="text-align:center">PO #</th>
                                                <th width="15%" class="middle-alignment" style="text-align:center">Date</th>
                                                <th width="15%" class="middle-alignment" style="text-align:center">Purchase Total Amount</th>
                                                <th width="15%" class="middle-alignment" style="text-align:center">Amount Applied</th>
                                            </tr>
                                        </thead>
                                        <tbody id="listView">

                                        </tbody>

                                    </table>
                                </div>
                            </div>


                        </div>
                    </div>
                    <?= form_close(); ?>
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

    $('.filter_daterange').daterangepicker({
        autoUpdateInput: false,
        parentEl: '#offcanvas-datatable-filter',
        locale: {
            cancelLabel: 'Clear'
        }
    }).on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + '.' + picker.endDate.format('YYYY-MM-DD'));
        // $(".btn-print-report").attr('disabled', false);
        suplier = $("#suplier_select").val();
        currency = $("#currency_select").val();
        date = $("#date").val();
        $("#total_general").html(0);
        $("#amount").val(0);
        // row_num = 0;
        $("#listView").html("");
        row = [];
        row_detail = [];

        getPo()
    }).on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        // $(".btn-print-report").attr('disabled', true);
        suplier = $("#suplier_select").val();
        currency = $("#currency_select").val();
        date = $("#date").val();
        $("#total_general").html(0);
        $("#amount").val(0);
        // row_num = 0;
        $("#listView").html("");
        row = [];
        row_detail = [];

        getPo()
    });

    $('#currency_select').change(function() {
        currency = $(this).val();

        // var akun_view = $('#account_select');
        // var supplier_view = $('#suplier_select');
        // akun_view.html('');
        // supplier_view.html('');
        // $.ajax({
        //     type: "POST",
        //     url: '<?= base_url() . "payment/get_akun" ?>',
        //     data: {
        //         'currency': currency
        //     },
        //     cache: false,
        //     success: function(response) {
        //         var data = jQuery.parseJSON(response);
        //         akun_view.html(data);
        //     }
        // });

        // $.ajax({
        //     type: "POST",
        //     url: '<?= base_url() . "payment/get_supplier" ?>',
        //     data: {
        //         'currency': currency
        //     },
        //     cache: false,
        //     success: function(response) {
        //         var data = jQuery.parseJSON(response);
        //         supplier_view.html(data);
        //     }
        // });

        suplier = $("#suplier_select").val();
        // currency = $("#currency_select").val();
        date = $("#date").val();
        $("#total_general").html(0);
        $("#amount").val(0);
        // row_num = 0;
        $("#listView").html("");
        row = [];
        row_detail = [];

        getPo()

    });

    $("#suplier_select").change(function(e) {

        suplier = $("#suplier_select").val();
        currency = $("#currency_select").val();
        date = $("#date").val();
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

    $(".btn-print-report").on('click', function(e) {
        suplier = $("#suplier_select").val();
        currency = $("#currency_select").val();
        date = $("#date").val();
        tipe = $(this).data('tipe');

        get_po_for_print()
    });

    var arr_po = []
    id_po = []

    function getPo() {
        $("#loadingScreen2").attr("style", "display:block");


        $.ajax({
            type: "POST",
            url: '<?= base_url() . "supplier_payment_history/getPo" ?>',
            data: {
                'currency': currency,
                'vendor': suplier,
                'date': date
            },
            cache: false,
            success: function(response) {
                $("#loadingScreen2").attr("style", "display:none");
                var data = jQuery.parseJSON(response);
                $("#listView").html(data.info);
                // console.log(data.count);
                // for (i = 1; i <= data.count_po; i++) {
                //     row.push(i);
                // }
                // for (i = 1; i <= data.count_detail; i++) {
                //     row_detail.push(i);
                // }
            }
        });
    }

    function get_po_for_print() {
        // $("#loadingScreen2").attr("style", "display:block");


        // $.ajax({
        //     type: "POST",
        //     url: '<?= base_url() . "purchase_supplier_summary/get_po_for_print" ?>',
        //     data: {
        //         'currency': currency,
        //         'vendor': suplier,
        //         'date': date,
        //         'tipe': tipe
        //     },
        //     cache: false,
        //     success: function(response) {
        //         // $("#loadingScreen2").attr("style", "display:none");
        //         var data = jQuery.parseJSON(response);
        //         window.open(data.info);
        //     }
        // });

        var data = {
            'currency': currency,
            'vendor': suplier,
            'date': date,
            'tipe': tipe

        };

        var urlPrint = '<?= base_url() ?>' + 'supplier_payment_history/get_po_for_print/' + tipe + '/' + currency + '/' + suplier + '/' + date;
        window.open(urlPrint);

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
        if ($("#account_select").val() === "" || $("#suplier_select").val() === "" || $("#no_cheque").val() === "" || $("#date").val() === "" || $("#amount").val() === 0) {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error("All field must be fill");
            return
        }
        if (parseInt($("#amount").val()) != parseInt($("#total_general").html())) {
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
                var data = {}
                data["document_number"] = $("#sel_item_" + po + "_" + item).val()
                data["value"] = parseInt($("#in_item_" + po + "_" + item).val())
                postData.push(data);
            });
        });
        $("#loadingScreen2").attr("style", "display:block");
        $.ajax({
            type: "POST",
            url: '<?= base_url() . "payment/save" ?>',
            data: {
                'account': $("#account_select").val(),
                "vendor": $("#suplier_select").val(),
                "currency": $("#currency_select").val(),
                "tipe": $("#tipe_select").val(),
                "no_cheque": $("#no_cheque").val(),
                "date": $("#date").val(),
                "amount": $("#amount").val(),
                "item": postData
            },
            cache: false,
            success: function(response) {
                $("#loadingScreen2").attr("style", "display:none");
                var data = jQuery.parseJSON(response);
                if (data.status == "success") {
                    clearForm()
                    toastr.options.timeOut = 10000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.success("Your data has been saved");

                } else {
                    toastr.options.timeOut = 10000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.error("Failed to save data");
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                $("#loadingScreen2").attr("style", "display:none");
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
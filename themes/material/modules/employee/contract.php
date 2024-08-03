<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="has-actions style-default">
    <div class="section-body">
        <div class="row">
            <div class="col-md-4">
                <?php $this->load->view('material/modules/employee/sidemenu') ?>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <?php startblock('page_head') ?>
                    <div class="card-head style-primary-dark">
                        <header>Employee's Contract</header>

                        <div class="tools">
                            <form class="navbar-search hidden" role="search" id="datatable-form">
                                <div class="form-group">
                                    <input type="text" class="form-control input-sm" id="datatable-search-box" placeholder="item, P/N, S/N">
                                </div>

                                <button type="submit" id="navbar-search-button" class="btn btn-icon-toggle btn-tooltip ink-reaction">
                                    <i class="md md-search"></i>
                                    <small class="bottom center">Search data</small>
                                </button>
                            </form>

                            <button class="btn btn-icon-toggle btn-tooltip ink-reaction" data-toggle="reload">
                                <i class="md md-refresh"></i>
                                <small class="bottom center">Reload table</small>
                            </button>

                            <a class="btn btn-icon-toggle btn-tooltip ink-reaction" data-toggle="offcanvas" href="#offcanvas-datatable-filter">
                                <i class="md md-more-horiz"></i>
                                <small class="bottom center">Data options</small>
                            </a>
                            </div>
                    </div>
                    <?php endblock() ?>

                    <?php
                        if ( $this->session->flashdata('alert') )
                            render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
                    ?>   
                    <div class="card-body no-padding">
                        <table data-provide="datatable">
                            <thead>
                            <tr>
                                <?php foreach ($grid['column'] as $column):?>
                                <th><?=$column;?></th>
                                <?php endforeach;?>
                            </tr>
                            </thead>

                            <?php if ($grid['summary_columns'] !== NULL):?>
                            <tfoot>
                                <tr>
                                <th></th>
                                <th>Total</th>

                                <?php for ($i = 2; $i < count($grid['column']); $i++):?>
                                    <th></th>
                                <?php endfor;?>
                                </tr>
                            </tfoot>
                            <?php endif;?>
                        </table>
                    </div>                 
                </div>
            </div>
        </div>
    </div>

    <div class="section-action style-default-bright">
        <div class="section-action-row">
            <div class="btn-toolbar">
                <div id="core-buttons" class="pull-left btn-group">
                    <button class="btn btn-icon-toggle btn-lg ink-reaction btn-back" data-toggle="back">
                        <i class="md md-arrow-back"></i>
                    </button>

                    <button class="btn btn-icon-toggle btn-lg ink-reaction btn-home" data-toggle="redirect" data-url="<?= site_url(); ?>">
                        <i class="md md-home"></i>
                    </button>
                </div>

                <!-- ACTIONS LEFT -->
                <?php emptyblock('actions_left') ?>
            </div>
        </div>

    <!-- ACTIONS RIGHT -->
    <?php if (is_granted($module, 'create')):?>
        <div class="section-floating-action-row">
            <button class="btn btn-floating-action btn-lg btn-danger ink-reaction" id="btn-create-data" onclick="$(this).popup()" data-source="<?=site_url($module['route'] .'/create_contract/'.$entity['employee_number'])?>" data-target="#data-modal">
                <i class="md md-add"></i>
            </button>
        </div>
    <?php endif ?>

    </div>

</section>
<div id="data-modal" class="modal fade-scale" role="dialog" aria-labelledby="data-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-fs" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>

                <h4 class="modal-title" id="data-modal-label"><?= strtoupper($module['parent']); ?></h4>
            </div>

            <div class="modal-body no-padding"></div>

            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<?php endblock() ?>

<?php startblock('offcanvas_left') ?>
<div id="offcanvas-datatable-filter" class="offcanvas-pane" style="width: 600px">
    <div class="offcanvas-head style-primary-dark">
        <header>Data Filter</header>
        <div class="offcanvas-tools">
        <a class="btn btn-icon-toggle pull-right" data-dismiss="offcanvas">
            <i class="md md-close"></i>
        </a>
        </div>
    </div>
    <div class="offcanvas-body no-padding">
        <ul class="list ">
            <div class="form force-padding">
                <div class="form-group">
                    <label for="filter_warehouse">Employee Number</label>
                    <input class="form-control input-sm filter_numeric_text" data-column="1" id="filter_employee_number" value="<?=$entity['employee_number'];?>" readonly>
                </div>
            </div>
        </ul>
    </div>
</div>
<div id="offcanvas-import" class="offcanvas-pane width-8">
    
</div>
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
<?= html_script('vendors/select2-4.0.3/dist/js/select2.min.js') ?>
<?= html_script('vendors/DataTables-1.10.12/datatables.min.js') ?>
<?=html_script('themes/script/jquery.number.js') ?>
<script>
    Pace.on('start', function() {
        $('.progress-overlay').show();
    });

    Pace.on('done', function() {
        $('.progress-overlay').hide();
    });

    var id_purchase_order = "";

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

        $('input[type=radio][name=request_to]').change(function() {
            var val = $(this).val();
            var url = $(this).data('source');
            console.log(val);
            $.get(url, {
                data: val
            }, function(data) {
                var result = jQuery.parseJSON(data);
                if (result.status == "success") {
                    window.location.reload();
                }
            });
        });

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

            var datatableElement = $('[data-provide="datatable"]');
            var datatableOptions = new Object();

            datatableOptions.selectedRows = [];
            datatableOptions.selectedIds = [];
            datatableOptions.clickDelay = 700;
            datatableOptions.clickCount = 0;
            datatableOptions.clickTimer = null;
            datatableOptions.summaryColumns = <?= json_encode($grid['summary_columns']); ?>;

            $(datatableElement)
                .addClass('stripe row-border cell-border order-column nowrap')
                .attr('width', '100%');

            $(datatableElement).find('thead tr:first-child th:first-child').attr('width', 1).text('No.');
            $(datatableElement).find('table td:first-child').attr('align', 'right');

            $.fn.dataTable.ext.errMode = 'throw';

            var datatable = $(datatableElement).DataTable({
                searchDelay: 350,
                scrollY: 410,
                scrollX: true,
                scrollCollapse: true,
                lengthMenu: [
                [10, 50, 100, -1],
                [10, 50, 100, "All"]
                ],
                <?php if (config_item('as_head_department')=='yes') {?>
                pageLength: -1,
                <?php }else {?>
                pageLength: 10,
                <?php }?>
                pagingType: 'full',

                order: <?= json_encode($grid['order_columns']); ?>,
                fixedColumns: {
                leftColumns: <?= $grid['fixed_columns']; ?>
                },

                language: {
                info: "Total _TOTAL_ entries"
                },

                processing: true,
                serverSide: true,
                ajax: {
                url: "<?= $grid['data_source']; ?>",
                type: "POST",
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.responseText);
                    if (xhr.status == 404) {
                    toastr.clear();
                    toastr.error('Request page not found. Please contact Technical Support.', 'Loading data failed!');
                    alert("page not found");
                    } else {
                    toastr.clear();
                    toastr.error(textStatus + ': ' + errorThrown + '. Report this error!', 'Loading data failed!');
                    }
                }
                },

                rowCallback: function(row, data) {
                if ($.inArray(data.DT_RowId, datatableOptions.selectedRows) !== -1) {
                    $(row).addClass('selected');
                }
                },
                drawCallback: function(settings) {
                var api = this.api();
                var data = api.rows({
                    page: 'current'
                }).data()
                $.each(data, function(i, item) {
                    var id = $(item[0]).attr("data-id");
                    if (id_purchase_order.indexOf("|" + id + ",") !== -1) {
                    $("#cb_" + id).attr('checked', true);
                    }
                });

                },

                columnDefs: [{
                searchable: false,
                orderable: false,
                targets: [0]
                }],

                dom: "<'row'<'col-sm-12'tr>>" +
                "<'datatable-footer force-padding no-y-padding'<'row'<'col-sm-4'i<'clearfix'>l><'col-sm-8'p>>>",
            });

            new $.fn.dataTable.Buttons(datatable, {
                dom: {
                container: {
                    className: 'btn-group pull-left'
                },
                button: {
                    className: 'btn btn-lg btn-icon-toggle ink-reaction'
                }
                },
                buttons: [{
                    extend: 'print',
                    className: 'btn-tooltip',
                    text: '<i class="fa fa-print"></i><small class="top center">Quick Print</small>',
                    // titleAttr: 'Quick print',
                    autoPrint: false,
                    footer: true,
                    exportOptions: {
                    columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    name: 'csv',
                    text: '<i class="fa fa-file-text-o"></i><small class="top center">export to CSV</small>',
                    // titleAttr: 'export to CSV',
                    className: 'btn-tooltip',
                    footer: true,
                    key: {
                    ctrlKey: true,
                    key: 's'
                    },
                    exportOptions: {
                    columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    name: 'excel',
                    text: '<i class="fa fa-file-excel-o"></i><small class="top center">export to EXCEL</small>',
                    // titleAttr: 'export to EXCEL',
                    className: 'btn-tooltip',
                    footer: true,
                    key: {
                    ctrlKey: true,
                    key: 'x'
                    },
                    exportOptions: {
                    columns: ':visible'
                    }
                },
                {
                    name: 'pdf',
                    className: 'buttons-pdf btn-tooltip',
                    text: '<i class="fa fa-file-pdf-o"></i><small class="top center">export to PDF</small>',
                    // titleAttr: 'export to PDF',
                    key: {
                    ctrlKey: true,
                    key: 'd'
                    },
                    action: function(e, dt, node, config) {
                    var pdfUrl = '<?= site_url('pdf'); ?>',
                        pdfTitle = '<?= PAGE_TITLE; ?>',
                        pdfData = datatable.buttons.exportData({
                        columns: ':visible'
                        });

                    submit_post_via_hidden_form(
                        pdfUrl, {
                        datatable: pdfData,
                        title: pdfTitle
                        }
                    );
                    }
                }
                ]
            });

            datatable.buttons(0, null).container()
                .appendTo($('.btn-toolbar'));

            if (datatableOptions.summaryColumns) {
                datatable.on('xhr', function() {
                var json = datatable.ajax.json();

                $.each(datatableOptions.summaryColumns, function(key, value) {
                    $(datatable.column(value).footer()).html(
                    json.total[value]
                    );
                });
                });
            }

            $(datatableElement).find('tbody').on('click', 'td', function() {
                datatableOptions.clickCount++;

                var modalOpenOnClick = datatable.row(this).data().DT_RowData.modal;
                var singleClickRow = datatable.row(this).data().DT_RowData.single_click;
                var doubleClickRow = datatable.row(this).data().DT_RowData.double_click;

                if (modalOpenOnClick) {
                var dataModal = $('#data-modal');
                var dataPrimaryKey = datatable.row(this).data().DT_RowData.pkey;

                $.get(modalOpenOnClick, function(data) {
                    var obj = $.parseJSON(data);

                    if (obj.type == 'denied') {
                    toastr.options.timeOut = 10000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.error(obj.info, 'ACCESS DENIED!');
                    } else {
                    $(dataModal)
                        .find('.modal-body')
                        .empty()
                        .append(obj.info);

                    $(dataModal)
                        .find('#modal-print-data-button')
                        .attr('href', obj.link.print);

                    $(dataModal)
                        .find('#modal-edit-data-button')
                        .attr('href', obj.link.edit);

                    $(dataModal)
                        .find('#modal-delete-data-button')
                        .attr('href', obj.link.delete);

                    $(dataModal).modal('show');

                    $(dataModal).on('click', '.modal-header:not(a)', function() {
                        $(dataModal).modal('hide');
                    });

                    $(dataModal).on('click', '.modal-footer:not(a)', function() {
                        $(dataModal).modal('hide');
                    });
                    }
                });
                } else {
                if (datatableOptions.clickCount === 1) {
                    datatableOptions.clickTimer = setTimeout(function() {
                    datatableOptions.clickCount = 0;

                    if (singleClickRow)
                        window.location = singleClickRow;
                    }, datatableOptions.clickDelay);
                } else {
                    clearTimeout(datatableOptions.clickTimer);
                    datatableOptions.clickCount = 0;

                    if (doubleClickRow)
                    window.location = doubleClickRow;
                }
                }
            });

            var buttonSubmitDocument = $('#btn-submit-document');
            var formDocument = $('#form-change-item');
            $(buttonSubmitDocument).on('click', function(e) {
                e.preventDefault();
                $(buttonSubmitDocument).attr('disabled', true);

                var url = $(this).attr('href');

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
            });

            $("#modal-approve-data-button-multi").click(function() {
                var action = $(this).data('source');
                encodeNotes();
                // if (!encodeNotes()) {
                //   toastr.options.timeOut = 10000;
                //   toastr.options.positionClass = 'toast-top-right';
                //   toastr.error('You must filled Notes for each item that you want to approve');
                // } else {
                $(this).attr('disabled', true);
                $("#modal-reject-data-button-multi").attr('disabled', true);
                if (id_purchase_order !== "") {
                    $.post(action, {
                    'id_capex_request': id_purchase_order,
                    'notes': notes
                    }).done(function(data) {
                    console.log(data);
                    $("#modal-approve-data-button-multi").attr('disabled', false);
                    $("#modal-reject-data-button-multi").attr('disabled', false);
                    var result = jQuery.parseJSON(data);
                    if (result.status == 'success') {
                        toastr.options.timeOut = 10000;
                        toastr.options.positionClass = 'toast-top-right';
                        toastr.success('Success aprove data the page will reload');
                        window.location.reload();
                    } else {
                        toastr.options.timeOut = 10000;
                        toastr.options.positionClass = 'toast-top-right';
                        toastr.danger('Failed aprove data');
                    }
                    }).fail(function() {
                    $("#modal-approve-data-button-multi").attr('disabled', false);
                    $("#modal-reject-data-button-multi").attr('disabled', false);
                    toastr.options.timeOut = 10000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.error('Delete Failed! This data is still being used by another document.');
                    });
                } else {
                    $(this).attr('disabled', false);
                    $("#modal-reject-data-button-multi").attr('disabled', false);
                    toastr.options.timeOut = 10000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.error('Empty selected data');
                }
                // }

            });

            $("#modal-close-data-button-multi").click(function() {
                var action = $(this).data('source');
                $(this).attr('disabled', true);
                encodeNotes();
                if (!encodeNotes()) {
                toastr.options.timeOut = 10000;
                toastr.options.positionClass = 'toast-top-right';
                toastr.error('You must filled notes for each item that you want to close');
                $("#modal-close-data-button-multi").attr('disabled', false);
                } else {
                if (id_purchase_order !== "") {
                    $.post(action, {
                    'id_purchase_order': id_purchase_order,
                    'notes': notes
                    }).done(function(data) {
                    console.log(data);
                    $("#modal-close-data-button-multi").attr('disabled', false);
                    var result = jQuery.parseJSON(data);
                    if (result.status == 'success') {
                        toastr.options.timeOut = 10000;
                        toastr.options.positionClass = 'toast-top-right';
                        toastr.success('Success close data the page will reload');
                        window.location.reload();
                    } else {
                        toastr.options.timeOut = 10000;
                        toastr.options.positionClass = 'toast-top-right';
                        toastr.danger('Failed close data Please Contact the Operator and Try Again Later');
                    }
                    }).fail(function() {
                    $("#modal-close-data-button-multi").attr('disabled', false);
                    toastr.options.timeOut = 10000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.danger('Failed close data Please Contact the Operator and Try Again Later');
                    });
                } else {
                    $(this).attr('disabled', false);
                    toastr.options.timeOut = 10000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.error('Empty selected data');
                }
                }

            });

            function encodeNotes() {
                new_id_purchase_order = id_purchase_order.replace(/\|/g, "");
                new_id_purchase_order = new_id_purchase_order.substring(0, new_id_purchase_order.length - 1);
                arr = new_id_purchase_order.split(",");
                notes = "";
                y = 0;
                $.each(arr, function(i, x) {
                if ($("#note_" + x).val() != "") {
                    notes = notes + "|" + $("#note_" + x).val() + "##,";
                    y += 1;
                } else {
                    return false;
                }
                });
                if (y == arr.length) {
                return true
                } else {
                return false
                }

            }

            function encodePrice() {
                new_id_purchase_order = id_purchase_order.replace(/\|/g, "");
                new_id_purchase_order = new_id_purchase_order.substring(0, new_id_purchase_order.length - 1);
                arr = new_id_purchase_order.split(",");
                price = "";
                y = 0;
                $.each(arr, function(i, x) {
                if ($("#price_" + x).val() != "") {
                    price = price + "|" + $("#price_" + x).val() + "##,";
                    y += 1;
                } else {
                    return false;
                }
                });
                if (y == arr.length) {
                return true
                } else {
                return false
                }

            }

            $("#modal-reject-data-button-multi").click(function() {
                $("#modal-reject-data-button-multi").attr('disabled', true);
                $("#modal-approve-data-button-multi").attr('disabled', true);
                if (!encodeNotes()) {
                toastr.options.timeOut = 10000;
                toastr.options.positionClass = 'toast-top-right';
                toastr.error('You must filled notes for each item that you want to reject');
                } 
                
                else {

                if (id_purchase_order == "") {
                    $("#modal-reject-data-button-multi").attr('disabled', false);
                    $("#modal-approve-data-button-multi").attr('disabled', false);
                    toastr.options.timeOut = 10000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.error('You must select item that you want to reject');
                } else {
                    $.ajax({
                    type: "POST",
                    url: 'capex_request/multi_reject',
                    data: {
                        "id_purchase_order": id_purchase_order,
                        "notes": notes,
                        // "price": price
                    },
                    cache: false,
                    success: function(response) {
                        console.log(response);
                        var data = jQuery.parseJSON(response);
                        if (data.status == "success") {
                        toastr.options.timeOut = 10000;
                        toastr.options.positionClass = 'toast-top-right';
                        toastr.success('Successfully reject item, the page will reload now');
                        window.location.reload();
                        } else {
                        toastr.options.timeOut = 10000;
                        toastr.options.positionClass = 'toast-top-right';
                        toastr.error('Failed rejected item');
                        }
                        $("#modal-reject-data-button-multi").attr('disabled', false);
                        $("#modal-approve-data-button-multi").attr('disabled', false);
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        $("#modal-reject-data-button-multi").attr('disabled', false);
                        $("#modal-approve-data-button-multi").attr('disabled', false);
                        console.log(xhr.status);
                        console.log(xhr.responseText);
                        console.log(thrownError);
                    }
                    });
                }
                }
            });

            $(datatableElement).find('tbody').on('click', 'tr', function(e) {
                console.log(e.target.nodeName);
                if (e.target.nodeName === "INPUT") {
                    if ($(e.target).attr("type") === "checkbox") {
                        if ($(e.target).prop('checked')) {
                        id_purchase_order += "|" + $(e.target).attr('data-id') + ",";
                        } else {
                        id_purchase_order = id_purchase_order.replace("|" + $(this).attr('data-id') + ",", "");
                        }
                    }
                    console.log(id_purchase_order);

                } else if (e.target.nodeName === "SPAN") {
                    var a = $(e.target).data('id');
                    console.log(e.target.nodeName);
                } else if (e.target.nodeName === "I") {
                    var id = $(this).attr('data-id');
                    getAttachment(id);
                } else {
                    $(this).popup();
                }

            });

            function getAttachment(id) {
                $.ajax({
                type: "GET",
                url: 'capex_request/listAttachment/' + id,
                cache: false,
                success: function(response) {
                    var data = jQuery.parseJSON(response)
                    $("#listView").html("")
                    $("#attachment_modal").modal("show");
                    $.each(data, function(i, item) {
                    var text = '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td><a href="<?= base_url() ?>' + item.file + '" target="_blank">' + item.file + '</a></td>' +
                        '</tr>';
                    $("#listView").append(text);
                    });
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(xhr.responseText);
                    console.log(thrownError);
                }
                });
            }
          
            // $(datatableElement).find('tbody').on('click', 'a', function(e) {
            //     e.preventDefault();
            //     // console.log("tuliskan fungsinya disini");
            //     // tulis disini
            //     var id = $(this).data('id');
            //     if (id == 'item') {
            //     var a = $(this).data('item-row');
            //     $.ajax({
            //         url: "<?= site_url($module['route'] . '/info_item/'); ?>" + "/" + a,
            //         type: 'get',
            //         success: function(data) {
            //         var dataModal = $('#modal-item');
            //         var obj = $.parseJSON(data);
            //         $(dataModal)
            //             .find('.modal-body')
            //             .empty()
            //             .append(obj.info);
            //         $(dataModal).modal('show');
            //         }
            //     });
            //     }

            //     if (id == 'on-hand') {
            //     var a = $(this).data('item-row');
            //     $.ajax({
            //         url: "<?= site_url($module['route'] . '/info_on_hand/'); ?>" + "/" + a,
            //         type: 'get',
            //         success: function(data) {
            //         var dataModal = $('#modal-item');
            //         var obj = $.parseJSON(data);
            //         $(dataModal)
            //             .find('.modal-body')
            //             .empty()
            //             .append(obj.info);
            //         $(dataModal).modal('show');
            //         }
            //     });
            //     }


            // });

            $('.filter_numeric_text').on('keyup click', function() {
                var i = $(this).data('column');
                var v = $(this).val();
                datatable.columns(i).search(v).draw();
            });

            $('.filter_dropdown').on('change', function() {
                var i = $(this).data('column');
                var v = $(this).val();
                datatable.columns(i).search(v).draw();
            });

            $('.filter_boolean').on('change', function() {
                var checked = $(this).is(':checked');
                var i = $(this).data('column');

                if (checked) {
                datatable.columns(i).search('true').draw();
                } else {
                datatable.columns(i).search('').draw();
                }
            });

            $('.filter_daterange').daterangepicker({
                autoUpdateInput: false,
                parentEl: '#offcanvas-datatable-filter',
                locale: {
                cancelLabel: 'Clear'
                },
            }).on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' ' + picker.endDate.format('YYYY-MM-DD'));
                var i = $(this).data('column');
                var v = $(this).val();
                datatable.columns(i).search(v).draw();
            }).on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                var i = $(this).data('column');
                datatable.columns(i).search('').draw();
            });

            $('a.column-toggle').on('click', function(e) {
                e.preventDefault();
                var column = datatable.column($(this).attr('data-column'));

                column.visible(!column.visible());

                var label = $(this).attr('data-label');
                var text = (column.visible() === true ? '<div class="tile-text">' + label + '</div>' : '<div class="tile-text text-muted">' + label + '</div>');

                $(this).html(text);
            });

            $('.dataTables_paginate').find('a').removeClass();
            $('#datatable-form').removeClass('hidden');
            $('#datatable-form input').on('keyup', function() {
                datatable.search(this.value).draw();
            });
            $('[data-toggle="reload"]').on('click', function() {
                datatable.ajax.reload(null, false);
            });

            datatable.on('processing.dt', function(e, settings, processing) {
                if (processing) {
                $('.progress-overlay').show();
                } else {
                $('.progress-overlay').hide();
                }
            });

            $(document).on('click', '.btn-xhr-delete', function(e) {
                e.preventDefault();

                var button = $(this);
                var form = $('.form-xhr');
                var action = button.attr('href');

                button.attr('disabled', true);

                if (confirm('Are you sure want to delete this data? Beware of this data can not be restored after it is removed. Continue?')) {
                $.post(action, form.serialize()).done(function(data) {
                    var obj = $.parseJSON(data);
                    if (obj.type == 'danger') {
                    toastr.options.timeOut = 10000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.error(obj.info);

                    buttonToDelete.attr('disabled', false);
                    } else {
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.success(obj.info);

                    form.reset();

                    $('[data-dismiss="modal"]').trigger('click');

                    if (datatable) {
                        datatable.ajax.reload(null, false);
                    }
                    }
                }).fail(function() {
                    toastr.options.timeOut = 10000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.error('Delete Failed! This data is still being used by another document.');
                });
                }

                button.attr('disabled', false);
            });

            $(document).on('click', '.btn-xhr-change', function(e) {
                e.preventDefault();

                var button = $(this);
                var type = $(this).data('type-po');


                if(type=='f'){
                var last_type = 'tanpa PO';
                var next_type = 'dengan PO';
                }else{
                var last_type = 'dengan PO';
                var next_type = 'tanpa PO';
                }
                button.attr('disabled', true);

                var form = $('.form-xhr-change');
                var action = button.attr('href');
                if (confirm('Capex Request ini merupakan Capex request '+last_type+'? Anda yakin akan mengubah request ini menjadi request '+next_type+'?')) {

                let notes = prompt("Please enter notes", "");
                $('form.form-xhr-change input[name=change_notes]').val(notes);
                
                $.post(action, form.serialize()).done(function(data) {
                    var obj = $.parseJSON(data);
                    if (obj.type == 'danger') {
                    toastr.options.timeOut = 10000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.error(obj.info);

                    buttonToDelete.attr('disabled', false);
                    } else {
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.success(obj.info);

                    form.reset();

                    $('[data-dismiss="modal"]').trigger('click');

                    if (datatable) {
                        datatable.ajax.reload(null, false);
                    }
                    }
                }).fail(function() {
                    toastr.options.timeOut = 10000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.error('Cancel Failed!');
                });
                }

                button.attr('disabled', false);
            });

            $(document).on('click', '.btn-xhr-submit', function(e) {
                e.preventDefault();

                var button = $(this);
                var form = $('.form-xhr');
                var action = form.attr('action');
                var formData = new FormData($('.form-xhr')[0]);
                var formParams = form.serializeArray();

                // $.each(form.find('input[type="file"]'), function (i, tag) {
                //     $.each($(tag)[0].files, function (i, file) {
                //         formData.append(tag.name, file);
                //     });
                // });

                // $.each(formParams, function (i, val) {
                //     formData.append(val.name, val.value);
                // });

                button.attr('disabled', true);
                console.log(formData);
                console.log(formParams);

                if (form.valid()) {
                    // alert('form valid');
                    // alert(action);
                    $.ajax({
                        type: "POST",
                        url: action,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data){
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
                                datatable.ajax.reload(null, false);
                                // window.location.href = '<?= site_url($module['route'].'/contract/'.$entity['employee_id']); ?>';                  
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                                alert('error');
                                toastr.options.timeOut = 10000;
                                toastr.options.positionClass = 'toast-top-right';
                                toastr.error('There are error while processing data. Please try again later.');
                            }
                        });  
                }else{
                    alert('form not valid');
                    toastr.options.timeOut = 10000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.error('There are error while processing data. Please try again later.');
                }

                button.attr('disabled', false);
            });
        });
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
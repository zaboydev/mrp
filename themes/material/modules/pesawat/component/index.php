<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<style>
    /* .table-aircraft-component td{
        height : 10px;
    } */

    .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
        padding: 3px 1px 3px 3px;
    }
</style>
<section class="has-actions style-default">
    <div class="section-body">
    <?= form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form-create-document')); ?>
        <div class="card">
            <div class="card-head style-primary-dark">
                <header><?= PAGE_TITLE; ?> </header>
                <div class="tools">
                    <a class="btn btn-icon-toggle btn-tooltip ink-reaction" data-toggle="offcanvas" href="#offcanvas-datatable-filter">
                        <i class="md md-more-horiz"></i>
                        <small class="bottom center">Data options</small>
                    </a>
                </div>
            </div>
            <div class="card-body no-padding">
                <?php
                    if ($this->session->flashdata('alert'))
                        render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
                ?>

                <div class="document-header force-padding">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table-aircraft-component">
                                    <tbody>
                                        <tr>
                                            <td width="15%" rowspan="4">
                                                <img src="<?=base_url('themes/admin_lte/assets/images/logo.png');?>" style="max-width:95%;">
                                                <p style="font-size:12px;font-weight:bold;text-align:center;margin-bottom:0;line-height: normal;">BALI WIDYA DIRGANTARA</p>
                                                <p style="font-size:12px;font-weight:bold;text-align:center;margin-bottom:0;line-height: normal;">Bali Intl'l Flight Academy</p>
                                            </td>
                                            <td width="5%" style="text-align:right;">REG</td>
                                            <td width="10%">: <?= $aircraft['nama_pesawat']; ?></td>
                                            <td width="55%" rowspan="3" style="font-weight:bolder;font-size:35px;text-decoration: underline;">
                                                AIRCRAFT PARTS AND TIME LIMIT DATA
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="5%" style="text-align:right;">MSN</td>
                                            <td width="10%">: <?= $aircraft['aircraft_serial_number']; ?></td>
                                        </tr>
                                        <tr>
                                            <td width="5%" style="text-align:right;">TYPE</td>
                                            <td width="10%">: <?= $aircraft['type']; ?></td>
                                        </tr>
                                        <tr>
                                            <td width="5%" style="text-align:right;">DOM</td>
                                            <td width="10%">: <?= $aircraft['date_of_manufacture']; ?></td>
                                            <td width="55%"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div style="clear: both;border-bottom: 3px double #999;padding-top:2px;padding-bottom:3px;"class="row"></div>
                    <div class="row" style="padding-top:4px;">
                        <div class="col-sm-12">
                            <div class="document-data table-responsive">
                                <table class="table table-bordered table-nowrap">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" style="text-align:center;">No</th>
                                            <th rowspan="2" style="text-align:center;">Description</th>
                                            <th rowspan="2" style="text-align:center;">Part Number</th>
                                            <th rowspan="2" style="text-align:center;">Alt Part Number</th>
                                            <th rowspan="2" style="text-align:center;">Serial Number</th>
                                            <th rowspan="2" style="text-align:center;">Interval</th>
                                            <th colspan="4" style="text-align:center;">Installation Data</th>
                                            <th colspan="2" style="text-align:center;">Next Due</th>
                                            <th rowspan="2" style="text-align:center;">Remarks</th>
                                        </tr>
                                        <tr>
                                            <th style="text-align:center;">Date</th>
                                            <th style="text-align:center;">AF TSN</th>
                                            <th style="text-align:center;">Part TSN</th>
                                            <th style="text-align:center;">Part TSO</th>
                                            <th style="text-align:center;">Date</th>
                                            <th style="text-align:center;">Hour</th>
                                        </tr>                                                       
                                    </thead>
                                    <tbody>
                                        <?php foreach (config_item('component_type') as $category) : ?>
                                        <?php 
                                            $n=1; 
                                            $type = str_replace(" ", "_", $category);
                                        ?>
                                        <tr>
                                            <td style="text-decoration: underline;font-weight:bold;"><?= ucwords($category); ?></td>
                                        </tr>
                                        <?php if(count($aircraft['component_'.$type])>0):?>
                                        <?php foreach ($aircraft['component_'.$type] as $i => $items) : ?>
                                        <tr>
                                            <td style="text-align:center;"><?= $n++; ?></td>
                                            <td><?= $items['description']; ?></td>
                                            <td><?= $items['part_number']; ?></td>
                                            <td><?= $items['alternate_part_number']; ?></td>
                                            <td><?= ($items['serial_number']==NULL)? 'N/A':$items['serial_number']; ?></td>
                                            <td><?= $items['interval']; ?> <?= $items['interval_satuan']; ?></td>
                                            <td><?= print_date($items['installation_date'], 'd M Y'); ?></td>
                                            <td><?= $items['af_tsn']; ?></td>
                                            <td><?= $items['equip_tsn']; ?></td>
                                            <td><?= $items['tso']; ?></td>
                                            <td><?= (!empty($data['next_due_date'])) ? print_date($items['next_due_date'], 'd M Y'): ''; ?></td>
                                            <td><?= $items['next_due_hour']; ?></td>
                                            <td><?= $items['remarks']; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php else:?>
                                        <tr>
                                            <td style="text-align:center;" colspan="12">No Component</td>
                                        </tr>
                                        <?php endif;?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-actionbar">
                <div class="card-actionbar-row">
                </div>
            </div>
        </div>
    <?= form_close(); ?>
    </div>
    <div class="section-action style-default-bright">
        <div class="section-floating-action-row">
            <div class="btn-group dropup">
                <button type="button" class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-create-document" data-toggle="dropdown">
                    <i class="md md-add"></i>
                    <small class="top right">Add Component</small>
                </button>

                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <?php foreach (config_item('component_type') as $category) : ?>
                    <li>
                        <a href="<?= site_url($module['route'] . '/create_component/' . $category.'/'.$page['aircraft_id']); ?>"><?= strtoupper($category); ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>    
</section>
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
            <li class="tile">
                <a class="tile-content ink-reaction" href="#offcanvas-import" data-toggle="offcanvas">
                <div class="tile-icon">
                    <i class="fa fa-download"></i>
                </div>
                <div class="tile-text">
                    Import Data
                    <small>import from csv file</small>
                </div>
                </a>
            </li>
        </ul>
    </div>
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

    <script>
        Pace.on('start', function() {
            $('.progress-overlay').show();
        });

        Pace.on('done', function() {
            $('.progress-overlay').hide();
        });

        function popup(mylink, windowname){
            var height = window.innerHeight;
            var widht;
            var href;

            if (screen.availWidth > 768){
                width = 769;
            } else {
                width = screen.availWidth;
            }

            var left = (screen.availWidth / 2) - (width / 2);
            var top = 0;
            // var top = (screen.availHeight / 2) - (height / 2);

            if (typeof(mylink) == 'string') href = mylink;
                else href = mylink.href;

            window.open(href, windowname, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+width+', height='+height+', top='+top+', left='+left);

            if (! window.focus) return true;
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
                            window.location.href = '<?= $page['route']; ?>';
                        }, 5000);
                    }

                    $(buttonSubmitDocument).attr('disabled', false);
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
                var id = $(this).attr('id');

                console.log(id);
                console.log(val);
                if(id=='received_from'){
                    if(val!=null){
                        $('.btn-item').removeClass('hide');
                    }else{
                        if($('.btn-item').hasClass('hide')){
                            $('.btn-item').addClass('hide');
                        }
                    }        
                }

                $.get(url, {
                    data: val
                });
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
                /*$('#ajax-form-create-document')[0].reset(); // reset form on modals*/


                $.ajax({
                    url: "<?= site_url($module['route'] . '/ajax_editItem/') ?>/" + id,
                    type: "GET",
                    data: data_send,
                    dataType: "JSON",
                    success: function(response) {
                        var action = "<?=site_url($module['route'] .'/edit_item')?>";
                        console.log(JSON.stringify(response));
                        $('[name="serial_number"]').val(response.serial_number);
                        $('[name="part_number"]').val(response.part_number);
                        $('[name="description"]').val(response.description);
                        $('[name="alternate_part_number"]').val(response.alternate_part_number);
                        $('[name="group"]').val(response.group);
                        $('[name="received_quantity"]').val(response.received_quantity);
                        $('[name="quantity_order"]').val(response.quantity_order);
                        $('[name="minimum_quantity"]').val(response.minimum_quantity);
                        $('[name="unit"]').val(response.received_unit);
                        // $('[name="received_unit_value"]').val(response.received_unit_value);
                        $('[name="condition"]').val(response.condition);
                        $('[name="stores"]').val(response.stores);
                        $('[name="expired_date"]').val(response.expired_date);
                        if (response.no_expired_date == "no") {
                            $('[id="no_expired_date"]').attr('checked', true);
                        }
                        if ($('[id="no_expired_date"]').is(':checked')) {
                            $('input[id="expired_date"]').prop('readonly', true);
                            $('input[id="expired_date"]').prop('required', false);
                        } else {
                            $('input[id="expired_date"]').prop('readonly', false);
                            $('input[id="expired_date"]').prop('required', true);
                        }
                        $('[name="purchase_order_number"]').val(response.purchase_order_number);
                        $('[name="tgl_nota"]').val(response.tgl_nota);
                        $('[name="reference_number"]').val(response.reference_number);
                        $('[name="awb_number"]').val(response.awb_number);
                        $('[name="remarks"]').val(response.remarks);
                        $('[name="item_id"]').val(id);
                        $('[name="edit_kode_akunting"]').val(response.kode_akunting);
                        // $('[name="edit_kurs"]').val('rupiah');
                        $('[name="unit_pakai"]').val(response.unit_pakai);
                        $('[name="qty_konversi"]').val(response.hasil_konversi);
                        // $('[id="edit_unit_terima"]').val(response.unit_pakai);
                        $('[name="received_unit"]').val(response.received_unit);
                        // $('[name="isi"]').val(response.hasil_konversi);
                        $('[name="unit_used"]').val(response.unit_pakai);
                        $('[name="kode_stok"]').val(response.kode_stok);
                        // if (response.isi) {
                        //   $('[name="edit_isi"]').val(response.isi);
                        // } else {
                        //   $('[name="edit_isi"]').val(response.qty_konversi);
                        // }
                        $('[name="isi"]').val(response.isi);
                        $('[name="value_order"]').val(response.value_order);
                        // $('[name="edit_isi"]').val(response.isi);
                        $('[name="qty_konversi"]').val(response.hasil_konversi);
                        // if (response.kurs_dollar > 1) {
                        //   $('[name="edit_kurs"]').val('dollar');
                        //   $('[name="received_unit_value"]').val(response.unit_value_dollar);

                        // } else {
                        //   $('[name="edit_kurs"]').val('rupiah');
                        //   $('[name="received_unit_value"]').val(response.received_unit_value);

                        // }
                        // if (response.cu) {
                        //   $('[name="edit_kurs"]').val(response.kurs);
                        // }
                        $('[name="received_unit_value"]').val(response.received_unit_value);
                        $('[name="received_unit_value_dollar"]').val(response.received_unit_value_dollar);
                        $('[name="kurs"]').val(response.currency);
                        $('[name="kode_stok"]').val(response.kode_stok);
                        $('[name="stock_in_store_id"]').val(response.stock_in_stores_id);
                        $('[name="receipt_items_id"]').val(response.receipt_items_id);

                        $('#edit_purchase_order_item_id').val(response.purchase_order_item_id);
                        $('#edit_internal_delivery_item_id').val(response.internal_delivery_item_id);

                        if (response.purchase_order_item_id != '') {
                            $('[name="received_unit_value"]').attr('readonly', true);
                            $('[name="purchase_order_number"]').attr('readonly', true);
                        }
                        
                        $('[name="item_id"]').val(id);




                        $('#modal-add-item').modal('show'); // show bootstrap modal when complete loaded
                        $('.modal-title').text('Edit Item'); // Set title to Bootstrap modal title
                        $('#modal-add-item form').attr('action', action);// Set form action

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error get data from ajax');
                    }
                });
            });
        });
        
    </script>
    <?= html_script('themes/material/assets/js/core/source/App.min.js') ?>

<?php endblock() ?>
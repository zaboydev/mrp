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
                    <div class="col-sm-12 col-lg-5">
                        <div class="row">  
                            <div class="col-sm-12">
                                <h4>SPD Info</h4>

                                <div class="">
                                    <dl class="dl-inline">
                                        <dt>
                                            SPD Number
                                        </dt>
                                        <dd>
                                            <?= $entity['document_number']; ?>
                                        </dd>

                                        <dt>
                                            Date
                                        </dt>
                                        <dd>
                                            <?=print_date($entity['date']);?>
                                        </dd>

                                        <dt>
                                            Supervisor / Atasan
                                        </dt>
                                        <dd>
                                            <?=print_string($entity['head_dept']);?>
                                        </dd>

                                        <dt>
                                            Name
                                        </dt>
                                        <dd>
                                            <?=print_string($entity['person_name']);?>
                                        </dd>

                                        <dt>
                                            Occupation / Jabatan
                                        </dt>
                                        <dd>
                                            <?=print_string($entity['occupation']);?>
                                        </dd>

                                        <dt>
                                            From / Kota Asal
                                        </dt>
                                        <dd>
                                            <?=print_string($entity['from_base']);?>
                                        </dd>

                                        <dt>
                                            Destination / Kota Tujuan
                                        </dt>
                                        <dd>
                                            <?=print_string($entity['business_trip_destination']);?>
                                        </dd>  
                                        
                                        <dt>
                                            Date / Tanggal
                                        </dt>
                                        <dd>
                                            <?=print_date($entity['start_date'], 'd F Y');?> s/d <?=print_date($entity['end_date'], 'd F Y');?>
                                        </dd>

                                        <dt>
                                            Duration / Kota Tujuan
                                        </dt>
                                        <dd>
                                            <?=print_string($entity['duration']);?>
                                        </dd> 
                                        
                                        <dt>
                                            Purpose of Travel on Duty
                                        </dt>
                                        <dd>
                                            <?=print_string($entity['notes']);?>
                                        </dd> 
                                        <dt>
                                            Transportasi
                                        </dt>
                                        <dd>
                                            <?=print_string($entity['transportation']);?>
                                        </dd> 
                                    </dl>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="col-sm-12 col-lg-7">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group" style="padding-top: 25px;">
                                    <select name="transportation" id="transportation" class="form-control select2" data-placeholder="Select Transport" required>
                                        <option></option>
                                        <?php foreach(transportation_list() as $transportation):?>
                                        <option value="<?=$transportation['transportation'];?>" <?= ($transportation['transportation'] == $entity['transportation']) ? 'selected' : ''; ?>><?=$transportation['transportation'];?></option>
                                        <?php endforeach;?>
                                    </select>
                                    <label for="transportation">Transportation / Jenis Transportasi</label>
                                </div>  

                                <div class="form-group">
                                    <textarea name="remarks_transport" id="remarks_transport" class="form-control" rows="1"><?= $entity['remarks_transport']; ?></textarea>
                                    <label for="remarks_transport">Remarks Transport</label>
                                </div>
                                
                                <div class="form-group">
                                    <label for="type">Type</label>
                                    <div class="radio">
                                        <input type="radio" name="spd_type" id="advance" value="advance" checked>
                                        <label for="advance">
                                        Advance
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <input type="radio" name="spd_type" id="expense" value="expense">
                                        <label for="expense">
                                        Expenses
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <textarea name="approval_notes" id="approval_notes" class="form-control" rows="3" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_approval_notes'); ?>"><?= $entity['approval_notes']; ?></textarea>
                                    <label for="approval_notes">Approval Notes</label>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <h4>SPD Expenses</h4>
                                <table class="table table-hover" id="table-document" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="5%"></th>
                                            <th width="45%">Description</th>
                                            <th width="10%">Days</th>
                                            <th width="20%">Amount</th>
                                            <th width="20%" class="text-center">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $grand_total = array(); $n=1;?>
                                        <?php foreach ($entity['items'] as $i => $items) : ?>
                                        <?php $grand_total[] = $items['total']; ?>
                                        <tr id="row_<?= $i; ?>">
                                            <td>
                                                <?= $n++; ?>
                                            </td>
                                            <td class="expense_name" style="font-weight:500;">
                                                <input name="expense_name[]" type="text" class="sel_applied form-control input-sm" value="<?=$items['expense_name'];?>" readonly required>
                                                <input name="account_code[]" type="hidden" class="sel_applied form-control input-sm" value="<?=$items['account_code'];?>" readonly required>
                                            </td>
                                            <td class="qty" style="font-weight:500;">
                                                <input name="qty[]" type="text" class="sel_applied form-control input-sm" value="<?=$items['qty'];?>" required>
                                            </td>
                                            <td class="amount" style="font-weight:500;word-wrap:break-word;">
                                                <input name="amount[]" type="text" class="sel_applied form-control number input-sm" value="<?=$items['amount'];?>" required>
                                            </td>
                                            <td class="total" style="font-weight:500;">
                                                <input name="total[]" type="text" class="sel_applied form-control number input-sm" value="<?=$items['total'];?>" required>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <button type="button" href="" onClick="addRow()" class="btn btn-primary ink-reaction number hide">
                                    Add
                                </button>
                            </div>
                        </div>
                        
                    </div>

                    <div class="col-sm-12 col-lg-4">
                        
                    </div>
                </div>
            </div>

            <div class="document-data table-responsive">
                
            </div>
        </div>
        <div class="card-actionbar">
            <div class="card-actionbar-row">
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
                    
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="modal-add-item-submit" class="btn btn-primary btn-create ink-reaction">
                        Add Item
                    </button>

                    <input type="hidden" name="consignor" id="consignor">
                    <input type="reset" name="reset" class="sr-only">
                </div>

                <?= form_close(); ?>
            </div>
        </div>
    </div>

    <div class="section-action style-default-bright">
        <div class="section-floating-action-row">
            <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?= site_url($module['route'] . '/save_hr_approve'); ?>">
                <i class="md md-save"></i>
                <small class="top right">Save & Approve SPD</small>
            </a>
        </div>
    </div>
    <table class="table-row-item hide">
        <tbody>
            <tr>
                <td class="item-list" style="text-align:center;">
                    <a  href="javascript:;" title="Delete" class="btn btn-icon-toggle btn-danger btn-xs btn-row-delete-item" data-tipe="delete"><i class="fa fa-trash"></i>
                    </a>                     
                </td>
                <td class="expense_name item-list">
                    <input type="text" name="expense_name[]" class="form-control input-sm" required>
                </td>
                <td class="qty item-list">
                    <input type="text" name="qty[]" value="<?= $entity['duration']?>" class="form-control input-sm" required>
                </td>
                
                <td class="amount item-list">
                    <input type="text" name="amount[]" class="form-control number input-sm" required>
                </td>

                <td class="total item-list">
                    <input type="text" name="total[]" class="form-control number input-sm" required>
                </td>
            </tr>
        </tbody>
    </table>
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
<?= html_script('themes/script/jquery.number.js') ?>
<?= html_script('vendors/select2-4.0.3/dist/js/select2.min.js') ?>
<?= html_script('vendors/select2-pmd/js/pmd-select2.js') ?>
<script>
    Pace.on('start', function() {
        $('.progress-overlay').show();
    });

    Pace.on('done', function() {
        $('.progress-overlay').hide();
    });
    $('.number').number(true, 2, '.', ',');
    set_qty();
    set_amount();

    function addRow() {
        var row_payment = $('.table-row-item tbody').html();
        var el = $(row_payment);
        $('#table-document tbody').append(el);
        $('#table-document tbody tr:last').find('input[name="amount[]"]').number(true, 2, '.', ',');
        $('#table-document tbody tr:last').find('input[name="total[]"]').number(true, 2, '.', ',');

        btn_row_delete_item();
        set_qty();
        set_amount();
    }

    function btn_row_delete_item() {
        $('.btn-row-delete-item').click(function () {
            $(this).parents('tr').remove();
        });
    }

    function set_qty() {
        $('[name="qty[]"]').keyup(function () {
            var amount = $(this).parents('td').siblings('td.amount').children('input').val();

            var qty = $(this).val();
            var subtotal = $(this).parents('td').siblings('td.total').children('input');
            if (qty != '' || qty > 0) {
                var sub_total = parseFloat(amount) * parseFloat(qty);
                total = Number.parseFloat(sub_total).toFixed(2);
                subtotal.val(total);
                // set_subtotal();
            }
        });
    }

    function set_amount() {
        $('[name="amount[]"]').keyup(function () {
            var qty = $(this).parents('td').siblings('td.qty').children('input').val();

            var amount = $(this).val();
            var subtotal = $(this).parents('td').siblings('td.total').children('input');
            if (amount != '' || amount > 0) {
                var sub_total = parseFloat(amount) * parseFloat(qty);
                total = Number.parseFloat(sub_total).toFixed(2);
                subtotal.val(total);
                // set_subtotal();
            }
        });
    }

    $('.select2').select2({
        // theme: "bootstrap",
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
            String.fromCharCode(event.which).toLowerCase() === 'x')) 
        {
            event.preventDefault();
        }
    });

    $(function() {
        // GENERAL ELEMENTS
        var formDocument = $('#form-create-document');
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

        var startDate = new Date(<?= config_item('period_year'); ?>, <?= config_item('period_month'); ?> - 1, 1);
        var lastDate = new Date(<?= config_item('period_year'); ?>, <?= config_item('period_month'); ?>, 0);
        var last_publish = $('[name="opname_start_date"]').val();
        var today = new Date();
        today.setDate(today.getDate() - 2);
        $('[data-provide="datepicker"]').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd-mm-yyyy',
            startDate: today,
            // endDate: last_opname
        });

        $('[data-provide="daterange"]').daterangepicker({
            autoUpdateInput: false,
            parentEl: '#offcanvas-datatable-filter',
            locale: {
                cancelLabel: 'Clear'
            }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' s/d ' + picker.endDate.format('DD-MM-YYYY')).trigger('change');

            var start_date  = new Date(picker.startDate.format('YYYY-MM-DD'));
            var end_date    = new Date(picker.endDate.format('YYYY-MM-DD'));

            // To calculate the time difference of two dates
            var Difference_In_Time = end_date.getTime() - start_date.getTime();
            
            // To calculate the no. of days between two dates
            var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);

            console.log(start_date);
            console.log(end_date);
            console.log(Difference_In_Days+1);

            $('#duration').val(Difference_In_Days+1).trigger('change');
            $('#start_date').val(picker.startDate.format('YYYY-MM-DD')).trigger('change');
            $('#end_date').val(picker.endDate.format('YYYY-MM-DD')).trigger('change');
            
        }).on('cancel.daterangepicker', function(ev, picker) {
            
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

            if (confirm('Are you sure want to save and approve this Document ? Continue?')) {
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
            }else{
                $(buttonSubmitDocument).attr('disabled', false);
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
            var id = $(this).attr('id');

            $.get(url, {
                data: val
            });
        });

        $('#person_in_charge').change(function () {
            var employee_number = $('#person_in_charge').val();                        
            var position = $('#person_in_charge option:selected').data('position');  
            $('#occupation').val(position).trigger('change');
        });
    });
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
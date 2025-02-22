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
                    <div class="col-sm-6 col-lg-4">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-content">
                                    <input type="text" name="document_number" id="document_number" class="form-control" maxlength="6" value="<?= $_SESSION['sppd']['document_number']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_doc_number'); ?>" required>
                                    <label for="document_number">SPPD No.</label>
                                </div>
                                <span class="input-group-addon"><?= $_SESSION['sppd']['format_number']; ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="text" name="date" id="date" data-provide="datepicker" data-date-format="dd-mm-yyyy" class="form-control" value="<?= $_SESSION['sppd']['date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_received_date'); ?>" required>
                            <label for="date">Date</label>
                        </div>

                        <div class="form-group">
                            <select name="with_po" id="with_po" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_head_dept'); ?>" required>
                                <option></option>
                                <?php foreach(list_user_approval([10,16]) as $atasan):?>
                                <option value="<?=$atasan['username'];?>" <?= ($atasan['username'] == $_SESSION['sppd']['head_dept']) ? 'selected' : ''; ?>><?=$atasan['person_name'];?></option>
                                <?php endforeach;?>
                                <?php foreach(list_user_in_head_department($_SESSION['sppd']['department_id']) as $head):?>
                                <option value="<?=$head['username'];?>" <?= ($head['username'] == $_SESSION['sppd']['head_dept']) ? 'selected' : ''; ?>><?=$head['person_name'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="notes">Supervisor / Atasan</label>
                        </div>

                        <?php if (isset($_SESSION['sppd']['edit'])) : ?>
                        <div class="form-group" style="padding-top: 25px;">
                            <select disabled name="spd_id" id="spd_id" data-placeholder="Pilih SPD" class="form-control select2" data-source="<?= site_url($module['route'] . '/set_spd_id'); ?>" required>
                                <option></option>
                                <?php foreach(all_spd() as $spd):?>
                                <option data-spd-number="<?=$spd['document_number'];?>" value="<?=$spd['id'];?>" <?= ($spd['id'] == $_SESSION['sppd']['spd_id']) ? 'selected' : ''; ?>><?=$spd['document_number'];?> | <?=$spd['person_name'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="notes">SPD No</label>
                        </div>
                        <?php else:?>
                        <div class="form-group" style="padding-top: 25px;">
                            <select name="spd_id" id="spd_id" data-placeholder="Pilih SPD" class="form-control select2" data-source="<?= site_url($module['route'] . '/set_spd_id'); ?>" required>
                                <option></option>
                                <?php foreach(available_spd() as $spd):?>
                                <option data-spd-number="<?=$spd['document_number'];?>" value="<?=$spd['id'];?>" <?= ($spd['id'] == $_SESSION['sppd']['spd_id']) ? 'selected' : ''; ?>><?=$spd['document_number'];?> | <?=$spd['person_name'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="notes">SPD No</label>
                        </div>
                        <?php endif;?>

                        <div class="form-group" style="padding-top: 25px;">
                            <select disabled name="person_in_charge" id="person_in_charge" class="form-control select2" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_person_in_charge'); ?>" required>
                                <option></option>
                                <?php foreach(available_employee($_SESSION['sppd']['department_id']) as $user):?>
                                <option data-identity-number="<?=$user['identity_number'];?>" data-phone-number="<?=$user['phone_number'];?>" data-position="<?=$user['position'];?>" value="<?=$user['employee_number'];?>" <?= ($user['employee_number'] == $_SESSION['sppd']['person_in_charge']) ? 'selected' : ''; ?>><?=$user['name'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="person_in_charge">Name Person in Charge</label>
                        </div>

                        <div class="form-group" style="padding-top: 25px;">
                            <select disabled name="occupation" id="occupation" class="form-control select2" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_occupation'); ?>" required>
                                <option></option>
                                <?php foreach(occupation_list() as $occupation):?>
                                <option value="<?=$occupation['position'];?>" <?= ($occupation['position'] == $_SESSION['sppd']['occupation']) ? 'selected' : ''; ?>><?=$occupation['position'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="occupation">Occupation / Jabatan</label>
                        </div>                 
                    </div>

                    <div class="col-sm-12 col-lg-8">
                        <div class="row">
                            <div class="col-sm-6 col-lg-6">
                                    <div class="form-group" style="padding-top: 25px;">
                                    <select disabled name="tujuan_perjalanan_dinas" id="tujuan_perjalanan_dinas" class="form-control select2" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_destination'); ?>" data-placeholder="Select Destination" required>
                                        <option></option>
                                        <?php foreach(destination_list() as $destination):?>
                                        <option value="<?=$destination['id'];?>" <?= ($destination['id'] == $_SESSION['sppd']['business_trip_destination_id']) ? 'selected' : ''; ?>><?=$destination['business_trip_destination'];?></option>
                                        <?php endforeach;?>
                                    </select>
                                    <label for="tujuan_perjalanan_dinas">To / Kota Tujuan</label>
                                </div> 

                                <div class="form-group">
                                    <textarea name="notes" id="notes" class="form-control" rows="4" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_notes'); ?>" readonly><?= $_SESSION['sppd']['notes']; ?></textarea>
                                    <label for="notes">Purpose of Travel on Duty / Maksud perjalanan dinas</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-lg-4">
                                <div class="form-group">
                                    <input type="hidden" name="start_date" id="start_date" class="form-control" value="<?= $_SESSION['sppd']['start_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_start_date'); ?>" required>
                                    <input type="hidden" name="end_date" id="end_date" class="form-control" value="<?= $_SESSION['sppd']['end_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_end_date'); ?>" required>
                                    <input type="text" name="dateline" id="dateline" data-provide="daterange" class="form-control" value="<?= $_SESSION['sppd']['dateline']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_dateline'); ?>" required readonly>
                                    <label for="dateline">Date</label>
                                </div>
                                <input type="text" name="dateline" id="dateline" data-provide="daterange" class="hide form-control" value="<?= $_SESSION['business_trip']['dateline']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_dateline'); ?>" required readonly>
                                <label for="dateline" class="hide">Date</label>

                                <div class="form-group">
                                    <input type="number" name="duration" id="duration" class="form-control" value="<?= $_SESSION['sppd']['duration']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_duration'); ?>" required readonly>
                                    <label for="duration">Duration</label>
                                </div>

                                <div class="form-group">
                                    <input type="number" name="advance" id="advance" class="form-control" value="<?= $_SESSION['sppd']['advance']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_duration'); ?>" required readonly>
                                    <label for="duration">Advance/Uang Muka Perjalanan Dinas</label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" name="real_start_date" id="real_start_date" data-provide="datepicker" class="form-control" value="<?= $_SESSION['sppd']['real_start_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_real_start_date'); ?>" required readonly>
                                            <label for="real_start_date">Realisation Startdate</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" name="real_end_date" id="real_end_date" data-provide="datepicker" class="form-control" value="<?= $_SESSION['sppd']['real_end_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_real_end_date'); ?>" required readonly>
                                            <label for="real_end_date">Realisation Enddate</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="number" name="real_duration" id="real_duration" class="form-control" value="<?= $_SESSION['sppd']['real_duration']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_real_duration'); ?>" required readonly>
                                    <label for="real_duration">Realisation Duration</label>
                                </div>      
                            </div>
                            <div class="col-sm-6 col-lg-4">
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 col-lg-4">
                        
                    </div>
                </div>
            </div>

            <div class="document-data table-responsive">
                <?php if (isset($_SESSION['sppd']['items'])) : ?>
                <table class="table table-hover table-striped" id="table-document">
                    <thead>
                        <tr>
                            <th colspan="5">Realization</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>Expense Name</th> 
                            <th style="text-align:center;" class="hide">Qty</th>
                            <th style="text-align:center;" class="hide">Amount Budget</th>
                            <th style="text-align:center;">Total Budget</th>
                            <th style="text-align:center;" class="hide">Amount Real</th>
                            <th style="text-align:center;">Total Real</th>
                            <th></th>
                        </tr>
                    </thead>
                <tbody>
                <?php foreach ($_SESSION['sppd']['items'] as $i => $items) : ?>
                    <tr id="row_<?= $i; ?>">
                        <td></td>
                        <td class="expense_name" style="font-weight:500;">
                            <input name="expense_name[]" type="text" class="sel_applied form-control input-sm" value="<?=$items['expense_name'];?>" readonly>
                            <input name="item_id[]" type="hidden" class="sel_applied form-control input-sm" value="<?=$items['id'];?>" readonly>
                            <input name="account_code[]" type="hidden" class="sel_applied form-control input-sm" value="<?=$items['account_code'];?>" readonly>
                        </td>
                        <td class="qty hide" style="font-weight:500;">
                            <input name="qty[]" type="text" class="sel_applied form-control input-sm" value="<?=$items['qty'];?>">
                            <input name="real_qty[]" type="text" class="sel_applied form-control input-sm real_qty" value="<?=$items['qty'];?>">
                        </td>
                        <td class="amount hide" style="font-weight:500;word-wrap:break-word;">
                            <input name="amount_budget[]" type="text" class="sel_applied form-control number input-sm" value="<?=$items['amount'];?>" readonly>
                        </td>
                        <td class="total" style="font-weight:500;">
                            <input name="total_budget[]" type="text" class="sel_applied form-control number input-sm" value="<?=$items['total'];?>" readonly>
                        </td>
                        <td class="amount hide" style="font-weight:500;word-wrap:break-word;">
                            <input name="amount[]" type="text" class="sel_applied form-control number input-sm" value="<?=$items['amount'];?>">
                        </td>
                        <td class="total" style="font-weight:500;">
                            <input name="total[]" type="text" class="sel_applied form-control number input-sm real_total" value="<?=($items['real_total']==0)?$items['total']:$items['real_total'];?>">
                        </td>
                        <td class="total" style="font-weight:500;">
                            <a style="margin-left: 15px;" href="<?= site_url($module['route'] . '/attachment_detail_spd/'.$items['id'].'/SPD-DETAIL'); ?>" onClick="return popup(this, 'attachment')" class="btn btn-xs btn-primary ink-reaction btn-tooltip">
                                <i class="md md-attach-file"></i>
                                <small class="top right">Manage Attachment</small>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>                    
                </tbody>
                <tfoot>
                    <tr>
                        <th></th> 
                        <th colspan="2">Total</th>                        
                        <th><span id="total_real"></span></th>
                        <th></th> 
                    </tr>
                </tfoot>
                </table>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-actionbar">
            <div class="card-actionbar-row">
                <div class="pull-left">
                    <button type="button" href="" onClick="addRow()" class="btn btn-primary ink-reaction pull-left hide">
                    Add
                    </button>

                    <a style="margin-left: 15px;" href="<?= site_url($module['route'] . '/attachment'); ?>" onClick="return popup(this, 'attachment')" class="btn btn-primary ink-reaction">
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
            <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?= site_url($module['route'] . '/save'); ?>">
                <i class="md md-save"></i>
                <small class="top right">Save Document</small>
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
            <td class="remarks item-list">
                <input type="text" name="expense_name[]" class="form-control">
            </td>
            <td class="value item-list">
                <input type="text" name="amount[]" class="form-control">
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

    function addRow() {
        var row_payment = $('.table-row-item tbody').html();
        var el = $(row_payment);
        $('#table-document tbody').append(el);
        $('#table-document tbody tr:last').find('input[name="amount[]"]').number(true, 2, '.', ',');

        btn_row_delete_item();
    }

    function btn_row_delete_item() {
        $('.btn-row-delete-item').click(function () {
            $(this).parents('tr').remove();
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
            format: 'yyyy-mm-dd',
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

        $('#real_start_date').on('change', function() {
            getDuration();
        });

        $('#real_end_date').on('change', function() {
            getDuration();
        });

        function getDuration(){
            var start_date_val = $('#real_start_date').val();
            var end_date_val = $('#real_end_date').val();
            console.log(start_date_val);
            console.log(end_date_val);

            if((start_date_val!='' && end_date_val!='')){
                var start_date = new Date(start_date_val);
                var end_date = new Date(end_date_val);
                // To calculate the time difference of two dates
                var Difference_In_Time = end_date.getTime() - start_date.getTime();
                
                // To calculate the no. of days between two dates
                var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);

                console.log(start_date);
                console.log(end_date);
                console.log(Difference_In_Days+1);

                var real_duration = Difference_In_Days+1;

                $('#real_duration').val(real_duration).trigger('change');
                $('[name="real_qty[]"]').val(real_duration).trigger('change');
            }
        }

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
                        window.location.href = '<?= site_url($module['route']); ?>';
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

            $.get(url, {
                data: val
            });
        });

        $('#spd_id').on('change', function() {
            var prev = $(this).data('val');
            var current = $(this).val();
            var url = $(this).data('source');

            if (prev != '') {
                var conf = confirm("Changing SPD NO will remove the items that have been added. Continue?");

                if (conf == false) {
                return false;
                }
            }

            window.location.href = url + '/' + current;
        });

        $("#table-document").on("change", ".real_total", function() {
            // console.log('test');
            sum_total_real();

        });

        $("#real_duration").on("change", function() {
            recount_total_real();

        });

        sum_total_real()

        function sum_total_real() {
            var sum = 0
            $('[name="total[]"]').each(function (key, val) {
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
            $("#total_real").html(currency);
        }

        function recount_total_real() {
            var sum = 0
            $('[name="real_qty[]"]').each(function (key, val) {
                var val_qty = $(this).val();
                var amount_budget = $(this).parents('td').siblings('td.amount').children('input').val();
                var real_total_input = $(this).parents('td').siblings('td.total').children('input');
                
                if(val_qty!=''){
                    console.log(val_qty);
                    var real_total = parseFloat(val_qty)*parseFloat(amount_budget);                    
                    real_total_input.val(real_total);
                }
                
            });
            sum_total_real()
        }
    });
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
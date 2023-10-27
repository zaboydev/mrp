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
                                    <input type="text" name="document_number" id="document_number" class="form-control" maxlength="6" value="<?= $_SESSION['business_trip']['document_number']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_doc_number'); ?>" required>
                                    <label for="document_number">Document No.</label>
                                </div>
                                <span class="input-group-addon"><?= $_SESSION['business_trip']['format_number']; ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="text" name="date" id="date" data-provide="datepicker" data-date-format="dd-mm-yyyy" class="form-control" value="<?= $_SESSION['business_trip']['date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_received_date'); ?>" required>
                            <label for="date">Date</label>
                        </div>

                        <div class="form-group">
                            <select name="with_po" id="with_po" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_head_dept'); ?>" required>
                                <option></option>
                                <?php foreach(list_user_in_head_department($_SESSION['business_trip']['department_id']) as $head):?>
                                <option value="<?=$head['username'];?>" <?= ($head['username'] == $_SESSION['business_trip']['head_dept']) ? 'selected' : ''; ?>><?=$head['person_name'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="notes">Supervisor / Atasan</label>
                        </div>

                        <div class="form-group" style="padding-top: 25px;">
                            <select name="person_in_charge" id="person_in_charge" class="form-control select2" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_person_in_charge'); ?>" required>
                                <option></option>
                                <?php foreach(available_employee($_SESSION['business_trip']['department_id']) as $user):?>
                                <option data-identity-number="<?=$user['identity_number'];?>" data-phone-number="<?=$user['phone_number'];?>" data-position="<?=$user['position'];?>" value="<?=$user['employee_number'];?>" <?= ($user['employee_number'] == $_SESSION['business_trip']['person_in_charge']) ? 'selected' : ''; ?>><?=$user['name'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="person_in_charge">Name Person in Charge</label>
                        </div>

                        <div class="form-group" style="padding-top: 25px;">
                            <select name="occupation" id="occupation" class="form-control select2" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_occupation'); ?>" required>
                                <option></option>
                                <?php foreach(occupation_list() as $occupation):?>
                                <option value="<?=$occupation['position'];?>" <?= ($occupation['position'] == $_SESSION['business_trip']['occupation']) ? 'selected' : ''; ?>><?=$occupation['position'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="occupation">Occupation / Jabatan</label>
                        </div>
                        
                        <div class="form-group">
                            <input type="text" name="id_number" id="id_number" class="form-control" value="<?= $_SESSION['business_trip']['id_number']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_id_number'); ?>" required>
                            <label for="id_number">ID. Number / No. Identitas</label>
                        </div> 
                        
                        <div class="form-group">
                            <input type="text" name="phone_number" id="phone_number" class="form-control" value="<?= $_SESSION['business_trip']['phone_number']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_phone_number'); ?>" required>
                            <label for="phone_number">Phone Number / No. HP</label>
                        </div>                    
                    </div>

                    <div class="col-sm-12 col-lg-4">
                        <div class="form-group" style="padding-top: 25px;">
                            <select name="from_base" id="from_base" class="form-control select2" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_from_base'); ?>" data-placeholder="Select Base" required>
                                <option></option>
                                <?php foreach(available_warehouses() as $warehouse):?>
                                <option value="<?=$warehouse;?>" <?= ($warehouse == $_SESSION['business_trip']['from_base']) ? 'selected' : ''; ?>><?=$warehouse;?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="from">From / Kota Asal</label>
                        </div>

                        <div class="form-group" style="padding-top: 25px;">
                            <select name="transportation" id="transportation" class="form-control select2" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_transportation'); ?>" data-placeholder="Select Transport" required>
                                <option></option>
                                <?php foreach(transportation_list() as $transportation):?>
                                <option value="<?=$transportation['transportation'];?>" <?= ($transportation['transportation'] == $_SESSION['business_trip']['transportation']) ? 'selected' : ''; ?>><?=$transportation['transportation'];?></option>
                                <?php endforeach;?>
                            </select>
                            <input type="hidden" name="transportation" id="transportationinput" class="form-control" value="<?= $_SESSION['business_trip']['transportation']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_transportation'); ?>" required>
                            <label for="transportation">Transportation / Jenis Transportasi</label>
                        </div>  

                        <div class="form-group">
                            <textarea name="remarks_transport" id="remarks_transport" class="form-control" rows="2" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_remarks_transport'); ?>"><?= $_SESSION['business_trip']['remarks_transport']; ?></textarea>
                            <label for="remarks_transport">Remarks Transport</label>
                        </div>

                        <div class="form-group" style="padding-top: 25px;">
                            <select name="tujuan_perjalanan_dinas" id="tujuan_perjalanan_dinas" class="form-control select2" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_destination'); ?>" data-placeholder="Select Destination" required>
                                <option></option>
                                <?php foreach(destination_list() as $destination):?>
                                <option value="<?=$destination['id'];?>" <?= ($destination['id'] == $_SESSION['business_trip']['business_trip_destination_id']) ? 'selected' : ''; ?>><?=$destination['business_trip_destination'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="tujuan_perjalanan_dinas">To / Kota Tujuan</label>
                        </div> 
                        
                        <!-- <div class="form-group"> -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="start_date" id="start_date" data-provide="datepicker" class="form-control" value="<?= $_SESSION['business_trip']['start_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_start_date'); ?>" required readonly>
                                        <label for="start_date">Startdate</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="end_date" id="end_date" data-provide="datepicker" class="form-control" value="<?= $_SESSION['business_trip']['end_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_end_date'); ?>" required readonly>
                                        <label for="end_date">Enddate</label>
                                    </div>
                                </div>
                            </div>                            
                            <input type="text" name="dateline" id="dateline" data-provide="daterange" class="hide form-control" value="<?= $_SESSION['business_trip']['dateline']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_dateline'); ?>" required readonly>
                            <label for="dateline" class="hide">Date</label>
                        <!-- </div> -->

                        <div class="form-group">
                            <input type="number" name="duration" id="duration" class="form-control" value="<?= $_SESSION['business_trip']['duration']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_duration'); ?>" required>
                            <label for="duration">Duration</label>
                        </div>
                    </div>

                    <div class="col-sm-12 col-lg-4">
                        <div class="form-group">
                            <textarea name="notes" id="notes" class="form-control" rows="4" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_notes'); ?>"><?= $_SESSION['business_trip']['notes']; ?></textarea>
                            <label for="notes">Purpose of Travel on Duty / Maksud perjalanan dinas</label>
                        </div>

                        <div class="form-group">
                            <textarea name="command_by" id="command_by" class="form-control" rows="4" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_command_by'); ?>"><?= $_SESSION['business_trip']['command_by']; ?></textarea>
                            <label for="notes">Keterangan Perjalanan Dinas</label>
                            
                            <?php if(isset($_SESSION['business_trip']['additional_notes'])):?>
                            <span class="input-group-addon" style="text-align:left;"><?= $_SESSION['business_trip']['additional_notes']; ?></span>
                            <?php endif;?>
                        </div>
                        
                        <div class="form-group">
                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="document-data table-responsive">
                <table class="table table-hover table-striped hide" id="table-document">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Expense Name</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                <tbody>
                    
                </tbody>
                </table>
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

        $('#start_date').on('change', function() {
            getDuration();
        });

        $('#end_date').on('change', function() {
            getDuration();
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

        function getDuration(){
            var start_date_val = $('#start_date').val();
            var end_date_val = $('#end_date').val();

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

                $('#duration').val(Difference_In_Days+1).trigger('change');
                // $('#start_date').val(picker.startDate.format('YYYY-MM-DD')).trigger('change');
                // $('#end_date').val(picker.endDate.format('YYYY-MM-DD')).trigger('change');
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

        $('#person_in_charge').change(function () {
            var employee_number = $('#person_in_charge').val();                        
            var position = $('#person_in_charge option:selected').data('position');    
            var phone_number = $('#person_in_charge option:selected').data('phone-number');    
            var identity_number = $('#person_in_charge option:selected').data('identity-number');  
            $('#occupation').val(position).trigger('change'); 
            $('#phone_number').val(phone_number).trigger('change'); 
            $('#id_number').val(identity_number).trigger('change');
        });
    });
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<style>
    
</style>
<section class="has-actions style-default">
    <div class="section-body">
        <?= form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form_movement_part')); ?>
        <div class="card">
            <div class="card-head style-primary-dark">
                <header><?= PAGE_TITLE; ?> </header>
            </div>
            <div class="card-body no-padding">
                <?php
                if ($this->session->flashdata('alert'))
                    render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
                ?>

                <div class="document-header force-padding">                
                    <div class="row">
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group">
                                <input type="text" name="aircraft" id="aircraft" class="form-control" value="<?= $_SESSION['component']['aircraft_code']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_installation_by'); ?>" readonly>
                                <label for="aircraft">Aircraft</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="document-data table-responsive">
                    <table class="table table-hover table-striped" id="table-document">
                    <thead>
                        <tr>
                            <th rowspan="2" style="text-align:center;">P/N</th>
                            <th rowspan="2" style="text-align:center;">S/N</th>
                            <th rowspan="2" style="text-align:center;">Alt. P/N</th>
                            <th rowspan="2" style="text-align:center;">Description</th>
                            <th rowspan="2" style="text-align:center;">Interval</th>
                            <th colspan="4" style="text-align:center;">Installation</th>
                            <th colspan="2" style="text-align:center;">Next Due</th>
                            <th rowspan="2" style="text-align:center;">Remarks</th>
                            <th rowspan="2" style="text-align:center;"></th>
                        </tr>
                        <tr>  
                            <th style="text-align:center;">Date</th>
                            <th style="text-align:center;">AF TSN</th>
                            <th style="text-align:center;">PART TSN</th>
                            <th style="text-align:center;">PART TSO</th>
                            <th style="text-align:center;">Date</th>
                            <th style="text-align:center;">Hour</th>
                        </tr> 
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['component']['items'] as $i => $items) : ?>
                            <tr id="row_<?= $i; ?>">
                                <td>
                                    <a href="<?= site_url($module['route'] . '/del_item/' . $i); ?>" class="btn btn-icon-toggle btn-danger btn-sm btn_delete_document_item">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <a class="btn btn-icon-toggle btn-info btn-sm btn_edit_document_item" data-todo='{"todo":<?= $i; ?>}'>
                                        <i class="fa fa-edit"></i>
                                    </a>                                
                                </td>
                                <td>
                                    <?= $items['part_number']; ?>
                                </td>
                                <td>
                                    <?= $items['serial_number']; ?>
                                </td>
                                <td>
                                    <?= $items['alternate_part_number']; ?>
                                </td>
                                <td>
                                    <?= $items['description']; ?>
                                </td>
                                <td>
                                    <?= $items['interval']; ?> <?= $items['interval_satuan']; ?>
                                </td>
                                <td>
                                    <?= print_date($items['installation_date']); ?>
                                </td>
                                <td>
                                    <?= $items['af_tsn']; ?>
                                </td>
                                <td>
                                    <?= $items['equip_tsn']; ?>
                                </td>
                                <td>
                                    <?= $items['tso']; ?>
                                </td>
                                <td>
                                    <?= print_date($items['next_due_date']); ?>
                                </td>
                                <td> 
                                    <?= $items['next_due_hour']; ?>
                                </td>
                                <td>
                                    <?= $items['remarks']; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-actionbar">
                <div class="card-actionbar-row">
                    <div class="pull-left">
                        <a href="#modal-add-item" data-toggle="modal" data-target="#modal-add-item" class="btn btn-primary ink-reaction btn-open-offcanvas pull-left">
                            Add Item
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

    <div id="modal-add-item" class="modal fade">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header style-primary-dark">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modal-add-item-label">Add Component Aircraft</h4>
                </div>

                <?= form_open(site_url($module['route'] . '/add_item'), array(
                    'autocomplete' => 'off',
                    'id'    => 'ajax-form-create-document',
                    'class' => 'form form-validate ui-front',
                    'role'  => 'form'
                )); ?>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-12 col-lg-12">
                            <div class="row">
                                <div class="col-sm-12 col-lg-6">
                                    <fieldset>
                                        <legend>Component <?=$_SESSION['component']['type']?></legend>

                                        <div class="form-group">
                                            <input type="text" name="part_number" id="part_number" class="form-control input-sm input-autocomplete" data-source="<?= site_url($module['route'] . '/search_items_by_part_number/'); ?>" required>
                                            <label for="part_number">Part Number</label>
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="description" id="description" data-tag-name="item_description" data-search-for="item_description" class="form-control input-sm" required>
                                            <label for="description">Description</label>
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="serial_number" id="serial_number" class="form-control input-sm input-autocomplete" data-source="<?= site_url($module['route'] . '/search_items_by_serial/'); ?>">
                                            <label for="serial_number">Serial Number</label>
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="alternate_part_number" id="alternate_part_number" data-tag-name="alternate_part_number" data-source="<?= site_url($modules['ajax']['route'] . '/json_alternate_part_number/' . $_SESSION['receipt']['category']); ?>" class="form-control input-sm">
                                            <label for="alternate_part_number">Alt. Part Number</label>
                                        </div>

                                        <div class="form-group" style="padding-top: 20px;">
                                            <select name="group" id="group" data-tag-name="group" class="form-control input-sm select2" required style="width: 100%">
                                                <option>-- Select One --</option>
                                                <?php foreach (available_item_groups_2(config_item('auth_inventory')) as $group) : ?>
                                                <option value="<?= $group['group']; ?>">
                                                <?= $group['group']; ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label for="group">Item Group</label>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="unit" id="unit" class="form-control input-sm input-autocomplete" data-source="<?= site_url($modules['ajax']['route'] . '/search_item_units/'); ?>" required>
                                            <label for="unit">Unit</label>
                                        </div>

                                        <div class="form-group">
                                            <input type="number" name="interval" id="interval" class="form-control input-sm input-autocomplete" value="">
                                            <label for="interval">Interval</label>
                                        </div>

                                        <div class="form-group">
                                            <select name="interval_satuan" id="interval_satuan" data-tag-name="interval_satuan" class="form-control input-sm">
                                                <option value="">-- Select One --</option>
                                                <option value="FH">FH</option>
                                                <option value="MTHS">MTHS</option>
                                            </select>
                                            <label for="interval_satuan">Interval Satuan</label>
                                        </div>

                                    </fieldset>
                                </div>
                                <div class="col-sm-12 col-lg-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-lg-12">
                                            <fieldset>
                                                <legend>Instalation Data</legend>

                                                <div class="form-group">
                                                    <input type="date" name="installation_date" id="installation_date" class="form-control input-sm" required>
                                                    <label for="installation_date">Installation Date</label>
                                                </div>

                                                <div class="form-group">
                                                    <input type="number" name="af_tsn" id="af_tsn" data-tag-name="af_tsn" data-search-for="af_tsn" class="form-control input-sm" required>
                                                    <label for="af_tsn">AF TSN</label>
                                                </div>

                                                <div class="form-group">
                                                    <input type="number" name="equip_tsn" id="equip_tsn" class="form-control input-sm">
                                                    <label for="equip_tsn">Part TSN</label>
                                                </div>

                                                <div class="form-group">
                                                    <input type="number" name="tso" id="tso" data-tag-name="tso" class="form-control input-sm">
                                                    <label for="tso">Part TSO</label>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 col-lg-12">
                                            <fieldset>
                                                <legend>Next Due</legend>

                                                <div class="form-group">
                                                    <input type="date" name="next_due_date" id="next_due_date" class="form-control input-sm">
                                                    <label for="next_due_date">Next Due Date</label>
                                                </div>

                                                <div class="form-group">
                                                    <input type="number" name="next_due_hour" id="next_due_hour" data-tag-name="next_due_hour" data-search-for="next_due_hour" class="form-control input-sm">
                                                    <label for="next_due_hour">Next Due Hour</label>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 col-lg-12">
                                            <fieldset>
                                                <legend>Additional</legend>
                                                <div class="form-group">
                                                    <textarea name="remarks" id="remarks" data-tag-name="remarks" class="form-control input-sm"></textarea>
                                                    <label for="remarks">Remarks</label>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="modal-add-item-submit" class="btn btn-primary btn-create ink-reaction">
                        Add Item
                    </button>
                </div>

                <?= form_close(); ?>
            </div>
        </div>
    </div>

    <div class="section-action style-default-bright">
        <div class="section-floating-action-row">
            <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?= site_url($module['route'] . '/save_component'); ?>">
                <i class="md md-save"></i>
                <small class="top right">Save Document</small>
            </a>
        </div>
    </div>
</section>
<?php endblock() ?>

<?php startblock('page_modals') ?>
<?php $this->load->view('material/templates/modal_fs') ?>

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
    <script>
        Pace.on('start', function() {
            $('.progress-overlay').show();
        });

        Pace.on('done', function() {
            $('.progress-overlay').hide();
        });

        $('.select2').select2({
            // theme: "bootstrap",
        });

        function addRow() {
            var row_payment = $('.table-row-item tbody').html();
            var el = $(row_payment);
            $('#table-document tbody').append(el);
            $('#table-document tbody tr:last').find('select[name="aircraft_register[]"]').select2();
            $('#table-document tbody tr:last').find('select[name="group[]"]').select2();
            $('#table-document tbody tr:last').find('select[name="component_remove_id[]"]').select2();
            $('#table-document tbody tr:last').find('select[name="category[]"]').select2();

            btn_row_delete_item();
            set_aircraft_register();
            set_group();
            
            // setAddValue();
        }

        // function set_aircraft_register() {
        $('[name="aircraft_register"]').change(function () {
            var aircraft_register = $(this).val();
            var group = $('[name="group_part"]').val();

            if(aircraft_register!='' && group!=''){
                component_aircraft(aircraft_register,group);
            }
        });
        // }

        // function set_group() {
        $('[name="group_part"]').change(function () {
            var group = $(this).val();
            var aircraft_register = $('[name="aircraft_register"]').val();
            if(aircraft_register!='' && group!=''){
                component_aircraft(aircraft_register,group);
            }
        });
        // }

        $('[name="component_remove_id"]').change(function () {
            var part_number = $(this).find(":selected").data("part-number");
            var serial_number = $(this).find(":selected").data("serial-number");
            var alternate_part_number = $(this).find(":selected").data("alternate-part-number");
            var description = $(this).find(":selected").data("description");

            $('[name="remove_part_number"]').val(part_number);
            $('[name="remove_serial_number"]').val(serial_number);
            $('[name="remove_alternate_part_number"]').val(alternate_part_number);
            $('[name="remove_description"]').val(description);
        });

        function btn_row_delete_item() {
            $('.btn-row-delete-item').click(function () {
                $(this).parents('tr').remove();
                
            });
        }

        function component_aircraft(aircraft_register,group) {
            var component_remove = $('[name="component_remove_id"]');
            // var component_remove = ini.parents('td').siblings('td.component_remove_id').children('select[name="component_remove_id[]"]');
            
            var data_send = {
                aircraft_code: aircraft_register,
                type: group
            };
            
            console.log(data_send);
            $.ajax({
                url: "<?= site_url($module['route'] . '/search_component_aircraft'); ?>",
                type: 'POST',
                data: data_send,
                dataType: "json",
                success: function (resource) {
                    console.log(resource);
                    component_remove.html('');
                    component_remove.append('<option value="">--Select Component--</option>');
                    $.each(resource, function(i, item) {
                        // if(head_dept==item.id){
                        //     var text = '<option value="' +item.id+'" selected> P/N : ' +item.part_number+'</option>';
                        // }else{
                            var text = '<option value="' +item.id+'" data-part-number="'+item.part_number+'" data-serial-number="'+item.serial_number+'" data-description="'+item.description+'" data-alternate-part-number="'+item.alternate_part_number+'"> P/N : ' +item.part_number+'</option>';
                        // }            
                        component_remove.append(text);
                    });
                }
            });
        }

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

            $('#btn-submit-document').on('click', function(e){
                e.preventDefault();
                $('#btn-submit-document').attr('disabled', true);

                var url = $(this).attr('href');
                var frm = $('#form_movement_part');

                $.post(url, frm.serialize(), function(data){
                var obj = $.parseJSON(data);

                if ( obj.success == false ){
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
                    window.location.href = '<?= $page['route']; ?>';
                    }, 5000);
                }

                $('#btn-submit-document').attr('disabled', false);
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

            $.ajax({
                url: $('input[id="serial_number"]').data('source'),
                dataType: "json",
                success: function(resource) {
                    $('input[id="serial_number"]').autocomplete({
                        autoFocus: true,
                        minLength: 1,

                        source: function(request, response) {
                            var results = $.ui.autocomplete.filter(resource, request.term);
                            response(results.slice(0, 5));
                        },

                        focus: function(event, ui) {
                            return false;
                        },

                        select: function(event, ui) {
                            $('input[id="serial_number"]').val(ui.item.serial_number);
                            return false;
                        }
                    })
                    .data("ui-autocomplete")._renderItem = function(ul, item) {
                        $(ul).addClass('list divider-full-bleed');

                        return $("<li class='tile'>")
                        .append('<a class="tile-content ink-reaction"><div class="tile-text">' + item.label + '</div></a>')
                        .appendTo(ul);
                    };
                }
            });

            $.ajax({
                url: $('input[id="part_number"]').data('source'),
                dataType: "json",
                success: function(resource) {
                    $('input[id="part_number"]').autocomplete({
                        autoFocus: true,
                        minLength: 1,

                        source: function(request, response) {
                            var results = $.ui.autocomplete.filter(resource, request.term);
                            response(results.slice(0, 5));
                        },

                        focus: function(event, ui) {
                            return false;
                        },

                        select: function(event, ui) {
                            $('input[id="part_number"]').val(ui.item.part_number);
                            $('input[id="alternate_part_number"]').val(ui.item.alternate_part_number);
                            $('input[id="description"]').val(ui.item.description);
                            $('select[id="group"]').val(ui.item.group).trigger('change');
                            $('input[id="unit"]').val(ui.item.unit);
                            $('input[id="minimum_quantity"]').val(ui.item.minimum_quantity);
                            $('input[id="serial_number"]').val(ui.item.serial_number);

                            return false;
                        }
                    })
                    .data("ui-autocomplete")._renderItem = function(ul, item) {
                        $(ul).addClass('list divider-full-bleed');

                        return $("<li class='tile'>")
                        .append('<a class="tile-content ink-reaction"><div class="tile-text">' + item.label + '</div></a>')
                        .appendTo(ul);
                    };
                }
            });
        });

        $.ajax({
            url: $('input[id="unit"]').data('source'),
            dataType: "json",
            success: function(data) {
                $('input[id="unit"]').autocomplete({
                    source: function(request, response) {
                        var results = $.ui.autocomplete.filter(data, request.term);
                        response(results.slice(0, 10));
                    }
                });
            }
        });
    </script>

    <?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
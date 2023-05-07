<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<style>
    .part-on {
        color: #0aa89e
    }

    .part-off {
        color: #f44336
    }
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
                        <div class="col-sm-6 col-lg-5">
                            <h4>Robbing Item Info</h4>

                            <div class="">
                                <dl class="dl-inline">
                                    <dt>
                                        Serial Number
                                    </dt>
                                    <dd>
                                        <?=print_string($entity['serial_number']);?>
                                    </dd>

                                    <dt>
                                        Part Number
                                    </dt>
                                    <dd>
                                        <?=print_string($entity['part_number']);?>
                                    </dd>

                                    <dt>
                                        Description
                                    </dt>
                                    <dd>
                                        <?=print_string($entity['description']);?>
                                    </dd> 
                                    
                                    <dt>
                                        TSN
                                    </dt>
                                    <dd>
                                        <?=print_string($entity['remove_tsn']);?>
                                    </dd> 

                                    <dt>
                                        TSO
                                    </dt>
                                    <dd>
                                        <?=print_string($entity['remove_tso']);?>
                                    </dd>

                                    <dt>
                                        Date Remove
                                    </dt>
                                    <dd>
                                        <?=print_date($entity['remove_date'], 'd M Y');?>
                                    </dd>
                                    
                                    <dt>
                                        A/C Reg
                                    </dt>
                                    <dd>
                                        <?=print_string($entity['remove_aircraft_register']);?>
                                    </dd>

                                    <dt>
                                        A/C Type
                                    </dt>
                                    <dd>
                                        <?=print_string($entity['remove_aircraft_type']);?>
                                    </dd>

                                    <dt>
                                        A/C Base
                                    </dt>
                                    <dd>
                                        <?=print_string($entity['remove_aircraft_base']);?>
                                    </dd>
                                </dl>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-7">
                        <h4>Install Form</h4>

                        <div class="well well-lg">
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <input type="text" name="install_date" id="install_date" class="form-control" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                        <label for="install_date">Install Date</label>
                                    </div>

                                    <div class="form-group" style="padding-top: 20px;">
                                        <select name="install_aircraft_register" id="install_aircraft_register" data-tag-name="install_aircraft_register" class="form-control input-sm select2" style="width: 100%" required>
                                            <option value="">-- SELECT Aircraft --</option>
                                            <?php foreach (pesawat() as $pesawat) : ?>
                                            <option value="<?= $pesawat; ?>"><?= $pesawat; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="install_aircraft_register">A/C Reg</label>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="install_pic" id="install_pic" class="form-control">
                                        <label for="install_pic">PIC</label>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="form-group">
                                    <textarea name="remarks" id="remarks" class="form-control" rows="5"></textarea>
                                    <label for="remarks">Remrks</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>             
                    <div class="row">
                        
                    </div>
                </div>
            </div>
            <div class="card-actionbar">
                <div class="card-actionbar-row">
                    <div class="pull-left">
                                                         
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
            <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?= site_url($module['route'] . '/install_save/'. $entity['id']); ?>">
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
            source_item_id();
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

        $('[name="source"]').change(function () {
            source_item_id();
        });

        $('[name="source_item_id"]').change(function () {
            var part_number = $(this).find(":selected").data("part-number");
            var serial_number = $(this).find(":selected").data("serial-number");
            var alternate_part_number = $(this).find(":selected").data("alternate-part-number");
            var description = $(this).find(":selected").data("description");
            var document_number = $(this).find(":selected").data("document-number");

            $('[name="install_part_number"]').val(part_number);
            $('[name="install_serial_number"]').val(serial_number);
            $('[name="install_alternate_part_number"]').val(alternate_part_number);
            $('[name="install_description"]').val(description);
            $('[name="issuance_document_number"]').val(document_number);
        });

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
                            var text = '<option value="' +item.id+'" data-part-number="'+item.part_number+'" data-serial-number="'+item.serial_number+'" data-description="'+item.description+'" data-alternate-part-number="'+item.alternate_part_number+'">'+item.description+' || P/N : ' +item.part_number+'</option>';
                        // }            
                        component_remove.append(text);
                    });
                }
            });
        }

        function source_item_id() {
            var source_item_id = $('[name="source_item_id"]');
            var source = $('[name="source"]').val();
            var aircraft = $('[name="aircraft_register"]').val();
            // var remove_part_number = $('[name="remove_part_number"]').val();

            var data_send = {
                source: source,
                aircraft: aircraft,
                // remove_part_number:remove_part_number
            };            
            console.log(data_send);
            if(source!='' && aircraft!=''){
                $.ajax({
                    url: "<?= site_url($module['route'] . '/search_item_by_source'); ?>",
                    type: 'POST',
                    data: data_send,
                    dataType: "json",
                    success: function (resource) {
                        console.log(resource);
                        source_item_id.html('');
                        source_item_id.append('<option value=""></option>');
                        if(source=='inventory'){
                            $.each(resource, function(i, item) {
                                var serial_number = (item.serial_number==null || item.serial_number=='')? 'N\A':item.serial_number;
                                // if(head_dept==item.id){
                                //     var text = '<option value="' +item.id+'" selected> P/N : ' +item.part_number+'</option>';
                                // }else{
                                    var text = '<option value="' +item.id+
                                    '" data-document-number="'+item.document_number+
                                    '" data-part-number="'+item.part_number+
                                    '" data-serial-number="'+item.serial_number+
                                    '" data-description="'+item.description+
                                    '" data-alternate-part-number="'+item.alternate_part_number+
                                    '" data-tsn="'+item.remove_tsn+
                                    '" data-tso="'+item.remove_tso+
                                    '">'+item.description+
                                    ' || P/N : ' +item.part_number+
                                    ' || S/N : ' +serial_number+
                                    ' || MS : ' +item.document_number+
                                    '</option>';
                                // }            
                                source_item_id.append(text);
                            });
                        }else if(source=='robbing'){
                            $.each(resource, function(i, item) {
                                var serial_number = (item.serial_number==null || item.serial_number=='')? 'N\A':item.serial_number;
                                // if(head_dept==item.id){
                                //     var text = '<option value="' +item.id+'" selected> P/N : ' +item.part_number+'</option>';
                                // }else{
                                    var text = '<option value="' +item.id+
                                    '" data-from-aircraft="'+item.aircraft_register+
                                    '" data-part-number="'+item.part_number+
                                    '" data-serial-number="'+item.serial_number+
                                    '" data-description="'+item.description+
                                    '" data-alternate-part-number="'+item.alternate_part_number+
                                    '">'+item.description+
                                    ' || P/N : ' +item.part_number+
                                    ' || S/N : ' +serial_number+
                                    ' || A/C : ' +item.remove_aircraft_register+
                                    '</option>';
                                // }            
                                source_item_id.append(text);
                            });
                        }
                        
                    }
                });
            }
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

            $('[data-provide="datepicker"]').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'yyyy-mm-dd',
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
                        window.location.href = '<?=site_url($module['route']);?>';
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

        });
    </script>

    <?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
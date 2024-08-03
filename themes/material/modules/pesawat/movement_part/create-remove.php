<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
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
                        
                    </div>
                </div>

                <div class="document-data table-responsive">
                    <table class="table table-hover table-striped" id="table-document">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="hide">Group</th>
                                <th class="middle-alignment">A/C Reg</th>
                                <th class="middle-alignment">Group Part</th>
                                <th class="middle-alignment">Date of AJLB</th>
                                <th class="middle-alignment">Part Off</th>
                                <th class="middle-alignment">Remove Date</th>
                                <th class="middle-alignment">Remove TSN</th>
                                <th class="middle-alignment">Remove TSO</th>
                                <th class="middle-alignment">PIC</th>
                                <th class="middle-alignment">Category</th>
                                <th class="middle-alignment">Status</th>
                                <th class="middle-alignment">Remark</th>
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
                        <button type="button" href="" onClick="addRow()" class="btn btn-primary ink-reaction">
                            Add
                        </button>                    
                    </div>

                    <a href="<?= site_url($module['route'] . '/discard'); ?>" class="btn btn-flat btn-danger ink-reaction">
                    Discard
                    </a>
                </div>
            </div>
        </div>
        <?= form_close(); ?>
    </div>
    <table class="table-row-item hide">
        <tbody>
            <tr>
                <td class="item-list">
                    <a  href="javascript:;" title="Delete" class="btn btn-icon-toggle btn-danger btn-xs btn-row-delete-item" data-tipe="delete"><i class="fa fa-trash"></i>
                    </a>                      
                </td>
                <td class="aircraft_register item-list">
                    <!-- <input type="text" name="account_code[]" class="form-control-payment"> -->
                    <select name="aircraft_register[]" class="form-control input-sm" style="width: 100%">
                        <option value="">-- SELECT Aircraft --</option>
                        <?php foreach (pesawat() as $pesawat) : ?>
                        <option value="<?= $pesawat; ?>"><?= $pesawat; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td class="group item-list">
                    <select name="group[]" class="form-control input-sm" style="width: 100%">
                        <option value="">-- SELECT Group --</option>
                        <?php foreach (config_item('component_type') as $category) : ?>
                        <option value="<?= $category; ?>"><?= ucwords($category); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>  
                <td class="date_of_ajlb item-list">
                    <input type="date" name="date_of_ajlb[]" class="form-control input-sm">
                </td>
                <td class="component_remove_id item-list">
                    <select name="component_remove_id[]" class="form-control input-sm" style="width: 100%" data-souce="">
                        <option value="">-- SELECT Part --</option>
                                        
                    </select>
                </td>   
                <td class="remove_date item-list">
                    <input type="date" name="remove_date[]" class="form-control input-sm">
                </td>
                <td class="remove_tsn item-list">
                    <input type="text" name="remove_tsn[]" class="form-control input-sm">
                </td>
                <td class="remove_tso item-list">
                    <input type="text" name="remove_tso[]" class="form-control input-sm">
                </td> 
                <td class="pic item-list">
                    <input type="text" name="pic[]" class="form-control input-sm">
                </td> 
                <td class="category item-list">
                    <select name="category[]" class="form-control input-sm" style="width: 100%">
                        <option value="">-- SELECT category --</option>
                        <option value="ROTABLE">ROTABLE</option>
                        <option value="CONSUMABLE">CONSUMABLE</option>
                        <option value="REPAIRABLE">REPAIRABLE</option>
                        <option value="ROBBING">ROBBING</option>
                    </select>
                </td>  
                <td class="status item-list">
                    <input type="text" name="status[]" class="form-control input-sm">
                </td>  
                <td class="remark item-list">
                    <input type="text" name="remark[]" class="form-control input-sm">
                </td>      
            </tr>
        </tbody>
    </table>

    <div class="section-action style-default-bright">
        <div class="section-floating-action-row">
            <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?= site_url($module['route'] . '/save'); ?>">
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
<?= html_script('vendors/select2-4.0.3/dist/js/select2.min.js') ?>
<?= html_script('vendors/select2-pmd/js/pmd-select2.js') ?>
<script>
    Pace.on('start', function() {
        $('.progress-overlay').show();
    });

    Pace.on('done', function() {
        $('.progress-overlay').hide();
    });

    $('.select2').select2({
        theme: "bootstrap",
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

    function set_aircraft_register() {
        $('[name="aircraft_register[]"]').change(function () {
            var ini = $(this);
            var aircraft_register = $(this).val();
            var group = $(this).parents('td').siblings('td.group').children('select[name="group[]"]').val();

            if(aircraft_register!='' && group!=''){
                component_aircraft(aircraft_register,group,ini);
            }
        });
    }

    function set_group() {
        $('[name="group[]"]').change(function () {
            var ini = $(this);
            var group = $(this).val();
            var aircraft_register = $(this).parents('td').siblings('td.aircraft_register').children('select[name="aircraft_register[]"]').val();
            if(aircraft_register!='' && group!=''){
                component_aircraft(aircraft_register,group,ini);
            }
        });
    }

    function btn_row_delete_item() {
        $('.btn-row-delete-item').click(function () {
            $(this).parents('tr').remove();
            
        });
    }

    function component_aircraft(aircraft_register,group,ini) {
        // var component_remove = $('#table-document tbody tr:last').find('select[name="component_remove_id[]"]');
        var component_remove = ini.parents('td').siblings('td.component_remove_id').children('select[name="component_remove_id[]"]');
        
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
                        var text = '<option value="' +item.id+'"> P/N : ' +item.part_number+' Desc : '+item.description+'</option>';
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
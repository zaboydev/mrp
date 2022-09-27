<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="has-actions style-default">
    <div class="section-body">
        <?= form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form_component_status')); ?>
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
                                <input type="text" name="status_date" id="status_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control" value="<?= $_SESSION['component_status']['status_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_status_date'); ?>" required readonly>
                                <label for="status_date">Status Date</label>
                            </div>
                            <div class="form-group">
                                <input type="text" name="prepared_by" id="prepared_by" class="form-control" value="<?= $_SESSION['component_status']['prepared_by']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_prepared_by'); ?>" required>
                                <label for="prepared_by">Prepared By</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group">
                                <select name="aircraft_id" id="aircraft_id" class="form-control" required data-source="<?= site_url($module['route'] . '/set_aircraft_id'); ?>">
                                    <option value=""></option>
                                    <?php foreach (available_aircrafts() as $aircraft) : ?>
                                    <option value="<?= $aircraft['id']; ?>" <?= ($aircraft['id']==$_SESSION['component_status']['aircraft_id'])?'selected':NULL;?>>
                                    <?= $aircraft['nama_pesawat']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="aircraft">Aircraft</label>
                            </div>
                            <div class="form-group">
                                <input type="text" name="base" class="form-control" value="<?= $_SESSION['component_status']['base']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_base'); ?>" readonly>
                                <label for="base">Base</label>
                            </div>

                            <div class="form-group">
                                <input type="number" name="tsn" id="tsn" class="form-control" value="<?= $_SESSION['component_status']['tsn']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_tsn'); ?>" required>
                                <label for="tsn">TSN</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group">
                                <textarea name="notes" id="notes" class="form-control" rows="4" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_component_status_notes'); ?>"><?= $_SESSION['component_status']['notes']; ?></textarea>
                                <label for="notes">Notes</label>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (isset($_SESSION['component_status']['items'])) : ?>
                <div class="document-data table-responsive">
                    <table class="table table-hover table-striped" id="table-document">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="hide">id</th>
                                <th class="hide">Group</th>
                                <th class="middle-alignment">Description</th>
                                <th class="middle-alignment">P/N</th>
                                <th class="middle-alignment">Alt. P/N</th>
                                <th class="middle-alignment">S/N</th>
                                <!-- <th>Qty</th> -->
                                <th class="hide">Unit</th>
                                <th class="middle-alignment">Installation Date</th>
                                <th class="middle-alignment" colspan="2">Interval (Hour & Date)</th>
                                <th class="middle-alignment">AF TSN</th>
                                <th class="middle-alignment">Equip TSN</th>
                                <th class="middle-alignment">TSO</th>
                                <th class="middle-alignment" colspan="2">Due At AF TSN (Hour & Date)</th>
                                <th class="middle-alignment" colspan="2">Remaining (Hour & Date)</th>
                                <th class="middle-alignment">Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="item-body">
                            <?php foreach ($_SESSION['component_status']['items'] as $i => $items) : ?>
                            <tr id="row_<?= $i; ?>">
                                <td width="1">

                                  <a href="<?= site_url($module['route'] . '/del_item/' . $i); ?>" class="btn btn-icon-toggle btn-danger btn-sm btn_delete_document_item">
                                      <i class="fa fa-trash"></i>
                                  </a>
                                
                                </td>
                                <td class="hide">
                                  <?= $items['item_id']; ?>
                                </td>
                                <td class="hide">
                                  <?= $items['group']; ?>
                                </td>
                                <td>
                                  <?= $items['description']; ?>
                                </td>
                                <td class="no-space">
                                  <?= $items['part_number']; ?>
                                </td>
                                <td class="no-space">
                                  <?= $items['alternate_part_number']; ?>
                                </td>
                                <td>
                                  <?= $items['serial_number']; ?>
                                </td>
                                <td class="hide">
                                  <?= number_format($items['quantity'], 2); ?>
                                </td>
                                <td class="hide">
                                <?= $items['unit']; ?>
                                </td>
                                <td class="hide">
                                  <?= $items['condition']; ?>
                                </td>
                                <td>
                                  <?= $items['installation_date']; ?>
                                </td>
                                <td class="interval">
                                <input type="hidden" name="items[<?=$items['id'];?>][aircraft_component_id]" value="<?=$items['id'];?>" class="form-control input-sm" required>
                                  <input type="text" name="items[<?=$items['id'];?>][interval]" value="" class="form-control input-sm" placeholder="hour" required>
                                </td>
                                <td class="interval_date">
                                    <input type="date" name="items[<?=$items['id'];?>][interval_date]" value="" class="form-control input-sm" required>
                                </td>
                                <td class="af_tsn"> 
                                  <input type="text" name="items[<?=$items['id'];?>][af_tsn]" value="" class="form-control input-sm" required>
                                </td>
                                <td class="equip_tsn">
                                  <input type="text" name="items[<?=$items['id'];?>][equip_tsn]" value="" class="form-control input-sm" required>
                                </td>
                                <td class="tso">
                                  <input type="text" name="items[<?=$items['id'];?>][tso]" value="" class="form-control input-sm" required>
                                </td>
                                <td class="due_at_af_tsn">
                                  <input type="text" name="items[<?=$items['id'];?>][due_at_af_tsn]" value="" placeholder="hour" class="form-control input-sm due_at_af_tsn_input" required>
                                </td>
                                <td class="due_at_af_tsn_date">
                                    <input type="date" name="items[<?=$items['id'];?>][due_at_af_tsn_date]" value="" class="form-control input-sm" required>
                                </td>
                                <td class="remaining">
                                  <input type="text" name="items[<?=$items['id'];?>][remaining]" value="" placeholder="hour" class="form-control input-sm" readonly>
                                </td>
                                <td class="remaining_date">
                                    <input type="date" name="items[<?=$items['id'];?>][remaining_date]" value="" class="form-control input-sm" required>
                                </td>
                                <td class="remarks">
                                    <select name="items[<?=$items['id'];?>][remarks]" class="form-control input-sm" required style="width: 100%">
                                        <option>-- Select One --</option>
                                        <option value="OVERHAUL">OVERHAUL</option>
                                        <option value="INSPECTION">INSPECTION</option>
                                        <option value="CHECK">CHECK</option>
                                        <option value="REPLACE">REPLACE</option>
                                        <option value="SWING">SWING</option>
                                    </select>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
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

        var startDate = new Date(<?= config_item('period_year'); ?>, <?= config_item('period_month'); ?> - 1, 1);
        var lastDate = new Date(<?= config_item('period_year'); ?>, <?= config_item('period_month'); ?>, 0);
        var last_publish = $('[name="opname_start_date"]').val();
        var today = new Date();
        today.setDate(today.getDate() - 2);
        $('[data-provide="datepicker"]').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd',
            // startDate: today,
            // endDate: last_opname
        });

        $('#expired_date,.tgl_nota').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd'
            //startDate: '0d'
        });

        $('#edit_expired_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd'
            //startDate: '0d'
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
            var frm = $('#form_component_status');

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

        $('#isi').data('rule-min', parseInt(1)).data('msg-min', 'min val 1');
        $('#edit_isi').data('rule-min', parseInt(1)).data('msg-min', 'min val 1');

        $('#search_purchase_order').on('click focus', function() {
            $.ajax({
                url: $('#search_purchase_order').data('source'),
                dataType: "json",
                success: function(resource) {
                    $('#search_purchase_order').autocomplete({
                        autoFocus: true,
                        minLength: 1,

                        source: function(request, response) {
                            var results = $.ui.autocomplete.filter(resource, request.term);
                            response(results.slice(0, 50));
                        },

                        focus: function(event, ui) {
                            return false;
                        },

                        select: function(event, ui) {
                            if (ui.item.default_currency == 'USD') {
                            var unit_value = parseFloat(ui.item.unit_price) * parseFloat(ui.item.exchange_rate);
                            } else {
                            var unit_value = parseFloat(ui.item.unit_price);
                            }

                            var source = $('#source').val();
                            // console.log(source);

                            $('#consignor').val(ui.item.vendor);
                            $('#serial_number').val(ui.item.serial_number);
                            $('#part_number').val(ui.item.part_number);
                            $('#description').val(ui.item.description);
                            $('#group').val(ui.item.group);
                            $('#condition').val(ui.item.condition);
                            $('#alternate_part_number').val(ui.item.alternate_part_number);
                            $('select[id="group"]').val(ui.item.group);
                            $('#received_quantity').val(parseFloat(ui.item.left_received_quantity));
                            $('#quantity_order').val(parseFloat(ui.item.left_received_quantity));
                            $('#unit').val(ui.item.unit);
                            $('#received_unit').val(ui.item.unit);
                            $('#unit_pakai').val(ui.item.unit_pakai);
                            $('#unit_used').val(ui.item.unit_pakai);
                            $('#received_unit_value').val(parseFloat(unit_value)); 
                            if (ui.item.default_currency != 'IDR'){
                            $('#received_unit_value_dollar').val(parseFloat(unit_value));
                            }else{
                            $('#received_unit_value_dollar').val(parseFloat(0));
                            }               
                            
                            $('#value_order').val(parseFloat(unit_value));
                            if(source=='purchase_order'){
                            $('#purchase_order_item_id').val(ui.item.id);
                            }else{
                            $('#internal_delivery_item_id').val(ui.item.id);
                            }               
                            
                            $('#purchase_order_number').val(ui.item.document_number);
                            $('#kode_stok').val(ui.item.kode_stok);
                            $('[name="kurs"]').val(ui.item.default_currency);
                            // if (ui.item.default_currency == 'USD' || ui.item.default_currency == 'AUD') {
                            //   $('[name="curre"]').val('dollar');
                            // } else {
                            //   $('[name="kurs"]').val('rupiah');
                            // }
                            $('#received_unit_value').attr('readonly', true);
                            $('#purchase_order_number').attr('readonly', true);

                            $('#quantity_order').data('rule-max', parseFloat(ui.item.left_received_quantity)).data('msg-max', 'max available ' + ui.item.left_received_quantity);

                            // if (ui.item.serial_number != null){
                            //   $( inputIssuedQuantity ).val(1).attr('readonly', true);
                            // }

                            $('#search_purchase_order').val('');

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
            url: $('input[id="serial_number"]').data('source'),
            dataType: "json",
            success: function(resource) {
                $('input[id="serial_number"]').autocomplete({
                    autoFocus: true,
                    minLength: 2,

                    source: function(request, response) {
                    var results = $.ui.autocomplete.filter(resource, request.term);
                    response(results.slice(0, 5));
                    },

                    focus: function(event, ui) {
                    return false;
                    },

                    select: function(event, ui) {
                    $('input[id="serial_number"]').val(ui.item.serial_number);
                    $('input[id="part_number"]').val(ui.item.part_number);
                    $('input[id="alternate_part_number"]').val(ui.item.alternate_part_number);
                    $('input[id="description"]').val(ui.item.description);
                    $('select[id="group"]').val(ui.item.group);
                    $('input[id="unit"]').val(ui.item.unit);
                    $('input[id="minimum_quantity"]').val(ui.item.minimum_quantity);
                    $('#kode_stok').val(ui.item.kode_stok);
                    $('#received_unit').val(ui.item.unit);
                    $('#unit_pakai').val(ui.item.unit);
                    $('#unit_used').val(ui.item.unit);

                    $('input[id="received_quantity"]').val(1).prop('readonly', true);

                    $('input[id="stores"]').focus();

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
                    minLength: 2,

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
                    $('select[id="group"]').val(ui.item.group);
                    $('input[id="unit"]').val(ui.item.unit);
                    $('input[id="minimum_quantity"]').val(ui.item.minimum_quantity);
                    $('#kode_stok').val(ui.item.kode_stok);
                    $('#received_unit').val(ui.item.unit);
                    $('#unit_pakai').val(ui.item.unit);
                    $('#unit_used').val(ui.item.unit);

                    $('input[id="received_quantity"]').focus();

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

        $('input[id="received_quantity"]').attr('data-rule-min', parseInt(1)).attr('data-msg-min', 'min available ' + parseInt(1));
        $('input[id="edit_received_quantity"]').attr('data-rule-min', parseInt(1)).attr('data-msg-min', 'min available ' + parseInt(1));

        // $('#issued_quantity').attr('max', parseInt(ui.item.qty_konvers)).focus();
        // $('#received_quantity').attr('max', parseInt(1)).focus();

        // input item description autocomplete
        $.ajax({
            url: $('input[id="item_description"]').data('source'),
            dataType: "json",
            success: function(data) {
                $('input[id="item_description"]').autocomplete({
                source: function(request, response) {
                    var results = $.ui.autocomplete.filter(data, request.term);
                    response(results.slice(0, 10));
                }
                });
            }
        });

        // input alt part number autocomplete
        $.ajax({
            url: $('input[id="alternate_part_number"]').data('source'),
            dataType: "json",
            success: function(data) {
                $('input[id="alternate_part_number"]').autocomplete({
                source: function(request, response) {
                    var results = $.ui.autocomplete.filter(data, request.term);
                    response(results.slice(0, 10));
                }
                });
            }
        });

        // input unit autocomplete
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



        $('input[id="unit"]').keyup(function() {
            var unit_terima = $('input[id="unit"]').val();
            $('input[id="unit_terima"]').val(unit_terima);
        });

        $('input[id="no_expired_date"]').change(function() {
            if ($('[id="no_expired_date"]').is(':checked')) {
                $('input[id="expired_date"]').prop('readonly', true);
                $('input[id="expired_date"]').prop('required', false);
                $('input[id="expired_date"]').val('');
            } else {
                $('input[id="expired_date"]').prop('readonly', false);
                $('input[id="expired_date"]').prop('required', true);
            }

        });

        $('input[id="expired_date"]').change(function() {
            if ($('input[id="expired_date"]').val() != '') {
                $('input[id="no_expired_date"]').prop('disabled', false);
                $('input[id="no_expired_date"]').prop('required', false);
            } else {
                $('input[id="no_expired_date"]').prop('disabled', false);
                $('input[id="no_expired_date"]').prop('required', true);
            }

        });

        $('input[id="edit_no_expired_date"]').change(function() {
            if ($('[id="edit_no_expired_date"]').is(':checked')) {
                $('input[id="edit_expired_date"]').prop('readonly', true);
                $('input[id="edit_expired_date"]').prop('required', false);
            } else {
                $('input[id="edit_expired_date"]').prop('readonly', false);
                $('input[id="edit_expired_date"]').prop('required', true);
            }

        });

        $('input[id="edit_expired_date"]').change(function() {
            if ($('input[id="edit_expired_date"]').val() != '') {
                $('input[id="edit_no_expired_date"]').prop('disabled', true);
                $('input[id="edit_no_expired_date"]').prop('required', false);
            } else {
                $('input[id="edit_no_expired_date"]').prop('disabled', false);
                $('input[id="edit_no_expired_date"]').prop('required', true);
            }

        });

        $.ajax({
            url: $('input[id="edit_unit"]').data('source'),
            dataType: "json",
            success: function(data) {
                $('input[id="edit_unit"]').autocomplete({
                source: function(request, response) {
                    var results = $.ui.autocomplete.filter(data, request.term);
                    response(results.slice(0, 10));
                }
                });
            }
        });

        $.ajax({
            url: $('input[id="unit_pakai"]').data('source'),
            dataType: "json",
            success: function(data) {
                $('input[id="unit_pakai"]').autocomplete({
                source: function(request, response) {
                    var results = $.ui.autocomplete.filter(data, request.term);
                    response(results.slice(0, 10));
                }
                });
            }
        });

        $('input[id="unit_pakai"]').keyup(function() {
            var unit_used = $('input[id="unit_pakai"]').val();
            $('input[id="unit_used"]').val(unit_used);
        });

        $('input[id="unit"]').keyup(function() {
            var unit = $('input[id="unit"]').val();
            $('input[id="received_unit"]').val(unit);
        });

        $.ajax({
            url: $('input[id="edit_unit_pakai"]').data('source'),
            dataType: "json",
            success: function(data) {
                $('input[id="edit_unit_pakai"]').autocomplete({
                    source: function(request, response) {
                        var results = $.ui.autocomplete.filter(data, request.term);
                        response(results.slice(0, 10));
                    }
                });
            }
        });

        $('input[id="edit_unit_pakai"]').keyup(function() {
            var unit_used = $('input[id="edit_unit_pakai"]').val();
            $('input[id="edit_unit_used"]').val(unit_used);
        });

        // input stores autocomplete
        $('input[id="stores"]').on('focus', function() {
            $.ajax({
                url: $('input[id="stores"]').data('source'),
                dataType: "json",
                success: function(data) {
                $('input[id="stores"]').autocomplete({
                    source: function(request, response) {
                    var results = $.ui.autocomplete.filter(data, request.term);
                    response(results.slice(0, 10));
                    }
                });
                }
            });
        });

        $('input[id="edit_stores"]').on('focus', function() {
            $.ajax({
                url: $('input[id="edit_stores"]').data('source'),
                dataType: "json",
                success: function(data) {
                $('input[id="edit_stores"]').autocomplete({
                    source: function(request, response) {
                    var results = $.ui.autocomplete.filter(data, request.term);
                    response(results.slice(0, 10));
                    }
                });
                }
            });
        });

        // input serial number
        $('input[id="serial_number"]').on('change', function() {
            if ($(this).val() != '') {
                $('input[id="received_quantity"]').val('1').attr('readonly', false);
            } else {
                $('input[id="received_quantity"]').attr('readonly', false);
            }
        });

        //hitung qty konversi
        $('input[name="isi"]').keyup(function() {
            var isi = $(this).val();

            if (isi !== '' || isi > 0) {
                var qty = $('[name="quantity_order"]').val();
                var value = $('[name="value_order"]').val();
                var qty_konversi = parseFloat(qty) * parseFloat(isi);
                $('[name="received_quantity"]').val(qty_konversi);

                var count_received_value = parseFloat(value) / parseFloat(isi);
                var received_value = Number.parseFloat(count_received_value).toFixed(2);
                $('[name="received_unit_value"]').val(received_value);
            }
        });

        $('input[name="quantity_order"]').keyup(function() {
            var qty = $(this).val();

            if (qty !== '' || qty > 0) {
                var isi = $('[name="isi"]').val();
                var value = $('[name="value_order"]').val();
                var qty_konversi = parseFloat(qty) * parseFloat(isi);
                $('[name="received_quantity"]').val(qty_konversi);

                var count_received_value = parseFloat(value) / parseFloat(isi);
                var received_value = Number.parseFloat(count_received_value).toFixed(2);
                $('[name="received_unit_value"]').val(received_value);
            }
        });

        $('input[name="received_unit_value"]').keyup(function() {
            var received_value = $(this).val();

            if (received_value !== '' || received_value > 0) {
                var isi = $('[name="isi"]').val();
                // var value = $('[name="value_order"]').val();
                // var qty_konversi = parseFloat(qty) * parseFloat(isi);
                // $('[name="received_quantity"]').val(qty_konversi);

                var count_value_order = parseFloat(received_value) * parseFloat(isi);
                var value_order = Number.parseFloat(count_value_order).toFixed(2);
                $('[name="value_order"]').val(value_order);
            }
        });

        $('input[id="edit_isi"]').keyup(function() {
            var isi = $(this).val();

            if (isi !== '' || isi > 0) {
                var qty = $('[name="quantity_order"]').val();
                var value = $('[id="edit_value_order"]').val();
                var qty_konversi = parseFloat(qty) * parseFloat(isi);
                $('[name="received_quantity"]').val(qty_konversi);

                var count_received_value = parseFloat(value) / parseFloat(isi);
                var received_value = Number.parseFloat(count_received_value).toFixed(2);
                $('[id="edit_received_unit_value"]').val(received_value);
                console.log(value);
            }
        });

        $('input[id="edit_quantity_order"]').keyup(function() {
            var qty = $(this).val();

            if (qty !== '' || qty > 0) {
                var isi = $('[id="edit_isi"]').val();
                var value = $('[id="edit_value_order"]').val();
                var qty_konversi = parseFloat(qty) * parseFloat(isi);
                $('[name="received_quantity"]').val(qty_konversi);

                var count_received_value = parseFloat(value) / parseFloat(isi);
                var received_value = Number.parseFloat(count_received_value).toFixed(2);
                $('[name="received_unit_value"]').val(received_value);
            }
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

        $('#source').on('change', function(){
            var prev = $(this).data('val');
            var current = $(this).val();
            var url = $(this).data('source');

            if (prev != ''){
                var conf = confirm("Changing the source will remove the items that have been added. Continue?");

                if (conf == false){
                    return false;
                }
            }

            window.location.href = url + '/' + current;
        });

        $('#aircraft_id').on('change', function() {
            var prev = $(this).data('val');
            var current = $(this).val();
            var url = $(this).data('source');

            if (prev != ''){
                var conf = confirm("Changing the vendor will remove the items that have been added. Continue?");

                if (conf == false){
                    return false;
                }
            }

            window.location.href = url + '/' + current;

        });

        $("#item-body").on("change", ".due_at_af_tsn_input", function() {
            var due_at_af_tsn = $(this).val();
            var tsn = $('#tsn').val();
            var remaining_input = $(this).parents('td').siblings('td.remaining').children('input');
            let regExp = /[a-z]/i;
            let hasLetters = regExp.test(due_at_af_tsn)
            if(hasLetters){
                var remaining_value = due_at_af_tsn;
            }else{
                var remaining_count = parseFloat(due_at_af_tsn)-parseFloat(tsn);
                var remaining_value = remaining_count.toFixed(2);
            }
            remaining_input.val(remaining_value);
        });

    });
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
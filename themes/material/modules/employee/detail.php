<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="style-default">
    <div class="section-body">
        <div class="row">
            <div class="col-md-4">
                <?php $this->load->view('material/modules/employee/sidemenu') ?>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-head style-primary">
                        <header>Employee Detail</header>
                    </div>
                    <div class="card-body no-padding">
                        <?php
                            if ( $this->session->flashdata('alert') )
                                render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
                        ?>

                        <div class="document-header force-padding">
                            <div class="row">
                                <div class="col-sm-12 col-lg-12">
                                    <div class="">
                                        <dl class="dl-inline">
                                            <dt>
                                                Employee Number
                                            </dt>
                                            <dd>
                                                <?=print_string($entity['employee_number']);?>
                                            </dd>

                                            <dt>
                                                Name
                                            </dt>
                                            <dd>
                                                <?=print_string($entity['name']);?>
                                            </dd>

                                            <dt>
                                                Base
                                            </dt>
                                            <dd>
                                                <?=print_string($entity['warehouse']);?>
                                            </dd>

                                            <dt>
                                                Department
                                            </dt>
                                            <dd>
                                                <?=print_string($entity['department_name']);?>
                                            </dd>

                                            <dt>
                                                Position
                                            </dt>
                                            <dd>
                                                <?=print_string($entity['position']);?>
                                            </dd>

                                            <dt>
                                                Date of Birth
                                            </dt>
                                            <dd>
                                                <?=print_date($entity['date_of_birth']);?>
                                            </dd>

                                            <dt>
                                                Phone
                                            </dt>
                                            <dd>
                                                <?=$entity['phone_number'];?>
                                            </dd> 

                                            <dt>
                                                E-mail
                                            </dt>
                                            <dd>
                                                <?=$entity['email'];?>
                                            </dd>

                                            <dt>
                                                Address
                                            </dt>
                                            <dd>
                                                <?=$entity['address'];?>
                                            </dd>

                                            <dt>
                                                Gender
                                            </dt>
                                            <dd>
                                                <?=$entity['gender'];?>
                                            </dd>

                                            <dt>
                                                Religion
                                            </dt>
                                            <dd>
                                                <?=$entity['religion'];?>
                                            </dd>

                                            <dt>
                                                Marital Status
                                            </dt>
                                            <dd>
                                                <?=$entity['marital_status'];?>
                                            </dd>

                                            <dt>
                                                Identity Number
                                            </dt>
                                            <dd>
                                                <?=$entity['identity_type'];?> <?=$entity['identity_number'];?>
                                            </dd>

                                            <dt>
                                                NPWP
                                            </dt>
                                            <dd>
                                                <?=$entity['npwp'];?>
                                            </dd>

                                            <dt>
                                                Bank Account
                                            </dt>
                                            <dd>
                                                <?=$entity['bank_account_name'];?> <?=$entity['bank_account'];?>
                                            </dd>
                                            
                                            <dt>
                                                Join Date
                                            </dt>
                                            <dd>
                                                <?=print_date($entity['tanggal_bergabung']);?>
                                            </dd>
                                            <dt>
                                                Level Akun
                                            </dt>
                                            <dd>
                                                <?=$entity['level_name'];?>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-actionbar">
                        <div class="card-actionbar-row">  
                            <div class="pull-left">
                                <a type="button" data-href="<?=site_url($module['route'] .'/edit/'. $entity['employee_number'])?>" class="btn btn-primary ink-reaction btn-open-offcanvas btn-edit">
                                    Edit
                                </a> 
                            </div>                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
<?=html_script('themes/script/jquery.number.js') ?>
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

        // var today       = new Date();
        var today = $('[name="required_date"]').val();
        var today_2 = $('[name="pr_date"]').val();
        // today.setDate(today.getDate() + 30);
        $('#required_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd',
            startDate: today,
        });

        $('[data-provide="datepicker"]').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd',
            startDate: today_2,
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

                        // $('[data-dismiss="modal"]').trigger('click');
                        window.location.href = '<?= site_url($module['route'].'/detail/'.$entity['employee_id']); ?>';                  
                    }
                });
            }

            button.attr('disabled', false);
        });

        $(buttonSubmitDocument).on('click', function(e) {
            e.preventDefault();
            $(buttonSubmitDocument).attr('disabled', true);

            var url = $(this).attr('href');
            if (confirm('Are you sure want to save this request and sending email? Continue?')) {
                $.post(url, formDocument.serialize(), function(data) {
                    console.log(data);
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


            $.ajax({
                url: "<?= site_url($module['route'] . '/ajax_editItem/') ?>/" + id,
                type: "GET",
                data: data_send,
                dataType: "JSON",
                success: function(response) {
                    var action = "<?= site_url($module['route'] . '/edit_item/') ?>/" + id;
                    console.log(JSON.stringify(action));
                    console.log(JSON.stringify(response));
                    var maximum_price = parseFloat(response.maximum_price);
                    var mtd_budget = parseFloat(response.mtd_budget);
                        

                    $('#account_id').val(response.account_id);
                    $('#account_name').val(response.account_name);
                    $('#account_code').val(response.account_code);
                    $('#annual_cost_center_id').val(response.annual_cost_center_id);
                    $('#maximum_price').val(maximum_price);
                    $('#mtd_budget').val(mtd_budget);
                    $('#expense_monthly_budget_id').val(response.expense_monthly_budget_id);
                    $('#additional_info').val(response.additional_info);
                    $('#amount').val(response.amount);
                    $('#reference_ipc').val(response.reference_ipc);

                    $('input[id="amount"]').attr('data-rule-max', parseFloat(response.maximum_price)).attr('data-msg-max', 'max available '+ parseInt(response.maximum_price));

                    // $('#issued_quantity').attr('max', parseInt(ui.item.qty_konvers)).focus();
                    $('#amount').attr('max', parseFloat(response.maximum_price)).focus();

                    $('#unbudgeted_item').val(0);

                    $('#account_name').prop("readonly", true);
                    $('#account_code').prop("readonly", true);

                    $('#modal-add-item form').attr('action', action);
                    $('#modal-add-item').modal('show'); // show bootstrap modal when complete loaded
                    $('.modal-title').text('Edit Item'); // Set title to Bootstrap modal title

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error get data from ajax');
                }
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

            $.get(url, {
                data: val
            });
        });

        $('.btn-edit').on('click', function(e) {
            var url = $(this).data('href');
            var modal = $('#data-modal');
            $.get(url, function(data) {                
                
                var obj = $.parseJSON(data);

                if (obj.type == 'denied') {
                    toastr.options.timeOut = 10000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.error(obj.info, 'ACCESS DENIED!');
                } else {
                    $(modal)
                    .find('.modal-body')
                    .empty()
                    .append(obj.info);

                    $(modal).modal('show');

                    $(modal).on('click', '.modal-header:not(a)', function() {
                        $(modal).modal('hide');
                    });

                    $(modal).on('click', '.modal-footer:not(a)', function() {
                        $(modal).modal('hide');
                    });
                }
            })
        });
    });
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
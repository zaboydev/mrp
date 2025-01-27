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
                    <div class="col-sm-12 col-lg-4">
                        <input type="hidden" name="id" id="id" value="<?= $_SESSION['expense_reimbursement']['id']; ?>">
                        <div class="form-group">
                            <input type="text" value="<?= $_SESSION['expense_reimbursement']['expense_name']; ?>" name="expense_name" id="expense_name" class="form-control" data-validation-rule="unique" data-validation-url="<?= site_url('ajax/expense_duty_name_validation'); ?>" data-validation-exception="" required>
                            <label for="expense_name"><?= $module['label']; ?></label>
                        </div>
                        <div class="form-group">
                            <select name="group_cost" id="group_cost" data-placeholder="Pilih Group" class="form-control select2" required>
                                <?php foreach(findListCostCenter() as $listCost):?>
                                <option value="<?= $listCost['id']; ?>" data-list-cost-id="<?= $listCost['id']; ?>" <?= ($listCost['id'] == $_SESSION['expense_reimbursement']['group_cost']) ? 'selected' : ''; ?>>
                                    <?= $listCost['group_name']; ?>
                                </option>
                                <?php endforeach;?>
                            </select>
                            <label for="group_cost">Group Cost</label>
                        </div>
                        
                        <div class="form-group">
                            <select name="id_benefit" id="id_benefit" data-placeholder="Pilih Benefit" class="form-control select2" required>
                                <option></option>
                                <?php foreach(getBenefits() as $benefit):?>
                                <option value="<?= $benefit['id']; ?>" data-benefit-id="<?= $benefit['id']; ?>" <?= ($benefit['id'] == $_SESSION['expense_reimbursement']['id_benefit']) ? 'selected' : ''; ?>>
                                    <?= $benefit['employee_benefit']; ?>
                                </option>
                                <?php endforeach;?>
                            </select>
                            <label for="id_benefit">Plafond Benefit</label>
                        </div>
                        <div class="form-group">
                            <select name="account_code" id="account_code" data-placeholder="Account Code" class="form-control select2" required>
                                <?php foreach (master_coa() as $group) : ?>
                                <option value="<?= $group['coa']; ?>" data-coa="<?= $group['coa']; ?>" data-group="<?= $group['group']; ?>" <?= ($group['coa'] == $_SESSION['expense_reimbursement']['account_code']) ? 'selected' : ''; ?>>
                                    <?= $group['coa']; ?> - <?= $group['group']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="account_code">Code of Account</label>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        </div>
        <?= form_close(); ?>
        <div class="section-action style-default-bright">
            <div class="section-floating-action-row">
                <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?= site_url($module['route'] . '/save'); ?>">
                    <i class="md md-save"></i>
                    <small class="top right">Save</small>
                </a>
            </div>
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

    $('.number').number(true, 2, '.', ',');

    $('.select2').select2({
        // theme: "bootstrap",
    });

    function setPlafondBalance() {
        var saldo_balance_modal = $('#saldo_balance').val();
        var plafond_balance_modal = $('#plafond_balance').val();
        var used_balance_modal = $('#used_balance').val();
        var account_code_item = $('#description option:selected').data('account-code-item');  

        console.log("MulaiBuka");
        console.log(account_code_item);

        $('#saldo_balance_modal').val(saldo_balance_modal).trigger('change');
        $('#plafond_balance_modal').val(plafond_balance_modal).trigger('change');
        $('#used_balance_modal').val(used_balance_modal).trigger('change');
        $('#account_code_item').val(account_code_item).trigger('change');


    }

    function updateSaldoBalance() {
        var initialBalance = $('#saldo_balance').val();
        const amount = parseFloat(document.getElementById('amount').value) || 0;
        const saldoBalanceField = document.getElementById('saldo_balance_modal');
        const paidAmountField = document.getElementById('paid_amount_modal'); // Field paid amount modal

        var paidAmount = amount; // Default nilai paidAmount adalah amount

        if (amount >= initialBalance) {
            // Jika amount melebihi initialBalance
            paidAmount = initialBalance; // Paid amount adalah sisa saldo awal
            saldoBalanceField.value = 0; // Saldo menjadi 0
        } else {
            // Jika amount tidak melebihi initialBalance
            const updatedBalance = initialBalance - amount; // Hitung saldo yang diperbarui
            saldoBalanceField.value = updatedBalance.toFixed(0); // Update saldo tersisa
        }

        // Update paidAmount field
        paidAmountField.value = paidAmount;
       
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
        today.setDate(today.getDate() - 30);
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
            

            $(buttonSubmitDocument).attr('disabled', false);            
        });

       

        $(buttonDeleteDocumentItem).on('click', function(e) {
            e.preventDefault();

            var url = $(this).attr('href');
            var tr = $(this).closest('tr');

            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    var data = JSON.parse(response);

                    // Remove the row from the table
                    $(tr).remove();

                    // Update the total in the footer
                    $('#total').val(data.total);
                    $('#table-document tfoot th:last').text(data.total.toFixed(2));

                    // Check if table is empty
                    if ($("#table-document > tbody > tr").length == 0) {
                        $(buttonSubmit).attr('disabled', true);
                    }
                },
                error: function() {
                    alert('Failed to delete the item.');
                }
            });
        });


        $(autosetInputData).on('change', function() {
            var val = $(this).val();
            var url = $(this).data('source');
            var id = $(this).attr('id');

            $.get(url, {
                data: val
            });
        });

       

        $('#employee_number').change(function () {
            var employee_number = $('#employee_number').val();                        
            var position = $('#employee_number option:selected').data('position');  
            var url = $(this).data('source-get-balance');
            var type = $('#type_reimbursement').val();   
            $('#occupation').val(position).trigger('change');

            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    employee_number   : employee_number,
                    type      : type,
                    position : position
                },
                success: function(data) {
                    console.log(data);
                    var obj = $.parseJSON(data);

                    if(obj.status=='success'){
                        $('#saldo_balance').val(obj.saldo_balance).trigger('change');
                        $('#plafond_balance').val(obj.plafond_balance).trigger('change');
                        $('#used_balance').val(obj.used_balance).trigger('change');
                        //modal
                        $('#saldo_balance_modal').val(obj.saldo_balance).trigger('change');
                        $('#plafond_balance_modal').val(obj.plafond_balance).trigger('change');
                        $('#used_balance_modal').val(obj.used_balance).trigger('change');



                        $('#employee_has_benefit_id').val(obj.employee_has_benefit_id).trigger('change');
                    }else{
                        toastr.options.timeOut = 10000;
                        toastr.options.positionClass = 'toast-top-right';
                        if(obj.status=='error'){
                            toastr.error(obj.message);
                        }else if(obj.status=='warning'){
                            toastr.warning(obj.message);
                        }

                        $('#saldo_balance').val(obj.saldo_balance).trigger('change');
                        $('#plafond_balance').val(obj.plafond_balance).trigger('change');
                        $('#used_balance').val(obj.used_balance).trigger('change');

                        $('#saldo_balance_modal').val(obj.saldo_balance).trigger('change');
                        $('#plafond_balance_modal').val(obj.plafond_balance).trigger('change');
                        $('#used_balance_modal').val(obj.used_balance).trigger('change');


                        $('#employee_has_benefit_id').val(obj.employee_has_benefit_id).trigger('change');
                        
                    }                    
                    
                }
            });
        });

        $('#description').change(function () {
            var account_code_item = $('#description option:selected').data('account-code-item');  
            $('#account_code_item').val(account_code_item).trigger('change');
            console.log("Data account code");
            console.log(account_code_item);
        });

        $('#type_reimbursement').change(function () {
            var account_code = $('#type_reimbursement option:selected').data('account-code');  
            var id_benefit = $('#type_reimbursement option:selected').data('account-id');

            $('#account_code').val(account_code).trigger('change');
            var employee_number = $('#employee_number').val();                        
            var position = $('#employee_number option:selected').data('position');  
            var url = $('#employee_number').data('source-get-balance');
            var sourceUrl = $('#type_reimbursement').data('source-get-expense-name');

            var type = $('#type_reimbursement').val();   


            $.ajax({
                    url: sourceUrl,
                    type: 'GET',
                    data: {
                        id_type   : id_benefit,
                    },
                    success: function (data) {
                        console.log("dataexpense");
                        console.log(data);
                        var obj = $.parseJSON(data);
                        $('#description').empty();
                        obj.forEach(function (item) {
                            const option = `
                                <option data-account-code-item="${item.account_code}" 
                                        value="${item.expense_name}">
                                    ${item.expense_name}
                                </option>`;
                            $('#description').append(option); // Append each option
                        });
                    },
                    error: function () {
                        alert('Failed to fetch data. Please try again.');
                    }
                });

            if(employee_number!=NULL){
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        employee_number   : employee_number,
                        type      : type,
                        position : position
                    },
                    success: function(data) {
                        console.log(data);
                        var obj = $.parseJSON(data);

                        if(obj.status=='success'){
                            $('#saldo_balance').val(obj.saldo_balance).trigger('change');
                            $('#plafond_balance').val(obj.plafond_balance).trigger('change');
                            $('#used_balance').val(obj.used_balance).trigger('change');

                            $('#employee_has_benefit_id').val(obj.employee_has_benefit_id).trigger('change');
                        }else{
                            toastr.options.timeOut = 10000;
                            toastr.options.positionClass = 'toast-top-right';
                            if(obj.status=='error'){
                                toastr.error(obj.message);
                            }else if(obj.status=='warning'){
                                toastr.warning(obj.message);
                            }

                            $('#saldo_balance').val(obj.saldo_balance).trigger('change');
                            $('#plafond_balance').val(obj.plafond_balance).trigger('change');
                            $('#used_balance').val(obj.used_balance).trigger('change');


                            $('#employee_has_benefit_id').val(obj.employee_has_benefit_id).trigger('change');
                            
                        }                    
                        
                    }
                });
            }
            
            type_reimbursement();            
        });
        

        function type_reimbursement(){
            var type_reimbursement = $('#type_reimbursement').val(); 
            if(type_reimbursement=='MEDICAL'){
                $('.title_1').html('Patient Name');
                $('.title_2').html('Diagnoses');
            }else{
                $('.title_1').html('Expense Detail');
                $('.title_2').html('Description/Notes');
            }
        }

        type_reimbursement();

        $(buttonEditDocumentItem).on('click', function(e) {
            e.preventDefault();

            var id = $(this).data('todo').todo;
            var data_send = {
                id: id
            };

            var save_method = 'update';

            $.ajax({
                url: "<?= site_url($module['route'] . '/ajax_editItem/') ?>/" + id,
                type: "GET",
                data: data_send,
                dataType: "JSON",
                success: function(response) {
                    var action = "<?=site_url($module['route'] .'/edit_item')?>/" + id;
                    console.log(JSON.stringify(response));
                    $('[name="description"]').val(response.description);
                    $('[name="date"]').val(response.transaction_date);
                    $('[name="notes"]').val(response.notes);
                    $('[name="amount"]').val(response.amount);


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
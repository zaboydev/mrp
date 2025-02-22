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
                                    <input type="text" name="document_number" id="document_number" class="form-control" maxlength="6" value="<?= $_SESSION['reimbursement']['document_number']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_doc_number'); ?>" required>
                                    <label for="document_number">Document No.</label>
                                </div>
                                <span class="input-group-addon"><?= $_SESSION['reimbursement']['format_number']; ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="text" name="date" id="date" data-provide="datepicker" data-date-format="dd-mm-yyyy" class="form-control" value="<?= $_SESSION['reimbursement']['date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_received_date'); ?>" required>
                            <label for="date">Date Invoice</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="date_created" id="date_created" data-date-format="dd-mm-yyyy" class="form-control" value="<?= date('Y-m-d') ?>" data-input-type="autoset" readonly>
                            <label for="date_created">Date Created</label>
                        </div>

                        <div class="form-group">
                            <select name="type" id="type_reimbursement" class="form-control select2" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_type_reimbursement'); ?>"  data-source-get-expense-name="<?= site_url($module['route'] . '/get_expense_name'); ?>" <?= !empty($_SESSION['reimbursement']['items']) ? 'disabled' : ''; ?> required>
                                <?php foreach(getBenefits($_SESSION['reimbursement']['employee_number']) as $benefit):?>
                                <option data-account-ben-type="<?=$benefit['benefit_type'];?>" data-account-id="<?=$benefit['id'];?>" data-account-ben-code="<?=$benefit['benefit_code'];?>" data-account-code="<?=$benefit['kode_akun'];?>" value="<?=$benefit['employee_benefit'];?>" <?= ($benefit['employee_benefit'] == $_SESSION['reimbursement']['type']) ? 'selected' : ''; ?>><?=$benefit['employee_benefit'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="type_reimbursement">Type</label>
                        </div>

                        <div class="form-group">
                            <select name="type" id="type_benefit" class="form-control" data-input-type="autoset" disabled>
                                <option value="yearly" <?= ("yearly" == $_SESSION['reimbursement']['benefit_type']) ? 'selected' : ''; ?>>2 Tahun Sekali</option>
                                <option value="once" <?= ("once" == $_SESSION['reimbursement']['benefit_type']) ? 'selected' : ''; ?>>Sekali</option>
                                <option value="contract" <?= ("contract" == $_SESSION['reimbursement']['benefit_type']) ? 'selected' : ''; ?>>Mengikuti Kontrak</option>
                            </select>
                            <label for="type_benefit">Type</label>
                        </div>

                        <!-- <div class="form-group">
                            <select name="head_dept" id="head_dept" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_head_dept'); ?>" required>
                                <option></option>
                                <?php foreach(list_user_in_head_department($_SESSION['reimbursement']['department_id']) as $head):?>
                                <option value="<?=$head['username'];?>" <?= ($head['username'] == $_SESSION['reimbursement']['head_dept']) ? 'selected' : ''; ?>><?=$head['person_name'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="head_dept">Supervisor / Atasan</label>
                        </div> -->

                                      
                    </div>

                    <div class="col-sm-12 col-lg-4">


                        <div class="form-group" style="padding-top: 25px;">
                            <select name="employee_number" id="employee_number" class="form-control select2" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_employee_number'); ?>" data-source-get-balance="<?= site_url($module['route'] . '/get_employee_saldo'); ?>" <?= !empty($_SESSION['reimbursement']['items']) ? 'disabled' : ''; ?> <?= (config_item('auth_role') == 'ADMIN' || config_item('auth_role') == 'SUPER ADMIN') ? '' : 'disabled'; ?>  required>
                                <option></option>
                                <?php foreach(available_employee($_SESSION['reimbursement']['department_id']) as $user):?>
                                <option data-position="<?=$user['position'];?>" value="<?=$user['employee_number'];?>" <?= ($user['employee_number'] == $_SESSION['reimbursement']['employee_number']) ? 'selected' : ''; ?>><?=$user['name'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="employee_number">Name</label>
                        </div>

                        <div class="form-group" style="padding-top: 25px;">
                            <select name="occupation" id="occupation" class="form-control select2" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_occupation'); ?>" <?= !empty($_SESSION['reimbursement']['items']) ? 'disabled' : ''; ?> <?= (config_item('auth_role') == 'ADMIN' || config_item('auth_role') == 'SUPER ADMIN') ? '' : 'disabled'; ?> required>
                                <option></option>
                                <?php foreach(occupation_list() as $occupation):?>
                                <option value="<?=$occupation['position'];?>" <?= ($occupation['position'] == $_SESSION['reimbursement']['occupation']) ? 'selected' : ''; ?>><?=$occupation['position'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="occupation">Occupation / Jabatan</label>
                        </div>
                        
                        <div class="form-group">
                            <input type="text" name="department_name" id="department_name" class="form-control" value="<?= $_SESSION['reimbursement']['department_name']; ?>" readonly>
                            <label for="department_name">Department</label>
                        </div>  
                        <div class="form-group">
                            <input type="text" name="plafond_balance" id="plafond_balance" class="form-control number" value="<?= $_SESSION['reimbursement']['plafond_balance']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_plafond_saldo_balance'); ?>" readonly>
                            <label for="plafond_balance">Plafond Balance</label>
                        </div> 

                        <div class="form-group">
                            <input type="text" name="used_balance" id="used_balance" class="form-control number" value="<?= $_SESSION['reimbursement']['used_balance']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_used_saldo_balance'); ?>" readonly>
                            <label for="used_balance">Used Balance</label>
                        </div> 
                        
                        <div class="form-group">
                            <input type="text" name="saldo_balance" id="saldo_balance" class="form-control number" value="<?= $_SESSION['reimbursement']['saldo_balance']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_saldo_balance'); ?>" readonly>
                            <label for="saldo_balance">Saldo Balance</label>
                        </div>  

                        <div class="form-group hide">
                            <input type="text" name="saldo_balance_initial" id="saldo_balance_initial" class="form-control number" value="<?= $_SESSION['reimbursement']['saldo_balance_initial']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_saldo_balance_init'); ?>" readonly>
                            <label for="saldo_balance_initial">Saldo Balance Initial</label>
                        </div>  


                        <div class="form-group hide">
                            <input type="text" name="employee_has_benefit_id" id="employee_has_benefit_id" class="form-control" value="<?= $_SESSION['reimbursement']['employee_has_benefit_id']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_employee_has_benefit_id'); ?>" readonly>
                            <label for="employee_has_benefit_id">employee_has_benefit_id</label>
                        </div>  
                    </div>

                    <div class="col-sm-12 col-lg-4">
                        <div class="form-group">
                            <textarea name="notes" id="notes" class="form-control" rows="4" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_notes'); ?>"><?= $_SESSION['reimbursement']['notes']; ?></textarea>
                            <label for="notes">Notes</label>
                        </div>
                        <div class="form-group hide">
                            <input type="text" name="account_code" id="account_code" class="form-control" value="<?= $_SESSION['reimbursement']['account_code']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_account_code'); ?>" readonly>
                            <label for="account_code">Account Code (COA)</label>
                        </div>  

                        <div class="form-group hide">
                            <input type="text" name="id_benefit" id="id_benefit" class="form-control" value="<?= $_SESSION['reimbursement']['id_benefit']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_id_benefit'); ?>" readonly>
                            <label for="id_benefit">ID Benefit</label>
                        </div>  

                        <div class="form-group hide">
                            <input type="text" name="benefit_code" id="benefit_code" class="form-control" value="<?= $_SESSION['reimbursement']['benefit_code']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_benefitcode'); ?>" readonly>
                            <label for="benefit_code">Benefit CODE</label>
                        </div>  

                        <div class="form-group hide">
                            <input type="text" name="cost_center_group_id" id="cost_center_group_id" class="form-control" value="<?= $_SESSION['reimbursement']['cost_center_group_id']; ?>" data-input-type="autoset" readonly>
                            <label for="cost_center_group_id">Cost Center Group</label>
                        </div> 

                        <div class="form-group hide">
                            <input type="text" name="id_reimbursement_log" id="id_reimbursement_log" class="form-control" value="<?= $_SESSION['reimbursement']['id']; ?>" data-input-type="autoset" readonly>
                            <label for="id_reimbursement_log">ID REIMBURSEMENT</label>
                        </div> 

                        <div class="form-group hide">
                            <input type="text" name="head_dept" id="head_dept" class="form-control" value="<?= $_SESSION['reimbursement']['head_dept']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_head_dept'); ?>" readonly>
                            <label for="head_dept">Head Dept</label>
                        </div> 
                    </div>
                </div>
            </div>

            <div class="document-data table-responsive">
                <table class="table table-hover table-striped" id="table-document">
                    <thead>
                        <tr>
                            <th></th>
                            <th><span class="title_1">Expense Detail</span></th>
                            <th>Date</th>
                            <th><span class="title_2">Description/Notes</span></th>
                            <th>Account Code (A)</th>
                            <th>Amount</th>
                            <th>Paid Amount</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total = array(); ?>

                        <?php if (!empty($_SESSION['reimbursement']['items'])): ?>
                            <?php foreach ($_SESSION['reimbursement']['items'] as $i => $items): ?>
                                <?php $total[] = $items['paid_amount']; ?>
                                <tr id="row_<?= $i; ?>">
                                    <td>
                                        <a href="<?= site_url($module['route'] . '/del_item/' . $i); ?>" class="btn btn-icon-toggle btn-danger btn-sm btn_delete_document_item">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                        <a class="btn btn-icon-toggle btn-info btn-sm btn_edit_document_item" data-todo='{"todo":<?= $i; ?>}'data-item-id-db='{"item":<?= $items['id']; ?>}'>
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                    <td><?= $items['description']; ?></td>
                                    <td><?= ($items['transaction_date'] == '') ?$_SESSION['reimbursement']['date'] : $items['transaction_date']; ?></td>
                                    <td><?= ($items['notes'] == '') ? $items['notes_modal'] : $items['notes']; ?></td>
                                    <td><?= ($items['account_code'] == '') ? $items['account_code_item'] : $items['account_code']; ?></td>
                                    <td><?= number_format($items['amount'], 2); ?></td>
                                    <td><?= number_format($items['paid_amount'], 2); ?></td>
                                    <td>
                                        <?php if (!empty($items['attachment'])): ?>
                                            <a href="<?= base_url('attachment/reimbursement/' . $items['attachment']); ?>" target="_blank" title="View Attachment">
                                                <i class="fa fa-eye text-primary"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">No Attachment</span>
                                        <?php endif; ?>
                                    </td>


                                    
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No items available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th colspan="3">Total</th>
                        <th><?= number_format(array_sum($total));?>
                        <input type="hidden" name="total" id="total" value="<?= array_sum($total); ?>">
                        </th>
                    </tr>
                </tfoot>
                </table>
            </div>
        </div>
        <div class="card-actionbar">
            <div class="card-actionbar-row">
                <button type="button" href="" onClick="addRow()" class="btn btn-primary ink-reaction pull-left hide">
                Add
                </button>

                <a href="#modal-add-item" data-toggle="modal" onClick="setPlafondBalance()" data-target="#modal-add-item" class="btn btn-primary ink-reaction btn-open-offcanvas pull-left">
                    Add Item
                </a>

                <div class="pull-left">
                    <button type="button" href="" onClick="addRow()" class="btn btn-primary ink-reaction pull-left hide">
                    Add
                    </button>
<!-- 
                    <a style="margin-left: 15px;" href="<?= site_url($module['route'] . '/attachment'); ?>" onClick="return popup(this, 'attachment')" class="btn btn-primary ink-reaction">
                        Attachment
                    </a> -->
                </div>

                <a href="<?= site_url($module['route'] . '/discard'); ?>" class="btn btn-flat btn-danger ink-reaction">
                    Discard
                </a>
            </div>
        </div>
        </div>
        <?= form_close(); ?>
        <div class="section-action style-default-bright">
            <div class="section-floating-action-row">
                <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction"<?= empty($_SESSION['reimbursement']['items']) ? 'disabled' : ''; ?>  id="btn-submit-document" href="<?= site_url($module['route'] . '/save'); ?>" >
                    <i class="md md-save"></i>
                    <small class="top right">Save Document</small>
                </a>
            </div>
        </div>
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

                <?= form_open_multipart(site_url($module['route'] . '/add_item'), array(
                    'autocomplete' => 'off',
                    'item_id'    => 'ajax-form-create-document',
                    'class' => 'form form-validate ui-front',
                    'role'  => 'form'
                )); ?>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-12">
                                <fieldset>
                                    <legend><?=$_SESSION['reimbursement']['type']?></legend>
                                    <div class="form-group" style="padding-top: 25px;">
                                        <select name="description" id="description" class="form-control" 
                                            data-input-type="autoset" 
                                            data-source="<?= site_url($module['route'] . '/set_description'); ?>"
                                            required>
                                            <option></option>
                                        </select>
                                        <label for="description">Expense Name</label>
                                    </div>


                                    <div class="form-group">
                                        <textarea name="notes_modal" id="notes_modal" data-tag-name="notes_modal" class="form-control input-sm"></textarea>
                                        <label for="notes_modal"><span class="notes_modal">Description/Notes</span></label>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" id="amount" class="form-control input-sm" name="amount" value="0" step=".02" oninput="updateSaldoBalance()">
                                        <label for="amount">Amount</label>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" id="paid_amount_modal" class="form-control input-sm" name="paid_amount_modal" value="0" step=".02" readonly>
                                        <label for="paid_amount_modal">Paid Amount</label>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" id="plafond_balance_modal" class="form-control input-sm" name="plafond_balance_modal" value="0" step=".02" readonly>
                                        <label for="plafond_balance_modal">Plafond Balance</label>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" id="used_balance_modal" class="form-control input-sm" name="used_balance_modal" value="0" step=".02" readonly>
                                        <label for="used_balance_modal">Last Used Balance</label>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" id="saldo_balance_modal" class="form-control input-sm" name="saldo_balance_modal" value="0" step=".02" readonly>
                                        <label for="saldo_balance_modal">Saldo Balance</label>
                                    </div>

                                    <!-- <div class="form-group">
                                        <label for="attachment">Attachment</label>
                                        <input type="file" id="attachment" class="form-control input-sm" name="attachment" accept=".jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.pdf">
                                        <small id="file-error" class="text-danger" style="display: none;">File size must be less than 1MB.</small>
                                    </div> -->
                                    <div class="form-group">
                                        <label for="attachment">Attachment</label>
                                        
                                        <!-- Display Existing File (if any) -->
                                        <div id="existing-attachment">
                                            <?php if (!empty($item['attachment'])): ?>
                                                <a href="<?= base_url('attachment/reimbursement/' . $item['attachment']); ?>" target="_blank">
                                                    <i class="fa fa-eye text-primary"></i> View Existing File
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">No Attachment</span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- File Input -->
                                        <input type="file" id="attachment" class="form-control input-sm" name="attachment"
                                            accept=".jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.pdf" required>
                                        <small id="file-error" class="text-danger" style="display: none;">File size must be less than 1MB.</small>
                                    </div>
                                    <label for="saldo_balance_modal"><?php $item['attachment']; ?></label>

                                    <div class="form-group hide">
                                        <input type="text" name="account_code_item" id="account_code_item" class="form-control"  data-input-type="autoset" readonly>
                                        <label for="account_code_item">Account Code (COA)</label>
                                    </div>

                                    <div class="form-group hide">
                                        <input type="text" name="id_reimbursement_item" id="id_reimbursement_item" class="form-control"  data-input-type="autoset" readonly>
                                        <label for="id_reimbursement_item">ID Reimbusement Item</label>
                                    </div>

                                    <div class="form-group hide">
                                        <input type="text" name="amount_awal_item" id="amount_awal_item" class="form-control" step=".02"  data-input-type="autoset" readonly>
                                        <label for="amount_awal_item">Amount Awal Item</label>
                                    </div>

                                    <div class="form-group hide">
                                        <textarea name="existing_attachment" id="existing_attachment" data-tag-name="existing_attachment" class="form-control input-sm"></textarea>
                                        <label for="existing_attachment">Existing Attachment</label>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="modal-add-item-submit" onClick="setLastBalance()" class="btn btn-primary btn-create ink-reaction">
                    <!-- <button type="submit" id="modal-add-item-submit" class="btn btn-primary btn-create ink-reaction"> -->
                        Add Item
                    </button>

                    <input type="hidden" name="consignor" id="consignor">
                    <input type="reset" name="reset" class="sr-only">
                </div>

                <?= form_close(); ?>
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


    window.onload = async function(){
        console.log('mulaiinit');
        var id = $('#id_reimbursement_log').val();
        

        // if(id != ''){
        // $('#type_reimbursement').trigger('change');
        // $('#employee_number').trigger('change');
        // await calculateItemReimbursement();
        // }

    };
    document.getElementById("attachment").addEventListener("change", function() {
        var file = this.files[0];
        var errorMessage = document.getElementById("file-error");

        if (file && file.size > 1048576) { // 1MB = 1048576 bytes
            errorMessage.style.display = "block"; // Show error message
            this.value = ""; // Clear the file input
        } else {
            errorMessage.style.display = "none"; // Hide error message if valid
        }
    });

    function calculateItemReimbursement(){
        console.log("calculated");

        var total = $('#total').val();
        var used_balance = $('#used_balance').val();
        var saldo_balance = $('#saldo_balance').val();


        var lastSaldo = parseFloat(used_balance)+parseFloat(total);
        var saldoLast = parseFloat(saldo_balance) - parseFloat(lastSaldo);


        localStorage.setItem("saldoModal", saldoLast); 
        localStorage.setItem("usedBalance", lastSaldo); 

        $('#saldo_balance').val(saldoLast).trigger('change');
        $('#used_balance').val(lastSaldo).trigger('change');
        
        console.log("HitungCalculate");
        console.log(total);
        console.log(used_balance);
        console.log(saldoLast);
        console.log(lastSaldo);



    }

    function setLastBalance() {
        console.log("Mulai menghitung saldo");
        var saldo_balance_modal = $('#saldo_balance_modal').val();
        var paid_amount_modal = $('#paid_amount_modal').val();
        var used_balance_modal = $('#used_balance_modal').val();
        var amount_awal_item = $('#amount_awal_item').val();
        var used_balance = $('#used_balance').val();
        var usedMerge = 0;
        if(amount_awal_item != 0){
            usedMerge = (parseFloat(used_balance)-parseFloat(amount_awal_item))+parseFloat(paid_amount_modal);
        } else {
            usedMerge = parseFloat(used_balance)+parseFloat(paid_amount_modal);
        }


        


        localStorage.setItem("saldoModal", saldo_balance_modal); 
        localStorage.setItem("usedBalanceModal", usedMerge); 

        $('#saldo_balance').val(saldo_balance_modal).trigger('change');
        $('#used_balance').val(usedMerge).trigger('change');

        console.log(saldo_balance_modal);
        console.log(paid_amount_modal);
        console.log(used_balance_modal);
        console.log(used_balance);
        console.log(usedMerge);
        
    }
        

        

    

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

       

        var objExpenseItem = localStorage.getItem("expense_name_item");
        var objExpenseData = $.parseJSON(objExpenseItem);
        $('#description').empty();
        const emptyOption = `
                                <option data-account-code-item="" 
                                        value="">
                                     Pilih Expense Name 
                                </option>`;
                        $('#description').append(emptyOption);
        objExpenseData.forEach(function (item) {
                const option = `
                    <option data-account-code-item="${item.account_code}" 
                            value="${item.expense_name}">
                        ${item.expense_name} - ${item.account_code}
                    </option>`;
                $('#description').append(option); // Append each option
        });

    }

    function updateSaldoBalance() {
        console.log('masuksini');


        var initialBalance = $('#saldo_balance').val();
        var initialBalanceAwal = $('#saldo_balance_initial').val();

        const valueAmountAwal = $('#amount_awal_item').val();
        const amountAwal = valueAmountAwal.trim() === "" ? 0 : parseFloat(valueAmountAwal);
        const amount = parseFloat(document.getElementById('amount').value) || 0;
        const saldoBalanceField = document.getElementById('saldo_balance_modal');
        const paidAmountField = document.getElementById('paid_amount_modal'); // Field paid amount modal

        const id_reimbursement_item = document.getElementById('id_reimbursement_item');

        var paidAmount = amount; // Default nilai paidAmount adalah amount

        if(amountAwal == 0){
            console.log('masuksini2');
            if (amount >= initialBalance) {
            // Jika amount melebihi initialBalance
            paidAmount = initialBalance; // Paid amount adalah sisa saldo awal
            saldoBalanceField.value = 0; // Saldo menjadi 0
            } else {
                // Jika amount tidak melebihi initialBalance
                const updatedBalance = (parseFloat(initialBalance) - parseFloat(amount)) + parseFloat(amountAwal); // Hitung saldo yang diperbarui
                saldoBalanceField.value = updatedBalance.toFixed(0); // Update saldo tersisa
            }
        } else {
            console.log('masuksini3');
            console.log(amount);
            console.log((parseFloat(initialBalance) + parseFloat(amountAwal)));

            if (amount >= (parseFloat(initialBalance) + parseFloat(amountAwal))) {
            console.log('masuksini4');

            // Jika amount melebihi initialBalance
            paidAmount = (parseFloat(initialBalance) + parseFloat(amountAwal)); // Paid amount adalah sisa saldo awal
            saldoBalanceField.value = 0; // Saldo menjadi 0
            } else {
            console.log('masuksini5');

                // Jika amount tidak melebihi initialBalance
                const updatedBalance = parseFloat(initialBalance) - parseFloat(amount) + parseFloat(amountAwal); // Hitung saldo yang diperbarui
                saldoBalanceField.value = updatedBalance.toFixed(0); // Update saldo tersisa
            }
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
        var lastToday = new Date();
        $('[data-provide="datepicker"]').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd',
            startDate: today,
            endDate: lastToday
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
                console.log("Masuk sini");
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

        $(buttonSubmitDocument).on('click', function (e) {
            e.preventDefault();
            var button = $(this);
            button.attr('disabled', true); // Disable the button to prevent multiple clicks.

            var dataBalanceInit = $('#saldo_balance_initial').val();

            var url = button.attr('href');
            var saldo = parseFloat($('#saldo_balance').val()) || 0; // Default to 0 if invalid.
            var saldoinit = parseFloat(dataBalanceInit) || 0; // Default to 0 if invalid.
            var id = parseFloat($('#employee_has_benefit_id').val()) || 0; // Default to 0 if invalid.

            var total = parseFloat($('#total').val()) || 0;         // Default to 0 if invalid.

            console.log("Total:", total);
            console.log("Saldo init:", saldoinit);
            console.log("Saldo balance:", saldo);


            

            // Validation for invalid inputs
            if (isNaN(saldo) || isNaN(total)) {
                alert('Invalid balance or total value. Please check your inputs.');
                button.attr('disabled', false); // Re-enable the button.
                return;
            }

            // Check if saldo is less than the total reimbursement
            if (saldoinit < total ) {
                if (confirm('Your balance is less than the total reimbursement. The amount to be reimbursed is the balance. Continue?')) {
                    submitForm(url, button);
                } else {
                    button.attr('disabled', false); // Re-enable the button if user cancels.
                }
            } else {
                submitForm(url, button);
            }
        });

// Function to submit the form via POST
function submitForm(url, button) {
    $.post(url, formDocument.serialize(), function (data) {
        var obj = $.parseJSON(data);

        if (obj.success === false) {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error(obj.message);
        } else {
            toastr.options.timeOut = 4500;
            toastr.options.closeButton = false;
            toastr.options.progressBar = true;
            toastr.options.positionClass = 'toast-top-right';
            toastr.success(obj.message);

            setTimeout(function () {
                window.location.href = '<?= site_url($module['route']); ?>';
            }, 5000);
        }

        button.attr('disabled', false); // Re-enable the button after completion.
    }).fail(function () {
        alert('An error occurred while submitting the form.');
        button.attr('disabled', false); // Re-enable the button on error.
    });
}


       

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
                    // if ($("#table-document > tbody > tr").length == 0) {
                    //     $(buttonSubmitDocument).attr('disabled', true);
                    // }
                    window.location.reload();


                    
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
                        $('#saldo_balance_initial').val(obj.saldo_balance).trigger('change');
                        $('#plafond_balance').val(obj.plafond_balance).trigger('change');
                        $('#used_balance').val(obj.used_balance).trigger('change');
                        //modal
                        $('#saldo_balance_modal').val(obj.saldo_balance).trigger('change');
                        $('#plafond_balance_modal').val(obj.plafond_balance).trigger('change');
                        $('#used_balance_modal').val(obj.used_balance).trigger('change');

                        var dataInit = $('#saldo_balance_initial').val();
                        console.log("Saldo init");
                        console.log(dataInit);


                        localStorage.setItem("saldoInit", obj.saldo_balance);
                        localStorage.setItem("saldoModal", obj.saldo_balance); 
                        localStorage.setItem("plafonModal", obj.plafond_balance); 
                        localStorage.setItem("usedBalance", obj.used_balance); 





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
                        $('#saldo_balance_initial').val(obj.saldo_balance).trigger('change');
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

        var objExpense;

        $('#type_reimbursement').change(function () {
            // var account_code = $('#type_reimbursement option:selected').data('account-code');  
            var id_benefit = $('#type_reimbursement option:selected').data('account-id');
            var benefit_code = $('#type_reimbursement option:selected').data('account-ben-code');
            var benefit_type = $('#type_reimbursement option:selected').data('account-ben-type');



            // $('#account_code').val(account_code).trigger('change');
            $('#id_benefit').val(id_benefit).trigger('change');
            $('#benefit_code').val(benefit_code).trigger('change');
            $('#type_benefit').val(benefit_type).trigger('change');



            var employee_number = $('#employee_number').val();     
                               
            var position = $('#employee_number option:selected').data('position');  
            var url = $('#employee_number').data('source-get-balance');
            var sourceUrl = $('#type_reimbursement').data('source-get-expense-name');

            var type = $('#type_reimbursement').val();   

            console.log("dataexpense");
            console.log(id_benefit);
            var dataGroup =$('#cost_center_group_id').val();     

            $.ajax({
                    url: sourceUrl,
                    type: 'GET',
                    data: {
                        id_type   : id_benefit,
                        cost_center_group_id : dataGroup
                    },
                    success: function (data) {
                       
                        console.log(data);
                        objExpense = $.parseJSON(data);
                        $('#description').empty();
                        const emptyOption = `<option data-account-code-item="" 
                                        value="">
                                        Pilih Expense Name 
                                </option>`;
                        $('#description').append(emptyOption);
                        objExpense.forEach(function (item) {
                            const option = `
                                <option data-account-code-item="${item.account_code}" 
                                        value="${item.expense_name}">
                                    ${item.expense_name} - ${item.account_code}
                                </option>`;
                            $('#description').append(option); // Append each option
                        });
                        

                        localStorage.setItem("expense_name_item", data); 
                    },
                    error: function () {
                        alert('Failed to fetch data. Please try again.');
                    }
                });

            if(employee_number != ""){

                $('#employee_number').trigger("change");
                

                var saldoModal = localStorage.getItem("saldoModal"); 
                var plafonModal = localStorage.getItem("plafonModal"); 
                var usedBalance = localStorage.getItem("usedBalance"); 

                console.log("Print Refresh");
                console.log(saldoModal);
                console.log(plafonModal);
                console.log(usedBalance);



                
                // $.ajax({
                //     url: url,
                //     type: 'GET',
                //     data: {
                //         employee_number   : employee_number,
                //         type      : type,
                //         position : position
                //     },
                //     success: function(data) {
                //         console.log(data);
                //         var obj = $.parseJSON(data);

                //         if(obj.status=='success'){
                //             $('#saldo_balance').val(obj.saldo_balance).trigger('change');
                //             $('#plafond_balance').val(obj.plafond_balance).trigger('change');
                //             $('#used_balance').val(obj.used_balance).trigger('change');

                //             $('#employee_has_benefit_id').val(obj.employee_has_benefit_id).trigger('change');
                //         }else{
                //             toastr.options.timeOut = 10000;
                //             toastr.options.positionClass = 'toast-top-right';
                //             if(obj.status=='error'){
                //                 toastr.error(obj.message);
                //             }else if(obj.status=='warning'){
                //                 toastr.warning(obj.message);
                //             }

                //             $('#saldo_balance').val(obj.saldo_balance).trigger('change');
                //             $('#plafond_balance').val(obj.plafond_balance).trigger('change');
                //             $('#used_balance').val(obj.used_balance).trigger('change');


                //             $('#employee_has_benefit_id').val(obj.employee_has_benefit_id).trigger('change');
                            
                //         }                    
                        
                //     }
                // });
            }
            
            // type_reimbursement();            
        });
        

        // function type_reimbursement(){
        //     var type_reimbursement = $('#type_reimbursement').val(); 
        //     if(type_reimbursement=='MEDICAL'){
        //         $('.title_1').html('Patient Name');
        //         $('.title_2').html('Diagnoses');
        //     }else{
        //         $('.title_1').html('Expense Detail');
        //         $('.title_2').html('Description/Notes');
        //     }
        // }

        // type_reimbursement();

        $(buttonEditDocumentItem).on('click', function(e) {
            e.preventDefault();

            var id = $(this).data('todo').todo;
            var idDbItem = $(this).data('item-id-db').item;

            console.log("ID DB");
            console.log(idDbItem);
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
                    $('[name="notes_modal"]').val(response.notes_modal);
                    $('[name="amount"]').val(response.amount);
                    $('[name="existing_attachment"]').val(response.attachment);
                    $('[name="id_reimbursement_item"]').val(response.id);
                    $('[name="amount_awal_item"]').val(response.paid_amount);


                    // Handle attachment display
                    if (response.attachment) {
                        var attachmentUrl = "<?= base_url('attachment/reimbursement') ?>" +"/" + response.attachment;
                        $('#existing-attachment').html(
                            `<a href="${attachmentUrl}" target="_blank" value="${response.attachment}">
                                <i class="fa fa-eye text-primary"></i> View Existing File
                            </a>`
                        );
                    } else {
                        $('#existing-attachment').html('<span class="text-muted">No Attachment</span>');
                    }

                    $('#saldo_balance_modal').val($('#saldo_balance').val()).trigger('change');
                    $('#paid_amount_modal').val(response.paid_amount).trigger('change');
                    $('#plafond_balance_modal').val($('#plafond_balance').val()).trigger('change');
                    $('#used_balance_modal').val($('#used_balance').val()).trigger('change');
                    

                    var objExpenseItem = localStorage.getItem("expense_name_item");
                    var objExpenseData = $.parseJSON(objExpenseItem);
                    $('#description').empty();
                    const emptyOption = `
                                <option data-account-code-item="" 
                                        value="">
                                     Pilih Expense Name
                                </option>`;
                        $('#description').append(emptyOption);
                    objExpenseData.forEach(function (item) {
                            const option = `
                                <option data-account-code-item="${item.account_code}" 
                                        value="${item.expense_name}">
                                    ${item.expense_name} - ${item.account_code}
                                </option>`;
                            $('#description').append(option); // Append each option
                        });

                        

                       

                    if(response.account_code_item == ""){
                        var account_code_item = $('#description option:selected').data('account-code-item');  
                        $('#account_code_item').val(account_code_item).trigger('change');
                    } else {
                        $('[name="account_code_item"]').val(response.account_code_item);
                    }



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
<?= form_open_multipart(site_url($module['route'] . '/save_benefit'), array(
    'autocomplete'  => 'off',
    // 'id'            => 'form-create-data',
    'class'         => 'form form-validate form-xhr ui-front',
    // 'role'          => 'form'
)); ?>

<div class="card style-default-bright">
    <div class="card-head style-primary-dark">
        <header>Add New Benefit to Employee <?= $module['label']; ?></header>

        <div class="tools">
        <div class="btn-group">
            <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-label="Close" title="close">
            <i class="md md-close"></i>
            </a>
        </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12 col-md-4 col-md-offset-4">

                        <div class="form-group">
                            <input type="text" name="employee_name" id="employee_name" class="form-control" value="<?=$entity['name'];?>" readonly>
                            <input type="hidden" name="employee_number" id="employee_number" class="form-control" value="<?=$entity['employee_number'];?>" readonly>
                            <label for="employee_name">Name</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="jabatan" id="jabatan" class="form-control" value="<?=$entity['position'];?>" readonly>
                            <label for="jabatan">Jabatan</label>
                        </div>

                        <div class="form-group" style="padding-top: 25px;">
                            <select name="employee_benefit_id" id="employee_benefit_id" class="form-control select2" style="width: 100%" data-placeholder="Select Benefit" data-source="<?= site_url($module['route'] . '/get_history_benefit'); ?>" required>
                                <option value="">Select Benefit</option>
                                <?php foreach(getBenefitsByEmployeeNumber($entity['employee_number'], $entity['gender']) as $benefit):?>
                                <option data-amount="<?=$benefit['amount'];?>" data-id="<?=$benefit['benefit_id'];?>" data-employee-number="<?=$entity['employee_number'];?>" data-benefit-type="<?=$benefit['name_type'];?>" value="<?=$benefit['benefit_id'];?>"><?=$benefit['employee_benefit'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="start_date">Benefit Name</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="benefit_name" id="benefit_name" class="form-control" value="" readonly>
                            <label for="benefit_name">Benefit Type</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="last_claim" id="last_claim" class="form-control" value="" readonly>
                            <label for="last_claim">Claim Terakhir</label>
                        </div>

                        <div class="form-group">
                            <input type="number" name="amount_plafond" id="amount_plafond" class="form-control number" value="0" step=".01">
                            <label for="amount_plafond">Amount Plafond</label>
                        </div>

                        <div class="form-group">
                            <p style="font-size:14px;"><?= print_string($kontrak_active['contract_number']) ?> (<?= print_date($kontrak_active['start_date']) ?> sd <?= print_date($kontrak_active['end_date']) ?>)</p>
                            <input type="hidden" name="employee_contract_id" id="employee_contract_id" class="form-control" value="<?=$kontrak_active['id'];?>" readonly>
                            <label for="jabatan">Periode Kontrak</label>
                        </div>

                       

                       
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-foot">
        <button type="submit" id="modal-create-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction pull-right" data-title="save and create">
        <i class="md md-save"></i>
        </button>
    </div>
</div>

<?= form_close(); ?>
<script type="text/javascript">
    $('.number').number(true, 2, '.', ',');
    $('.select2').select2();
    
    
    $('#employee_benefit_id').change(function () {
        var amount_plafond = $('#employee_benefit_id option:selected').data('amount'); 
        var benefit_type = $('#employee_benefit_id option:selected').data('benefit-type');  
        console.log('DataBerubah');

        console.log(amount_plafond);
        $('#amount_plafond').val(amount_plafond).trigger('change');
        $('#benefit_name').val(benefit_type).trigger('change');
        console.log(benefit_type);

        getHistoryBenefit();
    });

    function getHistoryBenefit() {

        
           
            var id_benefit = $('#employee_benefit_id option:selected').data('id');
            var employee = $('#employee_benefit_id option:selected').data('employee-number');
            var sourceUrl = $('#employee_benefit_id').data('source');
            console.log("Data Benefit");
            console.log(id_benefit);
            console.log(employee);
            console.log(sourceUrl);

            

            $.ajax({
                    url: sourceUrl,
                    type: 'GET',
                    data: {
                        id   : id_benefit,
                        employee : employee
                    },
                    success: function (data) {
                        console.log('GetData');
                        
                        objExpense = $.parseJSON(data);
                        console.log(Object.keys(objExpense).length);
                        console.log(objExpense[0]);
                        $('#last_claim').empty();

                        if(Object.keys(objExpense).length != 0){
                            
                            const date = new Date(objExpense[0]['created_at']);

                            const formattedDate = date.toLocaleDateString("en-US", {
                            year: "numeric",
                            month: "long",
                            day: "2-digit"
                            });

                            dataLastClaim = formattedDate + ' - ' + objExpense[0]['document_number'];
                            $('#last_claim').val(dataLastClaim).trigger('change');
                        } else {
                            $('#last_claim').val('-').trigger('change');

                        }

                    
                        
                    },
                    error: function () {
                        alert('Failed to fetch data. Please try again.');
                    }
                });
            
            // type_reimbursement();            
        };
</script>
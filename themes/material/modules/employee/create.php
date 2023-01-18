<?= form_open(site_url($module['route'] . '/save'), array(
    'autocomplete'  => 'off',
    'id'            => 'form-create-data',
    'class'         => 'form form-validate form-xhr ui-front',
    'role'          => 'form'
)); ?>

<div class="card style-default-bright">
    <div class="card-head style-primary-dark">
        <header>Create New <?= $module['label']; ?></header>

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
                    <div class="col-sm-12 col-lg-6">
                        <div class="form-group">
                            <input type="text" name="employee_number" id="employee_number" class="form-control" data-validation-rule="unique" data-validation-url="<?= site_url('ajax/employee_number_validation'); ?>" data-validation-exception="" required>
                            <label for="employee_number">Employee Number</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="name" id="name" class="form-control" data-validation-rule="unique" data-validation-url="<?= site_url('ajax/employee_name_validation'); ?>" data-validation-exception="" required>
                            <label for="name">Name</label>
                        </div>

                        <div class="form-group" style="padding-top: 25px;">
                            <select name="user_id" id="user_id" class="form-control" style="width: 100%" data-placeholder="Select User in MRP">
                                <option value="">Select User in MRP</option>
                                <?php foreach(available_user(array('person_name', 'user_id','username')) as $user):?>
                                <option value="<?=$user['user_id'];?>"><?=$user['username'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="user_id">User in MRP</label>
                        </div>

                        <div class="form-group">
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control">
                            <label for="date_of_birth">Date of Birth</label>
                        </div>

                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <div class="radio">
                                <input type="radio" name="gender" id="male" value="male">
                                <label for="male">
                                    Male
                                </label>
                            </div>
                            <div class="radio">
                                <input type="radio" name="gender" id="female" value="female">
                                <label for="female">
                                    Female
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <select name="religion" id="religion" class="form-control" required>
                                <option value=""></option>
                                <option value="Islam">Islam</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Budha">Budha</option>
                                <option value="Kong Hu Cu">Kong Hu Cu</option>
                            </select>
                            <label for="religion">Religion</label>
                        </div>

                        <div class="form-group">
                            <label for="marital_status">Marital Status</label>
                            <div class="radio">
                                <input type="radio" name="marital_status" id="Married" value="Married">
                                <label for="Married">
                                    Married
                                </label>
                            </div>
                            <div class="radio">
                                <input type="radio" name="gender" id="Single" value="Single">
                                <label for="Single">
                                    Single
                                </label>
                            </div>
                        </div>                        
                    </div>

                    <div class="col-sm-12 col-lg-6">
                        <div class="form-group">
                            <input type="text" name="phone_number" id="phone_number" class="form-control" required>
                            <label for="phone_number">Phone number</label>
                        </div>

                        <div class="form-group">
                            <input type="email" class="form-control" name="email" id="email" required>
                            <label for="email">Email</label>
                        </div>

                        <div class="form-group">
                            <textarea name="address" id="address" class="form-control"></textarea>
                            <label for="address">Address</label>
                        </div>

                        <div class="form-group" style="padding-top: 25px;">
                            <select name="department_id" id="department_id" class="form-control" style="width: 100%" data-placeholder="Select Department">
                                <option value="">Select Department</option>
                                <?php foreach(available_department() as $department):?>
                                <option value="<?=$department['id'];?>"><?=$department['department_code'];?> - <?=$department['department_name'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="user_id">Department</label>
                        </div>

                        <div class="form-group" style="padding-top: 25px;">
                            <select name="position" id="position" class="form-control" style="width: 100%" data-placeholder="Select Occupation">
                                <option value="">Select Occupation</option>
                                <?php foreach(occupation_list() as $occupation):?>
                                <option data-plafond-cuti="<?=$occupation['cuti'];?>" data-plafond-kesehatan="<?=$occupation['plafon_biaya_kesehatan'];?>" data-plafond-spd="<?=$occupation['plafon_biaya_dinas'];?>" value="<?=$occupation['position'];?>"><?=$occupation['position'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="position">Occupation</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="plafon_biaya_dinas" id="plafon_biaya_dinas" class="form-control number" value="0" step=".01">
                            <label for="plafon_biaya_dinas">Plafon Biaya Dinas</label>
                        </div>
                        <div class="form-group">
                            <input type="text" name="plafon_biaya_kesehatan" id="plafon_biaya_kesehatan" class="form-control number" value="0" step=".01">
                            <label for="plafon_biaya_kesehatan">Plafon Biaya Kesehatan</label>
                        </div>
                        <div class="form-group">
                            <input type="number" name="cuti" id="cuti" class="form-control" value="0">
                            <label for="cuti">Jumlah Cuti</label>
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
    $('#position').change(function () {
        var position = $('#position').val();                        
        var plafond_biaya_spd = $('#position option:selected').data('plafond-spd');             
        var plafond_kesehatan = $('#position option:selected').data('plafond-kesehatan');            
        var plafond_cuti = $('#position option:selected').data('plafond-cuti'); 
        $('#plafon_biaya_dinas').val(plafond_biaya_spd);
        $('#plafon_biaya_kesehatan').val(plafond_kesehatan);
        $('#cuti').val(plafond_cuti);
        console.log(plafond_kesehatan);
    });
</script>
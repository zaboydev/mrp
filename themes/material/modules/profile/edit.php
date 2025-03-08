<?= form_open(site_url($module['route'] . '/save'), array(
  'autocomplete'  => 'off',
  'id'            => 'form-edit-data',
  'class'         => 'form form-validate form-xhr ui-front',
  'role'          => 'form'
)); ?>

<div class="card style-default-bright">
    <div class="card-head style-primary-dark">
        <header><span id="header_text">Info</span> <?= $module['label']; ?></header>

        <div class="tools">
            <div class="btn-group">
                <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-label="Close" title="close">
                <i class="md md-close"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div id="view-form-edit" class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12 col-lg-4">
                        <div class="form-group">
                            <input type="hidden" value="<?=$entity['employee_id'];?>" name="employee_id" id="employee_id" class="form-control">
                            <input type="text" value="<?=$entity['employee_number'];?>" name="employee_number" id="employee_number" class="form-control" data-validation-rule="unique" data-validation-url="<?= site_url('ajax/employee_number_validation'); ?>" data-validation-exception="<?=$entity['employee_number'];?>" required readonly>
                            <label for="employee_number">Employee Number</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="name" id="name" class="form-control" data-validation-rule="unique" data-validation-url="<?= site_url('ajax/employee_name_validation'); ?>" data-validation-exception="<?=$entity['name'];?>" value="<?=$entity['name'];?>" required>
                            <label for="name">Name</label>
                        </div>

                        <div class="form-group">
                            <select name="user_id" id="user_id" class="form-control" style="width: 100%" data-placeholder="Select User in MRP">
                                <option value="">Select User in MRP</option>
                                <?php foreach(available_user(array('person_name', 'user_id','username')) as $user):?>
                                <option value="<?=$user['user_id'];?>" <?php if ($entity['user_id']==$user['user_id']):echo 'selected'; endif;?>><?=$user['username'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="user_id">User in MRP</label>
                        </div>

                        <div class="form-group">
                            <select name="warehouse" id="warehouse" class="form-control" required>
                                <option value="">
                                    
                                </option>
                                <?php foreach (available_warehouses() as $base) : ?>
                                <option value="<?= $base; ?>" <?php if ($entity['warehouse']==$base):echo 'selected'; endif;?>>
                                    <?= $base; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="warehouse">Base</label>
                        </div>

                        <div class="form-group">
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="<?=$entity['date_of_birth'];?>">
                            <label for="date_of_birth">Date of Birth</label>
                        </div>

                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <div class="radio">
                                <input type="radio" name="gender" id="male" value="male" <?php if ($entity['gender']=='male'):echo 'checked'; endif;?>>
                                <label for="male">
                                    Male
                                </label>
                            </div>
                            <div class="radio">
                                <input type="radio" name="gender" id="female" value="female" <?php if ($entity['gender']=='female'):echo 'checked'; endif;?>>
                                <label for="female">
                                    Female
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <select name="religion" id="religion" class="form-control" required>
                                <option value=""></option>
                                <option value="Islam" <?php if ($entity['religion']=='Islam'):echo 'selected'; endif;?>>Islam</option>
                                <option value="Hindu" <?php if ($entity['religion']=='Hindu'):echo 'selected'; endif;?>>Hindu</option>
                                <option value="Kristen" <?php if ($entity['religion']=='Kristen'):echo 'selected'; endif;?>>Kristen</option>
                                <option value="Katolik" <?php if ($entity['religion']=='Katolik'):echo 'selected'; endif;?>>Katolik</option>
                                <option value="Budha" <?php if ($entity['religion']=='Budha'):echo 'selected'; endif;?>>Budha</option>
                                <option value="Kong Hu Cu" <?php if ($entity['religion']=='Kong Hu Cu'):echo 'selected'; endif;?>>Kong Hu Cu</option>
                            </select>
                            <label for="religion">Religion</label>
                        </div>

                        <div class="form-group">
                            <label for="marital_status">Marital Status</label>
                            <div class="radio">
                                <input type="radio" name="marital_status" id="Married" value="Married" <?php if ($entity['marital_status']=='Married'):echo 'checked'; endif;?>>
                                <label for="Married">
                                    Married
                                </label>
                            </div>
                            <div class="radio">
                                <input type="radio" name="marital_status" id="Single" value="Single" <?php if ($entity['marital_status']=='Single'):echo 'checked'; endif;?>>
                                <label for="Single">
                                    Single
                                </label>
                            </div>
                        </div>                        
                    </div>

                    <div class="col-sm-12 col-lg-4">
                        <div class="form-group">
                            <select name="identity_type" id="identity_type" class="form-control" required>
                                <option value=""></option>
                                <option value="KTP" <?php if ($entity['identity_type']=='KTP'):echo 'selected'; endif;?>>KTP</option>
                                <option value="PASSPORT" <?php if ($entity['identity_type']=='PASSPORT'):echo 'selected'; endif;?>>PASSPORT</option>
                                <option value="SIM" <?php if ($entity['identity_type']=='SIM'):echo 'selected'; endif;?>>SIM</option>
                            </select>
                            <label for="identity_type">Identity Type</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="identity_number" id="identity_number" class="form-control" required value="<?=$entity['identity_number'];?>">
                            <label for="identity_number">Identity number</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="phone_number" id="phone_number" class="form-control" required value="<?=$entity['phone_number'];?>">
                            <label for="phone_number">Phone number</label>
                        </div>

                        <div class="form-group">
                            <input type="email" class="form-control" name="email" id="email" value="<?=$entity['email'];?>" required>
                            <label for="email">Email</label>
                        </div>

                        <div class="form-group">
                            <textarea name="address" id="address" class="form-control"><?=$entity['address'];?></textarea>
                            <label for="address">Address</label>
                        </div>      
                        
                        <div class="form-group">
                            <input type="text" name="bank_account_name" id="bank_account_name" class="form-control" value="<?=$entity['bank_account_name'];?>" required>
                            <label for="bank_account_name">Bank Name</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="bank_account" id="bank_account" class="form-control" value="<?=$entity['bank_account'];?>" required>
                            <label for="bank_account">Bank Account</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="npwp" id="npwp" class="form-control" value="<?=$entity['npwp'];?>">
                            <label for="npwp">NPWP</label>
                        </div>

                    </div>

                    <div class="col-sm-12 col-lg-4">
                        <div class="form-group">
                            <input type="date" name="tanggal_bergabung" id="tanggal_bergabung" class="form-control" value="<?=$entity['tanggal_bergabung'];?>" required>
                            <label for="tanggal_bergabung">Join Date</label>
                        </div>

                        <div class="form-group">
                            <select name="department_id" id="department_id" class="form-control" style="width: 100%" data-placeholder="Select Department">
                                <option value="">Select Department</option>
                                <?php foreach(available_department() as $department):?>
                                <option value="<?=$department['id'];?>" <?php if ($entity['department_id']==$department['id']):echo 'selected'; endif;?>><?=$department['department_code'];?> - <?=$department['department_name'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="user_id">Department</label>
                        </div>

                        <div class="form-group">
                            <select name="position" id="position" class="form-control" style="width: 100%" data-placeholder="Select Occupation">
                                <option value="">Select Occupation</option>
                                <?php foreach(occupation_list() as $occupation):?>
                                <option data-plafond-cuti="<?=$occupation['cuti'];?>" data-plafond-kesehatan="<?=$occupation['plafon_biaya_kesehatan'];?>" data-plafond-spd="<?=$occupation['plafon_biaya_dinas'];?>" value="<?=$occupation['position'];?>" <?php if ($entity['position']==$occupation['position']):echo 'selected'; endif;?>><?=$occupation['position'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="position">Occupation</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="basic_salary" id="basic_salary" class="form-control number" value="<?=$entity['basic_salary'];?>" step=".01">
                            <label for="basic_salary">Basic Salary</label>
                        </div>

                        <div class="form-group hide">
                            <input type="text" name="plafon_biaya_dinas" id="plafon_biaya_dinas" class="form-control number" value="<?=$entity['plafon_biaya_dinas'];?>" step=".01">
                            <label for="plafon_biaya_dinas">Plafon Biaya Dinas</label>
                        </div>
                        <div class="form-group hide">
                            <input type="text" name="plafon_biaya_kesehatan" id="plafon_biaya_kesehatan" class="form-control number" value="<?=$entity['plafon_biaya_kesehatan'];?>" step=".01">
                            <label for="plafon_biaya_kesehatan">Plafon Biaya Kesehatan</label>
                        </div>
                        <div class="form-group">
                            <input type="number" name="cuti" id="cuti" class="form-control" value="<?=$entity['cuti'];?>">
                            <label for="cuti">Jumlah Cuti</label>
                        </div>
                        <div class="form-group">
                            <select name="level_id" id="level_id" class="form-control" style="width: 100%" data-placeholder="Select Level">
                                <option value="">Select Level</option>
                                <?php foreach(level_list() as $level):?>
                                <option data-level-id="<?=$level['id'];?>" value="<?=$level['id'];?>" <?php if ($entity['level_id']==$level['id']):echo 'selected'; endif;?>><?=$level['level'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="level_id">Level</label>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-foot">
        <input type="hidden" name="id" id="id" value="<?= $entity['employee_number']; ?>">
        <input type="hidden" name="employee_number_exception" id="employee_number_exception" value="<?= $entity['employee_number']; ?>">

        <?php if (is_granted($module, 'delete')) : ?>
        <a href="<?= site_url($module['route'] . '/delete'); ?>" class="btn btn-floating-action btn-danger btn-xhr-delete ink-reaction hide" id="modal-delete-data-button" data-title="delete">
            <i class="md md-delete"></i>
        </a>
        <?php endif; ?>

        <div class="pull-right">            
            <button type="submit" id="modal-edit-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction" data-title="save and update">
                <i class="md md-save"></i>
            </button>
        </div>

        <input type="reset" name="reset" class="sr-only">
    </div>
</div>

<?= form_close(); ?>
<script type="text/javascript">
    $('.number').number(true, 2, '.', ',');
    $('#modal-edit-data-button').click(function() {
        if($('#view-form-edit').hasClass('hide')){
            $('#view-form-edit').removeClass('hide');
            $('#modal-edit-data-submit').removeClass('hide');
            $('#modal-delete-data-button').removeClass('hide');
            $('#document_master').addClass('hide');
            $('#modal-edit-data-button').addClass('hide');
            $('#header_text').html('Edit');
        }
    });

    $("#table_contents").on("click", ".btn_view_detail", function() {
        console.log('klik detail');
        var selRow = $(this).data("row");
        var tipe = $(this).data("tipe");
        if (tipe == "view") {
        $(this).data("tipe", "hide");
        $('.detail_' + selRow).removeClass('hide');
        } else {
        $(this).data("tipe", "view");
        $('.detail_' + selRow).addClass('hide');
        }
    });
</script>
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
        <div class="row" id="document_master">
            <div class="col-sm-12 col-md-4 col-md-push-8">
                <div class="well">
                    <div class="clearfix">
                        <div class="pull-left">Biaya SPD</div>
                        <div class="pull-right">
                            <?=number_format(($entity['plafon_biaya_dinas']-$entity['left_plafon_biaya_dinas']));?>/<?=number_format($entity['plafon_biaya_dinas']);?>                                
                        </div>
                    </div>

                    <div class="clearfix">
                        <div class="pull-left">Biaya Kesehatan</div>
                        <div class="pull-right">
                            <?=number_format(($entity['plafon_biaya_kesehatan']-$entity['left_plafon_biaya_kesehatan']));?>/<?=number_format($entity['plafon_biaya_kesehatan']);?>                            
                        </div>
                    </div>

                    <div class="clearfix">
                        <div class="pull-left">Cuti</div>
                        <div class="pull-right">
                            <?=number_format(($entity['cuti']-$entity['left_cuti']),0).'/'.number_format($entity['cuti'],0);?>
                        </div>
                    </div>
                
                </div>
            </div>

            <div class="col-sm-12 col-md-8 col-md-pull-4">
                <dl class="dl-inline">

                    <dt>Employee Number</dt>
                    <dd><?=print_string($entity['employee_number']);?></dd>

                    <dt>Name</dt>
                    <dd><?=print_string($entity['name']);?></dd>

                    <dt>Date of Birth</dt>
                    <dd><?=print_date($entity['date_of_birth']);?></dd>      

                    <dt>Gender</dt>
                    <dd><?=print_string($entity['gender']);?></dd>  

                    <dt>Religion</dt>
                    <dd><?=print_string($entity['religion']);?></dd>   

                    <dt>Marital Status</dt>
                    <dd><?=print_string($entity['marital_status']);?></dd>

                    <dt>Phone Number</dt>
                    <dd><?=print_string($entity['phone_number']);?></dd>

                    <dt>Address</dt>
                    <dd><?=$entity['address'];?></dd>

                    <dt>Department</dt>
                    <dd><?=($entity['department_name']==null)? 'N/A':print_string($entity['department_name']);?></dd>

                    <dt>Occupation</dt>
                    <dd><?=print_string($entity['position']);?></dd>
                
                </dl>
            </div>
        </div>
        <div id="view-form-edit" class="row hide">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12 col-lg-6">
                        <div class="form-group">
                            <input type="text" value="<?=$entity['employee_number'];?>" name="employee_number" id="employee_number" class="form-control" data-validation-rule="unique" data-validation-url="<?= site_url('ajax/employee_number_validation'); ?>" data-validation-exception="<?=$entity['employee_number'];?>" required>
                            <label for="employee_number">Employee Number</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="name" id="name" class="form-control" data-validation-rule="unique" data-validation-url="<?= site_url('ajax/employee_name_validation'); ?>" data-validation-exception="<?=$entity['name'];?>" value="<?=$entity['name'];?>" required>
                            <label for="name">Name</label>
                        </div>

                        <div class="form-group" style="padding-top: 25px;">
                            <select name="user_id" id="user_id" class="form-control" style="width: 100%" data-placeholder="Select User in MRP">
                                <option value="">Select User in MRP</option>
                                <?php foreach(available_user(array('person_name', 'user_id','username')) as $user):?>
                                <option value="<?=$user['user_id'];?>" <?php if ($entity['user_id']==$user['user_id']):echo 'selected'; endif;?>><?=$user['username'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="user_id">User in MRP</label>
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
                                <option value="Katolik" <?php if ($entity['religion']=='Islam'):echo 'selected'; endif;?>>Katolik</option>
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
                                <input type="radio" name="gender" id="Single" value="Single" <?php if ($entity['marital_status']=='Single'):echo 'checked'; endif;?>>
                                <label for="Single">
                                    Single
                                </label>
                            </div>
                        </div>                        
                    </div>

                    <div class="col-sm-12 col-lg-6">
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

                        <div class="form-group" style="padding-top: 25px;">
                            <select name="department_id" id="department_id" class="form-control" style="width: 100%" data-placeholder="Select Department">
                                <option value="">Select Department</option>
                                <?php foreach(available_department() as $department):?>
                                <option value="<?=$department['id'];?>" <?php if ($entity['department_id']==$department['id']):echo 'selected'; endif;?>><?=$department['department_code'];?> - <?=$department['department_name'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="user_id">Department</label>
                        </div>

                        <div class="form-group" style="padding-top: 25px;">
                            <select name="position" id="position" class="form-control" style="width: 100%" data-placeholder="Select Occupation">
                                <option value="">Select Occupation</option>
                                <?php foreach(occupation_list() as $occupation):?>
                                <option data-plafond-cuti="<?=$occupation['cuti'];?>" data-plafond-kesehatan="<?=$occupation['plafon_biaya_kesehatan'];?>" data-plafond-spd="<?=$occupation['plafon_biaya_dinas'];?>" value="<?=$occupation['position'];?>" <?php if ($entity['position']==$occupation['position']):echo 'selected'; endif;?>><?=$occupation['position'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="position">Occupation</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="plafon_biaya_dinas" id="plafon_biaya_dinas" class="form-control number" value="<?=$occupation['plafon_biaya_dinas'];?>" step=".01">
                            <label for="plafon_biaya_dinas">Plafon Biaya Dinas</label>
                        </div>
                        <div class="form-group">
                            <input type="text" name="plafon_biaya_kesehatan" id="plafon_biaya_kesehatan" class="form-control number" value="<?=$occupation['plafon_biaya_kesehatan'];?>" step=".01">
                            <label for="plafon_biaya_kesehatan">Plafon Biaya Kesehatan</label>
                        </div>
                        <div class="form-group">
                            <input type="number" name="cuti" id="cuti" class="form-control" value="<?=$occupation['cuti'];?>">
                            <label for="cuti">Jumlah Cuti</label>
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
        <a href="<?= site_url($module['route'] . '/delete'); ?>" class="btn btn-floating-action btn-danger btn-xhr-delete ink-reaction" id="modal-delete-data-button" data-title="delete">
            <i class="md md-delete"></i>
        </a>
        <?php endif; ?>

        <div class="pull-right">
            <button type="button" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
                <i class="md md-edit"></i>
                <small class="top right">edit</small>
            </button>
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
            $('#document_master').addClass('hide');
            $('#modal-edit-data-button').addClass('hide');
            $('#header_text').html('Edit');
        }
    });
</script>
<div class="card">
    <div class="card-head style-primary">
        <header><?=PAGE_TITLE?></header>
    </div>
    <div class="card-body no-padding">
        <div class="row">
            <div class="col-md-12">
                <div class="profile-userpic">
                    <img src="<?=base_url('themes/material/');?>/assets/img/user.png" class="img-responsive" alt=""/>
                </div>
                <div class="profile-usertitle">
                    <div class="profile-usertitle-name"> <?= $entity['name']?> </div>
                    <div class="profile-usertitle-job"> <?= $entity['position']?> </div>
                </div>
                <div class="profile-usermenu">
                    <ul class="nav">
                        <li class="<?=($page['menu']=='detail')? 'active':'';?>">
                            <a href="<?= site_url($module['route'].'/detail/'.$entity['employee_id']); ?>">
                            <i class="md md-home"></i> Overview </a>
                        </li>
                        <li class="<?=($page['menu']=='contract')? 'active':'';?>">
                            <a href="<?= site_url($module['route'].'/contract/'.$entity['employee_id']); ?>">
                                <i class="md md-settings"></i> Employee's Contract </a>
                        </li>
                        <li class="<?=($page['menu']=='benefit')? 'active':'';?>">
                            <a href="<?= site_url($module['route'].'/benefit/'.$entity['employee_id']); ?>">
                                <i class="md md-info-outline"></i> Employee's Benefit </a>
                        </li>
                    </ul>
                </div>
            </div>            
        </div>
    </div>
</div>
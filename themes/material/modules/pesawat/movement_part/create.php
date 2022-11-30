<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<style>
    .part-on {
        color: #0aa89e
    }

    .part-off {
        color: #f44336
    }
</style>
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
                    <?= $_SESSION['movement_part']['type']; ?>
                    </div>
                </div>

                <div class="document-data table-responsive">
                    <table class="table table-hover table-bordered table-nowrap" id="table-document">
                        <thead>
                            <tr>
                                <th rowspan="2"></th>
                                <th style="text-align:center;" rowspan="2">A/C Reg</th>
                                <th style="text-align:center;" rowspan="2">Classification</th>
                                <th style="text-align:center;" rowspan="2">Date of AJLB</th>
                                <th style="text-align:center;" rowspan="2">Description Part</th>
                                <?php if($_SESSION['movement_part']['type']!='remove'):?>
                                <th style="text-align:center;" colspan="3">Install</th>
                                <th style="text-align:center;" rowspan="2">P/N <span class="part-on"> On</span></th>
                                <th style="text-align:center;" rowspan="2">S/N <span class="part-on"> On</span></th>
                                <th style="text-align:center;" rowspan="2">Alt. P/N <span class="part-on"> On</span></th>
                                <?php endif;?>                                
                                <th style="text-align:center;" colspan="3">Remove</th>
                                <th style="text-align:center;" rowspan="2">P/N <span class="part-off"> Off</span></th>
                                <th style="text-align:center;" rowspan="2">S/N <span class="part-off"> Off</span></th>
                                <th style="text-align:center;" rowspan="2">Alt. P/N <span class="part-off"> Off</span></th>
                                <th style="text-align:center;" rowspan="2">Category</th>
                                <th style="text-align:center;" rowspan="2">PIC</th>
                                <th style="text-align:center;" rowspan="2">Status</th>
                                <th style="text-align:center;" rowspan="2">Remark</th>
                            </tr>
                            <tr>
                                <?php if($_SESSION['movement_part']['type']!='remove'):?>
                                <th style="text-align:center;">Date</th>
                                <th style="text-align:center;">TSN</th>
                                <th style="text-align:center;">TSO</th>
                                <?php endif;?>   
                                <th style="text-align:center;">Date</th>
                                <th style="text-align:center;">TSN</th>
                                <th style="text-align:center;">TSO</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($_SESSION['movement_part']['items'])) : ?>
                            <?php foreach ($_SESSION['movement_part']['items'] as $i => $items) : ?>
                            <tr id="row_<?= $i; ?>">
                                <td>

                                    <a href="<?= site_url($module['route'] . '/del_item/' . $i); ?>" class="btn btn-icon-toggle btn-danger btn-sm btn_delete_document_item">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <a class="btn btn-icon-toggle btn-info btn-sm btn_edit_document_item" data-todo='{"todo":<?= $i; ?>}'>
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                                <td>
                                    <?= $items['aircraft_register']; ?>
                                </td>
                                <td>
                                    <?= $items['group_part']; ?>
                                </td>
                                <td>
                                    <?= (empty($items['date_of_ajlb']))? NULL:print_date($items['date_of_ajlb'],'d M Y'); ?>
                                </td>
                                
                                <td>
                                    <?= ($_SESSION['movement_part']['type']!='remove')? $items['install_description']:$items['remove_description']; ?>
                                </td>
                                <?php if($_SESSION['movement_part']['type']!='remove'):?>
                                <td>
                                    <?= (empty($items['install_date']))? NULL:print_date($items['install_date'],'d M Y'); ?>
                                </td>
                                <td>
                                    <?= $items['install_tsn']; ?>
                                </td>
                                <td>
                                    <?= $items['install_tso']; ?>
                                </td>
                                <td>
                                    <?= $items['install_part_number']; ?>
                                </td>
                                <td>
                                    <?= $items['install_serial_number']; ?>
                                </td>
                                <td>
                                    <?= $items['install_alternate_part_number']; ?>
                                </td>
                                <?php endif;?>   
                                <td>
                                    <?= (empty($items['remove_date']))? NULL:print_date($items['remove_date'],'d M Y'); ?>
                                </td>
                                <td>
                                    <?= $items['remove_tsn']; ?>
                                </td>
                                <td>
                                    <?= $items['remove_tso']; ?>
                                </td>
                                <!-- <td>
                                    <?= $items['remove_description']; ?>
                                </td> -->
                                <td>
                                    <?= $items['remove_part_number']; ?>
                                </td>
                                <td>
                                    <?= $items['remove_serial_number']; ?>
                                </td>
                                <td>
                                    <?= $items['remove_alternate_part_number']; ?>
                                </td>
                                
                                <td>
                                    <?= $items['remove_category']; ?>
                                </td>
                                <td>
                                    <?= $items['pic']; ?>
                                </td>
                                <td>
                                    <?= $items['status']; ?>
                                </td>
                                <td>
                                    <?= $items['remarks']; ?>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        <?php endif;?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-actionbar">
                <div class="card-actionbar-row">
                    <div class="pull-left">
                        <a href="#modal-add-item" data-toggle="modal" data-target="#modal-add-item" class="btn btn-primary ink-reaction btn-open-offcanvas pull-left">
                            Add Item
                        </a>                                     
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

    <div id="modal-add-item" class="modal fade">
        <?php if($_SESSION['movement_part']['type']=='remove'):?>
        <div class="modal-dialog modal-md" role="document">
        <?php else:?>
        <div class="modal-dialog modal-lg" role="document" style="width: 100%;height: 100%">
        <?php endif;?>    
            <div class="modal-content">
                <div class="modal-header style-primary-dark">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modal-add-item-label">Add Item</h4>
                </div>

                <?= form_open(site_url($module['route'] . '/add_item'), array(
                    'autocomplete' => 'off',
                    'id'    => 'ajax-form-create-document',
                    'class' => 'form form-validate ui-front',
                    'role'  => 'form'
                )); ?>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 col-lg-6">
                            <div class="form-group" style="padding-top: 20px;">
                                <select name="aircraft_register" id="aircraft_register" data-tag-name="aircraft_register" class="form-control input-sm select2" style="width: 100%" required>
                                    <option value="">-- SELECT Aircraft --</option>
                                    <?php foreach (pesawat() as $pesawat) : ?>
                                    <option value="<?= $pesawat; ?>"><?= $pesawat; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="aircraft_register">Aircraft</label>
                            </div>                            
                        </div>
                        <div class="col-xs-12 col-lg-6">
                            <div class="form-group" style="padding-top: 20px;">
                                <select name="group_part" id="group_part" data-tag-name="group_part" class="form-control input-sm select2" style="width: 100%" required>
                                    <option value="">-- SELECT Group --</option>
                                    <?php foreach (config_item('component_type') as $category) : ?>
                                    <option value="<?= $category; ?>"><?= ucwords($category); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="group_part">Classification</label>
                            </div>
                        </div>    
                    </div>
                    <hr>
                    
                    <div class="row">
                        <!-- tab install part -->
                        <?php if($_SESSION['movement_part']['type']!='remove'):?>
                        <div class="col-sm-12 col-lg-6">
                            <fieldset>
                                <legend>Install Part</legend>

                                <div class="form-group" style="padding-top: 20px;">
                                    <select name="source" id="source" data-tag-name="source" class="form-control input-sm select2" style="width: 100%" required>
                                        <option value="">-- Select Source Part --</option>
                                        <option value="inventory">From Inventory</option>
                                        <option value="robbing">From Robbing Part</option>
                                    </select>
                                    <label for="source">Source Part</label>
                                </div>

                                <div class="form-group" style="padding-top: 20px;">
                                    <select name="source_item_id" id="source_item_id" data-tag-name="source_item_id" class="form-control input-sm select2" style="width: 100%" required data-placeholder="--Select Component Part--">
                                        <option value=""></option>
                                        
                                    </select>
                                    <input type="hidden" name="component_install_id" id="component_install_id" class="form-control input-sm input-autocomplete">
                                    <label for="source_item_id">Part On</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="install_part_number" id="install_part_number" class="form-control input-sm input-autocomplete" required>
                                    <label for="install_part_number">Part Number</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="install_serial_number" id="install_serial_number" class="form-control input-sm input-autocomplete">
                                    <label for="install_serial_number">Serial Number</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="install_description" id="install_description" data-tag-name="install_description" data-search-for="install_description" class="form-control input-sm" required>
                                    <label for="install_description">Description</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="install_alternate_part_number" id="install_alternate_part_number" data-tag-name="install_alternate_part_number" class="form-control input-sm">
                                    <label for="install_alternate_part_number">Alt. Part Number</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="issuance_document_number" id="issuance_document_number" data-tag-name="issuance_document_number" class="form-control input-sm">
                                    <label for="issuance_document_number">MS</label>
                                </div>

                                <div class="form-group">
                                    <input type="number" name="interval_component_install" id="interval" class="form-control input-sm input-autocomplete" value="">
                                    <label for="interval">Interval</label>
                                </div>

                                <div class="form-group">
                                    <select name="interval_satuan" id="interval_satuan_component_install" data-tag-name="interval_satuan" class="form-control input-sm">
                                        <option value="">-- Select One --</option>
                                        <option value="FH">FH</option>
                                        <option value="MTHS">MTHS</option>
                                    </select>
                                    <label for="interval_satuan">Interval Satuan</label>
                                </div>

                                <div class="form-group">
                                    <input type="date" name="install_date" id="install_date" data-tag-name="install_date" class="form-control input-sm">
                                    <label for="install_date">Install Date</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="install_tsn" id="install_tsn" data-tag-name="install_tsn" class="form-control input-sm">
                                    <label for="install_tsn">Install TSN</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="install_tso" id="install_tso" data-tag-name="install_tso" class="form-control input-sm">
                                    <label for="install_tso">Install TSO</label>
                                </div>

                            </fieldset>
                        </div>
                        <?php endif;?>
                        
                        <!-- tab remove part -->
                        <?php if($_SESSION['movement_part']['type']=='remove'):?>
                        <div class="col-sm-12">
                        <?php else:?>
                        <div class="col-sm-12 col-lg-6">
                        <?php endif;?>                        
                            <fieldset>
                                <legend>Remove Part</legend>

                                <div class="form-group" style="padding-top: 20px;">
                                    <input type="date" name="date_of_ajlb" id="date_of_ajlb" class="form-control input-sm">
                                    <label for="date_of_ajlb">Date of AJLB</label>
                                </div>

                                <div class="form-group" style="padding-top: 20px;">
                                    <select name="component_remove_id" id="component_remove_id" data-tag-name="component_remove_id" class="form-control input-sm select2" style="width: 100%" required>
                                        <option value="">-- SELECT Part --</option>                                        
                                    </select>
                                    <label for="component_remove_id">Part Off</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="remove_part_number" id="remove_part_number" class="form-control input-sm input-autocomplete" required>
                                    <label for="remove_part_number">Part Number</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="remove_serial_number" id="remove_serial_number" class="form-control input-sm input-autocomplete">
                                    <label for="remove_serial_number">Serial Number</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="remove_description" id="remove_description" data-tag-name="item_description" data-search-for="item_description" class="form-control input-sm" required>
                                    <label for="remove_description">Description</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="remove_alternate_part_number" id="remove_alternate_part_number" data-tag-name="alternate_part_number" class="form-control input-sm">
                                    <label for="remove_alternate_part_number">Alt. Part Number</label>
                                </div>

                                <div class="form-group">
                                    <input type="date" name="remove_date" id="remove_date" data-tag-name="remove_date" class="form-control input-sm">
                                    <label for="remove_date">Remove Date</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="remove_tsn" id="remove_tsn" data-tag-name="remove_tsn" class="form-control input-sm">
                                    <label for="remove_tsn">Remove TSN</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="remove_tso" id="remove_tso" data-tag-name="remove_tso" class="form-control input-sm">
                                    <label for="remove_tso">Remove TSO</label>
                                </div>

                                <div class="form-group">
                                    <select name="remove_category" id="remove_category" class="form-control input-sm" style="width: 100%">
                                        <option value="">-- SELECT category --</option>
                                        <option value="ROTABLE">ROTABLE</option>
                                        <option value="CONSUMABLE">CONSUMABLE</option>
                                        <option value="REPAIRABLE">REPAIRABLE</option>
                                        <option value="ROBBING">ROBBING</option>
                                    </select>
                                    <label for="category">Category</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="pic" id="pic" data-tag-name="pic" class="form-control input-sm">
                                    <label for="pic">PIC</label>
                                </div>

                                <div class="form-group">
                                    <select name="status" id="status" class="form-control input-sm">
                                        <?php foreach (available_conditions() as $key => $condition) : ?>
                                        <option value="<?= $condition; ?>"><?= $condition; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <!-- <input type="text" name="status" id="status" data-tag-name="status" class="form-control input-sm"> -->
                                    <label for="status">Status</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="remarks" id="remarks" data-tag-name="remarks" class="form-control input-sm">
                                    <label for="remarks">Remarks</label>
                                </div>

                            </fieldset>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="modal-add-item-submit" class="btn btn-primary btn-create ink-reaction">
                        Add Item
                    </button>

                    <input type="hidden" name="consignor" id="consignor">
                    <input type="reset" name="reset" class="sr-only">
                </div>

                <?= form_close(); ?>
            </div>
        </div>
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

<?php startblock('page_modals') ?>
<?php $this->load->view('material/templates/modal_fs') ?>

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
    <script>
        Pace.on('start', function() {
            $('.progress-overlay').show();
        });

        Pace.on('done', function() {
            $('.progress-overlay').hide();
        });

        $('.select2').select2({
            // theme: "bootstrap",
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

        // function set_aircraft_register() {
        $('[name="aircraft_register"]').change(function () {
            var aircraft_register = $(this).val();
            var group = $('[name="group_part"]').val();

            if(aircraft_register!='' && group!=''){
                component_aircraft(aircraft_register,group);
            }
            source_item_id();
        });
        // }

        // function set_group() {
        $('[name="group_part"]').change(function () {
            var group = $(this).val();
            var aircraft_register = $('[name="aircraft_register"]').val();
            if(aircraft_register!='' && group!=''){
                component_aircraft(aircraft_register,group);
            }
        });
        // }

        $('[name="component_remove_id"]').change(function () {
            var part_number = $(this).find(":selected").data("part-number");
            var serial_number = $(this).find(":selected").data("serial-number");
            var alternate_part_number = $(this).find(":selected").data("alternate-part-number");
            var description = $(this).find(":selected").data("description");

            $('[name="remove_part_number"]').val(part_number);
            $('[name="remove_serial_number"]').val(serial_number);
            $('[name="remove_alternate_part_number"]').val(alternate_part_number);
            $('[name="remove_description"]').val(description);
        });

        function btn_row_delete_item() {
            $('.btn-row-delete-item').click(function () {
                $(this).parents('tr').remove();
                
            });
        }

        $('[name="source"]').change(function () {
            source_item_id();
        });

        $('[name="source_item_id"]').change(function () {
            var part_number = $(this).find(":selected").data("part-number");
            var serial_number = $(this).find(":selected").data("serial-number");
            var alternate_part_number = $(this).find(":selected").data("alternate-part-number");
            var description = $(this).find(":selected").data("description");
            var document_number = $(this).find(":selected").data("document-number");

            $('[name="install_part_number"]').val(part_number);
            $('[name="install_serial_number"]').val(serial_number);
            $('[name="install_alternate_part_number"]').val(alternate_part_number);
            $('[name="install_description"]').val(description);
            $('[name="issuance_document_number"]').val(document_number);
        });

        function component_aircraft(aircraft_register,group) {
            var component_remove = $('[name="component_remove_id"]');
            // var component_remove = ini.parents('td').siblings('td.component_remove_id').children('select[name="component_remove_id[]"]');
            
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
                            var text = '<option value="' +item.id+'" data-part-number="'+item.part_number+'" data-serial-number="'+item.serial_number+'" data-description="'+item.description+'" data-alternate-part-number="'+item.alternate_part_number+'">'+item.description+' || P/N : ' +item.part_number+'</option>';
                        // }            
                        component_remove.append(text);
                    });
                }
            });
        }

        function source_item_id() {
            var source_item_id = $('[name="source_item_id"]');
            var source = $('[name="source"]').val();
            var aircraft = $('[name="aircraft_register"]').val();

            var data_send = {
                source: source,
                aircraft: aircraft
            };            
            console.log(data_send);
            if(source!='' && aircraft!=''){
                $.ajax({
                    url: "<?= site_url($module['route'] . '/search_item_by_source'); ?>",
                    type: 'POST',
                    data: data_send,
                    dataType: "json",
                    success: function (resource) {
                        console.log(resource);
                        source_item_id.html('');
                        source_item_id.append('<option value=""></option>');
                        if(source=='inventory'){
                            $.each(resource, function(i, item) {
                                var serial_number = (item.serial_number==null || item.serial_number=='')? 'N\A':item.serial_number;
                                // if(head_dept==item.id){
                                //     var text = '<option value="' +item.id+'" selected> P/N : ' +item.part_number+'</option>';
                                // }else{
                                    var text = '<option value="' +item.id+
                                    '" data-document-number="'+item.document_number+
                                    '" data-part-number="'+item.part_number+
                                    '" data-serial-number="'+item.serial_number+
                                    '" data-description="'+item.description+
                                    '" data-alternate-part-number="'+item.alternate_part_number+
                                    '" data-tsn="'+item.remove_tsn+
                                    '" data-tso="'+item.remove_tso+
                                    '">'+item.description+
                                    ' || P/N : ' +item.part_number+
                                    ' || S/N : ' +serial_number+
                                    ' || MS : ' +item.document_number+
                                    '</option>';
                                // }            
                                source_item_id.append(text);
                            });
                        }else if(source=='robbing'){
                            $.each(resource, function(i, item) {
                                var serial_number = (item.serial_number==null || item.serial_number=='')? 'N\A':item.serial_number;
                                // if(head_dept==item.id){
                                //     var text = '<option value="' +item.id+'" selected> P/N : ' +item.part_number+'</option>';
                                // }else{
                                    var text = '<option value="' +item.id+
                                    '" data-from-aircraft="'+item.aircraft_register+
                                    '" data-part-number="'+item.part_number+
                                    '" data-serial-number="'+item.serial_number+
                                    '" data-description="'+item.description+
                                    '" data-alternate-part-number="'+item.alternate_part_number+
                                    '">'+item.description+
                                    ' || P/N : ' +item.part_number+
                                    ' || S/N : ' +serial_number+
                                    ' || A/C : ' +item.remove_aircraft_register+
                                    '</option>';
                                // }            
                                source_item_id.append(text);
                            });
                        }
                        
                    }
                });
            }
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
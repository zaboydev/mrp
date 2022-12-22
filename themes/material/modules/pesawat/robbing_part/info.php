<style>
    .part-on {
        color: #0aa89e
    }

    .part-off {
        color: #f44336
    }
</style>
<div class="card card-underline style-default-bright">
    <div class="card-head style-primary-dark">
        <header><?=strtoupper($module['label']);?></header>

        <div class="tools">
            <div class="btn-group">
                <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-label="Close" title="close">
                <i class="md md-close"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row" id="document_details">
            <div class="col-sm-12">
                <div class="table-responsive">
                <table class="table table-striped table-nowrap">
                    <thead id="table_header">
                        <tr>
                            <th></th>
                            <th style="text-align:center;">Description Part</th>
                            <th style="text-align:center;">Part Number</th>
                            <th style="text-align:center;">Serial Number</th>
                            <th style="text-align:center;">TSN</th>
                            <th style="text-align:center;">TSO</th>
                            <th style="text-align:center;">Date of AJLB</th>
                            <th style="text-align:center;">Date (Remove)</th>
                            <th style="text-align:center;">A/C Reg (Remove)</th>
                            <th style="text-align:center;">A/C Type (Remove)</th>
                            <th style="text-align:center;">A/C Base (Remove)</th>
                            <th style="text-align:center;">PIC (Remove)</th>
                            <th style="text-align:center;">Date of AJLB</th>
                            <th style="text-align:center;">Date (Install)</th>
                            <th style="text-align:center;">A/C Reg (Install)</th>
                            <th style="text-align:center;">A/C Type (Install)</th>
                            <th style="text-align:center;">A/C Base (Install)</th>
                            <th style="text-align:center;">PIC (Install)</th>
                            <th style="text-align:center;">Remark</th>
                        </tr>
                    </thead>
                    <tbody id="table_contents">
                        <tr>
                            <td class="no-space">
                                
                            </td>
                            <td>
                                <?= $entity['description']; ?>
                            </td>
                            <td>
                                <?= $entity['part_number']; ?>
                            </td>
                            <td>
                                <?= $entity['serial_number']; ?>
                            </td>
                            <td>
                                <?= $entity['remove_tsn']; ?>
                            </td>
                            <td>
                                <?= $entity['remove_tso']; ?>
                            </td>
                            <td>
                                <?= (empty($entity['date_of_ajlb']))? NULL:print_date($entity['date_of_ajlb'],'d M Y'); ?>
                            </td>
                            <td>
                                <?= (empty($entity['remove_date']))? NULL:print_date($entity['remove_date'],'d M Y'); ?>
                            </td>
                            <td>
                                <?= $entity['remove_aircraft_register']; ?>
                            </td>
                            <td>
                                <?= $entity['remove_aircraft_type']; ?>
                            </td>
                            <td>
                                <?= $entity['remove_aircraft_base']; ?>
                            </td>
                            <td>
                                <?= $entity['remove_pic']; ?>
                            </td>
                            <td>
                                <?= (empty($entity['date_of_ajlb']))? NULL:print_date($entity['date_of_ajlb'],'d M Y'); ?>
                            </td>
                            <td>
                                <?= (empty($entity['install_date']))? NULL:print_date($entity['install_date'],'d M Y'); ?>
                            </td>
                            <td>
                                <?= $entity['install_aircraft_register']; ?>
                            </td>
                            <td>
                                <?= $entity['install_aircraft_type']; ?>
                            </td>
                            <td>
                                <?= $entity['install_aircraft_base']; ?>
                            </td>
                            <td>
                                <?= $entity['install_pic']; ?>
                            </td>

                            <td>
                                <?= $entity['install_remarks']; ?>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                    
                    </tfoot>
                </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card-foot">

        <div class="pull-right">
        <?php if (is_granted($module, 'install')):?>
            <a href="<?=site_url($module['route'] .'/install/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
            <i class="md md-file-download"></i>
            <small class="top right">install</small>
            </a>
        <?php endif;?>
        </div>
    </div>
</div>
<script type="text/javascript">
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
</script>

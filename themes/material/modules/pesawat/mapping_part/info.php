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
            <div class="col-sm-6">
                <h4>Mapping Part Info</h4>

                <div class="">
                    <dl class="dl-inline">
                        <dt>
                            Description
                        </dt>
                        <dd>
                            <?=print_string($entity['description']);?>
                        </dd> 

                        <dt>
                            Part Number
                        </dt>
                        <dd>
                            <?=print_string($entity['part_number']);?>
                        </dd>

                        <dt>
                            Serial Number
                        </dt>
                        <dd>
                            <?=print_string($entity['serial_number']);?>
                        </dd>
                        
                        <dt>
                            TSN
                        </dt>
                        <dd>
                            <?=print_string($entity['remove_tsn']);?>
                        </dd> 

                        <dt>
                            TSO
                        </dt>
                        <dd>
                            <?=print_string($entity['remove_tso']);?>
                        </dd>

                        <dt>
                            Date Remove
                        </dt>
                        <dd>
                            <?=print_date($entity['remove_date'], 'd M Y');?>
                        </dd>

                        <dt>
                            Position
                        </dt>
                        <dd>
                            <?=print_string($entity['remove_aircraft_base']);?>
                        </dd>

                        <?php if($entity['remove_aircraft_base']!='MRO'):?>
                        <dt>
                            Remarks
                        </dt>
                        <dd>
                            <?=print_string($entity['remarks']);?>
                        </dd>                        
                        <?php endif;?>

                        <?php if($entity['remove_aircraft_base']=='MRO'):?>
                        <dt>
                            Vendor
                        </dt>
                        <dd>
                            <?=print_string($entity['vendor']);?>
                        </dd>

                        <dt>
                            Date send MRO
                        </dt>
                        <dd>
                            <?=($entity['date_send_mro'])?print_date($entity['date_send_mro'],'d M Y'):'';?>
                        </dd>

                        <dt>
                            Date line
                        </dt>
                        <dd>
                            <?=($entity['date_line'])?print_string($entity['date_line']).' days':'';?> 
                        </dd>
                        <?php endif;?>
                    </dl>
                </div>
            </div>
            <?php if($entity['remove_aircraft_base']!='MRO'):?>
            <div class="col-sm-6 hide">
                <h4>Send to Vendor</h4>

                <div class="well well-lg">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <input type="text" name="date_send_mro" id="date_send_mro" class="form-control" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                <label for="date_send_mro">Send Date</label>
                            </div>

                            <div class="form-group" style="padding-top: 20px;">
                                <select name="install_aircraft_register" id="install_aircraft_register" data-tag-name="install_aircraft_register" class="form-control input-sm select2" style="width: 100%" required>
                                    <option value="">-- SELECT Aircraft --</option>
                                    <?php foreach (pesawat() as $pesawat) : ?>
                                    <option value="<?= $pesawat; ?>"><?= $pesawat; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="install_aircraft_register">A/C Reg</label>
                            </div>

                            <div class="form-group">
                                <input type="text" name="install_pic" id="install_pic" class="form-control">
                                <label for="install_pic">PIC</label>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group">
                            <textarea name="remarks" id="remarks" class="form-control" rows="5"></textarea>
                            <label for="remarks">Remrks</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif;?>
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

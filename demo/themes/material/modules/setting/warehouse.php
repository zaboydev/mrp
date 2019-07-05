<?=form_open(current_url(), array('class' => 'form-horizontal','autocomplete' => 'off'));?>
<div class="box-body">
    <?php
    if ( $this->session->flashdata('alert') )
        render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
    ?>

    <div class="form-group <?php echo (form_error('setting_value')) ? 'has-error' : '';?>">
        <label for="setting_value" class="col-sm-3 control-label">Main Base</label>
        <div class="col-sm-9">
            <select name="setting_value" id="setting_value" class="form-control" required="required">
                <option value="" <?=($setting_value == null) ? 'selected' : '';?>>-- Select Base</option>

                <?php foreach ($warehouses as $base):?>
                    <option value="<?=$base['warehouse'];?>" <?=($base['warehouse'] == $setting_value) ? 'selected' : '';?>>
                        <?=$base['warehouse'];?>
                    </option>
                <?php endforeach;?>
            </select>
            <?php echo form_error('setting_value', '<span class="help-block text-danger">', '</span>'); ?>
        </div>
    </div>
</div>
<div class="box-footer">
    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </div>
</div>
<?=form_close();?>
